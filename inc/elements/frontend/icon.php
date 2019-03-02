<?php

/**
 * Icon element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_icon_element( $elements ) {
	$template = '<% if (data.link != "") { %><a class="pagex-static-link" <%= data.link %>> <% } %><div class="pagex-icon-element d-flex"><div class="pagex-icon-wrapper d-flex trn-300"><% if (data.icon && data.icon.length) { print(pagex.genIcon("icon", data)) } else { print("<i class=\'fas fa-thumbs-up\'></i>")  } %></div><% if (data.link != "") { print("</a>") } %>';

	$elements[] = array(
		'id'          => 'icon',
		'category'    => 'media',
		'title'       => __( 'Icon', 'pagex' ),
		'description' => __( 'Simple icon element with optional link', 'pagex' ),
		'type'        => 'static',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'       => 'icon',
						'type'     => 'icon',
						'selector' => '.pagex-icon-wrapper',
					),
					array(
						'id'         => 'alignment',
						'title'      => __( 'Alignment', 'pagex' ),
						'type'       => 'select',
						'class'      => 'col-4',
						'responsive' => true,
						'action'     => 'class',
						'selector'   => '.pagex-icon-element',
						'options'    => array(
							''                            => __( 'Default', 'pagex' ),
							'justify-content[pref]start'  => __( 'Left', 'pagex' ),
							'justify-content[pref]center' => __( 'Center', 'pagex' ),
							'justify-content[pref]end'    => __( 'Right', 'pagex' ),
						)
					),
					array(
						'id'         => 'border_width',
						'title'      => __( 'Border Width', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-icon-wrapper {border-width: [val]}',
					),
					array(
						'id'         => 'border_radius',
						'title'      => __( 'Border Radius', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-icon-wrapper {border-radius: [val]}',
					),
					array(
						'id'          => 'box_shadow',
						'title'       => __( 'Box Shadow', 'pagex' ),
						'description' => __( 'Property values: horizontal, vertical, blur, size, color.', 'pagex' ),
						'class'       => 'col-6',
						'type'        => 'text',
						'responsive'  => true,
						'action'      => 'css',
						'selector'    => '[el] .pagex-icon-wrapper {box-shadow: [val]}',
					),
					array(
						'id'         => 'box_shadow_hover',
						'title'      => __( 'Box Shadow', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'      => 'col-6',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-icon-wrapper:hover {box-shadow: [val]}',
					),

					array(
						'id'        => 'color',
						'title'     => __( 'Color', 'pagex' ),
						'class'     => 'col-4',
						'type'      => 'color',
						'action'    => 'css',
						'selector'  => '[el] .pagex-icon {color: [val]}',
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg' )
						)
					),
					array(
						'id'        => 'color_hover',
						'title'     => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'     => 'col-8',
						'type'      => 'color',
						'action'    => 'css',
						'selector'  => '[el] .pagex-icon-wrapper:hover .pagex-icon {color: [val]}',
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg' )
						)
					),
					array(
						'id'       => 'border_color',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-icon-wrapper {border-color: [val]}',
					),
					array(
						'id'       => 'border_hover',
						'title'    => __( 'Border Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-icon-wrapper:hover {border-color: [val]}',
					),
					array(
						'id'       => 'bg',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-icon-wrapper {background: [val]}',
					),
					array(
						'id'       => 'bg_hover',
						'title'    => __( 'Background', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-icon-wrapper:hover {background: [val]}',
					),
					array(
						'id'    => 'link',
						'title' => __( 'Link', 'pagex' ),
						'type'  => 'link',
					),
				),
			),
		),
	);

	return $elements;
}