<?php

class Pagex_WooCommerce {
	public function __construct() {
		// wrap main content to avoid style issues
		add_filter( 'pagex_template_class', array( $this, 'add_product_classes' ) );

		// Disable all woocommerce stylesheets to avoid code duplication
		// all default basic style like gallery will be enqueue all the time in frontend stylesheet
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

		// replace rating font star with svg icons
		add_filter( 'woocommerce_get_star_rating_html', array( $this, 'rating_html' ), 10, 3 );
		add_filter( 'woocommerce_product_review_comment_form_args', array( $this, 'review_comment_form' ), 10, 3 );
	}

	/**
	 * Add product classes to #main wrapper
	 */
	public function add_product_classes( $classes ) {
		if ( is_singular( 'product' ) ) {
			$classes[] = join( ' ', wc_get_product_class() );
		}

		return $classes;
	}

	/**
	 * Add SVG rating
	 *
	 * @param $html
	 * @param $rating
	 * @param $count
	 *
	 * @return null|string|string[]|void
	 */
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

	/**
	 * Change stars review to SVG
	 *
	 * @param $comment_form
	 *
	 * @return mixed
	 */
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
}

new Pagex_WooCommerce();