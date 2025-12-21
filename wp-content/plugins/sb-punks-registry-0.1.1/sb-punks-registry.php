<?php
/**
 * Plugin Name: SB Punks Registry
 * Description: CPT + on-chain import + homepage mosaic for BurnedPunks / MuseumPunks.
 * Version: 0.1.1
 * Author: SB
 */

if (!defined('ABSPATH')) exit;

final class SB_Punks_Registry {
	const OPT_KEY = 'sb_punks_registry_options';

	// Contracts (Ethereum mainnet) - provided by site owner.
	const ADDR_V1        = '0x6Ba6f2207e343923BA692e5Cae646Fb0F566DB8D';
	const ADDR_V1_WRAPPER= '0x282BDD42f4eb70e7A9D9F40c8fEA0825B7f68C5D';
	const ADDR_V2        = '0xb47e3cd837ddf8e4c57f05d70ab865de6e193bbb';
	const ADDR_DATA      = '0x16F5A35647D6F03D5D3da7b35409D65ba03aF3B2';

	// Function selectors (keccak256 first 4 bytes)
	const SEL_PUNK_SVG   = '74beb047'; // punkImageSvg(uint16)
	const SEL_PUNK_OWNER = '58178168'; // punkIndexToAddress(uint256)
	const SEL_ERC721_OWNEROF = '6352211e'; // ownerOf(uint256)

	// CPT
	const CPT = 'sb_punk';

	// Meta keys
	const M_PUNK_ID     = '_sb_punk_id';
	const M_MODE        = '_sb_punk_mode'; // burned|museum
	const M_BURN_KIND   = '_sb_burn_kind'; // accidental|intentional
	const M_NOTES       = '_sb_notes';

	const M_SVG         = '_sb_svg';

	const M_V1_CLAIM_BLOCK = '_sb_v1_claim_block';
	const M_V1_CLAIM_TS    = '_sb_v1_claim_ts';
	const M_V1_CLAIM_WALLET= '_sb_v1_claim_wallet';

	const M_V2_BURN_BLOCK  = '_sb_v2_burn_block';
	const M_V2_BURN_TS     = '_sb_v2_burn_ts';
	const M_V2_BURN_FROM   = '_sb_v2_burn_from';
	const M_V2_BURN_TO     = '_sb_v2_burn_to';

	const M_V1_WRAPPED     = '_sb_v1_wrapped';
	const M_V1_WRAPPED_OWNER = '_sb_v1_wrapped_owner';

	// Cached deploy blocks
	const OPT_DEPLOY_BLOCKS = 'sb_punks_registry_deploy_blocks'; // array addr=>block

	// REST namespace
	const REST_NS = 'sbpunks/v1';

	public static function init() : void {
		add_action('init', [__CLASS__, 'register_cpt']);
		add_action('init', [__CLASS__, 'add_rewrites']);
		add_action('admin_menu', [__CLASS__, 'admin_menu']);
		add_action('admin_init', [__CLASS__, 'register_settings']);
		add_action('admin_post_sbpr_migrate_numeric_posts', [__CLASS__, 'handle_migrate_numeric_posts']);

		add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
		add_action('save_post', [__CLASS__, 'save_post'], 10, 2);

		add_action('admin_enqueue_scripts', [__CLASS__, 'admin_assets']);
		add_action('wp_enqueue_scripts', [__CLASS__, 'front_assets']);

		add_shortcode('sb_punks_home', [__CLASS__, 'shortcode_home']);

		add_action('rest_api_init', [__CLASS__, 'register_rest_routes']);

		add_filter('single_template', [__CLASS__, 'single_template']);

		register_activation_hook(__FILE__, [__CLASS__, 'on_activate']);
		register_deactivation_hook(__FILE__, [__CLASS__, 'on_deactivate']);
	}

	/* =========================
	   CPT + Admin UI
	========================= */

	public static function register_cpt() : void {
		$labels = [
			'name'          => 'Punks',
			'singular_name' => 'Punk',
		];

		register_post_type(self::CPT, [
			'labels' => $labels,
			'public' => true,
			'has_archive' => false,
			'menu_icon' => 'dashicons-art',
			'supports' => ['title', 'editor'],
			// IMPORTANT: we do NOT use the root slug rewrite here.
			// We add a numeric-only rewrite rule separately so we never hijack /about/, etc.
			'rewrite' => false,
			'query_var' => 'sb_punk',
			'show_in_rest' => false,
		]);
	}

	public static function add_rewrites() : void {
		// Route ONLY numeric root slugs (e.g. /5449/) to the punk CPT.
		add_rewrite_tag('%sb_punk%', '([0-9]{1,5})');
		add_rewrite_rule('^([0-9]{1,5})/?$', 'index.php?sb_punk=$matches[1]', 'top');
	}

