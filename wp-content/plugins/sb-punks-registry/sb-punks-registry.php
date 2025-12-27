<?php
/**
 * Plugin Name: SB Punks Registry
 * Description: BurnedPunks/MuseumPunks registry + front-page mosaic + numeric permalinks + single punk layout.
 * Version: 0.3.2
 * Author: SB
 */

if (!defined('ABSPATH')) exit;

final class SB_Punks_Registry {
	const PT = 'sb_punk';
	const OPT_KEY = 'sb_punks_registry_settings';

	// Contracts (mainnet)
	const WRAPPER_CONTRACT = '0x282BDD42f4eb70e7A9D9F40c8fEA0825B7f68C5D';

	// CryptoPunks site helpers
	const CP_DETAILS_BASE = 'https://cryptopunks.app/cryptopunks/details/';
	const CP_ACCOUNT_BASE = 'https://cryptopunks.app/cryptopunks/accountinfo?account=';

	// Meta keys
	const META_PUNK_ID       = '_sbpr_punk_id';          // 0-9999 (kept in sync with title/slug)
	const META_INTENT        = '_sbpr_intent';           // intentional|accidental|''
	const META_BURN_DATE     = '_sbpr_burn_date';        // YYYY-MM-DD

	const META_CLAIMER_WALLET = '_sbpr_claimer_wallet';
	const META_CLAIMER_NAME   = '_sbpr_claimer_name';
	const META_BURNER_WALLET  = '_sbpr_burner_wallet';
	const META_BURNER_NAME    = '_sbpr_burner_name';
	const META_FINAL_WALLET   = '_sbpr_final_wallet';
	const META_FINAL_NAME     = '_sbpr_final_name';
	const META_V1_WRAPPED     = '_sbpr_v1_wrapped';      // '1'|'0'

	public static function init() : void {
		add_action('init', [__CLASS__, 'register_cpt']);
		add_action('init', [__CLASS__, 'register_rewrites'], 20);
		add_filter('query_vars', [__CLASS__, 'register_query_vars']);
		add_filter('template_include', [__CLASS__, 'template_override'], 50);

		add_action('init', [__CLASS__, 'force_no_comments'], 30);

		add_shortcode('sb_punks_home', [__CLASS__, 'shortcode_home']);
		add_shortcode('sb_punks_index', [__CLASS__, 'shortcode_index']);

		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
		add_filter('body_class', [__CLASS__, 'body_class']);

		// Force pretty /####/ permalinks for sb_punk posts.
		add_filter('post_type_link', [__CLASS__, 'filter_sb_punk_link'], 10, 4);
		add_filter('post_link', [__CLASS__, 'filter_sb_punk_link'], 10, 3);

		// Use classic editor for this CPT.
		add_filter('use_block_editor_for_post_type', [__CLASS__, 'disable_block_editor_for_cpt'], 10, 2);

		// Hard-disable comments/pings for this CPT.
		add_filter('comments_open', [__CLASS__, 'comments_open'], 10, 2);
		add_filter('pings_open', [__CLASS__, 'pings_open'], 10, 2);

		// Admin UX
		add_action('admin_menu', [__CLASS__, 'register_settings_page']);
		add_action('admin_init', [__CLASS__, 'register_settings']);
		add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
		add_action('save_post_' . self::PT, [__CLASS__, 'save_meta'], 10, 3);
	}

	public static function activate() : void {
		self::register_cpt();
		self::register_rewrites();
		flush_rewrite_rules();
	}

	public static function deactivate() : void {
		flush_rewrite_rules();
	}

	public static function disable_block_editor_for_cpt($use, $post_type) {
		if ($post_type === self::PT) return false;
		return $use;
	}

	public static function force_no_comments() : void {
		remove_post_type_support(self::PT, 'comments');
		remove_post_type_support(self::PT, 'trackbacks');
		remove_post_type_support(self::PT, 'excerpt');
	}

	public static function comments_open($open, $post_id) {
		$post = get_post($post_id);
		if ($post && $post->post_type === self::PT) return false;
		return $open;
	}

