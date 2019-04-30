<?php

class Pagex_Frontend {
	function __construct() {
		// enqueue frontend scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// add frontend classes to a body
		add_filter( 'body_class', array( $this, 'builder_classes' ) );

		// add preloader layout
		// add GDPR layout
		add_action( 'wp_footer', array( $this, 'after_layout' ) );

		// main filter for builder content
		add_filter( 'pagex_content', array( $this, 'content_filter' ) );

		// print google font link with all fonts
		add_action( 'wp_head', array( $this, 'print_default_style' ), 999 );
		add_action( 'wp_footer', array( $this, 'print_google_fonts' ), 999 );

		// add frontend svg icons
		add_action( 'wp_footer', array( $this, 'svg_frontend_icons' ) );

		// remove all stuff to match w3c
		//add_filter('style_loader_tag', array( $this, 'remove_type_attr'), 10, 2);
		//add_filter('script_loader_tag', array( $this, 'remove_type_attr'), 10, 2);
		add_action( 'wp_footer', array( $this, 'filter_footer_script_type_start' ), 0 );
		add_action( 'wp_footer', array( $this, 'filter_footer_script_type_end' ), 9999 );
		add_action( 'wp_head', array( $this, 'filter_footer_script_type_start' ), 0 );
		add_action( 'wp_head', array( $this, 'filter_footer_script_type_end' ), 9999 );
		//remove_action('wp_head', 'print_emoji_detection_script', 7);
		//remove_action('wp_print_styles', 'print_emoji_styles');
		add_action( 'widgets_init', array( $this, 'remove_widget_action' ) );
	}

