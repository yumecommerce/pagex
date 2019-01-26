<?php

class Pagex_WooCommerce {
	public function __construct() {
		// wrap main content to avoid style issues
		add_action( 'pagex_before_post_content', array( $this, 'pagex_before_post_content' ), 99 );
		add_action( 'pagex_after_post_content', array( $this, 'pagex_after_post_content' ), 99 );

		// add woocommerce active class
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		// Disable all woocommerce stylesheets to avoid code duplication
		// all default basic style like gallery will be enqueue all the time in frontend stylesheet
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

		// replace rating font star with svg icons
		add_filter( 'woocommerce_get_star_rating_html', array( $this, 'rating_html' ), 10, 3 );
		add_filter( 'woocommerce_product_review_comment_form_args', array( $this, 'review_comment_form' ), 10, 3 );
	}

	public function rating_html( $html, $rating, $count ) {
		$rating_number = round( $rating );
		$empty_stars   = 5 - $rating_number;

		$stars = '';

		for ( $i = 1; $i <= $rating_number; $i ++ ) {
			$stars .= '<i class="fas fa-star"></i>';
		}

		if ( $empty_stars ) {
			for ( $i = 1; $i <= $empty_stars; $i ++ ) {
				$stars .= '<i class="far fa-star"></i>';
			}
		}

		$html = Pagex_FontAwesome_SVG_Replace::replace( $stars );

		return $html;
	}

	public function review_comment_form( $comment_form ) {
		$html = str_get_html( $comment_form['comment_field'] );

		$stars = '<div class="pagex-woo-stars"><div class="star-0"><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i></div><div class="star-1 pagex-star-rating" data-rating-star="1"><i class="fas fa-star"></i></div><div class="star-2 pagex-star-rating" data-rating-star="2"><i class="fas fa-star"></i><i class="fas fa-star"></i></div><div class="star-3 pagex-star-rating" data-rating-star="3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div><div class="star-4 pagex-star-rating" data-rating-star="4"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div><div class="star-5 pagex-star-rating" data-rating-star="5"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div></div>';

		foreach ( $html->find( '#rating' ) as $element ) {
			$element->outertext = $element->outertext . Pagex_FontAwesome_SVG_Replace::replace( $stars );
		}

		$html->save();

		$comment_form['comment_field'] = $html;

		return $comment_form;
	}


	public function singular_template_active() {
		return is_singular( 'product' ) && function_exists( 'wc' ) && ! Pagex::is_frontend_builder_active();
	}

	public function singular_builder_template_active() {
		return Pagex::is_frontend_builder_frame_active() && is_singular( 'pagex_post_tmp' ) && function_exists( 'wc' );
	}

	/**
	 * Add woocommerce class to avoid style issues
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function body_classes( $classes ) {
		if ( Pagex::is_frontend_builder_frame_active() ) {
			if ( is_singular( 'pagex_post_tmp' ) && function_exists( 'wc' ) ) {
				$classes[] = 'woocommerce';
			}
		}

		return $classes;
	}

	/**
	 * Add content after pagex_post_content action
	 */
	public function pagex_after_post_content() {
		// add close div for wrapper of woo product
		if ( $this->singular_template_active() || $this->singular_builder_template_active() ) {
			echo '</div>';
		}
	}

	/**
	 * Add content before pagex_post_content action
	 */
	public function pagex_before_post_content() {
		// add wrapper for woo product
		if ( $this->singular_template_active() || $this->singular_builder_template_active() ) {
			echo '<div id="product-' . get_the_ID() . '" class="' . esc_attr( join( ' ', wc_get_product_class() ) ) . '">';
		}
	}

	/**
	 * Enqueue woo scripts when we edit post templates for a single product
	 */
	public static function maybe_enqueue_scripts() {
		if ( ! function_exists( 'wc' ) ) {
			return;
		}

		if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
			wp_enqueue_script( 'zoom' );
		}
		if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
			wp_enqueue_script( 'flexslider' );
		}
		if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
			wp_enqueue_script( 'photoswipe-ui-default' );
			wp_enqueue_style( 'photoswipe-default-skin' );
			add_action( 'wp_footer', 'woocommerce_photoswipe' );
		}
		wp_enqueue_script( 'wc-single-product' );

		wp_enqueue_style( 'photoswipe' );
		wp_enqueue_style( 'photoswipe-default-skin' );
		wp_enqueue_style( 'photoswipe-default-skin' );
		wp_enqueue_style( 'woocommerce_prettyPhoto_css' );
	}
}

new Pagex_WooCommerce();