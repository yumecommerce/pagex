<?php

/**
 * WC notices element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_notices_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_notices',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'WooCommerce Notices', 'pagex' ),
		'description' => __( 'Outputs all queued notices on WC pages', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_notices',
		'options'     => array(
			array(
				'params' => array(
					array(
						'type' => 'heading',
						'title' => __( 'WooCommerce Notices', 'pagex' ),
						'description' => __( 'This element has no preview since the content of the element depends on WooCommerce actions like Add to Cart message or WooCommerce error and info messages.', 'pagex' ),
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
function pagex_woo_notices( $atts ) {

	ob_start();

	wc_print_notices();

	$html = ob_get_clean();

	if ( $html ) {
		$html = '<div class="woocommerce-notices-wrapper">' . $html . '</div>';
	}

	return $html;
}
