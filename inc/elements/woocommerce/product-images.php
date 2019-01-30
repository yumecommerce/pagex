<?php

/**
 * Product Images element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_product_images_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_product_images',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Product Images', 'pagex' ),
		'description' => __( 'Output product images for a single product', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_product_images',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'zoom',
						'type'        => 'checkbox',
						'label'       => __( 'Turn Off Zoom Effect', 'pagex' ),
						'description' => __( 'This option will be applied only after saving and refreshing the page.', 'pagex' ),
						'value'       => 'off',
					),
					array(
						'id'          => 'lightbox',
						'type'        => 'checkbox',
						'label'       => __( 'Turn Lightbox Effect', 'pagex' ),
						'description' => __( 'This option will be applied only after saving and refreshing the page.', 'pagex' ),
						'value'       => 'off',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Lightbox', 'pagex' ),
					),
					array(
						'id'       => 'qw',
						'type'     => 'checkbox',
						'label'    => __( 'Hide Caption', 'pagex' ),
						'action'   => 'css',
						'selector' => '.pswp__caption {display: none !important}'
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '.pswp__bg {background: [val] !important}',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Caption Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '.pswp__top-bar, .pswp__caption {background: [val] !important}',
					),
					array(
						'id'       => 'rt',
						'title'    => __( 'Caption Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '.pswp__caption {color: [val] !important}',
					),
					array(
						'id'         => 'ty',
						'title'      => __( 'Image Border Radius', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '.pswp__img {border-radius: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Product Images shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_product_images( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'zoom'     => 'on',
		'lightbox' => 'on',
	) );

	// turn off zoom if option is set
	if ( $data['zoom'] == 'off' ) {
		wp_deregister_script( 'zoom' );
	}

	// turn lightbox if option is set
	if ( $data['lightbox'] == 'off' ) {
		wp_deregister_script( 'photoswipe-ui-default' );
	}

	ob_start();

	if ( is_singular() ) {
		woocommerce_show_product_images();

		if ( wp_doing_ajax() ) {
			echo "<script>jQuery( '.woocommerce-product-gallery' ).each( function() {jQuery( this ).wc_product_gallery();} );</script>";
		}
	} else {
		woocommerce_show_product_thumbnails();
	}

	return ob_get_clean();
}