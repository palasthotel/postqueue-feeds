# Contributing

## Branching

`main` is the default branch and always reflects what is released (or about to be
released). Work on a feature branch and open a pull request against `main`.

## Commit messages

Releases and the changelog are generated from the commit history, so commit messages
follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>[optional scope][!]: <description>

[optional body]

[optional footer]
```

| Type | Effect on the version | Appears in changelog |
|---|---|---|
| `fix:` | patch (2.0.0 → 2.0.1) | yes, "Bug Fixes" |
| `feat:` | minor (2.0.0 → 2.1.0) | yes, "Features" |
| `feat!:` or `BREAKING CHANGE:` footer | major (2.0.0 → 3.0.0) | yes, highlighted |
| `docs:`, `refactor:`, `chore:`, `deps:`, `style:`, `test:`, `ci:` | none | no |

A pull request that should trigger a release needs at least one `fix:` or `feat:`
commit. When squash-merging, make sure the squash commit message itself is a
conventional commit — that is the message release-please reads.

### Which changes get `fix:` or `feat:`

Only changes that matter to someone using the plugin. `fix:` and `feat:` decide the
version *and* write the line that ends up in the changelog on the wordpress.org
plugin page, so the question to ask before committing is whether a user of the plugin
would care about that line.

Everything else takes a type that releases nothing — workflows and CI, release
tooling, repository documentation, internal refactoring, and anything touching files
that are not shipped. As a rule of thumb, a change confined to files outside
`public/` is almost never a `fix:`.

That includes hardening. Blocking direct access to a file that is not part of the
download is `chore:`, not `fix:` — nothing changes for anyone who installed the
plugin.

## Repository layout

`public/` is exactly what ships to WordPress.org. Everything outside it is
repository-only.

| Path | Description |
|---|---|
| `public/postqueue-feeds-plugin.php` | plugin header and bootstrap |
| `public/inc/` | the plugin's PHP |
| `public/template/` | the feed template, overridable from a theme |
| `public/public-functions.php` | the public API |
| `public/readme.txt` | the wordpress.org listing |
| `postqueue-feeds-dev.php` | development wrapper, loads `public/`; never deployed |
| `bin/` | release helper scripts |

The file name `public/postqueue-feeds-plugin.php` is deliberately unlovely and must not
be tidied up. WordPress identifies an installed plugin by
`<directory>/<main file>`, and that pair is what its activation state and update
checks hang on — renaming it would deactivate the plugin on every site that has it.

## Local setup

There is nothing to build: the plugin is plain PHP with no assets and no dependencies.

```sh
npx @wordpress/env start      # http://localhost:8890, admin / password
```

The bundled `.wp-env.json` also mounts `../ph-postqueue/public`, because this plugin
does nothing without Postqueue. Clone
[palasthotel/ph-postqueue](https://github.com/palasthotel/ph-postqueue) next to this
repository, or edit the path.

Save the permalink settings once after starting, or run `wp rewrite flush`. The shipped
plugin flushes them on activation, but that hook keys off
`public/postqueue-feeds-plugin.php` and WordPress activates the dev wrapper instead — so
in development it does not fire.

`bash bin/pack.sh` stages the payload in `build/postqueue-feeds/` and zips it to
`postqueue-feeds.zip` — the same payload the release deploys.

## Versions

Never edit version numbers by hand. `version.txt`, `CHANGELOG.md`,
`public/postqueue-feeds-plugin.php` and the `Stable tag:` in `public/readme.txt` are
all maintained by the release pipeline — see
[.github/WORKFLOWS.md](.github/WORKFLOWS.md).

Content changes to `public/readme.txt` (description, FAQ, tested-up-to) are of course
done by hand; just leave `Stable tag:` and the `== Changelog ==` entries alone.

## Checks

Every PR runs `php -l` against PHP 7.4, 8.2, 8.3 and 8.4, and packs the plugin so a
broken `bin/pack.sh` surfaces in the pull request rather than in a release.

The plugin declares `Requires at least: 6.6`. Two things depend on it: the
`Requires Plugins:` header, which WordPress only understands from 6.5 on, and
`get_feed_build_date()` in the feed template, which arrived in 5.2.
