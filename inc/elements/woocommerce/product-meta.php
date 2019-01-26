<?php

/**
 * Product Meta element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_product_meta_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_product_meta',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Product Meta', 'pagex' ),
		'description' => __( 'Output standard product meta: SKU, category, tags', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_product_meta',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'       => 'qw',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] {color: [val]}',
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Links Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a {color: [val]}',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Links Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a:hover {color: [val]}',
					),
					array(
						'id'       => 'rt',
						'type'     => 'typography',
						'selector' => '',
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
function pagex_woo_product_meta( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'type' => 'result_count',
	) );

	ob_start();

	woocommerce_template_single_meta();

	return ob_get_clean();
}

