<?php
/**
 * Filter ajax
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function minimalio_filter_ajax() {
	// Verify nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'minimalio_ajax_nonce' ) ) {
		wp_die( __( 'Security check failed', 'minimalio' ), 403 );
	}

	// Sanitize and validate inputs
	$minimalio_category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'all';
	$card = isset( $_POST['card'] ) ? sanitize_file_name( $_POST['card'] ) : '';
	$grid = isset( $_POST['grid'] ) ? sanitize_text_field( $_POST['grid'] ) : '';
	$author = isset( $_POST['author'] ) ? sanitize_text_field( $_POST['author'] ) : '';
	$nr_columns = isset( $_POST['nr_columns'] ) ? absint( $_POST['nr_columns'] ) : 3;
	$minimalio_nr_posts = isset( $_POST['nr_posts'] ) ? ( $_POST['nr_posts'] == '-1' ? -1 : absint( $_POST['nr_posts'] ) ) : 6;
	$post_type = isset( $_POST['post_type'] ) ? sanitize_key( $_POST['post_type'] ) : 'post';

	// Validate post type
	if ( ! post_type_exists( $post_type ) ) {
		wp_die( __( 'Invalid post type', 'minimalio' ), 400 );
	}

	// Validate card template exists
	if ( ! empty( $card ) && ! locate_template( 'templates/snippets/post-cards/' . $card . '.php' ) ) {
		wp_die( __( 'Invalid card template', 'minimalio' ), 400 );
	}

	// Limit posts per page (but allow -1 for unlimited)
	$minimalio_nr_posts = $minimalio_nr_posts == -1 ? -1 : min( $minimalio_nr_posts, 50 );
	$nr_columns = min( max( $nr_columns, 1 ), 6 );

	$minimalio_button_label = get_theme_mod( 'minimalio_settings_post_cart_button_label' ) ?
	get_theme_mod( 'minimalio_settings_post_cart_button_label' ) : 'Read me';

	if ( $post_type === 'portfolio' ) {
		$minimalio_display = get_theme_mod( 'minimalio_settings_portfolio_pagination' );
		$enable_masonry    = get_theme_mod( 'minimalio_settings_portfolio_type', 'grid' );
	} else {
		$minimalio_display = get_theme_mod( 'minimalio_settings_blog_pagination' );
		$enable_masonry    = get_theme_mod( 'minimalio_settings_blog_type', 'grid' );
	}

	if ( $minimalio_category !== 'all' ) {
		if ( isset( $minimalio_category ) ) {
			$taxonomy = [
				[
					'taxonomy' => $post_type === 'portfolio' ? 'portfolio-categories' : 'category',
					'field'    => 'slug',
					'terms'    => $minimalio_category,
				],
			];
		}
		$term = get_term_by( 'slug', $minimalio_category, $post_type === 'portfolio' ? 'portfolio-categories' : 'category' );
		$count_posts = $term ? $term->count : 0;
	} else {
		$taxonomy    = '';
		$count_posts = wp_count_posts( $post_type )->publish;
	}

	$minimalio_args = [
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $minimalio_nr_posts,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => $taxonomy,
	];

	$minimalio_the_query = new WP_Query( $minimalio_args );

	if ( $minimalio_the_query->have_posts() ) : ?>
		<div class="grid grid-cols-1 sm:grid-cols-2 posts__row pswp__wrap <?php echo 'lg:grid-cols-' . esc_attr( $nr_columns ); ?>">
			<?php
			while ( $minimalio_the_query->have_posts() ) :
				$minimalio_the_query->the_post();
				global $post;
				?>

				<div class="post-item
				<?php
				if ( $enable_masonry === 'masonry' ) :
					?>
					post-item__masonry grid-item<?php endif; ?>">

					<?php
					minimalio_get_part(
						'templates/snippets/post-cards/' . $card,
						[
							'id'                     => $post->ID,
							'author_type'            => $author,
							'author'                 => $post->post_author,
							'link_url'               => get_the_permalink( $post->ID ),
							'card_image'             => get_post_thumbnail_id( $post->ID ),
							'image_size'             => 'large',
							'heading_type'           => 'h5',
							'card_title'             => get_the_title( $post->ID ),
							'card_excerpt'           => get_the_excerpt( $post->ID ),
							'card_content'           => get_the_content( $post->ID ),
							'card_category'          => $post_type === 'portfolio' ? get_the_terms( $post->ID, 'portfolio-categories' ) : get_the_category( $post->ID ),
							'card_tag'               => $post_type === 'portfolio' ? get_the_terms( $post->ID, 'portfolio-tags' ) : wp_get_post_tags( $post->ID ),
							'minimalio_button_label' => $minimalio_button_label,
							'minimalio_hover_image'            => get_post_meta( $post->ID, '_hover_image_id', true ),
							'minimalio_hover_video'            => get_post_meta( $post->ID, '_hover_video_id', true ),
							'vimeo_id'               => get_post_meta( $post->ID, '_vimeo_id', true ),
						]
					);
					?>
				</div>


			<?php endwhile; ?>

		</div>

		<?php if ( $minimalio_display !== 'no' && $minimalio_display !== 'load_scroll' ) : ?>

			<div class="w-full posts__button">
				<a class="wp-block-button__link wp-element-button posts__button-link
				<?php
				if ( get_theme_mod( 'minimalio_settings_portfolio_pagination' ) === 'load_scroll' ) {
					echo 'posts__button--load';
				}
				?>
				" id="load-more-ajax">
					<?php _e( 'Load More', 'minimalio' ); ?>
				</a>
			</div>
		<?php endif; ?>

		<?php
	endif;

	wp_reset_postdata();
	die();
}
add_action( 'wp_ajax_filter', 'minimalio_filter_ajax' );
add_action( 'wp_ajax_nopriv_filter', 'minimalio_filter_ajax' );

// Load more ajax
function minimalio_load_ajax() {
	// Verify nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'minimalio_ajax_nonce' ) ) {
		wp_die( __( 'Security check failed', 'minimalio' ), 403 );
	}

	// Sanitize and validate inputs
	$minimalio_category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'all';
	$card = isset( $_POST['card'] ) ? sanitize_file_name( $_POST['card'] ) : '';
	$grid = isset( $_POST['grid'] ) ? sanitize_text_field( $_POST['grid'] ) : '';
	$author = isset( $_POST['author'] ) ? sanitize_text_field( $_POST['author'] ) : '';
	$nr_columns = isset( $_POST['nr_columns'] ) ? absint( $_POST['nr_columns'] ) : 3;
	$minimalio_nr_posts = isset( $_POST['nr_posts'] ) ? ( $_POST['nr_posts'] == '-1' ? -1 : absint( $_POST['nr_posts'] ) ) : 6;
	$exclude = isset( $_POST['exclude'] ) ? sanitize_text_field( $_POST['exclude'] ) : '';
	$post_type = isset( $_POST['post_type'] ) ? sanitize_key( $_POST['post_type'] ) : 'post';

	// Validate post type
	if ( ! post_type_exists( $post_type ) ) {
		wp_die( __( 'Invalid post type', 'minimalio' ), 400 );
	}

	// Validate card template exists
	if ( ! empty( $card ) && ! locate_template( 'templates/snippets/post-cards/' . $card . '.php' ) ) {
		wp_die( __( 'Invalid card template', 'minimalio' ), 400 );
	}

	// Limit posts per page and validate columns (but allow -1 for unlimited)
	$minimalio_nr_posts = $minimalio_nr_posts == -1 ? -1 : min( $minimalio_nr_posts, 50 );
	$nr_columns = min( max( $nr_columns, 1 ), 6 );

	// Validate and sanitize exclude IDs
	$exclude_ids = array();
	if ( ! empty( $exclude ) ) {
		$exclude_array = explode( ',', $exclude );
		foreach ( $exclude_array as $id ) {
			$clean_id = absint( trim( $id ) );
			if ( $clean_id > 0 ) {
				$exclude_ids[] = $clean_id;
			}
		}
	}

	$minimalio_button_label = get_theme_mod( 'minimalio_settings_post_cart_button_label' ) ?
		get_theme_mod( 'minimalio_settings_post_cart_button_label' ) : 'Read me';

	if ( $post_type === 'portfolio' ) {
		$enable_masonry = get_theme_mod( 'minimalio_settings_portfolio_type', 'grid' );
	} else {
		$enable_masonry = get_theme_mod( 'minimalio_settings_blog_type', 'grid' );
	}

	// Removed HTTP_REFERER usage for security - using category parameter instead

	if ( $minimalio_category !== 'all' ) {
		if ( $post_type === 'portfolio' ) {
			if ( isset( $minimalio_category ) ) {
				$taxonomy = [
					[
						'taxonomy' => 'portfolio-categories',
						'field'    => 'slug',
						'terms'    => $minimalio_category,
					],
				];
			}
			$term              = get_term_by( 'slug', $minimalio_category, 'portfolio-categories' );
			$category_selected = '';
		} else {
			$taxonomy = '';
			$category_selected = $minimalio_category;
		}
	} else {
		$taxonomy          = '';
		$category_selected = '';
	}

	$minimalio_args = [
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $minimalio_nr_posts,
		'orderby'        => 'date',
		'post__not_in'   => $exclude_ids,
		'order'          => 'DESC',
		'category_name'  => $category_selected,
		'tax_query'      => $taxonomy,
	];

	$minimalio_the_query = new WP_Query( $minimalio_args );

	if ( $minimalio_the_query->have_posts() ) :
		?>
		<?php
		while ( $minimalio_the_query->have_posts() ) :
			$minimalio_the_query->the_post();
			global $post;
			?>

			<div class="post-item
			<?php
			if ( $enable_masonry === 'masonry' ) :
				?>
				post-item__masonry grid-item<?php endif; ?>">

				<?php
				minimalio_get_part(
					'templates/snippets/post-cards/' . $card,
					[
						'id'                     => $post->ID,
						'show_author'            => 'true',
						'author_type'            => $author,
						'author'                 => $post->post_author,
						'link_url'               => get_the_permalink( $post->ID ),
						'card_image'             => get_post_thumbnail_id( $post->ID ),
						'image_size'             => 'large',
						'heading_type'           => 'h5',
						'card_title'             => get_the_title( $post->ID ),
						'card_excerpt'           => get_the_excerpt( $post->ID ),
						'card_category'          => $post_type === 'portfolio' ? get_the_terms( $post->ID, 'portfolio-categories' ) : get_the_category( $post->ID ),
						'card_tag'               => $post_type === 'portfolio' ? get_the_terms( $post->ID, 'portfolio-tag' ) : wp_get_post_tags( $post->ID ),
						'card_content'           => get_the_content( $post->ID ),
						'button_classes'         => 'btn-primary btn-sm',
						'minimalio_button_label' => $minimalio_button_label,
						'minimalio_hover_image'            => get_post_meta( $post->ID, '_hover_image_id', true ),
						'minimalio_hover_video'            => get_post_meta( $post->ID, '_hover_video_id', true ),
						'vimeo_id'               => get_post_meta( $post->ID, '_vimeo_id', true ),
					]
				);
				?>
			</div>

		<?php endwhile; ?>

		<?php
	endif;

	wp_reset_postdata();
	die();
}
add_action( 'wp_ajax_load', 'minimalio_load_ajax' );
add_action( 'wp_ajax_nopriv_load', 'minimalio_load_ajax' );

/* Preget posts for pagination */
function minimalio_pagination_pre_get_posts( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}

	if ( function_exists('is_woocommerce') ) {
		if (
			is_shop()
			|| is_product_category()
			|| is_product_tag()
			|| is_product()
			|| is_cart()
			|| is_checkout()
			|| is_account_page()
		) {
			return;
		}
	}


	// Don't affect AJAX requests, archives, or any paginated requests
	if ( 
		wp_doing_ajax() 
		|| is_post_type_archive( 'portfolio' ) 
		|| is_home() 
		|| is_category() 
		|| is_tag() 
		|| is_author()
		|| is_date()
		|| is_archive()
		|| $q->is_paged()
		|| get_query_var('paged') > 1
	) {
		return;
	}

	$q->set( 'posts_per_page', 1 );
	$q->set( 'orderby', 'modified' );
}
add_action( 'pre_get_posts', 'minimalio_pagination_pre_get_posts' );