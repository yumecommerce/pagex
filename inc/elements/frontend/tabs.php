<?php

/**
 * Tabs element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_tabs_element( $elements ) {
	$template = '<div class="pagex-tabs">
		<div class="pagex-tabs-nav-items">
		<% data.tabs.forEach( function(tab, index) { %>
			<div class="pagex-tabs-nav-item <% if (index == 0) { print("pagex-item-active"); } %>">
				<% if (tab.icon && tab.icon.length) { %>
				<div class="pagex-tabs-nav-item-icon">
					<% print(pagex.genIcon("icon", tab)) %>
				</div>
				<% } %>
				<div class="pagex-tabs-nav-item-wrapper">
					<div class="pagex-tabs-nav-item-title pagex-lang-str"><% if (tab.title && tab.title.length) { print(tab.title) } else { %>' . __( 'Title', 'pagex' ) . '<% } %></div>
					<div class="pagex-tabs-nav-item-description pagex-lang-str"><% if (tab.desc && tab.desc.length) { print(tab.desc) } %></div>
				</div>
			</div>
		<% }); %>
		</div>
		
		<div class="pagex-tabs-panes">
		<% data.tabs.forEach( function(tab, index) { %>
			<div class="pagex-tabs-pane pagex-inner-row-holder <% if (index == 0) { print("pagex-item-active"); } %>">
				<% if (tab.content && tab.content.length) { print(tab.content) } else { %><div class="row" data-id="<%= pagex.genID() %>" data-type="inner-row"><div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div></div><% } %>
			</div>
		<% }); %>
		</div>
	</div>';

	$elements[] = array(
		'id'          => 'tabs',
		'category'    => 'content',
		'title'       => __( 'Tabs', 'pagex' ),
		'description' => __( 'Tabbable panes with custom content', 'pagex' ),
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
								'id'    => 'desc',
								'title' => __( 'Description', 'pagex' ),
								'type'  => 'text',
							),
							array(
								'id'       => 'icon',
								'title'    => __( 'Icon', 'pagex' ),
								'selector' => '.pagex-tab-item-icon',
								'type'     => 'icon',
							),
							array(
								'id'       => 'content',
								'type'     => 'textarea',
								'hidden'   => true,
								'action'   => 'content',
								'selector' => '.pagex-tabs-pane',
							),
						),
					),
				),
			),
			array(
				'title'  => __( 'Tabs', 'pagex' ),
				'params' => array(
					array(
						'id'       => 'alignment',
						'title'    => __( 'Alignment', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-4',
						'action'   => 'class',
						'selector' => '.pagex-tabs-nav-items',
						'options'  => array(
							''                       => __( 'Left', 'pagex' ),
							'justify-content-center' => __( 'Center', 'pagex' ),
							'justify-content-end'    => __( 'Right', 'pagex' ),
							'pagex-tabs-justify'     => __( 'Justify', 'pagex' ),
						)
					),
					array(
						'id'       => 'style',
						'title'    => __( 'Style', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-4',
						'action'   => 'class',
						'selector' => '.pagex-tabs',
						'options'  => array(
							'pagex-tabs-border-style' => __( 'Border', 'pagex' ),
							'pagex-tabs-custom-style' => __( 'Custom', 'pagex' ),
						)
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Tabs', 'pagex' ),
					),
					array(
						'id'       => 'padding',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item {padding: [val]}',
					),
					array(
						'id'       => 'margin',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item {margin: [val]}',
					),
					array(
						'id'         => 'border_radius',
						'title'      => __( 'Border Radius', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-tabs-nav-item {border-radius: [val]}',
					),
					array(
						'id'         => 'border_width',
						'title'      => __( 'Border Width', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-tabs-nav-item {border-width: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Tab Title', 'pagex' ),
					),
					array(
						'id'       => 'title_typo',
						'type'     => 'typography',
						'selector' => '.pagex-tabs-nav-item-title',
					),
					array(
						'id'         => 'title_margin',
						'title'      => __( 'Margin', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-tabs-nav-item-title {margin: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Tab Description', 'pagex' ),
					),
					array(
						'id'       => 'desc_typo',
						'type'     => 'typography',
						'selector' => '.pagex-tabs-nav-item-description',
					),
					array(
						'id'         => 'desc_margin',
						'title'      => __( 'Margin', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-tabs-nav-item-description {margin: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Tab Icon', 'pagex' ),
					),
					array(
						'id'         => 'icon_size',
						'title'      => __( 'Size', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] .pagex-tabs-nav-item-icon .pagex-icon {width: [val]; height: [val]; font-size: [val]}',
					),
					array(
						'id'       => 'icon_margin',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item-icon {margin: [val]}',
					),
				)
			),
			array(
				'title'  => __( 'Content', 'pagex' ),
				'params' => array(
					array(
						'id'       => 'content_padding',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-panes {padding: [val]}',
					),
					array(
						'id'       => 'content_margin',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-panes {margin: [val]}',
					),
					array(
						'id'         => 'content_border_radius',
						'title'      => __( 'Border Radius', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-tabs-panes {border-radius: [val]}',
					),
					array(
						'id'         => 'content_border_width',
						'title'      => __( 'Border Width', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-tabs-panes {border-width: [val]}',
					),
					array(
						'type'     => 'clear',
					),
					array(
						'id'       => 'content_typo',
						'type'     => 'typography',
						'selector' => '.pagex-tabs-panes',
					),
				)
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Tabs', 'pagex' ),
					),
					array(
						'id'       => 'title_color',
						'title'    => __( 'Title Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item-title {color: [val]}',
					),
					array(
						'id'       => 'title_color_hover',
						'title'    => __( 'Title Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item:hover .pagex-tabs-nav-item-title {color: [val]}',
					),
					array(
						'id'       => 'title_color_active',
						'title'    => __( 'Active Title Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item.pagex-item-active .pagex-tabs-nav-item-title {color: [val]}',
					),
					array(
						'id'       => 'desc_color',
						'title'    => __( 'Description Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item-description {color: [val]}',
					),
					array(
						'id'       => 'desc_color_hover',
						'title'    => __( 'Description Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item:hover .pagex-tabs-nav-item-description {color: [val]}',
					),
					array(
						'id'       => 'desc_color_active',
						'title'    => __( 'Active Description Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item.pagex-item-active .pagex-tabs-nav-item-description {color: [val]}',
					),
					array(
						'id'       => 'icon_color',
						'title'    => __( 'Icon Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item-icon {color: [val]}',
					),
					array(
						'id'       => 'icon_color_hover',
						'title'    => __( 'Icon Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item:hover .pagex-tabs-nav-item-icon {color: [val]}',
					),
					array(
						'id'       => 'icon_color_active',
						'title'    => __( 'Active Icon Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item.pagex-item-active .pagex-tabs-nav-item-icon {color: [val]}',
					),
					array(
						'id'       => 'bg_color',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item {background: [val]}',
					),
					array(
						'id'       => 'bg_color_hover',
						'title'    => __( 'Background', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item:hover {background: [val]}',
					),
					array(
						'id'       => 'bg_color_active',
						'title'    => __( 'Active Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item.pagex-item-active {background: [val]} [el] .pagex-tabs-border-style .pagex-tabs-nav-item.pagex-item-active {border-bottom-color: [val] !important}',
					),
					array(
						'id'       => 'border_color',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item {border-color: [val]}',
					),
					array(
						'id'       => 'border_color_hover',
						'title'    => __( 'Border Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item:hover {border-color: [val]}',
					),
					array(
						'id'       => 'border_color_active',
						'title'    => __( 'Active Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-nav-item.pagex-item-active {border-color: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Content', 'pagex' ),
					),
					array(
						'id'       => 'text_color',
						'title'    => __( 'Text Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-panes {color: [val]}',
					),
					array(
						'id'       => 'content_bc',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-panes {border-color: [val]}',
					),
					array(
						'id'       => 'content_bg',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-tabs-panes {background: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}