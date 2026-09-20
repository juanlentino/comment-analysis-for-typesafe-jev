# Changelog

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
