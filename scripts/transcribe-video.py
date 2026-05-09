#!/usr/bin/env python3
"""
Transcribe a video file using faster-whisper.

Usage:
  python3 scripts/transcribe-video.py <video_path> [--model small.en] [--output-dir <dir>]

Output files (next to video, with .basename):
  <basename>.transcript.md   ← committed to git, frontmatter + timestamped lines
  <basename>.transcript.srt  ← gitignored, raw whisper SRT

Designed for the eeiblog.com webinar transcription pipeline.
Supports both the sandbox (small.en, CPU int8) and Mac local (large-v3 if you swap model).
"""
import argparse
import json
import sys
import time
from datetime import datetime, timezone
from pathlib import Path


def srt_timestamp(seconds: float) -> str:
    """Convert seconds to SRT timestamp HH:MM:SS,mmm."""
    if seconds < 0:
        seconds = 0
    h = int(seconds // 3600)
    m = int((seconds % 3600) // 60)
    s = int(seconds % 60)
    ms = int((seconds - int(seconds)) * 1000)
    return f"{h:02d}:{m:02d}:{s:02d},{ms:03d}"


def hms(seconds: float) -> str:
    """h:mm:ss for frontmatter."""
    seconds = int(seconds)
    h, m, s = seconds // 3600, (seconds % 3600) // 60, seconds % 60
    return f"{h}:{m:02d}:{s:02d}"


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("video", help="Path to video/audio file")
    ap.add_argument("--model", default="small.en", help="Whisper model name (tiny.en/base.en/small.en/medium.en/large-v3)")
    ap.add_argument("--device", default="cpu")
    ap.add_argument("--compute-type", default="int8")
    ap.add_argument("--cpu-threads", type=int, default=4)
    ap.add_argument("--language", default="en")
    ap.add_argument("--output-dir", default=None, help="Directory for transcript files (default: alongside the video)")
    ap.add_argument("--output-basename", default=None, help="Basename for transcript files (default: video stem)")
    ap.add_argument("--progress-file", default=None, help="Write progress lines to this file (for background runs)")
    args = ap.parse_args()

    video = Path(args.video).resolve()
    if not video.exists():
        print(f"ERROR: video not found: {video}", file=sys.stderr)
        sys.exit(1)

    out_dir = Path(args.output_dir) if args.output_dir else video.parent
    out_dir.mkdir(parents=True, exist_ok=True)
    base = args.output_basename or video.stem.replace(" ", "_")
    md_path = out_dir / f"{base}.transcript.md"
    srt_path = out_dir / f"{base}.transcript.srt"

    progress_fp = open(args.progress_file, "w", buffering=1) if args.progress_file else None

    def log(msg):
        line = f"[{datetime.now(timezone.utc).isoformat(timespec='seconds')}] {msg}"
        print(line, flush=True)
        if progress_fp:
            progress_fp.write(line + "\n")

    log(f"Loading whisper model: {args.model} ({args.compute_type}, {args.cpu_threads} threads)")
    t0 = time.monotonic()

    from faster_whisper import WhisperModel
    model = WhisperModel(
        args.model,
        device=args.device,
        compute_type=args.compute_type,
        cpu_threads=args.cpu_threads,
    )
    log(f"Model loaded in {time.monotonic() - t0:.1f}s")

    log(f"Starting transcription: {video.name}")
    t1 = time.monotonic()
    segments_iter, info = model.transcribe(
        str(video),
        language=args.language,
        beam_size=5,
        vad_filter=True,
        vad_parameters={"min_silence_duration_ms": 500},
    )
    log(f"Audio duration: {hms(info.duration)} ({info.duration:.1f}s), language: {info.language}")

    # Collect segments (this is where most time is spent — generator)
    segments = []
    last_log = t1
    for seg in segments_iter:
        segments.append({
            "start": seg.start,
            "end": seg.end,
            "text": seg.text.strip(),
        })
        now = time.monotonic()
        if now - last_log >= 30:
            pct = (seg.end / info.duration) * 100 if info.duration else 0
            log(f"  progress: segment {len(segments)}, audio pos {hms(seg.end)} / {hms(info.duration)} ({pct:.0f}%)")
            last_log = now

    elapsed = time.monotonic() - t1
    log(f"Transcription finished: {len(segments)} segments in {hms(elapsed)} (real-time factor: {info.duration / elapsed:.2f}x)")

    # Write SRT (gitignored)
    srt_lines = []
    for i, seg in enumerate(segments, 1):
        srt_lines.append(str(i))
        srt_lines.append(f"{srt_timestamp(seg['start'])} --> {srt_timestamp(seg['end'])}")
        srt_lines.append(seg["text"])
        srt_lines.append("")
    srt_path.write_text("\n".join(srt_lines), encoding="utf-8")
    log(f"Wrote {srt_path}")

    # Write Markdown with frontmatter (committed)
    transcribed_at = datetime.now(timezone.utc).isoformat(timespec="seconds")
    fm = [
        "---",
        f"video_file: {video.name}",
        f"transcript_source: \"faster-whisper ({args.model}, {args.compute_type}, cpu)\"",
        f"transcribed_at: \"{transcribed_at}\"",
        "review_status: machine",
        f"duration_human: \"{hms(info.duration)}\"",
        f"duration_seconds: {info.duration:.1f}",
        f"segment_count: {len(segments)}",
        f"language: {info.language}",
        "---",
        "",
    ]
    body = []
    for seg in segments:
        ts = f"[{int(seg['start'] // 3600):02d}:{int((seg['start'] % 3600) // 60):02d}:{int(seg['start'] % 60):02d}]"
        body.append(f"{ts} {seg['text']}")
    md_path.write_text("\n".join(fm + body) + "\n", encoding="utf-8")
    log(f"Wrote {md_path}")

    if progress_fp:
        progress_fp.close()


if __name__ == "__main__":
    main()
