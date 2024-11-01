<?php
/**
 * Plugin Name: WP Aritic
 * Plugin URI: https://aritic.com
 * Description: This plugin will allow you to add Aritic tracking to your site
 * Version: 2.2.5
 * Author: Aritic
 * Author URI: http://aritic.com
 * Text Domain: wp-aritic
 * License: GPL2
 *
 * @package wp-aritic
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

// Store plugin directory.
define( 'VPARITIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
// Store plugin main file path.
define( 'VPARITIC_PLUGIN_FILE', __FILE__ );

add_action( 'admin_menu', 'wparitic_settings' );
add_action( 'plugins_loaded', 'wparitic_injector' );

include_once( VPARITIC_PLUGIN_DIR . '/shortcodes.php' );

/**
 * Declare option page
 */
function wparitic_settings() {
	include_once( VPARITIC_PLUGIN_DIR . '/options.php' );

	add_options_page(
		__( 'WP Aritic Settings', 'wp-aritic' ),
		__( 'WPAritic', 'wp-aritic' ),
		'manage_options',
		'wparitic',
		'wparitic_options_page'
	);
}

/**
 * Settings Link in the ``Installed Plugins`` page
 *
 * @param  array  $links array of plugin action links.
 * @param  string $file  Path to the plugin file relative to the plugins directory.
 *
 * @return array
 */
function wparitic_plugin_actions( $links, $file ) {
	if ( plugin_basename( VPARITIC_PLUGIN_FILE ) === $file && function_exists( 'admin_url' ) ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=wparitic' ),
			__( 'Settings' )
		);
		// Add the settings link before other links.
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links', 'wparitic_plugin_actions', 10, 2 );

/**
 * Retrieve one of the wparitic options but sanitized
 *
 * @param  string $option  Option name to be retrieved (base_url, script_location).
 * @param  mixed  $default Default option value return if not exists.
 *
 * @return string
 *
 * @throws InvalidArgumentException Thrown when the option name is not given.
 */
function wparitic_option( $option, $default = null ) {
	$options = get_option( 'wparitic_options' );

	switch ( $option ) {
		case 'script_location':
			return ! isset( $options[ $option ] ) ? 'header' : $options[ $option ];
		case 'fallback_activated':
			return isset( $options[ $option ] ) ? (bool) $options[ $option ] : true;
		case 'track_logged_user':
			return isset( $options[ $option ] ) ? (bool) $options[ $option ] : false;
		default:
			if ( ! isset( $options[ $option ] ) ) {
				if ( isset( $default ) ) {
					return $default;
				}

				throw new InvalidArgumentException( 'You must give a valid option name !' );
			}

			return $options[ $option ];
	}
}

/**
 * Apply JS tracking to the right place depending script_location.
 *
 * @return void
 */
function wparitic_injector() {
	$script_location = wparitic_option( 'script_location' );
	if ( 'header' === $script_location ) {
		add_action( 'wp_head', 'wparitic_inject_script' );
	} else {
		add_action( 'wp_footer', 'wparitic_inject_script' );
	}

	if ( true === wparitic_option( 'fallback_activated', false ) ) {
		add_action( 'wp_footer', 'wparitic_inject_noscript' );
	}
}

/**
 * Writes Tracking JS to the HTML source
 *
 * @return void
 */
function wparitic_inject_script() {
	$base_url = wparitic_option( 'base_url', '' );
	if ( empty( $base_url ) ) {
		return;
	}

	$attrs = wparitic_get_tracking_attributes();

	?><script src = '<?php echo esc_url( $base_url ); ?>/ma/patc.js' async></script><script type="text/javascript">
	(function(w,d,t,u,n,a,m){w['AriticTrackingObject']=n;
		w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
		m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
	})(window,document,'script','<?php echo esc_url( $base_url ); ?>/ma/atc.js','at');
	var _aaq = window._aaq = window._aaq || [];
<?php if(count( $attrs ) > 0) : ?>
        _aaq.push(['setContactFields','<?php echo wp_json_encode( $attrs ); ?>]);
<?php endif; ?>
//	at('send', 'pageview'<?php echo count( $attrs ) > 0?', ' . wp_json_encode( $attrs ):'' ?>);
</script>
	<?php
}

/**
 * Writes Tracking image fallback to the HTML source
 * This is a separated function because <noscript> tags are not allowed in header !
 *
 * @return void
 */
function wparitic_inject_noscript() {
	$base_url = wparitic_option( 'base_url', '' );
	if ( empty( $base_url ) ) {
		return;
	}

	$url_query = wparitic_get_url_query();
	$payload = rawurlencode( base64_encode( serialize( $url_query ) ) );
	?>
	<noscript>
		<img src="<?php echo esc_url( $base_url ); ?>/ma/atracking.gif?d=<?php echo esc_attr( $payload ); ?>"  style="display:none;" alt="" />
	</noscript>
	<?php
}

/**
 * Builds and returns additional data for URL query
 *
 * @return array
 */
function wparitic_get_url_query() {
	global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

	$attrs = wparitic_get_tracking_attributes();

	$attrs['language']   = get_locale();
	$attrs['page_url']   = $current_url;
	$attrs['page_title'] = function_exists( 'wp_get_document_title' )
		? wp_get_document_title()
		: wp_title( '&raquo;', false );
	$attrs['referrer']   = function_exists( 'wp_get_raw_referer' )
		? wp_get_raw_referer()
		: null;
	if ( false === $attrs['referrer'] ) {
		$attrs['referrer'] = $current_url;
	}

	return $attrs;
}

/**
 * Create custom query parameters to be injected inside tracking
 *
 * @return array
 */
function wparitic_get_tracking_attributes() {
	$attrs = wparitic_get_user_query();

	/**
	 * Update / add data to be send withing Aritic tracker
	 *
	 * Default data only contains the 'language' key but every added key to the
	 * array will be sent to Aritic.
	 *
	 * @since 2.1.0
	 *
	 * @param array $attrs Attributes to be filters, default ['language' => get_locale()]
	 */
	return apply_filters( 'wparitic_tracking_attributes', $attrs );
}

/**
 * Extract logged user informations to be send within Aritic tracker
 *
 * @return array
 */
function wparitic_get_user_query() {
	$attrs = array();

	if (
		true === wparitic_option( 'track_logged_user', false ) &&
		is_user_logged_in()
	) {
		$current_user = wp_get_current_user();
		$attrs['email']	 = $current_user->user_email;
		$attrs['firstname']  = $current_user->user_firstname;
		$attrs['lastname']  = $current_user->user_lastname;

		// Following Aritic fields has to be created manually and the fields must match these names.
		$attrs['wp_user']  = $current_user->user_login;
		$attrs['wp_alias']  = $current_user->display_name;
		$attrs['wp_registration_date'] = date(
			'Y-m-d',
			strtotime( $current_user->user_registered )
		);
	}

	return $attrs;
}
