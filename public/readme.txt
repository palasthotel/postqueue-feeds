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

After adding a postqueue, visit **Settings › Permalinks** once. Saving there rebuilds the rewrite rules, which is what makes the pretty address work.

= Templates =

The feed is rendered from `template/postqueue-feed-rss2.php`, a copy of the RSS2 template WordPress ships. A theme can replace it by putting a file of the same name in a `plugin-parts` folder, or in any sub folder of one:

* `your-theme/plugin-parts/postqueue-feed-rss2.php`
* `your-theme/plugin-parts/anything/postqueue-feed-rss2.php`

Child themes take precedence over parent themes.

== Installation ==

1. Install and activate [Postqueue](https://wordpress.org/plugins/postqueue/) first - without it this plugin has nothing to publish.
2. Install Postqueue Feeds through **Plugins › Add New**, or upload it to `/wp-content/plugins/`.
3. Activate it through the **Plugins** menu.
4. Visit **Settings › Permalinks** and save, so the feed addresses work.

== Frequently Asked Questions ==

= Why does my feed answer with a 404? =

The rewrite rules are built once and then cached. Adding a postqueue does not rebuild them, so a new queue's `/feed/<slug>/` address is unknown until they are. Visiting **Settings › Permalinks** and saving is enough. `?feed=<slug>` works without it.

= Can a queue's feed still be reached as my-queue.xml? =

No, that address is gone as of 2.0.0. The rule behind it matched every `.xml` address on the site - `wp-sitemap.xml` among them - and answered those with a feed instead of their own content. Use `/feed/<slug>/`.

= Which posts appear in a feed? =

Every published post in the queue, in the queue's order, with no paging limit. Scheduled posts and drafts are left out.

== Changelog ==

= 1.0 =
* First release

== Upgrade Notice ==

= 2.0.0 =
Fixes wp-sitemap.xml, which this plugin has been answering with a feed ever since WordPress 5.5 introduced it. In exchange the address my-queue.xml is gone - use /feed/my-queue/ instead. Visit Settings › Permalinks and save after updating.
