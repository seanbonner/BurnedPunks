<?php
/**
 * Partial template for content in page.php
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">


	<?php if (( get_theme_mod( 'minimalio_settings_show_page_title' ) === 'yes' ) && !is_front_page()) : ?>
		<header class="entry-header">
			<?php $titleSize = get_theme_mod( 'minimalio_settings_page_title_size', 'h2' ); ?>
            <?php $titleAlign = get_theme_mod( 'minimalio_settings_page_title_align' ); ?>

			<?php the_title( "<h1 class=\"entry-title pb-8 mb-0 break-words $titleSize $titleAlign \">", '</h1>' ); ?>

		</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">

		<?php the_content(); ?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

	</footer><!-- .entry-footer -->

</article>
