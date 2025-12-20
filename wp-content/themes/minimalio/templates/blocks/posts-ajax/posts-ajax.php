<?php
/**
 * The template for displaying posts.
 *
 * @package minimalio
 */

/* Exit if accessed directly. */
defined( 'ABSPATH' ) || exit;

/*Exclude post from query. Used to exclude current post from latest list*/
$minimalio_post_not_in = isset( $exclude_post ) ? $exclude_post : 0;


/* Dynamic title for the archive page*/
$minimalio_dynamic_title = '';

/* Make the global query and post objects available */
if ( ! isset( $all_label ) || ! $all_label ) {
	$minimalio_label = __('All', 'minimalio');
} else {
	$minimalio_label = $all_label;
}

/* Filter decoration inherited from the header options */

$minimalio_decoration = get_theme_mod( 'minimalio_settings_link_decoration' );
if ( $minimalio_decoration === 'underline' ) {
	$minimalio_class = ' underlined';
} elseif ( $minimalio_decoration === 'line-through' ) {
	$minimalio_class = 'line-through ';
} else {
	$minimalio_class = '';
}

/** Get the number of all published posts */
$minimalio_all_posts = wp_count_posts( $post_type )->publish;

/* Check if category is searched */
if ( ! isset( $_GET['category'] ) ) {
	$minimalio_cat = '';
} else {
	$minimalio_cat_string = $_GET['category'];
	$minimalio_cat        = preg_replace( '/[^\p{L}\p{N}\s]/u', '', $minimalio_cat_string );
}

if ( $post_type === 'portfolio' ) {
	$minimalio_portfolio_class = 'portfolio-post-type';
	$minimalio_aspect_ratio    = get_theme_mod( 'minimalio_settings_post_card_image_aspect_ratio' );
	$minimalio_gap             = get_theme_mod( 'minimalio_settings_portfolio_gap' );
	$minimalio_posts_nr        = get_theme_mod( 'minimalio_settings_portfolio_posts_per_page' );
} else {
	$minimalio_portfolio_class = 'blog-post-type';
	$minimalio_aspect_ratio    = get_theme_mod( 'minimalio_settings_blog_post_card_image_aspect_ratio' );
	$minimalio_gap             = get_theme_mod( 'minimalio_settings_blog_gap' );
	$minimalio_posts_nr        = get_theme_mod( 'minimalio_settings_blog_posts_per_page' );
}

if ($nr_post) {
    $minimalio_posts_nr = $nr_post;
}

if ( $pagination_option === 'no' ) {
	$minimalio_posts_page = '-1';
} else {
	$minimalio_posts_page = $minimalio_posts_nr;
}

/* Check if category is defined to supress an "Undefined category" error on the archive list. */
$minimalio_check_cat    = isset( $minimalio_category ) ? $minimalio_category : 'all';
$minimalio_check_tag    = isset( $minimalio_tag ) ? $minimalio_tag : 'all';
$minimalio_check_author = isset( $author ) ? $author : 'all';
/* Check if custom taxonomy is set */
$minimalio_check_taxonomy = isset( $taxonomy ) && ( $taxonomy !== 'no_taxonomy' || $taxonomy_id !== -1 ) ? $taxonomy : 'all';

/* End check */
if ( $minimalio_check_tag !== 'all' ) {
	$minimalio_args = [
		'tag_id'         => $minimalio_check_tag,
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $minimalio_posts_page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	];
} elseif ( $minimalio_check_author !== 'all' ) {
	$minimalio_args = [
		'author'         => $minimalio_check_author,
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $minimalio_posts_page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	];
} elseif ( $minimalio_check_taxonomy !== 'all' ) {
	$minimalio_args = [
		'post_type' => 'portfolio',
		'tax_query' => [
			[
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => $taxonomy_id,
			],
		],
	];
} else {
	$minimalio_args = [
		'cat'            => $minimalio_check_cat,
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $minimalio_posts_page,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post__not_in'   => [ $minimalio_post_not_in ],
	];
}

