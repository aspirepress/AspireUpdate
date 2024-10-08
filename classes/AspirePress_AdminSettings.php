<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * This Class Implements the Aspire Press Admin Settings Page and functions to access Settings Values
 */
class AspirePress_AdminSettings {

	private $option_group = 'aspirepress_settings';
	private $option_name  = 'aspirepress_settings';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function register_admin_menu() {
		add_options_page(
			'AspirePress',
			'AspirePress',
			'manage_options',
			'aspirepress-settings',
			array( $this, 'the_settings_page' )
		);
	}

	public function admin_enqueue_scripts( $hook ) {
		if ( 'settings_page_aspirepress-settings' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'aspirepress_settings_css', plugin_dir_url( __DIR__ ) . 'assets/css/aspirepress.css', array(), '1.0' );
		wp_enqueue_script( 'aspirepress_settings_js', plugin_dir_url( __DIR__ ) . 'assets/js/aspirepress.js', array( 'jquery' ), '1.0', true );
	}

	public function the_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'AspirePress Settings', 'aspirepress' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( $this->option_group );
				do_settings_sections( 'aspirepress-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function register_settings() {
		register_setting(
			$this->option_group,
			$this->option_name,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		add_settings_section(
			'aspirepress_settings_section',
			__( 'API Configuration', 'aspirepress' ),
			null,
			'aspirepress-settings'
		);

		add_settings_field(
			'enable',
			'Enable AspirePress API Rewrites',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_settings_section',
			array(
				'id'   => 'enable',
				'type' => 'checkbox',
			)
		);

		add_settings_field(
			'api_key',
			__( 'API Key', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_settings_section',
			array(
				'id'          => 'api_key',
				'type'        => 'text',
				'description' => __( 'Provides an API key for repositories that may require authentication.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'api_host',
			__( 'API Host', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_settings_section',
			array(
				'id'          => 'api_host',
				'type'        => 'text',
				'description' => __( 'The Domain for your new API Host.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'rewrite_wporg_api',
			__( 'Override Default API', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_settings_section',
			array(
				'id'          => 'rewrite_wporg_api',
				'type'        => 'checkbox',
				'description' => __( 'Overrides the built-in WordPress API rewrite rules. Must be configured with "API URL".', 'aspirepress' ),
			)
		);

		add_settings_field(
			'api_url',
			__( 'API URL', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_settings_section',
			array(
				'id'          => 'api_url',
				'type'        => 'text',
				'description' => __( 'The URL to use for the third-party plugin API.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'rewrite_wporg_dl',
			__( 'Override Default Downloads', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_settings_section',
			array(
				'id'          => 'rewrite_wporg_dl',
				'type'        => 'checkbox',
				'description' => __( 'Overrides the built-in WordPress download rewrite rules. Must be configured with "API Download URL".', 'aspirepress' ),
			)
		);

		add_settings_field(
			'api_download_url',
			__( 'API Download URL', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_settings_section',
			array(
				'id'          => 'api_download_url',
				'type'        => 'text',
				'description' => __( 'The URL to use for the third-party plugin download API.', 'aspirepress' ),
			)
		);

		add_settings_section(
			'aspirepress_debug_settings_section',
			'API Debug Configuration',
			null,
			'aspirepress-settings'
		);

		add_settings_field(
			'enable_debug',
			'Enable Debug Mode',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'enable_debug',
				'type'        => 'checkbox',
				'description' => 'Enables debug mode for the plugin.',
			)
		);

		add_settings_field(
			'enable_debug_type_request',
			'Enable Debug Type - Request',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'enable_debug_type_request',
				'type'        => 'checkbox',
				'description' => 'Outputs the request URL and headers.',
			)
		);

		add_settings_field(
			'enable_debug_type_response',
			'Enable Debug Type - Response',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'enable_debug_type_response',
				'type'        => 'checkbox',
				'description' => 'Outputs the response headers and body.',
			)
		);

		add_settings_field(
			'enable_debug_type_string',
			'Enable Debug Type - String',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'enable_debug_type_string',
				'type'        => 'checkbox',
				'description' => 'Outputs the string that is being rewritten.',
			)
		);

		add_settings_field(
			'exclude_debug_type_request',
			'Exclude Debug Type - Request',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'exclude_debug_type_request',
				'type'        => 'checkbox',
				'description' => 'Defines requests you DON\'T WANT displayed. This runs AFTER the Enable Debug Types does, so it will remove anything you previously added if both are defined.',
			)
		);

		add_settings_field(
			'exclude_debug_type_response',
			'Exclude Debug Type - Response',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'exclude_debug_type_response',
				'type'        => 'checkbox',
				'description' => 'Defines responses you DON\'T WANT displayed. This runs AFTER the Enable Debug Types does, so it will remove anything you previously added if both are defined.',
			)
		);

		add_settings_field(
			'exclude_debug_type_string',
			'Exclude Debug Type - String',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'exclude_debug_type_string',
				'type'        => 'checkbox',
				'description' => 'Defines strings you DON\'T WANT displayed. This runs AFTER the Enable Debug Types does, so it will remove anything you previously added if both are defined.',
			)
		);

		add_settings_field(
			'debug_log_path',
			'Debug Log Path',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'debug_log_path',
				'type'        => 'text',
				'description' => 'Defines where to write the log. The log file name is hard-coded, but the path is up to you. File must be writable.',
			)
		);

		add_settings_field(
			'disable_ssl_verification',
			'Disable SSL Verification',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'disable_ssl_verification',
				'type'        => 'checkbox',
				'description' => 'Disables the verification of SSL to allow local testing.',
			)
		);

		add_settings_field(
			'examine_responses',
			'Examine Responses',
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'examine_responses',
				'type'        => 'checkbox',
				'description' => 'Examines the response and logs it as a debug value when set to true.',
			)
		);
	}

	public function add_settings_field_callback( $args = array() ) {
		$options = get_option( $this->option_name );

		$defaults    = array(
			'id'          => '',
			'type'        => 'text',
			'description' => '',
		);
		$args        = wp_parse_args( $args, $defaults );
		$id          = $args['id'];
		$type        = $args['type'];
		$description = $args['description'];

		echo '<div class="aspirepress-settings-field-wrapper aspirepress-settings-field-wrapper-' . esc_attr( $id ) . '">';
		switch ( $type ) {
			case 'text':
				?>
					<input type="text" id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name . "[$id]" ); ?>" value="<?php echo isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : ''; ?>" class="regular-text">
					<?php
				break;

			case 'textarea':
				?>
					<textarea id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name . "[$id]" ); ?>" rows="5" cols="50"><?php echo isset( $options[ $id ] ) ? esc_textarea( $options[ $id ] ) : ''; ?></textarea>
					<?php
				break;

			case 'checkbox':
				?>
					<input type="checkbox" id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name . "[$id]" ); ?>" value="1" <?php checked( 1, isset( $options[ $id ] ) ? $options[ $id ] : 0 ); ?>>
					<?php
				break;
		}
		echo '<p class="description">' . esc_html( $description ) . '</p>';
		echo '</div>';
	}

