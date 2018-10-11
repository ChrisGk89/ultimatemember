<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Field is required?
 *
 * @param $label
 * @param $data
 *
 * @return string
 */
function um_edit_label_all_fields( $label, $data ) {

	$asterisk = UM()->options()->get( 'form_asterisk' );
	if ( $asterisk && isset( $data['required'] ) && $data['required'] == 1 )
		$label = $label . '<span class="um-req" title="'.__('Required','ultimate-member').'">*</span>';

	return $label;
}
add_filter( 'um_edit_label_all_fields', 'um_edit_label_all_fields', 10, 2 );


/**
 * Outputs a soundcloud track
 *
 * @param $value
 * @param $data
 *
 * @return string|void
 */
function um_profile_field_filter_hook__soundcloud_track( $value, $data ) {

	if ( !is_numeric( $value ) ) {
		return __( 'Invalid soundcloud track ID', 'ultimate-member' );
	}

	$value = '<div class="um-soundcloud">
					<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' . $value . '&amp;color=ff6600&amp;auto_play=false&amp;show_artwork=true"></iframe>
					</div>';

	return $value;
}
add_filter( 'um_profile_field_filter_hook__soundcloud_track', 'um_profile_field_filter_hook__soundcloud_track', 99, 2 );


/**
 * Outputs a youtube video
 *
 * @param $value
 * @param $data
 *
 * @return bool|string
 */
function um_profile_field_filter_hook__youtube_video( $value, $data ) {
	if( empty( $value ) ){
		return '';
	}
	$value = ( strstr( $value, 'http') || strstr( $value, '://' ) ) ? um_youtube_id_from_url( $value ) : $value;
	$value = '<div class="um-youtube">
					<iframe width="600" height="450" src="https://www.youtube.com/embed/' . $value . '" frameborder="0" allowfullscreen></iframe>
					</div>';

	return $value;
	}
add_filter( 'um_profile_field_filter_hook__youtube_video', 'um_profile_field_filter_hook__youtube_video', 99, 2 );


/**
 * Outputs a vimeo video
 *
 * @param $value
 * @param $data
 *
 * @return int|string
 */
function um_profile_field_filter_hook__vimeo_video( $value, $data ) {
	if ( empty( $value ) ) {
		return '';
	}

	$value = ( !is_numeric( $value ) ) ? (int) substr(parse_url($value, PHP_URL_PATH), 1) : $value;
	$value = '<div class="um-vimeo">
					<iframe src="https://player.vimeo.com/video/'. $value . '" width="600" height="450" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					</div>';
	return $value;
}
add_filter( 'um_profile_field_filter_hook__vimeo_video', 'um_profile_field_filter_hook__vimeo_video', 99, 2 );


/**
 * Outputs a google map
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function um_profile_field_filter_hook__googlemap( $value, $data ) {
	$value = '<div class="um-googlemap">
					<iframe width="600" height="450" frameborder="0" style="border:0" src="https://maps.google.it/maps?q=' . urlencode( $value ) . '&output=embed"></iframe>
				</div>';
	return $value;
}
add_filter( 'um_profile_field_filter_hook__googlemap', 'um_profile_field_filter_hook__googlemap', 99, 2 );


/**
 * User's registration date
 *
 * @param $value
 * @param $data
 *
 * @return false|int|string
 */

function um_profile_field_filter_hook__user_registered( $value, $data ) {
	$value = strtotime($value);
	$value = sprintf(__('Joined %s','ultimate-member'), date_i18n('F d, Y', $value) );
	return $value;
}
add_filter( 'um_profile_field_filter_hook__user_registered', 'um_profile_field_filter_hook__user_registered', 99, 2 );


/**
 * Last login date
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function um_profile_field_filter_hook__last_login( $value, $data ) {
	$value = sprintf( __('Last login: %s','ultimate-member'), um_user_last_login( um_user('ID') ) );
	return $value;
}
add_filter( 'um_profile_field_filter_hook__last_login', 'um_profile_field_filter_hook__last_login', 99, 2 );
add_filter( 'um_profile_field_filter_hook___um_last_login', 'um_profile_field_filter_hook__last_login', 99, 2 );


/**
 * URLs in textarea
 *
 * @param $value
 * @param $data
 *
 * @return mixed|string|void
 */
