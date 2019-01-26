<?php

/**
 * Sale Flash  element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_sale_flash_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_sale_flash',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_post_tmp', 'pagex_excerpt_tmp' ),
		'title'       => __( 'Sale Flash', 'pagex' ),
		'description' => __( 'Output the product sale flash', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_sale_flash',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'       => 'ab',
						'type'     => 'checkbox',
						'label'    => __( 'Absolute position', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] {position: absolute}',
					),
					array(
						'id'       => 'cl',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .onsale {color: [val]}',
					),
					array(
						'id'       => 'bg',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .onsale {background: [val]}',
					),
					array(
						'id'       => 'bc',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .onsale {border-color: [val]}',
					),
					array(
						'id'       => 'bw',
						'type'     => 'text',
						'title'    => __( 'Border Width', 'pagex' ),
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .onsale {border-width: [val]; border-style: solid;}',
					),
					array(
						'id'       => 'br',
						'type'     => 'text',
						'title'    => __( 'Border Radius', 'pagex' ),
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .onsale {border-radius: [val]}',
					),
					array(
						'id'       => 'pa',
						'type'     => 'text',
						'title'    => __( 'Padding', 'pagex' ),
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .onsale {padding: [val]}',
					),
					array(
						'id'   => 'typo',
						'type' => 'typography',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Sale Flash shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_sale_flash( $atts ) {

	ob_start();

	wc_get_template( 'single-product/sale-flash.php' );

	return ob_get_clean();
}