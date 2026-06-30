<?php
/*
   Plugin Name:Zoho Marketing Automation
   Plugin URI:https://help.zoho.com/portal/en/kb/marketing-automation/user-guide/settings/integrations/articles/marketingautomation-plugin-for-wordpress
   Version:1.3.7
   Author:Zoho Marketing Automation
   Author URI:https://zoho.com/marketingautomation
   Description:With the Zoho Marketing Automation plugin, track visitor behavior, embed signup forms, and leverage the new eCommerce integration to drive conversions.
   WC requires at least: 3.7.1
   WC tested up to: 9.7.1
   License: GPLv2 or later
   License URI: https://www.gnu.org/licenses/gpl-2.0.html
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

define( 'ZMHUB_VERSION', '1.3.7' );
define( 'ZMHUB__MINIMUM_WP_VERSION', '5.0' );
define( 'ZMHUB__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ZMHUB__ACCOUNTS_URL', 'https://accounts.zoho.' );
define( 'ZMHUB__ACCOUNTS_URL_CA', 'https://accounts.zohocloud.' );
define( 'ZMHUB__HUB_URL', 'https://ma.zoho.' );
define( 'ZMHUB__HUB_URL_CA', 'https://ma.zohocloud.' );


require_once( ZMHUB__PLUGIN_DIR . 'includes/class.zmh.php' );

add_shortcode( 'zmhub', array('ZohoMarketingHub', 'zmhub_form_sc') );
add_shortcode( 'zmauto', array('ZohoMarketingHub', 'zmhub_form_sc') );
add_action('wp_footer', array('ZohoMarketingHub','zmhub_find_footer_tracking_codes') );
add_action( 'init', array( 'ZohoMarketingHub', 'zmhub_init' ) );
//add_action('zmhub_refresh_forms_event', 'zmhub_refresh_forms_event_hook');
add_action('plugins_loaded', array('ZohoMarketingHub','zmh_plugin_version'));
register_activation_hook( __FILE__, array('ZohoMarketingHub', 'zmhub_plugin_activation'));
register_deactivation_hook( __FILE__, array('ZohoMarketingHub', 'zmhub_plugin_deactivation'));

if ( is_admin() ) {
  require_once( ZMHUB__PLUGIN_DIR . 'includes/admin/class.zmh-admin.php' );
  add_action( 'init', array( 'ZohoMarketingHub_Admin', 'zmhub_init' ) );
  add_action('admin_notices', array( 'ZohoMarketingHub_Admin','zmhub_general_admin_notice'));
}
add_action('admin_notices', function() {
  // Check if we are on the desired plugin page
  if (isset($_GET['page']) && $_GET['page'] === 'mh-wa' && get_option("zmhub_token_details")!=null) {
    // Check the condition
     if (ZohoMarketingHub_Admin::getZmaVersion() == "2.0" && !get_option('zma_custom_banner_shown') && get_option('zmhub_script')) {
        ?>
        <div id="zma-wa-custom-banner" class="notice notice-info" style="padding: 15px; border-left-color: #5b6fe8;">
            <p><strong>Important Notice:</strong>Since you have migrated to MA 2.0, the current tracking code from the old version of MA will no longer work. Please click "Proceed" below to add the new tracking code.</p>
            <a href="#" id="zma-custom-banner" class="zmhbtn" style="background-color: #5b6fe8; color: white; border-color: #5b6fe8; padding: 6px 12px; border-radius: 4px; text-decoration: none; display: inline-block;">Proceed</a>
        </div>
        <?php
    }
  }
});
//HPOS Comaptibility
add_action('before_woocommerce_init', 'zma_before_woocommerce_hpos');
function zma_before_woocommerce_hpos (){
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}
//Add Custom Header
function zma_add_header_to_zma_webhook($http_args, $arg, $id) {
	// Get the webhook object
    $webhook = wc_get_webhook($id);
	// Check if the webhook ID starts with 'zma'
	if ($webhook && strpos($webhook->get_name(), 'zma') !== false) {
		$http_args['headers']['x-zohomarketingautomation-plugin-version'] = ZMHUB_VERSION; // Add current plugin version
	}
	return $http_args;
}
add_filter('woocommerce_webhook_http_args', 'zma_add_header_to_zma_webhook', 10, 3);

?>
