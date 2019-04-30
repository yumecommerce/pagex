<?php

/**
 * Image Gallery element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_image_gallery_element( $elements ) {
	$elements[] = array(
		'id'          => 'image_gallery',
		'category'    => 'media',
		'title'       => __( 'Image Gallery', 'pagex' ),
		'description' => __( 'Image gallery based on custom post meta', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_image_gallery',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'type',
						'title'   => __( 'Type', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'custom'  => __( 'Custom', 'pagex' ),
							'dynamic' => __( 'Dynamic', 'pagex' ),
						),
					),
					array(
						'id'        => 'images',
						'title'     => __( 'Items', 'pagex' ),
						'type'      => 'repeater',
						'condition' => array(
							'type' => array( 'custom' )
						),
						'params'    => array(
							array(
								'id'    => 'image',
								'title' => __( 'Image', 'pagex' ),
								'type'  => 'image',
								'class' => 'col-4',
								'sizes' => false,
							),
						),
					),
					array(
						'id'        => 'meta_key',
						'title'     => __( 'Post Meta Key', 'pagex' ),
						'type'      => 'select',
						'options'   => pagex_get_post_custom_keys( 'media' ),
						'condition' => array(
							'type' => array( 'dynamic' )
						),
					),
				),
			),
			array(
				'title'  => __( 'Layout', 'pagex' ),
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
						'selector'   => '[el] [data-columns]:before {content: "[val] .pagex-masonry-column.pagex-masonry-size-[val]"} [el] .pagex-posts-grid-layout .pagex-posts-item-wrapper, [el] [data-columns=""] .pagex-posts-item-wrapper {width: calc(100% / [val] - 0.1px)}', // -0.1px fix IE issue
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
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'id'      => 'size',
						'title'   => __( 'Image Size', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'full'      => __( 'Full', 'pagex' ),
							'large'     => __( 'Large', 'pagex' ),
							'medium'    => __( 'Medium', 'pagex' ),
							'thumbnail' => __( 'Thumbnail', 'pagex' ),
						),
					),
					array(
						'id'    => 'lightbox',
						'title' => __( 'Lightbox', 'pagex' ),
						'type'  => 'checkbox',
						'label' => __( 'Use lightbox slider', 'pagex' ),
					),
					array(
						'id'       => 'br',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-6',
						'selector' => '[el] img {border-radius: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Image Gallery shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_image_gallery( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'type'     => 'custom',
		'images'   => array(),
		'meta_key' => '',
		'size'     => 'full',
		'lightbox' => '',
		'layout'   => 'grid',
	) );

	global $post;

	if ( $data['type'] == 'custom' ) {
		$meta = array();
		foreach ( $data['images'] as $image ) {
			$image_id     = attachment_url_to_postid( $image['image'] );
			$meta[] = $image_id;
		}
	} else {
		$meta = pagex_get_custom_meta_value( $data['meta_key'], false );
	}

	if ( ! $meta ) {
		return '';
	}

	$container_class    = $data['layout'] == 'pagex_slider' ? 'pagex-posts swiper-container' : 'pagex-posts';
	$wrapper_class      = $data['layout'] == 'pagex_slider' ? 'pagex-posts-wrapper swiper-wrapper' : 'pagex-posts-wrapper';
	$item_wrapper_class = $data['layout'] == 'pagex_slider' ? 'pagex-posts-item-wrapper swiper-slide' : 'pagex-posts-item-wrapper';
	$item_data          = $data['layout'] == 'masonry' ? 'data-columns' : '';
	$item_class         = $data['lightbox'] ? 'pagex-posts-item pagex-gallery-item pagex-modal-trigger' : 'pagex-posts-item';

	ob_start();

	$lightbox_gallery = array();

	echo '<div class="pagex-dynamic-image-gallery ' . $container_class . ' pagex-posts-' . $data['layout'] . '-layout">';
	echo '<div class="' . $wrapper_class . '" ' . $item_data . '>';
	foreach ( $meta as $k => $image ) {
		echo '<div class="' . $item_wrapper_class . '">';
		echo '<div class="' . $item_class . '" data-gallery-item="' . intval( $k + 1 ) . '">';
		echo wp_get_attachment_image( $image, $data['size'] );
		if ( $data['lightbox'] ) {
			$lightbox_gallery[] = wp_get_attachment_image( $image, 'full' );
		}
		echo '</div>';
		echo '</div>';
	}
	echo '</div>';

	if ( $data['layout'] == 'pagex_slider' ) {
		$type = isset( $data['slider_nav_type'] ) ? 'long-arrow' : 'arrow';
		echo '<div class="swiper-pagination pagex-slider-pagination"></div><div class="swiper-button-prev pagex-slider-navigation"><svg class="pagex-icon"><use xlink:href="#pagex-' . $type . '-left-icon" /></svg></div><div class="swiper-button-next pagex-slider-navigation"><svg class="pagex-icon"><use xlink:href="#pagex-' . $type . '-right-icon" /></svg></div>';
	}

	echo '</div>';

	if ( $lightbox_gallery ) {

		$lightbox_slider = '<div class="swiper-container pagex-no-event pagex-gallery-slider">';
		$lightbox_slider .= '<div class="swiper-wrapper">';
		foreach ( $lightbox_gallery as $image ) {
			$lightbox_slider .= '<div class="swiper-slide pagex-no-event">' . $image . '</div>';
		}
		$lightbox_slider .= '</div>';
		$lightbox_slider .= '<div class="swiper-button-prev pagex-slider-navigation"><svg class="pagex-icon"><use xlink:href="#pagex-arrow-left-icon" /></svg></div><div class="swiper-button-next pagex-slider-navigation"><svg class="pagex-icon"><use xlink:href="#pagex-arrow-right-icon" /></svg></div>';
		$lightbox_slider .= '</div>';

		pagex_modal_window_template( array(
			'content' => $lightbox_slider,
			'class'   => 'pagex-image-lightbox-window'
		) );
	}

	return ob_get_clean();
}