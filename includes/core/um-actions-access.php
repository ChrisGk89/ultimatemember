<?php
	/**
	 * Global Access Settings
	 */
	function um_access_global_settings() {
		global $post, $wp_query;

		$access = um_get_option( 'accessible' );

		if ( $access == 2 && ! is_user_logged_in() ) {

            if ( um_is_core_post( $post, 'register' ) || um_is_core_post( $post, 'password-reset' ) ) {

                UM()->access()->allow_access = true;

            } else {

                $redirect = um_get_option( 'access_redirect' );
                if ( ! $redirect )
                    $redirect = um_get_core_page( 'login' );

                $redirects[] = untrailingslashit( um_get_core_page( 'login' ) );
                $redirects[] = untrailingslashit( um_get_option( 'access_redirect' ) );

                $exclude_uris = um_get_option( 'access_exclude_uris' );
                if ( $exclude_uris )
                    $redirects = array_merge( $redirects, $exclude_uris );

                $redirects = array_unique( $redirects );

                $current_url = UM()->permalinks()->get_current_url( get_option( 'permalink_structure' ) );
                $current_url = untrailingslashit( $current_url );
                $current_url_slash = trailingslashit( $current_url );

                if ( isset( $post->ID ) && ( in_array( $current_url, $redirects ) || in_array( $current_url_slash, $redirects ) ) ) {
                    // allow
                }else {
                    UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                }

                // Disallow access in homepage
                if( /*is_front_page() ||*/ is_home() ){
                    $home_page_accessible = um_get_option( "home_page_accessible" );
                    if ( $home_page_accessible == 0 ) {
                        UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );

                        wp_redirect( UM()->access()->redirect_handler ); exit;
                    }

                }

                // Disallow access in category pages
                if ( is_category() ) {
                    $cat_obj = $wp_query->get_queried_object();
                    $restriction = get_term_meta( $cat_obj->term_id, 'um_content_restriction', true );

                    if ( ! empty( $restriction['_um_custom_access_settings'] ) ) {

                        if ( ! isset( $restriction['_um_accessible'] ) || '0' == $restriction['_um_accessible'] ) {

                            UM()->access()->allow_access = true;

                        } else {
                            //post is private
                            if ( '1' == $restriction['_um_accessible'] ) {
                                //if post for not logged in users and user is not logged in
                                if ( ! is_user_logged_in() || current_user_can( 'administrator' ) ) {
                                    UM()->access()->allow_access = true;
                                } else {
                                    if ( ! isset( $restriction['_um_noaccess_action'] ) || '0' == $restriction['_um_noaccess_action'] ) {
                                        UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                        wp_redirect( UM()->access()->redirect_handler ); exit;
                                    } elseif ( '1' == $restriction['_um_noaccess_action'] ) {
                                        $curr = UM()->permalinks()->get_current_url();

                                        if ( ! isset( $restriction['_um_access_redirect'] ) || '0' == $restriction['_um_access_redirect'] ) {

                                            UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                            wp_redirect( UM()->access()->redirect_handler ); exit;

                                        } elseif ( '1' == $restriction['_um_access_redirect'] ) {

                                            if ( ! empty( $restriction['_um_access_redirect_url'] ) ) {
                                                $redirect = $restriction['_um_access_redirect_url'];
                                            } else {
                                                $redirect = esc_url( add_query_arg( 'redirect_to', urlencode_deep( $curr ), um_get_core_page( 'login' ) ) );
                                            }

                                            UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                            wp_redirect( UM()->access()->redirect_handler ); exit;
                                        }

                                    }
                                }
                            } elseif ( '2' == $restriction['_um_accessible'] ) {
                                //if post for logged in users and user is not logged in
                                if ( is_user_logged_in() ) {

                                    if ( current_user_can( 'administrator' ) ) {
                                        UM()->access()->allow_access = true;
                                    }

                                    $user_can = $this->user_can( get_current_user_id(), $restriction['_um_access_roles'] );

                                    if ( $user_can ) {
                                        UM()->access()->allow_access = true;
                                    }


                                    //if single post query
                                    if ( ! isset( $restriction['_um_noaccess_action'] ) || '0' == $restriction['_um_noaccess_action'] ) {
                                        UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                        wp_redirect( UM()->access()->redirect_handler ); exit;
                                    } elseif ( '1' == $restriction['_um_noaccess_action'] ) {

                                        $curr = UM()->permalinks()->get_current_url();

                                        if ( ! isset( $restriction['_um_access_redirect'] ) || '0' == $restriction['_um_access_redirect'] ) {

                                            UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                            wp_redirect( UM()->access()->redirect_handler ); exit;

                                        } elseif ( '1' == $restriction['_um_access_redirect'] ) {

                                            if ( ! empty( $restriction['_um_access_redirect_url'] ) ) {
                                                $redirect = $restriction['_um_access_redirect_url'];
                                            } else {
                                                $redirect = esc_url( add_query_arg( 'redirect_to', urlencode_deep( $curr ), um_get_core_page( 'login' ) ) );
                                            }

                                            UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                            wp_redirect( UM()->access()->redirect_handler ); exit;
                                        }

                                    }
                                } else {

                                    //if single post query
                                    if ( ! isset( $restriction['_um_noaccess_action'] ) || '0' == $restriction['_um_noaccess_action'] ) {
                                        UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                        wp_redirect( UM()->access()->redirect_handler ); exit;
                                    } elseif ( '1' == $restriction['_um_noaccess_action'] ) {

                                        $curr = UM()->permalinks()->get_current_url();

                                        if ( ! isset( $restriction['_um_access_redirect'] ) || '0' == $restriction['_um_access_redirect'] ) {
                                            UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                            wp_redirect( UM()->access()->redirect_handler ); exit;
                                        } elseif ( '1' == $restriction['_um_access_redirect'] ) {

                                            if ( ! empty( $restriction['_um_access_redirect_url'] ) ) {
                                                $redirect = $restriction['_um_access_redirect_url'];
                                            } else {
                                                $redirect = esc_url( add_query_arg( 'redirect_to', urlencode_deep( $curr ), um_get_core_page( 'login' ) ) );
                                            }

                                            UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                            wp_redirect( UM()->access()->redirect_handler ); exit;
                                        }
                                    }
                                }
                            }
                        }
                    } else {

                        if ( is_user_logged_in() && current_user_can( 'administrator' ) ) {
                            UM()->access()->allow_access = true;
                        } else {
                            $category_page_accessible = um_get_option( "category_page_accessible" );
                            if ( $category_page_accessible == 0 ) {

                                UM()->access()->redirect_handler = UM()->access()->set_referer( $redirect, "global" );
                                wp_redirect( UM()->access()->redirect_handler ); exit;

                            } else {

                                UM()->access()->allow_access = true;

                            }
                        }

                    }
                }
            }
		}

		$current_page_type = um_get_current_page_type();
			
		do_action( 'um_access_post_type', $current_page_type );
		do_action( "um_access_post_type_{$current_page_type}" );
	}
    add_action( 'um_access_global_settings', 'um_access_global_settings' );


    /**
     * Archives/Taxonomies/Categories access settings
     */
    add_action( 'um_access_category_settings', 'um_access_category_settings' );
    function um_access_category_settings() {
        global $post;
        if ( is_front_page() ||
            is_home() ||
            is_feed() ||
            is_page() ||
            is_404()
        ) {
            return;
        }

        $access = um_get_option( 'accessible' );
        $current_page_type = um_get_current_page_type();


        if ( is_category() && !in_array( $current_page_type, array( 'day', 'month', 'year', 'author', 'archive' ) ) ) {

            $um_category = get_the_category();
            $um_category = current( $um_category );
            $term_id = '';

            if (isset( $um_category->term_id )) {
                $term_id = $um_category->term_id;
            }

            if (isset( $term_id ) && !empty( $term_id )) {

                $opt = get_term_meta($term_id,'um_content_restriction',true);

                if (isset( $opt['_um_accessible'] )) {

                    $redirect = false;

                    switch ($opt['_um_accessible']) {

                        case 0:

                            UM()->access()->allow_access = true;
                            UM()->access()->redirect_handler = ''; // open to everyone

                            break;

                        case 1:

                            if (is_user_logged_in()) {

                                if (isset( $opt['_um_redirect2'] ) && !empty( $opt['_um_redirect2'] )) {
                                    $redirect = $opt['_um_redirect2'];
                                } else {
                                    $redirect = site_url();
                                }
                            }

                            UM()->access()->allow_access = false;

                            $redirect = UM()->access()->set_referer( $redirect, "category_1" );

                            UM()->access()->redirect_handler = esc_url( $redirect );

                            if (!is_user_logged_in() && !empty( $redirect )) {
                                UM()->access()->allow_access = true;
                            }

                            break;

                        case 2:

                            if (!is_user_logged_in()) {

                                if (isset( $opt['_um_redirect'] ) && !empty( $opt['_um_redirect'] )) {
                                    $redirect = $opt['_um_redirect'];
                                } else {
                                    $redirect = um_get_core_page( 'login' );
                                }

                                UM()->access()->allow_access = false;

                                $redirect = UM()->access()->set_referer( $redirect, "category_2a" );

                                UM()->access()->redirect_handler = esc_url( $redirect );
                            }

                            if (is_user_logged_in() && isset( $opt['_um_roles'] ) && !empty( $opt['_um_roles'] )) {
                                if (!in_array( um_user( 'role' ), $opt['_um_roles'] )) {


                                    if (isset( $opt['_um_redirect'] )) {
                                        $redirect = $opt['_um_redirect'];
                                    }
                                    $redirect = UM()->access()->set_referer( $redirect, "category_2b" );

                                    UM()->access()->redirect_handler = esc_url( $redirect );

                                }
                            }

                    }
                }
            }

        } else if ($access == 2 && !is_user_logged_in() && is_archive()) {

            UM()->access()->allow_access = false;
            $redirect = um_get_core_page( 'login' );
            $redirect = UM()->access()->set_referer( $redirect, "category_archive" );

            UM()->access()->redirect_handler = $redirect;

        } else if (is_tax() && get_post_taxonomies( $post )) {

            $taxonomies = get_post_taxonomies( $post );
            $categories_ids = array();

            foreach ($taxonomies as $key => $value) {
                $term_list = wp_get_post_terms( $post->ID, $value, array( "fields" => "ids" ) );
                foreach ($term_list as $term_id) {
                    array_push( $categories_ids, $term_id );
                }
            }

            foreach ($categories_ids as $term => $term_id) {

                $opt = get_term_meta($term_id,'um_content_restriction',true);

                if (isset( $opt['_um_accessible'] )) {
                    switch ($opt['_um_accessible']) {

                        case 0:
                            UM()->access()->allow_access = true;
                            UM()->access()->redirect_handler = false; // open to everyone
                            break;

                        case 1:

                            if (is_user_logged_in())
                                $redirect = ( isset( $opt['_um_redirect2'] ) && !empty( $opt['_um_redirect2'] ) ) ? $opt['_um_redirect2'] : site_url();
                            $redirect = UM()->access()->set_referer( $redirect, "categories_1" );
                            UM()->access()->redirect_handler = $redirect;
                            if (!is_user_logged_in())
                                UM()->access()->allow_access = true;

                            break;

                        case 2:

                            if (!is_user_logged_in()) {

                                $redirect = ( isset( $opt['_um_redirect'] ) && !empty( $opt['_um_redirect'] ) ) ? $opt['_um_redirect'] : um_get_core_page( 'login' );
                                $redirect = UM()->access()->set_referer( $redirect, "categories_2a" );

                                UM()->access()->redirect_handler = $redirect;
                            }

                            if (is_user_logged_in() && isset( $opt['_um_roles'] ) && !empty( $opt['_um_roles'] )) {
                                if (!in_array( um_user( 'role' ), $opt['_um_roles'] )) {
                                    $redirect = null;
                                    if (is_user_logged_in()) {
                                        $redirect = ( isset( $opt['_um_redirect'] ) ) ? $opt['_um_redirect'] : site_url();
                                    }

                                    if (!is_user_logged_in()) {
                                        $redirect = um_get_core_page( 'login' );
                                    }

                                    $redirect = UM()->access()->set_referer( $redirect, "categories_2b" );
                                    UM()->access()->redirect_handler = $redirect;
                                }
                            }

                    }
                }

            }
        }

    }

    /**
     * Tags access settings
     */
    add_action( 'um_access_tags_settings', 'um_access_tags_settings' );
    function um_access_tags_settings() {

        if ( is_front_page() ||
            is_home() ||
            is_feed() ||
            is_page() ||
            is_404()
        ) {

            return;

        }

        $access = um_get_option( 'accessible' );
        $current_page_type = um_get_current_page_type();

        $tag_id = get_query_var( 'tag_id' );

        if (is_tag() && $current_page_type == 'tag' && $tag_id) {

            if (isset( $tag_id ) && !empty( $tag_id )) {

                $opt = get_term_meta($tag_id,'um_content_restriction',true);

                if (isset( $opt['_um_accessible'] )) {

                    $redirect = false;

                    switch ($opt['_um_accessible']) {

                        case 0:

                            UM()->access()->allow_access = true;
                            UM()->access()->redirect_handler = ''; // open to everyone

                            break;

                        case 1:

                            if (is_user_logged_in()) {

                                if (isset( $opt['_um_redirect2'] ) && !empty( $opt['_um_redirect2'] )) {
                                    $redirect = $opt['_um_redirect2'];
                                } else {
                                    $redirect = site_url();
                                }
                            }

                            UM()->access()->allow_access = false;

                            $redirect = UM()->access()->set_referer( $redirect, "tag_1" );

                            UM()->access()->redirect_handler = esc_url( $redirect );

                            if (!is_user_logged_in() && !empty( $redirect )) {
                                UM()->access()->allow_access = true;
                            }

                            break;

                        case 2:

                            if (!is_user_logged_in()) {

                                if (isset( $opt['_um_redirect'] ) && !empty( $opt['_um_redirect'] )) {
                                    $redirect = $opt['_um_redirect'];
                                } else {
                                    $redirect = um_get_core_page( 'login' );
                                }

                                UM()->access()->allow_access = false;

                                $redirect = UM()->access()->set_referer( $redirect, "tag_2" );

                                UM()->access()->redirect_handler = esc_url( $redirect );
                            }

                            if (is_user_logged_in() && isset( $opt['_um_roles'] ) && !empty( $opt['_um_roles'] )) {
                                if (!in_array( um_user( 'role' ), $opt['_um_roles'] )) {


                                    if (isset( $opt['_um_redirect'] )) {
                                        $redirect = $opt['_um_redirect'];
                                    }
                                    $redirect = UM()->access()->set_referer( $redirect, "tag_2b" );

                                    UM()->access()->redirect_handler = esc_url( $redirect );

                                }
                            }

                    }
                }
            }

        } else if ($access == 2 && !is_user_logged_in() && is_tag()) {

            UM()->access()->allow_access = false;
            $redirect = um_get_core_page( 'login' );
            $redirect = UM()->access()->set_referer( $redirect, "tag" );

            UM()->access()->redirect_handler = $redirect;

        }

    }


	/**
	 * Custom User homepage redirection
	 */
	function um_access_user_custom_homepage() {
		if( ! is_user_logged_in() ) return;
		if ( ! is_home() ) return;

		$role_meta = UM()->roles()->role_data( um_user( 'role' ) );
		
		if ( empty( $role_meta['default_homepage'] ) ) {

            $redirect_to = ! empty( $role_meta['redirect_homepage'] ) ? $role_meta['redirect_homepage'] : um_get_core_page( 'user' );

            $redirect_to = UM()->access()->set_referer( $redirect_to, "custom_homepage" );

            wp_redirect( $redirect_to );
            exit;

		}
	}
    add_action( 'um_access_user_custom_homepage', 'um_access_user_custom_homepage' );


	/**
	 * Front page access settings
	 */
	function um_access_frontpage_per_role() {
		global $post;

		if ( is_admin() ) return;
		/*if ( ! is_front_page()  ) return;*/
		if(  is_404() ) return;
		
		if ( ! isset( $um_post_id ) && isset( $post->ID ) ){
			$um_post_id = $post->ID;
		}

		if( ! isset( $um_post_id ) ){
			return;
		}

		$args = UM()->access()->get_meta( $um_post_id );
		extract( $args );

		if ( !isset( $args['custom_access_settings'] ) || $args['custom_access_settings'] == 0 ) {

			$um_post_id = apply_filters('um_access_control_for_parent_posts', $um_post_id );

			$args = UM()->access()->get_meta( $um_post_id );
			extract( $args );

			if ( !isset( $args['custom_access_settings'] ) || $args['custom_access_settings'] == 0 ) {
				return;
			}

		}

		$redirect_to = null;

		if ( !isset( $accessible ) ) return;

		switch( $accessible ) {

			case 0:
				UM()->access()->allow_access = true;
				UM()->access()->redirect_handler = false; // open to everyone

				break;

			case 1:

				$redirect_to = $access_redirect2;
					
				if ( is_user_logged_in() ){
					UM()->access()->allow_access = false;
				}

				if ( ! is_user_logged_in()  ){
					UM()->access()->allow_access = true;
				}

				if( ! empty( $redirect_to  ) ){
					$redirect_to = UM()->access()->set_referer( $redirect_to, "frontpage_per_role_1a" );
					UM()->access()->redirect_handler = esc_url( $redirect_to );
				}else{
					if ( ! is_user_logged_in() ){
						$redirect_to = um_get_core_page("login");
					}else{
						$redirect_to = um_get_core_page("user");
					}

					$redirect_to = UM()->access()->set_referer( $redirect_to, "frontpage_per_role_1b" );
					UM()->access()->redirect_handler = esc_url( $redirect_to );
				}


				break;

			case 2:

				if ( ! is_user_logged_in() ){

					if ( empty( $access_redirect ) ) {
						$access_redirect = um_get_core_page('login');
					}
					
					$redirect_to = $access_redirect;
					$redirect_to = UM()->access()->set_referer( $redirect_to, "frontpage_per_role_2a" );
				
				}

				if ( is_user_logged_in() && isset( $access_roles ) && !empty( $access_roles ) ){
					$access_roles = unserialize( $access_roles );
					$access_roles = array_filter($access_roles);

					if ( ! empty( $access_roles ) && ! in_array( um_user( 'role' ), $access_roles ) ) {
						if ( empty( $access_redirect ) ) {
							if ( is_user_logged_in() ) {
								$access_redirect = site_url();
							} else {
								$access_redirect = um_get_core_page('login');
							}
						}
						$redirect_to = esc_url( $access_redirect );
						$redirect_to = UM()->access()->set_referer( $redirect_to, "frontpage_per_role_2b" );
				
					}
				}

					
				UM()->access()->redirect_handler = esc_url( $redirect_to );
				
				break;

		}

	}
    add_action( 'um_access_frontpage_per_role', 'um_access_frontpage_per_role' );


	/**
	 * Posts page access settings
	 */
	function um_access_homepage_per_role() {
		global $post;

		if ( is_admin() ) return;
		if ( ! is_home() ) return;
		if ( is_404() ) return;
		
		$access = um_get_option('accessible');

		$show_on_front = get_option( 'show_on_front' );

		if( $show_on_front == "page" ){

			$um_post_id = get_option( 'page_for_posts' );
			
			if ( $access == 2 && ! is_user_logged_in() ) {
				UM()->access()->allow_access = false;
			}else{
				UM()->access()->allow_access = true;
			}
		
		}else if( $show_on_front == "posts" ){
            UM()->access()->allow_access = true;
		}



		if ( isset( $um_post_id ) ){
		
			$args = UM()->access()->get_meta( $um_post_id );
			extract( $args );

			if ( !isset( $args['custom_access_settings'] ) || $args['custom_access_settings'] == 0 ) {

				$um_post_id = apply_filters('um_access_control_for_parent_posts', $um_post_id );

				$args = UM()->access()->get_meta( $um_post_id );
				extract( $args );

				if ( !isset( $args['custom_access_settings'] ) || $args['custom_access_settings'] == 0 ) {
					return;
				}

			}

			$redirect_to = null;

			if ( !isset( $accessible ) ) return;

			switch( $accessible ) {

				case 0:
					UM()->access()->allow_access = true;
					UM()->access()->redirect_handler = false; // open to everyone

					break;

				case 1:

					$redirect_to = esc_url( $access_redirect2 );
						
					if ( is_user_logged_in() ){
						UM()->access()->allow_access = false;
					}

					if ( ! is_user_logged_in()  ){
						UM()->access()->allow_access = true;
					}

					if( ! empty( $redirect_to  ) ){
						$redirect_to = UM()->access()->set_referer( $redirect_to, "homepage_per_role_1a" );
						UM()->access()->redirect_handler = esc_url( $redirect_to );
					}else{
						$redirect_to = null;
						if ( ! is_user_logged_in() ){
							$redirect_to = um_get_core_page("login");
						}else{
							$redirect_to = um_get_core_page("user");
						}
						$redirect_to = UM()->access()->set_referer( $redirect_to, "homepage_per_role_1b" );
						UM()->access()->redirect_handler = esc_url( $redirect_to );
					}


					break;

				case 2:

					if ( ! is_user_logged_in() ){

						if ( empty( $access_redirect ) ) {
							$access_redirect = um_get_core_page('login');
						}
						
						$redirect_to = $access_redirect;
						$redirect_to = UM()->access()->set_referer( $redirect_to, "homepage_per_role_2a" );
					}

					if ( is_user_logged_in() && isset( $access_roles ) && !empty( $access_roles ) ){
						$access_roles = unserialize( $access_roles );
						$access_roles = array_filter($access_roles);

						if ( ! empty( $access_roles ) && ! in_array( um_user( 'role' ), $access_roles ) ) {
							if ( ! $access_redirect ) {
								if ( is_user_logged_in() ) {
									$access_redirect = site_url();
								} else {
									$access_redirect = um_get_core_page('login');
								}
							}

							$redirect_to = $access_redirect;
							$redirect_to = UM()->access()->set_referer( $redirect_to, "homepage_per_role_2b" );
					
						}
					}
					UM()->access()->redirect_handler = esc_url( $redirect_to );
					
					break;

			}
		}
	}
    add_action( 'um_access_homepage_per_role', 'um_access_homepage_per_role' );


    /**
     * Profile Access
     *
     * @param int $user_id
     */
	function um_access_profile( $user_id ) {

		if ( ! um_is_myprofile() && um_is_core_page( 'user' ) && ! current_user_can( 'edit_users' ) ) {
			
			um_fetch_user( $user_id );

			if ( ! in_array( um_user( 'account_status' ), array( 'approved' ) ) ) {
				um_redirect_home();
			}

			um_reset_user();
			
		}
	}
    add_action( 'um_access_profile', 'um_access_profile' );