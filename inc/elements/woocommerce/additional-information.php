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
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Heading', 'pagex' ),
					),
					array(
						'id'       => 'qw',
						'label'    => __( 'Hide Heading', 'pagex' ),
						'type'     => 'checkbox',
						'action'   => 'css',
						'selector' => '[el] h2 {display: none}',
					),
					array(
						'id'       => 'we',
						'type'     => 'typography',
						'selector' => 'h2',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] h2 {margin: [val]}',
					),
				),
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
