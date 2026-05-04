#!/usr/bin/env python3
"""
Fetch historical publication dates from the Wayback Machine for all migrated posts.

For each MD file in content/posts/**/*.md, reads `wp_id`, `slug`, and `source.origin_url`,
queries Wayback CDX API for the earliest snapshot of that URL, and writes the result
to .tmp/historical-dates.csv.

Usage:
  python3 scripts/fetch-historical-dates.py

Output (.tmp/historical-dates.csv):
  wp_id,slug,origin_url,earliest_snapshot_date,status

`status` is one of: ok | not-found | error.

Then I (Claude) read the CSV and bulk-update post dates via the WP MCP.
"""
from __future__ import annotations

import csv
import json
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path

REPO = Path(__file__).resolve().parent.parent
POSTS_DIR = REPO / "content" / "posts"
OUTPUT = REPO / ".tmp" / "historical-dates.csv"
CDX_URL = "http://web.archive.org/cdx/search/cdx"
USER_AGENT = "eeiblog-migration-bot/1.0 (contact: r.kislenok@mu.se)"
DELAY = 0.7  # be polite to archive.org


def parse_yaml_frontmatter(text: str) -> dict | None:
    """Tiny YAML parser for the fields we need: wp_id, slug, source.origin_url."""
    if not text.startswith("---"):
        return None
    try:
        end = text.index("\n---", 3)
    except ValueError:
        return None
    block = text[3:end]
    out: dict = {}
    in_source = False
    for line in block.splitlines():
        stripped = line.strip()
        if not stripped or stripped.startswith("#"):
            continue
        if line.startswith("source:"):
            # could be inline `source: {origin: squarespace, origin_url: "..."}`
            if "{" in line:
                inner = line.split("{", 1)[1].rstrip()
                if inner.endswith("}"):
                    inner = inner[:-1]
                for pair in inner.split(","):
                    if ":" in pair:
                        k, v = pair.split(":", 1)
                        out[f"source.{k.strip()}"] = v.strip().strip('"')
            else:
                in_source = True
            continue
        if in_source:
            if line.startswith("  ") and ":" in line:
                k, v = line.strip().split(":", 1)
                out[f"source.{k.strip()}"] = v.strip().strip('"')
                continue
            in_source = False
        if ":" in line and not line.startswith(" "):
            k, v = line.split(":", 1)
            out[k.strip()] = v.strip().strip('"')
    return out


def query_cdx(url: str) -> str | None:
    """Return earliest Wayback snapshot timestamp (YYYYMMDDhhmmss) or None."""
    params = {
        "url": url,
        "output": "json",
        "limit": "1",
        "filter": "statuscode:200",
        "from": "20180101",  # Squarespace blog earliest content
    }
    full = f"{CDX_URL}?{urllib.parse.urlencode(params)}"
    req = urllib.request.Request(full, headers={"User-Agent": USER_AGENT})
    try:
        with urllib.request.urlopen(req, timeout=20) as r:
            data = json.loads(r.read())
    except urllib.error.HTTPError as e:
        return f"error:HTTP{e.code}"
    except Exception as e:
        return f"error:{type(e).__name__}"
    if len(data) < 2:
        return None  # not-found
    # data[0] is header, data[1+] is rows
    timestamp = data[1][1]  # second column = timestamp
    return timestamp


def ts_to_iso(ts: str) -> str:
    """Convert YYYYMMDDhhmmss to YYYY-MM-DDTHH:MM:SS."""
    return f"{ts[:4]}-{ts[4:6]}-{ts[6:8]}T{ts[8:10]}:{ts[10:12]}:{ts[12:14]}"


def main():
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    md_files = sorted(POSTS_DIR.glob("**/*.md"))
    print(f"Scanning {len(md_files)} MD files...", file=sys.stderr)
    rows = []
    for i, md in enumerate(md_files, 1):
        text = md.read_text(encoding="utf-8")
        meta = parse_yaml_frontmatter(text)
        if not meta:
            continue
        wp_id = meta.get("wp_id")
        slug = meta.get("slug", "").replace("-draft", "")
        origin = meta.get("source.origin_url")
        if not wp_id or not origin:
            continue
        # strip protocol + www for cdx
        clean = origin.replace("https://www.", "").replace("https://", "").replace("http://", "")
        print(f"  [{i:3}/{len(md_files)}] {clean} ...", end=" ", file=sys.stderr, flush=True)
        ts = query_cdx(clean)
        if ts and not ts.startswith("error"):
            iso = ts_to_iso(ts)
            status = "ok"
            print(iso, file=sys.stderr)
        elif ts is None:
            iso = ""
            status = "not-found"
            print("not-found", file=sys.stderr)
        else:
            iso = ""
            status = ts  # error:HTTPxxx
            print(ts, file=sys.stderr)
        rows.append({
            "wp_id": wp_id,
            "slug": slug,
            "origin_url": origin,
            "earliest_snapshot_date": iso,
            "status": status,
        })
        time.sleep(DELAY)

    with OUTPUT.open("w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=["wp_id", "slug", "origin_url", "earliest_snapshot_date", "status"])
        w.writeheader()
        w.writerows(rows)
    print(f"\nWrote {len(rows)} rows to {OUTPUT}", file=sys.stderr)
    ok = sum(1 for r in rows if r["status"] == "ok")
    print(f"  ok: {ok}, not-found: {sum(1 for r in rows if r['status'] == 'not-found')}, errors: {len(rows) - ok - sum(1 for r in rows if r['status'] == 'not-found')}", file=sys.stderr)


if __name__ == "__main__":
    main()
