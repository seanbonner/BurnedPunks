<?php
/**
 * Plugin Name: SB Punks Registry
 * Description: BurnedPunks/MuseumPunks registry + front-page mosaic + numeric permalinks.
 * Version: 0.1.6
 * Author: SB
 */

if (!defined('ABSPATH')) exit;

final class SB_Punks_Registry {
	const PT = 'sb_punk';
	const OPT_KEY = 'sb_punks_registry_settings';

	public static function init() : void {
		add_action('init', [__CLASS__, 'register_cpt']);
		add_action('init', [__CLASS__, 'register_rewrites'], 20);

		add_shortcode('sb_punks_home', [__CLASS__, 'shortcode_home']);
		add_shortcode('sb_punks_grid', [__CLASS__, 'shortcode_grid_mosaic']);
		add_shortcode('sb_punks_index', [__CLASS__, 'shortcode_index']);

		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
		add_filter('body_class', [__CLASS__, 'body_class']);

		// Force pretty /####/ permalinks for sb_punk posts.
		add_filter('post_type_link', [__CLASS__, 'filter_sb_punk_link'], 10, 4);
		add_filter('post_link', [__CLASS__, 'filter_sb_punk_link'], 10, 3);

		// Admin UX (kept minimal)
		add_action('admin_menu', [__CLASS__, 'register_settings_page']);
		add_action('admin_init', [__CLASS__, 'register_settings']);
		add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
		add_action('save_post_' . self::PT, [__CLASS__, 'save_meta'], 10, 2);
	}

	public static function activate() : void {
		self::register_cpt();
		self::register_rewrites();
		flush_rewrite_rules();
	}

	public static function deactivate() : void {
		flush_rewrite_rules();
	}

	public static function get_settings() : array {
		$defaults = [
			'mode' => 'burned', // burned|museum
			'about_url' => '/about/',
			'logo_default_url' => '',
			'logo_hover_url' => '',
		];
		$raw = get_option(self::OPT_KEY, []);
		if (!is_array($raw)) $raw = [];
		return array_merge($defaults, $raw);
	}

	public static function register_cpt() : void {
		register_post_type(self::PT, [
			'labels' => [
				'name' => 'Punks',
				'singular_name' => 'Punk',
			],
			'public' => true,
			'publicly_queryable' => true,
			'has_archive' => false,
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-art',
			'supports' => ['title','editor','thumbnail','excerpt','revisions'],
			'rewrite' => false,       // we provide our own numeric rewrite
			'query_var' => 'sb_punk', // fallback query var
		]);
	}

	public static function register_rewrites() : void {
		// /5449/ -> sb_punk with slug/name 5449
		add_rewrite_rule(
			'^([0-9]{1,5})/?$',
			'index.php?post_type=' . self::PT . '&name=$matches[1]',
			'top'
		);
	}

	public static function filter_sb_punk_link($permalink, $post, $leavename = false) {
		if (is_object($post) && isset($post->post_type) && $post->post_type === self::PT) {
			$slug = (string)$post->post_name;
			if (preg_match('/^[0-9]{1,5}$/', $slug)) {
				return home_url('/' . $slug . '/');
			}
		}
		return $permalink;
	}

	public static function enqueue_assets() : void {
		$ver = '0.1.6';
		wp_enqueue_style('sbpr', plugins_url('assets/sbpr.css', __FILE__), [], $ver);
		wp_enqueue_script('sbpr', plugins_url('assets/sbpr.js', __FILE__), [], $ver, true);
	}

	public static function body_class($classes) {
		if (is_front_page()) {
			$post_id = get_queried_object_id();
			if ($post_id) {
				$content = (string)get_post_field('post_content', $post_id);
				if ($content && has_shortcode($content, 'sb_punks_home')) {
					$classes[] = 'sbpr-front';
				}
			}
		}
		return $classes;
	}

	// -------------------------
	// Shortcodes
	// -------------------------

	public static function shortcode_home($atts = []) : string {
		$s = self::get_settings();
		$about = esc_url($s['about_url'] ?: '/about/');
		$logo_default = esc_url($s['logo_default_url']);
		$logo_hover = esc_url($s['logo_hover_url']);

		$items = self::get_punk_items(true); // shuffled

		ob_start(); ?>
		<div class="sbpr-home">
			<header class="sbpr-header">
				<a class="sbpr-logo" href="<?php echo $about; ?>" aria-label="About">
					<?php if ($logo_default): ?>
						<img class="sbpr-logo__img sbpr-logo__img--default" src="<?php echo $logo_default; ?>" alt="About" />
					<?php else: ?>
						<span class="sbpr-logo__text">About</span>
					<?php endif; ?>
					<?php if ($logo_hover): ?>
						<img class="sbpr-logo__img sbpr-logo__img--hover" src="<?php echo $logo_hover; ?>" alt="" aria-hidden="true" />
					<?php endif; ?>
				</a>
			</header>

			<section class="sbpr-mosaic" aria-label="Punks mosaic">
				<div class="sbpr-mosaic__grid"
					 data-sbpr-items="<?php echo esc_attr(wp_json_encode($items)); ?>"
					 data-sbpr-mode="home"></div>

				<div class="sbpr-mag" aria-hidden="true">
					<div class="sbpr-mag__inner"></div>
				</div>
			</section>
		</div>
		<?php
		return (string)ob_get_clean();
	}

