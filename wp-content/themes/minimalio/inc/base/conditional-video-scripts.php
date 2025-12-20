<?php

/**
 * Conditional Video Scripts Loading for Minimalio Theme
 * 
 * Loads video scripts only when video blocks are detected in content
 * Supports: Vimeo Banner, Vimeo iFrame, YouTube Banner blocks
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Block Detection Class for Video Scripts
 */
class Minimalio_Video_Block_Detection {
    
    /**
     * Video blocks that require scripts
     */
    private static $video_blocks = [
        'minimalio-blocks/minimalio-vimeo-banner',
        'minimalio-blocks/minimalio-vimeo-iframe',
        'minimalio-blocks/minimalio-youtube-banner',
        'minimalio-blocks/minimalio-video-banner',
        'minimalio-blocks/vimeo-banner',
        'minimalio-blocks/vimeo-iframe', 
        'minimalio-blocks/youtube-banner',
        'minimalio-blocks/video-banner'
    ];
    
    /**
     * Cache key for detection results
     */
    private static $cache_key_prefix = 'minimalio_video_blocks_';
    
    /**
     * Cache duration (1 hour)
     */
    private static $cache_duration = HOUR_IN_SECONDS;
    
    /**
     * Main detection function
     *
     * @return array Array with 'youtube' and 'vimeo' boolean values
     */
    public static function detect_video_blocks() {
        $cache_key = self::get_cache_key();
        $cached_result = get_transient( $cache_key );
        
        if ( $cached_result !== false ) {
            return $cached_result;
        }
        
        $result = [
            'youtube' => false,
            'vimeo' => false
        ];
        
        // Check current context for video blocks
        if ( self::scan_for_video_blocks() ) {
            $content = self::get_all_content();
            $result = self::analyze_content_for_videos( $content );
        }
        
        // Cache the result
        set_transient( $cache_key, $result, self::$cache_duration );
        
        return $result;
    }
    
    /**
     * Generate cache key based on current context
     */
    private static function get_cache_key() {
        global $wp_query;
        
        $key_parts = [ self::$cache_key_prefix ];
        
        if ( is_singular('portfolio') ) {
            $key_parts[] = 'portfolio_' . get_the_ID();
        } elseif ( is_singular() ) {
            $key_parts[] = 'post_' . get_the_ID();
        } elseif ( is_post_type_archive('portfolio') ) {
            $key_parts[] = 'portfolio_archive';
        } elseif ( is_tax(['portfolio-categories', 'portfolio-tags']) ) {
            $key_parts[] = 'portfolio_tax_' . get_queried_object_id();
        } elseif ( is_home() ) {
            $key_parts[] = 'home';
        } elseif ( is_category() ) {
            $key_parts[] = 'category_' . get_queried_object_id();
        } elseif ( is_tag() ) {
            $key_parts[] = 'tag_' . get_queried_object_id();
        } elseif ( is_author() ) {
            $key_parts[] = 'author_' . get_queried_object_id();
        } elseif ( is_date() ) {
            $key_parts[] = 'date_' . $wp_query->get('year') . '_' . $wp_query->get('monthnum');
        } else {
            $key_parts[] = 'other_' . md5( serialize( $wp_query->query_vars ) );
        }
        
        return implode( '', $key_parts );
    }
    
