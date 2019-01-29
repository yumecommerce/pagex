<?php

/**
 * Menu Cart element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_woo_menu_cart_element( $elements ) {
	$elements[] = array(
		'id'          => 'menu_cart',
		'category'    => 'woocommerce',
		'title'       => __( 'Widget Cart', 'pagex' ),
		'description' => __( 'Display WooCommerce cart as a widget', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_woo_menu_cart',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'type',
						'title'   => __( 'Type', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-6',
						'options' => array(
							'link'      => __( 'Simple Link', 'pagex' ),
							'dropdown'  => __( 'Dropdown', 'pagex' ),
							'offcanvas' => __( 'Offcanvas', 'pagex' ),
						),
					),
					array(
						'id'        => 'offcanvas_pos',
						'title'     => __( 'Offcanvas Position', 'pagex' ),
						'type'      => 'select',
						'class'     => 'col-6',
						'options'   => array(
							''      => __( 'Left', 'pagex' ),
							'right' => __( 'Right', 'pagex' ),
						),
						'condition' => array(
							'type' => array( 'offcanvas' )
						)
					),
					array(
						'id'        => 'uy',
						'title'     => __( 'Dropdown Offset', 'pagex' ),
						'type'      => 'text',
						'action'    => 'css',
						'class'     => 'col-3',
						'selector'  => '[el] .pagex-menu-cart-dropdown .pagex-menu-cart-widget {padding-top: [val]}',
						'condition' => array(
							'type' => array( 'dropdown' )
						)
					),
					array(
						'id'        => 'yt',
						'title'     => __( 'Position', 'pagex' ),
						'type'      => 'select',
						'action'    => 'css',
						'class'     => 'col-3',
						'selector'  => '[el] .pagex-menu-cart-dropdown .pagex-menu-cart-widget {[val]: 0}',
						'options'   => array(
							'left'  => __( 'Left', 'pagex' ),
							'right' => __( 'Right', 'pagex' ),
						),
						'condition' => array(
							'type' => array( 'dropdown' )
						)
					),
					array(
						'type' => 'clear'
					),
					array(
						'id'    => 'title',
						'title' => __( 'Widget Title', 'pagex' ),
						'type'  => 'text',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Custom Icon', 'pagex' ),
					),
					array(
						'id'   => 'icon',
						'type' => 'icon',
					),
				),
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'id'       => 're',
						'title'    => __( 'Alignment', 'pagex' ),
						'type'     => 'select',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart {justify-content: [val]}',
						'options'  => array(
							''         => __( 'Left', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Right', 'pagex' ),
						)
					),
					array(
						'id'       => 'wq',
						'type'     => 'checkbox',
						'label'    => __( 'Hide counter and subtotal if no products in the cart', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-amount-0 {display: none}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Icon', 'pagex' ),
					),
					array(
						'id'       => 'i_c',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-icon .pagex-icon {color: [val]}',
					),
					array(
						'id'       => 'i_ch',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-wrapper:hover .pagex-menu-cart-icon .pagex-icon {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Subtotal', 'pagex' ),
					),
					array(
						'id'       => 'ew',
						'type'     => 'checkbox',
						'label'    => __( 'Hide Subtotal', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-contents .pagex-menu-cart-subtotal {display: none}',
					),
					array(
						'id'       => 's_c',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-contents-body .amount {color: [val]}',
					),
					array(
						'id'       => 's_ch',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-wrapper:hover .pagex-menu-cart-contents-body .amount {color: [val]}',
					),
					array(
						'id'       => 'c_m',
						'title'    => __( 'Margin', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-counter {margin: [val]}',
					),
					array(
						'id'       => 's_t',
						'type'     => 'typography',
						'selector' => '.pagex-menu-cart-contents-body .amount',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Counter', 'pagex' ),
					),
					array(
						'id'      => 'count_t',
						'title'   => __( 'Counter Type', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'block'  => __( 'Block', 'pagex' ),
							'badge'  => __( 'Badge', 'pagex' ),
							'hidden' => __( 'Hidden', 'pagex' ),
						),
					),
					array(
						'id'       => 'c_c',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-counter {color: [val]}',
					),
					array(
						'id'       => 'c_ch',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-wrapper:hover .pagex-menu-cart-counter {color: [val]}',
					),
					array(
						'id'       => 'c_b',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-counter {background: [val]}',
					),
					array(
						'id'       => 'c_bh',
						'title'    => __( 'Background on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-wrapper:hover .pagex-menu-cart-counter {background: [val]}',
					),
					array(
						'id'       => 'c_t',
						'type'     => 'typography',
						'selector' => '.pagex-menu-cart-counter',
					),
					array(
						'id'          => 'tr',
						'title'       => __( 'Badge Margin', 'pagex' ),
						'description' => __( 'Margin for badge counter can control distance from left top corner of the element.', 'pagex' ),
						'type'        => 'text',
						'class'       => 'col-6',
						'action'      => 'css',
						'selector'    => '[el] .pagex-menu-cart-counter-badge .pagex-menu-cart-counter {margin: [val]}',
						'condition'   => array(
							'count_t' => array( 'badge' )
						)
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Widget Title', 'pagex' ),
					),
					array(
						'id'       => 'zx',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-title {color: [val]}',
					),
					array(
						'id'       => 'xc',
						'title'    => __( 'Margin', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .pagex-menu-cart-title {margin: [val]}',
					),
					array(
						'id'       => 'cv',
						'type'     => 'typography',
						'selector' => '.pagex-menu-cart-title',
					),
				)
			),
			array(
				'title'  => __( 'Widget', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Widget', 'pagex' ),
					),
					array(
						'id'          => 'vb',
						'title'       => __( 'Padding', 'pagex' ),
						'description' => __( 'Padding around the widget', 'pagex' ),
						'responsive'  => true,
						'class'       => 'col-4',
						'type'        => 'text',
						'action'      => 'css',
						'selector'    => '[el] .pagex-menu-cart-widget-content {padding: [val]}',
					),
					array(
						'id'          => 'ka',
						'title'       => __( 'Width', 'pagex' ),
						'responsive'  => true,
						'class'       => 'col-4',
						'type'        => 'text',
						'action'      => 'css',
						'selector'    => '[el] .pagex-menu-cart-dropdown .pagex-menu-cart-widget {width: [val]} [el].pagex-modal-offcanvas .pagex-modal-window-content {max-width: [val]}',
					),
					array(
						'id'       => 'w_sp',
						'title'    => __( 'Product Bottom Offset', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .widget .cart_list li {margin-bottom: [val]; padding-bottom: [val]}',
					),
					array(
						'id'       => 'w_is',
						'title'    => __( 'Product Image Size', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'number',
						'action'   => 'css',
						'selector' => '[el] .widget .cart_list li img {width: [val]px} [el] .mini_cart_item {padding-left: calc([val]px + 15px) !important; min-height: [val]px !important;}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Product Title', 'pagex' ),
					),
					array(
						'id'       => 'w_tc',
						'title'    => __( 'Title', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .widget .cart_list a {color: [val]}',
					),
					array(
						'id'       => 'w_tch',
						'title'    => __( 'Title on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .widget .cart_list a:hover {color: [val]}',
					),
					array(
						'id'       => 'w_tt',
						'type'     => 'typography',
						'selector' => '.widget .cart_list a',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Quantity', 'pagex' ),
					),
					array(
						'id'       => 'w_qc',
						'title'    => __( 'Quantity', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .widget .quantity {color: [val]}',
					),
					array(
						'id'       => 'w_qt',
						'type'     => 'typography',
						'selector' => '.widget .quantity',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Remove Button', 'pagex' ),
					),
					array(
						'id'       => 'w_rc',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .widget .cart_list a.remove {color: [val] !important}',
					),
					array(
						'id'       => 'w_rch',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .widget .cart_list a.remove:hover {color: [val] !important}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Subtotal', 'pagex' ),
					),

					array(
						'id'       => 'w_sc',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .widget .total {color: [val] }',
					),
					array(
						'id'       => 'w_sm',
						'title'    => __( 'Vertical Spacing', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .widget p.total {padding: [val] 0}',
					),
					array(
						'id'       => 'w_st',
						'type'     => 'typography',
						'selector' => '.widget p.total',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Buttons', 'pagex' ),
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Layout', 'pagex' ),
						'type'     => 'select',
						'action'   => 'css',
						'selector' => '[el] .widget .buttons a {width: [val]; margin-right: 0;}',
						'class'    => 'col-6',
						'options'  => array(
							''      => __( 'Inline', 'pagex' ),
							' 100%' => __( 'Block', 'pagex' ),
						),
					),
					array(
						'type'  => 'heading',
						'title' => __( 'View Cart Button', 'pagex' ),
					),
					array(
						'id'       => 'we',
						'type'     => 'button_style',
						'selector' => '.widget .buttons a:not(.checkout)',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Checkout Button', 'pagex' ),
					),
					array(
						'id'       => 'qw',
						'type'     => 'button_style',
						'selector' => '.widget .buttons a.checkout',
					),

				)
			)
		),
	);

	return $elements;
}

/**
 * Menu Cart shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_woo_menu_cart( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'type'          => 'link',
		'offcanvas_pos' => 'left',
		'count_t'       => 'block',
		'title'         => '',
	) );

	$widget = '';
	$class  = array(
		'pagex-menu-cart',
		'pagex-menu-cart-' . $data['type'],
		'pagex-menu-cart-counter-' . $data['count_t'],
	);

	$wrapper_class = array(
		'pagex-menu-cart-wrapper',
		$data['type'] == 'offcanvas' ? 'pagex-modal-trigger' : '',
	);

	if ( $data['type'] == 'dropdown' || $data['type'] == 'offcanvas' ) {
		ob_start();

		echo '<div class="pagex-menu-cart-widget">';
		echo '<div class="pagex-menu-cart-widget-content">';
		if ( $data['title'] ) {
			echo '<h5 class="pagex-menu-cart-title">' . $data['title'] . ' </h5>';
		}
		the_widget( 'WC_Widget_Cart' );
		echo '</div>';
		echo '</div>';

		$widget = ob_get_clean();
	}

	ob_start();

	echo '<div class="' . implode( ' ', $class ) . '">';
	echo '<div class="' . implode( ' ', $wrapper_class ) . '">';

	$icon = isset( $data['icon'] ) ? pagex_generate_icon( 'icon', $data ) : '<svg class="pagex-icon"><use xlink:href="#pagex-bag-icon" /></svg>';

	echo '<div class="pagex-menu-cart-icon">' . $icon . '</div>';

	echo '<div class="pagex-menu-cart-contents">';
	echo $data['type'] == 'link' || $data['type'] == 'dropdown' ? '<a class="pagex-cart-contents-link" href="' . esc_url( wc_get_cart_url() ) . '">' : '';

	$amount = WC()->cart->get_cart_contents_count();

	echo '<div class="pagex-menu-cart-contents-body pagex-menu-cart-amount-' . $amount . '">';
	echo '<div class="pagex-menu-cart-subtotal">';
	echo WC()->cart->get_cart_subtotal();
	echo '</div>';
	echo '<div class="pagex-menu-cart-counter"><span class="pagex-menu-cart-count">' . $amount . '</span> <span class="pagex-menu-cart-count-label">' . _n( 'item', 'items', $amount, "pagex" ) . '</span></div>';
	echo '</div>';

	echo $data['type'] == 'link' || $data['type'] == 'dropdown' ? '</a>' : '';
	echo '</div>';

	if ( $data['type'] == 'dropdown' ) {
		echo $widget;
	}

	echo '</div>';
	echo '</div>';

	if ( $data['type'] == 'offcanvas' ) {
		pagex_modal_window_template( array(
			'content'   => $widget,
			'class'     => 'pagex-menu-cart-offcanvas-container',
			'offcanvas' => $data['offcanvas_pos']
		) );
	}

	return ob_get_clean();
}


/**
 * Cart Fragments
 * Ensure cart contents update when products are added to the cart via AJAX
 *
 * @param  array
 *
 * @return array - Fragments to refresh via AJAX
 */
function pagex_cart_link_fragment( $fragments ) {
	ob_start();

	$amount = WC()->cart->get_cart_contents_count();

	echo '<div class="pagex-menu-cart-contents-body pagex-menu-cart-amount-' . $amount . '">';
	echo '<div class="pagex-menu-cart-subtotal">';
	echo WC()->cart->get_cart_subtotal();
	echo '</div>';
	echo '<div class="pagex-menu-cart-counter"><span class="pagex-menu-cart-count">' . $amount . '</span> <span class="pagex-menu-cart-count-label">' . _n( 'item', 'items', $amount, "pagex" ) . '</span></div>';
	echo '</div>';

	$fragments['.pagex-menu-cart-contents-body'] = ob_get_clean();

	return $fragments;
}

add_filter( 'woocommerce_add_to_cart_fragments', 'pagex_cart_link_fragment' );