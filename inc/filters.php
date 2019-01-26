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
		$string = urlencode( json_encode( $original_string ) );
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
		$rows   = json_decode( urldecode( $original_string ), true );
		$string = array();
		foreach ( $rows as $key => $value ) {
			if ( in_array( $key, array(
				'before',
				'after',
			) ) ) {
				$string[ $key ] = array( 'value' => $value, 'translate' => true );
			} else {
				$string[ $key ] = array( 'value' => $value, 'translate' => false );
			}
		}
	}

	return $string;
}

add_filter( 'wpml_pb_shortcode_decode', 'pagex_wpml_shortcode_decode', 10, 3 );
