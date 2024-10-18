<?php
/**
 * The Class for Admin Settings Page and functions to access Settings Values.
 *
 * @package aspire-update
 */

namespace AspirePress;

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
		$options              = array();
		$options['api_hosts'] = array( 'api.aspirecloud.org' );
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
			wp_verify_nonce( sanitize_key( $_GET['reset-nonce'] ), 'aspirepress-reset-nonce' )
		) {
			$options = $this->get_default_settings();
			update_option( $this->option_name, $options );
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
				$options = $this->get_default_settings();
				update_option( $this->option_name, $options );
			}
			$config_file_options = $this->get_settings_from_config_file();
			if ( is_array( $options ) ) {
				/**
				 * If User Options are saved do some processing to make it match the structure of the data from the config file.
				 */
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
		return ( isset( $this->options[ $setting_name ] ) ? $this->options[ $setting_name ] : $default_value );
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

		if ( ! defined( 'AP_API_KEY' ) ) {
			define( 'AP_API_KEY', '' );
		} else {
			$options['api_key'] = AP_API_KEY;
		}

		if ( ! defined( 'AP_HOSTS' ) ) {
			define( 'AP_HOSTS', array() );
		} elseif ( is_array( AP_HOSTS ) ) {
			$options['api_hosts'] = AP_HOSTS;
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
		wp_enqueue_style( 'aspire_update_settings_css', plugin_dir_url( __DIR__ ) . 'assets/css/aspire-update.css', array(), '1.0' );
		wp_enqueue_script( 'aspire_update_settings_js', plugin_dir_url( __DIR__ ) . 'assets/js/aspire-update.js', array( 'jquery' ), '1.0', true );
		wp_localize_script(
			'aspire_update_settings_js',
			'aspirepress',
			array(
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'aspirepress-ajax' ),
				'domain'         => Utilities::get_top_level_domain(),
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
		$options = get_option( $this->option_name );

		register_setting(
			$this->option_group,
			$this->option_name,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		add_settings_section(
			'aspirepress_settings_section',
			esc_html__( 'API Configuration', 'aspirepress' ),
			null,
			'aspirepress-settings',
			array(
				'before_section' => '<div class="%s">',
				'after_section'  => '</div>',
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
				'data' => $options,
			)
		);

		add_settings_field(
			'api_key',
			esc_html__( 'API Key', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_settings_section',
			array(
				'id'          => 'api_key',
				'type'        => 'api-key',
				'data'        => $options,
				'description' => esc_html__( 'Provides an API key for repositories that may require authentication.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'api_host',
			esc_html__( 'API Hosts', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_settings_section',
			array(
				'id'          => 'api_hosts',
				'type'        => 'hosts',
				'data'        => $options,
				'description' => esc_html__( 'The Domain rewrites for your new API Hosts.', 'aspirepress' ),
			)
		);

		add_settings_section(
			'aspirepress_debug_settings_section',
			esc_html__( 'API Debug Configuration', 'aspirepress' ),
			null,
			'aspirepress-settings',
			array(
				'before_section' => '<div class="%s">',
				'after_section'  => '</div>',
			)
		);

		add_settings_field(
			'enable_debug',
			esc_html__( 'Enable Debug Mode', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'enable_debug',
				'type'        => 'checkbox',
				'data'        => $options,
				'description' => esc_html__( 'Enables debug mode for the plugin.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'enable_debug_type',
			esc_html__( 'Enable Debug Type', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'enable_debug_type',
				'type'        => 'checkbox-group',
				'data'        => $options,
				'options'     => array(
					'request'  => esc_html__( 'Request', 'aspirepress' ),
					'response' => esc_html__( 'Response', 'aspirepress' ),
					'string'   => esc_html__( 'String', 'aspirepress' ),
				),
				'description' => esc_html__( 'Outputs the request URL and headers / response headers and body / string that is being rewritten.', 'aspirepress' ),
			)
		);

		add_settings_field(
			'disable_ssl_verification',
			esc_html__( 'Disable SSL Verification', 'aspirepress' ),
			array( $this, 'add_settings_field_callback' ),
			'aspirepress-settings',
			'aspirepress_debug_settings_section',
			array(
				'id'          => 'disable_ssl_verification',
				'type'        => 'checkbox',
				'data'        => $options,
				'class'       => 'advanced-setting',
				'description' => esc_html__( 'Disables the verification of SSL to allow local testing.', 'aspirepress' ),
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
							<input type="text" id="aspirepress-settings-field-<?php echo esc_attr( $id ); ?>-<?php echo esc_attr( $i ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>][<?php echo esc_attr( $i ); ?>]" value="<?php echo isset( $options[ $id ][ $i ] ) ? esc_attr( $options[ $id ][ $i ] ) : ''; ?>" class="regular-text" />
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

		$sanitized_input['enable']    = ( isset( $input['enable'] ) && $input['enable'] ) ? 1 : 0;
		$sanitized_input['api_key']   = isset( $input['api_key'] ) ? sanitize_text_field( $input['api_key'] ) : '';
		$sanitized_input['api_hosts'] = isset( $input['api_hosts'] ) ? sanitize_text_field( $input['api_hosts'] ) : '';
		if ( isset( $input['api_hosts'] ) && is_array( $input['api_hosts'] ) ) {
			$sanitized_input['api_hosts'] = array();
			foreach ( $input['api_hosts'] as $api_host ) {
				$sanitized_input['api_hosts'][] = isset( $api_host ) ? sanitize_text_field( $api_host ) : '';
			}
		} else {
			$sanitized_input['api_hosts'] = array();
		}

		$sanitized_input['enable_debug'] = isset( $input['enable_debug'] ) ? 1 : 0;
		if ( isset( $input['enable_debug_type'] ) && is_array( $input['enable_debug_type'] ) ) {
			$sanitized_input['enable_debug_type'] = array_map( 'sanitize_text_field', $input['enable_debug_type'] );
		} else {
			$sanitized_input['enable_debug_type'] = array();
		}
		$sanitized_input['disable_ssl_verification'] = ( isset( $input['disable_ssl_verification'] ) && $input['disable_ssl_verification'] ) ? 1 : 0;
		return $sanitized_input;
	}
}
