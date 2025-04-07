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
		                echo wp_specialchars_decode($mh_code);
	                else if($page_scripts['zmhub_code_loc'] == 'cateogry' && !is_front_page())
	                {
	                    $mh_cat = get_the_category();
	                    if(!empty($mh_cat))
	                    {
		                    foreach($mh_cat as $mh_cateogry) {
			                   if(($mh_cateogry->name == $page_scripts['zmhub_cateogry']))
			                   {
			                   		 echo wp_specialchars_decode($mh_code);
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
	                          echo wp_specialchars_decode($mh_code);
	                        break;
	                    }
	                }
	                foreach ($pagesId as $Id) {
	                 if(($Id == $post_id && !is_front_page())) {
	                         echo wp_specialchars_decode($mh_code);
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
// 		$zmhub_domname = 'com';
// 		if(get_option('zmhub_domname'))
// 		{
// 			$zmhub_domname = get_option('zmhub_domname');
// 		}
// 	    $headarray = array('Authorization' => 'Zoho-oauthtoken '. self::zmhub_get_parsed_val('zmhub_token_details','access_token') );
//     	$query_string = http_build_query(['integrationIdDigest' => self::zmhub_get_parsed_val('zmhub_intergration_details','integration_digest')]);
// 		$url= ZMHUB__HUB_URL. $zmhub_domname . '/api/v2/wp/ecommerce/deny?' . $query_string;
// 		$response = wp_remote_request( $url, array(
// 	    'method'      => 'POST',
// 	    'headers'     => $headarray,
// 	    ) );
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
		    update_option('zmh_plugin_version',"1.3.0");
		}
	}
	private static $initiated = false;
	public static function zmhub_checkout_field_update_order_meta( $order_id ) {
	  $current_user = wp_get_current_user();
	   if($current_user != null)
	   {
        if(isset($_POST['zma_optin_checkbox']) && get_user_meta($current_user->ID, 'zma_newsletter_subscription', true) == '')
         {
            update_user_meta( $current_user->ID, 'zma_newsletter_subscription', true );
         }
           else {
               update_user_meta( $current_user->ID, 'zma_newsletter_subscription', false );
           }
	   }
	 }
	 public static function zmhub_init() {
		if (!self::$initiated) {
			self::zmhub_init_hooks();
		}
	}
	 public static function zmhub_init_hooks() {
 		self::$initiated = true;
 		add_action('zmhub_track_order_event_hook', array('ZohoMarketingHub','zmhub_track_order_event_action'),10,2);
 		/**
 		 * Add opt-in checkbox
 		 **/
 		$hook = ZohoMarketingHub::zmhub_get_parsed_val('zmhub_optin_setting', 'hook');
 		add_action( $hook , array('ZohoMarketingHub','zmhub_custom_checkout_field'), 10, 1);
 		add_action('woocommerce_register_form', array('ZohoMarketingHub','zmhub_custom_registration_field'), 10);
 		add_action('woocommerce_created_customer', array('ZohoMarketingHub','zmhub_checkout_field_update_order_meta') );
    	add_action('woocommerce_checkout_order_processed', array('ZohoMarketingHub','zmhub_checkout_field_update_order_meta') );
 		/**
 		 * Update the order meta with field value
 		 **/
 		add_action('woocommerce_after_cart_totals',array('ZohoMarketingHub','zmhub_checkout_started'));
 		add_action('woocommerce_before_checkout_billing_form',array('ZohoMarketingHub','zmhub_checkout_started'));
 		add_action('woocommerce_cart_item_removed',array('ZohoMarketingHub','zmhub_checkout_started'));
 		add_action('woocommerce_checkout_order_processed',array('ZohoMarketingHub','zmhub_order_placed'),10,1);
 	}

	public static function zmhub_get_product($product_id){
        if(function_exists('wc_get_product')){
            return wc_get_product($product_id);
        }else{
            return get_product($product_id);
        }
    }

	public static function zmhub_checkout_started()
	{
		global $woocommerce;
		$woo = function_exists('WC') ? WC() : $woocommerce;
	  $current_user = wp_get_current_user();
		if (is_user_logged_in())
		{
	    	$user_id = $current_user->ID;
	    	$items = $woo->cart->get_cart();
	    	$product_arr = array();
	        foreach($items as $key ) {
	        	$product = $key['data'];
	            array_push($product_arr,$product->get_id());
	        }
			$a = array('id' => $user_id, 'user_email' => $current_user->user_email, 'total_price' => $woo->cart->total, 'checkout_url' => wc_get_checkout_url(), 'line_items' => $product_arr,'currency' => get_woocommerce_currency());
			$zmhub_domname = 'com';
			$id="";
			$od="";
			if(get_option('zmhub_domname'))
			{
				$zmhub_domname = get_option('zmhub_domname');
			}
			if(get_option('zmhub_intergration_details')!=null)
			{
				$mh_Object = unserialize(get_option('zmhub_intergration_details'));
				if(isset($mh_Object['encrypted_integration_digest']))
				{
				 	$id = $mh_Object['encrypted_integration_digest'];
				}
				if(isset($mh_Object['encrypted_org_digest']))
				{
				 	$od = $mh_Object['encrypted_org_digest'];
				}
			}
	        $zmhub_cart_action = 'cart.updated';
	        if(empty($product_arr))
	        {
	        	$zmhub_cart_action = 'cart.deleted';
	        }
			if(od!="" && id!="")	{
				$headarray = array('Content-type' => 'application/json', 'x-wc-webhook-topic' => $zmhub_cart_action ,'x-wc-webhook-referer' => 'zoho marketing automation plugin', 'x-zohomarketingautomation-plugin-version' => ZMHUB_VERSION);
				$query_string = http_build_query(['id' => $id, 'od' => $od]);
				$hub_url = ZMHUB__HUB_URL;
				if($zmhub_domname=='ca')	{
						$hub_url = ZMHUB__HUB_URL_CA;
				}
				$url= $hub_url. $zmhub_domname .'/ua/ecommercecallback.zc?' . $query_string;
				$response = wp_remote_request( $url, array(
				'method'      => 'POST',
				'body'        => json_encode($a,true),
				'headers'     => $headarray,
				'data_format' => 'body',
				));
			}
	    }
	}

	public static function zmhub_get_parsed_val($key,$value) {
		 $mh_Object = unserialize(get_option($key));
		 if(isset($mh_Object[$value]))
		 {
		 	return $mh_Object[$value];
		 }
		 else return false;
	}
	public static function zmhub_custom_checkout_field($checkout) {
			$checked = self::zmhub_get_parsed_val('zmhub_optin_setting', 'check');
		    if ($checked == 'hidden') {
		      return;
		    }
		    $default_checked = $checked == 'checked';
		    $label = self::zmhub_get_parsed_val('zmhub_optin_setting', 'label');
		    global $woocommerce;
			$woo = function_exists('WC') ? WC() : $woocommerce;
			if(get_option("zmhub_integration") != null && intval(get_option("zmhub_integration")) == 3)	{
				if (is_user_logged_in() ) {
			    $current_user = wp_get_current_user();
		        $status = get_user_meta($current_user->ID, 'zma_newsletter_subscription', true);
		        if ((bool) $status) {
		            $default_checked = true;
		        }
		        	echo '<style> #zma-optin-field span.optional {display:none;}</style>';
		        	echo '<div id="zma-optin-field">';
					woocommerce_form_field( 'zma_optin_checkbox', array(
					'type'  => 'checkbox',
					'label' => esc_html($label),
			        ), $default_checked);
			   		 echo '</div>';
				}
				else if(WC()->checkout()->is_registration_enabled())
				{
					echo '<style> #zma-optin-field span.optional {display:none;}</style>';
					echo '<div id="zma-optin-field">';
						woocommerce_form_field( 'zma_optin_checkbox', array(
						'type'  => 'checkbox',
						'label' => esc_html($label),
						), $default_checked);
						 echo '</div>';
				}
			}
	}
	public static function zmhub_custom_registration_field() {
			if(get_option("zmhub_integration") != null && intval(get_option("zmhub_integration")) == 3) {
		    $checked = self::zmhub_get_parsed_val('zmhub_optin_setting', 'check');
		    $default_checked = $checked == 'checked';
		    $label = self::zmhub_get_parsed_val('zmhub_optin_setting', 'label');
		    if ($checked == 'hidden') {
		      return;
		    }
	        else
	        {
	        	echo '<style> #zma-optin-field span.optional{display:none;}</style>';
	        	echo '<div id="zma-optin-field">';
				woocommerce_form_field( 'zma_optin_checkbox', array(
				'type'  => 'checkbox',
				'label' => esc_html($label),
		        ), $default_checked);
		   		 echo '</div>';
	        }
 		}
	}

	public static function zmhub_track_order_event_action($order_id,$recurrence,$zc_rid)
	{
		$logger = new WC_Logger();
				$logger->add('zmhub_track_order_event_action_logger_order_id', $order_id);
		$zmhub_domname = 'com';
		if(get_option('zmhub_domname'))
		{
			$zmhub_domname = get_option('zmhub_domname');
		}
		$hub_url = ZMHUB__HUB_URL;
		if($zmhub_domname=='ca')	{
				$hub_url = ZMHUB__HUB_URL_CA;
		}
			if($recurrence < 2)
			{
				$query_string = http_build_query(['service' => 'WooCommerce', 'zc_rid' => $zc_rid ,'order_id' => $order_id]);
			$url= $hub_url. $zmhub_domname . '/ua/ecommercetracking.zc?' . $query_string;
			$logger->add('zmhub_track_order_event_action_url', $url);
			$response = wp_remote_get($url);
				$logger->add('zmhub_track_order_event_action', $response);
				if( wp_remote_retrieve_response_code( $response ) != 200 && $recurrence < 1) {
					wp_schedule_single_event( time() + 720, 'zmhub_track_order_event_hook' , array($order_id,$recurrence + 1,$zc_rid));
				}
		}
	}
	public static function zmhub_order_placed($order_id)
	{
		if(isset($_COOKIE["zc_rid"]) && $order_id){
			$zmhub_domname = 'com';
			if(get_option('zmhub_domname'))
			{
				$zmhub_domname = get_option('zmhub_domname');
			}
			$query_string = http_build_query(['service' => 'WooCommerce', 'zc_rid' => $_COOKIE["zc_rid"] ,'order_id' => $order_id]);
			$hub_url = ZMHUB__HUB_URL;
			if($zmhub_domname=='ca')	{
					$hub_url = ZMHUB__HUB_URL_CA;
			}
			$url= $hub_url. $zmhub_domname . '/ua/ecommercetracking.zc?' . $query_string;
			$response = wp_remote_get($url);
				if( wp_remote_retrieve_response_code( $response ) != 200 ) {
					wp_schedule_single_event( time() + 120, 'zmhub_track_order_event_hook' , array($order_id, 0, $_COOKIE["zc_rid"]));
				}
		}
	}

}
?>
