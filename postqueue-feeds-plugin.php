<?php
/**
 * Plugin Name: Postqueue Feeds
 * Description: Provides a rss feed for every stored postqueue.
 * Version: 1.0
 * Author: Palasthotel <rezeption@palasthotel.de> (Jana Marie Eggebrecht)
 * Author URI: https://palasthotel.de
 */

namespace PostqueueFeeds;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die('I am the son / and the heir / of a shyness that is criminally vulgar / I am the son and the heir / of nothing in particular — The Smiths');
}

class Plugin {

	private static $instance;

	/** @return Plugin */
	public static function get_instance() {
		if ( Plugin::$instance == null ) {
			Plugin::$instance = new Plugin();
		}
		return Plugin::$instance;
	}

	/**
	 * Domain for translation
	 */
	const DOMAIN = "postqueue-feeds";

	/**
	 * Constants for templates in theme
	 */
	const THEME_FOLDER = "plugin-parts";
	const TEMPLATE_FEED = "postqueue-feed-rss2.php";

	/**
	 * Plugin constructor
	 */
	private function __construct() {
		/**
		 * Base paths
		 */
		$this->dir = plugin_dir_path( __FILE__ );
		$this->url = plugin_dir_url( __FILE__ );

		// Feed class
		require_once dirname(__FILE__) . '/inc/feed.php';
		$this->feed = new Feed( $this );
		
		// Rewriter class
		require_once dirname(__FILE__) . '/inc/rewrite.php';
		$this->rewrite = new Rewrite( $this );
	}

}
Plugin::get_instance();
require_once dirname(__FILE__) . '/public-functions.php';