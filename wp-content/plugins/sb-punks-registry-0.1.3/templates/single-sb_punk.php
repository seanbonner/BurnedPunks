<?php
get_header();

the_post();
$post_id = get_the_ID();

$punk_id   = (int) get_post_meta($post_id, '_sbpr_punk_id', true);
$svg       = (string) get_post_meta($post_id, '_sbpr_svg', true);

$v1_wallet = (string) get_post_meta($post_id, '_sbpr_v1_claim_wallet', true);
$v1_ts     = (int) get_post_meta($post_id, '_sbpr_v1_claim_ts', true);

$burn_from = (string) get_post_meta($post_id, '_sbpr_v2_burn_from', true);
$burn_to   = (string) get_post_meta($post_id, '_sbpr_v2_burn_to', true);
$burn_ts   = (int) get_post_meta($post_id, '_sbpr_v2_burn_ts', true);

$wrapped = (bool) get_post_meta($post_id, '_sbpr_v1_wrapped', true);
$wrapped_owner = (string) get_post_meta($post_id, '_sbpr_v1_wrapped_owner', true);

$notes = (string) get_post_meta($post_id, '_sbpr_notes', true);

$has_onchain = (bool) ($svg || $v1_wallet || $v1_ts || $burn_from || $burn_to || $burn_ts || $wrapped_owner);

// If we don't have any imported on-chain data yet, don't inject an empty/un-styled box
// on the public site. Just show your written post content.
if (!$has_onchain && !current_user_can('edit_post', $post_id)) :
	?>
	<main id="primary" class="site-main" style="max-width:820px;margin:0 auto;padding:48px 24px;">
		<h1 style="margin:0 0 18px;"><?php the_title(); ?></h1>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</main>
	<?php
	get_footer();
	return;
endif;

function sbpr_fmt_addr($a) {
	$a = strtolower(trim((string)$a));
	if (!$a) return '';
	if (!preg_match('/^0x[a-f0-9]{40}$/', $a)) return esc_html($a);
	return esc_html(substr($a, 0, 6) . '…' . substr($a, -4));
}

function sbpr_fmt_date($ts) {
	$ts = (int)$ts;
	if ($ts <= 0) return '';
	return esc_html(gmdate('Y-m-d', $ts));
}
?>

<style>
	.sbpr-wrap{max-width:1100px;margin:0 auto;padding:48px 24px;}
	.sbpr-top{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,340px);gap:28px;align-items:start;}
	.sbpr-card{border:1px solid rgba(0,0,0,0.12);border-radius:14px;padding:18px 18px 16px;background:#fff;}
	.sbpr-svg{width:100%;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;}
	.sbpr-svg svg{width:100%;height:100%;}
	.sbpr-kv{display:grid;grid-template-columns:1fr;gap:10px;}
	.sbpr-kv .row{display:flex;justify-content:space-between;gap:12px;font-size:14px;}
	.sbpr-kv .k{opacity:.7;}
	.sbpr-kv .v{font-family:ui-monospace,monospace;}
	.sbpr-body{max-width:820px;margin:28px 0 0;}
	@media (max-width: 860px){
		.sbpr-top{grid-template-columns:1fr;}
	}
</style>

<main id="primary" class="site-main sbpr-wrap">
	<h1 style="margin:0 0 18px;"><?php the_title(); ?></h1>

	<div class="sbpr-top">
		<div class="sbpr-body">
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<?php if ($notes): ?>
				<div class="sbpr-card" style="margin-top:18px;">
					<div style="opacity:.7;margin-bottom:8px;">Notes</div>
					<div><?php echo wp_kses_post($notes); ?></div>
				</div>
			<?php endif; ?>
		</div>

		<div class="sbpr-card">
			<div class="sbpr-svg">
				<?php if ($svg): ?>
					<?php echo $svg; // already sanitized on save via admin only ?>
				<?php else: ?>
					<div style="opacity:.7;">No SVG cached yet.</div>
				<?php endif; ?>
			</div>

			<div class="sbpr-kv" style="margin-top:14px;">
				<?php if ($punk_id || $punk_id === 0): ?>
					<div class="row"><div class="k">Punk</div><div class="v"><?php echo esc_html('#' . $punk_id); ?></div></div>
				<?php endif; ?>

				<?php if ($v1_wallet || $v1_ts): ?>
					<div class="row"><div class="k">Claimed (V1)</div><div class="v"><?php echo sbpr_fmt_date($v1_ts) ?: '—'; ?></div></div>
					<?php if ($v1_wallet): ?>
						<div class="row"><div class="k">Claimer</div><div class="v"><?php echo sbpr_fmt_addr($v1_wallet); ?></div></div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ($burn_from || $burn_to || $burn_ts): ?>
					<div class="row"><div class="k">Burn (V2)</div><div class="v"><?php echo sbpr_fmt_date($burn_ts) ?: '—'; ?></div></div>
					<?php if ($burn_from): ?>
						<div class="row"><div class="k">From</div><div class="v"><?php echo sbpr_fmt_addr($burn_from); ?></div></div>
					<?php endif; ?>
					<?php if ($burn_to): ?>
						<div class="row"><div class="k">To</div><div class="v"><?php echo sbpr_fmt_addr($burn_to); ?></div></div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ($wrapped || $wrapped_owner): ?>
					<div class="row"><div class="k">V1 Wrapped</div><div class="v"><?php echo $wrapped ? 'Yes' : 'No'; ?></div></div>
					<?php if ($wrapped_owner): ?>
						<div class="row"><div class="k">Owner</div><div class="v"><?php echo sbpr_fmt_addr($wrapped_owner); ?></div></div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if (current_user_can('edit_post', $post_id) && !$has_onchain): ?>
					<div style="margin-top:10px;opacity:.7;font-size:13px;">Admin: click “Import on-chain data” in the meta box to cache the SVG.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php get_footer();
