<?php

/** Display verbose errors */
define( 'IMPORT_DEBUG', false );


// include WXR file parsers
if ( ! class_exists( 'WXR_Parser' ) ) {
	require_once dirname( __FILE__ ) . '/parsers.php';
}

/**
 * WordPress Importer class for managing the import process of a WXR file
 *
 * @subpackage Importer
 */
class Pagex_Import {
	var $max_wxr_version = 1.2; // max. supported WXR version

	var $id; // WXR attachment ID

	// information to import from WXR file
	var $version;
	var $authors = array();
	var $posts = array();
	var $terms = array();
	var $categories = array();
	var $tags = array();
	var $base_url = '';

	// mappings from old information to new
	var $processed_authors = array();
	var $author_mapping = array();
	var $processed_terms = array();
	var $processed_posts = array();
	var $post_orphans = array();
	var $processed_menu_items = array();
	var $menu_item_orphans = array();
	var $missing_menu_items = array();

	var $fetch_attachments = false;
	var $url_remap = array();
	var $featured_images = array();


	/**
	 * The main controller for the actual import stage.
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	function import( $file ) {

		$this->delete_initial_data();

		add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
		add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

		$this->import_start( $file );

		$this->get_author_mapping();

		wp_suspend_cache_invalidation( true );
		$this->process_categories();
		$this->process_tags();
		$this->process_terms();
		$this->process_posts();
		wp_suspend_cache_invalidation( false );

		$this->import_end();
	}

	/**
	 * To make sure we have fresh WP installation, remove some post and tax so they will not be conflicted with imported one
	 */
	public function delete_initial_data() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->terms" );
		$wpdb->query( "DELETE FROM $wpdb->term_relationships" );
		$wpdb->query( "DELETE FROM $wpdb->term_taxonomy" );
		$wpdb->query( "DELETE FROM $wpdb->termmeta" );

		// remove default hello world post if exist so it would not be presented after demo import
		if ( $hello_world = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s", 'hello-world' ) ) ) {
			$wpdb->delete( "$wpdb->posts", array( 'ID' => $hello_world ) );
			$wpdb->delete( "$wpdb->postmeta", array( 'post_id' => $hello_world ) );
		}

		// remove shop pages if WooCommerce already setup
		$shop_id = get_option( 'woocommerce_shop_page_id' );

