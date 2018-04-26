<?php

namespace PostqueueFeeds;

class Feed {

	/**
	 * Feed constructor
	 *
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->sub_dirs = null;
		$this->postqueue = null;
		add_action( 'init', array( $this, 'init' ) );
	}
  
  /**
   * Called by init action
   * @return void
   */
  function init() {
    $this->add_the_feeds();
  }
  
  /**
   * Add the feeds to the WordPress magic
   * @return void
   */
  function add_the_feeds() {
    $postqueues = $this->get_postqueues();
    foreach ( $postqueues as $postqueue ) {
      add_action( 'do_feed_' . $postqueue->slug , array( $this, 'add_feed' ), 10, 2 );
    }
  }
  
  /**
   * Add the feed to the WordPress magic
   * @return void
   */
  function add_feed( $is_comment_feed, $feedname ) {
    // modify query
    $this->modify_query( $feedname );
    load_template( $this->get_template_path( Plugin::TEMPLATE_FEED ) );
    // reset main query
    wp_reset_query();
  }
  
  /**
   * Modifies the main query by replacing it with postqueue posts
   * @return void
   */
  function modify_query( $postqueue_slug ) {
    if ( class_exists( '\Postqueue\Store' )) {
      $store  = new \Postqueue\Store();
      $queues = $store->get_queue_by_slug( $postqueue_slug );
      
      if ( count( $queues ) > 0 ) {
    		$post_ids = array();
      	foreach ( $queues as $value ) {
      		$post_ids[] = $value->post_id;
      	}
      	$query_args = array(
      		'post__in'       => $post_ids,
      		'post_status'    => 'publish',
      		'orderby'        => 'post__in',
      		'post_type'      => 'any',
      		'posts_per_page' => -1,
      		'nopaging'       => true,
      		'ignore_sticky_posts' => 1,
      	);
      	query_posts( $query_args );
    	}
    }
  }
  
  /**
   * Getter for all stores postqueues
   * @return array
   */
  function get_postqueues() {
    if ( class_exists( '\Postqueue\Store' ) ) {
      $store  = new \Postqueue\Store();
      return $store->get_queues();
    }
    return array();
  }
  
	/**
	 * Look for existing template path
	 * @return string|false
	 */
	function get_template_path( $template ) {

		// theme or child theme
		if ( $overridden_template = locate_template( $this->get_template_dirs($template) ) ) {
			return $overridden_template;
		}

		// parent theme
		foreach ( $this->get_template_dirs( $template ) as $path ){
			if( is_file( get_template_directory() . "/$path" ) ){
				return get_template_directory() . "/$path";
			}
		}

		return $this->plugin->dir . 'template/' . $template;
	}
	
	/**
	 * get array of possible template files in theme
	 * @param $template
	 *
	 * @return array
	 */
	function get_template_dirs( $template ){
		$dirs = array(
			Plugin::THEME_FOLDER . "/" . $template,
		);
		foreach ( $this->get_sub_dirs() as $sub ) {
			$dirs[] = $sub.'/'.$template;
		}
		return $dirs;
	}
	
	/**
	 * paths for locate_template
	 * @return array
	 */
	function get_sub_dirs(){
		if ( $this->sub_dirs == null ) {
			$this->sub_dirs = array();
			$dirs = array_filter( glob( get_template_directory() . '/' . Plugin::THEME_FOLDER . '/*' ), 'is_dir' );
			foreach ( $dirs as $dir ) {
				$this->sub_dirs[] = str_replace( get_template_directory() . '/', '', $dir);
			}
		}
		return $this->sub_dirs;
	}	
}