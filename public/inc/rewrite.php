<?php

namespace PostqueueFeeds;

defined( 'WPINC' ) || exit;

class Rewrite {

	private Plugin $plugin;

	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		// An action, not a filter: core runs it with do_action_ref_array() and hands the
		// WP_Rewrite object over by reference, so the rules are changed in place and a
		// return value would be discarded. It was hooked with add_filter() before, which
		// works - both live in the same registry - but read like the wrong contract.
		add_action( 'generate_rewrite_rules', array( $this, 'add_rewrite' ) );
	}

	/**
	 * Add rewrite rules for feeds
	 *
	 * Makes https://yoursite.com/feed/mycustomfeed/ work.
	 *
	 * There used to be a second rule here, '(.+).xml', so that a queue could also be
	 * reached as https://yoursite.com/mycustomfeed.xml. It claimed every .xml address
	 * on the site: since WordPress 5.5 that includes wp-sitemap.xml, which became a
	 * feed request for a queue named "wp-sitemap" and answered 404 - and any sitemap a
	 * different plugin publishes went the same way. Dropping it is what fixes the
	 * sitemap; /feed/<slug>/ and ?feed=<slug> remain.
	 *
	 * @param \WP_Rewrite $wp_rewrite
	 */
	public function add_rewrite( $wp_rewrite ) {
		$feed_rules = array(
			'feed/(.+)' => 'index.php?feed=' . $wp_rewrite->preg_index( 1 ),
		);

		$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
	}
}
