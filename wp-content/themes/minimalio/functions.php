<?php

/**
 * minimalio functions and definitions
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Get theme version from style.css
$minimalio_theme = wp_get_theme();
define( 'MINIMALIO_VERSION', $minimalio_theme->get( 'Version' ) );

$minimalio_includes = [
	'/base/settings-theme.php',                     // Initialize theme default settings.
	'/base/setup.php',                              // Theme setup and custom theme supports.
	'/base/widgets.php',                            // Register widget area.
	'/base/add-styles-scripts.php',                 // Enqueue scripts and styles.
	'/base/template-tags.php',                      // Custom template tags for this theme.
	'/base/ajax-filter.php',                        // Custom pagination for this theme.
	'/base/pagination.php',                         // Custom pagination for this theme.
	'/base/bootstrap-navwalker.php',                // Load custom WordPress nav walker.
	'/custom/comments.php',                         // Custom Comments file.
	'/custom/custom-post-type.php',                 // Custom post type
	'/custom/customizer.php',                       // Customizer additions.
	'/custom/asset-loader.php',                     // Load deprecated functions.
	'/custom/theme-support.php',                    // Menus, Custom Admin CSS, Page redirect, Google fonts
	'/theme-customizer/php/theme-customizer.php',   // Load theme Customizer functions
	'/custom/minimalio-admin-pages.php',            // Load Minimalio admin pages
];

foreach ( $minimalio_includes as $file ) {
	$filepath = locate_template( 'inc' . $file );
	if ( ! $filepath ) {
		trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
	}
	require_once $filepath;
}

/**
 * TEXT DOMAIN
 */
function minimalio_load_textdomain() {
	load_theme_textdomain(
		'minimalio',
		get_parent_theme_file_path( 'languages' )
	);
}

add_action( 'after_setup_theme', 'minimalio_load_textdomain' );
