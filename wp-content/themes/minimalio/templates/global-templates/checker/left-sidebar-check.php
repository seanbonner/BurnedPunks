<?php

/**
 * Left sidebar check.
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( is_singular( 'post' ) ) {
	$sidebar_pos = get_theme_mod( 'minimalio_settings_single_template_sidebar_position' );
} elseif ( is_home() ) {
	$sidebar_pos = get_theme_mod( 'minimalio_settings_archive_template_sidebar_position' );
} elseif ( is_archive() ) {
	$sidebar_pos = get_theme_mod( 'minimalio_settings_archive_template_sidebar_position' );
} elseif ( is_page_template( 'templates/pages/blog-template.php' ) ) {
	$sidebar_pos = get_theme_mod( 'minimalio_settings_archive_template_sidebar_position' );
} else {
	$sidebar_pos = get_theme_mod( 'minimalio_settings_sidebar_position' );
}

if ( 'left' === $sidebar_pos || 'both' === $sidebar_pos ) {
	get_template_part( 'templates/global-templates/sidebar-templates/sidebar', 'left' );
}

if ( 'left' === $sidebar_pos || 'both' === $sidebar_pos || 'right' === $sidebar_pos ) {
	$overflow = 'overflow-hidden';
} else {
	$overflow = '';
}
?>

<div class="lg:grow lg:shrink-0 lg:basis-0 content-area <?php echo esc_attr( $overflow ); ?>" id="primary">
