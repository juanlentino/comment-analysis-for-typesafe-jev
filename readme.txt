=== Comment Analysis for TypeSafe Jev ===
Contributors: juanlentino
Tags: ai, comments, moderation, classification, typesafe
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 7.4
Requires Plugins: ai, connector-for-typesafe-jev
Stable tag: 0.2.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Backs the AI plugin's Comment Moderation feature with TypeSafe Jev: three typed questions instead of a generative model.

== Description ==

The AI plugin's **Comment Moderation** feature scores every new comment for toxicity, sentiment and value, and holds the toxic ones for review. Out of the box it asks a generative model for a JSON blob.

This plugin answers instead, with TypeSafe's Jev model. Jev does not generate text; it answers typed questions. So the three figures come from three questions:

* **Toxicity** — the probability that the comment is abusive, hateful, harassing or threatening.
* **Sentiment** — one of positive, neutral, negative, with a confidence figure.
* **Value** — the probability that the comment adds something a reader would want.

Everything else stays the AI plugin's: the hold threshold, the badges on the Comments screen, the filters, the comment meta. Switch the feature on under **Settings → AI**, and Jev is what answers.

If Jev cannot be reached, the plugin steps aside and the AI plugin proceeds with whichever generative provider it would have used. An outage never leaves a comment unanalysed.

TypeSafe and Jev are trademarks of their owner. This plugin is an independent integration and is not affiliated with, endorsed by, or sponsored by TypeSafe.

= Requirements =

* [AI](https://wordpress.org/plugins/ai/) with the Comment Moderation feature switched on. No other AI provider is needed: on a site whose only AI connector is Jev, this plugin tells the AI plugin that credentials exist for the two comment-moderation paths (new comments and the bulk "analyze" action) and nothing else, so the rest of the AI plugin's features stay off as they should.
* [Connector for TypeSafe Jev](https://github.com/juanlentino/connector-for-typesafe-jev) with a key connected under **Settings → Connectors**.

= Filters =

* `jevca_questions` — the three questions sent to Jev, keyed `toxic`, `sentiment`, `valuable`. Change the wording to match your community.
* Action `jevca_unavailable` — fires with the `WP_Error` when Jev could not be reached.

== External services ==

This plugin sends each new comment's text and the author name as typed to the TypeSafe System One API, through Connector for TypeSafe Jev, when the AI plugin's Comment Moderation feature analyses that comment. It sends nothing else: no email address, IP address or URL, and no post content. The request goes to `https://api.typesafe.ai/v1/systemone` using the API key held by the connector. Nothing is sent on activation, on page load, or on any schedule.

Service terms and policies: https://docs.typesafe.ai/legal

== Installation ==

1. Install and activate Connector for TypeSafe Jev, and connect a key under **Settings → Connectors**.
2. Install and activate the AI plugin, and switch on **Comment Moderation** under **Settings → AI**.
3. Install and activate this plugin. There is nothing to configure.

== Frequently Asked Questions ==

= Why doesn't Jev appear in the provider list on the AI tab? =

That list is the providers the core AI Client can drive. Jev returns probabilities and choices rather than text, and the AI Client has no capability for that kind of model yet, so Jev has no provider there. Until it does, this plugin uses the Comment Moderation feature's own extension point for a replacement analysis, which is the door the AI plugin provides for exactly this.

= Does it turn on the AI plugin's other features? =

No. The AI plugin asks one question, "are there AI credentials?", for everything it does. This plugin answers yes only while a new comment is being inserted or the bulk analysis action is running, and only when the Jev connector has a key. Alt text, suggestions and the rest keep seeing whatever generative providers you actually have.

= Which comments does it analyse? =

Whichever ones the AI plugin decides to analyse. This plugin never touches a comment on its own.

== Changelog ==

= 0.2.2 =
* The description now states that the plugin is independent and not affiliated with TypeSafe.
* The plugin's home moved to github.com/juanlentino/comment-analysis-for-typesafe-jev, named after the plugin.

= 0.2.1 =
* Corrected the FAQ on why Jev is not in the AI tab's provider list: it has no AI Client provider class yet, not a key the client rejects.

= 0.2.0 =
* Works on a site whose only AI connector is Jev. Comment Moderation no longer needs a generative provider connected alongside.

= 0.1.0 =
* First release.

== Upgrade Notice ==

= 0.2.2 =
Readme only: an independence statement and the plugin's new home. No behaviour change.
