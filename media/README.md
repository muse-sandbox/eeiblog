# `media/` — non-text source assets

Audio, video, and large image source files that are too big or too binary for git. The whole tree is mostly gitignored (see project root `.gitignore`); only metadata files and transcripts are tracked.

## Layout

```
media/
├── README.md                     # this file
├── podcasts/
│   └── ee-band-talk/             # 16-episode podcast, see local README
│       ├── README.md
│       ├── episodes.json
│       ├── audio/                # gitignored MP3s
│       └── transcripts/          # tracked markdown transcripts
└── images/
    └── squarespace-cdn/          # gitignored bulk-download landing
```

## Conventions

- One subfolder per asset family (`podcasts/<show>`, `videos/<series>`, `images/<source>`).
- Each subfolder has its own README and a JSON manifest with the canonical metadata.
- Source binaries (`*.mp3`, `*.mp4`, `*.wav`, big PNGs) are gitignored. Text companions (transcripts, captions, manifests) live alongside them and ARE tracked.
- Naming: zero-pad numeric prefixes (`ep01`, not `ep1`) so files sort lexicographically.

## Why this folder exists

The Squarespace migration left several embeds stranded — most notably the EE Band Talk podcast (`sqs-audio-embed` blocks didn't carry over). Rather than re-uploading binaries through WordPress and losing the source, we keep audio + transcripts here and reference them from WP posts via media URLs. Transcripts also feed downstream blog posts.

If we add more migrations later (videos, image sets), follow the same pattern: `media/<family>/<asset>/{README.md, manifest.json, source/, derived/}`.
