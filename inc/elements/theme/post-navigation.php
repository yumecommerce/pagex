<?php

/**
 * Post Navigation element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_post_navigation_element( $elements ) {
	$elements[] = array(
		'id'          => 'post_navigation',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Post Navigation', 'pagex' ),
		'description' => __( 'Displays the navigation to next/previous post', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_post_navigation',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'next_text',
						'title'       => __( 'Text for next post', 'pagex' ),
						'description' => __( 'HTML tags are allowed. Use %title to print the title of the post.', 'pagex' ),
						'type'        => 'text',
					),
					array(
						'id'    => 'prev_text',
						'title' => __( 'Text for previous post', 'pagex' ),
						'type'  => 'text',
					),
					array(
						'id'      => 'display',
						'title'   => __( 'Display', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							''     => __( 'All', 'pagex' ),
							'next' => __( 'Only next link', 'pagex' ),
							'prev' => __( 'Only prev link', 'pagex' ),
						),
					),
					array(
						'id'       => 'qw',
						'type'     => 'typography',
						'selector' => 'a',
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Link Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a {color: [val]}',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Link Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a:hover {color: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Post Navigation shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_post_navigation( $atts ) {
	$data = wp_parse_args( Pagex::get_dynamic_data( $atts ), array(
		'prev_text' => '%title',
		'next_text' => '%title',
		'display'   => '',
	) );

	if ( ! $nav = get_the_post_navigation( $data ) ) {
		return;
	}
	// remove link wrapper if we display only a specific part
	if ($data['display'] == 'next') {
		$nav = preg_replace( '/<div class="nav-previous">.*?<\/a><\/div>/s', '', $nav );
	} elseif ($data['display'] == 'prev') {
		$nav = preg_replace( '/<div class="nav-next">.*?<\/a><\/div>/s', '', $nav );
	}

	// check if nav-links is empty. in case we do not have prev/next post and we removed any prev/next one
	if (strpos($nav, '<div class="nav-links"></div>') !== false) {
		return;
	}

	return '<div class="pagex-post-navigation">' . $nav . '</div>';
}