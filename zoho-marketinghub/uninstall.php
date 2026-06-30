<?php
/**
 * Zoho Marketing Automation Uninstall
 *
 * Uninstalling Zoho Marketing Automation deletes user data, settings, tables, and options.
 *
 * @package Zoho MarketingHub\Uninstaller
 * @version 1.3.7
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
function zmhub_dummy_get_parsed_val($key,$value) {
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

global $wpdb, $wp_version;
$headarray = array('Authorization' => 'Zoho-oauthtoken '. zmhub_dummy_get_parsed_val('zmhub_token_details','access_token') );
$query_string = http_build_query(['integrationIdDigest' => zmhub_dummy_get_parsed_val('zmhub_intergration_details','integration_digest')]);
$zmhub_domname = 'com';
if(get_option('zmhub_domname'))
{
	$zmhub_domname = get_option('zmhub_domname');
}
$zma_url = ZMHUB__HUB_URL;
if($zmhub_domname=='ca')	{
		$zma_url = ZMHUB__HUB_URL_CA;
}
$url= $zma_url . $zmhub_domname . '/api/v2/wp/ecommerce/deny?' . $query_string;
			$response = wp_remote_request( $url, array(
		    'method'      => 'POST',
		    'headers'     => $headarray
		    ) );
delete_option('zmhub_store_stats');
delete_option('zmhub_integration');
delete_option('zmhub_intergration_details');
delete_option('zmhub_error_msg');
delete_option('zmhub_optin_setting');

global $wpdb, $wp_version;

// Remove options
delete_option('zmhub_script');
delete_option('zmhub_script_setting');
delete_option('zmhub_token_details');
delete_option('zmhub_connect_time');
delete_option('zmhub_user_email');
delete_option('zmhub_rated');
delete_option('zmhub_domname');
delete_option('zmh_plugin_version');

// Remove tables
global $wpdb;
$table_name = $wpdb->prefix . 'zmhub_forms';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);
