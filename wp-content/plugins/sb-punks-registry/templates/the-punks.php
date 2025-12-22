<?php
/**
 * Template override for /the-punks/
 */
if (!defined('ABSPATH')) exit;

get_header();

echo '<main id="primary" class="site-main">';
echo do_shortcode('[sb_punks_index]');
echo '</main>';

get_footer();
