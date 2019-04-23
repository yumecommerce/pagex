<?php

/**
 * Layout element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_layout_element( $elements ) {

	$posts = pagex_get_layout_templates();

	$elements[] = array(
		'id'          => 'layout',
		'category'    => 'content',
		'title'       => __( 'Layout', 'pagex' ),
		'description' => __( 'Display selected layout from Layout Builder', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_layout',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'layout',
						'title'       => __( 'Layout', 'pagex' ),
						'description' => __( 'You can create and change layouts via', 'pagex' ) . ' <a href="' . admin_url( 'edit.php?post_type=pagex_layout_builder' ) . '" target="_blank">' . __( 'Layout Builder', 'pagex' ) . '</a>',
						'type'        => 'select',
						'options'     => $posts
					)
				)
			),

		),
	);

	return $elements;
}

/**
 * Shortcode for layout element
 *
 * @param $atts
 *
 * @return string
 */
function pagex_layout( $atts ) {
	$atts = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $atts, array(
		'layout' => '',
	) );

	if ( ! $data['layout'] ) {
		return __( 'Layout is not set.', 'pagex' );
	}

	$layout = Pagex::get_translation_id( $data['layout'], 'pagex_layout_builder' );
	$layout = do_shortcode( get_post_field( 'post_content', $layout ) );

	// prevent modal builder area from editing
	$layout = preg_replace( '/pagex-modal-builder-area/s', '', $layout );

	return apply_filters( 'pagex_content', $layout );
}