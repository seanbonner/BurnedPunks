<?php

defined( 'ABSPATH' ) || exit;

class Minimalio_Announcement_Control extends WP_Customize_Control {
	public $type = 'announcement';
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
			<span class="description customize-control-description" style="color: red;"><?php echo $this->description; ?></span>
			<?php
		endif;
		if ( ! empty( $this->input_attrs['message'] ) ) :
			?>
			<span class="description customize-control-description" style="color: red;"><?php echo $this->input_attrs['message']; ?></span>
			<?php
		endif;
		if ( ! empty( $this->input_attrs['link'] ) ) :
			?>
			<a href="<?php echo $this->input_attrs['link']; ?>" target="_blank" class="description customize-control-description"><?php echo $this->input_attrs['link']; ?></a>
			<?php
		endif;
		if ( ! empty( $this->input_attrs['image'] ) ) :
			?>
			<span class="description customize-control-description"><?php echo ( '<img src = "' . get_template_directory_uri() . '/assets/dist/images/' . $this->input_attrs['image'] . '" style="filter: opacity(0.8);">' ); ?></span>
			<?php
		endif;
		?>
		</label>
		<?php
	}
}
