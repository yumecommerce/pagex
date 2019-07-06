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
		'info'        => 'https://github.com/yumecommerce/pagex/wiki/Google-Maps',
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

	$map_styles = array(
		'black' => '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]',

		'darkblue' => '[{"elementType":"geometry","stylers":[{"color":"#1d2c4d"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#8ec3b9"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#1a3646"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#64779e"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"landscape.man_made","elementType":"geometry.stroke","stylers":[{"color":"#334e87"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#023e58"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#283d6a"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#6f9ba5"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#023e58"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#3C7680"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#304a7d"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#2c6675"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#255763"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#b0d5ce"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"color":"#023e58"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#283d6a"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#3a4762"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#0e1626"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#4e6d70"}]}]',

		'greyscale' => '[{"elementType":"geometry","stylers":[{"color":"#f5f5f5"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f5f5"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#dadada"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#c9c9c9"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]}]',

		'white' => '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]',
	);

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

		if ( $data['style'] ) {
			$data['style'] = $map_styles[ $data['style'] ];
		}

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