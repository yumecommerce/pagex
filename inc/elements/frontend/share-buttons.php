<?php

/**
 * Share Buttons element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_share_buttons_element( $elements ) {
	$template = '<div class="pagex-share-buttons d-flex flex-wrap">
		<% data.items.forEach( function(item, index) { %>
			<div class="pagex-share-buttons-item">
				<div class="pagex-share-button pagex-share-button-<%- item.social %>" data-share="<%- item.social %>">
					<% if (data.hide_icon != "true") { %>
						<div class="pagex-share-button-icon"><i class="<% if (item.social == "envelope" || item.social == "print") { print("fas") } else { print("fab") } %> fa-<%- item.social %> pagex-icon"></i></div>
					<% } %>
					<% if (data.hide_label != "true") { %>
					<div class="pagex-share-button-label pagex-lang-str"><% if (item.label && item.label.length) { print(item.label) } else {  print(item.social) } %></div>
					<% } %>
				</div>
			</div>
		<% }); %>
	</div>';

	$elements[] = array(
		'id'          => 'share_buttons',
		'category'    => 'content',
		'title'       => __( 'Share Buttons', 'pagex' ),
		'description' => __( 'List of social media buttons with share link', 'pagex' ),
		'type'        => 'static',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'     => 'items',
						'title'  => __( 'Items', 'pagex' ),
						'type'   => 'repeater',
						'params' => array(
							array(
								'id'      => 'social',
								'title'   => __( 'Social Network', 'pagex' ),
								'type'    => 'select',
								'options' => array(
									'facebook'      => 'Facebook',
									'twitter'       => 'Twitter',
									'google'        => 'Google',
									'linkedin'      => 'Linkedin',
									'pinterest'     => 'Pinterest',
									'reddit'        => 'Reddit',
									'vk'            => 'Vk',
									'odnoklassniki' => 'Odnoklassniki',
									'tumblr'        => 'Tumblr',
									'delicious'     => 'Delicious',
									'digg'          => 'Digg',
									'skype'         => 'Skype',
									'stumbleupon'   => 'Stumbleupon',
									'telegram'      => 'Telegram',
									'pocket'        => 'Pocket',
									'xing'          => 'Xing',
									'whatsapp'      => 'WhatsApp',
									'envelope'      => 'Email',
									'print'         => __( 'Print', 'pagex' ),
								)
							),
							array(
								'id'    => 'label',
								'title' => __( 'Custom Label', 'pagex' ),
								'type'  => 'text',
							),
						),
					),
				),
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
//					array(
//						'id'      => 'style',
//						'title'   => __( 'Style', 'pagex' ),
//						'type'    => 'select',
//						'class'   => 'col-6',
//						'options' => array(
//							''       => __( 'Official', 'pagex' ),
//							'custom' => __( 'Custom', 'pagex' ),
//						)
//					),
					array(
						'id'       => 'wq',
						'title'    => __( 'Layout', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-6',
						'action'   => 'class',
						'selector' => '.pagex-share-buttons',
						'options'  => array(
							''            => __( 'Horizontal', 'pagex' ),
							'flex-column' => __( 'Vertical', 'pagex' ),
						)
					),
					array(
						'id'         => 'qw',
						'title'      => __( 'Alignment', 'pagex' ),
						'type'       => 'select',
						'class'      => 'col-6',
						'responsive' => true,
						'action'     => 'class',
						'selector'   => '.pagex-share-buttons',
						'options'    => array(
							''                            => __( 'Default', 'pagex' ),
							'justify-content[pref]start'  => __( 'Left', 'pagex' ),
							'justify-content[pref]center' => __( 'Center', 'pagex' ),
							'justify-content[pref]end'    => __( 'Right', 'pagex' ),
						),
						'condition'  => array(
							'!wq' => array( 'flex-column' )
						)
					),
					array(
						'id'         => 'ew',
						'title'      => __( 'Alignment', 'pagex' ),
						'type'       => 'select',
						'class'      => 'col-6',
						'responsive' => true,
						'action'     => 'class',
						'selector'   => '.pagex-share-buttons',
						'options'    => array(
							''                        => __( 'Default', 'pagex' ),
							'align-items[pref]start'  => __( 'Left', 'pagex' ),
							'align-items[pref]center' => __( 'Center', 'pagex' ),
							'align-items[pref]end'    => __( 'Right', 'pagex' ),
						),
						'condition'  => array(
							'wq' => array( 'flex-column' )
						)
					),
					array(
						'id'    => 'hide_icon',
						'label' => __( 'Hide Icon', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
					),
					array(
						'id'    => 'hide_label',
						'label' => __( 'Hide Label', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Label', 'pagex' ),
					),
					array(
						'id'       => 'we',
						'type'     => 'typography',
						'selector' => '.pagex-share-button-label',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Icon', 'pagex' ),
					),
					array(
						'id'         => 'is',
						'title'      => __( 'Size', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] .pagex-share-button-icon .pagex-icon {width: [val]; height: [val]; font-size: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Button', 'pagex' ),
					),
					array(
						'id'       => 'ma',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-share-button',
						'property' => 'margin',
					),
					array(
						'id'       => 'pa',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-share-button',
						'property' => 'padding',
					),
					array(
						'id'       => 'br',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-share-button {border-radius: [val]}',
					),
					array(
						'id'       => 'bw',
						'title'    => __( 'Border Width', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-share-button {border-width: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Style', 'pagex' ),
					),

					array(
						'id'       => 'er',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-share-button {color: [val]}',
					),
					array(
						'id'       => 'rt',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-share-button:hover {color: [val]}',
					),
					array(
						'id'       => 'ty',
						'title'    => __( 'Background Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-share-button {background: [val]}',
					),
					array(
						'id'       => 'yu',
						'title'    => __( 'Background Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-share-button:hover {background: [val]}',
					),
					array(
						'id'       => 'ui',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-share-button {border-color: [val]}',
					),
					array(
						'id'       => 'io',
						'title'    => __( 'Border Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-share-button:hover {border-color: [val]}',
					),
					array(
						'id'          => 'op',
						'title'       => __( 'Box Shadow', 'pagex' ),
						'description' => __( 'Property values: horizontal, vertical, blur, size, color.', 'pagex' ),
						'type'        => 'text',
						'action'      => 'css',
						'responsive'  => true,
						'class'       => 'col-6',
						'selector'    => '[el] .pagex-share-button {box-shadow: [val]}',
					),
					array(
						'id'         => 'as',
						'title'      => __( 'Box Shadow', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-6',
						'selector'   => '[el] .pagex-share-button:hover {box-shadow: [val]}',
					),
				)
			),
		),
	);

	return $elements;
}