	public static function pings_open($open, $post_id) {
		$post = get_post($post_id);
		if ($post && $post->post_type === self::PT) return false;
		return $open;
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
			'show_in_rest' => false,
			'menu_icon' => 'dashicons-art',
			'supports' => ['title','editor','thumbnail','revisions'],
			'rewrite' => false,
			'query_var' => 'sb_punk',
		]);
	}

	public static function register_rewrites() : void {
		// /5449/ -> sb_punk with slug/name 5449
		add_rewrite_rule(
			'^([0-9]{1,5})/?$',
			'index.php?post_type=' . self::PT . '&name=$matches[1]',
			'top'
		);

		// Force /the-punks/ to always render our index.
		add_rewrite_rule(
			'^the-punks/?$',
			'index.php?sbpr_punks=1',
			'top'
		);
	}

	public static function register_query_vars($vars) {
		$vars[] = 'sbpr_punks';
		return $vars;
	}

	public static function template_override($template) {
		if ((int)get_query_var('sbpr_punks') === 1) {
			$t = plugin_dir_path(__FILE__) . 'templates/the-punks.php';
			if (file_exists($t)) return $t;
		}

		if (is_singular(self::PT)) {
			$t = plugin_dir_path(__FILE__) . 'templates/single-sb_punk.php';
			if (file_exists($t)) return $t;
		}

		return $template;
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
		$ver = '0.3.2';
		wp_enqueue_style('sbpr', plugins_url('assets/sbpr.css', __FILE__), [], $ver);
		wp_enqueue_script('sbpr', plugins_url('assets/sbpr.js', __FILE__), [], $ver, true);
	}

	public static function body_class($classes) {
		if ((int)get_query_var('sbpr_punks') === 1) $classes[] = 'sbpr-punks-index';

		if (is_front_page()) {
			$post_id = get_queried_object_id();
			if ($post_id) {
				$content = (string)get_post_field('post_content', $post_id);
				if ($content && has_shortcode($content, 'sb_punks_home')) $classes[] = 'sbpr-front';
			}
		}

		if (is_singular(self::PT)) $classes[] = 'sbpr-single';

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

		$items = self::get_punk_items(false);

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
			</section>
		</div>
		<?php
		return (string)ob_get_clean();
	}

	public static function shortcode_index($atts = []) : string {
		$items = self::get_punk_items(true);
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
	// Sorting helpers
	// -------------------------

	private static function normalize_date($s) : string {
		$s = (string)$s;
		if (preg_match('/\b(20\d{2}-\d{2}-\d{2})\b/', $s, $m)) return $m[1];
		return '';
	}

	private static function date_key($s) : int {
		$d = self::normalize_date($s);
		if (!$d) return 0;
		return (int) str_replace('-', '', $d);
	}

	private static function extract_burn_date_from_content($content) : string {
		$content = (string)$content;
		if (!$content) return '';

		if (!preg_match_all('/\b(20\d{2}-\d{2}-\d{2})\b/', $content, $matches, PREG_OFFSET_CAPTURE)) return '';
		$dates = $matches[1];

		foreach ($dates as $d) {
			$date = $d[0];
			$pos  = (int)$d[1];
			$window_start = max(0, $pos - 80);
			$window_len   = 160;
			$window = strtolower(substr($content, $window_start, $window_len));
			if (strpos($window, 'burn') !== false) return $date;
		}

		$last = end($dates);
		return $last ? (string)$last[0] : '';
	}

	// -------------------------
	// Data helpers
	// -------------------------

	private static function get_punk_items(bool $sort_by_burn_date) : array {
		global $wpdb;

		$sql = "
			SELECT ID, post_name, post_date
			FROM {$wpdb->posts}
			WHERE post_status='publish'
			  AND post_type = %s
			  AND post_name REGEXP '^[0-9]{1,5}$'
			ORDER BY CAST(post_name AS UNSIGNED) ASC
			LIMIT 2000
		";
		$rows = $wpdb->get_results($wpdb->prepare($sql, self::PT));
		if (!$rows) return [];

		$items = [];
		foreach ($rows as $r) {
			$id = (int)$r->ID;
			$slug = (string)$r->post_name;

			$href = home_url('/' . $slug . '/');

			$thumb = '';
			if (has_post_thumbnail($id)) {
				$thumb = (string)get_the_post_thumbnail_url($id, 'large');
			} else {
				$attached = get_attached_media('image', $id);
				if (!empty($attached)) {
					$first = reset($attached);
					$thumb = $first ? wp_get_attachment_image_url($first->ID, 'large') : '';
				}
			}

			$burn_date = self::normalize_date((string)get_post_meta($id, self::META_BURN_DATE, true));
			if (!$burn_date) {
				$content = (string)get_post_field('post_content', $id);
				$burn_date = self::extract_burn_date_from_content($content);
			}

			$items[] = [
				'num' => $slug,
				'href' => $href,
				'thumb' => $thumb,
				'burn_date' => $burn_date,
				'post_date' => (string)$r->post_date,
			];
		}

		if ($sort_by_burn_date && count($items) > 1) {
			usort($items, function($a, $b){
				$ak = self::date_key($a['burn_date']);
				$bk = self::date_key($b['burn_date']);

				if ($ak === 0 || $bk === 0) {
					$ap = strtotime((string)$a['post_date']) ?: 0;
					$bp = strtotime((string)$b['post_date']) ?: 0;
					if ($ap !== $bp) return $bp <=> $ap;
				}

				if ($ak === 0 && $bk !== 0) return 1;
				if ($bk === 0 && $ak !== 0) return -1;

				if ($ak !== $bk) return $bk <=> $ak;
				return (int)$b['num'] <=> (int)$a['num'];
			});
		}

		foreach ($items as &$it) { unset($it['post_date']); }
		return $items;
	}

	// -------------------------
	// Single rendering helpers
	// -------------------------

	public static function cp_details_url($punk_num) : string {
		$n = preg_replace('/[^0-9]/', '', (string)$punk_num);
		return esc_url(self::CP_DETAILS_BASE . $n);
	}

	public static function cp_account_url($wallet) : string {
		$w = strtolower(trim((string)$wallet));
		return esc_url(self::CP_ACCOUNT_BASE . $w);
	}

	public static function os_wrapped_url($punk_num) : string {
		$n = preg_replace('/[^0-9]/', '', (string)$punk_num);
		return esc_url('https://opensea.io/assets/ethereum/' . self::WRAPPER_CONTRACT . '/' . $n);
	}

	public static function extract_story_html($content) : string {
		// Take everything after the first </h4> if present, otherwise return full content.
		$content = (string)$content;
		if (!$content) return '';

		$pos = stripos($content, '</h4>');
		if ($pos !== false) {
			$after = substr($content, $pos + 5);
			return $after;
		}
		return $content;
	}

	private static function parse_wallet_from_cp_account_href($href) : string {
		$href = (string)$href;
		if (!$href) return '';
		if (preg_match('/account=0x[a-fA-F0-9]{40}/', $href, $m)) {
			$parts = explode('account=', $m[0]);
			return isset($parts[1]) ? $parts[1] : '';
		}
		return '';
	}

	private static function parse_first_cp_account_link($html) : array {
		// returns [wallet, name]
		$wallet = '';
		$name = '';
		if (preg_match('/<a[^>]+href="([^"]+account=0x[a-fA-F0-9]{40}[^"]*)"[^>]*>(.*?)<\/a>/i', $html, $m)) {
			$wallet = self::parse_wallet_from_cp_account_href($m[1]);
			$name = wp_strip_all_tags($m[2]);
		}
		return [$wallet, $name];
	}

	private static function parse_wallet_literal($html) : string {
		if (preg_match('/0x[a-fA-F0-9]{40}/', $html, $m)) return $m[0];
		return '';
	}

	private static function parse_section_value($html, $label) : string {
		// Extract the chunk after "<strong>Label:</strong>" up to <br or </h4>
		$label_q = preg_quote($label, '/');
		if (preg_match('/<strong>\s*' . $label_q . '\s*:\s*<\/strong>\s*(.*?)(<br\s*\/?>|<\/h4>)/is', $html, $m)) {
			return trim((string)$m[1]);
		}
		return '';
	}

	private static function maybe_migrate_meta_from_content($post_id, $post) : void {
		// Only fill meta fields if they are empty; never overwrite user edits.
		$content = (string)$post->post_content;
		if (!$content) return;

		$need = [
			self::META_CLAIMER_WALLET,
			self::META_BURNER_WALLET,
			self::META_FINAL_WALLET,
		];

		$missing_any = false;
		foreach ($need as $k) {
			if (!get_post_meta($post_id, $k, true)) { $missing_any = true; break; }
		}
		// V1 wrapped can default to 0; only set if we detect wrapped.
		$has_v1 = get_post_meta($post_id, self::META_V1_WRAPPED, true) !== '';

		if (!$missing_any && $has_v1) return;

		// Find the h4 block that contains the data lines.
		if (!preg_match('/<h4[^>]*>(.*?)<\/h4>/is', $content, $m)) return;
		$h4 = $m[1];

		// Claimer
		if (!get_post_meta($post_id, self::META_CLAIMER_WALLET, true)) {
			$val = self::parse_section_value($h4, 'Claimer');
			list($w, $name) = self::parse_first_cp_account_link($val);
			if ($w) update_post_meta($post_id, self::META_CLAIMER_WALLET, strtolower($w));
			if ($name && !get_post_meta($post_id, self::META_CLAIMER_NAME, true)) update_post_meta($post_id, self::META_CLAIMER_NAME, $name);
		}

		// Burner
		if (!get_post_meta($post_id, self::META_BURNER_WALLET, true)) {
			$val = self::parse_section_value($h4, 'Burner');
			list($w, $name) = self::parse_first_cp_account_link($val);
			if (!$w) $w = self::parse_wallet_literal($val);
			if ($w) update_post_meta($post_id, self::META_BURNER_WALLET, strtolower($w));
			if ($name && !get_post_meta($post_id, self::META_BURNER_NAME, true)) update_post_meta($post_id, self::META_BURNER_NAME, $name);
		}

		// Final Location
		if (!get_post_meta($post_id, self::META_FINAL_WALLET, true)) {
			$val = self::parse_section_value($h4, 'Final Location');
			list($w, $name) = self::parse_first_cp_account_link($val);
			if (!$w) $w = self::parse_wallet_literal($val);
			if ($w) update_post_meta($post_id, self::META_FINAL_WALLET, strtolower($w));
			if ($name && !get_post_meta($post_id, self::META_FINAL_NAME, true)) update_post_meta($post_id, self::META_FINAL_NAME, $name);
		}

		// V1 status (Wrapped/Unwrapped)
		if (get_post_meta($post_id, self::META_V1_WRAPPED, true) === '') {
			$val = self::parse_section_value($h4, 'V1');
			$txt = strtolower(wp_strip_all_tags($val));
			$wrapped = (strpos($txt, 'wrapped') !== false) ? '1' : '0';
			update_post_meta($post_id, self::META_V1_WRAPPED, $wrapped);
		}
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
			<p><strong>After updating:</strong> Settings → Permalinks → Save (once).</p>
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
		add_meta_box('sbpr_party', 'Participants', [__CLASS__, 'render_party_box'], self::PT, 'normal', 'high');
		add_meta_box('sbpr_status', 'Status', [__CLASS__, 'render_status_box'], self::PT, 'normal', 'default');
	}

	private static function field_row($label, $name, $value, $placeholder = '') {
		?>
		<p style="margin:0 0 12px;">
			<label><strong><?php echo esc_html($label); ?></strong></label><br/>
			<input type="text" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" style="width:100%;" />
		</p>
		<?php
	}

	public static function render_meta_box($post) : void {
		wp_nonce_field('sbpr_save_meta', 'sbpr_nonce');

		$punk_id = (string)get_post_meta($post->ID, self::META_PUNK_ID, true);
		$intent = (string)get_post_meta($post->ID, self::META_INTENT, true);
		$burn_date = (string)get_post_meta($post->ID, self::META_BURN_DATE, true);

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

		<p>
			<label for="sbpr_burn_date"><strong>Burn date (YYYY-MM-DD)</strong></label><br/>
			<input type="text" id="sbpr_burn_date" name="sbpr_burn_date" value="<?php echo esc_attr($burn_date); ?>" placeholder="2021-04-21" style="width:100%;" />
			<span class="description">Controls ordering on /the-punks/ (newest first).</span>
		</p>
		<?php
	}

	public static function render_party_box($post) : void {
		$claimer_wallet = (string)get_post_meta($post->ID, self::META_CLAIMER_WALLET, true);
		$claimer_name   = (string)get_post_meta($post->ID, self::META_CLAIMER_NAME, true);
		$burner_wallet  = (string)get_post_meta($post->ID, self::META_BURNER_WALLET, true);
		$burner_name    = (string)get_post_meta($post->ID, self::META_BURNER_NAME, true);
		$final_wallet   = (string)get_post_meta($post->ID, self::META_FINAL_WALLET, true);
		$final_name     = (string)get_post_meta($post->ID, self::META_FINAL_NAME, true);

		echo '<p class="description" style="margin-top:0;">If a name is set, it will display instead of the wallet address (but still links to that wallet on cryptopunks.app).</p>';

		self::field_row('Claimer wallet', 'sbpr_claimer_wallet', $claimer_wallet, '0x...');
		self::field_row('Claimer name (optional)', 'sbpr_claimer_name', $claimer_name, 'Psyborg');

		echo '<hr/>';

		self::field_row('Burner wallet', 'sbpr_burner_wallet', $burner_wallet, '0x...');
		self::field_row('Burner name (optional)', 'sbpr_burner_name', $burner_name, '');

		echo '<hr/>';

		self::field_row('Final location wallet', 'sbpr_final_wallet', $final_wallet, '0x...');
		self::field_row('Final location name (optional)', 'sbpr_final_name', $final_name, 'Cryptopunks Contract');
	}

	public static function render_status_box($post) : void {
		$v1_wrapped = (string)get_post_meta($post->ID, self::META_V1_WRAPPED, true);
		$checked = ($v1_wrapped === '1') ? 'checked' : '';
		?>
		<p>
			<label>
				<input type="checkbox" name="sbpr_v1_wrapped" value="1" <?php echo $checked; ?> />
				<strong>V1 Wrapped</strong>
			</label>
			<br/>
			<span class="description">If checked, the template will automatically link to OpenSea for the wrapper contract using this punk number.</span>
		</p>
		<?php
	}

	// -------------------------
	// Punk image generation
	// -------------------------

	// Larva Labs official punk images
	const PUNK_IMAGE_BASE = 'https://www.larvalabs.com/public/images/cryptopunks/punk';

	/**
	 * Fetch punk PNG from Larva Labs and scale up with nearest-neighbor
	 */
	private static function fetch_punk_image(int $punk_id, int $target_size = 480) : string {
		if ($punk_id < 0 || $punk_id > 9999) return '';

		// Format: punk0001.png, punk0123.png, punk9999.png
		$padded = str_pad((string)$punk_id, 4, '0', STR_PAD_LEFT);
		$url = self::PUNK_IMAGE_BASE . $padded . '.png';

		$response = wp_remote_get($url, [
			'timeout' => 30,
		]);

		if (is_wp_error($response)) {
			error_log('SBPR: Failed to fetch punk image - ' . $response->get_error_message());
			return '';
		}

		$code = wp_remote_retrieve_response_code($response);
		if ($code !== 200) {
			error_log('SBPR: Punk image returned HTTP ' . $code . ' for punk ' . $punk_id);
			return '';
		}

		$body = wp_remote_retrieve_body($response);
		if (empty($body)) {
			error_log('SBPR: Empty response for punk ' . $punk_id);
			return '';
		}

		// Scale up the 24x24 image to target size using nearest-neighbor
		$scaled = self::scale_image_nearest_neighbor($body, $target_size);
		if (!empty($scaled)) {
			return $scaled;
		}

		// Fallback to original if scaling fails
		return $body;
	}

	/**
	 * Scale PNG image data using nearest-neighbor interpolation
	 */
	private static function scale_image_nearest_neighbor(string $image_data, int $target_size) : string {
		// Try Imagick first (best quality control)
		if (class_exists('Imagick')) {
			try {
				$im = new Imagick();
				$im->readImageBlob($image_data);
				$im->setImageInterpolateMethod(Imagick::INTERPOLATE_NEAREST_NEIGHBOR);
				$im->resizeImage($target_size, $target_size, Imagick::FILTER_POINT, 1);
				$im->setImageFormat('png');
				$result = $im->getImageBlob();
				$im->destroy();
				return $result;
			} catch (Exception $e) {
				error_log('SBPR: Imagick scaling failed - ' . $e->getMessage());
			}
		}

		// Fallback to GD
		if (function_exists('imagecreatefrompng')) {
			$src = @imagecreatefromstring($image_data);
			if ($src === false) {
				error_log('SBPR: GD could not read image');
				return '';
			}

			$src_w = imagesx($src);
			$src_h = imagesy($src);

			$dst = imagecreatetruecolor($target_size, $target_size);
			if ($dst === false) {
				imagedestroy($src);
				return '';
			}

			// Preserve transparency
			imagealphablending($dst, false);
			imagesavealpha($dst, true);
			$transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
			imagefill($dst, 0, 0, $transparent);

			// Scale using nearest-neighbor (pixel-by-pixel copy)
			$scale = $target_size / $src_w;
			for ($y = 0; $y < $target_size; $y++) {
				for ($x = 0; $x < $target_size; $x++) {
					$src_x = (int)floor($x / $scale);
					$src_y = (int)floor($y / $scale);
					$color = imagecolorat($src, $src_x, $src_y);
					imagesetpixel($dst, $x, $y, $color);
				}
			}

			ob_start();
			imagepng($dst);
			$result = ob_get_clean();

			imagedestroy($src);
			imagedestroy($dst);

			return $result;
		}

		error_log('SBPR: No image library available for scaling');
		return '';
	}

	/**
	 * Generate punk image and set as featured image
	 */
	public static function generate_punk_image(int $post_id, int $punk_id) : bool {
		// Don't regenerate if featured image already exists
		if (has_post_thumbnail($post_id)) {
			return true;
		}

		$image_data = self::fetch_punk_image($punk_id);
		if (empty($image_data)) {
			error_log('SBPR: Could not fetch image for punk ' . $punk_id);
			return false;
		}

		// Save to temp file
		$tmp_file = wp_tempnam('punk_') . '.png';
		if (file_put_contents($tmp_file, $image_data) === false) {
			error_log('SBPR: Could not write temp file for punk ' . $punk_id);
			return false;
		}

		$filename = 'punk-' . $punk_id . '.png';

		// Upload to media library
		$file_array = [
			'name' => $filename,
			'tmp_name' => $tmp_file,
		];

		// Need to include media handling functions
		if (!function_exists('media_handle_sideload')) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		$attachment_id = media_handle_sideload($file_array, $post_id, 'CryptoPunk #' . $punk_id);

		// Clean up temp file if still exists
		if (file_exists($tmp_file)) {
			@unlink($tmp_file);
		}

		if (is_wp_error($attachment_id)) {
			error_log('SBPR: Failed to upload punk image - ' . $attachment_id->get_error_message());
			return false;
		}

		// Set as featured image
		set_post_thumbnail($post_id, $attachment_id);

		return true;
	}

	public static function save_meta($post_id, $post, $update) : void {
		if (!isset($_POST['sbpr_nonce']) || !wp_verify_nonce($_POST['sbpr_nonce'], 'sbpr_save_meta')) return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!current_user_can('edit_post', $post_id)) return;

		// Parse legacy content into meta fields (only when fields are empty).
		self::maybe_migrate_meta_from_content($post_id, $post);

		// Punk # (keep title/slug synced)
		$punk_id_to_generate = null;
		if (isset($_POST['sbpr_punk_id']) && $_POST['sbpr_punk_id'] !== '') {
			$punk_id = (int)$_POST['sbpr_punk_id'];
			if ($punk_id >= 0 && $punk_id <= 9999) {
				$old_punk_id = (string)get_post_meta($post_id, self::META_PUNK_ID, true);
				update_post_meta($post_id, self::META_PUNK_ID, (string)$punk_id);

				$desired = (string)$punk_id;
				if ($post->post_name !== $desired || $post->post_title !== $desired) {
					remove_action('save_post_' . self::PT, [__CLASS__, 'save_meta'], 10);
					wp_update_post([
						'ID' => $post_id,
						'post_name' => $desired,
						'post_title' => $desired,
					]);
					add_action('save_post_' . self::PT, [__CLASS__, 'save_meta'], 10, 3);
				}

				// Generate image if this is a new punk ID or no featured image exists
				if ($old_punk_id !== (string)$punk_id || !has_post_thumbnail($post_id)) {
					$punk_id_to_generate = $punk_id;
				}
			}
		}

		$intent = isset($_POST['sbpr_intent']) ? sanitize_text_field((string)$_POST['sbpr_intent']) : '';
		if (!in_array($intent, ['intentional','accidental',''], true)) $intent = '';
		update_post_meta($post_id, self::META_INTENT, $intent);

		$burn_date_in = isset($_POST['sbpr_burn_date']) ? (string)$_POST['sbpr_burn_date'] : '';
		$burn_date = self::normalize_date($burn_date_in);
		if ($burn_date) update_post_meta($post_id, self::META_BURN_DATE, $burn_date);

		// Participants
		$claimer_wallet = isset($_POST['sbpr_claimer_wallet']) ? sanitize_text_field((string)$_POST['sbpr_claimer_wallet']) : '';
		$claimer_name   = isset($_POST['sbpr_claimer_name']) ? sanitize_text_field((string)$_POST['sbpr_claimer_name']) : '';
		$burner_wallet  = isset($_POST['sbpr_burner_wallet']) ? sanitize_text_field((string)$_POST['sbpr_burner_wallet']) : '';
		$burner_name    = isset($_POST['sbpr_burner_name']) ? sanitize_text_field((string)$_POST['sbpr_burner_name']) : '';
		$final_wallet   = isset($_POST['sbpr_final_wallet']) ? sanitize_text_field((string)$_POST['sbpr_final_wallet']) : '';
		$final_name     = isset($_POST['sbpr_final_name']) ? sanitize_text_field((string)$_POST['sbpr_final_name']) : '';

		if ($claimer_wallet) update_post_meta($post_id, self::META_CLAIMER_WALLET, strtolower($claimer_wallet));
		if ($claimer_name !== '') update_post_meta($post_id, self::META_CLAIMER_NAME, $claimer_name);

		if ($burner_wallet) update_post_meta($post_id, self::META_BURNER_WALLET, strtolower($burner_wallet));
		if ($burner_name !== '') update_post_meta($post_id, self::META_BURNER_NAME, $burner_name);

		if ($final_wallet) update_post_meta($post_id, self::META_FINAL_WALLET, strtolower($final_wallet));
		if ($final_name !== '') update_post_meta($post_id, self::META_FINAL_NAME, $final_name);

		// V1 status checkbox
		$v1 = isset($_POST['sbpr_v1_wrapped']) ? '1' : '0';
		update_post_meta($post_id, self::META_V1_WRAPPED, $v1);

		// Generate on-chain punk image if needed
		if ($punk_id_to_generate !== null) {
			self::generate_punk_image($post_id, $punk_id_to_generate);
		}
	}
}

SB_Punks_Registry::init();
register_activation_hook(__FILE__, ['SB_Punks_Registry', 'activate']);
register_deactivation_hook(__FILE__, ['SB_Punks_Registry', 'deactivate']);
