<?php
/**
 * Plugin Name:       Comment Analysis for TypeSafe Jev
 * Plugin URI:        https://github.com/juanlentino/jev-comment-analysis
 * Description:       Backs the AI plugin's Comment Moderation feature with TypeSafe Jev. Toxicity, sentiment and value come from three typed questions instead of a generative model.
 * Version:           0.2.0
 * Requires at least: 7.0
 * Requires PHP:      7.4
 * Requires Plugins:  ai, connector-for-typesafe-jev
 * Author:            Juan Lentino
 * Author URI:        https://juanlentino.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       comment-analysis-for-typesafe-jev
 *
 * @package JevCommentAnalysis
 */

declare( strict_types = 1 );

namespace JevCommentAnalysis;

defined( 'ABSPATH' ) || exit;

/**
 * The three questions, in the shape the connector's builders produce.
 *
 * Kept as a function so the wording can be filtered and so it reads as the
 * whole contract with Jev in one place.
 *
 * @return array<string,array<string,mixed>>
 */
function questions(): array {
	$questions = array(
		'toxic'     => array(
			'type'         => 'noul',
			'instructions' => 'Is this comment toxic: abusive, hateful, harassing, threatening, or demeaning toward a person or group?',
		),
		'sentiment' => array(
			'type'         => 'choice',
			'instructions' => 'What is the overall sentiment of this comment toward the post and its author?',
			'criteria'     => array(
				'positive' => 'Warm, appreciative, encouraging, or constructive',
				'neutral'  => 'Factual, mixed, procedural, or too short to tell',
				'negative' => 'Hostile, dismissive, mocking, or complaining',
			),
		),
		'valuable'  => array(
			'type'         => 'noul',
			'instructions' => 'Does this comment add something a reader of the post would want: information, a correction, a genuine question, a relevant experience, or a considered disagreement?',
		),
	);

	/**
	 * Filters the questions sent to Jev for each comment.
	 *
	 * @param array<string,array<string,mixed>> $questions Keyed toxic, sentiment, valuable.
	 */
	return (array) apply_filters( 'jevca_questions', $questions );
}

/**
 * Turn a connector Response into the array the AI plugin expects.
 *
 * Pure. Returns null when any answer is missing, so the caller can fall
 * through to the generative provider rather than hand the feature a guess.
 *
 * @param \JevConnector\Response $response The answer.
 * @return array{toxicity_score:float,sentiment:string,value_score:float}|null
 */
function result_from( \JevConnector\Response $response ): ?array {
	$toxic     = $response->noul( 'toxic' );
	$sentiment = $response->choice( 'sentiment' );
	$valuable  = $response->noul( 'valuable' );

	if ( null === $toxic || null === $sentiment || null === $valuable ) {
		return null;
	}

	return array(
		'toxicity_score' => $toxic,
		'sentiment'      => $sentiment,
		'value_score'    => $valuable,
	);
}

/**
 * Answer the AI plugin's comment analysis with Jev.
 *
 * Any failure returns $pre untouched, which lets the AI plugin carry on with
 * whichever generative provider it would have used. An outage here must not
 * turn into comments that never get analysed.
 *
 * @param mixed  $pre     Null, or a result another integration already supplied.
 * @param string $content Comment content.
 * @param string $author  Comment author name as typed.
 * @return mixed
 */
function analyze( $pre, $content, $author ) {
	if ( null !== $pre || ! function_exists( 'JevConnector\\ask' ) ) {
		return $pre;
	}

	$response = \JevConnector\ask(
		array(
			'author'  => (string) $author,
			'comment' => (string) $content,
		),
		questions()
	);

	if ( is_wp_error( $response ) ) {
		/**
		 * Fires when Jev could not analyse a comment. The AI plugin proceeds
		 * with its generative provider.
		 *
		 * @param \WP_Error $response The failure.
		 * @param string    $content  Comment content.
		 */
		do_action( 'jevca_unavailable', $response, $content );

		return $pre;
	}

	return result_from( $response ) ?? $pre;
}

add_filter( 'wpai_comment_analysis_result', __NAMESPACE__ . '\\analyze', 10, 3 );

/**
 * Let Comment Moderation run on a site whose only AI connector is Jev.
 *
 * The AI plugin's has_ai_credentials() counts ai_provider connectors only, so
 * on a Jev-only site moderate_comment() and the bulk analysis action bail
 * before analyze() is ever asked. The plugin offers this filter for exactly
 * that case, but it is global: answering true everywhere would light up
 * every generative feature the plugin has, and those would then fail for
 * want of a text model. So the answer is confined to the two comment paths.
 *
 * ponytail: this couples to the AI plugin's call sites (wp_insert_comment and
 * the bulk action filter). If a third comment path appears, add it here.
 *
 * @param bool $has_credentials What the AI plugin worked out on its own.
 * @return bool
 */
function has_credentials( $has_credentials ) {
	if ( $has_credentials || ! function_exists( 'JevConnector\\is_ready' ) || ! \JevConnector\is_ready() ) {
		return (bool) $has_credentials;
	}

	return doing_action( 'wp_insert_comment' ) || doing_filter( 'handle_bulk_actions-edit-comments' );
}

add_filter( 'wpai_has_ai_credentials', __NAMESPACE__ . '\\has_credentials' );
