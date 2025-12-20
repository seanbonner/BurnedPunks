<?php defined( 'ABSPATH' ) || exit; ?>

<?php $minimalio_hover = get_theme_mod( 'minimalio_settings_portfolio_hover_option' ); ?>
<?php $minimalio_behaviour = get_theme_mod( 'minimalio_settings_portfolio_behaviour' ); ?>

<?php if ( $minimalio_behaviour === 'html_in_lightbox' ) : ?>

	<div class="<?php echo esc_attr( $minimalio_hover ); ?>"
		data-card-id="<?php echo esc_attr( $id ); ?>">
		<?php if ( $card_image ) : ?>
			<figure class="relative flex flex-col w-full text-left post-card post-card-3 photoswipe-item photoswipe-html" data-card-id="<?php echo esc_attr( $id ); ?>">
				<?php $image_data = wp_get_attachment_image_src( $card_image, 'full' ) ?: [ 0, 0, 0 ]; ?>
				<a class="relative w-full h-full post-card__image photoswipe-image" href="<?php echo esc_url( wp_get_attachment_url( $card_image ) ); ?>"
					data-width="<?php echo $image_data[1]; ?>"
					data-height="<?php echo $image_data[2]; ?>">
					<?php echo wp_get_attachment_image( $card_image, 'large' ); ?>
					<?php
					if ( isset( $minimalio_hover ) && ! empty( $minimalio_hover_image ) && $minimalio_hover === 'hover-image' ) {
						echo wp_get_attachment_image( $minimalio_hover_image, 'full', '', [ 'class' => 'hover-image' ] );}

					if ( isset( $minimalio_hover ) && ! empty( $minimalio_hover_video ) && $minimalio_hover === 'hover-video' ) {
						echo '<video class="portfolio-hover-video" loop muted preload="none"><source src="' . wp_get_attachment_url( $minimalio_hover_video ) . '" type="video/mp4"></video>';
					}
					?>
					<div class="absolute top-0 bottom-0 left-0 right-0 z-10 transition-opacity duration-300 bg-black opacity-0 post-card__overlay"></div>
					<div class="absolute top-0 z-10 flex flex-col justify-center w-full h-full p-2 m-0 text-center transition-opacity duration-300 opacity-0 post-card__body md:pt-4 md:pl-4 md:pr-4 md:pb-8">

						<?php if ( $card_title ) : ?>
							<<?php echo esc_attr( $heading_type ); ?> class="post-card__heading">
								<?php echo esc_html( $card_title ); ?>
							</<?php echo esc_attr( $heading_type ); ?>>
						<?php endif; ?>

						<?php if ( is_array( $card_category ) && ! empty( $card_category ) ) : ?>
							<div class="hidden post-card__categories md:block">
								<?php
								$minimalio_i = 0;
								foreach ( $card_category as $minimalio_category ) :
									++$minimalio_i;
									?>
									<?php
									echo esc_html( $minimalio_category->name );
									if ( $minimalio_i < count( $card_category ) ) {
										echo ', ';
									}
									?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</a>

				<?php if ( $card_content ) : ?>
					<div class="post-card__content hidden_content">
						<?php echo wp_kses_post( $card_content ); ?>
					</div>
				<?php endif; ?>

			</figure>

		<?php endif; ?>


	</div>

