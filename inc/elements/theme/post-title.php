<?php

/**
 * Post Title element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_post_title_element( $elements ) {
	$elements[] = array(
		'id'          => 'post_title',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp', 'pagex_excerpt_tmp' ),
		'title'       => __( 'Post Title', 'pagex' ),
		'description' => __( 'Display post title with optional link to a single post', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_post_title',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'tag',
						'title'   => 'HTML ' . __( 'Tag', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							'h1',
							'h2',
							'h3',
							'h4',
							'h5',
							'h6',
							'p',
							'div'
						),
					),
					array(
						'id'    => 'before',
						'title' => __( 'Before', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-4',
					),
					array(
						'id'    => 'after',
						'title' => __( 'After', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-4',
					),

					array(
						'id'    => 'link',
						'label' => __( 'Use as a link', 'pagex' ),
						'type'  => 'checkbox',
					),
					array(
						'id'       => 'typo',
						'type'     => 'typography',
						'selector' => '.pagex-post-title',
					),
					array(
						'id'       => 'qw',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-title {color: [val]}',
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-title:hover {color: [val]}',
					),
					array(
						'type'     => 'clear',
					),

					array(
						'id'         => 'er',
						'title'      => __( 'Opacity', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'selector'   => '[el] .pagex-post-title {opacity: [val]}',
					),
					array(
						'id'         => 'rt',
						'title'      => __( 'Opacity on Hover', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'selector'   => '[el] .pagex-post-title:hover {opacity: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Post Title shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_post_title( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'tag'  => 'h1',
		'link' => false,
	) );

	$title = wp_kses_post( get_the_title() );

	if ( isset( $data['before'] ) ) {
		$title = $data['before'] . $title;
	}

	if ( isset( $data['after'] ) ) {
		$title = $title . $data['after'];
	}

	if ( $data['link'] ) {
		$title = '<a href="' . esc_url( get_permalink() ) . '">' . $title . '</a>';
	}

	$title = '<' . $data['tag'] . ' class="pagex-post-title">' . $title . '</' . $data['tag'] . '>';

	return $title;
}