	/**
	 * Enqueue frontend scripts and styles
	 */
	public function enqueue_scripts() {
		wp_register_script( 'parallax-scroll', PAGEX_PLUGIN_URL . 'assets/js/frontend/vendors/parallax-scroll.js', array( 'jquery' ), PAGEX_VERSION, true );
		wp_register_script( 'swiper', PAGEX_PLUGIN_URL . 'assets/js/frontend/vendors/swiper.min.js', array(), PAGEX_VERSION, true );
		wp_register_script( 'salvattore', PAGEX_PLUGIN_URL . 'assets/js/frontend/vendors/salvattore.js', array(), PAGEX_VERSION, true );
		wp_register_script( 'waypoints', PAGEX_PLUGIN_URL . 'assets/js/frontend/vendors/waypoints.js', array(), PAGEX_VERSION, true );

		// do not enqueue frontend scripts for a frame builder wrapper
		if ( ! Pagex::is_frontend_builder_active() ) {
			wp_enqueue_script( 'pagex-frontend', PAGEX_PLUGIN_URL . 'assets/js/frontend/frontend.js', array(
				'salvattore',
				'parallax-scroll',
				'swiper',
				'waypoints',
			), PAGEX_VERSION, true );
		}

		wp_enqueue_style( 'pagex-frontend', PAGEX_PLUGIN_URL . 'assets/css/pagex-frontend.css', array(), PAGEX_VERSION );

		wp_localize_script( 'pagex-frontend', 'pagexVars',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Add frontend classes
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function builder_classes( $classes ) {
		$classes[] = 'pagex-' . PAGEX_VERSION;

		// check for visually impaired accessibility module [pagex_visually_impaired_module]
		if ( isset( $_COOKIE['pagex_visually_impaired'] ) ) {
			$classes[] = 'pagex-visually-impaired-is-on';
			// font size
			if ( isset( $_COOKIE['pagex-vi-fs-is-big'] ) ) {
				$classes[] = 'pagex-vi-fs-is-big';
			}
			if ( isset( $_COOKIE['pagex-vi-fs-is-huge'] ) ) {
				$classes[] = 'pagex-vi-fs-is-huge';
			}

			// main color
			if ( isset( $_COOKIE['pagex-vi-cl-is-bw'] ) ) {
				$classes[] = 'pagex-vi-cl-is-bw';
			}
			if ( isset( $_COOKIE['pagex-vi-cl-is-bb'] ) ) {
				$classes[] = 'pagex-vi-cl-is-bb';
			}

			// if images are off
			if ( isset( $_COOKIE['pagex-vi-img-is-off'] ) ) {
				$classes[] = 'pagex-vi-img-is-off';
			}
		}

		if ( ! $settings = Pagex::get_settings() ) {
			return $classes;
		}

		$use_preloader = isset( $settings['design']['preloader']['active'] );

		if ( $use_preloader ) {
			$preloader_pages = $settings['design']['preloader']['pages'];

			if ( ( $preloader_pages == 'main' && is_front_page() ) || $preloader_pages == 'all' ) {
				if ( ! Pagex::is_frontend_builder_active() ) {
					$classes[] = 'pagex-preloader-body';
					$classes[] = 'pagex-preloader-body-active';
				}
			}
		}

		return $classes;
	}

	/**
	 * Print preloader layout
	 * Print GDPR notice
	 */
	public function after_layout() {
		if ( Pagex::is_frontend_builder_active() ) {
			return;
		}

		if ( ! $settings = Pagex::get_settings() ) {
			return;
		}

		// Preloader
		$use_preloader   = isset( $settings['design']['preloader']['active'] );
		$preloader_pages = $settings['design']['preloader']['pages'];
		$preloader_color = $settings['design']['preloader']['color'];

		$style = 'style="border-color: ' . $preloader_color . '"';

		$icon = '<div class="circle circle-1" ' . $style . '></div><div class="circle circle-1a" ' . $style . '></div><div class="circle circle-2" ' . $style . '></div><div class="circle circle-3" ' . $style . '></div>';

		if ( $use_preloader ) {
			if ( ( $preloader_pages == 'main' && is_front_page() ) || $preloader_pages == 'all' ) {
				echo '<div id="pagex-main-preloader"><div class="pagex-main-preloader-icon">' . $icon . '</div></div>';
			}
		}

		// GDPR
		$use_gdpr            = isset( $settings['advanced']['gdpr']['active'] );
		$gdpr_page_id        = $settings['advanced']['gdpr']['id'];
		$gdpr_custom_message = $settings['advanced']['gdpr']['message'];

		if ( $use_gdpr && ! isset( $_COOKIE['pagex_gdpr'] ) ) {
			if ( ! $gdpr_custom_message ) {
				$message = sprintf(
				/* translators: %1$s: link attributes. */
					__( 'This site uses cookies. By continuing to use this website, you consent to the use of cookies in accordance with our %1$s Cookie Policy.%2$s', 'pagex' ),
					'<a href="' . esc_url( get_permalink( $gdpr_page_id ) ) . '">', '</a>' );
			} else {
				$message = $gdpr_custom_message;
			}

			echo '<div id="pagex-gdpr-notice"><div id="pagex-gdpr-notice-close"><svg class="pagex-icon"><use xlink:href="#pagex-close-icon"></use></svg></div><div class="pagex-gdpr-notice-message">' . $message . '</div></div>';
		}
	}

	/**
	 * Main filter for builder content
	 * - combine all styles and fonts
	 * - remove data-callback attr
	 * - remove empty elements
	 *
	 * @param $content
	 *
	 * @return string
	 */
	function content_filter( $content ) {
		// in case no content or theme template is empty
		if ( ! $content ) {
			return $content;
		}

		// do not modify content when builder is active
		if ( Pagex::is_frontend_builder_active() ||
		     Pagex::is_frontend_builder_frame_active() ||
		     Pagex::is_excerpt_preview_frame_active() ) {
			return $content;
		}

		$html = str_get_html( $content );

		// combine all styles
		$styles = ' ';
		foreach ( $html->find( 'style' ) as $element ) {
			// check if style is already added; may happens with repeated excerpts
			if ( strpos( $styles, $element->innertext ) === false ) {
				$styles .= $element->innertext;
			}
			$element->outertext = '';
		}

		// combine all google font links and pass it to a filter in a footer
		$links = array();
		foreach ( $html->find( '.pagex-google-font' ) as $element ) {
			parse_str( substr( strstr( $element->{'href'}, '?' ), 1 ), $output );
			$family = explode( ':', $output['family'] );

			if ( ! isset( $links[ $family[0] ] ) ) {
				// add font family and weight
				$links[ $family[0] ] = isset( $family[1] ) ? $family[1] : '';
			} elseif ( isset( $family[1] ) ) {
				// add font weight to a font if it was not added before
				if ( strpos( $links[ $family[0] ], $family[1] ) === false ) {
					$links[ $family[0] ] = $links[ $family[0] ] . ',' . $family[1];
				}
			}

			// remove google font link
			$element->outertext = '';
		}

		// remove empty elements
		foreach ( $html->find( '.element-wrap' ) as $element ) {
			if ( $element->innertext == '' ) {
				$element->outertext = '';
			}
		}

		// remove element if user is not logged in
		foreach ( $html->find( '.pagex-display-logged-in' ) as $element ) {
			if ( ! is_user_logged_in() ) {
				$element->outertext = '';
			}
		}

		// remove element if user is logged in
		foreach ( $html->find( '.pagex-display-not-logged-in' ) as $element ) {
			if ( is_user_logged_in() ) {
				$element->outertext = '';
			}
		}

		// remove contenteditable attribute
		foreach ( $html->find( '.pagex-lang-str' ) as $element ) {
			if ( $element->{'contenteditable'} ) {
				$element->contenteditable = null;
			}
		}

		// add custom element link
		foreach ( $html->find( '[data-custom-link]' ) as $element ) {
			$element->innertext   = '<a class="pagex-custom-link-element d-none" ' . urldecode( $element->{'data-custom-link'} ) . '></a>' . $element->innertext;
			$element->{'onclick'} = 'pagexCustomLink(this)';
		}

		// init HTML to apply new DOM elements: ex custom link dynamic data
		$html = $html->save();
		$html = str_get_html( $html );

		foreach ( $html->find( '[data-dynamic-link]' ) as $element ) {
			$element->href = pagex_get_dynamic_link( $element->{'data-dynamic-link'} );
		}

		// replace static href with translated one
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$site_url = get_site_url();
			global $sitepress;
			$default_lang = $sitepress->get_default_language();
			// only if set language which is different from default one
			if ( $default_lang != ICL_LANGUAGE_CODE ) {
				foreach ( $html->find( '.pagex-static-link' ) as $element ) {
					// do not filter dynamic links since they already have the right url
					if ( $element->{'data-dynamic-link'} ) {
						continue;
					}

					$href = $element->{'href'};
					if ( $site_url == rtrim( $href, '/' ) && get_option( 'page_on_front' ) ) {
						// resole url for main page since it will not be filtered via wpml
						$element->{'href'} = apply_filters( 'wpml_permalink', get_permalink( get_option( 'page_on_front' ) ), ICL_LANGUAGE_CODE, true );
					} else {
						// set true to make wpml resolve the object behind the URL and try to find the matching translation's URL.
						$element->{'href'} = apply_filters( 'wpml_permalink', $href, ICL_LANGUAGE_CODE, true );
					}
				}
			}
		}

		// DOM tree back into string
		$html = $html->save();

		if ( ! empty( $links ) ) {
			add_filter( 'pagex_google_fonts', function ( $fonts ) use ( &$links ) {
				$fonts[] = $links;

				return $fonts;
			} );
		}

		// remove data-callback attr
		$html = preg_replace( '/(<[^>]+) data-callback=".*?"/i', '$1', $html );

		// remove data link attr
		$html = preg_replace( '/(<[^>]+) data-dynamic-link=".*?"/i', '$1', $html );
		$html = preg_replace( '/(<[^>]+) data-custom-link=".*?"/i', '$1', $html );

		add_action( 'pagex_head_styles', function () use ( $styles ) {
			echo '<style>' . $styles . '</style>';
		} );

		$content = $html;

		return $content;
	}

	/**
	 * Print all default styles from main settings
	 */
	public function print_default_style() {

	    // no need to load default style for frontend builder
		if ( Pagex::is_frontend_builder_active() ) {
			return;
		}

		$settings = Pagex::get_settings();

		if ( ! isset( $settings['design'] ) ) {
			return;
		}

		$style = array(
			array(
				'selector' => 'body',
				'rules'    => array(
					'font-family'    => $settings['design']['main_font']['name'],
					'font-size'      => $settings['design']['main_font']['size'],
					'font-weight'    => $settings['design']['main_font']['weight'],
					'line-height'    => $settings['design']['main_font']['lineheight'],
					'letter-spacing' => $settings['design']['main_font']['letterspacing'],
					'color'          => $settings['design']['main_font']['color'],
				)
			),
			array(
				'selector' => 'b, strong, table th',
				'rules'    => array(
					'font-weight' => $settings['design']['main_font']['bold_weight'],
				)
			),
			array(
				'selector' => 'h1,h2,h3,h4,h5,h6',
				'rules'    => array(
					'font-family'    => $settings['design']['heading_font']['name'],
					'letter-spacing' => $settings['design']['heading_font']['letterspacing'],
					'font-weight'    => $settings['design']['heading_font']['weight'],
					'color'          => $settings['design']['heading_font']['color'],
				)
			),
			array(
				'selector' => 'a',
				'rules'    => array(
					'color' => $settings['design']['main_font']['links_color'],
				)
			),
			array(
				'selector' => 'a:hover',
				'rules'    => array(
					'color' => $settings['design']['main_font']['links_hover'],
				)
			),
			array(
				'selector' => '[type="submit"], .button, .added_to_cart',
				'rules'    => array(
					'font-size'      => $settings['design']['button']['font_size'],
					'font-weight'    => $settings['design']['button']['font_weight'],
					'letter-spacing' => $settings['design']['button']['letter_spacing'],
					'text-transform' => $settings['design']['button']['text_transform'],
					'border-width'   => $settings['design']['button']['border_width'],
					'border-radius'  => $settings['design']['button']['border_radius'],
					'color'          => $settings['design']['button']['color'],
					'background'     => $settings['design']['button']['bg'],
					'border-color'   => $settings['design']['button']['border_color'],
					'min-height'     => $settings['design']['button']['min_height'],
					'box-shadow'     => $settings['design']['button']['box_shadow'],
				)
			),
			array(
				'selector' => '[type="submit"]:hover, .button:hover, .added_to_cart:hover',
				'rules'    => array(
					'color'        => $settings['design']['button']['color_hover'],
					'background'   => $settings['design']['button']['bg_hover'],
					'border-color' => $settings['design']['button']['border_color_hover'],
					'box-shadow'   => $settings['design']['button']['box_shadow_hover'],
				)
			),
			// WooCommerce remove product button
			array(
				'selector' => '.remove[data-product_id]',
				'rules'    => array(
					'color'      => $settings['design']['button']['color'],
					'background' => $settings['design']['button']['bg'],
				)
			),
			array(
				'selector' => '.remove[data-product_id]:hover',
				'rules'    => array(
					'color'      => $settings['design']['button']['color_hover'],
					'background' => $settings['design']['button']['bg_hover'],
				)
			),

			// loading icon color in a button
			array(
				'selector' => '[type="submit"]:after, .button:after',
				'rules'    => array(
					'color' => $settings['design']['button']['color'],
				)
			),
			array(
				'selector' => '[type="submit"]:hover:after, .button:hover:after',
				'rules'    => array(
					'color' => $settings['design']['button']['color_hover'],
				)
			),

			array(
				'selector' => '.select2-container .select2-selection--single, textarea, select, input[type="email"], input[type="number"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"]',
				'rules'    => array(
					'font-size'     => $settings['design']['form']['font_size'],
					'border-width'  => $settings['design']['form']['border_width'],
					'border-radius' => $settings['design']['form']['border_radius'],
					'border-color'  => $settings['design']['form']['border_color'],
					'min-height'    => $settings['design']['form']['min_height'],
				)
			),
			array(
				'selector' => '.form-control:focus, textarea:focus, select:focus, input[type="email"]:focus, input[type="number"]:focus, input[type="password"]:focus, input[type="search"]:focus, input[type="tel"]:focus, input[type="text"]:focus, input[type="url"]:focus',
				'rules'    => array(
					'border-color' => $settings['design']['form']['border_color_focus'],
				)
			),
			array(
				'selector' => 'input[type="radio"], input[type="checkbox"]',
				'rules'    => array(
					'background' => $settings['design']['form']['checkbox_bg'],
				)
			),
			array(
				'selector' => 'input[type="radio"]:checked, input[type="checkbox"]:checked',
				'rules'    => array(
					'border-color' => $settings['design']['form']['checkbox_checked'],
				)
			),
		);

		// custom fonts
		if ( isset( $settings['design']['custom_font'] ) ) {
			foreach ( $settings['design']['custom_font'] as $font ) {
				foreach ( $font['weight'] as $k => $v ) {
					$style[] = array(
						'selector' => '@font-face',
						'rules'    => array(
							'font-family' => '\'' . $font['name'] . '\'',
							'src'         => 'url(\'' . $font['woff'][ $k ] . '\') format(\'woff\')',
							'font-weight' => $font['weight'][ $k ],
							'font-style'  => $font['style'][ $k ],
						)
					);
				}
			}
		}

		// adobe fonts
		if ( isset( $settings['design']['adobe_font'] ) && $settings['design']['adobe_font']['id'] ) {
			echo '<link rel="stylesheet" href="https://use.typekit.net/' . $settings['design']['adobe_font']['id'] . '.css">';
		}

		$css_style = '';

		if ( $settings['design']['max_width_xl'] ) {
			$css_style .= '@media (min-width: 1200px) {.container { max-width: ' . $settings['design']['max_width_xl'] . ' }}';
		}

		foreach ( $style as $css ) {
			$design_style = '';
			foreach ( $css['rules'] as $k => $v ) {
				if ( $v != '' ) {
					$design_style .= $k . ':' . $v . ';';
				}
			}
			if ( $design_style ) {
				$css_style .= $css['selector'] . '{' . $design_style . '}';
			}
		}

		if ( $css_style ) {
			echo '<style class="pagex-default-style">' . $css_style . '</style>';
		}
	}

	/**
	 * Combine all google font from layouts and default settings and print a request line
	 */
	public function print_google_fonts() {
		$fonts  = array();
		$_fonts = apply_filters( 'pagex_google_fonts', array() );

		$settings     = Pagex::get_settings();
		$google_fonts = Pagex_Editor_Control_Attributes::get_google_fonts();
		$subsets      = isset( $settings['design']['google_fonts_subsets'] ) ? array_keys( $settings['design']['google_fonts_subsets'] ) : array();

		$main_font           = isset( $settings['design']['main_font']['name'] ) ? $settings['design']['main_font']['name'] : '';
		$main_font_weight    = isset( $settings['design']['main_font']['google_weight'] ) ? $settings['design']['main_font']['google_weight'] : array();
		$heading_font        = isset( $settings['design']['heading_font']['name'] ) ? $settings['design']['heading_font']['name'] : '';
		$heading_font_weight = isset( $settings['design']['heading_font']['google_weight'] ) ? $settings['design']['heading_font']['google_weight'] : array();

		// add main font
		if ( $main_font ) {
			if ( in_array( $main_font, $google_fonts['google_fonts']['options'] ) ) {
				$_fonts[] = array( $main_font => implode( ',', $main_font_weight ) );
			}
		}

		// add heading font
		if ( $heading_font ) {
			if ( in_array( $heading_font, $google_fonts['google_fonts']['options'] ) ) {
				$_fonts[] = array( $heading_font => implode( ',', $heading_font_weight ) );
			}
		}

		foreach ( $_fonts as $font_families ) {
			foreach ( $font_families as $font => $weight ) {
				if ( ! isset( $fonts[ $font ] ) ) {
					$fonts[ $font ] = $weight;
				} elseif ( $weight ) {
					if ( strpos( $fonts[ $font ], $weight ) === false ) {
						$fonts[ $font ] = $fonts[ $font ] . ',' . $weight;
					}
				}
			}
		}

		if ( $fonts ) {
			$families = array();
			foreach ( $fonts as $family => $weight ) {
				$families[] = $weight ? $family . ':' . $weight : $family;
			}

			echo '<link id="pagex-google-fonts" href="https://fonts.googleapis.com/css?family=' . urlencode( implode( "|", $families ) ) . '&subset=' . urlencode( implode( ",", $subsets ) ) . '" rel="stylesheet">';
		}

	}

	/**
	 * Custom SVG icons for some elements like slider and etc
	 */
	public function svg_frontend_icons() {
		?>
        <svg style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1"
             xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <defs>
                <symbol id="pagex-close-icon" viewBox="0 0 384 512">
                    <path fill="currentColor"
                          d="M231.6 256l130.1-130.1c4.7-4.7 4.7-12.3 0-17l-22.6-22.6c-4.7-4.7-12.3-4.7-17 0L192 216.4 61.9 86.3c-4.7-4.7-12.3-4.7-17 0l-22.6 22.6c-4.7 4.7-4.7 12.3 0 17L152.4 256 22.3 386.1c-4.7 4.7-4.7 12.3 0 17l22.6 22.6c4.7 4.7 12.3 4.7 17 0L192 295.6l130.1 130.1c4.7 4.7 12.3 4.7 17 0l22.6-22.6c4.7-4.7 4.7-12.3 0-17L231.6 256z"></path>
                </symbol>

                <symbol id="pagex-arrow-down-icon" viewBox="0 0 451.847 451.847">
                    <path fill="currentColor"
                          d="M225.923,354.706c-8.098,0-16.195-3.092-22.369-9.263L9.27,151.157c-12.359-12.359-12.359-32.397,0-44.751   c12.354-12.354,32.388-12.354,44.748,0l171.905,171.915l171.906-171.909c12.359-12.354,32.391-12.354,44.744,0   c12.365,12.354,12.365,32.392,0,44.751L248.292,345.449C242.115,351.621,234.018,354.706,225.923,354.706z"></path>
                </symbol>

                <symbol id="pagex-arrow-left-icon" viewBox="0 0 129 129">
                    <path fill="currentColor"
                          d="m88.6,121.3c0.8,0.8 1.8,1.2 2.9,1.2s2.1-0.4 2.9-1.2c1.6-1.6 1.6-4.2 0-5.8l-51-51 51-51c1.6-1.6 1.6-4.2 0-5.8s-4.2-1.6-5.8,0l-54,53.9c-1.6,1.6-1.6,4.2 0,5.8l54,53.9z"></path>
                </symbol>

                <symbol id="pagex-arrow-right-icon" viewBox="0 0 129 129">
                    <path fill="currentColor"
                          d="m40.4,121.3c-0.8,0.8-1.8,1.2-2.9,1.2s-2.1-0.4-2.9-1.2c-1.6-1.6-1.6-4.2 0-5.8l51-51-51-51c-1.6-1.6-1.6-4.2 0-5.8 1.6-1.6 4.2-1.6 5.8,0l53.9,53.9c1.6,1.6 1.6,4.2 0,5.8l-53.9,53.9z"></path>
                </symbol>

                <symbol id="pagex-long-arrow-right-icon" viewBox="0 0 224 224">
                    <path fill="currentColor"
                          d="M222.4,53.5L170.3,1.5c-2.1-2.1-5.4-2.1-7.5,0c-2.1,2.1-2.1,5.4,0,7.5l43,42.8H5.3c-2.9,0-5.3,2.4-5.3,5.3 c0,2.9,2.4,5.3,5.3,5.3h200.5l-43,42.8c-2.1,2.1-2.1,5.4,0,7.5c1,1,2.4,1.6,3.8,1.6c1.4,0,2.7-0.5,3.7-1.5L222.4,61 c1-1,1.6-2.3,1.6-3.8C224,55.8,223.4,54.4,222.4,53.5z"></path>
                </symbol>

                <symbol id="pagex-long-arrow-left-icon" viewBox="0 0 224 224">
                    <path fill="currentColor"
                          d="M1.6,53.5L53.7,1.5c2.1-2.1,5.4-2.1,7.5,0c2.1,2.1,2.1,5.4,0,7.5l-43,42.8h200.5c2.9,0,5.3,2.4,5.3,5.3 c0,2.9-2.4,5.3-5.3,5.3H18.2l43,42.8c2.1,2.1,2.1,5.4,0,7.5c-1,1-2.4,1.6-3.8,1.6c-1.4,0-2.7-0.5-3.7-1.5L1.6,61 c-1-1-1.6-2.3-1.6-3.8C0,55.8,0.6,54.4,1.6,53.5z"></path>
                </symbol>

                <symbol id="pagex-nav-menu-icon" viewBox="0 0 512 512">
                    <path fill="currentColor"
                          d="M491.3 235.3H20.7C9.3 235.3 0 244.6 0 256s9.3 20.7 20.7 20.7h470.6c11.4 0 20.7-9.3 20.7-20.7C512 244.6 502.7 235.3 491.3 235.3z"></path>
                    <path fill="currentColor"
                          d="M491.3 78.4H20.7C9.3 78.4 0 87.7 0 99.1c0 11.4 9.3 20.7 20.7 20.7h470.6c11.4 0 20.7-9.3 20.7-20.7C512 87.7 502.7 78.4 491.3 78.4z"></path>
                    <path fill="currentColor"
                          d="M491.3 392.2H20.7C9.3 392.2 0 401.5 0 412.9s9.3 20.7 20.7 20.7h470.6c11.4 0 20.7-9.3 20.7-20.7S502.7 392.2 491.3 392.2z"></path>
                </symbol>

                <symbol id="pagex-search-icon" viewBox="0 0 57 57">
                    <path fill="currentColor"
                          d="M53.3,57c-0.7,0-1.4-0.3-1.9-0.8L37.3,41.5l-0.3,0.2c-3.9,2.7-8.4,4.2-13.1,4.2C11.3,45.9,1,35.6,1,22.9 C1,10.3,11.3,0,23.9,0s22.9,10.3,22.9,22.9c0,5.4-1.9,10.6-5.4,14.7l-0.2,0.3l14,14.5c1,1.1,1,2.7-0.1,3.8C54.7,56.7,54,57,53.3,57 z M23.9,5.3c-9.7,0-17.6,7.9-17.6,17.6s7.9,17.6,17.6,17.6c9.7,0,17.6-7.9,17.6-17.6S33.6,5.3,23.9,5.3z"></path>
                </symbol>

                <symbol id="pagex-bag-icon" viewBox="0 0 489 489">
                    <path fill="currentColor"
                          d="M444.4,415.9l-27.1-298.7C416.4,107,407.7,99,397.5,99h-49.4c-1.7-25.3-10.9-48.6-26.2-66.3c-18.5-21.4-44.1-32.7-74.1-32.7 c-30.7,0-56.6,11.3-74.9,32.8c-14.8,17.5-23.7,40.8-25.4,66.3H98.3c-10.4,0-18.9,7.8-19.8,18.1L51.3,416.3l0,0.4 c0,0.3-0.1,0.8-0.1,1.4c0,38.6,35,70,78,70h237.3c43,0,78-31.4,78-70C444.6,417.6,444.6,416.8,444.4,415.9z M247.9,39.7 c48.5,0,58.4,39.3,60.3,59.3H187.6C190.4,70.3,207.4,39.7,247.9,39.7z M163.7,191.4c12.8,2.5,24.1-7.2,24.1-19.5v-33h121v32.6 c0,9.5,6.6,18,15.9,19.9c12.8,2.5,24.1-7.2,24.1-19.5v-33h32.3l25.6,279.7c-0.6,16.2-17.7,29.3-38.4,29.3H129.7 c-20.7,0-37.9-13.1-38.4-29.3L116.8,139h31v32.5C147.8,181.1,154.4,189.6,163.7,191.4z"></path>
                </symbol>

                <symbol id="pagex-heart-icon" viewBox="0 0 176.104 176.104">
                    <path fill="currentColor"
                          d="M150.383,18.301c-7.13-3.928-15.308-6.187-24.033-6.187c-15.394,0-29.18,7.015-38.283,18.015    c-9.146-11-22.919-18.015-38.334-18.015c-8.704,0-16.867,2.259-24.013,6.187C10.388,26.792,0,43.117,0,61.878    C0,67.249,0.874,72.4,2.457,77.219c8.537,38.374,85.61,86.771,85.61,86.771s77.022-48.396,85.571-86.771 c1.583-4.819,2.466-9.977,2.466-15.341C176.104,43.124,165.716,26.804,150.383,18.301z"></path>
                </symbol>

                <symbol id="pagex-speech-icon" viewBox="0 0 35.99 35.991">
                    <path fill="currentColor"
                          d="M35.49,17.416c0,3.613-1.594,6.91-4.217,9.453v6.248l-6.248-2.39c-2.152,0.789-4.527,1.232-7.03,1.232    C8.333,31.959,0.5,25.448,0.5,17.417c0-8.031,7.833-14.543,17.495-14.543S35.49,9.385,35.49,17.416z"></path>
                </symbol>

                <symbol id="pagex-wave" viewBox="0 0 1000 100" preserveAspectRatio="none">
                    <path fill="currentColor"
                          d="M421.9,6.5c22.6-2.5,51.5,0.4,75.5,5.3c23.6,4.9,70.9,23.5,100.5,35.7c75.8,32.2,133.7,44.5,192.6,49.7  c23.6,2.1,48.7,3.5,103.4-2.5c54.7-6,106.2-25.6,106.2-25.6V0H0v30.3c0,0,72,32.6,158.4,30.5c39.2-0.7,92.8-6.7,134-22.4  c21.2-8.1,52.2-18.2,79.7-24.2C399.3,7.9,411.6,7.5,421.9,6.5z"></path>
                </symbol>

                <symbol id="pagex-clouds" viewBox="0 0 1280 110" preserveAspectRatio="xMidYMax slice">
                    <path fill="currentColor"
                          d="M0 0v66c6.8 0 13.5.9 20.1 2.6 3.5-5.4 8.1-10.1 13.4-13.6 24.9-26.8 64.7-33.4 96.8-16 10.5-17.4 28.2-29.1 48.3-32 36.1-15.1 77.7-5.2 103.2 24.5 19.7.4 37.1 13.1 43.4 31.8 11.5-4.5 24.4-4.2 35.6 1.1l.4-.2c15.4-21.4 41.5-32.4 67.6-28.6 25-21 62.1-18.8 84.4 5.1 6.7-6.6 16.7-8.4 25.4-4.8 29.2-37.4 83.3-44.1 120.7-14.8l1.8 1.5c37.3-32.9 94.3-29.3 127.2 8 1.2 1.3 2.3 2.7 3.4 4.1 9.1-3.8 19.5-1.9 26.6 5 24.3-26 65-27.3 91-3.1.5.5 1 .9 1.5 1.4 12.8 3.1 24.4 9.9 33.4 19.5 7.9-.5 15.9.4 23.5 2.8 7-.1 13.9 1.5 20.1 4.7 3.9-11.6 15.5-18.9 27.7-17.5.2-.3.3-.6.5-.9 22.1-39.2 70.7-54.7 111.4-35.6 30.8-15.3 68.2-6.2 88.6 21.5 18.3 1.7 35 10.8 46.5 25.1 5.2-4.3 11.1-7.4 17.6-9.3V0H0z"></path>
                </symbol>

                <symbol id="pagex-flipped-clouds" viewBox="0 0 1280 110" preserveAspectRatio="xMidYMax slice">
                    <path fill="currentColor"
                          d="M1349.3-0.4H0v40C3.8,38.5,7.5,37,11.1,35c6.7-3.8,12.6-9.1,17.1-15.5c8.6,1.6,17.5,1,25.8-1.8 c3.4,41.6,39.8,72.7,81.5,69.3c26.5-2.1,49.9-18,61.8-41.9c21.2,19.7,54.4,18.5,74.1-2.8c8.8-9.4,13.7-21.7,14-34.5 c0.4-0.2,1-0.4,1.4-0.7c3.7-2,7-4.7,10-7.7c17.7,26.6,51.4,37.4,81.3,26c13,35.6,52.4,54,88,41c10.1-3.7,19.3-9.7,26.7-17.6 c7.6,7.4,18.7,9.4,28.4,5.3C553.6,96,614,103.5,655.8,70.9c15.8-12.3,27.3-29.2,33.1-48.5c5.2-0.6,10.4-2,15.2-4.2 c8.8,27.6,38.2,42.9,65.8,34.2c16-5.1,28.7-17.6,33.9-33.6c15.9-1.4,30.9-7.7,43-18.1c12.3,39.9,54.6,62.3,94.7,49.9 c15.8-4.9,29.6-14.8,39.2-28.3c13.4,6.4,29.1,6.1,42.4-0.6c4.4,13,17.3,21.1,30.9,19.6c0.2,0.3,0.3,0.6,0.5,1 c26,46.2,84.7,62.5,130.9,36.5c14.2-8,26-19.4,34.6-33.3c39.4,26.1,92.5,15.5,118.8-23.9C1343.4,14.8,1346.8,7.4,1349.3-0.4z"></path>
                </symbol>

                <symbol id="pagex-clouds-opacity" viewBox="0 0 1280 110" preserveAspectRatio="xMidYMax slice">
                    <path fill="currentColor"
                          d="M833.9 27.5c-5.8 3.2-11 7.3-15.5 12.2-7.1-6.9-17.5-8.8-26.6-5-30.6-39.2-87.3-46.1-126.5-15.5-1.4 1.1-2.8 2.2-4.1 3.4C674.4 33.4 684 48 688.8 64.3c4.7.6 9.3 1.8 13.6 3.8 7.8-24.7 34.2-38.3 58.9-30.5 14.4 4.6 25.6 15.7 30.3 30 14.2 1.2 27.7 6.9 38.5 16.2C840.6 49.6 876 29.5 910.8 38c-20.4-20.3-51.8-24.6-76.9-10.5zM384 43.9c-9 5-16.7 11.9-22.7 20.3 15.4-7.8 33.3-8.7 49.4-2.6 3.7-10.1 9.9-19.1 18.1-26-15.4-2.3-31.2.6-44.8 8.3zm560.2 13.6c2 2.2 3.9 4.5 5.7 6.9 5.6-2.6 11.6-4 17.8-4.1-7.6-2.4-15.6-3.3-23.5-2.8zM178.7 7c29-4.2 57.3 10.8 70.3 37 8.9-8.3 20.7-12.8 32.9-12.5C256.4 1.8 214.7-8.1 178.7 7zm146.5 56.3c1.5 4.5 2.4 9.2 2.5 14 .4.2.8.4 1.2.7 3.3 1.9 6.3 4.2 8.9 6.9 5.8-8.7 13.7-15.7 22.9-20.5-11.1-5.2-23.9-5.6-35.5-1.1zM33.5 54.9c21.6-14.4 50.7-8.5 65 13 .1.2.2.3.3.5 7.3-1.2 14.8-.6 21.8 1.6.6-10.3 3.5-20.4 8.6-29.4.3-.6.7-1.2 1.1-1.8-32.1-17.2-71.9-10.6-96.8 16.1zm1228.9 2.7c2.3 2.9 4.4 5.9 6.2 9.1 3.8-.5 7.6-.8 11.4-.8V48.3c-6.4 1.8-12.4 5-17.6 9.3zM1127.3 11c1.9.9 3.7 1.8 5.6 2.8 14.2 7.9 25.8 19.7 33.5 34 13.9-11.4 31.7-16.9 49.6-15.3-20.5-27.7-57.8-36.8-88.7-21.5z"
                          fill-opacity=".5"></path>
                    <path fill="currentColor"
                          d="M0 0v66c6.8 0 13.5.9 20.1 2.6 3.5-5.4 8.1-10.1 13.4-13.6 24.9-26.8 64.7-33.4 96.8-16 10.5-17.4 28.2-29.1 48.3-32 36.1-15.1 77.7-5.2 103.2 24.5 19.7.4 37.1 13.1 43.4 31.8 11.5-4.5 24.4-4.2 35.6 1.1l.4-.2c15.4-21.4 41.5-32.4 67.6-28.6 25-21 62.1-18.8 84.4 5.1 6.7-6.6 16.7-8.4 25.4-4.8 29.2-37.4 83.3-44.1 120.7-14.8l1.8 1.5c37.3-32.9 94.3-29.3 127.2 8 1.2 1.3 2.3 2.7 3.4 4.1 9.1-3.8 19.5-1.9 26.6 5 24.3-26 65-27.3 91-3.1.5.5 1 .9 1.5 1.4 12.8 3.1 24.4 9.9 33.4 19.5 7.9-.5 15.9.4 23.5 2.8 7-.1 13.9 1.5 20.1 4.7 3.9-11.6 15.5-18.9 27.7-17.5.2-.3.3-.6.5-.9 22.1-39.2 70.7-54.7 111.4-35.6 30.8-15.3 68.2-6.2 88.6 21.5 18.3 1.7 35 10.8 46.5 25.1 5.2-4.3 11.1-7.4 17.6-9.3V0H0z"></path>
                </symbol>

                <symbol id="pagex-flipped-clouds-opacity" viewBox="0 0 1280 110" preserveAspectRatio="xMidYMax slice">
                    <path fill-opacity=".5" fill="currentColor"
                          d="M0,20.9c4.1,0,8.1-0.3,12.2-0.9c19.5,34.8,63.6,47.2,98.4,27.6c3.7-2.1,7.4-4.6,10.7-7.4 c24.2,44.5,79.9,61,124.4,36.8c15.2-8.2,27.6-20.6,36.1-35.5c0.2-0.3,0.3-0.6,0.5-0.9c13,1.5,25.3-6.3,29.6-18.7 c12.7,6.5,27.6,6.7,40.4,0.6c23.2,32.4,68.3,40,100.7,16.8c12.9-9.2,22.4-22.4,27.1-37.6c11.5,9.9,25.9,16,41.1,17.3 c8.6,26.3,36.9,40.6,63.2,31.9c15.3-5,27.2-17.1,32-32.3c4.6,2,9.5,3.4,14.5,4.1c14.5,48.6,65.6,76.2,114.2,61.7 c18.2-5.4,34.3-16.4,46.1-31.4c9.3,3.8,20,1.9,27.1-5.1c24.7,26.5,66.1,28,92.5,3.3c7.6-7,13.3-15.8,17-25.6 c28.5,10.8,60.7,0.4,77.7-24.9c2.8,2.9,6,5.4,9.5,7.4c0.4,0.2,0.9,0.4,1.3,0.7c0.6,27.6,23.5,49.5,51.1,48.9c12.3-0.3,24-5,33-13.3 c17.6,35.8,60.9,50.5,96.7,32.9c23.4-11.7,38.7-34.9,40.1-60.9c7.5,2.5,15.5,3,23.3,1.7c15,23.2,46.1,29.9,69.3,14.8 c6-3.8,11-9,14.8-14.9c7,1.8,14.2,2.8,21.5,2.8c0.1,0,0-21.1,0-21.1H0V20.9z"></path>
                    <path fill="currentColor"
                          d="M1349.3-0.4H0v40C3.8,38.5,7.5,37,11.1,35c6.7-3.8,12.6-9.1,17.1-15.5c8.6,1.6,17.5,1,25.8-1.8 c3.4,41.6,39.8,72.7,81.5,69.3c26.5-2.1,49.9-18,61.8-41.9c21.2,19.7,54.4,18.5,74.1-2.8c8.8-9.4,13.7-21.7,14-34.5 c0.4-0.2,1-0.4,1.4-0.7c3.7-2,7-4.7,10-7.7c17.7,26.6,51.4,37.4,81.3,26c13,35.6,52.4,54,88,41c10.1-3.7,19.3-9.7,26.7-17.6 c7.6,7.4,18.7,9.4,28.4,5.3C553.6,96,614,103.5,655.8,70.9c15.8-12.3,27.3-29.2,33.1-48.5c5.2-0.6,10.4-2,15.2-4.2 c8.8,27.6,38.2,42.9,65.8,34.2c16-5.1,28.7-17.6,33.9-33.6c15.9-1.4,30.9-7.7,43-18.1c12.3,39.9,54.6,62.3,94.7,49.9 c15.8-4.9,29.6-14.8,39.2-28.3c13.4,6.4,29.1,6.1,42.4-0.6c4.4,13,17.3,21.1,30.9,19.6c0.2,0.3,0.3,0.6,0.5,1 c26,46.2,84.7,62.5,130.9,36.5c14.2-8,26-19.4,34.6-33.3c39.4,26.1,92.5,15.5,118.8-23.9C1343.4,14.8,1346.8,7.4,1349.3-0.4z"></path>
                </symbol>

                <symbol id="pagex-triangle" viewBox="0 0 1000 100" preserveAspectRatio="none">
                    <path fill="currentColor" d="M500,98.9L0,6.1V0h1000v6.1L500,98.9z"></path>
                </symbol>

                <symbol id="pagex-flipped-triangle" viewBox="0 0 1366 768" preserveAspectRatio="none">
                    <path fill="currentColor" d="M682.7,40.7L1366,768V0H0v768L682.7,40.7z"></path>
                </symbol>

                <symbol id="pagex-wave-opacity" viewBox="0 0 1280 140" preserveAspectRatio="none">
                    <path fill="currentColor"
                          d="M0 51.76c36.21-2.25 77.57-3.58 126.42-3.58 320 0 320 57 640 57 271.15 0 312.58-40.91 513.58-53.4V0H0z"
                          fill-opacity=".3"></path>
                    <path fill="currentColor"
                          d="M0 24.31c43.46-5.69 94.56-9.25 158.42-9.25 320 0 320 89.24 640 89.24 256.13 0 307.28-57.16 481.58-80V0H0z"
                          fill-opacity=".5"></path>
                    <path fill="currentColor"
                          d="M0 0v3.4C28.2 1.6 59.4.59 94.42.59c320 0 320 84.3 640 84.3 285 0 316.17-66.85 545.58-81.49V0z"></path>
                </symbol>

                <symbol id="pagex-curve" viewBox="0 0 1000 100" preserveAspectRatio="none">
                    <path fill="currentColor"
                          d="M1000,4.3V0H0v4.3C0.9,23.1,126.7,99.2,500,100S1000,22.7,1000,4.3z"></path>
                </symbol>

                <symbol id="pagex-flipped-curve" viewBox="0 0 1366 100" preserveAspectRatio="none">
                    <path fill="currentColor"
                          d="M683,3c509.9,0.7,683,97,683,97V0H0v99C0,99,173.1,2.2,683,3z"></path>
                </symbol>

                <symbol id="pagex-tilt" viewBox="0 0 1000 100" preserveAspectRatio="none">
                    <path fill="currentColor" d="M0,6V0h1000v100L0,6z"></path>
                </symbol>

                <symbol id="pagex-tilt-opacity" viewBox="0 0 1280 140" preserveAspectRatio="none">
                    <path fill="currentColor" d="M1280 0L640 70 0 0v140l640-70 640 70V0z" fill-opacity=".5"></path>
                    <path fill="currentColor" d="M1280 0H0l640 70 640-70z"></path>
                </symbol>

                <symbol id="pagex-wave-small-border" viewBox="0 0 336 25" preserveAspectRatio="xMidYMin slice">
                    <path fill="currentColor"
                          d="M336,0v5.6c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0 c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0 c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0 c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0 c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0 c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0 c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0 c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0 c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0 c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0 c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0 c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0c-1.9-1.9-3.7-1.9-5.6,0c-1.9,1.9-3.7,1.9-5.6,0 C3.7,3.7,1.9,3.7,0,5.6V0H336z"></path>
                </symbol>
            </defs>
        </svg>
		<?php
	}


	// remove type attr from style and scripts to match w3c validation
	public function remove_type_attr( $tag, $handle ) {
		return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );
	}

	public function filter_footer_script_type_start() {
		ob_start();
	}

	public function filter_footer_script_type_end() {
		$footer = ob_get_clean();
		echo preg_replace( "/ type=['\"]text\/(javascript|css)['\"]/", '', $footer );
	}

	public function remove_widget_action() {
		global $wp_widget_factory;

		remove_action( 'wp_head', array(
			$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
			'recent_comments_style'
		) );
	}
}

new Pagex_Frontend();