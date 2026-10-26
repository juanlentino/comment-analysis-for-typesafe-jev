# Changelog

## [Unreleased]

## [0.3.0] - 2026-10-26

### Changed

- `Requires Plugins` names `tribar-connector-for-typesafe-jev`. The connector
  was renamed to Tribar Connector for TypeSafe Jev in its 0.4.6 at the
  WordPress.org directory review's request, and WordPress matches plugin
  dependencies by folder slug, so the old `connector-for-typesafe-jev` folder
  no longer satisfies the dependency. Minor bump: a site must install the
  renamed connector before updating. Its settings and key carry over (the
  setting names did not change).
- README and readme.txt use the new name and link the renamed repo.

## [0.2.2] - 2026-10-21

Directory readiness, checked against the plugin guidelines and the readme
template. No behaviour change.

### Changed

- `readme.txt` states that the plugin is independent and not affiliated with,
  endorsed by, or sponsored by TypeSafe (until now only README said it).
- `readme.txt` gains the template's `== Upgrade Notice ==` section.
- The GitHub repo is renamed from `jev-comment-analysis` to
  `comment-analysis-for-typesafe-jev`, the slug, so its public name no longer
  starts with TypeSafe's mark. GitHub redirects old URLs. `Plugin URI` updated.

### Documentation

- Links to Connector for TypeSafe Jev point at its renamed repo,
  `connector-for-typesafe-jev` (GitHub redirects the old `jev-connector` URL).

## [0.2.1] - 2026-10-05

### Fixed

- The README and the readme.txt FAQ said registering Jev as an `ai_provider`
  would get its key rejected by AI Client validation. The actual mechanism:
  core checks `ai_provider` keys on save against the provider class registered
  under the same id, and clears the key when there is none. Jev has no
  provider class because the AI Client has no capability yet for decision
  models (php-ai-client#296). Matches the correction in Connector for TypeSafe
  Jev and on Core Trac #66146. No behaviour change.

## [0.2.0] - 2026-09-20

### Added

- Comment Moderation now works on a site whose only AI connector is Jev. The
  AI plugin's `has_ai_credentials()` counts `ai_provider` connectors only and
  gates both `moderate_comment()` and the bulk analysis action on it. This
  plugin answers the `wpai_has_ai_credentials` filter with `true` only inside
  those two paths (`wp_insert_comment`, `handle_bulk_actions-edit-comments`)
  and only when the Jev connector has a key. The filter is global, so a
  blanket `true` would have switched on every generative feature of the AI
  plugin with no model to serve them; the scoping is the whole point.

## [0.1.0] - 2026-09-19

### Added

- First release. Answers the AI plugin's `wpai_comment_analysis_result`
  filter with three Jev questions (toxicity noul, sentiment choice, value
  noul) through Connector for TypeSafe Jev. Falls through to the AI plugin's
  generative provider on any failure.
- `jevca_questions` filter and `jevca_unavailable` action.
