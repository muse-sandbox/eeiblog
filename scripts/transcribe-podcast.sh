#!/usr/bin/env bash
# Transcribe EE Band Talk podcast episodes locally on a Mac.
#
# Usage:
#   bash scripts/transcribe-podcast.sh                     # transcribe all missing episodes
#   bash scripts/transcribe-podcast.sh ep01 ep02           # transcribe specific episodes
#   bash scripts/transcribe-podcast.sh --backend openai    # use OpenAI Whisper API instead of local
#
# Two backends:
#   - whisper-cpp (default, local, free, requires whisper-cpp via homebrew)
#   - openai (cloud, ~$0.006/min, requires OPENAI_API_KEY env var)
#
# Output: media/podcasts/ee-band-talk/transcripts/ee-band-talk-<id>.md
#         (markdown with frontmatter; tracked in git)

set -euo pipefail

# Resolve repo root
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
PODCAST_DIR="$REPO_ROOT/media/podcasts/ee-band-talk"
AUDIO_DIR="$PODCAST_DIR/audio"
TRANSCRIPT_DIR="$PODCAST_DIR/transcripts"
EPISODES_JSON="$PODCAST_DIR/episodes.json"

mkdir -p "$TRANSCRIPT_DIR"

# Defaults
BACKEND="whisper-cpp"
MODEL="${WHISPER_MODEL:-large-v3}"
TARGETS=()

# Parse args
while [[ $# -gt 0 ]]; do
  case "$1" in
    --backend)
      BACKEND="$2"
      shift 2
      ;;
    --model)
      MODEL="$2"
      shift 2
      ;;
    --help|-h)
      sed -n '2,15p' "$0"
      exit 0
      ;;
    *)
      TARGETS+=("$1")
      shift
      ;;
  esac
done

