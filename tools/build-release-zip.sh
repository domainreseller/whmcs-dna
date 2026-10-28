#!/usr/bin/env bash
# Build the WHMCS release package.
#
#   tools/build-release-zip.sh [ref] [output-dir]
#
# Archive root: the modules/ folder, ready to unpack over a WHMCS installation, plus the
# README files, DISCLAIMER and LICENSE. Only tracked files are packed and macOS
# metadata (.DS_Store, ._*) is dropped, so the package never carries OS files.
#
# Writes DomainNameAPI-WHMCS-v<version>.zip and an unversioned copy, so
# releases/latest/download/DomainNameAPI-WHMCS.zip is a stable direct link
# (the WHMCS Marketplace requires a direct download URL).
set -euo pipefail

cd "$(dirname "$0")/.."
REF="${1:-HEAD}"
OUT_DIR="${2:-dist}"

VERSION="$(git show "$REF:modules/registrars/domainnameapi/domainnameapi.php" | sed -n "s/.*return '\([0-9][0-9.]*\)'.*/\1/p" | head -1)"
[ -n "$VERSION" ] || { echo "version not found at $REF" >&2; exit 1; }

WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

git archive "$REF" modules | tar -x -C "$WORK"
find "$WORK" \( -name .DS_Store -o -name '._*' \) -delete

EXTRA=()
while IFS= read -r f; do
    git show "$REF:$f" > "$WORK/$f"
    EXTRA+=("$f")
done < <(git ls-tree --name-only "$REF" | grep -E '^(README.*\.md|DISCLAIMER\.md|LICENSE)$')

mkdir -p "$OUT_DIR"
OUT_DIR="$(cd "$OUT_DIR" && pwd)"
VERSIONED="$OUT_DIR/DomainNameAPI-WHMCS-v${VERSION}.zip"
LATEST="$OUT_DIR/DomainNameAPI-WHMCS.zip"
rm -f "$VERSIONED" "$LATEST"
(cd "$WORK" && zip -q -r -X "$VERSIONED" modules ${EXTRA[@]+"${EXTRA[@]}"})
cp "$VERSIONED" "$LATEST"

echo "$VERSIONED"
echo "$LATEST"
