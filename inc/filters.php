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
				$output[$parts[0]][$parts[1]][$parts[2]] = $value;
			} else {
				$output[$key] = $value;
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
			if ( is_array( $row ) ) {
				foreach ( $row as $key => $value ) {
					if ( is_array( $value ) ) {
						foreach ( $value as $k => $v ) {
							if ( in_array( $k, array(
								// list of id params which needs to be translated
								// form element
								'label',
								'placeholder',
								'options',

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

					// posts
					'title',

					// post title
					'before',
					'after',
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
 *
 */
add_filter( 'pods_meta_handler_get', '__return_false' );
