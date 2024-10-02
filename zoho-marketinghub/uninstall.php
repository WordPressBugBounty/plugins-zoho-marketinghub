<?php
/**
 * Zoho Marketing Automation Uninstall
 *
 * Uninstalling Zoho Marketing Automation deletes user data, settings, tables, and options.
 *
 * @package Zoho MarketingHub\Uninstaller
 * @version 1.2.8
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

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
