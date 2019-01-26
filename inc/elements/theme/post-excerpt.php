<?php

/**
 * Post Excerpt element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_post_excerpt_element( $elements ) {
	$elements[] = array(
		'id'          => 'post_excerpt',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp', 'pagex_excerpt_tmp' ),
		'title'       => __( 'Post Excerpt', 'pagex' ),
		'description' => __( 'Display the excerpt of the post', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_post_excerpt',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'       => 'typography',
						'type'     => 'typography',
						'selector' => '.pagex-post-excerpt',
					),
					array(
						'id'      => 'tag',
						'title'   => 'HTML ' . __( 'Tag', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							'p',
							'h1',
							'h2',
							'h3',
							'h4',
							'h5',
							'h6',
							'div'
						),
					),
					array(
						'id'       => 'color',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-excerpt {color: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Post Excerpt shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_post_excerpt( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );
	$data = wp_parse_args( $data, array(
		'tag' => 'p',
	) );

	$status  = get_post_meta( get_the_ID(), '_pagex_status', true );
	$content = get_the_excerpt();

	ob_start();

	if ( $status != 'true' ) {
		echo '<' . $data['tag'] . ' class="pagex-post-excerpt">' . $content . '</' . $data['tag'] . '>';
	} else {
		echo '<div class="pagex-post-excerpt">' . apply_filters( 'pagex_content', do_shortcode( $content ) ) . '</div>';
	}

	return ob_get_clean();
}