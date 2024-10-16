<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This Class Implements the Aspire Press Admin Settings Page and functions to access Settings Values
 */
class AspirePress_AdminSettings {

	/**
	 * The Name of the Option Group.
	 *
	 * @var string
	 */
	private $option_group = 'aspirepress_settings';

	/**
	 * The Name of the Option.
	 *
	 * @var string
	 */
	private $option_name = 'aspirepress_settings';

	/**
	 * An Array containing the values of the Options.
	 *
	 * @var array
	 */
	private $options = null;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'reset_settings' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'reset_admin_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Handles the Reset Functionality and triggers a Notice to inform the same.
	 *
	 * @return void
	 */
	public function reset_settings() {
		if (
			isset( $_GET['reset'] ) &&
			( 'reset' === $_GET['reset'] ) &&
			isset( $_GET['reset-nonce'] ) &&
			wp_verify_nonce( sanitize_key( $_GET['reset-nonce'] ), 'aspirepress-reset-nonce' )
		) {
			delete_option( $this->option_name );
			update_option( 'aspirepress-reset', 'true' );

			wp_safe_redirect(
				add_query_arg(
					array(
						'reset-success'       => 'success',
						'reset-success-nonce' => wp_create_nonce( 'aspirepress-reset-success-nonce' ),
					),
					admin_url( 'options-general.php?page=aspirepress-settings' )
				)
			);
			exit;
		}
	}

	/**
	 * The Admin Notice to convey a Reset Operation has happened.
	 *
	 * @return void
	 */
	public function reset_admin_notice() {
		if (
			( 'true' === get_option( 'aspirepress-reset' ) ) &&
			isset( $_GET['reset-success'] ) &&
			( 'success' === $_GET['reset-success'] ) &&
			isset( $_GET['reset-success-nonce'] ) &&
			wp_verify_nonce( sanitize_key( $_GET['reset-success-nonce'] ), 'aspirepress-reset-success-nonce' )
		) {
			echo '<div class="notice notice-success is-dismissible"><p>Settings have been reset to default.</p></div>';
			delete_option( 'aspirepress-reset' );
		}
	}

	/**
	 * Get the value of a Setting by giving priority to hard coded values.
	 *
	 * @param string $setting_name The name of the settings field.
	 * @param mixed  $default_value The Default value to return if the field is not defined.
	 *
	 * @return string The value of the settings field.
	 */
	public function get_setting( $setting_name, $default_value = false ) {
		if ( null === $this->options ) {
			$options = get_option( $this->option_name, false );
			/**
			 * If the options are not set load defaults.
			 */
			if ( false === $options ) {
				$options             = array();
				$options['api_host'] = array(
					array(
						'search'  => 'api.wordpress.org',
						'replace' => 'api.aspirecloud.org',
					),
				);
				update_option( $this->option_name, $options );
			}
			$config_file_options = $this->get_settings_from_config_file();
			if ( is_array( $options ) ) {
				/**
				 * If User Options are saved do some processing to make it match the structure of the data from the config file.
				 */
				if ( isset( $options['api_host'] ) && is_array( $options['api_host'] ) ) {
					$api_hosts = array();
					foreach ( $options['api_host'] as $api_host ) {
						if (
							isset( $api_host['search'] ) &&
							( '' !== $api_host['search'] ) &&
							isset( $api_host['replace'] ) &&
							( '' !== $api_host['replace'] )
						) {
							$api_hosts[ $api_host['search'] ] = $api_host['replace'];
						}
					}
					$options['api_host'] = $api_hosts;
				}

				if ( isset( $options['enable_debug_type'] ) && is_array( $options['enable_debug_type'] ) ) {
					$debug_types = array();
					foreach ( $options['enable_debug_type'] as $debug_type_name => $debug_type_enabled ) {
						if ( $debug_type_enabled ) {
							$debug_types[] = $debug_type_name;
						}
					}
					$options['enable_debug_type'] = $debug_types;
				}

				if ( isset( $options['exclude_debug_type'] ) && is_array( $options['exclude_debug_type'] ) ) {
					$exclude_debug_types = array();
					foreach ( $options['exclude_debug_type'] as $exclude_debug_type_name => $exclude_debug_type_enabled ) {
						if ( $exclude_debug_type_enabled ) {
							$exclude_debug_types[] = $exclude_debug_type_name;
						}
					}
					$options['exclude_debug_type'] = $exclude_debug_types;
				}
				$this->options = wp_parse_args( $config_file_options, $options );
			}
		}
		return ( isset( $this->options[ $setting_name ] ) ? $this->options[ $setting_name ] : $default_value );
	}

