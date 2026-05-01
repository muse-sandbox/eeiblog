#!/usr/bin/env bash
# Download images from images-todo.csv into the local /assets/ tree.
#
# Run from the repo root:
#   bash scripts/download-images.sh
#
# Idempotent: skips files already present.
# After download, updates the `status` column from `pending` to `downloaded`.
# Failures keep the row as `pending` and append a note in the `notes` column.

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
CSV="$REPO_ROOT/content/_meta/images-todo.csv"
ASSETS_ROOT="$REPO_ROOT/assets"
TMP_CSV="$(mktemp)"

if [ ! -f "$CSV" ]; then
  echo "ERROR: $CSV not found" >&2
  exit 1
fi

mkdir -p "$ASSETS_ROOT"

downloaded_count=0
skipped_count=0
failed_count=0

# Copy header
head -1 "$CSV" > "$TMP_CSV"

# Process each non-comment data row
tail -n +2 "$CSV" | while IFS= read -r line; do
  # Pass through comment / blank lines unchanged
  if [[ "$line" =~ ^# ]] || [[ -z "$line" ]]; then
    echo "$line" >> "$TMP_CSV"
    continue
  fi

  # CSV columns: filename,squarespace_url,used_in_post,wp_media_id,status,notes
  # Naive comma split is fine here because none of our values contain commas
  # except `notes`, which is the LAST column. So we split at most 5 times.
  IFS=',' read -r filename url used_in_post wp_media_id status notes <<< "$line"

  target_dir="$ASSETS_ROOT/$used_in_post"
  target_file="$target_dir/$filename"

  if [ "$status" = "downloaded" ] && [ -f "$target_file" ]; then
    echo "[skip] $used_in_post/$filename already downloaded"
    skipped_count=$((skipped_count + 1))
    echo "$line" >> "$TMP_CSV"
    continue
  fi

  if [ -f "$target_file" ]; then
    echo "[skip] $used_in_post/$filename already on disk; marking downloaded"
    skipped_count=$((skipped_count + 1))
    echo "$filename,$url,$used_in_post,$wp_media_id,downloaded,$notes" >> "$TMP_CSV"
    continue
  fi

  mkdir -p "$target_dir"

  echo "[get ] $used_in_post/$filename"
  if curl -sSL --fail --max-time 60 -o "$target_file" "$url"; then
    downloaded_count=$((downloaded_count + 1))
    echo "$filename,$url,$used_in_post,$wp_media_id,downloaded,$notes" >> "$TMP_CSV"
  else
    rc=$?
    failed_count=$((failed_count + 1))
    echo "[fail] $used_in_post/$filename (curl exit $rc)"
    rm -f "$target_file"
    new_notes="$notes; download failed $(date -u +%Y-%m-%dT%H:%M:%SZ) (exit $rc)"
    echo "$filename,$url,$used_in_post,$wp_media_id,pending,$new_notes" >> "$TMP_CSV"
  fi

  # Polite pacing
  sleep 0.3
done

mv "$TMP_CSV" "$CSV"

echo ""
echo "Done."
echo "  Downloaded: $downloaded_count"
echo "  Skipped:    $skipped_count"
echo "  Failed:     $failed_count"
echo ""
echo "Files saved under: $ASSETS_ROOT/<post-slug>/"
echo ""
echo "Next steps:"
echo "  1. Review the downloads in $ASSETS_ROOT/"
echo "  2. Bulk-upload to WordPress: wp-admin → Media → Add New → drag the per-post folders in"
echo "  3. After upload, fill the wp_media_id column in $CSV"
