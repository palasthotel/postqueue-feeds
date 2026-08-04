<?php

namespace PostqueueFeeds;

defined( 'WPINC' ) || exit;

class Feed {

	private Plugin $plugin;

	/**
	 * Theme sub directories that may hold a feed template, resolved once per request.
	 */
	private ?array $sub_dirs = null;

	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Called by init action
	 */
	public function init(): void {
		$this->add_the_feeds();
	}

	/**
	 * Add the feeds to the WordPress magic
	 *
	 * One do_feed_<slug> action per postqueue. Registering them costs a query for the
	 * list of queues, so it is skipped unless this request asks for a feed at all -
	 * without that check every single page view of the site paid for it.
	 */
	public function add_the_feeds(): void {
		if ( ! $this->is_feed_request() ) {
			return;
		}

		foreach ( $this->get_postqueues() as $postqueue ) {
			add_action( 'do_feed_' . $postqueue->slug, array( $this, 'add_feed' ), 10, 2 );
		}
	}

	/**
	 * Whether this request asks for a feed.
	 *
	 * do_feed() runs on template_redirect, well after init, but at init the query
	 * variables are not parsed yet - so the raw request is what there is to go by. A
	 * false positive only means the queues are looked up needlessly, while a false
	 * negative would mean no feed at all, hence the deliberately generous match.
	 */
	private function is_feed_request(): bool {
		if ( ! empty( $_GET['feed'] ) ) {
			return true;
		}

		$path = isset( $_SERVER['REQUEST_URI'] )
			? wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH )
			: '';

		return is_string( $path ) && 1 === preg_match( '#(^|/)feed(/|$)#', $path );
	}

	/**
	 * Add the feed to the WordPress magic
	 *
	 * @param bool   $is_comment_feed
	 * @param string $feedname
	 */
	public function add_feed( $is_comment_feed, $feedname ): void {
		// modify query
		$this->modify_query( (string) $feedname );
		load_template( $this->get_template_path( Plugin::TEMPLATE_FEED ) );
		// reset main query
		wp_reset_query();
	}

	/**
	 * Modifies the main query by replacing it with postqueue posts
	 */
	public function modify_query( string $postqueue_slug ): void {
		if ( ! class_exists( '\Postqueue\Store' ) ) {
			return;
		}

		$store = new \Postqueue\Store();
		$items = $store->get_queue_by_slug( $postqueue_slug );

		if ( count( $items ) < 1 ) {
			return;
		}

		$post_ids = array();
		foreach ( $items as $item ) {
			$post_ids[] = (int) $item->post_id;
		}

		$query_args = array(
			'post__in'            => $post_ids,
			'post_status'         => 'publish',
			'orderby'             => 'post__in',
			'post_type'           => 'any',
			'posts_per_page'      => -1,
			'nopaging'            => true,
			'ignore_sticky_posts' => 1,
		);

		query_posts( $query_args );
	}

	/**
	 * Getter for all stored postqueues
	 *
	 * @return array
	 */
	public function get_postqueues(): array {
		if ( ! class_exists( '\Postqueue\Store' ) ) {
			return array();
		}

		$store = new \Postqueue\Store();

		return $store->get_queues();
	}

	/**
	 * Look for an existing template path
	 */
	public function get_template_path( string $template ): string {
		// theme or child theme
		$overridden_template = locate_template( $this->get_template_dirs( $template ) );
		if ( $overridden_template ) {
			return $overridden_template;
		}

		// parent theme
		foreach ( $this->get_template_dirs( $template ) as $path ) {
			if ( is_file( get_template_directory() . "/$path" ) ) {
				return get_template_directory() . "/$path";
			}
		}

		return $this->plugin->dir . 'template/' . $template;
	}

	/**
	 * get array of possible template files in theme
	 *
	 * @return array
	 */
	public function get_template_dirs( string $template ): array {
		$dirs = array(
			Plugin::THEME_FOLDER . '/' . $template,
		);
		foreach ( $this->get_sub_dirs() as $sub ) {
			$dirs[] = $sub . '/' . $template;
		}

		return $dirs;
	}

	/**
	 * paths for locate_template
	 *
	 * @return array
	 */
	public function get_sub_dirs(): array {
		if ( null === $this->sub_dirs ) {
			$this->sub_dirs = array();
			// glob() returns false on failure, which array_filter would choke on.
			$paths = glob( get_template_directory() . '/' . Plugin::THEME_FOLDER . '/*' );
			foreach ( array_filter( is_array( $paths ) ? $paths : array(), 'is_dir' ) as $dir ) {
				$this->sub_dirs[] = str_replace( get_template_directory() . '/', '', $dir );
			}
		}

		return $this->sub_dirs;
	}
}
