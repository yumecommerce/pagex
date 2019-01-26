<?php

/**
 * WC notices element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_checkout_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_checkout',
		'category'    => 'woo-theme',
		'title'       => __( 'WooCommerce Checkout', 'pagex' ),
		'description' => __( 'Checkout form for checkout page', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_checkout',
		'options'     => array(
			array(
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Navigation', 'pagex' ),
					),
					array(
						'id'       => 'nt',
						'type'     => 'typography',
						'selector' => '.woocommerce-MyAccount-navigation',
					),
					array(
						'id'       => 'pa',
						'title'    => __( 'Padding', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .woocommerce-MyAccount-navigation a {padding: [val]}',
					),
					array(
						'id'       => 'lc',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .woocommerce-MyAccount-navigation a {color: [val]}',
					),
					array(
						'id'       => 'cx',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .woocommerce-MyAccount-navigation a:hover, [el] .woocommerce-MyAccount-navigation li.is-active a {color: [val]}',
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Content', 'pagex' ),
					),
					array(
						'id'       => 'cl',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .woocommerce-MyAccount-content {color: [val]}',
					),
					array(
						'id'       => 'ds',
						'title'    => __( 'Links Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .woocommerce-MyAccount-content a:not(.button) {color: [val]}',
					),
					array(
						'id'       => 'as',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .woocommerce-MyAccount-content a:not(.button):hover {color: [val]}',
					),
				),
			),
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
function pagex_woo_checkout( $atts ) {

	ob_start();

	// add woocommerce-checkout wrapper in case editing post template with checkout page as a preview to avoid issues with styles
	echo '<div class="pagex-woo-checkout woocommerce-checkout">';
	echo do_shortcode( '[woocommerce_checkout]' );
	echo '</div>';

	return ob_get_clean();
}