function um_profile_field_filter_hook__textarea( $value, $data ) {
	if ( isset( $data['html'] ) && $data['html'] == 1 ) {
		return $value;
	}

	$value = esc_textarea( $value );
	$value = preg_replace('$(https?://[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', ' <a href="$1" target="_blank">$1</a> ', $value." ");
	$value = preg_replace('$(www\.[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', '<a target="_blank" href="http://$1">$1</a> ', $value." ");
	$value = wpautop($value);

	return $value;
}
add_filter( 'um_profile_field_filter_hook__textarea', 'um_profile_field_filter_hook__textarea', 99, 2 );


    /***
     ***	@urls in description
     ***/
/*    add_filter('um_profile_field_filter_hook__description', 'um_profile_field_filter_hook__description', 99, 2);
    function um_profile_field_filter_hook__description( $value, $data ) {

        if ( isset( $data ) && isset( $data['html'] ) && $data['html'] == 1 )
            return $value;

        $value = esc_textarea( $value );
        $value = preg_replace('$(https?://[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', ' <a href="$1" target="_blank">$1</a> ', $value." ");
        $value = preg_replace('$(www\.[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', '<a target="_blank" href="http://$1">$1</a> ', $value." ");

        return $value;
    }*/


/**
 * Time field
 *
 * @param $value
 * @param $data
 *
 * @return mixed|string
 */
function um_profile_field_filter_hook__time( $value, $data ) {
	$value = UM()->datetime()->format( $value, $data['format'] );

	$value = str_replace('am', 'a.m.', $value );
	$value = str_replace('pm', 'p.m.', $value );
	return $value;
}
add_filter( 'um_profile_field_filter_hook__time', 'um_profile_field_filter_hook__time', 99, 2 );

/**
 * Translations for day and time
 * @staticvar array $translations
 * @param string $locale
 * @return array
 */
function um_get_pickadate_translations( $locale = NULL ) {
	static $translations = array();

	if ( empty( $locale ) ) {
		$locale = get_locale();
	}

	if ( empty( $translations[$locale] ) ) {
		$translations[$locale] = array();

		if ( file_exists( um_path . 'assets/js/pickadate/translations/' . $locale . '.js' ) ) {
			$file = file_get_contents( um_path . 'assets/js/pickadate/translations/' . $locale . '.js' );
		}
		elseif ( file_exists( um_path . 'assets/js/pickadate/translations/' . substr( $locale, 0, 2 ) . '.js' ) ) {
			$file = file_get_contents( um_path . 'assets/js/pickadate/translations/' . substr( $locale, 0, 2 ) . '.js' );
		}

		if ( isset( $file ) ) {
			preg_match( '/.*monthsFull:\[([^\]]+).*monthsShort:\[([^\]]+).*weekdaysFull:\[([^\]]+).*weekdaysShort:\[([^\]]+).*/i', $file, $matches );
		}

		if ( isset( $matches ) && count( $matches ) === 5 ) {
			try {
				$translations[$locale]['monthsFull'] = json_decode( "[{$matches[1]}]" );
				$translations[$locale]['monthsShort'] = json_decode( "[{$matches[2]}]" );
				$translations[$locale]['weekdaysFull'] = json_decode( "[{$matches[3]}]" );
				$translations[$locale]['weekdaysShort'] = json_decode( "[{$matches[4]}]" );
			} catch ( Exception $exc ) {
				error_log( 'Can not decode translations in ' . __FUNCTION__ );
			}
		}
	}

	return $translations[$locale];
}

/**
 * Date field
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function um_profile_field_filter_hook__date( $value, $data ) {
	if ( $data['pretty_format'] == 1 ) {
		$value = UM()->datetime()->get_age( $value );
	} else {
		$value = UM()->datetime()->format( $value, $data['format'] );
	}

	$translations = um_get_pickadate_translations();
	if ( !empty( $translations['monthsFull'] ) ) {
		$value = str_ireplace( array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'), $translations['monthsFull'], $value );
	}
	if ( !empty( $translations['monthsShort'] ) ) {
		$value = str_ireplace( array('jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'sept', 'oct', 'nov', 'dec'), $translations['monthsShort'], $value );
	}

	return $value;
}
add_filter( 'um_profile_field_filter_hook__date', 'um_profile_field_filter_hook__date', 99, 2 );


/**
 * File field
 * @param $value
 * @param $data
 *
 * @return string
 */
