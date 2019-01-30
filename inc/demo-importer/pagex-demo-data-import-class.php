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
			if ( $post['post_type'] != 'attachment' ) {
				global $wpdb;

				// delete existing meta (assumes import contains all meta needed for post)
				$wpdb->delete( 'wp_postmeta', array( 'post_id' => $post_id ) );
				// force the post to be imported
				$post_id = 0;
			}

			return $post_id;
		}
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
			// update the existing post
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
				if ( function_exists( 'pods_api' ) ) {
					// import pods with second parameter as true to replace old settings
					$pods_imported = $this->import_pods( $demo_data['migration']['pods'], true );
				}
			}
		}

		// import widgets
		if ( isset( $demo_data['widgets'] ) ) {
			$this->import_widgets( $demo_data['widgets'] );
		}

		// update Pagex plugin settings
		if ( isset( $demo_data['settings'] ) ) {
			update_option( 'pagex_settings', json_decode( $demo_data['settings'], true ) );
		}

		// after import action
		do_action( 'pagex_after_import_demo_data' );

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
	 * Code from Import a Package https://ru.wordpress.org/plugins/pods/ v 2.7.12
	 *
	 * @param $data
	 * @param bool $replace
	 *
	 * @return array|bool
	 */
	public function import_pods( $data, $replace = false ) {
		if ( ! defined( 'PODS_FIELD_STRICT' ) ) {
			define( 'PODS_FIELD_STRICT', false );
		}

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

		$api = pods_api();

		if ( ! isset( $data['meta'] ) || ! isset( $data['meta']['version'] ) || empty( $data['meta']['version'] ) ) {
			return false;
		}

		if ( false === strpos( $data['meta']['version'], '.' ) && (int) $data['meta']['version'] < 1000 ) {
			// Pods 1.x < 1.10
			$data['meta']['version'] = implode( '.', str_split( $data['meta']['version'] ) );
		} elseif ( false === strpos( $data['meta']['version'], '.' ) ) {
			// Pods 1.10 <= 2.0
			$data['meta']['version'] = pods_version_to_point( $data['meta']['version'] );
		}

		$found = array();

		if ( isset( $data['pods'] ) && is_array( $data['pods'] ) ) {
			foreach ( $data['pods'] as $pod_data ) {
				if ( isset( $pod_data['id'] ) ) {
					unset( $pod_data['id'] );
				}

				$pod = $api->load_pod( array( 'name' => $pod_data['name'] ), false );

				$existing_fields = array();

				if ( ! empty( $pod ) ) {
					// Delete Pod if it exists
					if ( $replace ) {
						$api->delete_pod( array( 'id' => $pod['id'] ) );

						$pod = array( 'fields' => array() );
					} else {
						$existing_fields = $pod['fields'];
					}
				} else {
					$pod = array( 'fields' => array() );
				}

				// Backwards compatibility
				if ( version_compare( $data['meta']['version'], '2.0', '<' ) ) {
					$core_fields = array(
						array(
							'name'    => 'created',
							'label'   => 'Date Created',
							'type'    => 'datetime',
							'options' => array(
								'datetime_format'      => 'ymd_slash',
								'datetime_time_type'   => '12',
								'datetime_time_format' => 'h_mm_ss_A',
							),
							'weight'  => 1,
						),
						array(
							'name'    => 'modified',
							'label'   => 'Date Modified',
							'type'    => 'datetime',
							'options' => array(
								'datetime_format'      => 'ymd_slash',
								'datetime_time_type'   => '12',
								'datetime_time_format' => 'h_mm_ss_A',
							),
							'weight'  => 2,
						),
						array(
							'name'        => 'author',
							'label'       => 'Author',
							'type'        => 'pick',
							'pick_object' => 'user',
							'options'     => array(
								'pick_format_type'   => 'single',
								'pick_format_single' => 'autocomplete',
								'default_value'      => '{@user.ID}',
							),
							'weight'      => 3,
						),
					);

					$found_fields = array();

					if ( ! empty( $pod_data['fields'] ) ) {
						foreach ( $pod_data['fields'] as $k => $field ) {
							$field_type = $field['coltype'];

							if ( 'txt' === $field_type ) {
								$field_type = 'text';
							} elseif ( 'desc' === $field_type ) {
								$field_type = 'wysiwyg';
							} elseif ( 'code' === $field_type ) {
								$field_type = 'paragraph';
							} elseif ( 'bool' === $field_type ) {
								$field_type = 'boolean';
							} elseif ( 'num' === $field_type ) {
								$field_type = 'number';
							} elseif ( 'date' === $field_type ) {
								$field_type = 'datetime';
							}

							$multiple = min( max( (int) $field['multiple'], 0 ), 1 );

							$new_field = array(
								'name'        => trim( $field['name'] ),
								'label'       => trim( $field['label'] ),
								'description' => trim( $field['comment'] ),
								'type'        => $field_type,
								'weight'      => (int) $field['weight'],
								'options'     => array(
									'required'     => min( max( (int) $field['required'], 0 ), 1 ),
									'unique'       => min( max( (int) $field['unique'], 0 ), 1 ),
									'input_helper' => $field['input_helper'],
								),
							);

							if ( in_array( $new_field['name'], $found_fields, true ) ) {
								unset( $pod_data['fields'][ $k ] );

								continue;
							}

							$found_fields[] = $new_field['name'];

							if ( 'pick' === $field_type ) {
								$new_field['pick_object'] = 'pod';
								$new_field['pick_val']    = $field['pickval'];

								if ( 'wp_user' === $field['pickval'] ) {
									$new_field['pick_object'] = 'user';
								} elseif ( 'wp_post' === $field['pickval'] ) {
									$new_field['pick_object'] = 'post_type-post';
								} elseif ( 'wp_page' === $field['pickval'] ) {
									$new_field['pick_object'] = 'post_type-page';
								} elseif ( 'wp_taxonomy' === $field['pickval'] ) {
									$new_field['pick_object'] = 'taxonomy-category';
								}

								// This won't work if the field doesn't exist
								// $new_field[ 'sister_id' ] = $field[ 'sister_field_id' ];
								$new_field['options']['pick_filter']  = $field['pick_filter'];
								$new_field['options']['pick_orderby'] = $field['pick_orderby'];
								$new_field['options']['pick_display'] = '';
								$new_field['options']['pick_size']    = 'medium';

								if ( 1 == $multiple ) {
									$new_field['options']['pick_format_type']  = 'multi';
									$new_field['options']['pick_format_multi'] = 'checkbox';
									$new_field['options']['pick_limit']        = 0;
								} else {
									$new_field['options']['pick_format_type']   = 'single';
									$new_field['options']['pick_format_single'] = 'dropdown';
									$new_field['options']['pick_limit']         = 1;
								}
							} elseif ( 'file' === $field_type ) {
								$new_field['options']['file_format_type'] = 'multi';
								$new_field['options']['file_type']        = 'any';
							} elseif ( 'number' === $field_type ) {
								$new_field['options']['number_decimals'] = 2;
							} elseif ( 'desc' === $field['coltype'] ) {
								$new_field['options']['wysiwyg_editor'] = 'tinymce';
							} elseif ( 'text' === $field_type ) {
								$new_field['options']['text_max_length'] = 128;
							}//end if

							if ( isset( $pod['fields'][ $new_field['name'] ] ) ) {
								$new_field = array_merge( $pod['fields'][ $new_field['name'] ], $new_field );
							}

							$pod_data['fields'][ $k ] = $new_field;
						}//end foreach
					}//end if

					if ( pods_var( 'id', $pod, 0 ) < 1 ) {
						$pod_data['fields'] = array_merge( $core_fields, $pod_data['fields'] );
					}

					if ( empty( $pod_data['label'] ) ) {
						$pod_data['label'] = ucwords( str_replace( '_', ' ', $pod_data['name'] ) );
					}

					if ( isset( $pod_data['is_toplevel'] ) ) {
						$pod_data['show_in_menu'] = ( 1 == $pod_data['is_toplevel'] ? 1 : 0 );

						unset( $pod_data['is_toplevel'] );
					}

					if ( isset( $pod_data['detail_page'] ) ) {
						$pod_data['detail_url'] = $pod_data['detail_page'];

						unset( $pod_data['detail_page'] );
					}

					if ( isset( $pod_data['before_helpers'] ) ) {
						$pod_data['pre_save_helpers'] = $pod_data['before_helpers'];

						unset( $pod_data['before_helpers'] );
					}

					if ( isset( $pod_data['after_helpers'] ) ) {
						$pod_data['post_save_helpers'] = $pod_data['after_helpers'];

						unset( $pod_data['after_helpers'] );
					}

					if ( isset( $pod_data['pre_drop_helpers'] ) ) {
						$pod_data['pre_delete_helpers'] = $pod_data['pre_drop_helpers'];

						unset( $pod_data['pre_drop_helpers'] );
					}

					if ( isset( $pod_data['post_drop_helpers'] ) ) {
						$pod_data['post_delete_helpers'] = $pod_data['post_drop_helpers'];

						unset( $pod_data['post_drop_helpers'] );
					}

					$pod_data['name'] = pods_clean_name( $pod_data['name'] );

					$pod_data = array(
						'name'    => $pod_data['name'],
						'label'   => $pod_data['label'],
						'type'    => 'pod',
						'storage' => 'table',
						'fields'  => $pod_data['fields'],
						'options' => array(
							'pre_save_helpers'    => pods_var_raw( 'pre_save_helpers', $pod_data ),
							'post_save_helpers'   => pods_var_raw( 'post_save_helpers', $pod_data ),
							'pre_delete_helpers'  => pods_var_raw( 'pre_delete_helpers', $pod_data ),
							'post_delete_helpers' => pods_var_raw( 'post_delete_helpers', $pod_data ),
							'show_in_menu'        => ( 1 == pods_var_raw( 'show_in_menu', $pod_data, 0 ) ? 1 : 0 ),
							'detail_url'          => pods_var_raw( 'detail_url', $pod_data ),
							'pod_index'           => 'name',
						),
					);
				}//end if

				$pod = array_merge( $pod, $pod_data );

				foreach ( $pod['fields'] as $k => $field ) {
					if ( isset( $field['id'] ) && ! isset( $existing_fields[ $field['name'] ] ) ) {
						unset( $pod['fields'][ $k ]['id'] );
					}

					if ( isset( $field['pod_id'] ) ) {
						unset( $pod['fields'][ $k ]['pod_id'] );
					}

					if ( isset( $existing_fields[ $field['name'] ] ) ) {
						$existing_field = pods_api()->load_field(
							array(
								'name' => $field['name'],
								'pod'  => $pod['name'],
							)
						);
						if ( $existing_field ) {
							$pod['fields'][ $k ]['id'] = $existing_field['id'];
						}
					}

					if ( isset( $field['pod'] ) ) {
						unset( $pod['fields'][ $k ]['pod'] );
					}
				}//end foreach

				$api->save_pod( $pod );

				if ( ! isset( $found['pods'] ) ) {
					$found['pods'] = array();
				}

				$found['pods'][ $pod['name'] ] = $pod['label'];
			}//end foreach
		}//end if

		if ( isset( $data['templates'] ) && is_array( $data['templates'] ) ) {
			foreach ( $data['templates'] as $template_data ) {
				if ( isset( $template_data['id'] ) ) {
					unset( $template_data['id'] );
				}

				$template = $api->load_template( array( 'name' => $template_data['name'] ) );

				if ( ! empty( $template ) ) {
					// Delete Template if it exists
					if ( $replace ) {
						$api->delete_template( array( 'id' => $template['id'] ) );

						$template = array();
					}
				} else {
					$template = array();
				}

				$template = array_merge( $template, $template_data );

				$api->save_template( $template );

				if ( ! isset( $found['templates'] ) ) {
					$found['templates'] = array();
				}

				$found['templates'][ $template['name'] ] = $template['name'];
			}//end foreach
		}//end if

		// Backwards compatibility
		if ( isset( $data['pod_pages'] ) ) {
			$data['pages'] = $data['pod_pages'];

			unset( $data['pod_pages'] );
		}

		if ( isset( $data['pages'] ) && is_array( $data['pages'] ) ) {
			foreach ( $data['pages'] as $page_data ) {
				if ( isset( $page_data['id'] ) ) {
					unset( $page_data['id'] );
				}

				$page = $api->load_page( array( 'name' => pods_var_raw( 'name', $page_data, pods_var_raw( 'uri', $page_data ), null, true ) ) );

				if ( ! empty( $page ) ) {
					// Delete Page if it exists
					if ( $replace ) {
						$api->delete_page( array( 'id' => $page['id'] ) );

						$page = array();
					}
				} else {
					$page = array();
				}

				// Backwards compatibility
				if ( isset( $page_data['uri'] ) ) {
					$page_data['name'] = $page_data['uri'];

					unset( $page_data['uri'] );
				}

				if ( isset( $page_data['phpcode'] ) ) {
					$page_data['code'] = $page_data['phpcode'];

					unset( $page_data['phpcode'] );
				}

				$page = array_merge( $page, $page_data );

				$page['name'] = trim( $page['name'], '/' );

				$api->save_page( $page );

				if ( ! isset( $found['pages'] ) ) {
					$found['pages'] = array();
				}

				$found['pages'][ $page['name'] ] = $page['name'];
			}//end foreach
		}//end if

		if ( isset( $data['helpers'] ) && is_array( $data['helpers'] ) ) {
			foreach ( $data['helpers'] as $helper_data ) {
				if ( isset( $helper_data['id'] ) ) {
					unset( $helper_data['id'] );
				}

				$helper = $api->load_helper( array( 'name' => $helper_data['name'] ) );

				if ( ! empty( $helper ) ) {
					// Delete Helper if it exists
					if ( $replace ) {
						$api->delete_helper( array( 'id' => $helper['id'] ) );

						$helper = array();
					}
				} else {
					$helper = array();
				}

				// Backwards compatibility
				if ( isset( $helper_data['phpcode'] ) ) {
					$helper_data['code'] = $helper_data['phpcode'];

					unset( $helper_data['phpcode'] );
				}

				if ( isset( $helper_data['type'] ) ) {
					if ( 'before' === $helper_data['type'] ) {
						$helper_data['type'] = 'pre_save';
					} elseif ( 'after' === $helper_data['type'] ) {
						$helper_data['type'] = 'post_save';
					}
				}

				$helper = array_merge( $helper, $helper_data );

				if ( isset( $helper['type'] ) ) {
					$helper['helper_type'] = $helper['type'];

					unset( $helper['helper_type'] );
				}

				$api->save_helper( $helper );

				if ( ! isset( $found['helpers'] ) ) {
					$found['helpers'] = array();
				}

				$found['helpers'][ $helper['name'] ] = $helper['name'];
			}//end foreach
		}//end if

		$found = apply_filters( 'pods_packages_import', $found, $data, $replace );

		if ( ! empty( $found ) ) {
			return $found;
		}

		return false;
	}
}

new Pagex_Demo_Data_Import();