if ( get_theme_mod( 'minimalio_settings_post_cart_button_label' ) ) {
	$minimalio_button_label = get_theme_mod( 'minimalio_settings_post_cart_button_label' );
} else {
	$minimalio_button_label = 'Read More';
}

/* Posts query */
$minimalio_the_query = new WP_Query( $minimalio_args );
/*
Get the queried object (category, tag, author) */
/* so that we can build a dynamic page title */
$minimalio_queried_object = get_queried_object();

if ( isset( $minimalio_queried_object->user_nicename ) ) {
	$minimalio_dynamic_title = 'Posts by ' . ucfirst( $minimalio_queried_object->user_nicename );
} elseif ( isset( $minimalio_queried_object->label ) ) {
	$minimalio_dynamic_title = ucfirst( $minimalio_queried_object->label );
} else {
	$minimalio_dynamic_title = '';}
?>

<?php if ( ! empty( $minimalio_dynamic_title ) ) { ?>
    <?php $titleSize = get_theme_mod( 'minimalio_settings_page_title_size', 'h2' ); ?>
    <?php $titleAlign = get_theme_mod( 'minimalio_settings_page_title_align' ); ?>
    <h1 class="entry-title pb-8 mb-0 break-words <?php echo esc_html($titleSize) ?> <?php echo esc_html($titleAlign) ?>">  <?php echo $minimalio_dynamic_title; ?></h1>
<?php } ?>

<div class="posts-ajax overflow-hidden <?php echo $enable_masonry; ?> <?php echo $pagination_option === 'load_scroll' ? 'posts__posts-infinite' : null; ?> w-full pb-12 lg:pb-16" data-ajax="true">


		<?php if ( $filter !== 'no' ) : ?>

		<div class="pt-0 pb-8 m-0 posts-ajax__categories">

			<div class="flex items-center justify-between m-0 posts-ajax__row" data-filter>

					<div class="flex flex-wrap items-center justify-start w-auto posts-ajax__categories-wrapper gap-x-4 lg:gap-x-8 gap-y-4">

						<label class="posts-ajax__tab
						<?php
						if ( $minimalio_cat === '' ) :
							?>
checked <?php endif; ?> inline-block bg-transparent h-fit rounded-none" data-label="all" data-number="<?php echo $minimalio_all_posts; ?>" tabindex="0">
							<input type="radio" class="posts-ajax__radio
							<?php
							echo $enable_masonry;  if ( $minimalio_cat === '' ) :
								?>
checked <?php endif; ?> absolute top-0 right-0 bottom-0 left-0 invisible" name="category" value="all" tabindex="0"/>
							<span class="posts-ajax__tab-label <?php echo esc_attr( $minimalio_class ); ?> block">
								<?php echo $minimalio_label; ?>
							</span>

						</label>

						<?php

						if ( $categories ) :
							foreach ( $categories as $minimalio_category ) :
								/* args that will help us to count posts by taxonomy */
								$minimalio_args_count  = [
									'post_type'   => $post_type,
									'post_status' => 'publish',
									'numberposts' => -1,
									'tax_query'   => [
										[
											'taxonomy' => $post_type === 'portfolio' ? 'portfolio-categories' : 'category',
											'field'    => 'slug',
											'terms'    => $minimalio_category->slug,
										],
									],
								];
								$minimalio_total_posts = count( get_posts( $minimalio_args_count ) );
								?>

							<label class="posts-ajax__tab
								<?php
								if ( $minimalio_cat === $minimalio_category->slug ) :
									?>
checked <?php endif; ?> inline-block bg-transparent h-fit rounded-none" data-label="<?php echo esc_attr( $minimalio_category->slug ); ?>" data-number="<?php echo $minimalio_total_posts; ?>" tabindex="0">

								<input type="radio" class="posts-ajax__radio
								<?php
								echo $enable_masonry; if ( $minimalio_cat === $minimalio_category->slug ) :
									?>
