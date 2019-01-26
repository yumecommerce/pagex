<?php

/**
 * Add to Cart element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_add_to_cart_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_add_to_cart',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_post_tmp', 'pagex_excerpt_tmp' ),
		'title'       => __( 'Add To Cart', 'pagex' ),
		'description' => __( 'Output the add to cart button for single and archive products', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_add_to_cart',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'type',
						'title'   => __( 'Button Type', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'single'  => __( 'For Single Product Page', 'pagex' ),
							'archive' => __( 'For Archive Product Page', 'pagex' ),
						)
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Add To Cart Button', 'pagex' ),
					),
					array(
						'id'       => 'a',
						'type'     => 'button_style',
						'selector' => '.add_to_cart_button',
					),
					array(
						'id'       => 'ma',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-4',
						'action'   => 'css',
						'selector' => '[el] .button {margin: [val]}',
					),
					array(
						'id'       => 'gf',
						'title'    => __( 'Align', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-4',
						'action'   => 'css',
						'selector' => '[el] .pagex-add-to-cart-archive {justify-content: [val]}',
						'options'  => array(
							''           => __( 'Default', 'pagex' ),
							'flex-start' => __( 'Left', 'pagex' ),
							'center'     => __( 'Center', 'pagex' ),
							'flex-end'   => __( 'Right', 'pagex' ),
						)
					),
					array(
						'type'      => 'row-start',
						'condition' => array(
							'type' => array( 'archive' ),
						),
					),
					array(
						'type'  => 'heading',
						'title' => __( 'View Cart Button', 'pagex' ),
					),
					array(
						'id'       => 'la',
						'label'    => __( 'Hide "Add To Cart Button" after product added via AJAX', 'pagex' ),
						'type'     => 'checkbox',
						'action'   => 'css',
						'selector' => '[el] .added {display: none}',
					),
					array(
						'id'       => 'v',
						'type'     => 'button_style',
						'selector' => '.added_to_cart',
					),
					array(
						'type' => 'row-end',
					),

				),
			),
		),
	);

	return $elements;
}

/**
 * Add to Cart shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_add_to_cart( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'type' => 'single',
	) );

	ob_start();
	if ( $data['type'] == 'single' ) {
		woocommerce_template_single_add_to_cart();
	} else {
		woocommerce_template_loop_add_to_cart();
	}

	$html = ob_get_clean();

	if ( $html ) {
		$html = '<div class="pagex-add-to-cart-' . $data['type'] . '">' . $html . '</div>';
	}

	return $html;
}
