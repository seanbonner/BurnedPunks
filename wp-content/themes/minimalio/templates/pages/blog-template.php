<?php
/**
 * Template Name: Blog Template
 *
 * This template can be used to override the default template and sidebar setup
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
if ( get_theme_mod( 'minimalio_settings_container_type' ) ) {
	$minimalio_container = get_theme_mod( 'minimalio_settings_container_type' );
} else {
	$minimalio_container = 'container';
}

$card      = get_theme_mod( 'minimalio_settings_blog_post_card' );
$post_card = minimalio_post_postcard( $card );

$minimalio_display = get_theme_mod( 'minimalio_settings_blog_pagination', 'pagination' );

?>

<div class="portfolio__wrapper wrapper" id="archive-wrapper">

	<div class="<?php echo esc_attr( $minimalio_container ); ?>" id="content" tabindex="-1">

			<div class="row">

			<?php get_template_part( 'templates/global-templates/checker/left-sidebar-check' ); ?>

			<main class="site-main" id="main">

			<div class="row">

				<!-- Page content -->
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<?php get_template_part( 'templates/loop-templates/content', 'page' ); ?>
				<?php endwhile; // end of the loop. ?>
				<!-- End page content -->
				<!-- Blog list -->
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

						minimalio_get_part( 'templates/blocks/posts-ajax/posts-ajax',
							[
								'nr_post'           => get_theme_mod( 'minimalio_settings_blog_posts_per_page' ),
								'nr_columns'        => get_theme_mod( 'minimalio_settings_blog_columns', 4 ),
								'pagination_option' => get_theme_mod( 'minimalio_settings_blog_pagination' ),
								'all_label'         => get_theme_mod( 'minimalio_settings_blog_all', 'All' ),
								'post_type'         => 'post',
								'post_card'         => $post_card,
								'author_type'       => 'author-first',
								'categories'        => get_categories( [ 'hide_empty' => true ] ),
								'enable_masonry'    => get_theme_mod( 'minimalio_settings_blog_type', 'grid' ),
								'filter'            => get_theme_mod( 'minimalio_settings_archive_template_filter_enable' ),

							]
						);
					endif;
					?>

				<?php endif; ?>
				<!-- End Blog list -->

				</div> <!-- .row -->

			</main><!-- #main -->

		<?php get_template_part( 'templates/global-templates/checker/right-sidebar-check' ); ?>

				</div> <!-- .row -->

	</div><!-- #content -->

	</div><!-- #archive-wrapper -->

<?php get_footer(); ?>
