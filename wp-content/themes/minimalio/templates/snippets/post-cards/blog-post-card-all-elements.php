<?php defined( 'ABSPATH' ) || exit; ?>

<div class="relative flex flex-col w-full text-left post-card post-card-all" data-card-id="<?php echo esc_attr( $id ); ?>">
	<?php if ( $link_url ) : ?>
	<a class="absolute top-0 bottom-0 left-0 right-0 z-20 no-underline opacity-0 post-card__link" href="<?php echo esc_url( $link_url ); ?>">
		<?php echo esc_html( $card_title ); ?>
	</a>
	<?php endif; ?>

	<?php if ( $card_image ) : ?>
	<figure class="relative h-auto post-card__image">
		<?php echo wp_get_attachment_image( $card_image, 'large' ); ?>

		<div class="absolute top-0 bottom-0 left-0 right-0 z-10 transition-opacity duration-300 bg-black opacity-0 post-card__overlay"></div>
	</figure>
	<?php endif; ?>

	<?php if ( $card_title ) : ?>
		<h1 class="py-4 m-0 break-words post-card__heading">
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

	<?php if ( $card_excerpt ) : ?>
	<div class="pb-4 post-card__excerpt">
		<?php echo $card_excerpt; ?>
	</div>
	<?php endif; ?>

	<?php if ( $minimalio_button_label ) : ?>
	<div class="pb-4 post-card__button">
		<a class="post-card__btn" href="<?php echo esc_url( $link_url ); ?>" aria-label="<?php echo esc_html( $card_title ); ?>">
		<?php echo $minimalio_button_label; ?>
		</a>
	</div>
	<?php endif; ?>

	<div class="pb-4 post-card__date">
	<?php
	$minimalio_post_date = get_the_date();
	echo $minimalio_post_date;
	?>
	</div>

	<?php if ( is_array( $card_tag ) && ! empty( $card_tag ) ) : ?>
	<div class="pb-4 post-card__tags">
		<?php
		$minimalio_i = 0;
		foreach ( $card_tag as $minimalio_tag ) :
			++$minimalio_i;
			?>
			<?php
			echo $minimalio_tag->name;
			if ( $minimalio_i < count( $card_tag ) ) {
				echo ', ';
			}
			?>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>


	<div class="pb-4 post-card__author">
		<?php if ( $author !== 'false' ) : ?>
			<?php
			minimalio_get_part(
				'templates/snippets/authors/' . $author_type,
				[
					'author' => $author,
					'colour' => 'white',

				]
			);
			?>
		<?php endif; ?>
	</div>

</div>
