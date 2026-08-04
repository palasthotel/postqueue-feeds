<?php
/**
 * Plugin Name:       Postqueue Feeds
 * Plugin URI:        https://wordpress.org/plugins/postqueue-feeds/
 * Description:       Provides an RSS feed for every stored postqueue.
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
 * Domain Path:       /languages
 */

namespace PostqueueFeeds;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die( 'I am the son / and the heir / of a shyness that is criminally vulgar / I am the son and the heir / of nothing in particular — The Smiths' );
}

class Plugin {

	/**
	 * Domain for translation
	 */
	const DOMAIN = 'postqueue-feeds';

	/**
	 * Constants for templates in theme
	 */
	const THEME_FOLDER  = 'plugin-parts';
	const TEMPLATE_FEED = 'postqueue-feed-rss2.php';

	private static ?Plugin $instance = null;

	// Declared instead of assigned into existence: PHP 8.2 deprecates creating
	// properties on the fly, and this plugin emitted eight such notices per request.
	public string $dir;
	public string $url;
	public Feed $feed;
	public Rewrite $rewrite;

	public static function get_instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new Plugin();
		}

		return self::$instance;
	}

	private function __construct() {
		/**
		 * Base paths
		 */
		$this->dir = plugin_dir_path( __FILE__ );
		$this->url = plugin_dir_url( __FILE__ );

		// Feed class
		require_once __DIR__ . '/inc/feed.php';
		$this->feed = new Feed( $this );

		// Rewriter class
		require_once __DIR__ . '/inc/rewrite.php';
		$this->rewrite = new Rewrite( $this );
	}
}

Plugin::get_instance();
require_once __DIR__ . '/public-functions.php';