function um_profile_field_filter_hook__file( $value, $data ) {
	$file_type = wp_check_filetype( $value );
	$uri = UM()->files()->get_download_link( UM()->fields()->set_id, $data['metakey'], um_user( 'ID' ) );

	$removed = false;
	if ( ! file_exists( UM()->uploader()->get_upload_base_dir() . um_user( 'ID' ) . DIRECTORY_SEPARATOR . $value ) ) {
		if ( is_multisite() ) {
			//multisite fix for old customers
			$file_path = str_replace( DIRECTORY_SEPARATOR . 'sites' . DIRECTORY_SEPARATOR . get_current_blog_id() . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, UM()->uploader()->get_upload_base_dir() . um_user( 'ID' ) . DIRECTORY_SEPARATOR . $value );
			if ( ! file_exists( $file_path ) ) {
				$removed = true;
			}
		} else {
			$removed = true;
		}
	}

	if ( $removed ) {
		$value = __( 'This file has been removed.', 'ultimate-member' );
	} else {
		$file_info = um_user( $data['metakey'] . "_metadata" );
		if ( ! empty( $file_info['original_name'] ) ) {
			$value = $file_info['original_name'];
		}
		$value = '<div class="um-single-file-preview show">
                        <div class="um-single-fileinfo">
                            <a href="' . esc_attr( $uri )  . '" target="_blank">
                                <span class="icon" style="background:'. UM()->files()->get_fonticon_bg_by_ext( $file_type['ext'] ) . '"><i class="'. UM()->files()->get_fonticon_by_ext( $file_type['ext'] ) .'"></i></span>
                                <span class="filename">' . esc_attr( $value ) . '</span>
                            </a>
                        </div>
                    </div>';
	}

	return $value;
}
add_filter( 'um_profile_field_filter_hook__file', 'um_profile_field_filter_hook__file', 99, 2 );


/**
 * Image field
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function um_profile_field_filter_hook__image( $value, $data ) {
	$uri = UM()->files()->get_download_link( UM()->fields()->set_id, $data['metakey'], um_user( 'ID' ) );
	$title = ( isset( $data['title'] ) ) ? $data['title'] : __( 'Untitled photo', 'ultimate-member' );

	$removed = false;
	if ( ! file_exists( UM()->uploader()->get_upload_base_dir() . um_user( 'ID' ) . DIRECTORY_SEPARATOR . $value ) ) {
		if ( is_multisite() ) {
			//multisite fix for old customers
			$file_path = str_replace( DIRECTORY_SEPARATOR . 'sites' . DIRECTORY_SEPARATOR . get_current_blog_id() . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, UM()->uploader()->get_upload_base_dir() . um_user( 'ID' ) . DIRECTORY_SEPARATOR . $value );
			if ( ! file_exists( $file_path ) ) {
				$removed = true;
			}
		} else {
			$removed = true;
		}
	}

	// if value is an image tag
	if( preg_match( '/\<img.*src=\"([^"]+).*/', $value, $matches ) ) {
		$uri   = $matches[1];
		$value = '<div class="um-photo"><a href="#" class="um-photo-modal" data-src="' . esc_attr( $uri ) . '"><img src="' . esc_attr( $uri ) . '" alt="' . esc_attr( $title ) . '" title="' . esc_attr( $title ) . '" class="" /></a></div>';
	} else if ( ! $removed ) {
		$value = '<div class="um-photo"><a href="#" class="um-photo-modal" data-src="' . esc_attr( $uri ) . '"><img src="' . esc_attr( $uri ) . '" alt="' . esc_attr( $title ) . '" title="' . esc_attr( $title ) . '" class="" /></a></div>';
	} else {
		$value = '';
	}

	return $value;
}
add_filter( 'um_profile_field_filter_hook__image', 'um_profile_field_filter_hook__image', 99, 2 );

/**
 * Global sanitize
 *
 * @param $value
 * @param $data
 * @param string $type
 *
 * @return string
 */
