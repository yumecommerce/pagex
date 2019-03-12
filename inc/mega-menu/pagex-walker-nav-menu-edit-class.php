<?php

class Pagex_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

	/**
	 * Starts the element output.
	 *
	 * Calls the Walker_Nav_Menu_Edit start_el function and then injects the custom field HTML
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param WP_Post $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args An object of wp_nav_menu() arguments.
	 * @param int $id Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_output = '';
		parent::start_el( $item_output, $item, $depth, $args, $id );

		// inject custom field HTML before field-move class
		$output .= preg_replace(
			'/(?=<(fieldset|p)[^>]+class="[^"]*field-move)/',
			$this->get_fields( $item, $depth, $args, $id ),
			$item_output
		);
	}


	/**
	 * Get custom fields HTML
	 *
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args Menu item args.
	 * @param int $id Nav menu ID.
	 *
	 * @return string
	 */
	function get_fields( $item, $depth, $args = array(), $id = 0 ) {
		ob_start();

		do_action( 'pagex_nav_menu_item_custom_fields', $item->ID, $item, $depth, $args, $id );

		return ob_get_clean();
	}
}
