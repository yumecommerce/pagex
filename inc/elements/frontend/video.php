<?php

/**
 * Video element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_video_element( $elements ) {
	$elements[] = array(
		'id'          => 'video',
		'category'    => 'media',
		'title'       => __( 'Video', 'pagex' ),
		'description' => __( 'Play YouTube or self hosted video files', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_video',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'      => 'source',
						'title'   => __( 'Source', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							'hosted'  => __( 'Self Hosted', 'pagex' ),
							'youtube' => 'YouTube',
							'dynamic' => __( 'Dynamic', 'pagex' ),
//							'vimeo' => 'Vimeo',
//							'dailymotion' => 'Dailymotion',
						),
					),
					array(
						'id'        => 'meta_key',
						'title'     => __( 'Meta Key', 'pagex' ),
						'type'      => 'select',
						'options'   => pagex_get_post_custom_keys( 'all' ),
						'condition' => array(
							'source' => array( 'dynamic' )
						)
					),
					array(
						'id'    => 'video_url',
						'title' => 'URL',
						'type'  => 'url',
						'condition' => array(
							'source' => array( 'hosted', 'youtube' )
						)
					),
					array(
						'id'          => 'start',
						'class'       => 'col-6',
						'title'       => __( 'Start Time', 'pagex' ),
						'description' => __( 'Specify a start time (in seconds).', 'pagex' ) . ' ' . __( 'Will not be applied with loop mode.', 'pagex' ),
						'type'        => 'number',
					),
					array(
						'id'          => 'end',
						'class'       => 'col-6',
						'title'       => __( 'End Time', 'pagex' ),
						'description' => __( 'Specify an end time (in seconds).', 'pagex' ) . ' ' . __( 'Will not be applied with loop mode.', 'pagex' ),
						'type'        => 'number',
					),
					array(
						'id'    => 'autoplay',
						'label' => __( 'Autoplay', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
						'value' => 'autoplay',
					),
					array(
						'id'    => 'muted',
						'label' => __( 'Mute', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
						'value' => 'muted',
					),
					array(
						'id'    => 'loop',
						'label' => __( 'Loop', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
						'value' => 'loop',
					),
					array(
						'id'    => 'controls',
						'label' => __( 'Player Controls', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
						'value' => 'controls',
					),
					array(
						'id'    => 'showinfo',
						'label' => __( 'Video Info', 'pagex' ),
						'type'  => 'checkbox',
						'class' => 'col-auto',
						'value' => '1',
					),
					array(
						'id'    => 'overlay',
						'label' => __( 'Use Overlay', 'pagex' ),
						'type'  => 'checkbox',
					),
					array(
						'type'      => 'row-start',
						'condition' => array(
							'overlay' => 'true',
						),
					),
					array(
						'id'       => 'background',
						'title'    => __( 'Image Overlay', 'pagex' ),
						'type'     => 'image',
						'action'   => 'css',
						'selector' => '[el] .pagex-video-overlay {background-image: url([val])}',
					),
					array(
						'id'       => 'icon',
						'label'    => __( 'Icon', 'pagex' ),
						'type'     => 'icon',
						'selector' => '.pagex-video-overlay-button',
					),
					array(
						'id'        => 'qw',
						'title'     => __( 'Icon Box Shadow', 'pagex' ),
						'type'      => 'text',
						'action'    => 'css',
						'class'     => 'col-4',
						'selector'  => '[el] .pagex-video-overlay-button {box-shadow: [val]}',
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg', 'image' )
						)
					),
					array(
						'id'        => 'we',
						'title'     => __( 'Box Shadow on Hover', 'pagex' ),
						'type'      => 'text',
						'action'    => 'css',
						'class'     => 'col-4',
						'selector'  => '[el] .pagex-video-overlay-button:hover {box-shadow: [val]}',
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg', 'image' )
						)
					),
					array(
						'id'        => 'er',
						'title'     => __( 'Icon Border Radius', 'pagex' ),
						'type'      => 'text',
						'action'    => 'css',
						'class'     => 'col-4',
						'selector'  => '[el] .pagex-video-overlay-button {border-radius: [val]}',
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg', 'image' )
						)
					),
					array(
						'id'        => 'rt',
						'title'     => __( 'Color', 'pagex' ),
						'type'      => 'color',
						'action'    => 'css',
						'class'     => 'col-4',
						'selector'  => '[el] .pagex-video-overlay-button {color: [val]}',
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg' )
						)
					),
					array(
						'id'        => 'ty',
						'title'     => __( 'Color on Hover', 'pagex' ),
						'type'      => 'color',
						'action'    => 'css',
						'class'     => 'col-8',
						'selector'  => '[el] .pagex-video-overlay-button:hover {color: [val]}',
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg' )
						)
					),
					array(
						'id'        => 'ui',
						'title'     => __( 'Background', 'pagex' ),
						'type'      => 'color',
						'action'    => 'css',
						'class'     => 'col-4',
						'selector'  => '[el] .pagex-video-overlay-button {background: [val]}',
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg', 'image' )
						)
					),
					array(
						'id'        => 'io',
						'title'     => __( 'Background on Hover', 'pagex' ),
						'type'      => 'color',
						'action'    => 'css',
						'class'     => 'col-8',
						'selector'  => '[el] .pagex-video-overlay-button:hover {background: [val]}',
						'condition' => array(
							'icon' => array( 'font-awesome', 'svg', 'image' )
						)
					),
					array(
						'type' => 'row-end',
					)
				),
			),
		),
	);

	return $elements;
}

/**
 * Video shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_video( $atts ) {

	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'source'    => 'hosted',
		'video_url' => '',
		'start'     => '',
		'end'       => '',
		'overlay'   => false,
		'autoplay'  => '',
		'showinfo'  => '',
		'muted'     => '',
		'loop'      => '',
		'controls'  => '',
		'meta_key'  => '',
	) );

	if ( ! $data['video_url'] ) {
		return '';
	}

	$file_src = '';
	$video_id = '';

	$video = '';

	if ( $data['source'] == 'hosted' && $data['video_url'] ) {
		$file_src = trim( $data['video_url'] );
	} elseif ( $data['source'] == 'youtube' && $data['video_url'] ) {
		preg_match( '/^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user)\/))([^?&"\'>]+)/', $data['video_url'], $videoID );

		if ( isset( $videoID[1] ) ) {
			$video_id = $videoID[1];
		}
	} elseif ( $data['source'] == 'dynamic' && $data['meta_key'] ) {
		$meta = pagex_get_custom_meta_value( $data['meta_key'] );

		if ( $meta ) {
			if ( is_array( $meta ) && isset( $meta['guid'] ) ) {
				// if media file
				$file_src = $meta['guid'];
			} elseif ( is_string( $meta ) ) {
				// if youtube text link
				preg_match( '/^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user)\/))([^?&"\'>]+)/', $meta, $videoID );

				if ( isset( $videoID[1] ) ) {
					$video_id = $videoID[1];
				}
			}
		}
	}

	if ( $file_src ) {
		$src = $file_src;
		$src .= $data['start'] ? '#t=' . $data['start'] : '';
		$src .= $data['end'] ? ',' . $data['end'] : '';

		$attributes = implode( ' ', array( $data['autoplay'], $data['muted'], $data['loop'], $data['controls'] ) );

		$video .= '<video class="position-absolute" src="' . $src . '" ' . $attributes . ' controlslist="nodownload"></video>';
	} elseif ( $video_id ) {
		$params = array(
			'autoplay'       => $data['autoplay'] ? 1 : 0,
			'start'          => $data['start'] ? $data['start'] : null,
			'end'            => $data['end'] ? $data['end'] : null,
			'loop'           => $data['loop'] ? 1 : 0,
			'controls'       => $data['controls'] ? 1 : 0,
			'mute'           => $data['muted'] ? 1 : 0,
			'showinfo'       => $data['showinfo'] ? 1 : 0,
			'rel'            => 0,
			'modestbranding' => 0,
		);

		if ( $params['loop'] ) {
			$params['playlist'] = $video_id;
		}

		$src = 'https://www.youtube-nocookie.com/embed/' . $video_id . '?feature=oembed&wmode=opaque&' . http_build_query( $params );

		if ( Pagex::is_frontend_builder_frame_active() ) {
			$video .= '<iframe class="pagex-video-iframe pagex-iframe-lazy position-absolute" allow="autoplay; encrypted-media" allowfullscreen data-lazy-load="' . $src . '" src="' . $src . '"></iframe>';
		} else {
			$video .= '<iframe class="pagex-video-iframe pagex-iframe-lazy position-absolute" allow="autoplay; encrypted-media" allowfullscreen data-lazy-load="' . $src . '"></iframe>';
		}
	}

	if ( $video ) {
		$html = ' <div class="pagex-video"><div class="pagex-video-video-wrapper aspect-ratio-16-9 position-relative">';
		if ( $data['overlay'] ) {
			$icon = pagex_generate_icon( 'icon', $data );
			$html .= ' <div class="pagex-video-overlay d-flex background-cover position-absolute d-flex align-items-center justify-content-center trn-300"><div class="pagex-video-overlay-button cursor-pointer pointer-events-none-holder trn-300"> ' . $icon . '</div></div> ';
		}
		$html .= $video;
		$html .= '</div></div>';

		return $html;
	} else {
		return '';
	}
}