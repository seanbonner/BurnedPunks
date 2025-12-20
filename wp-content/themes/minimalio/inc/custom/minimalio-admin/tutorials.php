<?php

/**
 * Tutorials Admin Page
 * 
 * @package minimalio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get hardcoded tutorials data
 * Used when plugin is not active (WordPress.org theme requirements)
 */
function minimalio_get_hardcoded_tutorials() {
    return array(
        'tutorials' => array(
            array(
                'id' => 'portfolio-website-from-start-to-finish',
                'name' => 'Portfolio Website from Start to Finish',
                'description' => 'A comprehensive tutorial that walks through creating a complete WordPress portfolio website, covering steps like deleting default content, installing Minimalio Theme and Premium Plugin, installing security plugins, creating portfolio and about pages, setting up menus and design, creating backups and SEO configuration.',
                'url' => 'https://minimalio.org/tutorials/portfolio-website-from-start-to-finish/',
                'image' => 'https://minimalio.sirv.com/Images/portfolio-website.webp',
                'category' => 'beginners',
                'videoUrl' => 'FZS7AEz'
            ),
            array(
                'id' => 'gutenberg-editor-for-beginners',
                'name' => 'Gutenberg Editor for Beginners',
                'description' => 'A tutorial explaining the basics of editing content with the default WordPress Gutenberg editor, covering UI, blocks, design elements, and content management. Covers four parts: UI and buttons, how blocks work, important blocks, and content backup and recovery.',
                'url' => 'https://minimalio.org/tutorials/gutenberg-editor-for-beginners/',
                'image' => 'https://minimalio.sirv.com/Images/gutenberg.webp',
                'category' => 'content',
                'videoUrl' => 'mXFEkNb'
            ),
            array(
                'id' => 'setting-up-the-portfolio-page',
                'name' => 'Setting up the Portfolio Page',
                'description' => 'A tutorial for creating a portfolio page in WordPress using the Minimalio theme, covering page creation, customizer options, portfolio display settings, and configuration details. Explains how to create a new page with Portfolio Template and customize portfolio settings including filtering, load more functionality, and display options.',
                'url' => 'https://minimalio.org/tutorials/setting-up-the-portfolio-page/',
                'image' => 'https://minimalio.sirv.com/Images/portfolio-page.webp',
                'category' => 'content',
                'videoUrl' => 'JTgJR3R'
            ),
            array(
                'id' => 'creating-a-single-portfolio',
                'name' => 'Creating a Single Portfolio',
                'description' => 'A tutorial about creating individual portfolio pages in WordPress, specifically for the Minimalio theme/plugin. Covers steps to create a portfolio item in WordPress, including guidance on setting title, featured image, categories, and explains customizer options for portfolio pages.',
                'url' => 'https://minimalio.org/tutorials/creating-a-single-portfolio/',
                'image' => 'https://minimalio.sirv.com/Images/single-portfolio.webp',
                'category' => 'content',
                'videoUrl' => 'Ljs3veZ'
            ),
            array(
                'id' => 'how-to-import-a-demo',
                'name' => 'How to Import a Demo',
                'description' => 'A step-by-step tutorial for importing WordPress demo content and the Minimalio theme options. It explains what exactly do the buttons (Import Content, Import Settings) do and how to use them. How to adjust the imported content and what to do, if you want to import another demo.',
                'url' => 'https://minimalio.org/tutorials/how-to-import-a-demo/',
                'image' => 'https://minimalio.sirv.com/Images/demo-import.webp',
                'category' => 'beginners',
                'videoUrl' => 'EyFNXsq'
            ),
            array(
                'id' => 'setting-up-the-blog-page',
                'name' => 'Setting up the Blog Page',
                'description' => 'A tutorial about configuring a blog page in the Minimalio WordPress theme, covering various settings like sidebar positioning, category filtering, post display options, and single post configurations. Explains how to create a blog page and customize blog settings in Theme Settings.',
                'url' => 'https://minimalio.org/tutorials/setting-up-the-blog-page/',
                'image' => 'https://minimalio.sirv.com/Images/blog-page.webp',
                'category' => 'content',
                'videoUrl' => 'YATgcjP'
            ),
            array(
                'id' => 'how-to-install-the-theme-and-the-premium-plugin',
                'name' => 'How to Install the Theme and the Premium Plugin',
                'description' => 'Tutorial explaining how to install the Minimalio WordPress theme and its optional Premium plugin, highlighting the difference between theme and plugin installation processes. Covers installation from WordPress repository, uploading ZIP files, and license key activation.',
                'url' => 'https://minimalio.org/tutorials/how-to-install-the-theme-and-the-premium-plugin/',
                'image' => 'https://minimalio.sirv.com/Images/installing-theme.webp',
                'category' => 'beginners',
                'videoUrl' => 'VHtDVWx'
            ),
            array(
                'id' => 'mobile-menu-adjustments',
                'name' => 'Mobile Menu Adjustments',
                'description' => 'A tutorial about configuring mobile menu settings in the Minimalio WordPress theme, specifically focusing on options available in the Premium plugin. Covers breaking point for mobile menu display, mobile menu styles, logo, color customization, and font settings.',
                'url' => 'https://minimalio.org/tutorials/mobile-menu/',
                'image' => 'https://minimalio.sirv.com/Images/mobile-menu.webp',
                'category' => 'theme options',
                'videoUrl' => 'ooSXYZj'
            ),
            array(
                'id' => 'small-design-changes-with-custom-css',
                'name' => 'Small Design Changes with Custom CSS',
                'description' => 'A tutorial explaining how to make simple design modifications to a website using CSS, focusing on finding element classes and applying custom styles. Teaches how to find HTML element classes using browser Developer Tools and apply custom CSS in theme options.',
                'url' => 'https://minimalio.org/tutorials/small-design-changes-with-custom-css/',
                'image' => 'https://minimalio.sirv.com/Images/custom-css.webp',
                'category' => 'theme options',
                'videoUrl' => 'i5g25oU'
            ),
            array(
                'id' => 'adding-videos-in-gutenberg-editor',
                'name' => 'Adding Videos in Gutenberg Editor',
                'description' => 'The tutorial explains how to add videos to a WordPress website using Gutenberg, with a focus on using Vimeo and YouTube. Covers using custom Gutenberg blocks (Vimeo/YouTube iframes), video banners, and embedding via HTML. Recommends video hosting services like Vimeo and YouTube.',
                'url' => 'https://minimalio.org/tutorials/adding-videos/',
                'image' => 'https://minimalio.sirv.com/Images/videos.webp',
                'category' => 'content',
                'videoUrl' => 'MLFk8q8'
            ),
            array(
                'id' => 'header-options-social-media-icons',
                'name' => 'Header Options + Social Media Icons',
                'description' => 'A tutorial about customizing header styles, social media icons, and various design options in the Minimalio WordPress theme. Covers header styles like horizontal, vertical, centered menu, explains how to set up logo, fixed header, transparent header, and demonstrates configuring social media icons.',
                'url' => 'https://minimalio.org/tutorials/header-options-social-media-icons/',
                'image' => 'https://minimalio.sirv.com/Images/header.webp',
                'category' => 'theme options',
                'videoUrl' => '3Vtb6Ve'
            ),
            array(
                'id' => 'wordpress-security-and-backup',
                'name' => 'WordPress Security and Backup',
                'description' => 'A tutorial about securing WordPress websites and creating backups, focusing on two key plugins: Duplicator (for backups) and All in One Security (for website protection). Explains that WordPress is frequently targeted by automated bots and how changing login URL can improve security.',
                'url' => 'https://minimalio.org/tutorials/wordpress-security-and-backup/',
                'image' => 'https://minimalio.sirv.com/Images/security.webp',
                'category' => 'beginners',
                'videoUrl' => '5mFvJ7n'
            ),
            array(
                'id' => 'setting-google-title-and-description-seo-in-wordpress',
                'name' => 'Setting Google Title and Description (SEO) in WordPress',
                'description' => 'A tutorial about configuring how your website appears in Google search results, specifically focusing on setting the title and description using the Rank Math SEO plugin in WordPress. Covers controlling search result appearance, recommends keeping description around 150-160 characters.',
                'url' => 'https://minimalio.org/tutorials/setting-google-title-and-description-seo-in-wordpress/',
                'image' => 'https://minimalio.sirv.com/Images/seo.webp',
                'category' => 'beginners',
                'videoUrl' => 'pQ5gCbs'
            ),
            array(
                'id' => 'typography-google-fonts-responsive-headings',
                'name' => 'Typography, Google fonts, Responsive Headings',
                'description' => 'A tutorial about typography settings in the Minimalio WordPress theme, covering font selection, styling, and responsive design for headings. Explains how to choose between web safe fonts and Google Fonts, customize font weight, style, color, and configure responsive heading sizes.',
                'url' => 'https://minimalio.org/tutorials/typography/',
                'image' => 'https://minimalio.sirv.com/Images/typography.webp',
                'category' => 'theme options',
                'videoUrl' => 'rhgAfnB'
            ),
            array(
                'id' => 'theme-layout-page-options-and-background',
                'name' => 'Theme Layout, Page Options and Background',
                'description' => 'A tutorial about WordPress theme customization options, covering container width, scrollbar settings, background configuration, page title display, and sidebar positioning. Includes sections on Theme Layout Options, Container Width, Stable Scrollbar, Default 404 Page, and Background Settings.',
                'url' => 'https://minimalio.org/tutorials/theme-layout-page-options-and-background/',
                'image' => 'https://minimalio.sirv.com/Images/layout.webp',
                'category' => 'theme options',
                'videoUrl' => '6NSPniw'
            ),
            array(
                'id' => 'adding-a-gallery-in-gutenberg',
                'name' => 'Adding a Gallery in Gutenberg',
                'description' => 'A tutorial about adding image galleries in WordPress Gutenberg editor, with options for using default WordPress gallery or Minimalio Gallery plugin. Covers gallery features like Grid or Masonry layout options, configurable column counts, adjustable image spacing, hover effects, and automatic lightbox functionality.',
                'url' => 'https://minimalio.org/tutorials/adding-a-gallery/',
                'image' => 'https://minimalio.sirv.com/Images/gallery.webp',
                'category' => 'content',
                'videoUrl' => '2tgTsH6'
            ),
            array(
                'id' => 'francis-demo',
                'name' => 'Francis Demo',
                'description' => 'A tutorial video explaining the technical details of a website demo, focusing on layout, responsiveness, and CSS customization for a portfolio website. Discusses website layout challenges, explains use of custom CSS for responsive design, and recommends using Yoast Duplicate Post plugin to replicate the demo.',
                'url' => 'https://minimalio.org/tutorials/francis-demo/',
                'image' => 'https://minimalio.sirv.com/Images/francis.webp',
                'category' => 'demos',
                'videoUrl' => 'kwtkHA3'
            ),
            array(
                'id' => 'art-portfolio-child-theme',
                'name' => 'Art Portfolio Child Theme / Eadweard Demo',
                'description' => 'A tutorial about the Art Portfolio child theme for Minimalio, featuring the Eadweard Demo. This Minimalio child theme adds extra Theme Options for the purpose of full control over the specific adjustments of the Eadward Demo. So, if you like this demo, the Art Portfolio Child theme is exactly for you.',
                'url' => 'https://minimalio.org/art-portfolio-child-theme/',
                'image' => 'https://minimalio.sirv.com/Images/art-portfolio-child-theme.jpg',
                'category' => 'demos',
                'videoUrl' => 'C7tvjgh'
            ),
        ),
        'last_updated' => '2025-06-26T10:00:00Z'
    );
}

