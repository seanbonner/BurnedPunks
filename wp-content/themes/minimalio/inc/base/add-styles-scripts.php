<?php

/**
 * minimalio enqueue scripts
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'minimalio_scripts' ) ) {

	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function minimalio_scripts() {
		$directory = get_template_directory_uri();

		$ver = MINIMALIO_VERSION;

		/**
		 * Load the compiled and minified stylesheet
		 */
		wp_enqueue_style( 'minimalio_theme', $directory . '/assets/dist/css/minimalio-theme.min.css', false, $ver );

		/**
		 * Load WordPress' jQuery version
		 */
		wp_enqueue_script( 'minimalio_jquery', null, false, false, true );

		/**
		 * Load minified and concatenated Node Modules file (See gulpfile.babel.js 'packageScripts' task)
		 */
		wp_enqueue_script( 'minimalio_components', $directory . '/assets/dist/js/minimalio-components.min.js', false, $ver, true );

		/**
		 * Load main theme script file
		 */
		wp_enqueue_script( 'minimalio_theme', $directory . '/assets/dist/js/minimalio-theme.min.js', false, $ver, true );

		/**
		 * Load conditional video scripts
		 */
		require_once get_template_directory() . '/inc/base/conditional-video-scripts.php';

		if ( is_singular() && comments_open() && get_option( 'minimalio_thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Add the theme site URL to available Jquery arguments in case this is needed (e.g. WP AJAX call URLS)
		$theme = [ 'siteurl' => get_option( 'siteurl' ) ];
		wp_localize_script( 'minimalio_theme', 'minimalio_theme', $theme );
		wp_localize_script( 'minimalio_theme', 'wpAjaxLoad', [ 
			'ajax_loadUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'minimalio_ajax_nonce' )
		] );
	}
} // endif function_exists( 'minimalio_scripts' ).

add_action( 'wp_enqueue_scripts', 'minimalio_scripts' );

/**
 * Registers an editor stylesheet in a sub-directory.
 */
function minimalio_add_editor_styles_sub_dir() {
	add_theme_support( 'editor-styles' ); // if you don't add this line, your stylesheet won't be added
	add_editor_style( trailingslashit( get_template_directory_uri() ) . 'assets/dist/css/minimalio-editor-style.css' );
}
add_action( 'after_setup_theme', 'minimalio_add_editor_styles_sub_dir' );

/**
 * Child style
 */
function minimalio_parent_enqueue_styles() {
	wp_enqueue_style(
		'minimalio-child-style',
		get_stylesheet_uri()
	);
}

add_action( 'wp_enqueue_scripts', 'minimalio_parent_enqueue_styles' );
