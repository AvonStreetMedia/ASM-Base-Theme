<?php
/**
 * Schema Settings Panel
 *
 * @package ASM_Base_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ASM_Schema_Settings {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_settings_page() {
		add_theme_page(
			'Schema Settings',
			'Schema Settings',
			'manage_options',
			'asm-schema-settings',
			array( $this, 'settings_page_html' )
		);
	}

	public function register_settings() {
		register_setting( 'asm_schema_settings', 'asm_enabled_schemas' );
	}

	public function settings_page_html() {
		$enabled_schemas = get_option( 'asm_enabled_schemas', [] );
		?>
		<div class="wrap">
			<h1>Schema Settings</h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'asm_schema_settings' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">Enable Schema Types</th>
						<td>
							<label><input type="checkbox" name="asm_enabled_schemas[]" value="article" <?php checked( in_array( 'article', $enabled_schemas ) ); ?>> Article</label><br>
							<label><input type="checkbox" name="asm_enabled_schemas[]" value="website" <?php checked( in_array( 'website', $enabled_schemas ) ); ?>> Website</label><br>
							<label><input type="checkbox" name="asm_enabled_schemas[]" value="product" <?php checked( in_array( 'product', $enabled_schemas ) ); ?>> Product</label><br>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

new ASM_Schema_Settings();
