#!/usr/bin/env bash
# Download PDFs we still need to host on WP and place them in assets/pdfs/.
# Roman uploads them to WP Media Library afterwards (drag-drop in wp-admin),
# then runs the URL replacement to update post content with WP URLs.
#
# Usage:
#   bash scripts/download-pdfs.sh
#   bash scripts/download-pdfs.sh --include-s3   # also pull the HL S3 toolkit PDF
#   bash scripts/download-pdfs.sh --include-uploaded   # re-pull files we already uploaded (for backup)

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
DEST="$REPO_ROOT/assets/pdfs"

mkdir -p "$DEST"

INCLUDE_S3=false
INCLUDE_UPLOADED=false
for arg in "$@"; do
  case "$arg" in
    --include-s3) INCLUDE_S3=true ;;
    --include-uploaded) INCLUDE_UPLOADED=true ;;
    --help|-h)
      sed -n '2,12p' "$0"
      exit 0
      ;;
  esac
done

download() {
  local url="$1"
  local out="$2"
  local note="$3"
  if [[ -f "$DEST/$out" ]]; then
    echo "  SKIP   $out (already present)"
    return
  fi
  echo "==>     $note"
  echo "        $url"
  echo "    →   $DEST/$out"
  if curl -fL --silent --show-error -o "$DEST/$out" "$url"; then
    local size
    size=$(du -h "$DEST/$out" | cut -f1)
    echo "        OK ($size)"
  else
    echo "        FAILED — leaving partial file (if any) for inspection"
    return 1
  fi
}

echo "===================================================================="
echo " STILL TO UPLOAD — these PDFs need to be downloaded and re-hosted."
echo "===================================================================="
echo

# 1. EEi Lesson Ideas — referenced in eei-webinar-engaging-students-draft (post 196)
download \
  "https://www.eeiblog.com/s/EEi-Lesson-Ideas.pdf" \
  "eei-webinar-engaging-students-eei-lesson-ideas.pdf" \
  "EEi Lesson Ideas → eei-webinar-engaging-students post"

# 2. EEi Self-Assessment Rubrics — same post
download \
  "https://www.eeiblog.com/s/EEi-Self-Assessment-Rubrics.pdf" \
  "eei-webinar-engaging-students-eei-self-assessment-rubrics.pdf" \
  "EEi Self-Assessment Rubrics → eei-webinar-engaging-students post"

if [[ "$INCLUDE_S3" == "true" ]]; then
  echo
  echo "===================================================================="
  echo " HL S3 — Roman wants a local copy too (originally external link)."
  echo "===================================================================="
  echo
  # 3. HL Promo Toolkit 2025-2026 — used in musiciseessential-draft archive
  download \
    "https://halleonard-common.s3.amazonaws.com/websites/hlo/bin/PromoBandOrchestraToolkit2025-2026.pdf" \
    "musiciseessential-promo-band-orchestra-toolkit-2025-2026.pdf" \
    "HL Promo Band/Orchestra Toolkit 2025-2026 → musiciseessential archive"
fi

if [[ "$INCLUDE_UPLOADED" == "true" ]]; then
  echo
  echo "===================================================================="
  echo " ALREADY UPLOADED — backup copies (skipped by default)."
  echo "===================================================================="
  echo
  download \
    "https://www.eeiblog.com/s/EEi-Lesson-Plans.pdf" \
    "eeidistancelearning2-eei-lesson-plans.pdf" \
    "EEi Lesson Plans (already in WP)"
  download \
    "https://www.eeiblog.com/s/EEi-Advantage-Flyer.pdf" \
    "ee-dealer-resource-page-eei-advantage-flyer.pdf" \
    "EEi Advantage Flyer (already in WP, used by dealer + clinician)"
  download \
    "https://www.eeiblog.com/s/EEi-Brochure-sm.pdf" \
    "ee-dealer-resource-page-eei-brochure-sm.pdf" \
    "EEi Brochure (already in WP, used by dealer + clinician)"
fi

echo
echo "===================================================================="
echo " DONE. Files in: $DEST"
echo
echo " Next steps:"
echo "   1. wp-admin → Media → Add New → drag-drop the *.pdf files above"
echo "   2. After upload, send Claude the WP media URLs OR just say 'pdfs uploaded'"
echo "      and Claude will replace post content with WP URLs."
echo "===================================================================="
ls -lh "$DEST"