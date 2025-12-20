<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'left-sidebar' ) ) {
	return;
}

if ( is_singular( 'post' ) or is_home() or is_page_template( 'templates/pages/blog-template.php' ) ) {
	$sidebar = 'left-sidebar-blog';
	// when both sidebars turned on reduce col size to 2 from 3.
	if ( is_singular( 'post' ) ) {
		$sidebar_pos = get_theme_mod( 'minimalio_settings_single_template_sidebar_position' );
	} else {
		$sidebar_pos = get_theme_mod( 'minimalio_settings_archive_template_sidebar_position' );
	}
} elseif ( is_archive() ) {
	$sidebar     = 'left-sidebar-blog';
	$sidebar_pos = get_theme_mod( 'minimalio_settings_archive_template_sidebar_position' );
} else {
	$sidebar     = 'left-sidebar';
	$sidebar_pos = get_theme_mod( 'minimalio_settings_sidebar_position' );
}

?>

<?php if ( 'both' === $sidebar_pos ) : ?>
	<div class="w-full lg:w-1/4 widget-area lg:pr-8 max-w-[50%]" id="left-sidebar" role="complementary">
<?php else : ?>
	<div class="w-full lg:w-1/3 widget-area lg:pr-8 max-w-[50%]" id="left-sidebar" role="complementary">
<?php endif; ?>
<?php dynamic_sidebar( $sidebar ); ?>

</div><!-- #left-sidebar -->
