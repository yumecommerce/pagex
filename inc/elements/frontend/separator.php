<?php

/**
 * Separator element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_separator_element( $elements ) {
	$template = '<div class="pagex-separator-wrapper"><div class="pagex-separator"><div class="pagex-separator-line pagex-separator-left-line"></div><% if (data.icon_before && data.icon_before.length) { %><div class="pagex-separator-icon pagex-separator-icon-before d-flex"><% print(pagex.genIcon("icon_before", data)) %></div> <% } %><% if (data.title && data.title.length) { %><div class="pagex-separator-title pagex-lang-str"><%= data.title %></div><% } %><% if (data.icon_after && data.icon_after.length) { %><div class="pagex-separator-icon pagex-separator-icon-after d-flex"><% print(pagex.genIcon("icon_after", data)) %></div> <% } %><% if(data.icon_before.length || data.icon_after.length || data.title.length) { %><div class="pagex-separator-line pagex-separator-right-line"></div><% } %></div></div>';

	$elements[] = array(
		'id'          => 'separator',
		'category'    => 'content',
		'title'       => __( 'Separator', 'pagex' ),
		'description' => __( 'Horizontal separator line with a custom title', 'pagex' ),
		'type'        => 'static',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Separator', 'pagex' ),
					),
					array(
						'id'       => 'qw',
						'class'    => 'col-4',
						'title'    => __( 'Border Style', 'pagex' ),
						'type'     => 'select',
						'action'   => 'css',
						'selector' => '[el] .pagex-separator-line {border-style: [val]}',
						'options'  => array(
							''       => __( 'Solid', 'pagex' ),
							'dashed' => __( 'Dashed', 'pagex' ),
							'dotted' => __( 'Dotted', 'pagex' ),
						)
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Border Width', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-separator-line {border-width: [val]}',
						'class'    => 'col-4',
						'type'     => 'text',
					),
					array(
						'id'         => 'wq',
						'title'      => __( 'Max. Width', 'pagex' ),
						'action'     => 'css',
						'selector'   => '[el] .pagex-separator {max-width: [val]}',
						'responsive' => true,
						'class'      => 'col-4',
						'type'       => 'text',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Border Color', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-separator-line {border-color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
					array(
						'id'          => 'aq',
						'title'       => __( 'Align', 'pagex' ),
						'description' => __( 'If Max. Width option is set', 'pagex' ),
						'type'        => 'select',
						'responsive'  => true,
						'action'      => 'css',
						'class'       => 'col-4',
						'selector'    => '[el] .pagex-separator-wrapper {justify-content: [val]}',
						'options'     => array(
							''           => __( 'Center', 'pagex' ),
							'flex-start' => __( 'Left', 'pagex' ),
							'flex-end'   => __( 'Right', 'pagex' ),
						)
					),
					array(
						'type' => 'clear',
					),
					array(
						'id'       => 'rt',
						'label'    => __( 'Hide Left Line', 'pagex' ),
						'action'   => 'class',
						'selector' => '.pagex-separator-left-line',
						'class'    => 'col-auto',
						'type'     => 'checkbox',
						'value'    => 'd-none',
					),
					array(
						'id'       => 'ty',
						'label'    => __( 'Hide Right Line', 'pagex' ),
						'action'   => 'class',
						'selector' => '.pagex-separator-right-line',
						'class'    => 'col-auto',
						'type'     => 'checkbox',
						'value'    => 'd-none',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Title', 'pagex' ),
					),
					array(
						'id'       => 'title',
						'title'    => __( 'Title', 'pagex' ),
						'type'     => 'text',
						'action'   => 'content',
						'selector' => '.pagex-separator-title',
					),
					array(
						'id'       => 'yu',
						'type'     => 'typography',
						'selector' => '.pagex-separator-title',
					),
					array(
						'id'       => 'ui',
						'title'    => __( 'Title Color', 'pagex' ),
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-separator-title {color: [val]}',
						'type'     => 'color',
					),
					array(
						'id'       => 'io',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-4',
						'action'   => 'css',
						'selector' => '[el] .pagex-separator-title {margin: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Icon Before Title', 'pagex' ),
					),
					array(
						'id'       => 'icon_before',
						'type'     => 'icon',
						'selector' => '.pagex-separator-icon-before',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Icon After Title', 'pagex' ),
					),
					array(
						'id'       => 'icon_after',
						'type'     => 'icon',
						'selector' => '.pagex-separator-icon-after',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Icon Style', 'pagex' ),
					),
					array(
						'id'       => 'op',
						'title'    => __( 'Icon Color', 'pagex' ),
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-separator-icon {color: [val]}',
						'type'     => 'color',
					),
				),
			),
		),
	);

	return $elements;
}