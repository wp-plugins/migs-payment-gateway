<?php
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}
global $userdata;
$specialnonce = $userdata->user_login . $userdata->user_pass;
$ajax_nonce = wp_create_nonce($specialnonce);
?>
<link rel="stylesheet" type="text/css" href="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/css/smoothness/jquery-ui-1.8.4.custom.css" />
<link rel="stylesheet" type="text/css" href="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/css/main.css" />
<?php
require_once(MYMIGSPAYMENTGATEWAYPATH . "/admin/view/dashboard/Chart.php");
require_once(MYMIGSPAYMENTGATEWAYPATH . "/admin/view/dashboard/Grid.php");
?>
