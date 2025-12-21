<?php
/**
 * Minimalio Child Theme - BurnedPunks
 *
 * @package minimalio-child
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Enqueue parent and child theme styles
 */
function minimalio_child_enqueue_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style(
        'minimalio-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme('minimalio')->get('Version')
    );

    // Enqueue child theme stylesheet
    wp_enqueue_style(
        'minimalio-child-style',
        get_stylesheet_uri(),
        array('minimalio-parent-style'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'minimalio_child_enqueue_styles');
