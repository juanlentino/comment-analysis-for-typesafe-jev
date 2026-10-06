# Changelog

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
