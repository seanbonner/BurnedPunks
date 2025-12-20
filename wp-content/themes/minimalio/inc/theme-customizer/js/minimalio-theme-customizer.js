/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
(function ($) {

    // Update the site title in real time...
    wp.customize('blogname', function (value) {
        value.bind(function (newval) {
            $('#site-title a').html(newval);
        });
    });

    //Update the site description in real time...
    wp.customize('blogdescription', function (value) {
        value.bind(function (newval) {
            $('.site-description').html(newval);
        });
    });

    /**
     * Layout
     */
    //Update site background color...
    wp.customize('minimalio_settings_color_background', function (value) {
        value.bind(function (newval) {
            $('body').css('background', newval);
        });
    });


    /**
     * Typography
     */


    //Font size
    wp.customize('minimalio_settings_google_font_size', function (value) {
        value.bind(function (font_size) {
            $('.site .site-content p').css('font-size', font_size + 'px');
        });
    });

    //Font weight
    wp.customize('minimalio_typography_settings_google_font_wight', function (value) {
        value.bind(function (font_weight) {
            $('.site .site-content p').css('font-weight', font_weight);
        });
    });

    //Font style
    wp.customize('minimalio_settings_google_font_style', function (value) {
        value.bind(function (font_style) {
            $('.site .site-content p').css('font-style', font_style);
        });
    });

    //Font line-height
    wp.customize('minimalio_settings_google_line_height', function (value) {
        value.bind(function (line_height) {
            $('.site .site-content p').css('line-height', line_height + 'px');
        });
    });

    //Font spacing
    wp.customize('minimalio_settings_google_letter_spacing', function (value) {
        value.bind(function (spacing) {
            $('.site .site-content p').css('letter-spacing', spacing + 'px');
        });
    });

    //Font color
    wp.customize('minimalio_settings_font_color', function (value) {
        value.bind(function (font_color_general) {
            $('body').css('color', font_color_general);
        });
    });

    //Font H1 font size
    wp.customize('minimalio_settings_google_font_h1_size', function (value) {
        value.bind(function (h1_font) {
            $('.site .site-content h1 .h1').css('font-size', h1_font + 'px');
        });
    });

    //Font H2 font size
    wp.customize('minimalio_settings_google_font_h2_size', function (value) {
        value.bind(function (h2_font) {
            $('.site .site-content h2 .h2').css('font-size', h2_font + 'px');
        });
    });

    //Font H3 font size
    wp.customize('minimalio_settings_google_font_h3_size', function (value) {
        value.bind(function (h3_font) {
            $('.site .site-content h3 .h3').css('font-size', h3_font + 'px');
        });
    });

    //Font H4 font size
    wp.customize('minimalio_settings_google_font_h4_size', function (value) {
        value.bind(function (h4_font) {
            $('.site .site-content h4 .h4').css('font-size', h4_font + 'px');
        });
    });

    //Font H5 font size
    wp.customize('minimalio_settings_google_font_h5_size', function (value) {
        value.bind(function (h5_font) {
            $('.site .site-content h5 .h5').css('font-size', h5_font + 'px');
        });
    });

    //Font H6 font size
    wp.customize('minimalio_settings_google_font_h6_size', function (value) {
        value.bind(function (h6_font) {
            $('.site .site-content h6 .h6').css('font-size', h6_font + 'px');
        });
    });

    //Font link decoration
    wp.customize('minimalio_settings_google_link_decoration', function (value) {
        value.bind(function (link_decoration) {
            $('.site .site-content a:not(.btn)').css('text-decoration', link_decoration);
        });
    });

    //Font link color
    wp.customize('minimalio_settings_link_color', function (value) {
        value.bind(function (link_color) {
            $('.site a:not(.btn)').css('color', link_color);
        });
    });

    //Font link color hover
    wp.customize('minimalio_settings_hover_color', function (value) {
        value.bind(function (link_color_hover) {
            $('.site a:not(.btn):hover').css('color', link_color_hover);
        });
    });


    //Logo width
    wp.customize('minimalio_settings_logo_width', function (value) {
        value.bind(function (logo_width) {
            $('.site .custom-logo-link .img-fluid').css('width', logo_width + 'px');
            $('.site .custom-logo-link .img-fluid').css('max-height', 'none');
        });
    });

    //Header backgrund
    wp.customize('minimalio_settings_header_background', function (value) {
        value.bind(function (background) {
            $('.header').css('background', background);
        });
    });

    //Header menu color
    wp.customize('minimalio_settings_header_color', function (value) {
        value.bind(function (header_color) {
            $('.header ul li .nav__link').css('color', header_color);
        });
    });


    //Header menu color hover
    wp.customize('minimalio_settings_header_color_hover', function (value) {
        value.bind(function (header_color_hover) {
            $('.site .menu-main-container .current-menu-item a.nav__link:hover').css('color', header_color_hover);
            $('.site .menu-main-container .current-menu-item a.nav__link').css('color', header_color_hover);
            $('.site .menu-main-container .current-menu-item a.nav__link::after').css('color', header_color_hover);
        });
    });

    //Header backgrund - fixed
    wp.customize('minimalio_settings_fixed_header_background', function (value) {
        value.bind(function (background_fixed) {
            $('.header.header__fixed.active').css('background', background_fixed);
        });
    });

    //Header menu color - fixed
    wp.customize('minimalio_settings_fixed_color', function (value) {
        value.bind(function (header_color_fixed) {
            $('.header ul li .nav__link').css('color', header_color_fixed);
        });
    });


    //Header menu color hover - fixed
    wp.customize('minimalio_settings_fixed_color_hover', function (value) {
        value.bind(function (header_color_hover_fixed) {
            $('body .header.header__fixed.active ul li .nav__link:hover').css('color', header_color_hover_fixed);
            $('body .menu-main-container .current-menu-item a.nav__link').css('color', header_color_hover_fixed);
            $('body .menu-main-container .current-menu-item a.nav__link::after').css('color', header_color_hover_fixed);
        });
    });

    //Font size - header
    wp.customize('minimalio_settings_menu_google_font_size', function (value) {
        value.bind(function (font_size_header) {
            $('body .menu-main-container .nav__link').css('font-size', font_size_header + 'px');
        });
    });

    //Font weight - header
    wp.customize('minimalio_settings_menu_google_font_wight', function (value) {
        value.bind(function (font_weight_header) {
            $('body .menu-main-container .nav__link').css('font-weight', font_weight_header);
        });
    });

    //Font style - header
    wp.customize('minimalio_settings_menu_google_font_style', function (value) {
        value.bind(function (font_style_header) {
            $('body .menu-main-container .nav__link').css('font-style', font_style_header);
        });
    });

    //Font spacing - header
    wp.customize('minimalio_settings_menu_google_letter_spacing', function (value) {
        value.bind(function (spacing_header) {
            $('body .menu-main-container .nav__link').css('letter-spacing', spacing_header + 'px');
        });
    });


    //Portfolio hover color
    wp.customize('minimalio_settings_portfolio_hover_color', function (value) {
        value.bind(function (portfolio_hover_color) {
            $('.post-card__overlay').css('background-color', portfolio_hover_color);
        });
    });

    //Footer background
    wp.customize('minimalio_settings_footer_background', function (value) {
        value.bind(function (footer_background) {
            $('footer').css('background-color', footer_background);
        });
    });

    //Footer font color
    wp.customize('minimalio_settings_footer_font_color', function (value) {
        value.bind(function (footer_font_color) {
            $('footer *').css('color', footer_font_color);
        });
    });

    //Mobile icon color
    wp.customize('minimalio_settings_mobile_menu_icon_colour', function (value) {
        value.bind(function (menu_icon) {
            $('.site .header__mobile-button').css('color', menu_icon + '!important');
        });
    });

    //Mobile icon color - fixed
    wp.customize('minimalio_settings_mobile_menu_icon_colour_fixed', function (value) {
        value.bind(function (menu_icon_fixed) {
            $('.site .header__fixed.active .header__mobile-button').css('color', menu_icon_fixed + '!important');
        });
    });

})(jQuery);
