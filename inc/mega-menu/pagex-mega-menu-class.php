<?php

class Pagex_Mega_Menu_Class {
	public function __construct() {
		//add_filter('walker_nav_menu_start_el', array($this, 'add_items_to_start_el'), 10, 4);
	}

	public function add_items_to_start_el($item_output, $item, $depth, $args) {
		//return '1';
	}
}

// new Pagex_Mega_Menu_Class();