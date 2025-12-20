<?php
/**
 * Template Name: Right Sidebar
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
?>

<div class="wrapper" id="page-wrapper">

	<div class="<?php echo esc_attr( $minimalio_container ); ?>" id="content">

		<div class="row">

			<div
				class="overflow-hidden lg:grow lg:shrink-0 lg:basis-0 content-area" id="primary">

				<main class="site-main" id="main">

					<?php
					while ( have_posts() ) :
						the_post();
						?>

						<?php get_template_part( 'templates/loop-templates/content', 'page' ); ?>

						<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
						?>

					<?php endwhile; // end of the loop. ?>

				</main><!-- #main -->

			</div><!-- #primary -->

			<?php get_template_part( 'templates/global-templates/sidebar-templates/sidebar', 'right' ); ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #page-wrapper -->

<?php get_footer(); ?>
