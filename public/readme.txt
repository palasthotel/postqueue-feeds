=== Postqueue Feeds ===
Contributors: palasthotel, janaeggebrecht
Donate link: https://palasthotel.de/
Tags: rss, feed, postqueue, syndication, curated
Requires at least: 6.6
Tested up to: 7.0.2
Requires PHP: 7.4
Requires Plugins: postqueue
Stable tag: 1.0.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

An RSS feed for every postqueue, in the order the queue is in.

== Description ==

An extension for [Postqueue](https://wordpress.org/plugins/postqueue/ "Postqueue Plugin"). For every postqueue it publishes an RSS feed containing that queue's posts, in exactly the order the queue puts them in.

Two addresses lead to a queue's feed, both using the queue's slug:

* `https://example.com/feed/my-queue/`
* `https://example.com/?feed=my-queue`

Nothing has to be set up. Every postqueue gets its feed the moment it exists - including queues added later.

Under **Tools › Postqueues**, where Postqueue lists the queues, this plugin adds a **Feed** column showing each queue's address - so it can be read off and copied where the queue is managed.

= Templates =

The feed is rendered from `template/postqueue-feed-rss2.php`, a copy of the RSS2 template WordPress ships. A theme can replace it by putting a file of the same name in a `plugin-parts` folder, or in any sub folder of one:

* `your-theme/plugin-parts/postqueue-feed-rss2.php`
* `your-theme/plugin-parts/anything/postqueue-feed-rss2.php`

Child themes take precedence over parent themes.

== Installation ==

1. Install and activate [Postqueue](https://wordpress.org/plugins/postqueue/) first - without it this plugin has nothing to publish.
2. Install Postqueue Feeds through **Plugins › Add New**, or upload it to `/wp-content/plugins/`.
3. Activate it through the **Plugins** menu. That is all: activating rebuilds the rewrite rules by itself.

== Frequently Asked Questions ==

= Do I have to save the permalink settings? =

No. Activating the plugin rebuilds the rewrite rules, and the rule covers every queue slug at once - so a postqueue created afterwards has its feed straight away. Up to version 1.0 this was necessary, which is why older instructions ask for it.

= Can a queue's feed still be reached as my-queue.xml? =

No, that address is gone as of 2.0.0. The rule behind it matched every `.xml` address on the site - `wp-sitemap.xml` among them - and answered those with a feed instead of their own content. Use `/feed/<slug>/`.

= Which posts appear in a feed? =

Every published post in the queue, in the queue's order, with no paging limit. Scheduled posts and drafts are left out.

== Changelog ==

= 1.0 =
* First release

== Upgrade Notice ==

= 2.0.0 =
Fixes wp-sitemap.xml, which this plugin has been answering with a feed ever since WordPress 5.5 introduced it. In exchange the address my-queue.xml is gone - use /feed/my-queue/ instead.
