<?php

/**
 * Search element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_search_form_element( $elements ) {
	$_post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );

	$post_types = array( '' => __( 'All post types', 'pagex' ) );

	foreach ( $_post_types as $post_type => $object ) {
		$post_types[ $post_type ] = $object->label;
	}

	$elements[] = array(
		'id'          => 'search_form',
		'category'    => 'content',
		'title'       => __( 'Search Form', 'pagex' ),
		'description' => __( 'Search for all posts or by specific post type', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_search_form',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'post_type',
						'type'        => 'select',
						'title'       => __( 'Post Type', 'pagex' ),
						'description' => __( 'Select post type if you wish to search only for specific posts.', 'pagex' ),
						'options'     => $post_types,
					),
					array(
						'id'       => 'style',
						'type'     => 'select',
						'class'    => 'col-4',
						'action'   => 'class',
						'selector' => '.element-wrap',
						'title'    => __( 'Style', 'pagex' ),
						'options'  => array(
							''                          => __( 'Classic', 'pagex' ),
							'pagex-search-form-overlay' => __( 'Overlay', 'pagex' ),
						),
					),
					array(
						'id'    => 'placeholder',
						'title' => __( 'Placeholder', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-4',
					),
					array(
						'id'    => 'button_text',
						'title' => __( 'Button Text', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-4',
					),
				)
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Input', 'pagex' ),
					),
					array(
						'id'       => 'xz',
						'type'     => 'typography',
						'selector' => 'input:not([type="checkbox"]):not([type="radio"])',
					),
					array(
						'id'       => 'cx',
						'title'    => __( 'Color', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]) {color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
					array(
						'id'       => 'vc',
						'title'    => __( 'Color on Focus', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]):focus {color: [val]}',
						'class'    => 'col-8',
						'type'     => 'color',
					),
					array(
						'id'       => 'bv',
						'title'    => __( 'Border Color', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]) {border-color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
					array(
						'id'       => 'nb',
						'title'    => __( 'Border Color', 'pagex' ) . ' ' . __( 'on Focus', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]):focus {border-color: [val]}',
						'class'    => 'col-8',
						'type'     => 'color',
					),
					array(
						'id'       => 'mn',
						'title'    => __( 'Background', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]) {background: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
					array(
						'id'       => 'nm',
						'title'    => __( 'Background', 'pagex' ) . ' ' . __( 'on Focus', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]):focus {background: [val]}',
						'class'    => 'col-8',
						'type'     => 'color',
					),
					array(
						'id'          => 'bn',
						'title'       => __( 'Box Shadow', 'pagex' ),
						'description' => __( 'Property values: horizontal, vertical, blur, size, color.', 'pagex' ),
						'type'        => 'text',
						'action'      => 'css',
						'responsive'  => true,
						'class'       => 'col-6',
						'selector'    => '[el] input:not([type="checkbox"]):not([type="radio"]) {box-shadow: [val]}',
					),
					array(
						'id'         => 'vb',
						'title'      => __( 'Box Shadow', 'pagex' ) . ' ' . __( 'on Focus', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] input:not([type="checkbox"]):not([type="radio"]):focus {box-shadow: [val]}',
					),
					array(
						'id'       => 'cv',
						'title'    => __( 'Height', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]) {height: [val]}',
					),
					array(
						'id'       => 'xc',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]) {padding: [val]}',
					),
					array(
						'id'       => 'zx',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]) {border-radius: [val]}',
					),
					array(
						'id'       => 'tr',
						'title'    => __( 'Border Width', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] input:not([type="checkbox"]):not([type="radio"]) {border-width: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Button', 'pagex' ),
					),
					array(
						'id'       => 'al',
						'type'     => 'button_style',
						'selector' => '[type="submit"]',
					),
				),
			),
			array(
				'title'  => __( 'Mobile', 'pagex' ),
				'params' => array(
					array(
						'id'          => 'breakpoint',
						'title'       => __( 'Mobile Breakpoint', 'pagex' ),
						'description' => __( 'If selected "None", the search will not be collapsed to its mobile state.', 'pagex' ),
						'type'        => 'select',
						'options'     => array(
							''                    => __( 'None', 'pagex' ),
							'pagex-breakpoint-sm' => __( 'Mobile', 'pagex' ) . ' < 576px',
							'pagex-breakpoint-md' => __( 'Tablet', 'pagex' ) . ' < 768px',
							'pagex-breakpoint-lg' => __( 'Tablet (landscape)', 'pagex' ) . ' < 1024px',
							'pagex-breakpoint-xl' => __( 'Desktop', 'pagex' ),
						)
					),
					array(
						'type'      => 'row-start',
						'condition' => array(
							'breakpoint' => array(
								'pagex-breakpoint-sm',
								'pagex-breakpoint-md',
								'pagex-breakpoint-lg',
								'pagex-breakpoint-xl'
							)
						)
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Toggle Button', 'pagex' ),
					),
					array(
						'id'       => 'io',
						'title'    => __( 'Align', 'pagex' ),
						'type'     => 'select',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-mobile-trigger-wrapper {justify-content: [val]}',
						'scope'    => true,
						'options'  => array(
							''         => __( 'Left', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Right', 'pagex' ),
						)
					),
					array(
						'id'       => 'ui',
						'title'    => __( 'Size', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-search-form-icon .pagex-icon {width: [val]; height: [val]}',
					),
					array(
						'id'       => 'yu',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-search-form-icon {padding: [val]}',
					),
					array(
						'id'       => 'ty',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-search-form-icon {border-radius: [val]}',
					),
					array(
						'id'       => 'qw',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-search-form-icon {color: [val]}',
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-modal-trigger:hover .pagex-search-form-icon {color: [val]}',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-search-form-icon {background: [val]}',
					),
					array(
						'id'       => 'rt',
						'title'    => __( 'Background on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-modal-trigger:hover .pagex-search-form-icon {background: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Custom Icon', 'pagex' ),
					),
					array(
						'id'   => 'icon',
						'type' => 'icon',
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
 * Shortcode for search form element
 *
 * @param $atts
 *
 * @return string
 */
