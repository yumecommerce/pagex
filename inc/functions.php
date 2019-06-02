<?php

/**
 * Display the classes for the header div
 */
function pagex_header_class() {
	$classes = apply_filters( 'pagex_header_class', array() );
	if ( $classes ) {
		echo 'class="' . join( ' ', $classes ) . '"';
	}
}

/**
 * Display the classes for the main div
 */
function pagex_template_class() {
	$classes = apply_filters( 'pagex_template_class', array() );
	if ( $classes ) {
		echo 'class="' . join( ' ', $classes ) . '"';
	}
}

/**
 * Display the classes for the footer div
 */
function pagex_footer_class() {
	$classes = apply_filters( 'pagex_footer_class', array() );
	if ( $classes ) {
		echo 'class="' . join( ' ', $classes ) . '"';
	}
}

/**
 * Modal window template
 * Used by dynamic element shortcodes with any type of modal windows
 *
 * @param $data array with parameters
 */
function pagex_modal_window_template( $data ) {
	$class = array(
		'pagex-modal',
		isset( $data['offcanvas'] ) ? 'pagex-modal-offcanvas pagex-offcanvas-position-' . $data['offcanvas'] : '',
		isset( $data['class'] ) ? $data['class'] : '',
	)
	?>
    <div class="<?php echo implode( ' ', $class ); ?>">
        <div class="pagex-modal-window-wrapper">
            <div class="pagex-modal-window">
                <div class="pagex-modal-window-close">
                    <svg class="pagex-icon">
                        <use xlink:href="#pagex-close-icon"/>
                    </svg>
                </div>
                <div class="pagex-modal-window-content">
					<?php echo $data['content']; ?>
                </div>
            </div>
        </div>
    </div>
	<?php
}

/**
 * Generates an array with excerpt ids
 *
 * @return array
 */
function pagex_get_excerpt_templates() {
	$data = array();

	$query = get_posts( array(
		'post_type'   => 'pagex_excerpt_tmp',
		'numberposts' => - 1
	) );

	if ( ! $query ) {
		return array( '' => __( 'None', 'pagex' ) );
	}

	foreach ( $query as $post ) {
		$data[ $post->ID ] = $post->post_title;
	}
	wp_reset_postdata();

	return $data;
}

/**
 * List of all pages with activated builder including layouts and templates
 *
 * @return array
 */
function pagex_get_builder_layouts() {
	$data = array();
	$pts  = get_post_types( array(), 'objects' );

	$post_type = array( 'page', 'pagex_layout_builder' );

	global $post;

	if ( $post ) {
		if ( $post->post_type == 'pagex_post_tmp' ) {
			$post_type[] = 'pagex_post_tmp';
		} elseif ( $post->post_type == 'pagex_excerpt_tmp' ) {
			$post_type[] = 'pagex_excerpt_tmp';
		}
	}

	$query = get_posts( array(
		'post_type'   => $post_type,
		'numberposts' => - 1,
		'meta_key'    => '_pagex_status',
		'meta_value'  => 'true',
	) );

	if ( ! $query ) {
		return array();
	}

	foreach ( $query as $post ) {
		$value = array(
			'title'     => $post->post_title ? $post->post_title : '#ID ' . $post->ID,
			'post_type' => $pts[ $post->post_type ]->labels->singular_name,
		);

		$data[ $post->ID ] = $value;
	}
	wp_reset_postdata();

	return $data;
}


/**
 * Retrieve layout templates
 *
 * @return array
 */
function pagex_get_layout_templates() {
	static $data = array();

	if ( ! empty( $data ) ) {
		return $data;
	}

	$data['0'] = __( 'None', 'pagex' );

	$query = get_posts( array(
		'post_type'   => 'pagex_layout_builder',
		'numberposts' => - 1
	) );

	foreach ( $query as $post ) {
		$data[ $post->ID ] = $post->post_title;
	}
	wp_reset_postdata();

	return $data;
}

/**
 * Retrieve all registered taxonomies
 * Used in several elements
 *
 * @return array
 */
function pagex_get_taxonomies() {
	$_taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'objects' );

	$taxonomies = array( '' => __( 'None', 'pagex' ) );

	foreach ( $_taxonomies as $taxonomy ) {
		$taxonomies[ $taxonomy->name ] = $taxonomy->label;
	}

	return $taxonomies;
}


/**
 * Generate html icon based on passed data
 * Used by dynamic elements with icons.
 *
 * @param $id - string with id of the option
 * @param $data - array with parameters of the element or repeater item
 *
 * @return string
 */
