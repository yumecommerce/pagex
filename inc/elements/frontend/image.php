<?php

/**
 * Image element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_image_element( $elements ) {
	$elements[] = array(
		'id'          => 'image',
		'category'    => 'media',
		'title'       => __( 'Image', 'pagex' ),
		'description' => __( 'Display single, featured or custom field image', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_image',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'type',
						'title'   => __( 'Type', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							'single'     => __( 'Single Image', 'pagex' ),
							'meta_field' => __( 'Dynamic', 'pagex' ),
						),
					),
					array(
						'id'        => 'meta_key',
						'title'     => __( 'Post Meta Key', 'pagex' ),
						'type'      => 'select',
						'class'     => 'col-4',
						'options'   => pagex_get_dynamic_media_keys(),
						'condition' => array(
							'type' => array( 'meta_field' )
						),
					),
					array(
						'id'      => 'size',
						'title'   => __( 'Image Size', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							'full'      => __( 'Full', 'pagex' ),
							'large'     => __( 'Large', 'pagex' ),
							'medium'    => __( 'Medium', 'pagex' ),
							'thumbnail' => __( 'Thumbnail', 'pagex' ),
						),
					),
					array(
						'id'        => 'image',
						'title'     => __( 'Image', 'pagex' ),
						'type'      => 'image',
						'sizes'     => false,
						'condition' => array(
							'type' => array( 'single' )
						),
					),


					array(
						'id'    => 'link',
						'title' => __( 'Link', 'pagex' ),
						'type'  => 'link',
					),
					array(
						'type' => 'clear',
					),
					array(
						'id'          => 'fallback',
						'title'       => __( 'Fallback Image', 'pagex' ),
						'description' => __( 'Image which will be used if main type is not set.', 'pagex' ),
						'type'        => 'image',
						'class'       => 'col-4',
						'sizes'       => false,
						'condition'   => array(
							'type' => array( 'meta_field' )
						),
					),
				),
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'id'       => 'br',
						'title'    => __( 'Border Radius', 'pagex' ),
						'type'     => 'text',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-image, [el] img {border-radius: [val]}',
					),
					array(
						'id'       => 'al',
						'title'    => __( 'Align', 'pagex' ),
						'type'     => 'select',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-image, [el] .pagex-image-wrapper {justify-content: [val]}',
						'options'  => array(
							''         => __( 'Left', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Right', 'pagex' ),
						),
					),
					array(
						'id'      => 'aspect_ratio',
						'title'   => __( 'Aspect Ratio', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							''                  => __( 'None', 'pagex' ),
							'aspect-ratio-16-9' => '16:9',
							'aspect-ratio-4-3'  => '4:3',
							'aspect-ratio-3-2'  => '3:2',
							'aspect-ratio-2-1'  => '2:1',
							'aspect-ratio-1-1'  => '1:1',
							'custom'            => __( 'Custom', 'pagex' ),
						),
					),
					array(
						'id'         => 'xs',
						'title'      => __( 'Custom Aspect Ratio', 'pagex' ),
						'type'       => 'number',
						'responsive' => true,
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-image-wrapper {padding-top: [val]%}',
						'condition'  => array(
							'aspect_ratio' => array( 'custom' )
						)
					),
					array(
						'id'          => 'fw',
						'type'        => 'checkbox',
						'title'       => __( 'Full Width', 'pagex' ),
						'label'       => __( 'Make Image Full Width', 'pagex' ),
						'description' => __( 'For some image dimensions and aspect ratios you need to set full width option', 'pagex' ),
						'action'      => 'css',
						'value'       => '100%',
						'selector'    => '[el] img {width: [val] !important; height: auto !important;}',
					),
					array(
						'id'         => 'zx',
						'title'      => __( 'Opacity', 'pagex' ),
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '[el] img {opacity: [val]}',
					),
					array(
						'id'         => 'xc',
						'title'      => __( 'Opacity on Hover', 'pagex' ),
						'type'       => 'number',
						'attributes' => 'min="0" max="1" step="0.1"',
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '[el] img:hover {opacity: [val]}',
					),
				)
			)
		),
	);

	return $elements;
}


/**
 * Image shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_image( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'type'         => 'single',
		'image'        => '',
		'meta_key'     => '',
		'fallback'     => '',
		'size'         => 'full',
		'link'         => '',
		'aspect_ratio' => '',
	) );

	global $post;

	if ( ! $post && $data['type'] != 'single' ) {
		return '';
	}

	$image = '';

	if ( $data['type'] == 'single' ) {
		if ( $data['image'] ) {
			$image_id = attachment_url_to_postid( $data['image'] );
			if ( $image_id ) {
				$image = wp_get_attachment_image( $image_id, $data['size'] );
			}
		}
	} elseif ( $data['meta_key'] ) {
		$meta = pagex_get_custom_meta_value( $data['meta_key'] );

		if ( $meta ) {
			if ( is_numeric( $meta ) ) {
				$image = wp_get_attachment_image( $meta, $data['size'] );
			} elseif ( is_array( $meta ) ) {
				$image = wp_get_attachment_image( $meta['ID'], $data['size'] );
			} else {
				$image_id = attachment_url_to_postid( $meta );

				if ( $image_id ) {
					$image = wp_get_attachment_image( $image_id, $data['size'] );
				} else {
					// in case direct src like avatars
					$image = '<img src="' . $meta . '">';
				}
			}
		}
	}

	if ( ! $image && $data['type'] != 'single' && $data['fallback'] ) {
		$image_id = attachment_url_to_postid( $data['fallback'] );

		if ( $image_id ) {
			$image = wp_get_attachment_image( $image_id, $data['size'] );
		}
	}

	// if impossible to get attachment post setup image if URL is provided
	if ( ! $image && $data['image'] ) {
		$image = '<img src="' . $data['image'] . '" alt="">';
	}

	if ( $image && $data['aspect_ratio'] ) {
		if ( $data['aspect_ratio'] == 'custom' ) {
			$image = '<div class="pagex-image-wrapper pagex-image-ar">' . $image . '</div>';
		} else {
			$image = '<div class="pagex-image-wrapper pagex-image-ar ' . $data['aspect_ratio'] . '">' . $image . '</div>';
		}
	}

	if ( $image && $data['link'] ) {
		$image = '<a ' . $data['link'] . ' class="pagex-image-link pagex-static-link">' . $image . '</a>';
	}

	if ( $image ) {
		$image = '<div class="pagex-image">' . $image . '</div>';
	}


	return $image;
}