	// Kept for debugging/legacy; mosaic repeated with fixed count.
	public static function shortcode_grid_mosaic($atts = []) : string {
		$items = self::get_punk_items(false);
		ob_start(); ?>
		<div class="sbpr-gridpage">
			<div class="sbpr-mosaic__grid"
				 data-sbpr-items="<?php echo esc_attr(wp_json_encode($items)); ?>"
				 data-sbpr-mode="mosaic"></div>
		</div>
		<?php
		return (string)ob_get_clean();
	}

	// The "nice" Punks index: big, full color, shows the number.
	public static function shortcode_index($atts = []) : string {
		$items = self::get_punk_items(false);

		if (empty($items)) return '<p class="sbpr-empty">No punks found yet.</p>';

		$out = '<div class="sbpr-index">';
		foreach ($items as $it) {
			$href = esc_url($it['href']);
			$thumb = esc_url($it['thumb']);
			$num = esc_html($it['num']);
			$out .= '<a class="sbpr-index__card" href="'.$href.'">';
			if ($thumb) {
				$out .= '<img class="sbpr-index__img" src="'.$thumb.'" alt="" loading="lazy" decoding="async" />';
			} else {
				$out .= '<span class="sbpr-index__ph" aria-hidden="true"></span>';
			}
			$out .= '<span class="sbpr-index__num">'.$num.'</span>';
			$out .= '</a>';
		}
		$out .= '</div>';

		return $out;
	}

	// -------------------------
	// Data helpers
	// -------------------------

	/**
	 * Returns array of punk items:
	 * [
	 *   { "num": "5449", "href": "https://site/5449/", "thumb": "https://..." },
	 * ]
	 */
	private static function get_punk_items(bool $shuffle) : array {
		global $wpdb;

		// Prefer CPT posts, but fall back to any numeric-slug published posts.
		$sql = "
			SELECT ID, post_name
			FROM {$wpdb->posts}
			WHERE post_status='publish'
			  AND post_type NOT IN ('revision','nav_menu_item','attachment')
			  AND post_name REGEXP '^[0-9]{1,5}$'
			ORDER BY CAST(post_name AS UNSIGNED) ASC
			LIMIT 2000
		";
		$rows = $wpdb->get_results($sql);
		if (!$rows) return [];

		$items = [];
		foreach ($rows as $r) {
			$id = (int)$r->ID;
			$slug = (string)$r->post_name;

			// Force pretty numeric link always.
			$href = home_url('/' . $slug . '/');

			$thumb = '';
			if (has_post_thumbnail($id)) {
				$thumb = (string)get_the_post_thumbnail_url($id, 'medium');
			}

			$items[] = [
				'num' => $slug,
				'href' => $href,
				'thumb' => $thumb,
			];
		}

		if ($shuffle && count($items) > 1) {
			shuffle($items);
		}

		return $items;
	}

	// -------------------------
	// Admin: settings + meta
	// -------------------------

	public static function register_settings_page() : void {
		add_options_page(
			'SB Punks Registry',
			'SB Punks Registry',
			'manage_options',
			'sb-punks-registry',
			[__CLASS__, 'render_settings_page']
		);
	}

	public static function register_settings() : void {
		register_setting('sb_punks_registry', self::OPT_KEY, [
			'type' => 'array',
			'sanitize_callback' => [__CLASS__, 'sanitize_settings'],
			'default' => [],
		]);

		add_settings_section('sbpr_main', 'Settings', '__return_false', 'sb-punks-registry');

		add_settings_field('mode', 'Site mode', [__CLASS__, 'field_mode'], 'sb-punks-registry', 'sbpr_main');
		add_settings_field('about_url', 'About URL', [__CLASS__, 'field_about_url'], 'sb-punks-registry', 'sbpr_main');
		add_settings_field('logo_default_url', 'Logo image URL (default)', [__CLASS__, 'field_logo_default'], 'sb-punks-registry', 'sbpr_main');
		add_settings_field('logo_hover_url', 'Logo image URL (hover)', [__CLASS__, 'field_logo_hover'], 'sb-punks-registry', 'sbpr_main');
	}

