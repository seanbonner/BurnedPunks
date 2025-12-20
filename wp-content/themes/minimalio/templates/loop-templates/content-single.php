<?php
/**
 * Single post partial template.
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();

get_header();

global $post;

// Check if the post has portfolio-categories taxonomy
$minimalio_all_taxonomy = get_post_taxonomies( $post->ID );
?>

<article
	<?php post_class(); ?>
	id="post-
	<?php the_ID(); ?>
	">
	<header class="entry-header">
		<?php
		if (
		get_theme_mod( 'minimalio_settings_single_post_thumbnail' ) === 'yes'
		) :
			?>
		<div class="mb-8 overflow-hidden single-post__thumbnail">
				<?php
				echo get_the_post_thumbnail($post->ID, 'full', [
					'class' => 'w-full h-full',
				]);
				?>
		</div>
		<?php endif; ?>

		<?php $titleAlign = get_theme_mod( 'minimalio_settings_single_post_title_align', 'justify-center' ) ?>
		<?php if ( get_theme_mod( 'minimalio_settings_single_post_title' ) === 'yes' ) : ?>
				<?php $titleSize = get_theme_mod( 'minimalio_settings_single_post_title_size', 'h2' ) ?>
                

				<div class="single-post__title">
					<?php the_title( "<h1 class=\"entry-title flex pb-8 mb-0 break-words $titleSize $titleAlign\">", "</h1>" ); ?>
				</div>

			
		<?php endif; ?>

		<?php
		if (
		get_theme_mod( 'minimalio_settings_single_post_meta' ) === 'yes' ||
		get_theme_mod( 'minimalio_settings_single_post_share' ) === 'yes'
		) :
			?>
			<div class="mb-8 single-post__entry-meta entry-meta">
				<div class="flex-col row lg:flex-row <?php echo esc_html($titleAlign ) ?>">
					<?php if ( get_theme_mod( 'minimalio_settings_single_post_meta' ) === 'yes' ) : ?>
						<div class="flex flex-col flex-wrap single-post__details-left lg:flex-row lg:text-left ">
							<span class="flex mb-2 lg:mb-0 lg:mr-2">
								<?php
								esc_html_e( 'By:&nbsp;', 'minimalio' );
								the_author_posts_link();
								?>
							</span><span class="hidden mb-2 lg:block lg:mb-0 lg:mr-2">|</span>
							<span class="flex flex-wrap mb-2 lg:mb-0 lg:mr-2">
								<?php the_date(); ?>
							</span>

							<?php
							if ( $minimalio_all_taxonomy ) {
								foreach ( $minimalio_all_taxonomy as $minimalio_key => $minimalio_value ) {
									if ( get_the_term_list( $post->ID, $minimalio_value ) !== false ) {
										$minimalio_name = get_taxonomy( $minimalio_value )->label;
										?>
										<span class="hidden mb-2 lg:block lg:mb-0 lg:mr-2">|</span>
										<span class="flex flex-wrap mb-2 lg:mb-0 lg:mr-2">
																<?php
																	printf( '%s', esc_html( $minimalio_name ) );
																	echo ':&nbsp;' . get_the_term_list( $post->ID, $minimalio_value, '', ',&nbsp;' );
																?>
										</span>
															<?php
									}
								}
							}
							?>
						</div>
					<?php endif; ?>

					<?php if ( get_theme_mod( 'minimalio_settings_single_post_share' ) === 'yes' ) : ?>
						<div class="flex flex-wrap single-post__details-right">
							<div class="single-post__details-right--extra">
								<?php _e( 'Share: ', 'minimalio' ); ?>
							</div>
							<div class="flex single-post__details-right--share">
								<span>
									<a href="http://www.facebook.com/sharer.php?u=
									<?php echo esc_url( get_permalink() ); ?>
									"
									target="_blank">
										<?php
										echo minimalio_get_svg('facebook', [
											'role'  => 'presentation',
											'title' => __( 'facebook', 'minimalio' ),
											'class' => 'single-post__facebook single-post__icons w-4 h-4',
										]);
										?>
									</a>
								</span>
								<span>
									<a href="https://twitter.com/share?url=
									<?php echo esc_url( get_permalink() ); ?>
									" target="_blank">
										<?php
										echo minimalio_get_svg('twitter', [
											'role'  => 'presentation',
											'title' => __( 'twitter', 'minimalio' ),
											'class' => 'single-post__twitter single-post__icons w-4 h-4',
										]);
										?>
									</a>
								</span>
								<span>
									<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=
									<?php echo esc_url( get_permalink() ); ?>
									"
										target="_blank">
										<?php
										echo minimalio_get_svg('linkedin', [
											'role'  => 'presentation',
											'title' => __( 'linkedin', 'minimalio' ),
											'class' => 'single-post__linkedin single-post__icons w-4 h-4',
										]);
										?>
									</a>
								</span>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>


		<?php
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
	endif;
		?>

		<?php
		wp_link_pages([
			'before' => '<div class="page-links">' . __( 'Pages:', 'minimalio' ),
			'after'  => '</div>',
		]);
		?>
	</div><!-- .entry-content -->

	<?php if ( get_theme_mod( 'minimalio_settings_single_post_author' ) === 'yes' ) : ?>
		<footer class="entry-footer flex w-full py-8 mb-8 border-t border-b border-solid border-[var(--preset--font-color)]">
			<?php minimalio_entry_footer(); ?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>

	<?php
	if (
	get_theme_mod( 'minimalio_settings_single_post_latest_posts' ) === 'yes'
	) :
		?>
		<div class="mt-12 latest-posts">
			<?php minimalio_latest_posts(); ?>
		</div>
	<?php endif; ?>

</article>
