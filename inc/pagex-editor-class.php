<?php

class Pagex_Editor {
	public function __construct() {
		// register custom post types for builder
		add_action( 'init', array( $this, 'register_post_types' ), 5 );

		// add and manage columns for custom post types
		add_filter( 'manage_pagex_post_tmp_posts_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'manage_columns' ), 10, 2 );

		// register all the editor scripts
		add_action( 'init', array( $this, 'register_scripts' ) );

		// enqueue scripts only when frontend builder is active
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// hide admin bar when frontend builder is active
		add_action( 'init', array( $this, 'hide_admin_bar' ) );

		// add builder active class
		add_filter( 'body_class', array( $this, 'builder_active_class' ) );

		// add editor controls
		add_filter( 'pagex_controls', array( $this, 'controls' ) );

		// replace custom controls like typography, slider or icon with set of default controls
		add_filter( 'pagex_elements', array( $this, 'replace_controls' ), 99 );

		// print all modal forms and element templates
		add_action( 'wp_footer', array( $this, 'print_editor_controls' ) );
		add_action( 'admin_footer', array( $this, 'print_editor_controls' ) );

		// add ajax actions for some editor controls
		add_action( 'init', array( $this, 'pagex_editor_callbacks' ) );

		// add ajax actions and creating shortcodes for all dynamic elements
		add_action( 'init', array( $this, 'pagex_callbacks' ), 9999 );
	}

	/**
	 * Register all post types
	 */
	public static function register_post_types() {
		register_post_type( 'pagex_layout_builder', array(
			'labels'              => array(
				'name'           => __( 'Layout Builder', 'pagex' ),
				'singular_name'  => __( 'Layout Builder', 'pagex' ),
				'menu_name'      => __( 'Layout Builder', 'pagex' ),
				'name_admin_bar' => __( 'Layout', 'pagex' ),
				'add_new'        => __( 'Add Layout', 'pagex' ),
				'add_new_item'   => __( 'Add Layout', 'pagex' ),
				'new_item'       => __( 'New Layout', 'pagex' ),
				'edit_item'      => __( 'Edit Layout', 'pagex' ),
				'view_item'      => __( 'View Layout', 'pagex' ),
				'all_items'      => __( 'Layout Builder', 'pagex' ),
			),
			'public'              => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'query_var'           => true,
			'show_ui'             => true,
			'supports'            => array( 'title' ),
			'show_in_menu'        => 'pagex',
			'hierarchical'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
		) );

		register_post_type( 'pagex_post_tmp', array(
			'labels'              => array(
				'name'           => __( 'Theme Template', 'pagex' ),
				'singular_name'  => __( 'Theme Template', 'pagex' ),
				'menu_name'      => __( 'Theme Template', 'pagex' ),
				'name_admin_bar' => __( 'Theme Template', 'pagex' ),
				'add_new'        => __( 'Add Template', 'pagex' ),
				'add_new_item'   => __( 'Add Template', 'pagex' ),
				'new_item'       => __( 'New Template', 'pagex' ),
				'edit_item'      => __( 'Edit Template', 'pagex' ),
				'view_item'      => __( 'View Template', 'pagex' ),
				'all_items'      => __( 'Theme Templates', 'pagex' ),
			),
			'public'              => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'query_var'           => true,
			'show_ui'             => true,
			'supports'            => array( 'title' ),
			'show_in_menu'        => 'pagex',
			'hierarchical'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
		) );

		register_post_type( 'pagex_excerpt_tmp', array(
			'labels'              => array(
				'name'           => __( 'Excerpt Template', 'pagex' ),
				'singular_name'  => __( 'Excerpt Template', 'pagex' ),
				'menu_name'      => __( 'Excerpt Template', 'pagex' ),
				'name_admin_bar' => __( 'Excerpt Template', 'pagex' ),
				'add_new'        => __( 'Add Layout', 'pagex' ),
				'add_new_item'   => __( 'Add Layout', 'pagex' ),
				'new_item'       => __( 'New Layout', 'pagex' ),
				'edit_item'      => __( 'Edit Layout', 'pagex' ),
				'view_item'      => __( 'View Layout', 'pagex' ),
				'all_items'      => __( 'Excerpt Templates', 'pagex' ),
			),
			'public'              => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'query_var'           => true,
			'show_ui'             => true,
			'supports'            => array( 'title' ),
			'show_in_menu'        => 'pagex',
			'hierarchical'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
		) );
	}

	/**
	 * Add admin columns for pagex_post_tmp post type
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function add_columns( $columns ) {
		unset( $columns['date'] );

		return array_merge( $columns,
			array(
				'pagex_template_type' => __( 'Template Type', 'pagex' ),
				'pagex_post_type'     => __( 'Post Type', 'pagex' )
			) );
	}

	/**
	 * Add values to custom columns of pagex_post_tmp post type
	 *
	 * @param $column
	 * @param $post_id
	 */
	public function manage_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'pagex_template_type':
				echo ucfirst( get_post_meta( $post_id, '_pagex_template_type', true ) );
				break;

			case 'pagex_post_type':
				echo ucfirst( get_post_meta( $post_id, '_pagex_template_post_type', true ) );
				break;
		}
	}

	/**
	 * Registers all the editor scripts and styles
	 */
	public function register_scripts() {
		wp_register_script( 'serializejson', PAGEX_PLUGIN_URL . 'assets/js/jquery.serializejson.min.js', array( 'jquery' ), '2.9.0', true );
		wp_register_script( 'pagex-color-picker', PAGEX_PLUGIN_URL . 'assets/js/color-picker.js', array(), PAGEX_VERSION, true );
		wp_register_script( 'tippy', PAGEX_PLUGIN_URL . 'assets/js/tippy.all.min.js', array(), '2.4.4', true );

		// main scripts for builder area
		wp_register_script( 'pagex-builder', PAGEX_PLUGIN_URL . 'assets/js/builder-editor.js', array(
			'jquery',
			'jquery-ui-sortable',
			'serializejson',
			'underscore',
			'tippy',
		), PAGEX_VERSION, true );

		// main scripts for admin settings page
		wp_register_script( 'pagex-admin-page', PAGEX_PLUGIN_URL . 'assets/js/admin/admin.js', array(
			'jquery',
			'pagex-color-picker',
		), PAGEX_VERSION, true );

		// scripts for iFrame holder
		wp_register_script( 'pagex-builder-form', PAGEX_PLUGIN_URL . 'assets/js/builder-editor-form.js', array(
			'jquery',
			'jquery-ui-draggable',
			'underscore',
			'pagex-color-picker',
		), PAGEX_VERSION, true );

		// we need font awesome only for frontend builder
		// after saving all fa icons will be replaced with SVG
		wp_register_style( 'fontawesome', PAGEX_PLUGIN_URL . 'assets/css/fontawesome.css', array(), '5.2.0' );

		wp_register_style( 'pagex-backend', PAGEX_PLUGIN_URL . 'assets/css/pagex-backend.css', array(), PAGEX_VERSION );
		wp_register_style( 'pagex-admin-page', PAGEX_PLUGIN_URL . 'assets/css/pagex-admin.css', array(), PAGEX_VERSION );
		wp_register_style( 'pagex-builder', PAGEX_PLUGIN_URL . 'assets/css/pagex-builder.css', array(
			'fontawesome',
		), PAGEX_VERSION );
	}

	/**
	 * Enqueue all registered editor scripts and styles
	 */
	public function enqueue_scripts() {
		if ( ! ( Pagex::is_frontend_builder_frame_active() || Pagex::is_frontend_builder_active() ) ) {
			return;
		}

		// add scripts only for builder iFrame holder
		if ( ! Pagex::is_frontend_builder_frame_active() ) {
			wp_enqueue_media();
			wp_enqueue_script( 'pagex-builder-form' );
			wp_enqueue_style( 'pagex-builder' );

			return;
		}

		wp_enqueue_style( 'media', admin_url( '/css/media.css' ) );
		wp_enqueue_style( 'pagex-builder' );
		wp_enqueue_script( 'pagex-builder' );

		Pagex_Editor::localizeEditorScript();
	}

	/**
	 * Add global js variables with all saved layout params and builder settings
	 */
	public static function localizeEditorScript() {
		global $post;

		$settings = Pagex::get_settings();
		$subsets  = isset( $settings['design']['google_fonts_subsets'] ) ? array_keys( $settings['design']['google_fonts_subsets'] ) : array();

		$all_elements_params = $post ? get_post_meta( $post->ID, '_pagex_elements_params', true ) : '';
		$all_elements_params = $all_elements_params ? $all_elements_params : '{}';

		// preview link with settings for a frontend builder
		$preview_settings = $post ? add_query_arg( array(
			'pagex'               => '',
			'pagex-query-preview' => array(
				'post_type'      => $post->post_name,
				'page'           => '',
				'name'           => $post->post_name,
				$post->post_name => $post->post_name
			),
			'pagex-exit-link'     => admin_url( 'post.php?post=' . $post->ID . '&action=edit' ),
		), get_permalink( $post->ID ) ) : '';

		wp_localize_script( 'pagex-builder', 'pagexLocalize',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'post_id'    => get_the_ID(),
				'all_params' => json_decode( $all_elements_params ),
				'settings'   => array(
					'subsets' => $subsets
				),
				'front_link' => $preview_settings,
				'string'     => array(
					'save'                => esc_html__( 'Save', 'pagex' ),
					'saving'              => esc_html__( 'Saving...', 'pagex' ),
					'import'              => esc_html__( 'Import Layout', 'pagex' ),
					'importing'           => esc_html__( 'Importing...', 'pagex' ),
					'clear_layout'        => esc_html__( 'Delete all layout elements?', 'pagex' ),
					'edit_with_wordpress' => esc_html__( 'Back to WordPress Editor', 'pagex' ),
					'edit_with_frontend'  => esc_html__( 'Frontend Builder', 'pagex' ),
					'edit_with_pagex'     => esc_html__( 'Edit with Pagex', 'pagex' ),
				)
			)
		);
	}

	/**
	 * Hide admin bar when builder is active
	 */
	public function hide_admin_bar() {
		if ( Pagex::is_frontend_builder_active() || Pagex::is_frontend_builder_frame_active() || isset( $_REQUEST['pagex-excerpt-preview'] ) ) {
			show_admin_bar( false );
		}
	}


	/**
	 * Add builder active classes
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function builder_active_class( $classes ) {
		if ( Pagex::is_frontend_builder_active() ) {
			$classes[] = 'pagex-builder-active';
		}

		if ( Pagex::is_frontend_builder_frame_active() ) {
			$classes[] = 'pagex-builder-frame-active';
		}

		if ( Pagex::is_excerpt_preview_frame_active() ) {
			$classes[] = 'pagex-excerpt-preview-frame';
		}

		return $classes;
	}

	/**
	 * All default controls for builder including element control options and layout template for builder editor form
	 *
	 * @param $controls array with all controls added via pagex_controls filter
	 *
	 * @return array
	 */
	public function controls( $controls ) {
		// Option controls
		$controls['element-options'] = '<div class="pagex-options pagex-element-options btn-group btn-group-sm"><button type="button" class="btn pagex-option-elname"></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Edit Element', 'pagex' ) . '" data-edit="element"><i class="fas fa-pen"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Clone Element', 'pagex' ) . '" data-clone="element"><i class="fas fa-clone"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Remove Element', 'pagex' ) . '" data-remove="element"><i class="fas fa-trash"></i></button></div>';

		$controls['column-options'] = '<div class="pagex-options pagex-column-options btn-group btn-group-sm"><button type="button" class="btn pagex-tooltip" title="' . __( 'Edit Column', 'pagex' ) . '" data-edit="column"><i class="fas fa-pen"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Append New Element', 'pagex' ) . '" data-add="element"><i class="fas fa-plus-circle"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Clone Column', 'pagex' ) . '" data-clone="column"><i class="fas fa-clone"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Remove Column', 'pagex' ) . '" data-remove="column"><i class="fas fa-trash"></i></button><button type="button" class="btn pagex-options-toggle pagex-tooltip" title="' . __( 'Hide/Show Options', 'pagex' ) . '"></button></div>';

		$controls['add-element-options'] = '<div class="pagex-options pagex-add-element-options btn-group btn-group-sm"><button type="button" class="btn pagex-tooltip" title="' . __( 'Add Element', 'pagex' ) . '" data-add="element"><i class="fas fa-plus-circle"></i></button></div>';

		$controls['section-options'] = '<div class="pagex-options pagex-section-controls btn-group btn-group-sm"><div class="btn pagex-section-options pagex-section-set">' . __( 'Section', 'pagex' ) . '</div><div class="btn-group pagex-section-options btn-group-sm pagex-hide"><button type="button" class="btn pagex-tooltip" title="' . __( 'Edit Section', 'pagex' ) . '" data-edit="section"><i class="fas fa-pen"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Append New Container', 'pagex' ) . '" data-add="container"><i class="fas fa-plus-circle"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Clone Section', 'pagex' ) . '" data-clone="section"><i class="fas fa-clone"></i></button><button type="button" class="btn pagex-tooltip pagex-save-custom-layout-modal" title="' . __( 'Save as Layout', 'pagex' ) . '"><i class="fas fa-save"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Remove Section', 'pagex' ) . '" data-remove="section"><i class="fas fa-trash"></i></button></div><div class="btn pagex-container-options pagex-section-set">' . __( 'Container', 'pagex' ) . '</div><div class="btn-group pagex-container-options btn-group-sm pagex-hide"><button type="button" class="btn pagex-tooltip" title="' . __( 'Edit Container', 'pagex' ) . '" data-edit="container"><i class="fas fa-pen"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Append New Row', 'pagex' ) . '" data-add="row"><i class="fas fa-plus-circle"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Clone Container', 'pagex' ) . '" data-clone="container"><i class="fas fa-clone"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Remove Container', 'pagex' ) . '" data-remove="container"><i class="fas fa-trash"></i></button></div><div class="btn pagex-row-options pagex-section-set">' . __( 'Row', 'pagex' ) . '</div><div class="btn-group pagex-row-options btn-group-sm pagex-hide"><button type="button" class="btn pagex-tooltip" title="' . __( 'Edit Row', 'pagex' ) . '" data-edit="row"><i class="fas fa-pen"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Append New Column', 'pagex' ) . '" data-add="column"><i class="fas fa-plus-circle"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Clone Row', 'pagex' ) . '" data-clone="row"><i class="fas fa-clone"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Remove Row', 'pagex' ) . '" data-remove="row"><i class="fas fa-trash"></i></button></div><div class="btn pagex-options-toggle pagex-tooltip" title="' . __( 'Hide/Show Options', 'pagex' ) . '"></div></div>';

		$controls['inner-row'] = '<div class="pagex-options pagex-inner-row-options btn-group btn-group-sm"><div class="btn pagex-section-set">' . __( 'Inner Row', 'pagex' ) . '</div><div class="btn-group pagex-inner-row-options btn-group-sm pagex-hide"><button type="button" class="btn pagex-tooltip" title="' . __( 'Edit Inner Row', 'pagex' ) . '" data-edit="inner-row"><i class="fas fa-pen"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Append New Column', 'pagex' ) . '" data-add="column"><i class="fas fa-plus-circle"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Clone Inner Row', 'pagex' ) . '" data-clone="inner-row"><i class="fas fa-clone"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Remove Inner Row', 'pagex' ) . '" data-remove="inner-row"><i class="fas fa-trash"></i></button></div></div>';

		$controls['section-add-new'] = '<div class="pagex-options pagex-section-add btn-group btn-group-sm"><button type="button" class="btn pagex-tooltip" title="' . __( 'Add Section', 'pagex' ) . '" data-add="section"><i class="fas fa-plus-circle"></i></button><button type="button" class="btn pagex-tooltip" title="' . __( 'Add Layout', 'pagex' ) . '" data-add="layout"><i class="fas fa-layer-group"></i></button></div>';

		// Element controls

		$controls['element-start'] = '<div class="element" data-id="<%= pagex.genID() %>" data-type="<%- data.id %>" <% if(data.callback) { %>data-callback="<%- data.callback %>"<% } %>><div class="element-wrap">';
		$controls['element-end']   = '</div></div>';

		$controls['option-start'] = '<div class="pagex-control-wrapper <%- data.class ? data.class : "col-12"%> <%- data.hidden ? "pagex-hide" : ""%> <% if (data.action) { print("pagex-control-action-type-" + data.action) } %>" <% if (!_.isUndefined(data.condition)) { %>data-condition="<%- JSON.stringify(data.condition) %>"<% } %>><div class="form-group"><% data.title ? print("<label>"+data.title+"</label>") : "" %><% if (!_.isUndefined(data.responsive) || data.type == "dimension") { %><div class="pagex-responsive-switchers"><div class="d-inline ml-1"><span class="pagex-responsive-switcher-button badge badge-secondary trn-300"><i class="far fa-window-restore"></i></span></div></div> <% } %>';
		$controls['option-end']   = '<small class="form-text text-muted"><%= data.description %></small></div></div>';

		$controls['responsive-switcher-options'] = '<div class="pagex-responsive-switchers-options"><div class="dropdown-menu d-block"><li class="dropdown-item pagex-device-switcher pagex-device-default active" data-device-switcher="default"><i class="far fa-window-restore"></i><span>' . __( 'Default', 'pagex' ) . '</span><small>' . __( 'No media query', 'pagex' ) . '</small></li><li class="dropdown-item pagex-device-switcher pagex-device-xs" data-device-switcher="xs"><i class="fas fa-mobile-alt"></i><span>' . __( 'Mobile', 'pagex' ) . ' <sup>xs</sup></span><small>' . __( 'No media query', 'pagex' ) . '</small></li><li class="dropdown-item pagex-device-switcher pagex-device-sm" data-device-switcher="sm"><i class="fas fa-mobile-alt fa-rotate-90"></i><span>' . __( 'Mobile (landscape)', 'pagex' ) . ' <sup>sm</sup></span><small>≥ 576px</small></li><li class="dropdown-item pagex-device-switcher pagex-device-md" data-device-switcher="md"><i class="fas fa-tablet-alt"></i><span>' . __( 'Tablet', 'pagex' ) . ' <sup>md</sup></span><small>≥ 768px</small></li><li class="dropdown-item pagex-device-switcher pagex-device-lg" data-device-switcher="lg"><i class="fas fa-tablet-alt fa-rotate-90"></i><span>' . __( 'Tablet (landscape)', 'pagex' ) . ' <sup>lg</sup></span><small>≥ 992px</small></li><li class="dropdown-item pagex-device-switcher pagex-device-xl" data-device-switcher="xl"><i class="fas fa-desktop"></i><span>' . __( 'Desktop', 'pagex' ) . ' <sup>xl</sup></span><small>≥1200px</small></li> <li class="dropdown-item pagex-device-switcher pagex-device-all" data-device-switcher="all"><i class="fas fa-desktop"></i><span>' . __( 'Show/hide all options ', 'pagex' ) . '</span></li></div></div>';

		// for complex form layouts
		$controls['row-start'] = '<div class="form-row <%- data.class ? data.class : "col-12"%>" <% if (!_.isUndefined(data.condition)) { %>data-condition="<%- JSON.stringify(data.condition) %>"<% } %>>';
		$controls['row-end']   = '</div>';

		$controls['heading'] = '<div class="col-12 pagex-form-heading-control" <% if (!_.isUndefined(data.condition)) { %>data-condition="<%- JSON.stringify(data.condition) %>"<% } %>><%- data.title %><small class="form-text text-muted"><%- data.description %></small></div>';

		// clear type
		$controls['clear'] = '<div class="col-12"></div>';

		// repeater
		$controls['repeater']            = ''; // we need empty repeater control to prevent JS errors
		$controls['repeater-start']      = '<div class="pagex-control-wrapper pagex-repeater col-12 mb-3" <% if (!_.isUndefined(data.condition)) { %>data-condition="<%- JSON.stringify(data.condition) %>"<% } %>><div class="pagex-repeater-items">';
		$controls['repeater-end']        = '</div><button type="button" class="btn pagex-add-repeater-item"><i class="fas fa-plus-circle mr-2"></i>' . __( 'Add Item', 'pagex' ) . '</button></div>';
		$controls['repeater-item-start'] = '<div class="pagex-repeater-item mb-2"><div class="pagex-repeater-tools d-flex align-items-center"><div class="pagex-repeater-title">Item</div><div class="pagex-repeater-clone pagex-repeater-button trn-300 ml-auto"><i class="fas fa-clone"></i></div><div class="pagex-repeater-remove pagex-repeater-button trn-300 ml-3"><i class="fas fa-trash"></i></div></div><div class="pagex-repeater-controls form-row align-items-start">';
		$controls['repeater-item-end']   = '</div></div>';

		// standard controls for form params
		$controls['textarea'] = '<textarea class="pagex-control-field form-control" name="<% print(data.id); if(data.action == "content") {print(":skip")} %>"></textarea>';

		// color is simple color picker; background control can create gradients
		$controls['color']      = '<div class="pagex-clpi"><input type="text" class="pagex-control-field pagex-control-option-color clpi form-control" name="<%= data.id %>"></div>';
		$controls['background'] = '<div class="pagex-clpi"><input type="text" class="pagex-control-field pagex-control-option-background clpi clpi-gradient form-control" name="<%= data.id %>"></div>';

		$controls['url'] = '<div class="input-group"><input type="text" class="pagex-control-field form-control pagex-control-url" name="<%- data.id %>"><div class="input-group-append"><button type="button" class="btn btn-outline-secondary pagex-url-control-insert">' . __( ' Media Gallery', 'pagex' ) . '</button></div></div>';

		$controls['text'] = '<% if ( _.isUndefined(data.responsive) ) { %><input type="text" class="pagex-control-field form-control" name="<% print(data.id); if(data.action == "content") {print(":skip")} %>"><% } else { _.forEach(["xs", "sm", "md", "lg", "xl"], function(pref) { %><div class="pagex-responsive-params pagex-device-<%- pref %> <%- pref == "xs" ? "active pagex-device-default" : "" %>"><input type="text" class="pagex-control-field form-control" placeholder="<%- pref == "xs" ? "' . __( 'Default', 'pagex' ) . '" : "' . __( 'Inherit', 'pagex' ) . '" %>" name="<%- data.id %>[<%- pref %>]"></div><%})} %>';

		// number must not react on keyup events only on change since browser controls (increase/decrease) trigger change event also it prevents from duplicated params during fast changing the current element
		// for number can be specified attributes as a sting with min. max. and step
		$controls['number'] = '<% if ( _.isUndefined(data.responsive) ) { %><input type="number" class="pagex-control-field pagex-control-option form-control" name="<%- data.id %>" <%= data.attributes %>><% } else { _.forEach(["xs", "sm", "md", "lg", "xl"], function(pref) { %><div class="pagex-responsive-params pagex-device-<%- pref %> <%- pref == "xs" ? "active pagex-device-default" : "" %>"><input type="number" class="pagex-control-option form-control" placeholder="<%- pref == "xs" ? "' . __( 'Default', 'pagex' ) . '" : "' . __( 'Inherit', 'pagex' ) . '" %>" name="<%- data.id %>[<%- pref %>]"></div><%})} %>';

		// checkbox can have specified value or "true" as a default one
		// checkbox can have a label field
		$controls['checkbox'] = '<div><label class="m-0"><input class="pagex-control-option" type="checkbox" value="<%- data.value ? data.value : true  %>" name="<%- data.id %>"> <span><%- data.label %></span></label></div>';

		// image control can have sizes param which will print a select with wordpress image sizes
		// {param_id}_data will store all image data like alt, caption, sizes, link and id
		$controls['image'] = '<div class="pagex-image-control"><div class="pagex-image-placeholder mb-2"><div class="pagex-image-delete trn-300">' . __( 'Delete', 'pagex' ) . '</div></div><div class="input-group"><input class="pagex-control-option form-control pagex-image-url" placeholder="URL" type="text" name="<%- data.id %>"><% if (data.sizes != false) { %><select class="pagex-control-option pagex-image-sizes custom-select col-4" name="<%= data.id %>_size"><option value="full">' . __( 'Full size', 'pagex' ) . '</option><option value="large">' . __( 'Large', 'pagex' ) . '</option><option value="medium">' . __( 'Medium', 'pagex' ) . '</option><option value="thumbnail">' . __( 'Thumbnail', 'pagex' ) . '</option></select><% } %></div><input class="pagex-control-option form-control pagex-image-data pagex-hide" type="text" name="<%- data.id %>_data"></div>';

		// dimension control is always responsive
		$controls['dimension'] = '<% var opt = ["top","right","bottom","left"]; _.forEach({"xs": opt, "sm": opt, "md": opt, "lg": opt, "xl": opt}, function(options, key) { %> <div class="pagex-responsive-params pagex-device-<%- key %> <%- key == "xs" ? "active pagex-device-default" : "" %>"><div class="input-group"><% _.forEach(options, function(pos) { %><input type="text" placeholder="-" class="pagex-control-field form-control" name="<%- data.id %>[<%- key %>][<%- pos %>]"><% }); %></div></div><% }); %><div class="d-flex"><small class="text-muted flex-fill text-center">' . __( 'Top', 'pagex' ) . '</small><small class="text-muted flex-fill text-center">' . __( 'Right', 'pagex' ) . '</small><small class="text-muted flex-fill text-center">' . __( 'Bottom', 'pagex' ) . '</small><small class="text-muted flex-fill text-center">' . __( 'Left', 'pagex' ) . '</small></div>';

		// link control has hidden field with id of parameter. This field will have the string with all attributes like href/target and etc.
		// link can have dynamic href based on custom post meta
		$controls['link'] = '<input class="pagex-control-field pagex-link-control pagex-hide" type="text" name="<%- data.id %>"><div class="mb-3"><div class="input-group"><input data-condition=\'{"<%- data.id %>-dynamic":[""]}\' class="pagex-control-field form-control pagex-link-control-field pagex-link-control-href" type="text" placeholder="Url" name="<%- data.id %>-href"><select class="custom-select pagex-control-option pagex-link-control-field pagex-link-control-dynamic" name="<%- data.id %>-dynamic">' . pagex_get_dynamic_url_keys() . '</select><div class="input-group-append"><button type="button" data-condition=\'{"<%- data.id %>-dynamic":[""]}\' class="btn btn-outline-secondary pagex-link-control-search">' . __( 'Search', 'pagex' ) . '</button><button type="button" class="btn btn-outline-secondary pagex-link-show-attrs"><i class="fas fa-cog"></i></button></div></div><div class="pagex-link-search-results"></div></div><div class="form-row align-items-center pagex-link-attrs pagex-hide"><div class="col-3"><input class="pagex-control-field form-control pagex-link-control-field pagex-link-control-onclick" type="text" placeholder="Onclick" name="<%- data.id %>-onclick"></div><div class="col-2"><input class="pagex-control-field form-control pagex-link-control-field pagex-link-control-title" type="text" placeholder="' . __( 'Title', 'pagex' ) . '" name="<%- data.id %>-title"></div><div class="col-auto"><div><label class="m-0"><input class="pagex-control-option pagex-link-control-field pagex-link-control-rel" type="checkbox" value="pagex-checkbox-true" name="<%- data.id %>-rel"> <span>Nofollow</span></label></div></div><div class="col-auto"><div><label class="m-0"><input class="pagex-control-option pagex-link-control-field pagex-link-control-target" type="checkbox" value="pagex-checkbox-true" name="<%- data.id %>-target"> ' . __( 'Open in a new tab', 'pagex' ) . '</label></div></div></div>';


		$controls['iconpicker'] = '<select class="pagex-control-option custom-select pagex-control-iconpicker" name="<%= data.id %>"><% _.forEach(data.options, function(option, k) { %><option value="<%= k %>"><%= option %></option><% }); %></select>';

		// select accept associative array with options
		// if select is not responsive it can accept simple array
		// if select is responsive it can accept [pref] key in options which will be replaced by responsive prefix also responsive prefixes can be used as keys for select options with another associative array of options
		$controls['select'] = '<% if ( _.isUndefined(data.responsive) || data.responsive == "false" ) { %><select class="pagex-control-option custom-select" name="<%= data.id %>" <% if(data.multiple) {print("multiple")} %>><% _.forEach(data.options, function(option, k) { %><% if (_.isObject(option)) { %><optgroup label="<%- option.label %>"><% _.forEach(option.options, function(option, k) { %><option value="<% if (_.isNumber(k)) { print(option) } else { print(k) } %>"><%- option %></option><% }); %></optgroup><% } else { %><option value="<% if (_.isNumber(k)) { print(option) } else { print(k) } %>"><%- option %></option><% } %><% }); %></select><% } else { _.forEach(["xs", "-sm-", "-md-", "-lg-", "-xl-"], function(pref) { %><div class="pagex-responsive-params pagex-device-<%- pref.replace(new RegExp("-", "g"), "") %> <%- pref == "xs" ? "active pagex-device-default" : "" %>"><select class="pagex-control-option custom-select" name="<%= data.id %>[<%- pref.replace(new RegExp("-", "g"), "") %>]"><% if (_.isObject(data.options.xs)) { _.forEach(data.options[pref.replace(new RegExp("-", "g"), "")], function(optionValue, optionKey) { %><option value="<%- optionKey %>"><%- optionValue %></option><% });} else { _.forEach(data.options, function(option, k) { %><option value="<% var value = ""; if (pref == "xs") { value = k.replace(new RegExp("\\\[pref\\\]", "g"), "-") } else { value = k.replace(new RegExp("\\\[pref\\\]", "g"), pref) } if (value.indexOf("-", value.length - 1) !== -1 ) { print(value.slice(0, -1)) } else { print(value) } %>"><%- option %></option><% })} %></select></div><% }); } %>';

		return $controls;
	}

	/**
	 * Replace custom controls by numbers of predefined controls
	 *
	 * @param $elements
	 *
	 * @return array
	 */
	public function replace_controls( $elements ) {
		// replace button_style
		$elements = $this->replace_button_style_type( $elements );

		// replace typography by set of controls
		$elements = $this->replace_typography_type( $elements );

		// replace icon by set of controls
		$elements = $this->replace_icon_type( $elements );

		// replace icon type in repeater;
		// replace it separately from control attributes since icon in repeater will have no css styling options
		$elements = $this->replace_repeater_icon_type( $elements );

		// replace slider by set of controls
		$elements = $this->replace_slider_type( $elements );

		// add parent key to repeater params
		foreach ( $elements as $element_key => $element ) {
			foreach ( $element['options'] as $option_key => $params ) {
				foreach ( $params['params'] as $param_key => $param ) {
					if ( isset( $param['type'] ) && $param['type'] == 'repeater' ) {
						foreach ( $param['params'] as $k => $v ) {
							if ( isset( $v['id'] ) ) {
								$elements[ $element_key ]['options'][ $option_key ]['params'][ $param_key ]['params'][ $k ]['id'] = $param['id'] . '[][]' . $v['id'];

								if ( isset( $elements[ $element_key ]['options'][ $option_key ]['params'][ $param_key ]['params'][ $k ]['condition'] ) ) {
									$new_condition = array();
									foreach ( $elements[ $element_key ]['options'][ $option_key ]['params'][ $param_key ]['params'][ $k ]['condition'] as $cond_key => $cond_val ) {
										$new_condition[ $param['id'] . '[][]' . $cond_key ] = $cond_val;
									}
									if ( $new_condition ) {
										$elements[ $element_key ]['options'][ $option_key ]['params'][ $param_key ]['params'][ $k ]['condition'] = $new_condition;
									}
								}
							}
						}
					}
				}
			}
		}

		return $elements;
	}

	/**
	 * Replace typography type by numbers of predefined controls
	 *
	 * @param $elements
	 *
	 * @return array - with all elements
	 */
	public function replace_typography_type( $elements ) {
		foreach ( $elements as &$element ) {
			foreach ( $element['options'] as &$params ) {
				$new_options = array();
				foreach ( $params['params'] as $param ) {
					if ( isset( $param['type'] ) && $param['type'] == 'typography' ) {
						$typography_options = Pagex_Editor_Control_Attributes::get_typography_control( $param['id'], isset( $param['selector'] ) ? $param['selector'] : '' );
						$new_options        = array_merge( $new_options, $typography_options );
					} else {
						$new_options[] = $param;
					}
				}
				$params['params'] = $new_options;
			}
		}

		return $elements;
	}

	/**
	 * Replace custom icon type by numbers of predefined controls
	 *
	 * @param $elements
	 *
	 * @return array - with all elements
	 */
	public function replace_icon_type( $elements ) {
		foreach ( $elements as &$element ) {
			foreach ( $element['options'] as &$params ) {
				$new_options = array();
				foreach ( $params['params'] as $param ) {
					if ( isset( $param['type'] ) && $param['type'] == 'icon' ) {
						$icon_options = Pagex_Editor_Control_Attributes::get_icon_control( $param['id'], isset( $param['selector'] ) ? $param['selector'] : '', $element['type'] == 'dynamic' );
						$new_options  = array_merge( $new_options, $icon_options );
					} else {
						$new_options[] = $param;
					}
				}
				$params['params'] = $new_options;
			}
		}

		return $elements;
	}

	/**
	 * Replace custom icon type in repeater by numbers of predefined controls
	 * Repeater controls do not need to have css options so we apply different controls rather than standard icon_type controls
	 *
	 * @param $elements
	 *
	 * @return array - with all elements
	 */
	public function replace_repeater_icon_type( $elements ) {
		foreach ( $elements as &$element ) {
			foreach ( $element['options'] as &$params ) {
				foreach ( $params['params'] as &$param ) {
					if ( isset( $param['type'] ) && $param['type'] == 'repeater' ) {
						$new_options = array();
						foreach ( $param['params'] as $v ) {
							if ( isset( $v['type'] ) && $v['type'] == 'icon' ) {
								$icon_options = Pagex_Editor_Control_Attributes::get_icon_control( $v['id'], isset( $v['selector'] ) ? $v['selector'] : false, $element['type'] == 'dynamic', false );
								$new_options  = array_merge( $new_options, $icon_options );
							} else {
								$new_options[] = $v;
							}
						}
						$param['params'] = $new_options;
					}
				}
			}
		}

		return $elements;
	}

	/**
	 * Replace custom slider type by numbers of predefined controls
	 *
	 * @param $elements
	 *
	 * @return array - with all elements
	 */
	public function replace_slider_type( $elements ) {
		foreach ( $elements as &$element ) {
			foreach ( $element['options'] as &$params ) {
				$new_options = array();
				foreach ( $params['params'] as $param ) {
					if ( isset( $param['type'] ) && $param['type'] == 'slider' ) {
						$icon_options = Pagex_Editor_Control_Attributes::get_slider_params();
						$new_options  = array_merge( $new_options, $icon_options );
					} else {
						$new_options[] = $param;
					}
				}
				$params['params'] = $new_options;
			}
		}

		return $elements;
	}

	/**
	 * Replace button_style type by numbers of predefined controls
	 *
	 * @param $elements
	 *
	 * @return array - with all elements
	 */
	public function replace_button_style_type( $elements ) {
		foreach ( $elements as &$element ) {
			foreach ( $element['options'] as &$params ) {
				$new_options = array();
				foreach ( $params['params'] as $param ) {
					if ( isset( $param['type'] ) && $param['type'] == 'button_style' ) {
						$icon_options = Pagex_Editor_Control_Attributes::get_button_style( $param['id'], $param['selector'] );
						$new_options  = array_merge( $new_options, $icon_options );
					} else {
						$new_options[] = $param;
					}
				}
				$params['params'] = $new_options;
			}
		}

		return $elements;
	}

	/**
	 * Print all modal forms for editor like params form and all element select
	 * Print JS with global var to access all elements
	 */
	public function print_editor_controls() {

		global $post;

		// might happened when custom post type is not save right
		if ( ! $post ) {
			return;
		}

		$settings = Pagex::get_settings();

		if ( is_admin() ) {
			$current_screen = get_current_screen();
			if ( $current_screen->post_type == '' || $current_screen->base == 'edit' || $current_screen->base == 'upload' ) {
				return;
			}

			if ( ! isset( $settings['builder'][ $current_screen->post_type ] ) ) {
				return;
			}
		} elseif ( ! ( Pagex::is_frontend_builder_active() || Pagex::is_frontend_builder_frame_active() ) ) {
			return;
		}

		$controls   = apply_filters( 'pagex_controls', array() );
		$categories = apply_filters( 'pagex_categories', array() );
		$elements   = apply_filters( 'pagex_elements', array() );

		if ( is_admin() || Pagex::is_frontend_builder_active() ) {
			echo '<div id="pagex-control-responsive-switcher" class="pagex-hide">' . $controls['responsive-switcher-options'] . '</div>';
		}

		if ( is_admin() || Pagex::is_frontend_builder_frame_active() ) {
			// print style
			$element_names_style = '';
			foreach ( $elements as $element ) {
				$element_names_style .= '.element[data-type="' . $element['id'] . '"] > .pagex-options .pagex-option-elname:before {content: "' . $element['title'] . '"}';
			}
			echo '<style>' . $element_names_style . '</style>';

			// print all controls
			foreach ( $controls as $key => $control ) {
				echo '<script type="text/template" id="pagex-control-' . $key . '-template">' . $control . '</script>';
			}

			// print all element templates
			foreach ( $elements as $key => $element ) {
				if ( isset( $element['template'] ) ) {
					echo '<script type="text/template" id="pagex-element-' . $element['id'] . '-template">' . $element['template'] . '</script>';
					unset( $elements[ $key ]['template'] );
				}
			}
		}

		// remove all elements which are not related to current post type
		foreach ( $elements as $key => $element ) {
			if ( isset( $element['post_type'] ) ) {
				if ( ! in_array( $post->post_type, $element['post_type'] ) ) {
					unset( $elements[ $key ] );
				}
			}
		}

		// validate titles for tabs and add design and advanced tab for all elements
		foreach ( $elements as $element_key => $element ) {
			foreach ( $element['options'] as $option_key => $params ) {
				// set basic tab title if it is not presented in options
				if ( ! isset( $params['title'] ) ) {
					$elements[ $element_key ]['options'][ $option_key ]['title'] = __( 'Basic Options', 'pagex' );
				}
			}

			// add design tab
			$elements[ $element_key ]['options'][] = Pagex_Editor_Control_Attributes::get_design_params();

			// add advanced tab
			$elements[ $element_key ]['options'][] = Pagex_Editor_Control_Attributes::get_advanced_params();
		}

		// create global variable with all elements only for builder iFrame or admin footer
		if ( is_admin() || Pagex::is_frontend_builder_frame_active() ) {
			echo '<script>var pagexElements = ' . json_encode( $elements ) . '</script>';
		}

		$fa_icons = Pagex_Editor_Control_Attributes::get_font_awesome();

		$icons_modal = '<div id="pagex-icons-modal" class="pagex-main-modal-window pagex-params-modal pagex-hide"><div class="pagex-params-modal-head"><div class="pagex-all-elements-modal-title">' . __( 'Icons', 'pagex' ) . '</div><div class="pagex-params-modal-controls"><div class="input-group"><input type="text" id="pagex-search-icons" placeholder="' . __( 'Search icons', 'pagex' ) . '" class="form-control pagex-modal-search-input"><div class="input-group-append"><i class="fas fa-search input-group-text"></i></div></div><div class="pagex-params-modal-close trn-300 ml-4" ><i class="fas fa-times"></i></div></div></div><div class="pagex-params-tab-content">';

		foreach ( $fa_icons as $key => $name ) {
			$icons_modal .= '<i class="pagex-iconpicker-icon ' . $key . '" data-iconpicker="' . $key . '"></i>';
		}

		$icons_modal .= '</div></div>';

		// save layout modal
		$save_layouts_modal = '<div id="pagex-save-layouts-modal" class="pagex-main-modal-window pagex-params-modal pagex-hide"><div class="pagex-params-modal-head"><div class="pagex-all-elements-modal-title">' . __( 'Save Custom Layout', 'pagex' ) . '</div><div class="pagex-params-modal-controls"><div class="pagex-params-modal-close"><i class="fas fa-times"></i></div></div></div><div class="pagex-params-tab-content"><div class="input-group mt-2 mb-1"><input type="text" name="pagex-custom-layout-title" id="pagex-custom-layout-title" class="form-control" placeholder="' . __( 'Enter Layout Name', 'pagex' ) . '"><div class="input-group-append"><button type="button" class="btn btn-outline-secondary pagex-params-button pagex-save-custom-layout"><i class="fas fa-save"></i><span>' . __( 'Save Layout', 'pagex' ) . '</span></button></div></div><p>' . __( 'Layout will be saved in Layout Builder Library.', 'pagex' ) . ' <a href="' . admin_url( 'edit.php?post_type=pagex_layout_builder' ) . '" target="_blank">' . __( 'Layout Builder', 'pagex' ) . '</a></p></div></div>';

		// modal with layouts, pages and templates
		$layouts_modal = '<div id="pagex-layouts-modal" class="pagex-main-modal-window pagex-params-modal pagex-hide"><div class="pagex-params-modal-head"><div class="pagex-all-elements-modal-title">' . __( 'Library', 'pagex' ) . '</div><div class="pagex-params-modal-controls"><div class="pagex-params-modal-close trn-300 ml-4" ><i class="fas fa-times"></i></div></div></div>';

		// tabs titles with a list of all created builder layouts
		$layouts_modal .= '<div class="pagex-params-tabs d-flex"><div class="pagex-params-tab-title active">' . __( 'All Created', 'pagex' ) . '</div>';

		$layouts_modal .= '<div class="pagex-params-tab-title pagex-layouts-modal-layouts">' . __( 'Layouts', 'pagex' ) . '</div>';

		// theme templates layout library
		if ( $post->post_type == 'pagex_post_tmp' ) {
			$layouts_modal .= '<div class="pagex-params-tab-title pagex-layouts-modal-templates">' . __( 'Theme Templates', 'pagex' ) . '</div>';
		}

		// excerpt layout library
		if ( $post->post_type == 'pagex_excerpt_tmp' ) {
			$layouts_modal .= '<div class="pagex-params-tab-title pagex-layouts-modal-excerpts">' . __( 'Excerpts', 'pagex' ) . '</div>';
		}

		// close tabs titles
		$layouts_modal .= '</div>';

		$all_layouts = pagex_get_builder_layouts();

		// all layouts
		$layouts_modal .= '<div class="pagex-params-tab-content">';
		if ( $all_layouts ) {
			foreach ( $all_layouts as $post_id => $layout ) {
				$layouts_modal .= '<div class="pagex-params-library-layout">';
				$layouts_modal .= '<div class="pagex-params-library-layout-title">' . $layout['title'] . '</div>';
				$layouts_modal .= '<div class="pagex-params-library-layout-type">' . $layout['post_type'] . '</div>';
				$layouts_modal .= '<div class="pagex-params-library-layout-action"><button type="button" class="btn btn-outline-secondary pagex-library-post-layout-import" data-import-post-layout="' . $post_id . '"><i class="fas fa-file-import"></i><span>' . __( 'Import Layout', 'pagex' ) . '</span></button></div>';

				$layouts_modal .= '</div>';
			}
		} else {
			$layouts_modal .= __( 'No layouts found.', 'pagex' );
		}
		// close tabs with all layouts
		$layouts_modal .= '</div>';

		// layouts library
		$layouts_modal .= '<div class="pagex-params-tab-content pagex-hide">';

		$layout_categories = array(
			''            => __( 'All', 'pagex' ),
//			'404'            => __( '404 page', 'pagex' ),
			'about'       => __( 'About', 'pagex' ),
//			'call_to_action' => __( 'Call to Action', 'pagex' ),
//			'clients'        => __( 'Clients', 'pagex' ),
			'coming_soon' => __( 'Coming Soon', 'pagex' ),
			'contacts'    => __( 'Contacts', 'pagex' ),
//			'faq'            => __( 'FAQ', 'pagex' ),
//			'features'    => __( 'Features', 'pagex' ),
			'footer'      => __( 'Footer', 'pagex' ),
			'header'      => __( 'Header', 'pagex' ),
//			'pricing'        => __( 'Pricing', 'pagex' ),
		);

		$layouts_modal .= '<div class="pagex-layouts-modal-filter">';
		$layouts_modal .= '<select class="pagex-layouts-modal-filter-cat custom-select">';
		foreach ( $layout_categories as $k => $v ) {
			$layouts_modal .= '<option value="' . $k . '">' . $v . '</option>';
		}
		$layouts_modal .= '</select>';
		$layouts_modal .= '</div>';

		$layouts_modal .= '<div class="pagex-layouts-modal-content pagex-layouts-modal-content-layouts">';
		$layouts_modal .= __( 'Loading...', 'pagex' );
		$layouts_modal .= '</div>';

		// close tab
		$layouts_modal .= '</div>';

		// theme templates library

		if ( $post->post_type == 'pagex_post_tmp' ) {
			$layouts_modal       .= '<div class="pagex-params-tab-content pagex-hide">';
			$template_categories = array(
				''                => __( 'All', 'pagex' ),
				'archive'         => __( 'Archive', 'pagex' ),
				'product_archive' => __( 'Product Archive', 'pagex' ),
				'single_page'     => __( 'Single Page', 'pagex' ),
				'single_post'     => __( 'Single Post', 'pagex' ),
				'single_product'  => __( 'Single Product', 'pagex' ),
			);

			$layouts_modal .= '<div class="pagex-layouts-modal-filter">';
			$layouts_modal .= '<select class="pagex-layouts-modal-filter-cat custom-select">';
			foreach ( $template_categories as $k => $v ) {
				$layouts_modal .= '<option value="' . $k . '">' . $v . '</option>';
			}
			$layouts_modal .= '</select>';
			$layouts_modal .= '</div>';

			$layouts_modal .= '<div class="pagex-layouts-modal-content pagex-layouts-modal-content-templates">';
			$layouts_modal .= __( 'Loading...', 'pagex' );
			$layouts_modal .= '</div>';

			// close tab
			$layouts_modal .= '</div>';
		}

		// excerpt templates library
		if ( $post->post_type == 'pagex_excerpt_tmp' ) {
			$layouts_modal      .= '<div class="pagex-params-tab-content pagex-hide">';
			$excerpt_categories = array(
				''        => __( 'All', 'pagex' ),
				'post'    => __( 'Post', 'pagex' ),
				'product' => __( 'Product', 'pagex' ),
			);
			$layouts_modal      .= '<div class="pagex-layouts-modal-filter">';
			$layouts_modal      .= '<select class="pagex-layouts-modal-filter-cat custom-select">';
			foreach ( $excerpt_categories as $k => $v ) {
				$layouts_modal .= '<option value="' . $k . '">' . $v . '</option>';
			}
			$layouts_modal .= '</select>';
			$layouts_modal .= '</div>';

			$layouts_modal .= '<div class="pagex-layouts-modal-content pagex-layouts-modal-content-excerpts">';
			$layouts_modal .= __( 'Loading...', 'pagex' );
			$layouts_modal .= '</div>';

			// close tab
			$layouts_modal .= '</div>';
		}

		// close library modal
		$layouts_modal .= '</div>';

		// select element form
		$all_elements_form = '<div id="pagex-all-elements-modal" class="pagex-main-modal-window pagex-params-modal pagex-hide"><div class="pagex-params-modal-head d-flex align-items-center"><div class="pagex-all-elements-modal-title">' . __( 'All Elements', 'pagex' ) . '</div><div class="pagex-params-modal-controls d-flex align-items-center ml-auto"><div class="input-group"><input type="text" id="pagex-search-elements" placeholder="' . __( 'Search elements', 'pagex' ) . '" class="form-control pagex-modal-search-input"><div class="input-group-append"><i class="fas fa-search input-group-text"></i></div></div><div class="pagex-params-modal-close"><i class="fas fa-times"></i></div></div></div>';

		// list of categories
		$all_elements_form .= '<div class="pagex-params-tabs d-flex"><div id="pagex-search-elements-title" class="pagex-params-tab-title pagex-hide">' . __( 'Search', 'pagex' ) . '</div><div class="pagex-params-tab-title active">' . __( 'All', 'pagex' ) . '</div>';

		foreach ( $categories as $categoryKey => $category ) {
			// check if category has any elements
			$category_elements = array_filter( $elements, function ( $element ) use ( $categoryKey ) {
				return isset( $element['category'] ) && $element['category'] == $categoryKey;
			} );
			if ( ! empty( $category_elements ) ) {
				$all_elements_form .= '<div class="pagex-params-tab-title">' . $category . '</div>';
			}
		}
		$all_elements_form .= '</div>';

		$all_elements_form .= '<div class="pagex-params-tabs-wrapper pagex-scroll">';

		// search result tab content
		$all_elements_form .= '<div id="pagex-search-elements-result" class="pagex-params-tab-content pagex-hide"></div>';

		// print all elements
		$all_elements_form .= '<div class="pagex-params-tab-content">';
		foreach ( $elements as $element ) {
			if ( ! isset( $element['category'] ) ) {
				continue;
			}
			$all_elements_form .= '<div class="pagex-elements-item trn-300  mr-2 mb-2 p-3" data-element-item-id="' . $element["id"] . '"><h5>' . $element["title"] . '</h5><small>' . $element["description"] . '</small></div>';
		}
		$all_elements_form .= '</div>';

		// print element by category
		foreach ( $categories as $categoryKey => $category ) {
			// find all elements for a category
			$category_elements = array_filter( $elements, function ( $element ) use ( $categoryKey ) {
				return isset( $element['category'] ) && $element['category'] == $categoryKey;
			} );

			if ( empty( $category_elements ) ) {
				continue;
			}

			$all_elements_form .= '<div class="pagex-params-tab-content pagex-hide">';
			foreach ( $category_elements as $element ) {
				$all_elements_form .= '<div class="pagex-elements-item trn-300 mr-2 mb-2 p-3" data-element-item-id="' . $element["id"] . '"><h5>' . $element["title"] . '</h5><small>' . $element["description"] . '</small></div>';
			}
			$all_elements_form .= '</div>';
		}
		$all_elements_form .= '</div>';
		$all_elements_form .= '</div>';

		if ( is_admin() || Pagex::is_frontend_builder_active() ) {
			echo $icons_modal;
			echo $save_layouts_modal;
			echo $layouts_modal;
			echo $all_elements_form;

			if ( isset( $_REQUEST['pagex-exit-link'] ) ) {
				$exit_link = $_REQUEST['pagex-exit-link'];
				if ( isset( $_REQUEST['action'] ) ) {
					$exit_link = $exit_link . '&action=' . $_REQUEST['action'];
				}
			} else {
				$exit_link = get_permalink();
			}

			// undo remove action button
			echo '<button type="button" class="btn btn-primary pagex-undo-remove"><i class="fas fa-undo-alt mr-2"></i>' . __( 'Undo a remove action', 'pagex' ) . '</button>';

			// settings menu
			echo '<div id="pagex-settings">
					<div class="pagex-main-settings pagex-scroll">
						<p>' . __( 'Main Settings', 'pagex' ) . '</p>
						<ul>
							<li class="pagex-responsive-mode" onclick="this.nextElementSibling.classList.toggle(\'active\')"><span><i class="fas fa-desktop mr-2"></i>' . __( 'Responsive Mode', 'pagex' ) . '</span><i class="fas fa-angle-down"></i></li>
	
							<ul class="pagex-hide pagex-responsive-mode-list">
								<li class="pagex-device-switcher" data-device-switcher="default"><i class="far fa-window-restore"></i><span>' . __( 'Default', 'pagex' ) . ' </span><small>' . __( 'No media query', 'pagex' ) . '</small></li>
								<li class="pagex-device-switcher" data-device-switcher="xs"><i class="fas fa-mobile-alt"></i><span>' . __( 'Mobile', 'pagex' ) . ' <sup>xs</sup></span><small>' . __( 'No media query', 'pagex' ) . '</small></li>
								<li class="pagex-device-switcher" data-device-switcher="sm"><i class="fas fa-mobile-alt fa-rotate-90"></i><span>' . __( 'Mobile (landscape)', 'pagex' ) . ' <sup>sm</sup></span> <small>≥ 576px</small></li>
								<li class="pagex-device-switcher" data-device-switcher="md"><i class="fas fa-tablet-alt"></i><span>' . __( 'Tablet', 'pagex' ) . ' <sup>md</sup></span> <small>≥ 768px</small></li>
								<li class="pagex-device-switcher" data-device-switcher="lg"><i class="fas fa-tablet-alt fa-rotate-90"></i><span>' . __( 'Tablet (landscape)', 'pagex' ) . ' <sup>lg</sup></span> <small>≥ 992px</small></li>
								<li class="pagex-device-switcher" data-device-switcher="xl"><i class="fas fa-desktop"></i><span>' . __( 'Desktop', 'pagex' ) . ' <sup>xl</sup></span> <small>≥ 1200px</small></li>
							</ul>
							
							<li class="" onclick="this.nextElementSibling.classList.toggle(\'active\')"><span><i class="far fa-eye mr-2"></i>' . __( 'Preview Mode', 'pagex' ) . '</span><i class="fas fa-angle-down"></i></li>
							
							<ul class="pagex-hide">
								<li class="pagex-preview-mode pagex-setting">' . __( 'Remove builder styles', 'pagex' ) . '<span class="pagex-settings-active-text">' . __( 'active', 'pagex' ) . '</span></li>
								<li class="pagex-preview-mode-hide-controls pagex-setting">' . __( 'Hide control buttons', 'pagex' ) . '<span class="pagex-settings-active-text">' . __( 'active', 'pagex' ) . '</span></li>
							</ul>
							
							<li class="" onclick="this.nextElementSibling.classList.toggle(\'active\')"><span><i class="fas fa-wrench mr-2"></i>' . __( 'Debug Mode', 'pagex' ) . '</span><i class="fas fa-angle-down"></i></li>
							
							<ul class="pagex-hide">
								<li class="pagex-debug-mode-hide-header pagex-setting">' . __( 'Hide header', 'pagex' ) . '<span class="pagex-settings-active-text">' . __( 'active', 'pagex' ) . '</span></li>
								<li class="pagex-debug-mode-clear-layout pagex-setting">' . __( 'Clear layout', 'pagex' ) . '<span class="pagex-settings-active-text">' . __( 'active', 'pagex' ) . '</span></li>
							</ul>
							
							<li class="pagex-save"><div><i class="far fa-save mr-2"></i><span>' . __( 'Save', 'pagex' ) . '</span></div></li>
							<li class="pagex-exit"><a href="' . $exit_link . '"></a><span><i class="fas fa-sign-out-alt mr-2"></i>' . __( 'Exit', 'pagex' ) . '</span></li>
						</ul>
					</div>
					<div class="pagex-main-settings-icon" onclick="this.closest(\'#pagex-settings\').classList.toggle(\'active\')"></div>
				</div>';

			// main modal form
			echo '<div id="pagex-params-modal" class="pagex-params-modal pagex-hide"><div class="pagex-params-modal-head pagex-params-modal-params-move"><div id="pagex-params-modal-title"></div><div class="pagex-params-modal-controls"><div class="pagex-params-modal-close"><i class="fas fa-times"></i></div></div></div><form id="pagex-params-form"></form></div>';
		}
	}

	/**
	 * Create global query based on preview query
	 */
	public static function setup_query_posts() {
		if ( isset( $_REQUEST['pagex-query-preview'] ) ) {
			// when post query preview loads at the first time
			query_posts( $_REQUEST['pagex-query-preview'] );
		} elseif ( isset( $_POST['url'] ) ) {
			// when dynamic element gets updated
			parse_str( $_POST['url'], $query );
			if ( isset( $query['pagex-query-preview'] ) ) {
				query_posts( array_merge(
						$query['pagex-query-preview'],
						array(
							'post_status' => 'publish'
						) )
				);
			}
		}

		// setup data for woocommerce
		if ( function_exists( 'wc_setup_loop' ) ) {
			wc_setup_loop( array(
				'is_search'    => $GLOBALS['wp_query']->is_search(),
				'is_filtered'  => is_filtered(),
				'total'        => $GLOBALS['wp_query']->found_posts,
				'total_pages'  => $GLOBALS['wp_query']->max_num_pages,
				'per_page'     => $GLOBALS['wp_query']->get( 'posts_per_page' ),
				'current_page' => max( 1, $GLOBALS['wp_query']->get( 'paged', 1 ) ),
				'loop'         => 0,
				'columns'      => wc_get_default_products_per_row(),
				'name'         => '',
				'is_shortcode' => false,
				'is_paginated' => true,
			) );
		}

		wp_reset_postdata();
	}


	/**
	 * Create ajax actions for all dynamic elements
	 * Add shortcodes for all elements
	 */
	public function pagex_callbacks() {
		$pagex_callback_elements = apply_filters( 'pagex_elements_dynamic_callbacks', array() );

		// add custom shortcode which are not part of elements
		// some shortcode should be loaded the same way as dynamic elements are getting updated
		// since they might have content which is depending on current preview post
		$pagex_custom_shortcodes = array(
			'pagex_dynamic_background'
		);

		$pagex_callback_elements = array_merge( $pagex_callback_elements, $pagex_custom_shortcodes );

		// add all callbacks from dynamic elements
		foreach ( $pagex_callback_elements as &$action ) {
			add_shortcode( $action, $action );

			// tool for frontend editor to preview element changes
			add_action( 'wp_ajax_' . $action, function () use ( &$action ) {
				$shortcode_data = isset( $_POST['atts'] ) ? urlencode( json_encode( $_POST['atts'] ) ) : '';

				Pagex_Editor::setup_query_posts();

				echo do_shortcode( '[' . $action . ' data="' . $shortcode_data . '"]' );

				wp_die();
			} );
		}
	}

	/**
	 * Create ajax actions for editor controls callbacks
	 */
	public function pagex_editor_callbacks() {
		foreach (
			array(
				'pagex_link_search_callback',
				'pagex_save_layout',
				'pagex_save_as_layout',
				'pagex_excerpt_preview',
				'pagex_import_post_layout',
				'pagex_get_layouts_from_library',
				'pagex_export_layout'
			) as $action
		) {
			add_action( 'wp_ajax_' . $action, array( $this, $action ) );
		}
	}

	/**
	 * Return all layouts from library
	 *
	 * @return string
	 */
	public function pagex_get_layouts_from_library() {
		if ( ! is_super_admin() ) {
			return;
		}

		$all_layouts = '';
		$url         = 'https://raw.githubusercontent.com/yumecommerce/pagexLibrary/master/';
		$type        = $_REQUEST['type'];

		$remote_response = wp_remote_get( $url . $type . '.json' );
		$headers         = wp_remote_retrieve_headers( $remote_response );
		if ( ! $headers ) {
			wp_send_json_error( array(
				'content' => __( 'Error. Something wrong with library server', 'pagex' ),
			) );

			return;
		}

		$remote_response_code = wp_remote_retrieve_response_code( $remote_response );

		if ( $remote_response_code != '200' ) {
			wp_send_json_error( array(
				'content' => __( 'Error. Library server response code:', 'pagex' ) . ' ' . $remote_response_code
			) );

			return;
		}

		$layouts = json_decode( wp_remote_retrieve_body( $remote_response ), true );

		foreach ( $layouts as $layout ) {
			foreach ( $layout['paths'] as $path ) {
				$img_src     = $url . $type . '/' . $layout['cat'] . '/' . $path . '/' . 'img.png';
				$data_path   = $url . $type . '/' . $layout['cat'] . '/' . $path . '/' . 'data.json';
				$all_layouts .= '<div data-library-cat="' . $layout['cat'] . '"><img src="' . $img_src . '"><button type="button" class="btn btn-outline-secondary pagex-library-post-layout-import" data-import-post-layout="' . $data_path . '"><i class="fas fa-file-import"></i><span>' . __( 'Import Layout', 'pagex' ) . '</span></button></div>';
			}
		}

		wp_send_json_success( array(
			'content' => $all_layouts,
		) );

		return;
	}

	/**
	 *  Importing layouts from local site or from library
	 */
	function pagex_import_post_layout() {
		if ( ! is_super_admin() ) {
			return;
		}

		Pagex_Editor::setup_query_posts();

		$post_layout = $_REQUEST['post_layout'];

		// check if we are going to import post id
		if ( is_numeric( $post_layout ) ) {
			wp_send_json_success( array(
				'params'  => get_post_meta( $post_layout, '_pagex_elements_params', true ),
				'content' => do_shortcode( get_post_field( 'post_content', $post_layout ) ),
			) );

			return;
		}

		// request a data from layout library
		$remote_response = wp_remote_get( $post_layout );
		$headers         = wp_remote_retrieve_headers( $remote_response );
		if ( ! $headers ) {
			wp_send_json_error( array(
				'content' => __( 'Error. Something wrong with library server', 'pagex' ),
			) );

			return;
		}

		$remote_response_code = wp_remote_retrieve_response_code( $remote_response );

		if ( $remote_response_code != '200' ) {
			wp_send_json_error( array(
				'content' => __( 'Error. Library server response code:', 'pagex' ) . ' ' . $remote_response_code
			) );

			return;
		}

		$layout = json_decode( wp_remote_retrieve_body( $remote_response ), true );

		wp_send_json_success( array(
			'params'  => json_encode( $layout['params'] ),
			'content' => do_shortcode( $layout['layout'] ),
		) );
	}

	/**
	 * Prepare layout for export.
	 */
	public function pagex_export_layout() {
		if ( ! is_super_admin() ) {
			return;
		}

		// replace dynamic elements with shortcodes and remove all frontend builder classes
		$content = Pagex_Editor::pre_save_post( $_POST['content'], true );

		wp_send_json_success(
			wp_unslash( $content )
		);
	}

	/**
	 * Performs preview for excerpt builder
	 */
	public function pagex_excerpt_preview() {
		if ( ! is_super_admin() ) {
			return;
		}

		$content = Pagex_Editor::pre_save_post( $_POST['content'] );

		// prevent link click inside excerpt preview window
		$content = preg_replace( '/(<a.*?href=")(.*?)(")/s', '$1#$3', $content );

		$args = array(
			'post_type'      => $_POST['post_type'],
			'posts_per_page' => 1,
		);

		if ( isset( $_POST['post_id'] ) && $_POST['post_id'] ) {
			$args['p']         = $_POST['post_id'];
			$args['post_type'] = 'any';
		}

		$posts = new WP_Query( $args );

		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();
				$content = do_shortcode( wp_unslash( $content ) );
				// replace all href in a tag to prevent exit from the iframe page
				echo preg_replace( '/(<a.*?href=")(.*?)(")/s', '$1#$3', $content );
			}
		} else {
			echo __( 'Post matching your preview settings is not found. Please change Post ID or Post Type options.' );
		}

		wp_die();
	}

	/**
	 * Performs post queries for internal linking
	 */
	function pagex_link_search_callback() {
		if ( ! is_super_admin() ) {
			return;
		}

		$pts = get_post_types( array( 'public' => true ), 'objects' );

		// remove builder post types from search
		unset( $pts['pagex_layout_builder'] );
		unset( $pts['pagex_post_tmp'] );

		$pt_names = array_keys( $pts );

		$query = array(
			'post_type'              => $pt_names,
			'suppress_filters'       => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status'            => 'publish',
			'posts_per_page'         => 20,
			's'                      => wp_unslash( $_POST['search'] ),
		);

		$get_posts = new WP_Query;
		$posts     = $get_posts->query( $query );

		echo '<div class="list-group mt-3">';
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center pagex-link-control-insert" data-link-url="' . get_permalink( $post->ID ) . '">';
				echo trim( esc_html( strip_tags( get_the_title( $post ) ) ) );
				echo '<small class="text-muted pl-2">' . $pts[ $post->post_type ]->labels->singular_name . '</small>';
				echo '</li>';
			}
		} else {
			echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="parentNode.remove()">';
			echo __( 'Nothing found', 'pagex' );
			echo '<div class="pagex-close trn-300"><i class="fas fa-times"></i></div>';
			echo '</li>';
		}
		echo '</div>';

		wp_die();
	}


	/**
	 * Performs saving section layout
	 */
	public function pagex_save_as_layout() {
		if ( ! is_super_admin() ) {
			return;
		}

		$content = isset( $_REQUEST['pagex_post_content'] ) ? $_REQUEST['pagex_post_content'] : '';
		$params  = isset( $_REQUEST['pagex_elements_params'] ) ? $_REQUEST['pagex_elements_params'] : '{}';
		$title   = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : '';

		$post_data = array(
			'post_title'   => wp_strip_all_tags( $title ),
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'pagex_layout_builder',
			'meta_input'   => array(
				'_pagex_layout_type'     => 'custom',
				'_pagex_elements_params' => $params,
				'_pagex_status'          => true,
			)
		);

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json( array(
				'error'   => true,
				'message' => $post_id->get_error_message(),
			) );
		} else {
			wp_send_json( array(
				'error'   => false,
				'message' => esc_html__( 'Layout was saved', 'pagex' ),
			) );
		}
	}

	/**
	 * Performs saving post layout
	 */
	public function pagex_save_layout() {
		if ( ! is_super_admin() ) {
			wp_send_json( array(
				'error'   => true,
				'message' => esc_html__( 'You do not have permissions to save layouts.', 'pagex' ),
			) );
		}

		$post_id = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : false;
		$content = isset( $_REQUEST['pagex_post_content'] ) ? $_REQUEST['pagex_post_content'] : false;
		$params  = isset( $_REQUEST['pagex_elements_params'] ) ? $_REQUEST['pagex_elements_params'] : false;

		if ( ! $content || ! $post_id || ! $params ) {
			wp_send_json( array(
				'error'   => true,
				'message' => esc_html__( 'Error! Something went wrong.', 'pagex' ),
			) );
		}

		$error      = false;
		$error_text = '';

		// update post
		$my_post = array(
			'ID'           => $post_id,
			'post_content' => $content,
		);

		$update_post = wp_update_post( $my_post, true );

		if ( is_wp_error( $update_post ) ) {
			$error  = true;
			$errors = $update_post->get_error_messages();
			foreach ( $errors as $error ) {
				$error_text .= $error;
			}
		} else {
			update_post_meta( $post_id, '_pagex_elements_params', $params );
			update_post_meta( $post_id, '_pagex_status', 'true' );
		}

		if ( $error ) {
			$return = array(
				'error'   => true,
				'message' => 'ERROR: ' . $error_text,
			);
		} else {
			$return = array(
				'error'   => false,
				'message' => esc_html__( 'Layout was saved', 'pagex' )
			);
		}

		wp_send_json( $return );
	}

	/**
	 * Remove all builder elements before save the post content
	 *
	 * @param $content
	 * @param bool $ignore_multilingual
	 *
	 * @return string
	 */
	public static function pre_save_post( $content, $ignore_multilingual = false ) {
		$settings     = Pagex::get_settings();
		$multilingual = isset( $settings['advanced']['multilingual'] ) ? $settings['advanced']['multilingual'] : '';

		$content = wp_unslash( $content );

		// do not processed if it is not a builder page
		if ( strpos( $content, 'data-type="section"' ) === false ) {
			return $content;
		}

		// take json obj with all element parameters for replacing dynamic elements with shortcodes
		$params = isset( $_REQUEST['pagex_elements_params'] ) ? $_REQUEST['pagex_elements_params'] : false;
		if ( $params === false ) {
			return $content;
		} else {
			$params = json_decode( wp_unslash( $params ), true );
		}

		// remove empty spaces
		$content = preg_replace( '/(\>)\s*(\<)/m', '$1$2', $content );
		$content = preg_replace( '/(\>)\s*(\[)/m', '$1$2', $content );
		$content = preg_replace( '/(\])\s*(\<)/m', '$1$2', $content );

		// remove inline styles from divs
		$content = preg_replace( '/(<div[^>]+)( style=".*?")/s', '$1', $content );

		// remove frontend CSS classes
		$frontend_classes = array(
			'/ui-sortable/m',
			'/pagex-hide-row-controls/m',
			'/pagex-section-hover/m',
			'/pagex-hide-row-column-controls/m',
			'/pagex-hide-container-options/m',
			'/pagex-hide-section-options/m',
			'/pagex-video-overlay-hide/m',
			'/pagex-modal-active/m',
			'/pagex-modal-show/m',

			'/ pagex-section-fixed/m',
			'/pagex-animated/m',

			'/swiper-button-disabled/m',
			'/swiper-slide-active/m',
			'/swiper-slide-next/m',
			'/ swiper-pagination-bullet-active/m',
			// space before so we remove only class and do not remove rules from <style>

			'/swiper-pagination-clickable/m',
			'/swiper-pagination-bullets/m',

		);

		$content = preg_replace( $frontend_classes, '', $content );

		$html = str_get_html( $content );

		// just in case remove builder options divs that might be presented
		foreach ( $html->find( '.pagex-options' ) as $element ) {
			$element->outertext = '';
		}

		// remove builder placeholders in case empty elements
		foreach ( $html->find( '.pagex-element-placeholder' ) as $element ) {
			$element->outertext = '';
		}

		// remove src from iframe video elements to make it load each time via lazy loading
		foreach ( $html->find( '.pagex-iframe-lazy' ) as $element ) {
			$element->src = null;
		}

		// remove iframe from background video and replace it with placeholder
		foreach ( $html->find( '.pagex-video-bg-youtube' ) as $element ) {
			$element->innertext = '<div class="pagex-video-youtube" id="' . uniqid( 'p' ) . '"></div>';
		}

		// wrap each string with shortcode to make it available for translation in multilingual plugins
		if ( $multilingual && ! $ignore_multilingual ) {
			foreach ( $html->find( '.pagex-lang-str' ) as $element ) {
				if ( strpos( $element->innertext, 'pagex_lang_str' ) === false ) {
					$element->innertext = '[pagex_lang_str]' . $element->innertext . '[/pagex_lang_str]';
				}
			}
		}

		$pagex_elements = apply_filters( 'pagex_elements', array() );

		// replace dynamic html content with shortcode
		foreach ( $html->find( '[data-callback]' ) as $element ) {
			$shortcode_data = Pagex_Editor::get_element_shortcode_data( $element->{'data-type'}, $element->{'data-id'}, $pagex_elements, $params );

			$element->find( '.element-wrap', 0 )->innertext = '[' . $element->{'data-callback'} . ' data="' . urlencode( json_encode( $shortcode_data ) ) . '"]';
		}

		// replace dynamic background image placeholder with shortcode
		foreach ( $html->find( '[data-dynamic-background]' ) as $element ) {
			$element->innertext = '[pagex_dynamic_background data="' . urlencode( json_encode( array( 'key' => $element->{'data-dynamic-background'} ) ) ) . '"]';
		}

		// replace dynamic link attribute with shortcode
		foreach ( $html->find( '[data-dynamic-link]' ) as $element ) {
			$element->href = '[pagex_dynamic_link ' . $element->{'data-dynamic-link'} . ']';
		}

		$html->save();

		$content = $html;

		// replace Font Awesome icon tags with SVG
		$content = Pagex_FontAwesome_SVG_Replace::replace( $content );

		$content = trim( $content );
		// keep slashes in data attr
		$content = wp_slash( $content );

		return $content;
	}

	/**
	 * Find dynamic element by given name and get all saved params
	 * Shortcode data does not save actions like CSS and Class and default builder params
	 *
	 * @param $type
	 * @param $id
	 * @param $elements
	 * @param $params
	 *
	 * @return array
	 */
	public static function get_element_shortcode_data( $type, $id, $elements, $params ) {
		$key = array_search( $type, array_column( $elements, 'id' ) );

		// if shortcode has no saved params
		if ( ! isset( $params[ $id ] ) ) {
			return array();
		}

		// make link-dynamic attr which is not param attribute be saved anyway
		$validate_data = array( 'link-dynamic' );

		// get only important (not style or class actions) params
		foreach ( $elements[ $key ]['options'] as $parameters ) {
			foreach ( $parameters['params'] as $param ) {
				if ( ! isset( $param['action'] ) && ! isset( $param['selector'] ) && isset( $param['id'] ) ) {
					// make sure that array values with id[] also saved
					$validate_data[] = preg_replace( '/\[.*?\]/i', '', $param['id'] );
				}
			}
		}

		// if element does not have params
		if ( empty( $validate_data ) ) {
			return array();
		}

		// save all validated element params
		$shortcode_atts = array();
		foreach ( $params[ $id ] as $key => $param ) {
			if ( in_array( $key, $validate_data ) ) {
				$shortcode_atts[ $key ] = $param;
			}
		}

		return $shortcode_atts;
	}
}

new Pagex_Editor();