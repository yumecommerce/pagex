<?php

/**
 * Nav Menu element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_nav_menu_element( $elements ) {
	$menus = wp_get_nav_menus();

	$menu_options = array();

	foreach ( $menus as $menu ) {
		$menu_options[ $menu->term_id ] = $menu->name;
	}

	if ( empty( $menu_options ) ) {
		$menu_options = array( '' => __( 'There are no menus on your site.', 'pagex' ) );
	}

	$elements[] = array(
		'id'          => 'nav_menu',
		'category'    => 'content',
		'title'       => __( 'Nav Menu', 'pagex' ),
		'description' => __( 'Vertical or horizontal nav menu with optional mobile view', 'pagex' ),
		'type'        => 'dynamic',
		'info'        => 'https://github.com/yumecommerce/pagex/wiki/Nav-Menu',
		'callback'    => 'pagex_nav_menu',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'menu',
						'title'       => __( 'Menu', 'pagex' ),
						'description' => __( 'You can manage your menus in WordPress admin dashboard.', 'pagex' ) . ' <a href="' . admin_url( 'nav-menus.php' ) . '" target="_blank">' . __( 'Open menus screen', 'pagex' ) . '</a>',
						'type'        => 'select',
						'options'     => $menu_options
					),
					array(
						'id'      => 'nav_layout',
						'title'   => __( 'Layout', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							'pagex-nav-menu-dropdown'   => __( 'Dropdown', 'pagex' ),
							'pagex-nav-menu-vertical'   => __( 'Vertical', 'pagex' ),
							'pagex-nav-menu-horizontal' => __( 'Horizontal', 'pagex' ),
						)
					),
				),
			),
			array(
				'title'  => __( 'Top Level', 'pagex' ),
				'params' => array(
					array(
						'type'        => 'heading',
						'title'       => __( 'Style for Desktop Menu', 'pagex' ),
						'description' => __( 'These options will affect only top level of menu.', 'pagex' ),
					),
					array(
						'id'      => 'align',
						'title'   => __( 'Align', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							''                       => __( 'Left', 'pagex' ),
							'pagex-nav-menu-center'  => __( 'Center', 'pagex' ),
							'pagex-nav-menu-end'     => __( 'Right', 'pagex' ),
							'pagex-nav-menu-between' => __( 'Between', 'pagex' ),
							'pagex-nav-menu-around'  => __( 'Around', 'pagex' ),
							'pagex-nav-menu-stretch' => __( 'Stretch', 'pagex' ),
						)
					),
					array(
						'id'       => 'qw',
						'type'     => 'typography',
						'selector' => '> .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a',
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a',
						'property' => 'margin',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a',
						'property' => 'padding',
					),
					array(
						'id'       => 'rt',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a {border-radius: [val]}',
					),
					array(
						'id'       => 'ty',
						'title'    => __( 'Border Width', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a {border-width: [val]}',
					),
					array(
						'type' => 'clear',
					),

					array(
						'id'       => 'yu',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a {color: [val]}',
					),
					array(
						'id'       => 'ui',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:hover > a {color: [val]}',
					),
					array(
						'id'       => 'io',
						'title'    => __( 'Active Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li.current-menu-item > a {color: [val]}',
					),

					array(
						'id'       => 'op',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a {background: [val]}',
					),
					array(
						'id'       => 'as',
						'title'    => __( 'Background on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:hover > a {background: [val]}',
					),
					array(
						'id'       => 'sd',
						'title'    => __( 'Active Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li.current-menu-item > a {background: [val]}',
					),
					array(
						'id'       => 'df',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a {border-color: [val]}',
					),
					array(
						'id'       => 'fg',
						'title'    => __( 'Border Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:hover > a {border-color: [val]}',
					),
					array(
						'id'       => 'gh',
						'title'    => __( 'Active Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li.current-menu-item > a {border-color: [val]}',
					),
					array(
						'id'          => 'hj',
						'title'       => __( 'Box Shadow', 'pagex' ),
						'description' => __( 'Property values: horizontal, vertical, blur, size, color.', 'pagex' ),
						'type'        => 'text',
						'action'      => 'css',
						'responsive'  => true,
						'class'       => 'col-4',
						'selector'    => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a {box-shadow: [val]}',
					),
					array(
						'id'         => 'jk',
						'title'      => __( 'Box Shadow on Hover', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:hover > a {box-shadow: [val]}',
					),
					array(
						'id'         => 'kl',
						'title'      => __( 'Active Box Shadow', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li.current-menu-item > a {box-shadow: [val]}',
					),
					array(
						'id'         => 'wq',
						'title'      => __( 'Opacity', 'pagex' ),
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li > a {opacity: [val]}',
					),
					array(
						'id'         => 'ew',
						'title'      => __( 'Opacity on Hover', 'pagex' ),
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:hover > a {opacity: [val]}',
					),
					array(
						'id'         => 're',
						'title'      => __( 'Active Opacity', 'pagex' ),
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li.current-menu-item > a {opacity: [val]}',
					),
				)
			),
			array(
				'title'  => __( 'Sub Menu', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Style for Desktop Sub Menus', 'pagex' ),
					),
					array(
						'id'       => 'zx',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu {border-radius: [val]} [el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu > li:first-child > a {border-top-right-radius: [val]; border-top-left-radius: [val]} [el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu > li:last-child > a {border-bottom-right-radius: [val]; border-bottom-left-radius: [val]}',
					),
					array(
						'id'       => 'xc',
						'title'    => __( 'Vertical Padding', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu {padding: [val] 0} [el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu .sub-menu {top: -[val]}',
					),
					array(
						'id'       => 'cv',
						'title'    => __( 'Box Shadow', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu {box-shadow: [val]}',
					),
					array(
						'id'       => 'vb',
						'title'    => __( 'Min Width', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) ul {min-width: [val]}',
					),
					array(
						'id'       => 'bn',
						'title'    => __( 'Background', 'pagex' ),
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu {background: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Style for Desktop Sub Menu Items', 'pagex' ),
					),
					array(
						'id'       => 'nm',
						'type'     => 'typography',
						'selector' => '> .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu',
					),
					array(
						'id'         => 'aq',
						'title'      => __( 'Padding', 'pagex' ),
						'type'       => 'text',
						'responsive' => true,
						'class'      => 'col-6',
						'action'     => 'css',
						'selector'   => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu a {padding: [val]}',
					),

					array(
						'type' => 'clear',
					),

					array(
						'id'       => 'sw',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu a {color: [val]}',
					),
					array(
						'id'       => 'de',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu li:hover > a {color: [val]}',
					),
					array(
						'id'       => 'fr',
						'title'    => __( 'Active Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu li.current-menu-item > a {color: [val]}',
					),

					array(
						'id'       => 'gt',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu a {background: [val]}',
					),
					array(
						'id'       => 'hy',
						'title'    => __( 'Background on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu li:hover > a {background: [val]}',
					),
					array(
						'id'       => 'ju',
						'title'    => __( 'Active Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu li.current-menu-item > a {background: [val]}',
					),
					array(
						'id'          => 'ki',
						'title'       => __( 'Box Shadow', 'pagex' ),
						'description' => __( 'Property values: horizontal, vertical, blur, size, color.', 'pagex' ),
						'type'        => 'text',
						'action'      => 'css',
						'class'       => 'col-4',
						'selector'    => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu a {box-shadow: [val]}',
					),
					array(
						'id'       => 'lo',
						'title'    => __( 'Box Shadow on Hover', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu li:hover > a {box-shadow: [val]}',
					),
					array(
						'id'       => 'qa',
						'title'    => __( 'Active Box Shadow', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] > .element-wrap > .pagex-nav-menu-wrapper > .pagex-nav-menu-desktop > ul > li:not(.has-mega-menu) .sub-menu li.current-menu-item > a {box-shadow: [val]}',
					),
				)
			),
			array(
				'title'  => __( 'Mega Menu', 'pagex' ),
				'params' => array(
					array(
						'type'        => 'heading',
						'title'       => __( 'Mega Menu', 'pagex' ),
						'description' => __( 'Mega menu nav is Layout Builder items added as nav submenu. Each Layout can have its own style but also some global styles can be added via these options.', 'pagex' ),
					),
					array(
						'id'         => 'nb',
						'title'      => __( 'Padding', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-nav-menu-desktop li.has-mega-menu > .sub-menu {padding: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Style for Desktops', 'pagex' ),
					),
					array(
						'id'       => 'mn',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-nav-menu-desktop li.has-mega-menu > .sub-menu {border-radius: [val]}',
					),
					array(
						'id'       => 'ml',
						'title'    => __( 'Box Shadow', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-nav-menu-desktop li.has-mega-menu > .sub-menu {box-shadow: [val]}',
					),
					array(
						'id'       => 'bv',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-desktop li.has-mega-menu > .sub-menu {background: [val]}',
					),
					array(
						'id'       => 'xz',
						'title'    => __( 'Text Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-desktop li.has-mega-menu .sub-menu {color: [val]}',
					),
					array(
						'id'       => 'cx',
						'title'    => __( 'Links Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-desktop li.has-mega-menu .sub-menu a {color: [val]}',
					),
					array(
						'id'       => 'vc',
						'title'    => __( 'Links Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-desktop li.has-mega-menu .sub-menu li:hover > a {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Style for Mobiles', 'pagex' ),
					),

					array(
						'id'       => 'az',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile li.has-mega-menu > .sub-menu {background: [val]}',
					),
					array(
						'id'       => 'sx',
						'title'    => __( 'Text Color', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile li.has-mega-menu .sub-menu {color: [val]}',
					),
					array(
						'id'       => 'dc',
						'title'    => __( 'Links Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile li.has-mega-menu .sub-menu a {color: [val]}',
					),
					array(
						'id'       => 'fv',
						'title'    => __( 'Links Color on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile li.has-mega-menu .sub-menu li:hover > a {color: [val]}',
					),
				)
			),
			array(
				'title'  => __( 'Mobile', 'pagex' ),
				'params' => array(
					array(
						'id'          => 'breakpoint',
						'title'       => __( 'Mobile Breakpoint', 'pagex' ),
						'description' => __( 'If selected "None", the menu will not be collapsed to its mobile state.', 'pagex' ),
						'type'        => 'select',
						'options'     => array(
							''                    => __( 'None', 'pagex' ),
							'pagex-breakpoint-sm' => __( 'Mobile', 'pagex' ) . ' < 576px',
							'pagex-breakpoint-md' => __( 'Tablet', 'pagex' ) . ' < 768px',
							'pagex-breakpoint-lg' => __( 'Tablet (landscape)', 'pagex' ) . ' < 1024px',
						)
					),
					array(
						'type'      => 'row-start',
						'condition' => array(
							'breakpoint' => array(
								'pagex-breakpoint-sm',
								'pagex-breakpoint-md',
								'pagex-breakpoint-lg'
							)
						)
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Toggle Button', 'pagex' ),
					),
					array(
						'id'       => 'ed',
						'title'    => __( 'Align', 'pagex' ),
						'type'     => 'select',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-nav-menu-mobile-trigger-wrapper {justify-content: [val]}',
						'scope'    => true,
						'options'  => array(
							''         => __( 'Left', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Right', 'pagex' ),
						)
					),
					array(
						'id'       => 'rf',
						'title'    => __( 'Size', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-nav-menu-mobile-trigger .pagex-icon {width: [val]; height: [val]}',
					),
					array(
						'id'       => 'tg',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-nav-menu-mobile-trigger {padding: [val]}',
					),
					array(
						'id'       => 'yh',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-nav-menu-mobile-trigger {border-radius: [val]}',
					),
					array(
						'id'       => 'uj',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile-trigger {color: [val]}',
					),
					array(
						'id'       => 'ik',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile-trigger {background: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Menu Position', 'pagex' ),
					),
					array(
						'id'      => 'offcanvas_pos',
						'title'   => __( 'Offcanvas Position', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							''      => __( 'Left', 'pagex' ),
							'right' => __( 'Right', 'pagex' ),
						)
					),


					array(
						'type'  => 'heading',
						'title' => __( 'Style for Mobile Menu', 'pagex' ),
					),
					array(
						'id'       => 'm_typo',
						'type'     => 'typography',
						'selector' => '.pagex-nav-menu-mobile',
					),
					array(
						'id'       => 'pl',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile a',
						'property' => 'padding',
					),
					array(
						'type' => 'clear',
					),
					array(
						'id'       => 'za',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile a {color: [val]}',
					),
					array(
						'id'       => 'xs',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile li:hover > a {color: [val]}',
					),
					array(
						'id'       => 'cd',
						'title'    => __( 'Active Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-nav-menu-mobile li.current-menu-item > a {color: [val]}',
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

/**
 * Shortcode for nav menu element
 *
 * @param $atts
 *
 * @return string
 */