<?php elseif ( $minimalio_behaviour === 'video_in_lightbox' ) : ?>

	<div class="<?php echo esc_attr( $minimalio_hover ); ?>"
		data-card-id="<?php echo esc_attr( $id ); ?>">

		<?php if ( $card_image ) : ?>
			<figure class="relative flex flex-col w-full text-left post-card post-card-3 photoswipe-item photoswipe-vimeo" data-card-id="<?php echo esc_attr( $id ); ?>">
				<?php $image_data = wp_get_attachment_image_src( $card_image, 'full' ) ?: [ 0, 0, 0 ]; ?>
				<a class="relative w-full h-full post-card__image photoswipe-image" href="<?php echo esc_url( wp_get_attachment_url( $card_image ) ); ?>"
					data-width="<?php echo $image_data[1]; ?>"
					data-height="<?php echo $image_data[2]; ?>">
					<?php echo wp_get_attachment_image( $card_image, 'large' ); ?>
					<?php
					if ( isset( $minimalio_hover ) && ! empty( $minimalio_hover_image ) && $minimalio_hover === 'hover-image' ) {
						echo wp_get_attachment_image( $minimalio_hover_image, 'full', '', [ 'class' => 'hover-image' ] );}

					if ( isset( $minimalio_hover ) && ! empty( $minimalio_hover_video ) && $minimalio_hover === 'hover-video' ) {
						echo '<video class="portfolio-hover-video" loop muted preload="none"><source src="' . wp_get_attachment_url( $minimalio_hover_video ) . '" type="video/mp4"></video>';
					}
					?>

					<div class="absolute top-0 bottom-0 left-0 right-0 z-10 transition-opacity duration-300 bg-black opacity-0 post-card__overlay"></div>

					<div class="absolute top-0 z-10 flex flex-col justify-center w-full h-full p-2 m-0 text-center transition-opacity duration-300 opacity-0 post-card__body md:pt-4 md:pl-4 md:pr-4 md:pb-8">
						<?php if ( $card_title ) : ?>
							<<?php echo esc_attr( $heading_type ); ?> class="post-card__heading">
								<?php echo esc_html( $card_title ); ?>
							</<?php echo esc_attr( $heading_type ); ?>>
						<?php endif; ?>

						<?php if ( is_array( $card_category ) && ! empty( $card_category ) ) : ?>
							<div class="hidden post-card__categories md:block">
								<?php
								$minimalio_i = 0;
								foreach ( $card_category as $minimalio_category ) :
									++$minimalio_i;
									?>
									<?php
									echo esc_html( $minimalio_category->name );
									if ( $minimalio_i < count( $card_category ) ) {
										echo ', ';
									}
									?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

					</div>
				</a>


				<?php if ( $vimeo_id ) : ?>
					<div class="post-card__vimeo hidden_vimeo" data-vimeo="<?php echo esc_attr( $vimeo_id ); ?>">
						<?php echo esc_html( $vimeo_id ); ?>
					</div>
				<?php endif; ?>


			</figure>


		<?php endif; ?>

	</div>

<?php else : ?>

	<div class="post-card post-card-3 relative w-full flex flex-col text-left <?php echo esc_attr( $minimalio_hover ); ?>"
		data-card-id="<?php echo esc_attr( $id ); ?>">
		<?php if ( $link_url ) : ?>
			<a class="absolute top-0 bottom-0 left-0 right-0 z-20 no-underline opacity-0 post-card__link" href="<?php echo esc_url( $link_url ); ?>">
				<?php echo esc_html( $card_title ); ?>
			</a>
		<?php endif; ?>

		<?php if ( $card_image ) : ?>
			<figure class="relative w-full h-full post-card__image">
				<?php echo wp_get_attachment_image( $card_image, 'large' ); ?>

				<?php
				if ( isset( $minimalio_hover ) && ! empty( $minimalio_hover_image ) && $minimalio_hover === 'hover-image' ) {
					echo wp_get_attachment_image( $minimalio_hover_image, 'full', '', [ 'class' => 'hover-image' ] );}

				if ( isset( $minimalio_hover ) && ! empty( $minimalio_hover_video ) && $minimalio_hover === 'hover-video' ) {
					echo '<video class="portfolio-hover-video" loop muted preload="none"><source src="' . wp_get_attachment_url( $minimalio_hover_video ) . '" type="video/mp4"></video>';
				}
				?>

				<div class="absolute top-0 bottom-0 left-0 right-0 z-10 transition-opacity duration-300 bg-black bg-white opacity-0 post-card__overlay"></div>
			</figure>
		<?php endif; ?>

		<div class="absolute top-0 z-10 flex flex-col justify-center w-full h-full p-2 m-0 text-center transition-opacity duration-300 opacity-0 post-card__body md:p-4">

			<?php if ( $card_title ) : ?>
				<<?php echo esc_attr( $heading_type ); ?> class="post-card__heading">
					<?php echo esc_html( $card_title ); ?>
				</<?php echo esc_attr( $heading_type ); ?>>
			<?php endif; ?>


			<?php if ( is_array( $card_category ) && ! empty( $card_category ) ) : ?>
				<div class="hidden post-card__categories md:block">
					<?php
					$minimalio_i = 0;
					foreach ( $card_category as $minimalio_category ) :
						++$minimalio_i;
						?>
						<?php
						echo esc_html( $minimalio_category->name );
						if ( $minimalio_i < count( $card_category ) ) {
							echo ', ';
						}
						?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>

	</div>

<?php endif; ?>