	/**
	 * Get the values defined in the config file.
	 *
	 * @return array An array of values as defined in the Config File.
	 */
	private function get_settings_from_config_file() {
		$options = array();

		if ( defined( 'AP_UPDATER_ENABLE' ) && AP_UPDATER_ENABLE ) {
			$options['enable'] = AP_UPDATER_ENABLE;
		}

		if ( defined( 'AP_UPDATER_API_KEY' ) ) {
			$options['api_key'] = AP_UPDATER_API_KEY;
		}

		if ( defined( 'AP_UPDATER_HOST_REWRITES' ) && is_array( AP_UPDATER_HOST_REWRITES ) ) {
			$options['api_host'] = AP_UPDATER_HOST_REWRITES;
		}

		if ( defined( 'AP_UPDATER_REWRITE_WPORG_API' ) && AP_UPDATER_REWRITE_WPORG_API ) {
			$options['rewrite_wporg_api'] = AP_UPDATER_REWRITE_WPORG_API;
		}

		if ( defined( 'AP_UPDATER_API_URL' ) ) {
			$options['api_url'] = AP_UPDATER_API_URL;
		}

		if ( defined( 'AP_UPDATER_REWRITE_WPORG_DL' ) && AP_UPDATER_REWRITE_WPORG_DL ) {
			$options['rewrite_wporg_dl'] = AP_UPDATER_REWRITE_WPORG_DL;
		}

		if ( defined( 'AP_UPDATER_DL_URL' ) ) {
			$options['api_download_url'] = AP_UPDATER_DL_URL;
		}

		if ( defined( 'AP_UPDATER_DEBUG' ) && AP_UPDATER_DEBUG ) {
			$options['enable_debug'] = AP_UPDATER_DEBUG;
		}

		if ( defined( 'AP_UPDATER_DEBUG_TYPES' ) ) {
			$options['enable_debug_type'] = AP_UPDATER_DEBUG_TYPES;
		}

		if ( defined( 'AP_UPDATER_DEBUG_TYPES_EXCLUDE' ) ) {
			$options['exclude_debug_type'] = AP_UPDATER_DEBUG_TYPES_EXCLUDE;
		}

		if ( defined( 'AP_UPDATER_DEBUG_LOG_PATH' ) ) {
			$options['debug_log_path'] = AP_UPDATER_DEBUG_LOG_PATH;
		}

		if ( defined( 'AP_UPDATER_DEBUG_SSL' ) && AP_UPDATER_DEBUG_SSL ) {
			$options['disable_ssl_verification'] = AP_UPDATER_DEBUG_SSL;
		}

		if ( defined( 'AP_UPDATER_EXAMINE_RESPONSES' ) && AP_UPDATER_EXAMINE_RESPONSES ) {
			$options['examine_responses'] = AP_UPDATER_EXAMINE_RESPONSES;
		}

		return $options;
	}

	/**
	 * Register the Admin Menu.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		add_options_page(
			'AspirePress',
			'AspirePress',
			'manage_options',
			'aspirepress-settings',
			array( $this, 'the_settings_page' )
		);
	}

	/**
	 * Enqueue the Scripts and Styles.
	 *
	 * @param string $hook The page identifier.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( 'settings_page_aspirepress-settings' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'aspirepress_settings_css', plugin_dir_url( __DIR__ ) . 'assets/css/aspirepress.css', array(), '1.0' );
		wp_enqueue_script( 'aspirepress_settings_js', plugin_dir_url( __DIR__ ) . 'assets/js/aspirepress.js', array( 'jquery' ), '1.0', true );
		wp_localize_script(
			'aspirepress_settings_js',
			'aspirepress',
			array(
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'aspirepress-ajax' ),
				'domain'         => AspirePress_Utils::get_top_domain(),
				'apikey_api_url' => 'api.aspirepress.org/repository/api/v1/apitoken',
			)
		);
	}

	/**
	 * The Settings Page Markup.
	 *
	 * @return void
	 */
	public function the_settings_page() {
		$reset_url = add_query_arg(
			array(
				'reset'       => 'reset',
				'reset-nonce' => wp_create_nonce( 'aspirepress-reset-nonce' ),
			),
			admin_url( 'options-general.php?page=aspirepress-settings' )
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AspirePress Settings', 'aspirepress' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( $this->option_group );
				do_settings_sections( 'aspirepress-settings' );
				?>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
					<a href="<?php echo esc_url( $reset_url ); ?>" class="button button-secondary" >Reset</a>
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Register all Settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$nonce   = wp_create_nonce( 'aspirepress-settings' );
		$ui_mode = ( ( isset( $_GET['advanced'] ) && ( 'true' === $_GET['advanced'] ) && wp_verify_nonce( $nonce, 'aspirepress-settings' ) ) ? 'advanced-mode' : 'normal-mode' );

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
			'aspirepress-settings',
			array(
				'before_section' => '<div class="%s">',
				'after_section'  => '</div>',
				'section_class'  => $ui_mode,
			)
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
				'type'        => 'api-key',
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
				'type'        => 'hosts',
				'class'       => 'advanced-setting',
				'description' => __( 'The Domain rewrites for your new API Host.', 'aspirepress' ),
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
				'class'       => 'advanced-setting',
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
				'class'       => 'advanced-setting',
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
				'class'       => 'advanced-setting',
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
				'class'       => 'advanced-setting',
				'description' => __( 'The URL to use for the third-party plugin download API.', 'aspirepress' ),
			)
		);

