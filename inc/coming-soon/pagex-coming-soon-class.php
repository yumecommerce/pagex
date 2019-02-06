<?php

class Pagex_Coming_Soon {

	// coming soon layout id
	public $layout_id = '';

	public function __construct() {
		$settings = Pagex::get_settings();

		$page   = isset( $settings['coming_soon']['id'] ) ? $settings['coming_soon']['id'] : null;
		$active = isset( $settings['coming_soon']['active'] ) ? $settings['coming_soon']['active'] : null;

		if ( $active == 'yes' && ! $page ) {
			add_action( 'admin_notices', array( $this, 'no_page_set' ) );
		} elseif ( $active == 'yes' && $page ) {
			$this->layout_id = $page;
			add_action( 'admin_notices', array( $this, 'coming_soon_active' ) );
			add_action( 'wp', array( $this, 'coming_soon' ) );
			add_filter( 'template_include', array( $this, 'coming_soon_template' ), 99 );
		}
	}

	/**
	 * Error message when no page is set for coming soon mode
	 */
	public function no_page_set() {
		echo '<div class="notice notice-error" style="margin: 20px 0 30px;"><p>' . __( 'To activate the coming soon mode, please select a coming soon layout.', 'pagex' ) . ' <a href="' . admin_url( 'admin.php?page=pagex#tab_advanced' ) . '">' . __( 'Pagex Settings.', 'pagex' ) . '</a></p></div>';
	}

	/**
	 * Notice message when coming soon mode is active
	 */
	public function coming_soon_active() {
		echo '<div class="notice notice-error" style="margin: 20px 0 30px;"><p>' . __( 'Coming soon mode is active.', 'pagex' ) . ' <a href="' . admin_url( 'admin.php?page=pagex#tab_advanced' ) . '">' . __( 'Pagex Settings.', 'pagex' ) . '</a></p></div>';
	}

	/**
	 * Make website available only for admin for other users display coming soon layout
	 */
	public function coming_soon() {
		if ( is_admin() || is_super_admin() ) {
			return;
		}

		if ( ! is_front_page() ) {
			wp_redirect( home_url() );
			exit();
		}
	}

	/**
	 * Remove all previously registered actions and render coming soon layout
	 *
	 * @param $template
	 *
	 * @return mixed
	 */
	public function coming_soon_template( $template ) {
		if ( is_super_admin() ) {
			return $template;
		}

		add_action( 'pagex_before_page_layout', function () {
			remove_all_actions( 'pagex_header_layout' );
			remove_all_actions( 'pagex_post_content' );
			remove_all_actions( 'pagex_footer_layout' );

			$template_page_id = Pagex::get_translation_id( $this->layout_id, 'pagex_layout_builder' );
			add_action( 'pagex_post_content', function () use ( $template_page_id ) {
				echo do_shortcode( get_post_field( 'post_content', $template_page_id ) );
			} );
		} );

		return $template;
	}
}

new Pagex_Coming_Soon();
