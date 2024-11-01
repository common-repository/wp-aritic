<?php
/**
 * Shortcode definition
 *
 * @package wp-aritic
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

add_shortcode( 'aritic', 'wparitic_shortcode' );
add_shortcode( 'ariticcontent', 'wparitic_dwc_shortcode' );
add_shortcode( 'ariticvideo', 'wparitic_video_shortcode' );
add_shortcode( 'ariticform', 'wparitic_form_shortcode' );
add_shortcode( 'aritictags', 'wparitic_tags_shortcode' );
add_shortcode( 'ariticfocus', 'wparitic_focus_shortcode' );

/**
 * Handle aritic shortcode. Must include a type attribute.
 *
 * @param array       $atts    Shortcode attributes.
 * @param string|null $content Default content to be displayed.
 *
 * @return string
 */
function wparitic_shortcode( $atts, $content = null ) {
	$default = shortcode_atts(array(
		'type' => null,
	), $atts);

	switch ( $default['type'] ) {
		case 'form':
			return wparitic_form_shortcode( $atts );
		case 'content':
			return wparitic_dwc_shortcode( $atts, $content );
		case 'video':
			return wparitic_video_shortcode( $atts );
		case 'tags':
			return wparitic_tags_shortcode( $atts );
		case 'focus':
			return wparitic_focus_shortcode( $atts );
	}

	return false;
}

/**
 * Handle ariticform shortcode
 * example: [ariticform id="1"]
 * example: [aritic type="form" id="1"]
 *
 * @param  array $atts Shortcode attributes.
 *
 * @return string
 */
function wparitic_form_shortcode( $atts ) {
	$base_url = wparitic_option( 'base_url', '' );
	if ( '' === $base_url ) {
		return false;
	}

	$atts = shortcode_atts( array(
		'id' => '',
	), $atts );

	if ( empty( $atts['id'] ) ) {
		return false;
	}

	return '<script type="text/javascript" ' . sprintf(
		'src="%s/ma/form/generate.js?id=%s"',
		esc_url( $base_url ),
		esc_attr( $atts['id'] )
	) . '></script>';
}

/**
 * Dynamic content shortcode handling
 * example: [aritic type="content" slot="slot_name"]Default Content[/aritic]
 * example: [ariticcontent slot="slot_name"]Default Content[/ariticcontent]
 *
 * @param  array       $atts    Shortcode attributes.
 * @param  string|null $content Default content to be displayed.
 *
 * @return string
 */
function wparitic_dwc_shortcode( $atts, $content = null ) {
	$atts     = shortcode_atts( array(
		'slot' => '',
	), $atts, 'aritic' );

	return sprintf(
		'<div class="aritic-slot" data-slot-name="%s">%s</div>',
		esc_attr( $atts['slot'] ),
		esc_textarea( $content )
	);
}

/**
 * Video shortcode handling
 * example: [aritic type="video" gate-time="15" form-id="1" src="https://www.youtube.com/watch?v=QT6169rdMdk"]
 * example: [ariticvideo gate-time="15" form-id="1" src="https://www.youtube.com/watch?v=QT6169rdMdk"]
 *
 * @param  array $atts Shortcode attributes.
 *
 * @return string
 */
function wparitic_video_shortcode( $atts ) {
	$atts = shortcode_atts(array(
		'gate-time' => 15,
		'form-id' => '',
		'src' => '',
		'video-type' => '',
		'aritic-video' => 'true',
		'width' => 640,
		'height' => 360,
	), $atts);

	if ( empty( $atts['src'] ) ) {
		return __( 'You must provide a video source. Add a src="URL" attribute to your shortcode. Replace URL with the source url for your video.', 'wp-aritic' );
	}

	if ( empty( $atts['form-id'] ) && 'true' !== $atts['aritic-video'] ) {
		return __( 'You must provide a aritic form id. Add a form-id="#" attribute to your shortcode. Replace # with the id of the form you want to use.', 'wp-aritic' );
	}

	if ( preg_match( '/^.*((youtu.be)|(youtube.com))\/((v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))?\??v?=?([^#\&\?]*).*/', $atts['src'] ) ) {
		$atts['video-type'] = 'youtube';
	}
	if ( preg_match( '/^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/', $atts['src'] ) ) {
		$atts['video-type'] = 'vimeo';
	}
	if ( strtolower( substr( $atts['src'], -3 ) ) === 'mp4' ) {
		$atts['video-type'] = 'mp4';
	}

	if ( empty( $atts['video-type'] ) ) {
		return __( 'Please define a valid video type with video-type="#".', 'wp-aritic' );
	}

	return sprintf(
		'<video height="%1$s" width="%2$s"' . (empty( $atts['form-id'] ) ? '' : ' data-form-id="%3$s"') . ' data-gate-time="%4$s" data-aritic-video="%5$s">' .
			'<source type="video/%6$s" src="%7$s" />' .
		'</video>',
		esc_attr( $atts['height'] ),
		esc_attr( $atts['width'] ),
		esc_attr( $atts['form-id'] ),
		esc_attr( $atts['gate-time'] ),
		esc_attr( $atts['aritic-video'] ),
		esc_attr( $atts['video-type'] ),
		esc_attr( $atts['src'] )
	);
}

/**
 * Handle aritic tags by Wordpress shortcodes
 * example: [aritic type="tags" values="addtag,-removetag"]
 * example: [aritictags values="addtag,-removetag"]
 *
 * @param  array $atts Shortcode attributes.
 *
 * @return string
 */
function wparitic_tags_shortcode( $atts ) {
	$base_url = wparitic_option( 'base_url', '' );
	if ( '' === $base_url ) {
		return false;
	}

	$atts = shortcode_atts( array(
		'values' => '',
	), $atts );

	if ( empty( $atts['values'] ) ) {
		return false;
	}

	return sprintf(
		'<img src="%s/ma/atracking.gif?tags=%s" alt="%s" />',
		esc_url( $base_url ),
		esc_attr( $atts['values'] ),
		esc_attr__( 'Aritic Tags', 'wp-aritic' )
	);
}

/**
 * Handle aritic focus itens on Wordpress Page
 * example: [aritic type="focus" id="1"]
 *
 * @param  array $atts Shortcode attributes.
 *
 * @return string
 */
 
function wparitic_focus_shortcode( $atts ) {
	$base_url = wparitic_option( 'base_url', '' );
	if ( '' === $base_url ) {
		return false;
	}

	$atts = shortcode_atts( array(
		'id' => '',
	), $atts );

	if ( empty( $atts['id'] ) ) {
		return false;
	}

	return '<script type="text/javascript" ' . sprintf(
		'src="%s/ma/focus/%s.js"',
		esc_url( $base_url ),
		esc_attr( $atts['id'] )
	) . ' async="async"></script>';
}
