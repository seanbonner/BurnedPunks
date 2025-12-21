<?php
/**
 * Single Punk template (CPT: sb_punk)
 */
get_header();

the_post();

$post_id = get_the_ID();

$punk_id = (int) get_post_meta($post_id, '_sb_punk_id', true);
$svg = (string) get_post_meta($post_id, '_sb_svg', true);

$notes = (string) get_post_meta($post_id, '_sb_notes', true);
$burn_kind = (string) get_post_meta($post_id, '_sb_burn_kind', true);

$v1_wallet = (string) get_post_meta($post_id, '_sb_v1_claim_wallet', true);
$v1_ts = (int) get_post_meta($post_id, '_sb_v1_claim_ts', true);

$burn_from = (string) get_post_meta($post_id, '_sb_v2_burn_from', true);
$burn_to = (string) get_post_meta($post_id, '_sb_v2_burn_to', true);
$burn_ts = (int) get_post_meta($post_id, '_sb_v2_burn_ts', true);

$wrapped = (int) get_post_meta($post_id, '_sb_v1_wrapped', true);
$wrapped_owner = (string) get_post_meta($post_id, '_sb_v1_wrapped_owner', true);

function sbpr_fmt_ts($ts){
	return $ts ? gmdate('Y-m-d H:i:s', (int)$ts) . ' UTC' : '';
}

$cp_url = "https://www.cryptopunks.app/cryptopunks/details/{$punk_id}";
$os_url = "https://opensea.io/item/ethereum/0xb47e3cd837ddf8e4c57f05d70ab865de6e193bbb/{$punk_id}";
?>
<main id="primary" class="site-main" style="max-width:980px;margin:0 auto;padding:48px 24px;">
	<header style="margin-bottom:24px;">
		<h1 style="margin:0;">Punk #<?php echo esc_html($punk_id); ?></h1>
	</header>

	<div style="display:grid;grid-template-columns: 220px 1fr; gap:32px; align-items:start;">
		<div>
			<div style="width:220px;height:220px;border-radius:12px;border:1px solid rgba(0,0,0,0.12);display:flex;align-items:center;justify-content:center;background:#fff;">
				<?php
				if ($svg) {
					// Render the SVG, but force pixelated scaling.
					echo '<div style="width:200px;height:200px;image-rendering:pixelated;">' . $svg . '</div>';
				} else {
					echo '<div style="font:12px/1.4 ui-monospace,monospace;color:#666;">(no SVG cached yet)</div>';
				}
				?>
			</div>

			<p style="margin-top:14px;">
				<a href="<?php echo esc_url($cp_url); ?>" target="_blank" rel="noopener">CryptoPunks details</a><br />
				<a href="<?php echo esc_url($os_url); ?>" target="_blank" rel="noopener">OpenSea</a>
			</p>
		</div>

		<div>
			<?php if ($burn_kind): ?>
				<p><strong>Burn:</strong> <?php echo esc_html(ucfirst($burn_kind)); ?></p>
			<?php endif; ?>

			<table style="width:100%;border-collapse:collapse;">
				<tbody>
					<tr>
						<th style="text-align:left;padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);width:220px;">V1 claimer</th>
						<td style="padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);"><code><?php echo esc_html($v1_wallet); ?></code></td>
					</tr>
					<tr>
						<th style="text-align:left;padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);">V1 claim date</th>
						<td style="padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);"><?php echo esc_html(sbpr_fmt_ts($v1_ts)); ?></td>
					</tr>
					<tr>
						<th style="text-align:left;padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);">Burned by (last owner)</th>
						<td style="padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);"><code><?php echo esc_html($burn_from); ?></code></td>
					</tr>
					<tr>
						<th style="text-align:left;padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);">Burn destination</th>
						<td style="padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);"><code><?php echo esc_html($burn_to); ?></code></td>
					</tr>
					<tr>
						<th style="text-align:left;padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);">Burn date</th>
						<td style="padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);"><?php echo esc_html(sbpr_fmt_ts($burn_ts)); ?></td>
					</tr>
					<tr>
						<th style="text-align:left;padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);">V1 wrapped?</th>
						<td style="padding:10px 0;border-bottom:1px solid rgba(0,0,0,0.1);">
							<?php echo $wrapped ? 'Yes' : 'No'; ?>
							<?php if ($wrapped && $wrapped_owner): ?>
								<br /><small>Owner: <code><?php echo esc_html($wrapped_owner); ?></code></small>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>

			<?php if ($notes): ?>
				<div style="margin-top:24px;">
					<h2 style="margin:0 0 10px;">Notes</h2>
					<div><?php echo wpautop(wp_kses_post($notes)); ?></div>
				</div>
			<?php endif; ?>

			<div style="margin-top:30px;">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>