<?php
/**
 * Schema Validator
 *
 * @package ASM_Base_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ASM_Schema_Validator {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_validator_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_ajax_validate_schema', array( $this, 'ajax_validate_schema' ) );
	}

	public function add_validator_page() {
		add_submenu_page(
			'tools.php',
			'Schema Validator',
			'Schema Validator',
			'manage_options',
			'asm-schema-validator',
			array( $this, 'render_validator_page' )
		);
	}

	public function enqueue_admin_scripts( $hook ) {
		if ( $hook !== 'tools_page_asm-schema-validator' ) {
			return;
		}

		wp_enqueue_script(
			'asm-schema-validator',
			get_template_directory_uri() . '/assets/js/schema-validator.js',
			array( 'jquery' ),
			'1.0',
			true
		);
	}

	public function render_validator_page() {
		?>
		<div class="wrap">
			<h1>Schema Validator</h1>
			<p>Validate your schema markup with Google’s Rich Results API.</p>
			<input type="text" id="schema-test-url" class="regular-text" placeholder="https://example.com/post-url" />
			<button id="validate-schema-btn" class="button button-primary">Validate</button>
			<div id="schema-validation-results" style="display:none;">
				<h2>Validation Results</h2>
				<pre id="schema-response"></pre>
			</div>
		</div>
		<?php
	}

	public function ajax_validate_schema() {
		check_ajax_referer( 'schema_validator_nonce', 'nonce' );

		$url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';
		if ( empty( $url ) ) {
			wp_send_json_error( array( 'message' => 'Invalid URL provided.' ) );
		}

		$api_url = 'https://search.google.com/test/rich-results?url=' . urlencode( $url );
		$response = wp_remote_get( $api_url, array( 'timeout' => 15 ) );
		$body     = wp_remote_retrieve_body( $response );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => 'Failed to connect to Google API.' ) );
		}

		wp_send_json_success( array( 'response' => json_decode( $body, true ) ) );
	}
}

new ASM_Schema_Validator();
