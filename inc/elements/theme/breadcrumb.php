<?php

defined( 'ABSPATH' ) || exit;

function pagex_register_breadcrumb_element( $elements ) {
	$elements[] = array(
		'id'          => 'breadcrumb',
		'category'    => 'theme-elements',
		'post_type'   => array( 'pagex_post_tmp' ),
		'title'       => __( 'Breadcrumb', 'pagex' ),
		'description' => __( 'Path-based navigation', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_breadcrumb',
		'options'     => array(
			array(
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Conditions', 'pagex' ),
					),
					array(
						'id'          => 'display_tax_name',
						'type'        => 'checkbox',
						'label'       => __( 'Display taxonomy name for archive pages', 'pagex' ),
						'description' => __( 'For custom taxonomies', 'pagex' ),
						'value'       => 'true',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Style', 'pagex' ),
					),
					array(
						'id'       => 'typo',
						'type'     => 'typography',
						'selector' => '.pagex-breadcrumb',
					),
					array(
						'id'       => 'mc',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-6',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] {color: [val]}',
					),
					array(
						'id'         => 'align',
						'title'      => __( 'Alignment', 'pagex' ),
						'type'       => 'select',
						'class'      => 'col-4',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-breadcrumb {justify-content: [val]}',
						'options'    => array(
							''         => __( 'Left', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Right', 'pagex' ),
						)
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Links', 'pagex' ),
					),
					array(
						'id'       => 'co',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a {color: [val]}',
					),
					array(
						'id'       => 'ch',
						'title'    => __( 'Color', 'pagex' ) . ' ' . __( 'on Hover', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] a:hover {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Delimiter', 'pagex' ),
					),
					array(
						'id'    => 'delimiter',
						'title' => __( 'Delimiter', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-2',
					),
					array(
						'id'       => 'dc',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .delimiter {color: [val]}',
					),
					array(
						'id'       => 'm',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'dimension',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .delimiter {margin: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Breadcrumb shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_breadcrumb( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'delimiter'        => '&nbsp;&#47;&nbsp;',
		'display_tax_name' => false,
	) );

	$breadcrumb = new Pagex_Breadcrumb( $data );
	$breadcrumb->add_crumb( _x( 'Main Page', 'breadcrumb', 'pagex' ), home_url() );
	$bread_data = $breadcrumb->generate();

	ob_start();

	if ( ! empty( $bread_data ) ) {
		echo '<nav class="pagex-breadcrumb">';
		foreach ( $bread_data as $key => $crumb ) {
			if ( ! empty( $crumb[1] ) && sizeof( $bread_data ) !== $key + 1 ) {
				echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
			} else {
				echo '<span class="">' . esc_html( $crumb[0] ) . '</span>';
			}

			if ( sizeof( $bread_data ) !== $key + 1 ) {
				echo '<span class="delimiter">' . htmlspecialchars_decode( $data['delimiter'] ) . '</span>';
			}
		}
		echo '</nav>';
	}

	return ob_get_clean();
}

/**
 * Breadcrumb Class
 */
class Pagex_Breadcrumb {
	/**
	 * Breadcrumb trail.
	 *
	 * @var array
	 */
	private $crumbs = array();

	/**
	 * Display taxonomy name for archive pages
	 */
	private $display_tax_name = false;

	public function __construct( $data ) {
		$this->display_tax_name = $data['display_tax_name'];
	}

	/**
	 * Add a crumb so we don't get lost.
	 *
	 * @param string $name Name.
	 * @param string $link Link.
	 */
	public function add_crumb( $name, $link = '' ) {
		$this->crumbs[] = array(
			strip_tags( $name ),
			$link,
		);
	}

	/**
	 * Reset crumbs.
	 */
	public function reset() {
		$this->crumbs = array();
	}

	/**
	 * Get the breadcrumb.
	 *
	 * @return array
	 */
	public function get_breadcrumb() {
		return $this->crumbs;
	}

	/**
	 * Generate breadcrumb trail.
	 *
	 * @return array of breadcrumbs
	 */
	public function generate() {
		$conditionals = array(
			'is_home',
			'is_404',
			'is_attachment',
			'is_single',
			'is_product_category',
			'is_product_tag',
			'is_shop',
			'is_page',
			'is_post_type_archive',
			'is_category',
			'is_tag',
			'is_author',
			'is_date',
			'is_tax',
		);

		if ( ! is_front_page() ) {
			foreach ( $conditionals as $conditional ) {
				if ( function_exists( $conditional ) && call_user_func( $conditional ) ) {
					call_user_func( array( $this, 'add_crumbs_' . substr( $conditional, 3 ) ) );
					break;
				}
			}

			$this->search_trail();
			$this->paged_trail();

			return $this->get_breadcrumb();
		}

		return array();
	}

	/**
	 * Prepend the shop page to shop breadcrumbs.
	 */
	private function prepend_shop_page() {
		$permalinks   = wc_get_permalink_structure();
		$shop_page_id = wc_get_page_id( 'shop' );
		$shop_page    = get_post( $shop_page_id );


		// If permalinks contain the shop page in the URI prepend the breadcrumb with shop.
		if ( $shop_page_id && $shop_page && isset( $permalinks['product_base'] ) && strstr( $permalinks['product_base'], '/' . $shop_page->post_name ) && intval( get_option( 'page_on_front' ) ) !== $shop_page_id ) {
			$this->add_crumb( get_the_title( $shop_page ), get_permalink( $shop_page ) );
		}
	}

	/**
	 * Product category trail.
	 */
	private function add_crumbs_product_category() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_shop_page();
		$this->term_ancestors( $current_term->term_id, 'product_cat' );
		$this->add_crumb( $current_term->name );
	}

	/**
	 * Product tag trail.
	 */
	private function add_crumbs_product_tag() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_shop_page();

		/* translators: %s: product tag */
		$this->add_crumb( sprintf( __( 'Products tagged &ldquo;%s&rdquo;', 'woocommerce' ), $current_term->name ) );
	}

	/**
	 * Shop breadcrumb.
	 */
	private function add_crumbs_shop() {
		if ( intval( get_option( 'page_on_front' ) ) === wc_get_page_id( 'shop' ) ) {
			return;
		}

		$_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';

		if ( ! $_name ) {
			$product_post_type = get_post_type_object( 'product' );
			$_name             = $product_post_type->labels->singular_name;
		}

		$this->add_crumb( $_name, get_post_type_archive_link( 'product' ) );
	}

	/**
	 * Is home trail..
	 */
	private function add_crumbs_home() {
		$this->add_crumb( single_post_title( '', false ) );
	}

	/**
	 * Prepend the blog page
	 *
	 */
	private function prepend_blog_page() {
		$blog_page = get_option( 'page_for_posts' );
		if ( $blog_page ) {
			$this->add_crumb( get_the_title( $blog_page ), get_permalink( $blog_page ) );
		}
	}

	/**
	 * 404 trail.
	 */
	private function add_crumbs_404() {
		$this->add_crumb( '404' );
	}

	/**
	 * Attachment trail.
	 */
	private function add_crumbs_attachment() {
		global $post;

		$this->add_crumbs_single( $post->post_parent, get_permalink( $post->post_parent ) );
		$this->add_crumb( get_the_title(), get_permalink() );
	}

	/**
	 * Single post trail.
	 *
	 * @param int $post_id Post ID.
	 * @param string $permalink Post permalink.
	 */
	private function add_crumbs_single( $post_id = 0, $permalink = '' ) {
		if ( ! $post_id ) {
			global $post;
		} else {
			$post = get_post( $post_id ); // WPCS: override ok.
		}

		$post_type = get_post_type( $post );

		if ( 'product' === $post_type ) {
			$this->prepend_shop_page();

			$terms = wc_get_product_terms(
				$post->ID, 'product_cat', apply_filters(
					'woocommerce_breadcrumb_product_terms_args', array(
						'orderby' => 'parent',
						'order'   => 'DESC',
					)
				)
			);

			if ( $terms ) {
				$main_term = apply_filters( 'woocommerce_breadcrumb_main_term', $terms[0], $terms );
				$this->term_ancestors( $main_term->term_id, 'product_cat' );
				$this->add_crumb( $main_term->name, get_term_link( $main_term ) );
			}
		} elseif ( 'post' !== $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			//if ( ! empty( $post_type->has_archive ) ) {
			$this->add_crumb( $post_type_object->labels->name, get_post_type_archive_link( $post_type ) );
			//}

			if ( $taxonomies = get_post_taxonomies( $post ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					if ( has_term( '', $taxonomy ) ) {
						$post_terms = get_the_terms( $post, $taxonomy );

						if ( $post_terms && ! is_wp_error( $post_terms ) ) {
							$post_term = $post_terms[0];
							$this->term_ancestors( $post_term->term_id, $taxonomy );
							$this->add_crumb( $post_term->name, get_term_link( $post_term ) );
						}

						break;
					}
				}
			}
		} else {
			$this->prepend_blog_page();

			$cat = current( get_the_category( $post ) );
			if ( $cat ) {
				$this->term_ancestors( $cat->term_id, 'category' );
				$this->add_crumb( $cat->name, get_term_link( $cat ) );
			}
		}

		$this->add_crumb( get_the_title( $post ), $permalink );
	}

	/**
	 * Page trail.
	 */
	private function add_crumbs_page() {
		global $post;

		if ( $post->post_parent ) {
			$parent_crumbs = array();
			$parent_id     = $post->post_parent;

			while ( $parent_id ) {
				$page            = get_post( $parent_id );
				$parent_id       = $page->post_parent;
				$parent_crumbs[] = array( get_the_title( $page->ID ), get_permalink( $page->ID ) );
			}

			$parent_crumbs = array_reverse( $parent_crumbs );

			foreach ( $parent_crumbs as $crumb ) {
				$this->add_crumb( $crumb[0], $crumb[1] );
			}
		}

		$this->add_crumb( get_the_title(), get_permalink() );
	}

	/**
	 * Post type archive trail.
	 */
	private function add_crumbs_post_type_archive() {
		$post_type = get_post_type_object( get_post_type() );

		if ( $post_type ) {
			$this->add_crumb( $post_type->labels->name, get_post_type_archive_link( get_post_type() ) );
		}
	}

	/**
	 * Category trail.
	 */
	private function add_crumbs_category() {

		$this->prepend_blog_page();

		$this_category = get_category( $GLOBALS['wp_query']->get_queried_object() );

		if ( 0 !== intval( $this_category->parent ) ) {
			$this->term_ancestors( $this_category->term_id, 'category' );
		}

		$this->add_crumb( single_cat_title( '', false ), get_category_link( $this_category->term_id ) );
	}

	/**
	 * Tag trail.
	 */
	private function add_crumbs_tag() {
		$queried_object = $GLOBALS['wp_query']->get_queried_object();
		$this->add_crumb( single_tag_title( '', false ), get_tag_link( $queried_object->term_id ) );
	}

	/**
	 * Add crumbs for date based archives.
	 */
	private function add_crumbs_date() {
		if ( is_year() || is_month() || is_day() ) {
			$this->add_crumb( get_the_time( 'Y' ), get_year_link( get_the_time( 'Y' ) ) );
		}
		if ( is_month() || is_day() ) {
			$this->add_crumb( get_the_time( 'F' ), get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) );
		}
		if ( is_day() ) {
			$this->add_crumb( get_the_time( 'd' ) );
		}
	}

	/**
	 * Add crumbs for taxonomies
	 */
	private function add_crumbs_tax() {
		$this_term = $GLOBALS['wp_query']->get_queried_object();
		$taxonomy  = get_taxonomy( $this_term->taxonomy );

		$this->add_crumbs_post_type_archive();

		if ( $this->display_tax_name ) {
			$this->add_crumb( $taxonomy->labels->name );
		}

		if ( 0 !== intval( $this_term->parent ) ) {
			$this->term_ancestors( $this_term->term_id, $this_term->taxonomy );
		}

		$this->add_crumb( single_term_title( '', false ), get_term_link( $this_term->term_id, $this_term->taxonomy ) );
	}

	/**
	 * Add a breadcrumb for author archives.
	 */
	private function add_crumbs_author() {
		global $author;

		$userdata = get_queried_object();

		/* translators: %s: author name */
		$this->add_crumb( sprintf( $userdata->display_name ) );
	}

	/**
	 * Add crumbs for a term.
	 *
	 * @param int $term_id Term ID.
	 * @param string $taxonomy Taxonomy.
	 */
	private function term_ancestors(
		$term_id, $taxonomy
	) {
		$ancestors = get_ancestors( $term_id, $taxonomy );
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor ) {
			$ancestor = get_term( $ancestor, $taxonomy );

			if ( ! is_wp_error( $ancestor ) && $ancestor ) {
				$this->add_crumb( $ancestor->name, get_term_link( $ancestor ) );
			}
		}
	}

	/**
	 * Add a breadcrumb for search results.
	 */
	private function search_trail() {
		if ( is_search() ) {
			/* translators: %s: search term */
			add_filter( 'document_title_parts', 'pagex_remove_document_title_parts' );
			$this->add_crumb( wp_get_document_title() );
			remove_filter( 'document_title_parts', 'pagex_remove_document_title_parts' );
		}
	}

	/**
	 * Add a breadcrumb for pagination.
	 */
	private function paged_trail() {
		if ( get_query_var( 'paged' ) ) {
			/* translators: %d: page number */
			$this->add_crumb( sprintf( __( 'Page %d', 'pagex' ), get_query_var( 'paged' ) ) );
		}
	}
}