<?php
/**
 * Declaring widgets
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add filter to the parameters passed to a widget's display callback.
 * The filter is evaluated on both the front and the back end!
 *
 * @link https://developer.wordpress.org/reference/hooks/dynamic_sidebar_params/
 */


add_action( 'widgets_init', 'minimalio_widgets_init' );

if ( ! function_exists( 'minimalio_widgets_init' ) ) {
	/**
	 * Initializes themes widgets.
	 */
	function minimalio_widgets_init() {
		register_sidebar(
			[
				'name'          => __( 'General Right Sidebar', 'minimalio' ),
				'id'            => 'right-sidebar',
				'description'   => __( 'Right sidebar widget area', 'minimalio' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			]
		);

		register_sidebar(
			[
				'name'          => __( 'General Left Sidebar', 'minimalio' ),
				'id'            => 'left-sidebar',
				'description'   => __( 'Left sidebar widget area', 'minimalio' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			]
		);

		register_sidebar(
			[
				'name'          => __( 'Blog Right Sidebar', 'minimalio' ),
				'id'            => 'right-sidebar-blog',
				'description'   => __( 'Right sidebar widget area for Blog', 'minimalio' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			]
		);

		register_sidebar(
			[
				'name'          => __( 'Blog Left Sidebar', 'minimalio' ),
				'id'            => 'left-sidebar-blog',
				'description'   => __( 'Left sidebar widget area for Blog', 'minimalio' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			]
		);

		register_sidebar(
			[
				'name'          => __( 'Header widget', 'minimalio' ),
				'id'            => 'headerfull',
				'description'   => __( 'Header widget for extra information', 'minimalio' ),
				'before_widget' => '<div id="%1$s" class="header-widget %2$s dynamic-classes">',
				'after_widget'  => '</div><!-- .header-widget -->',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			]
		);

		register_sidebar(
			[
				'name'          => __( 'Footer Full', 'minimalio' ),
				'id'            => 'footerfull',
				'description'   => __( 'Full sized footer widget', 'minimalio' ),
				'before_widget' => '<div id="%1$s" class="footer-widget %2$s dynamic-classes">',
				'after_widget'  => '</div><!-- .footer-widget -->',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			]
		);
	}
}
// endif function_exists( 'minimalio_widgets_init' ).
