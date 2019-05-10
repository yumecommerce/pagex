<?php

/**
 * Post Content element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_post_content_element( $elements ) {
	$elements[] = array(
		'id'          => 'post_content',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp', 'pagex_excerpt_tmp' ),
		'title'       => __( 'Post Content', 'pagex' ),
		'description' => __( 'Display the full content or excerpt of the post in archive pages', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_post_content',
		'options'     => array(
			array(
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Paragraphs', 'pagex' ),
					),
					array(
						'id'       => 'qw',
						'type'     => 'typography',
						'selector' => '',
					),
					array(
						'id'       => 'wq',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] p, [el] blockquote {margin: [val]}',
					),
					array(
						'type'  => 'clear',
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] {color: [val]}',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Links Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a {color: [val]}',
					),
					array(
						'id'       => 'rt',
						'title'    => __( 'Links Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a:hover {color: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Headings', 'pagex' ),
					),
					array(
						'id'       => 'ty',
						'type'     => 'typography',
						'selector' => 'h1, [el] h2, [el] h3, [el] h4, [el] h5, [el] h6',
					),
					array(
						'id'       => 'ew',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] h1, [el] h2, [el] h3, [el] h4, [el] h5, [el] h6 {margin: [val]}',
					),
					array(
						'id'       => 'yu',
						'title'    => __( 'Headings Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] h1, [el] h2, [el] h3, [el] h4, [el] h5, [el] h6 {color: [val]}',
					),
					array(
						'type'        => 'heading',
						'title'       => __( 'Gutenberg Editor', 'pagex' ),
						'description' => __( 'If use Gutenberg editor for the post content you might want to adjust Wide and Full width elements. To make sure your content displays properly make the full width container element, where Post Content element is located.', 'pagex' ),
					),
					array(
						'id'         => 'aq',
						'title'      => __( 'Content Width', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .element-wrap > * {max-width: [val]; margin-left: auto; margin-right: auto;}',
					),
					array(
						'id'         => 'sw',
						'title'      => __( 'Wide Content Width', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .alignwide {max-width: [val] !important;}', // important to overwrite responsive Content Width option
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Post Content shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_post_content( $atts ) {
	$status = get_post_meta( get_the_ID(), '_pagex_status', true );

	$content = get_the_content();

	ob_start();

	if ( $status != 'true' ) {
		echo apply_filters( 'the_content', $content );
	} else {
		echo do_shortcode( $content );
	}

	wp_link_pages();

	return ob_get_clean();
}