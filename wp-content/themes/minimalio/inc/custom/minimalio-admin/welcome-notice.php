<?php

/**
 * Minimalio Welcome Notice
 * 
 * @package minimalio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Show welcome notice on themes page after theme activation
 */
add_action( 'after_switch_theme', 'minimalio_set_activation_flag' );
function minimalio_set_activation_flag() {
    // Show notice every time theme is activated, but only if not already dismissed
    if ( ! get_user_meta( get_current_user_id(), 'minimalio_welcome_dismissed', true ) ) {
        set_transient( 'minimalio_welcome_notice', true, 0 );
    }
}

/**
 * Display welcome notice on themes page
 */
add_action( 'admin_notices', 'minimalio_welcome_notice' );
function minimalio_welcome_notice() {
    // Only show on themes page and if notice hasn't been dismissed
    if ( ! get_transient( 'minimalio_welcome_notice' ) || ! function_exists( 'get_current_screen' ) ) {
        return;
    }
    
    // Check if user has already dismissed the notice
    if ( get_user_meta( get_current_user_id(), 'minimalio_welcome_dismissed', true ) ) {
        return;
    }
    
    $screen = get_current_screen();
    if ( $screen->id !== 'themes' ) {
        return;
    }
    
    // Get current theme
    $theme = wp_get_theme();
    
    // Check if this is a child theme
    $is_child_theme = is_child_theme();
    $theme_name = $theme->get( 'Name' );
    
    // Get theme screenshot (will automatically use child theme screenshot if available)
    $screenshot = $theme->get_screenshot();
    
    // Prepare welcome message based on whether it's a child theme
    if ( $is_child_theme ) {
        $welcome_title = sprintf( __( 'Thank you for using %s!', 'minimalio' ), $theme_name );
        $welcome_text = sprintf( __( 'You are using a child theme based on Minimalio. Please checkout the Minimalio admin page, where you can find the demos, tutorials and more.', 'minimalio' ) );
        $screenshot_alt = sprintf( __( '%s Theme Screenshot', 'minimalio' ), $theme_name );
    } else {
        $welcome_title = __( 'Thank you for using Minimalio!', 'minimalio' );
        $welcome_text = __( 'Please checkout the Minimalio admin page, where you can find the demos, tutorials and more.', 'minimalio' );
        $screenshot_alt = __( 'Minimalio Theme Screenshot', 'minimalio' );
    }
    
    $customizer_text = __( 'To adjust the theme, please use the Customizer (Appearance → Customize).', 'minimalio' );
    
    ?>
    <div class="notice notice-success is-dismissible minimalio-welcome-notice">
        <div class="minimalio-notice-content">
            <div class="minimalio-notice-text">
                <h1><?php echo esc_html( $welcome_title ); ?></h1>
                <p><?php echo esc_html( $welcome_text ); ?></p>
                <p><?php echo esc_html( $customizer_text ); ?></p>
                <a href="<?php echo admin_url( 'admin.php?page=minimalio-dashboard' ); ?>" class="button minimalio-premium-button">
                    <?php _e( 'Minimalio Admin Page', 'minimalio' ); ?>
                </a>
            </div>
            <?php if ( $screenshot ) : ?>
            <div class="minimalio-notice-image">
                <img src="<?php echo esc_url( $screenshot ); ?>" alt="<?php echo esc_attr( $screenshot_alt ); ?>">
            </div>
            <?php endif; ?>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('.minimalio-welcome-notice').on('click', '.notice-dismiss', function() {
                $.post(ajaxurl, {
                    action: 'minimalio_dismiss_notice',
                    notice: 'minimalio-welcome-notice',
                    nonce: '<?php echo wp_create_nonce( 'minimalio_dismiss_notice' ); ?>'
                });
            });
        });
        </script>
    </div>
    <?php
}

/**
 * Handle AJAX dismiss for welcome notice
 */
add_action( 'wp_ajax_minimalio_dismiss_notice', 'minimalio_dismiss_notice' );
function minimalio_dismiss_notice() {
    check_ajax_referer( 'minimalio_dismiss_notice', 'nonce' );
    
    if ( isset( $_POST['notice'] ) && $_POST['notice'] === 'minimalio-welcome-notice' ) {
        // Set user meta to permanently dismiss the notice for this user
        update_user_meta( get_current_user_id(), 'minimalio_welcome_dismissed', true );
        delete_transient( 'minimalio_welcome_notice' );
        wp_die();
    }
    
    wp_die();
}

