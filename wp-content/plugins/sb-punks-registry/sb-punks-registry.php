<?php
/**
 * Plugin Name: SB Punks Registry (Hotfix)
 * Description: Safe baseline for BurnedPunks/MuseumPunks CPT + shortcodes. Hotfix build to prevent site-breaking errors.
 * Version: 0.1.4
 * Author: SB
 */

if (!defined('ABSPATH')) exit;

final class SB_Punks_Registry_Hotfix {
	const PT = 'sb_punk';
	const OPT_KEY = 'sb_punks_registry_settings';

	public static function init() : void {
		add_action('init', [__CLASS__, 'register_cpt']);
		add_action('init', [__CLASS__, 'register_shortcodes']);
		add_action('admin_menu', [__CLASS__, 'register_settings_page']);
		add_action('admin_init', [__CLASS__, 'register_settings']);
		add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
		add_action('save_post_' . self::PT, [__CLASS__, 'save_meta'], 10, 2);

		// Numeric-only root routing: /5449/ -> CPT item with slug "5449"
		add_action('init', [__CLASS__, 'register_numeric_rewrite'], 20);
		add_filter('query_vars', [__CLASS__, 'add_query_vars']);
		add_action('pre_get_posts', [__CLASS__, 'handle_numeric_route']);

		// Front-end assets (minimal)
		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
	}

	public static function activate() : void {
		self::register_cpt();
		self::register_numeric_rewrite();
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
		$labels = [
			'name' => 'Punks',
			'singular_name' => 'Punk',
			'add_new' => 'Add Punk',
			'add_new_item' => 'Add Punk',
			'edit_item' => 'Edit Punk',
			'new_item' => 'New Punk',
			'view_item' => 'View Punk',
			'search_items' => 'Search Punks',
		];

		register_post_type(self::PT, [
			'labels' => $labels,
			'public' => true,
			'has_archive' => false,
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-art',
			'supports' => ['title','editor','thumbnail','excerpt','revisions'],
			'rewrite' => false, // we provide numeric rewrite (and we want to avoid slug conflicts)
		]);
	}

	public static function register_numeric_rewrite() : void {
		add_rewrite_rule(
			'^([0-9]{1,5})/?$',
			'index.php?sb_punk_numeric=$matches[1]',
			'top'
		);
	}

	public static function add_query_vars($vars) {
		$vars[] = 'sb_punk_numeric';
		return $vars;
	}

	public static function handle_numeric_route($q) : void {
		if (is_admin() || !$q->is_main_query()) return;

		$num = $q->get('sb_punk_numeric');
		if (!$num) return;

		// Route to CPT by slug/name = numeric
		$q->set('post_type', self::PT);
		$q->set('name', sanitize_title((string)$num));
		$q->set('sb_punk_numeric', null);
	}

	public static function register_shortcodes() : void {
		add_shortcode('sb_punks_home', [__CLASS__, 'shortcode_home']);
		add_shortcode('sb_punks_grid', [__CLASS__, 'shortcode_grid']);
	}

	public static function enqueue_assets() : void {
		$ver = '0.1.4';
		wp_register_style('sb-punks-registry', plugins_url('assets/sb-punks.css', __FILE__), [], $ver);
		wp_enqueue_style('sb-punks-registry');
	}

	public static function shortcode_home($atts = []) : string {
		$s = self::get_settings();
		$about = esc_url($s['about_url'] ?: '/about/');
		$logo_default = esc_url($s['logo_default_url']);
		$logo_hover = esc_url($s['logo_hover_url']);

		ob_start(); ?>
		<div class="sbpr-home">
			<header class="sbpr-header">
				<a class="sbpr-logo" href="<?php echo $about; ?>">
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

			<section class="sbpr-mosaic" aria-label="Punks">
				<?php echo self::render_grid_markup(1200); ?>
			</section>
		</div>
		<?php
		return (string)ob_get_clean();
	}

	public static function shortcode_grid($atts = []) : string {
		ob_start(); ?>
		<div class="sbpr-gridwrap">
			<?php echo self::render_grid_markup(600); ?>
		</div>
		<?php
		return (string)ob_get_clean();
	}

	private static function render_grid_markup(int $max_items = 600) : string {
		$q = new WP_Query([
			'post_type' => self::PT,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
			'fields' => 'ids',
		]);

		$ids = $q->posts ?: [];
		if (empty($ids)) {
			return '<p class="sbpr-empty">No punks found yet.</p>';
		}

		// Repeat to fake "thousands"
		$links = [];
		foreach ($ids as $id) {
			$links[] = get_permalink($id);
		}

		$out = '<div class="sbpr-mosaic__grid">';
		$len = count($links);
		$total = max(1, min($max_items, $len * (int)ceil($max_items / max(1,$len))));
		for ($i=0; $i<$total; $i++) {
			$href = esc_url($links[$i % $len]);
			$out .= '<a class="sbpr-tile" href="'.$href.'" aria-label="Punk"></a>';
		}
		$out .= '</div>';

		return $out;
	}

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
		$out = [];
		$out['mode'] = ($in['mode'] ?? 'burned') === 'museum' ? 'museum' : 'burned';
		$out['about_url'] = esc_url_raw((string)($in['about_url'] ?? '/about/'));
		$out['logo_default_url'] = esc_url_raw((string)($in['logo_default_url'] ?? ''));
		$out['logo_hover_url'] = esc_url_raw((string)($in['logo_hover_url'] ?? ''));
		return $out;
	}

	public static function render_settings_page() : void {
		if (!current_user_can('manage_options')) return;
		?>
		<div class="wrap">
			<h1>SB Punks Registry</h1>
			<p><strong>Hotfix mode:</strong> this build disables all chain fetching/import to keep the site stable.</p>
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
		<p class="description">Paste full image URL.</p>
		<?php
	}

	public static function field_logo_hover() : void {
		$s = self::get_settings();
		?>
		<input type="text" class="regular-text" name="<?php echo esc_attr(self::OPT_KEY); ?>[logo_hover_url]" value="<?php echo esc_attr($s['logo_hover_url']); ?>" />
		<p class="description">Optional hover-swap image URL.</p>
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
		<p class="description">Hotfix build: chain data/import is disabled.</p>
		<?php
	}

	public static function save_meta($post_id, $post) : void {
		if (!isset($_POST['sbpr_nonce']) || !wp_verify_nonce($_POST['sbpr_nonce'], 'sbpr_save_meta')) return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!current_user_can('edit_post', $post_id)) return;

		$punk_id = isset($_POST['sbpr_punk_id']) ? (int)$_POST['sbpr_punk_id'] : null;
		if ($punk_id !== null && $punk_id >= 0 && $punk_id <= 9999) {
			update_post_meta($post_id, '_sbpr_punk_id', (string)$punk_id);

			// Ensure numeric slug/permalink stability if user wants /5449/
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

SB_Punks_Registry_Hotfix::init();
register_activation_hook(__FILE__, ['SB_Punks_Registry_Hotfix', 'activate']);
register_deactivation_hook(__FILE__, ['SB_Punks_Registry_Hotfix', 'deactivate']);
