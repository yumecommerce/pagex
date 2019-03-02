<?php

/**
 * Heading element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_heading_element( $elements ) {
	$template = '<% if (data.link != "") { %><a class="pagex-static-link" <%= data.link %>> <% } %><<%= data.tag %> class="pagex-heading pagex-content-editable pagex-lang-str" contenteditable="true"><% if (data.content && data.content.length) { print(data.content) } else { print("' . __( 'Heading or small paragraph', 'pagex' ) . '")  } %></<%= data.tag %>><% if (data.link != "") { %></a><% } %>';

	$elements[] = array(
		'id'          => 'heading',
		'category'    => 'content',
		'title'       => __( 'Heading', 'pagex' ),
		'description' => __( 'Heading or small paragraph text', 'pagex' ),
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
						'selector'    => '.pagex-heading',
					),
					array(
						'id'      => 'tag',
						'title'   => 'HTML ' . __( 'Tag', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'p',
							'h1',
							'h2',
							'h3',
							'h4',
							'h5',
							'h6',
							'span',
							'pre',
							'blockquote',
							'small',
							'code',
							'em',
							'strong',
							'sup',
							'sub',
							'div'
						),
					),
					array(
						'id'       => 'font',
						'type'     => 'typography',
						'selector' => '.pagex-heading',
					),
					array(
						'id'       => 'color',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-heading {color: [val]}',
					),
					array(
						'id'       => 'color_hover',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-heading:hover {color: [val]}',
					),
					array(
						'id'    => 'link',
						'title' => __( 'Link', 'pagex' ),
						'type'  => 'link',
					),
				),
			),
		),
	);

	return $elements;
}