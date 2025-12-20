<?php
/**
 * Dynamyc CSS
 * Used to display Theme CSS options
 */
global $parameters;

$css = '';
// Layout
if ( isset( $parameters['container_width'] ) && $parameters['container_width'] ) :
	$css .= sprintf( '.container {max-width: %spx } ', esc_attr( $parameters['container_width'] ) );
	$css .= sprintf( '.site.vertical {max-width: %spx } ', esc_attr( $parameters['container_width'] ) );
	$css .= sprintf( 'body {--wp--style--global--content-size: %spx} ', esc_attr( $parameters['container_width'] ) );
else :
	$css .= sprintf( '.container {max-width: %spx } ', '1240' );
endif;

if ( isset( $parameters['scrollbar'] ) && $parameters['scrollbar'] ) :
	$css .= sprintf( 'html {scrollbar-gutter:%s } ', esc_attr( $parameters['scrollbar'] ) );
endif;

if ( isset( $parameters['body_color'] ) && $parameters['body_color'] ) :
	$css .= sprintf( 'body{ background-color: %s } ', esc_attr( $parameters['body_color'] ) );
	$css .= sprintf( 'body{ --preset--background-color: %s } ', esc_attr( $parameters['body_color'] ) );
endif;

if ( isset( $parameters['body_background'] ) && $parameters['body_background'] ) :
	// Check header variation to determine which element gets the background
	$header_variation = get_theme_mod( 'minimalio_settings_header_variation' );
	if ( $header_variation === 'vertical' ) :
		$css .= sprintf( "body {background-image:url('%s') } ", esc_attr( $parameters['body_background'] ) );
		$css .= sprintf( "body {background-repeat:no-repeat; background-size: cover; background-position: center; background-attachment: fixed; } " );
	else :
		$css .= sprintf( ".site {background-image:url('%s') } ", esc_attr( $parameters['body_background'] ) );
	endif;
endif;


if ( isset( $parameters['main_font_color'] ) && $parameters['main_font_color'] ) :
	$css .= sprintf( 'body{color: %s } ', esc_attr( $parameters['main_font_color'] ) );
	$css .= sprintf( 'body{--preset--font-color: %s } ', esc_attr( $parameters['main_font_color'] ) );
endif;

if ( isset( $parameters['link_color'] ) && $parameters['link_color'] ) :
	$css .= sprintf( '.site .content-area a:not(.btn, .wp-block-button__link, .header__brand){color: %s } ', esc_attr( $parameters['link_color'] ) );
	$css .= sprintf( 'body{--preset--secondary-color: %s } ', esc_attr( $parameters['link_color'] ) );
endif;

if ( isset( $parameters['link_color_hover'] ) && $parameters['link_color_hover'] ) :
	$css .= sprintf( '.site a:hover{color: %s!important} ', esc_attr( $parameters['link_color_hover'] ) );
	$css .= sprintf( 'body{--preset--tertiary-color: %s } ', esc_attr( $parameters['link_color_hover'] ) );
endif;

if ( isset( $parameters['main_font'] ) && $parameters['main_font'] ) :
	$css .= sprintf( 'body {font-family:%s, %s } ', str_replace( '+', ' ', esc_attr( $parameters['main_font'] ) ), 'sans-serif' );
endif;

if ( isset( $parameters['main_font_size'] ) && $parameters['main_font_size'] ) :
	$css .= sprintf( '.site .site-content p {font-size:%s%s} ', esc_attr( $parameters['main_font_size'] ), 'px' );
	$css .= sprintf( '.site .site-content ul {font-size:%s%s} ', esc_attr( $parameters['main_font_size'] ), 'px' );
	$css .= sprintf( '.site .site-content li {font-size:%s%s} ', esc_attr( $parameters['main_font_size'] ), 'px' );
	$css .= sprintf( '.site .site-content span {font-size:%s%s} ', esc_attr( $parameters['main_font_size'] ), 'px' );
	$css .= sprintf( '.site .site-content a {font-size:%s%s} ', esc_attr( $parameters['main_font_size'] ), 'px' );
	$css .= sprintf( '.site .site-content {font-size:%s%s} ', esc_attr( $parameters['main_font_size'] ), 'px' );
endif;

