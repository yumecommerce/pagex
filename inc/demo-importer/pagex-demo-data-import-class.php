<?php

class Pagex_Demo_Data_Import {

	protected $existing_post;

	public function __construct() {
		// force the post to be imported
		add_filter( 'wp_import_existing_post', array( $this, 'wp_import_existing_post' ), 10, 2 );
		add_filter( 'wp_import_post_data_processed', array( $this, 'wp_import_post_data_processed' ), 10, 2 );

		// admin import demo notice
		add_action( 'admin_notices', array( $this, 'alert' ) );

		// ajax function for import
		add_action( 'wp_ajax_pagex_demo_content_import', array( $this, 'import' ) );
	}

	/**
	 * Force the post to be imported
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return int
	 */
	function wp_import_existing_post( $post_id, $post ) {
		if ( $this->existing_post = $post_id ) {
			if ( get_post_type( $post_id ) != 'attachment' ) {
				global $wpdb;
				// delete existing meta
				$wpdb->delete( 'wp_postmeta', array( 'post_id' => $post_id ) );

				// force the post to be imported
				$post_id = 0;
			}
		}

		return $post_id;
	}

	/**
	 * Add the post meta to import for forced post
	 *
	 * @param $postdata
	 * @param $post
	 *
	 * @return mixed
	 */
	function wp_import_post_data_processed( $postdata, $post ) {
		if ( $this->existing_post ) {
			$postdata['ID'] = $this->existing_post;
		}

		return $postdata;
	}

	/**
	 * Notice window with demo content import instructions
	 */
	public function alert() {
		$settings = Pagex::get_settings();

		if ( isset( $settings['demo_data_imported'] ) && $settings['demo_data_imported'] == 'yes' ) {
			return;
		}

		$import_data = apply_filters( 'pagex_import_demo_data', array() );

		// theme might has no data to import
		if ( ! $import_data ) {
			return;
		}

		// check dependencies
		if ( isset( $import_data['dependencies'] ) ) {
			foreach ( $import_data['dependencies'] as $function_name ) {
				if ( ! is_callable( $function_name ) ) {
					return;
				}
			}
		}

		// prevent clicks while import is in action
		echo '<style>.pagex-no-click {pointer-events: none}</style>';

		echo '<div class="notice notice-info" id="pagex_demo_content_import_alert" style="margin: 30px 0;"><h2 style="margin-bottom: 10px; font-size:20px;">' . __( 'Demo Data Import', 'pagex' ) . '</h2><p style="color: #72777c; margin-bottom: 15px;">' . __( 'Great, after you have installed and activated all required plugins, it is time to import demo content.', 'pagex' ) . '<br><br><span style="color: #d20e00"><b>' . __( 'Note!', 'pagex' ) . '</b></span> ' . __( 'Import demo content only on a fresh WordPress site since some of your existing content might be replaced.', 'pagex' ) . '<br>' . __( 'This is a required step! Demo Data contains post and page templates which are used by WordPress to display initial content of your website.', 'pagex' ) . '<br></p><form id="pagex-demo-content-import-form"><input type="submit" class="button button-primary button-hero" id="pagex-demo-import-button" data-importing="' . __( 'Importing... Please wait.', 'pagex' ) . '" value="' . __( 'Import Demo Data', 'pagex' ) . '"></form><div style="font-size: 14px;" id="pagex-demo-content-import-form-response"></div><p></p></div>';

		echo '<script>
			jQuery("#pagex-demo-content-import-form").submit(function() {
				var btn = jQuery(this).find(".button");

				btn.val(btn.attr("data-importing"));
					
				jQuery("body").addClass("pagex-no-click");
	
				jQuery.post( "' . admin_url( 'admin-ajax.php' ) . '", {action: "pagex_demo_content_import"}, function( response ) {
					btn.remove();
					jQuery("body").removeClass("pagex-no-click");
					jQuery("#pagex-demo-content-import-form-response").html(response.data);
				}).fail(function() {
					alert( "Error. Something went wrong, please try again." );
					btn.remove();
					jQuery("body").removeClass("pagex-no-click");
				});
				return false;
			});
		</script>';
	}

