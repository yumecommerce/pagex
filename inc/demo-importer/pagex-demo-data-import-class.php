<?php

class Pagex_Demo_Data_Import {

	public function __construct() {
		// admin import demo notice
		add_action( 'admin_notices', array( $this, 'alert' ) );

		// ajax function for import
		add_action( 'wp_ajax_pagex_demo_content_import', array( $this, 'import' ) );
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

		echo '<div class="notice notice-info" id="pagex_demo_content_import_alert" style="margin: 30px 0;"><h2 style="margin-bottom: 10px; font-size:20px;">' . __( 'Demo Data Import', 'pagex' ) . '</h2><p style="color: #72777c; margin-bottom: 15px;">' . __( 'Great, after you have installed and activated all required plugins, it is time to import demo content.', 'pagex' ) . '<br><br><span style="color: #d20e00"><b>' . __( 'NOTE!', 'pagex' ) . '</b></span> <b>' . __( 'Import demo content only on a fresh WordPress site since some of your existing content might be replaced.', 'pagex' ) . '</b><br><br>' . __( 'This is a required step! Demo Data contains theme templates which are used by WordPress to display initial content of your website.', 'pagex' ) . '<br> ' . __( 'Please be patient and wait for the import process to complete. It can take up to 3-5 minutes.', 'pagex' ) . '</p><form id="pagex-demo-content-import-form"><input type="submit" class="button button-primary button-hero" id="pagex-demo-import-button" data-importing="' . __( 'Importing... Please wait.', 'pagex' ) . '" value="' . __( 'Import Demo Data', 'pagex' ) . '"></form><div style="font-size: 14px;" id="pagex-demo-content-import-form-response"></div><p></p></div>';

		echo '<script>

			var pagexImportBtn = jQuery("#pagex-demo-import-button")

			jQuery("#pagex-demo-content-import-form").submit(function() {

				pagexImportBtn.val(pagexImportBtn.attr("data-importing"));
					
				jQuery("body").addClass("pagex-no-click");
	
				jQuery.post( "' . admin_url( 'admin-ajax.php' ) . '", {action: "pagex_demo_content_import", step: 1}, function( response ) {
					console.log("Import: first step done.");
					pagexImportSecondStep();
				}).fail(function() {
					pagexImportBtn.remove();
					jQuery("body").removeClass("pagex-no-click");
					alert( "Error. Something went wrong with first step of import, please check your PHP error log." );
				});
				return false;
			});

			function pagexImportSecondStep() {
				jQuery.post( "' . admin_url( 'admin-ajax.php' ) . '", {action: "pagex_demo_content_import", step: 2}, function( response ) {
					console.log("Import: second step done.");
					pagexImportThirdStep();
				}).fail(function() {
					pagexImportBtn.remove();
					jQuery("body").removeClass("pagex-no-click");
					alert( "Error. Something went wrong with second step of import, please check your PHP error log." );
				});
			}

			function pagexImportThirdStep() {
				jQuery.post( "' . admin_url( 'admin-ajax.php' ) . '", {action: "pagex_demo_content_import", step: 3}, function( response ) {
					console.log("Import: third step done.");
					pagexImportBtn.remove();
					jQuery("body").removeClass("pagex-no-click");
					jQuery("#pagex-demo-content-import-form-response").html(response.data);
				}).fail(function() {
					alert( "Error. Something went wrong with third step of import, please check your PHP error log." );
					pagexImportBtn.remove();
					jQuery("body").removeClass("pagex-no-click");
				});
			}
		</script>';
	}

