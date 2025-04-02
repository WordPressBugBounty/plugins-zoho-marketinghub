<?php
ob_start();
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'ZMHUB__CLIENT_ID', '1000.B687AAL9TV0P283904RCPJXR3A6XWN' );
define( 'ZMHUB__CLIENT_SECRET_COM', '11ef879e247b9d16fb2a6a08ff2e308be1a150900a' );
define( 'ZMHUB__CLIENT_SECRET_EU', 'b39cf89204fe48f45d54f78e1e9fc4afb24fa6981e' );
define( 'ZMHUB__CLIENT_SECRET_IN', 'b599306aad286c1904281065e5f0924da38e211d1c' );
define( 'ZMHUB__CLIENT_SECRET_AU', 'a201e9a7e67f4e214ef7d67ab2a3f9e9523b49ed78' );
define( 'ZMHUB__CLIENT_SECRET_JP', 'f90c8e7d2ad6bb6eb9401f53b6d584cf20c3827699' );
define( 'ZMHUB__CLIENT_SECRET_CA', 'eaf9d86a4597f3ea6ac79d631c18378ebfa99bb11b');


class ZohoMarketingHub_Admin {

	const DELETED = 0;
	const NOT_USED = 1;
	const VISIBLE = 2;
	const INVISIBLE = 3;

	private static $initiated = false;

	public static function zmhub_init() {
		if (!self::$initiated) {
			self::zmhub_init_hooks();
		}
	}

	public static function zmhub_init_hooks() {
		self::$initiated = true;

		add_action( 'admin_menu', array( 'ZohoMarketingHub_Admin', 'zmhub_admin_menu' ));
		add_action( 'admin_enqueue_scripts', array( 'ZohoMarketingHub_Admin', 'zmhub_load_resources' ) );

		// Admin-Post
		add_action( 'admin_post_zmhub_save_settings', array( 'ZohoMarketingHub_Admin', 'zmhub_save_settings' ));
		add_action('admin_init', array( 'ZohoMarketingHub_Admin', 'zmhub_register_settings' ));
		add_action( 'current_screen', array( 'ZohoMarketingHub_Admin', 'zmhub_admin_texts' ) );

		// Ajax-Post
		add_action( 'wp_ajax_zmhub_connect', array( 'ZohoMarketingHub_Admin', 'zmhub_connect' ));
		add_action( 'wp_ajax_zmhub_disconnect', array( 'ZohoMarketingHub_Admin', 'zmhub_disconnect' ));
		add_action( 'wp_ajax_zmhub_fetch_form', array( 'ZohoMarketingHub_Admin', 'zmhub_fetch_form' ));
		add_action( 'wp_ajax_zmhub_fetch_webcode', array( 'ZohoMarketingHub_Admin', 'zmhub_fetch_webcode' ));
		add_action( 'wp_ajax_zmhub_change_form_status', array( 'ZohoMarketingHub_Admin', 'zmhub_change_form_status' ) );
		add_action( 'wp_ajax_zmhub_refresh_forms_list', array( 'ZohoMarketingHub_Admin', 'zmhub_refresh_forms_list' ) );
		add_action( 'wp_ajax_zmhub_get_short_code', array( 'ZohoMarketingHub_Admin', 'zmhub_get_short_code' ) );
		add_action( 'wp_ajax_zoho_marketinghub_rated', array( 'ZohoMarketingHub_Admin', 'zoho_marketinghub_rated' ) );
		add_action( 'wp_ajax_zma_update_notice', array( 'ZohoMarketingHub_Admin', 'zma_update_notice' ) );
		add_action( 'wp_ajax_zmhub_woocommerce_authorize', array( 'ZohoMarketingHub_Admin', 'zmhub_woocommerce_authorize' ));
		add_action( 'wp_ajax_zmhub_add_list', array( 'ZohoMarketingHub_Admin', 'zmhub_add_list' ));
		add_action( 'wp_ajax_zmhub_integration_status', array( 'ZohoMarketingHub_Admin', 'zmhub_integration_status' ));
		add_action( 'wp_ajax_zmhub_get_list', array( 'ZohoMarketingHub_Admin', 'zmhub_integration_status' ));
		add_action( 'wp_ajax_zmhub_integration_disconnect', array( 'ZohoMarketingHub_Admin', 'zmhub_integration_disconnect' ));
		add_action( 'wp_ajax_zmhub_optin_save', array( 'ZohoMarketingHub_Admin', 'zmhub_optin_save' ));
		add_action('wp_ajax_zmhub_tracking_migrate',array('ZohoMarketingHub_Admin' , 'zmhub_tracking_migrate'));
	}

	public static function zmhub_admin_texts() {
		add_filter( 'admin_footer_text', array( 'ZohoMarketingHub_Admin', 'zmhub_footer_text' ) );
	}

	public static function zmhub_footer_text( $text ) {
		if ( ! current_user_can( 'manage_options' ) || ! get_option('zmhub_connect_time') ) {
			return $text;
		}
		if(! empty( $_GET['page'] ) &&  (strpos( $_GET['page'], 'mh-start' )  === 0 || strpos( $_GET['page'], 'mh-wa' )  === 0 || strpos( $_GET['page'], 'mh-forms' ) === 0  || strpos( $_GET['page'], 'mh-wc' ) === 0) )    {

			if(!get_option("zmhub_rated"))
			{
				$text = sprintf( 'If you enjoy using <strong>Zoho Marketing Automation</strong>, please <a href="%s" target="_blank" class="zmhub-rating-link" >leave us a ★★★★★ rating</a>. A huge thanks in advance!', 'https://wordpress.org/support/plugin/zoho-marketinghub/reviews/?rate=5#new-post' );
			}
			else
			 $text = sprintf( 'Thanks for using <a href="%s" target="_blank"> Zoho Marketing Automation</a>.', 'https://wordpress.org/plugins/zoho-marketinghub' );
		}
		return $text;
	}
	public static function zma_update_notice() {
			update_option('zma_notice',true);
	}
	public static function zoho_marketinghub_rated() {
		if(current_user_can('manage_options') && check_ajax_referer( 'mh-ajax-nonce', 'security' ))
		{
			update_option('zmhub_rated',true);
		}
	}
	public static function zmhub_register_settings() {
    	register_setting('zmhub_settings', 'zmhub_script', 'trim');
    	register_setting('zmhub_settings', 'zmhub_script_setting', 'trim');
	}

