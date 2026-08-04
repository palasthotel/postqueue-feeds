# CI/CD Workflows

This repository uses four GitHub Actions workflows. The plugin is versioned by
[release-please](https://github.com/googleapis/release-please) based on
[conventional commits](https://www.conventionalcommits.org/):
`fix:` → patch, `feat:` → minor, `feat!:` / `BREAKING CHANGE:` → major.

Tag format: `v*` (e.g. `v2.0.1`).

---

## Overview

```
Push to main
    │
    ├──▶ [release-please.yml]
    │        Creates / updates the release PR, bumping version.txt + CHANGELOG.md
    │
    │    On release PR (opened / synchronize)
    ├──▶ [update-plugin-version.yml]
    │        Syncs the Version header in public/postqueue-feeds-plugin.php
    │        and postqueue-feeds-dev.php
    │        + readme.txt Stable tag & changelog entry
    │
    │    On PR to main
    └──▶ [pr.yml]
             php -l on 7.4 / 8.2 / 8.3 / 8.4
             pack + "is the payload clean?"
             "do the version carriers agree?"


Merge release PR  →  release-please pushes tag v2.0.1 + creates GitHub Release
    │
    └── v*  ──▶ [wordpress-svn-release.yml]
                    version check → pack → upload zip to the Release
                    → deploy to WordPress.org SVN (trunk + tags/2.0.1)
```

There is no build step anywhere: the plugin is plain PHP with no assets and no
dependencies.

---

## `pr.yml` — PR checks

Three jobs:

- **php-lint** — `php -l` over every PHP file, on PHP 7.4, 8.2, 8.3 and 8.4.
- **pack** — runs `bin/pack.sh` and asserts the staged payload contains the plugin
  file, the classes, the template, the readme and the licence, and that it carries
  none of the repository-only files. `postqueue-feeds-dev.php` matters most there: it
  declares a plugin header, so shipping it would put a second "Postqueue Feeds (DEV)"
  entry in everybody's plugin list.
- **versions** — runs `bin/version-checker.sh`, so a hand-edited version number fails
  in the pull request instead of aborting a release.

## `release-please.yml` — release PR

Runs on every push to `main`. Uses a short-lived installation token of the org-owned
"Palasthotel Release Bot" app rather than `GITHUB_TOKEN`, because the tag this job
pushes has to trigger `wordpress-svn-release.yml` — and tags pushed with
`GITHUB_TOKEN` trigger nothing.

`release-type` is `simple`: the version lives in `version.txt`. Nothing else here
needs a manifest, so there is no `package.json` to bump.

## `update-plugin-version.yml` — version carriers

Runs only on the release-please PR (`startsWith(github.head_ref, 'release-please--')`).
It reads the version from `version.txt` and writes it into:

- the `Version:` header of `public/postqueue-feeds-plugin.php`
- the `Version:` header of `postqueue-feeds-dev.php`
- `Stable tag:` in `public/readme.txt`
- a new `= x.y.z =` section under `== Changelog ==` in `public/readme.txt`, converted
  from the Markdown release-please wrote into `CHANGELOG.md`

It pushes with the app token, not `GITHUB_TOKEN`: a `GITHUB_TOKEN` push triggers no
workflows, which would leave the release PR without any check results.

## `wordpress-svn-release.yml` — deploy

Triggered by a `v*` tag, or manually by `workflow_dispatch` with a version input.

`bin/version-checker.sh` runs first and compares the tag against all four version
carriers, so a mismatch stops the run before anything is published.

`bin/pack.sh` stages `public/` in `build/postqueue-feeds/` and zips it. The zip is
attached to the GitHub Release, and the SVN commit rsyncs from the very same
directory — so the release asset and the wordpress.org download are identical.

`rsync -rL`, not `cp -r`: GNU `cp` keeps symlinks while descending and BSD `cp`
resolves them, so a local rehearsal on macOS would pass while the Ubuntu runner
failed. wordpress.org discards symlinks when it builds the download, and SVN refuses a
commit that puts a symlink where it versions a regular file.

`assets/` (banner, icon, screenshots for the plugin page) is only mirrored when the
repository carries the directory. It does not today, and the one in SVN is empty — the
guard is there so that adding a banner later cannot be turned into `--delete`-ing the
plugin page's media.

### Required repository configuration

| Kind | Name | Purpose |
|---|---|---|
| Variable | `RELEASE_BOT_APP_ID` | GitHub App id of the release bot |
| Variable | `SVN_REPO_URL` | `https://plugins.svn.wordpress.org/postqueue-feeds/` |
| Secret | `RELEASE_BOT_PRIVATE_KEY` | private key of that app |
| Secret | `SVN_USERNAME` | wordpress.org account with commit rights |
| Secret | `SVN_PASSWORD` | its password |

### When a release fails

Do not try to re-push the tag. A tag event replays the workflow file **as it was at
that tag**, so a fix to the workflow cannot be picked up that way, and a tag ruleset
usually refuses to move a tag at all (`GH013`). Use **Run workflow** on
`wordpress-svn-release.yml` instead, from a branch that has the fix, and give it the
version to deploy.