/**
 * Get tutorials data
 * Allows plugin to override with remote data via filter
 */
function minimalio_get_tutorials_data() {
    // Get hardcoded tutorials as default
    $tutorials_data = minimalio_get_hardcoded_tutorials();

    /**
     * Filter tutorials data
     * Allows plugins (like minimalio-portfolio) to provide remote data
     *
     * @param array $tutorials_data Default hardcoded tutorials data
     */
    $filtered_data = apply_filters( 'minimalio_tutorials_data', $tutorials_data );

    // Validate filter return value to prevent errors from malformed plugin data
    if ( ! is_array( $filtered_data ) || ! isset( $filtered_data['tutorials'] ) || ! is_array( $filtered_data['tutorials'] ) ) {
        error_log( 'Minimalio: Invalid tutorials data returned from filter, using hardcoded fallback' );
        return $tutorials_data; // Return original hardcoded data as fallback
    }

    return $filtered_data;
}


/**
 * Group tutorials by category in specific order
 */
function minimalio_group_tutorials_by_category($tutorials) {
    $grouped = array();
    $category_order = array('beginners', 'content', 'theme options', 'demos');
    
    // Initialize categories in order
    foreach ($category_order as $category) {
        $grouped[$category] = array();
    }
    
    // Group tutorials by category
    foreach ($tutorials as $tutorial) {
        if (isset($tutorial['category'])) {
            $category = $tutorial['category'];
            if (isset($grouped[$category])) {
                $grouped[$category][] = $tutorial;
            }
        }
    }
    
    // Remove empty categories
    return array_filter($grouped);
}

