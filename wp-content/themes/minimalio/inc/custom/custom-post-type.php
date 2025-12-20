<?php
/**
 * Custom post types
 */
defined( 'ABSPATH' ) || exit;

/**
 * Select Post card
 */
function minimalio_post_postcard( $card ) {
	if ( $card === 'style_1' ) {
		$post_card = 'blog-post-card-1';
	} elseif ( $card === 'style_2' ) {
		$post_card = 'blog-post-card-2';
	} elseif ( $card === 'style_3' ) {
		$post_card = 'blog-post-card-3';
	} elseif ( $card === 'style_4' ) {
		$post_card = 'blog-post-card-4';
	} elseif ( $card === 'style_5' ) {
		$post_card = 'blog-post-card-5';
	} elseif ( $card === 'all_elements' ) {
		$post_card = 'blog-post-card-all-elements';
	} else {
		$post_card = 'blog-post-card-1';
	}

	return $post_card;
}

/**
 * Select Portfolio card
 */
function minimalio_portfolio_postcard( $card ) {
	if ( $card === 'style_1' ) {
		$post_card = 'post-card-1';
	} elseif ( $card === 'style_2' ) {
		$post_card = 'post-card-2';
	} elseif ( $card === 'style_3' ) {
		$post_card = 'post-card-3';
	} elseif ( $card === 'style_4' ) {
		$post_card = 'post-card-4';
	} elseif ( $card === 'content' ) {
		$post_card = 'post-card-content';
	} elseif ( $card === 'all_elements' ) {
		$post_card = 'post-card-all-elements';
	} else {
		$post_card = 'post-card-1';
	}
	return $post_card;
}

class minimalio_ARGS_Build {


	/**
	 * Options will be merged into these, as opposed to using
	 * the standard WordPress defaults.
	 * @var array
	 */
	public $postDefaults = [
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => [ 'slug' => '' ],
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'supports'           => [ 'title', 'editor', 'author', 'excerpt', 'thumbnail', 'revisions' ],
	];

	/**
	 * Options will be merged into these, as opposed to using
	 * the standard WordPress defaults.
	 * @var array
	 */
	public $taxonomyDefaults = [
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => [ 'slug' => '' ],
	];

	/**
	 * Build the post types labels based solely on the capitalised
	 * singular and plural form.
	 * @param string $singular Singular & capitalised form for the post type, eg 'Post'
	 * @param string $plural   Plural & capitalised form for the post type, eg 'Posts'
	 */
	public function minimalio_buildPostLabels( $singular = 'Post', $plural = 'Posts' ) {
		if ( $singular !== 'Post' && $plural === 'Posts' ) {
			$plural = $singular . 's';
		}

		$minimalio_labels = [
			'name'               => sprintf(
				_x( '%s', 'post type general name', 'minimalio' ),
				ucfirst( $plural )
			),
			'singular_name'      => sprintf(
				_x( '%s', 'post type singular name', 'minimalio' ),
				ucfirst( $singular )
			),
			'menu_name'          => sprintf(
				_x( '%s', 'admin menu', 'minimalio' ),
				ucfirst( $plural )
			),
			'name_admin_bar'     => sprintf(
				_x( '%s', 'add new on admin bar', 'minimalio' ),
				ucfirst( $singular )
			),
			'add_new'            => sprintf(
				_x( 'Add New', '%s', 'minimalio' ),
				ucfirst( $singular )
			),
			'add_new_item'       => sprintf( __( 'Add New %s', 'minimalio' ), ucfirst( $singular ) ),
			'new_item'           => sprintf( __( 'New %s ', 'minimalio' ), ucfirst( $singular ) ),
			'edit_item'          => sprintf( __( 'Edit %s ', 'minimalio' ), ucfirst( $singular ) ),
			'view_item'          => sprintf( __( 'View %s ', 'minimalio' ), ucfirst( $singular ) ),
			'all_items'          => sprintf( __( 'All %s ', 'minimalio' ), ucfirst( $plural ) ),
			'search_items'       => sprintf( __( 'Search %s ', 'minimalio' ), ucfirst( $plural ) ),
			'parent_item_colon'  => sprintf(
				__( 'Parent %s: ', 'minimalio' ),
				ucfirst( $plural )
			),
			'not_found'          => sprintf( __( 'No %s found.', 'minimalio' ), strtolower( $plural ) ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'minimalio' ), strtolower( $plural ) ),
		];

