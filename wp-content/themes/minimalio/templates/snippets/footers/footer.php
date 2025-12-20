<?php
/**
 * Footer template
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

	$copyright_mesage = get_theme_mod( 'minimalio_settings_copyright' );
	$developer        = get_theme_mod( 'minimalio_settings_developer' );

if ( get_theme_mod( 'minimalio_settings_footer_logo' ) === 'general' ) {
	$footer_logo = get_custom_logo();
} elseif ( get_theme_mod( 'minimalio_settings_footer_logo' ) === 'white' ) {
	$footer_logo = get_theme_mod( 'minimalio_white-logo-settings' );
} else {
	$footer_logo = 'disabled';
}

	$social_enable = get_theme_mod( 'minimalio_settings_social_media_location' );
	$social_media_style = get_theme_mod( 'minimalio_settings_social_media_style' );

?>

<div class="footer__section-row row">

	<div class="w-full footer__left md:w-1/3">
		<div class="site-info">

			<span class="flex justify-center footer__copyright-first md:justify-start">


				<?php
				if ( get_theme_mod( 'minimalio_settings_footer_logo' ) === 'general' ) :
					the_custom_logo();
					elseif ( get_theme_mod( 'minimalio_settings_footer_logo' ) === 'white' ) :
						?>
						<a href="<?php global $wp; echo esc_url( home_url( $wp->request ) ) ?>" class="mr-4 navbar-brand custom-logo-link" rel="home" aria-current="page">
							<?php
									$logo = get_theme_mod( 'minimalio_white-logo-settings' );
									echo '<img src="' . wp_get_attachment_image_url( $logo ) . '" alt="' . get_bloginfo( 'name' ) . '">';
							?>
						</a>
						<?php
					else :
endif;
					?>

					<span class="mr-4 footer__copyright-first-copy">
						<?php if ( $copyright_mesage ) : ?>

							<?php echo wp_kses_post( $copyright_mesage ); ?>

						<?php endif; ?>
					</span>
					<?php if ( get_theme_mod( 'minimalio_settings_footer_menu' ) === 'yes' ) : ?>
							<span class="pb-2 footer__copyright-menu">
								<?php

									wp_nav_menu(
										[
											'theme_location' => 'footer_menu',
											'container_id' => 'copyright-menu',
											'menu_class'   => 'copyright-menu list-none m-0 p-0',
											'fallback_cb'  => '',
											'menu_id'      => 'footer-copyright-menu',
											'depth'        => 1,
											'walker'       => new Minimalio_BemNavWalker(),
										]
									);
								?>
							</span>

					<?php endif; ?>




				</span>

		</div><!-- .site-info -->
	</div>
	<div class="flex justify-center w-full mt-4 text-center footer__center md:w-1/3 md:mt-0">

					<?php if ( $social_enable === 'footer' || $social_enable === 'both' ) : ?>

						<span class="flex flex-wrap justify-center gap-2 pt-2 social__block footer__social-block">
							<?php if ( $socials = minimalio_get_social_links() ) : ?>

										<?php foreach ( $socials as $social => $url ) : ?>

											<a class="socials__link <?php echo $social_media_style; ?> relative w-5 h-5 block socials__link--<?php echo $social; ?>" aria-label="<?php echo $social; ?>" target="_blank" href="<?php echo $url; ?>">
												<?php
												echo minimalio_get_svg( $social . $social_media_style, [
													'role' => 'presentation',
													'title' => $social,
													'class' => 'socials__icon w-full h-full object-fill socials__icon--' . $social,
												]);
												?>
											</a>

										<?php endforeach; ?>

								<?php endif; ?>
						</span>
					<?php endif; ?>


	</div>

	<div class="w-full footer__right md:w-1/3">
		<div class="flex justify-center mt-4 footer__copyright-last md:justify-end md:mt-0">
			<?php if ( $developer ) : ?>

				<?php echo wp_kses_post($developer); ?>

			<?php endif; ?>
		</div>
	</div>

</div><!-- row end -->
