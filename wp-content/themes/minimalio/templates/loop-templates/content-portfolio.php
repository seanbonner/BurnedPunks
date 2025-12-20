<?php
/**
 * Single post partial template.
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

global $post;

// Check if the post has portfolio-categories taxonomy
$minimalio_all_taxonomy = get_post_taxonomies( $post->ID );

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<header class="entry-header">
		<?php if ( get_theme_mod( 'minimalio_settings_single_portfolio_image' ) === 'yes' ) : ?>
			<div class="mb-8 overflow-hidden single-post__thumbnail">
				<?php echo get_the_post_thumbnail( $post->ID, 'full', [ 'class' => 'w-full h-full' ] ); ?>
			</div>
		<?php endif; ?>

		<?php $titleAlign = get_theme_mod( 'minimalio_settings_single_portfolio_title_align', 'justify-center' ) ?>
		<?php if ( get_theme_mod( 'minimalio_settings_single_portfolio_title' ) === 'yes' ) : ?>
				<?php $titleSize = get_theme_mod( 'minimalio_settings_single_portfolio_title_size', 'h2' ) ?>

				<div class="single-post__title">
					<?php the_title( "<h1 class=\"entry-title flex pb-8 mb-0 break-words $titleSize $titleAlign\">", "</h1>" ); ?>
				</div>
		<?php endif; ?>

		<?php if ( get_theme_mod( 'minimalio_settings_single_portfolio_metadata' ) === 'yes' || get_theme_mod( 'minimalio_settings_single_portfolio_share' ) === 'yes' ) : ?>
			<div class="mb-8 single-post__entry-meta entry-meta">

				<?php // minimalio_posted_on(); ?>
				<div class="flex-col row lg:flex-row <?php echo esc_html($titleAlign ) ?>">
					<?php if ( get_theme_mod( 'minimalio_settings_single_portfolio_metadata' ) === 'yes' ) : ?>
						<div class="flex flex-col flex-wrap justify-center text-center single-post__details-left lg:flex-row lg:text-left">
							<span class="flex mb-2 lg:mb-0 lg:mr-2">
								<?php
								_e( 'By:&nbsp;', 'minimalio' );
								the_author_posts_link();
								?>
							</span><span class="hidden mb-2 lg:block lg:mb-0 lg:mr-2">|</span>
							<span class="flex mb-2 lg:mb-0 lg:mr-2">
								<?php the_date(); ?>
							</span>

							<?php
							if ( $minimalio_all_taxonomy ) {
								foreach ( $minimalio_all_taxonomy as $key => $value ) {
									if ( get_the_term_list( $post->ID, $value ) !== false ) {
										$name = get_taxonomy( $value )->label;
										?>
										<span class="hidden mb-2 lg:block lg:mb-0 lg:mr-2">|</span>
										<span class="flex flex-wrap mb-2 lg:mb-0 lg:mr-2">
											<?php
											printf( '%s', $name );
											echo ':&nbsp;' . get_the_term_list( $post->ID, $value, '', ',&nbsp;' );
											?>
										</span>
										<?php
									}
								}
							}
							?>
						</div>
				<?php endif; ?>

					<?php if ( get_theme_mod( 'minimalio_settings_single_portfolio_share' ) === 'yes' ) : ?>
						<div class="flex flex-wrap justify-center single-post__details-right">
							<div class="single-post__details-right--extra"> <?php _e( 'Share: ', 'minimalio' ); ?></div>
							<div class="flex single-post__details-right--share">
								<span>
									<a href="http://www.facebook.com/sharer.php?u=<?php echo esc_url( get_permalink() ); ?>" target="_blank">
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
									<a href="https://twitter.com/share?url=<?php echo esc_url( get_permalink() ); ?> " target="_blank">
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
									<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo esc_url( get_permalink() ); ?>" target="_blank">
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
			wp_link_pages(
				[
					'before' => '<div class="page-links">' . __( 'Pages:', 'minimalio' ),
					'after'  => '</div>',
				]
			);
			?>
		</div><!-- .entry-content -->

		<?php if ( get_theme_mod( 'minimalio_settings_single_portfolio_latest' ) === 'yes' ) : ?>
			<div class="mt-12 latest-posts">
				<?php minimalio_latest_portfolio_posts(); ?>
			</div>
		<?php endif; ?>

</article>
