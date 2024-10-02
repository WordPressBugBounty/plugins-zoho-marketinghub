<?php
/*
   Plugin Name:Zoho Marketing Automation
   Plugin URI:https://help.zoho.com/portal/en/kb/marketing-automation/user-guide/settings/integrations/articles/marketingautomation-plugin-for-wordpress
   Version:1.2.8
   Author:Zoho Marketing Automation
   Author URI:https://zoho.com/marketingautomation
   Description:Using the Zoho Marketing Automation plugin, analyze your website visitor’s behavior and activities, and convert them into leads by embedding signup forms on your web pages.
*/
/*
    Copyright (c) 2019, ZOHO CORPORATION
    All rights reserved.

    Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

    2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

// Prevent direct accesss
defined( 'ABSPATH' ) or exit;

define( 'ZMHUB_VERSION', '1.2.8' );
define( 'ZMHUB__MINIMUM_WP_VERSION', '5.0' );
define( 'ZMHUB__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ZMHUB__ACCOUNTS_URL', 'https://accounts.zoho.' );
define( 'ZMHUB__HUB_URL', 'https://ma.zoho.' );

require_once( ZMHUB__PLUGIN_DIR . 'includes/class.zmh.php' );

add_shortcode( 'zmhub', array('ZohoMarketingHub', 'zmhub_form_sc') );
add_shortcode( 'zmauto', array('ZohoMarketingHub', 'zmhub_form_sc') );
add_action('wp_footer', array('ZohoMarketingHub','zmhub_find_footer_tracking_codes') );
//add_action('zmhub_refresh_forms_event', 'zmhub_refresh_forms_event_hook');
add_action('plugins_loaded', array('ZohoMarketingHub','zmh_plugin_version'));
register_activation_hook( __FILE__, array('ZohoMarketingHub', 'zmhub_plugin_activation'));
register_deactivation_hook( __FILE__, array('ZohoMarketingHub', 'zmhub_plugin_deactivation'));

if ( is_admin() ) {
  require_once( ZMHUB__PLUGIN_DIR . 'includes/admin/class.zmh-admin.php' );
  add_action( 'init', array( 'ZohoMarketingHub_Admin', 'zmhub_init' ) );
  add_action('admin_notices', array( 'ZohoMarketingHub_Admin','zmhub_general_admin_notice'));
}
?>
