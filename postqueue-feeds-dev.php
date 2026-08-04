<?php
/**
 * Plugin Name:       Postqueue Feeds (DEV)
 * Plugin URI:        https://github.com/palasthotel/postqueue-feeds
 * Description:       Development wrapper. Loads the plugin from public/, which is what ships to wordpress.org. Do not deploy this file.
 * Version:           0.0.0-dev
 * Requires at least: 6.6
 * Requires Plugins:  postqueue
 * Author:            Palasthotel <rezeption@palasthotel.de> (Jana Marie Eggebrecht, Edward Bock)
 * Author URI:        https://palasthotel.de
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       postqueue-feeds
 * Domain Path:       /public/languages
 */

defined( 'WPINC' ) || exit;

// The version above is deliberately not a real one and nothing syncs it. This file never
// ships, so its version means nothing - and while the release pipeline did insist that it
// matched, a decorative number could block a release. "Tested up to" is not a plugin
// header field at all; WordPress only reads that from readme.txt.

// Lets the repository be symlinked into wp-content/plugins as a whole while the plugin
// itself stays in public/, the directory bin/pack.sh packs and the release deploys.
require_once __DIR__ . '/public/postqueue-feeds-plugin.php';
