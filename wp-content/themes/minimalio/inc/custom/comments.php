<?php
/**
 * Comment layout.
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Comments form.
add_filter( 'comment_form_default_fields', 'minimalio_bootstrap_comment_form_fields' );

/**
 * Creates the comments form.
 *
 * @param string $fields Form fields.
 *
 * @return array
 */

if ( ! function_exists( 'minimalio_bootstrap_comment_form_fields' ) ) {

	function minimalio_bootstrap_comment_form_fields( $fields ) {
		$commenter = wp_get_current_commenter();
		$req       = get_option( 'require_name_email' );
		$aria_req  = ( $req ? " aria-required='true'" : '' );
		$html5     = current_theme_supports( 'html5', 'comment-form' ) ? 1 : 0;
		$consent   = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
		$fields    = [
			'author'  => '<div class="form-group comment-form-author"><label for="author">' . __( 'Name',
			'minimalio' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
						'<input class="form-control" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . '></div>',
			'email'   => '<div class="form-group comment-form-email"><label for="email">' . __( 'Email',
			'minimalio' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
						'<input class="form-control" id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . '></div>',
			'url'     => '<div class="form-group comment-form-url"><label for="url">' . __( 'Website',
			'minimalio' ) . '</label> ' .
						'<input class="form-control" id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30"></div>',
			'cookies' => '<div class="form-group form-check comment-form-cookies-consent"><input class="form-check-input" id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' /> ' .
					'<label class="form-check-label" for="wp-comment-cookies-consent">' . __( 'Save my name, email, and website in this browser for the next time I comment', 'minimalio' ) . '</label></div>',
		];

		return $fields;
	}
} // endif function_exists( 'minimalio_bootstrap_comment_form_fields' )

add_filter( 'comment_form_defaults', 'minimalio_bootstrap_comment_form' );

/**
 * Builds the form.
 *
 * @param string $minimalio_args Arguments for form's fields.
 *
 * @return mixed
 */

if ( ! function_exists( 'minimalio_bootstrap_comment_form' ) ) {

	function minimalio_bootstrap_comment_form( $minimalio_args ) {
		$minimalio_args['comment_field'] = '<div class="form-group comment-form-comment">
	    <label for="comment">' . _x( 'Comment', 'noun', 'minimalio' ) . ( ' <span class="required">*</span>' ) . '</label>
	    <textarea class="form-control" id="comment" name="comment" aria-required="true" cols="45" rows="8"></textarea>
	    </div>';
		$minimalio_args['class_submit']  = 'btn btn-secondary'; // since WP 4.1.
		return $minimalio_args;
	}
} // endif function_exists( 'minimalio_bootstrap_comment_form' )
