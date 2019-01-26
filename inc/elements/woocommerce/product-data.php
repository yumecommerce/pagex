<?php

/**
 * Product Data element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_product_data_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_product_data',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_post_tmp', 'pagex_excerpt_tmp' ),
		'title'       => __( 'Product Data', 'pagex' ),
		'description' => __( 'Output stock, price, rating, SKU', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_product_data',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'type',
						'title'   => __( 'Type', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'stock'  => __( 'Stock', 'pagex' ),
							'price'  => __( 'Price', 'pagex' ),
							'rating' => __( 'Rating', 'pagex' ),
							'sku'    => __( 'SKU', 'pagex' ),
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
					array(
						'type'      => 'row-start',
						'condition' => array(
							'type' => array( 'price' ),
						),
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Sale Price', 'pagex' ),
					),
					array(
						'id'       => 'sdb',
						'type'     => 'checkbox',
						'label'    => __( 'Display sale price as a new line', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] del {display: block}',
					),
					array(
						'id'       => 'scl',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-6',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] del {color: [val]}',
					),
					array(
						'id'       => 'slt',
						'type'     => 'typography',
						'selector' => 'del',
					),
					array(
						'type' => 'row-end',
					),



					array(
						'type'      => 'row-start',
						'condition' => array(
							'type' => array( 'rating' ),
						),
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Rating', 'pagex' ),
					),
					array(
						'id'       => 'sts',
						'title'    => __( 'Star Size', 'pagex' ),
						'class'    => 'col-6',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] svg {width: [val]; height: [val]}',
					),
					array(
						'id'       => 'stm',
						'title'    => __( 'Margin', 'pagex' ),
						'class'    => 'col-6',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .star-rating {margin: [val]}',
					),
					array(
						'id'       => 'stn',
						'type'     => 'checkbox',
						'label'    => __( 'Hide customer reviews link', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .woocommerce-review-link {display: none}',
					),
					array(
						'id'       => 'sti',
						'type'     => 'checkbox',
						'label'    => __( 'Make reviews link inline', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .woocommerce-product-rating {display: flex}',
					),
					array(
						'id'       => 'strl',
						'title'    => __( 'Link', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .woocommerce-review-link {color: [val]}',
					),
					array(
						'id'       => 'strlh',
						'title'    => __( 'Link on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .woocommerce-review-link:hover {color: [val]}',
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
 * Result Count shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_product_data( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'type' => 'stock',
	) );

	global $product;
	ob_start();

	switch ( $data['type'] ) {
		case 'stock':
			echo wc_get_stock_html( $product );
			break;
		case 'price':
			woocommerce_template_single_price();
			break;
		case 'rating':
			woocommerce_template_single_rating();
			break;
		case 'sku':
			if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) {
				echo '<span class="sku">' . ( $sku = $product->get_sku() ) ? $sku : "" . '</span>';
			}
			break;

	}

	return ob_get_clean();
}