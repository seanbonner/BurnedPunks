<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID() ?>">

	<header class="entry-header">

		<?php
        $titleSize = get_theme_mod( 'minimalio_settings_page_title_size', 'h2' );
        $titleAlign = get_theme_mod( 'minimalio_settings_page_title_align' );

        the_title(
			sprintf( "<h1 class=\"entry-title pb-8 mb-0 break-words $titleSize $titleAlign\"><a href=\"%s\" rel=\"bookmark\">", esc_url( get_permalink() ) ),
			'</a></h1>'
		);
		?>


		<?php if ( 'post' === get_post_type() ) : ?>

			<div class="entry-meta">
				<?php minimalio_posted_on(); ?>
			</div><!-- .entry-meta -->

		<?php endif; ?>

	</header><!-- .entry-header -->

	<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

	<div class="entry-content">

		<?php the_excerpt(); ?>

		<?php
		wp_link_pages(
			[
				'before' => '<div class="page-links">' . __( 'Pages:', 'minimalio' ),
				'after'  => '</div>',
			]
		);
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php minimalio_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article>
