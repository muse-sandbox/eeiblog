#!/usr/bin/env bash
# Transcribe a video file using whisper.cpp on a Mac (Metal-accelerated).
#
# Usage:
#   bash scripts/transcribe-video.sh <video-file>            # one video
#   bash scripts/transcribe-video.sh assets/videos/*.mp4     # all videos in a glob
#   bash scripts/transcribe-video.sh                         # all videos in assets/videos/ (skips already-transcribed)
#
# Reuses the whisper.cpp install + ggml-large-v3.bin model from the podcast pipeline (assets/podcasts/whisper-models/).
# Requires: whisper-cpp, ffmpeg (both via brew).
#
# Output for each video at <repo>/assets/videos/:
#   <basename>.audio.mp3            (gitignored, intermediate)
#   <basename>.transcript.srt       (gitignored, raw whisper output)
#   <basename>.transcript.md        (committed, frontmatter + timestamped lines)
#
# <basename> is the video filename with the trailing " (720p)" / " (1080p)" stripped and spaces → underscores.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
VIDEO_DIR="$REPO_ROOT/assets/videos"
AUDIO_DIR="$VIDEO_DIR/audio"
TRANSCRIPT_DIR="$VIDEO_DIR/transcripts"
MODEL="${WHISPER_MODEL:-large-v3}"
MODEL_PATH="$REPO_ROOT/assets/podcasts/whisper-models/ggml-${MODEL}.bin"

mkdir -p "$AUDIO_DIR" "$TRANSCRIPT_DIR"

# Verify tooling — whisper.cpp binary may be `whisper-cli` (modern) or `whisper-cpp` (older / via brew alias)
WHISPER_BIN=""
for candidate in whisper-cli whisper-cpp; do
  if command -v "$candidate" >/dev/null; then
    WHISPER_BIN="$candidate"
    break
  fi
done

if [[ -z "$WHISPER_BIN" ]]; then
  echo "ERROR: neither whisper-cli nor whisper-cpp found." >&2
  echo "Install via: brew install whisper-cpp" >&2
  echo "(Brew installs the binary as 'whisper-cli' on modern versions.)" >&2
  exit 1
fi

for tool in ffmpeg ffprobe; do
  if ! command -v "$tool" >/dev/null; then
    echo "ERROR: $tool not found. Install: brew install ffmpeg" >&2
    exit 1
  fi
done

echo "Using whisper binary: $(command -v "$WHISPER_BIN")"

if [[ ! -f "$MODEL_PATH" ]]; then
  echo "ERROR: Whisper model not found at $MODEL_PATH" >&2
  echo "Expected from podcast pipeline. Re-download with:" >&2
  echo "  cd $REPO_ROOT/assets/podcasts/whisper-models && bash ./download-ggml-model.sh $MODEL" >&2
  exit 1
fi

