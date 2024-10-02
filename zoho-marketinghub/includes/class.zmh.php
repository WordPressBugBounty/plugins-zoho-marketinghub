<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ZohoMarketingHub {

	public static function zmhub_form_sc($attr) {
	  if( isset( $attr['id'] ) ) {
	    $sc_id = $attr['id'] ;
	    return self::zmhub_form_post($sc_id);
		}
	}

	public static function zmhub_find_footer_tracking_codes() {
	   global $wp_query;
	   if(get_option('zmhub_script_setting') && !is_admin()){
	        $page_scripts = unserialize(get_option('zmhub_script_setting'));
	        $mh_code = trim(get_option('zmhub_script'));
	        $flag = '1';
	        if(intval($page_scripts['zmhub_status'])== 1)
	        {
		        if(isset($page_scripts['zmhub_date']) || is_front_page())
		        {
		        	if(isset($page_scripts['zmhub_date']))
		            {
		            	$timestamp = $page_scripts['zmhub_date'] + 86400;
		            	if(get_post_time('U','false') < $timestamp)
		                $flag = '0';
		       		}
		        }
		        if($flag =='1' && $page_scripts['zmhub_code_loc'] != 'specific')
		        {
		            if($page_scripts['zmhub_code_loc'] == 'global')
		                echo stripslashes($mh_code);
	                else if($page_scripts['zmhub_code_loc'] == 'cateogry' && !is_front_page())
	                {
	                    $mh_cat = get_the_category();
	                    if(!empty($mh_cat))
	                    {
		                    foreach($mh_cat as $mh_cateogry) {
			                   if(($mh_cateogry->name == $page_scripts['zmhub_cateogry']))
			                   {
			                   		echo stripslashes($mh_code);
			                   		break;
			                   }
	                        }
		               	}
	                }
		        }
	            else if($wp_query->have_posts() && $page_scripts['zmhub_code_loc'] == 'specific')
	            {
	                 $post_id = $wp_query->post->ID;
	                 $pagesId = explode(",", $page_scripts['zmhub_pagevalue']);
	                 $postsId = explode(",", $page_scripts['zmhub_postvalue']);
	                foreach($postsId as $Id) {
	                 if(($Id == $post_id && !is_front_page())) {
	                         echo stripslashes($mh_code);
	                        break;
	                    }
	                }
	                foreach ($pagesId as $Id) {
	                 if(($Id == $post_id && !is_front_page())) {
	                        echo stripslashes($mh_code);
	                        break;
	                    }
	                }
	            }
	        }
	    }
    }

	public static function zmhub_plugin_activation()
	{
		ZohoMarketingHub_Admin::zmhub_create_mhforms_table();
		wp_schedule_event( time(), 'daily','zmhub_refresh_forms_event' );
	}

	public static function zmhub_plugin_deactivation()
	{
		wp_clear_scheduled_hook( 'zmhub_refresh_forms_event' );
	}

	public static function zmhub_form_post($id)
	{
		if (filter_var($id, FILTER_VALIDATE_INT)!== false) {
			global $wpdb, $table_prefix;
		    $tblname = $table_prefix . 'zmhub_forms';
			$sql = "SELECT * FROM $tblname WHERE id = $id and status = 2";
			$zmh_form = $wpdb->get_row($sql,ARRAY_A);
			if($zmh_form){
				$response = wp_remote_get($zmh_form['url']);
				if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 || isset($response_body['error']) )
					return false;
				else if(strpos($response['body'], 'signupFormContainer') != false)
				{
					return str_replace("absolute", "" , str_replace("fixed", "", $response['body']));
				}
				else return false;
			}
		}
	}
	public static function zmh_plugin_version() {

		if (get_option('zmh_plugin_version') == "")
		{
			ZohoMarketingHub_Admin::zmh_remove_duplicate();
		    ZohoMarketingHub_Admin::zmhub_create_mhforms_table();
		    update_option('zmh_plugin_version',"1.2.5");
		}
	}

}
?>
