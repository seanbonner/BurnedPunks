<?php defined( 'ABSPATH' ) || exit; ?>

<div class="relative flex flex-col w-full text-left post-card blog-post-card-2" data-card-id="<?php echo esc_attr( $id ); ?>">
	<?php if ( $link_url ) : ?>
		<a class="absolute top-0 bottom-0 left-0 right-0 z-20 no-underline opacity-0 post-card__link" href="<?php echo esc_url( $link_url ); ?>">
			<?php echo esc_html( $card_title ); ?>
		</a>
	<?php endif; ?>

	<?php if ( $card_image ) : ?>
		<figure class="relative w-full h-full post-card__image">
			<?php echo wp_get_attachment_image( $card_image, 'large' ); ?>

			<div class="absolute top-0 bottom-0 left-0 right-0 z-10 transition-opacity duration-300 bg-black bg-white opacity-0 post-card__overlay"></div>
		</figure>
	<?php endif; ?>

	<div class="absolute top-0 z-10 flex flex-col justify-center w-full h-full p-2 m-0 overflow-hidden text-center transition-opacity duration-300 opacity-0 post-card__body md:p-4">

		<?php if ( $card_title ) : ?>
			<h1 class="break-words post-card__heading">
				<?php echo esc_html( $card_title ); ?>
			</h1>
		<?php endif; ?>


		<?php if ( is_array( $card_category ) && ! empty( $card_category ) ) : ?>
			<div class="pb-4 post-card__categories">
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
