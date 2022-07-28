<?php

class Pagex_Admin_Controls {
	public function __construct() {
		// add scripts for main settings page
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style' ) );

		// add builder links to the admin bar
		add_action( 'admin_bar_menu', array( $this, 'toolbar_links' ), 100 );

		// register plugin page with settings
		add_action( 'admin_menu', array( $this, 'register_page' ), 0 );

		// add links for plugins page
		add_filter( 'plugin_action_links_pagex/pagex.php', array( $this, 'plugin_links' ) );

		// add settings to a plugin page
		add_action( 'admin_init', array( $this, 'settings_sections' ) );

		// export pagex settings
		add_action( 'wp_ajax_pagex_export_settings', array( $this, 'ajax_pagex_export_settings' ) );

		// synchronize Adobe fonts
		add_action( 'wp_ajax_pagex_sync_adobe_fonts', array( $this, 'ajax_pagex_sync_adobe_fonts' ) );
	}

	public function admin_style() {
		if ( is_admin() ) {
			$current_screen = get_current_screen();
			if ( $current_screen->base == 'toplevel_page_pagex' ) {
				wp_enqueue_media();
				wp_enqueue_script( 'mediaelement-core' );
				wp_enqueue_script( 'pagex-admin-page' );
				wp_enqueue_style( 'pagex-admin-page' );
			}
		}
	}

