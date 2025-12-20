<?php

/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$minimalio_container = get_theme_mod( 'minimalio_settings_container_type', 'container' );
$card                = get_theme_mod( 'minimalio_settings_blog_post_card', 'style_1' );
$post_card           = minimalio_post_postcard( $card );
$minimalio_display   = get_theme_mod( 'minimalio_settings_blog_pagination', 'pagination' );

?>
<div class="wrapper" id="archive-wrapper">
	<div class="<?php echo esc_attr( $minimalio_container ); ?>" id="content" tabindex="-1">
		<div class="row">
			<!-- Do the left sidebar check -->
			<?php get_template_part( 'templates/global-templates/checker/left-sidebar-check' ); ?>
			<main class="w-full site-main" id="main">
				<?php if ( have_posts() ) : ?>
					<?php
					if ( $minimalio_display === 'pagination' ) :
						minimalio_get_part(
							'templates/blocks/posts/posts',
							[
								'nr_post'           => get_theme_mod( 'minimalio_settings_blog_posts_per_page', 12 ),
								'nr_columns'        => get_theme_mod( 'minimalio_settings_blog_columns', 4 ),
								'pagination_option' => $minimalio_display,
								'all_label'         => get_theme_mod( 'minimalio_settings_blog_all', 'All' ),
								'post_type'         => 'post',
								'post_card'         => $post_card,
								'author_type'       => 'author-first',
								'categories'        => get_categories( [ 'hide_empty' => true ] ),
								'enable_masonry'    => get_theme_mod( 'minimalio_settings_blog_type', 'grid' ),
								'filter'            => get_theme_mod( 'minimalio_settings_archive_template_filter_enable', 'yes' ),
							]
						);
					else :
						minimalio_get_part(
							'templates/blocks/posts-ajax/posts-ajax',
							[
								'nr_post'           => get_theme_mod( 'minimalio_settings_blog_posts_per_page', 12 ),
								'nr_columns'        => get_theme_mod( 'minimalio_settings_blog_columns', 4 ),
								'pagination_option' => $minimalio_display,
								'all_label'         => get_theme_mod( 'minimalio_settings_blog_all', 'All' ),
								'post_type'         => 'post',
								'post_card'         => $post_card,
								'author_type'       => 'author-first',
								'categories'        => get_categories( [ 'hide_empty' => true ] ),
								'enable_masonry'    => get_theme_mod( 'minimalio_settings_blog_type', 'grid' ),
								'filter'            => get_theme_mod( 'minimalio_settings_archive_template_filter_enable', 'yes' ),
							]
						);
					endif;
					?>
				<?php endif; ?>
			</main><!-- #main -->
			<!-- Do the right sidebar check -->
			<?php get_template_part( 'templates/global-templates/checker/right-sidebar-check' ); ?>
		</div> <!-- .row -->
	</div><!-- #content -->
</div><!-- #archive-wrapper -->
<?php get_footer(); ?>
