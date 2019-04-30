<?php

class Pagex_Template_Manager {
	public function __construct() {
		// make created post templates selectable from page template attribute
		add_action( 'init', array( $this, 'assign_templates_to_posts' ) );
		add_action( 'init', array( $this, 'add_builder_area_class' ) );

		add_action( 'template_redirect', array( $this, 'builder_redirect' ) );
		add_filter( 'template_include', array( $this, 'page_template' ), 99 );
	}

	/**
	 * Assign post templates for all wordpress and selected custom post types
	 */
	public function assign_templates_to_posts() {
		$settings               = Pagex::get_settings();
		$custom_active_posts    = isset( $settings['active_post_templates'] ) ? $settings['active_post_templates'] : array();
		$default_post_templates = array( 'page' => 'page', 'post' => 'post' );

		$all_post_types = array_merge( $custom_active_posts, $default_post_templates );

		foreach ( $all_post_types as $post_type ) {
			add_filter( 'theme_' . $post_type . '_templates', array( $this, 'assign_post_templates_to_pages' ), 10, 4 );
		}

		// add blank template for pages
		add_filter( 'theme_page_templates', array( $this, 'page_post_template' ), 10, 4 );
	}

	/**
	 * Add builder area class if frame for header/footer/template is active
	 */
	public function add_builder_area_class() {
		if ( Pagex::is_frontend_builder_frame_active() && isset( $_REQUEST['pagex-layout-type'] ) ) {
			$type = $_REQUEST['pagex-layout-type'];

			add_filter( 'pagex_' . $type . '_class', function ( $classes ) {
				$classes[] = 'pagex-builder-area';

				return $classes;
			} );

			add_filter( 'body_class', function ( $classes ) use ( $type ) {
				$classes[] = 'pagex-builder-' . $type;

				return $classes;
			} );
		}
	}

	/**
	 * Add all created post templates to a select of the Page Templates attributes
	 *
	 * @param $post_templates
	 * @param $wp_theme
	 * @param $post
	 * @param $post_type
	 *
	 * @return mixed
	 */
	public function assign_post_templates_to_pages( $post_templates, $wp_theme, $post, $post_type ) {
		$args = array(
			'post_type'   => 'pagex_post_tmp',
			'numberposts' => - 1,
			'meta_query'  => array(
				array(
					'key'   => '_pagex_template_type',
					'value' => 'single'
				),
				array(
					'key'   => '_pagex_template_post_type',
					'value' => $post_type
				)
			)
		);

		$query = get_posts( $args );
		if ( $query ) {
			foreach ( $query as $post ) {
				setup_postdata( $post );
				$post_templates[ 'pagex-post-template-' . $post->ID ] = esc_attr( $post->post_title );
			}
		}
		wp_reset_postdata();

		return $post_templates;
	}

	/**
	 * Add blank template for pages
	 *
	 * @param $post_templates
	 * @param $wp_theme
	 * @param $post
	 * @param $post_type
	 *
	 * @return mixed
	 */
	public function page_post_template( $post_templates, $wp_theme, $post, $post_type ) {
		$post_templates['pagex-blank-template'] = __( 'Pagex Blank Template', 'pagex' );

		return $post_templates;
	}

	/**
	 * Make sure that custom post types for builder editor is assessable only for admin
	 */
	public function builder_redirect() {
		$post_type = get_query_var( 'post_type' );

		if ( $post_type == 'pagex_post_tmp' ) {
			if ( ! is_super_admin() ) {
				wp_redirect( home_url() );
				exit();
			}
		}
	}

	/**
	 * Add header and footer layouts style and content to template page actions
	 *
	 * @param $id
	 * @param $post
	 * @param $type
	 */
	public function setup_layout_content( $id, $post, $type ) {
		add_action( 'pagex_head', function () use ( $id, $post, $type ) {
			$id      = Pagex::get_translation_id( $id, 'pagex_layout_builder' );
			$content = apply_filters( 'pagex_content', do_shortcode( get_post_field( 'post_content', $id ) ) );

			add_action( 'pagex_' . $type . '_layout', function () use ( $content ) {
				echo $content;
			} );
		} );
	}

