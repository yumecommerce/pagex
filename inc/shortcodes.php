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

/**
 * Generate module for visually impaired
 *
 * @return string
 */
function pagex_visually_impaired_module() {
	$html = '<div class="pagex-visually-impaired-module d-inline-flex flex-wrap align-items-end">';
	if ( isset( $_COOKIE['pagex_visually_impaired'] ) ) {
		$html .= '<div class="mr-4"><div style="font-size: 16px !important;">' . __( 'Font Size', 'pagex' ) . '</div><div class="btn-group"><button type="button" style="font-size: 14px !important; background: #fff !important; color: #000 !important;" class="btn pagex-vi-fs-default" onclick="pagexAccessibility.visuallyImpaired.switch(0, 0)">A</button><button type="button" style="font-size: 20px !important; background: #fff !important; color: #000 !important;" class="btn pagex-vi-fs-big" onclick="pagexAccessibility.visuallyImpaired.switch(1, 0)">A</button><button type="button" style="font-size: 25px !important; background: #fff !important; color: #000 !important;" class="btn btn-secondary pagex-vi-fs-huge" onclick="pagexAccessibility.visuallyImpaired.switch(2, 0)">A</button></div></div>';

		$html .= '<div class="mr-4"><div style="font-size: 16px !important;">' . __( 'Website color', 'pagex' ) . '</div><div class="btn-group"><button type="button" style="background: #fff !important; color: #000 !important; font-size: 20px !important;" class="btn pagex-vi-cl-wb" onclick="pagexAccessibility.visuallyImpaired.switch(0, 1)">A</button><button type="button" style="background: #000 !important; color: #fff !important; font-size: 20px !important;" class="btn pagex-vi-cl-bw" onclick="pagexAccessibility.visuallyImpaired.switch(1, 1)">A</button><button type="button" style="background: #9dd1ff !important; color: #000 !important; font-size: 20px !important;" class="btn btn-secondary pagex-vi-cl-bb" onclick="pagexAccessibility.visuallyImpaired.switch(2, 1)">A</button></div></div>';

		$html .= '<div class="mr-4"><div style="font-size: 16px !important;">' . __( 'Images', 'pagex' ) . '</div><div class="btn-group"><button type="button" style="background: #fff !important; color: #000 !important; font-size: 13px !important;" class="btn pagex-vi-img-on" onclick="pagexAccessibility.visuallyImpaired.switch(0, 2)">' . __( 'On', 'pagex' ) . '</button><button type="button" style="background: #fff !important; color: #000 !important; font-size: 13px !important;" class="btn pagex-vi-img-off" onclick="pagexAccessibility.visuallyImpaired.switch(1, 2)">' . __( 'Off', 'pagex' ) . '</button></div></div>';

		$html .= '<div class="pagex-visually-impaired-module-off pointer-events-none-holder cursor-pointer d-flex align-items-center" onclick="pagexAccessibility.visuallyImpaired.init(0)"><button type="button" class="btn" style="background: #fff !important; color: #000 !important; font-size: 14px !important;">' . __( 'Standard web site version', 'pagex' ) . '</button></div>';
	} else {
		$html .= '<div class="pagex-visually-impaired-module-on pointer-events-none-holder cursor-pointer d-flex align-items-center" onclick="pagexAccessibility.visuallyImpaired.init(1)"><i class="pagex-visually-impaired-module-icon"></i>' . __( 'Version for visually impaired', 'pagex' ) . '</div>';
	}
	$html .= '</div>';

	return $html;
}

add_shortcode( 'pagex_visually_impaired_module', 'pagex_visually_impaired_module' );