function um_profile_field_filter_hook__( $value, $data, $type = '' ) {
	if ( !$value ) return '';

	if ( ( isset( $data['validate'] ) && $data['validate'] != '' && strstr( $data['validate'], 'url' ) ) || ( isset( $data['type'] ) && $data['type'] == 'url' ) ) {
		$alt = ( isset( $data['url_text'] ) && !empty( $data['url_text'] ) ) ? $data['url_text'] : $value;
		$url_rel = ( isset( $data['url_rel'] ) && $data['url_rel'] == 'nofollow' ) ? 'rel="nofollow"' : '';
		if( !strstr( $value, 'http' )
		    && !strstr( $value, '://' )
		    && !strstr( $value, 'www.' )
		    && !strstr( $value, '.com' )
		    && !strstr( $value, '.net' )
		    && !strstr( $value, '.org' )
		) {
			if ( $data['validate'] == 'soundcloud_url' ) 	$value = 'https://soundcloud.com/' . $value;
			if ( $data['validate'] == 'youtube_url' ) 		$value = 'https://youtube.com/user/' . $value;
			if ( $data['validate'] == 'facebook_url' ) 		$value = 'https://facebook.com/' . $value;
			if ( $data['validate'] == 'twitter_url' ) 		$value = 'https://twitter.com/' . $value;
			if ( $data['validate'] == 'linkedin_url' ) 		$value = 'https://linkedin.com/' . $value;
			if ( $data['validate'] == 'skype' ) 			$value = $value;
			if ( $data['validate'] == 'googleplus_url' ) 	$value = 'https://plus.google.com/' . $value;
			if ( $data['validate'] == 'instagram_url' ) 	$value = 'https://instagram.com/' . $value;
			if ( $data['validate'] == 'vk_url' ) 			$value = 'https://vk.com/' . $value;
		}

		if ( isset( $data['validate'] ) && $data['validate'] == 'skype' ) {

			$value = $value;

		} else {

			if ( strpos($value, 'http://') !== 0 ) {
				$value = 'http://' . $value;
			}
			$data['url_target'] = ( isset( $data['url_target'] ) ) ? $data['url_target'] : '_blank';
			$value = '<a href="'. $value .'" title="'.$alt.'" target="'.$data['url_target'].'" ' . $url_rel . '>'.$alt.'</a>';

		}

	}

	if ( !is_array( $value ) ) {
		if ( is_email( $value ) )
			$value = '<a href="mailto:'. $value.'" title="'.$value.'">'.$value.'</a>';
	} else {
		$value = implode(', ', $value);
	}

	$value = str_replace('https://https://','https://',$value);
	$value = str_replace('http://https://','https://',$value);
	//$value = UM()->shortcodes()->emotize( $value );
	return $value;

}
add_filter( 'um_profile_field_filter_hook__', 'um_profile_field_filter_hook__', 99, 3 );


/**
 * Get form fields
 *
 * @param $array
 *
 * @return mixed|string
 */
function um_get_form_fields( $array ) {

	$form_id = (isset ( UM()->fields()->set_id ) ) ? UM()->fields()->set_id : null;
	$mode = (isset( UM()->fields()->set_mode ) ) ? UM()->fields()->set_mode : null;

	if ( $form_id && $mode ) {
		$array = UM()->query()->get_attr('custom_fields', $form_id );
	} else {
		$array = '';
	}

	return $array;

}
add_filter( 'um_get_form_fields', 'um_get_form_fields', 99 );


/**
 * Validate conditional logic
 *
 * @param $array
 * @param $fields
 * @return mixed
 */
