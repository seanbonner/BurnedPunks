<?php

/**
 * The taxonomy template file.
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

$card      = get_theme_mod( 'minimalio_settings_portfolio_style' );
$post_card = minimalio_portfolio_postcard( $card );

$minimalio_nr_post = '-1';

// NY is_tax() returns True for custom taxonomy archive pages,
if ( is_tax() ) {
	$minimalio_queried_object = get_queried_object();
	// get the taxonomy
	$tax      = $minimalio_queried_object->taxonomy;
	$tax_id   = $minimalio_queried_object->term_id;
	$tax_name = $minimalio_queried_object->name;
	$tax_term = $minimalio_queried_object->slug;
} else {
	// just some fallback values in case taxonomy is not defined.
	$tax    = 'no_taxonomy';
	$tax_id = -1;
}
?>

<div class="w-full portfolio__wrapper wrapper content-area" id="archive-wrapper">

	<div class="<?php echo esc_attr( $minimalio_container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<main class="site-main" id="main">

				<?php if ( have_posts() ) : ?>
					<?php

					minimalio_get_part(
						'templates/blocks/posts/posts',
						[
							'nr_post'           => $minimalio_nr_post,
							'nr_columns'        => get_theme_mod( 'minimalio_settings_portfolio_columns', 4 ),
							'pagination_option' => 'no',
							'all_label'         => get_theme_mod( 'minimalio_settings_blog_all', 'All' ),
							'post_type'         => 'portfolio',
							'post_card'         => $post_card,
							'author_type'       => 'author-first',
							'categories'        => get_terms( [
								'taxonomy'   => 'portfolio-categories',
								'hide_empty' => true,
							] ),
							'enable_masonry'    => get_theme_mod( 'minimalio_settings_portfolio_type', 'grid' ),
							'filter'            => 'no',
							'extra_class'       => get_theme_mod( 'minimalio_settings_portfolio_style', 'style_1' ),
							'taxonomy'          => $tax,
							'taxonomy_id'       => $tax_id,
						]
					);
					?>

				<?php endif; ?>

			</main><!-- #main -->

		</div> <!-- .row -->

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php get_footer(); ?>
