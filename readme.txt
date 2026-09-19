=== Comment Analysis for TypeSafe Jev ===
Contributors: juanlentino
Tags: ai, comments, moderation, classification, typesafe
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 7.4
Requires Plugins: ai, connector-for-typesafe-jev
Stable tag: 0.1.0
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

= Requirements =

* [AI](https://wordpress.org/plugins/ai/) with the Comment Moderation feature switched on. The feature itself needs at least one AI provider connected, because the AI plugin gates it that way; this plugin then answers in that provider's place.
* [Connector for TypeSafe Jev](https://github.com/juanlentino/jev-connector) with a key connected under **Settings → Connectors**.

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

That list is generative providers the core AI Client can drive. Jev returns probabilities and choices, not text, so it is not one of them, and registering it as one would get its key rejected. This plugin uses the Comment Moderation feature's own extension point for a replacement analysis, which is the door the AI plugin provides for exactly this.

= Which comments does it analyse? =

Whichever ones the AI plugin decides to analyse. This plugin never touches a comment on its own.

== Changelog ==

= 0.1.0 =
* First release.
