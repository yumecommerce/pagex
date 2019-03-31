<?php

/**
 * Post Data element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_post_data_element( $elements ) {
	$elements[] = array(
		'id'          => 'post_data',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp', 'pagex_excerpt_tmp' ),
		'title'       => __( 'Post Data', 'pagex' ),
		'description' => __( 'Author, date, comments, terms, custom post meta.', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_post_data',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'     => 'items',
						'title'  => __( 'Items', 'pagex' ),
						'type'   => 'repeater',
						'params' => array(
							array(
								'id'      => 'type',
								'title'   => __( 'Type', 'pagex' ),
								'type'    => 'select',
								'class'   => 'col-6',
								'options' => array(
									'author'    => __( 'Author', 'pagex' ),
									'date'      => __( 'Date', 'pagex' ),
									'comments'  => __( 'Comments', 'pagex' ),
									'taxonomy'  => __( 'Taxonomy', 'pagex' ),
									'post_meta' => __( 'Custom Meta Field', 'pagex' ),
									'custom'    => __( 'Custom Text', 'pagex' ),
								),
							),
							array(
								'id'    => 'before',
								'title' => __( 'Prefix', 'pagex' ),
								'type'  => 'text',
								'class' => 'col-6 pagex-repeater-value',
							),
							array(
								'id'        => 'custom_text',
								'title'     => __( 'Custom Text', 'pagex' ),
								'type'      => 'text',
								'class'     => 'col-12',
								'condition' => array(
									'type' => array( 'custom' )
								),
							),
							array(
								'id'        => 'post_meta_key',
								'title'     => __( 'Post Meta Key', 'pagex' ),
								'type'      => 'select',
								'class'     => 'col-6',
								'options'   => pagex_get_post_custom_keys(),
								'condition' => array(
									'type' => array( 'post_meta' )
								),
							),
							array(
								'id'          => 'meta_fallback',
								'title'       => __( 'Fallback', 'pagex' ),
								'class'       => 'col-6',
								'description' => __( 'Text which will be used if meta field is not set.', 'pagex' ),
								'type'        => 'text',
								'condition'   => array(
									'type' => array( 'post_meta' )
								),
							),
							array(
								'id'        => 'taxonomy',
								'title'     => __( 'Taxonomy', 'pagex' ),
								'type'      => 'select',
								'class'     => 'col-6',
								'options'   => pagex_get_taxonomies(),
								'condition' => array(
									'type' => array( 'taxonomy' )
								),
							),
							array(
								'id'        => 'taxonomy_sep',
								'title'     => __( 'Separator', 'pagex' ),
								'type'      => 'text',
								'class'     => 'col-6',
								'condition' => array(
									'type' => array( 'taxonomy' )
								),
							),
							array(
								'id'        => 'comments_layout',
								'title'     => __( 'Comments Format', 'pagex' ),
								'type'      => 'select',
								'options'   => array(
									''       => __( 'Default', 'pagex' ),
									'number' => __( 'Number', 'pagex' ),
								),
								'condition' => array(
									'type' => array( 'comments' )
								),
							),
							array(
								'id'        => 'date_type',
								'title'     => __( 'Type of Date', 'pagex' ),
								'type'      => 'select',
								'class'     => 'col-6',
								'options'   => array(
									'published' => __( 'Published', 'pagex' ),
									'modified'  => __( 'Modified', 'pagex' ),
								),
								'condition' => array(
									'type' => array( 'date' )
								),
							),
							array(
								'id'          => 'date_format',
								'title'       => __( 'Date Format', 'pagex' ),
								'description' => '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">' . __( 'Documentation on date and time formatting', 'pagex' ) . '</a>',
								'class'       => 'col-6',
								'type'        => 'text',
								'condition'   => array(
									'type' => array( 'date' )
								),
							),
							array(
								'id'        => 'link',
								'title'     => __( 'Link', 'pagex' ),
								'type'      => 'link',
								'condition' => array(
									'type' => array( 'author', 'date', 'comments', 'post_meta', 'custom' )
								),
							),
							array(
								'id'    => 'icon',
								'title' => __( 'Icon', 'pagex' ),
								'type'  => 'icon',
							),
						),
					),
				),
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'id'      => 'layout',
						'title'   => __( 'Layout', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							''                         => __( 'Horizontal', 'pagex' ),
							'pagex-post-data-vertical' => __( 'Vertical', 'pagex' ),
						)
					),
					array(
						'id'         => 'qw',
						'title'      => __( 'Alignment', 'pagex' ),
						'type'       => 'select',
						'class'      => 'col-4',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-post-data {justify-content: [val]} [el] .pagex-post-data-vertical {align-items: [val]}',
						'options'    => array(
							''         => __( 'Left', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Right', 'pagex' ),
						)
					),
					array(
						'id'      => 'tag',
						'title'   => 'HTML ' . __( 'Tag', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							''    => __( 'None', 'pagex' ),
							'p'   => 'p',
							'h1'  => 'h1',
							'h2'  => 'h2',
							'h3'  => 'h3',
							'h4'  => 'h4',
							'h5'  => 'h5',
							'h6'  => 'h6',
							'div' => 'div'
						),
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Divider', 'pagex' ),
					),
					array(
						'id'       => 'we',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-data-divider {margin: [val]}',
					),
					array(
						'id'       => 'er',
						'title'    => __( 'Border Style', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-data-divider {border-style: [val]}',
						'options'  => array(
							''       => __( 'Solid', 'pagex' ),
							'double' => __( 'Double', 'pagex' ),
							'dotted' => __( 'Dotted', 'pagex' ),
							'dashed' => __( 'Dashed', 'Dashed' ),
						)
					),
					array(
						'id'          => 'rt',
						'title'       => __( 'Width', 'pagex' ),
						'description' => __( 'Width of divider for vertical layout.', 'pagex' ),
						'type'        => 'text',
						'action'      => 'css',
						'responsive'  => true,
						'class'       => 'col-4',
						'selector'    => '[el] .pagex-post-data-divider {width: [val]}',
					),
					array(
						'id'         => 'ty',
						'title'      => __( 'Border Width', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-post-data-divider {border-width: [val]}',
					),
					array(
						'id'       => 'yu',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-data-divider {border-color: [val]}',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Basic Style', 'pagex' ),
					),
					array(
						'id'       => 'basic_t',
						'type'     => 'typography',
						'selector' => '.pagex-post-data',
					),
					array(
						'id'       => 'ui',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-data {color: [val]}',
					),
					array(
						'id'       => 'io',
						'title'    => __( 'Links Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a {color: [val]}',
					),
					array(
						'id'       => 'op',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a:hover .pagex-post-data-value, [el] .pagex-post-data-value a:hover {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Icon', 'pagex' ),
					),
					array(
						'id'         => 'as',
						'title'      => __( 'Size', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-3',
						'selector'   => '[el] .pagex-post-data-icon .pagex-icon {width: [val]; height: [val]}',
					),
					array(
						'id'       => 'sd',
						'title'    => __( 'Position', 'pagex' ),
						'type'     => 'select',
						'action'   => 'css',
						'class'    => 'col-3',
						'selector' => '[el] .pagex-post-data-icon {order: [val]}',
						'options'  => array(
							''   => __( 'Left', 'pagex' ),
							' 2' => __( 'Right', 'pagex' ),
						)
					),
					array(
						'id'       => 'df',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-data-icon {margin: [val]}',
					),
					array(
						'id'       => 'fg',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-data-icon {color: [val]}',
					),
					array(
						'id'       => 'gh',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a:hover .pagex-post-data-icon {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Prefix', 'pagex' ),
					),
					array(
						'id'       => 'hj',
						'title'    => __( 'Margin', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-data-prefix {margin: [val]}',
					),
					array(
						'id'         => 'zq',
						'title'      => __( 'Width', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'selector'   => '[el] .pagex-post-data-prefix {width: [val]}',
					),
					array(
						'id'       => 'qw',
						'title'    => __( 'Alignment', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-4',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-data-prefix {align-self: [val]}',
						'options'  => array(
							''           => __( 'Center', 'pagex' ),
							'flex-start' => __( 'Top', 'pagex' ),
							'flex-end'   => __( 'Bottom', 'pagex' ),
						)
					),
					array(
						'id'       => 'prefix_t',
						'type'     => 'typography',
						'selector' => '.pagex-post-data-prefix',
					),
					array(
						'id'       => 'jk',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-post-data-prefix {color: [val]}',
					),
					array(
						'id'       => 'zx',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a:hover .pagex-post-data-prefix {color: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Post Data shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_post_data( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'layout' => '',
		'tag'    => '',
		'items'  => array()
	) );

	$html = '';

	if ( empty( $data['items'] ) ) {
		return $html;
	}

	foreach ( $data['items'] as $k => $v ) {
		$item = '';

		switch ( $v['type'] ) {
			case 'author':
				$item .= get_the_author_meta( 'display_name' );
				break;
			case 'date':
				$format = $v['date_format'] ? $v['date_format'] : 'F j, Y';
				if ( $v['date_type'] == 'published' ) {
					$item .= get_the_time( $format );
				} else {
					$item .= get_the_modified_date( $format );
				}
				break;
			case 'taxonomy':
				if ( $v['taxonomy'] && taxonomy_exists( $v['taxonomy'] ) ) {
					$terms = wp_get_post_terms( get_the_ID(), $v['taxonomy'] );

					if ( ! empty( $terms ) ) {
						$count = count( $terms );
						$sep   = $v['taxonomy_sep'] ? '<span class="pagex-post-data-taxonomy-sep">' . $v['taxonomy_sep'] . '</span>' : '';

						foreach ( $terms as $key => $term ) {

							$item .= '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
							if ( $sep && $key + 1 != $count ) {
								$item .= $sep;
							}
						}
					}
				}
				break;
			case 'comments':
				if ( comments_open() ) {
					$comments = get_comments_number();
					if ( $v['comments_layout'] == '' ) {
						$item .= sprintf( esc_html( _n( '1 Comment', '%d Comments', $comments, 'pagex' ) ), $comments );
					} else {
						$item .= $comments;
					}
				}
				break;
			case 'post_meta':
				$meta = isset( $v['post_meta_key'] ) ? pagex_get_custom_meta_value( $v['post_meta_key'] ) : '';

				if ( $meta ) {
					$item .= $meta;
				} elseif ( $v['meta_fallback'] ) {
					$item .= $v['meta_fallback'];
				}

				break;
			case 'custom':
				if ( $v['custom_text'] ) {
					$item .= $v['custom_text'];
				}
				break;
		}

		if ( $item && $data['tag'] ) {
			$item = '<' . $data['tag'] . ' class="pagex-post-data-wrapper-tag">' . $item . '</' . $data['tag'] . '>';
		}

		if ( $item ) {
			$item = '<div class="pagex-post-data-value">' . $item . '</div>';
			if ( $v['before'] ) {
				$v['before'] = $data['tag'] ? '<' . $data['tag'] . ' class="pagex-post-data-wrapper-tag">' . $v['before'] . '</' . $data['tag'] . '>' : $v['before'];
				$item        = '<div class="pagex-post-data-prefix">' . $v['before'] . '</div> ' . $item;
			}

			if ( $v['icon'] ) {
				$icon = pagex_generate_icon( 'icon', $v );
				if ( $icon ) {
					$item = '<div class="pagex-post-data-icon">' . $icon . '</div> ' . $item;
				}
			}

			if ( $v['link'] && $v['type'] != 'taxonomy' ) {
				$item = '<a ' . $v['link'] . ' class="pagex-post-data-link pagex-static-link">' . $item . '</a>';
			}

			$html .= '<div class="pagex-post-data-item pagex-post-data-' . $v['type'] . '-type">' . $item . '</div>';
			$html .= '<div class="pagex-post-data-divider"></div>';
		}
	}

	return '<div class="pagex-post-data ' . $data['layout'] . '">' . $html . '</div>';
}