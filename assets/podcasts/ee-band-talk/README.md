# EE Band Talk — podcast workflow

A 16-episode podcast (July 2020 – April 2021) by Charlie Menghini, Tim Lautzenheiser, and Paul Lavender. Originally hosted as `sqs-audio-embed` blocks on the Squarespace `/ee-band-talk` page; those blocks did not migrate to WordPress, so we are reconstructing the archive from local audio files.

## Folder layout

```
podcasts/ee-band-talk/
├── README.md            # this file
├── episodes.json        # canonical metadata for all 16 episodes
├── audio/               # source MP3s (gitignored — large binaries kept locally only)
│   ├── ee-band-talk-ep01.mp3
│   ├── ee-band-talk-ep02.mp3
│   └── … ee-band-talk-ep16.mp3
└── transcripts/         # transcripts (markdown, in git)
    ├── ee-band-talk-ep01.md
    └── …
```

Naming convention: zero-padded two-digit episode number (`ep01` … `ep16`) so files sort lexicographically.

## Pipeline

Three stages, each independent.

### 1. Place source audio

Download the 16 episodes from wherever you found them and rename to the convention above. Drop them into `audio/`. The folder is gitignored so the files stay on your laptop only — fine for transcription, but they'll need a separate decision for re-publishing (WP media library upload, S3, podcast host, etc.).

After audio is in place, fill in the `duration_sec` field in `episodes.json` (run `ffprobe -i audio/ee-band-talk-ep01.mp3 -show_entries format=duration -v quiet -of csv="p=0"` if you want to script it).

### 2. Transcribe

For each episode produce a `transcripts/ee-band-talk-epNN.md` with frontmatter:

```yaml
---
episode: 1
date: 2020-07-27
title: Episode 1 — <topic if known>
audio_file: audio/ee-band-talk-ep01.mp3
duration_sec: null
hosts: [Charlie Menghini, Tim Lautzenheiser, Paul Lavender]
guests: []
transcript_source: whisper-large-v3   # or "manual" / "human-edited"
transcribed_at: 2026-MM-DDTHH:MM:SSZ
---
```

…followed by the transcript body. Suggested format: speaker labels (`**Charlie:**`, `**Tim:**`, `**Paul:**`), one paragraph per turn, timestamps every minute or so:

```
[00:00] **Charlie:** Welcome to EE Band Talk…

[01:14] **Tim:** Thanks, Charlie. Today we're talking about…
```

Transcripts are tracked in git — small text files, useful as source-of-truth for derived blog posts and for SEO.

### 3. Use in WordPress

Two parallel uses:

1. **Archive post** — WP post 212 (`ee-band-talk`) currently lists the 16 episode headings as placeholders. After audio is hosted somewhere (WP media library, podcast platform, etc.), update post 212 with a player block per episode plus an excerpt from the transcript.

2. **Derived blog posts** — pick highlights/topics from each transcript and turn them into standalone teaching-tips or news posts. Each derived post should link back to the archive post and reference the episode number + date.

## Episode index

See `episodes.json` for the canonical list. Topics are `null` until we transcribe and identify them.

| # | Date | Notes |
|---|---|---|
| 1 | 2020-07-27 | Series premiere |
| 2 | 2020-08-03 | |
| 3 | 2020-08-10 | |
| 4 | 2020-08-17 | |
| 5 | 2020-08-24 | |
| 6 | 2020-08-31 | |
| 7 | 2020-09-08 | |
| 8 | 2020-09-14 | |
| 9 | 2020-09-21 | |
| 10 | 2020-10-05 | |
| 11 | 2020-10-12 | |
| 12 | 2020-11-02 | |
| 13 | 2020-12-12 | |
| 14 | 2021-02-08 | |
| 15 | 2021-02-24 | |
| 16 | 2021-04-23 | Last episode |

## Transcription tooling notes

- **whisper.cpp** (local, fast on M-series Macs): `./main -m models/ggml-large-v3.bin -f audio/ee-band-talk-ep01.mp3 -of transcripts/ee-band-talk-ep01 --output-md`
- **OpenAI Whisper API**: `whisper audio/ee-band-talk-ep01.mp3 --model large-v3 --output_format md --output_dir transcripts/`
- **Manual / human review** is recommended for the first pass since speakers will overlap and music education terminology will throw off the model.

## Open follow-ups

- [ ] Place 16 MP3s in `audio/`
- [ ] Transcribe episodes 1–16
- [ ] Identify episode topics, fill in `episodes.json` `topic` field
- [ ] Decide audio hosting (WP media library? Existing podcast host? S3?)
- [ ] Update WP post 212 with player embeds + show notes per episode
- [ ] Pull blog post topics from transcripts (link back to post 212)
