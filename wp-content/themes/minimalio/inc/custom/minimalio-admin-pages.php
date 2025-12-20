<?php

/**
 * Minimalio Admin Pages
 * 
 * @package minimalio
 */

defined( 'ABSPATH' ) || exit;

// Include WordPress plugin functions
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Include individual admin page files
require_once get_template_directory() . '/inc/custom/minimalio-admin/welcome-notice.php';
require_once get_template_directory() . '/inc/custom/minimalio-admin/import-demos.php';
require_once get_template_directory() . '/inc/custom/minimalio-admin/tutorials.php';

// Only include demo importer if portfolio plugin is active
if ( is_plugin_active( 'minimalio-portfolio/minimalio-portfolio.php' ) ) {
    // Demo importer is now handled by the plugin
}

new Minimalio_Admin_Pages;

class Minimalio_Admin_Pages {

    function __construct() {
        add_action( 'admin_menu', [ $this, 'minimalio_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'minimalio_admin_styles' ] );
    }

    /**
     * Add Minimalio admin menu with subpages
     */
    public function minimalio_admin_menu() {
        // Main menu page
        add_menu_page(
            __( 'Minimalio', 'minimalio' ),
            __( 'Minimalio', 'minimalio' ),
            'manage_options',
            'minimalio-dashboard',
            [ $this, 'minimalio_dashboard_page' ],
            get_template_directory_uri() . '/assets/dist/vectors/icon-logo.svg',
            3
        );


        // Import demos subpage
        add_submenu_page(
            'minimalio-dashboard',
            __( 'Demos', 'minimalio' ),
            __( 'Demos', 'minimalio' ),
            'manage_options',
            'minimalio-demos',
            'minimalio_demos_page'
        );

        // Tutorials subpage
        add_submenu_page(
            'minimalio-dashboard',
            __( 'Tutorials', 'minimalio' ),
            __( 'Tutorials', 'minimalio' ),
            'manage_options',
            'minimalio-tutorials',
            'minimalio_tutorials_page'
        );

        // Theme Settings subpage
        add_submenu_page(
            'minimalio-dashboard',
            __( 'Theme Settings', 'minimalio' ),
            __( 'Theme Settings', 'minimalio' ),
            'manage_options',
            '/customize.php',
            ''
        );
    }

    /**
     * Enqueue admin styles and scripts
     */
    public function minimalio_admin_styles( $hook_suffix ) {
        if ( strpos( $hook_suffix, 'minimalio' ) !== false || $hook_suffix === 'themes.php' ) {
            wp_enqueue_style( 'minimalio-admin', get_template_directory_uri() . '/assets/dist/css/admin.min.css', [], MINIMALIO_VERSION );
        }
    }

    /**
     * Main dashboard page
     */
    public function minimalio_dashboard_page() {
        ?>
        <div class="wrap minimalio-admin-page">
            <h1><?php _e( 'Minimalio Dashboard', 'minimalio' ); ?></h1>
            
            <div class="minimalio-admin-card">
                <div class="minimalio-two-columns">
                    <div class="minimalio-column-text">
                        <h2><?php _e( 'Welcome to Minimalio!', 'minimalio' ); ?></h2>
                        <p  class="minimalio-moto"><?php _e( 'The minimalist portfolio theme.', 'minimalio' ); ?></p>

                        <p class="minimalio-inspire-text"><?php _e( 'Get inspired by the websites made with Minimalio:', 'minimalio' ); ?></p>
                    
                        
                        <a href="https://minimalio.org/#showcase" target="_blank" class="button minimalio-premium-button">
                            <?php _e( 'Minimalio Showcase', 'minimalio' ); ?>
                        </a>
                        <a href="https://minimalio.org/" target="_blank"  class="button">
                            <?php _e( 'Minimalio Website', 'minimalio' ); ?>
                        </a>

                        <p class="minimalio-inspire-text"><?php _e( 'Minimalio isn\'t just a theme, it comes with demos and tutorials!', 'minimalio' ); ?></p>    
                        <a href="<?php echo admin_url( 'customize.php' ); ?>" class="button minimalio-premium-button">
                            <?php _e( 'Theme Settings', 'minimalio' ); ?>
                        </a>
                        <a href="<?php echo admin_url( 'admin.php?page=minimalio-demos' ); ?>" class="button button-primary">
                            <?php _e( 'Demos', 'minimalio' ); ?>
                        </a>
                        <a href="<?php echo admin_url( 'admin.php?page=minimalio-tutorials' ); ?>" class="button button-primary">
                            <?php _e( 'Tutorials', 'minimalio' ); ?>
                        </a>
                       
                        
                        
                        
                    </div>
                    <div class="minimalio-column-video">
                        <div class="muse-video-player" data-title="0" data-video="L4hM7LG" data-logo="https://minimalio.sirv.com/Images/Minimalio-logo-muse.png" data-subtitles="auto" data-width="100%"></div><script src="https://muse.ai/static/js/embed-player.min.js"></script>
                    </div>
                </div>
            </div>
            
            <div class="minimalio-admin-card">
                <h2><?php _e( 'Premium Features with the Premium Plugin', 'minimalio' ); ?></h2>
                <p><?php _e( 'Unlock the full potential of Minimalio:', 'minimalio' ); ?></p>
                
                <div class="minimalio-premium-features">
                    <ul>
                        <li><strong><?php _e( 'Demo Import', 'minimalio' ); ?></strong> - <?php _e( 'Import complete demo sites with content, theme settings, and widgets in just two clicks.', 'minimalio' ); ?></li>
                        <li><strong><?php _e( 'Extra Content Type - Portfolio', 'minimalio' ); ?></strong> - <?php _e( 'Access to portfolio post type with  extra possibilities for the portfolio page.', 'minimalio' ); ?></li>
                        <li><strong><?php _e( 'Gutenberg Blocks for Gallery and Videos', 'minimalio' ); ?></strong> - <?php _e( 'Get a capable image gallery, and video blocks for Vimeo and YouTube.', 'minimalio' ); ?></li>
                        <li><strong><?php _e( 'Unlocks the Theme Settings', 'minimalio' ); ?></strong> - <?php _e( 'Get control over the Portfolio section, Social Icons, Lightbox and Mobile menu.', 'minimalio' ); ?></li>
                        <li><strong><?php _e( 'Premium Support', 'minimalio' ); ?></strong> - <?php _e( 'If you have any issues, just contact the support. ', 'minimalio' ); ?></li>
                        <li><strong><?php _e( '30 Days Money Back Guarantee', 'minimalio' ); ?></strong> - <?php _e( 'No questions asked, simply get your money back.', 'minimalio' ); ?></li>
                        <li><strong><?php _e( 'Lifetime Updates', 'minimalio' ); ?></strong> - <?php _e( 'No subscriptions, just pay once and enjoy forever.', 'minimalio' ); ?></li>
                    </ul>
                    
                    <a href="https://minimalio.org/premium-plugin/" target="_blank" class="button minimalio-premium-button">
                        <?php _e( 'Purchase Premium Plugin ($49)', 'minimalio' ); ?>
                    </a>

                    <a href="<?php echo admin_url( 'admin.php?page=minimalio-tutorials#install' ); ?>" class="button button-primary">
                        <?php _e( 'How to Install Tutorial', 'minimalio' ); ?>
                    </a>
                </div>
            </div>
            
            <div class="minimalio-admin-grid">

                <div class="minimalio-admin-card">
                    <h3><?php _e( 'Import Demos', 'minimalio' ); ?></h3>
                    <p><?php _e( 'Check out the Minimalio Demos, and with the Premium Plugin, you can import them by simple two clicks.', 'minimalio' ); ?></p>
                    <a href="<?php echo admin_url( 'admin.php?page=minimalio-demos' ); ?>" class="button button-primary">
                        <?php _e( 'Browse Demos', 'minimalio' ); ?>
                    </a>
                </div>

                <div class="minimalio-admin-card">
                    <h3><?php _e( 'Tutorials', 'minimalio' ); ?></h3>
                    <p><?php _e( 'Minimalio isn\'t just a theme, it\'s a platform for people with zero WordPress and coding skills to create their website.', 'minimalio' ); ?></p>
                    <a href="<?php echo admin_url( 'admin.php?page=minimalio-tutorials' ); ?>" class="button button-primary">
                        <?php _e( 'View Tutorials', 'minimalio' ); ?>
                    </a>
                </div>

                <div class="minimalio-admin-card">
                    <h3><?php _e( 'Support', 'minimalio' ); ?></h3>
                    <p><?php _e( 'The customers of the Premium Plugin receive premium support. After the purchase, you will get the contact email.', 'minimalio' ); ?></p>
                    <a href="https://minimalio.org/premium-plugin/support/" target="_blank" class="button button-primary">
                        <?php _e( 'Support page', 'minimalio' ); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
}