	public function sanitize_settings( $input ) {
		$sanitized_input = array();

		$sanitized_input['enable']            = isset( $input['enable'] ) ? 1 : 0;
		$sanitized_input['api_key']           = isset( $input['api_key'] ) ? sanitize_text_field( $input['api_key'] ) : '';
		$sanitized_input['api_host']          = isset( $input['api_host'] ) ? sanitize_text_field( $input['api_host'] ) : '';
		$sanitized_input['rewrite_wporg_api'] = isset( $input['rewrite_wporg_api'] ) ? 1 : 0;
		$sanitized_input['api_url']           = isset( $input['api_url'] ) ? sanitize_text_field( $input['api_url'] ) : '';
		$sanitized_input['rewrite_wporg_dl']  = isset( $input['rewrite_wporg_dl'] ) ? 1 : 0;
		$sanitized_input['api_download_url']  = isset( $input['api_download_url'] ) ? sanitize_text_field( $input['api_download_url'] ) : '';

		$sanitized_input['enable_debug']                = isset( $input['enable_debug'] ) ? 1 : 0;
		$sanitized_input['enable_debug_type_request']   = isset( $input['enable_debug_type_request'] ) ? 1 : 0;
		$sanitized_input['enable_debug_type_response']  = isset( $input['enable_debug_type_response'] ) ? 1 : 0;
		$sanitized_input['enable_debug_type_string']    = isset( $input['enable_debug_type_string'] ) ? 1 : 0;
		$sanitized_input['exclude_debug_type_request']  = isset( $input['exclude_debug_type_request'] ) ? 1 : 0;
		$sanitized_input['exclude_debug_type_response'] = isset( $input['exclude_debug_type_response'] ) ? 1 : 0;
		$sanitized_input['exclude_debug_type_string']   = isset( $input['exclude_debug_type_string'] ) ? 1 : 0;
		$sanitized_input['debug_log_path']              = isset( $input['debug_log_path'] ) ? sanitize_text_field( $input['debug_log_path'] ) : '';
		$sanitized_input['disable_ssl_verification']    = isset( $input['disable_ssl_verification'] ) ? 1 : 0;
		$sanitized_input['examine_responses']           = isset( $input['examine_responses'] ) ? 1 : 0;
		return $sanitized_input;
	}
}
