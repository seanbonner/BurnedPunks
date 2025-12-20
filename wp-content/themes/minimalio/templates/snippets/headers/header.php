<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$minimalio_decoration = get_theme_mod( 'minimalio_settings_link_decoration' );
if ( $minimalio_decoration === 'underline' ) {
	$minimalio_class = 'hidden menu-main-container lg:block underlined';
} elseif ( $minimalio_decoration === 'line-through' ) {
	$minimalio_class = 'hidden line-through menu-main-container lg:block';
} else {
	$minimalio_class = 'hidden menu-main-container lg:block';
}

$weight = get_theme_mod( 'minimalio_settings_link_weight' );

$social_enable = get_theme_mod( 'minimalio_settings_social_media_location' );
$social_media_style = get_theme_mod( 'minimalio_settings_social_media_style' );

?>

<div id="wrapper-navbar" itemscope itemtype="http://schema.org/WebSite">

	<div class="header__row flex flex-wrap justify-between items-center
	<?php
	if ( get_theme_mod( 'minimalio_settings_logo_position' ) === 'center' ) {
		echo 'center-logo';
	}
	if ( get_theme_mod( 'minimalio_settings_menu_position' ) === 'center' ) {
		echo ' center-menu';
	}
	?>
	">
	<div class="w-auto p-0 header__col-left">
		<!-- Your site title as branding in the menu -->
		<?php if ( ! has_custom_logo() ) { ?>

			<a class="inline-block mr-0 text-black header__brand whitespace-nowrap" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>"
			title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a>

			<?php
		} elseif ( wp_is_mobile() && get_theme_mod( 'minimalio_mobile-logo-settings' ) ) {
				
			?>

			<a class="header__logo-link-mobile" href="<?php echo esc_url( home_url() ); ?>"
			title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<img src="<?php echo wp_get_attachment_image_url( get_theme_mod( 'minimalio_mobile-logo-settings' ), 'full' ); ?>"
				alt="mobile-logo" class="header__logo-mobile header__logo-mobile--<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			</a>
				<?php
		} else {
			the_custom_logo();
		}
		?>
		<!-- end custom logo -->
	</div><!-- end header col left -->
	<div class="flex flex-wrap items-center w-auto p-0 header__col-right">
		<!-- The WordPress Main Menu goes here -->
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				[
					'theme_location'  => 'primary',
					'container'       => 'nav',
					'container_class' => $minimalio_class . ' header__main-menu-' . $weight,
					'walker'          => new Minimalio_BemNavWalker(),
					'items_wrap'      => '<ul class="flex flex-wrap justify-end m-0 align-middle header__menu gap-x-8">%3$s</ul>',
					'bem_block'       => 'nav',
					'before_ul'       => '<div class="absolute invisible w-full p-4 mx-auto my-0 text-left -translate-x-4 bg-white opacity-0 header__submenu-wrap max-w-72 -z-10">',
					'after_ul'        => '</div>',
				]
			);
		}

		if ( has_nav_menu( 'secondary' ) ) {
			wp_nav_menu(
				[
					'theme_location'  => 'secondary',
					'container_class' => $minimalio_class . ' header__secondary-menu-' . $weight,
					'walker'          => new Minimalio_BemNavWalker(),
					'items_wrap'      => '<ul class="flex flex-wrap justify-end m-0 align-middle header__menu header__secondary__menu gap-x-8">%3$s</ul>',
					'bem_block'       => 'nav',
					'before_ul'       => '<div class="absolute invisible w-full p-4 mx-auto my-0 text-left -translate-x-4 bg-white opacity-0 header__submenu-wrap max-w-72 -z-10">',
					'after_ul'        => '</div>',
				]
			);
		}
		?>

		<span class="flex flex-wrap justify-end gap-2 social__block header__social-block">
		<?php if ( $social_enable === 'header' || $social_enable === 'both' ) : ?>
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
		<?php endif; ?>
		</span>
	</div><!-- end header col right -->
	</div><!-- end wrapper header -->

	<?php if ( is_active_sidebar( 'headerfull' ) ) { ?>
		<!-- header widget -->
		<div class="p-0 header-widget-wrapper">

				<?php dynamic_sidebar( 'headerfull' ); ?>

			</div>
		<!-- end header widget -->	
		<?php } ?>


</div><!-- #wrapper-navbar end -->
