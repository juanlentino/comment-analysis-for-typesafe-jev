# Comment Analysis for TypeSafe Jev

Backs the [AI plugin](https://github.com/WordPress/ai)'s **Comment Moderation** feature with [TypeSafe Jev](https://docs.typesafe.ai), through [Connector for TypeSafe Jev](https://github.com/juanlentino/connector-for-typesafe-jev).

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

That dropdown lists providers the PHP AI Client can drive, and Jev has no provider class there yet: the client describes models by capability, and none of its capabilities fits a model that returns a probability or a choice rather than text. Core checks every `ai_provider` key on save against the provider class registered under the same id and clears it when there is none, which is why Connector for TypeSafe Jev registers under its own type for now. The Core AI team's direction is decision-model support inside the AI Client ([php-ai-client#296](https://github.com/WordPress/php-ai-client/issues/296)); until then, the feature's own extension point is the right layer.

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

GPL-2.0-or-later. TypeSafe and Jev are trademarks of their owner; this plugin is an independent integration and is not affiliated with, endorsed by, or sponsored by TypeSafe.
