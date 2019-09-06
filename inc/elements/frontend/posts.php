<?php

/**
 * Posts element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_posts_element( $elements ) {

	$excerpts = pagex_get_excerpt_templates();

	$_post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );
	unset( $_post_types['page'] );

	$post_types = array();

	foreach ( $_post_types as $post_type => $object ) {
		$post_types[ $post_type ] = $object->label;
	}

	$post_types['related'] = __( 'Related Posts', 'pagex' );

	$post_types['by_id'] = __( 'Custom List', 'pagex' );

	$post_params = array(
		array(
			'id'      => 'post_type',
			'title'   => __( 'Type', 'pagex' ),
			'type'    => 'select',
			'class'   => 'col-12',
			'options' => $post_types
		),
	);

	$post_params[] = array(
		'id'          => 'template',
		'title'       => __( 'Excerpt Template', 'pagex' ),
		'description' => __( 'You can create and change templates via', 'pagex' ) . ' <a href="' . admin_url( 'edit.php?post_type=pagex_excerpt_tmp' ) . '" target="_blank">' . __( 'Excerpt Templates Builder', 'pagex' ) . '</a>',
		'type'        => 'select',
		'options'     => $excerpts
	);

	if ( function_exists( 'wc' ) ) {
		$product_options = array(
			''                => __( 'Predefined WooCommerce Queries', 'pagex' ),
			'sale'            => __( 'Sale Products', 'pagex' ),
			'best_selling'    => __( 'Best Selling Products', 'pagex' ),
			'top_rated'       => __( 'Top Rated Products', 'pagex' ),
			'featured'        => __( 'Featured Products', 'pagex' ),
			'recently_viewed' => __( 'Recently Viewed Products', 'pagex' ),
			'cross_sell'      => __( 'Cross Sell Products', 'pagex' ),
		);

		if ( is_singular( 'pagex_post_tmp' ) ) {
			$product_options['upsells'] = __( 'Up-Sells Products', 'pagex' );
		}

		$post_params[] = array(
			'id'        => 'woo_products',
			'title'     => 'WooCommerce',
			'type'      => 'select',
			'options'   => $product_options,
			'condition' => array(
				'post_type' => 'product',
			),
		);
	}

	$_taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'objects' );

	// clear before and after taxonomy list
	$post_params[] = array(
		'type' => 'clear',
	);

	foreach ( $_taxonomies as $taxonomy => $object ) {
		$options = array();
		$terms   = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		) );

		if ( empty( $terms ) ) {
			continue;
		}

		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}

		$post_params[] = array(
			'id'        => 'taxonomy[][' . $object->name . '][]',
			'title'     => $object->label,
			'type'      => 'select',
			'class'     => 'col-6',
			'multiple'  => true,
			'options'   => $options,
			'condition' => array(
				'post_type' => $object->object_type,
			),
		);
	}

	$post_params[] = array(
		'type' => 'clear',
	);

	$post_params[] = array(
		'id'          => 'list_by_id',
		'title'       => __( 'List of IDs', 'pagex' ),
		'description' => __( 'Separate post IDs by comma.', 'pagex' ),
		'type'        => 'text',
		'condition'   => array(
			'post_type' => array( 'by_id' ),
		),
	);

	$post_params[] = array(
		'id'      => 'order_by',
		'title'   => __( 'Order By', 'pagex' ),
		'class'   => 'col-3',
		'type'    => 'select',
		'options' => array(
			'post_date'  => __( 'Date', 'pagex' ),
			'post_title' => __( 'Title', 'pagex' ),
			'menu_order' => __( 'Menu Order', 'pagex' ),
			'rand'       => __( 'Random', 'pagex' ),
		),
	);

	$post_params[] = array(
		'id'      => 'order',
		'title'   => __( 'Order', 'pagex' ),
		'class'   => 'col-3',
		'type'    => 'select',
		'options' => array(
			'DESC' => __( 'DESC', 'pagex' ),
			'ASC'  => __( 'ASC', 'pagex' ),
		),
	);

	$post_params[] = array(
		'id'          => 'per_page',
		'title'       => __( 'Posts Number', 'pagex' ),
		'description' => __( 'Number of posts to load', 'pagex' ),
		'class'       => 'col-3',
		'type'        => 'number',
	);

	$post_params[] = array(
		'id'          => 'offset',
		'title'       => __( 'Offset', 'pagex' ),
		'description' => __( 'Number of post to pass over', 'pagex' ),
		'class'       => 'col-3',
		'type'        => 'number',
	);

	$elements[] = array(
		'id'          => 'posts',
		'category'    => 'content',
		'title'       => __( 'Posts', 'pagex' ),
		'description' => __( 'Display post grid based on selected query', 'pagex' ),
		'type'        => 'dynamic',
		'info'        => 'https://github.com/yumecommerce/pagex/wiki/Posts',
		'callback'    => 'pagex_posts',
		'options'     => array(
			array(
				'params' => $post_params,
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'id'      => 'layout',
						'title'   => __( 'Layout', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'grid'         => __( 'Grid', 'pagex' ),
							'masonry'      => __( 'Masonry', 'pagex' ),
							'pagex_slider' => __( 'Slider', 'pagex' ),
						),
					),
					array(
						'type'      => 'row-start',
						'condition' => array(
							'layout' => array( 'pagex_slider' ),
						),
					),
					array(
						'type' => 'slider',
					),
					array(
						'type' => 'row-end',
					),
					array(
						'type'      => 'row-start',
						'condition' => array(
							'layout' => array( 'masonry', 'grid' ),
						),
					),
					array(
						'id'         => 'column',
						'title'      => __( 'Columns', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'select',
						'action'     => 'css',
						'selector'   => '[el] [data-columns]:before {content: "[val] .pagex-masonry-column.pagex-masonry-size-[val]"} [el] .pagex-posts-grid-layout .pagex-posts-item-wrapper, [el] [data-columns=""] .pagex-posts-item-wrapper {width: calc(100% / [val] - 0.1px)}',
						// -0.1px to fix IE issue
						'responsive' => true,
						'options'    => array(
							// we need space before value so browsers do not change options order
							''   => __( 'Inherit', 'pagex' ),
							' 1' => 1,
							' 2' => 2,
							' 3' => 3,
							' 4' => 4,
							' 5' => 5,
							' 6' => 6,
						),
					),
					array(
						'id'         => 'gap_c',
						'title'      => __( 'Columns Gap', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-posts-item {padding-left: [val]; padding-right: [val]} [el] .pagex-posts-wrapper {margin-left: -[val]; margin-right: -[val]}',
					),
					array(
						'id'         => 'gap_r',
						'title'      => __( 'Row Gap', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-posts-item {margin-bottom: [val]}',
					),
					array(
						'type' => 'row-end',
					),
				)
			),
			array(
				'title'  => __( 'Title', 'pagex' ),
				'params' => array(
					array(
						'id'    => 'title',
						'title' => __( 'Title', 'pagex' ),
						'type'  => 'text',
					),
					array(
						'id'       => 't_t',
						'type'     => 'typography',
						'selector' => '.pagex-posts-title',
					),
					array(
						'id'       => 't_c',
						'title'    => __( 'Color', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-title {color: [val]}',
						'class'    => 'col-4',
						'type'     => 'color',
					),
					array(
						'id'         => 't_m',
						'title'      => __( 'Margin', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-posts-title {margin: [val]}',
					),
				),
			)
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
function pagex_posts( $atts ) {
	$atts = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $atts, array(
		'layout'       => 'grid',
		'post_type'    => 'post',
		'title'        => '',
		'template'     => '',
		'list_by_id'   => '',
		'order_by'     => '',
		'order'        => 'DESC',
		'offset'       => '0',
		'per_page'     => '5',
		'woo_products' => '',
	) );

	if ( ! $data['template'] ) {
		return __( 'Template is not set.', 'pagex' );
	}

	global $post;

	if ( ! $post ) {
		return '';
	}

	if ( is_singular( 'page' ) && $data['post_type'] == 'related' ) {
		return '';
	}

	$excerpt = Pagex::get_translation_id( $data['template'], 'pagex_excerpt_tmp' );

	$args = array(
		'post_status'         => 'publish',
		'suppress_filters'    => false, // must be false to get right translation for multilingual
		'ignore_sticky_posts' => true,
		'no_found_rows'       => 1,
	);

	if ( $data['post_type'] == 'by_id' ) {
		if ( ! $data['list_by_id'] ) {
			return __( 'List of IDs is empty.', 'pagex' );
		}
		$args['post_type'] = 'any';
		$args['post__in']  = wp_parse_id_list( $data['list_by_id'] );
	} elseif ( $data['post_type'] == 'related' ) {
		$args['post_type'] = $post->post_type;
		$post_taxonomies   = get_post_taxonomies( $post->ID );
		$tax_query         = array();
		if ( $post_taxonomies ) {
			// check if post has assign terms to any of its taxonomy
			foreach ( $post_taxonomies as $k => $taxonomy ) {
				$terms = get_the_terms( $post->ID, $taxonomy );
				if ( $terms ) {
					foreach ( $terms as $key => $tag ) {
						$tax_query[] = array(
							'taxonomy' => $tag->taxonomy,
							'field'    => 'term_id',
							'terms'    => $tag->term_id
						);
					}
				}
			}
		}

		// if post has some assign taxonomy add condition
		if ( $tax_query ) {
			$tax_query['relation'] = 'OR';
			$args['tax_query']     = $tax_query;
		}
	} else {
		$args['post_type'] = $data['post_type'];

		if ( isset( $data['taxonomy'] ) ) {
			$args['tax_query'] = array();
			foreach ( $data['taxonomy'][0] as $taxonomy => $terms ) {
				$args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $terms
				);
			}
		}

		if ( $data['post_type'] == 'product' && $data['woo_products'] ) {
			switch ( $data['woo_products'] ) {
				case 'sale' :
					$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );

					break;
				case 'best_selling' :
					$args['meta_key'] = 'total_sales';
					$args['order']    = 'DESC';
					$args['orderby']  = 'meta_value_num';

					break;
				case 'top_rated' :
					$args['meta_key'] = '_wc_average_rating';
					$args['order']    = 'DESC';
					$args['orderby']  = 'meta_value_num';

					break;
				case 'featured' :
					$product_visibility_term_ids = wc_get_product_visibility_term_ids();

					$args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['featured'],
					);

					break;
				case 'recently_viewed' :
					$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array();
					$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

					if ( empty( $viewed_products ) ) {
						return;
					}

					$args['post__in'] = $viewed_products;
					$args['orderby']  = 'post__in';

					break;
				case 'cross_sell' :
					$cross_sells = WC()->cart->get_cross_sells();

					if ( empty( $cross_sells ) ) {
						return;
					}

					$args['post_type'] = array( 'product', 'product_variation' );
					$args['post__in']  = $cross_sells;

					break;
				case 'upsells' :
					global $product;

					if ( ! $product || ! $upsell_ids = $product->get_upsell_ids() ) {
						return;
					}

					$args['post_type'] = array( 'product', 'product_variation' );
					$args['post__in']  = $upsell_ids;

					break;
			}
		}
	}

	if ( $data['post_type'] != 'by_id' ) {
		$args['offset']         = $data['offset'];
		$args['posts_per_page'] = $data['per_page'];
		$args['orderby']        = isset( $args['orderby'] ) ? $args['orderby'] : $data['order_by'];
		$args['order']          = isset( $args['order'] ) ? $args['order'] : $data['order'];
		$args['post__not_in']   = array( $post->ID );
	}

	$posts = new WP_Query( $args );

	ob_start();

	$container_class = $data['layout'] == 'pagex_slider' ? 'pagex-posts swiper-container' : 'pagex-posts';
	$wrapper_class   = $data['layout'] == 'pagex_slider' ? 'pagex-posts-wrapper swiper-wrapper' : 'pagex-posts-wrapper';
	$item_class      = $data['layout'] == 'pagex_slider' ? 'pagex-posts-item-wrapper swiper-slide' : 'pagex-posts-item-wrapper';
	$item_data       = $data['layout'] == 'masonry' ? 'data-columns' : '';

	if ( $posts->have_posts() ) {
		if ( $data['title'] ) {
			echo '<h2 class="pagex-posts-title">' . $data['title'] . '</h2>';
		}
		echo '<div class="' . $container_class . ' pagex-posts-' . $data['layout'] . '-layout">';
		echo '<div class="' . $wrapper_class . '" ' . $item_data . '>';
		while ( $posts->have_posts() ) : $posts->the_post();
			echo '<div class="' . $item_class . '">';
			echo '<div class="pagex-posts-item">';
			echo pagex_generate_excerpt_template( do_shortcode( get_post_field( 'post_content', $excerpt ) ) );
			echo '</div>';
			echo '</div>';
		endwhile;
		echo '</div>';
		echo '</div>';
		if ( $data['layout'] == 'pagex_slider' ) {
			echo '<div class="swiper-pagination pagex-slider-pagination"></div>';
		}
		if ( $data['layout'] == 'pagex_slider' ) {
			$type = isset( $data['slider_nav_type'] ) ? 'long-arrow' : 'arrow';
			echo '<div class="swiper-button-prev pagex-slider-navigation"><svg class="pagex-icon"><use xlink:href="#pagex-' . $type . '-left-icon" /></svg></div><div class="swiper-button-next pagex-slider-navigation"><svg class="pagex-icon"><use xlink:href="#pagex-' . $type . '-right-icon" /></svg></div>';
		}
	}
	wp_reset_postdata();

	return ob_get_clean();
}