	/**
	 * Override WordPress's default template behavior.
	 *
	 * @param $default_template - template setup by WordPress
	 *
	 * @return string - full path to template file
	 */
	public function page_template( $default_template ) {
		global $post, $wp;

		$settings = Pagex::get_settings();

		if ( ! $settings ) {
			return $default_template;
		}

		$current_url    = home_url( $wp->request );
		$basic_template = PAGEX_DIR_NAME . '/inc/templates/basic-template.php';

		// setup iFrame for a builder
		if ( Pagex::is_frontend_builder_active() ) {
			// check if we open page from admin area of the post which is not saved
			if ( isset( $_REQUEST['pagex-layout-id'] ) && get_post_status( intval( $_REQUEST['pagex-layout-id'] ) ) == 'auto-draft' ) {
				$post_id = wp_insert_post( array(
					'post_title'  => 'ID: ' . intval( $_REQUEST['pagex-layout-id'] ),
					'ID'          => intval( $_REQUEST['pagex-layout-id'] ),
					'post_status' => 'publish',
					'post_type'   => $_REQUEST['pagex-post-type'],
				), true );

				if ( ! is_wp_error( $post_id ) ) {
					wp_safe_redirect( add_query_arg( array(
						'pagex'             => '',
						'pagex-layout-id'   => $post_id,
						'pagex-layout-type' => 'layout',
						'pagex-post-type'   => get_post_type( $post_id ),
					), get_permalink( $post_id ) ) );
					exit;
				}
			}

			add_action( 'pagex_post_content', function () use ( $current_url ) {
				$frame_src                = $_REQUEST;
				$frame_src['pagex-frame'] = '';

				unset( $frame_src['pagex'] );

				echo '<div id="pagex-builder-wrapper"><div id="pagex-builder-responsive-wrapper"><iframe src="' . add_query_arg( $frame_src, $current_url ) . '" allowfullscreen="1" id="pagex-frame"></iframe></div></div>';
			} );

			return $basic_template;
		}

		$header = $settings['default_header'];
		$footer = $settings['default_footer'];

		if ( is_singular( 'pagex_post_tmp' ) ) {
			$template_id        = $post->ID;
			$template_type      = get_post_meta( $template_id, '_pagex_template_type', true );
			$template_post_type = get_post_meta( $template_id, '_pagex_template_post_type', true );

			$href_preview = array(
				'pagex'                  => '',
				'pagex-layout-id'        => $template_id,
				'pagex-layout-type'      => 'template',
				'pagex-post-type'        => 'pagex_post_tmp',
				'pagex-template-preview' => $template_id // need for preview
			);

			if ( $template_type == 'archive' ) {
				wp_redirect( add_query_arg( $href_preview, get_post_type_archive_link( $template_post_type ) ) );
				exit();
			} else {
				// get the last published post to preview the template
				$last_post = wp_get_recent_posts(
					array(
						'numberposts' => 1,
						'post_status' => 'publish',
						'post_type'   => $template_post_type
					) );

				if ( $last_post ) {
					wp_redirect( add_query_arg( $href_preview, get_permalink( $last_post[0]['ID'] ) ) );
					exit();
				} else {
					// just in case if no posts were created
					die( 'no post to preview' );
				}
			}
		} elseif ( is_singular( 'pagex_layout_builder' ) ) {
			add_action( 'pagex_post_content', function () use ( $post ) {
				echo do_shortcode( get_post_field( 'post_content', $post->ID ) );
			} );

			// return template before header/footer layouts will be initialized
			return $basic_template;
		} else {
			$template = self::get_current_post_template();

			if ( ! $template ) {
				return $default_template;
			}

			if ( $template == 'pagex-blank-template' ) {
				if ( Pagex::is_frontend_builder_frame_active() ) {
					// add builder area manually since blank template has no post template with content area to which builder area can be applied for
					add_action( 'pagex_post_content', function () use ( $post ) {
						echo '<div class="pagex-builder-area">' . do_shortcode( get_post_field( 'post_content', $post->ID ) ) . '</div>';
					} );
				} else {
					add_action( 'pagex_head', function () use ( $post ) {
						$content = apply_filters( 'pagex_content', do_shortcode( get_post_field( 'post_content', $post->ID ) ) );

						add_action( 'pagex_post_content', function () use ( $content ) {
							echo $content;
						} );
					} );
				}

				return $basic_template;
			}

			// print post template
			$template_page_id = apply_filters( 'pagex_template_id', Pagex::get_translation_id( $template, 'pagex_post_tmp' ) );

			add_action( 'pagex_head', function () use ( $template_page_id, $post ) {
				$content      = get_post_field( 'post_content', $template_page_id );
				$post_content = '';
				if ( $content ) {
					if ( is_singular() ) {
						while ( have_posts() ) {
							the_post();
							$post_content .= apply_filters( 'pagex_content', do_shortcode( $content ) );
						}
					} else {
						$post_content .= apply_filters( 'pagex_content', do_shortcode( $content ) );
					}
				}
				add_action( 'pagex_post_content', function () use ( $post_content ) {
					echo $post_content;
				} );
			} );
		}

		// print header
		// demo preview for header layout
		if ( isset( $_REQUEST['pagex-header-preview'] ) ) {
			$header = intval( $_REQUEST['pagex-header-preview'] );
		}

		if ( $header ) {
			add_filter( 'pagex_admin_menu_header_layout_id', function () use ( $header ) {
				return $header;
			} );
			$this->setup_layout_content( $header, $post, 'header' );
		}

		// print footer
		// demo preview for header layout
		if ( isset( $_REQUEST['pagex-footer-preview'] ) ) {
			$footer = intval( $_REQUEST['pagex-footer-preview'] );
		}

		if ( $footer ) {
			add_filter( 'pagex_admin_menu_footer_layout_id', function () use ( $footer ) {
				return $footer;
			} );
			$this->setup_layout_content( $footer, $post, 'footer' );
		}

		// return basic template for all types of queries
		return $basic_template;
	}