	public static function on_activate() : void {
		// Ensure CPT + rewrite rule are registered before flushing.
		self::register_cpt();
		self::add_rewrites();
		flush_rewrite_rules();
	}

	public static function on_deactivate() : void {
		flush_rewrite_rules();
	}

	public static function admin_menu() : void {
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
			'sanitize_callback' => [__CLASS__, 'sanitize_options'],
			'default' => self::default_options(),
		]);

		add_settings_section('sbpr_main', 'Main', function(){}, 'sb_punks_registry');

		add_settings_field('mode', 'Site mode', [__CLASS__, 'field_mode'], 'sb_punks_registry', 'sbpr_main');
		add_settings_field('logo_default', 'Logo (default)', [__CLASS__, 'field_logo_default'], 'sb_punks_registry', 'sbpr_main');
		add_settings_field('logo_hover', 'Logo (hover)', [__CLASS__, 'field_logo_hover'], 'sb_punks_registry', 'sbpr_main');
		add_settings_field('rpc_urls', 'Ethereum RPC URLs', [__CLASS__, 'field_rpc_urls'], 'sb_punks_registry', 'sbpr_main');
	}

	private static function default_options() : array {
		return [
			'mode' => 'burned', // burned|museum
			'logo_default' => '',
			'logo_hover' => '',
			'rpc_urls' => implode("\n", [
				'https://cloudflare-eth.com',
				'https://ethereum.publicnode.com',
				'https://rpc.ankr.com/eth',
			]),
		];
	}

	public static function sanitize_options($opts) : array {
		$defaults = self::default_options();
		$opts = is_array($opts) ? $opts : [];
		$mode = isset($opts['mode']) ? strtolower(trim((string)$opts['mode'])) : $defaults['mode'];
		if (!in_array($mode, ['burned','museum'], true)) $mode = $defaults['mode'];

		return [
			'mode' => $mode,
			'logo_default' => esc_url_raw((string)($opts['logo_default'] ?? '')),
			'logo_hover' => esc_url_raw((string)($opts['logo_hover'] ?? '')),
			'rpc_urls' => trim((string)($opts['rpc_urls'] ?? $defaults['rpc_urls'])),
		];
	}

	public static function get_options() : array {
		$opts = get_option(self::OPT_KEY, []);
		$defaults = self::default_options();
		return array_merge($defaults, is_array($opts) ? $opts : []);
	}

	public static function field_mode() : void {
		$opts = self::get_options();
		?>
		<select name="<?php echo esc_attr(self::OPT_KEY); ?>[mode]">
			<option value="burned" <?php selected($opts['mode'], 'burned'); ?>>BurnedPunks</option>
			<option value="museum" <?php selected($opts['mode'], 'museum'); ?>>MuseumPunks</option>
		</select>
		<p class="description">Locks behavior + fields so the plugin never mixes "burned" and "museum" data.</p>
		<?php
	}

	public static function field_logo_default() : void {
		$opts = self::get_options();
		printf(
			'<input type="url" class="regular-text" name="%s[logo_default]" value="%s" placeholder="https://.../logo-black.png" />',
			esc_attr(self::OPT_KEY),
			esc_attr($opts['logo_default'])
		);
	}

	public static function field_logo_hover() : void {
		$opts = self::get_options();
		printf(
			'<input type="url" class="regular-text" name="%s[logo_hover]" value="%s" placeholder="https://.../logo-color.png" />',
			esc_attr(self::OPT_KEY),
			esc_attr($opts['logo_hover'])
		);
	}

	public static function field_rpc_urls() : void {
		$opts = self::get_options();
		printf(
			'<textarea name="%s[rpc_urls]" rows="5" class="large-text code" placeholder="%s">%s</textarea>',
			esc_attr(self::OPT_KEY),
			esc_attr("One URL per line.\nExample: https://cloudflare-eth.com"),
			esc_textarea($opts['rpc_urls'])
		);
		?>
		<p class="description">No keys required. The plugin will try them in order and fail over automatically.</p>
		<?php
	}

	public static function render_settings_page() : void {
		$numeric_posts = self::find_numeric_posts();
		$numeric_count = count($numeric_posts);
		?>
		<div class="wrap">
			<h1>SB Punks Registry</h1>
			<?php if (isset($_GET['sbpr_migrated']) || isset($_GET['sbpr_skipped'])): ?>
				<div class="notice notice-success is-dismissible">
					<p>
						Migration complete: <strong><?php echo esc_html((string)($_GET['sbpr_migrated'] ?? '0')); ?></strong> migrated, <strong><?php echo esc_html((string)($_GET['sbpr_skipped'] ?? '0')); ?></strong> skipped.
					</p>
				</div>
			<?php endif; ?>
			<form method="post" action="options.php">
				<?php
				settings_fields('sb_punks_registry');
				do_settings_sections('sb_punks_registry');
				submit_button();
				?>
			</form>
			<hr />
			<h2>Shortcodes</h2>
			<ul>
				<li><code>[sb_punks_home]</code> — homepage layout + mosaic.</li>
			</ul>
			<hr />
			<h2>Tools</h2>
			<p>
				This plugin uses <code>/1234/</code> numeric permalinks for punk pages. If you already have existing WordPress posts with numeric slugs, you should either unpublish/rename them or migrate them into the Punk CPT.
			</p>
			<p>
				<strong><?php echo esc_html((string)$numeric_count); ?></strong> existing non-Punk posts with numeric slugs were found.
			</p>
			<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
				<?php wp_nonce_field('sbpr_migrate_numeric_posts', 'sbpr_migrate_nonce'); ?>
				<input type="hidden" name="action" value="sbpr_migrate_numeric_posts" />
				<?php submit_button('Migrate numeric posts into Punk CPT', 'secondary', 'submit', false); ?>
				<span class="description">Moves matching posts to the Punk CPT and keeps the same <code>/1234/</code> URL.</span>
			</form>
		</div>
		<?php
	}

	public static function handle_migrate_numeric_posts() : void {
		if (!current_user_can('manage_options')) {
			wp_die('Insufficient permissions.');
		}
		if (!isset($_POST['sbpr_migrate_nonce']) || !wp_verify_nonce((string)$_POST['sbpr_migrate_nonce'], 'sbpr_migrate_numeric_posts')) {
			wp_die('Invalid nonce.');
		}

		$opts = self::get_options();
		$rows = self::find_numeric_posts();
		$done = 0;
		$skipped = 0;
		foreach ($rows as $r) {
			$punk_id = (int)$r->post_name;
			if ($punk_id < 0 || $punk_id > 9999) { $skipped++; continue; }

			// If a Punk CPT entry already exists with this slug, don't clobber it.
			$existing = get_page_by_path((string)$punk_id, OBJECT, self::CPT);
			if ($existing && (int)$existing->ID !== (int)$r->ID) {
				$skipped++;
				continue;
			}

			wp_update_post([
				'ID' => (int)$r->ID,
				'post_type' => self::CPT,
				'post_title' => "Punk #{$punk_id}",
				'post_name' => (string)$punk_id,
			]);

			update_post_meta((int)$r->ID, self::M_PUNK_ID, $punk_id);
			update_post_meta((int)$r->ID, self::M_MODE, $opts['mode']);
			$done++;
		}

		flush_rewrite_rules();
		$redirect = add_query_arg([
			'page' => 'sb-punks-registry',
			'sbpr_migrated' => $done,
			'sbpr_skipped' => $skipped,
		], admin_url('options-general.php'));
		wp_safe_redirect($redirect);
		exit;
	}

	private static function find_numeric_posts() : array {
		global $wpdb;
		// Find existing posts/pages (NOT media/attachments) with numeric slugs.
		// These will conflict with /1234/ punk routes.
		$sql = "
			SELECT ID, post_name
			FROM {$wpdb->posts}
			WHERE post_type IN ('post','page')
			AND post_status <> 'trash'
			AND post_name REGEXP '^[0-9]{1,5}$'
		";
		return (array)$wpdb->get_results($sql);
	}

	public static function add_meta_boxes() : void {
		add_meta_box('sbpr_core', 'Punk Details', [__CLASS__, 'metabox_core'], self::CPT, 'normal', 'high');
		add_meta_box('sbpr_onchain', 'On-chain Data (read-only)', [__CLASS__, 'metabox_onchain'], self::CPT, 'normal', 'default');
	}

	public static function metabox_core(\WP_Post $post) : void {
		$opts = self::get_options();
		$punk_id = (int) get_post_meta($post->ID, self::M_PUNK_ID, true);
		$mode = (string) get_post_meta($post->ID, self::M_MODE, true);
		if (!$mode) $mode = $opts['mode'];

		wp_nonce_field('sbpr_save', 'sbpr_nonce');

		?>
		<p>
			<label><strong>Punk #</strong></label><br />
			<input type="number" min="0" max="9999" name="sbpr_punk_id" value="<?php echo esc_attr($punk_id ?: ''); ?>" style="width:120px" />
		</p>

		<input type="hidden" name="sbpr_mode" value="<?php echo esc_attr($opts['mode']); ?>" />

		<?php if ($opts['mode'] === 'burned'): ?>
			<p>
				<label><strong>Burn type</strong></label><br />
				<?php $kind = (string) get_post_meta($post->ID, self::M_BURN_KIND, true); ?>
				<select name="sbpr_burn_kind">
					<option value="intentional" <?php selected($kind, 'intentional'); ?>>Intentional</option>
					<option value="accidental" <?php selected($kind, 'accidental'); ?>>Accidental</option>
				</select>
			</p>
		<?php endif; ?>

		<p>
			<label><strong>Notes</strong></label><br />
			<?php $notes = (string) get_post_meta($post->ID, self::M_NOTES, true); ?>
			<textarea name="sbpr_notes" rows="5" class="widefat" placeholder="Your context / story / links..."><?php echo esc_textarea($notes); ?></textarea>
		</p>

		<p>
			<button type="button" class="button button-primary" id="sbpr-import" data-post-id="<?php echo esc_attr($post->ID); ?>">
				Import / Refresh on-chain data
			</button>
			<span id="sbpr-import-status" style="margin-left:10px;"></span>
		</p>
		<?php
	}

	public static function metabox_onchain(\WP_Post $post) : void {
		$fields = [
			'V1 claimer' => self::M_V1_CLAIM_WALLET,
			'V1 claim date (UTC)' => self::M_V1_CLAIM_TS,
			'V2 burn-from wallet' => self::M_V2_BURN_FROM,
			'V2 burn-to address' => self::M_V2_BURN_TO,
			'Burn date (UTC)' => self::M_V2_BURN_TS,
			'V1 wrapped?' => self::M_V1_WRAPPED,
		];

		echo '<table class="widefat striped"><tbody>';
		foreach ($fields as $label => $key) {
			$val = get_post_meta($post->ID, $key, true);
			if ($key === self::M_V1_WRAPPED) $val = $val ? 'Yes' : 'No';
			if ($key === self::M_V1_CLAIM_TS || $key === self::M_V2_BURN_TS) {
				$val = $val ? gmdate('Y-m-d H:i:s', (int)$val) : '';
			}
			printf('<tr><th style="width:200px">%s</th><td><code>%s</code></td></tr>', esc_html($label), esc_html((string)$val));
		}
		echo '</tbody></table>';

		$punk_id = (int) get_post_meta($post->ID, self::M_PUNK_ID, true);
		if ($punk_id || $punk_id === 0) {
			echo '<p style="margin-top:12px;"><strong>Links</strong></p><ul style="margin-top:0;">';
			printf('<li><a href="%s" target="_blank" rel="noopener">CryptoPunks (details)</a></li>', esc_url(self::link_cryptopunks($punk_id)));
			printf('<li><a href="%s" target="_blank" rel="noopener">OpenSea</a></li>', esc_url(self::link_opensea($punk_id)));
			echo '</ul>';
		}
	}

	public static function save_post(int $post_id, \WP_Post $post) : void {
		if ($post->post_type !== self::CPT) return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!isset($_POST['sbpr_nonce']) || !wp_verify_nonce((string)$_POST['sbpr_nonce'], 'sbpr_save')) return;
		if (!current_user_can('edit_post', $post_id)) return;

		$opts = self::get_options();

		$punk_id = isset($_POST['sbpr_punk_id']) ? (int)$_POST['sbpr_punk_id'] : 0;
		if ($punk_id < 0) $punk_id = 0;
		if ($punk_id > 9999) $punk_id = 9999;

		update_post_meta($post_id, self::M_PUNK_ID, $punk_id);
		update_post_meta($post_id, self::M_MODE, $opts['mode']);

		if ($opts['mode'] === 'burned') {
			$kind = isset($_POST['sbpr_burn_kind']) ? strtolower(trim((string)$_POST['sbpr_burn_kind'])) : 'intentional';
			if (!in_array($kind, ['intentional','accidental'], true)) $kind = 'intentional';
			update_post_meta($post_id, self::M_BURN_KIND, $kind);
		}

		$notes = isset($_POST['sbpr_notes']) ? (string)$_POST['sbpr_notes'] : '';
		update_post_meta($post_id, self::M_NOTES, wp_kses_post($notes));

		// Auto title + slug
		$title = "Punk #{$punk_id}";
		remove_action('save_post', [__CLASS__, 'save_post'], 10);
		wp_update_post([
			'ID' => $post_id,
			'post_title' => $title,
			'post_name' => (string)$punk_id,
		]);
		add_action('save_post', [__CLASS__, 'save_post'], 10, 2);
	}

	/* =========================
	   Assets
	========================= */

	public static function admin_assets(string $hook) : void {
		global $post;
		if (($hook === 'post.php' || $hook === 'post-new.php') && $post && $post->post_type === self::CPT) {
			wp_enqueue_script('sbpr-admin', plugin_dir_url(__FILE__) . 'assets/admin.js', ['jquery'], '0.1.1', true);
			wp_localize_script('sbpr-admin', 'SBPR_ADMIN', [
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('sbpr_import'),
			]);
			add_action('wp_ajax_sbpr_import', [__CLASS__, 'ajax_import']);
		}
	}

	public static function front_assets() : void {
		// Loaded only when shortcode is used.
	}

	/* =========================
	   Shortcode: Home
	========================= */

	public static function shortcode_home($atts = []) : string {
		$opts = self::get_options();

		wp_enqueue_style('sbpr-home', plugin_dir_url(__FILE__) . 'assets/home.css', [], '0.1.1');
		wp_enqueue_script('sbpr-home', plugin_dir_url(__FILE__) . 'assets/home.js', [], '0.1.1', true);

		$punks = self::get_punks_for_mode($opts['mode']);
		$ids = array_map(function($p){ return (int)get_post_meta($p->ID, self::M_PUNK_ID, true); }, $punks);

		wp_localize_script('sbpr-home', 'SBPR_HOME', [
			'mode' => $opts['mode'],
			'logo_default' => $opts['logo_default'],
			'logo_hover' => $opts['logo_hover'],
			'about_url' => site_url('/about/'),
			'ids' => array_values(array_unique($ids)),
			'svg_endpoint' => rest_url(self::REST_NS . '/punk-svg/'), // + {id}
		]);

		ob_start();
		?>
		<div class="sbpr-home">
			<header class="sbpr-home__header">
				<a class="sbpr-logo" href="<?php echo esc_url(site_url('/about/')); ?>" aria-label="About">
					<?php if ($opts['logo_default']): ?>
						<img class="sbpr-logo__img sbpr-logo__img--default" src="<?php echo esc_url($opts['logo_default']); ?>" alt="" />
					<?php else: ?>
						<span class="sbpr-logo__fallback"><?php echo esc_html(get_bloginfo('name')); ?></span>
					<?php endif; ?>

					<?php if ($opts['logo_hover']): ?>
						<img class="sbpr-logo__img sbpr-logo__img--hover" src="<?php echo esc_url($opts['logo_hover']); ?>" alt="" />
					<?php endif; ?>
				</a>
			</header>

			<main class="sbpr-home__mosaic" aria-label="Punk mosaic">
				<div class="sbpr-mosaic" id="sbpr-mosaic">
					<canvas class="sbpr-mosaic__canvas" id="sbpr-canvas" aria-hidden="true"></canvas>
					<div class="sbpr-magnifier" id="sbpr-magnifier" aria-hidden="true">
						<div class="sbpr-magnifier__inner">
							<div class="sbpr-magnifier__img" id="sbpr-magnifier-img"></div>
							<div class="sbpr-magnifier__label" id="sbpr-magnifier-label"></div>
						</div>
					</div>
				</div>
			</main>
		</div>
		<?php
		return (string)ob_get_clean();
	}

	private static function get_punks_for_mode(string $mode) : array {
		return get_posts([
			'post_type' => self::CPT,
			'post_status' => 'publish',
			'posts_per_page' => 2000,
			'orderby' => 'date',
			'order' => 'ASC',
			'meta_query' => [[
				'key' => self::M_MODE,
				'value' => $mode,
				'compare' => '=',
			]],
		]);
	}

	/* =========================
	   REST: SVG
	========================= */

	public static function register_rest_routes() : void {
		register_rest_route(self::REST_NS, '/punk-svg/(?P<id>\d{1,5})', [
			'methods' => 'GET',
			'permission_callback' => '__return_true',
			'callback' => function(\WP_REST_Request $req) {
				$id = (int) $req['id'];
				$svg = self::get_svg_for_punk_id($id);
				if (!$svg) {
					return new \WP_REST_Response(['error' => 'svg_not_found'], 404);
				}
				// Return raw SVG so the browser can render it directly.
				return new \WP_REST_Response($svg, 200, ['Content-Type' => 'image/svg+xml; charset=utf-8']);
			},
		]);
	}

	private static function get_svg_for_punk_id(int $punk_id) : string {
		$post_id = self::find_post_id_by_punk_id($punk_id);
		if (!$post_id) return '';
		$svg = (string) get_post_meta($post_id, self::M_SVG, true);
		if ($svg) return $svg;

		// Lazy fetch on first request, then cache.
		$svg = self::fetch_punk_svg($punk_id);
		if ($svg) update_post_meta($post_id, self::M_SVG, $svg);
		return $svg;
	}

	private static function find_post_id_by_punk_id(int $punk_id) : int {
		$posts = get_posts([
			'post_type' => self::CPT,
			'post_status' => ['publish','draft','private'],
			'posts_per_page' => 1,
			'fields' => 'ids',
			'meta_query' => [[
				'key' => self::M_PUNK_ID,
				'value' => (string)$punk_id,
				'compare' => '=',
			]],
		]);
		return $posts ? (int)$posts[0] : 0;
	}

	/* =========================
	   Template
	========================= */

	public static function single_template($template) {
		if (is_singular(self::CPT)) {
			$t = plugin_dir_path(__FILE__) . 'templates/single-sb_punk.php';
			if (file_exists($t)) return $t;
		}
		return $template;
	}

	/* =========================
	   AJAX Import
	========================= */

	public static function ajax_import() : void {
		if (!current_user_can('edit_posts')) wp_send_json_error(['error' => 'forbidden'], 403);
		check_ajax_referer('sbpr_import', 'nonce');

		$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
		if (!$post_id || get_post_type($post_id) !== self::CPT) wp_send_json_error(['error' => 'bad_post'], 400);

		$opts = self::get_options();
		$punk_id = (int) get_post_meta($post_id, self::M_PUNK_ID, true);

		if ($punk_id < 0 || $punk_id > 9999) wp_send_json_error(['error' => 'bad_punk_id'], 400);

		$errors = [];

		// SVG
		$svg = self::fetch_punk_svg($punk_id);
		if ($svg) update_post_meta($post_id, self::M_SVG, $svg);
		else $errors[] = 'svg';

		// V1 claim (wallet + timestamp)
		$claim = self::get_v1_claim_info($punk_id);
		if ($claim) {
			update_post_meta($post_id, self::M_V1_CLAIM_WALLET, $claim['wallet']);
			update_post_meta($post_id, self::M_V1_CLAIM_BLOCK, (int)$claim['block']);
			update_post_meta($post_id, self::M_V1_CLAIM_TS, (int)$claim['ts']);
		} else {
			$errors[] = 'v1_claim';
		}

		// V2 burn (last transfer to current owner) - derived from owner mapping.
		$burn = self::get_v2_last_transfer_info($punk_id);
		if ($burn) {
			update_post_meta($post_id, self::M_V2_BURN_FROM, $burn['from']);
			update_post_meta($post_id, self::M_V2_BURN_TO, $burn['to']);
			update_post_meta($post_id, self::M_V2_BURN_BLOCK, (int)$burn['block']);
			update_post_meta($post_id, self::M_V2_BURN_TS, (int)$burn['ts']);
		} else {
			$errors[] = 'v2_burn';
		}

		// V1 wrapped?
		$wrapped = self::get_v1_wrap_status($punk_id);
		if ($wrapped !== null) {
			update_post_meta($post_id, self::M_V1_WRAPPED, $wrapped['wrapped'] ? 1 : 0);
			update_post_meta($post_id, self::M_V1_WRAPPED_OWNER, $wrapped['owner'] ?? '');
		} else {
			$errors[] = 'v1_wrapped';
		}

		wp_send_json_success([
			'ok' => true,
			'punk_id' => $punk_id,
			'errors' => $errors,
		]);
	}

	/* =========================
	   Ethereum RPC helpers
	========================= */

	private static function rpc_urls() : array {
		$opts = self::get_options();
		$lines = preg_split('/\r\n|\r|\n/', (string)$opts['rpc_urls']);
		$urls = [];
		foreach ($lines as $l) {
			$l = trim($l);
			if (!$l) continue;
			if (!preg_match('#^https?://#i', $l)) continue;
			$urls[] = $l;
		}
		return $urls ?: ['https://cloudflare-eth.com'];
	}

	private static function rpc(string $method, array $params) : array {
		$payload = [
			'jsonrpc' => '2.0',
			'id' => 1,
			'method' => $method,
			'params' => $params,
		];

		$last_err = '';
		foreach (self::rpc_urls() as $url) {
			$res = wp_remote_post($url, [
				'timeout' => 15,
				'headers' => ['Content-Type' => 'application/json'],
				'body' => wp_json_encode($payload),
			]);

			if (is_wp_error($res)) {
				$last_err = $res->get_error_message();
				continue;
			}

			$code = wp_remote_retrieve_response_code($res);
			$body = wp_remote_retrieve_body($res);
			$data = json_decode($body, true);

			if ($code >= 200 && $code < 300 && is_array($data)) {
				if (isset($data['error'])) {
					// Some endpoints return an error for certain calls; try next.
					$last_err = is_array($data['error']) ? (string)($data['error']['message'] ?? 'rpc_error') : 'rpc_error';
					continue;
				}
				return ['ok' => true, 'result' => $data['result'] ?? null];
			}
			$last_err = "HTTP {$code}";
		}

		return ['ok' => false, 'error' => $last_err ?: 'rpc_failed'];
	}

	private static function hex_u256(int $n) : string {
		if ($n < 0) $n = 0;
		return '0x' . dechex($n);
	}

	private static function abi_encode_u256(int $n) : string {
		$hex = dechex(max(0, $n));
		return str_pad($hex, 64, '0', STR_PAD_LEFT);
	}

	private static function abi_encode_u16(int $n) : string {
		// ABI still uses 32-byte words; we just clamp to uint16.
		$n = max(0, min(65535, $n));
		$hex = dechex($n);
		return str_pad($hex, 64, '0', STR_PAD_LEFT);
	}

	private static function parse_addr_from_abi(?string $hex32) : string {
		if (!$hex32 || !is_string($hex32)) return '';
		$hex32 = strtolower(preg_replace('/^0x/', '', $hex32));
		if (strlen($hex32) < 40) return '';
		return '0x' . substr($hex32, -40);
	}

	private static function eth_block_number() : int {
		$r = self::rpc('eth_blockNumber', []);
		if (!$r['ok']) return 0;
		return (int) hexdec((string)$r['result']);
	}

	private static function eth_get_block_ts(int $block) : int {
		$r = self::rpc('eth_getBlockByNumber', [self::hex_u256($block), false]);
		if (!$r['ok'] || !is_array($r['result'])) return 0;
		$ts_hex = (string)($r['result']['timestamp'] ?? '0x0');
		return (int) hexdec($ts_hex);
	}

	private static function eth_get_code(string $addr, int $block) : string {
		$r = self::rpc('eth_getCode', [$addr, self::hex_u256($block)]);
		if (!$r['ok']) return '';
		return (string)$r['result'];
	}

	private static function eth_call(string $to, string $data, $blockTag = 'latest') : ?string {
		$tag = is_int($blockTag) ? self::hex_u256($blockTag) : (string)$blockTag;
		$r = self::rpc('eth_call', [[
			'to' => $to,
			'data' => $data,
		], $tag]);

		if (!$r['ok']) return null;
		return is_string($r['result']) ? $r['result'] : null;
	}

	private static function get_deploy_block(string $addr) : int {
		$cache = get_option(self::OPT_DEPLOY_BLOCKS, []);
		if (is_array($cache) && isset($cache[$addr])) return (int)$cache[$addr];

		$latest = self::eth_block_number();
		if ($latest <= 0) return 0;

		// Binary search for first block where code exists.
		$low = 0;
		$high = $latest;
		while ($low < $high) {
			$mid = intdiv($low + $high, 2);
			$code = self::eth_get_code($addr, $mid);
			$has = is_string($code) && strlen($code) > 2 && $code !== '0x';
			if ($has) $high = $mid;
			else $low = $mid + 1;
		}

		$deploy = $low;
		if (!is_array($cache)) $cache = [];
		$cache[$addr] = $deploy;
		update_option(self::OPT_DEPLOY_BLOCKS, $cache, false);

		return $deploy;
	}

	/* =========================
	   On-chain fetches
	========================= */

	private static function fetch_punk_svg(int $punk_id) : string {
		// function punkImageSvg(uint16 punkIndex) returns (string)
		$data = '0x' . self::SEL_PUNK_SVG . self::abi_encode_u16($punk_id);
		$out = self::eth_call(self::ADDR_DATA, $data, 'latest');
		if (!$out) return '';

		$hex = preg_replace('/^0x/', '', $out);
		if (!$hex) return '';

		// ABI dynamic string decode.
		// [0:32] offset, [offset:offset+32] length, [data...]
		try {
			$offset = hexdec(substr($hex, 0, 64));
			$len = hexdec(substr($hex, $offset*2, 64));
			$start = ($offset + 32) * 2;
			$str_hex = substr($hex, $start, $len * 2);
			$bin = hex2bin($str_hex);
			return $bin !== false ? $bin : '';
		} catch (\Throwable $e) {
			return '';
		}
	}

	private static function v1_owner_at(int $punk_id, $blockTag) : string {
		$data = '0x' . self::SEL_PUNK_OWNER . self::abi_encode_u256($punk_id);
		$out = self::eth_call(self::ADDR_V1, $data, $blockTag);
		return self::parse_addr_from_abi($out);
	}

	private static function v2_owner_at(int $punk_id, $blockTag) : string {
		$data = '0x' . self::SEL_PUNK_OWNER . self::abi_encode_u256($punk_id);
		$out = self::eth_call(self::ADDR_V2, $data, $blockTag);
		return self::parse_addr_from_abi($out);
	}

	private static function get_v1_claim_info(int $punk_id) : ?array {
		$latest = self::eth_block_number();
		if ($latest <= 0) return null;

		$deploy = self::get_deploy_block(self::ADDR_V1);
		if ($deploy <= 0) return null;

		// Binary search first block where owner != 0x0.
		$low = $deploy;
		$high = $latest;

		$owner_latest = self::v1_owner_at($punk_id, 'latest');
		if (!$owner_latest || $owner_latest === '0x0000000000000000000000000000000000000000') return null;

		$zero = '0x0000000000000000000000000000000000000000';

		while ($low < $high) {
			$mid = intdiv($low + $high, 2);
			$o = self::v1_owner_at($punk_id, $mid);
			$claimed = ($o && strtolower($o) !== $zero);
			if ($claimed) $high = $mid;
			else $low = $mid + 1;
		}

		$claim_block = $low;
		$wallet = self::v1_owner_at($punk_id, $claim_block);
		if (!$wallet) return null;
		$ts = self::eth_get_block_ts($claim_block);

		return [
			'block' => $claim_block,
			'ts' => $ts,
			'wallet' => strtolower($wallet),
		];
	}

	private static function get_v2_last_transfer_info(int $punk_id) : ?array {
		$latest = self::eth_block_number();
		if ($latest <= 0) return null;

		$deploy = self::get_deploy_block(self::ADDR_V2);
		if ($deploy <= 0) return null;

		$current = self::v2_owner_at($punk_id, 'latest');
		if (!$current) return null;

		// Find a block where owner != current by stepping backwards (exponential backoff).
		$step = 1;
		$low = $latest;
		$zero = '0x0000000000000000000000000000000000000000';

		while (true) {
			$probe = $latest - $step;
			if ($probe <= $deploy) {
				$probe = $deploy;
			}
			$o = self::v2_owner_at($punk_id, $probe);
			if (!$o) return null;

			if (strtolower($o) !== strtolower($current)) {
				$low = $probe;
				break;
			}

			if ($probe === $deploy) {
				// Never changed owner (unlikely for burned punks).
				return null;
			}

			$step *= 2;
			if ($step > $latest) $step = $latest;
		}

		// Now we have: owner(low) != current, owner(latest) == current.
		// Binary search for the first block after low where owner == current, assuming no re-entry oscillation.
		$lo = $low;
		$hi = $latest;

		while ($lo + 1 < $hi) {
			$mid = intdiv($lo + $hi, 2);
			$o = self::v2_owner_at($punk_id, $mid);
			if (!$o) return null;

			if (strtolower($o) === strtolower($current)) {
				$hi = $mid;
			} else {
				$lo = $mid;
			}
		}

		$burn_block = $hi;
		$to = strtolower($current);
		$from = self::v2_owner_at($punk_id, $burn_block - 1);
		$from = $from ? strtolower($from) : $zero;
		$ts = self::eth_get_block_ts($burn_block);

		return [
			'block' => $burn_block,
			'ts' => $ts,
			'from' => $from,
			'to' => $to,
		];
	}

	private static function get_v1_wrap_status(int $punk_id) : ?array {
		$data = '0x' . self::SEL_ERC721_OWNEROF . self::abi_encode_u256($punk_id);
		$out = self::eth_call(self::ADDR_V1_WRAPPER, $data, 'latest');

		// If the token isn't wrapped, many RPCs return an eth_call error, which we treat as null.
		if (!$out) {
			return ['wrapped' => false];
		}

		$owner = self::parse_addr_from_abi($out);
		if (!$owner || $owner === '0x0000000000000000000000000000000000000000') {
			return ['wrapped' => false];
		}
		return ['wrapped' => true, 'owner' => strtolower($owner)];
	}

	/* =========================
	   Links
	========================= */

	private static function link_cryptopunks(int $punk_id) : string {
		// Stable public UI for details pages.
		return "https://www.cryptopunks.app/cryptopunks/details/{$punk_id}";
	}

	private static function link_opensea(int $punk_id) : string {
		return "https://opensea.io/item/ethereum/" . self::ADDR_V2 . "/{$punk_id}";
	}
}

SB_Punks_Registry::init();
