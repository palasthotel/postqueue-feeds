# Changelog

All notable changes to this project are documented here. The format follows
[Conventional Commits](https://www.conventionalcommits.org/) and from the next release
on the file is maintained by
[release-please](https://github.com/googleapis/release-please) — do not edit it by hand.

## [2.0.0](https://github.com/palasthotel/postqueue-feeds/compare/v1.0.0...v2.0.0) (2026-08-04)


### ⚠ BREAKING CHANGES

* a queue's feed is no longer served at <slug>.xml. That address was documented in the README and is gone with the rule behind it. Use /feed/<slug>/ or ?feed=<slug>.

### Features

* show each queue's feed address on the Postqueues screen ([41f8c4a](https://github.com/palasthotel/postqueue-feeds/commit/41f8c4a96e8df3b7f350287d22598c390d304f56))
* show each queue's feed address on the Postqueues screen ([8d65219](https://github.com/palasthotel/postqueue-feeds/commit/8d652196402f903d6c08a1edcddcd4b747c708dd))


### Bug Fixes

* make the feed work as soon as the plugin is activated ([232a560](https://github.com/palasthotel/postqueue-feeds/commit/232a5600dd99d02b1e3a49aade830ae31bcfb907))
* stop answering wp-sitemap.xml with a feed ([d92e5e0](https://github.com/palasthotel/postqueue-feeds/commit/d92e5e0ab0b65e0a65b74f70ec6f73bb32d972d5))

## 1.0

* First release
