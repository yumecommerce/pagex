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
	ob_start();

	echo '<div class="pagex-post-navigation">';
	the_post_navigation();
	echo '</div>';

	return ob_get_clean();
}