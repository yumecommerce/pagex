<?php

/**
 * Document Title element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_document_title_element( $elements ) {
	$elements[] = array(
		'id'          => 'document_title',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Document Title', 'pagex' ),
		'description' => __( 'Display document title for the current page.', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_document_title',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'context',
						'title'       => __( 'Include Context', 'pagex' ),
						'type'        => 'checkbox',
						'label'       => __( 'Display context label for taxonomies', 'pagex' ),
						'description' => __( 'If checked the title will include category, tags, date and etc. label before taxonomy name. ', 'pagex' ),
					),
					array(
						'id'          => 'paged',
						'title'       => __( 'Include page number', 'pagex' ),
						'type'        => 'checkbox',
						'label'       => __( 'Display page number for archives', 'pagex' ),
						'description' => __( 'If checked the title will include page number for archives', 'pagex' ),
					),
					array(
						'id'       => 'typo',
						'type'     => 'typography',
						'selector' => '.pagex-document-title',
					),
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
						'id'       => 'color',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-document-title {color: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Document Title shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_document_title( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'tag'     => 'h1',
		'context' => false,
		'paged'   => false,
	) );

	$title = pagex_get_document_title( $data['context'], $data['paged'] );

	if ( $title ) {
		$title = '<' . $data['tag'] . ' class="pagex-document-title m-0">' . $title . '</' . $data['tag'] . '>';
	}

	return $title;
}