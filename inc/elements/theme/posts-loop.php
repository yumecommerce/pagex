<?php

/**
 * Posts Loop element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_posts_loop_element( $elements ) {
	$excerpts = pagex_get_excerpt_templates();
	$layouts  = pagex_get_layout_templates();

	$elements[] = array(
		'id'          => 'posts_loop',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Posts Loop', 'pagex' ),
		'description' => __( 'Display posts in a loop for blog and archive pages', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_posts_loop',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'          => 'template',
						'title'       => __( 'Excerpt Template', 'pagex' ),
						'description' => __( 'You can create and change templates via', 'pagex' ) . ' <a href="' . admin_url( 'edit.php?post_type=pagex_excerpt_tmp' ) . '" target="_blank">' . __( 'Excerpt Templates Builder', 'pagex' ) . '</a>',
						'type'        => 'select',
						'options'     => $excerpts
					),
					array(
						'id'          => 'search_template',
						'title'       => __( 'Search Excerpt Template', 'pagex' ),
						'description' => __( 'You can use a separate Excerpt Template for search results if you use the same Theme Template for Archive and Search Results page', 'pagex' ),
						'type'        => 'select',
						'class'       => 'col-6',
						'options'     => $excerpts
					),
					array(
						'id'          => 'nothing_found',
						'title'       => __( 'Search Results: Nothing Found', 'pagex' ),
						'description' => __( 'Select layout which will be displayed when nothing matched a search term. If nothing is selected the default message will be displayed.', 'pagex' ),
						'type'        => 'select',
						'class'       => 'col-6',
						'options'     => $layouts
					),
					array(
						'id'          => 'no_posts',
						'title'       => __( 'No posts to display', 'pagex' ),
						'description' => __( 'Select layout which will be displayed when no posts to show based on main query. If nothing is selected the default message will be displayed.', 'pagex' ),
						'type'        => 'select',
						'options'     => $layouts
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
						'options' => array(
							'grid'    => __( 'Grid', 'pagex' ),
							'masonry' => __( 'Masonry', 'pagex' ),
						),
					),
					array(
						'id'         => 'column',
						'title'      => __( 'Columns', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'select',
						'action'     => 'css',
						'selector'   => '[el] [data-columns]:before {content: "[val] .pagex-masonry-column.pagex-masonry-size-[val]"} [el] .pagex-posts-grid-layout .pagex-posts-item-wrapper, [el] [data-columns=""] .pagex-posts-item-wrapper {width: calc(100% / [val] - 0.1px)}',
						// 0.1px fix IE
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
				)
			),
			array(
				'title'  => __( 'Pagination', 'pagex' ),
				'params' => array(
					array(
						'id'      => 'pagination',
						'title'   => __( 'Pagination Type', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'default' => __( 'Default', 'pagex' ),
							'numbers' => __( 'Numbers', 'pagex' ),
						)
					),
					array(
						'id'       => 'typo',
						'type'     => 'typography',
						'selector' => '.pagex-posts-loop-navigation',
					),
					array(
						'id'       => 'to',
						'title'    => __( 'Top Offset', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation {margin-top: [val]}',
					),
					array(
						'id'       => 's_p',
						'title'    => __( 'Padding', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation a, [el] .pagex-posts-loop-navigation span {padding: [val]}',
					),
					array(
						'id'       => 's_m',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation a, [el] .pagex-posts-loop-navigation span {margin: [val]}',
					),
					array(
						'id'       => 's_br',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-3',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation a, [el] .pagex-posts-loop-navigation span {border-radius: [val]}',
					),
					array(
						'id'       => 'd_c',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation a {color: [val]}',
					),
					array(
						'id'       => 'd_ch',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation a:hover {color: [val]}',
					),
					array(
						'id'       => 'd_ca',
						'title'    => __( 'Active Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation .current {color: [val]}',
					),
					array(
						'id'       => 'd_bg',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation a {background: [val]}',
					),
					array(
						'id'       => 'd_bgh',
						'title'    => __( 'Background', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation a:hover {background: [val]}',
					),
					array(
						'id'       => 'd_bga',
						'title'    => __( 'Active Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'background',
						'action'   => 'css',
						'selector' => '[el] .pagex-posts-loop-navigation .current {background: [val]}',
					),
				)
			)
		),
	);

	return $elements;
}


/**
 * Remove screen_reader_text from WordPress posts navigation and pagination template
 *
 * @param $template
 * @param $class
 *
 * @return string
 */
function pagex_posts_navigation_template( $template, $class ) {
	return '<nav class="navigation %1$s" role="navigation"><div class="nav-links">%3$s</div></nav>';
}

add_filter( 'navigation_markup_template', 'pagex_posts_navigation_template', 10, 2 );


/**
 * Posts Loop shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_posts_loop( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'template'        => '',
		'search_template' => '',
		'layout'          => 'grid',
		'nothing_found'   => '',
		'no_posts'        => '',
		'pagination'      => 'default',
	) );

	if ( ! $data['template'] ) {
		return __( 'Template is not set.', 'pagex' );
	}

	// add excerpt id to admin menu for editing link
	add_filter( 'pagex_admin_menu_posts_loop_excerpt_id', function () use ( $data ) {
		return $data['template'];
	} );

	$excerpt   = Pagex::get_translation_id( $data['template'], 'pagex_excerpt_tmp' );
	$item_data = $data['layout'] == 'masonry' ? 'data-columns' : '';

	if ( is_search() && $data['search_template'] ) {
		$excerpt_content = get_post_field( 'post_content', Pagex::get_translation_id( $data['search_template'], 'pagex_excerpt_tmp' ) );
	} else {
		$excerpt_content = get_post_field( 'post_content', $excerpt );
	}

	// if layout does not exist (deleted for example)
	if ( ! $excerpt_content ) {
		return __( 'Template is not set.', 'pagex' );
	}

	ob_start();

	if ( have_posts() ) {
		echo '<div class="pagex-posts-' . $data['layout'] . '-layout">';
		echo '<div class="pagex-posts-wrapper" ' . $item_data . '>';
		while ( have_posts() ) {
			the_post();
			echo '<div class="pagex-posts-item-wrapper"><div class="pagex-posts-item">';
			echo pagex_generate_excerpt_template( do_shortcode( $excerpt_content ) );
			echo '</div></div>';
		}
		echo '</div>';
		echo '</div>';

		echo '<div class="pagex-posts-loop-navigation pagex-posts-loop-navigation-' . $data['pagination'] . '">';
		if ( $data['pagination'] == 'default' ) {
			echo get_the_posts_navigation();
		} else {
			echo get_the_posts_pagination();
		}
		echo '</div>';
	} else {
		if ( is_search() ) {
			if ( $data['nothing_found'] ) {
				$layout = Pagex::get_translation_id( $data['nothing_found'], 'pagex_excerpt_tmp' );
				echo do_shortcode( get_post_field( 'post_content', $layout ) );
			} else {
				_e( 'Sorry, but nothing matched your search term.', 'pagex' );
			}
		} else {
			if ( $data['no_posts'] ) {
				$layout = Pagex::get_translation_id( $data['no_posts'], 'pagex_excerpt_tmp' );
				echo do_shortcode( get_post_field( 'post_content', $layout ) );
			} else {
				_e( 'It seems we cannot find what you are looking for.', 'pagex' );
			}
		}
	}

	return ob_get_clean();
}