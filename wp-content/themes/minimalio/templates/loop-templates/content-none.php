<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<section class="no-results not-found">

	<header class="page-header">

        <?php $titleSize = get_theme_mod( 'minimalio_settings_page_title_size', 'h2' ); ?>
        <?php $titleAlign = get_theme_mod( 'minimalio_settings_page_title_align' ); ?>
        <h1 class="entry-title pb-8 mb-0 break-words <?php echo esc_html($titleSize) ?> <?php echo esc_html($titleAlign) ?>">
            <?php esc_html_e( 'Nothing Found', 'minimalio' ); ?>
        </h1>

	</header><!-- .page-header -->

	<div class="page-content">

		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :
			?>

			<p>
			<?php
			printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'minimalio' ), [
				'a' => [
					'href' => [],
				],
			] ), esc_url( admin_url( 'post-new.php' ) ) );
			?>
				</p>

		<?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'minimalio' ); ?></p>
			<?php
				get_search_form();
		else :
			?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'minimalio' ); ?></p>
			<?php
				get_search_form();
		endif;
		?>
	</div><!-- .page-content -->

</section><!-- .no-results -->
