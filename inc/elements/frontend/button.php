<?php

/**
 * Button element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_button_element( $elements ) {
	$template = '<%
		var link = data.link && data.link.length && data.modal != "true" ? true : false;
	%>
	<div class="pagex-button-wrapper">
	<% if (link) print("<a class=\'pagex-button-link pagex-static-link\' " + data.link + ">");  %>
		<div class="pagex-button <% if (data.modal == "true") { %>pagex-modal-trigger<% } %>">
			<% if (data.icon && data.icon.length) { %>
			<div class="pagex-button-icon"><%= pagex.genIcon("icon", data) %></div>
			<% } %>
			<div class="pagex-button-content pagex-lang-str"><% if (data.content && data.content.length) { print(data.content) } else if (firstInit) { %>' . __( 'Button Text', 'pagex' ) . '<% } %></div>
		</div>
	<% if (link) print("</a>"); %>
	</div>
	<% if (data.modal == "true") { %>
		<div class="pagex-modal<% if (data.modal_offcanvas == "true") {print(" pagex-modal-offcanvas " + data.modal_offcanvas_pos)} %>">
			<div class="pagex-modal-window-wrapper">
				<div class="pagex-modal-window">
					<% if (data.mx.length) { %>
						<div class="pagex-modal-window-close">
							<svg class="pagex-icon"><use xlink:href="#pagex-close-icon" /></svg>
						</div>
					<% } %>
					<div class="pagex-modal-window-content">
						<% if (!data.mx.length) { %>
							<div class="pagex-modal-window-close"><svg class="pagex-icon"><use xlink:href="#pagex-close-icon" /></svg></div>
						<% } %>
						<div class="pagex-inner-row-holder pagex-modal-builder-area">
						<% if (data.modal_content && data.modal_content.length) { print(data.modal_content) } else { %><div class="row" data-id="<%= pagex.genID() %>" data-type="inner-row"><div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div></div><% } %>
						</div>
					</div>
				</div>
			</div>
		</div>
	<% } %>';

	$elements[] = array(
		'id'          => 'button',
		'category'    => 'content',
		'title'       => __( 'Button', 'pagex' ),
		'description' => __( 'Could be setup as simple link or modal window', 'pagex' ),
		'type'        => 'static',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'content',
						'title'       => __( 'Text', 'pagex' ),
						'type'        => 'textarea',
						'description' => __( 'HTML tags are allowed.', 'pagex' ),
						'action'      => 'content',
						'selector'    => '.pagex-button-content',
					),
					array(
						'id'    => 'link',
						'title' => __( 'Link', 'pagex' ),
						'type'  => 'link',
					),
					array(
						'id'       => 'icon',
						'type'     => 'icon',
						'selector' => '.pagex-button-icon',
					),
					array(
						'id'        => 'ip',
						'title'     => __( 'Icon Position', 'pagex' ),
						'type'      => 'select',
						'action'    => 'class',
						'selector'  => '.pagex-button-icon',
						'options'   => array(
							''        => __( 'Left', 'pagex' ),
							'order-2' => __( 'Right', 'pagex' ),
						),
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg', 'image' )
						)
					),
				),
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'id'       => 'fo',
						'type'     => 'typography',
						'selector' => '.pagex-button-content',
					),
					array(
						'id'       => 'fw',
						'type'     => 'checkbox',
						'title'    => __( 'Full Width', 'pagex' ),
						'label'    => __( 'Make Button Full Width', 'pagex' ),
						'action'   => 'class',
						'class'    => 'col-6',
						'scope'    => true,
						'value'    => 'w-100',
						'selector' => '.pagex-button',
					),
					array(
						'id'         => 'ca',
						'title'      => __( 'Content Alignment', 'pagex' ),
						'type'       => 'select',
						'class'      => 'col-4',
						'responsive' => true,
						'action'     => 'class',
						'scope'      => true,
						'selector'   => '.pagex-button',
						'options'    => array(
							''                            => __( 'Default', 'pagex' ),
							'justify-content[pref]start'  => __( 'Left', 'pagex' ),
							'justify-content[pref]center' => __( 'Center', 'pagex' ),
							'justify-content[pref]end'    => __( 'Right', 'pagex' ),
						),
						'condition'  => array(
							'fw' => array( 'true' )
						)
					),
					array(
						'type' => 'clear',
					),
					array(
						'id'       => 'pa',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {padding: [val]}',
					),
					array(
						'id'       => 'bw',
						'title'    => __( 'Border Width', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {border-width: [val]}',
					),
					array(
						'id'       => 'br',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {border-radius: [val]}',
					),
					array(
						'id'         => 'al',
						'title'      => __( 'Alignment', 'pagex' ),
						'type'       => 'select',
						'class'      => 'col-6',
						'responsive' => true,
						'action'     => 'class',
						'scope'      => true,
						'selector'   => '.pagex-button-wrapper',
						'options'    => array(
							''                            => __( 'Default', 'pagex' ),
							'justify-content[pref]start'  => __( 'Left', 'pagex' ),
							'justify-content[pref]center' => __( 'Center', 'pagex' ),
							'justify-content[pref]end'    => __( 'Right', 'pagex' ),
						),
					),
					array(
						'id'         => 'mh',
						'title'      => __( 'Min. Height', 'pagex' ),
						'type'       => 'text',
						'class'      => 'col-6',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {min-height: [val]}',
					),
					array(
						'id'       => 'cl',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {color: [val]}',
					),
					array(
						'id'       => 'ch',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button:hover {color: [val]}',
					),
					array(
						'id'       => 'bg',
						'title'    => __( 'Background Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {background: [val]}',
					),
					array(
						'id'       => 'bh',
						'title'    => __( 'Background on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button:hover {background: [val]}',
					),

					array(
						'id'       => 'io',
						'title'    => __( 'Icon Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button .pagex-button-icon {color: [val]}',
					),
					array(
						'id'       => 'ih',
						'title'    => __( 'Icon Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button:hover .pagex-button-icon {color: [val]}',
					),
					array(
						'id'       => 'bc',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {border-color: [val]}',
					),
					array(
						'id'       => 'bu',
						'title'    => __( 'Border Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button:hover {border-color: [val]}',
					),
					array(
						'id'         => 'aq',
						'title'      => __( 'Opacity on Hover', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'selector'   => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {opacity: [val]}',
					),
					array(
						'id'         => 'sw',
						'title'      => __( 'Opacity on Hover', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'selector'   => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button:hover {opacity: [val]}',
					),
					array(
						'id'          => 'bs',
						'title'       => __( 'Box Shadow', 'pagex' ),
						'description' => __( 'Property values: horizontal, vertical, blur, size, color.', 'pagex' ),
						'type'        => 'text',
						'action'      => 'css',
						'responsive'  => true,
						'class'       => 'col-6',
						'selector'    => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {box-shadow: [val]}',
					),
					array(
						'id'         => 'bj',
						'title'      => __( 'Box Shadow', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button:hover {box-shadow: [val]}',
					),
					array(
						'id'         => 'to',
						'title'      => __( 'Text Shadow', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button {text-shadow: [val]}',
					),
					array(
						'id'         => 'tg',
						'title'      => __( 'Text Shadow on Hover', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] > .element-wrap > .pagex-button-wrapper .pagex-button:hover {text-shadow: [val]}',
					),
				)
			),
			array(
				'title'  => __( 'Modal', 'pagex' ),
				'params' => array(
					array(
						'id'    => 'modal',
						'type'  => 'checkbox',
						'title' => __( 'Modal Window', 'pagex' ),
						'label' => __( 'Use Button to Trigger Modal Window', 'pagex' ),
						'description' => __( 'Modal window content has an inner builder area. To open a modal window click on a button itself.', 'pagex' ),
					),
					array(
						'id'       => 'modal_content',
						'type'     => 'textarea',
						'action'   => 'content',
						'hidden'   => true,
						'selector' => '.pagex-inner-row-holder',
					),
					array(
						'type'      => 'row-start',
						'condition' => array(
							'modal' => 'true',
						),
					),
					array(
						'id'    => 'modal_offcanvas',
						'type'  => 'checkbox',
						'title' => __( 'Off-canvas', 'pagex' ),
						'label' => __( 'Add Modal Window Off-canvas Effect', 'pagex' ),
					),

					array(
						'id'        => 'modal_offcanvas_pos',
						'title'     => __( 'Off-canvas Position', 'pagex' ),
						'type'      => 'select',
						'options'   => array(
							'pagex-offcanvas-position-left'   => __( 'Left', 'pagex' ),
							'pagex-offcanvas-position-right'  => __( 'Right', 'pagex' ),
							'pagex-offcanvas-position-top'    => __( 'Top', 'pagex' ),
							'pagex-offcanvas-position-bottom' => __( 'Bottom', 'pagex' ),
						),
						'condition' => array(
							'modal_offcanvas' => 'true',
						),
					),
					array(
						'type'        => 'heading',
						'title'       => __( 'Modal Window', 'pagex' ),
						'description' => __( 'Area outside the modal content', 'pagex' ),
					),
					array(
						'id'         => 'modal_window_padding',
						'title'      => __( 'Padding', 'pagex' ),
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'class'      => 'col-3',
						'selector'   => '[el] .pagex-modal-window {padding: [val]}',
					),
					array(
						'id'       => 'mg',
						'title'    => __( 'Background', 'pagex' ),
						'type'     => 'background',
						'class'    => 'col-9',
						'action'   => 'css',
						'selector' => '[el].pagex-modal {background: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Modal Window Content', 'pagex' ),
					),
					array(
						'id'         => 'mp',
						'title'      => __( 'Padding', 'pagex' ),
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'class'      => 'col-3',
						'selector'   => '[el] .pagex-modal-window-content {padding: [val]}',
					),
					array(
						'id'         => 'mw',
						'title'      => __( 'Max. Width', 'pagex' ),
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'class'      => 'col-3',
						'selector'   => '[el] .pagex-modal-window-content {max-width: [val]}',
					),
					array(
						'id'       => 'mr',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-modal-window-content {border-radius: [val]}',
					),
					array(
						'id'       => 'ms',
						'title'    => __( 'Box Shadow', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-modal-window-content {box-shadow: [val]}',
					),
					array(
						'id'       => 'mc',
						'title'    => __( 'Background', 'pagex' ),
						'type'     => 'background',
						'class'    => 'col-9',
						'action'   => 'css',
						'selector' => '[el] .pagex-modal-window-content {background: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Close Icon', 'pagex' ),
					),
					array(
						'id'       => 'me',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-modal-window-close {margin: [val]}',
					),
					array(
						'id'      => 'mx',
						'title'   => __( 'Icon Position', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-3',
						'options' => array(
							''        => __( 'Inside modal content', 'pagex' ),
							'outside' => __( 'Outside modal content', 'pagex' ),
						),
					),
					array(
						'id'       => 'kj',
						'title'    => __( 'Icon Align', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-3',
						'action'   => 'class',
						'selector' => '.pagex-modal-window-close',
						'options'  => array(
							''                              => __( 'Right', 'pagex' ),
							'pagex-modal-window-close-left' => __( 'Left', 'pagex' ),
						),
					),
					array(
						'id'       => 'oi',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'class'    => 'col-4',
						'action'   => 'css',
						'selector' => '[el] .pagex-modal-window-close {color: [val]}',
					),
					array(
						'id'       => 'qw',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'type'     => 'color',
						'class'    => 'col-8',
						'action'   => 'css',
						'selector' => '[el] .pagex-modal-window-close:hover {color: [val]}',
					),
					array(
						'type' => 'row-end',
					),
				)
			)
		),
	);

	return $elements;
}