<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
$widgets = get_theme_mod( 'minimalio_settings_footer_widgets' );
?>

<!--Close page container -->
</div>
<!-- ******************* FOOTER ******************* -->
<footer id="wrapper-footer" class="minimalio-footer">

	<?php if ( $widgets !== 'no' and is_active_sidebar( 'footerfull' ) == 'true' ) : ?>

		<div class="footer__widgets <?php
		if ( ( get_theme_mod( 'minimalio_settings_footer_container' ) === 'container-fluid' ) ) {
			echo 'container-fluid';
		} else {
			echo 'container';
		}
		?>">

			<?php get_template_part( 'templates/global-templates/sidebar-templates/sidebar', 'footerfull' ); ?>

		</div>

	<?php endif; ?>

	<?php if ( get_theme_mod( 'minimalio_settings_enable_copyright_section' ) !== 'no' ) : ?>

		<div class="footer__copyright <?php
		if ( ( get_theme_mod( 'minimalio_settings_footer_container' ) === 'container-fluid' ) ) {
			echo 'container-fluid';
		} else {
			echo 'container';
		}
		?>">
			<div class="wrapper footer__wrapper">

				<div id="copyright" class="footer__section">

					<?php get_template_part( 'templates/snippets/footers/footer' ); ?>

				</div>
			</div>


		</div>
	<?php endif; ?>

</footer>

</div><!-- page end -->

<?php wp_footer(); ?>

</body>

</html>