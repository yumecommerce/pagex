<?php
/*
Plugin Name: Pagex
Description: Theme Builder for WordPress. Design your theme templates dynamically with front-end and back-end builder.
Author: YumeCommerce
Plugin URI: https://github.com/yumecommerce/pagex
Text Domain: pagex
Domain Path: /languages
Version: 0.1
*/

define( 'PAGEX_VERSION', '0.1' );
define( 'PAGEX_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PAGEX_FILE', __FILE__ );
define( 'PAGEX_PLUGIN_BASE', plugin_basename( PAGEX_FILE ) );
define( 'PAGEX_DIR_NAME', dirname( __FILE__ ) );

class Pagex {

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

		// main init. add all plugin's classes and functions
		$this->init();
	}

	/**
	 * Main options data of all styles, templates and etc.
	 *
	 * @return array
	 */
	public static function get_settings() {
		return get_option( 'pagex_settings', array() );
	}

	/**
	 * Do things after all plugins are loaded
	 */
	public function plugins_loaded() {
		// load textdomain
		load_plugin_textdomain( 'pagex', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// register all bundled elements
		include_once( PAGEX_DIR_NAME . '/inc/elements.php' );
	}

	/**
	 * Init all plugin classes
	 */
	public function init() {
		// frontend scripts and styles
		include_once( PAGEX_DIR_NAME . '/inc/pagex-frontend-class.php' );

		// vendors
		include_once( PAGEX_DIR_NAME . '/inc/vendors/pagex-fontawesome-svg-replace-class.php' );
		if ( ! function_exists( 'file_get_html' ) ) {
			include_once( PAGEX_DIR_NAME . '/inc/vendors/simple_html_dom.php' );
		}

		include_once( PAGEX_DIR_NAME . '/inc/pagex-editor-control-attributes-class.php' );
		include_once( PAGEX_DIR_NAME . '/inc/functions.php' );
		include_once( PAGEX_DIR_NAME . '/inc/filters.php' );
		include_once( PAGEX_DIR_NAME . '/inc/shortcodes.php' );
		include_once( PAGEX_DIR_NAME . '/inc/pagex-editor-class.php' );
		include_once( PAGEX_DIR_NAME . '/inc/pagex-backend-editor-class.php' );
		include_once( PAGEX_DIR_NAME . '/inc/pagex-admin-controls-class.php' );
		include_once( PAGEX_DIR_NAME . '/inc/pagex-woocommerce-class.php' );

		// control WordPress templates
		include_once( PAGEX_DIR_NAME . '/inc/pagex-template-manager-class.php' );

		// mega menu
		include_once( PAGEX_DIR_NAME . '/inc/mega-menu/pagex-mega-menu-class.php' );

		// coming soon
		include_once( PAGEX_DIR_NAME . '/inc/coming-soon/pagex-coming-soon-class.php' );

		// demo importer
		include_once( PAGEX_DIR_NAME . '/inc/demo-importer/pagex-demo-data-import-class.php' );

		// initialize activation after main init
		register_activation_hook( PAGEX_FILE, array( $this, 'activation' ) );
	}

	/**
	 * Initialize plugin activation functions
	 * Refresh WordPress permalinks when a plugin registers a custom post type. This gets rid of the nasty 404 errors.
	 */
	public function activation() {
		Pagex_Editor::register_post_types();
		flush_rewrite_rules();
	}

	/**
	 * Returns the translation for given post id
	 *
	 * @param $id - initial post id
	 * @param $post_type - post type to pass to translation plugin filter
	 *
	 * @return string
	 */
	public static function get_translation_id( $id, $post_type ) {
		// WPML
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return apply_filters( 'wpml_object_id', $id, $post_type, true );
		}

		// Polylang
		if ( function_exists( 'icl_object_id' ) ) {
			return icl_object_id( $id, $post_type, true );
		}

		return $id;
	}

	/**
	 * Create array based on saved shortcode attributes
	 *
	 * @param $atts - array with data key and encoded value
	 *
	 * @return array
	 */
	public static function get_dynamic_data( $atts ) {
		return $atts ? wp_unslash( json_decode( urldecode( $atts['data'] ), true ) ) : array();
	}

	/**
	 * Check if builder mode is on
	 *
	 * @return bool
	 */
	public static function is_frontend_builder_active() {
		if ( isset( $_REQUEST['pagex'] ) && is_super_admin() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if we inside a frame of builder mode
	 *
	 * @return bool
	 */
	public static function is_frontend_builder_frame_active() {
		if ( isset( $_REQUEST['pagex-frame'] ) && is_super_admin() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if we in preview excerpt frame
	 *
	 * @return bool
	 */
	public static function is_excerpt_preview_frame_active() {
		if ( isset( $_REQUEST['pagex-excerpt-preview'] ) && is_super_admin() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if builder is active for a theme
	 * Used in a theme function to find out when dummy style and functions should be loaded
	 *
	 * @return bool
	 */
	public static function is_theme_builder_active() {
		if ( is_admin() ) {
			$settings = self::get_settings();
			if ( ! empty( $settings ) ) {
				return true;
			}
		} else {
			if ( Pagex_Template_Manager::get_current_post_template() ) {
				return true;
			}
		}

		if ( self::is_frontend_builder_active() || self::is_frontend_builder_frame_active() || self::is_excerpt_preview_frame_active() ) {
			return true;
		}

		if ( is_singular( 'pagex_layout_builder' ) ) {
			return true;
		}

		return false;
	}
}

new Pagex();