if ( isset( $parameters['main_font_size_tablet'] ) && $parameters['main_font_size_tablet'] ) :
	$css .= sprintf( '@media(max-width: 1120px) { .site .site-content p {font-size:%s%s} }', esc_attr( $parameters['main_font_size_tablet'] ), 'px' );
	$css .= sprintf( '@media(max-width: 1120px) { .site .site-content ul {font-size:%s%s} }', esc_attr( $parameters['main_font_size_tablet'] ), 'px' );
	$css .= sprintf( '@media(max-width: 1120px) { .site .site-content li {font-size:%s%s} }', esc_attr( $parameters['main_font_size_tablet'] ), 'px' );
	$css .= sprintf( '@media(max-width: 1120px) { .site .site-content span {font-size:%s%s} }', esc_attr( $parameters['main_font_size_tablet'] ), 'px' );
	$css .= sprintf( '@media(max-width: 1120px) { .site .site-content a {font-size:%s%s} }', esc_attr( $parameters['main_font_size_tablet'] ), 'px' );
	$css .= sprintf( '@media(max-width: 1120px) { .site .site-content {font-size:%s%s} }', esc_attr( $parameters['main_font_size_tablet'] ), 'px' );

endif;

if ( isset( $parameters['main_font_size_mobile'] ) && $parameters['main_font_size_mobile'] ) :
	$css .= sprintf( '@media(max-width: 767px) { .site .site-content p {font-size:%s%s} }', esc_attr( $parameters['main_font_size_mobile'] ), 'px' );
	$css .= sprintf( '@media(max-width: 767px) { .site .site-content ul {font-size:%s%s} }', esc_attr( $parameters['main_font_size_mobile'] ), 'px' );
	$css .= sprintf( '@media(max-width: 767px) { .site .site-content li {font-size:%s%s} }', esc_attr( $parameters['main_font_size_mobile'] ), 'px' );
	$css .= sprintf( '@media(max-width: 767px) { .site .site-content span {font-size:%s%s} }', esc_attr( $parameters['main_font_size_mobile'] ), 'px' );
	$css .= sprintf( '@media(max-width: 767px) { .site .site-content a {font-size:%s%s} }', esc_attr( $parameters['main_font_size_mobile'] ), 'px' );
	$css .= sprintf( '@media(max-width: 767px) { .site .site-content {font-size:%s%s} }', esc_attr( $parameters['main_font_size_mobile'] ), 'px' );

endif;

if ( isset( $parameters['main_font_weight'] ) && $parameters['main_font_weight'] ) :
	$css .= sprintf( '.site .site-content p {font-weight:%s} ', esc_attr( $parameters['main_font_weight'] ) );
	$css .= sprintf( '.site .site-content ul {font-weight:%s} ', esc_attr( $parameters['main_font_weight'] ) );
	$css .= sprintf( '.site .site-content li {font-weight:%s} ', esc_attr( $parameters['main_font_weight'] ) );
	$css .= sprintf( '.site .site-content span {font-weight:%s} ', esc_attr( $parameters['main_font_weight'] ) );
	$css .= sprintf( '.site .site-content a {font-weight:%s} ', esc_attr( $parameters['main_font_weight'] ) );
	$css .= sprintf( '.site .site-content {font-weight:%s} ', esc_attr( $parameters['main_font_weight'] ) );
	$css .= sprintf( 'body {font-weight:%s} ', esc_attr( $parameters['main_font_weight'] ) );
endif;

if ( isset( $parameters['main_font_style'] ) && $parameters['main_font_style'] ) :
	$css .= sprintf( '.site .site-content p {font-style:%s} ', esc_attr( $parameters['main_font_style'] ) );
	$css .= sprintf( '.site .site-content ul {font-style:%s} ', esc_attr( $parameters['main_font_style'] ) );
	$css .= sprintf( '.site .site-content li {font-style:%s} ', esc_attr( $parameters['main_font_style'] ) );
	$css .= sprintf( '.site .site-content span {font-style:%s} ', esc_attr( $parameters['main_font_style'] ) );
	$css .= sprintf( '.site .site-content a {font-style:%s} ', esc_attr( $parameters['main_font_style'] ) );
	$css .= sprintf( '.site .site-content {font-style:%s} ', esc_attr( $parameters['main_font_style'] ) );
endif;

if ( isset( $parameters['main_font_line'] ) && $parameters['main_font_line'] ) :
	$css .= sprintf( 'body {line-height:%s} ', esc_attr( $parameters['main_font_line'] ) );
endif;

if ( isset( $parameters['main_font_spacing'] ) && $parameters['main_font_spacing'] ) :
	$css .= sprintf( 'body {letter-spacing:%s%s} ', esc_attr( $parameters['main_font_spacing'] ), 'px' );
