<?php

/**
 * Product Meta element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_product_additional_information_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_product_additional_information',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Product Additional Information', 'pagex' ),
		'description' => __( 'Output attributes content for single product', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_product_additional_information',
		'options'     => array(
			array(
				'params' => array(),
			),
		),
	);

	return $elements;
}

/**
 * Product Meta shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_product_additional_information( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'type' => 'result_count',
	) );

	ob_start();

	woocommerce_product_additional_information_tab();

	return ob_get_clean();
}
