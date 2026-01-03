<?php
/**
 * Single Punk template (sb_punk)
 */
if (!defined('ABSPATH')) exit;

get_header();

the_post();
$post_id = get_the_ID();

$punk_num = get_the_title($post_id);
$punk_num_clean = preg_replace('/[^0-9]/', '', (string)$punk_num);

$intent = (string)get_post_meta($post_id, SB_Punks_Registry::META_INTENT, true);
$burn_date = (string)get_post_meta($post_id, SB_Punks_Registry::META_BURN_DATE, true);

$claimer_wallet = (string)get_post_meta($post_id, SB_Punks_Registry::META_CLAIMER_WALLET, true);
$claimer_name   = (string)get_post_meta($post_id, SB_Punks_Registry::META_CLAIMER_NAME, true);
$claim_date     = (string)get_post_meta($post_id, SB_Punks_Registry::META_CLAIM_DATE, true);
$burner_wallet  = (string)get_post_meta($post_id, SB_Punks_Registry::META_BURNER_WALLET, true);
$burner_name    = (string)get_post_meta($post_id, SB_Punks_Registry::META_BURNER_NAME, true);
$final_wallet   = (string)get_post_meta($post_id, SB_Punks_Registry::META_FINAL_WALLET, true);
$final_name     = (string)get_post_meta($post_id, SB_Punks_Registry::META_FINAL_NAME, true);
$v1_wrapped     = (string)get_post_meta($post_id, SB_Punks_Registry::META_V1_WRAPPED, true);

$label_burn = '';
if ($intent === 'accidental') $label_burn = 'ACCIDENTAL BURN';
if ($intent === 'intentional') $label_burn = 'INTENTIONAL BURN';
if (!$label_burn) $label_burn = 'BURN';

$burn_human = '';
if ($burn_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $burn_date)) {
	$ts = strtotime($burn_date . ' 12:00:00');
	if ($ts) $burn_human = date_i18n('F j, Y', $ts);
}

$img_url = '';
if (has_post_thumbnail($post_id)) {
	$img_url = (string)get_the_post_thumbnail_url($post_id, 'large');
} else {
	// Try first image in content as fallback.
	$content_raw = (string)get_post_field('post_content', $post_id);
	if (preg_match('/<img[^>]+src="([^"]+)"/i', $content_raw, $m)) {
		$img_url = $m[1];
	}
}

$story_html = SB_Punks_Registry::extract_story_html((string)get_post_field('post_content', $post_id));

function sbpr_wallet_link($wallet, $name = '') {
	$wallet = trim((string)$wallet);
	if (!$wallet) return '';
	$url = SB_Punks_Registry::cp_account_url($wallet);

	// If there's a name, just show the name (no truncation needed)
	if ($name) {
		return '<a href="'.esc_url($url).'">'.esc_html($name).'</a>';
	}

	// For wallet addresses, show full on desktop, truncated on mobile
	$short = strlen($wallet) > 10
		? substr($wallet, 0, 6) . '…' . substr($wallet, -4)
		: $wallet;

	return '<a href="'.esc_url($url).'" class="sbpr-wallet-addr">'
		. '<span class="sbpr-wallet-full">'.esc_html($wallet).'</span>'
		. '<span class="sbpr-wallet-short">'.esc_html($short).'</span>'
		. '</a>';
}

?>
<main id="primary" class="site-main sbpr-single__main">
	<div class="sbpr-single__wrap">
		<div class="sbpr-single__media">
			<?php if ($img_url): ?>
				<a class="sbpr-single__imglink" href="<?php echo SB_Punks_Registry::cp_details_url($punk_num_clean); ?>" aria-label="View on CryptoPunks">
					<img class="sbpr-single__img" src="<?php echo esc_url($img_url); ?>" alt="" decoding="async" loading="eager" />
				</a>
			<?php endif; ?>
		</div>

		<div class="sbpr-single__content">
			<h1 class="sbpr-single__num"><?php echo esc_html($punk_num_clean); ?></h1>
			<div class="sbpr-single__label"><?php echo esc_html($label_burn); ?></div>

			<dl class="sbpr-single__facts">
				<?php if ($claimer_wallet): ?>
					<div class="sbpr-single__fact">
						<dt>Claimer:</dt>
						<dd><?php echo sbpr_wallet_link($claimer_wallet, $claimer_name); ?></dd>
					</div>
				<?php endif; ?>

				<?php if ($claim_date): ?>
					<div class="sbpr-single__fact">
						<dt>Claimed:</dt>
						<dd>June <?php echo esc_html($claim_date); ?>, 2017</dd>
					</div>
				<?php endif; ?>

				<?php if ($burner_wallet): ?>
					<div class="sbpr-single__fact">
						<dt>Burner:</dt>
						<dd><?php echo sbpr_wallet_link($burner_wallet, $burner_name); ?></dd>
					</div>
				<?php endif; ?>

				<?php if ($burn_human): ?>
					<div class="sbpr-single__fact">
						<dt>Burned:</dt>
						<dd class="sbpr-single__burned"><?php echo esc_html($burn_human); ?></dd>
					</div>
				<?php endif; ?>

				<?php if ($final_wallet): ?>
					<div class="sbpr-single__fact">
						<dt>Final Location:</dt>
						<dd><?php echo sbpr_wallet_link($final_wallet, $final_name); ?></dd>
					</div>
				<?php endif; ?>

				<div class="sbpr-single__fact">
					<dt>V1:</dt>
					<dd>
						<?php if ($v1_wrapped === '1'): ?>
							<a href="<?php echo SB_Punks_Registry::os_wrapped_url($punk_num_clean); ?>">Wrapped</a>
						<?php else: ?>
							Unwrapped
						<?php endif; ?>
					</dd>
				</div>
			</dl></div>

		<div class="sbpr-single__story sbpr-single__story--full">
			<?php echo wp_kses_post($story_html); ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