endif;

if ( isset( $parameters['main_font_link_decoration'] ) && $parameters['main_font_link_decoration'] ) :
	$css .= sprintf( '.site .site-content a:not(.btn, .wp-block-button__link) {text-decoration:%s} ', esc_attr( $parameters['main_font_link_decoration'] ) );
endif;

if ( isset( $parameters['h6_size_tablet'] ) && $parameters['h6_size_tablet'] ) :
	$css .= sprintf( '@media(max-width: 1120px) { .site .site-content h6 {font-size:%s%s} }', esc_attr( $parameters['h6_size_tablet'] ), 'px' );
endif;

if ( isset( $parameters['h6_size_mobile'] ) && $parameters['h6_size_mobile'] ) :
	$css .= sprintf( '@media(max-width: 767px) { .site .site-content h6 {font-size:%s%s} } ', esc_attr( $parameters['h6_size_mobile'] ), 'px' );
endif;


if ( isset( $parameters['logo_width'] ) && $parameters['logo_width'] ) :
	$css .= sprintf( '.site .custom-logo-link .img-fluid {width:%s%s} ', esc_attr( $parameters['logo_width'] ), 'px' );
	$css .= sprintf( '.site .custom-logo-link .img-fluid {max-height:%s} ', 'none' );
endif;

if ( isset( $parameters['header_background'] ) && $parameters['header_background'] ) :
	$css .= sprintf( '.header {background: %s } ', esc_attr( $parameters['header_background'] ) );
endif;

if ( isset( $parameters['header_color'] ) && $parameters['header_color'] ) :
	$css .= sprintf( '.header ul li .nav__link {color: %s } ', esc_attr( $parameters['header_color'] ) );
	$css .= sprintf( '.header .socials__icon {color: %s } ', esc_attr( $parameters['header_color'] ) );
	$css .= sprintf( '.header .socials__icon {fill: %s } ', esc_attr( $parameters['header_color'] ) );
	$css .= sprintf( '.header .header__brand {color: %s } ', esc_attr( $parameters['header_color'] ) );
endif;

if ( isset( $parameters['header_color_hover'] ) && $parameters['header_color_hover'] ) :
	$css .= sprintf( '.site .menu-main-container .nav__link:hover {color: %s!important} ', esc_attr( $parameters['header_color_hover'] ) );
	$css .= sprintf( '.site .menu-main-container .nav__link:after, .posts-ajax__tab span:after {border-color: %s !important} ', esc_attr( $parameters['header_color_hover'] ) );
	$css .= sprintf( '.site .menu-main-container .current-menu-item > .nav__link {color: %s!important} ', esc_attr( $parameters['header_color_hover'] ) );
	$css .= sprintf( '.header .header__brand:hover {color: %s } ', esc_attr( $parameters['header_color_hover'] ) );
	$css .= sprintf( '.posts-ajax__tab.checked {color: %s } ', esc_attr( $parameters['header_color_hover'] ) );
	$css .= sprintf( '.posts-ajax__tab:hover {color: %s } ', esc_attr( $parameters['header_color_hover'] ) );
	$css .= sprintf( '.posts__tab.checked {color: %s } ', esc_attr( $parameters['header_color_hover'] ) );
	$css .= sprintf( '.posts__tab:hover {color: %s } ', esc_attr( $parameters['header_color_hover'] ) );
endif;

if ( isset( $parameters['submenu_background'] ) && $parameters['submenu_background'] ) :
	$css .= sprintf( '.site .menu-main-container .header__submenu-wrap {background-color: %s } ', esc_attr( $parameters['submenu_background'] ) );
endif;

if ( isset( $parameters['submenu_color'] ) && $parameters['submenu_color'] ) :
	$css .= sprintf( '.site .menu-main-container .header__submenu-wrap .menu__submenu--depth-1 li a {color: %s } ', esc_attr( $parameters['submenu_color'] ) );
endif;

if ( isset( $parameters['submenu_color_hover'] ) && $parameters['submenu_color_hover'] ) :
	$css .= sprintf( '.site .menu-main-container .header__submenu-wrap .menu__submenu--depth-1 li a:hover {color: %s } ', esc_attr( $parameters['submenu_color_hover'] ) );
	$css .= sprintf( '.site .menu-main-container .header__submenu-wrap .menu__submenu--depth-1 li a:after {border-color: %s } ', esc_attr( $parameters['submenu_color_hover'] ) );
