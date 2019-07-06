<?php

/**
 * Counter element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_counter_element( $elements ) {
	$template = '
<% var json = {
startVal: Number(data.start),
endVal: data.end ? Number(data.end) : 1000,
separator: data.sep,
duration: data.duration ? Number(data.duration) : 2,
};  %>
<div class="pagex-counter">
	<% if (data.text_before) {  %><div class="pagex-counter-text-before pagex-lang-str"><%= data.text_before %></div><% } %>
<div class="pagex-counter-wrapper d-flex">
	<div class="pagex-counter-prefix pagex-lang-str"><%= data.prefix %></div>
	<div class="pagex-counter-number" data-counter="<%- JSON.stringify(json) %>"></div>
	<div class="pagex-counter-suffix pagex-lang-str"><%= data.suffix %></div>
</div>
	<div class="pagex-counter-text pagex-lang-str"><%= data.text %></div>
</div>';

	$elements[] = array(
		'id'          => 'counter',
		'category'    => 'content',
		'title'       => __( 'Counter', 'pagex' ),
		'description' => __( 'Animated numbered counter', 'pagex' ),
		'type'        => 'static',
		'info'        => 'https://github.com/yumecommerce/pagex/wiki/Counter',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'    => 'start',
						'title' => __( 'Starting Number', 'pagex' ),
						'class' => 'col-3',
						'type'  => 'number',
					),
					array(
						'id'    => 'end',
						'title' => __( 'Ending Number', 'pagex' ),
						'class' => 'col-3',
						'type'  => 'number',
					),
					array(
						'id'    => 'prefix',
						'title' => __( 'Prefix', 'pagex' ),
						'class' => 'col-3',
						'type'  => 'text',
					),
					array(
						'id'    => 'suffix',
						'title' => __( 'Suffix', 'pagex' ),
						'class' => 'col-3',
						'type'  => 'text',
					),
					array(
						'id'          => 'duration',
						'title'       => __( 'Animation Duration', 'pagex' ),
						'description' => __( 'In seconds', 'pagex' ),
						'class'       => 'col-3',
						'type'        => 'number',
					),
					array(
						'id'    => 'sep',
						'title' => __( 'Thousand Separator', 'pagex' ),
						'class' => 'col-3',
						'type'  => 'text',
					),
					array(
						'type' => 'clear',
					),
					array(
						'id'    => 'text_before',
						'title' => __( 'Text Before', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-6',
					),
					array(
						'id'    => 'text',
						'title' => __( 'Text After', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-6',
					),
				),
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Basic Style', 'pagex' ),
					),
					array(
						'id'       => 'h',
						'title'    => __( 'Vertical Align', 'pagex' ),
						'type'     => 'select',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-counter-wrapper {align-items: [val]}',
						'options'  => array(
							''         => __( 'Top', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Bottom', 'pagex' ),
						),
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Prefix', 'pagex' ),
					),
					array(
						'id'       => 'q',
						'type'     => 'typography',
						'selector' => '.pagex-counter-prefix',
					),
					array(
						'id'       => 'w',
						'title'    => __( 'Margin', 'pagex' ),
						'action'   => 'css',
						'type'     => 'dimension',
						'class'    => 'col-6',
						'selector' => '[el] .pagex-counter-prefix',
						'property' => 'margin',
					),
					array(
						'id'       => 'o',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-counter-prefix {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Number', 'pagex' ),
					),
					array(
						'id'       => 'e',
						'type'     => 'typography',
						'selector' => '.pagex-counter-number',
					),
					array(
						'id'       => 'r',
						'title'    => __( 'Margin', 'pagex' ),
						'action'   => 'css',
						'type'     => 'dimension',
						'class'    => 'col-6',
						'selector' => '[el] .pagex-counter-number',
						'property' => 'margin',
					),
					array(
						'id'       => 'p',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-counter-number {color: [val]}',
					),
					array(
						'id'         => 'j',
						'title'      => __( 'Width', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-3',
						'selector'   => '[el] .pagex-counter-number {width: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Suffix', 'pagex' ),
					),
					array(
						'id'       => 't',
						'type'     => 'typography',
						'selector' => '.pagex-counter-suffix',
					),
					array(
						'id'       => 'y',
						'title'    => __( 'Margin', 'pagex' ),
						'action'   => 'css',
						'type'     => 'dimension',
						'class'    => 'col-6',
						'selector' => '[el] .pagex-counter-suffix',
						'property' => 'margin',
					),
					array(
						'id'       => 'a',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-counter-suffix {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Text Before', 'pagex' ),
					),
					array(
						'id'       => 'd',
						'type'     => 'typography',
						'selector' => '.pagex-counter-text-before',
					),
					array(
						'id'       => 'f',
						'title'    => __( 'Margin', 'pagex' ),
						'action'   => 'css',
						'type'     => 'dimension',
						'class'    => 'col-6',
						'selector' => '[el] .pagex-counter-text-before',
						'property' => 'margin',
					),
					array(
						'id'       => 'g',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-counter-text-before {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Text After', 'pagex' ),
					),
					array(
						'id'       => 'u',
						'type'     => 'typography',
						'selector' => '.pagex-counter-text',
					),
					array(
						'id'       => 'i',
						'title'    => __( 'Margin', 'pagex' ),
						'action'   => 'css',
						'type'     => 'dimension',
						'class'    => 'col-6',
						'selector' => '[el] .pagex-counter-text',
						'property' => 'margin',
					),
					array(
						'id'       => 's',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-counter-text {color: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}