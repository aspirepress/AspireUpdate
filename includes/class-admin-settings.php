<?php
/**
 * The Class for Admin Settings Page and functions to access Settings Values.
 *
 * @package aspire-update
 */

namespace AspireUpdate;

/**
 * The Class for Admin Settings Page and functions to access Settings Values.
 */
class Admin_Settings {

	/**
	 * Hold a single instance of the class.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * The Name of the Option Group.
	 *
	 * @var string
	 */
	private $option_group = 'aspireupdate_settings';

	/**
	 * The Name of the Option.
	 *
	 * @var string
	 */
	private $option_name = 'aspireupdate_settings';

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
		add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'reset_settings' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'reset_admin_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'admin_init', array( $this, 'update_settings' ) );
		add_action( 'network_admin_edit_aspireupdate-settings', array( $this, 'update_settings' ) );
	}

	/**
	 * Initialize Class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get the default values for the settings.
	 *
	 * @return array The default values.
	 */
	private function get_default_settings() {
		$options             = array();
		$options['api_host'] = 'api.aspirecloud.org';
		return $options;
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
			wp_verify_nonce( sanitize_key( $_GET['reset-nonce'] ), 'aspireupdate-reset-nonce' )
		) {
			$options = $this->get_default_settings();
			update_site_option( $this->option_name, $options );
			update_site_option( 'aspireupdate-reset', 'true' );

			wp_safe_redirect(
				add_query_arg(
					array(
						'reset-success'       => 'success',
						'reset-success-nonce' => wp_create_nonce( 'aspireupdate-reset-success-nonce' ),
					),
					network_admin_url( 'index.php?page=aspireupdate-settings' )
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
			( 'true' === get_site_option( 'aspireupdate-reset' ) ) &&
			isset( $_GET['reset-success'] ) &&
			( 'success' === $_GET['reset-success'] ) &&
			isset( $_GET['reset-success-nonce'] ) &&
			wp_verify_nonce( sanitize_key( $_GET['reset-success-nonce'] ), 'aspireupdate-reset-success-nonce' )
		) {
			echo '<div class="notice notice-success is-dismissible"><p>Settings have been reset to default.</p></div>';
			delete_site_option( 'aspireupdate-reset' );
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
			$options = get_site_option( $this->option_name, false );
			/**
			 * If the options are not set load defaults.
			 */
			if ( false === $options ) {
				$options = $this->get_default_settings();
				update_site_option( $this->option_name, $options );
			}
			$config_file_options = $this->get_settings_from_config_file();
			if ( is_array( $options ) ) {
				/**
				 * If User Options are saved do some processing to make it match the structure of the data from the config file.
				 */
				if ( isset( $options['api_host'] ) && ( 'other' === $options['api_host'] ) ) {
					$options['api_host'] = $options['api_host_other'];
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
				$this->options = wp_parse_args( $config_file_options, $options );
			}
		}
		return $this->options[ $setting_name ] ?? $default_value;
	}

	/**
	 * Get the values defined in the config file.
	 *
	 * @return array An array of values as defined in the Config File.
	 */
	private function get_settings_from_config_file() {
		$options = array();

		if ( ! defined( 'AP_ENABLE' ) ) {
			define( 'AP_ENABLE', false );
		} elseif ( AP_ENABLE ) {
			$options['enable'] = AP_ENABLE;
		}

		if ( ! defined( 'AP_HOST' ) ) {
			define( 'AP_HOST', '' );
		} else {
			$options['api_host'] = AP_HOST;
		}

		if ( ! defined( 'AP_API_KEY' ) ) {
			define( 'AP_API_KEY', '' );
		} else {
			$options['api_key'] = AP_API_KEY;
		}

		if ( ! defined( 'AP_DEBUG' ) ) {
			define( 'AP_DEBUG', false );
		} elseif ( AP_DEBUG ) {
			$options['enable_debug'] = AP_DEBUG;
		}

		if ( ! defined( 'AP_DEBUG_TYPES' ) ) {
			define( 'AP_DEBUG_TYPES', array() );
		} elseif ( is_array( AP_DEBUG_TYPES ) ) {
			$options['enable_debug_type'] = AP_DEBUG_TYPES;
		}

		if ( ! defined( 'AP_DISABLE_SSL' ) ) {
			define( 'AP_DISABLE_SSL', false );
		} elseif ( AP_DISABLE_SSL ) {
			$options['disable_ssl_verification'] = AP_DISABLE_SSL;
		}

		return $options;
	}

	/**
	 * Update settings for single site or network activated.
	 *
	 * @link http://wordpress.stackexchange.com/questions/64968/settings-api-in-multisite-missing-update-message
	 * @link http://benohead.com/wordpress-network-wide-plugin-settings/
	 */
	public function update_settings() {
		// Exit if improper privileges.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'aspireupdate-settings' ) ) {
			return;
		}

		// Save settings and redirect.
		if ( ( isset( $_POST['option_page'] ) && 'aspireupdate_settings' === $_POST['option_page'] ) ) {
			update_site_option( $this->option_name, $this->sanitize_settings( wp_unslash( $_POST['aspireupdate_settings'] ) ) );

			wp_safe_redirect(
				add_query_arg( array( network_admin_url( 'index.php?page=aspireupdate-settings' ) ) )
			);
			exit;
		}
	}

	/**
	 * Register the Admin Menu.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		$capability = is_multisite() ? 'manage_network_options' : 'manage_options';

		if ( ! defined( 'AP_REMOVE_UI' ) ) {
			define( 'AP_REMOVE_UI', false );
		}
		if ( false === AP_REMOVE_UI ) {
			add_submenu_page(
				'index.php',
				'AspireUpdate',
				'AspireUpdate',
				$capability,
				'aspireupdate-settings',
				array( $this, 'the_settings_page' )
			);
		}
	}

	/**
	 * Enqueue the Scripts and Styles.
	 *
	 * @param string $hook The page identifier.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, array( 'dashboard_page_aspireupdate-settings','index_page_aspireupdate-settings' ) ) ) {
			return;
		}
		wp_enqueue_style( 'aspire_update_settings_css', plugin_dir_url( __DIR__ ) . 'assets/css/aspire-update.css', array(), AP_VERSION );
		wp_enqueue_script( 'aspire_update_settings_js', plugin_dir_url( __DIR__ ) . 'assets/js/aspire-update.js', array( 'jquery' ), AP_VERSION, true );
		wp_localize_script(
			'aspire_update_settings_js',
			'aspireupdate',
			array(
				'ajax_url' => network_admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'aspireupdate-ajax' ),
				'domain'   => Utilities::get_top_level_domain(),
			)
		);
	}

	/**
	 * The Settings Page Markup.
	 *
	 * @return void
	 */
	public function the_settings_page() {
		$action = is_multisite() ? 'index.php?page=aspireupdate-settings' : 'options.php';
		$reset_url = add_query_arg(
			array(
				'reset'       => 'reset',
				'reset-nonce' => wp_create_nonce( 'aspireupdate-reset-nonce' ),
			),
			network_admin_url( 'index.php?page=aspireupdate-settings' )
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AspireUpdate Settings', 'AspireUpdate' ); ?></h1>
			<form id="aspireupdate-settings-form" method="post" action="<?php echo esc_attr( $action ); ?>">
				<?php
				settings_fields( $this->option_group );
				do_settings_sections( 'aspireupdate-settings' );
				?>
				<p class="submit">
					<?php wp_nonce_field( 'aspireupdate-settings' ); ?>
					<?php submit_button( esc_html__( 'Save Changes', 'aspireupdate-settings' ), 'primary', 'submit', false ); ?>

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
		$nonce   = wp_create_nonce( 'aspireupdate-settings' );
		$options = get_site_option( $this->option_name, false );
		/**
		 * If the options are not set load defaults.
		 */
		if ( false === $options ) {
			$options = $this->get_default_settings();
			update_site_option( $this->option_name, $options );
		}

		register_setting(
			$this->option_group,
			$this->option_name,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		add_settings_section(
			'aspireupdate_settings_section',
			esc_html__( 'API Configuration', 'AspireUpdate' ),
			null,
			'aspireupdate-settings',
			array(
				'before_section' => '<div class="%s">',
				'after_section'  => '</div>',
			)
		);

		add_settings_field(
			'enable',
			esc_html__( 'Enable AspireUpdate API Rewrites', 'AspireUpdate' ),
			array( $this, 'add_settings_field_callback' ),
			'aspireupdate-settings',
			'aspireupdate_settings_section',
			array(
				'id'   => 'enable',
				'type' => 'checkbox',
				'data' => $options,
			)
		);

		add_settings_field(
			'api_host',
			esc_html__( 'API Host', 'AspireUpdate' ),
			array( $this, 'add_settings_field_callback' ),
			'aspireupdate-settings',
			'aspireupdate_settings_section',
			array(
				'id'          => 'api_host',
				'type'        => 'hosts',
				'data'        => $options,
				'description' => esc_html__( 'Your new API Host.', 'AspireUpdate' ),
				'options'     => array(
					array(
						'value'           => 'api.aspirecloud.org',
						'label'           => 'AspireCloud (api.aspirecloud.org)',
						'require-api-key' => 'true',
						'api-key-url'     => 'api.aspirecloud.org/v1/apitoken',
					),
					array(
						'value'           => 'other',
						'label'           => 'Other',
						'require-api-key' => 'false',
					),
				),
			)
		);

		add_settings_field(
			'api_key',
			esc_html__( 'API Key', 'AspireUpdate' ),
			array( $this, 'add_settings_field_callback' ),
			'aspireupdate-settings',
			'aspireupdate_settings_section',
			array(
				'id'          => 'api_key',
				'type'        => 'api-key',
				'data'        => $options,
				'description' => esc_html__( 'Provides an API key for repositories that may require authentication.', 'AspireUpdate' ),
			)
		);

		add_settings_section(
			'aspireupdate_debug_settings_section',
			esc_html__( 'API Debug Configuration', 'AspireUpdate' ),
			null,
			'aspireupdate-settings',
			array(
				'before_section' => '<div class="%s">',
				'after_section'  => '</div>',
			)
		);

		add_settings_field(
			'enable_debug',
			esc_html__( 'Enable Debug Mode', 'AspireUpdate' ),
			array( $this, 'add_settings_field_callback' ),
			'aspireupdate-settings',
			'aspireupdate_debug_settings_section',
			array(
				'id'          => 'enable_debug',
				'type'        => 'checkbox',
				'data'        => $options,
				'description' => esc_html__( 'Enables debug mode for the plugin.', 'AspireUpdate' ),
			)
		);

		add_settings_field(
			'enable_debug_type',
			esc_html__( 'Enable Debug Type', 'AspireUpdate' ),
			array( $this, 'add_settings_field_callback' ),
			'aspireupdate-settings',
			'aspireupdate_debug_settings_section',
			array(
				'id'          => 'enable_debug_type',
				'type'        => 'checkbox-group',
				'data'        => $options,
				'options'     => array(
					'request'  => esc_html__( 'Request', 'AspireUpdate' ),
					'response' => esc_html__( 'Response', 'AspireUpdate' ),
					'string'   => esc_html__( 'String', 'AspireUpdate' ),
				),
				'description' => esc_html__( 'Outputs the request URL and headers / response headers and body / string that is being rewritten.', 'AspireUpdate' ),
			)
		);

		add_settings_field(
			'disable_ssl_verification',
			esc_html__( 'Disable SSL Verification', 'AspireUpdate' ),
			array( $this, 'add_settings_field_callback' ),
			'aspireupdate-settings',
			'aspireupdate_debug_settings_section',
			array(
				'id'          => 'disable_ssl_verification',
				'type'        => 'checkbox',
				'data'        => $options,
				'class'       => 'advanced-setting',
				'description' => esc_html__( 'Disables the verification of SSL to allow local testing.', 'AspireUpdate' ),
			)
		);
	}

	/**
	 * The Fields API which any CMS should have in its core but something we dont, hence this ugly hack.
	 *
	 * @param array $args The Field Parameters.
	 *
	 * @return void Echos the Field HTML.
	 */
	public function add_settings_field_callback( $args = array() ) {

		$defaults      = array(
			'id'          => '',
			'type'        => 'text',
			'description' => '',
			'data'        => array(),
			'options'     => array(),
		);
		$args          = wp_parse_args( $args, $defaults );
		$id            = $args['id'];
		$type          = $args['type'];
		$description   = $args['description'];
		$group_options = $args['options'];
		$options       = $args['data'];

		echo '<div class="aspireupdate-settings-field-wrapper aspireupdate-settings-field-wrapper-' . esc_attr( $id ) . '">';
		switch ( $type ) {
			case 'text':
				?>
					<input type="text" id="aspireupdate-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>]" value="<?php echo esc_attr( $options[ $id ] ?? '' ); ?>" class="regular-text" />
					<?php
				break;

			case 'textarea':
				?>
					<textarea id="aspireupdate-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>]" rows="5" cols="50"><?php echo esc_textarea( $options[ $id ] ?? '' ); ?></textarea>
					<?php
				break;

			case 'checkbox':
				?>
					<input type="checkbox" id="aspireupdate-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>]" value="1" <?php checked( 1, $options[ $id ] ?? 0 ); ?> />
					<?php
				break;

			case 'checkbox-group':
				foreach ( $group_options as $key => $label ) {
					?>
					<p>
						<label>
							<input type="checkbox" id="aspireupdate-settings-field-<?php echo esc_attr( $id ); ?>-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( 1, $options[ $id ][ $key ] ?? 0 ); ?> /> <?php echo esc_html( $label ); ?>
						</label>
					</p>
					<?php
				}
				break;

			case 'api-key':
				?>
					<input type="text" id="aspireupdate-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>]" value="<?php echo esc_attr( $options[ $id ] ?? '' ); ?>" class="regular-text" />
					<input type="button" id="aspireupdate-generate-api-key" value="Generate API Key" title="Generate API Key" />
					<p class="error"></p>
					<?php
				break;

			case 'hosts':
				?>
				<select id="aspireupdate-settings-field-<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>]" class="regular-text">
					<?php
					foreach ( $group_options as $group_option ) {
						?>
							<option
								data-api-key-url="<?php echo esc_html( $group_option['api-key-url'] ?? '' ); ?>"
								data-require-api-key="<?php echo esc_html( $group_option['require-api-key'] ?? 'false' ); ?>"
								value="<?php echo esc_attr( $group_option['value'] ?? '' ); ?>"
								<?php selected( esc_attr( $group_option['value'] ?? '' ), esc_attr( $options[ $id ] ?? '' ) ); ?>
							>
								<?php echo esc_html( $group_option['label'] ?? '' ); ?>
							</option>
						<?php
					}
					?>
				</select>
				<p>
					<input
						type="text"
						id="aspireupdate-settings-field-<?php echo esc_attr( $id ); ?>_other"
						name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>_other]"
						value="<?php echo esc_attr( $options[ $id . '_other' ] ?? '' ); ?>"
						class="regular-text"
					/>
				</p>
				<?php
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

		$sanitized_input['enable']         = ( ! empty( $input['enable'] ) && $input['enable'] ) ? 1 : 0;
		$sanitized_input['api_key']        = sanitize_text_field( $input['api_key'] ?? '' );
		$sanitized_input['api_host']       = sanitize_text_field( $input['api_host'] ?? '' );
		$sanitized_input['api_host_other'] = sanitize_text_field( $input['api_host_other'] ?? '' );

		$sanitized_input['enable_debug'] = ! empty( $input['enable_debug'] ) ? 1 : 0;
		if ( isset( $input['enable_debug_type'] ) && is_array( $input['enable_debug_type'] ) ) {
			$sanitized_input['enable_debug_type'] = array_map( 'sanitize_text_field', $input['enable_debug_type'] );
		} else {
			$sanitized_input['enable_debug_type'] = array();
		}
		$sanitized_input['disable_ssl_verification'] = ( ! empty( $input['disable_ssl_verification'] ) && $input['disable_ssl_verification'] ) ? 1 : 0;
		return $sanitized_input;
	}
}
