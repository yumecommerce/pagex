<?php

/**
 * Product Thumbnail element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_product_thumbnail_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_product_thumbnail',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_excerpt_tmp', 'pagex_post_tmp' ),
		'title'       => __( 'Product Thumbnail', 'pagex' ),
		'description' => __( 'Output the product thumbnail image, or the placeholder if not set', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_product_thumbnail',
		'options'     => array(
			array(
				'params' => array(
					array(
						'type' => 'heading',
						'title' => __( 'Product Thumbnail', 'pagex' ),
						'description' => __( 'Note! The size of the product thumbnail controlled by global WooCommerce settings in the customizer', 'pagex' ),
					),

					array(
						'id'       => 'br',
						'type'     => 'text',
						'title'    => __( 'Border Radius', 'pagex' ),
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] img {border-radius: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Product Thumbnail shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_product_thumbnail( $atts ) {
	ob_start();
	echo '<div class="pagex-product-thumbnail">';
	woocommerce_template_loop_product_link_open();
	woocommerce_template_loop_product_thumbnail();
	woocommerce_template_loop_product_link_close();
	echo '</div>';

	return ob_get_clean();
}