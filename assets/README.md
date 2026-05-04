# /assets/ — non-text source assets

Audio, video, image, and PDF source files used during the migration.
Most subtrees are gitignored; metadata, transcripts, and PDFs are tracked.

## Layout

```
/assets/
├── images/                    # Squarespace-CDN image originals + WP-ready optimized output (gitignored)
├── pdfs/                      # PDFs uploaded to WP media library (tracked)
├── videos/                    # mp4/mov pending YouTube upload (gitignored)
└── podcasts/
    ├── ee-band-talk/          # 16-episode podcast
    │   ├── audio/             # gitignored MP3s
    │   ├── transcripts/       # tracked markdown transcripts
    │   └── episodes.json      # canonical metadata
    └── whisper-models/        # Whisper.cpp model binaries (gitignored, ~1-3 GB)
```

## Conventions

- Source binaries (`*.mp3`, `*.mp4`, `*.wav`) are gitignored. Text companions (transcripts, JSON manifests) live alongside them and ARE tracked.
- Naming: zero-pad numeric prefixes (`ep01`, not `ep1`) so files sort lexicographically.
- Image filenames in WP media library follow `<post-slug>-<sanitized-source-filename>.webp` (see `scripts/optimize-assets.sh`).

## Why this folder exists

The Squarespace migration left several embeds stranded — most notably the EE Band Talk podcast (`sqs-audio-embed` blocks didn't carry over) and several Vimeo-hosted videos that need to be re-uploaded to YouTube. Source assets stay here so we can re-upload them, and text companions (transcripts, captions, manifests) get committed for traceability.