function um_get_custom_field_array( $array, $fields ) {

	if ( ! empty( $array['conditions'] ) ) {
		foreach ( $array['conditions'] as $key => $value ) {
			$condition_metakey = $fields[ $value[1] ]['metakey'];
			if ( isset( $_POST[ $condition_metakey ] ) ) {
				$cond_value = ( $fields[ $value[1] ]['type'] == 'radio' ) ? $_POST[ $condition_metakey ][0] : $_POST[ $condition_metakey ];
				list( $visibility, $parent_key, $op, $parent_value ) = $value;

				if ( $visibility == 'hide' ) {
					if ( $op == 'empty' ) {
						if ( empty( $cond_value ) ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'not empty' ) {
						if ( ! empty( $cond_value ) ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'equals to' ) {
						if ( $cond_value == $parent_value ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'not equals' ) {
						if ( $cond_value != $parent_value ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'greater than' ) {
						if ( $cond_value > $parent_value ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'less than' ) {
						if ( $cond_value < $parent_value ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'contains' ) {
						if ( is_string( $cond_value ) && strstr( $cond_value, $parent_value ) ) {
							$array['required'] = 0;
						}
						if( is_array( $cond_value ) && in_array( $parent_value, $cond_value ) ) {
							$array['required'] = 0;
						}
					}
				} elseif ( $visibility == 'show' ) {
					if ( $op == 'empty' ) {
						if ( ! empty( $cond_value ) ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'not empty' ) {
						if ( empty( $cond_value ) ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'equals to' ) {
						if ( $cond_value != $parent_value ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'not equals' ) {
						if ( $cond_value == $parent_value ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'greater than' ) {
						if ( $cond_value <= $parent_value ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'less than' ) {
						if ( $cond_value >= $parent_value ) {
							$array['required'] = 0;
						}
					} elseif ( $op == 'contains' ) {
						if( is_string( $cond_value ) && !strstr( $cond_value, $parent_value ) ) {
							$array['required'] = 0;
						}
						if( is_array( $cond_value ) && !in_array( $parent_value, $cond_value ) ) {
							$array['required'] = 0;
						}
					}
				}
			}
		}
	}

	return $array;
}
add_filter( 'um_get_custom_field_array', 'um_get_custom_field_array', 99, 2 );


/**
 * Force fields to use UTF-8 encoding
 *
 * @param mixed $value
 * @param array $data
 * @param string $type
 *
 * @return mixed
 */
function um_force_utf8_fields( $value, $data, $type = '' ) {

	if( ! UM()->options()->get('um_force_utf8_strings') )
		return $value;

		$value = um_force_utf8_string( $value );

		return $value;

	}
add_filter('um_profile_field_filter_hook__','um_force_utf8_fields', 9, 3 );


/**
 * Filter profile data value
 * @param  mixed $value
 * @return mixed
 * @uses   hook filter: um_is_selected_filter_value
 */
function um_is_selected_filter_value( $value ) {
	if ( ! UM()->options()->get('um_force_utf8_strings') )
		return $value;

	$value = um_force_utf8_string( $value );

	return $value;
}
add_filter( 'um_is_selected_filter_value','um_is_selected_filter_value', 1, 9 );
add_filter( 'um_select_dropdown_dynamic_option_value','um_is_selected_filter_value', 1, 10 );

/**
 * Filter select dropdown to use UTF-8 encoding
 *
 * @param  array $options
 * @param  array $data
 * @return array
 * @uses   hook filter: um_select_dropdown_dynamic_options
 */
function um_select_dropdown_dynamic_options_to_utf8( $options, $data ){
	if ( ! UM()->options()->get( 'um_force_utf8_strings' ) )
		return $options;

	foreach ( $options as $key => $value ) {
		$options[ $key ] = um_force_utf8_string( $value );
	}

	return $options;
}
add_filter( 'um_select_dropdown_dynamic_options','um_select_dropdown_dynamic_options_to_utf8', 2, 10 );


/**
 * Filter non-UTF8 strings
 * @param  string $value
 * @return string
 * @uses hook filter: um_field_non_utf8_value
 */
function um_field_non_utf8_value( $value ) {

	if ( function_exists( 'mb_detect_encoding' ) ) {
		$encoding = mb_detect_encoding( $value, 'utf-8, iso-8859-1, ascii', true );
		if ( strcasecmp( $encoding, 'UTF-8' ) !== 0 ) {
			$value = iconv( $encoding, 'utf-8', $value );
		}
	}

	return $value;
}
add_filter( 'um_field_non_utf8_value', 'um_field_non_utf8_value' );


/**
 * Returns dropdown/multi-select options from a callback function
 * @param  $options array
 * @param  $data array
 * @return array
 * @uses   hook filter: um_select_dropdown_dynamic_options, um_multiselect_options
 */
function um_select_dropdown_dynamic_callback_options( $options, $data ) {

	if( isset( $data['custom_dropdown_options_source'] ) && ! empty( $data['custom_dropdown_options_source'] ) ){

		if( function_exists( $data['custom_dropdown_options_source'] ) ){
			$options = call_user_func( $data['custom_dropdown_options_source'] );
		}
	}

	return $options;
}
add_filter( 'um_select_dropdown_dynamic_options','um_select_dropdown_dynamic_callback_options', 10, 2 );
add_filter( 'um_multiselect_options','um_select_dropdown_dynamic_callback_options', 10, 2 );


/**
 * Pair dropdown/multi-select options from a callback function
 *
 * @param  $value string
 * @param  $data  array
 * @return string
 * @uses   hook filter: um_profile_field_filter_hook__
 */

function um_option_match_callback_view_field( $value, $data ) {
	if( ! empty( $data['custom_dropdown_options_source'] ) ){
		return UM()->fields()->get_option_value_from_callback( $value, $data, $data['type'] );
	}

	return $value;
}
add_filter('um_profile_field_filter_hook__select','um_option_match_callback_view_field', 10, 2);
add_filter('um_profile_field_filter_hook__multiselect','um_option_match_callback_view_field', 10, 2);
add_filter('um_field_select_default_value','um_option_match_callback_view_field', 10, 2);
add_filter('um_field_multiselect_default_value','um_option_match_callback_view_field', 10, 2);


/**
 * Apply textdomain in select/multi-select options
 *
 * @param  $value string
 * @param  $data  array
 * @return string
 * @uses   hook filters: um_profile_field_filter_hook__select, um_profile_field_filter_hook__multiselect
 */

function um_profile_field__select_translate( $value, $data ) {

	if ( empty( $value  ) ) return $value;

	$options = explode(", ", $value );
	$arr_options = array();
	if( is_array( $options ) ){
		foreach ( $options as $item ) {
			$arr_options[] = __( $item, 'ultimate-member' );
		}
	}

	$value = implode(", ", $arr_options);

	return $value;
}
add_filter( 'um_profile_field_filter_hook__select','um_profile_field__select_translate', 10, 2 );
add_filter( 'um_profile_field_filter_hook__multiselect','um_profile_field__select_translate', 10, 2 );


/**
 * Cleaning on XSS injection
 * @param  $value string
 * @param  $data  array
 * @param  string $type
 * @return string $value
 * @uses   hook filters: um_profile_field_filter_hook__
 */
function um_profile_field_filter_xss_validation( $value, $data, $type = '' ) {
	if ( ! empty( $value ) && is_string( $value ) ) {
		$value = stripslashes( $value );
		$data['validate'] = isset( $data['validate'] ) ? $data['validate'] : '';

		if( 'text' == $type && ! in_array( $data['validate'], array( 'unique_email' ) ) || 'password' == $type ) {
			$value = esc_attr( $value );
		} elseif( $type == 'url' ) {
			$value = esc_url( $value );
		} elseif ( 'textarea' == $type ) {
			if ( empty( $data['html'] ) ) {
				$value =  wp_kses_post( $value );
			}
		}
	}

	return $value;
}
add_filter( 'um_profile_field_filter_hook__','um_profile_field_filter_xss_validation', 10, 3 );


/**
 * Trim All form POST submitted data
 *
 * @param $post_form
 * @param $mode
 *
 * @return mixed
 */
function um_submit_form_data_trim_fields( $post_form, $mode ) {
	foreach ( $post_form as $key => $field ) {
		if ( is_string( $field ) ) {
			$post_form[ $key ] = trim( $field );
		}
	}

	return $post_form;
}
add_filter( 'um_submit_form_data', 'um_submit_form_data_trim_fields', 9, 2 );


/**
 * add role_select and role_radio to the $post_form
 * It is necessary for that if on these fields the conditional logic
 * @param $post_form array
 * @param $mode
 *
 * @return $post_form
 * @uses   hook filters: um_submit_form_data
 */
function um_submit_form_data_role_fields( $post_form, $mode ) {
	$custom_fields = unserialize( $post_form['custom_fields'] );
	if ( ! empty( $post_form['role'] ) && array_key_exists( 'role_select', $custom_fields ) ) {
		$post_form['role_select'] = $post_form['role'];
	}
	if (! empty( $post_form['role'] ) && array_key_exists( 'role_radio', $custom_fields ) ) {
		$post_form['role_radio'] = $post_form['role'];
	}

	return $post_form;
}
add_filter( 'um_submit_form_data', 'um_submit_form_data_role_fields', 10, 2 );


/**
 * Cleaning on XSS injection for url editing field
 * @param  $value string
 * @param  $key   string
 *
 * @return string $value
 * @uses   hook filters: um_edit_url_field_value
 */
function um_edit_url_field_value( $value, $key ) {
	$value = esc_attr( $value );
	return $value;
}
add_filter( 'um_edit_url_field_value', 'um_edit_url_field_value', 10, 2 );