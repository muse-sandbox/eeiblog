#!/usr/bin/env python3
"""
Bulk image URL replacement helper.

Reads a post (slug + content) and a media library snapshot, replaces
Squarespace CDN URLs (`https://i0.wp.com/.../squarespace-cdn.com/.../<filename>.<ext>?ssl=1`)
with their WordPress webp counterparts following Roman's naming convention:

    <post-slug>-<sanitized-name>.webp

where post-slug strips a trailing `-draft` (or `-draft-2`) and sanitized-name
is the lowercase, hyphen-separated form of the original Squarespace filename.

Usage (as a library):
    from bulk_replace_image_urls import process_post
    new_content, replaced, missing = process_post(post_slug, content, media_index)

Usage (CLI):
    python3 bulk-replace-image-urls.py <media.json> <post.json>
where post.json = {"id": <int>, "slug": "<slug>", "content": "<html>"}.
Outputs to stdout: {"new_content": ..., "replaced": [...], "missing": [...]}
"""

from __future__ import annotations

import json
import re
import sys
import unicodedata
from urllib.parse import unquote


SQUARESPACE_CDN_RE = re.compile(
    r'https://i0\.wp\.com/images\.squarespace-cdn\.com/[^"\s\']+',
    re.IGNORECASE,
)


def strip_draft_suffix(slug: str) -> str:
    """Roman's webp naming uses the original (clean) slug, without `-draft` suffixes."""
    for suffix in ("-draft-2", "-draft"):
        if slug.endswith(suffix):
            return slug[: -len(suffix)]
    return slug


def sanitize_name(name: str) -> str:
    """Apply Roman's image-name sanitization.

    Examples (verified against the WP media library):
        EE+Both+Books.jpg                            -> ee-both-books
        EEi%2BFingering%2B2%2BCello.png              -> eei-fingering-2-cello
        Screen+Shot+2020-08-04+at+3.48.30+PM.png     -> screen-shot-2020-08-04-at-3-48-30-pm
        EE_i_WHITE                                   -> ee-i-white   (also seen)
        eebandtalklogo.jpg                           -> eebandtalklogo
        EEi_Blog_4+(1).png                           -> eei-blog-4   (drops the (1) marker)
    """
    name = unquote(name)             # %2B -> +, %20 -> space, etc.
    name = name.replace("+", " ")    # Squarespace uses + for spaces
    # Drop file extension
    if "." in name:
        name = name.rsplit(".", 1)[0]
    # Drop trailing "(N)" disambiguators that Squarespace adds for duplicate uploads.
    name = re.sub(r"\s*\(\d+\)\s*$", "", name)
    # Normalize Unicode (NFKD) and strip non-ASCII
    name = unicodedata.normalize("NFKD", name)
    name = name.encode("ascii", "ignore").decode("ascii")
    name = name.lower()
    # Collapse anything that isn't [a-z0-9] to a hyphen
    name = re.sub(r"[^a-z0-9]+", "-", name)
    # Trim hyphens
    name = name.strip("-")
    return name


def derive_filename_from_url(url: str) -> str:
    """Extract the last path segment from a Squarespace CDN URL."""
    # url like: https://i0.wp.com/images.squarespace-cdn.com/content/v1/.../FILE.png?ssl=1
    path = url.split("?", 1)[0]
    return path.rsplit("/", 1)[-1]


def build_media_index(media_list: list[dict]) -> dict[str, str]:
    """Map sanitized webp filename (without extension) -> full WP source_url."""
    index: dict[str, str] = {}
    for item in media_list:
        url = item.get("source_url", "")
        if not url:
            continue
        # Extract filename without extension and any -123 numeric suffix
        path = url.rsplit("/", 1)[-1]
        filename = path.rsplit(".", 1)[0]
        # Decode HTML entities WordPress may inject in titles (we use source_url so usually clean)
        index[filename] = url
        # Also index the form WITHOUT a trailing -<digits> in case WP added a uniqueness suffix.
        # E.g. eei-overview-1-ee2025-800x800-1.webp matches expected eei-overview-1-ee2025-800x800.
        m = re.match(r"^(.*)-(\d+)$", filename)
        if m:
            stem = m.group(1)
            index.setdefault(stem, url)
    return index


def process_post(
    post_slug: str,
    content: str,
    media_index: dict[str, str],
) -> tuple[str, list[dict], list[dict]]:
    """Find Squarespace URLs in content and replace with WP webp where available.

    Returns: (new_content, replaced[], missing[])
    Each replaced/missing dict: {"orig_url", "expected_webp", "wp_url" (replaced only)}.
    """
    clean_slug = strip_draft_suffix(post_slug)
    replaced: list[dict] = []
    missing: list[dict] = []

    def repl(match: re.Match) -> str:
        orig_url = match.group(0)
        # Strip any trailing punctuation that the regex may have grabbed
        orig_url = orig_url.rstrip('"\'<>')
        filename = derive_filename_from_url(orig_url)
        sanitized = sanitize_name(filename)
        expected = f"{clean_slug}-{sanitized}"
        wp_url = media_index.get(expected)
        if wp_url:
            replaced.append({
                "orig_url": orig_url,
                "expected_webp": expected + ".webp",
                "wp_url": wp_url,
            })
            return wp_url
        else:
            missing.append({
                "orig_url": orig_url,
                "expected_webp": expected + ".webp",
                "filename": filename,
            })
            return orig_url

    new_content = SQUARESPACE_CDN_RE.sub(repl, content)
    return new_content, replaced, missing


def main() -> None:
    if len(sys.argv) != 3:
        print("usage: bulk-replace-image-urls.py <media.json> <post.json>", file=sys.stderr)
        sys.exit(2)

    media_path, post_path = sys.argv[1], sys.argv[2]
    with open(media_path, "r", encoding="utf-8") as f:
        media_list = json.load(f)
    with open(post_path, "r", encoding="utf-8") as f:
        post = json.load(f)

    index = build_media_index(media_list)
    new_content, replaced, missing = process_post(
        post["slug"], post["content"], index
    )
    json.dump(
        {
            "id": post.get("id"),
            "slug": post["slug"],
            "new_content": new_content,
            "replaced": replaced,
            "missing": missing,
            "stats": {
                "replaced_count": len(replaced),
                "missing_count": len(missing),
                "changed": new_content != post["content"],
            },
        },
        sys.stdout,
        ensure_ascii=False,
    )
    sys.stdout.write("\n")


if __name__ == "__main__":
    main()