function pagex_generate_icon( $id, $data ) {
	$html = '';

	if ( $data[ $id ] === 'font-awesome' && isset( $data[ $id . "_fa" ] ) ) {
		$html = '<i class="' . $data[ $id . "_fa" ] . ' pagex-icon"></i>';
		$html = Pagex_FontAwesome_SVG_Replace::replace( $html );
	}

	if ( $data[ $id ] === 'svg' && isset( $data[ $id . '_svg' ] ) ) {
		$html = urldecode( $data[ $id . '_svg' ] );
		$html = preg_replace( '/((\<svg|\<img).*?)(class=".*?")/s', '$1', $html );
		$html = preg_replace( '/((\<svg|\<img).*?)(>)/s', '$1 class="pagex-icon" $3', $html );
	}

	if ( $data[ $id ] === 'image' && isset( $data[ $id . '_image' ] ) ) {
		$html = '<img class="pagex-icon" src="' . $data[ $id . "_image" ] . '" alt="icon">';
	}

	return $html;
}

/**
 * Remove site name from wp_get_document_title() function
 * Used by document_title_parts filter in document-title element
 *
 * @param $title
 *
 * @return array
 */
function pagex_remove_document_title_parts( $title ) {
	unset( $title['tagline'] );
	unset( $title['site'] );

	return $title;
}

/**
 * Custom document title function returns document title for the current page.
 * Similar to wp_get_document_title() except it can display context label for taxonomies like get_the_archive_title().
 * Also can display single post title for WooCommerce shop.
 *
 * Some part still using wp_get_document_title() so we do not have to translate default WordPress strings like "Page not found" and etc.
 *
 * @param bool $context - display taxonomy label before its name or not
 *
 * @return string
 */
function pagex_get_document_title( $context = false, $use_page = false ) {
	global $page, $paged;

	$title = '';

	add_filter( 'document_title_parts', 'pagex_remove_document_title_parts' );

	if ( is_404() ) {
		// If it's a 404 page, use a "Page not found" title.
		$title = wp_get_document_title();
	} elseif ( is_search() ) {
		// If it's a search, use a dynamic search results title.
		$title = wp_get_document_title();
	} elseif ( is_front_page() ) {
		// If on the front page, use the site title.
		$title = get_bloginfo( 'name', 'display' );
	} elseif ( is_post_type_archive() ) {
		// If on a post type archive, use the post type archive title.
		if ( is_post_type_archive( 'product' ) && function_exists( 'wc' ) ) {
			if ( $shop_page_id = get_option( 'woocommerce_shop_page_id' ) ) {
				$title = get_the_title( $shop_page_id );
			}
		} else {
			$title = post_type_archive_title( '', false );
		}
	} elseif ( is_tax() ) {
		// If on a taxonomy archive, use the term title.
		if ( $context ) {
			$title = get_the_archive_title();
		} else {
			$title = wp_get_document_title();
		}
	} elseif ( is_home() || is_singular() ) {
		// If we're on the blog page that is not the homepage
		$title = single_post_title( '', false );
	} elseif ( is_category() || is_tag() ) {
		// If on a category or tag archive, use the term title.
		if ( $context ) {
			$title = get_the_archive_title();
		} else {
			$title = single_term_title( '', false );
		}
	} elseif ( is_author() && $author = get_queried_object() ) {
		// If on an author archive, use the author's display name.
		if ( $context ) {
			$title = get_the_archive_title();
		} else {
			$title = $author->display_name;
		}
	} elseif ( is_year() ) {
		// If it's a date archive, use the date as the title.
		if ( $context ) {
			$title = get_the_archive_title();
		} else {
			$title = wp_get_document_title();
		}
	} elseif ( is_month() ) {
		if ( $context ) {
			$title = get_the_archive_title();
		} else {
			$title = wp_get_document_title();
		}
	} elseif ( is_day() ) {
		if ( $context ) {
			$title = get_the_archive_title();
		} else {
			$title = wp_get_document_title();
		}
	}

	// Add a page number if necessary.
	if ( $use_page && ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		/* translators: %d: page number */
		$title = $title . ' - ' . sprintf( __( 'Page %d', 'pagex' ), max( $paged, $page ) );
	}

	remove_filter( 'document_title_parts', 'pagex_remove_document_title_parts' );

	return $title;
}

/**
 * Generate href for dynamic link attribute
 *
 * @param $type
 *
 * @return string
 */
function pagex_get_dynamic_link( $type ) {
	switch ( $type ) {
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
			$link = pagex_get_custom_meta_value( $type );
			if ( ! $link ) {
				$link = '#';
			}
	}

	return $link;
}

/**
 * Used by Posts, Posts Loop elements to get right dynamic links for elements
 *
 * @param $html
 *
 * @return bool|simple_html_dom
 */
function pagex_generate_excerpt_template( $html ) {
	$html = str_get_html( $html );

	foreach ( $html->find( '[data-custom-link]' ) as $element ) {
		$element->innertext            = '<a class="pagex-custom-link-element d-none" ' . urldecode( $element->{'data-custom-link'} ) . '></a>' . $element->innertext;
		$element->{'onclick'}          = 'pagexCustomLink(this)';
		$element->{'data-custom-link'} = null;
	}

	// init HTML to apply new DOM elements: ex custom link dynamic data
	$html = $html->save();
	$html = str_get_html( $html );

	foreach ( $html->find( '[data-dynamic-link]' ) as $element ) {
		$element->href                  = pagex_get_dynamic_link( $element->{'data-dynamic-link'} );
		$element->{'data-dynamic-link'} = null;
	}

	$html = $html->save();

	return $html;
}

