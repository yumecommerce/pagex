<?php

/**
 * Encode data param to json for translated shortcodes
 *
 * @param $string
 * @param $encoding
 * @param $original_string
 *
 * @return string
 */
function pagex_wpml_shortcode_encode( $string, $encoding, $original_string ) {
	if ( 'pagex_encoded_json' === $encoding ) {
		$output = array();

		foreach ( $original_string as $key => $value ) {
			$parts = explode( '__', $key );
			// if repeated value
			if ( count( $parts ) > 1 ) {
				$output[ $parts[0] ][ $parts[1] ][ $parts[2] ] = $value;
			} else {
				$output[ $key ] = $value;
			}
		}

		$string = urlencode( json_encode( $output ) );
	}

	return $string;
}

add_filter( 'wpml_pb_shortcode_encode', 'pagex_wpml_shortcode_encode', 10, 3 );

/**
 * Decode json string of dynamic elements to make some string params translatable in WPML
 *
 * @param $string
 * @param $encoding
 * @param $original_string
 *
 * @return array
 */
function pagex_wpml_shortcode_decode( $string, $encoding, $original_string ) {
	if ( 'pagex_encoded_json' === $encoding ) {
		$rows = json_decode( urldecode( $original_string ), true );

		$string = array();

		foreach ( $rows as $i => $row ) {
			// if repeated param
			if ( is_array( $row ) && $i == 'items' ) {
				foreach ( $row as $key => $value ) {
					if ( is_array( $value ) ) {
						foreach ( $value as $k => $v ) {
							if ( in_array( $k, array(
								// list of id params which needs to be translated
								// form element
								'label',
								'placeholder',
								'options',
								'html',

								// google maps
								'info',

								// post data
								'before',
								'custom_text',
							) ) ) {
								$string[ $i . '__' . $key . '__' . $k ] = array( 'value' => $v, 'translate' => true );
							} else {
								$string[ $i . '__' . $key . '__' . $k ] = array( 'value' => $v, 'translate' => false );
							}
						}
					}
				}
			} else {
				if ( in_array( $i, array(
					// list of id params which needs to be translated
					// form, login form, search form
					'button_text',
					'placeholder',

					// login form
					'label_username',
					'label_password',

					// posts, menu cart
					'title',

					// post title
					'before',
					'after',

					// post navigation
					'prev_text',
					'next_text'
				) ) ) {
					$string[ $i ] = array( 'value' => $row, 'translate' => true );
				} else {
					$string[ $i ] = array( 'value' => $row, 'translate' => false );
				}
			}

		}
	}

	return $string;
}

add_filter( 'wpml_pb_shortcode_decode', 'pagex_wpml_shortcode_decode', 10, 3 );

/**
 * This filter prevent Pods plugin from affecting default WP logic meta fields
 * and also fix issues with getting saved meta media with WPML plugin
 */
add_filter( 'pods_meta_handler_get', '__return_false' );

/**
 * Hide Pods Shortcode button from edit post screen
 */
function pagex_remove_pods_shortcode_button() {
	if ( class_exists( 'PodsInit' ) ) {
		remove_action( 'media_buttons', array( PodsInit::$admin, 'media_button' ), 12 );
	}
}

add_action( 'admin_init', 'pagex_remove_pods_shortcode_button', 14 );

/**
 * A workaround for upload validation
 *
 * @param $data
 * @param $file
 * @param $filename
 * @param $mimes
 *
 * @return array
 */
function pagex_check_filetype_and_ext( $data, $file, $filename, $mimes ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
		return $data;
	}

	$registered_file_types = array(
		'woff' => 'font/woff|application/font-woff|application/x-font-woff|application/octet-stream',
	);

	$filetype = wp_check_filetype( $filename, $mimes );

	if ( ! isset( $registered_file_types[ $filetype['ext'] ] ) ) {
		return $data;
	}

	return array(
		'ext'             => $filetype['ext'],
		'type'            => $filetype['type'],
		'proper_filename' => $data['proper_filename'],
	);
}

add_filter( 'wp_check_filetype_and_ext', 'pagex_check_filetype_and_ext', 10, 4 );


/**
 * Add allowed mime types and file extensions
 *
 * @param $mine_types
 *
 * @return mixed
 */
function pagex_upload_mimes( $mine_types ) {
	$mine_types['woff'] = 'font/woff|application/font-woff|application/x-font-woff|application/octet-stream';

	return $mine_types;
}

add_filter( 'upload_mimes', 'pagex_upload_mimes' );
