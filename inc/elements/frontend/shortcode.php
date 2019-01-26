<?php

/**
 * Shortcode element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_shortcode_element( $elements ) {
	$elements[] = array(
		'id'          => 'shortcode',
		'category'    => 'content',
		'title'       => __( 'Shortcode', 'pagex' ),
		'description' => __( 'Display any custom shortcode', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_shortcode',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'shortcode',
						'title'       => __( 'Shortcode', 'pagex' ),
						'description' => __( 'Enter your shortcode', 'pagex' ),
						'type'        => 'text',
					)
				)
			),

		),
	);

	return $elements;
}

/**
 * Shortcode for shortcode element :)
 *
 * @param $atts
 *
 * @return string
 */
function pagex_shortcode( $atts ) {
	$atts = Pagex::get_dynamic_data($atts);

	$data = wp_parse_args( $atts, array(
		'shortcode' => '',
	) );

	if ( ! $data['shortcode'] ) {
		return;
	}

	return apply_filters( 'pagex_content', do_shortcode( $data['shortcode'] ) );
}