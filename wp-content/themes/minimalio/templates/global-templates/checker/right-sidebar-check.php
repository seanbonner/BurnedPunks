<?php
/**
 * Right sidebar check.
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

</div><!-- #primary container -->

<?php

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
if ( 'right' === $sidebar_pos || 'both' === $sidebar_pos ) {
	get_template_part( 'templates/global-templates/sidebar-templates/sidebar', 'right' );
} ?>