	/**
	 * Determine what template should be loaded based on main query
	 *
	 * @return null|string
	 */
	public static function get_current_post_template() {
		global $post;

		if ( ! $settings = Pagex::get_settings() ) {
			return false;
		}

		$settings = $settings['post_templates'];

		$template = '';

		$post_type = get_query_var( 'post_type' );

		// happen with is_tax() query
		if ( ! $post_type && $post ) {
			$post_type = $post->post_type;
		}

		if ( is_404() ) {
			$template = $settings['page_404'];
		} elseif ( is_search() ) {
			$template = self::get_post_template( $post_type, 'search_results' );
		} elseif ( is_home() ) {
			$template = self::get_post_template( 'post', 'archive' );
		} elseif ( is_post_type_archive() ) {
			$template = self::get_post_template( $post_type, 'archive' );
		} elseif ( is_tax() ) {
			$template = self::get_post_template( $post_type, 'taxonomy' );
		} elseif ( is_singular() ) {
			// check if single post has custom post template
			$page_template_slug = get_page_template_slug( $post->ID );

			// if page has blank template remove all header/footer layouts
			if ( $page_template_slug == 'pagex-blank-template' ) {
				return 'pagex-blank-template';
			}

			if ( strpos( $page_template_slug, 'pagex-post-template' ) === 0 ) {
				// get template id from slug name
				$template = preg_replace( '/pagex-post-template-/', '', $page_template_slug );
			} else {
				$template = self::get_post_template( $post_type, 'single', true );
			}
		} elseif ( is_archive() ) {
			// for all wp post taxonomies is_category(), is_tag(), is_author(), is_day(), is_month(), is_year()
			$template = self::get_post_template( 'post', 'taxonomy' );
		}

		// demo preview for the main template
		if ( isset( $_REQUEST['pagex-template-preview'] ) ) {
			$template = intval( $_REQUEST['pagex-template-preview'] );
		}

		return $template;
	}

	/**
	 * Get template based on logic of provided type
	 *
	 * @param $post_type
	 * @param $type
	 * @param bool $single
	 *
	 * @return null
	 */
	public static function get_post_template( $post_type, $type, $single = false ) {
		$settings = Pagex::get_settings();

		$settings = $settings['post_templates'];

		$template = isset( $settings[ $post_type . '_' . $type ] ) ? $settings[ $post_type . '_' . $type ] : null;

		if ( ! $template ) {
			if ( $single ) {
				// check if for single template
				$template = isset( $settings[ $post_type . '_single' ] ) ? $settings[ $post_type . '_single' ] : null;

				// return basic page template if no custom is set
				if ( ! $template ) {
					return $settings['page_single'];
				}
			} else {
				// check for custom archive template
				$template = isset( $settings[ $post_type . '_archive' ] ) ? $settings[ $post_type . '_archive' ] : null;

				if ( ! $template ) {
					return $settings['post_archive'];
				}
			}
		}

		return $template;
	}
}

new Pagex_Template_Manager();
