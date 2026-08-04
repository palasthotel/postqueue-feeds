#!/usr/bin/env bash
# Aborts a release when the version carriers disagree, before anything is published.
set -euo pipefail

VERSION="${VERSION:-}"

if [[ -z "$VERSION" ]]; then
  echo "ERROR: VERSION ist nicht gesetzt (z.B. VERSION=2.0.1)" >&2
  exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

# 1) version.txt - what release-please bumps
TXT_VERSION="$(head -n1 "$ROOT_DIR/version.txt" | tr -d '[:space:]')"
if [[ -z "$TXT_VERSION" ]]; then
  echo "ERROR: version.txt ist leer oder fehlt" >&2
  exit 1
fi

# 2) readme.txt Stable tag
README_VERSION="$(grep -E '^Stable tag:' "$ROOT_DIR/public/readme.txt" | head -n1 | sed -E 's/^Stable tag:[[:space:]]*//')"
if [[ -z "$README_VERSION" ]]; then
  echo "ERROR: Konnte 'Stable tag:' nicht in public/readme.txt finden" >&2
  exit 1
fi

# 3) the plugin header
PLUGIN_VERSION="$(grep -E '^[[:space:]]*\*?[[:space:]]*Version:[[:space:]]*[0-9]+\.[0-9]+\.[0-9]+' "$ROOT_DIR/public/postqueue-feeds-plugin.php" \
  | head -n1 \
  | sed -E 's/.*Version:[[:space:]]*([0-9]+\.[0-9]+\.[0-9]+).*/\1/')"

if [[ -z "$PLUGIN_VERSION" ]]; then
  echo "ERROR: Konnte 'Version:' nicht in public/postqueue-feeds-plugin.php finden" >&2
  exit 1
fi

# 4) the dev wrapper, so a developer does not see a stale version in wp-admin
DEV_VERSION="$(grep -E '^[[:space:]]*\*?[[:space:]]*Version:[[:space:]]*[0-9]+\.[0-9]+\.[0-9]+' "$ROOT_DIR/postqueue-feeds-dev.php" \
  | head -n1 \
  | sed -E 's/.*Version:[[:space:]]*([0-9]+\.[0-9]+\.[0-9]+).*/\1/')"

fail=0

check_eq () {
  local label="$1"
  local got="$2"
  if [[ "$got" != "$VERSION" ]]; then
    echo "ERROR: ${label} ist $got, erwartet $VERSION" >&2
    fail=1
  else
    echo "OK: ${label} == $VERSION"
  fi
}

check_eq "version.txt" "$TXT_VERSION"
check_eq "readme.txt Stable tag" "$README_VERSION"
check_eq "Plugin-Header Version" "$PLUGIN_VERSION"
check_eq "DEV-Wrapper Version" "$DEV_VERSION"

if [[ "$fail" -ne 0 ]]; then
  echo "Release-Version-Check fehlgeschlagen." >&2
  exit 1
fi

echo "Alle Versionen passen ✅"
