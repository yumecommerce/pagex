<?php

/**
 * Archive Data element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_archive_data_element( $elements ) {
	$elements[] = array(
		'id'          => 'archive_data',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Archive Data', 'pagex' ),
		'description' => __( 'Display archive (taxonomy) title or description', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_archive_data',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'type',
						'title'   => __( 'Type', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'title'       => __( 'Title', 'pagex' ),
							'description' => __( 'Description', 'pagex' ),
						)
					),
					array(
						'id'       => 'typo',
						'type'     => 'typography',
						'selector' => '.pagex-archive-data',
					),
					array(
						'id'       => 'cl',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-6',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] {color: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Archive Data shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_archive_data( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'type' => 'title',
	) );

	ob_start();

	if ( $data['type'] == 'title' ) {
		the_archive_title( '<h1 class="pagex-archive-title m-0">', '</h1>' );
	} else {
		the_archive_description( '<div class="pagex-taxonomy-description">', '</div>' );
	}

	$html = ob_get_clean();

	if ( $html ) {
		$html = '<div class="pagex-archive-data">' . $html . '</div>';
	}

	return $html;
}