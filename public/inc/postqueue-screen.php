<?php

namespace PostqueueFeeds;

defined( 'WPINC' ) || exit;

/**
 * A Feed column on Postqueue's Postqueues screen, under Tools.
 *
 * Postqueue knows nothing about feeds and does not need to: its overview is an ordinary
 * WP_List_Table, so the column goes in through the same two hooks a column is added to
 * any core list table with. If this plugin is not installed, the column is simply not
 * there - nothing in Postqueue has to check for it.
 */
class PostqueueScreen {

	/**
	 * Column id, prefixed because the key also names the Screen Options checkbox and the
	 * generated CSS class in Postqueue's table.
	 */
	const COLUMN = 'postqueue_feed';

	private Plugin $plugin;

	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		// admin_init rather than plugin load: the screen id is derived from a Postqueue
		// constant, and the order in which the two plugins load is not guaranteed.
		// admin_init runs in wp-admin/admin.php before load-{$page_hook}, which is where
		// the list table is built and the column filters are read.
		add_action( 'admin_init', array( $this, 'register' ) );
	}

	public function register(): void {
		$screen_id = $this->screen_id();

		if ( null === $screen_id ) {
			return;
		}

		add_filter( "manage_{$screen_id}_columns", array( $this, 'add_column' ) );
		add_filter( "manage_{$screen_id}_custom_column", array( $this, 'render_column' ), 10, 3 );
	}

	/**
	 * The screen the Postqueues overview lives on, or null without Postqueue.
	 */
	private function screen_id(): ?string {
		if ( ! class_exists( '\Postqueue\Editor' ) ) {
			return null;
		}

		// Built from Postqueue's own constant instead of a copied string, so a renamed
		// page takes the column along rather than silently dropping it.
		return 'tools_page_' . \Postqueue\Editor::PAGE_SLUG;
	}

	/**
	 * @param array $columns
	 * @return array
	 */
	public function add_column( $columns ) {
		if ( ! is_array( $columns ) ) {
			return $columns;
		}

		$columns[ self::COLUMN ] = _x( 'Feed', 'postqueues screen', 'postqueue-feeds' );

		return $columns;
	}

	/**
	 * @param string $content
	 * @param string $column
	 * @param array  $item     the queue row: id, name, slug, items
	 * @return string
	 */
	public function render_column( $content, $column, $item ) {
		if ( self::COLUMN !== $column ) {
			return $content;
		}

		$slug = is_array( $item ) && isset( $item['slug'] ) ? (string) $item['slug'] : '';

		if ( '' === $slug ) {
			return $content;
		}

		// get_feed_link() gives the pretty address where permalinks are enabled and
		// ?feed=<slug> where they are not, which is exactly the pair this plugin serves.
		$url = get_feed_link( $slug );

		// The path, not the whole address: within one site the host adds nothing and the
		// full URL does not fit the column. The link still carries it, so it can be
		// opened or copied.
		return sprintf(
			'<a href="%s"><code>%s</code></a>',
			esc_url( $url ),
			esc_html( wp_make_link_relative( $url ) )
		);
	}
}
