<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $author ) : ?>
	<div class="author author-first">

		<?php if ( $author_image = get_avatar( get_the_author_meta( 'email', $author ), 100 ) ) : ?>
			<figure class="block float-left h-auto rounded-full author__image author-first__image max-w-8">
				<?php echo $author_image; ?>
			</figure>
		<?php endif; ?>

		<?php if ( $author_name = get_the_author_meta( 'display_name', $author ) ) : ?>
			<p class="float-left ml-2 leading-8 author__name">
				<?php echo $author_name; ?>
			</p>
		<?php endif; ?>

	</div>
<?php endif; ?>
