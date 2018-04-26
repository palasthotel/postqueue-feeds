<?php

use PostqueueFeeds\Plugin;

/**
 * @return \PostqueueFeeds\Plugin
 */
function postqueue_feeds_get_plugin() {
	return Plugin::getInstance();
}
