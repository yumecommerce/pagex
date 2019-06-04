<?php

class Pagex_Backend_Editor {
	function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );

		// limit num of revisions for pagex post types
		// todo currently wp 5.1 does not allow to save meta with post data
		//add_filter( 'wp_revisions_to_keep', array( $this, 'revisions_to_keep' ), 10, 2 );

		add_filter( 'wp_insert_post_data', array( $this, 'save_post_data' ), 99, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style' ) );
	}

	/**
	 * Add meta box for backend builder editor
	 * Meta box for choosing custom header/footer for templates and pagex
	 * Meta box for choosing layout type for layout builder
	 */
	public function add_meta_boxes() {
		$settings = Pagex::get_settings();

		add_meta_box(
			'pagex-backend-editor',
			'Pagex',
			array( $this, 'backend_editor' ),
			isset( $settings['builder'] ) ? $settings['builder'] : array(
				'pagex_post_tmp',
				'pagex_layout_builder',
				'pagex_excerpt_tmp'
			),
			'advanced',
			'high'
		);

		// todo custom header/footer for templates and posts
//		add_meta_box(
//			'pagex-header-footer-layouts',
//			__( 'Custom Layouts', 'pagex' ),
//			array( $this, 'header_footer_layouts' ),
//			array( 'page', 'pagex_post_tmp' ),
//			'side'
//		);

		add_meta_box(
			'pagex-layout-type',
			__( 'Layout Type', 'pagex' ),
			array( $this, 'layout_type' ),
			array( 'pagex_layout_builder' ),
			'side'
		);

		add_meta_box(
			'pagex-template-type',
			__( 'Template Type', 'pagex' ),
			array( $this, 'template_type' ),
			array( 'pagex_post_tmp' ),
			'side'
		);

		add_meta_box(
			'pagex-excerpt-preview',
			__( 'Excerpt Preview', 'pagex' ),
			array( $this, 'excerpt_preview' ),
			array( 'pagex_excerpt_tmp' ),
			'advanced'
		);
	}

	/**
	 * Meta box for choosing custom header and footer for pagex post templates or wordpress pages
	 *
	 * @param $post
	 */
	public function header_footer_layouts( $post ) {
		$header        = get_post_meta( $post->ID, '_pagex_custom_header', true );
		$footer        = get_post_meta( $post->ID, '_pagex_custom_footer', true );
		$header_select = '<option value="">' . __( 'Default', 'pagex' ) . '</option>';
		$footer_select = $header_select;

		$args = array(
			'post_type'   => 'pagex_layout_builder',
			'numberposts' => - 1
		);

		$query = get_posts( $args );
		$data  = array();

		if ( $query ) {
			foreach ( $query as $post ) {
				setup_postdata( $post );
				$data[ $post->post_name ] = esc_attr( $post->post_title );
			}
		}
		wp_reset_postdata();

		foreach ( $data as $key => $value ) {
			$header_select .= '<option value="' . $key . '" ' . selected( $key, $header, false ) . '>' . $value . '</option>';
		}

		foreach ( $data as $key => $value ) {
			$footer_select .= '<option value="' . $key . '" ' . selected( $key, $footer, false ) . '>' . $value . '</option>';
		}

		echo '<p>' . __( 'Custom Header', 'pagex' ) . '</p><select name="pagex-custom-header">' . $header_select . '</select>';
		echo '<p>' . __( 'Custom Footer', 'pagex' ) . '</p><select name="pagex-custom-footer">' . $footer_select . '</select>';
	}

	/**
	 * Meta box for layout builder to choose type of the layout
	 *
	 * @param $post
	 */
	public function layout_type( $post ) {
		$option = get_post_meta( $post->ID, '_pagex_layout_type', true );

		echo '<p>' . __( 'Type of current layout', 'pagex' ) . '</p><select name="pagex_layout_type"><option value="custom" ' . selected( 'custom', $option, false ) . '>' . __( 'Custom', 'pagex' ) . '</option><option value="header" ' . selected( 'header', $option, false ) . '>' . __( 'Header', 'pagex' ) . '</option><option value="footer" ' . selected( 'footer', $option, false ) . '>' . __( 'Footer', 'pagex' ) . '</option><option value="megamenu" ' . selected( 'megamenu', $option, false ) . '>' . __( 'Mega Menu Item', 'pagex' ) . '</option></select>';
	}

	/**
	 * Meta box for template builder to choose type of the layout
	 *
	 * @param $post
	 */
	public function template_type( $post ) {
		$type     = get_post_meta( $post->ID, '_pagex_template_type', true );
		$post_tmp = get_post_meta( $post->ID, '_pagex_template_post_type', true );

		$_post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );

		$post_types = array();

		foreach ( $_post_types as $post_type => $object ) {
			$post_types[ $post_type ] = $object->label;
		}

		echo '<p>' . __( 'Template Type', 'pagex' ) . '</p><select name="pagex_template_type"><option value="single" ' . selected( 'single', $type, false ) . '>' . __( 'Single', 'pagex' ) . '</option><option value="archive" ' . selected( 'archive', $type, false ) . '>' . __( 'Archive', 'pagex' ) . '</option></select>';

		echo '<p>' . __( 'Post Type', 'pagex' ) . '</p><select name="pagex_template_post_type">';
		foreach ( $post_types as $post_type => $type_label ) {
			echo '<option value="' . $post_type . '" ' . selected( $post_type, $post_tmp, false ) . '>' . $type_label . '</option>';
		}
		echo '</select>';
	}

	/**
	 * Meta box with backend editor area
	 * _pagex_status - if true, makes editor active by default
	 * _pagex_elements_params - js object with saved params of all elements
	 *
	 * @param $post
	 */
	public function backend_editor( $post ) {
		$builder_status      = get_post_meta( $post->ID, '_pagex_status', true );
		$all_elements_params = get_post_meta( $post->ID, '_pagex_elements_params', true );

		if ( ! $builder_status ) {
			$builder_status = 'false';
		}

		// make post types without wordpress editors active by default
		if ( in_array( $post->post_type, array(
			'pagex_layout_builder',
			'pagex_post_tmp',
			'pagex_excerpt_tmp'
		) ) ) {
			$builder_status = 'true';
		}

		// remove multilingual shortcode from builder so it would not appear in settings
		$content = preg_replace( array( '/\[pagex_lang_str\]/m', '/\[\/pagex_lang_str\]/m' ), '', $post->post_content );

		// wrap custom styles so it would not break default layout
		$content = preg_replace( '/(<style.*?>)(.*?)(<\/style>)/m', "$1/*pagexstyle $2 pagexstyle*/$3", $content );

		echo '<div id="pagex_backend_data" class="pagex-hide"><input name="pagex_page_status" value="' . $builder_status . '"><textarea name="pagex_post_content" id="pagex_post_content">' . $post->post_content . '</textarea><textarea name="pagex_elements_params" id="pagex_elements_params">' . $all_elements_params . '</textarea></div>';
		echo '<div id="pagex-backend-content" class="pagex-builder-area">' . $content . '</div>';
	}

	/**
	 *  Meta box with excerpt preview
	 */
	public function excerpt_preview( $post ) {
		$post_type_preview  = get_post_meta( $post->ID, '_pagex_excerpt_preview_post_type', true );
		$post_preview_id    = get_post_meta( $post->ID, '_pagex_excerpt_preview_post_id', true );
		$post_preview_width = get_post_meta( $post->ID, '_pagex_excerpt_preview_width', true );

		$post_preview_width = $post_preview_width ? $post_preview_width : 'small';

		$_post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );
		$post_types  = array();

		foreach ( $_post_types as $post_type => $object ) {
			$post_types[ $post_type ] = $object->label;
		}

		echo '<div class="pagex-excerpt-preview-settings">';

		echo '<div class="pagex-excerpt-preview-setting">' . __( 'Post Type', 'pagex' ) . ': <select id="pagex-excerpt-preview-post-type" name="pagex_excerpt_preview_post_type">';
		foreach ( $post_types as $post_type => $type_label ) {
			echo '<option value="' . $post_type . '" ' . selected( $post_type, $post_type_preview, false ) . '>' . $type_label . '</option>';
		}
		echo '</select><p class="description">' . __( 'Preview specific post type', 'pagex' ) . '</p></div>';

		echo '<div class="pagex-excerpt-preview-setting">' . __( 'Post ID', 'pagex' ) . ': <input type="text" id="pagex-excerpt-preview-post-id" name="pagex_excerpt_preview_post_id" value="' . $post_preview_id . '"><p class="description">' . __( 'Preview specific post by its ID', 'pagex' ) . '</p></div>';

		echo '<div class="pagex-excerpt-preview-setting">' . __( 'Preview Width', 'pagex' ) . ': <select id="pagex-excerpt-preview-width" name="pagex_excerpt_preview_width">';
		echo '<option value="small" ' . selected( 'small', $post_preview_width, false ) . '>' . __( 'Small', 'pagex' ) . '</option>';
		echo '<option value="medium" ' . selected( 'medium', $post_preview_width, false ) . '>' . __( 'Medium', 'pagex' ) . '</option>';
		echo '<option value="large" ' . selected( 'large', $post_preview_width, false ) . '>' . __( 'Large', 'pagex' ) . '</option>';
		echo '</select><p class="description">' . __( 'Width of the preview window', 'pagex' ) . '</p></div>';

		echo '</div>';

		echo '<iframe id="pagex-excerpt-preview-frame" class="pagex-excerpt-preview-window-width-' . $post_preview_width . '" src="' . home_url() . '?pagex-excerpt-preview" onload="pagex.excerptPreviewIframeLoaded()"></iframe>';
	}

	/**
	 * Save all custom meta boxes data
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function save_meta( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( isset( $_POST['pagex_page_status'] ) ) {
			update_post_meta( $post_id, '_pagex_status', $_POST['pagex_page_status'] );
		}

		if ( isset( $_POST['pagex_layout_type'] ) ) {
			update_post_meta( $post_id, '_pagex_layout_type', $_POST['pagex_layout_type'] );
		}

		if ( isset( $_POST['pagex_elements_params'] ) ) {
			update_post_meta( $post_id, '_pagex_elements_params', $_POST['pagex_elements_params'] );
		}

		if ( isset( $_POST['pagex_custom_header'] ) ) {
			update_post_meta( $post_id, '_pagex_custom_header', $_POST['pagex_custom_header'] );
		}

		if ( isset( $_POST['pagex_custom_footer'] ) ) {
			update_post_meta( $post_id, '_pagex_custom_footer', $_POST['pagex_custom_footer'] );
		}

		if ( isset( $_POST['pagex_template_type'] ) ) {
			update_post_meta( $post_id, '_pagex_template_type', $_POST['pagex_template_type'] );
		}

		if ( isset( $_POST['pagex_template_post_type'] ) ) {
			update_post_meta( $post_id, '_pagex_template_post_type', $_POST['pagex_template_post_type'] );
		}

		if ( isset( $_POST['pagex_excerpt_preview_post_type'] ) ) {
			update_post_meta( $post_id, '_pagex_excerpt_preview_post_type', $_POST['pagex_excerpt_preview_post_type'] );
		}

		if ( isset( $_POST['pagex_excerpt_preview_post_id'] ) ) {
			update_post_meta( $post_id, '_pagex_excerpt_preview_post_id', $_POST['pagex_excerpt_preview_post_id'] );
		}

		if ( isset( $_POST['pagex_excerpt_preview_width'] ) ) {
			update_post_meta( $post_id, '_pagex_excerpt_preview_width', $_POST['pagex_excerpt_preview_width'] );
		}
	}

	/**
	 * Limit the number of revisions to store for custom post types
	 *
	 * @param $num
	 * @param $post
	 *
	 * @return int
	 */
	public function revisions_to_keep( $num, $post ) {
		$post_type = get_post_type( $post );

		if ( $post_type && in_array( $post_type, array(
				'pagex_layout_builder',
				'pagex_post_tmp',
				'pagex_excerpt_tmp'
			) ) ) {
			return 10;
		}

		return $num;
	}

	/**
	 * @param $data
	 * @param $post
	 *
	 * @return mixed
	 */
	public function save_post_data( $data, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $data;
		}

		if ( isset( $_POST['pagex_page_status'] ) && isset( $_POST['pagex_post_content'] ) && $_POST['pagex_page_status'] == 'true' ) {
			$data['post_content'] = Pagex_Editor::pre_save_post( $_POST['pagex_post_content'] );

			return $data;
		}

		return $data;
	}


	/**
	 * Print admin styles and scripts for a builder editor only when we are creating or editing post
	 */
	public function admin_style() {
		$settings = Pagex::get_settings();

		if ( is_admin() ) {
			$current_screen = get_current_screen();
			if ( $current_screen->post_type == '' || $current_screen->base != 'post' ) {
				return;
			}
			if ( ! isset( $settings['builder'][ $current_screen->post_type ] ) && ! in_array( $current_screen->post_type, array(
					'pagex_layout_builder',
					'pagex_post_tmp',
					'pagex_excerpt_tmp'
				) ) ) {
				return;
			}
		}

		wp_enqueue_media();
		wp_enqueue_script( 'mediaelement-core' );

		wp_enqueue_style(
			'media',
			admin_url( '/css/media.css' )
		);

		wp_enqueue_style( 'pagex-builder' );
		wp_enqueue_style( 'pagex-backend' );
		wp_enqueue_script( 'pagex-builder' );

		Pagex_Editor::localizeEditorScript();

		wp_enqueue_script( 'pagex-backend-builder', PAGEX_PLUGIN_URL . 'assets/js/backend/backend.js', array( 'jquery' ), PAGEX_VERSION, true );

		wp_enqueue_script( 'pagex-builder-form' );

		// Add element title for backend style
		$element_names_style = '';
		$elements            = apply_filters( 'pagex_elements', array() );
		foreach ( $elements as $element ) {
			$element_names_style .= '.element[data-type="' . $element['id'] . '"]:before {content: "' . $element['title'] . '"}';
		}

		// modal window label
		$element_names_style .= '.pagex-modal-builder-area:before {content: "' . __( 'Modal Window', 'pagex' ) . '"}';

		wp_add_inline_style( 'pagex-backend', $element_names_style );
	}
}

new Pagex_Backend_Editor();