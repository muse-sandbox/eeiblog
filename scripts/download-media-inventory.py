#!/usr/bin/env python3
"""
Download images listed in content/_meta/media-inventory.csv into the
local /assets/images/<post_slug>/<basename>/ tree, ready for optimize-assets.sh.

Idempotent:
  - Skips if images/<post_slug>/<basename> is already on disk
  - Skips if assets/images/optimized/<suggested_filename> already exists
    (image was already converted in a previous run)

Run from repo root:
    python3 scripts/download-media-inventory.py
    python3 scripts/download-media-inventory.py --dry-run

Only rows where media_type == "image" are processed. youtube/pdf/form/script
rows are skipped.
"""
from __future__ import annotations

import argparse
import csv
import os
import sys
import time
import urllib.parse
import urllib.request
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parent.parent
CSV_PATH = REPO_ROOT / "content" / "_meta" / "media-inventory.csv"
ASSETS_ROOT = REPO_ROOT / "assets"
OPTIMIZED_DIR = ASSETS_ROOT / "optimized"
PACING_SECONDS = 0.25
TIMEOUT_SECONDS = 60
USER_AGENT = "Mozilla/5.0 (eeiblog-migration; +https://eeiblog.com)"


def url_basename(url: str) -> str:
    """Extract the filename from a URL: strip query, take last path segment, URL-decode."""
    parsed = urllib.parse.urlparse(url)
    last = parsed.path.rsplit("/", 1)[-1]
    return urllib.parse.unquote(last)


def asset_folder(post_slug: str) -> str:
    """Map a CSV post_slug to its images/ folder name.

    The inventory uses `<slug>-draft` for in-progress posts, but the
    `suggested_filename` column drops the `-draft` suffix so optimized
    output can land on its final WP filename. Mirror that here so both
    halves of the pipeline agree on folder naming.
    """
    if post_slug.endswith("-draft"):
        return post_slug[: -len("-draft")]
    return post_slug


def download(url: str, dest: Path) -> None:
    req = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    dest.parent.mkdir(parents=True, exist_ok=True)
    tmp = dest.with_suffix(dest.suffix + ".part")
    with urllib.request.urlopen(req, timeout=TIMEOUT_SECONDS) as resp, open(tmp, "wb") as f:
        while True:
            chunk = resp.read(64 * 1024)
            if not chunk:
                break
            f.write(chunk)
    tmp.rename(dest)


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--dry-run", action="store_true")
    ap.add_argument("--csv", default=str(CSV_PATH))
    args = ap.parse_args()

    csv_path = Path(args.csv)
    if not csv_path.is_file():
        print(f"ERROR: {csv_path} not found", file=sys.stderr)
        return 1

    downloaded = 0
    on_disk = 0
    already_optimized = 0
    failed: list[tuple[str, str]] = []
    skipped_non_image = 0

    with open(csv_path, newline="") as f:
        rdr = csv.DictReader(f)
        for row in rdr:
            if row.get("media_type") != "image":
                skipped_non_image += 1
                continue

            post_slug = row["post_slug"].strip()
            url = row["source_url"].strip()
            suggested = row["suggested_filename"].strip()

            if not post_slug or not url:
                continue

            basename = url_basename(url)
            if not basename:
                print(f"[skip] empty basename for {url}", file=sys.stderr)
                continue

            target = ASSETS_ROOT / asset_folder(post_slug) / basename

            if target.is_file() and target.stat().st_size > 0:
                on_disk += 1
                continue

            # If the optimized output already exists, no need to re-download
            # the source — optimize-assets.sh's idempotency check would just
            # skip it anyway.
            if suggested and (OPTIMIZED_DIR / suggested).is_file():
                already_optimized += 1
                continue

            rel = target.relative_to(REPO_ROOT)
            if args.dry_run:
                print(f"[plan] {rel}  <-  {url}")
                downloaded += 1
                continue

            print(f"[get ] {rel}")
            try:
                download(url, target)
                downloaded += 1
                time.sleep(PACING_SECONDS)
            except Exception as e:  # noqa: BLE001 — surface anything as a failure
                failed.append((str(rel), str(e)))
                print(f"[fail] {rel}  ({e})", file=sys.stderr)
                # Clean up partial file
                tmp = target.with_suffix(target.suffix + ".part")
                if tmp.exists():
                    tmp.unlink()

    print()
    print("Done." if not args.dry_run else "Dry run.")
    print(f"  Downloaded:        {downloaded}")
    print(f"  Already on disk:   {on_disk}")
    print(f"  Already optimized: {already_optimized}")
    print(f"  Failed:            {len(failed)}")
    print(f"  Non-image rows:    {skipped_non_image}")
    if failed:
        print("\nFailures:")
        for rel, err in failed[:20]:
            print(f"  - {rel}: {err}")
        if len(failed) > 20:
            print(f"  ... and {len(failed) - 20} more")
        return 2
    return 0


if __name__ == "__main__":
    sys.exit(main())