function pagex_search_form( $atts ) {
	$atts = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $atts, array(
		'post_type'   => '',
		'placeholder' => '',
		'button_text' => '',
		'breakpoint'  => '',
	) );

	$placeholder = $data['placeholder'] ? esc_attr( $data['placeholder'] ) : __( 'Search...', 'pagex' );
	$button      = $data['button_text'] ? esc_attr( $data['button_text'] ) : __( 'Search', 'pagex' );

	$post_type_input = $data['post_type'] ? '<input type="hidden" name="post_type" value="' . $data['post_type'] . '">' : '';

	$query_s = get_query_var( 's' );
	$query_t = get_query_var( 'post_type' );

	// setup value only if query vars are the same with shortcode data
	$search_val = $query_s && $query_t == $data['post_type'] ? esc_attr( $query_s ) : '';

	ob_start();

	echo '<div class="pagex-search-form-wrapper ' . $data['breakpoint'] . '">';

	$form = '<form role="search" method="get" class="pagex-search-form d-flex pagex-breakpoint-desktop" action="' . esc_url( home_url( '/' ) ) . '">';
	$form .= '<input type="search" name="s" class="form-control pagex-search-input" placeholder="' . $placeholder . '" value="' . $search_val . '">';
	$form .= '<button type="submit" class="btn">' . $button . '</button>';
	$form .= $post_type_input;
	$form .= '</form>';

	// print form only when mobile breakpoint is not equal desktop
	if ( $data['breakpoint'] !== 'pagex-breakpoint-xl' ) {
		echo $form;
	}

	$icon = isset( $data['icon'] ) ? pagex_generate_icon( 'icon', $data ) : '<svg class="pagex-icon"><use xlink:href="#pagex-search-icon" /></svg>';

	if ( $data['breakpoint'] ) {
		echo '<div class="pagex-mobile-trigger-wrapper pagex-modal-trigger pagex-breakpoint-mobile"><div class="pagex-search-form-icon">' . $icon . '</div></div>';

		pagex_modal_window_template( array(
			'content' => str_replace( 'pagex-breakpoint-desktop', '', $form ),
			'class'   => 'pagex-search-form-mobile-wrapper'
		) );

	}

	echo '</div>';

	return ob_get_clean();
}