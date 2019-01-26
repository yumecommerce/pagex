<?php

/**
 * Instagram element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_instagram_element( $elements ) {
	$elements[] = array(
		'id'          => 'instagram',
		'category'    => 'media',
		'title'       => __( 'Instagram', 'pagex' ),
		'description' => __( 'Display showcase of Instagram photos', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_instagram',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'    => 'user',
						'title' => __( 'Username', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-4',
					),
					array(
						'id'      => 'size',
						'title'   => __( 'Photo Size', 'pagex' ),
						'type'    => 'select',
						'class'   => 'col-4',
						'options' => array(
							''     => __( 'Original', 'pagex' ),
							' 150' => __( 'Low', 'pagex' ),
							' 320' => __( 'Standard', 'pagex' ),
							' 640' => __( 'Big', 'pagex' ),
						),
					),
					array(
						'id'          => 'amount',
						'title'       => __( 'Number of Photos', 'pagex' ),
						'description' => __( 'Maximum is 12', 'pagex' ),
						'type'        => 'number',
						'attributes'  => 'min="1" max="12"',
						'class'       => 'col-4',
					),
					array(
						'id'      => 'cache',
						'title'   => __( 'Cache Timeout', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							''     => __( 'None', 'pagex' ),
							'hour' => __( 'Hour', 'pagex' ),
							'day'  => __( 'Day', 'pagex' ),
							'week' => __( 'Week', 'pagex' ),
						),
					),
					array(
						'id'    => 'link',
						'label' => __( 'Link Photos', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
					),
					array(
						'id'    => 'likes',
						'label' => __( 'Likes Count', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
					),
					array(
						'id'    => 'comments',
						'label' => __( 'Comments Count', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
					),

				),
			),
			array(
				'title'  => __( 'Layout', 'pagex' ),
				'params' => array(
					array(
						'id'         => 'qw',
						'title'      => __( 'Columns', 'pagex' ),
						'type'       => 'number',
						'responsive' => true,
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-instagram-item-wrapper {width: calc(100% / [val])}',
					),
					array(
						'id'         => 'we',
						'title'      => __( 'Gap', 'pagex' ),
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-instagram-item-wrapper {padding: [val]} [el] .pagex-instagram {margin: 0 -[val]}',
					),
					array(
						'id'         => 'er',
						'title'      => __( 'Border Radius', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'class'      => 'col-4',
						'selector'   => '[el] .pagex-instagram-item {border-radius: [val]}',
					),
					array(
						'id'       => 'rt',
						'title'    => __( 'Meta Color', 'pagex' ),
						'type'     => 'color',
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-instagram-item-meta {color: [val]}',
					),
					array(
						'id'       => 'ty',
						'title'    => __( 'Overlay Background', 'pagex' ),
						'type'     => 'background',
						'action'   => 'css',
						'class'    => 'col-8',
						'selector' => '[el] .pagex-instagram-item-meta {background: [val]}',
					),
				)
			)
		),
	);

	return $elements;
}

/**
 * Instagram shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_instagram( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'user'     => '',
		'size'     => '',
		'amount'   => '5',
		'cache'    => '',
		'link'     => false,
		'comments' => false,
		'likes'    => false,
	) );

	$data['user'] = trim($data['user']);

	if ( ! $data['user'] ) {
		return __( 'Please, enter Username.', 'pagex' );
	}

	$photos = get_transient( 'pagex_instagram_element_data_' . $data['user'] . '_' . $data['cache'] );

	if ( ! $photos ) {
		$url = 'https://www.instagram.com/' . $data['user'] . '/';

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			if ( is_super_admin() ) {
				return $response->get_error_message();
			} else {
				return '';
			}
		}

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			if ( is_super_admin() ) {
				return 'Error: ' . wp_remote_retrieve_response_code( $response );
			} else {
				return '';
			}
		}

		$response = wp_remote_retrieve_body( $response );

		$arr = explode( 'window._sharedData = ', $response );
		$arr = explode( ';</script>', $arr[1] );
		$obj = json_decode( $arr[0], true );

		$insta_data = $obj['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];

		if ( ! $insta_data ) {
			if ( is_super_admin() ) {
				return 'Error';
			} else {
				return '';
			}
		}

		switch ( $data['cache'] ) {
			case 'hour':
				$timeout = HOUR_IN_SECONDS;
				break;
			case 'day':
				$timeout = DAY_IN_SECONDS;
				break;
			case 'week':
				$timeout = WEEK_IN_SECONDS;
				break;
			default:
				$timeout = 0;
				break;
		}

		$photo_data = array();

		foreach ( $insta_data as $photo ) {
			$sizes = array();
			foreach ( $photo['node']['thumbnail_resources'] as $thumbnail ) {
				$sizes[ $thumbnail['config_width'] ] = $thumbnail['src'];
			}

			$photo_data[] = array(
				'shortcode' => $photo['node']['shortcode'],
				'comments'  => $photo['node']['edge_media_to_comment']['count'],
				'likes'     => $photo['node']['edge_liked_by']['count'],
				'original'  => $photo['node']['display_url'],
				'sizes'     => $sizes,
			);
		}

		set_transient( 'pagex_instagram_element_data_' . $data['user'] . '_' . $data['cache'], $photo_data, $timeout );

		$photos = $photo_data;

	}

	if ( ! $photos ) {
		return '';
	}

	$data['amount'] = intval( $data['amount'] );

	if ( $data['amount'] > 12 || $data['amount'] < 1 ) {
		$data['amount'] = 5;
	}

	$photos = array_slice( $photos, 0, $data['amount'] );

	$html = '<div class="pagex-instagram">';
	foreach ( $photos as $photo ) {
		$html .= '<div class="pagex-instagram-item-wrapper">';
		$html .= '<div class="pagex-instagram-item">';
		if ( $data['link'] ) {
			$html .= '<a href="https://www.instagram.com/p/'.$photo['shortcode'].'" target="_blank">';
		}
		$html .= '<div class="pagex-instagram-item-meta">';
		if ( $data['likes'] ) {
			$html .= '<div class="pagex-instagram-item-meta-likes">';
			$html .= '<svg class="pagex-icon"><use xlink:href="#pagex-heart-icon"></use></svg>';
			$html .= $photo['likes'];
			$html .= '</div>';
		}
		if ( $data['comments'] ) {
			$html .= '<div class="pagex-instagram-item-meta-comments">';
			$html .= '<svg class="pagex-icon"><use xlink:href="#pagex-speech-icon"></use></svg>';
			$html .= $photo['comments'];
			$html .= '</div>';
		}
		$html .= '</div>';

		$src  = ! $data['size'] ? $photo['original'] : $photo['sizes'][ trim( $data['size'] ) ];
		$html .= '<img src="' . $src . '" alt="Instagram Image">';
		$html .= '</div>';
		if ( $data['link'] ) {
			$html .= '</a>';
		}
		$html .= '</div>';
	}
	$html .= '</div>';


	return $html;
}