function pagex_nav_menu( $atts ) {
	$atts = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $atts, array(
		'menu'          => '',
		'breakpoint'    => '',
		'nav_layout'    => '',
		'align'         => '',
		'offcanvas_pos' => 'left',
	) );

	if ( ! $data['menu'] ) {
		return;
	}

	$args = array(
		'menu'        => $data['menu'],
		'menu_class'  => 'pagex-nav-menu pagex-nav-menu-' . $data['menu'],
		'fallback_cb' => '__return_empty_string',
		'container'   => '',
		'echo'        => false,
	);

	// desktop menu
	$menu = wp_nav_menu( $args );

	if ( ! $menu ) {
		return;
	}

	// remove id from ul and li to prevent duplicates between desktop and mobile menus
	$menu = preg_replace( '/(<[^>]+) id=".*?"/i', '$1', $menu );

	if ( $data['nav_layout'] == 'pagex-nav-menu-dropdown' ) {
		// remove has-mega-menu class from li for dropdown so custom style from submenu option will apply
		$menu = preg_replace( '/has-mega-menu/i', '', $menu );
	}

	ob_start();

	echo '<div class="pagex-nav-menu-wrapper ' . $data['breakpoint'] . '">';

	// mobile menu
	// mobile menu should print first so if we have modal window inside mega menu it would not be triggered instead
	if ( $data['breakpoint'] ) {
		echo '<div class="pagex-nav-menu-mobile-trigger-wrapper pagex-breakpoint-mobile pagex-modal-trigger"><div class="pagex-nav-menu-mobile-trigger d-flex"><svg class="pagex-icon"><use xlink:href="#pagex-nav-menu-icon" /></svg></div></div>';
		echo '<div class="pagex-nav-menu-mobile-wrapper">';
		$mobile_menu = preg_replace( '/has-mega-menu/i', '', $menu );
		pagex_modal_window_template( array(
			'content'   => '<div class="pagex-nav-menu-dropdown pagex-nav-menu-mobile">' . $mobile_menu . '</div>',
			'offcanvas' => $data['offcanvas_pos']
		) );
		echo '</div>';
	}

	echo '<div class="pagex-breakpoint-desktop pagex-nav-menu-desktop ' . $data['nav_layout'] . ' ' . $data['align'] . '">';
	echo $menu;
	echo '</div></div>';

	return ob_get_clean();
}
