# Postqueue Feeds

An RSS feed for every postqueue, in the order the queue is in.

This is an extension for [Postqueue](https://wordpress.org/plugins/postqueue/). Postqueue
lets an editor arrange posts into a hand-sorted list; this plugin publishes each of those
lists as an RSS feed, keeping the order intact.

- **wordpress.org:** https://wordpress.org/plugins/postqueue-feeds/
- **Requires:** WordPress 6.6, PHP 7.4, and the Postqueue plugin

## Feed addresses

Both use the postqueue's slug:

```
https://example.com/feed/my-queue/
https://example.com/?feed=my-queue
```

After adding a postqueue, visit **Settings › Permalinks** and save once. That rebuilds
the rewrite rules, which is what the pretty address needs. `?feed=<slug>` works without
it.

> A queue used to be reachable as `https://example.com/my-queue.xml` as well. That is
> gone as of 2.0.0 — the rule behind it matched *every* `.xml` address on the site, so
> `wp-sitemap.xml` and sitemaps published by other plugins were answered with a feed and
> a 404.

## Overriding the template

The feed is rendered from `public/template/postqueue-feed-rss2.php`, a copy of the RSS2
template WordPress ships. A theme can replace it with a file of the same name in a
`plugin-parts` folder, or in any sub folder of one:

```
your-theme/plugin-parts/postqueue-feed-rss2.php
your-theme/plugin-parts/feeds/postqueue-feed-rss2.php
```

A child theme's copy wins over the parent theme's.

## Public API

```php
$plugin = postqueue_feeds_get_plugin();   // \PostqueueFeeds\Plugin
```

## Repository layout

`public/` is exactly what ships to wordpress.org; everything else is repository-only.
`postqueue-feeds-dev.php` in the root loads `public/`, so the whole repository can be
symlinked into `wp-content/plugins` during development.

Releases are cut by release-please and deployed to the wordpress.org SVN by GitHub
Actions — see [.github/WORKFLOWS.md](.github/WORKFLOWS.md). Contribution rules and the
local setup are in [CONTRIBUTING.md](CONTRIBUTING.md).

## A note on the repository name

There is a second, older repository called `ph-postqueue-feeds` (2015, three commits). It
was a separate first attempt at the same idea, never reached wordpress.org, and is not an
ancestor of this code. **This** repository is the one behind
[the wordpress.org listing](https://wordpress.org/plugins/postqueue-feeds/).

## License

GPL-3.0-or-later — see [LICENSE](LICENSE).
