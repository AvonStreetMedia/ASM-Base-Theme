
<?php
/**
 * Schema Meta Box
 *
 * @package ASM_Base_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ASM_Schema_Meta_Box {
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
	}

	public function add_meta_box() {
		add_meta_box(
			'asm_schema_meta',
			'Schema Override',
			array( $this, 'render_meta_box' ),
			'post',
			'side'
		);
	}

	public function render_meta_box( $post ) {
		$custom_schema = get_post_meta( $post->ID, '_asm_custom_schema', true );
		wp_nonce_field( 'asm_schema_meta_nonce', 'asm_schema_meta_nonce' );
		?>
		<textarea name="asm_custom_schema" rows="5" class="widefat"><?php echo esc_textarea( $custom_schema ); ?></textarea>
		<?php
	}

	public function save_meta_box( $post_id ) {
		if ( ! isset( $_POST['asm_schema_meta_nonce'] ) || ! wp_verify_nonce( $_POST['asm_schema_meta_nonce'], 'asm_schema_meta_nonce' ) ) {
			return;
		}

		if ( isset( $_POST['asm_custom_schema'] ) ) {
			update_post_meta( $post_id, '_asm_custom_schema', sanitize_textarea_field( $_POST['asm_custom_schema'] ) );
		}
	}
}

new ASM_Schema_Meta_Box();
