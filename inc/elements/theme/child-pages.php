<?php

/**
 * Child Pagex element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_child_pages_element( $elements ) {
	$elements[] = array(
		'id'          => 'child_pages',
		'category'    => 'content',
		'title'       => __( 'Child Pages', 'pagex' ),
		'description' => __( 'Show list to child and relative pages', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_child_pages',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'display',
						'title'   => __( 'Display', 'pagex' ),
						'class'   => 'col-6',
						'type'    => 'select',
						'options' => array(
							''           => __( 'Unordered List', 'pagex' ),
							'ordered'    => __( 'Ordered List', 'pagex' ),
							'list'       => __( 'Vertical List', 'pagex' ),
							'horizontal' => __( 'Horizontal List', 'pagex' ),
						),
					),
					array(
						'id'      => 'cond',
						'title'   => __( 'Condition', 'pagex' ),
						'class'   => 'col-6',
						'type'    => 'select',
						'options' => array(
							''       => __( 'For parent and child pages', 'pagex' ),
							'parent' => __( 'For parent pages only', 'pagex' ),
							'child'  => __( 'For child pages only', 'pagex' ),
						),
					),
					array(
						'id'       => 'typo',
						'type'     => 'typography',
						'selector' => '',
					),
					array(
						'id'       => 'qw',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a {color: [val]}',
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Color on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a:hover {color: [val]}',
					),
					array(
						'id'       => 'ew',
						'title'    => __( 'Current Item Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-child-page-current a {color: [val]}',
					),
					array(
						'id'         => 'er',
						'title'      => __( 'Opacity', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'selector'   => '[el] a {opacity: [val]}',
					),
					array(
						'id'         => 'rt',
						'title'      => __( 'Opacity on Hover', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'selector'   => '[el] a:hover {opacity: [val]}',
					),
					array(
						'type' => 'clear',
					),
					array(
						'id'        => 'separator',
						'title'     => __( 'Separator', 'pagex' ),
						'type'      => 'text',
						'class'     => 'col-4',
						'condition' => array(
							'display' => array( 'horizontal' )
						),
					),
					array(
						'id'        => 'wq',
						'title'     => __( 'Color', 'pagex' ),
						'class'     => 'col-4',
						'type'      => 'color',
						'action'    => 'css',
						'selector'  => '[el] .pagex-child-pages-sep {color: [val]}',
						'condition' => array(
							'display' => array( 'horizontal' )
						),
					),
					array(
						'id'        => 're',
						'title'     => __( 'Margin', 'pagex' ),
						'class'     => 'col-4',
						'action'    => 'css',
						'type'      => 'text',
						'selector'  => '[el] .pagex-child-pages-sep {margin: [val]}',
						'condition' => array(
							'display' => array( 'horizontal' )
						),
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Child Pagex shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_child_pages( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'display'   => '',
		'separator' => '',
		'cond'      => '',
	) );

	global $post;

	if ( ! $post || ! is_singular() ) {
		return;
	}

	$html    = $separator = '';
	$post_id = $post->ID;

	if ( $data['display'] == 'horizontal' ) {
		$separator = '<li class="pagex-child-pages-sep">' . $data['separator'] . '</li>';
	}

	$args = array(
		'post_status'         => 'publish',
		'posts_per_page'      => - 1,
		'suppress_filters'    => false, // must be false to get right translation for multilingual
		'ignore_sticky_posts' => true,
		'no_found_rows'       => 1,
		'post_type'           => $post->post_type,
	);

	$parent_page = $post->post_parent;

	if ( $parent_page ) {
		if ( $data['cond'] == 'parent' ) {
			return $html;
		}

		$args['post_parent__in'] = array( $parent_page );
	} else {
		if ( $data['cond'] == 'child' ) {
			return $html;
		}

		// if is is not parent try to get children posts
		$args['post_parent'] = $post_id;
	}

	$posts = new WP_Query( $args );

	if ( $posts->have_posts() ) {
		$i = 1;
		while ( $posts->have_posts() ) {
			$posts->the_post();
			$id    = get_the_ID();
			$class = 'pagex-child-page-item';

			if ( $id == $post_id ) {
				$class .= ' pagex-child-page-current';
			}

			$html .= '<li class="' . $class . '"><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';

			if ( $separator && $posts->post_count != $i ) {
				$html .= $separator;
			}

			$i ++;
		}
	}
	wp_reset_postdata();

	if ( $html ) {
		switch ( $data['display'] ) {
			case 'ordered':
				$html = '<ol class="pagex-child-pages pagex-child-pages-ordered">' . $html . '</ol>';
				break;
			case 'list':
				$html = '<ul class="pagex-child-pages pagex-child-pages-list list-unstyled">' . $html . '</ul>';
				break;
			case 'horizontal':
				$html = '<ul class="pagex-child-pages pagex-child-pages-horizontal m-0 d-flex flex-wrap align-items-center list-unstyled">' . $html . '</ul>';
				break;
			default:
				$html = '<ul class="pagex-child-pages pagex-child-pages-unordered">' . $html . '</ul>';
				break;
		}
	}

	return $html;
}