		return $minimalio_labels;
	}

	/**
	 * Generate the complete arguments ready for post type creation,
	 * including the URL slug and merging of new defaults above.
	 * @param string $slug     The URL slug for the post type, eg 'posts'
	 * @param string $singular Singular & capitalised form for the post type, eg 'Post'
	 * @param string $plural   Plural & capitalised form for the post type, eg 'Posts'
	 * @param array  $minimalio_args     Additional arguments to override the defaults
	 */
	public function minimalio_buildPostArgs( $slug, $singular = 'Post', $plural = 'Posts', $minimalio_args = [] ) {
		$minimalio_args = wp_parse_args( $minimalio_args, $this->postDefaults );

		$minimalio_args['rewrite']['slug'] = $slug;

		$minimalio_args['labels'] = $this->minimalio_buildPostLabels( $singular, $plural );

		return $minimalio_args;
	}

	/**
	 * Build the taxonomies labels based solely on the capitalised
	 * singular and plural form.
	 * @param string $singular Singular & capitalised form for the taxonomy, eg 'Category'
	 * @param string $plural   Plural & capitalised form for the taxonomy, eg 'Categories'
	 */
	public function minimalio_buildTaxonomyLabels( $singular = 'Category', $plural = 'Categories' ) {
		if ( $singular !== 'Category' && $plural === 'Categories' ) {
			$plural = $singular . 's';
		}

		$minimalio_labels = [
			'name'                       => sprintf(
				_x( '%s', 'taxonomy general name', 'minimalio' ),
				$plural
			),
			'singular_name'              => sprintf(
				_x( '%s', 'taxonomy singular name', 'minimalio' ),
				$singular
			),
			'search_items'               => sprintf( __( 'Search %s ', 'minimalio' ), $plural ),
			'all_items'                  => sprintf( __( 'All %s ', 'minimalio' ), $plural ),
			'parent_item'                => sprintf( __( 'Parent %s ', 'minimalio' ), $singular ),
			'parent_item_colon'          => sprintf( __( 'Parent %s: ', 'minimalio' ), $singular ),
			'edit_item'                  => sprintf( __( 'Edit  %s ', 'minimalio' ), $singular ),
			'update_item'                => sprintf( __( 'Update %s ', 'minimalio' ), $singular ),
			'add_new_item'               => sprintf( __( 'Add New %s ', 'minimalio' ), $singular ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'minimalio' ), $singular ),
			'menu_name'                  => sprintf( __( '%s', 'minimalio' ), $plural ),
			// Tags
			'popular_items'              => sprintf( __( 'Popular %s ', 'minimalio' ), $plural ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s  with commas', 'minimalio' ), strtolower( $plural ) ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s ', 'minimalio' ), strtolower( $plural ) ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s ', 'minimalio' ), strtolower( $plural ) ),
			'not_found'                  => sprintf( __( 'No %s found ', 'minimalio' ), strtolower( $plural ) ),
		];

		return $minimalio_labels;
	}

	/**
	 * Generate the complete arguments ready for taxonomy creation,
	 * including the URL slug and merging of new defaults above.
	 * @param string $slug     The URL slug for the taxonomy, eg 'category'
	 * @param string $singular Singular & capitalised form for the taxonomy, eg 'Category'
	 * @param string $plural   Plural & capitalised form for the taxonomy, eg 'Categories'
	 * @param array  $minimalio_args     Additional arguments to override the defaults
	 */
	public function minimalio_buildTaxonomyArgs( $slug, $singular = 'Category', $plural = 'Categories', $minimalio_args = [] ) {
		$minimalio_args = wp_parse_args( $minimalio_args, $this->taxonomyDefaults );

		$minimalio_args['rewrite']['slug'] = $slug;

		$minimalio_args['labels'] = $this->minimalio_buildTaxonomyLabels( $singular, $plural );

		return $minimalio_args;
	}
}

/**
 * These public functions exist as procedural functions to keep in style
 * with WordPress theme development.
 */
function minimalio_build_post_args( $slug, $singular = 'Post', $plural = 'Posts', $minimalio_args = [] ) {
	$builder = new minimalio_ARGS_Build;
	return $builder->minimalio_buildPostArgs( $slug, $singular, $plural, $minimalio_args );
}

function minimalio_build_taxonomy_args( $slug, $singular = 'Category', $plural = 'Categories', $minimalio_args = [] ) {
	$builder = new minimalio_ARGS_Build;
	return $builder->minimalio_buildTaxonomyArgs( $slug, $singular, $plural, $minimalio_args );
}

/**
 * Classes that fitch pages
 * used for customizer options
 */
if ( ! function_exists( 'minimalio_theme_customize_register' ) ) {
	/**
	 * Register individual settings through customizer's API.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer reference.
	 */
	function minimalio_theme_customize_register() {

		class Minimalio_My_Dropdown_Category_Control_Header extends WP_Customize_Control {

			public $type = 'dropdown-category';

			protected $dropdown_args = false;

			protected function render_content() {
				?><label>
				<?php

				if ( ! empty( $this->label ) ) :
					?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
																	<?php
				endif;

				if ( ! empty( $this->description ) ) :
					?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
																						<?php
				endif;

				$dropdown_args = wp_parse_args( $this->dropdown_args, [
					'show_option_none' => __( 'Default', 'minimalio' ),
					'orderby'          => 'title',
					'hide_empty'       => false,
					'post_type'        => 'page',
					'suppress_filters' => true,
				] );

				$dropdown_args['echo'] = false;

				$dropdown = wp_dropdown_pages( $dropdown_args );
				$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );
				echo $dropdown;

				?>
				</label>
				<?php
			}
		}
	}
	add_action( 'customize_register', 'minimalio_theme_customize_register' );
}
