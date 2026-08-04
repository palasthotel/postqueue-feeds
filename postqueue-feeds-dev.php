<?php
/**
 * Plugin Name:       Postqueue Feeds (DEV)
 * Plugin URI:        https://github.com/palasthotel/postqueue-feeds
 * Description:       Development wrapper. Loads the plugin from public/, which is what ships to wordpress.org. Do not deploy this file.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Tested up to:      7.0.2
 * Requires PHP:      7.4
 * Requires Plugins:  postqueue
 * Author:            Palasthotel <rezeption@palasthotel.de> (Jana Marie Eggebrecht, Edward Bock)
 * Author URI:        https://palasthotel.de
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       postqueue-feeds
 * Domain Path:       /public/languages
 */

defined( 'WPINC' ) || exit;

// Lets the repository be symlinked into wp-content/plugins as a whole while the plugin
// itself stays in public/, the directory bin/pack.sh packs and the release deploys.
require_once __DIR__ . '/public/postqueue-feeds-plugin.php';
