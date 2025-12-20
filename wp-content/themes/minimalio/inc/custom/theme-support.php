<?php

/**
 * Store our theme settings for re-use
 * (Rather than refetching and decoding)
 * @param  string $key Return a specific key element (top level only)
 * @return null
 */

defined( 'ABSPATH' ) || exit;

/**
 * Init hook for registering of post types etc
 */

new Minimalio_ThemeFunctions;
class Minimalio_ThemeFunctions {


	function __construct() {
		// Load theme text domain
		load_theme_textdomain( 'minimalio' );

		// Menu hooks
		add_action( 'init', [ $this, 'minimalio_register_menus' ] );

		// Theme support hook
		add_action( 'init', [ $this, 'minimalio_theme_support' ] );

		/** Display Posts Category and Tags */
		add_action( 'init', [ $this, 'minimalio_add_page_cats' ] );

		/* Add custom style for item menu */
		add_action( 'admin_head', [ $this, 'minimalio_my_custom_css' ] );

		/* Add redirect from single portfolio */
		add_action( 'template_redirect', [ $this, 'minimalio_redirect_pages_not_single' ] );
	}

	/**
	 * Hook to register navigation menu positions
	 * @return null
	 */
	public function minimalio_register_menus() {
		// Register the nav menus
		register_nav_menus(
			[
				'primary'     => __( 'Primary Menu', 'minimalio' ),
				'secondary'   => __( 'Secondary Menu', 'minimalio' ),
				'footer_menu' => __( 'Footer Copyright Menu', 'minimalio' ),
			]
		);
	}

	/**
	 * Hook to add varying theme support
	 * @return null
	 */
	public function minimalio_theme_support() {
		// Add post thumbnail support to post types as required
		add_theme_support( 'post-thumbnails', [ 'post', 'portfolio' ] );
		// HTML5 Support
		add_theme_support( 'html5', [ 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ] );
		// WordPress 4.4 Title Support
		add_theme_support( 'title-tag' );
	}

	/**
	 * Hook to display post categories and taxonomies
	 * @return null
	 */
	public function minimalio_add_page_cats() {
		register_taxonomy_for_object_type( 'post_tag', 'page' );
		register_taxonomy_for_object_type( 'category', 'page' );
	}

	/**
	 * Adds custom css to admin panel
	 */
	public function minimalio_my_custom_css() {
		echo '<style type="text/css" id="minimalio_admin_style">
        .wp-menu-image img {
          max-width: 15px;
          padding-top: 12px !important;
        }
      </style>';
	}

	/**
	 * Disable single custom post type - portfolio
	 * if is selected from Customiser
	 */
	public function minimalio_redirect_pages_not_single() {
		if (
		get_theme_mod( 'minimalio_settings_portfolio_behaviour' ) === 'html_in_lightbox' ||
		get_theme_mod( 'minimalio_settings_portfolio_behaviour' ) === 'video_in_lightbox' ) {
			if ( is_singular( 'portfolio' ) ) {
				wp_redirect( home_url(), 302 );
				exit;
			}
		}
	}
}

/**
 * Creates resized background image HTML
 * @param  integer $image Image ID
 * @param  array   $size Size of image resizing parameters
 * @param  array   $options Options array used to set additional css properties
 * @return string  Background image style HTML
 */
function minimalio_bg_image( $image, $size, $options = [] ) {
	// Return if no ID is found
	if ( ! isset( $image ) || ! $image ) {
		return false;
	}

	// Default options
	$options['size']     = ( isset( $options['size'] ) ? $options['size'] : 'cover' );
	$options['repeat']   = ( isset( $options['repeat'] ) ? $options['repeat'] : 'no-repeat' );
	$options['position'] = ( isset( $options['position'] ) ? $options['position'] : 'center' );

	// Get dynamic image url
	$url = wp_get_attachment_image_url( $image, $size );

	// Output background image style HTML code
	$output = sprintf(
		' style="background-image: url(\'%1$s\'); background-size: %2$s; background-repeat: %3$s; background-position: %4$s;"',
		$url,
		$options['size'],
		$options['repeat'],
		$options['position']
	);

	return $output;
}

/**
 * Load an item from the spritesheet as an SVG
 * @param  string $name   ID reference for SVG, typically path/filename
 * @param  array  $atts   Additional HTML attributes for SVG element
 * @param  string $method Choice of 'use', 'inline', or 'img'
 * @return string         SVG with use reference
 */
