<?php

/**
 * Sidebar element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_sidebar_element( $elements ) {
	global $wp_registered_sidebars;

	$options = array();

	if ( ! $wp_registered_sidebars ) {
		$options[''] = __( 'No sidebars were found', 'pagex' );
	} else {
		foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
			$options[ $sidebar_id ] = $sidebar['name'];
		}
	}

	$elements[] = array(
		'id'          => 'sidebar',
		'category'    => 'pagex_post_tmp',
		'title'       => __( 'Sidebar', 'pagex' ),
		'description' => __( 'Display WordPress sidebar area', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_sidebar',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'sidebar',
						'title'       => __( 'Sidebar', 'pagex' ),
						'description' => __( 'Chose sidebar.', 'pagex' ) . ' <a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . __( 'WordPress sidebar areas.', 'pagex' ) . '</a>',
						'type'        => 'select',
						'options'     => $options,
					)
				)
			),
			array(
				'title' => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Widgets', 'pagex' ),
					),
					array(
						'id'       => 'w_p',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .widget {padding: [val]}',
					),
					array(
						'id'       => 'w_m',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .widget {margin: [val]}',
					),
					array(
						'id'       => 'w_b',
						'title'    => __( 'Box Shadow', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .widget {box-shadow: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Widget Title', 'pagex' ),
					),
					array(
						'id'       => 't_t',
						'type'     => 'typography',
						'selector' => '.widgettitle',
					),
					array(
						'id'       => 't_p',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .widgettitle {padding: [val]}',
					),
					array(
						'id'       => 't_m',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .widgettitle {margin: [val]}',
					),
					array(
						'id'       => 't_b',
						'title'    => __( 'Box Shadow', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .widgettitle {box-shadow: [val]}',
					),
					array(
						'id'       => 't_c',
						'title'    => __( 'Color', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .widgettitle {color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Basic Style', 'pagex' ),
					),
					array(
						'id'       => 'b_t',
						'type'     => 'typography',
						'selector' => '.pagex-sidebar',
					),
					array(
						'id'       => 'b_c',
						'title'    => __( 'Text Color', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-sidebar {color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
					array(
						'id'       => 'bl_c',
						'title'    => __( 'Links Color', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-sidebar a {color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
					array(
						'id'       => 'bl_ch',
						'title'    => __( 'Links Color on Hover', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-sidebar a:hover {color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
				)
			)
		),
	);

	return $elements;
}

/**
 * Shortcode for sidebar element
 *
 * @param $atts
 *
 * @return string
 */
function pagex_sidebar( $atts ) {
	$atts = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $atts, array(
		'sidebar' => '',
	) );

	if ( ! $data['sidebar'] ) {
		return '';
	}

	ob_start();

	echo '<div class="pagex-sidebar">';
	dynamic_sidebar( $data['sidebar'] );
	echo '</div>';

	return ob_get_clean();
}