checked <?php endif; ?> absolute top-0 right-0 bottom-0 left-0 invisible" name="category" value="<?php echo esc_attr( $minimalio_category->slug ); ?>" />
								<span class="posts-ajax__tab-label <?php echo esc_attr( $minimalio_class ); ?> block">
									<?php echo esc_html( $minimalio_category->name ); ?>
								</span>

							</label>

								<?php
						endforeach;
endif;
						?>

					</div>
			</div>

		</div>

	<?php endif; ?>

		<div class="posts-ajax__posts <?php echo $minimalio_portfolio_class ?> <?php
		if ( $minimalio_gap ) {
			echo $minimalio_gap;
		} else {
			echo esc_attr( 'gap_1' );}
		?>
		aspect-ratio-<?php echo $minimalio_aspect_ratio ?>" data-card="<?php echo $post_card; ?>" data-grid="<?php echo $enable_masonry; ?>" data-number="<?php echo $minimalio_all_posts; ?>" data-author="<?php echo $author_type; ?>" data-columns="<?php echo $nr_columns; ?>" data-posts_number="<?php echo $minimalio_posts_page; ?>">

			<div class="posts__container photoswipe-wrapper">

				<?php if ( $minimalio_the_query->have_posts() ) : ?>
					<div class="my-posts" data-category="all" data-type="<?php echo $post_type; ?>">

						<div class="grid grid-cols-1 sm:grid-cols-2 posts__row pswp__wrap <?php echo 'lg:grid-cols-' . $nr_columns . ''; ?>">
							<?php
							while ( $minimalio_the_query->have_posts() ) :
								$minimalio_the_query->the_post();
								global $post;
								?>

							<div class="post-item
								<?php
								if (

															$enable_masonry === 'masonry'

														) :
									?>
														post-item__masonry grid-item<?php endif; ?>">

								<?php
								minimalio_get_part( 'templates/snippets/post-cards/' . $post_card,
									[
										'id'            => $post->ID,
										'author_type'   => $author_type,
										'author'        => $post->post_author,
										'link_url'      => get_the_permalink( $post->ID ),
										'card_image'    => get_post_thumbnail_id( $post->ID ),
										'image_size'    => 'large',
										'heading_type'  => 'h5',
										'card_title'    => get_the_title( $post->ID ),
										'card_excerpt'  => get_the_excerpt( $post->ID ),
										'card_content'  => get_the_content( $post->ID ),
										'card_category' => $post_type === 'portfolio' ? get_the_terms( $post->ID, 'portfolio-categories' ) : get_the_category( $post->ID ),
										'card_tag'      => $post_type === 'portfolio' ? get_the_terms( $post->ID, 'portfolio-tags' ) : wp_get_post_tags( $post->ID ),
										'minimalio_button_label' => $minimalio_button_label,
										'minimalio_hover_image'   => get_post_meta( $post->ID, '_hover_image_id', true ),
										'minimalio_hover_video'   => get_post_meta( $post->ID, '_hover_video_id', true ),
										'vimeo_id'      => get_post_meta( $post->ID, '_vimeo_id', true ),
									]
								);
								?>
							</div>
							<?php endwhile; ?>

						</div>

						<?php if ( $pagination_option === 'load' ) : ?>

							<?php if ( $minimalio_all_posts > $minimalio_posts_page && $minimalio_posts_page !== '-1' ) : ?>
							<div class="posts__button col-12">
							<a class="wp-block-button__link wp-element-button posts__button-link" id="load-more-ajax">
								<?php _e( 'Load More', 'minimalio' ); ?>
							</a>
							</div>
						<?php endif; ?>
						<?php endif; ?>
					</div>


				<?php endif; ?>
				<?php wp_reset_postdata(); ?>



			</div>

		</div>

	</div>
