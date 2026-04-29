#!/usr/bin/env bash
#
# Build script for the EEi Blog WordPress theme.
# Bumps the version in style.css and produces a distributable zip.
#
# Usage:
#   ./build.sh                # bump patch (default): 1.0.1 -> 1.0.2
#   ./build.sh patch          # same as above
#   ./build.sh minor          # 1.0.1 -> 1.1.0
#   ./build.sh major          # 1.0.1 -> 2.0.0
#   ./build.sh 1.2.3          # set explicit version
#   ./build.sh --no-bump      # build current version without bumping

set -euo pipefail

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
THEME_DIR="$SCRIPT_DIR/eeiblog-theme"
STYLE_CSS="$THEME_DIR/style.css"

if [[ ! -f "$STYLE_CSS" ]]; then
    echo "error: $STYLE_CSS not found" >&2
    exit 1
fi

BUMP="${1:-patch}"

current_version() {
    awk -F': *' '/^Version:/ { print $2; exit }' "$STYLE_CSS" | tr -d '[:space:]'
}

bump_version() {
    local current="$1" kind="$2"
    IFS=. read -r major minor patch <<< "$current"

    case "$kind" in
        major) major=$((major + 1)); minor=0; patch=0 ;;
        minor) minor=$((minor + 1)); patch=0 ;;
        patch) patch=$((patch + 1)) ;;
        *)
            if [[ "$kind" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
                echo "$kind"
                return
            fi
            echo "error: unknown bump kind '$kind' (expected major|minor|patch|X.Y.Z)" >&2
            exit 1
            ;;
    esac

    echo "$major.$minor.$patch"
}

write_version() {
    local new="$1"
    # macOS sed needs -i ''; GNU sed needs -i. Pick portable form via temp file.
    local tmp
    tmp="$(mktemp)"
    awk -v v="$new" '
        !done && /^Version:/ { print "Version: " v; done=1; next }
        { print }
    ' "$STYLE_CSS" > "$tmp"
    mv "$tmp" "$STYLE_CSS"
}

CURRENT="$(current_version)"
if [[ -z "$CURRENT" ]]; then
    echo "error: could not read Version from $STYLE_CSS" >&2
    exit 1
fi

if [[ "$BUMP" == "--no-bump" ]]; then
    NEW="$CURRENT"
    echo "Building current version: $NEW (no bump)"
else
    NEW="$(bump_version "$CURRENT" "$BUMP")"
    write_version "$NEW"
    echo "Version: $CURRENT -> $NEW"
fi

ZIP_NAME="eeiblog-theme-$NEW.zip"
ZIP_PATH="$SCRIPT_DIR/$ZIP_NAME"
rm -f "$ZIP_PATH"

( cd "$SCRIPT_DIR" && zip -rq "$ZIP_NAME" eeiblog-theme \
    -x "*.DS_Store" "*/.DS_Store" "*/node_modules/*" "*/.git/*" )

SIZE="$(du -h "$ZIP_PATH" | cut -f1)"
echo "Built: $ZIP_PATH ($SIZE)"