# If no targets specified, transcribe all missing
if [[ ${#TARGETS[@]} -eq 0 ]]; then
  for f in "$AUDIO_DIR"/*.mp3; do
    base="$(basename "$f" .mp3)"
    short_id="${base#ee-band-talk-}"     # ep01, ep02, …, bonus-eccles
    transcript_path="$TRANSCRIPT_DIR/${base}.md"
    if [[ ! -f "$transcript_path" ]]; then
      TARGETS+=("$short_id")
    fi
  done
fi

if [[ ${#TARGETS[@]} -eq 0 ]]; then
  echo "All episodes already transcribed — nothing to do."
  echo "Re-run with --backend openai to overwrite, or delete transcripts manually."
  exit 0
fi

echo "Backend: $BACKEND"
echo "Model:   $MODEL"
echo "Targets: ${TARGETS[*]}"
echo

# Verify backend availability
case "$BACKEND" in
  whisper-cpp)
    # whisper-cpp 1.7+ ships its CLI as `whisper-cli` (older versions used `whisper-cpp` or `main`).
    WHISPER_BIN=""
    for cand in whisper-cli whisper-cpp whisper main; do
      if command -v "$cand" &>/dev/null; then
        WHISPER_BIN="$cand"
        break
      fi
    done
    if [[ -z "$WHISPER_BIN" ]]; then
      echo "ERROR: no whisper.cpp CLI found in PATH (tried: whisper-cli, whisper-cpp, whisper, main)."
      echo "Install with:    brew install whisper-cpp"
      echo "Verify with:     ls \$(brew --prefix whisper-cpp)/bin/"
      echo "Then download a model:"
      echo "    bash \$(brew --prefix whisper-cpp)/share/whisper-cpp/download-ggml-model.sh large-v3"
      exit 1
    fi
    # Resolve model file. Try several known locations (brew-prefix-aware).
    MODEL_FILE=""
    candidate_dirs=()
    if command -v brew &>/dev/null; then
      brew_prefix="$(brew --prefix 2>/dev/null || true)"
      brew_pkg_prefix="$(brew --prefix whisper-cpp 2>/dev/null || true)"
      [[ -n "$brew_pkg_prefix" ]] && candidate_dirs+=("$brew_pkg_prefix/share/whisper-cpp")
      [[ -n "$brew_prefix"     ]] && candidate_dirs+=("$brew_prefix/share/whisper-cpp")
    fi
    candidate_dirs+=(
      "$REPO_ROOT/media/whisper-models"
      "/opt/homebrew/share/whisper-cpp"
      "/usr/local/share/whisper-cpp"
      "$HOME/.local/share/whisper-cpp"
      "$HOME/whisper.cpp/models"
    )
    for prefix in "${candidate_dirs[@]}"; do
      candidate="$prefix/ggml-$MODEL.bin"
      if [[ -f "$candidate" ]]; then
        MODEL_FILE="$candidate"
        break
      fi
    done
    if [[ -z "$MODEL_FILE" ]]; then
      echo "ERROR: model file ggml-$MODEL.bin not found. Tried:"
      for d in "${candidate_dirs[@]}"; do echo "    $d/ggml-$MODEL.bin"; done
      echo "Download with:"
      echo "    bash \$(brew --prefix whisper-cpp)/share/whisper-cpp/download-ggml-model.sh $MODEL"
      exit 1
    fi
    echo "Using whisper binary:  $WHISPER_BIN"
    echo "Using model file:      $MODEL_FILE"
    echo
    ;;
  openai)
    if [[ -z "${OPENAI_API_KEY:-}" ]]; then
      echo "ERROR: OPENAI_API_KEY env var not set."
      exit 1
    fi
    if ! command -v curl &>/dev/null; then
      echo "ERROR: curl required for OpenAI backend."
      exit 1
    fi
    ;;
  *)
    echo "ERROR: unknown backend '$BACKEND' (use whisper-cpp or openai)"
    exit 1
    ;;
esac

transcribe_one() {
  local short_id="$1"
  local audio_file="$AUDIO_DIR/ee-band-talk-${short_id}.mp3"
  local transcript_file="$TRANSCRIPT_DIR/ee-band-talk-${short_id}.md"
  local raw_file="$TRANSCRIPT_DIR/ee-band-talk-${short_id}.raw"

  if [[ ! -f "$audio_file" ]]; then
    echo "SKIP $short_id: audio not found at $audio_file"
    return
  fi

  echo "==> Transcribing $short_id ($(du -h "$audio_file" | cut -f1))"
  local started_at
  started_at="$(date -u +"%Y-%m-%dT%H:%M:%SZ")"

  case "$BACKEND" in
    whisper-cpp)
      # whisper-cli 1.8.4 dropped --output-md. Use SRT (universally supported) and parse it ourselves.
      # We need 16kHz wav input for best results — whisper-cli will resample MP3 internally.
      local out_prefix="$TRANSCRIPT_DIR/ee-band-talk-${short_id}"
      "$WHISPER_BIN" \
        -m "$MODEL_FILE" \
        -f "$audio_file" \
        -of "$out_prefix" \
        -osrt \
        -pp \
        2>&1 | tail -3
      if [[ ! -f "${out_prefix}.srt" ]]; then
        echo "ERROR: whisper-cli did not produce ${out_prefix}.srt — bailing on $short_id"
        return 1
      fi
      # Convert SRT to a flat timestamped transcript (one line per segment)
      python3 - "$out_prefix.srt" "$raw_file" <<'PYEOF'
import re, sys
src, dst = sys.argv[1], sys.argv[2]
out = []
block = []
with open(src, 'r', encoding='utf-8') as f:
    for line in f:
        line = line.rstrip()
        if not line:
            if block:
                # block: index / "HH:MM:SS,mmm --> HH:MM:SS,mmm" / text...
                if len(block) >= 3:
                    times = block[1]
                    m = re.match(r'(\d\d):(\d\d):(\d\d),\d\d\d', times)
                    if m:
                        h, mm, ss = m.groups()
                        ts = f'[{h}:{mm}:{ss}]'
                    else:
                        ts = ''
                    text = ' '.join(b.strip() for b in block[2:]).strip()
                    if text:
                        out.append(f'{ts} {text}'.strip())
                block = []
        else:
            block.append(line)
    if block and len(block) >= 3:
        times = block[1]
        m = re.match(r'(\d\d):(\d\d):(\d\d),\d\d\d', times)
        ts = f'[{m.group(1)}:{m.group(2)}:{m.group(3)}]' if m else ''
        text = ' '.join(b.strip() for b in block[2:]).strip()
        if text:
            out.append(f'{ts} {text}'.strip())
with open(dst, 'w', encoding='utf-8') as f:
    f.write('\n'.join(out) + '\n')
PYEOF
      # Keep the SRT file too (gitignored) — useful for re-import or as VTT for video players
      ;;
    openai)
      # OpenAI Whisper API has 25 MB limit per request — split if needed
      local size_bytes
      size_bytes=$(stat -f%z "$audio_file" 2>/dev/null || stat -c%s "$audio_file")
      if [[ "$size_bytes" -gt 26214400 ]]; then
        echo "  File >25MB — re-encoding to 64kbps mono mp3 for upload"
        local tmp_audio="${TRANSCRIPT_DIR}/_tmp_${short_id}.mp3"
        ffmpeg -y -i "$audio_file" -ac 1 -b:a 64k "$tmp_audio" 2>&1 | tail -3
        audio_file="$tmp_audio"
      fi
      curl -sS https://api.openai.com/v1/audio/transcriptions \
        -H "Authorization: Bearer $OPENAI_API_KEY" \
        -F file="@$audio_file" \
        -F model="whisper-1" \
        -F response_format="verbose_json" \
        -F timestamp_granularities[]="segment" \
        > "${raw_file}.json"
      python3 -c "
import json
data = json.load(open('${raw_file}.json'))
for seg in data.get('segments', []):
    ts = int(seg['start'])
    h, m, s = ts // 3600, (ts % 3600) // 60, ts % 60
    print(f'[{h:02d}:{m:02d}:{s:02d}] {seg[\"text\"].strip()}')
" > "$raw_file"
      [[ -f "${TRANSCRIPT_DIR}/_tmp_${short_id}.mp3" ]] && rm "${TRANSCRIPT_DIR}/_tmp_${short_id}.mp3"
      ;;
  esac

  # Wrap with frontmatter
  local episode_meta
  episode_meta=$(python3 -c "
import json, sys
data = json.load(open('$EPISODES_JSON'))
sid = '$short_id'
for ep in data['episodes']:
    if ep.get('audio_filename', '').endswith(f'{sid}.mp3'):
        print(json.dumps({'number': ep['number'], 'date': ep.get('page_date'), 'duration_sec': ep.get('duration_sec'), 'duration_human': ep.get('duration_human'), 'audio_filename': ep['audio_filename']}))
        sys.exit(0)
for ep in data.get('bonus_episodes', []):
    if ep.get('audio_filename', '').endswith(f'{sid}.mp3'):
        print(json.dumps({'number': None, 'slug': ep.get('slug'), 'duration_sec': ep.get('duration_sec'), 'duration_human': ep.get('duration_human'), 'audio_filename': ep['audio_filename'], 'bonus': True}))
        sys.exit(0)
print('{}')
")

  {
    echo "---"
    echo "podcast: ee-band-talk"
    python3 -c "
import json
m = json.loads('''$episode_meta''')
for k, v in m.items():
    if isinstance(v, str):
        print(f'{k}: \"{v}\"')
    elif v is None:
        print(f'{k}: null')
    else:
        print(f'{k}: {v}')
"
    echo "audio_file: audio/ee-band-talk-${short_id}.mp3"
    echo "transcript_source: $BACKEND ($MODEL)"
    echo "transcribed_at: $started_at"
    echo "review_status: machine"
    echo "hosts: [Charlie Menghini, Tim Lautzenheiser, Paul Lavender]"
    echo "guests: []"
    echo "topic: null"
    echo "---"
    echo
    cat "$raw_file"
  } > "$transcript_file"

  rm -f "$raw_file" "${raw_file}.json"
  echo "  Wrote $transcript_file"
  echo
}

for tgt in "${TARGETS[@]}"; do
  transcribe_one "$tgt"
done

echo "Done. Review transcripts in $TRANSCRIPT_DIR"
