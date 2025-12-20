<?php
/**
 * The template for displaying page-404 pages (Not Found)
 */

defined( 'ABSPATH' ) || exit;

$minimalio_heading    = 'Oops!';
$minimalio_subheading = 'This could be a spelling error in the URL or the page has been removed. ';
if ( get_theme_mod( 'minimalio_settings_container_type' ) ) {
	$minimalio_container = get_theme_mod( 'minimalio_settings_container_type' );
} else {
	$minimalio_container = 'container';
}

get_header(); ?>

	<div id="page-404" class="page-404">
		<div class="page-404__container <?php echo esc_attr( $minimalio_container ); ?> pt-8 pb-8">

		<?php if ( get_theme_mod( 'minimalio_settings_default_404' ) <= 1 ) : ?>

					<div class="text-center page-404__wrap">
						<div class="page-404__404">
								<h1><?php '404'; ?></h1>
						</div>
						<div class="page-404__column-title">
							<?php echo esc_html( $minimalio_heading ); ?>
						</div>
						<div class="pb-8 page-404__column-subtitle">
							<?php echo esc_html( $minimalio_subheading ); ?>
						</div>
					</div>

			<?php
			else :
				$minimalio_my_page_id = get_theme_mod( 'minimalio_settings_default_404' ); // your page or post ID
				$minimalio_my_page    = get_post( $minimalio_my_page_id ); // retrieves the page via the ID
				$minimalio_content    = $minimalio_my_page->post_content; // gets the unfiltered page content
				$minimalio_content    = apply_filters( 'the_content', $minimalio_content ); // cleanup content
				$minimalio_content    = str_replace( ']]>', ']]&gt;', $minimalio_content ); // cleanup content
				$minimalio_title      = $minimalio_my_page->post_title; // retrieves page title and sets the variable

				echo wp_kses_post($minimalio_content) ; // show page content

			endif;
			?>
		</div>
	</div>


<?php get_footer(); ?>
