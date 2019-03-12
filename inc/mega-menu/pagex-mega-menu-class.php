<?php

class Pagex_Mega_Menu_Class {
	public function __construct() {
		// replace walker
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'wp_edit_nav_menu_walker' ), 20, 2 );
		// add and save icon select for nav edit screen
		add_action( 'pagex_nav_menu_item_custom_fields', array( $this, 'add_controls' ), 10, 5 );
		add_action( 'wp_update_nav_menu', array( $this, 'update_nav_menu' ) );
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'add_custom_nav_fields' ) );
		add_action( 'admin_footer', array( $this, 'add_scripts' ) );

		// add icons and additional classes for menu nav
		add_filter( 'wp_nav_menu_objects', array( $this, 'mega_menu_items' ) );

		// add class attr to nav links
		add_filter( 'nav_menu_link_attributes', array( $this, 'add_link_attributes' ), 10, 4 );

		// display layout content instead of simple link for menu nav
		add_filter( 'walker_nav_menu_start_el', array( $this, 'mega_menu_content' ), 10, 4 );
	}

	/**
	 * Replace walker for WP edit menu class
	 *
	 * @param $walker
	 * @param $menu_id
	 *
	 * @return string
	 */
	public function wp_edit_nav_menu_walker( $walker, $menu_id ) {
		if ( class_exists( 'Walker_Nav_Menu_Edit' ) ) {
			include PAGEX_DIR_NAME . '/inc/mega-menu/pagex-walker-nav-menu-edit-class.php';
		}

		return 'Pagex_Walker_Nav_Menu_Edit';
	}

	/**
	 * Add icon select control for each item menu on admin nav-menus.php
	 *
	 * @param $item_ID
	 * @param $item
	 * @param $depth
	 * @param $args
	 * @param $id
	 */
	public function add_controls( $item_ID, $item, $depth, $args, $id ) {
		// no icon for mega menu item
		if ( $item->object == 'pagex_layout_builder' ) {
			return;
		}

		$icon = wp_parse_args( get_post_meta( $item_ID, '_pagex_menu_item_icon', true ), array(
			'type' => '',
			'fa'   => '',
			'svg'  => '',
		) );

		$mega = wp_parse_args( get_post_meta( $item_ID, '_pagex_menu_item_mega', true ), array(
			'width' => '',
			'pos'   => ''
		) );

		// Icon
		echo '<div class="pagex-menu-item-edit icon-' . $icon['type'] . '-active">';
		echo '<p class="description description-wide"><label>' . __( 'Icon', 'pagex' ) . '</label><select class="pagex-menu-item-type widefat" name="pagex-menu-item-icon[' . $item_ID . '][type]"><option value="">' . __( 'None', 'pagex' ) . '</option><option value="fa" ' . selected( 'fa', $icon['type'], false ) . '>Font Awesome</option><option value="svg" ' . selected( 'svg', $icon['type'], false ) . '>' . __( 'SVG or Base 64', 'pagex' ) . '</option></select></p>';
		echo '<p class="pagex-menu-item-icon pagex-menu-item-icon-fa-wrapper description description-wide"><label>' . __( 'Font Awesome Icon', 'pagex' ) . '</label><i class="' . $icon['fa'] . '"></i><input type="text" class="pagex-menu-item-type-icon-fa widefat" name="pagex-menu-item-icon[' . $item_ID . '][fa]" value="' . $icon['fa'] . '"><button type="button" class="button">' . __( 'Select', 'pagex' ) . '</button></p>';
		echo '<p class="pagex-menu-item-icon pagex-menu-item-icon-svg-wrapper description description-wide"><label>' . __( 'Icon HTML Code', 'pagex' ) . '</label><textarea type="text" class="pagex-menu-item-type-icon-svg widefat" name="pagex-menu-item-icon[' . $item_ID . '][svg]" rows="3" cols="20">' . $icon['svg'] . '</textarea><span>' . __( 'Insert inline SVG code or inline HTML with encoded image. If you wish SVG inherits color from element settings add attribute fill="currentColor" to each SVG path.', 'pagex' ) . '</span></p>';
		echo '</div>';

		// Mega menu
		echo '<div class="pagex-menu-item-edit">';
		echo '<p class="description description-wide">' . __( 'To add mega menu, set Layout Builder as a submenu item.', 'pagex' ) . '</p>';
		echo '<p class="description description-thin"><label>' . __( 'Mega Menu Item Width', 'pagex' ) . '</label><input type="number" class="widefat" name="pagex-menu-item-mega[' . $item_ID . '][width]" value="' . $mega['width'] . '"><span>' . __( 'By default equals the width of menu element.', 'pagex' ) . '</span></p>';
		echo '<p class="description description-thin"><label>' . __( 'Mega Menu Position', 'pagex' ) . '</label><select class="widefat" name="pagex-menu-item-mega[' . $item_ID . '][pos]"><option value="">' . __( 'Left', 'pagex' ) . '</option><option value="right" ' . selected( 'right', $mega['pos'], false ) . '>' . __( 'Right', 'pagex' ) . '</option></select><span>' . __( 'Horizontal positioning when custom width is set.', 'pagex' ) . '</span></p>';

		echo '</div>';
	}

	/**
	 * Save all custom params for nav
	 *
	 * @param $menu_id
	 */
	function update_nav_menu( $menu_id ) {
		if ( ! empty( $_POST['pagex-menu-item-icon'] ) ) {
			foreach ( $_POST['pagex-menu-item-icon'] as $post_id => $value ) {
				if ( isset( $value['type'] ) && $value['type'] ) {
					if ( $value['type'] == 'fa' && $value['fa'] ) {
						// convert font awesome icon to svg
						$value['svg'] = Pagex_FontAwesome_SVG_Replace::replace( '<i class="' . $value['fa'] . ' pagex-icon"></i>' );
					} elseif ( $value['type'] == 'svg' && $value['svg'] ) {
						// add default class to HTML icon
						$value['svg'] = preg_replace( '/((svg|img).*?)(class=".*?")/m', '$1', $value['svg'] );
						$value['svg'] = preg_replace( '/((svg|img).*?)(>)/m', '$1 class="pagex-icon" $3', $value['svg'] );
					}

					update_post_meta( $post_id, '_pagex_menu_item_icon', $value );
				} else {
					delete_post_meta( $post_id, '_pagex_menu_item_icon' );
				}
			}
		}

		if ( ! empty( $_POST['pagex-menu-item-mega'] ) ) {
			foreach ( $_POST['pagex-menu-item-mega'] as $post_id => $value ) {
				if ( isset( $value['width'] ) && $value['width'] ) {
					update_post_meta( $post_id, '_pagex_menu_item_mega', $value );
				} else {
					delete_post_meta( $post_id, '_pagex_menu_item_mega' );
				}
			}
		}
	}

	/**
	 * Make custom fields accessible from nav item obj
	 *
	 * @param $menu_item
	 *
	 * @return mixed
	 */
	public function add_custom_nav_fields( $menu_item ) {
		$menu_item->icon = get_post_meta( $menu_item->ID, '_pagex_menu_item_icon', true );
		$menu_item->mega = get_post_meta( $menu_item->ID, '_pagex_menu_item_mega', true );

		return $menu_item;
	}

	/**
	 * Add class to parent <li> if it has mega menu type item menu
	 *
	 * @param $items
	 *
	 * @return mixed
	 */
	function mega_menu_items( $items ) {
		$itemsMega = array();

		foreach ( $items as $item ) {
			// find all parents with mega menu siblings
			if ( $item->object == 'pagex_layout_builder' ) {
				$itemsMega[] = $item->menu_item_parent;
			}
		}

		// if item has child mega menu add class
		foreach ( $items as $item ) {
			in_array( $item->ID, $itemsMega ) && $item->classes[] = 'has-mega-menu';
		}

		foreach ( $items as $item ) {
			// add span around title so it would be easy to control with CSS
			$item->title = '<span class="pagex-nav-item-title">' . $item->title . '</span>';

//			if ( $item->description ) {
//				$item->title = $item->title . '<span class="pagex-nav-item-description">' . $item->description . '</span>';
//			}

			if ( $item->icon && $item->icon['svg'] ) {
				$item->title     = '<i class="pagex-nav-item-icon">' . $item->icon['svg'] . '</i>' . $item->title;
				$item->classes[] = 'has-icon';
			}

			// add class and print style for custom width nav items
			if ( $item->mega && $item->mega['width'] ) {
				$item->title = '<style>.pagex-nav-menu-desktop li.menu-item.menu-item-' . $item->ID . ' > ul {min-width: ' . $item->mega['width'] . 'px}</style>' . $item->title;

				$item->classes[] = 'mega-menu-custom-width';

				if ( $item->mega['pos'] ) {
					$item->classes[] = 'mega-menu-position-right';
				}
			}
		}

		return $items;
	}

	/**
     * Add nav link class to avoid global rules in CSS with mega menu
     *
	 * @param $atts
	 * @param $item
	 * @param $args
	 * @param $depth
	 *
	 * @return mixed
	 */
	public function add_link_attributes($atts, $item, $args, $depth) {
		$atts['class'] = 'nav-link';

		return $atts;
    }

	/**
	 * Make layouts which assigned as nav item children display content
	 *
	 * @param $item_output
	 * @param $item
	 * @param $depth
	 * @param $args
	 *
	 * @return mixed
	 */
	function mega_menu_content( $item_output, $item, $depth, $args ) {
		$menu_id = $args->menu;
		// display layout content instead of nav link
		if ( $item->type == 'post_type' && $item->object == 'pagex_layout_builder' && $depth > 0 ) {
			$output = get_post_field( 'post_content', $item->object_id );

			// prevent infinite loop when layout content has menu with the same id as main one
			if ( preg_match( '/menu%22%3A%22' . $menu_id . '%22/m', $output ) ) {
				$output = preg_replace( '/%22' . $menu_id . '%22/m', '%22%22', $output );
			}

			$item_output = do_shortcode( $output );
		}

		return $item_output;
	}


	/**
	 * Print inline style and script for basic functionality
	 */
	public function add_scripts() {
		global $pagenow;

		if ( $pagenow != 'nav-menus.php' ) {
			return;
		}

		wp_enqueue_style( 'fontawesome' );

		$fa_icons = Pagex_Editor_Control_Attributes::get_font_awesome();

		echo '<div id="pagex-fa-menu-icons"><div id="pagex-fa-menu-icons-back"></div><div id="pagex-fa-menu-icons-wrapper"><ul>';
		foreach ( $fa_icons as $k => $v ) {
			echo '<li><i class="' . $k . '"></i></li>';
		}
		echo '</ul></div></div>';

		?>

        <style>
            #pagex-fa-menu-icons {
                position: fixed;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                z-index: 99999;
                display: none;
            }

            #pagex-fa-menu-icons-back {
                background: rgba(0, 0, 0, .6);
                position: absolute;
                width: 100%;
                height: 100%;
                cursor: pointer;
            }

            #pagex-fa-menu-icons-wrapper {
                width: 1000px;
                height: 400px;
                background: #fff;
                z-index: 22;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translateX(-50%) translateY(-50%);
                padding: 20px;
                overflow: auto;
            }

            #pagex-fa-menu-icons li {
                margin: 0;
                display: block;
                width: 31px;
                height: 31px;
                line-height: 31px;
                text-align: center;
                font-size: 18px;
                color: #0a0a0a;
                transition: .1s;
                cursor: pointer;
            }

            #pagex-fa-menu-icons li:hover {
                transform: scale(1.3);
                color: #00aef0;
            }

            #pagex-fa-menu-icons ul {
                list-style: none;
                display: grid;
                grid-template-columns: repeat(24, 31px);
                grid-column-gap: 10px;
                grid-row-gap: 12px;
            }

            .pagex-menu-item-edit {
                float: left;
                width: 100%;
                clear: both;
                margin: 10px 0 15px;
            }

            .pagex-menu-item-icon {
                display: none;
            }

            .icon-fa-active .pagex-menu-item-icon-fa-wrapper {
                display: grid;
                grid-template-columns: 30px auto 100px;
            }

            .pagex-menu-item-icon-fa-wrapper label {
                grid-column-start: 1;
                grid-column-end: span 3;
            }

            .pagex-menu-item-icon-fa-wrapper button {
                margin-left: 10px !important;
            }

            .pagex-menu-item-icon-fa-wrapper i {
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid #ddd;
                margin-right: -1px;
                color: #00aef0;
                line-height: 0;
                font-size: 12px;
            }

            .icon-svg-active .pagex-menu-item-icon-svg-wrapper {
                display: block;
            }
        </style>

        <script>
            (function ($) {
                var faIconInput, faIcon;

                $(document).on('change', '.pagex-menu-item-type', function () {
                    var v = $(this).val(),
                        p = $(this).parents('.pagex-menu-item-edit');

                    p.removeClass('icon-fa-active icon-svg-active');

                    if (v) {
                        p.addClass('icon-' + v + '-active');
                    }
                });

                $(document).on('click', '.pagex-menu-item-icon-fa-wrapper button', function () {
                    faIconInput = $(this).parents('.pagex-menu-item-icon-fa-wrapper').find('input');
                    faIcon = $(this).parents('.pagex-menu-item-icon-fa-wrapper').find('i');
                    $('#pagex-fa-menu-icons').fadeIn();
                });

                $(document).on('click', '#pagex-fa-menu-icons-back', function () {
                    $('#pagex-fa-menu-icons').fadeOut();
                });

                $(document).on('click', '#pagex-fa-menu-icons i', function () {
                    let cl = $(this).attr('class');
                    faIconInput.val(cl);
                    faIcon.attr('class', cl);

                    $('#pagex-fa-menu-icons').fadeOut();
                });
            })(jQuery);
        </script>
		<?php
	}
}

new Pagex_Mega_Menu_Class();