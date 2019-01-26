<?php

/**
 * Catalog Data element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_catalog_data_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_catalog_data',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Catalog Elements', 'pagex' ),
		'description' => __( 'Output the result count text or product sorting options', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_catalog_data',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'type',
						'title'   => __( 'Type', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'result_count' => __( 'Result Count', 'pagex' ),
							'sorting'      => __( 'Sorting Options', 'pagex' ),
						)
					),
					array(
						'id'       => 'typo',
						'type'     => 'typography',
						'selector' => '',
					),
					array(
						'id'       => 'cl',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-6',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] {color: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Catalog Data shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_catalog_data( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'type' => 'result_count',
	) );

	ob_start();
	if ( $data['type'] == 'result_count' ) {
		woocommerce_result_count();
	} else {
		woocommerce_catalog_ordering();
	}

	return ob_get_clean();
}