	/**
	 * Add links to main admin top bar
	 *
	 * @param $wp_admin_bar
	 */
	public function toolbar_links( $wp_admin_bar ) {
		if ( is_admin() || ! is_super_admin() ) {
			return;
		}

		global $post, $wp;

		$settings = Pagex::get_settings();

		$post_id     = get_the_ID();
		$current_url = home_url( $wp->request );

		$wp_admin_bar->add_menu( array(
			'id'    => 'pagex',
			'title' => __( 'Pagex Builder', 'pagex' ),
			'href'  => admin_url( 'admin.php?page=pagex' ),
		) );

		$current_header = apply_filters( 'pagex_admin_menu_header_layout_id', '' );
		if ( $current_header && ! is_404() ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'pagex',
				'id'     => 'pagex-header-layout',
				'title'  => __( 'Header Layout', 'pagex' ),
				'href'   => add_query_arg(
					array(
						'pagex'             => '',
						'pagex-layout-id'   => $current_header,
						'pagex-layout-type' => 'header',
						'pagex-post-type'   => 'pagex_layout_builder'
					), $current_url ),
			) );
		}

		if ( $post_id && is_singular() ) {
			if ( is_singular( 'pagex_layout_builder' ) ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'pagex',
					'id'     => 'pagex-post-layout',
					'title'  => __( 'Current Layout', 'pagex' ),
					'href'   => add_query_arg(
						array(
							'pagex'             => '',
							'pagex-layout-id'   => $post_id,
							'pagex-layout-type' => 'template',
							'pagex-post-type'   => 'pagex_layout_builder'
						), $current_url ),
				) );
			} elseif ( isset( $settings['builder'] ) && isset( $settings['builder'][ $post->post_type ] ) ) {
				$page_post_string = __( 'Post Layout', 'pagex' );

				if ( is_page() ) {
					$page_post_string = __( 'Page Layout', 'pagex' );
				}

				$wp_admin_bar->add_menu( array(
					'parent' => 'pagex',
					'id'     => 'pagex-post-layout',
					'title'  => $page_post_string,
					'href'   => add_query_arg(
						array(
							'pagex'             => '',
							'pagex-layout-id'   => $post_id,
							'pagex-layout-type' => 'layout',
							'pagex-post-type'   => get_post_type( $post_id )
						), $current_url ),
				) );
			}
		}

		// get page template based on current query
		$template = Pagex_Template_Manager::get_current_post_template();

		if ( is_singular() ) {
			// do not add template builder for layouts of template pages
			if ( ! is_singular( 'pagex_layout_builder' ) ) {
				if ( $template && $template != 'pagex-blank-template' ) {
					$wp_admin_bar->add_menu( array(
						'parent' => 'pagex',
						'id'     => 'pagex-single-post-template',
						'title'  => __( 'Post Template', 'pagex' ) . ' <span class="pagex-nav-template-name">' . get_the_title( $template ) . '</span>',
						'href'   => add_query_arg(
							array(
								'pagex'             => '',
								'pagex-layout-id'   => $template,
								'pagex-layout-type' => 'template',
								'pagex-post-type'   => 'pagex_post_tmp'
							), $current_url ),
					) );
				}
			}
		} elseif ( is_home() ) {
			if ( $template ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'pagex',
					'id'     => 'pagex-post-templates-blog',
					'title'  => __( 'Blog Template', 'pagex' ) . ' <span class="pagex-nav-template-name">' . get_the_title( $template ) . '</span>',
					'href'   => add_query_arg(
						array(
							'pagex'             => '',
							'pagex-layout-id'   => $template,
							'pagex-layout-type' => 'template',
							'pagex-post-type'   => 'pagex_post_tmp'
						), $current_url ),
				) );
			}
		} elseif ( is_search() ) {
			if ( $template ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'pagex',
					'id'     => 'pagex-post-templates-search-results',
					'title'  => __( 'Search Template', 'pagex' ) . ' <span class="pagex-nav-template-name">' . get_the_title( $template ) . '</span>',
					'href'   => add_query_arg(
						array(
							'pagex'             => '',
							'pagex-layout-id'   => $template,
							'pagex-layout-type' => 'template',
							'pagex-post-type'   => 'pagex_post_tmp'
						), $current_url ),
				) );
			}
		} elseif ( is_archive() ) {
			if ( $template ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'pagex',
					'id'     => 'pagex-post-templates-blog',
					'title'  => __( 'Archive Template', 'pagex' ) . ' <span class="pagex-nav-template-name">' . get_the_title( $template ) . '</span>',
					'href'   => add_query_arg(
						array(
							'pagex'             => '',
							'pagex-layout-id'   => $template,
							'pagex-layout-type' => 'template',
							'pagex-post-type'   => 'pagex_post_tmp'
						), $current_url ),
				) );
			}
		} elseif ( is_404() ) {
			if ( $template ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'pagex',
					'id'     => 'pagex-template-404',
					'title'  => __( '404 Layout', 'pagex' ),
					'href'   => add_query_arg(
						array(
							'pagex'             => '',
							'pagex-layout-id'   => $template,
							'pagex-layout-type' => 'template',
							'pagex-post-type'   => 'pagex_layout_builder'
						), get_permalink( $template ) ),
				) );
			}
		}

		$current_excerpt = apply_filters( 'pagex_admin_menu_posts_loop_excerpt_id', '' );
		if ( $current_excerpt ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'pagex',
				'id'     => 'pagex-excerpt-layout',
				'title'  => __( 'Excerpt Layout', 'pagex' ) . ' <span class="pagex-nav-template-name">' . get_the_title( $current_excerpt ) . '</span>',
				'href'   => admin_url( 'post.php?post=' . $current_excerpt . '&action=edit' ),
				'meta'   => array( 'target' => '_blank' )
			) );
		}

		$current_footer = apply_filters( 'pagex_admin_menu_footer_layout_id', '' );
		if ( $current_footer && ! is_404() ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'pagex',
				'id'     => 'pagex-footer-layout',
				'title'  => __( 'Footer Layout', 'pagex' ),
				'href'   => add_query_arg(
					array(
						'pagex'             => '',
						'pagex-layout-id'   => $current_footer,
						'pagex-layout-type' => 'footer',
						'pagex-post-type'   => 'pagex_layout_builder'
					), $current_url ),
			) );
		}
	}

	/**
	 * Add Pagex admin menu at the backend
	 */
	public function register_page() {
		add_menu_page( 'Pagex', 'Pagex', 'manage_options', 'pagex', array(
			$this,
			'admin_menu'
		), 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMzIgMzIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDMyIDMyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PHBhdGggZD0iTTEzLDMydi03aDYuNGMwLDAsOS44LTEuMyw5LjgtMTIuNEMyOS4xLDEuOCwxOS40LDAsMTkuNCwwSDR2OWwxMy40LDBjMS45LDAsMy41LDEuNCwzLjUsMy42YzAsMi0xLjUsMy40LTMuNSwzLjRsLTgsMCBMNCwyM3Y5SDEzeiIvPjwvc3ZnPgo=', 3 );
		add_submenu_page( 'pagex', __( 'Pagex Settings', 'pagex' ), __( 'Settings', 'pagex' ), 'manage_options', 'pagex' );
	}

	/**
	 * Add "setting link to plugins table, left part
	 *
	 * @param array $links Initial list of links.
	 *
	 * @return array
	 */
	public function plugin_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=pagex' ) . '">' . __( 'Settings', 'pagex' ) . '</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Add settings via setting sections
	 */
	public function admin_menu() {
		$settings = Pagex::get_settings();
		echo '<h1>' . get_admin_page_title() . '</h1>';
		echo '<div class="pagex-settings-wrapper">';
		echo '<div class="pagex-settings">';
		echo '<form class="pagex-admin-settings" method="POST" action="options.php">';
		settings_fields( 'pagex_group' );
		echo '<h2 class="nav-tab-wrapper">
				<a href="#tab_basic" class="nav-tab nav-tab-active">' . __( 'Basic Options', 'pagex' ) . '</a>
				<a href="#tab_design" class="nav-tab">' . __( 'Design', 'pagex' ) . '</a>
				<a href="#tab_apis" class="nav-tab">APIs</a>
				<a href="#tab_advanced" class="nav-tab">' . __( 'Advanced', 'pagex' ) . '</a>
				</h2>';
		echo '<div class="pagex-tabs-options">';
		echo '<div class="pagex-tab-section active" id="tab_basic">';
		do_settings_sections( 'pagex_page' );
		echo '</div>';
		echo '<div class="pagex-tab-section" id="tab_design">';
		do_settings_sections( 'pagex_page_design' );
		echo '</div>';
		echo '<div class="pagex-tab-section" id="tab_apis">';
		do_settings_sections( 'pagex_apis' );
		echo '</div>';
		echo '<div class="pagex-tab-section" id="tab_advanced">';
		do_settings_sections( 'pagex_advanced' );
		echo '</div>';
		echo '</div>';
		submit_button();
		echo '</form>';
		echo '</div>';
		echo '<div class="pagex-settings-sidebar">';

		if ( empty( $settings ) ) {
			echo '<div class="pagex-settings-sidebar-notice"><div id="pagex-easy-start"><input type="hidden" name="pagex_settings[easy_start]" value="true"></div><h3>' . __( 'Easy Start', 'pagex' ) . '</h3>' . __( 'It looks like you have a fresh installation, would you like to create all basic theme layouts and templates?', 'pagex' ) . '<a href="#" onclick="document.querySelector(\'.pagex-admin-settings\').insertAdjacentHTML(\'afterbegin\', document.querySelector(\'#pagex-easy-start\').innerHTML); document.querySelector(\'#submit\').click();">' . __( 'Setup', 'pagex' ) . '</a></div>';
		}

		echo '<div class="pagex-settings-sidebar-notice"><h3>' . __( 'Documentation', 'pagex' ) . '</h3>' . __( 'Learn how to use Pagex. It is a great starting point.', 'pagex' ) . '<a href="https://github.com/yumecommerce/pagex/wiki" target="_blank">' . __( 'Read our guide', 'pagex' ) . '</a></div>';
		echo '<div class="pagex-settings-sidebar-notice"><h3>' . __( 'Report Issue', 'pagex' ) . '</h3>' . __( 'Create a report to help us improve.', 'pagex' ) . '<a href="https://github.com/yumecommerce/pagex/issues" target="_blank">' . __( 'Report', 'pagex' ) . '</a></div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Register settings page with tabs
	 */
	public function settings_sections() {
		register_setting( 'pagex_group', 'pagex_settings', array(
			'sanitize_callback' => array(
				$this,
				'sanitize_options'
			)
		) );

		add_settings_section( 'pagex_section', __( 'Basic Options', 'pagex' ), '', 'pagex_page' );
		add_settings_section( 'pagex_section', __( 'Design', 'pagex' ), '', 'pagex_page_design' );
		add_settings_section( 'pagex_section', 'APIs', '', 'pagex_apis' );
		add_settings_section( 'pagex_section', __( 'Advanced', 'pagex' ), '', 'pagex_advanced' );

		// default
		add_settings_field( 'post_type_support', __( 'Post Types', 'pagex' ), array(
			$this,
			'enabled_post_types'
		), 'pagex_page', 'pagex_section' );
		add_settings_field( 'default_layouts', __( 'Default Layouts', 'pagex' ), array(
			$this,
			'default_layouts'
		), 'pagex_page', 'pagex_section' );
		add_settings_field( 'post_templates', __( 'Theme Templates', 'pagex' ), array(
			$this,
			'post_templates'
		), 'pagex_page', 'pagex_section' );

		// design
		add_settings_field( 'design_layout', __( 'Layout', 'pagex' ), array(
			$this,
			'design_layout'
		), 'pagex_page_design', 'pagex_section' );
		add_settings_field( 'design_typography', __( 'Typography', 'pagex' ), array(
			$this,
			'design_typography'
		), 'pagex_page_design', 'pagex_section' );
		add_settings_field( 'design_buttons', __( 'Buttons', 'pagex' ), array(
			$this,
			'design_buttons'
		), 'pagex_page_design', 'pagex_section' );
		add_settings_field( 'design_forms', __( 'Form Elements', 'pagex' ), array(
			$this,
			'design_forms'
		), 'pagex_page_design', 'pagex_section' );
		add_settings_field( 'design_preloader', __( 'Preloader', 'pagex' ), array(
			$this,
			'design_preloader'
		), 'pagex_page_design', 'pagex_section' );

		// apis
		add_settings_field( 'apis_mailchimp', 'MailChimp', array(
			$this,
			'apis_mailchimp'
		), 'pagex_apis', 'pagex_section' );
		add_settings_field( 'apis_google_map', 'Google Maps', array(
			$this,
			'apis_google_map'
		), 'pagex_apis', 'pagex_section' );

		// advanced
		add_settings_field( 'advanced_coming_soon', __( 'Coming Soon or Maintenance Mode', 'pagex' ), array(
			$this,
			'coming_soon'
		), 'pagex_advanced', 'pagex_section' );
		add_settings_field( 'advanced_multilingual', __( 'Multilingual', 'pagex' ), array(
			$this,
			'advanced_multilingual'
		), 'pagex_advanced', 'pagex_section' );
		add_settings_field( 'advanced_gdpr', __( 'GDPR Notice', 'pagex' ), array(
			$this,
			'advanced_gdpr'
		), 'pagex_advanced', 'pagex_section' );
		add_settings_field( 'advanced_export', __( 'Export', 'pagex' ), array(
			$this,
			'advanced_export'
		), 'pagex_advanced', 'pagex_section' );
	}

	/**
	 * Sanitize settings before save
	 * Setup easy installation if request is set
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function sanitize_options( $value ) {
		// easy start setup
		if ( isset( $value['easy_start'] ) && empty( Pagex::get_settings() ) ) {
			$excerpt_post = json_decode( '{"params":{"pn7dc1":{"container_width":"container-fluid","pagex_custom_class":"p-0"},"pcojnw":{"gutters":"no-gutters","pagex_custom_class":""},"pml8v8":{"tag":"p","pagex_custom_class":""},"p6tt0p":{"tag":"h4","link":"true","pagex_margin":{"xs":{"top":"","right":"","bottom":"30","left":""},"sm":{"top":"","right":"","bottom":"","left":""},"md":{"top":"","right":"","bottom":"","left":""},"lg":{"top":"","right":"","bottom":"","left":""},"xl":{"top":"","right":"","bottom":"","left":""}},"pagex_custom_class":""}},"layout":"<div class=\"section\" data-id=\"p7zchb\" data-type=\"section\"><div class=\"container  container-fluid p-0\" data-id=\"pn7dc1\" data-type=\"container\"><div class=\"row  no-gutters\" data-id=\"pcojnw\" data-type=\"row\"><div class=\"col\" data-id=\"pkvmc5\" data-type=\"column\"><div class=\"element\" data-id=\"p6tt0p\" data-type=\"post_title\" data-callback=\"pagex_post_title\"><style id=\"p6tt0p\">[data-id=\"p6tt0p\"] {margin: 0 0 30px 0 !important}</style><div class=\"element-wrap\">[pagex_post_title data=\"%7B%22tag%22%3A%22h4%22%2C%22link%22%3A%22true%22%7D\"]</div></div><div class=\"element\" data-id=\"pml8v8\" data-type=\"post_excerpt\" data-callback=\"pagex_post_excerpt\"><div class=\"element-wrap\">[pagex_post_excerpt data=\"%7B%22tag%22%3A%22p%22%7D\"]</div></div></div></div></div></div>"}', true );

			$excerpt_post_id = wp_insert_post( array(
				'post_title'   => 'Post: Excerpt',
				'post_type'    => 'pagex_excerpt_tmp',
				'post_status'  => 'publish',
				'post_content' => $excerpt_post['layout'],
				'meta_input'   => array(
					'_pagex_elements_params' => json_encode( $excerpt_post['params'] ),
				),
			) );

			$layouts = array(
				'default_header' => 'Header',
				'default_footer' => 'Footer',
				'page_404'       => '404'
			);

			$templates = array(
				'page_single'      => array(
					'Page: Single',
					'single',
					'page',
					'{"params":{"p81f4s":{"pagex_custom_class":""}},"layout":"<div class=\"section\" data-id=\"pu5ft7\" data-type=\"section\"><div class=\"container\" data-id=\"pv40ig\" data-type=\"container\"><div class=\"row\" data-id=\"pmrxs9\" data-type=\"row\"><div class=\"col\" data-id=\"p5imo0\" data-type=\"column\"><div class=\"element\" data-id=\"p81f4s\" data-type=\"post_content\" data-callback=\"pagex_post_content\"><div class=\"element-wrap\">[pagex_post_content data=\"%5B%5D\"]</div></div></div></div></div></div>"}'
				),
				'page_single_full' => array(
					'Page: Full Width',
					'single',
					'page',
					'{"params":{"p9cr0q":{"pagex_custom_class":""},"pa7oiw":{"container_width":"container-fluid","pagex_custom_class":"p-0"},"pv4fve":{"gutters":"no-gutters","pagex_custom_class":""}},"layout":"<div class=\"section\" data-id=\"ppff5q\" data-type=\"section\"><div class=\"container  container-fluid p-0\" data-id=\"pa7oiw\" data-type=\"container\"><div class=\"row  no-gutters\" data-id=\"pv4fve\" data-type=\"row\"><div class=\"col\" data-id=\"pw0q8v\" data-type=\"column\"><div class=\"element\" data-id=\"p9cr0q\" data-type=\"post_content\" data-callback=\"pagex_post_content\"><div class=\"element-wrap\">[pagex_post_content data=\"%5B%5D\"]</div></div></div></div></div></div>"}'
				),
				'post_single'      => array(
					'Post: Single',
					'single',
					'post',
					'{"params":{"plbt22":{"pagex_custom_class":""}},"layout":"<div class=\"section\" data-id=\"pww22x\" data-type=\"section\"><div class=\"container\" data-id=\"plt8sq\" data-type=\"container\"><div class=\"row\" data-id=\"pdheur\" data-type=\"row\"><div class=\"col\" data-id=\"pds4fq\" data-type=\"column\"><div class=\"element\" data-id=\"plbt22\" data-type=\"post_content\" data-callback=\"pagex_post_content\"><div class=\"element-wrap\">[pagex_post_content data=\"%5B%5D\"]</div></div></div></div></div></div>"}'
				),
				'post_archive'     => array(
					'Post: Archive',
					'archive',
					'post',
					'{"params":{"pydpwy":{"template":"' . $excerpt_post_id . '","nothing_found":"0","no_posts":"0","layout":"grid","pagination":"default","pagex_custom_class":""}},"layout":"<div class=\"section\" data-id=\"ppcsvf\" data-type=\"section\"><div class=\"container\" data-id=\"pygbus\" data-type=\"container\"><div class=\"row\" data-id=\"p6cp6d\" data-type=\"row\"><div class=\"col\" data-id=\"pewt00\" data-type=\"column\"><div class=\"element\" data-id=\"pydpwy\" data-type=\"posts_loop\" data-callback=\"pagex_posts_loop\"><div class=\"element-wrap\">[pagex_posts_loop data=\"%7B%22template%22%3A%22' . $excerpt_post_id . '%22%2C%22nothing_found%22%3A%220%22%2C%22no_posts%22%3A%220%22%2C%22layout%22%3A%22grid%22%2C%22pagination%22%3A%22default%22%7D\"]</div></div></div></div></div></div>"}'
				)
			);

			foreach ( $layouts as $k => $layout ) {
				$post_id = wp_insert_post( array(
					'post_title'  => $layout,
					'post_type'   => 'pagex_layout_builder',
					'post_status' => 'publish'
				) );

				if ( $k == 'page_404' ) {
					$value['post_templates']['page_404'] = $post_id;
				} else {
					$value[ $k ] = $post_id;
				}
			}

			foreach ( $templates as $k => $template ) {
				$data    = json_decode( $template[3], true );
				$post_id = wp_insert_post( array(
					'post_title'   => $template[0],
					'post_type'    => 'pagex_post_tmp',
					'post_content' => $data['layout'],
					'post_status'  => 'publish',
					'meta_input'   => array(
						'_pagex_template_type'      => $template[1],
						'_pagex_template_post_type' => $template[2],
						'_pagex_elements_params'    => json_encode( $data['params'] ),
					)
				) );

				$value['post_templates'][ $k ] = $post_id;
			}

			// make pages editable
			$value['builder']['page'] = 'page';

			// enable permalink
			global $wp_rewrite;
			$wp_rewrite->set_permalink_structure( '/%postname%/' );
			$wp_rewrite->flush_rules();

			// remove easy_start from options since if main option does not exist sanitize_option will be called twice
			unset( $value['easy_start'] );
		}

		return $value;
	}

	/**
	 * Turn on/off builder editor for different post types
	 */
	public function enabled_post_types() {
		$settings = Pagex::get_settings();

		$post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );

		echo '<p>' . __( 'Enable Builder Editor for next post types.', 'pagex' ) . '</p><br>';
		foreach ( $post_types as $key => $post_type ) {
			// skip layout builder since it will be always on
			if ( $post_type->name == 'pagex_layout_builder' ) {
				continue;
			}

			$active = isset( $settings['builder'][ $post_type->name ] ) ? $settings['builder'][ $post_type->name ] : null;
			echo '<label><input type="checkbox" name="pagex_settings[builder][' . $post_type->name . ']" value="' . $post_type->name . '" ' . checked( $post_type->name, $active, false ) . '>' . $post_type->label . '&nbsp;&nbsp;</label>';
		}

		// make default builder post types activated all the time
		echo '<input type="hidden" name="pagex_settings[builder][pagex_layout_builder]" value="pagex_layout_builder">';
		echo '<input type="hidden" name="pagex_settings[builder][pagex_post_tmp]" value="pagex_post_tmp">';
		echo '<input type="hidden" name="pagex_settings[builder][pagex_excerpt_tmp]" value="pagex_excerpt_tmp">';

	}

	public function default_layouts() {
		$val            = Pagex::get_settings();
		$default_header = isset( $val['default_header'] ) ? $val['default_header'] : null;
		$default_footer = isset( $val['default_footer'] ) ? $val['default_footer'] : null;

		$data = pagex_get_layout_templates();

		?>
        <p><?php _e( 'Define default header and footer layouts. You can create and change layouts via', 'pagex' ) ?> <a
                    href="<?php echo admin_url( 'edit.php?post_type=pagex_layout_builder' ); ?>"><?php _e( 'Layout Builder', 'pagex' ); ?></a>
        </p>
        <br>
        <table class="pagex-post-templates-table">
            <thead>
            <tr>
                <th><?php _e( 'Type', 'pagex' ); ?></th>
                <th><?php _e( 'Layout', 'pagex' ); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th><?php _e( 'Default Header', 'pagex' ); ?></th>
                <td>
                    <select name="pagex_settings[default_header]">
						<?php
						foreach ( $data as $key => $value ) {
							echo '<option value="' . $key . '" ' . selected( $key, $default_header, false ) . '>' . $value . '</option>';
						}
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Default Footer', 'pagex' ); ?></th>
                <td>
                    <select name="pagex_settings[default_footer]">
						<?php
						foreach ( $data as $key => $value ) {
							echo '<option value="' . $key . '" ' . selected( $key, $default_footer, false ) . '>' . $value . '</option>';
						}
						?>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>


		<?php
	}

	/**
	 * Returns array with layouts for different types of templates
	 *
	 * @param $tmp_type
	 *
	 * @return array
	 */
	public function get_post_templates( $tmp_type ) {

		if ( $tmp_type == 'taxonomy' ) {
			$tmp_type = 'archive';
		}

		$args  = array(
			'post_type'   => 'pagex_post_tmp',
			'numberposts' => - 1,
			'meta_query'  => array(
				array(
					'key'   => '_pagex_template_type',
					'value' => $tmp_type
				),
			)
		);
		$query = get_posts( $args );
		$data  = array();
		if ( $query ) {
			foreach ( $query as $post ) {
				setup_postdata( $post );
				$data[ $post->ID ] = esc_attr( $post->post_title );
			}
		}
		wp_reset_postdata();

		return $data;
	}

	/**
	 * Specify basic WordPress and custom post type theme templates
	 */
	public function post_templates() {
		$page_templates         = $this->get_post_templates( 'single' );
		$post_archive_templates = $this->get_post_templates( 'archive' );

		$settings            = get_option( 'pagex_settings', array() );
		$page_404            = isset( $settings['post_templates']['page_404'] ) ? $settings['post_templates']['page_404'] : null;
		$page_page           = isset( $settings['post_templates']['page_single'] ) ? $settings['post_templates']['page_single'] : null;
		$page_post           = isset( $settings['post_templates']['post_single'] ) ? $settings['post_templates']['post_single'] : null;
		$page_blog           = isset( $settings['post_templates']['post_archive'] ) ? $settings['post_templates']['post_archive'] : null;
		$page_archive        = isset( $settings['post_templates']['post_taxonomy'] ) ? $settings['post_templates']['post_taxonomy'] : null;
		$page_search_results = isset( $settings['post_templates']['search_results'] ) ? $settings['post_templates']['search_results'] : null;

		?>

        <p><?php _e( 'Define template layouts based on page type. You can create and change layouts via', 'pagex' ) ?>
            <a href="<?php echo admin_url( 'edit.php?post_type=pagex_post_tmp' ); ?>"><?php _e( 'Theme Templates', 'pagex' ); ?></a>
        </p>
        <h2>WordPress</h2>
        <table class="pagex-post-templates-table">
            <thead>
            <tr>
                <th><?php _e( 'Type', 'pagex' ); ?></th>
                <th><?php _e( 'Template', 'pagex' ); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th><?php _e( 'Single Page', 'pagex' ); ?></th>
                <td>
                    <select name="pagex_settings[post_templates][page_single]">
                        <option value=""><?php _e( 'None', 'pagex' ) ?></option>
						<?php foreach ( $page_templates as $key => $value ) {
							echo '<option value="' . $key . '" ' . selected( $key, $page_page, false ) . '>' . $value . '</option>';
						} ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Single Post', 'pagex' ); ?></th>
                <td>
                    <select name="pagex_settings[post_templates][post_single]">
                        <option value=""><?php _e( 'None', 'pagex' ) ?></option>
						<?php foreach ( $page_templates as $key => $value ) {
							echo '<option value="' . $key . '" ' . selected( $key, $page_post, false ) . '>' . $value . '</option>';
						} ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Blog Page', 'pagex' ); ?></th>
                <td>
                    <select name="pagex_settings[post_templates][post_archive]">
                        <option value=""><?php _e( 'None', 'pagex' ) ?></option>
						<?php foreach ( $post_archive_templates as $key => $value ) {
							echo '<option value="' . $key . '" ' . selected( $key, $page_blog, false ) . '>' . $value . '</option>';
						} ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Post Taxonomy', 'pagex' ); ?> <p
                            class="description"><?php _e( 'Post tags, categories, and etc.', 'pagex' ) ?></p></th>
                <td>
                    <select name="pagex_settings[post_templates][post_taxonomy]">
                        <option value=""><?php _e( 'None', 'pagex' ) ?></option>
						<?php foreach ( $post_archive_templates as $key => $value ) {
							echo '<option value="' . $key . '" ' . selected( $key, $page_archive, false ) . '>' . $value . '</option>';
						} ?>
                    </select>
                    <p class="description"><?php _e( 'If none is selected the Blog Page template will be used instead.', 'pagex' ) ?></p>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Search Results', 'pagex' ); ?></th>
                <td>
                    <select name="pagex_settings[post_templates][post_search_results]">
                        <option value=""><?php _e( 'None', 'pagex' ) ?></option>
						<?php foreach ( $post_archive_templates as $key => $value ) {
							echo '<option value="' . $key . '" ' . selected( $key, $page_search_results, false ) . '>' . $value . '</option>';
						} ?>
                    </select>
                    <p class="description"><?php _e( 'If none is selected the Blog Page template will be used instead.', 'pagex' ) ?></p>
                </td>
            </tr>
            <tr>
                <th>404</th>
                <td>
                    <select name="pagex_settings[post_templates][page_404]" id="post_templates_404">
						<?php

						$data = pagex_get_layout_templates();

						foreach ( $data as $key => $value ) {
							echo '<option value="' . $key . '" ' . selected( $key, $page_404, false ) . '>' . $value . '</option>';
						}

						?>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <br>

		<?php
		$post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );
		unset( $post_types['page'], $post_types['attachment'], $post_types['post'], $post_types['pagex_layout_builder'], $post_types['pagex_post_tmp'] );

		if ( ! empty( $post_types ) ) {
			echo '<h3>' . __( 'Custom post types', 'pagex' ) . '</h3>';
			echo '<p>' . __( 'Enable template layouts for custom post types.', 'pagex' ) . '</p>';
			echo '<br>';

			// checkboxes for custom post type
			foreach ( $post_types as $key => $post_type ) {
				$active = isset( $settings['active_post_templates'][ $post_type->name ] ) ? $settings['active_post_templates'][ $post_type->name ] : null;
				echo '<label><input type="checkbox" name="pagex_settings[active_post_templates][' . $post_type->name . ']" value="' . $post_type->name . '" ' . checked( $post_type->name, $active, false ) . '>' . $post_type->label . '&nbsp;&nbsp;</label>';
			}

			// print table settings for custom post types
			if ( isset( $settings['active_post_templates'] ) && ! empty( $settings['active_post_templates'] ) ) {
				foreach ( $settings['active_post_templates'] as $key_post_template => $post_template ) {
					// check if post type is registered
					if ( ! array_key_exists( $key_post_template, $post_types ) ) {
						return;
					}

					?>
                    <br>
                    <br>
                    <h2><?php echo $post_types[ $key_post_template ]->label; ?></h2>
                    <table class="pagex-post-templates-table">
                        <thead>
                        <tr>
                            <th><?php _e( 'Type', 'pagex' ); ?></th>
                            <th><?php _e( 'Template', 'pagex' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
						foreach (
							array(
								'single'   => __( 'Single Page', 'pagex' ) . '<p class="description">' . __( 'When single post page is being displayed', 'pagex' ) . '</p>',
								'archive'  => __( 'Archive', 'pagex' ) . '<p class="description">' . __( 'Main post type page with list of all posts', 'pagex' ) . '</p>',
								'taxonomy' => __( 'Taxonomy', 'pagex' ) . '<p class="description">' . __( 'Only if post type has registered taxonomies', 'pagex' ) . '</p>',
							) as $type_key => $type
						) {
							$post_template_type_name = $key_post_template . '_' . $type_key;
							?>
                            <tr>
                                <th><?php echo $type; ?></th>
                                <td>
                                    <select name="pagex_settings[post_templates][<?php echo $post_template_type_name; ?>]">
                                        <option value=""><?php _e( 'None', 'pagex' ) ?></option>
										<?php
										$data = $type_key == 'single' ? $page_templates : $post_archive_templates;
										foreach ( $data as $key => $value ) {
											$option_value = isset( $settings['post_templates'][ $post_template_type_name ] ) ? $settings['post_templates'][ $post_template_type_name ] : null;

											echo '<option value="' . $key . '" ' . selected( $key, $option_value, false ) . '>' . $value . '</option>';
										}
										?>
                                    </select>
                                </td>
                            </tr>
							<?php
						}
						?>
                        </tbody>
                    </table>
					<?php
				}
			}
		}
	}

	/**
	 * Max width for a Bootstrap .container when viewport is > 1200px
	 */
	public function design_layout() {
		$controls = array(
			array(
				'id'          => 'max_width_xl',
				'label'       => __( 'Container width for desktops', 'pagex' ),
				'description' => __( 'Optional. Default is 1140px.', 'pagex' ),
				'type'        => 'text',
			),
		);

		$this->make_form( $controls, array( 'design' ) );
	}


	/**
	 * Typography settings
	 */
	public function design_typography() {
		$fonts    = Pagex_Editor_Control_Attributes::get_fonts();
		$settings = Pagex::get_settings();

		$font_weights = array(
			''     => __( 'Default', 'pagex' ),
			'bold' => __( 'Bold', 'pagex' ),
			'100'  => '100',
			'200'  => '200',
			'300'  => '300',
			'400'  => '400',
			'500'  => '500',
			'600'  => '600',
			'700'  => '700',
			'800'  => '800',
			'900'  => '900',
		);

		$main_font_controls = array(
			array(
				'id'      => 'name',
				'label'   => __( 'Font Family', 'pagex' ),
				'type'    => 'select',
				'options' => $fonts,
				'clear'   => true,
			),
			array(
				'id'    => 'size',
				'label' => __( 'Font Size', 'pagex' ),
				'type'  => 'text',
			),
			array(
				'id'    => 'lineheight',
				'label' => __( 'Line Height', 'pagex' ),
				'type'  => 'text',
			),
			array(
				'id'    => 'letterspacing',
				'label' => __( 'Letter Spacing', 'pagex' ),
				'type'  => 'text',
				'clear' => true,
			),
			array(
				'id'      => 'weight',
				'label'   => __( 'Font Weight', 'pagex' ),
				'type'    => 'select',
				'options' => $font_weights,
			),
			array(
				'id'      => 'bold_weight',
				'label'   => __( 'Font Weight for Bold Tag', 'pagex' ),
				'type'    => 'select',
				'options' => $font_weights,
				'clear'   => true,
			),
			array(
				'id'    => 'color',
				'label' => __( 'Text Color', 'pagex' ),
				'type'  => 'color',
				'clear' => true,
			),
			array(
				'id'    => 'links_color',
				'label' => __( 'Links Color', 'pagex' ),
				'type'  => 'color',
			),
			array(
				'id'    => 'links_hover',
				'label' => __( 'Links Color on Hover', 'pagex' ),
				'type'  => 'color',
			),
		);

		$heading_font_controls = array(
			array(
				'id'      => 'name',
				'label'   => __( 'Font Family', 'pagex' ),
				'type'    => 'select',
				'options' => $fonts,
				'clear'   => true,
			),
			array(
				'id'      => 'weight',
				'label'   => __( 'Font Weight', 'pagex' ),
				'type'    => 'select',
				'options' => $font_weights,
			),
			array(
				'id'    => 'letterspacing',
				'label' => __( 'Letter Spacing', 'pagex' ),
				'type'  => 'text',
				'clear' => true,
			),
			array(
				'id'    => 'color',
				'label' => __( 'Headings Color', 'pagex' ),
				'type'  => 'color',
			),
		);

		// Google fonts subsets
		$controls = array(
			array(
				'label'       => __( 'Google Fonts Subsets', 'pagex' ),
				'description' => __( 'Select subsets for Google Fonts.', 'pagex' ),
				'type'        => 'label',
				'clear'       => true,
			),
			array(
				'id'    => 'latin',
				'label' => 'latin',
				'type'  => 'checkbox',
				'value' => 1,
			),
			array(
				'id'    => 'latin-ext',
				'label' => 'latin-ext',
				'type'  => 'checkbox',
				'value' => 1,
			),
			array(
				'id'    => 'cyrillic',
				'label' => 'cyrillic',
				'type'  => 'checkbox',
				'value' => 1,
			),
			array(
				'id'    => 'cyrillic-ext',
				'label' => 'cyrillic-ext',
				'type'  => 'checkbox',
				'value' => 1,
			),
			array(
				'id'    => 'greek',
				'label' => 'greek',
				'type'  => 'checkbox',
				'value' => 1,
			),
			array(
				'id'    => 'greek-ext',
				'label' => 'greek-ext',
				'type'  => 'checkbox',
				'value' => 1,
			),
			array(
				'id'    => 'vietnamese',
				'label' => 'vietnamese',
				'type'  => 'checkbox',
				'value' => 1,
			),
		);

		$this->make_form( $controls, array( 'design', 'google_fonts_subsets' ) );

		$main_font_weight = isset( $settings['design']['main_font']['google_weight'] ) ? $settings['design']['main_font']['google_weight'] : array();

		$heading_font_weight = isset( $settings['design']['heading_font']['google_weight'] ) ? $settings['design']['heading_font']['google_weight'] : array();

		?>

        <p><?php _e( 'Default Fonts', 'pagex' ); ?></p>
        <br>
        <table class="pagex-post-templates-table">
            <thead>
            <tr>
                <th><?php _e( 'Type', 'pagex' ); ?></th>
                <th><?php _e( 'Font', 'pagex' ); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th><?php _e( 'Main Font', 'pagex' ); ?></th>
                <td>
					<?php $this->make_form( $main_font_controls, array( 'design', 'main_font' ) ); ?>
                    <p class="description"><?php _e( 'Select weights for Google Fonts which will be loaded all the time.', 'pagex' ); ?></p>
                    <p>
						<?php
						foreach ( range( 100, 900, 100 ) as $value ) {
							echo '<label><input type="checkbox" name="pagex_settings[design][main_font][google_weight][' . $value . ']" value="' . $value . '" ' . checked( $value, isset( $main_font_weight[ $value ] ) ? $value : 0, false ) . ' /> ' . $value . '</label>&nbsp;&#160;';
						}
						?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Headings', 'pagex' ); ?></th>
                <td>
					<?php $this->make_form( $heading_font_controls, array( 'design', 'heading_font' ) ); ?>
                    <p class="description"><?php _e( 'Select weights for Google Fonts which will be loaded all the time.', 'pagex' ); ?></p>
                    <p>
                    <p>
						<?php
						foreach ( range( 100, 900, 100 ) as $value ) {
							echo '<label><input type="checkbox" name="pagex_settings[design][heading_font][google_weight][' . $value . ']" value="' . $value . '" ' . checked( $value, isset( $heading_font_weight[ $value ] ) ? $value : 0, false ) . ' /> ' . $value . '</label>&nbsp;&#160;';
						}
						?>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>

        <br>
        <p><?php _e( 'Custom Fonts', 'pagex' ); ?></p>
        <p class="description"><?php _e( 'Self-hosted fonts. Once you upload your font variations, you’ll be able to choose the font inside Pagex.', 'pagex' ); ?></p>

		<?php

		$custom_fonts        = '';
		$custom_fonts_weight = array_merge( array( 'normal', 'bold' ), range( 100, 900, 100 ) );
		$custom_fonts_style  = array( 'normal', 'italic', 'oblique' );

		if ( isset( $settings['design']['custom_font'] ) ) {
			foreach ( $settings['design']['custom_font'] as $k => $font ) {
				$custom_fonts .= '<div class="pagex-row custom-font-row"><div><label>Font Family:</label><input name="pagex_settings[design][custom_font][' . $k . '][name]" value="' . $font['name'] . '" type="text" placeholder="Font Name" data-font-id="' . $k . '"><div class="delete-font">Delete</div></div> <div class="pagex-row custom-font-variations">';

				foreach ( $font['weight'] as $v_key => $v_val ) {
					$weights = $styles = '';

					foreach ( $custom_fonts_weight as $weight ) {
						$weights .= '<option value="' . $weight . '" ' . selected( $weight, $font['weight'][ $v_key ], false ) . '>' . $weight . '</option>';
					}

					foreach ( $custom_fonts_style as $weight ) {
						$styles .= '<option value="' . $weight . '" ' . selected( $weight, $font['style'][ $v_key ], false ) . '>' . $weight . '</option>';
					}

					$file = $font['woff'][ $v_key ];

					$custom_fonts .= '<div class="pagex-row custom-font-variation"><p><label>Weight</label><select name="pagex_settings[design][custom_font][' . $k . '][weight][]">' . $weights . '</select></p><p><label>Style</label><select name="pagex_settings[design][custom_font][' . $k . '][style][]">' . $styles . '</select></p><p><label>WOFF File</label><input name="pagex_settings[design][custom_font][' . $k . '][woff][]" type="text" value="' . $file . '"><button type="button" class="button button-primary select-font-file">Select</button></p><div><button type="button" class="button delete-font-variation" title="Delete Font Variation">×</button></div></div>';
				}

				$custom_fonts .= '<button type="button" class="button add-font-variation">Add Font Variation</button></div></div>';
			}
		}

		?>

        <div class="pagex-custom-fonts"><?php echo $custom_fonts; ?></div>

        <script>
            var pagexCustomFont = '<div class="pagex-row custom-font-row"><div><label>Font Family:</label><input name="pagex_settings[design][custom_font][uniqid][name]" type="text" placeholder="Font Name" data-font-id="uniqid"><div class="delete-font">Delete</div></div> <div class="pagex-row custom-font-variations"><div class="pagex-row custom-font-variation"><p><label>Weight</label><select name="pagex_settings[design][custom_font][uniqid][weight][]"><option value="normal">normal</option><option value="bold">bold</option><option value="100">100</option><option value="200">200</option><option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option><option value="800">800</option><option value="900">900</option></select></p><p><label>Style</label><select name="pagex_settings[design][custom_font][uniqid][style][]"><option value="normal">normal</option><option value="italic">italic</option><option value="oblique">oblique</option></select></p><p><label>WOFF File</label><input name="pagex_settings[design][custom_font][uniqid][woff][]" type="text"><button type="button" class="button button-primary select-font-file">Select</button></p><div><button type="button" class="button delete-font-variation" title="Delete Font Variation">×</button></div></div><button type="button" class="button add-font-variation">Add Font Variation</button></div></div>';

            var pagexCustomFontVariation = '<div class="pagex-row custom-font-variation"><p><label>Weight</label><select name="pagex_settings[design][custom_font][uniqid][weight][]"><option value="normal">normal</option><option value="bold">bold</option><option value="100">100</option><option value="200">200</option><option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option><option value="800">800</option><option value="900">900</option></select></p><p><label>Style</label><select name="pagex_settings[design][custom_font][uniqid][style][]"><option value="normal">normal</option><option value="italic">italic</option><option value="oblique">oblique</option></select></p><p><label>WOFF File</label><input name="pagex_settings[design][custom_font][uniqid][woff][]" type="text"><button type="button" class="button button-primary select-font-file">Select</button></p><div><button type="button" class="button delete-font-variation" title="Delete Font Variation">×</button></div></div>';
        </script>

        <div class="pagex-row">
            <button type="button" class="button add-new-font">Add New Font</button>
        </div>
        <hr>
        <br>
        <p>Adobe Fonts (Typekit)</p>
        <p class="description"><?php _e( 'Adobe Fonts (formerly Typekit) is an online service which offers a subscription library
            of high-quality fonts.', 'pagex' ); ?></p>
        <br>

		<?php
		$adobe_fonts = $adobe_fonts_id = '';

		if ( isset( $settings['design']['adobe_font'] ) ) {
			$adobe_fonts_id = $settings['design']['adobe_font']['id'];

			if ( $adobe_fonts_id ) {
				$adobe_fonts_values = $settings['design']['adobe_font']['fonts'];
				$adobe_fonts        .= __( 'The next fonts were added to library:', 'pagex' ) . ' ' . implode( ', ', $adobe_fonts_values );
				foreach ( $adobe_fonts_values as $font ) {
					$adobe_fonts .= '<input type="hidden" name="pagex_settings[design][adobe_font][fonts][]" value="' . $font . '">';
				}
			}
		}
		?>

        <div class="adobe-fonts"><?php echo $adobe_fonts; ?></div>

        <div class="pagex-row">
            <div>
                <label>Project ID (Kit ID)</label>
                <input type="text" id="adobe_fonts_id" value="<?php echo $adobe_fonts_id; ?>"
                       name="pagex_settings[design][adobe_font][id]">
                <button type="button" class="button sync-adobe-fonts"><?php _e( 'Synchronize', 'pagex' ) ?></button>
            </div>
        </div>
        <br>
<!--        <hr>-->
<!--        <br>-->
<!--        <label><input type="checkbox" name="pagex_settings[design][self_hosted_fonts]" value="true" --><?php //checked( true, isset($settings['design']['self_hosted_fonts']) ); ?><!-- ' />--><?php //_e( 'Make selected Adobe and Google Fonts self-hosted', 'pagex' ) ?><!--</label>-->
<!--        <p class="description">--><?php //_e( 'It will download all chosen fonts to WordPress upload folder.', 'pagex' ); ?><!--</p>-->
<!--        <br><br>-->

		<?php
	}

	/**
	 * Buttons style
	 */
	public function design_buttons() {
		$controls = array(
			array(
				'id'      => 'font_size',
				'label'   => __( 'Font Size', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'id'      => 'font_weight',
				'label'   => __( 'Font Weight', 'pagex' ),
				'type'    => 'select',
				'options' => array(
					''     => __( 'Default', 'pagex' ),
					'bold' => __( 'Bold', 'pagex' ),
					'100'  => '100',
					'200'  => '200',
					'300'  => '300',
					'400'  => '400',
					'500'  => '500',
					'600'  => '600',
					'700'  => '700',
					'800'  => '800',
					'900'  => '900',
				),
			),
			array(
				'id'      => 'letter_spacing',
				'label'   => __( 'Letter Spacing', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'id'      => 'text_transform',
				'label'   => __( 'Text Transform', 'pagex' ),
				'type'    => 'select',
				'options' => array(
					''           => __( 'Default', 'pagex' ),
					'lowercase'  => __( 'Lowercase', 'pagex' ),
					'uppercase'  => __( 'Uppercase', 'pagex' ),
					'capitalize' => __( 'Capitalize', 'pagex' ),
				),
			),
			array(
				'id'      => 'min_height',
				'label'   => __( 'Min. Height', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'id'      => 'min_width',
				'label'   => __( 'Min. Width', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'id'      => 'border_width',
				'label'   => __( 'Border Width', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'id'      => 'border_radius',
				'label'   => __( 'Border Radius', 'pagex' ),
				'type'    => 'text',
				'default' => '',
				'clear'   => true,
			),
			array(
				'id'      => 'color',
				'label'   => __( 'Color', 'pagex' ),
				'type'    => 'color',
				'default' => '',
			),
			array(
				'id'      => 'color_hover',
				'label'   => __( 'Color on Hover', 'pagex' ),
				'type'    => 'color',
				'default' => '',
				'clear'   => true,
			),
			array(
				'id'      => 'bg',
				'label'   => __( 'Background', 'pagex' ),
				'type'    => 'color',
				'default' => '',
			),
			array(
				'id'      => 'bg_hover',
				'label'   => __( 'Background on Hover', 'pagex' ),
				'type'    => 'color',
				'default' => '',
				'clear'   => true,
			),
			array(
				'id'      => 'border_color',
				'label'   => __( 'Border Color', 'pagex' ),
				'type'    => 'color',
				'default' => '',
			),
			array(
				'id'      => 'border_color_hover',
				'label'   => __( 'Border Color on Hover', 'pagex' ),
				'type'    => 'color',
				'default' => '',
				'clear'   => true,
			),
			array(
				'id'      => 'box_shadow',
				'label'   => __( 'Box Shadow', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'id'      => 'box_shadow_hover',
				'label'   => __( 'Box Shadow on Hover', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
		);

		$this->make_form( $controls, array( 'design', 'button' ) );
	}

	/**
	 * Form style including inputs, checkbox, select and etc.
	 */
	public function design_forms() {
		$controls = array(
			array(
				'id'      => 'font_size',
				'label'   => __( 'Font Size', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'id'      => 'border_width',
				'label'   => __( 'Border Width', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'id'      => 'border_radius',
				'label'   => __( 'Border Radius', 'pagex' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'id'      => 'min_height',
				'label'   => __( 'Min. Height', 'pagex' ),
				'type'    => 'text',
				'default' => '',
				'clear'   => true,
			),
			array(
				'id'      => 'padding',
				'label'   => __( 'Padding', 'pagex' ),
				'type'    => 'text',
				'default' => '',
				'clear'   => true,
			),
			array(
				'id'      => 'border_color',
				'label'   => __( 'Border Color', 'pagex' ),
				'type'    => 'color',
				'default' => '',
			),
			array(
				'id'      => 'border_color_focus',
				'label'   => __( 'Border Color on Focus', 'pagex' ),
				'type'    => 'color',
				'default' => '',
				'clear'   => true,
			),
			array(
				'id'      => 'checkbox_bg',
				'label'   => __( 'Checkbox Background', 'pagex' ),
				'type'    => 'color',
				'default' => '',
			),
			array(
				'id'      => 'checkbox_checked',
				'label'   => __( 'Checked Input Color', 'pagex' ),
				'type'    => 'color',
				'default' => '',
				'clear'   => true,
			),
		);

		$this->make_form( $controls, array( 'design', 'form' ) );
	}

	/**
	 * Preloader style and activation option
	 */
	public function design_preloader() {
		$controls = array(
			array(
				'id'    => 'active',
				'label' => __( 'Use Preloader', 'pagex' ),
				'type'  => 'checkbox',
				'value' => 'yes',
				'clear' => true,
			),
			array(
				'id'      => 'pages',
				'label'   => __( 'Pages', 'pagex' ),
				'type'    => 'select',
				'options' => array(
					'all'  => __( 'All Pages', 'pagex' ),
					'main' => __( 'Main Page', 'pagex' )
				),
				'clear'   => true,
			),
			array(
				'id'    => 'color',
				'label' => __( 'Color', 'pagex' ),
				'type'  => 'color',
			),
		);

		$this->make_form( $controls, array( 'design', 'preloader' ) );

	}

	/**
	 * Mailchimp Maps API key
	 */
	public function apis_mailchimp() {
		$controls = array(
			array(
				'id'          => 'mailchimp_key',
				'label'       => __( 'MailChimp API Key is required for a "Form" element with "MailChimp" action', 'pagex' ),
				'description' => '<a target="_blank" href="https://admin.mailchimp.com/account/api">' . __( 'Get MailChimp API key', 'pagex' ) . '</a>',
				'type'        => 'text',
			),
		);

		$this->make_form( $controls, array( 'apis' ) );
	}

	/**
	 * Google Maps API key
	 */
	public function apis_google_map() {
		$controls = array(
			array(
				'id'          => 'google_maps',
				'label'       => __( 'Google Maps API Key is required for "Google Maps" element', 'pagex' ),
				'description' => '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">' . __( 'Get Google Maps API key', 'pagex' ) . '</a>',
				'type'        => 'text',
			),
		);

		$this->make_form( $controls, array( 'apis' ) );
	}

	/**
	 * Coming Soon Advanced Tab
	 */
	public function coming_soon() {
		$controls = array(
			array(
				'id'          => 'active',
				'label'       => __( 'Activate coming soon mode', 'pagex' ),
				'description' => __( 'Work on your site in private while visitors see a «Coming Soon» or «Maintenance Mode» page.', 'pagex' ),
				'type'        => 'checkbox',
				'value'       => 'yes',
				'clear'       => true,
			),
			array(
				'id'          => 'id',
				'label'       => '',
				'description' => __( 'Select a layout for coming soon mode', 'pagex' ),
				'type'        => 'select',
				'options'     => pagex_get_layout_templates(),
			),
		);

		$this->make_form( $controls, array( 'coming_soon' ) );
	}

	/**
	 * Multilingual Advanced Tab
	 * Checkbox for multilingual functionality
	 */
	public function advanced_multilingual() {
		$controls = array(
			array(
				'id'          => 'multilingual',
				'label'       => __( 'Add support for multilingual plugins', 'pagex' ),
				'description' => __( 'If you use multilingual plugins like WPML turn on this option to make your content available for translation.', 'pagex' ),
				'type'        => 'checkbox',
				'value'       => 'yes',
				'clear'       => true,
			),
		);

		$this->make_form( $controls, array( 'advanced' ) );
	}

	public function advanced_gdpr() {
		$controls = array(
			array(
				'id'          => 'active',
				'label'       => __( 'Activate GDPR notice', 'pagex' ),
				'description' => __( 'Add European privacy regulations notice', 'pagex' ),
				'type'        => 'checkbox',
				'value'       => 'yes',
				'clear'       => true,
			),
			array(
				'id'          => 'id',
				'label'       => __( 'Privacy and policy page', 'pagex' ),
				'description' => __( 'Select privacy and policy page', 'pagex' ),
				'type'        => 'select',
				'options'     => $this->get_pages(),
			),
			array(
				'id'          => 'message',
				'label'       => __( 'Custom Message', 'pagex' ),
				'description' => __( 'If field is empty the default message will be displayed.', 'pagex' ),
				'type'        => 'text',
			),
		);

		$this->make_form( $controls, array( 'advanced', 'gdpr' ) );
	}

	/**
	 * Export Advanced Tab
	 */
	public function advanced_export() {
		$settings = Pagex::get_settings();
		if ( isset( $settings['demo_data_imported'] ) && $settings['demo_data_imported'] == 'yes' ) {
			$imported = 'yes';
		} else {
			$imported = 'no';
		}

		echo '<input type="hidden" name="pagex_settings[demo_data_imported]" value="' . $imported . '">';
		echo '<button type="button" id="pagex-export-settings" class="button">' . __( 'Export Settings', 'pagex' ) . '</button>';
		echo '<div id="pagex-export-settings-area"></div>';
	}

	/**
	 * Create inputs for admin form based on passed settings
	 *
	 * @param $ar
	 * @param $section
	 *
	 */
	public function make_form( $ar, $section ) {
		$settings = Pagex::get_settings();
		$form     = '<div class="pagex-row">';

		$setting_name = 'pagex_settings[' . implode( '][', $section ) . ']';

		if ( isset( $settings[ $section[0] ] ) ) {
			if ( isset( $section[1] ) && isset( $settings[ $section[0] ][ $section[1] ] ) ) {
				$setting = $settings[ $section[0] ][ $section[1] ];
			} else {
				$setting = $settings[ $section[0] ];
			}
		} else {
			$setting = array();
		}

		foreach ( $ar as $input ) {
			$form .= '<p>';

			if ( $input['type'] != 'checkbox' ) {
				$form .= '<label>' . $input['label'] . '</label>';
			}

			// if it is input or simple label
			if ( isset( $input['id'] ) ) {
				$value = isset( $setting[ $input['id'] ] ) ? $setting[ $input['id'] ] : '';
				$name  = $setting_name . '[' . $input['id'] . ']';
			}

			switch ( $input['type'] ) {
				case 'text' :
				case 'number' :
					$form .= '<input type="' . $input['type'] . '" name="' . $name . '" value="' . $value . '">';
					break;
				case 'color' :
					$form .= '<input type="text" class="clpi" name="' . $name . '" value="' . $value . '">';
					break;
				case 'checkbox':
					$form .= '<label><input type="checkbox" ' . checked( $input['value'], $value, false ) . ' name="' . $name . '" value="' . $input['value'] . '"> ' . $input['label'] . '</label>';
					break;
				case 'select':
					$form .= '<select name="' . $name . '">';
					foreach ( $input['options'] as $key => $val ) {
						if ( is_array( $val ) ) {
							$form .= '<optgroup label="' . $val['label'] . '">';
							// for typography
							foreach ( $val['options'] as $s_key => $s_val ) {
								$form .= "<option value='" . $s_val . "' " . selected( $s_val, $value, false ) . ">" . $s_val . "</option>";
							}
							$form .= '</optgroup>';
						} else {
							$form .= '<option value="' . $key . '" ' . selected( $key, $value, false ) . '>' . $val . '</option>';
						}
					}
					$form .= '</select>';
					break;
			}
			if ( isset( $input['description'] ) ) {
				$form .= '<span class="description">' . $input['description'] . '</span>';
			}
			$form .= '</p>';


			if ( isset( $input['clear'] ) ) {
				$form .= '<div class="pagex-row-clear"></div>';
			}
		}

		$form .= '</div>';

		echo $form;
	}

	/**
	 * Get WP pages
	 */
	public function get_pages() {
		$_pages = get_pages();
		$pages  = array( '' => __( 'None', 'pagex' ) );

		foreach ( $_pages as $page ) {
			$pages[ $page->ID ] = $page->post_title;
		}

		return $pages;
	}

	/**
	 * Export main settings via ajax
	 */
	public function ajax_pagex_export_settings() {
		if ( ! is_super_admin() ) {
			wp_die();
		}

		$settings = Pagex::get_settings();

		// remove api keys
		$settings['apis']['mailchimp_key'] = '';
		$settings['apis']['google_maps']   = '';

		// switch imported option so we can easily install settings for demo import data
		$settings['demo_data_imported'] = 'yes';

		wp_send_json( $settings );
	}

	/**
	 * Synchronize Adobe fonts from remote CSS file
	 */
	public function ajax_pagex_sync_adobe_fonts() {
		if ( ! is_super_admin() ) {
			wp_die();
		}

		$id = isset( $_REQUEST['id'] ) ? trim( $_REQUEST['id'] ) : null;

		if ( ! $id || $id == '' ) {
			wp_die( __( 'ID is not provided', 'pagex' ) );
		}

		$url = 'https://use.typekit.net/' . $id . '.css';

		$remote_response = wp_remote_get( $url );
		$headers         = wp_remote_retrieve_headers( $remote_response );
		if ( ! $headers ) {
			wp_die( __( 'No connection.', 'pagex' ) );
		}

		$remote_response_code = wp_remote_retrieve_response_code( $remote_response );

		if ( $remote_response_code != '200' ) {
			wp_die( __( 'No connection.', 'pagex' ) );
		}

		$body = wp_remote_retrieve_body( $remote_response );

		preg_match_all( '/font-family:"(.*?)"/', $body, $matches );

		$fonts = array();

		if ( ! isset( $matches[1] ) ) {
			wp_die( __( 'No fonts found.', 'pagex' ) );
		}

		foreach ( $matches[1] as $match ) {
			$fonts[ $match ] = $match;
		}

		$response = __( 'The next fonts were added to library:', 'pagex' ) . ' ' . implode( ', ', $fonts );

		foreach ( $fonts as $font ) {
			$response .= '<input type="hidden" name="pagex_settings[design][adobe_font][fonts][]" value="' . $font . '">';
		}

		$response .= '<br>' . __( 'Save changes to apply new settings.', 'pagex' );

		wp_send_json( $response );
	}
}

new Pagex_Admin_Controls();