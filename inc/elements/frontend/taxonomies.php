<?php

/**
 * Taxonomies element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_taxonomies_element( $elements ) {

	$_taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'objects' );
	$taxonomy    = array( '' => __( 'Select Taxonomy', 'pagex' ) );

	foreach ( $_taxonomies as $tax => $object ) {
		$taxonomy[ $tax ] = $object->label;
	}

	$elements[] = array(
		'id'          => 'taxonomies',
		'category'    => 'content',
		'title'       => __( 'Taxonomies', 'pagex' ),
		'description' => __( 'Display taxonomy list based on selected query', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_taxonomies',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'taxonomy',
						'title'       => __( 'taxonomy', 'pagex' ),
						'description' => __( 'Separate terms IDs by comma.', 'pagex' ),
						'type'        => 'select',
						'options'     => $taxonomy,
					),
					array(
						'id'          => 'include',
						'title'       => __( 'List of IDs to include', 'pagex' ),
						'description' => __( 'Separate terms IDs by comma.', 'pagex' ),
						'type'        => 'text',
						'class'       => 'col-6',
					),
					array(
						'id'          => 'exclude',
						'title'       => __( 'List of IDs to exclude', 'pagex' ),
						'description' => __( 'Separate terms IDs by comma.', 'pagex' ),
						'type'        => 'text',
						'class'       => 'col-6',
					),
					array(
						'id'      => 'orderby',
						'title'   => __( 'Order By', 'pagex' ),
						'class'   => 'col-3',
						'type'    => 'select',
						'options' => array(
							''        => __( 'Name', 'pagex' ),
							'id'      => 'ID',
							'count'   => __( 'Count', 'pagex' ),
							'include' => __( 'Include', 'pagex' ),
						)
					),
					array(
						'id'      => 'order',
						'title'   => __( 'Order', 'pagex' ),
						'class'   => 'col-3',
						'type'    => 'select',
						'options' => array(
							''     => __( 'ASC', 'pagex' ),
							'DESC' => __( 'DESC', 'pagex' ),
						)
					),
					array(
						'id'          => 'number',
						'title'       => __( 'Terms Number', 'pagex' ),
						'description' => __( 'Number of terms to load', 'pagex' ),
						'class'       => 'col-3',
						'type'        => 'number',
					),
					array(
						'id'          => 'offset',
						'title'       => __( 'Offset', 'pagex' ),
						'description' => __( 'Number of post to pass over', 'pagex' ),
						'class'       => 'col-3',
						'type'        => 'number',
					),
					array(
						'id'          => 'not_hide_empty',
						'type'        => 'checkbox',
						'title'       => __( 'Empty Terms', 'pagex' ),
						'label'       => __( 'Do not hide empty terms', 'pagex' ),
						'description' => __( 'By default terms with no posts will not be shown', 'pagex' ),
						'value'       => 'true',
					),
				),
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'id'       => 'qw',
						'type'     => 'typography',
						'selector' => 'a',
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Color', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] a {color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] a:hover {color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
				)
			),
		),
	);

	return $elements;
}

/**
 * Shortcode for posts element
 *
 * @param $atts
 *
 * @return string
 */
function pagex_taxonomies( $atts ) {
	$atts = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $atts, array(
		'taxonomy'       => '',
		'include'        => '',
		'exclude'        => '',
		'orderby'        => '',
		'order'          => '',
		'number'         => '',
		'offset'         => '',
		'not_hide_empty' => false,
	) );

	if ( ! $data['taxonomy'] ) {
		return __( 'Select Taxonomy', 'pagex' );
	}

	$data['hide_empty'] = $data['not_hide_empty'] ? false : true;

	$terms = get_terms( $data );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return;
	}

	ob_start();

	echo '<ul class="pagex-taxonomies">';
	foreach ( $terms as $term ) {
		echo '<li><a href="' . get_term_link( $term ) . '">' . $term->name . '</a></li>';
	}
	echo "</ul>";

	return ob_get_clean();
}