		add_settings_section(
			'aspirepress_debug_settings_section',
			__( 'API Debug Configuration', 'aspirepress' ),
			null,
			'aspirepress-settings',
			array(
				'before_section' => '<div class="%s">',
				'after_section'  => '</div>',
				'section_class'  => $ui_mode,
			)
		);

		add_settings_field(
			'enable_debug',
			__( 'Enable Debug Mode', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'enable_debug',
				'type'        => 'checkbox',
				'description' => __( 'Enables debug mode for the plugin.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'enable_debug_type',
			__( 'Enable Debug Type', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'enable_debug_type',
				'type'        => 'checkbox-group',
				'class'       => 'advanced-setting',
				'options'     => array(
					'request'  => __( 'Request', 'aspirepress' ),
					'response' => __( 'Response', 'aspirepress' ),
					'string'   => __( 'String', 'aspirepress' ),
				),
				'description' => __( 'Outputs the request URL and headers / response headers and body / string that is being rewritten.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'exclude_debug_type',
			__( 'Exclude Debug Type', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'exclude_debug_type',
				'type'        => 'checkbox-group',
				'class'       => 'advanced-setting',
				'options'     => array(
					'request'  => __( 'Request', 'aspirepress' ),
					'response' => __( 'Response', 'aspirepress' ),
					'string'   => __( 'String', 'aspirepress' ),
				),
				'description' => __( 'Defines requests / responses / strings you DON\'T WANT displayed. This runs AFTER the Enable Debug Types does, so it will remove anything you previously added if both are defined.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'debug_log_path',
			__( 'Debug Log Path', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'debug_log_path',
				'type'        => 'text',
				'class'       => 'advanced-setting',
				'description' => __( 'Defines where to write the log. The log file name is hard-coded, but the path is up to you. File must be writable.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'disable_ssl_verification',
			__( 'Disable SSL Verification', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'disable_ssl_verification',
				'type'        => 'checkbox',
				'class'       => 'advanced-setting',
				'description' => __( 'Disables the verification of SSL to allow local testing.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'examine_responses',
			__( 'Examine Responses', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'examine_responses',
				'type'        => 'checkbox',
				'class'       => 'advanced-setting',
				'description' => __( 'Examines the response and logs it as a debug value when set to true.', 'aspirepress' ),
			)
		);
	}

	/**
	 * The Fields API which any CMS should have in its core but something we dont.
	 *
	 * @param array $args The Field Parameters.
	 *
	 * @return void Echos the Field HTML.
	 */
	public function add_settings_field_callback( $args = array() ) {
		$options = get_option( $this->option_name );

		$defaults      = array(
			'id'          => '',
			'type'        => 'text',
			'description' => '',
			'options'     => array(),
		);
		$args          = wp_parse_args( $args, $defaults );
		$id            = $args['id'];
		$type          = $args['type'];
		$description   = $args['description'];
		$group_options = $args['options'];

		echo '<div class="aspirepress-settings-field-wrapper aspirepress-settings-field-wrapper-' . esc_attr( $id ) . '">';
		switch ( $type ) {
			case 'text':
				?>
					<input type="text" id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>]" value="<?php echo isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : ''; ?>" class="regular-text" />
					<?php
				break;

			case 'textarea':
				?>
					<textarea id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>]" rows="5" cols="50"><?php echo isset( $options[ $id ] ) ? esc_textarea( $options[ $id ] ) : ''; ?></textarea>
					<?php
				break;

			case 'checkbox':
				?>
					<input type="checkbox" id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>]" value="1" <?php checked( 1, isset( $options[ $id ] ) ? $options[ $id ] : 0 ); ?> />
					<?php
				break;

			case 'checkbox-group':
				foreach ( $group_options as $key => $label ) {
					?>
					<p>
						<label>
							<input type="checkbox" id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( 1, isset( $options[ $id ][ $key ] ) ? $options[ $id ][ $key ] : 0 ); ?> /> <?php echo esc_html( $label ); ?>
						</label>
					</p>
					<?php
				}
				break;

			case 'api-key':
				?>
					<input type="text" id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>]" value="<?php echo isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : ''; ?>" class="regular-text" />
					<input type="button" id="aspirepress-generate-api-key" value="Generate API Key" title="Generate API Key" />
					<p class="error"></p>
					<?php
				break;

			case 'hosts':
				echo '<div class="aspirepress-settings-field-hosts-wrapper">';
				for ( $i = 0; $i < 10; $i++ ) {
					?>
						<div class="aspirepress-settings-field-hosts-row">
							<div class="aspirepress-settings-field-hosts-left">
								<input type="text" placeholder="Search" id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>-<?php echo esc_attr( $i ); ?>-search" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>][<?php echo esc_attr( $i ); ?>][search]" value="<?php echo isset( $options[ $id ][ $i ]['search'] ) ? esc_attr( $options[ $id ][ $i ]['search'] ) : ''; ?>" class="regular-text" />
							</div>
							<div class="aspirepress-settings-field-hosts-right">
								<input type="text" placeholder="Replace" id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>-<?php echo esc_attr( $i ); ?>-replace" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>][<?php echo esc_attr( $i ); ?>][replace]" value="<?php echo isset( $options[ $id ][ $i ]['replace'] ) ? esc_attr( $options[ $id ][ $i ]['replace'] ) : ''; ?>" class="regular-text" />
							</div>
						</div>
						<?php
				}
				echo '</div>';
				break;
		}
		echo '<p class="description">' . esc_html( $description ) . '</p>';
		echo '</div>';
	}

	/**
	 * Sanitize the Inputs.
	 *
	 * @param array $input The Input values.
	 * @return array The processed Input.
	 */
	public function sanitize_settings( $input ) {
		$sanitized_input = array();

		$sanitized_input['enable']   = ( isset( $input['enable'] ) && $input['enable'] ) ? 1 : 0;
		$sanitized_input['api_key']  = isset( $input['api_key'] ) ? sanitize_text_field( $input['api_key'] ) : '';
		$sanitized_input['api_host'] = isset( $input['api_host'] ) ? sanitize_text_field( $input['api_host'] ) : '';
		if ( isset( $input['api_host'] ) && is_array( $input['api_host'] ) ) {
			$sanitized_input['api_host'] = array();
			foreach ( $input['api_host'] as $api_host ) {
				$sanitized_input['api_host'][] = array(
					'search'  => isset( $api_host['search'] ) ? sanitize_text_field( $api_host['search'] ) : '',
					'replace' => isset( $api_host['replace'] ) ? sanitize_text_field( $api_host['replace'] ) : '',
				);
			}
		} else {
			$sanitized_input['api_host'] = array();
		}
		$sanitized_input['rewrite_wporg_api'] = ( isset( $input['rewrite_wporg_api'] ) && $input['rewrite_wporg_dl'] ) ? 1 : 0;
		$sanitized_input['api_url']           = isset( $input['api_url'] ) ? sanitize_text_field( $input['api_url'] ) : '';
		$sanitized_input['rewrite_wporg_dl']  = ( isset( $input['rewrite_wporg_dl'] ) && $input['rewrite_wporg_dl'] ) ? 1 : 0;
		$sanitized_input['api_download_url']  = isset( $input['api_download_url'] ) ? sanitize_text_field( $input['api_download_url'] ) : '';

		$sanitized_input['enable_debug'] = isset( $input['enable_debug'] ) ? 1 : 0;
		if ( isset( $input['enable_debug_type'] ) && is_array( $input['enable_debug_type'] ) ) {
			$sanitized_input['enable_debug_type'] = array_map( 'sanitize_text_field', $input['enable_debug_type'] );
		} else {
			$sanitized_input['enable_debug_type'] = array();
		}
		if ( isset( $input['exclude_debug_type'] ) && is_array( $input['exclude_debug_type'] ) ) {
			$sanitized_input['exclude_debug_type'] = array_map( 'sanitize_text_field', $input['exclude_debug_type'] );
		} else {
			$sanitized_input['exclude_debug_type'] = array();
		}
		$sanitized_input['debug_log_path']           = isset( $input['debug_log_path'] ) ? sanitize_text_field( $input['debug_log_path'] ) : '';
		$sanitized_input['disable_ssl_verification'] = ( isset( $input['disable_ssl_verification'] ) && $input['disable_ssl_verification'] ) ? 1 : 0;
		$sanitized_input['examine_responses']        = ( isset( $input['examine_responses'] ) && $input['examine_responses'] ) ? 1 : 0;
		return $sanitized_input;
	}
}