function minimalio_get_svg( $name, $atts = [], $method = 'use' ) {
	// Default to role of presentation (icon)
	if ( ! isset( $atts['role'] ) ) {
		$atts['role'] = 'presentation';
	}

	// Handle title independently of attribute if method is use
	if ( isset( $atts['title'] ) && 'use' === $method ) {
		$minimalio_title = $atts['title'];
		unset( $atts['title'] );
	}

	// Build attributes up
	$attributes = '';
	foreach ( $atts as $attr => $value ) {
		if ( ! empty( $value ) ) {
			$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}
	}

	// Create output for each different method
	switch ( $method ) {
		case 'inline': {
				// Output the SVG element inline
				$path     = get_template_directory() . '/assets/dist/vectors/';
				$svg_file = $path . $name . '.svg';
				$output   = file_exists( $svg_file ) ? file_get_contents( $svg_file ) : '';
				break;
		}
		case 'img': {
				// Output an image tag
				$path   = get_template_directory_uri() . '/assets/dist/vectors/';
				$output = '<img src="' . esc_attr( $path . $name . '.svg' ) . '"' . $attributes . '/>';
				break;
		}
		default: {
				// If not inclded already, add spritesheet to footer
			if ( ! has_action( 'wp_footer', 'minimalio_include_spritesheet' ) ) {
				add_action( 'wp_footer', 'minimalio_include_spritesheet' );
			}

				// Alternatively here we could reference the spritesheet as url before #.

				$output  = '<svg' . $attributes . '>';
				$output .= ( isset( $minimalio_title ) ? '<title>' . $minimalio_title . '</title>' : '' );
				$output .= '<use xlink:href="#' . esc_attr( trim( $name ) ) . '"/>';
				$output .= '</svg>';
			break;
		}
	}

	return $output;
}

/**
 * Action function to include the SVG spritesheet
 * @return null
 */
function minimalio_include_spritesheet() {
	echo '<div class="svg-sprites" style="display: none;">';
	include_once get_template_directory() . '/assets/dist/minimalio-spritesheet.svg';
	echo '</div>';
}

/**
 * Define all of the active social links for use in the CMS (displaying social link fields)
 * and when processing the social links on the front-end of the website
 * @return array $socials a key => value array of the field/social name and the
 * "pretty field label/front end display label"
 */
function minimalio_get_socials_available() {

	// This mapping controls which socials are available in the CMS
	$socials = [
		'mail' => 'Mail',
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'twitter'   => 'Twitter',
		'linkedin'  => 'LinkedIN',
		'pinterest' => 'Pinterest',
		'youtube'   => 'YouTube',
		'vimeo'     => 'Vimeo',
		'applemusic' => 'Apple Music',
		'bandcamp' => 'Bandcamp',
		'behance' => 'Behance',
		'bluesky' => 'Bluesky',
		'codepen' => 'CodePen',
		'deviantart' => 'DeviantArt',
		'dribbble' => 'Dribbble',
		'discord' => 'Discord',
		'etsy' => 'Etsy',
		'flickr' => 'Flickr',
		'github' => 'GitHub',
		'goodreads' => 'Goodreads',
		'imdb' => 'IMDB',
		'lastfm' => 'Last.fm',
		'mastodon' => 'Mastodon',
		'medium' => 'Medium',
		'patreon' => 'Patreon',
		'pixelfed' => 'Pixelfed',
		'reddit' => 'Reddit',
		'rss' => 'RSS',
		'snapchat' => 'Snapchat',
		'soundcloud' => 'SoundCloud',
		'spotify' => 'Spotify',
		'tiktok' => 'TikTok',
		'twitch' => 'Twitch',
		'vk' => 'VK',
		'x' => 'X',
	];

	// Return this list for futher processing
	return $socials;
}

/**
 * Get all the active social links
 * @return null
 */
function minimalio_get_social_links() {

	// Get the array of social links to process
	$socials = minimalio_get_socials_available();

	// Set up an empty array for output
	$output = [];

	// Loop over all the available social options
	foreach ( $socials as $key => $name ) {
		// If no value is set then skip it
		if ( ! $url = get_theme_mod( 'minimalio_settings_social_media_' . $key ) ) {
			continue;
		}

		// Append key and URL to output
		$output[ $key ] = $url;
	}

	return $output;
}

/**
 * Load a template and safely pass variables into it
 *
 * @deprecated Use minimalio_get_part instead.
 * @param  string $template  Template path without extension
 * @param  array  $variables Variables to pass into part
 * @return boolean           True on success, false on failure
 */
function minimalio_load_part( $template, $variables = [] ) {
	return minimalio_get_part( $template, $variables );
}

/**
 * Get a template and safely pass variables into it
 * @param  string       $template  Template path without extension
 * @param  array        $variables Variables to pass into part
 * @param  array|string $cache     Cache key as string or key & ttl in array
 * @param  boolean      $echo      Echo output when true, return when false
 * @return boolean                  True on success, false on failure
 */
