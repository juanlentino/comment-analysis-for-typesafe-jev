# Changelog

## [0.1.0] - 2026-09-19

### Added

- First release. Answers the AI plugin's `wpai_comment_analysis_result`
  filter with three Jev questions (toxicity noul, sentiment choice, value
  noul) through Connector for TypeSafe Jev. Falls through to the AI plugin's
  generative provider on any failure.
- `jevca_questions` filter and `jevca_unavailable` action.
