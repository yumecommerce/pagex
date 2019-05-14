<?php

/**
 * Accordion element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_accordion_element( $elements ) {
	$template = '<div class="pagex-accordion">
	<% data.tabs.forEach( function(tab) {
		%>
			<div class="pagex-accordion-item">
				<div class="pagex-accordion-item-header trn-300"><div class="pagex-accordion-item-title trn-300 w-100"><% if (tab.icon && tab.icon.length) { %><div class="pagex-accordion-item-icon"><% print(pagex.genIcon("icon", tab)) %></div><% } %><div class="pagex-lang-str"><% if (tab.title && tab.title.length) { print(tab.title) } else { %>' . __( 'Title', 'pagex' ) . '<% } %></div></div><div class="pagex-accordion-toggle trn-300 ml-4"><svg class="pagex-icon"><use xlink:href="#pagex-arrow-down-icon" /></svg></div></div>
				<div class="pagex-accordion-item-content-wrapper"><div class="pagex-accordion-item-content pagex-inner-row-holder"><% if (tab.content && tab.content.length) { print(tab.content) } else { %><div class="row" data-id="<%= pagex.genID() %>" data-type="inner-row"><div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div></div><% } %></div></div>
			</div>
		<%
	});
	%>
	</div>';

	$elements[] = array(
		'id'          => 'accordion',
		'category'    => 'content',
		'title'       => __( 'Accordion', 'pagex' ),
		'description' => __( 'Accordion tabs with custom content', 'pagex' ),
		'type'        => 'static',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'     => 'tabs',
						'title'  => __( 'Items', 'pagex' ),
						'type'   => 'repeater',
						'params' => array(
							array(
								'id'    => 'title',
								'title' => __( 'Title', 'pagex' ),
								'class' => 'col pagex-repeater-value',
								'type'  => 'text',
							),
							array(
								'id'       => 'icon',
								'title'    => __( 'Icon', 'pagex' ),
								'selector' => '.pagex-accordion-item-icon',
								'type'     => 'icon',
							),
							array(
								'id'       => 'content',
								'type'     => 'textarea',
								'hidden'   => true,
								'action'   => 'content',
								'selector' => '.pagex-accordion-item-content',
							),
						),
					),
				),
			),
			array(
				'title'  => __( 'Header', 'pagex' ),
				'params' => array(
					array(
						'id'       => 'toggle_separately',
						'label'    => __( 'Toggle Every Item Separately', 'pagex' ),
						'type'     => 'checkbox',
						'action'   => 'class',
						'selector' => '.pagex-accordion',
						'value'    => 'pagex-accordion-toggle-separately',
					),
					array(
						'id'         => 'offset',
						'title'      => __( 'Item Offset', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-accordion-item {margin-bottom: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Accordion Header', 'pagex' ),
					),
					array(
						'id'       => 'typography',
						'type'     => 'typography',
						'selector' => '.pagex-accordion-item-title',
					),
					array(
						'id'          => 'box_shadow',
						'title'       => __( 'Box Shadow', 'pagex' ),
						'description' => __( 'Property values: horizontal, vertical, blur, size, color.', 'pagex' ),
						'type'        => 'text',
						'action'      => 'css',
						'responsive'  => true,
						'class'       => 'col-6',
						'selector'    => '[el] .pagex-accordion-item-header {box-shadow: [val]}',
					),
					array(
						'id'         => 'box_shadow_hover',
						'title'      => __( 'Box Shadow on Hover', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] .pagex-accordion-item-header:hover {box-shadow: [val]}',
					),
					array(
						'id'         => 'padding',
						'title'      => __( 'Padding', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-accordion-item-header {padding: [val]}',
					),
					array(
						'id'       => 'radius',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-accordion-item-header {border-radius: [val]}',
					),
					array(
						'id'       => 'border_width',
						'title'    => __( 'Border Width', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-accordion-item-header {border-width: [val]}',
					),
					array(
						'id'       => 'color',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-title {color: [val]}',
					),
					array(
						'id'       => 'color_hover',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-header:hover .pagex-accordion-item-title {color: [val]}',
					),
					array(
						'id'       => 'bg',
						'title'    => __( 'Background Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-header {background: [val]}',
					),
					array(
						'id'       => 'bg_hover',
						'title'    => __( 'Background Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-header:hover {background: [val]}',
					),
					array(
						'id'       => 'border_color',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-header {border-color: [val]}',
					),
					array(
						'id'       => 'border_color_hover',
						'title'    => __( 'Border Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-header:hover {border-color: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Icon', 'pagex' ),
					),
					array(
						'id'         => 'sd',
						'title'      => __( 'Size', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] .pagex-accordion-item-icon .pagex-icon {width: [val]; height: [val]; font-size: [val]}',
					),
					array(
						'id'       => 'df',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-icon',
						'property' => 'margin',
					),
					array(
						'id'       => 'fg',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-icon {color: [val]}',
					),
					array(
						'id'       => 'gh',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-header:hover .pagex-accordion-item-icon {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Toggle Icon', 'pagex' ),
					),
					array(
						'id'         => 'toggle_size',
						'title'      => __( 'Size', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-accordion-toggle .pagex-icon {width: [val]; height: [val]}',
					),
					array(
						'id'         => 'toggle_padding',
						'title'      => __( 'Padding', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-accordion-toggle {padding: [val]}',
					),
					array(
						'id'       => 'toggle_radius',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-accordion-toggle {border-radius: [val]}',
					),
					array(
						'id'          => 'box_shadow_toggle',
						'title'       => __( 'Box Shadow', 'pagex' ),
						'description' => __( 'Property values: horizontal, vertical, blur, size, color.', 'pagex' ),
						'type'        => 'text',
						'action'      => 'css',
						'responsive'  => true,
						'class'       => 'col-6',
						'selector'    => '[el] .pagex-accordion-toggle {box-shadow: [val]}',
					),
					array(
						'id'         => 'box_shadow_toggle_hover',
						'title'      => __( 'Box Shadow', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] .pagex-accordion-item-header:hover .pagex-accordion-toggle {box-shadow: [val]}',
					),
					array(
						'id'       => 'color_toggle',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-toggle {color: [val]}',
					),
					array(
						'id'       => 'color_toggle_hover',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-header:hover .pagex-accordion-toggle {color: [val]}',
					),
					array(
						'id'       => 'bg_toggle',
						'title'    => __( 'Background Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-toggle {background: [val]}',
					),
					array(
						'id'       => 'bg_toggle_hover',
						'title'    => __( 'Background Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-header:hover .pagex-accordion-toggle {background: [val]}',
					),
				)
			),
			array(
				'title'  => __( 'Content', 'pagex' ),
				'params' => array(
					array(
						'id'         => 'content_padding',
						'title'      => __( 'Padding', 'pagex' ),
						'type'       => 'text',
						'class'      => 'col-3',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-accordion-item-content {padding: [val]}',
					),
					array(
						'id'         => 'content_margin',
						'title'      => __( 'Margin', 'pagex' ),
						'type'       => 'text',
						'class'      => 'col-3',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-accordion-item-content {margin: [val]}',
					),
					array(
						'id'       => 'content_border_width',
						'title'    => __( 'Border Width', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-content {border-width: [val]}',
					),
					array(
						'id'       => 'content_radius',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-content {border-radius: [val]}',
					),
					array(
						'id'       => 'content_text_color',
						'title'    => __( 'Text Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-content {color: [val]}',
					),
					array(
						'id'       => 'content_border_color',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-content {border-color: [val]}',
					),
					array(
						'id'       => 'bg_content',
						'title'    => __( 'Background Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-accordion-item-content {background: [val]}',
					),
				)
			)
		),
	);

	return $elements;
}