	public static function zmhub_return_constant_val($start,$end)
	{
		 if($start == 'CLIENT_SECRET')
		 {
		 	switch ($end) {
	 			case 'com':
	 			return ZMHUB__CLIENT_SECRET_COM;
	 			break;

	 			case 'eu':
	 			return ZMHUB__CLIENT_SECRET_EU;
	 			break;

	 			case 'in':
	 			return ZMHUB__CLIENT_SECRET_IN;
	 			break;

	 			case 'com.au':
	 			return ZMHUB__CLIENT_SECRET_AU;
	 			break;

				case 'jp':
	 			return ZMHUB__CLIENT_SECRET_JP;
	 			break;

				case 'ca':
				return ZMHUB__CLIENT_SECRET_CA;
				break;
		 	}
		 }
	}

	public static function zmhub_connect() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
	   	$response_body ='';
		$auth_url = "https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id=" . ZMHUB__CLIENT_ID . "&scope=AaaServer.profile.READ,ZohoMarketingAutomation.lead.READ,ZohoMarketingAutomation.wa.READ&redirect_uri=https://mh.zoho.com/ua/wpredirect&prompt=consent&access_type=offline&state=" .get_admin_url('') ."admin.php?page=mh-start";
		echo $auth_url;
	}

	public static function zmhub_disconnect() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
	    ZohoMarketingHub_Admin::zmhub_remove_account();
	}
	public static function zmhub_tracking_migrate()	{
		//fetch tacking script
		update_option("zma_custom_banner_shown",true);
		ZohoMarketingHub_Admin:: zmhub_fetch_webcode();
	}
	public static function zmhub_fetch_form() {

		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
        ZohoMarketingHub_Admin:: zmhub_construct_fetch_zmhub_forms();
        wp_die();
	}

	public static function zmhub_fetch_webcode() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
        $response =  ZohoMarketingHub_Admin:: zmhub_fetch_web_code();
        if($response != '')
        {
           if($response['code'] == 7701)
            {
                echo esc_html("You have not connected your domain. The website analytics tracking code will be generated only after you connect your domain.");
            }
            else if($response['code'] == 901)
            {
                echo esc_html("No org exists for the user.");
            }
            else if(isset($response['code']) && $response['code'] != 200)
			{
				echo esc_html("An internal error occured while processing your request, Please try again in some time.");
			}
            else
            {
            	if($response['domain_status'] =="Deleted")
			    {
			    	echo esc_html("You have not connected your domain. The website analytics tracking code will be generated only after you connect your domain.");
			    }
			    else{
	                 $mh_script = $response['tracking_script'];
	                 update_option('zmhub_script',stripslashes($mh_script),false);
									 update_option('zma_custom_banner_shown', true);
             	}

            }
        }
        wp_die();
	}

	// Validate & Sanatize Website Analytics Settings Before Save.
	public static function zmhub_save_settings() {

		if(isset($_POST['ldsubmit']) && $_POST['ldsubmit'] == 'save' ){
		    if(isset( $_POST['mhnonce'] ) && wp_verify_nonce($_POST['mhnonce'], 'save_wa') && current_user_can( 'manage_options' )){
		        $page_scripts = array();
		        $mh_check =1; // Set this to zero if any validation fails.
		        //This atrtribute value changes to 1 for any value other than 0.
		        $page_scripts['zmhub_status'] = intval($_POST['zmhub_status']) ? 1 :0 ;
		        if($_POST['zmhub_code_loc'] == 'cateogry') {
	        		$page_scripts['zmhub_code_loc'] = 'cateogry';

	        		// Check If this Cateogry Exist.
	        		$mh_term = term_exists( sanitize_text_field($_POST['zmhub_cateogry']), 'category' );
	        		if ( $mh_term !== 0 && $mh_term !== null ) {
		                $page_scripts['zmhub_cateogry'] = sanitize_text_field($_POST['zmhub_cateogry']);
		                if($_POST['datepicker'])
		                {
		                	if(strtotime(str_replace('/', '-', $_POST['datepicker'])) != false)
		                	{
		                		$timeStamp = strtotime(str_replace('/', '-', $_POST['datepicker']));
			                	if($timeStamp < 1041379200 || $timeStamp > time() )
			                		$mh_check = 0;
			                	else
			                	{
			                		$page_scripts['zmhub_date'] = strtotime(get_date_from_gmt(gmtdate('Y-m-d H:i:s' , $timeStamp), get_option('date_format') .' '. get_option('time_format') ));
			                	}
		                	}
		                	else $mh_check = 0;
		                }
	            	}
	            	else $mh_check = 0;
	            }
		        else if($_POST['zmhub_code_loc'] == 'specific'){
	                 $page_scripts['zmhub_code_loc'] = 'specific';

	                //Validate the valid pages & posts
	                if($_POST['zmhub_pagevalue'] != '' && !self::zmhub_validatePostArray(explode(",", $_POST['zmhub_pagevalue'])))
	                	$mh_check = 0;
	                if($_POST['zmhub_postvalue'] != '' && !self::zmhub_validatePostArray(explode(",", $_POST['zmhub_postvalue'])))
	                	$mh_check = 0;
	                $page_scripts['zmhub_pagevalue'] = sanitize_text_field($_POST['zmhub_pagevalue']);
	                $page_scripts['zmhub_postvalue'] = sanitize_text_field($_POST['zmhub_postvalue']);
	            }
	            else {
	            		//Assign 'global' if the value is not in {'cateogry' , 'specific'}.
		                $page_scripts['zmhub_code_loc'] = 'global';
		                if($_POST['datepicker'])
		                {
		                	if(strtotime(str_replace('/', '-', $_POST['datepicker'])) != false)
		                	{
		                		$timeStamp = strtotime(str_replace('/', '-', $_POST['datepicker']));
			                	if($timeStamp < 1041379200 || $timeStamp > 1609372800 )
			                		$mh_check = 0;
			                	else
			                	{
			                		$page_scripts['zmhub_date'] = strtotime(get_date_from_gmt(gmtdate('Y-m-d H:i:s' , $timeStamp), get_option('date_format') .' '. get_option('time_format') ));
			                	}
		                	}
		                	else $mh_check = 0;
		                }
	            }
		        if($mh_check == 1)
		        {
		        	$res = update_option('zmhub_script_setting', serialize($page_scripts),false);
		    		wp_safe_redirect(get_admin_url() .'admin.php?page=mh-wa&saved=true');
		        }
		        else{
		        	wp_safe_redirect(get_admin_url() .'admin.php?page=mh-wa&saved=false');
		        }
		       	exit;
		    }
	 	}
	}

	public static function zmhub_validatePostArray($postArray){
	 	foreach ($postArray as $Id) {
	 		if( 'publish' != get_post_status($Id))
	 		return false;
	 	}
		return true;
	}

    // Change Form Status
	public static function zmhub_change_form_status() {

		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		global $wpdb;
		$table = $wpdb->prefix . 'zmhub_forms';
	    $id = intval( $_POST['id'] );
	    if (filter_var($id, FILTER_VALIDATE_INT)!== false) {
	    $form_data = $wpdb->get_row( "SELECT * FROM " .$table. " WHERE id =" .$id );
	    if(!is_null($form_data))
	    {
	    	$form_status = $form_data->status;
	    	if($form_status == self::VISIBLE ||  $form_status == self::INVISIBLE)
	    	{
	    		$form_status = ($form_status == self::INVISIBLE ? self::VISIBLE : self::INVISIBLE);
	    		$res = $wpdb->update( $table, array( 'status' => $form_status ), array( 'id' => $id ), array( '%d' ), array( '%d' ) );
	    		echo esc_html("successful");
	    	}
	    	else echo esc_html("unsuccessful");
	    }
	    else
	    {
	    	echo esc_html("unsuccessful");
	    }
	 }
		wp_die();
	}

	// Get the Website Analytics code for the site domain.

	public static function  zmhub_fetch_web_code() {

		// TODO MULTISITE
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		$zmhub_domname = 'com';
		if(get_option('zmhub_domname'))
		{
			$zmhub_domname = get_option('zmhub_domname');
		}
		$response_body ='';
		$mh_get_params = array(
					'domain' => get_site_url(),
				);
		$headarray = array('Authorization' => 'Zoho-oauthtoken '. self::zmhub_get_parsed_val('zmhub_token_details','access_token') );
		$hub_url = ZMHUB__HUB_URL;
		if($zmhub_domname=='ca')	{
				$hub_url = ZMHUB__HUB_URL_CA;
		}
		$auth_url = $hub_url. $zmhub_domname .  '/api/v2/domains/info';
		$response = wp_remote_get( $auth_url, array(
	    'method'      => 'GET',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => $mh_get_params,
	    'headers'     => $headarray
	    ) );
	    if ( is_wp_error( $response ) ) {

		    $error_message = $response->get_error_message();
		    echo esc_html($error_message);
		}
		else if( wp_remote_retrieve_response_code( $response ) != 200 ) {
			echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
		}
		else if(isset($response['error']))
		{
			echo esc_html($response['error']);
		}
		else {
		    $response_body = json_decode($response['body'],true);
		}
	    return $response_body;
	}

	public static function zmhub_insert_record( $zmh_tbl_name, $data, &$allforms ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		global $wpdb;
		if(!empty($allforms) && array_search($data['id'], $allforms) !== false)
		{
			$newdata = array(
          	'form_name' => sanitize_text_field($data['name']),
          	'form_type' => sanitize_text_field($data['type']),
          	'list_name' => sanitize_text_field($data['list_name']),
			);
			$res = $wpdb->update($zmh_tbl_name,$newdata, array( 'form_id' => sanitize_text_field($data['id']) ), array( '%s' ,'%s' , '%s'), array( '%s' ) );
			unset($allforms[array_search(sanitize_text_field($data['id']), $allforms)]);
		}
		else
		{
			$newdata = array(
			'form_id' => sanitize_text_field($data['id']),
          	'form_name' => sanitize_text_field($data['name']),
          	'form_type' => sanitize_text_field($data['type']),
          	'list_name' => sanitize_text_field($data['list_name']),
          	'url' => esc_url_raw($data['url']),
          	'created_time' => sanitize_text_field($data['created_time']),
			);
			$wpdb->insert($zmh_tbl_name,$newdata );
		}
	}

	public static function zmhub_construct_fetch_zmhub_forms()
	{
		self::zmhub_fetch_zmhub_forms(1,25,0);
		wp_die();
	}

	public static function zmhub_fetch_zmhub_forms($from, $count, $total) {

		global $wpdb, $table_prefix;
		$tblname = $table_prefix . 'zmhub_forms';
		$sql = "SELECT form_id FROM $tblname";
		$allforms = $wpdb->get_col($sql);
		self::zmhub_fetch_zmhub_forms_update($from, $count, $total, $allforms);
		foreach ($allforms as $singleform) {
	 		$wpdb->update( $tblname, array( 'status' => 0 ), array( 'form_id' => $singleform ), array( '%d' ), array( '%s' ) );
		}
	}

	public static function zmhub_fetch_zmhub_forms_update($from, $count, $total, &$allforms) {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		$zmhub_domname = 'com';
		if(get_option('zmhub_domname'))
		{
			$zmhub_domname = get_option('zmhub_domname');
		}
		$response_body ='';
		$details = json_encode(array("from" => $from, "count" => $count, "version" => 2));
		$headarray = array('Authorization' => 'Zoho-oauthtoken '. self::zmhub_get_parsed_val('zmhub_token_details','access_token'), 'Content-Type' => 'application/json; charset=utf-8' );
		$hub_url = ZMHUB__HUB_URL;
		if($zmhub_domname=='ca')	{
				$hub_url = ZMHUB__HUB_URL_CA;
		}
		$auth_url = $hub_url. $zmhub_domname .  '/api/v2/forms?details=' . $details;
		$response = wp_remote_get( $auth_url, array(
	    'method'      => 'GET',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => NULL,
	    'headers'     => $headarray,
	    ) );
	    $response_body = json_decode($response['body'],true);
	    if ( is_wp_error( $response ) ) {

		    $error_message = $response->get_error_message();
		    echo esc_html($error_message);
		}
		else if( wp_remote_retrieve_response_code( $response ) != 200 ) {
			echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
		}
		else if(isset($response_body['code']) && $response_body['code'] == 901)
        {
            echo esc_html("No org exists for the user.");
        }
		else if(isset($response_body['code']) && $response_body['code'] != 200)
		{
			echo esc_html("An internal error occured while processing your request, Please try again in some time.");
		}
		else {
		    if(!empty($response_body['forms']))
		    {
		    	global $table_prefix;
		    	$tblname = $table_prefix . 'zmhub_forms';
			    foreach ($response_body['forms'] as $singleform) {
			   		self::zmhub_insert_record($tblname, $singleform, $allforms);
				}
				if($total == 0)
				{
					$total = $response_body['count'];
				}
				$total -= 26;
				if($total > 0)
				{
					$from += 26;
					return self::zmhub_fetch_zmhub_forms_update($from, $count, $total, $allforms);
				}
			}
			else if(intval($response_body['count']) == 0)
			{
				echo ("Oops! Looks like you don't have any signup form in your account. Create one in Marketing Automation to embed it in your website.");
			}
		}
	}

	public static function zmhub_get_short_code()
	{

		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		global $wpdb;
		$table = $wpdb->prefix . 'zmhub_forms';
	    $id = intval( $_POST['id'] );
	if (filter_var($id, FILTER_VALIDATE_INT)!== false) {
	    $form_data = $wpdb->get_row( "SELECT * FROM " .$table. " WHERE id =" .$id );
	    if(!is_null($form_data))
	    {
    		$res = $wpdb->update( $table, array( 'status' => self::VISIBLE ), array( 'id' => $id ), array( '%d' ), array( '%d' ) );
    		if($res)
    			echo esc_html("successful");
    		else
    			echo esc_html("unsuccessful");
	    }
	    else
	    {
	    	echo esc_html("unsuccessful");
	    }
	}
		wp_die();
	}

	public static function zmhub_refresh_forms_event_hook()
	{
		if(get_option('zmhub_connect_time'))
		self::zmhub_construct_fetch_zmhub_forms();
	}

	public static function zmhub_refresh_forms_list()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		global $wpdb, $table_prefix;
		$tblname = $table_prefix . 'zmhub_forms';
		self:: zmhub_construct_fetch_zmhub_forms();
	}

	public static function zmhub_admin_menu() {
	 	add_menu_page( 'Zoho Marketing Automation', 'Zoho Marketing Automation', 'manage_options', 'mh-start', array( 'ZohoMarketingHub_Admin', 'zmhub_display_page' ), 'data:image/svg+xml;base64,'.self::zmhub_icon_svg());
	 	add_submenu_page('mh-start','Website-Analytics','Website Analytics','manage_options','mh-wa',array( 'ZohoMarketingHub_Admin', 'zmhub_wa_page' ));
		add_submenu_page('mh-start','SignUp-Forms','Signup Forms','manage_options','mh-forms',array( 'ZohoMarketingHub_Admin', 'zmhub_form_page' ));
		add_submenu_page('mh-start','eCommerce','eCommerce','manage_options','mh-wc',array( 'ZohoMarketingHub_Admin', 'zmhub_ecommerce_page' ));
	}

	protected static function zmhub_icon_svg()
    	{
       return base64_encode('<?xml version="1.0" encoding="UTF-8"?>
			 <svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 530 477">
			   <!-- Generator: Adobe Illustrator 29.2.0, SVG Export Plug-In . SVG Version: 2.1.0 Build 108)  -->
			   <defs>
			     <style>
			       .st0 {
			         fill: #fff;
			       }
			     </style>
			   </defs>
			   <path class="st0" d="M49.9,177.4c-5.1,0-9.8-2.5-12.2-7.3l-10.5-18.1c-8-13.4-8-29.6,0-43,8-13.4,21.7-21.3,37.1-21.3h324c8,0,14.4,6.4,14.4,14.4s-6.4,14.4-14.4,14.4H64.5c-7.6,0-11.2,5.1-12.2,7.3s-3.6,8,0,14.4l10.5,18.1c3.9,6.8,1.5,15.6-5.4,19.5-2.2,1-4.7,1.5-7.3,1.5l-.2.2Z"/>
			   <path class="st0" d="M232.7,441.2c-24.2,0-45.9-12.7-57.8-33.5l-97.7-169.5c-8-13.4-8-29.6,0-43s21.7-21.3,37.1-21.3h224.8c8,0,14.4,6.4,14.4,14.4s-6.4,14.4-14.4,14.4H113.8c-7.6,0-11.2,5.1-12.2,7.3s-3.6,8,0,14.4l97.7,169.5c6.8,11.9,19.1,19.1,33.2,19.1s25.9-7.3,33.2-19.1l43-74.4c3.9-6.8,12.7-9.3,19.5-5.4,6.8,3.9,9.3,12.7,5.4,19.5l-43,74.4c-13,20.5-34.7,33.2-58.1,33.2l.2.2Z"/>
			   <path class="st0" d="M508.7,98.4l-14.4-53.4h0s0-.3-.3-.7h0s0-.3-.3-.3h0s0-.3-.3-.3h0s-.3-.3-.3-.7h0c-1-2.2-2.5-3.9-4.4-5.4h0s-.3-.3-.7-.3h-.3l-.3-.3-.3-.3h0c-2.5-1.5-5.4-1.9-8-1.5h-2.2l-53.7,14.4c-7.6,2.2-12.2,9.8-10.2,17.6,2.2,7.6,9.8,12.2,17.6,10.2l20.5-5.4-97.7,168.9c-6.8,11.9-19.5,19.1-33.2,19.1h-115.8c-8,0-14.4,6.4-14.4,14.4s6.4,14.4,14.4,14.4h116.2c23.9,0,45.9-12.7,57.8-33.5l97.7-168.9,5.4,20.2c1.9,6.4,7.6,10.5,13.7,10.5s2.5,0,3.6-.3c7.6-2.5,12.2-10.2,10.2-18.1h0Z"/>
			 </svg>');
    	}

	public static function zmhub_display_page() {

		if (self::zmhub_get_access_token() )
			self::zmhub_view( 'start' );
		else if(isset($_GET['code']))
			self::zmhub_display_configuration_page();
		else
			self::zmhub_view( 'conf' );
	}

	public static function zmhub_pre_configuration_page() {
		$zmhub_domname = 'com';
		if(get_option('zmhub_domname'))
		{
			$zmhub_domname = get_option('zmhub_domname');
		}
		$auth_params = array(
					'client_id' => ZMHUB__CLIENT_ID,
					'response_type' => 'code',
					'scope' => 'ZohoAutomation.lead.READ,ZohoAutomation.wa.READ',
					'redirect_uri'    => 'https://mh.zoho.com/ua/wpredirect',
					'prompt'          => 'consent',
					'access_type'     => 'offline',
					'state'          => esc_url(get_admin_url() .'admin.php?page=mh-start'),
				);
		$account_url = ZMHUB__ACCOUNTS_URL;
		if($zmhub_domname=='ca')	{
				$account_url = ZMHUB__ACCOUNTS_URL_CA;
		}
		$auth_url = esc_url($account_url. $zmhub_domname . '/oauth/v2/auth');
		$response = wp_remote_post( $auth_url, array(
	    'method'      => 'POST',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => $auth_params
	    ) );
	}

	public static function zmhub_display_configuration_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		$zmhub_domname = 'com';
		if(isset($_GET['location']))
		{
			$loc = $_GET['location'];
			if($loc == 'in' || $loc == 'eu' || $loc == 'jp')
				$zmhub_domname = $loc;
			else if($loc == 'au')
				$zmhub_domname = 'com.au';
			else if($loc=='ca')
				$zmhub_domname = 'ca';
		}
		update_option("zmhub_domname",$zmhub_domname);
		$req_time = time();
		$auth_params = array(
					'client_id' => ZMHUB__CLIENT_ID,
					'grant_type'     => 'authorization_code',
					'client_secret'     => self::zmhub_return_constant_val('CLIENT_SECRET', $zmhub_domname),
					'code'          => $_GET['code'],
					'redirect_uri'    => esc_url('https://mh.zoho.com/ua/wpredirect'),
				);
		$account_url = ZMHUB__ACCOUNTS_URL;
		if($zmhub_domname=='ca')	{
				$account_url = ZMHUB__ACCOUNTS_URL_CA;
		}
		$auth_url = esc_url($account_url. $zmhub_domname. '/oauth/v2/token');
		$response = wp_remote_post( $auth_url, array(
	    'method'      => 'POST',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => $auth_params
	    ) );
	    $response_body = json_decode($response['body'],true);
		if ( is_wp_error( $response ) ) {

		    $error_message = $response->get_error_message();
		    echo esc_html($error_message);
		}
		else if( wp_remote_retrieve_response_code( $response ) != 200 ) {
			echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
		}
		else if(isset($response_body['error']))
		{
			echo esc_html($response_body['error']);
		}
		else {
		    $response_body['req_time'] = $req_time;
		    update_option('zmhub_token_details', serialize($response_body),false);
		    update_option('zmhub_connect_time',$req_time,false);
		    self::zmhub_updateUserDetails();
		}
		wp_safe_redirect(esc_url(admin_url() ."?page=mh-start"));
		exit;
	}

	// Save user details after acoount is connected.
	public static function zmhub_updateUserDetails()
	{
		$zmhub_domname = 'com';
		if(get_option('zmhub_domname'))
		{
			$zmhub_domname = get_option('zmhub_domname');
		}
		$headarray = array('Authorization' => 'Zoho-oauthtoken '. self::zmhub_get_parsed_val('zmhub_token_details','access_token'));
		$account_url = ZMHUB__ACCOUNTS_URL;
		if($zmhub_domname=='ca')	{
				$account_url = ZMHUB__ACCOUNTS_URL_CA;
		}
		$auth_url = esc_url($account_url. $zmhub_domname. '/oauth/user/info');
		$response = wp_remote_get( $auth_url, array(
	    'method'      => 'GET',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => NULL,
	    'headers'     => $headarray,
	    ) );
	    if ( is_wp_error( $response ) ) {

		    $error_message = $response->get_error_message();
		    echo esc_html($error_message);
		}
		else if( wp_remote_retrieve_response_code( $response ) != 200 ) {
			echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
		}
		else if($response)
		{
			 $response_body = json_decode($response['body'],true);
			 update_option('zmhub_user_email',sanitize_email($response_body['Email']),true);
		}
	}

	// Include Required php file
	public static function zmhub_view( $name, array $args = array() ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		foreach ( $args AS $key => $val ) {
			$$key = $val;
		}
		$file = ZMHUB__PLUGIN_DIR . 'includes/'. $name . '.php';
		include( $file );
	}

	//Load styles and scripts maintaining version number.
	public static function zmhub_load_resources($hook) {
		$zmh_pages = array( 'toplevel_page_mh-start', 'zoho-marketing-automation_page_mh-wa', 'zoho-marketing-automation_page_mh-forms', 'zoho-marketing-automation_page_mh-wc' );
		wp_enqueue_script( 'mh_common_js', plugin_dir_url(  __FILE__ ) . '../../assets/js/mh-common.js', array('jquery'), ZMHUB_VERSION  );
		if(!in_array($hook, $zmh_pages)) {
                return;
        	}
			wp_register_style( 'mh-admin.css', plugin_dir_url( __FILE__ ) . '../../assets/css/mhadmin.css', array(), ZMHUB_VERSION );
			wp_enqueue_style( 'mh-admin.css');

		    wp_register_style('zcstyle', plugin_dir_url(  __FILE__ ) . '../../assets/css/old/style.css', array(), ZMHUB_VERSION );
		    wp_enqueue_style('zcstyle');

		    wp_register_style('zcfonts', plugin_dir_url(  __FILE__ ) . '../../assets/css/old/zcfonts.css', array(), ZMHUB_VERSION );
		    wp_enqueue_style('zcfonts');

		    wp_enqueue_script( 'jquery');
		    wp_enqueue_script( 'jquery-ui-datepicker');

		    wp_enqueue_script( 'mh_functions_js', plugin_dir_url(  __FILE__ ) . '../../assets/js/mh-functions.js', array('jquery'), ZMHUB_VERSION  );
		    wp_enqueue_script( 'mh_onload_js', plugin_dir_url(  __FILE__ ) . '../../assets/js/mh-onload.js', array('jquery'), ZMHUB_VERSION  );


  			wp_register_style( 'mh-jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/Redmond/jquery-ui.css' );
    		wp_enqueue_style( 'mh-jquery-ui' );
	}

	protected static function zmhub_get_parsed_val($key,$value) {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		 $mh_Object = unserialize(get_option($key));
		 if(isset($mh_Object[$value]))
		 {
		 	return $mh_Object[$value];
		 }
		 else return false;
	}

	protected static function zmhub_get_access_token() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		$mh_aceess_token = self::zmhub_get_parsed_val('zmhub_token_details','access_token');
		if ( $mh_aceess_token ) {
			if((time() - self::zmhub_get_parsed_val('zmhub_token_details','req_time')) >= 3600)
			{
				return self::zmhub_update_token(self::zmhub_get_parsed_val('zmhub_token_details','refresh_token'));
			}
			else
			return $mh_aceess_token;
		}
		else return false;
	}

	protected static function zmhub_update_token($ref_token) {
		$zmhub_domname = 'com';
		if(get_option('zmhub_domname'))
		{
			$zmhub_domname = get_option('zmhub_domname');
		}
		$req_time = time();
		$ref_auth_params = array(
					'client_id' => ZMHUB__CLIENT_ID,
					'grant_type'     => 'refresh_token',
					'client_secret'     => self::zmhub_return_constant_val('CLIENT_SECRET', $zmhub_domname),
					'refresh_token'          => $ref_token,
				);
		$account_url = ZMHUB__ACCOUNTS_URL;
		if($zmhub_domname=='ca')	{
				$account_url = ZMHUB__ACCOUNTS_URL_CA;
		}
		$auth_url = $account_url. $zmhub_domname . '/oauth/v2/token';
		$response = wp_remote_post( $auth_url, array(
	    'method'      => 'POST',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => $ref_auth_params
	    ) );
	    $response_body = json_decode($response['body'],true);
	    if ( is_wp_error( $response ) ) {

		    $error_message = $response->get_error_message();
		    echo esc_html($error_message);
		    return false;
		}
		else if( wp_remote_retrieve_response_code( $response ) != 200 ) {
			echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
			return false;
		}
		else if(isset($response_body['error']))
		{
			echo esc_html($response_body['error']);
			return false;
		}
		else {
		    $response_body['refresh_token'] = $ref_token;
		    $response_body['req_time'] = $req_time;
		    update_option('zmhub_token_details', serialize($response_body),false);
		    return $response_body['access_token'];
		}
	}

	// Redirect to connect page if account is not connected.
	public static function zmhub_form_page() {

		if (self::zmhub_get_access_token() )
			self::zmhub_view( 'mh-signup-form' );
		else
			self::zmhub_view( 'conf' );
	}

	public static function zmhub_wa_page() {
		if (self::zmhub_get_access_token() )
			self::zmhub_view( 'mh-wa' );
		else
			self::zmhub_view( 'conf' );
	}

	// Delete All Data and Settings Before Removing the Account.
	public static function zmhub_remove_account() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		self::zmhub_integration_disconnect();
		global $wpdb, $table_prefix;
		$tblname = $table_prefix . 'zmhub_forms';
		$delete = $wpdb->query("TRUNCATE TABLE $tblname");
		delete_option('zmhub_script');
		delete_option('zmhub_script_setting');
		delete_option('zmhub_token_details');
		delete_option('zmhub_connect_time');
		delete_option('zmhub_user_email');
		delete_option('zmhub_rated');
		delete_option('zmhub_domname');
	}

	// Runs on Activation, Any meta changes will be updated to the table.
	public static function zmhub_create_mhforms_table()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		  global $wpdb, $table_prefix;
	      $tblname = $table_prefix . 'zmhub_forms';
	      $charset_collate = $wpdb->get_charset_collate();
	        $sql = "CREATE TABLE $tblname (
	          id int(11) NOT NULL AUTO_INCREMENT,
	          form_id varchar(56) NOT NULL,
	          form_name varchar(56) NOT NULL,
	          form_type varchar(30) NOT NULL,
	          list_name varchar(100) NOT NULL,
	          status tinyint(2) NOT NULL DEFAULT 1,
	          url varchar(255)  NOT NULL,
	          created_time bigint(19),
	          PRIMARY KEY  id (id),
	          UNIQUE KEY  form_id (form_id)
	        ) $charset_collate;";
	        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	        dbDelta($sql);
	     self::zmhub_update_zmhub_formid();
	}
	public static function zmh_remove_duplicate()
	{
		global $wpdb, $table_prefix;
	    $tblname = $table_prefix . 'zmhub_forms';
	    if($wpdb->get_var("SHOW TABLES LIKE '$tblname'") == $tblname) {
	    	$result = $wpdb->get_results("SELECT id from $tblname WHERE `id` IS NOT NULL");
		    if(count($result) != 0)
		    {
		        $wpdb->query(
				        "DELETE n1 FROM $tblname n1, $tblname n2 WHERE n1.id > n2.id AND n1.form_id like n2.form_id"
				);
		    }
	    }
	}

	public static function zmhub_update_zmhub_formid() {

		global $wpdb, $table_prefix;
		$tblname = $table_prefix . 'zmhub_forms';
		$sql = "SELECT form_id FROM $tblname";
		$allforms = $wpdb->get_col($sql);
		foreach ($allforms as $singleform) {
	 		$wpdb->update( $tblname, array( 'form_id' => self::zmhub_decrypt_value($singleform,'cryptoutill')), array( 'form_id' => $singleform ), array( '%s' ), array( '%s'));
	 				}
	}

	public static function zmhub_decrypt_value($val,$enc_type)
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		$zmhub_domname = 'com';
		if(get_option('zmhub_domname'))
		{
			$zmhub_domname = get_option('zmhub_domname');
		}
		$response_body ='';
		$auth_url = 'https://ma.zoho.com/api/v2/forms/decrypt?&id=' .$val;
		$response = wp_remote_get( $auth_url, array(
	    'method'      => 'GET',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => NULL,
	    ) );
	     $response_body = json_decode($response['body'],true);
	     return $response_body['value'];
	}

	public static function zmhub_general_admin_notice(){
		if(get_option('zma_notice') == null)
		{
			echo'<div class="notice hide-dismiss-button" style="padding-right: 12px !important;border-left-color: #72aee6;" id="zma-notices-message"><p><b class="notice-title" style="display: inline-block;color: #1d2327;font-size: 18px;">Zoho MarketingHub is now Zoho Marketing Automation</b> <br>Read more about the changes <a href="https://www.zoho.com/blog/marketingautomation/zoho-marketinghub-is-now-zoho-marketing-automation.html" target="_blank">here</a></p><button class="zma-notice-dismiss">Thanks, Got it.</button></div>';
		}
	}
	public static function zmhub_ecommerce_page() {
			if (self::zmhub_get_access_token())	{
				if(isset($_GET['success']) && $_GET['success'] == 0)
					{
						update_option('zmhub_error_msg',"Integration with Zoho MarketingAutomation has been denied");
						wp_safe_redirect(get_admin_url('') ."admin.php?page=mh-wc");
						exit();
					}
				else if(isset($_GET['user_id']))
				{
					self::zmhub_update_integration_data();
					wp_safe_redirect(get_admin_url('') ."admin.php?page=mh-wc");
				}
				else {
						if(get_option('zmhub_integration') == 1)
						{
							self::zmhub_update_integration_data();
						}
						else if(get_option('zmhub_integration') == 3)
						{
							$query_string = http_build_query(['integrationIdDigest' => self::zmhub_get_parsed_val('zmhub_intergration_details','integration_digest')]);
							$new_response = self::zmhub_http_request('/api/v2/wp/ecommerce/storestats?' .$query_string,'GET','');
							$new_response_body = json_decode($new_response['body'],true);
							if( wp_remote_retrieve_response_code( $new_response ) != 200 ) {
								echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $new_response )) ;
							}
							else if(isset($new_response_body['account_status']) && $new_response_body['account_status'] == 0 )
							{
								//self::zmhub_integration_disconnect();
								delete_option('zmhub_store_stats');
								delete_option('zmhub_integration');
								delete_option('zmhub_intergration_details');
								delete_option('zmhub_error_msg');
								delete_option('zmhub_optin_setting');
								wp_safe_redirect(get_admin_url('') ."admin.php?page=mh-wc");
										exit();
							}
							else
							{
								update_option('zmhub_store_stats',serialize($new_response_body));
							}
						}
						self::zmhub_view( 'mh-wc' );
						delete_option('zmhub_error_msg');
				}
			}
			else  {
				self::zmhub_view( 'conf' );
				delete_option('zmhub_error_msg');
			}
		}
		public static function zmhub_update_integration_data() {
			delete_option('zmhub_error_msg');
			$response  = self::zmhub_http_request('/api/v2/wp/ecommerce/validate?storeurl='.get_site_url(),'POST','');
			$response_body = json_decode($response['body'],true);
			if(isset($response_body['message']))
			{
				update_option("zmhub_error_msg",$response_body['message']);
			}
			else if(isset($response_body['code']) && $response_body['code'] == 200)
			{
				update_option("zmhub_intergration_details",serialize($response_body));
				update_option("zmhub_integration",1);
				//echo $response_body;
			}
			else
			{
				echo "Error in fetching details";
				update_option("zmhub_error_msg","Error in fetching details. Try Again & If the issue still persists contact customer support");
			}
		}
		public static function zmhub_http_request($endpoint,$req_type,$params) {
			$zmhub_domname = 'com';
			if(get_option('zmhub_domname'))
			{
				$zmhub_domname = get_option('zmhub_domname');
			}
			$hub_url = ZMHUB__HUB_URL;
			if($zmhub_domname=='ca')	{
					$hub_url = ZMHUB__HUB_URL_CA;
			}
			$headarray = array('Authorization' => 'Zoho-oauthtoken '. self::zmhub_get_parsed_val('zmhub_token_details','access_token') );
			$auth_url = $hub_url. $zmhub_domname .  $endpoint;
			$response = wp_remote_request( $auth_url, array(
				'method'      => $req_type,
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'body'        => $params,
				'headers'     => $headarray
				) );
				return $response;
		}
	public static function zmhub_woocommerce_authorize() {
				if ( ! current_user_can( 'manage_options' ) ) {
					die();
				}
				check_ajax_referer( 'mh-ajax-nonce', 'security' );
				$response  = self::zmhub_http_request('/api/v2/wp/ecommerce/ping','GET','');
				$response_body = json_decode($response['body'],true);

				if(isset($response_body['code']) && $response_body['code'] == 500)
				{
					echo("1");
				}
				else if((isset($response_body['isUserPresent']) && !$response_body['isUserPresent']) || (isset($response_body['code']) && $response_body['code'] == 901) )
				{
					echo("2");
				}
				else if(isset($response_body['code']) && $response_body['code'] == 401)
				{
					echo("5");
				}
				else if(isset($response_body['code']) && $response_body['code'] == 200 && ($response_body['message'])['status_message'] == 'Plan unsupported')	{
					echo("6");
				}
				else if(isset($response_body['isAdmin']) && $response_body['isAdmin'] )
				{
					$user_id =  $response_body['userid'];
					$store_url = get_site_url();
					$endpoint = '/wc-auth/v1/authorize';
					$zmhub_domname = 'com';
					if(get_option('zmhub_domname'))
					{
						$zmhub_domname = get_option('zmhub_domname');
					}
					$hub_url = ZMHUB__HUB_URL;
					if($zmhub_domname=='ca')	{
							$hub_url = ZMHUB__HUB_URL_CA;
					}
					$params = [
							'app_name' => 'Zoho Marketing Automation',
							'scope' => 'read_write',
						  'user_id' => get_site_url(). '_' .$user_id,
							'return_url' => get_admin_url('') .'admin.php?page=mh-wc',
							'callback_url' => $hub_url . $zmhub_domname .  '/ua/wccallback'
					];
					$query_string = http_build_query( $params );
					echo esc_url_raw($store_url . $endpoint . '?' . $query_string);
				}
				else if(isset($response_body['isAdmin']) && !$response_body['isAdmin'] )
				{
					echo("4");
				}
				else
				{
					echo("3");
				}
				wp_die();
		}

		public static function zmhub_add_list() {
			if ( ! current_user_can( 'manage_options' ) ) {
				die();
			}
			check_ajax_referer( 'mh-ajax-nonce', 'security' );
			$query_string = http_build_query(['integrationIdDigest' => self::zmhub_get_parsed_val('zmhub_intergration_details','integration_digest'), 'listIdDigest' => $_POST['list_digest'], 'is_subscribed' => $_POST['isSubscribed'], 'domain' => get_site_url(), 'store_name' => get_bloginfo( 'name' ), 'currency' => get_woocommerce_currency()]);
			$response = self::zmhub_http_request('/api/v2/wp/ecommerce/authorize?' .$query_string,'POST','');
			$response_body = json_decode($response['body'],true);
			if($response_body['code'] == 200)
			{
				update_option('zmhub_integration', 2);
				//Add default Options
				$zmhub_optin_setting = array('check' => 'unchecked', 'label' => 'Subscribe to our newsletter', 'hook' => 'woocommerce_after_checkout_billing_form');
				// if(isset($_POST['isSubscribed']) && $_POST['isSubscribed'] == 'true')
				// {
				// 	$wp_user_query = new WP_User_Query(array('role' => 'Customer'));
				// 	$users = $wp_user_query->get_results();
				// 	if (!empty($users)) {
				// 			foreach ($users as $user)
				// 			{
				// 				if(get_user_meta($user->ID, 'zmhub_newsletter_subscription', true) == '')
				// 				{
				// 					add_user_meta( $user->id, 'zmhub_newsletter_subscription', true );
				// 				}
				// 			}
				// 	}
				// }
				update_option('zmhub_optin_setting',serialize($zmhub_optin_setting));
				echo esc_url(get_admin_url('') ."admin.php?page=mh-wc");
				exit();
			}
			else {
				echo esc_html($response_body['message']);
			}
		wp_die();
		}
		public static function zmhub_integration_status()
		{
				if ( ! current_user_can( 'manage_options' ) ) {
					die();
				}
				check_ajax_referer( 'mh-ajax-nonce', 'security' );
				$query_string = http_build_query(['integrationIdDigest' => self::zmhub_get_parsed_val('zmhub_intergration_details','integration_digest')]);
					$response = self::zmhub_http_request('/api/v2/wp/ecommerce/status?' .$query_string,'GET','');
					if( wp_remote_retrieve_response_code( $response ) == 200 ) {
						$response_body = json_decode($response['body'],true);
						if($response_body['code'] == 200)
						{
							$new_response = self::zmhub_http_request('/api/v2/wp/ecommerce/storestats?' .$query_string,'GET','');
							$new_response_body = json_decode($new_response['body'],true);
							if($new_response_body['account_status'] && $new_response_body['account_status'] == 0 )
							{
								//self::zmhub_integration_disconnect();
								delete_option('zmhub_store_stats');
								delete_option('zmhub_integration');
								delete_option('zmhub_intergration_details');
								delete_option('zmhub_error_msg');
								delete_option('zmhub_optin_setting');
								wp_safe_redirect(get_admin_url('') ."admin.php?page=mh-wc");
											exit();
							}
							update_option('zmhub_store_stats',serialize($new_response_body));
							update_option('zmhub_integration', 3);
							echo esc_url(get_admin_url('') ."admin.php?page=mh-wc");
							exit();
						}
						else
						{
							echo 'error';
							exit();
						}
					}
		}
		public static function zmhub_integration_disconnect() {
				if (!current_user_can('manage_options')) {
					die();
				}
				check_ajax_referer('mh-ajax-nonce', 'security');
				try {
					$query_string = http_build_query(['integrationIdDigest' => self::zmhub_get_parsed_val('zmhub_intergration_details', 'integration_digest')]);
					$response = self::zmhub_http_request('/api/v2/wp/ecommerce/deny?' . $query_string, 'POST', '');
					$response_body = json_decode($response['body'], true);
					// $wp_user_query = new WP_User_Query(array('role' => 'Customer'));
					// $users = $wp_user_query->get_results();
					// if (!empty($users)) {
					// 	foreach ($users as $user) {
					// 		delete_user_meta($user->ID, 'zmhub_newsletter_subscription');
					// 	}
					// }
				} catch (Exception $e) {
					// Empty catch block to ignore exceptions
				} finally {
					delete_option('zmhub_store_stats');
					delete_option('zmhub_integration');
					delete_option('zmhub_intergration_details');
					delete_option('zmhub_error_msg');
					delete_option('zmhub_optin_setting');
				}
		}
		public static function zmhub_optin_save()
		{
			if ( ! current_user_can( 'manage_options' ) ) {
				die();
			}
			check_ajax_referer( 'mh-ajax-nonce', 'security' );
			if (isset($_POST['msg']) && isset($_POST['check'])) {
				$allowed_values = array('checked', 'unchecked', 'hidden');
				$check_value = sanitize_text_field($_POST['check']);
				if (!in_array($check_value, $allowed_values)) {
					$check_value = 'unchecked';
				}
				$zmhub_optin_setting = array(
					'check' => $check_value,
					'label' => sanitize_text_field(wp_slash($_POST['msg'])),
					'hook' => sanitize_text_field(wp_slash($_POST['hook']))
				);
				update_option('zmhub_optin_setting', serialize($zmhub_optin_setting));
				}
		}
		public static function getZmaVersion()	{
			$zma_version = "1.0";
			if(get_option('zma_version')!=null)	{
				$zma_version = get_option('zma_version');
			}
			else {
				$response  = self::zmhub_http_request('/api/v2/wp/ecommerce/ping','GET','');
				$response_body = json_decode($response['body'],true);
				if(isset($response_body['code']) && $response_body['code'] == 200 && $response_body['isMANewversion'])
				{
					update_option('zma_version',"2.0");
					$zma_version = "2.0";
				}
			}
			return $zma_version;
		}
}
?>
