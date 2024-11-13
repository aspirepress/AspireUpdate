<?php
namespace AspireUpdate;

$reset_url    = $args['reset_url'] ?? '';
$option_group = $args['option_group'] ?? '';
?>
<div class="wrap">
	<h1><?php esc_html_e( 'AspireUpdate Settings', 'AspireUpdate' ); ?></h1>
	<form id="aspireupdate-settings-form" method="post" action="index.php?page=aspireupdate-settings">
		<?php
		settings_fields( $option_group );
		do_settings_sections( 'aspireupdate-settings' );
		?>
		<p class="submit">
			<?php wp_nonce_field( 'aspireupdate-settings' ); ?>
			<?php submit_button( '', 'primary', 'submit', false ); ?>
			<a href="<?php echo esc_url( $reset_url ); ?>" class="button button-secondary" ><?php esc_html_e( 'Reset', 'AspireUpdate' ); ?></a>
		</p>
	</form>
	<?php Utilities::include_file( 'voltron.txt' ); ?>
</div>
