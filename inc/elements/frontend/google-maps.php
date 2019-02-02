<?php

/**
 * Google Maps element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_google_maps_element( $elements ) {
	$elements[] = array(
		'id'          => 'google_maps',
		'category'    => 'content',
		'title'       => __( 'Google Maps', 'pagex' ),
		'description' => __( 'Interactive Google map', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_google_maps',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'type',
						'title'   => __( 'Type', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'custom'  => __( 'Custom', 'pagex' ),
							'dynamic' => __( 'Dynamic', 'pagex' ),
						)
					),
					array(
						'id'        => 'meta_key',
						'title'     => __( 'Post Meta Key', 'pagex' ),
						'type'      => 'select',
						'options'   => pagex_get_post_custom_keys(),
						'condition' => array(
							'type' => array( 'dynamic' )
						),
					),
					array(
						'id'          => 'address',
						'title'       => __( 'Map Marker', 'pagex' ),
						'description' => __( 'Enter address or latitude & longitude. For example: 52.291266, 4.964126', 'pagex' ) . ' <a href="https://support.google.com/maps/answer/18539?co=GENIE.Platform%3DDesktop&hl=en" target="_blank">' . __( 'How to get the coordinates?', 'pagex' ) . '</a>',
						'type'        => 'text',
						'condition'   => array(
							'type' => array( 'custom' ),
						),
					),
					array(
						'id'          => 'mode',
						'title'       => __( 'Mode', 'pagex' ),
						'description' => __( 'Simple mode embedded the map via an iFrame. Advanced mode uses Maps JavaScript API.', 'pagex' ),
						'type'        => 'select',
						'options'     => array(
							'simple'   => __( 'Simple', 'pagex' ),
							'advanced' => __( 'Advanced', 'pagex' ),
						)
					),
					array(
						'id'          => 'zoom',
						'title'       => __( 'Zoom', 'pagex' ),
						'description' => __( 'A value from 1 (the world) to 21 (street level).', 'pagex' ),
						'type'        => 'number',
						'attributes'  => 'min="1" max="21" step="1"',
						'class'       => 'col-4',
					),
					array(
						'id'        => 'style',
						'title'     => __( 'Style', 'pagex' ),
						'type'      => 'select',
						'class'     => 'col-4',
						'options'   => array(
							''          => __( 'Default', 'pagex' ),
							'black'     => __( 'Black', 'pagex' ),
							'darkblue'  => __( 'Dark Blue', 'pagex' ),
							'greyscale' => __( 'Greyscale', 'pagex' ),
							'white'     => __( 'White', 'pagex' ),
						),
						'condition' => array(
							'mode' => array( 'advanced' ),
						),
					),
					array(
						'id'         => 'height',
						'title'      => __( 'Height', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'selector'   => '[el] .pagex-google-maps {height: [val]}',
						'class'      => 'col-4',
					),
					array(
						'id'        => 'scroll',
						'label'     => __( 'Use Scroll', 'pagex' ),
						'type'      => 'checkbox',
						'class'     => 'col-auto',
						'condition' => array(
							'mode' => array( 'advanced' ),
						),
					),
					array(
						'id'        => 'ui',
						'label'     => __( 'Disable default UI', 'pagex' ),
						'type'      => 'checkbox',
						'class'     => 'col-auto',
						'condition' => array(
							'mode' => array( 'advanced' ),
						),
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Google maps shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_google_maps( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'zoom'     => 15,
		'scroll'   => false,
		'ui'       => false,
		'address'  => 'New York',
		'mode'     => 'simple',
		'style'    => '',
		'meta_key' => '',
		'type'     => 'custom',
	) );

	$settings = Pagex::get_settings();

	$api = isset( $settings['apis']['google_maps'] ) ? $settings['apis']['google_maps'] : '';

	if ( ! $api ) {
		return '<div class="pagex-alert pagex-alert-warning"><div class="pagex-alert-wrapper"><h3>' . __( 'API key is not set', 'pagex' ) . '</h3>' . __( 'Google Maps API Key is required for "Google Maps" element.', 'pagex' ) . ' <a href="' . admin_url( 'admin.php?page=pagex' ) . '" class="text-underline" target="_blank">' . __( 'Enter API key', 'pagex' ) . '</a> ' . __( 'and then refresh page to apply changes.', 'pagex' ) . '</div></div>';
	}

	if ( $data['type'] == 'dynamic' ) {
		$data['address'] = get_post_meta( get_the_ID(), $data['meta_key'], true );
		if ( ! $data['address'] ) {
			return '';
		}
	}

	ob_start();

	echo '<div class="pagex-google-maps">';
	if ( $data['mode'] == 'simple' ) {
		$src = 'https://www.google.com/maps/embed/v1/place?key=' . $api . '&q=' . urlencode( $data['address'] ) . '&zoom=' . $data['zoom'];

		if ( wp_doing_ajax() ) {
			echo '<iframe class="pagex-google-maps-iframe pagex-iframe-lazy" frameborder="0" allowfullscreen src="' . $src . '"></iframe>';
		} else {
			echo '<iframe class="pagex-google-maps-iframe pagex-iframe-lazy" frameborder="0" allowfullscreen data-lazy-load="' . $src . '"></iframe>';
		}
	} else {
		$embed = array(
			'key'     => $api,
			'address' => $data['address'],
			'zoom'    => $data['zoom'],
			'scroll'  => $data['scroll'],
			'ui'      => $data['ui'],
			'style'   => $data['style'],
		);

		echo '<div class="pagex-google-maps-embed" data-google-map="' . urlencode( json_encode( $embed ) ) . '"></div>';
	}

	echo '</div>';

	return ob_get_clean();

}