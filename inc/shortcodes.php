<?php

/**
 * When multilingual option is active it wraps each text sting of static elements with class .pagex-lang-str
 * with shortcode "[pagex_lang_str][/pagex_lang_str]" to make it translatable in WPML
 *
 * This shortcode will be added automatically to each element during saving or updating content
 *
 * @param $atts
 * @param null $content
 *
 * @return string
 */
function pagex_multilingual_shortcode_support( $atts = array(), $content = null ) {
	return $content;
}

add_shortcode( 'pagex_lang_str', 'pagex_multilingual_shortcode_support' );

/**
 * Displaying dynamic image as a background
 *
 * @return string
 */
function pagex_dynamic_background( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'key' => '',
	) );

	if ( ! $data['key'] ) {
		return '';
	}

	$meta = pagex_get_custom_meta_value( $data['key'] );

	if ( ! $meta ) {
		return '';
	}

	if ( is_numeric( $meta ) ) {
		$url = wp_get_attachment_url( $meta );
	} elseif ( is_array( $meta ) ) {
		$url = wp_get_attachment_url( $meta['ID'] );
	} else {
		$image_id = attachment_url_to_postid( $meta );

		if ( $image_id ) {
			$url = wp_get_attachment_url( $image_id );
		}
	}

	if ( ! $url ) {
		return '<div class="pagex-dynamic-image-bg"></div>';
	}

	return '<div class="pagex-dynamic-image-bg" style="background-image:url(' . $url . ')"></div>';
}

add_shortcode( 'pagex_dynamic_background', 'pagex_dynamic_background' );


/**
 * Generate href for dynamic link attribute
 *
 * @param $atts
 *
 * @return string
 */
function pagex_dynamic_link( $atts ) {
	switch ( $atts[0] ) {
		case 'post':
			$link = esc_url( get_permalink() );
			break;
		case 'day':
			$link = get_day_link( get_post_time( 'Y' ), get_post_time( 'm' ), get_post_time( 'j' ) );
			break;
		case 'author':
			$link = get_author_posts_url( get_the_author_meta( 'ID' ) );
			break;
		case 'comments':
			$link = get_comments_link();
			break;
		default:
			$link = pagex_get_custom_meta_value( $atts[0] );
			if ( ! $link ) {
				$link = '#';
			}
	}

	return $link;
}

add_shortcode( 'pagex_dynamic_link', 'pagex_dynamic_link' );