function minimalio_get_part( $template, $variables = [], $cache = false, $echo = true, $ttl = 0 ) {
	// If variables aren't an array then fail
	if ( ! is_array( $variables ) ) {
		return false;
	}
	// Check cache options
	if ( $cache ) {
		// Handle different $cache formats
		if ( is_string( $cache ) ) {
			$key = $cache;
			$ttl = DAY_IN_SECONDS;
		} elseif ( is_array( $cache ) ) {
			$key         = isset( $cache['key'] ) ? $cache['key'] : null;
			$ttl         = isset( $cache['ttl'] ) ? $cache['ttl'] : DAY_IN_SECONDS;
			$cache_users = isset( $cache['cache_users'] ) ? $cache['cache_users'] : true;
		}

		if ( ! $key ) {
			throw new Exception( 'Invalid cache arguments supplied to ' . __FUNCTION__ );
		}

		// Prepend the cache key
		$key = apply_filters( 'minimalio_part_fragment_prefix', 'minimalio_part_fragment_' ) . $key;

		// If not logged or cache users is set to true attempt cache
		if ( ! is_user_logged_in() || $cache_users ) {
			$output = get_transient( $key );

			// Cache is not empty so let's fetch it.
			if ( ! empty( $output ) ) {
				if ( $echo ) {
					echo $output;
					return true;
				} else {
					return $output;
				}
			}
		}
	} // Cache block for fragment caching. End if().

	// Find the requested template
	$template = locate_template( $template . '.php' );

	// If template couldn't be found then fail
	if ( ! $template ) {
		return false;
	}

	// Store each key value pair in actual variables
	foreach ( $variables as $var => $val ) {
		$$var = $val;
	}

	// Start output buffering
	ob_start();

	// Include the template
	include $template;

	// Get the contents of the buffer
	$output = ob_get_clean();

	// If caching is enabled ($key is set) then save in cache
	if ( isset( $key ) ) {
		set_transient( $key, $output, $ttl );
	}

	// Echo the output and return true
	if ( $echo ) {
		echo $output;
		return true;
	} else {
		return $output;
	}
}


// Filter custom logo with correct classes.
add_filter( 'get_custom_logo', 'minimalio_change_logo_class' );

if ( ! function_exists( 'minimalio_change_logo_class' ) ) {
	/**
	 * Replaces logo CSS class.
	 *
	 * @param string $html Markup.
	 *
	 * @return mixed
	 */
	function minimalio_change_logo_class( $html ) {

		$html = str_replace( 'class="custom-logo"', 'class="img-fluid"', $html );
		$html = str_replace( 'class="custom-logo-link"', 'class="navbar-brand custom-logo-link"', $html );
		$html = str_replace( 'alt=""', 'title="Home" alt="logo"', $html );

		return $html;
	}
}


// Fetch Google Fonts
function minimalio_getGooglefonts() {
	// google api key
	$fonts_list     = [];
	$google_api_key = 'AIzaSyDJE7BEyz0eCDG9S822vr1R8-YvuIaK9Ro';
	$url            = 'https://www.googleapis.com/webfonts/v1/webfonts?key=' .
	$google_api_key;
	$response       = wp_safe_remote_get( $url );

	// Check if request was successful
	if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
		$body = wp_remote_retrieve_body( $response );
		$fonts_list = json_decode( $body, true );

		// Validate response structure
		if ( ! is_array( $fonts_list ) ) {
			$fonts_list = [];
		}
	}

	return $fonts_list;
}

// Google Font weights
function minimalio_getGooglefontWeight() {
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

	$currentFontStyle = get_theme_mod( 'minimalio_typography_settings_google_font' );
	// $currentFontStyle is a web safe font
	if ( $currentFontStyle ) {
		if ( in_array( $currentFontStyle, $safe_fonts ) ) {
			return [
				'100' => __( '100', 'minimalio' ),
				'400' => __( '400', 'minimalio' ),
				'700' => __( '700', 'minimalio' ),
				'900' => __( '900', 'minimalio' ),
			];
		} else {
			// google api key
			$google_api_key = 'AIzaSyDJE7BEyz0eCDG9S822vr1R8-YvuIaK9Ro';
			$url            = 'https://www.googleapis.com/webfonts/v1/webfonts?family=' .
			$currentFontStyle . '&key=' . $google_api_key;
			$response       = wp_safe_remote_get( $url );

			// Check if request was successful
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
				// Return default weight if API call fails
				return [
					'400' => __( '400', 'minimalio' ),
				];
			}

			// Parse the successful response
			$fontWeightsList = json_decode( wp_remote_retrieve_body( $response ), true );

			// Validate the response structure
			if ( isset( $fontWeightsList['items'][0]['variants'] ) && is_array( $fontWeightsList['items'][0]['variants'] ) ) {
				return $fontWeightsList['items'][0]['variants'];
			}

			// Fallback if response structure is unexpected
			return [
				'400' => __( '400', 'minimalio' ),
			];
		}
	} else {
		return [
			'400' => __( '400', 'minimalio' ),
		];
	}
}
