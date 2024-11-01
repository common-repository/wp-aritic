<?php
/**
 * Option page definition
 *
 * @package wp-aritic
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

/**
 * HTML for the Aritic option page
 */
function wparitic_options_page() {
	?>
	<div>
		<h2><?php esc_html_e( 'WP Aritic', 'wp-aritic' ); ?></h2>
		<p><?php esc_html_e( 'Add Aritic tracking capabilities to your website.', 'wp-aritic' ); ?></p>
		<form action="options.php" method="post">
			<?php settings_fields( 'wparitic' ); ?>
			<?php do_settings_sections( 'wparitic' ); ?>
			<?php submit_button(); ?>
		</form>
		<h3><?php esc_html_e( 'Shortcode Examples:', 'wp-aritic' ); ?></h3>
		<ul>
			<li><?php esc_html_e( 'Aritic Form Embed:', 'wp-aritic' ); ?> <code>[aritic type="form" id="1"]</code></li>
			<li><?php esc_html_e( 'Aritic Dynamic Content:', 'wp-aritic' ); ?> <code>[aritic type="content" slot="slot_name"]<?php esc_html_e( 'Default Text', 'wp-aritic' ); ?>[/aritic]</code></li>
		</ul>
		<h3><?php esc_html_e( 'Quick Links', 'wp-aritic' ); ?></h3>
		<ul>
			<li>
				<a href="https://aritic.com" target="_blank"><?php esc_html_e( 'Plugin docs', 'wp-aritic' ); ?></a>
			</li>
			<li>
				<a href="https://app.aritic.com/customer" target="_blank"><?php esc_html_e( 'Plugin support', 'wp-aritic' ); ?></a>
			</li>
			<li>
				<a href="https://aritic.com" target="_blank"><?php esc_html_e( 'Aritic project', 'wp-aritic' ); ?></a>
			</li>
			<li>
				<a href="http://aritic.com/" target="_blank"><?php esc_html_e( 'Aritic docs', 'wp-aritic' ); ?></a>
			</li>
			<li>
				<a href="https://www.aritic.com/" target="_blank"><?php esc_html_e( 'Aritic forum', 'wp-aritic' ); ?></a>
			</li>
		</ul>
	</div>
	<?php
}

/**
 * Define admin_init hook logic
 */
function wparitic_admin_init() {
	register_setting( 'wparitic', 'wparitic_options', 'wparitic_options_validate' );

	add_settings_section(
		'wparitic_main',
		__( 'Main Settings', 'wp-aritic' ),
		'wparitic_section_text',
		'wparitic'
	);

	add_settings_field(
		'wparitic_base_url',
		__( 'Aritic URL', 'wp-aritic' ),
		'wparitic_base_url',
		'wparitic',
		'wparitic_main'
	);
	add_settings_field(
		'wparitic_script_location',
		__( 'Tracking script location', 'wp-aritic' ),
		'wparitic_script_location',
		'wparitic',
		'wparitic_main'
	);
	add_settings_field(
		'wparitic_fallback_activated',
		__( 'Tracking image', 'wp-aritic' ),
		'wparitic_fallback_activated',
		'wparitic',
		'wparitic_main'
	);
	add_settings_field(
		'wparitic_track_logged_user',
		__( 'Logged user', 'wp-aritic' ),
		'wparitic_track_logged_user',
		'wparitic',
		'wparitic_main'
	);
}
add_action( 'admin_init', 'wparitic_admin_init' );

/**
 * Section text
 */
function wparitic_section_text() {
}

/**
 * Define the input field for Aritic base URL
 */
function wparitic_base_url() {
	$url = wparitic_option( 'base_url', '' );

	?>
	<input
		id="wparitic_base_url"
		name="wparitic_options[base_url]"
		size="40"
		type="text"
		placeholder="https://..."
		value="<?php echo esc_url_raw( $url, array( 'http', 'https' ) ); ?>"
	/>
	<?php
}

/**
 * Define the input field for Aritic script location
 */
function wparitic_script_location() {
	$position = wparitic_option( 'script_location', '' );

	?>
	<fieldset id="wparitic_script_location">
		<label>
			<input
				type="radio"
				name="wparitic_options[script_location]"
				value="header"
				<?php if ( 'footer' !== $position ) : ?>checked<?php endif; ?>
			/>
			<?php esc_html_e( 'Embedded within the `wp_head` action.', 'wp-aritic' ); ?>
		</label>
		<br/>
		<label>
			<input
				type="radio"
				name="wparitic_options[script_location]"
				value="footer"
				<?php if ( 'footer' === $position ) : ?>checked<?php endif; ?>
			/>
			<?php esc_html_e( 'Embedded within the `wp_footer` action.', 'wp-aritic' ); ?>
		</label>
	</fieldset>
	<?php
}

/**
 * Define the input field for Aritic fallback flag
 */
function wparitic_fallback_activated() {
	$flag = wparitic_option( 'fallback_activated', false );

	?>
	<input
		id="wparitic_fallback_activated"
		name="wparitic_options[fallback_activated]"
		type="checkbox"
		value="1"
		<?php if ( true === $flag ) : ?>checked<?php endif; ?>
	/>
	<label for="wparitic_fallback_activated">
		<?php esc_html_e( 'Activate it when JavaScript is disabled ?', 'wp-aritic' ); ?>
	</label>
	<?php
}

/**
 * Define the input field for Aritic logged user tracking flag
 */
function wparitic_track_logged_user() {
	$flag = wparitic_option( 'track_logged_user', false );

	?>
	<input
		id="wparitic_track_logged_user"
		name="wparitic_options[track_logged_user]"
		type="checkbox"
		value="1"
		<?php if ( true === $flag ) : ?>checked<?php endif; ?>
	/>
	<label for="wparitic_track_logged_user">
		<?php esc_html_e( 'Track user information when logged ?', 'wp-aritic' ); ?>
	</label>
	<?php
}

/**
 * Validate base URL input value
 *
 * @param  array $input Input data.
 * @return array
 */
function wparitic_options_validate( $input ) {
	$options = get_option( 'wparitic_options' );

	$input['base_url'] = isset( $input['base_url'] )
		? trim( $input['base_url'], " \t\n\r\0\x0B/" )
		: '';

	$options['base_url'] = esc_url_raw( trim( $input['base_url'], " \t\n\r\0\x0B/" ) );
	$options['script_location'] = isset( $input['script_location'] )
		? trim( $input['script_location'] )
		: 'header';
	if ( ! in_array( $options['script_location'], array( 'header', 'footer' ), true ) ) {
		$options['script_location'] = 'header';
	}

	$options['fallback_activated'] = isset( $input['fallback_activated'] ) && '1' === $input['fallback_activated']
		? true
		: false;
	$options['track_logged_user'] = isset( $input['track_logged_user'] ) && '1' === $input['track_logged_user']
		? true
		: false;

	return $options;
}
