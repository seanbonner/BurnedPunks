<?php defined( 'ABSPATH' ) || exit; ?>

<div class="post-card post-card-1<?php echo esc_attr( ' ' . $additional_classes ); ?>" data-card-id="<?php echo esc_attr( $id ); ?>">

	<div class="post-card__body post-card-1__body">

		<?php if ( $card_content ) : ?>
			<div class="post-card__card_content post-card-1__card_content">
				<?php echo wp_kses_post( $card_content ); ?>
			</div>
		<?php endif; ?>

	</div>

</div>