# Collect target video paths
TARGETS=()
if [[ $# -eq 0 ]]; then
  # No args — find all mp4/mov in assets/videos/ that don't have a transcript yet
  shopt -s nullglob
  for video in "$VIDEO_DIR"/*.{mp4,mov,mkv,m4v,webm}; do
    [[ -e "$video" ]] || continue
    base=$(basename "$video")
    base="${base%.*}"
    base=$(echo "$base" | sed -E 's/ *\([0-9]+p\)$//' | tr ' ' '_')
    if [[ -f "$TRANSCRIPT_DIR/${base}.transcript.md" ]]; then
      echo "Skip (already transcribed): $(basename "$video")"
    else
      TARGETS+=("$video")
    fi
  done
  shopt -u nullglob
else
  TARGETS=("$@")
fi

if [[ ${#TARGETS[@]} -eq 0 ]]; then
  echo "Nothing to transcribe."
  exit 0
fi

echo "Targets:"
for t in "${TARGETS[@]}"; do echo "  $t"; done
echo "Model: $MODEL"
echo

transcribe_one() {
  local VIDEO="$1"
  if [[ ! -f "$VIDEO" ]]; then
    echo "Skip (not found): $VIDEO" >&2
    return
  fi

  local base
  base=$(basename "$VIDEO")
  base="${base%.*}"
  base=$(echo "$base" | sed -E 's/ *\([0-9]+p\)$//' | tr ' ' '_')

  local AUDIO="$AUDIO_DIR/${base}.audio.mp3"
  local SRT="$TRANSCRIPT_DIR/${base}.transcript.srt"
  local MD="$TRANSCRIPT_DIR/${base}.transcript.md"

  echo "===== $base ====="

  # 1. Extract audio
  if [[ ! -f "$AUDIO" ]]; then
    echo "[1/3] Extracting audio..."
    ffmpeg -hide_banner -loglevel error -y -i "$VIDEO" \
      -vn -ar 16000 -ac 1 -c:a libmp3lame -q:a 4 "$AUDIO"
  fi

  # 2. Transcribe
  #
  # --no-fallback disables temperature fallback, which is the main source of
  # "stuck" hallucinated loops at end-of-audio. That alone usually fixes it.
  # We do NOT pass other thresholds — defaults are tuned for whisper.cpp; aggressive
  # values (e.g. --word-thold 0.5) drop legitimate words and have caused some
  # whisper-cli builds to error out silently.
  if [[ ! -f "$SRT" ]]; then
    echo "[2/3] Transcribing with $WHISPER_BIN ($MODEL)..."
    # Do NOT pipe through `tail` here — it would hide whisper-cli errors and you'd
    # think it ran when it didn't. Print everything and check exit status.
    "$WHISPER_BIN" -m "$MODEL_PATH" -f "$AUDIO" -of "${SRT%.srt}" -osrt -otxt -l en \
      --no-fallback \
      --print-progress
    if [[ ! -f "$SRT" ]]; then
      echo "ERROR: whisper finished but $SRT was not produced. Check the output above." >&2
      return 1
    fi
    rm -f "${SRT%.srt}.txt"
  fi

  # 3. Build markdown
  echo "[3/3] Building markdown..."
  local DURATION_HUMAN=""
  local DURATION_SEC
  DURATION_SEC=$(ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "$AUDIO" 2>/dev/null || echo "0")
  local DURATION_INT=${DURATION_SEC%.*}
  if [[ "$DURATION_INT" -gt 0 ]]; then
    local H=$((DURATION_INT / 3600))
    local M=$(( (DURATION_INT % 3600) / 60 ))
    local S=$((DURATION_INT % 60))
    DURATION_HUMAN=$(printf "%d:%02d:%02d" "$H" "$M" "$S")
  fi

  python3 - "$SRT" "$MD" "$VIDEO" "$MODEL" "$DURATION_HUMAN" <<'PYEOF'
import sys, re
from datetime import datetime, timezone
from pathlib import Path

srt_path, md_path, video_path, model, duration_human = sys.argv[1:6]
src = Path(srt_path).read_text(encoding='utf-8')

lines = src.splitlines()
out = []
block = []

def flush(block):
    if len(block) >= 3:
        m = re.match(r'(\d\d):(\d\d):(\d\d),\d\d\d', block[1])
        ts = f'[{m.group(1)}:{m.group(2)}:{m.group(3)}]' if m else ''
        text = ' '.join(b.strip() for b in block[2:]).strip()
        if text:
            out.append(f'{ts} {text}'.strip())

for line in lines:
    if line.strip() == '':
        flush(block)
        block = []
    else:
        block.append(line)
flush(block)

fm = [
    '---',
    f'video_file: {Path(video_path).name}',
    f'transcript_source: "whisper.cpp ({model})"',
    f'transcribed_at: "{datetime.now(timezone.utc).isoformat(timespec="seconds")}"',
    'review_status: machine',
]
if duration_human:
    fm.append(f'duration_human: "{duration_human}"')
fm.append(f'segment_count: {len(out)}')
fm.append('---')
fm.append('')

Path(md_path).write_text('\n'.join(fm + out) + '\n', encoding='utf-8')
print(f'  Wrote {md_path} ({len(out)} segments)')
PYEOF

  echo "  Done: $MD"
  echo
}

for t in "${TARGETS[@]}"; do
  transcribe_one "$t"
done

echo "All done."