endif;

if ( isset( $parameters['header_text_weight'] ) && $parameters['header_text_weight'] ) :
	$css .= sprintf( 'body .menu-main-container > .current-menu-item > .nav__link {font-weight:%s} ', esc_attr( $parameters['header_text_weight'] ) );
endif;

if ( isset( $parameters['header_text_font_size'] ) && $parameters['header_text_font_size'] ) :
	$css .= sprintf( 'body .menu-main-container .nav__link {font-size:%s%s} ', esc_attr( $parameters['header_text_font_size'] ), 'px' );
	$css .= sprintf( '.posts-ajax__tab span {font-size: %s%s } ', esc_attr( $parameters['header_text_font_size'] ), 'px' );
	$css .= sprintf( '.socials__link {font-size: %s%s } ', esc_attr( $parameters['header_text_font_size'] ), 'px' );
endif;

if ( isset( $parameters['submenu_font_size'] ) && $parameters['submenu_font_size'] ) :
	$css .= sprintf( '.site .menu-main-container .header__submenu-wrap .menu__submenu--depth-1 li a {font-size:%s%s} ', esc_attr( $parameters['submenu_font_size'] ), 'px' );
endif;

if ( isset( $parameters['header_text_font_style'] ) && $parameters['header_text_font_style'] ) :
	$css .= sprintf( 'body .menu-main-container .nav__link {font-style:%s} ', esc_attr( $parameters['header_text_font_style'] ) );
endif;

if ( isset( $parameters['header_text_font_spacing'] ) && $parameters['header_text_font_spacing'] ) :
	$css .= sprintf( 'body .menu-main-container .nav__link {letter-spacing:%s%s} ', esc_attr( $parameters['header_text_font_spacing'] ), 'px' );
endif;

if ( isset( $parameters['header_fixed_background'] ) && $parameters['header_fixed_background'] ) :
	$css .= sprintf( '.header.header__fixed.active {background: %s } ', esc_attr( $parameters['header_fixed_background'] ) );
endif;

if ( isset( $parameters['header_fixed_color'] ) && $parameters['header_fixed_color'] ) :
	$css .= sprintf( '.header.header__fixed.active ul li .nav__link {color: %s } ', esc_attr( $parameters['header_fixed_color'] ) );
	$css .= sprintf( '.header.header__fixed.active .header__brand {color: %s } ', esc_attr( $parameters['header_fixed_color'] ) );
endif;

if ( isset( $parameters['header_fixed_color_hover'] ) && $parameters['header_fixed_color_hover'] ) :
	$css .= sprintf( 'body .header.header__fixed.active .menu-main-container .nav__link:hover {color: %s} ', esc_attr( $parameters['header_fixed_color_hover'] ) );
	$css .= sprintf( 'body .header.header__fixed.active .menu-main-container .nav__link:hover {color: %s} ', esc_attr( $parameters['header_fixed_color_hover'] ) );
	$css .= sprintf( 'body .header.header__fixed.active .menu-main-container .nav__link:after {border-color: %s} ', esc_attr( $parameters['header_fixed_color_hover'] ) );
	$css .= sprintf( 'body .header.header__fixed.active .menu-main-container .current-menu-item .nav__link {color: %s} ', esc_attr( $parameters['header_fixed_color_hover'] ) );
	$css .= sprintf( 'body .header.header__fixed.active .menu-main-container .current-menu-item .nav__link {color: %s} ', esc_attr( $parameters['header_fixed_color_hover'] ) );
endif;

if ( isset( $parameters['blog_hover_color'] ) && $parameters['blog_hover_color'] ) :
	$css .= sprintf( '.blog-post-type .post-card__overlay {background-color: %s } ', esc_attr( $parameters['blog_hover_color'] ) );
endif;

if ( isset( $parameters['portfolio_hover_color'] ) && $parameters['portfolio_hover_color'] ) :
	$css .= sprintf( '.portfolio-post-type .post-card__overlay {background-color: %s } ', esc_attr( $parameters['portfolio_hover_color'] ) );
endif;

if ( isset( $parameters['footer_background'] ) && $parameters['footer_background'] ) :
	$css .= sprintf( 'footer.minimalio-footer  {background-color: %s } ', esc_attr( $parameters['footer_background'] ) );
endif;

