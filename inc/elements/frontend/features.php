<?php

/**
 * Features element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_features_element( $elements ) {
	$template = '<div class="pagex-features">
		<div class="pagex-features-items pagex-features-position-<%- data.position %>">
		<% if (data.anim) {data.anim_delay = data.anim_delay ? data.anim_delay : 250;} %>
		<% data.features.forEach( function(feature, index) { %>
			<div class="pagex-feature-item-wrapper" <% if (data.anim) { %> data-animate="<%= data.anim %>" <% } %> <% if (data.anim) { %> data-animate-delay="<% if (index == 0) { print(data.anim_delay) } else { data.anim_delay = data.anim_delay * 1.5; print(data.anim_delay) } %>" <% } %>>
				<div class="pagex-feature-item">
					<% if (data.link_block && feature.link) { %><a class="pagex-feature-item-link-block" <%= feature.link %>></a><% } %>
					<% if (data.position == "title") { %> <div class="pagex-feature-item-content"> <% } %>
					<% if (feature.icon && feature.icon.length) { %>
					<div class="pagex-feature-item-icon-wrapper"><div class="pagex-feature-item-icon"><% print(pagex.genIcon("icon", feature)) %></div></div>
					<% } %>
					<% if (data.position == "left") { %> <div class="pagex-feature-item-content"> <% } %>
					<% if (feature.title) { %>
					<div class="pagex-feature-item-title-wrapper"><h4 class="pagex-feature-item-title pagex-content-editable pagex-lang-str" contenteditable="true"><%= feature.title %></h4></div>
					<% } %>
					<% if (data.position == "title") { %> </div> <% } %>
					<% if (feature.desc) { %>
					<div class="pagex-feature-item-description pagex-content-editable pagex-lang-str" contenteditable="true"><%= feature.desc %></div>
					<% } %>
					<% if (feature.link_text) { %>
					<a class="button pagex-feature-item-link" <%= feature.link %>><%= feature.link_text %></a>
					<% } %>
					<% if (data.position == "left") { %> </div> <% } %>
				</div>
			</div>
		<% }); %>
		</div>
	</div>';

	$elements[] = array(
		'id'          => 'features',
		'category'    => 'content',
		'title'       => __( 'Features', 'pagex' ),
		'description' => __( 'List of boxes', 'pagex' ),
		'type'        => 'static',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'     => 'features',
						'title'  => __( 'Items', 'pagex' ),
						'type'   => 'repeater',
						'params' => array(
							array(
								'id'       => 'title',
								'type'     => 'text',
								'class'    => 'col-12 pagex-repeater-value',
								'title'    => __( 'Title', 'pagex' ),
								'action'   => 'content',
								'selector' => '.pagex-feature-item-title',
							),
							array(
								'id'    => 'desc',
								'type'  => 'text',
								'title' => __( 'Description', 'pagex' ),
								'action'   => 'content',
								'selector' => '.pagex-feature-item-description',
							),
							array(
								'id'    => 'link_text',
								'type'  => 'text',
								'title' => __( 'Link Text', 'pagex' ),
							),
							array(
								'id'    => 'link',
								'type'  => 'link',
								'title' => __( 'Link', 'pagex' ),
							),
							array(
								'id'       => 'icon',
								'type'     => 'icon',
								'selector' => '.pagex-feature-item-icon',
							),
						),
					),
				),
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Layout', 'pagex' ),
					),
					array(
						'id'         => 'qa',
						'title'      => __( 'Columns', 'pagex' ),
						'type'       => 'number',
						'responsive' => true,
						'class'      => 'col-4',
						'action'     => 'css',
						'selector'   => '[el] .pagex-feature-item-wrapper {width: calc(100% / [val] - 0.1px)}', // -0.1px to fix IE issue
					),
					array(
						'id'         => 'ws',
						'title'      => __( 'Horizontal Gap', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-feature-item {margin-left: [val]; margin-right: [val]} [el] .pagex-features-items {margin-left: -[val]; margin-right: -[val]}',
					),
					array(
						'id'         => 'ed',
						'title'      => __( 'Vertical Gap', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-feature-item-wrapper {margin-bottom: [val]}',
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Entrance Animation', 'pagex' ),
					),
					array(
						'id'      => 'anim',
						'title'   => __( 'Animation', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							''           => __( 'None', 'pagex' ),
							'group_fade' => array(
								'label'   => __( 'Fade Animations', 'pagex' ),
								'options' => array(
									'fade-in'       => __( 'Fade In', 'pagex' ),
									'fade-in-up'    => __( 'Fade In Up', 'pagex' ),
									'fade-in-down'  => __( 'Fade In Down', 'pagex' ),
									'fade-in-left'  => __( 'Fade In Left', 'pagex' ),
									'fade-in-right' => __( 'Fade In Right', 'pagex' ),
								)
							),
						),
					),
					array(
						'id'    => 'anim_delay',
						'title' => __( 'Delay (ms)', 'pagex' ),
						'class' => 'col-4',
						'type'  => 'number',
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Icon', 'pagex' ),
					),
					array(
						'id'      => 'position',
						'title'   => __( 'Position', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							'top'   => __( 'Top', 'pagex' ),
							'left'  => __( 'Left', 'pagex' ),
							'title' => __( 'Next to Title', 'pagex' ),
						)
					),
					array(
						'id'        => 'aq',
						'title'     => __( 'Align', 'pagex' ),
						'type'      => 'select',
						'class'     => 'col-4',
						'action'    => 'css',
						'selector'  => '[el] .pagex-feature-item-icon-wrapper {justify-content: [val]}',
						'options'   => array(
							''         => __( 'Left', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Right', 'pagex' ),
						),
						'condition' => array(
							'position' => array( 'top' )
						)
					),
					array(
						'id'        => 'sw',
						'title'     => __( 'Align', 'pagex' ),
						'type'      => 'select',
						'class'     => 'col-4',
						'action'    => 'css',
						'selector'  => '[el] .pagex-features-position-left .pagex-feature-item, [el] .pagex-features-position-title .pagex-feature-item-content {align-items: [val]}',
						'options'   => array(
							''         => __( 'Top', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Bottom', 'pagex' ),
						),
						'condition' => array(
							'position' => array( 'left', 'title' )
						)
					),
					array(
						'id'         => 'we',
						'title'      => __( 'Size', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-feature-item-icon .pagex-icon {width: [val]; height: [val]; font-size: [val]}',
					),
					array(
						'id'         => 'er',
						'title'      => __( 'Padding', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-feature-item-icon {padding: [val]}',
					),
					array(
						'id'         => 'rt',
						'title'      => __( 'Margin', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-feature-item-icon {margin: [val]}',
					),
					array(
						'id'       => 'ty',
						'title'    => __( 'Border Width', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-icon {border: solid [val]}',
					),
					array(
						'id'       => 'yu',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-icon {border-radius: [val]}',
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Title', 'pagex' ),
					),
					array(
						'id'       => 'ui',
						'type'     => 'typography',
						'selector' => '.pagex-feature-item-title',
					),
					array(
						'id'       => 'io',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-feature-item-title-wrapper {margin: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Description', 'pagex' ),
					),
					array(
						'id'       => 'op',
						'type'     => 'typography',
						'selector' => '.pagex-feature-item-description',
					),
					array(
						'id'       => 'pa',
						'title'    => __( 'Margin', 'pagex' ),
						'action'   => 'css',
						'type'     => 'dimension',
						'class'    => 'col-6',
						'selector' => '[el] .pagex-feature-item-description {margin: [val]}',
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Link', 'pagex' ),
					),
					array(
						'id'    => 'link_block',
						'type'  => 'checkbox',
						'label' => __( 'Link the feature block', 'pagex' ),
					),
					array(
						'id'       => 'uj',
						'type'     => 'typography',
						'selector' => '.pagex-feature-item-link',
					),
					array(
						'id'       => 'bw',
						'title'    => __( 'Border Width', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-link {border-width: [val]}',
					),
					array(
						'id'       => 'br',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-link {border-radius: [val]}',
					),
					array(
						'id'       => 'gp',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-link {padding: [val]}',
					),
				)
			),
			array(
				'title'  => __( 'Color', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Icon', 'pagex' ),
					),
					array(
						'id'       => 'az',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-icon {color: [val]}',
					),
					array(
						'id'       => 'sx',
						'title'    => __( 'Background', 'pagex' ),
						'type'     => 'background',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-icon {background: [val]}',
					),
					array(
						'id'       => 'dc',
						'title'    => __( 'Border', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-icon {border-color: [val]}',
					),
					array(
						'id'       => 'fv',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item:hover .pagex-feature-item-icon {color: [val]}',
					),
					array(
						'id'       => 'gb',
						'title'    => __( 'Background on Hover', 'pagex' ),
						'type'     => 'background',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item:hover .pagex-feature-item-icon {background: [val]}',
					),
					array(
						'id'       => 'hn',
						'title'    => __( 'Border on Hover', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item:hover .pagex-feature-item-icon {border-color: [val]}',
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Title', 'pagex' ),
					),
					array(
						'id'       => 'jm',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-title {color: [val]}',
					),
					array(
						'id'       => 'za',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-8',
						'selector' => '[el] .pagex-feature-item:hover .pagex-feature-item-title {color: [val]}',
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Description', 'pagex' ),
					),
					array(
						'id'       => 'xs',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-description {color: [val]}',
					),
					array(
						'id'       => 'cd',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-8',
						'selector' => '[el] .pagex-feature-item:hover .pagex-feature-item-description {color: [val]}',
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Link', 'pagex' ),
					),


					array(
						'id'       => 'vf',
						'title'    => __( 'Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-link {color: [val]}',
					),
					array(
						'id'       => 'bg',
						'title'    => __( 'Background', 'pagex' ),
						'type'     => 'background',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-link {background: [val]}',
					),
					array(
						'id'       => 'nh',
						'title'    => __( 'Border', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item-link {border-color: [val]}',
					),
					array(
						'id'       => 'mj',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item:hover .pagex-feature-item-link {color: [val]}',
					),
					array(
						'id'       => 'ki',
						'title'    => __( 'Background on Hover', 'pagex' ),
						'type'     => 'background',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item:hover .pagex-feature-item-link {background: [val]}',
					),
					array(
						'id'       => 'ju',
						'title'    => __( 'Border on Hover', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-feature-item:hover .pagex-feature-item-link {border-color: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}