    /**
     * Initial scan to check if any video blocks exist
     */
    private static function scan_for_video_blocks() {
        global $posts, $wp_query;
        
        // Check current post/page (singular) - includes Portfolio posts
        if ( is_singular() ) {
            $post = get_post();
            return self::post_has_video_blocks( $post );
        }
        
        // Check Portfolio-specific contexts
        if ( is_post_type_archive('portfolio') || is_tax(['portfolio-categories', 'portfolio-tags']) ) {
            if ( isset( $posts ) && is_array( $posts ) ) {
                foreach ( $posts as $post ) {
                    if ( self::post_has_video_blocks( $post ) ) {
                        return true;
                    }
                }
            }
        }
        
        // Check all posts in archive/index pages
        if ( is_home() || is_archive() || is_search() ) {
            if ( isset( $posts ) && is_array( $posts ) ) {
                foreach ( $posts as $post ) {
                    if ( self::post_has_video_blocks( $post ) ) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check if a post contains video blocks
     */
    private static function post_has_video_blocks( $post ) {
        if ( ! $post || ! isset( $post->post_content ) ) {
            return false;
        }
        
        // Check for registered video blocks
        foreach ( self::$video_blocks as $block_type ) {
            if ( has_block( $block_type, $post ) ) {
                return true;
            }
        }
        
        // Check for video embeds and shortcodes
        return self::has_video_embeds( $post->post_content );
    }
    
    /**
     * Get all content from current context
     */
    private static function get_all_content() {
        global $posts;
        $content = '';
        
        if ( is_singular() ) {
            $post = get_post();
            if ( $post ) {
                $content = $post->post_content;
            }
        } elseif ( is_post_type_archive('portfolio') || is_tax(['portfolio-categories', 'portfolio-tags']) ) {
            // Handle Portfolio archives and taxonomy pages
            if ( isset( $posts ) && is_array( $posts ) ) {
                foreach ( $posts as $post ) {
                    if ( isset( $post->post_content ) ) {
                        $content .= $post->post_content . "\n";
                    }
                }
            }
        } elseif ( isset( $posts ) && is_array( $posts ) ) {
            foreach ( $posts as $post ) {
                if ( isset( $post->post_content ) ) {
                    $content .= $post->post_content . "\n";
                }
            }
        }
        
        return $content;
    }
    
    /**
     * Analyze content to determine which video scripts are needed
     */
    private static function analyze_content_for_videos( $content ) {
        $result = [
            'youtube' => false,
            'vimeo' => false
        ];

        // Check for YouTube content - includes YouTube Banner blocks
        if ( self::has_youtube_content( $content ) || self::has_youtube_banner( $content ) ) {
            $result['youtube'] = true;
        }

        // Check for Vimeo content - includes Vimeo Banner and Vimeo iFrame
        if ( self::has_vimeo_content( $content ) ) {
            $result['vimeo'] = true;
        }

        return $result;
    }
    
    /**
     * Check for YouTube-related content
     */
    private static function has_youtube_content( $content ) {
        $youtube_patterns = [
            '/wp:minimalio-blocks\/minimalio-video-banner/'  // Video banner block
        ];

        foreach ( $youtube_patterns as $pattern ) {
            if ( preg_match( $pattern, $content ) ) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Check for Vimeo-related content
     */
    private static function has_vimeo_content( $content ) {
        $vimeo_patterns = [
            '/wp:minimalio-blocks\/.*vimeo/'
        ];
        
        foreach ( $vimeo_patterns as $pattern ) {
            if ( preg_match( $pattern, $content ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check for YouTube Banner blocks
     */
    private static function has_youtube_banner( $content ) {
        $youtube_banner_patterns = [
            '/wp:minimalio-blocks\/.*youtube-banner/',
            '/wp:minimalio-blocks\/minimalio-youtube-banner/'
        ];
        
        foreach ( $youtube_banner_patterns as $pattern ) {
            if ( preg_match( $pattern, $content ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check for video embeds and shortcodes
     */
    private static function has_video_embeds( $content ) {
        $video_patterns = [
            '/wp:minimalio-blocks\/minimalio-video-banner/',  // Only specific video-banner block
            '/wp:minimalio-blocks\/.*vimeo/',
            '/wp:minimalio-blocks\/.*youtube/',
            '/wp:minimalio-blocks\/.*banner/'  // Any banner blocks (youtube-banner, etc.)
        ];
        
        foreach ( $video_patterns as $pattern ) {
            if ( preg_match( $pattern, $content ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Clear cache (called on post updates)
     */
    public static function clear_cache() {
        global $wpdb;
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_' . self::$cache_key_prefix . '%'
            )
        );
    }
}

/**
 * Conditional video scripts enqueue function
 */
function minimalio_conditional_video_scripts() {
    $video_detection = Minimalio_Video_Block_Detection::detect_video_blocks();
    
    $directory = get_template_directory_uri();
    $ver = MINIMALIO_VERSION;
    
    // Load YouTube script if YouTube content detected
    if ( $video_detection['youtube'] ) {
        wp_enqueue_script( 
            'minimalio_youtube', 
            $directory . '/assets/dist/js/minimalio-youtube.min.js', 
            array( 'minimalio_theme' ), 
            $ver, 
            true 
        );
    }
    
    // Load Vimeo script if Vimeo content or YouTube banner detected
    if ( $video_detection['vimeo'] ) {
        wp_enqueue_script( 
            'minimalio_vimeo', 
            $directory . '/assets/dist/js/minimalio-vimeo.min.js', 
            array( 'minimalio_theme' ), 
            $ver, 
            true 
        );
    }
}

// Hook the conditional loading function
add_action( 'wp_enqueue_scripts', 'minimalio_conditional_video_scripts', 15 );

// Clear cache on post updates
add_action( 'save_post', [ 'Minimalio_Video_Block_Detection', 'clear_cache' ] );
add_action( 'wp_update_nav_menu', [ 'Minimalio_Video_Block_Detection', 'clear_cache' ] );

// Clear cache immediately to apply new detection patterns
Minimalio_Video_Block_Detection::clear_cache();