/**
 * Replace comments.php template from the theme to a plugin template to avoid style issues
 * Used by comments_template filter in post-comments element
 *
 * @param $comment_template
 *
 * @return string
 */
function pagex_comments_template( $comment_template ) {
	return PAGEX_DIR_NAME . '/inc/templates/comments.php';
}

/**
 * Retrieve all registered custom meta fields for a post which is in preview during editing
 *
 * @param string $type
 *
 * @return array
 */
function pagex_get_post_custom_keys( $type = 'text' ) {
	$groups = array();

	if ( function_exists( 'pods_api' ) ) {
		$media = array( 'file' );

		$all_pods = pods_api()->load_pods( array(
			'table_info' => true,
			'fields'     => true,
		) );

		foreach ( $all_pods as $group ) {
			$options   = array();
			// get type of pod so then we could get right meta
			switch ( $group['object_type'] ) {
                case 'taxonomy':
					$meta_type = 'term';
					break;
                case 'post_type':
					$meta_type = 'post';
					break;
				default:
				    // meta for users
	                $meta_type = $group['object_type'];
			}

			foreach ( $group['fields'] as $field ) {
				if ( $type != 'all' ) {
					if ( $type == 'text' && in_array( $field['type'], $media ) ) {
						continue;
					}

					if ( $type == 'media' && ! in_array( $field['type'], $media ) ) {
						continue;
					}
				}

				$options[ $meta_type . ':' . $field['name'] ] = $field['label'];
			}

			if ( empty( $options ) ) {
				continue;
			}

			$groups[] = array(
				'label'   => ucfirst( $group['name'] ),
				'options' => $options,
			);
		}
	}

	return $groups;
}

/**
 * Options for dynamic link attribute
 *
 * @return string
 */
function pagex_get_dynamic_url_keys() {
	$options = array(
		'post'     => __( 'Post Link', 'pagex' ),
		// todo taxonomy link
		//'taxonomy' => __( 'Taxonomy Link', 'pagex' ),
		'day'      => __( 'Day Link', 'pagex' ),
		'author'   => __( 'Post Author Link', 'pagex' ),
		'comments' => __( 'Comments Link', 'pagex' ),
	);

	$html = '<option value="">' . __( 'Dynamic', 'pagex' ) . '</option>';

	$html .= '<optgroup label="WordPress">';
	foreach ( $options as $key => $value ) {
		$html .= '<option value="' . $key . '">' . $value . '</option>';
	}
	$html .= '</optgroup>';

	$custom = pagex_get_post_custom_keys();

	if ( $custom ) {
		foreach ( $custom as $key => $value ) {
			$html .= '<optgroup label="' . $value['label'] . '">';
			foreach ( $value['options'] as $k => $v ) {
				$html .= '<option value="' . $k . '">' . $v . '</option>';
			}
			$html .= '</optgroup>';
		}
	}

	return $html;
}

/**
 * Options for dynamic link attribute
 *
 * @return array
 */
function pagex_get_dynamic_media_keys() {
	$options = array();

	$options['']          = __( 'Select Custom Meta Field', 'pagex' );
	$options['wordpress'] = array(
		'label'   => 'WordPress',
		'options' => array(
			'post:_thumbnail_id' => __( 'Featured Image', 'pagex' ),
			'user:author_avatar' => __( 'Author Avatar', 'pagex' ),
		)
	);

	$custom = pagex_get_post_custom_keys( 'media' );

	if ( $custom ) {
		foreach ( $custom as $array ) {
			$options[ $array['label'] ] = $array;
		}
	}

	return $options;
}

/**
 * Return meta value based on a key it could be post meta or term meta
 *
 * @param $key - example: post:gallery or term:main_bg
 *
 * @param bool $single
 *
 * @return string/array
 */
function pagex_get_custom_meta_value( $key, $single = true ) {
	$meta       = explode( ':', $key );
	$meta_type  = $meta[0];
	$meta_key   = $meta[1];
	$meta_value = '';

	// return meta value based on current/loop post
	switch ( $meta_type ) {
        case 'post':
			global $post;
			if ( $post ) {
				$meta_value = get_metadata( $meta_type, $post->ID, $meta_key, $single );
			}
			break;
        case 'user':
			$user_id    = get_the_author_meta( 'ID' );
			$meta_value = get_metadata( $meta_type, $user_id, $meta_key, $single );
			break;
	}
// todo taxonomy meta
// todo theme options: from options table
// todo custom meta
//	if ( $meta[0] == 'taxonomy' ) {
//		// return meta value based on current taxonomy
//		if ( isset( get_queried_object()->taxonomy ) && get_queried_object()->taxonomy == $meta[1] ) {
//			$meta_value = get_term_meta( get_queried_object()->term_id, $meta[2], $single );
//		}
//	}

	return $meta_value;
}