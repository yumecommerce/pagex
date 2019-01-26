<?php

/**
 * Product Data Tabs element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_product_data_tabs_element( $elements ) {
	$elements[] = array(
		'id'          => 'woo_product_data_tabs',
		'category'    => 'woo-theme',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Product Data Tabs', 'pagex' ),
		'description' => __( 'Output product tabs for a single product', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_product_data_tabs',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'    => 'hide_desc',
						'label' => __( 'Hide Description Tab', 'pagex' ),
						'type'  => 'checkbox',
					),
					array(
						'id'    => 'hide_info',
						'label' => __( 'Hide Additional Information Tab', 'pagex' ),
						'type'  => 'checkbox',
					),
					array(
						'id'    => 'hide_reviews',
						'label' => __( 'Hide Reviews Tab', 'pagex' ),
						'type'  => 'checkbox',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Tab Titles', 'pagex' ),
					),
					array(
						'id'       => 'ty',
						'type'     => 'typography',
						'selector' => '.tabs a',
					),
					array(
						'id'       => 'cl',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .tabs a {color: [val]}',
					),
					array(
						'id'       => 'clh',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .tabs a:hover {color: [val]}',
					),
					array(
						'id'       => 'cla',
						'title'    => __( 'Active Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .tabs .active a {color: [val]}',
					),
					array(
						'id'       => 'bg',
						'title'    => __( 'Background Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .tabs a {background: [val]}',
					),
					array(
						'id'       => 'bgh',
						'title'    => __( 'Background on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .tabs a:hover {background: [val]}',
					),
					array(
						'id'       => 'bga',
						'title'    => __( 'Active Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .tabs .active a {background: [val]}',
					),
					array(
						'id'       => 'bc',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .tabs a {border-color: [val]}',
					),
					array(
						'id'       => 'bch',
						'title'    => __( 'Border on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .tabs a:hover {border-color: [val]}',
					),
					array(
						'id'       => 'bca',
						'title'    => __( 'Active Border', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .tabs .active a {border-color: [val]}',
					),
					array(
						'id'       => 'br',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .tabs a {border-radius: [val]}',
					),
					array(
						'id'       => 'bw',
						'title'    => __( 'Border Width', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] .tabs a {border-width: [val]}',
					),
					array(
						'id'       => 'pa',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .tabs a {padding: [val]}',
					),
					array(
						'id'       => 'ma',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .tabs a {margin: [val]}',
					),
					array(
						'id'       => 'al',
						'title'    => __( 'Alignment', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .tabs {justify-content: [val]}',
						'options'  => array(
							''              => __( 'Left', 'pagex' ),
							'center'        => __( 'Center', 'pagex' ),
							'flex-end'      => __( 'Right', 'pagex' ),
							'space-between' => __( 'Justify', 'pagex' ),
						)
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Tab Content', 'pagex' ),
					),
					array(
						'id'       => 'hct',
						'label'    => __( 'Hide tab content titles', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .panel h2 {display: none}',
						'type'     => 'checkbox',
					),
					array(
						'id'       => 'tc',
						'type'     => 'typography',
						'selector' => '.panel',
					),


				),
			),
		),
	);

	return $elements;
}

/**
 * Product Data Tabs shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_product_data_tabs( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'hide_desc'    => false,
		'hide_info'    => false,
		'hide_reviews' => false,
	) );

	$hide_tabs = array();

	if ( $data['hide_desc'] ) {
		$hide_tabs[] = 'description';
	}

	if ( $data['hide_info'] ) {
		$hide_tabs[] = 'additional_information';
	}

	if ( $data['hide_reviews'] ) {
		$hide_tabs[] = 'reviews';
	}

	if ( ! empty( $hide_tabs ) ) {
		// remove tabs via woo filter
		add_filter( 'woocommerce_product_tabs', function ( $tabs ) use ( $hide_tabs ) {
			foreach ( $hide_tabs as $tab ) {
				unset( $tabs[ $tab ] );
			}

			return $tabs;
		} );
	}

	ob_start();

	woocommerce_output_product_data_tabs();

	if ( wp_doing_ajax() ) {
		echo "<script>jQuery('.wc-tabs-wrapper, .woocommerce-tabs, #rating').trigger('init')</script>";
	}

	return ob_get_clean();
}