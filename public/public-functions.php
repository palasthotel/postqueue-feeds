<?php

defined( 'WPINC' ) || exit;

use PostqueueFeeds\Plugin;

/**
 * The plugin instance.
 *
 * Called Plugin::getInstance() until now, a method that has never existed - the class
 * declares get_instance(). Every call to this function ended in a fatal error.
 *
 * @return \PostqueueFeeds\Plugin
 */
function postqueue_feeds_get_plugin() {
	return Plugin::get_instance();
}
