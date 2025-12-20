<?php

/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
if (! function_exists('minimalio_posted_on')) {
	function minimalio_posted_on()
	{
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if (get_the_time('U') !== get_the_modified_time('U')) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s"> (%4$s) </time>';
		}
		$time_string = sprintf(
			$time_string,
			esc_attr(get_the_date('c')),
			esc_html(get_the_date()),
			esc_attr(get_the_modified_date('c')),
			esc_html(get_the_modified_date())
		);
		$posted_on   = apply_filters(
			'minimalio_posted_on',
			sprintf(
				'<span class="posted-on">%1$s <a href="%2$s" rel="bookmark">%3$s</a></span>',
				esc_html_x('Posted on', 'post date', 'minimalio'),
				esc_url(get_permalink()),
				apply_filters('minimalio_posted_on_time', $time_string)
			)
		);
		$byline      = apply_filters(
			'minimalio_posted_by',
			sprintf(
				'<span class="byline"> %1$s<span class="author vcard"><a class="url fn n" href="%2$s"> %3$s</a></span></span>',
				$posted_on ? esc_html_x('by', 'post author', 'minimalio') : esc_html_x('Posted by', 'post author', 'minimalio'),
				esc_url(get_author_posts_url(get_the_author_meta('ID'))),
				esc_html(get_the_author())
			)
		);
		echo $posted_on . $byline; // WPCS: XSS OK.
	}
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
if (! function_exists('minimalio_entry_footer')) {
	function minimalio_entry_footer()
	{

		if ('post' === get_post_type()) {
			echo '<div class="w-full author-info-single row">';
			echo '<div class="mr-8 author-image">';
			printf(get_avatar(get_the_author_meta('ID'), 'full'));
			echo '</div>';

			echo '<div class="author-info">';
			printf('<h5 class="author-name">' . get_the_author_posts_link() . '</h5>');
			printf('<p class="date">' . get_the_date() . '</p>');
			printf('<p class="author-name">' . get_the_author_meta('description') . '</p>');
			echo '</div></div>';
		}
	}
}

/**
 * Latest posts at the bottom of single post
 */
if (! function_exists('minimalio_latest_posts')) {
	function minimalio_latest_posts()
	{
		if ('post' === get_post_type()) {
			$id        = get_the_ID();
			$card      = get_theme_mod('minimalio_settings_blog_post_card');
			$post_card = minimalio_post_postcard($card);
			if (get_theme_mod('minimalio_settings_single_post_latest_posts_title') !== 'no') {
				$titleSize = get_theme_mod('minimalio_settings_single_post_latest_posts_title_size');
				$titleAlign = get_theme_mod('minimalio_settings_single_post_latest_posts_title_align');
				$label = get_theme_mod('minimalio_settings_single_post_latest_posts_title_label');

				if (!$label || !trim($label)) {
					$label = __('Latest', 'minimalio');
				}

				printf("<h3 class=\"latest-posts-title p-0 mb-8 $titleSize $titleAlign\">" . esc_html__($label, 'minimalio')  . "</h3>");
			}
			minimalio_get_part(
				'templates/blocks/posts-ajax/posts-ajax',
				[
					'nr_post'           => get_theme_mod('minimalio_settings_single_post_latest_posts_number', 4),
					'nr_columns'        => get_theme_mod('minimalio_settings_blog_columns', 4),
					'post_type'         => 'post',
					'pagination_option' => 'pagination',
					'post_card'         => $post_card,
					'author_type'       => 'author-first',
					'categories'        => get_categories(['hide_empty' => true]),
					'enable_masonry'    => 'grid',
					'filter'            => 'no',
					'exclude_post'      => $id,
				]
			);
		}
	}
}

/**
 * Latest portfolio posts at the bottom of single post
 */
if (! function_exists('minimalio_latest_portfolio_posts')) {
	function minimalio_latest_portfolio_posts()
	{
		if ('portfolio' === get_post_type()) {
			$id        = get_the_ID();
			$card      = get_theme_mod('minimalio_settings_portfolio_style');
			$post_card = minimalio_portfolio_postcard($card);
			if (get_theme_mod('minimalio_settings_single_portfolio_latest_portfolio_title') === 'yes') {
				$titleSize = get_theme_mod('minimalio_settings_single_portfolio_latest_portfolio_title_size');
				$titleAlign = get_theme_mod('minimalio_settings_single_portfolio_latest_portfolio_title_align');
				$label = get_theme_mod('minimalio_settings_single_portfolio_latest_portfolio_title_label');
				
				if (!$label || !trim($label)) {
					$label = __('Latest', 'minimalio');
				}

				printf("<h3 class=\"latest-posts-title p-0 mb-8 $titleSize $titleAlign\">" . esc_html__($label, 'minimalio')  . "</h3>");
			};

			minimalio_get_part(
				'templates/blocks/posts-ajax/posts-ajax',
				[
					'nr_post'           => get_theme_mod('minimalio_settings_single_portfolio_latest_portfolio_number', 4),
					'nr_columns'        => get_theme_mod('minimalio_settings_portfolio_columns', 4),
					'pagination_option' => 'pagination',
					'post_type'         => 'portfolio',
					'post_card'         => $post_card,
					'author_type'       => 'author-first',
					'categories'        => get_categories(['hide_empty' => true]),
					'enable_masonry'    => 'no',
					'filter'            => 'no',
					'exclude_post'      => $id,
				]
			);
		}
	}
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
if (! function_exists('minimalio_categorized_blog')) {
	function minimalio_categorized_blog()
	{
		if (false === ($all_the_cool_cats = get_transient('minimalio_categories'))) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories([
				'fields'     => 'ids',
				'hide_empty' => 1,
				// We only need to know if there is more than one category.
				'number'     => 2,
			]);
			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count($all_the_cool_cats);
			set_transient('minimalio_categories', $all_the_cool_cats);
		}
		if ($all_the_cool_cats > 1) {
			// This blog has more than 1 category so components_categorized_blog should return true.
			return true;
		} else {
			// This blog has only 1 category so components_categorized_blog should return false.
			return false;
		}
	}
}

/**
 * Flush out the transients used in minimalio_categorized_blog.
 */
add_action('edit_category', 'minimalio_category_transient_flusher');
add_action('save_post', 'minimalio_category_transient_flusher');

if (! function_exists('minimalio_category_transient_flusher')) {
	function minimalio_category_transient_flusher()
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		// Like, beat it. Dig?
		delete_transient('minimalio_categories');
	}
}


if (! function_exists('minimalio_post_nav')) {
	function minimalio_post_nav()
	{
		// Don't print empty markup if there's nowhere to navigate.
		$previous = (is_attachment()) ? get_post(get_post()->post_parent) : get_adjacent_post(false, '', true);
		$next     = get_adjacent_post(false, '', false);

		if (! $next && ! $previous) {
			return;
		}
?>
		<nav class="pb-8 navigation post-navigation">
			<h2 class="sr-only"><?php esc_html_e('Post navigation', 'minimalio'); ?></h2>
			<div class="justify-between w-full row nav__links">
				<div>
				<?php
				if (get_next_post_link()) {
					next_post_link('<span class="w-auto nav-next">%link</span>');
				}
				?>
				</div>
				<div>
				<?php
				if (get_previous_post_link()) {
					previous_post_link('<span class="w-auto nav-previous">%link</span>');
				}
				?>
				</div>
			</div><!-- .nav__links -->
		</nav><!-- .navigation -->
<?php
	}
}
