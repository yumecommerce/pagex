<?php

/**
 * Heading element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_html_element( $elements ) {
	$template = '<<%= data.tag %> class="pagex-html" contenteditable="true"><% if (data.content && data.content.length) { print(data.content) } else { print("' . __( 'HTML Bloc', 'pagex' ) . '")  } %></<%= data.tag %>>';

	$elements[] = array(
		'id'          => 'html',
		'category'    => 'content',
		'title'       => __( 'HTML', 'pagex' ),
		'description' => __( 'HTML Bloc', 'pagex' ),
		'type'        => 'static',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'content',
						'title'       => __( 'Content', 'pagex' ),
						'type'        => 'textarea',
						'description' => __( 'HTML tags are allowed.', 'pagex' ),
						'action'      => 'content',
						'selector'    => '.pagex-html',
					),
					array(
						'id'      => 'tag',
						'title'   => 'HTML ' . __( 'Tag', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'div',
						),
					),
				),
			),
		),
	);

	return $elements;
}