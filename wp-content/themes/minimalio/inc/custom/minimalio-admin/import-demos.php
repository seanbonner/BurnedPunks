<?php

/**
 * Import Demos Admin Page
 * 
 * @package minimalio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get hardcoded demos data
 * Used when plugin is not active (WordPress.org theme requirements)
 */
function minimalio_get_hardcoded_demos() {
    return array(
        'demos' => array(
            array(
                'id' => 'john',
                'name' => 'John',
                'image' => 'https://minimalio.sirv.com/Images/john-minimalio.webp',
                'preview_url' => 'https://john.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'jane',
                'name' => 'Jane',
                'image' => 'https://minimalio.sirv.com/Images/jane-minimalio.webp',
                'preview_url' => 'https://jane.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'caroline',
                'name' => 'Caroline',
                'image' => 'https://minimalio.sirv.com/Images/caroline-minimalio.webp',
                'preview_url' => 'https://caroline.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'stefan',
                'name' => 'Stefan',
                'image' => 'https://minimalio.sirv.com/Images/stefan-minimalio.webp',
                'preview_url' => 'https://stefan.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'alex',
                'name' => 'Alex',
                'image' => 'https://minimalio.sirv.com/Images/alex-minimalio.webp',
                'preview_url' => 'https://alex.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'george',
                'name' => 'George',
                'image' => 'https://minimalio.sirv.com/Images/george-minimalio.webp',
                'preview_url' => 'https://george.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'teresa',
                'name' => 'Teresa',
                'image' => 'https://minimalio.sirv.com/Images/teresa-minimalio.webp',
                'preview_url' => 'https://teresa.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'sofia',
                'name' => 'Sofia',
                'image' => 'https://minimalio.sirv.com/Images/sofia-minimalio.webp',
                'preview_url' => 'https://sofia.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'samuel',
                'name' => 'Samuel',
                'image' => 'https://minimalio.sirv.com/Images/samuel-minimalio.webp',
                'preview_url' => 'https://samuel.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'richard',
                'name' => 'Richard',
                'image' => 'https://minimalio.sirv.com/Images/richard-minimalio.webp',
                'preview_url' => 'https://richard.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'nicole',
                'name' => 'Nicole',
                'image' => 'https://minimalio.sirv.com/Images/nicole-minimalio.webp',
                'preview_url' => 'https://nicole.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'mark',
                'name' => 'Mark',
                'image' => 'https://minimalio.sirv.com/Images/mark-minimalio.webp',
                'preview_url' => 'https://mark.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'francis',
                'name' => 'Francis',
                'image' => 'https://minimalio.sirv.com/Images/francis-minimalio.webp',
                'preview_url' => 'https://francis.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'jimmy',
                'name' => 'Jimmy',
                'image' => 'https://minimalio.sirv.com/Images/jimmy-minimalio.webp',
                'preview_url' => 'https://jimmy.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'lucy',
                'name' => 'Lucy',
                'image' => 'https://minimalio.sirv.com/Images/lucy-minimalio.webp',
                'preview_url' => 'https://lucy.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'alfred',
                'name' => 'Alfred',
                'image' => 'https://minimalio.sirv.com/Images/alfred-minimalio.webp',
                'preview_url' => 'https://alfred.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'tony',
                'name' => 'Tony',
                'image' => 'https://minimalio.sirv.com/Images/tony-minimalio.webp',
                'preview_url' => 'https://tony.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'eadweard',
                'name' => 'Eadweard',
                'image' => 'https://minimalio.sirv.com/Images/eadweard-minimalio.webp',
                'preview_url' => 'https://eadweard.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
            array(
                'id' => 'charles',
                'name' => 'Charles',
                'image' => 'https://minimalio.sirv.com/Images/charles.webp',
                'preview_url' => 'https://charles.minimalio.org',
                'content_url' => '',
                'settings_url' => ''
            ),
        ),
        'last_updated' => '2025-06-26'
    );
}

/**
 * Get demos data
 * Allows plugin to override with remote data via filter
 */
function minimalio_get_demos_data() {
    // Get hardcoded demos as default
    $demos_data = minimalio_get_hardcoded_demos();

    /**
     * Filter demos data
     * Allows plugins (like minimalio-portfolio) to provide remote data
     *
     * @param array $demos_data Default hardcoded demos data
     */
    $filtered_data = apply_filters( 'minimalio_demos_data', $demos_data );

    // Validate filter return value to prevent errors from malformed plugin data
    if ( ! is_array( $filtered_data ) || ! isset( $filtered_data['demos'] ) || ! is_array( $filtered_data['demos'] ) ) {
        error_log( 'Minimalio: Invalid demos data returned from filter, using hardcoded fallback' );
        return $demos_data; // Return original hardcoded data as fallback
    }

    return $filtered_data;
}


/**
 * Import demos page content
 */
function minimalio_demos_page() {
    // Check if minimalio-portfolio plugin is active
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $is_portfolio_plugin_active = is_plugin_active( 'minimalio-portfolio/minimalio-portfolio.php' );

    // Get demos data (hardcoded by default, can be overridden by plugin filter)
    $demos_data = minimalio_get_demos_data();
    $demos = ($demos_data && isset($demos_data['demos'])) ? $demos_data['demos'] : array();
    ?>
    <div class="wrap minimalio-admin-page">
        <h1><?php _e( 'Demos', 'minimalio' ); ?></h1>
        
        <?php if ( ! $is_portfolio_plugin_active ): ?>
            <div class="minimalio-admin-card">
                <h2><?php _e( 'Import Available Only with the Premium Plugin', 'minimalio' ); ?></h2>
                <p><?php _e( 'You can preview the demos, but importing them is only available with the Premium Plugin.', 'minimalio' ); ?></p>
                <a href="https://minimalio.org/premium-plugin/" target="_blank" class="button minimalio-premium-button">
                    <?php _e( 'Purchase Premium Plugin ($49)', 'minimalio' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=minimalio-tutorials#install' ); ?>" class="button button-primary">
                    <?php _e( 'How to Install Tutorial', 'minimalio' ); ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="minimalio-admin-card">
        <h2><?php _e( 'How to Import? Content First, Then Settings.', 'minimalio' ); ?></h2>
            <p class="max-width-62"><?php _e( 'Importing demos couldn\'t be easier. But please, check out the Import Demo Tutorial, to find out how it works, what it imports and how to use the demo content.', 'minimalio' ); ?></p>
            
                <a href="<?php echo admin_url( 'admin.php?page=minimalio-tutorials#tutorial-how-to-import-a-demo' ); ?>" class="button button-secondary minimalio-premium-button">
                    <?php _e( 'Import Demo Tutorial', 'minimalio' ); ?>
                </a>
                <a href="https://minimalio.org/demos" target="_blank" class="button button-primary">
                    <?php _e( 'View Demos on Minimalio Website', 'minimalio' ); ?>
                </a>
            

        </div>
        

        <div class="minimalio-demos-grid">
            <?php if ( empty($demos) ): ?>
                <div class="notice notice-warning">
                    <p><?php _e( 'Sorry, Minimalio is having trouble finding the demos, make sure you are connected to the internet or you can try to refresh demos at the bottom of this page. If the problem persists, please contact the support. Thank you.', 'minimalio' ); ?></p>
                </div>
            <?php else: ?>
                <?php foreach ( $demos as $demo ): ?>
                    <?php
                    // Safely extract demo data with fallbacks
                    $demo_id = isset($demo['id']) ? $demo['id'] : '';
                    $demo_image = isset($demo['image']) ? $demo['image'] : '';
                    $demo_name = isset($demo['name']) ? $demo['name'] : __('Unknown Demo', 'minimalio');
                    $demo_content_url = isset($demo['content_url']) ? $demo['content_url'] : '';
                    $demo_settings_url = isset($demo['settings_url']) ? $demo['settings_url'] : '';
                    $demo_preview_url = isset($demo['preview_url']) ? $demo['preview_url'] : '';
                    ?>
                    <div class="minimalio-demo-item" data-demo-id="<?php echo esc_attr( $demo_id ); ?>">
                        <div class="minimalio-demo-preview">
                            <img src="<?php echo esc_url( $demo_image ); ?>" alt="<?php echo esc_attr( $demo_name . ' Demo' ); ?>">
                        </div>
                        <div class="minimalio-demo-info">
                            <h3><?php echo esc_html( $demo_name ); ?></h3>
                            <div class="minimalio-demo-actions">
                                <?php if ( $is_portfolio_plugin_active ): ?>
                                    <?php if ( !empty($demo_content_url) ): ?>
                                        <a href="#" class="button button-primary minimalio-import-content"
                                           data-demo-type="<?php echo esc_attr( $demo_id ); ?>"
                                           data-content-url="<?php echo esc_url( $demo_content_url ); ?>">
                                            <?php _e( 'Import Content', 'minimalio' ); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ( !empty($demo_settings_url) ): ?>
                                        <a href="#" class="button button-secondary minimalio-import-settings"
                                           data-demo-type="<?php echo esc_attr( $demo_id ); ?>"
                                           data-settings-url="<?php echo esc_url( $demo_settings_url ); ?>">
                                            <?php _e( 'Import Settings', 'minimalio' ); ?>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="button button-primary disabled" title="<?php _e( 'Requires Minimalio Portfolio Plugin', 'minimalio' ); ?>">
                                        <?php _e( 'Import Content', 'minimalio' ); ?>
                                    </span>
                                    <span class="button button-secondary disabled" title="<?php _e( 'Requires Minimalio Portfolio Plugin', 'minimalio' ); ?>">
                                        <?php _e( 'Import Settings', 'minimalio' ); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ( !empty($demo_preview_url) ): ?>
                                    <a href="<?php echo esc_url( $demo_preview_url ); ?>" class="button minimalio-premium-button" target="_blank">
                                        <?php _e( 'Preview', 'minimalio' ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if ( ! $is_portfolio_plugin_active ): ?>
            <div class="minimalio-admin-card">
                <h2><?php _e( 'View all demos at Minimalio website', 'minimalio' ); ?></h2>
                <p><?php _e( 'There might be some new ones...', 'minimalio' ); ?></p>
                <a href="https://minimalio.org/demos" target="_blank" class="button button-primary minimalio-premium-button">
                    <?php _e( 'All Demos', 'minimalio' ); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ( current_user_can( 'manage_options' ) ): ?>
            <div class="minimalio-admin-info">
                <?php
                /**
                 * Allow plugins to add content to the demo management section
                 * Plugin can add refresh functionality here
                 *
                 * @since 1.0.0
                 */
                do_action( 'minimalio_demo_management_section' );
                ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}