	/**
	 * Import demo via ajax
	 */
	public function import() {
		// try to update PHP memory limit
		ini_set( 'memory_limit', '350M' );
		set_time_limit( 300 );

		if ( ! is_super_admin() ) {
			wp_send_json_error( __( 'Only super admin can import the content.', 'pagex' ) );
		}

		$demo_data = apply_filters( 'pagex_import_demo_data', array() );

		// before import action
		do_action( 'pagex_before_import_demo_data' );

		// First Step
		// import migration settings for first step since it can contains custom post types which will be required for main importer
		if ( $_POST['step'] == '1' ) {
			// import additional third party plugin settings
			if ( isset( $demo_data['migration'] ) ) {
				if ( isset( $demo_data['migration']['pods'] ) ) {
					if ( function_exists( 'pods_api' ) ) {
						$pods_imported = $this->import_pods( $demo_data['migration']['pods'] );
					}
				}
			}

			// import widgets
			if ( isset( $demo_data['widgets'] ) ) {
				// disable all widgets
				add_filter( 'sidebars_widgets', function ( $sidebars_widgets ) {
					return array( false );
				} );

				$this->import_widgets( $demo_data['widgets'] );
			}

			wp_send_json_success();
		}

		// Second Step
		// Import posts/terms/attachments
		if ( $_POST['step'] == '2' ) {
			/*
			* Pagex Importer based on WordPress Importer
			* Version: 0.6.4
			* https://wordpress.org/plugins/wordpress-importer/
			*
			* IMPORTANT: custom changes, added wp_slash() for add_post_meta( $post_id, $key, wp_slash( $value ) );
			* It helps import JSON with escaped backslash (\") so it would not be deleted during add_post_meta()
			*
			*/
			require dirname( __FILE__ ) . '/importer.php';

			// path to demo xml file
			$path = $demo_data['path'];

			if ( ! $path || ! is_file( $path ) ) {
				wp_send_json_error( __( 'Failed to find the sample page file.', 'pagex' ) );
			}

			try {
				ob_start();

				$importer                    = new Pagex_Import();
				$importer->fetch_attachments = true;
				$importer->import( $path );

				ob_end_clean();
			} catch ( Exception $e ) {
				wp_send_json_error( __( 'Failed to import:', 'pagex' ) . ' ' . $e->getMessage() );
			}

			wp_send_json_success();
		}

		// Third Step
		// some actions when all demo is imported

		// update Pagex plugin settings
		if ( isset( $demo_data['settings'] ) ) {
			update_option( 'pagex_settings', json_decode( $demo_data['settings'], true ) );
		}

		// make all meta boxes available for nav menu
		// it makes all custom post types visible
		$user = wp_get_current_user();
		update_user_option( $user->ID, 'metaboxhidden_nav-menus', array(), true );

		// remap urls in post content and post meta
		global $wpdb;

		$from_url = $demo_data['demo_url'];
		$to_url   = site_url();

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url ) );
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s)", $from_url, $to_url ) );

		// remap urls in encoded shortcodes
		$to_url_encoded   = urlencode( str_replace( '/', '\/', $to_url ) );
		$from_url_encoded = urlencode( str_replace( '/', '\/', $from_url ) );
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url_encoded, $to_url_encoded ) );
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s)", $from_url_encoded, $to_url_encoded ) );


		// after import action
		do_action( 'pagex_after_import_demo_data' );

		// enable permalink
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules();

		// send success message
		wp_send_json_success( __( 'All Done! Demo data was successfully installed.', 'pagex' ) . ' ' . '<a href="' . $to_url . '">' . __( 'Visit Site.', 'pagex' ) . '</a>' );
	}


	/**
	 * Import widget JSON data
	 * Code from https://wordpress.org/plugins/widget-importer-exporter/ v1.5.5
	 *
	 * @param $data
	 */
	public function import_widgets( $data ) {
		global $wp_registered_sidebars, $wp_registered_widget_controls;

		$data = json_decode( $data );

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
				$sidebar_message      = esc_html__( 'Widget area does not exist in theme (using Inactive)', 'pagex' );
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

	/**
	 * Import Pods
	 * Use custom function to import insted of Pods Migration since we must keep same post IDs
	 *
	 * @param $data
	 *
	 * @return array|bool
	 */
	public function import_pods( $data ) {
		if ( ! is_array( $data ) ) {
			$json_data = @json_decode( $data, true );

			if ( ! is_array( $json_data ) ) {
				$json_data = @json_decode( pods_unslash( $data ), true );
			}

			$data = $json_data;
		}

		if ( ! is_array( $data ) || empty( $data ) ) {
			return false;
		}


		global $wpdb;


		if ( isset( $data['pods'] ) && is_array( $data['pods'] ) ) {
			foreach ( $data['pods'] as $pod ) {
				if ( $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d", $pod['id'] ) ) ) {
					$wpdb->delete( "$wpdb->posts", array( 'ID' => $pod['id'] ) );
					$wpdb->delete( "$wpdb->postmeta", array( 'post_id' => $pod['id'] ) );
				}

				$postdata = array(
					'ID'             => $pod['id'],
					'post_title'     => $pod['label'],
					'post_name'      => $pod['name'],
					'post_content'   => $pod['description'],
					'post_parent'    => 0,
					'menu_order'     => 0,
					'post_status'    => 'publish',
					'post_type'      => '_pods_pod',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
				);

				$wpdb->insert( "$wpdb->posts", $postdata );

				if ( isset( $pod['fields'] ) && ! empty( $pod['fields'] ) ) {
					foreach ( $pod['fields'] as $field ) {

						if ( $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d", $field['id'] ) ) ) {
							$wpdb->delete( "$wpdb->posts", array( 'ID' => $field['id'] ) );
							$wpdb->delete( "$wpdb->postmeta", array( 'post_id' => $field['id'] ) );
						}

						$postdata = array(
							'ID'             => $field['id'],
							'post_title'     => $field['label'],
							'post_name'      => $field['name'],
							'post_content'   => $field['description'],
							'post_parent'    => $pod['id'],
							'menu_order'     => $field['weight'],
							'post_status'    => 'publish',
							'post_type'      => '_pods_field',
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
						);

						$wpdb->insert( "$wpdb->posts", $postdata );

						$field_meta = $field;

						unset( $field_meta['id'], $field_meta['label'], $field_meta['name'], $field_meta['description'], $field_meta['weight'] );

						foreach ( $field_meta as $meta_key => $meta_value ) {
							if ( $meta_value ) {
								add_post_meta( $field['id'], $meta_key, wp_slash( $meta_value ) );
							}
						}
					}
				}

				$pod_meta = $pod;

				unset( $pod_meta['id'], $pod_meta['label'], $pod_meta['name'], $pod_meta['description'], $pod_meta['fields'] );

				foreach ( $pod_meta as $meta_key => $meta_value ) {
					if ( $meta_value ) {
						add_post_meta( $pod['id'], $meta_key, wp_slash( $meta_value ) );
					}
				}
			}
		}

		$api = pods_api();

		$api->cache_flush_pods();

		if ( defined( 'PODS_PRELOAD_CONFIG_AFTER_FLUSH' ) && PODS_PRELOAD_CONFIG_AFTER_FLUSH ) {
			$api->load_pods( array( 'bypass_cache' => true ) );
		}

		return;
	}
}

new Pagex_Demo_Data_Import();