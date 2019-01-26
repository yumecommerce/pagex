<?php

/**
 * Post Comments element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_post_comments_element( $elements ) {
	$elements[] = array(
		'id'          => 'post_comments',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Post Comments', 'pagex' ),
		'description' => __( 'Display comments form', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_post_comments',
		'options'     => array(
			array(
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Comments Title', 'pagex' ),
					),
					array(
						'id'       => 'qw',
						'type'     => 'typography',
						'selector' => '.comments-title',
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .comments-title {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Comments Meta', 'pagex' ),
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Comment Author Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .comment-author {color: [val]}',
					),
					array(
						'id'       => 'rt',
						'title'    => __( 'Comment Date Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .comment-metadata {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Comment Content', 'pagex' ),
					),
					array(
						'id'       => 'ty',
						'type'     => 'typography',
						'selector' => '.comment-content',
					),
					array(
						'id'       => 'yu',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .comment-content {color: [val]}',
					),
					array(
						'id'       => 'ui',
						'title'    => __( 'Separator Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .comment-list article, [el] .comment-list .pingback, [el] .comment-list .trackback {border-color: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Reply Link', 'pagex' ),
					),
					array(
						'id'       => 'io',
						'title'    => __( 'Reply Link', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .comment-reply-link {color: [val]}',
					),
					array(
						'id'       => 'op',
						'title'    => __( 'Reply Link on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .comment-reply-link:hover {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Reply Title', 'pagex' ),
					),
					array(
						'id'       => 'as',
						'type'     => 'typography',
						'selector' => '.comment-reply-title',
					),
					array(
						'id'       => 'sd',
						'title'    => __( 'Reply Title', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .comment-reply-title {color: [val]}',
					),
					array(
						'id'       => 'df',
						'title'    => __( 'Reply Comment Notes', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .comment-notes, [el] .comment-awaiting-moderation, [el] .logged-in-as, [el] .form-allowed-tags {color: [val]}',
					),
				),
			),
			array(
				'title' => __( 'Form', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Inputs', 'pagex' ),
					),
					array(
						'id'       => 'gh',
						'type'     => 'typography',
						'selector' => 'p[class^=comment-form]',
					),
					array(
						'id'       => 'hj',
						'title'    => __( 'Label Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] p[class^=comment-form] {color: [val]}',
					),
					array(
						'id'       => 'jk',
						'title'    => __( 'Inputs Color', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] p[class^=comment-form] [name] {color: [val]}',
					),
					array(
						'id'       => 'kl',
						'title'    => __( 'Border Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] p[class^=comment-form] [name] {border-color: [val]}',
					),
					array(
						'id'       => 'lz',
						'title'    => __( 'Border Color on Focus', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] p[class^=comment-form] [name]:focus {border-color: [val]}',
					),
					array(
						'id'       => 'zx',
						'title'    => __( 'Border Radius', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] p[class^=comment-form] [name] {border-radius: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Button', 'pagex' ),
					),
					array(
						'id'       => 'fg',
						'type'     => 'button_style',
						'selector' => '[type="submit"]',
					),
				)
			)
		),
	);

	return $elements;
}

/**
 * Post Comments shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_post_comments( $atts ) {
	ob_start();

	add_filter( 'comments_template', 'pagex_comments_template' );
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;
	remove_filter( 'comments_template', 'pagex_comments_template' );

	return ob_get_clean();
}