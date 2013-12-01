<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

add_action('admin_menu', 'my_'.MyConstants::PREFIX.'_theme_options_panel');

function my_migs_payment_gateway_theme_options_panel() {
    add_menu_page('Migs Dashboard', 'Migs Dashboard', 'manage_options', 'bank-migs-dashboard', 'my_'.MyConstants::PREFIX.'_wps_theme_func', MYMIGSPAYMENTGATEWAYURL . 'static/images/icon.png');
    add_submenu_page('bank-migs-dashboard', 'Settings', 'Settings', 'manage_options', 'bank-migs-settings', 'my_'.MyConstants::PREFIX.'_wps_theme_func_settings');
    add_submenu_page('bank-migs-dashboard', 'Test-Case', 'Test-Case', 'manage_options', 'bank-migs-testcase', 'my_'.MyConstants::PREFIX.'_wps_theme_func_test_case');
    add_submenu_page('bank-migs-dashboard', 'Help', 'Help', 'manage_options', 'bank-migs-help', 'my_'.MyConstants::PREFIX.'_wps_theme_func_help');
}

function my_migs_payment_gateway_wps_theme_func() {
    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br/></div><h2>Migs Transections</h2></div>';
    include(MYMIGSPAYMENTGATEWAYPATH . "/admin/view/ApplicationDashboard.php");
}

function my_migs_payment_gateway_wps_theme_func_settings() {
    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br/></div><h2>Settings/Options</h2></div>';
    include(MYMIGSPAYMENTGATEWAYPATH . "/admin/view/ApplicationSettings.php");
}

function my_migs_payment_gateway_wps_theme_func_test_case() {
    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br/></div><h2>Test case</h2></div>';
    include(MYMIGSPAYMENTGATEWAYPATH . "/admin/view/MigsTestCase.php");
}


function my_migs_payment_gateway_wps_theme_func_help() {
    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br/></div><h2>Help</h2></div>';
    include(MYMIGSPAYMENTGATEWAYPATH . "/admin/view/ApplicationHelp.php");
}
