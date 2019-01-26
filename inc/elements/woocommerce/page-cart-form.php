<?php

/**
 * WC notices element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_cart_form_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_cart_form',
		'category'    => 'woo-theme',
		'title'       => __( 'WooCommerce Cart Form', 'pagex' ),
		'description' => __( 'Cart form for a cart page', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_cart_form',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'       => 'bc',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] table.cart td {border-color: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Delete Icon', 'pagex' ),
					),
					array(
						'id'       => 'dc',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .remove {color: [val]}',
					),
					array(
						'id'       => 'dch',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .remove:hover {color: [val]}',
					),
					array(
						'id'       => 'db',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .remove {background: [val]}',
					),
					array(
						'id'       => 'dbh',
						'title'    => __( 'Background on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .remove:hover {background: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Product Name', 'pagex' ),
					),
					array(
						'id'       => 'pc',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .product-name a {color: [val]}',
					),
					array(
						'id'       => 'pch',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .product-name a:hover {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Product Image', 'pagex' ),
					),
					array(
						'id'       => 'is',
						'title'    => __( 'Image Width', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .product-thumbnail img, [el] .product-thumbnail {width: [val] !important}',
					),
					array(
						'id'       => 'ibr',
						'title'    => __( 'Border Radius', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .product-thumbnail img {border-radius: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Apply Coupon Button', 'pagex' ),
					),
					array(
						'id'       => 'ac',
						'type'     => 'button_style',
						'selector' => '[name="apply_coupon"]',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Update Cart Button', 'pagex' ),
					),
					array(
						'id'       => 'uc',
						'type'     => 'button_style',
						'selector' => '[name="update_cart"]',
					),
				),
			),
			array(
				'title'  => __( 'Cart Totals', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Basic Block', 'pagex' ),
					),
					array(
						'id'         => 'cp',
						'title'      => __( 'Padding', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .cart-collaterals {padding: [val]}',
					),
					array(
						'id'         => 'cb',
						'title'      => __( 'Border Width', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .cart-collaterals {border-width: [val]}',
					),
					array(
						'id'         => 'cbr',
						'title'      => __( 'Border Radius', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .cart-collaterals {border-radius: [val]}',
					),
					array(
						'id'       => 'cbc',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .cart-collaterals {border-color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Cart Totals Heading', 'pagex' ),
					),
					array(
						'id'       => 'ct',
						'type'     => 'typography',
						'selector' => '.cart_totals h2',
					),
					array(
						'id'       => 'cc',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .cart_totals h2 {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Proceed to Checkout Button', 'pagex' ),
					),
					array(
						'id'       => 'bb',
						'type'     => 'button_style',
						'selector' => '.checkout-button',
					),
				)
			)
		),
	);

	return $elements;
}

/**
 * WC notices shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_cart_form( $atts ) {

	// remove default cross_sell since we have it in a posts element
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

	ob_start();

	// add woocommerce-cart wrapper in case editing post template with cart page as a preview to avoid issues with styles
	echo '<div class="woocommerce-cart">';
	echo do_shortcode( '[woocommerce_cart]' );
	echo '</div>';

	return ob_get_clean();
}