<?php

/**
 * Standard elements like section, container, row, inner row and column
 * These elements except inner row have no category so they will not be displayed in elements select window
 */
foreach ( glob( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/elements/basic/*.php' ) as $filename ) {
	include $filename;
}

/**
 * Frontend elements
 */
foreach ( glob( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/elements/frontend/*.php' ) as $filename ) {
	include $filename;
}

/**
 * Elements for theme layouts
 */
foreach ( glob( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/elements/theme/*.php' ) as $filename ) {
	include $filename;
}

/**
 * Register all default categories for a builder using pagex_categories filter
 *
 * @param $categories - array with all registered categories
 *
 * @return array
 */
function pagex_register_categories( $categories ) {
	$categories['content']        = __( 'Content', 'pagex' );
	$categories['media']          = __( 'Media', 'pagex' );
	$categories['theme-elements'] = __( 'Theme Elements', 'pagex' );
	$categories['woocommerce']    = 'WooCommerce';
	$categories['woo-theme']      = __( 'Woo Theme Elements', 'pagex' );

	return $categories;
}

add_filter( 'pagex_categories', 'pagex_register_categories' );


/**
 * Register all bundled elements using pagex_elements filter
 */
foreach (
	array(
		'accordion',
		'archive_data',
		'breadcrumb',
		'button',
		'column',
		'container',
		'countdown',
		'child_pages',
		'document_title',
		'features',
		'form',
		'google_maps',
		'heading',
		'icon',
		'image',
		'image_gallery',
		'instagram',
		'layout',
		'list',
		'login_form',
		'nav_menu',
		'post_comments',
		'post_content',
		'post_navigation',
		'post_data',
		'post_excerpt',
		'post_title',
		'posts',
		'posts_loop',
		'row',
		'search_form',
		'section',
		'separator',
		'share_buttons',
		'shortcode',
		'sidebar',
		'slider',
		'tabs',
		'video',
	) as $element
) {
	add_filter( 'pagex_elements', 'pagex_register_' . $element . '_element' );
}


/**
 * Register all callback of dynamic elements
 *
 * @param $callbacks - array with a list of function names of dynamic elements
 *
 * @return array
 */
function pagex_register_bundled_dynamic_callbacks( $callbacks ) {
	$dynamic_callbacks = array(
		'pagex_archive_data',
		'pagex_breadcrumb',
		'pagex_child_pages',
		'pagex_document_title',
		'pagex_form',
		'pagex_google_maps',
		'pagex_image',
		'pagex_image_gallery',
		'pagex_instagram',
		'pagex_layout',
		'pagex_login_form',
		'pagex_nav_menu',
		'pagex_post_comments',
		'pagex_post_content',
		'pagex_post_navigation',
		'pagex_post_data',
		'pagex_post_excerpt',
		'pagex_post_title',
		'pagex_posts',
		'pagex_posts_loop',
		'pagex_search_form',
		'pagex_shortcode',
		'pagex_sidebar',
		'pagex_video',
	);

	foreach ( $dynamic_callbacks as $callback ) {
		$callbacks[] = $callback;
	}

	return $callbacks;
}

add_filter( 'pagex_elements_dynamic_callbacks', 'pagex_register_bundled_dynamic_callbacks' );


/**
 * Elements for WooCommerce
 */
if ( function_exists( 'wc' ) ) {
	foreach ( glob( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/elements/woocommerce/*.php' ) as $filename ) {
		include $filename;
	}

	// register WooCommerce elements
	foreach (
		array(
			'woo_add_to_cart',
			'woo_catalog_data',
			'woo_menu_cart',
			'woo_product_additional_information',
			'woo_product_data',
			'woo_product_data_tabs',
			'woo_product_images',
			'woo_product_meta',
			'woo_product_thumbnail',
			'woo_sale_flash',
			'woo_notices',
		) as $element
	) {
		add_filter( 'pagex_elements', 'pagex_register_' . $element . '_element' );
	}

	// callback of WooCommerce dynamic elements
	function pagex_register_bundled_woo_dynamic_callbacks( $callbacks ) {
		$dynamic_callbacks = array(
			'pagex_woo_add_to_cart',
			'pagex_woo_catalog_data',
			'pagex_woo_menu_cart',
			'pagex_woo_product_additional_information',
			'pagex_woo_product_data',
			'pagex_woo_product_data_tabs',
			'pagex_woo_product_images',
			'pagex_woo_product_meta',
			'pagex_woo_product_thumbnail',
			'pagex_woo_sale_flash',
			'pagex_woo_notices',
		);

		foreach ( $dynamic_callbacks as $callback ) {
			$callbacks[] = $callback;
		}

		return $callbacks;
	}

	add_filter( 'pagex_elements_dynamic_callbacks', 'pagex_register_bundled_woo_dynamic_callbacks' );

}