/**
 * Get category display name
 */
function minimalio_get_category_display_name($category) {
    $names = array(
        'beginners' => __('Beginner Tutorials', 'minimalio'),
        'content' => __('Content Management', 'minimalio'),
        'demos' => __('Demos and Child Themes', 'minimalio'),
        'theme options' => __('Theme Options', 'minimalio')
    );
    
    return isset($names[$category]) ? $names[$category] : ucfirst($category);
}

/**
 * Tutorials page content
 */
function minimalio_tutorials_page() {
    // Get tutorials data (hardcoded by default, can be overridden by plugin filter)
    $tutorials_data = minimalio_get_tutorials_data();
    $tutorials = ($tutorials_data && isset($tutorials_data['tutorials'])) ? $tutorials_data['tutorials'] : array();

    // Group tutorials by category
    $grouped_tutorials = minimalio_group_tutorials_by_category($tutorials);
    ?>
    <div class="wrap minimalio-admin-page">
        <h1><?php _e( 'Tutorials', 'minimalio' ); ?></h1>

        <?php if ( empty($tutorials) ): ?>
            <div class="notice notice-warning">
                <p><?php _e( 'Sorry, Minimalio is having trouble finding the tutorials, make sure you are connected to the internet and if the problem persists, please contact the support. Thank you.', 'minimalio' ); ?></p>
            </div>
        <?php else: ?>
            <?php foreach ( $grouped_tutorials as $category => $category_tutorials ): ?>
                <?php if ( !empty($category_tutorials) ): ?>
                    <div class="minimalio-tutorials-category" id="<?php echo esc_attr($category); ?>">
                        <h2 class="minimalio-category-title"><?php echo esc_html( minimalio_get_category_display_name($category) ); ?></h2>
                        
                        <div class="minimalio-tutorials-grid">
                            <?php foreach ( $category_tutorials as $tutorial ): ?>
                                <?php
                                // Safely extract tutorial data with fallbacks
                                $tutorial_id = isset($tutorial['id']) ? $tutorial['id'] : '';
                                $tutorial_image = isset($tutorial['image']) ? $tutorial['image'] : '';
                                $tutorial_name = isset($tutorial['name']) ? $tutorial['name'] : __('Unknown Tutorial', 'minimalio');
                                $tutorial_description = isset($tutorial['description']) ? $tutorial['description'] : '';
                                $tutorial_url = isset($tutorial['url']) ? $tutorial['url'] : '';
                                ?>
                                <div class="minimalio-tutorial-item" id="tutorial-<?php echo esc_attr( $tutorial_id ); ?>" data-tutorial-id="<?php echo esc_attr( $tutorial_id ); ?>">
                                    <div class="minimalio-tutorial-image">
                                        <img src="<?php echo esc_url( $tutorial_image ); ?>" alt="<?php echo esc_attr( $tutorial_name ); ?>">
                                    </div>
                                    <div class="minimalio-tutorial-content">
                                        <h3><?php echo esc_html( $tutorial_name ); ?></h3>
                                        <p><?php echo esc_html( $tutorial_description ); ?></p>
                                        <div class="minimalio-tutorial-actions">
                                            <?php if ( !empty($tutorial_url) ): ?>
                                                <a href="<?php echo esc_url( $tutorial_url ); ?>" class="button button-primary" target="_blank">
                                                    <?php _e( 'View Tutorial', 'minimalio' ); ?>
                                                </a>
                                            <?php endif; ?>
                                            <?php
                                            /**
                                             * Allow plugins to add video button or other actions
                                             *
                                             * @param array $tutorial Tutorial data (must be sanitized before output)
                                             */
                                            do_action( 'minimalio_tutorial_actions', $tutorial );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="minimalio-tutorials-footer minimalio-admin-info">
            <h3><?php _e( 'Need More Help?', 'minimalio' ); ?></h3>
            <p><?php _e( 'If you have purchased the Premium Plugin, you should have received the support email address. Please contact the support there. Thank you.', 'minimalio' ); ?></p>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Handle anchor links for direct category or tutorial navigation
        function handleAnchorNavigation() {
            var hash = window.location.hash;

            if (hash) {
                var target = $(hash);

                // If specific tutorial not found, try to find installation-related tutorial
                if (target.length === 0 && hash.includes('install')) {
                    $('.minimalio-tutorial-item').each(function() {
                        var tutorialId = $(this).attr('id');
                        var tutorialTitle = $(this).find('h3').text().toLowerCase();

                        if (tutorialId && (tutorialId.includes('install') || tutorialTitle.includes('install'))) {
                            target = $(this);
                            return false; // Break the loop
                        }
                    });
                }

                if (target.length) {
                    // Scroll to the target
                    $('html, body').animate({
                        scrollTop: target.offset().top - 50
                    }, 500);

                    // Check if it's a tutorial item or category
                    if (target.hasClass('minimalio-tutorial-item')) {
                        // Highlight the tutorial item
                        target.css({
                            'border': '3px solid #f86d5b',
                            'box-shadow': '0 0 20px rgba(248, 109, 91, 0.3)',
                            'transition': 'all 0.3s ease'
                        });

                        // Remove highlight after 4 seconds
                        setTimeout(function() {
                            target.css({
                                'border': '',
                                'box-shadow': ''
                            });
                        }, 4000);
                    } else if (target.hasClass('minimalio-tutorials-category')) {
                        // Highlight the category title
                        target.find('.minimalio-category-title').css({
                            'background-color': '#f86d5b',
                            'color': '#fff',
                            'padding': '10px',
                            'margin': '-10px -10px 10px -10px',
                            'transition': 'all 0.3s ease'
                        });

                        // Remove highlight after 3 seconds
                        setTimeout(function() {
                            target.find('.minimalio-category-title').css({
                                'background-color': '',
                                'color': '',
                                'padding': '',
                                'margin': ''
                            });
                        }, 3000);
                    }
                }
            }
        }

        // Handle on page load
        handleAnchorNavigation();

        // Handle when hash changes
        $(window).on('hashchange', handleAnchorNavigation);
    });
    </script>

    <?php
    /**
     * Allow plugins to add scripts for tutorials page
     *
     * @since 1.0.0
     */
    do_action( 'minimalio_tutorials_page_scripts' );
    ?>
    <?php
}