<?php

namespace PostqueueFeeds;

class Rewrite {

	/**
	 * Rewrite constructor
	 *
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		add_filter( 'generate_rewrite_rules', array( $this, 'add_rewrite' ) );
	}
  
  /**
   * Add rewrite rules for feeds 
   * (make http://yoursite.com/mycustomfeed.xml as well as a http://yoursite.com/feed/mycustomfeed/ work)
   * @return void
   */
  function add_rewrite( $wp_rewrite ) {
    $feed_rules = array('feed/(.+)' => 'index.php?feed=' . $wp_rewrite->preg_index(1), '(.+).xml' => 'index.php?feed=' . $wp_rewrite->preg_index(1));
    $wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
  }
}