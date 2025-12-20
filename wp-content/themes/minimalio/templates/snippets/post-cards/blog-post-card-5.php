<?php defined( 'ABSPATH' ) || exit; ?>

<div class="post-card blog-post-card-5 relative w-full flex flex-col lg:flex-row text-left<?php echo ' ' . esc_attr( $additional_classes ); ?>"
	data-card-id="<?php echo esc_attr( $id ); ?>">
	<?php if ( $link_url ) : ?>
		<a class="absolute top-0 bottom-0 left-0 right-0 z-20 no-underline opacity-0 post-card__link"
			href="<?php echo esc_url( $link_url ); ?>">
			<?php echo esc_html( $card_title ); ?>
		</a>
	<?php endif; ?>

	<?php if ( $card_image ) : ?>
		<figure class="relative w-full h-auto mr-8 post-card__image lg:basis-1/2 ">
			<?php echo wp_get_attachment_image( $card_image, 'large' ); ?>

			<div
				class="absolute top-0 bottom-0 left-0 right-0 z-10 transition-opacity duration-300 bg-black opacity-0 post-card__overlay">
			</div>
		</figure>
	<?php endif; ?>

	<div class="flex flex-col justify-center overflow-auto post-card__body basis-1/2">
		<div class="mt-4 post-card__date lg:mt-0 lg:mb-6">
			<?php
			$minimalio_post_date = get_the_date();
			echo $minimalio_post_date;
			?>
		</div>

		<?php if ( $card_title ) : ?>
			<h1 class="py-4 m-0 break-words post-card__heading">
				<?php echo esc_html( $card_title ); ?>
			</h1>
		<?php endif; ?>


		<?php if ( $card_excerpt ) : ?>
			<div class="my-1 post-card__excerpt lg:my-4">
				<?php echo $card_excerpt; ?>
			</div>
		<?php endif; ?>

		<?php if ( $minimalio_button_label ) : ?>
			<div class="mt-4 post-card__button lg:mb-16">
				<a class="post-card__btn" href="<?php echo esc_url( $link_url ); ?>" aria-label="<?php echo esc_html( $card_title ); ?>">
					<?php echo $minimalio_button_label; ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
