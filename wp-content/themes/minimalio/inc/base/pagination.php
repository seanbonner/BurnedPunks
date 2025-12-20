<?php
/**
 * Pagination layout.
 *
 * @package minimalio
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'minimalio_pagination' ) ) {
	function minimalio_pagination( $minimalio_args = [], $minimalio_class = 'pagination' ) {
		if ( $GLOBALS['wp_query']->max_num_pages <= 1 ) {
			return;
		}
		$minimalio_args = wp_parse_args(
			$minimalio_args,
			[
				'mid_size'           => 1,
				'prev_next'          => true,
				'prev_text'          => __( '&laquo;', 'minimalio' ),
				'next_text'          => __( '&raquo;', 'minimalio' ),
				'screen_reader_text' => __( 'Posts navigation', 'minimalio' ),
				'type'               => 'array',
				'current'            => max( 1, get_query_var( 'paged' ) ),
			]
		);
		$links          = paginate_links( $minimalio_args );
		?>
		<nav aria-label="<?php echo $minimalio_args['screen_reader_text']; ?>">
			<ul class="pagination">
				<?php
				foreach ( $links as $key => $link ) {
					?>
					<li class="page-item <?php echo strpos( $link, 'current' ) ? 'active' : ''; ?>">
						<?php echo str_replace( 'page-numbers', 'page-link', $link ); ?>
					</li>
					<?php
				}
				?>
			</ul>
		</nav>
		<?php
	}
}
?>
