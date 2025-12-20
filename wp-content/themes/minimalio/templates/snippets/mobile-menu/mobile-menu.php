<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if (get_theme_mod( 'minimalio_settings_mobile_menu_width' ) === 'full-width' )  {
	$mobile_menu_width = 'full-width';
} else {
	$mobile_menu_width = 'right-wrapper';
} 

$social_media_style = get_theme_mod( 'minimalio_settings_social_media_style' );
?>

<div id="mobilemenu" class="mobile-menu <?php echo esc_attr( $mobile_menu_width ); ?> fixed overflow-y-scroll top-0 bottom-0 right-0 max-w-full w-full bg-white sm:[&.right-wrapper]:max-w-sm">

	<div class="relative p-0 bg-white mobile-menu__logo-wrap">



		<?php
		$minimalio_mobile_logo = get_theme_mod( 'minimalio_settings_mobile_logo' );
		if ( !empty($minimalio_mobile_logo) && $minimalio_mobile_logo !== 'nologo' ) :
			if ( $minimalio_mobile_logo !== 'general' ) :
				if ( $minimalio_mobile_logo === 'white' ) {
					$logo = get_theme_mod( 'minimalio_white-logo-settings' );
				} else {
					$logo = get_theme_mod( 'minimalio_mobile-logo-settings' );
				}
				?>
	
				<a class="mobile-menu__logo-link" rel="home" href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<img src="<?php echo wp_get_attachment_image_url( $logo, 'full' ); ?>" alt="" class="mobile-menu__logo mobile-menu__logo--<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				</a>

				<?php
			else :
					the_custom_logo();
			endif;
		endif;
		?>

	</div>

	<div class="pb-8 mobile-menu__container">

			<?php

			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					[
						'theme_location'  => 'primary',
						'container'       => 'nav',
						'container_class' => 'minimalio-mobile-menu',
						'walker'          => new Minimalio_BemNavWalker(),
						'items_wrap'      => '<ul class="px-8 py-4 mobile-menu__menu">%3$s</ul>',
						'bem_block'       => 'mobile-menu',
					]
				);
			}


			if ( has_nav_menu( 'secondary' ) ) {
				wp_nav_menu(
					[
						'theme_location'  => 'secondary',
						'container'       => 'nav',
						'container_class' => 'minimalio-mobile-menu',
						'walker'          => new Minimalio_BemNavWalker(),
						'items_wrap'      => '<ul class="px-8 py-4 mobile-menu__menu">%3$s</ul>',
						'bem_block'       => 'mobile-menu',
					]
				);
			}
			?>

		<div class="container px-8 mobile-menu__container-social">
			<span class="flex flex-wrap justify-center gap-2 mt-8 social__block">
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
		</div>


	</div>
</div>