if ( isset( $parameters['footer_font_color'] ) && $parameters['footer_font_color'] ) :
	$css .= sprintf( 'footer:not(.entry-footer) * {color: %s } ', esc_attr( $parameters['footer_font_color'] ) );
	$css .= sprintf( 'footer .socials__icon {color: %s } ', esc_attr( $parameters['footer_font_color'] ) );
	$css .= sprintf( 'footer .socials__icon {fill: %s } ', esc_attr( $parameters['footer_font_color'] ) );
endif;

if ( isset( $parameters['icons_bar_color'] ) && $parameters['icons_bar_color'] ) :
	$css .= sprintf( '.site .header__mobile-button .mobile-menu__open-icon {color: %s !important } ', esc_attr( $parameters['icons_bar_color'] ) );
endif;


if ( isset( $parameters['breaking_point'] ) && $parameters['breaking_point'] ) :
	$css .= sprintf( '@media (min-width: %s%s) { .header__mobile-button {display:none}} ', esc_attr( $parameters['breaking_point'] ), 'px' );
	$css .= sprintf( '@media (max-width: %s%s) { .header__mobile-button {display:block}} ', esc_attr( $parameters['breaking_point'] ), 'px' );
	$css .= sprintf( '@media (min-width: %s%s) { .menu-main-container {display:flex}} ', esc_attr( $parameters['breaking_point'] ), 'px' );
	$css .= sprintf( '@media (max-width: %s%s) { .menu-main-container {display:none!important}} ', esc_attr( $parameters['breaking_point'] ), 'px' );
	$css .= sprintf( '@media (min-width: %s%s) { .mobile-menu {display:none}} ', esc_attr( $parameters['breaking_point'] ), 'px' );
	$css .= sprintf( '@media (max-width: %s%s) { .mobile-menu.is-active {display:block}} ', esc_attr( $parameters['breaking_point'] ), 'px' );
	$css .= sprintf( '@media (min-width: %s%s) { .menu-main-container.centered {display:flex}} ', esc_attr( $parameters['breaking_point'] ), 'px' );
	$css .= sprintf( '@media (max-width: %s%s) { .menu-main-container.centered {display:none}} ', esc_attr( $parameters['breaking_point'] ), 'px' );
	$css .= sprintf( '@media (max-width: %s%s) { .header__social-block {display:none}} ', esc_attr( $parameters['breaking_point'] ), 'px' );
else :
	$css .= sprintf( '@media (min-width: %s) { .header__mobile-button {display:none}} ', '768px' );
	$css .= sprintf( '@media (max-width: %s) { .header__mobile-button {display:block}} ', '768px' );
	$css .= sprintf( '@media (min-width: %s) { .menu-main-container {display:flex}} ', '768px' );
	$css .= sprintf( '@media (max-width: %s) { .menu-main-container {display:none}} ', '768px' );
	$css .= sprintf( '@media (min-width: %s) { .mobile-menu {display:none}} ', '768px' );
	$css .= sprintf( '@media (max-width: %s) { .mobile-menu.is-active {display:block}} ', '768px' );
	$css .= sprintf( '@media (min-width: %s) { .menu-main-container.centered {display:flex}} ', '768px' );
	$css .= sprintf( '@media (max-width: %s) { .menu-main-container.centered {display:none}} ', '768px' );
	$css .= sprintf( '@media (max-width: %s) { .header__social-block {display:none}} ', '768px' );
endif;

if ( isset( $parameters['mobile_top_background'] ) && $parameters['mobile_top_background'] ) :
	$css .= sprintf( '.mobile-menu__logo-wrap {background-color: %s } ', esc_attr( $parameters['mobile_top_background'] ) );
endif;

if ( isset( $parameters['mobile_background'] ) && $parameters['mobile_background'] ) :
	$css .= sprintf( '.mobile-menu  {background-color: %s } ', esc_attr( $parameters['mobile_background'] ) );
endif;

if ( isset( $parameters['mobile_close_color'] ) && $parameters['mobile_close_color'] ) :
	$css .= sprintf( '.site .header__mobile-button .mobile-menu__close-icon  {color: %s } ', esc_attr( $parameters['mobile_close_color'] ) );
	$css .= sprintf( '.site .header__mobile-button .mobile-menu__close-icon:hover {color: %s } ', esc_attr( $parameters['mobile_close_color'] ) );
endif;

