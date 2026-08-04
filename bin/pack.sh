#!/bin/sh
# Stages public/ in build/postqueue-feeds/ - exactly what is deployed to WordPress.org -
# and zips it to postqueue-feeds.zip in the project root.
#
# The build directory is left in place on purpose: the release workflow rsyncs from it
# into the SVN checkout, so the zip and the SVN trunk are byte-identical.
#
# The plugin is plain PHP: nothing to compile, no dependencies to install.
set -e

PLUGIN_SLUG="postqueue-feeds"
SCRIPT_DIR=$(cd "$(dirname "$0")" && pwd)
PROJECT_PATH=$(cd "$SCRIPT_DIR/.." && pwd)
BUILD_PATH="$PROJECT_PATH/build"
DEST_PATH="$BUILD_PATH/$PLUGIN_SLUG"

echo "Generating build directory..."
rm -rf "$BUILD_PATH"
mkdir -p "$DEST_PATH"

if [ ! -f "$PROJECT_PATH/public/${PLUGIN_SLUG}-plugin.php" ]; then
  echo "public/${PLUGIN_SLUG}-plugin.php is missing - is this the right directory?" >&2
  exit 1
fi

echo "Syncing files..."
# -L resolves symlinks into real files. wordpress.org discards symlinks when it builds
# the download, and SVN refuses to put one where it versions a regular file.
rsync -rL "$PROJECT_PATH/public/" "$DEST_PATH/"

echo "Generating zip file..."
cd "$BUILD_PATH" || exit 1
zip -q -r "${PLUGIN_SLUG}.zip" "$PLUGIN_SLUG/"
mv "${PLUGIN_SLUG}.zip" "$PROJECT_PATH/"

cd "$PROJECT_PATH" || exit 1
echo "${PLUGIN_SLUG}.zip file generated!"
echo "Build done!"
