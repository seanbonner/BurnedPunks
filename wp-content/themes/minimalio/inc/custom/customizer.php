<?php
/**
 * Minimalio Theme Customizer
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();

new Minimalio_Customizer();
class Minimalio_Customizer {
	/**
	 * Called on class initialisation
	 */
	public function __construct() {
		/* Add the WordPress actions to register customizer components */
		add_action( 'customize_register', [ $this, 'minimalio_settings' ] );
	}

	/**
	 * Register minimalio customizer area
	 * @return null
	 */
	public function minimalio_settings( $customizer ) {
		if ( ! function_exists( 'minimalio_theme_slug_sanitize_select' ) ) {
			/**
			 * Select sanitization function
			 * @param string               $input Ensure input is a slug.
			 * @param WP_Customize_Setting $setting Settings.
			 * @return string
			 */
			function minimalio_theme_slug_sanitize_select( $input, $setting ) {
				// Ensure input is a slug (lowercase alphanumeric characters, dashes and underscores are allowed only).
				$input = sanitize_key( $input );
				// Get the list of possible select options.
				$choices = $setting->manager->get_control( $setting->id )
					->choices;
				// If the input is a valid key, return it; otherwise, return the default.
				return array_key_exists( $input, $choices )
					? $input
					: $setting->default;
			}
			// old definition here
		}

		/**
		 * Include alpha color picker
		 */
		require_once dirname( __DIR__, 1 ) .
			'/theme-customizer/php/alpha-color-picker.php';
		require_once dirname( __DIR__, 1 ) .
			'/theme-customizer/php/announcement.php';

		// LOAD FONTS

		// Array of web safe fonts
		$safe_fonts = [
			'Arial'           => 'Arial',
			'Verdana'         => 'Verdana',
			'Tahoma'          => 'Tahoma',
			'Times+New+Roman' => 'Times New Roman',
			'Georgia'         => 'Georgia',
			'Garamond'        => 'Garamond',
			'Courier+New'     => 'Courier New',
			'Brush+Script+MT' => 'Brush Script MT',
		];
		// An empty array that will be populated by $safe_fonts + Google Fonts
		$fonts_names = [];
		// Get the list of Google Fonts by calling minimalio_getGooglefonts(), see functions.php
		$fonts = minimalio_getGooglefonts();
		// if we got fonts from Google API, then populate the $fonts_names array with them &
		// array_merge with $safe_fonts
		// else - return $safe_fonts
		if ( ! empty( $fonts ) && isset( $fonts['items'] ) ) {
			foreach ( $fonts['items'] as $font ) {
				$fonts_names[] = esc_attr( $font['family'] );
			}
			$fonts_slugs  = str_replace( ' ', '+', $fonts_names );
			$google_fonts = array_combine( $fonts_slugs, $fonts_names );
			$fonts_array  = array_merge( $safe_fonts, $google_fonts );
		} else {
			$fonts_array = $safe_fonts;
		}

		// Font Weights (based on the selected Font)
		$fonts_weights = [];
		$weights       = minimalio_getGooglefontWeight();
		if ( ! empty( $weights ) ) {
			foreach ( $weights as $value ) {
				if ( $value === 'regular' ) {
					$fonts_weights['400'] = '400';
				} elseif ( preg_match( '/^[0-9]+$/', $value ) ) {
					$fonts_weights[ $value ] = $value;
				}
			}
		}

		// Add setting for site title size
		$customizer->add_setting(
			'minimalio_site_title_size',
			[
				'default'           => '',
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			]
		);

		// Add control for site title size
		$customizer->add_control(
			'minimalio_site_title_size',
			[
				'label'       => esc_html__( 'Site Title Size in PX', 'minimalio' ),
				'description' => esc_html__( 'Leave empty for the default.', 'minimalio' ),
				'section'     => 'title_tagline',
				'type'        => 'number',
				'priority'    => 10,
			]
		);

		/**
		 * Custom logo for mobile on title section
		 */
		$customizer->add_setting('minimalio_mobile-logo-settings', [
			'type'              => 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);
		$customizer->add_setting('minimalio_white-logo-settings', [
			'type'              => 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);

		// Add control for our logo option
		$customizer->add_control(
			new WP_Customize_Media_Control(
				$customizer,
				'minimalio_mobile-logo-options',
				[
					'label'       => esc_html__( 'Mobile Logo', 'minimalio' ),
					'description' => esc_html__(
						'This logo is displaid on mobile view',
						'minimalio'
					),
					'section'     => 'title_tagline',
					'settings'    => 'minimalio_mobile-logo-settings',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Media_Control(
				$customizer,
				'minimalio_fixed-logo-options',
				[
					'label'       => esc_html__( 'White Logo', 'minimalio' ),
					'description' => esc_html__(
						'Another variation of the logo',
						'minimalio'
					),
					'section'     => 'title_tagline',
					'settings'    => 'minimalio_white-logo-settings',
				]
			)
		);

		

		/**
		 * Custom Panel for Minimalio Options
		 */
		$customizer->add_panel('minimalio_panel', [
			'priority'       => 10,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => esc_html__( 'Minimalio Options', 'minimalio' ),
			'description'    => '',
		]);

		// Theme Layout options
		$customizer->add_section('minimalio_theme_layout_options', [
			'title'      => esc_html__( 'Theme Layout Options', 'minimalio' ),
			'capability' => 'edit_theme_options',
			'panel'      => 'minimalio_panel',
		]);

		// Settings

		$customizer->add_setting('minimalio_settings_container_type', [
			'default'           => 'container',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_container_width', [
			'default'           => '1240',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_scrollbar', [
			'default'           => 'unset',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_default_404', [
			'default'           => '',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);

		// Controls
		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio__options_container_type',
				[
					'label'       => esc_html__( 'Container Width', 'minimalio' ),
					'description' => esc_html__(
						'Choose between Full width and Container',
						'minimalio'
					),
					'section'     => 'minimalio_theme_layout_options',
					'settings'    => 'minimalio_settings_container_type',
					'type'        => 'select',
					'choices'     => [
						'container'       => esc_html__(
							'Fixed width container',
							'minimalio'
						),
						'container-fluid' => esc_html__(
							'Full width container',
							'minimalio'
						),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio__options_container_width',
				[
					'label'       => esc_html__( 'Container Width in px', 'minimalio' ),
					'description' => esc_html__(
						'Add container width for large screens',
						'minimalio'
					),
					'section'     => 'minimalio_theme_layout_options',
					'settings'    => 'minimalio_settings_container_width',
					'type'        => 'number',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio__options_scrollbar',
				[
					'label'       => esc_html__(
						'Enable Stable Scrollbar',
						'minimalio'
					),
					'description' => esc_html__(
						'If enabled, prevents layout shifts but introduces space on the right side',
						'minimalio'
					),
					'section'     => 'minimalio_theme_layout_options',
					'settings'    => 'minimalio_settings_scrollbar',
					'type'        => 'radio',
					'capability'  => 'edit_theme_options',
					'choices'     => [
						'stable' => esc_html__( 'yes', 'minimalio' ),
						'unset'  => esc_html__( 'no', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_My_Dropdown_Category_Control_Header(
				$customizer,
				'minimalio_options_default_404',
				[
					'label'    => esc_html__( 'Default 404 Page', 'minimalio' ),
					'section'  => 'minimalio_theme_layout_options',
					'settings' => 'minimalio_settings_default_404',
				]
			)
		);

		/**
		 * Section 'Background'
		 */
		$customizer->add_section('minimalio_background', [
			'title' => esc_html__( 'Background', 'minimalio' ),
			'panel' => 'minimalio_panel',
		]);

		// Settings
		$customizer->add_setting('minimalio_settings_color_background', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_image_background', [
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		]);

		// Add controls for our settings, within a section
		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_background_color_background',
				[
					'label'        => esc_html__(
						'Website Color Background',
						'minimalio'
					),
					'description'  => '',
					'section'      => 'minimalio_background',
					'settings'     => 'minimalio_settings_color_background',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Image_Control(
				$customizer,
				'minimalio_image_background',
				[
					'settings' => 'minimalio_settings_image_background',
					'section'  => 'minimalio_background',
					'label'    => esc_html__(
						'Website Background Image',
						'minimalio'
					),
				]
			)
		);

		// Page options
		$customizer->add_section('minimalio_page_options', [
			'title'      => esc_html__( 'Page Options', 'minimalio' ),
			'capability' => 'edit_theme_options',
			'panel'      => 'minimalio_panel',
		]);

		// Settings
		$customizer->add_setting('minimalio_settings_show_page_title', [
			'default'           => 'no',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_page_title_size', [
			
				'default'           => 'h2',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
				
			
		]);

		$customizer->add_setting('minimalio_settings_page_title_align', [
			
				'default'           => 'text-left',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			
		]);

		$customizer->add_setting('minimalio_settings_sidebar_position', [
			'default'           => 'none',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
		]);

		// Controls

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_show_page_title',
				[
					'label'       => esc_html__( 'Enable Page Title', 'minimalio' ),
					'description' => '',
					'section'     => 'minimalio_page_options',
					'settings'    => 'minimalio_settings_show_page_title',
					'type'        => 'radio',
					'capability'  => 'edit_theme_options',
					'choices'     => [
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_page_title_size',
				[
					'label'       => esc_html__(
						'Title Size (Pages, Archives)',
						'minimalio'
					),
					'description'  => esc_html__(
						'Applies to page titles and all archive page titles. Only changes the size, the html tag stays H1 for SEO and accessibility reasons.',
						'minimalio'
					),
					'section'     => 'minimalio_page_options',
					'settings'    => 'minimalio_settings_page_title_size',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						'h1' => esc_html__( 'h1', 'minimalio' ),
						'h2' => esc_html__( 'h2', 'minimalio' ),
						'h3' => esc_html__( 'h3', 'minimalio' ),
						'h4' => esc_html__( 'h4', 'minimalio' ),
						'h5' => esc_html__( 'h5', 'minimalio' ),
						'h6' => esc_html__( 'h6', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_page_title_align',
				[
					'label'       => esc_html__(
						'Title Position (Pages, Archives)',
						'minimalio'
					),
					'section'     => 'minimalio_page_options',
					'settings'    => 'minimalio_settings_page_title_align',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						'text-left' => esc_html__( 'Left', 'minimalio' ),
						'text-center'  => esc_html__( 'Center', 'minimalio' ),
						'text-right'  => esc_html__( 'Right', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_sidebar_position',
				[
					'label'             => esc_html__( 'Sidebar Positioning', 'minimalio' ),
					'description'       => esc_html__(
						'The sidebar that will be displayed on all pages',
						'minimalio'
					),
					'section'           => 'minimalio_page_options',
					'settings'          => 'minimalio_settings_sidebar_position',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'right' => esc_html__( 'Right sidebar', 'minimalio' ),
						'left'  => esc_html__( 'Left sidebar', 'minimalio' ),
						'both'  => esc_html__(
							'Left & Right sidebars',
							'minimalio'
						),
						'none'  => esc_html__( 'No sidebar', 'minimalio' ),
					],
				]
			)
		);

		/**
		 * Section 'Typography Settings'
		 */
		$customizer->add_section('minimalio_typography_settings', [
			'title'      => esc_html__( 'Typography', 'minimalio' ),
			'capability' => 'edit_theme_options',
			'panel'      => 'minimalio_panel',
		]);

		// Settings
		$customizer->add_setting('minimalio_typography_settings_google_font', [
			'default'           => 'Abel',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting(
			'minimalio_typography_settings_google_font_wight',
			[
				'default'           => '400',
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			]
		);

		$customizer->add_setting('minimalio_settings_google_font_style', [
			'default'           => 'normal',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_google_line_height', [
			'default'           => '1.5',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_google_letter_spacing', [
			'default'           => '0',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_font_color', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_google_link_decoration', [
			'default'           => 'underline',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);


		$customizer->add_setting('minimalio_settings_link_color', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);
		$customizer->add_setting('minimalio_settings_hover_color', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_heading_sizes', [
			'default'           => 'default',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		// Add controls for FONT settings

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_typography_options_google_font',
				[
					'label'             => esc_html__( 'Main Google Font', 'minimalio' ),
					'section'           => 'minimalio_typography_settings',
					'settings'          => 'minimalio_typography_settings_google_font',
					'priority'          => 10, // Optional. Order priority to load the control. Default: 10
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => $fonts_array,
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_typography_options_google_font_weight',
				[
					'label'             => esc_html__( 'Main Font Weight', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_typography_settings',
					'settings'          =>
						'minimalio_typography_settings_google_font_wight',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => $fonts_weights,
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_typography_options_google_font_style',
				[
					'label'             => esc_html__( 'Main Font Style', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_typography_settings',
					'settings'          => 'minimalio_settings_google_font_style',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'normal' => esc_html__( 'Regular', 'minimalio' ),
						'italic' => esc_html__( 'Italic', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_typography_options_font_color',
				[
					'label'        => esc_html__( 'Main Font Color', 'minimalio' ),
					'section'      => 'minimalio_typography_settings',
					'settings'     => 'minimalio_settings_font_color',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_typography_settings_link_decoration',
				[
					'label'             => esc_html__( 'Link Decoration', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_typography_settings',
					'settings'          => 'minimalio_settings_google_link_decoration',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'underline'    => esc_html__( 'Underline', 'minimalio' ),
						'line-through' => esc_html__(
							'Line-through',
							'minimalio'
						),
						'none'         => esc_html__( 'No decoration', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_typography_options_link_color',
				[
					'label'        => esc_html__( 'Link Color', 'minimalio' ),
					'section'      => 'minimalio_typography_settings',
					'settings'     => 'minimalio_settings_link_color',
					'show_opacity' => false,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_menu_font_color',
				[
					'label'        => esc_html__( 'Hover Link Color', 'minimalio' ),
					'section'      => 'minimalio_typography_settings',
					'settings'     => 'minimalio_settings_hover_color',
					'show_opacity' => false,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_typography_options_google_line_height',
				[
					'label'       => esc_html__( 'Main Line Height', 'minimalio' ),
					'description' => esc_html__(
						'Usually between 1.3 to 1.8',
						'minimalio'
					),
					'section'     => 'minimalio_typography_settings',
					'settings'    => 'minimalio_settings_google_line_height',
					'type'        => 'text',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_typography_options_google_letter_spacing',
				[
					'label'    => esc_html__(
						'Main Letter Spacing (px)',
						'minimalio'
					),
					'section'  => 'minimalio_typography_settings',
					'settings' => 'minimalio_settings_google_letter_spacing',
					'type'     => 'text',
				]
			)
		);


		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_typography_settings_heading_sizes',
				[
					'label'             => esc_html__( 'Heading Sizes Type Scale', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_typography_settings',
					'settings'          => 'minimalio_settings_heading_sizes',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'default'    => esc_html__( 'Default', 'minimalio' ),
						'headings-scale-1-25' => esc_html__(
							'Scale 1.25',
							'minimalio'
						),
						'headings-scale-1-333' => esc_html__(
							'Scale 1.333',
							'minimalio'
						),
						'headings-scale-1-414' => esc_html__(
							'Scale 1.414',
							'minimalio'
						),
						'headings-scale-1-5' => esc_html__(
							'Scale 1.5',
							'minimalio'
						),

					],
				]
			)
		);


		/**
		 * Section 'Header Settings'
		 */
		$customizer->add_section('minimalio_heading_settings_fixed', [
			'title' => esc_html__( 'Header', 'minimalio' ),
			'panel' => 'minimalio_panel',
		]);

		// Settings

		$customizer->add_setting('minimalio_settings_header_container_type', [
			'default'           => 'container',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_header_variation', [
			'default'           => 'horizontal',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_logo_position', [
			'default'           => 'left',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_menu_position', [
			'default'           => 'right',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_logo_width', [
			'default'           => '180',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_fixed_heading', [
			'default'           => 'no',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_header_background', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);
		$customizer->add_setting('minimalio_settings_header_color', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);
		$customizer->add_setting('minimalio_settings_header_color_hover', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_fixed_header_background', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);
		$customizer->add_setting('minimalio_settings_fixed_color', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);
		$customizer->add_setting('minimalio_settings_fixed_color_hover', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_transparent', [
			'default'           => 'no',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_link_decoration', [
			'default'           => 'no',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_link_weight', [
			'default'           => 'normal',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_menu_google_font_size', [
			'transport'         => 'refresh',
			'default'           => '20',
			'sanitize_callback' => 'absint',
		]);

		$customizer->add_setting('minimalio_settings_menu_google_font_style', [
			'default'           => 'normal',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting(
			'minimalio_settings_menu_google_letter_spacing',
			[
				'transport'         => 'refresh',
				'default'           => '0',
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		$customizer->add_setting('minimalio_settings_submenu_open', [
			'default'           => 'no',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_submenu_color', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_submenu_color_hover', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting(
			'minimalio_settings_submenu_background_color',
			[
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			]
		);

		$customizer->add_setting('minimalio_settings_submenu_font_size', [
			'default'           => '',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);

		// Add controls for our settings, within a section

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_header_variation',
				[
					'label'    => esc_html__( 'Select Header', 'minimalio' ),
					'section'  => 'minimalio_heading_settings_fixed',
					'settings' => 'minimalio_settings_header_variation',
					'type'     => 'select',
					'choices'  => [
						'horizontal' => esc_html__(
							'Horizontal header',
							'minimalio'
						),
						'vertical'   => esc_html__(
							'Vertical header',
							'minimalio'
						),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_header_container_type',
				[
					'label'       => esc_html__( 'Container Width', 'minimalio' ),
					'description' => esc_html__(
						'Choose between Full width and Container (The size you set up on Theme Layout Options)',
						'minimalio'
					),
					'section'     => 'minimalio_heading_settings_fixed',
					'settings'    => 'minimalio_settings_header_container_type',
					'type'        => 'select',
					'choices'     => [
						'container'       => esc_html__(
							'Fixed width container',
							'minimalio'
						),
						'container-fluid' => esc_html__(
							'Full width container',
							'minimalio'
						),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_logo_position',
				[
					'label'    => esc_html__( 'Logo Position', 'minimalio' ),
					'section'  => 'minimalio_heading_settings_fixed',
					'settings' => 'minimalio_settings_logo_position',
					'type'     => 'select',
					'choices'  => [
						'left'   => esc_html__( 'Left', 'minimalio' ),
						'center' => esc_html__( 'Center', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_header_options_logo_width',
				[
					'label'       => esc_html__( 'Logo Width', 'minimalio' ),
					'description' => esc_html__(
						'For Logo images from Site Identity',
						'minimalio'
					),
					'section'     => 'minimalio_heading_settings_fixed',
					'settings'    => 'minimalio_settings_logo_width',
					'type'        => 'number',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_menu_position',
				[
					'label'    => esc_html__( 'Menu Position', 'minimalio' ),
					'section'  => 'minimalio_heading_settings_fixed',
					'settings' => 'minimalio_settings_menu_position',
					'type'     => 'select',
					'choices'  => [
						'right'  => esc_html__( 'Right', 'minimalio' ),
						'center' => esc_html__( 'Center', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_heading_options_enable_fixed',
				[
					'label'      => esc_html__( 'Enable Fixed Header', 'minimalio' ),
					'section'    => 'minimalio_heading_settings_fixed',
					'settings'   => 'minimalio_settings_fixed_heading',
					'priority'   => 10,
					'type'       => 'radio',
					'capability' => 'edit_theme_options',
					'choices'    => [
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_heading_options_transparent',
				[
					'label'      => esc_html__(
						'Enable Transparent Header',
						'minimalio'
					),
					'section'    => 'minimalio_heading_settings_fixed',
					'settings'   => 'minimalio_settings_transparent',
					'priority'   => 10,
					'type'       => 'radio',
					'capability' => 'edit_theme_options',
					'choices'    => [
						'no'        => esc_html__( 'No', 'minimalio' ),
						'home'      => esc_html__( 'Only Homepage', 'minimalio' ),
						'portfolio' => esc_html__(
							'Only Single Portfolio',
							'minimalio'
						),
						'yes'       => esc_html__( 'Everywhere', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_heading_options_background',
				[
					'label'        => esc_html__(
						'Header Background Color',
						'minimalio'
					),
					'section'      => 'minimalio_heading_settings_fixed',
					'settings'     => 'minimalio_settings_header_background',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_heading_options_color',
				[
					'label'        => esc_html__( 'Header Menu Color', 'minimalio' ),
					'section'      => 'minimalio_heading_settings_fixed',
					'settings'     => 'minimalio_settings_header_color',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_heading_options_color_hover',
				[
					'label'        => esc_html__(
						'Header Menu Hover/Active Color',
						'minimalio'
					),
					'section'      => 'minimalio_heading_settings_fixed',
					'settings'     => 'minimalio_settings_header_color_hover',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_heading_options_fixed_background',
				[
					'label'        => esc_html__(
						'Fixed Header Background',
						'minimalio'
					),
					'section'      => 'minimalio_heading_settings_fixed',
					'settings'     => 'minimalio_settings_fixed_header_background',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_heading_options_fixed_color',
				[
					'label'        => esc_html__( 'Fixed Header Color', 'minimalio' ),
					'section'      => 'minimalio_heading_settings_fixed',
					'settings'     => 'minimalio_settings_fixed_color',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_heading_options_fixed_color_hover',
				[
					'label'        => esc_html__(
						'Fixed Header Hover/Active Color',
						'minimalio'
					),
					'section'      => 'minimalio_heading_settings_fixed',
					'settings'     => 'minimalio_settings_fixed_color_hover',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_header_options_link_decoration',
				[
					'label'             => esc_html__(
						'Link Hover/Active Decoration',
						'minimalio'
					),
					'description'       => '',
					'section'           => 'minimalio_heading_settings_fixed',
					'settings'          => 'minimalio_settings_link_decoration',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'no'           => esc_html__( 'No decoration', 'minimalio' ),
						'underline'    => esc_html__( 'Underline', 'minimalio' ),
						'line-through' => esc_html__(
							'Line-through',
							'minimalio'
						),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_header_options_link_font_weight',
				[
					'label'             => esc_html__(
						'Link Hover/Active Font Weight',
						'minimalio'
					),
					'description'       => '',
					'section'           => 'minimalio_heading_settings_fixed',
					'settings'          => 'minimalio_settings_link_weight',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'light'    => esc_html__( 'Light', 'minimalio' ),
						'normal'   => esc_html__( 'Normal', 'minimalio' ),
						'semibold' => esc_html__( 'Semibold', 'minimalio' ),
						'bold'     => esc_html__( 'Bold', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_header_options_google_font_size',
				[
					'label'    => esc_html__( 'Menu Font Size (px)', 'minimalio' ),
					'section'  => 'minimalio_heading_settings_fixed',
					'settings' => 'minimalio_settings_menu_google_font_size',
					'type'     => 'number',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_header_options_google_font_style',
				[
					'label'             => esc_html__( 'Menu Font Style', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_heading_settings_fixed',
					'settings'          => 'minimalio_settings_menu_google_font_style',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'normal' => esc_html__( 'Regular', 'minimalio' ),
						'italic' => esc_html__( 'Italic', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_header_options_google_letter_spacing',
				[
					'label'    => esc_html__(
						'Menu Letter Spacing (px)',
						'minimalio'
					),
					'section'  => 'minimalio_heading_settings_fixed',
					'settings' =>
						'minimalio_settings_menu_google_letter_spacing',
					'type'     => 'text',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_submenu_opened',
				[
					'label'             => esc_html__(
						'Keep Submenu Opened On Parent Page',
						'minimalio'
					),
					'description'       => '',
					'section'           => 'minimalio_heading_settings_fixed',
					'settings'          => 'minimalio_settings_submenu_open',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);
		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_heading_options_submenu_color',
				[
					'label'        => esc_html__( 'Submenu Color', 'minimalio' ),
					'section'      => 'minimalio_heading_settings_fixed',
					'settings'     => 'minimalio_settings_submenu_color',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_heading_options_submenu_color_hover',
				[
					'label'        => esc_html__( 'Submenu Color Hover', 'minimalio' ),
					'section'      => 'minimalio_heading_settings_fixed',
					'settings'     => 'minimalio_settings_submenu_color_hover',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_heading_options_submenu_background_color',
				[
					'label'        => esc_html__(
						'Submenu Background Color',
						'minimalio'
					),
					'section'      => 'minimalio_heading_settings_fixed',
					'settings'     => 'minimalio_settings_submenu_background_color',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_header_options_submenu_font_size',
				[
					'label'    => esc_html__(
						'Submenu Font Size (px)',
						'minimalio'
					),
					'section'  => 'minimalio_heading_settings_fixed',
					'settings' => 'minimalio_settings_submenu_font_size',
					'type'     => 'number',
				]
			)
		);

		/**
		 * Section 'Footer'
		 */
		$customizer->add_section('minimalio_footer_customizer', [
			'title' => esc_html__( 'Footer', 'minimalio' ),
			'panel' => 'minimalio_panel',
		]);

		// Settings

		$customizer->add_setting('minimalio_settings_footer_widgets', [
			'default'           => 'yes',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_footer_container', [
			'default'           => 'container',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting(
			'minimalio_settings_enable_copyright_section',
			[
				'default'           => 'yes',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			]
		);

		$customizer->add_setting('minimalio_settings_footer_menu', [
			'default'           => 'no',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_footer_logo', [
			'default'           => 'disable',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_copyright', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_developer', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_footer_background', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_footer_font_color', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_footer_container',
				[
					'label'       => esc_html__(
						'Footer Container Width',
						'minimalio'
					),
					'description' => esc_html__(
						'Choose between Full width and Container (The size you set up on Theme Layout Options)',
						'minimalio'
					),
					'section'     => 'minimalio_footer_customizer',
					'settings'    => 'minimalio_settings_footer_container',
					'type'        => 'select',
					'choices'     => [
						'container'       => esc_html__(
							'Fixed width container',
							'minimalio'
						),
						'container-fluid' => esc_html__(
							'Full width container',
							'minimalio'
						),
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_footer_options_footer_background',
				[
					'label'        => esc_html__( 'Footer Background', 'minimalio' ),
					'section'      => 'minimalio_footer_customizer',
					'settings'     => 'minimalio_settings_footer_background',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_footer_options_footer_font_color',
				[
					'label'        => esc_html__( 'Footer Font Color', 'minimalio' ),
					'section'      => 'minimalio_footer_customizer',
					'settings'     => 'minimalio_settings_footer_font_color',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_footer_widgets',
				[
					'label'             => esc_html__( 'Enable Footer Widgets', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_footer_customizer',
					'settings'          => 'minimalio_settings_footer_widgets',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_enable_copyright_section',
				[
					'label'             => esc_html__(
						'Enable Copyright Section',
						'minimalio'
					),
					'description'       => '',
					'section'           => 'minimalio_footer_customizer',
					'settings'          => 'minimalio_settings_enable_copyright_section',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_footer_menu',
				[
					'label'             => esc_html__( 'Enable Copyright Menu', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_footer_customizer',
					'settings'          => 'minimalio_settings_footer_menu',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_footer_logo',
				[
					'label'             => esc_html__( 'Choose Copyright Logo', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_footer_customizer',
					'settings'          => 'minimalio_settings_footer_logo',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'general' => esc_html__( 'General logo', 'minimalio' ),
						'white'   => esc_html__( 'White logo', 'minimalio' ),
						'disable' => esc_html__(
							'Disable footer logo',
							'minimalio'
						),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_footer_copyright',
				[
					'label'    => esc_html__( 'Copyright Message', 'minimalio' ),
					'section'  => 'minimalio_footer_customizer',
					'settings' => 'minimalio_settings_copyright',
					'type'     => 'text',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_developed',
				[
					'label'    => esc_html__( 'Designer/Developer', 'minimalio' ),
					'section'  => 'minimalio_footer_customizer',
					'settings' => 'minimalio_settings_developer',
					'type'     => 'text',
				]
			)
		);

		// Blog options
		$customizer->add_section('minimalio_blog_options', [
			'title'      => esc_html__( 'Blog Options', 'minimalio' ),
			'capability' => 'edit_theme_options',
			'panel'      => 'minimalio_panel',
		]);

		// Settings
		$customizer->add_setting(
			'minimalio_settings_archive_template_filter_enable',
			[
				'default'           => 'yes',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			]
		);

		$customizer->add_setting('minimalio_settings_blog_columns', [
			'default'           => 4,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_blog_type', [
			'default'           => 'grid',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_blog_pagination', [
			'default'           => 'pagination',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_blog_posts_per_page', [
			'default'           => 12,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_post_cart_button_label', [
			'default'           => 'Read More',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_blog_hover_color', [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting(
			'minimalio_settings_single_template_sidebar_position',
			[
				'default'           => 'none',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
				'capability'        => 'edit_theme_options',
			]
		);

		$customizer->add_setting(
			'minimalio_settings_archive_template_sidebar_position',
			[
				'default'           => 'none',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
			]
		);

		$customizer->add_setting(
			'minimalio_settings_blog_post_card_image_aspect_ratio',
			[
				'default'           => '1-1',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			]
		);

		$customizer->add_setting('minimalio_settings_blog_gap', [
			'default'           => 'gap_1',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_blog_post_card', [
			'default'           => 'style_1',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_single_post_thumbnail', [
			[
				'default'           => 'no',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			],
		]);

		$customizer->add_setting('minimalio_settings_single_post_title', [
			[
				'default'           => 'yes',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			],
		]);

		$customizer->add_setting('minimalio_settings_single_post_title_size', [
			
				'default'           => 'h2',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			],
		);

		$customizer->add_setting('minimalio_settings_single_post_title_align', [
			
				'default'           => 'justify-center',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			
		]);

		$customizer->add_setting('minimalio_settings_single_post_meta', [
			[
				'default'           => 'no',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			],
		]);

		$customizer->add_setting('minimalio_settings_single_post_share', [
			[
				'default'           => 'no',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			],
		]);

		$customizer->add_setting('minimalio_settings_single_post_author', [
			[
				'default'           => 'no',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			],
		]);

		$customizer->add_setting(
			'minimalio_settings_single_post_latest_posts',
			[
				[
					'default'           => 'no',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				],
			]
		);

		$customizer->add_setting(
			'minimalio_settings_single_post_latest_posts_title',
			[
				[
					'default'           => 'yes',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				],
			]
		);

		$customizer->add_setting(
			'minimalio_settings_single_post_latest_posts_title_label',
			[
				[
					'default'           => 'Latest',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				],
			]
		);

		$customizer->add_setting(
			'minimalio_settings_single_post_latest_posts_title_align',
			[
				[
					'default'           => 'left',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				],
			]
		);

		$customizer->add_setting('minimalio_settings_single_post_latest_posts_title_size', [
			'default'           => 'h2',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_single_post_latest_posts_number', [
			'default'           => '4',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);

		$customizer->add_setting('minimalio_settings_single_post_navigation', [
			[
				'default'           => 'no',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			],
		]);

		$customizer->add_setting('minimalio_settings_blog_all', [
			[
				'default'           => 'All',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			],
		]);

		// Controls

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_archive_sidebar_position',
				[
					'label'             => esc_html__( 'Sidebar Positioning', 'minimalio' ),
					'description'       => esc_html__(
						'The sidebar that will be displayed on blog and archive pages',
						'minimalio'
					),
					'section'           => 'minimalio_blog_options',
					'settings'          =>
						'minimalio_settings_archive_template_sidebar_position',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'right' => esc_html__( 'Right sidebar', 'minimalio' ),
						'left'  => esc_html__( 'Left sidebar', 'minimalio' ),
						'both'  => esc_html__(
							'Left & Right sidebars',
							'minimalio'
						),
						'none'  => esc_html__( 'No sidebar', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_archive_posts_filter_enable',
				[
					'label'             => esc_html__(
						'Enable Blog Filter By Categories',
						'minimalio'
					),
					'description'       => '',
					'section'           => 'minimalio_blog_options',
					'settings'          =>
						'minimalio_settings_archive_template_filter_enable',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_blog_all',
				[
					'label'    => esc_html__(
						'All Categories Filter Label',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_blog_all',
					'type'     => 'text',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_blog_pagination',
				[
					'label'             => esc_html__( 'Load More Options', 'minimalio' ),
					'section'           => 'minimalio_blog_options',
					'settings'          => 'minimalio_settings_blog_pagination',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'load'        => esc_html__( 'Load More Button', 'minimalio' ),
						'load_scroll' => esc_html__(
							'Load More on Scroll',
							'minimalio'
						),
						'pagination'  => esc_html__( 'Pagination', 'minimalio' ),
						'no'          => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_blog_posts_per_page',
				[
					'label'       => esc_html__( 'Posts Per Page/Load', 'minimalio' ),
					'section'     => 'minimalio_blog_options',
					'settings'    => 'minimalio_settings_blog_posts_per_page',
					'type'        => 'number',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_blog_columns',
				[
					'label'             => esc_html__( 'Blog Archive Columns', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_blog_options',
					'settings'          => 'minimalio_settings_blog_columns',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'1' => esc_html__( '1 column', 'minimalio' ),
						'2' => esc_html__( '2 columns', 'minimalio' ),
						'3' => esc_html__( '3 columns', 'minimalio' ),
						'4' => esc_html__( '4 columns', 'minimalio' ),
						'5' => esc_html__( '5 columns', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_blog_type',
				[
					'label'             => esc_html__( 'Blog Display Type', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_blog_options',
					'settings'          => 'minimalio_settings_blog_type',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'grid'    => esc_html__( 'Grid', 'minimalio' ),
						'masonry' => esc_html__( 'Masonry', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_blog_post_card',
				[
					'label'             => esc_html__( 'Blog Card Style', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_blog_options',
					'settings'          => 'minimalio_settings_blog_post_card',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'style_1'      => esc_html__( 'Style 1', 'minimalio' ),
						'style_2'      => esc_html__( 'Style 2', 'minimalio' ),
						'style_3'      => esc_html__( 'Style 3', 'minimalio' ),
						'style_4'      => esc_html__( 'Style 4', 'minimalio' ),
						'style_5'      => esc_html__( 'Style 5', 'minimalio' ),
						'all_elements' => esc_html__(
							'All elements',
							'minimalio'
						),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_blog_post_card_image_aspect_ratio',
				[
					'label'             => esc_html__( 'Image Aspect Ratio', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_blog_options',
					'settings'          =>
						'minimalio_settings_blog_post_card_image_aspect_ratio',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'1-1'  => esc_html__( '1/1', 'minimalio' ),
						'4-3'  => esc_html__( '4/3', 'minimalio' ),
						'16-9' => esc_html__( '16/9', 'minimalio' ),
						'3-4'  => esc_html__( '3/4', 'minimalio' ),
						'9-16' => esc_html__( '9/16', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_blog_gap',
				[
					'label'             => esc_html__( 'Blog Card Gap', 'minimalio' ),
					'description'       => '',
					'section'           => 'minimalio_blog_options',
					'settings'          => 'minimalio_settings_blog_gap',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'gap_0' => esc_html__( 'No gap', 'minimalio' ),
						'gap_1' => esc_html__( 'Small gap', 'minimalio' ),
						'gap_2' => esc_html__( 'Big gap', 'minimalio' ),
						'gap_3' => esc_html__( 'Very big gap', 'minimalio' ),
						'gap_4' => esc_html__( 'Huge gap', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_post_cart_button_label',
				[
					'label'       => esc_html__(
						'Read More Button Label',
						'minimalio'
					),
					'description' => 'Read more button label on cards',
					'section'     => 'minimalio_blog_options',
					'settings'    => 'minimalio_settings_post_cart_button_label',
					'type'        => 'text',
				]
			)
		);

		$customizer->add_control(
			new Minimalio_Direction_Customizer_Alpha_Color_Control(
				$customizer,
				'minimalio_options_blog_hover_color',
				[
					'label'        => esc_html__( 'Blog Hover Color', 'minimalio' ),
					'section'      => 'minimalio_blog_options',
					'settings'     => 'minimalio_settings_blog_hover_color',
					'show_opacity' => true,
					'palette'      => [
						'#ffffff',
						'#0a0a0a',
						'#002778',
						'#007392',
						'#3F0055',
						'#006D57',
						'#00CC99',
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_sidebar_position',
				[
					'label'             => esc_html__(
						'Single Post Sidebar Positioning',
						'minimalio'
					),
					'section'           => 'minimalio_blog_options',
					'settings'          =>
						'minimalio_settings_single_template_sidebar_position',
					'type'              => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'           => [
						'right' => esc_html__( 'Right sidebar', 'minimalio' ),
						'left'  => esc_html__( 'Left sidebar', 'minimalio' ),
						'both'  => esc_html__(
							'Left & Right sidebars',
							'minimalio'
						),
						'none'  => esc_html__( 'No sidebar', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_thumbnail',
				[
					'label'    => esc_html__(
						'Enable Single Post Thumbnail Image',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_single_post_thumbnail',
					'type'     => 'select',
					'choices'  => [
						// Optional.
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_title',
				[
					'label'    => esc_html__(
						'Enable Single Post Title',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_single_post_title',
					'type'     => 'select',
					'choices'  => [
						// Optional.
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);


		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_title_align',
				[
					'label'    => esc_html__(
						'Title Position',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_single_post_title_align',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						'justify-start' => esc_html__( 'Left', 'minimalio' ),
						'justify-center'  => esc_html__( 'Center', 'minimalio' ),
						'justify-end'  => esc_html__( 'Right', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_title_size',
				[
					'label'       => esc_html__(
						'Title Size',
						'minimalio'
					),
					'section'     => 'minimalio_blog_options',
					'settings'    => 'minimalio_settings_single_post_title_size',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						'h1' => esc_html__( 'h1', 'minimalio' ),
						'h2' => esc_html__( 'h2', 'minimalio' ),
						'h3' => esc_html__( 'h3', 'minimalio' ),
						'h4' => esc_html__( 'h4', 'minimalio' ),
						'h5' => esc_html__( 'h5', 'minimalio' ),
						'h6' => esc_html__( 'h6', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_meta',
				[
					'label'    => esc_html__(
						'Enable Single Post Meta',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_single_post_meta',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						// Optional.
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_share',
				[
					'label'    => esc_html__(
						'Enable Single Post Share Buttons',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_single_post_share',
					'type'     => 'select',
					'choices'  => [
						// Optional.
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_author',
				[
					'label'    => esc_html__(
						'Enable Single Post Author',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_single_post_author',
					'type'     => 'select',
					'choices'  => [
						// Optional.
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_latest_posts',
				[
					'label'    => esc_html__(
						'Enable Single Post Latest Posts',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_single_post_latest_posts',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_latest_posts_title',
				[
					'label'       => esc_html__(
						'Latest Posts Title',
						'minimalio'
					),
					'section'     => 'minimalio_blog_options',
					'settings'    => 'minimalio_settings_single_post_latest_posts_title',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						// Optional.
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_latest_posts_title_label',
				[
					'label'       => esc_html__(
						'Latest Posts Title Label',
						'minimalio'
					),
					'section'     => 'minimalio_blog_options',
					'settings'    => 'minimalio_settings_single_post_latest_posts_title_label',
					'type'        => 'text',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_latest_posts_title_align',
				[
					'label'    => esc_html__(
						'Latest Posts Title Position',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_single_post_latest_posts_title_align',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						'text-left' => esc_html__( 'Left', 'minimalio' ),
						'text-center'  => esc_html__( 'Center', 'minimalio' ),
						'text-right'  => esc_html__( 'Right', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_latest_posts_title_size',
				[
					'label'       => esc_html__(
						'Latest Posts Title Size',
						'minimalio'
					),
					'section'     => 'minimalio_blog_options',
					'settings'    => 'minimalio_settings_single_post_latest_posts_title_size',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						'h1' => esc_html__( 'h1', 'minimalio' ),
						'h2' => esc_html__( 'h2', 'minimalio' ),
						'h3' => esc_html__( 'h3', 'minimalio' ),
						'h4' => esc_html__( 'h4', 'minimalio' ),
						'h5' => esc_html__( 'h5', 'minimalio' ),
						'h6' => esc_html__( 'h6', 'minimalio' ),
					],
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_latest_posts_number',
				[
					'label'       => esc_html__(
						'Latest Posts Number',
						'minimalio'
					),
					'section'     => 'minimalio_blog_options',
					'settings'    => 'minimalio_settings_single_post_latest_posts_number',
					'type'        => 'number',
				]
			)
		);

		$customizer->add_control(
			new WP_Customize_Control(
				$customizer,
				'minimalio_options_single_post_navigation',
				[
					'label'    => esc_html__(
						'Enable Single Post Navigation',
						'minimalio'
					),
					'section'  => 'minimalio_blog_options',
					'settings' => 'minimalio_settings_single_post_navigation',
					'type'     => 'select',
					'sanitize_callback' =>
						'minimalio_theme_slug_sanitize_select',
					'choices'  => [
						// Optional.
						'yes' => esc_html__( 'Yes', 'minimalio' ),
						'no'  => esc_html__( 'No', 'minimalio' ),
					],
				]
			)
		);

		// Portfolio options
		$customizer->add_section('minimalio_portfolio_options', [
			'title'      => esc_html__( 'Portfolio Settings', 'minimalio' ),
			'capability' => 'edit_theme_options',
			'panel'      => 'minimalio_panel',
		]);
		// Settings
		$customizer->add_setting(
			'minimalio_settings_archive_portfolio_filter_enable',
			[
				'default'           => 'yes',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
				'capability'        => 'edit_theme_options',
			]
		);

		// PORTFOLIO CONTROLS

		// Announce the portfolio filter by categories, available in Minimalio-Portfolio Plugin.
        if ( !in_array( 'minimalio-portfolio/minimalio-portfolio.php', (array) get_option( 'active_plugins', array() ) ) ) {
            $customizer->add_control(
                new Minimalio_Announcement_Control(
                    $customizer,
                    'minimalio_options_archive_portfolio_filter_enable',
                    [
                        'label' => esc_html__(
                            'Portfolio settings only available with premium plugin',
                            'minimalio'
                        ),
                        'section' => 'minimalio_portfolio_options',
                        'settings' =>
                            'minimalio_settings_archive_portfolio_filter_enable',

                        'input_attrs' => [
                            'message' => esc_html__('Go Premium >>>', 'minimalio'),
                            'link' => esc_url(
                                'https://minimalio.org/'
                            ),
                            'image' => esc_html__(
                                'minimalio-portfolio-customizer.jpg',
                                'minimalio'
                            ),
                        ],
                    ]
                )
            );
        }
        /**
         * Section 'Mobile menu'
		 */
		$customizer->add_section('minimalio_mobile_menu', [
			'title'    => esc_html__( 'Mobile Menu', 'minimalio' ),
			'priority' => 1200,
			'panel'    => 'minimalio_panel',
		]);

		// Settings
		$customizer->add_setting('minimalio_settings_mobile_menu_breack', [
			'default'           => '768',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		]);

		// Add controls for our settings, within a section
		if ( !in_array( 'minimalio-portfolio/minimalio-portfolio.php', (array) get_option( 'active_plugins', array() ) ) ) {
			$customizer->add_control(
				new Minimalio_Announcement_Control(
					$customizer,
					'minimalio_options_mobile_menu_breack',
					[
						'label'       => esc_html__(
							'Mobile menu settings only available with premium plugin',
							'minimalio'
						),
						'section'     => 'minimalio_mobile_menu',
						'settings'    => 'minimalio_settings_mobile_menu_breack',
						'input_attrs' => [
							'message' => esc_html__( 'Go Premium >>>', 'minimalio' ),
							'link'    => esc_url(
								'https://minimalio.org/',
								'minimalio'
							),
							'image'   => esc_html__(
								'minimalio-mobile-customizer.jpg',
								'minimalio'
							),
						],
					]
				)
			);
		}

		/**
		 * Section 'Social Media'
		 */
		$customizer->add_section('minimalio_social_media', [
			'title'    => esc_html__( 'Social Media', 'minimalio' ),
			'priority' => 1200,
			'panel'    => 'minimalio_panel',
		]);

		// Settings
		$customizer->add_setting('minimalio_settings_social_media_location', [
			'default'           => 'no',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]);


		// Controls
		if ( !in_array( 'minimalio-portfolio/minimalio-portfolio.php', (array) get_option( 'active_plugins', array() ) ) ) {

			$customizer->add_control(
				new Minimalio_Announcement_Control(
					$customizer,
					'minimalio_settings_social_media_position',
					[
						'label'       => esc_html__(
							'Social media settings only available with premium plugin',
							'minimalio'
						),
						'section'     => 'minimalio_social_media',
						'settings'    => 'minimalio_settings_social_media_location',
						'input_attrs' => [
							'message' => esc_html__( 'Go Premium >>>', 'minimalio' ),
							'link'    => esc_url(
								'https://minimalio.org/',
								'minimalio'
							),
							'image'   => esc_html__(
								'minimalio-social-customizer.jpg',
								'minimalio'
							),
						],
					]
				)
			);
		}

		// LIGTBOX SECTION

		$customizer->add_section('minimalio_lightbox', [
			'title'    => esc_html__( 'Ligtbox', 'minimalio' ),
			'priority' => 1200,
			'panel'    => 'minimalio_panel',
		]);

		// Settings
		$customizer->add_setting('minimalio_gallery_bg_color_settings', [
			'default'           => '#cccccc',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		]);

		// CONTROLS

		// Announce the lightbox background color control, available in Minimalio-Child
		if ( !in_array( 'minimalio-portfolio/minimalio-portfolio.php', (array) get_option( 'active_plugins', array() ) ) ) {

			$customizer->add_control(
				new Minimalio_Announcement_Control(
					$customizer,
					'minimalio_gallery_bg_color_control',
					[
						'label'       => esc_html__(
							'Ligtbox settings only available with premium plugin',
							'minimalio'
						),
						'section'     => 'minimalio_lightbox',
						'settings'    => 'minimalio_gallery_bg_color_settings',
						'input_attrs' => [
							'message' => esc_html__( 'Go Premium >>>', 'minimalio' ),
							'link'    => esc_url(
								'https://minimalio.org/',
								'minimalio'
							),
							'image'   => esc_html__(
								'minimalio-lightbox-customizer.jpg',
								'minimalio'
							),
						],
					]
				)
			);
		}

        return $customizer;
	}
}

