<?php

/**
 * Load the relevant assets
 */

defined( 'ABSPATH' ) || exit;

new minimalio__Loader;
class minimalio__Loader {

	/**
	 * Called on class initialisation
	 */
	function __construct() {
		/* Setup wp global for removing query vars */
		global $wp;

		/* Add conditional tags around scripts that require them */
		add_filter( 'script_loader_tag', [ $this, 'minimalio_conditional_scripts' ], 10, 2 );

		/* Add our page templates to the page attributes dropdown */
		add_filter( 'theme_page_templates', [ $this, 'minimalio_add_page_templates' ], 10, 3 );

		/* Add compatibility tags for browsers */
		add_action( 'wp_head', [ $this, 'minimalio_compatibility_tags' ] );

		/* Load TypeKit kit */
		add_action( 'wp_head', [ $this, 'minimalio_load_typekit' ] );

		/* Remove jQuery migrate code from queue */
		add_filter( 'wp_default_scripts', [ $this, 'minimalio_remove_jquery_migrate' ] );
	}


	/**
	 * Selectively add conditional tags around assets
	 * @param  string $minimalio_tag    Full <link> or <script> tag
	 * @param  string $handle The handle/slug passed to enqueue function
	 * @return string         Modified $minimalio_tag
	 */
	public function minimalio_conditional_scripts( $minimalio_tag, $handle ) {
		// Add lower than ie9 wrapper around html5shiv
		if ( 'html5shiv' === $handle ) {
			$minimalio_tag = '<!--[if lt IE 9]>' . $minimalio_tag . '<![endif]-->';
		}

		return $minimalio_tag;
	}

	/**
	 * Add tags required for best compatibility
	 */
	public function minimalio_compatibility_tags() {
		echo '<meta charset="utf-8">';
	}

	/**
	 * Load TypeKit fonts
	 */
	public function minimalio_load_typekit() {
		// To load a typekit "kit" simply use the `load_typekit` function
		// containing the kit ID as the first and only parameter.
		$selected_font = get_theme_mod( 'minimalio_typography_settings_google_font' );

		if ( false !== $selected_font ) {
			// Array of web safe fonts that should not be loaded from Google Fonts
			$safe_fonts = [
				'Arial',
				'Verdana',
				'Tahoma',
				'Times+New+Roman',
				'Georgia',
				'Garamond',
				'Courier+New',
				'Brush+Script+MT'
			];

			// Only load from Google Fonts if it's not a web-safe font
			if ( ! in_array( $selected_font, $safe_fonts ) ) {
				// Get the selected weight and ensure 700 is also loaded
				$selected_weight = get_theme_mod( 'minimalio_typography_settings_google_font_wight', 400 );
				$weights = array_unique( [ $selected_weight, '700' ] );
				sort( $weights );
				$weights_string = implode( ';', $weights );

				echo '	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=' . $selected_font . ':wght@' . $weights_string . '&display=swap">';
			}
		}
	}

	/**
	 * Add our 2nd level page templates into the attribute dropdown
	 */
	public function minimalio_add_page_templates( $templates ) {
		/* Check to see if their is a page template cache */
		$theme_templates = wp_cache_get( 'minimalio_page_templates', 'minimalio' );

		/* If the cache is valid, merge it with existing templates and return */
		if ( is_array( $theme_templates ) ) {
			return array_merge( $templates, $theme_templates );
		}

		/* Empty the templates variable incase cache was invalid */
		$theme_templates = [];

		/* Get all the files within our templates folder */
		$dir   = get_template_directory() . '/templates/pages/';
		$files = scandir( $dir );

		/* Loop over template files */
		foreach ( $files as $file ) {
			/* Get the headers from the file */
			$headers = get_file_data( $dir . $file, [ 'Template Name' => 'Template Name' ] );

			/* If no template name is given, then skip */
			if ( empty( $headers['Template Name'] ) ) {
				continue;
			}

			/* Internationalise the header into the array with file as key */
			$theme_templates[ 'templates/pages/' . $file ] = $headers['Template Name'];
		}

		/* Build our page templates cache */
		wp_cache_add( 'minimalio_page_templates', $theme_templates, 'minimalio' );

		/* Add our page templates to the list */
		$templates = array_merge( $templates, $theme_templates );

		/* Return the templates */
		return $templates;
	}


	/**
	 * Remove jQuery migrate scripts from queue
	 * @param  Object $scripts Scripts queue object
	 * @return null
	 */
	public function minimalio_remove_jquery_migrate( &$scripts ) {
		// If this is not the admin area, remove the migrate files.
		if ( ! is_admin() ) {
			$scripts->remove( 'minimalio_jquery' );
			$scripts->add( 'minimalio_jquery', false, [ 'jquery-core' ], '3.7.1' );
		}
	}
}