		if ( $shop_id ) {
			$woo_pages = array(
				'woocommerce_shop_page_id',
				'woocommerce_cart_page_id',
				'woocommerce_checkout_page_id',
				'woocommerce_myaccount_page_id',
				'woocommerce_terms_page_id',
			);

			foreach ( $woo_pages as $woo_page ) {
				if ($woo_page_id = get_option( $woo_page )) {
					$wpdb->delete( "$wpdb->posts", array( 'ID' => $woo_page_id ) );
					$wpdb->delete( "$wpdb->postmeta", array( 'post_id' => $woo_page_id ) );
				}
			}
		}
	}

	/**
	 * Parses the WXR file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	function import_start( $file ) {
		$import_data = $this->parse( $file );

		if ( is_wp_error( $import_data ) ) {
			die();
		}

		$this->version = $import_data['version'];
		$this->get_authors_from_import( $import_data );
		$this->posts      = $import_data['posts'];
		$this->terms      = $import_data['terms'];
		$this->categories = $import_data['categories'];
		$this->tags       = $import_data['tags'];
		$this->base_url   = esc_url( $import_data['base_url'] );

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	function import_end() {
		wp_import_cleanup( $this->id );

		wp_cache_flush();
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );
	}

	/**
	 * Handles the WXR upload and initial parsing of the file to prepare for
	 * displaying author import options
	 *
	 * @return bool False if error uploading or invalid file, true otherwise
	 */
	function handle_upload() {
		$file = wp_import_handle_upload();

		if ( isset( $file['error'] ) ) {
			error_log( 'Pagex Importer: ' . esc_html( $file['error'] ) );

			return false;
		} else if ( ! file_exists( $file['file'] ) ) {
			error_log( 'Pagex Importer: The export file could not be found at - ' . esc_html( $file['error'] ) );

			return false;
		}

		$this->id    = (int) $file['id'];
		$import_data = $this->parse( $file['file'] );
		if ( is_wp_error( $import_data ) ) {
			error_log( 'Pagex Importer: there has been an error - ' . esc_html( $import_data->get_error_message() ) );

			return false;
		}

		$this->version = $import_data['version'];
		if ( $this->version > $this->max_wxr_version ) {
			error_log( 'Pagex Importer: WXR file may not be supported by this version of the importer' );
		}

		$this->get_authors_from_import( $import_data );

		return true;
	}

	/**
	 * Retrieve authors from parsed WXR data
	 *
	 * Uses the provided author information from WXR 1.1 files
	 * or extracts info from each post for WXR 1.0 files
	 *
	 * @param array $import_data Data returned by a WXR parser
	 */
	function get_authors_from_import( $import_data ) {
		if ( ! empty( $import_data['authors'] ) ) {
			$this->authors = $import_data['authors'];
			// no author information, grab it from the posts
		} else {
			foreach ( $import_data['posts'] as $post ) {
				$login = sanitize_user( $post['post_author'], true );
				if ( empty( $login ) ) {
					printf( esc_html__( 'Failed to import author %s. Their posts will be attributed to the current user.', 'pagex' ), esc_html( $post['post_author'] ) );
					echo '<br />';
					continue;
				}

				if ( ! isset( $this->authors[ $login ] ) ) {
					$this->authors[ $login ] = array(
						'author_login'        => $login,
						'author_display_name' => $post['post_author']
					);
				}
			}
		}
	}

	/**
	 * Map old author logins to local user IDs based on decisions made
	 * in import options form. Can map to an existing user, create a new user
	 * or falls back to the current user in case of error with either of the previous
	 */
	function get_author_mapping() {
		if ( ! isset( $_POST['imported_authors'] ) ) {
			return;
		}

		$create_users = $this->allow_create_users();

		foreach ( (array) $_POST['imported_authors'] as $i => $old_login ) {
			// Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
			$santized_old_login = sanitize_user( $old_login, true );
			$old_id             = isset( $this->authors[ $old_login ]['author_id'] ) ? intval( $this->authors[ $old_login ]['author_id'] ) : false;

			if ( ! empty( $_POST['user_map'][ $i ] ) ) {
				$user = get_userdata( intval( $_POST['user_map'][ $i ] ) );
				if ( isset( $user->ID ) ) {
					if ( $old_id ) {
						$this->processed_authors[ $old_id ] = $user->ID;
					}
					$this->author_mapping[ $santized_old_login ] = $user->ID;
				}
			} else if ( $create_users ) {
				if ( ! empty( $_POST['user_new'][ $i ] ) ) {
					$user_id = wp_create_user( $_POST['user_new'][ $i ], wp_generate_password() );
				} else if ( $this->version != '1.0' ) {
					$user_data = array(
						'user_login'   => $old_login,
						'user_pass'    => wp_generate_password(),
						'user_email'   => isset( $this->authors[ $old_login ]['author_email'] ) ? $this->authors[ $old_login ]['author_email'] : '',
						'display_name' => $this->authors[ $old_login ]['author_display_name'],
						'first_name'   => isset( $this->authors[ $old_login ]['author_first_name'] ) ? $this->authors[ $old_login ]['author_first_name'] : '',
						'last_name'    => isset( $this->authors[ $old_login ]['author_last_name'] ) ? $this->authors[ $old_login ]['author_last_name'] : '',
					);
					$user_id   = wp_insert_user( $user_data );
				}

				if ( ! is_wp_error( $user_id ) ) {
					if ( $old_id ) {
						$this->processed_authors[ $old_id ] = $user_id;
					}
					$this->author_mapping[ $santized_old_login ] = $user_id;
				} else {
					printf( esc_html__( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'pagex' ), esc_html( $this->authors[ $old_login ]['author_display_name'] ) );
					if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
						echo ' ' . $user_id->get_error_message();
					}
					echo '<br />';
				}
			}

			// failsafe: if the user_id was invalid, default to the current user
			if ( ! isset( $this->author_mapping[ $santized_old_login ] ) ) {
				if ( $old_id ) {
					$this->processed_authors[ $old_id ] = (int) get_current_user_id();
				}
				$this->author_mapping[ $santized_old_login ] = (int) get_current_user_id();
			}
		}
	}

	/**
	 * Create new categories based on import information
	 *
	 * Doesn't create a new category if its slug already exists
	 */
	function process_categories() {
		if ( empty( $this->categories ) ) {
			return;
		}

		global $wpdb;

		foreach ( $this->categories as $cat ) {

			$wpdb->replace( "$wpdb->terms", array(
				'term_id'    => $cat['term_id'],
				'name'       => $cat['cat_name'],
				'slug'       => $cat['category_nicename'],
				'term_group' => 0
			) );

			$wpdb->replace( "$wpdb->term_taxonomy", array(
				'term_taxonomy_id' => $cat['term_id'],
				'term_id'          => $cat['term_id'],
				'taxonomy'         => 'category',
				'description'      => isset( $cat['category_description'] ) ? $cat['category_description'] : '',
				'parent'           => empty( $cat['category_parent'] ) ? 0 : $cat['category_parent'],
				'count'            => 0
			) );

			$this->processed_terms[ intval( $cat['term_id'] ) ] = $cat['term_id'];

			$this->process_termmeta( $cat, $cat['term_id'] );
		}

		unset( $this->categories );
	}

	/**
	 * Create new post tags based on import information
	 *
	 * Doesn't create a tag if its slug already exists
	 */
	function process_tags() {
		if ( empty( $this->tags ) ) {
			return;
		}

		global $wpdb;

		foreach ( $this->tags as $tag ) {

			$wpdb->replace( "$wpdb->terms", array(
				'term_id'    => $tag['term_id'],
				'name'       => $tag['tag_name'],
				'slug'       => $tag['tag_slug'],
				'term_group' => 0
			) );

			$wpdb->replace( "$wpdb->term_taxonomy", array(
				'term_taxonomy_id' => $tag['term_id'],
				'term_id'          => $tag['term_id'],
				'taxonomy'         => 'post_tag',
				'description'      => isset( $tag['tag_description'] ) ? $tag['tag_description'] : '',
				'parent'           => 0,
				'count'            => 0
			) );

			$this->processed_terms[ intval( $tag['term_id'] ) ] = $tag['term_id'];

			$this->process_termmeta( $tag, $tag['term_id'] );
		}

		unset( $this->tags );
	}

	/**
	 * Create new terms based on import information
	 *
	 * Doesn't create a term its slug already exists
	 */
	function process_terms() {
		if ( empty( $this->terms ) ) {
			return;
		}

		global $wpdb;

		foreach ( $this->terms as $term ) {

			$wpdb->replace( "$wpdb->terms", array(
				'term_id'    => $term['term_id'],
				'name'       => $term['term_name'],
				'slug'       => $term['slug'],
				'term_group' => 0
			) );

			$wpdb->replace( "$wpdb->term_taxonomy", array(
				'term_taxonomy_id' => $term['term_id'],
				'term_id'          => $term['term_id'],
				'taxonomy'         => $term['term_taxonomy'],
				'description'      => isset( $term['term_description'] ) ? $term['term_description'] : '',
				'parent'           => empty( $term['term_parent'] ) ? 0 : $term['term_parent'],
				'count'            => 0
			) );

			$this->processed_terms[ intval( $term['term_id'] ) ] = $term['term_id'];

			$this->process_termmeta( $term, $term['term_id'] );
		}

		unset( $this->terms );
	}

	/**
	 * Add metadata to imported term.
	 *
	 * @since 0.6.2
	 *
	 * @param array $term Term data from WXR import.
	 * @param int $term_id ID of the newly created term.
	 */
	protected function process_termmeta( $term, $term_id ) {
		if ( ! isset( $term['termmeta'] ) ) {
			$term['termmeta'] = array();
		}

		/**
		 * Filters the metadata attached to an imported term.
		 *
		 * @since 0.6.2
		 *
		 * @param array $termmeta Array of term meta.
		 * @param int $term_id ID of the newly created term.
		 * @param array $term Term data from the WXR import.
		 */
		$term['termmeta'] = apply_filters( 'wp_import_term_meta', $term['termmeta'], $term_id, $term );

		if ( empty( $term['termmeta'] ) ) {
			return;
		}

		foreach ( $term['termmeta'] as $meta ) {
			/**
			 * Filters the meta key for an imported piece of term meta.
			 *
			 * @since 0.6.2
			 *
			 * @param string $meta_key Meta key.
			 * @param int $term_id ID of the newly created term.
			 * @param array $term Term data from the WXR import.
			 */
			$key = apply_filters( 'import_term_meta_key', $meta['key'], $term_id, $term );
			if ( ! $key ) {
				continue;
			}

			// Export gets meta straight from the DB so could have a serialized string
			$value = maybe_unserialize( $meta['value'] );

			add_term_meta( $term_id, $key, $value );

			/**
			 * Fires after term meta is imported.
			 *
			 * @since 0.6.2
			 *
			 * @param int $term_id ID of the newly created term.
			 * @param string $key Meta key.
			 * @param mixed $value Meta value.
			 */
			do_action( 'import_term_meta', $term_id, $key, $value );
		}
	}

	/**
	 * Create new posts based on import information
	 *
	 * Posts marked as having a parent which doesn't exist will become top level items.
	 * Doesn't create a new post if: the post type doesn't exist, the given post ID
	 * is already noted as imported or a post with the same title and date already exists.
	 * Note that new/updated terms, comments and meta are imported for the last of the above.
	 */
	function process_posts() {
		$this->posts = apply_filters( 'wp_import_posts', $this->posts );

		global $wpdb;

		foreach ( $this->posts as $post ) {
			if ( isset( $this->processed_posts[ $post['post_id'] ] ) && ! empty( $post['post_id'] ) ) {
				continue;
			}

			if ( $post['status'] == 'auto-draft' ) {
				continue;
			}

			// delete post if it is already added
			if ( $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d", $post['post_id'] ) ) ) {
				$wpdb->delete( "$wpdb->posts", array( 'ID' => $post['post_id'] ) );
				$wpdb->delete( "$wpdb->postmeta", array( 'post_id' => $post['post_id'] ) );
			}

			$post_type_object = get_post_type_object( $post['post_type'] );

			$post_exists = false;

			$post_parent = (int) $post['post_parent'];
			if ( $post_parent ) {
				// if we already know the parent, map it to the new local ID
				if ( isset( $this->processed_posts[ $post_parent ] ) ) {
					$post_parent = $this->processed_posts[ $post_parent ];
					// otherwise record the parent for later
				} else {
					$this->post_orphans[ intval( $post['post_id'] ) ] = $post_parent;
					$post_parent                                      = 0;
				}
			}

			// map the post author
			$author = sanitize_user( $post['post_author'], true );
			if ( isset( $this->author_mapping[ $author ] ) ) {
				$author = $this->author_mapping[ $author ];
			} else {
				$author = (int) get_current_user_id();
			}

			$postdata = array(
				'import_id'      => $post['post_id'],
				'post_author'    => $author,
				'post_date'      => $post['post_date'],
				'post_date_gmt'  => $post['post_date_gmt'],
				'post_content'   => $post['post_content'],
				'post_excerpt'   => $post['post_excerpt'],
				'post_title'     => $post['post_title'],
				'post_status'    => $post['status'],
				'post_name'      => $post['post_name'],
				'comment_status' => $post['comment_status'],
				'ping_status'    => $post['ping_status'],
				'guid'           => $post['guid'],
				'post_parent'    => $post_parent,
				'menu_order'     => $post['menu_order'],
				'post_type'      => $post['post_type'],
				'post_password'  => $post['post_password']
			);

			$original_post_ID = $post['post_id'];
			$postdata         = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

			$postdata = wp_slash( $postdata );

			if ( 'attachment' == $postdata['post_type'] ) {
				$remote_url = ! empty( $post['attachment_url'] ) ? $post['attachment_url'] : $post['guid'];

				// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
				// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
				$postdata['upload_date'] = $post['post_date'];
				if ( isset( $post['postmeta'] ) ) {
					foreach ( $post['postmeta'] as $meta ) {
						if ( $meta['key'] == '_wp_attached_file' ) {
							if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) ) {
								$postdata['upload_date'] = $matches[0];
							}
							break;
						}
					}
				}

				$comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url );
			} else {
				$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
				do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
			}

			if ( is_wp_error( $post_id ) ) {
				printf( esc_html__( 'Failed to import %s &#8220;%s&#8221;', 'pagex' ),
					$post_type_object->labels->singular_name, esc_html( $post['post_title'] ) );
				if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
					echo ': ' . $post_id->get_error_message();
				}
				echo '<br />';
				continue;
			}

			if ( $post['is_sticky'] == 1 ) {
				stick_post( $post_id );
			}

			// map pre-import ID to local ID
			$this->processed_posts[ intval( $post['post_id'] ) ] = (int) $post_id;

			if ( ! isset( $post['terms'] ) ) {
				$post['terms'] = array();
			}

			// add categories, tags and other terms
			if ( ! empty( $post['terms'] ) ) {
				$terms_to_set = array();
				foreach ( $post['terms'] as $term ) {
					// back compat with WXR 1.0 map 'tag' to 'post_tag'
					$taxonomy    = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
					$term_exists = term_exists( $term['slug'], $taxonomy );
					$term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
					if ( ! $term_id ) {
						$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
						if ( ! is_wp_error( $t ) ) {
							$term_id = $t['term_id'];
							do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
						} else {
							printf( esc_html__( 'Failed to import %s %s', 'pagex' ), esc_html( $taxonomy ), esc_html( $term['name'] ) );
							if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
								echo ': ' . $t->get_error_message();
							}
							echo '<br />';
							do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
							continue;
						}
					}
					$terms_to_set[ $taxonomy ][] = intval( $term_id );
				}

				foreach ( $terms_to_set as $tax => $ids ) {
					$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
					do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
				}
				unset( $post['terms'], $terms_to_set );
			}

			if ( ! isset( $post['comments'] ) ) {
				$post['comments'] = array();
			}

			// add/update comments
			if ( ! empty( $post['comments'] ) ) {
				$num_comments      = 0;
				$inserted_comments = array();
				foreach ( $post['comments'] as $comment ) {
					$comment_id                                         = $comment['comment_id'];
					$newcomments[ $comment_id ]['comment_post_ID']      = $comment_post_ID;
					$newcomments[ $comment_id ]['comment_author']       = $comment['comment_author'];
					$newcomments[ $comment_id ]['comment_author_email'] = $comment['comment_author_email'];
					$newcomments[ $comment_id ]['comment_author_IP']    = $comment['comment_author_IP'];
					$newcomments[ $comment_id ]['comment_author_url']   = $comment['comment_author_url'];
					$newcomments[ $comment_id ]['comment_date']         = $comment['comment_date'];
					$newcomments[ $comment_id ]['comment_date_gmt']     = $comment['comment_date_gmt'];
					$newcomments[ $comment_id ]['comment_content']      = $comment['comment_content'];
					$newcomments[ $comment_id ]['comment_approved']     = $comment['comment_approved'];
					$newcomments[ $comment_id ]['comment_type']         = $comment['comment_type'];
					$newcomments[ $comment_id ]['comment_parent']       = $comment['comment_parent'];
					$newcomments[ $comment_id ]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
					if ( isset( $this->processed_authors[ $comment['comment_user_id'] ] ) ) {
						$newcomments[ $comment_id ]['user_id'] = $this->processed_authors[ $comment['comment_user_id'] ];
					}
				}
				ksort( $newcomments );

				foreach ( $newcomments as $key => $comment ) {
					// if this is a new post we can skip the comment_exists() check
					if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
						if ( isset( $inserted_comments[ $comment['comment_parent'] ] ) ) {
							$comment['comment_parent'] = $inserted_comments[ $comment['comment_parent'] ];
						}
						$comment                   = wp_slash( $comment );
						$comment                   = wp_filter_comment( $comment );
						$inserted_comments[ $key ] = wp_insert_comment( $comment );
						do_action( 'wp_import_insert_comment', $inserted_comments[ $key ], $comment, $comment_post_ID, $post );

						foreach ( $comment['commentmeta'] as $meta ) {
							$value = maybe_unserialize( $meta['value'] );
							add_comment_meta( $inserted_comments[ $key ], $meta['key'], $value );
						}

						$num_comments ++;
					}
				}
				unset( $newcomments, $inserted_comments, $post['comments'] );
			}

			if ( ! isset( $post['postmeta'] ) ) {
				$post['postmeta'] = array();
			}

			$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

			// add/update post meta
			if ( ! empty( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					$key   = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
					$value = false;

					if ( '_edit_last' == $key ) {
						if ( isset( $this->processed_authors[ intval( $meta['value'] ) ] ) ) {
							$value = $this->processed_authors[ intval( $meta['value'] ) ];
						} else {
							$key = false;
						}
					}

					if ( $key ) {
						// export gets meta straight from the DB so could have a serialized string
						if ( ! $value ) {
							$value = maybe_unserialize( $meta['value'] );
						}

						add_post_meta( $post_id, $key, wp_slash( $value ) );

					}
				}
			}
		}

		unset( $this->posts );
	}

	/**
	 * Attempt to create a new menu item from import data
	 *
	 * Fails for draft, orphaned menu items and those without an associated nav_menu
	 * or an invalid nav_menu term. If the post type or term object which the menu item
	 * represents doesn't exist then the menu item will not be imported (waits until the
	 * end of the import to retry again before discarding).
	 *
	 * @param array $item Menu item details from WXR file
	 */
	function process_menu_item( $item ) {
		// skip draft, orphaned menu items
		if ( 'draft' == $item['status'] ) {
			return;
		}

		$menu_slug = false;
		if ( isset( $item['terms'] ) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' == $term['domain'] ) {
					$menu_slug = $term['slug'];
					break;
				}
			}
		}

		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			error_log( 'Pagex Importer: menu item skipped due to missing menu slug' );
			echo '<br />';

			return;
		}

		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			error_log( 'Pagex Importer: menu item skipped due to invalid menu slug: ' . $menu_slug );
			echo '<br />';

			return;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}

		foreach ( $item['postmeta'] as $meta ) {
			${$meta['key']} = $meta['value'];
		}

		if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[ intval( $_menu_item_object_id ) ] ) ) {
			$_menu_item_object_id = $this->processed_terms[ intval( $_menu_item_object_id ) ];
		} else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[ intval( $_menu_item_object_id ) ] ) ) {
			$_menu_item_object_id = $this->processed_posts[ intval( $_menu_item_object_id ) ];
		} else if ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$this->missing_menu_items[] = $item;

			return;
		}

		if ( isset( $this->processed_menu_items[ intval( $_menu_item_menu_item_parent ) ] ) ) {
			$_menu_item_menu_item_parent = $this->processed_menu_items[ intval( $_menu_item_menu_item_parent ) ];
		} else if ( $_menu_item_menu_item_parent ) {
			$this->menu_item_orphans[ intval( $item['post_id'] ) ] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent                           = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) ) {
			$_menu_item_classes = implode( ' ', $_menu_item_classes );
		}

		$args = array(
			'menu-item-object-id'   => $_menu_item_object_id,
			'menu-item-object'      => $_menu_item_object,
			'menu-item-parent-id'   => $_menu_item_menu_item_parent,
			'menu-item-position'    => intval( $item['menu_order'] ),
			'menu-item-type'        => $_menu_item_type,
			'menu-item-title'       => $item['post_title'],
			'menu-item-url'         => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title'  => $item['post_excerpt'],
			'menu-item-target'      => $_menu_item_target,
			'menu-item-classes'     => $_menu_item_classes,
			'menu-item-xfn'         => $_menu_item_xfn,
			'menu-item-status'      => $item['status']
		);

		$id = wp_update_nav_menu_item( $menu_id, 0, $args );

		if ( $id && ! is_wp_error( $id ) ) {
			$this->processed_menu_items[ intval( $item['post_id'] ) ] = (int) $id;
		}
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment
	 *
	 * @param array $post Attachment post details from WXR
	 * @param string $url URL to fetch attachment from
	 *
	 * @return int|WP_Error Post ID on success, WP_Error otherwise
	 */
	function process_attachment( $post, $url ) {
		if ( ! $this->fetch_attachments ) {
			return new WP_Error( 'attachment_processing_error',
				esc_html__( 'Fetching attachments is not enabled', 'pagex' ) );
		}

		// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
		if ( preg_match( '|^/[\w\W]+$|', $url ) ) {
			$url = rtrim( $this->base_url, '/' ) . $url;
		}

		$upload = $this->fetch_remote_file( $url, $post );
		if ( is_wp_error( $upload ) ) {
			return $upload;
		}

		if ( $info = wp_check_filetype( $upload['file'] ) ) {
			$post['post_mime_type'] = $info['type'];
		} else {
			return new WP_Error( 'attachment_processing_error', esc_html__( 'Invalid file type', 'pagex' ) );
		}

		$post['guid'] = $upload['url'];

		// as per wp-admin/includes/upload.php
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$parts = pathinfo( $url );
			$name  = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

			$parts_new = pathinfo( $upload['url'] );
			$name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

			$this->url_remap[ $parts['dirname'] . '/' . $name ] = $parts_new['dirname'] . '/' . $name_new;
		}

		return $post_id;
	}

	/**
	 * Attempt to download a remote file attachment
	 *
	 * @param string $url URL of item to fetch
	 * @param array $post Attachment details
	 *
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise
	 */
	function fetch_remote_file( $url, $post ) {
		// extract the file name and extension from the url
		$file_name = basename( $url );

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
		if ( $upload['error'] ) {
			return new WP_Error( 'upload_dir_error', $upload['error'] );
		}

		// fetch the remote url and write it to the placeholder file
		$remote_response = wp_safe_remote_get( $url, array(
			'timeout'  => 300,
			'stream'   => true,
			'filename' => $upload['file'],
		) );

		$headers = wp_remote_retrieve_headers( $remote_response );

		// request failed
		if ( ! $headers ) {
			@unlink( $upload['file'] );

			return new WP_Error( 'import_file_error', esc_html__( 'Remote server did not respond', 'pagex' ) );
		}

		$remote_response_code = wp_remote_retrieve_response_code( $remote_response );

		// make sure the fetch was successful
		if ( $remote_response_code != '200' ) {
			@unlink( $upload['file'] );

			return new WP_Error( 'import_file_error', sprintf( esc_html__( 'Remote server returned error response %1$d %2$s', 'pagex' ), esc_html( $remote_response_code ), get_status_header_desc( $remote_response_code ) ) );
		}

		$filesize = filesize( $upload['file'] );

		if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
			@unlink( $upload['file'] );

			return new WP_Error( 'import_file_error', esc_html__( 'Remote file is incorrect size', 'pagex' ) );
		}

		if ( 0 == $filesize ) {
			@unlink( $upload['file'] );

			return new WP_Error( 'import_file_error', esc_html__( 'Zero size file downloaded', 'pagex' ) );
		}

		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			@unlink( $upload['file'] );

			return new WP_Error( 'import_file_error', sprintf( esc_html__( 'Remote file is too large, limit is %s', 'pagex' ), size_format( $max_size ) ) );
		}

		// keep track of the old and new urls so we can substitute them later
		$this->url_remap[ $url ]          = $upload['url'];
		$this->url_remap[ $post['guid'] ] = $upload['url']; // r13735, really needed?
		// keep track of the destination if the remote url is redirected somewhere else
		if ( isset( $headers['x-final-location'] ) && $headers['x-final-location'] != $url ) {
			$this->url_remap[ $headers['x-final-location'] ] = $upload['url'];
		}

		return $upload;
	}


	/**
	 * Parse a WXR file
	 *
	 * @param string $file Path to WXR file for parsing
	 *
	 * @return array Information gathered from the WXR file
	 */
	function parse( $file ) {
		$parser = new WXR_Parser();

		return $parser->parse( $file );
	}


	/**
	 * Decide if the given meta key maps to information we will want to import
	 *
	 * @param string $key The meta key to check
	 *
	 * @return string|bool The key if we do want to import, false if not
	 */
	function is_valid_meta_key( $key ) {
		// skip attachment metadata since we'll regenerate it from scratch
		// skip _edit_lock as not relevant for import
		if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) ) {
			return false;
		}

		return $key;
	}

	/**
	 * Decide whether or not the importer is allowed to create users.
	 * Default is true, can be filtered via import_allow_create_users
	 *
	 * @return bool True if creating users is allowed
	 */
	function allow_create_users() {
		return apply_filters( 'import_allow_create_users', true );
	}

	/**
	 * Decide whether or not the importer should attempt to download attachment files.
	 * Default is true, can be filtered via import_allow_fetch_attachments. The choice
	 * made at the import options screen must also be true, false here hides that checkbox.
	 *
	 * @return bool True if downloading attachments is allowed
	 */
	function allow_fetch_attachments() {
		return apply_filters( 'import_allow_fetch_attachments', true );
	}

	/**
	 * Decide what the maximum file size for downloaded attachments is.
	 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
	 *
	 * @return int Maximum attachment file size to import
	 */
	function max_attachment_size() {
		return apply_filters( 'import_attachment_size_limit', 0 );
	}

	/**
	 * Added to http_request_timeout filter to force timeout at 60 seconds during import
	 * @return int 60
	 */
	function bump_request_timeout( $val ) {
		return 60;
	}

	// return the difference in length between two strings
	function cmpr_strlen( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}
}
