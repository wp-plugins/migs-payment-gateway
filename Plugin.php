<?php

/*
  Plugin Name: Migs Payment Gateway
  Plugin URI: http://www.yourwwwdesign.com/migs_payment_gateway/
  Description: Migs (Not Official) payment gateway.
  Version: 1.0
  Author: Gabriel82
  Author URI: http://www.yourwwwdesign.com/
  License: GPLv2
 */
$wp_rewrite = new WP_Rewrite();
define('MYMIGSPAYMENTGATEWAYPATH', plugin_dir_path(__FILE__));
define('MYMIGSPAYMENTGATEWAYURL', plugin_dir_url(__FILE__));

require_once(MYMIGSPAYMENTGATEWAYPATH . "/objects/MyConstants.php");
require_once(MYMIGSPAYMENTGATEWAYPATH . "/objects/PaymentLogs.php");
require_once(MYMIGSPAYMENTGATEWAYPATH . "/objects/Settings.php");
require_once(MYMIGSPAYMENTGATEWAYPATH . "PublicServlet.php");

require_once("Notification.php");
require_once("Utilities.php");
//This plugin is allowed only for users who can manage wordpress options and above
//Means to super admins and administrators
$user = wp_get_current_user();
$caneditmigspaymentgatewaysettings = MigsUtilities::userCanEdit($user);
require_once("ShortCodes.php");

if (is_admin() && $caneditmigspaymentgatewaysettings) {
    include(MYMIGSPAYMENTGATEWAYPATH . "/objects/GridObject.php");
    require_once(MYMIGSPAYMENTGATEWAYPATH . "/admin/Servlet.php");
    require_once(MYMIGSPAYMENTGATEWAYPATH . "/admin/view/ShortCodeAddon.php");
    require_once(MYMIGSPAYMENTGATEWAYPATH . "/admin/Menu.php");
    add_action('admin_notices', 'Notification::NotifyUtil');
    
    // Add settings link on plugin page
    function migs_payment_gateway_settings_link($links) {
        $settings_link = '<a href="admin.php?page=bank-migs-settings">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_$plugin", MyConstants::PREFIX . '_settings_link');
}

//Add the install and uninstall script
require_once(MYMIGSPAYMENTGATEWAYPATH . "Install.php");
register_activation_hook(__FILE__, array('MyMigsPaymentGatewayPlugin', 'install'));
register_deactivation_hook(__FILE__, array('MyMigsPaymentGatewayPlugin', 'uninstall'));
