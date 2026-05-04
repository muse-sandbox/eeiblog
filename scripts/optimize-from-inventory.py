#!/usr/bin/env python3
"""
Convert images listed in content/_meta/media-inventory.csv to WebP.

Output names come straight from the CSV's `suggested_filename` column
(authoritative — content rewrites reference these exact names), so the
filename sanitization rule lives in the inventory, not in this script.

For each image row:
  source = images/<asset_folder(post_slug)>/<url_basename(source_url)>
  output = assets/images/optimized/<suggested_filename>

Idempotent: skips when the output webp is at least as new as its source.

Run from repo root:
    python3 scripts/optimize-from-inventory.py
    python3 scripts/optimize-from-inventory.py --prune     # also remove
                                                            # orphan webp files
                                                            # not referenced in CSV
    python3 scripts/optimize-from-inventory.py --quality 85

Requires `cwebp` (brew install webp).
"""
from __future__ import annotations

import argparse
import csv
import shutil
import subprocess
import sys
import urllib.parse
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parent.parent
CSV_PATH = REPO_ROOT / "content" / "_meta" / "media-inventory.csv"
ASSETS_ROOT = REPO_ROOT / "assets"
OUT_DIR = ASSETS_ROOT / "optimized"
MANIFEST = ASSETS_ROOT / "optimized-manifest.csv"


def url_basename(url: str) -> str:
    parsed = urllib.parse.urlparse(url)
    last = parsed.path.rsplit("/", 1)[-1]
    return urllib.parse.unquote(last)


def asset_folder(post_slug: str) -> str:
    if post_slug.endswith("-draft"):
        return post_slug[: -len("-draft")]
    return post_slug


def human(n: int) -> str:
    units = "BKMGT"
    i = 0
    f = float(n)
    while f >= 1024 and i < len(units) - 1:
        f /= 1024
        i += 1
    return f"{f:.1f}{units[i]}"


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--quality", type=int, default=82)
    ap.add_argument(
        "--prune",
        action="store_true",
        help="remove .webp files in assets/images/optimized/ that aren't referenced in the CSV",
    )
    ap.add_argument("--csv", default=str(CSV_PATH))
    args = ap.parse_args()

    if shutil.which("cwebp") is None:
        print("ERROR: cwebp not found. Install with: brew install webp", file=sys.stderr)
        return 1

    csv_path = Path(args.csv)
    if not csv_path.is_file():
        print(f"ERROR: {csv_path} not found", file=sys.stderr)
        return 1

    OUT_DIR.mkdir(parents=True, exist_ok=True)

    converted = 0
    skipped = 0
    missing_source = 0
    failed: list[tuple[str, str]] = []
    referenced_outputs: set[str] = set()

    manifest_rows: list[tuple[str, str, str, int, int, str]] = []
    total_orig = 0
    total_new = 0

    with open(csv_path, newline="") as f:
        rdr = csv.DictReader(f)
        for row in rdr:
            if row.get("media_type") != "image":
                continue
            post_slug = row["post_slug"].strip()
            url = row["source_url"].strip()
            sug = row["suggested_filename"].strip()
            if not (post_slug and url and sug):
                continue

            referenced_outputs.add(sug)

            src = ASSETS_ROOT / asset_folder(post_slug) / url_basename(url)
            dst = OUT_DIR / sug

            if not src.is_file():
                missing_source += 1
                print(f"[miss] no source for {sug}  (expected {src.relative_to(REPO_ROOT)})", file=sys.stderr)
                continue

            if dst.is_file() and dst.stat().st_mtime >= src.stat().st_mtime:
                skipped += 1
                orig = src.stat().st_size
                new = dst.stat().st_size
                ratio = (new / orig * 100) if orig else 0
                manifest_rows.append((post_slug, src.name, sug, orig, new, f"{ratio:.1f}"))
                total_orig += orig
                total_new += new
                continue

            r = subprocess.run(
                ["cwebp", "-quiet", "-q", str(args.quality), "-metadata", "none", str(src), "-o", str(dst)],
                capture_output=True,
            )
            if r.returncode == 0 and dst.is_file():
                orig = src.stat().st_size
                new = dst.stat().st_size
                ratio = (new / orig * 100) if orig else 0
                rel_src = src.relative_to(ASSETS_ROOT)
                print(f"[ok  ] {rel_src}  ->  {sug}  ({ratio:.1f}% of original)")
                manifest_rows.append((post_slug, src.name, sug, orig, new, f"{ratio:.1f}"))
                total_orig += orig
                total_new += new
                converted += 1
            else:
                err = r.stderr.decode("utf-8", errors="replace").strip() or f"exit {r.returncode}"
                failed.append((str(src.relative_to(REPO_ROOT)), err))
                print(f"[fail] {src.relative_to(REPO_ROOT)}: {err}", file=sys.stderr)

    # Write manifest
    with open(MANIFEST, "w", newline="") as mf:
        w = csv.writer(mf)
        w.writerow(["post_slug", "original_filename", "optimized_filename", "original_bytes", "optimized_bytes", "ratio_pct"])
        w.writerows(manifest_rows)

    pruned = 0
    if args.prune:
        for p in sorted(OUT_DIR.iterdir()):
            if p.is_file() and p.suffix == ".webp" and p.name not in referenced_outputs:
                p.unlink()
                pruned += 1
                print(f"[prune] {p.name}")

    print()
    print("Done.")
    print(f"  Converted:        {converted}")
    print(f"  Skipped:          {skipped} (already up-to-date)")
    print(f"  Missing source:   {missing_source}")
    print(f"  Failed:           {len(failed)}")
    if args.prune:
        print(f"  Pruned (orphans): {pruned}")
    if total_orig:
        saved = (1 - total_new / total_orig) * 100
        print(f"  Total:            {human(total_orig)} -> {human(total_new)}  (-{saved:.1f}%)")
    print()
    print(f"Output:   {OUT_DIR}")
    print(f"Manifest: {MANIFEST}")
    return 0 if not failed else 2


if __name__ == "__main__":
    sys.exit(main())