if ( isset( $parameters['mobile_color'] ) && $parameters['mobile_color'] ) :
	$css .= sprintf( '.mobile-menu__menu li .mobile-menu__link {color: %s } ', esc_attr( $parameters['mobile_color'] ) );
	$css .= sprintf( '.menu-item-has-children.mobile-menu__item::after {color: %s } ', esc_attr( $parameters['mobile_color'] ) );
	$css .= sprintf( '.mobile-menu__container-social a.socials__link svg {color: %s } ', esc_attr( $parameters['mobile_color'] ) );
endif;

if ( isset( $parameters['mobile_font_size'] ) && $parameters['mobile_font_size'] ) :
	$css .= sprintf( '.mobile-menu__menu li {font-size:%s%s} ', esc_attr( $parameters['mobile_font_size'] ), 'px' );
	$css .= sprintf( '.mobile-menu a.socials__link {font-size:%s%s} ', esc_attr( $parameters['mobile_font_size'] ), 'px' );
endif;

if ( isset( $parameters['mobile_font_style'] ) && $parameters['mobile_font_style'] ) :
	$css .= sprintf( '.mobile-menu__menu li a {font-style:%s} ', esc_attr( $parameters['mobile_font_style'] ) );
endif;

if ( isset( $parameters['mobile_font_spacing'] ) && $parameters['mobile_font_spacing'] ) :
	$css .= sprintf( '.mobile-menu__menu li a {letter-spacing:%s%s} ', esc_attr( $parameters['mobile_font_spacing'] ), 'px' );
endif;

if ( isset( $parameters['mobile_font_spacing'] ) && $parameters['mobile_font_spacing'] ) :
	$css .= sprintf( '.mobile-menu__menu li a {letter-spacing:%s%s} ', esc_attr( $parameters['mobile_font_spacing'] ), 'px' );
endif;

if ( isset( $parameters['lightbox_background_color'] ) && $parameters['lightbox_background_color'] ) :
	$css .= sprintf( '.pswp__bg {background: %s } ', esc_attr( $parameters['lightbox_background_color'] ) );
endif;

if ( isset( $parameters['lightbox_icons_color'] ) && $parameters['lightbox_icons_color'] ) :
	$css .= sprintf( '.pswp--open svg * {fill: %s } ', esc_attr( $parameters['lightbox_icons_color'] ) );
	$css .= sprintf( '.pswp--open svg * {stroke: %s } ', esc_attr( $parameters['lightbox_icons_color'] ) );
	$css .= sprintf( '.pswp--open .pswp__counter {color: %s } ', esc_attr( $parameters['lightbox_icons_color'] ) );
endif;

if ( isset( $parameters['lightbox_captions_bg_color'] ) && $parameters['lightbox_captions_bg_color'] ) :
	$css .= sprintf( '.pswp__custom-caption {background-color: %s } ', esc_attr( $parameters['lightbox_captions_bg_color'] ) );
endif;

if ( isset( $parameters['lightbox_captions_font_color'] ) && $parameters['lightbox_captions_font_color'] ) :
	$css .= sprintf( '.pswp__custom-caption {color: %s } ', esc_attr( $parameters['lightbox_captions_font_color'] ) );
endif;

if ( isset( $parameters['lightbox_captions_font_size'] ) && $parameters['lightbox_captions_font_size'] ) :
	$css .= sprintf( '.pswp__custom-caption {font-size: %s%s } ', esc_attr( $parameters['lightbox_captions_font_size'] ), 'px' );
endif;

if ( null !== get_theme_mod( 'minimalio_lightbox_hide_arrows_mobile_settings' ) && get_theme_mod( 'minimalio_lightbox_hide_arrows_mobile_settings' ) === 'yes' ) :
	$css .= '@media (max-width: 768px) { .pswp__button--arrow {display: none !important} } ';
endif;

if ( null !== get_theme_mod( 'minimalio_lightbox_hide_image_counter_settings' ) && get_theme_mod( 'minimalio_lightbox_hide_image_counter_settings' ) === 'yes' ) :
	$css .= '.pswp__counter {display: none !important} ';
endif;

// Hide default PhotoSwipe icons only when using custom icon styles
$icon_style = get_theme_mod( 'minimalio_lightbox_icons_style_settings' );
if ( $icon_style === 'style_1' || $icon_style === 'style_2' ) :
	$css .= '.pswp__button--arrow .pswp__icn, .pswp__button--close .pswp__icn { display: none !important; } ';
endif;

if ( isset( $parameters['site_title_size'] ) && $parameters['site_title_size'] ) :
	$css .= sprintf( '.header__brand {font-size: %s%s !important} ', esc_attr( $parameters['site_title_size'] ), 'px' );
endif;

return $css;