# Comment Analysis for TypeSafe Jev

Backs the [AI plugin](https://github.com/WordPress/ai)'s **Comment Moderation** feature with [TypeSafe Jev](https://docs.typesafe.ai), through [Connector for TypeSafe Jev](https://github.com/juanlentino/jev-connector).

One file, one filter. The AI plugin's `Comment_Analysis` ability opens with

```php
$pre_result = apply_filters( 'wpai_comment_analysis_result', null, $content, $author, $post_id );
```

"for integrations that provide their own comment analysis implementation." This plugin returns `{ toxicity_score, sentiment, value_score }` from three typed questions, and the generative call never happens. The AI plugin's thresholds, badges, filters and comment meta all keep working.

| Figure | Question to Jev | Answer used |
| --- | --- | --- |
| `toxicity_score` | noul: is this comment toxic? | the probability |
| `sentiment` | choice: positive / neutral / negative | the chosen option |
| `value_score` | noul: does it add something a reader would want? | the probability |

On any failure (connector absent, no key, API error, incomplete answer) it returns the filter's input untouched, so the AI plugin proceeds with its generative provider.

## Why not a provider in the AI tab

That dropdown is the set of `ai_provider` connectors the PHP AI Client can drive: generative models. Jev is a judgement model. Registering it as `ai_provider` would hand its key to the AI Client for validation, which it cannot pass, and core would clear it. The feature's own extension point is the right layer.

## Requires

- WordPress 7.0+, PHP 7.4+
- **AI** plugin with Comment Moderation on. No generative provider is needed alongside: the AI plugin's `has_ai_credentials()` counts `ai_provider` connectors only, so this plugin answers its `wpai_has_ai_credentials` filter with `true` during exactly two paths (`wp_insert_comment`, and the bulk `handle_bulk_actions-edit-comments` action) when the Jev connector has a key. Every other feature of the AI plugin still sees the truth, so nothing generative lights up without a text model behind it.
- **Connector for TypeSafe Jev** with a key connected under Settings → Connectors.

Both are declared in `Requires Plugins`, so WordPress enforces the order.

## Hooks

Hooks it *consumes* from the AI plugin: `wpai_comment_analysis_result` (the analysis), `wpai_has_ai_credentials` (scoped, see above).

Hooks it *provides*:

- `jevca_questions` (filter) — the three questions, keyed `toxic`, `sentiment`, `valuable`. Tune the wording to your community. Keep the keys.
- `jevca_unavailable` (action) — `( WP_Error $error, string $content )` when Jev could not be reached.

## License

GPL-2.0-or-later. TypeSafe and Jev are trademarks of their owner; this plugin is an independent integration and is not affiliated with or endorsed by TypeSafe.