	/**
	 * Import demo via ajax
	 */
	public function import() {
		///
		///
		///
		///
		wp_send_json_success( __( 'All Done! Demo data was successfully installed.', 'pagex' ) );
		///
		///
		///
		///

		// try to update PHP memory limit
		ini_set( 'memory_limit', '350M' );
		set_time_limit( 300 );

		if ( ! is_super_admin() ) {
			wp_send_json_error( __( 'Only super admin can import the content.', 'pagex' ) );
		}

		/*
		* WordPress Importer
		* Version: 0.6.4
		* https://wordpress.org/plugins/wordpress-importer/
		*/
		if ( ! class_exists( 'WP_Import' ) ) {
			require dirname( __FILE__ ) . '/wordpress-importer/wordpress-importer.php';
		}

		$demo_data = apply_filters( 'pagex_import_demo_data', array() );

		// initial demo url
		$from_url = $demo_data['demo_url'];

		// path to demo xml file
		$path = $demo_data['path'];

		if ( ! $path || ! is_file( $path ) ) {
			wp_send_json_error( __( 'Failed to find the sample page file.', 'pagex' ) );
		}

		try {
			ob_start();

			$importer                    = new WP_Import();
			$importer->fetch_attachments = true;
			$importer->import( $path );

			ob_end_clean();
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Failed to import:', 'pagex' ) . ' ' . $e->getMessage() );
		}

		// import additional third party plugin settings
		if ( isset( $demo_data['migration'] ) ) {
			if ( isset( $demo_data['migration']['pods'] ) ) {
				if ( class_exists( 'Pods_Migrate_Packages' ) ) {
					// import pods with second parameter as true to replace old settings
					$pods_imported = Pods_Migrate_Packages::import( $demo_data['migration']['pods'], true );
				}
			}
		}

		// import widgets
		if ( isset( $demo_data['widgets'] ) ) {
			$this->import_widgets( $demo_data['widgets'] );
		}

		// update Pagex plugin settings
		if ( isset( $demo_data['settings'] ) ) {
			update_option( 'pagex_settings', json_decode( $demo_data['settings'] ) );
		}

		// after import action
		do_action( 'pagex_after_import_demo_content' );

		// make all meta boxes available for nav menu
		// it makes all custom post types visible
		$initial_meta_boxes = array();
		$user               = wp_get_current_user();
		update_user_option( $user->ID, 'metaboxhidden_nav-menus', $initial_meta_boxes, true );

		// enable permalink
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules();

		// remap urls in post content and post meta
		global $wpdb;
		$to_url = site_url();
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url ) );
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s)", $from_url, $to_url ) );

		// send success message
		wp_send_json_success( __( 'All Done! Demo data was successfully installed.', 'pagex' ) . ' ' . '<a href="' . $to_url . '">' . __( 'Visit Site.', 'pagex' ) . '</a>' );
	}


	/**
	 * Import widget JSON data
	 * Code from https://wordpress.org/plugins/widget-importer-exporter/ v 1.5.5
	 *
	 * @param $data
	 */
	public function import_widgets( $data ) {
		global $wp_registered_sidebars;
		global $wp_registered_widget_controls;

		// Get all available widgets site supports.
		$widget_controls = $wp_registered_widget_controls;

		$available_widgets = array();

		foreach ( $widget_controls as $widget ) {
			// No duplicates.
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
				$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
				$available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];
			}

		}

		// Get all existing widget instances.
		$widget_instances = array();
		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
		}

		// Begin results.
		$results = array();

		// Loop import data's sidebars.
		foreach ( $data as $sidebar_id => $widgets ) {

			// Skip inactive widgets (should not be in export file).
			if ( 'wp_inactive_widgets' === $sidebar_id ) {
				continue;
			}

			// Check if sidebar is available on this site.
			// Otherwise add widgets to inactive, and say so.
			if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
				$use_sidebar_id       = $sidebar_id;
				$sidebar_message_type = 'success';
				$sidebar_message      = '';
			} else {
				$use_sidebar_id       = 'wp_inactive_widgets'; // Add to inactive if sidebar does not exist in theme.
				$sidebar_message_type = 'error';
				$sidebar_message      = esc_html__( 'Widget area does not exist in theme (using Inactive)', 'widget-importer-exporter' );
			}

			// Result for sidebar
			// Sidebar name if theme supports it; otherwise ID.
			$results[ $sidebar_id ]['name']         = ! empty( $wp_registered_sidebars[ $sidebar_id ]['name'] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : $sidebar_id;
			$results[ $sidebar_id ]['message_type'] = $sidebar_message_type;
			$results[ $sidebar_id ]['message']      = $sidebar_message;
			$results[ $sidebar_id ]['widgets']      = array();

			// Loop widgets.
			foreach ( $widgets as $widget_instance_id => $widget ) {

				$fail = false;

				// Get id_base (remove -# from end) and instance ID number.
				$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );

				// Does site support this widget?
				if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
					$fail = true;
				}

				// Filter to modify settings object before conversion to array and import
				// Leave this filter here for backwards compatibility with manipulating objects (before conversion to array below)
				// Ideally the newer wie_widget_settings_array below will be used instead of this.
				$widget = apply_filters( 'wie_widget_settings', $widget );

				// Convert multidimensional objects to multidimensional arrays
				// Some plugins like Jetpack Widget Visibility store settings as multidimensional arrays
				// Without this, they are imported as objects and cause fatal error on Widgets page
				// If this creates problems for plugins that do actually intend settings in objects then may need to consider other approach: https://wordpress.org/support/topic/problem-with-array-of-arrays
				// It is probably much more likely that arrays are used than objects, however.
				$widget = json_decode( wp_json_encode( $widget ), true );

				// Filter to modify settings array
				// This is preferred over the older wie_widget_settings filter above
				// Do before identical check because changes may make it identical to end result (such as URL replacements).
				$widget = apply_filters( 'wie_widget_settings_array', $widget );

				// Does widget with identical settings already exist in same sidebar?
				if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {

					// Get existing widgets in this sidebar.
					$sidebars_widgets = get_option( 'sidebars_widgets' );
					$sidebar_widgets  = isset( $sidebars_widgets[ $use_sidebar_id ] ) ? $sidebars_widgets[ $use_sidebar_id ] : array(); // Check Inactive if that's where will go.

					// Loop widgets with ID base.
					$single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : array();
					foreach ( $single_widget_instances as $check_id => $check_widget ) {

						// Is widget in same sidebar and has identical settings?
						if ( in_array( "$id_base-$check_id", $sidebar_widgets, true ) && (array) $widget === $check_widget ) {

							$fail = true;
							break;
						}
					}
				}

				// No failure.
				if ( ! $fail ) {
					// Add widget instance
					$single_widget_instances   = get_option( 'widget_' . $id_base ); // All instances for that widget ID base, get fresh every time.
					$single_widget_instances   = ! empty( $single_widget_instances ) ? $single_widget_instances : array(
						'_multiwidget' => 1, // Start fresh if have to.
					);
					$single_widget_instances[] = $widget; // Add it.

					// Get the key it was given.
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load,
					// and the widget doesn't stick (reload wipes it).
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number                             = 1;
						$single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity.
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget.
					update_option( 'widget_' . $id_base, $single_widget_instances );

					// Assign widget instance to sidebar.
					// Which sidebars have which widgets, get fresh every time.
					$sidebars_widgets = get_option( 'sidebars_widgets' );

					// Avoid rarely fatal error when the option is an empty string
					// https://github.com/churchthemes/widget-importer-exporter/pull/11.
					if ( ! $sidebars_widgets ) {
						$sidebars_widgets = array();
					}

					// Use ID number from new widget instance.
					$new_instance_id = $id_base . '-' . $new_instance_id_number;

					// Add new instance to sidebar.
					$sidebars_widgets[ $use_sidebar_id ][] = $new_instance_id;

					// Save the amended data.
					update_option( 'sidebars_widgets', $sidebars_widgets );
				}
			}
		}
	}
}

new Pagex_Demo_Data_Import();