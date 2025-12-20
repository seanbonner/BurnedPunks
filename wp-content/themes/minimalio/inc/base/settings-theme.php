<?php
/**
 * Check and setup theme's default settings
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'minimalio_setup_theme_default_settings' ) ) {
	function minimalio_setup_theme_default_settings() {

		// check if settings are set, if not set defaults.
		// Sidebar position.
		$minimalio_settings_sidebar_position = get_theme_mod( 'minimalio_settings_sidebar_position' );
		if ( '' === $minimalio_settings_sidebar_position ) {
			set_theme_mod( 'minimalio_settings_sidebar_position', 'none' );
		}

		// Container width.
		$minimalio_settings_container_type = get_theme_mod( 'minimalio_settings_container_type' );
		if ( '' === $minimalio_settings_container_type ) {
			set_theme_mod( 'minimalio_settings_container_type', 'container' );
		}

		// Header container
		$minimalio_settings_header_container_type = get_theme_mod( 'minimalio_settings_header_container_type' );
		if ( '' == $minimalio_settings_header_container_type ) {
			set_theme_mod( 'minimalio_settings_header_container_type', 'container' );
		}

		// Footer container
		$minimalio_settings_footer_container = get_theme_mod( 'minimalio_settings_footer_container' );
		if ( '' === $minimalio_settings_footer_container ) {
			set_theme_mod( 'minimalio_settings_footer_container', 'container' );
		}
	}
}
