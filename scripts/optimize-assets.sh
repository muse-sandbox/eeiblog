#!/usr/bin/env bash
# Optimize images in /assets/ for WordPress upload:
#   1. Convert PNG/JPG -> WebP (quality 82)
#   2. Sanitize filenames (lowercase, '+'/space -> '-', strip duplicate dashes)
#   3. Output to assets/optimized/ as a flat directory ready to drag into WP Media
#   4. Write a manifest mapping <post-slug>/<orig-name> -> <new-name>.webp
#
# Run from repo root:
#   bash scripts/optimize-assets.sh
#
# Idempotent: skips files where the .webp already exists and is newer than the source.
# Requires `cwebp` (brew install webp).

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ASSETS_ROOT="$REPO_ROOT/assets"
OUT_DIR="$ASSETS_ROOT/optimized"
MANIFEST="$ASSETS_ROOT/optimized-manifest.csv"
QUALITY=82

if ! command -v cwebp >/dev/null 2>&1; then
  echo "ERROR: cwebp not found. Install with: brew install webp" >&2
  exit 1
fi

mkdir -p "$OUT_DIR"

# Lowercase, replace +, spaces, _ with -, collapse repeats, strip non-alphanum/dot/dash
sanitize() {
  local name="$1"
  # Strip extension first so we can rebuild it
  local base="${name%.*}"
  base="$(echo "$base" \
    | tr '[:upper:]' '[:lower:]' \
    | sed -E 's/[+ _]+/-/g; s/[^a-z0-9.-]+/-/g; s/-+/-/g; s/^-//; s/-$//')"
  echo "$base"
}

# (Re)write manifest header
echo "post_slug,original_filename,optimized_filename,original_bytes,optimized_bytes,ratio_pct" > "$MANIFEST"

converted=0
skipped=0
failed=0
total_orig=0
total_new=0

# Walk every jpg/png under assets/, excluding our own output dir
while IFS= read -r src; do
  rel="${src#$ASSETS_ROOT/}"          # e.g. eebandbook1/EE+Band+1.png
  post_slug="${rel%%/*}"              # eebandbook1
  orig_name="$(basename "$src")"      # EE+Band+1.png
  base_sanitized="$(sanitize "$orig_name")"
  # Prefix with post slug to avoid name collisions in the flat output dir
  out_name="${post_slug}-${base_sanitized}.webp"
  out_path="$OUT_DIR/$out_name"

  if [ -f "$out_path" ] && [ "$out_path" -nt "$src" ]; then
    skipped=$((skipped + 1))
    # Still record in manifest so the mapping stays complete
    orig_bytes=$(stat -f%z "$src")
    new_bytes=$(stat -f%z "$out_path")
    ratio=$(awk -v a="$new_bytes" -v b="$orig_bytes" 'BEGIN{ if (b==0) print 0; else printf "%.1f", (a/b)*100 }')
    echo "$post_slug,$orig_name,$out_name,$orig_bytes,$new_bytes,$ratio" >> "$MANIFEST"
    total_orig=$((total_orig + orig_bytes))
    total_new=$((total_new + new_bytes))
    continue
  fi

  if cwebp -quiet -q "$QUALITY" -metadata none "$src" -o "$out_path" 2>/dev/null; then
    orig_bytes=$(stat -f%z "$src")
    new_bytes=$(stat -f%z "$out_path")
    ratio=$(awk -v a="$new_bytes" -v b="$orig_bytes" 'BEGIN{ if (b==0) print 0; else printf "%.1f", (a/b)*100 }')
    echo "[ok  ] $rel  ->  $out_name  (${ratio}% of original)"
    echo "$post_slug,$orig_name,$out_name,$orig_bytes,$new_bytes,$ratio" >> "$MANIFEST"
    converted=$((converted + 1))
    total_orig=$((total_orig + orig_bytes))
    total_new=$((total_new + new_bytes))
  else
    echo "[fail] $rel" >&2
    failed=$((failed + 1))
  fi
done < <(find "$ASSETS_ROOT" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" \) -not -path "$OUT_DIR/*")

echo ""
echo "Done."
echo "  Converted: $converted"
echo "  Skipped:   $skipped (already up-to-date)"
echo "  Failed:    $failed"
if [ "$total_orig" -gt 0 ]; then
  saved_pct=$(awk -v a="$total_new" -v b="$total_orig" 'BEGIN{ printf "%.1f", (1 - a/b)*100 }')
  human() { awk -v n="$1" 'BEGIN{ s="BKMGT"; i=1; while (n>=1024 && i<5){n/=1024; i++} printf "%.1f%s", n, substr(s,i,1) }'; }
  echo "  Total:     $(human $total_orig) -> $(human $total_new)  (-${saved_pct}%)"
fi
echo ""
echo "Output:   $OUT_DIR/"
echo "Manifest: $MANIFEST"
echo ""
echo "Next: drag the contents of $OUT_DIR/ into WP Media → Add New."