	public static function sanitize_settings($in) : array {
		if (!is_array($in)) return [];
		return [
			'mode' => (($in['mode'] ?? 'burned') === 'museum') ? 'museum' : 'burned',
			'about_url' => esc_url_raw((string)($in['about_url'] ?? '/about/')),
			'logo_default_url' => esc_url_raw((string)($in['logo_default_url'] ?? '')),
			'logo_hover_url' => esc_url_raw((string)($in['logo_hover_url'] ?? '')),
		];
	}

	public static function render_settings_page() : void {
		if (!current_user_can('manage_options')) return;
		?>
		<div class="wrap">
			<h1>SB Punks Registry</h1>
			<p><strong>Important:</strong> after updating this plugin, go to <em>Settings → Permalinks</em> and click <em>Save</em> once.</p>
			<form method="post" action="options.php">
				<?php
				settings_fields('sb_punks_registry');
				do_settings_sections('sb-punks-registry');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public static function field_mode() : void {
		$s = self::get_settings();
		?>
		<select name="<?php echo esc_attr(self::OPT_KEY); ?>[mode]">
			<option value="burned" <?php selected($s['mode'], 'burned'); ?>>BurnedPunks</option>
			<option value="museum" <?php selected($s['mode'], 'museum'); ?>>MuseumPunks</option>
		</select>
		<?php
	}

	public static function field_about_url() : void {
		$s = self::get_settings();
		?>
		<input type="text" class="regular-text" name="<?php echo esc_attr(self::OPT_KEY); ?>[about_url]" value="<?php echo esc_attr($s['about_url']); ?>" />
		<?php
	}

	public static function field_logo_default() : void {
		$s = self::get_settings();
		?>
		<input type="text" class="regular-text" name="<?php echo esc_attr(self::OPT_KEY); ?>[logo_default_url]" value="<?php echo esc_attr($s['logo_default_url']); ?>" />
		<?php
	}

	public static function field_logo_hover() : void {
		$s = self::get_settings();
		?>
		<input type="text" class="regular-text" name="<?php echo esc_attr(self::OPT_KEY); ?>[logo_hover_url]" value="<?php echo esc_attr($s['logo_hover_url']); ?>" />
		<?php
	}

	public static function add_meta_boxes() : void {
		add_meta_box('sbpr_meta', 'Punk Details', [__CLASS__, 'render_meta_box'], self::PT, 'side', 'high');
	}

	public static function render_meta_box($post) : void {
		wp_nonce_field('sbpr_save_meta', 'sbpr_nonce');
		$punk_id = get_post_meta($post->ID, '_sbpr_punk_id', true);
		$intent = get_post_meta($post->ID, '_sbpr_intent', true);
		?>
		<p>
			<label for="sbpr_punk_id"><strong>Punk #</strong></label><br/>
			<input type="number" min="0" max="9999" id="sbpr_punk_id" name="sbpr_punk_id" value="<?php echo esc_attr($punk_id); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="sbpr_intent"><strong>Burn type</strong></label><br/>
			<select id="sbpr_intent" name="sbpr_intent" style="width:100%;">
				<option value="" <?php selected($intent, ''); ?>>(not set)</option>
				<option value="intentional" <?php selected($intent, 'intentional'); ?>>Intentional</option>
				<option value="accidental" <?php selected($intent, 'accidental'); ?>>Accidental</option>
			</select>
		</p>
		<?php
	}

	public static function save_meta($post_id, $post) : void {
		if (!isset($_POST['sbpr_nonce']) || !wp_verify_nonce($_POST['sbpr_nonce'], 'sbpr_save_meta')) return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!current_user_can('edit_post', $post_id)) return;

		$punk_id = isset($_POST['sbpr_punk_id']) ? (int)$_POST['sbpr_punk_id'] : null;
		if ($punk_id !== null && $punk_id >= 0 && $punk_id <= 9999) {
			update_post_meta($post_id, '_sbpr_punk_id', (string)$punk_id);

			$desired = (string)$punk_id;
			if ($post->post_name !== $desired) {
				remove_action('save_post_' . self::PT, [__CLASS__, 'save_meta'], 10);
				wp_update_post([
					'ID' => $post_id,
					'post_name' => $desired,
					'post_title' => $desired,
				]);
				add_action('save_post_' . self::PT, [__CLASS__, 'save_meta'], 10, 2);
			}
		}

		$intent = isset($_POST['sbpr_intent']) ? sanitize_text_field((string)$_POST['sbpr_intent']) : '';
		if (!in_array($intent, ['intentional','accidental',''], true)) $intent = '';
		update_post_meta($post_id, '_sbpr_intent', $intent);
	}
}

SB_Punks_Registry::init();
register_activation_hook(__FILE__, ['SB_Punks_Registry', 'activate']);
register_deactivation_hook(__FILE__, ['SB_Punks_Registry', 'deactivate']);
