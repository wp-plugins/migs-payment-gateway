<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

class MigsUtilities {

    const encrypt = "encrypt";
    const decrypt = "decrypt";

    public static function e_d($string, $action) {
        $output = "";
        $secret_key = get_option(MyConstants::PREFIX . "_salt_1");
        if ($action == MigsUtilities::encrypt) {
            $output = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secret_key, $string, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
        } else if ($action == MigsUtilities::decrypt) {
            $output = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secret_key, base64_decode($string), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
        }
        return $output;
    }

    public static function userCanEdit($user = '') {
        if ($user == '')
            $user = wp_get_current_user();
        return user_can($user, 'manage_options');
    }

    public static function getEncUtility($post_id, $amount, $bs) {
        $t = time();
        $encUtil = new stdClass();
        $encUtil->merchTxnRef = $post_id . "-" . $t . "-" . $amount;
        $encUtil->orderInfo = $t;
        $encUtil->enc = $bs->getHashedPassword($t);
        $encUtil->formba = $t;
        $encUtil->formc = $bs->getHashedPassword($t . $amount . $post_id . $encUtil->merchTxnRef);
        $encUtil->return_url = $bs->return_url . '&t=' . $t . '&formbc=' . $encUtil->formc;
        return $encUtil;
    }

    public static function getMigsCachingInvaidation($post_id, $amount) {
        $post_id = "'" . MigsUtilities::e_d($post_id, MigsUtilities::encrypt) . "'";
        $amount = "'" . MigsUtilities::e_d($amount, MigsUtilities::encrypt) . "'";
        $adminUrl = admin_url("admin-ajax.php");
        $htmlCall = "<script type=\"text/javascript\" charset=\"utf-8\">";
        $htmlCall .= " var ajaxurl = '$adminUrl'; ";
        $htmlCall .= "jQuery(document).ready(function($) {"
                . "var data = {action: 'my_migs_action', pid: $post_id, am: $amount};
                    jQuery.post(ajaxurl, data, function(response) {
                        if(null!= response){
                            jQuery('#merchTxnRef').val(response.merchTxnRef);
                            jQuery('#orderInfo').val(response.orderInfo);
                            jQuery('#enc').val(response.enc);
                            jQuery('#formc').val(response.formc);
                            jQuery('#return_url').val(response.return_url);
                        }
                    });"
                . "});"
                . "</script>";
        return $htmlCall;
    }

    public static function filterNumberForChart($a) {
        $a = str_replace("$", "", $a);
        $a = str_replace(",", "", $a);
        return $a;
    }

    public static function curPageURL() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"])) {
            if ($_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
        }
        $pageURL .= "://";
        if (($_SERVER["SERVER_PORT"] != "80") && ($_SERVER["SERVER_PORT"] != "8080")) {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    //function to map each response code number to a text message	
    public static function getMigsPaymentGatewayResponseDescription($responseCode) {
        switch ($responseCode) {
            case "0" : $result = "Transaction Successful";
                break;
            case "?" : $result = "Transaction status is unknown";
                break;
            case "1" : $result = "Unknown Error";
                break;
            case "2" : $result = "Bank Declined Transaction";
                break;
            case "3" : $result = "No Reply from Bank";
                break;
            case "4" : $result = "Expired Card";
                break;
            case "5" : $result = "Insufficient funds";
                break;
            case "6" : $result = "Error Communicating with Bank";
                break;
            case "7" : $result = "Payment Server System Error";
                break;
            case "8" : $result = "Transaction Type Not Supported";
                break;
            case "9" : $result = "Bank declined transaction (Do not contact Bank)";
                break;
            case "A" : $result = "Transaction Aborted";
                break;
            case "C" : $result = "Transaction Cancelled";
                break;
            case "D" : $result = "Deferred transaction has been received and is awaiting processing";
                break;
            case "E" : $result = "Invalid Credit Card";
                break;
            case "F" : $result = "3D Secure Authentication failed";
                break;
            case "I" : $result = "Card Security Code verification failed";
                break;
            case "G" : $result = "Invalid Merchant";
                break;
            case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)";
                break;
            case "N" : $result = "Cardholder is not enrolled in Authentication scheme";
                break;
            case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed";
                break;
            case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed";
                break;
            case "S" : $result = "Duplicate SessionID (OrderInfo)";
                break;
            case "T" : $result = "Address Verification Failed";
                break;
            case "U" : $result = "Card Security Code Failed";
                break;
            case "V" : $result = "Address Verification and Card Security Code Failed";
                break;
            case "X" : $result = "Credit Card Blocked";
                break;
            case "Y" : $result = "Invalid URL";
                break;
            case "B" : $result = "Transaction was not completed";
                break;
            case "M" : $result = "Please enter all required fields";
                break;
            case "J" : $result = "Transaction already in use";
                break;
            case "BL" : $result = "Card Bin Limit Reached";
                break;
            case "CL" : $result = "Card Limit Reached";
                break;
            case "LM" : $result = "Merchant Amount Limit Reached";
                break;
            case "Q" : $result = "IP Blocked";
                break;
            case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed";
                break;
            case "Z" : $result = "Bin Blocked";
                break;

            default : $result = "Unable to be determined";
        }
        return $result;
    }

    public static function reloadMigsPaymentGatewayPage($http = '', $msg = '') {
        $js = "<script>";
        if (!empty($msg)) {
            $js .= "alert('$msg');";
        }
        $js .= "window.location ='$http';";
        $js .= "</script>";

        echo $js;
    }

    public static function sendMigsPaymentGatewayEmailToAdmin($bpl) {
        do_action(MyConstants::PREFIX . '_send_admin_email', $bpl);

        $admin_email = get_option('admin_email');
        $url = get_option('siteurl');
        $blogname = get_option('blogname');
        add_filter('wp_mail_content_type', 'set_html_content_type_for_transaction_email');
        $subject = "Payment accepted! " . $bpl->merchtxnref . "";
        $bpl = $bpl->getPaymentLogsByMerchentTxtnRef($bpl->merchtxnref);

        $post = get_post($bpl->post_id);
        $permalink = "";
        if (!empty($post)) {
            $permalink = get_permalink($bpl->post_id);
        }
        $userinfo = "";
        if ($bpl->user_id != 0) {
            $user = get_userdata($bpl->user_id);
            $userinfo .= '<br />===User information=== <br />';
            $userinfo .= 'Username: ' . $user->user_login . "<br/>";
            $userinfo .= 'User email: ' . $user->user_email . "<br/>";
            $userinfo .= 'User first name: ' . $user->user_firstname . "<br/>";
            $userinfo .= 'User last name: ' . $user->user_lastname . "<br/>";
            $userinfo .= 'User display name: ' . $user->display_name . "<br/>";
            $userinfo .= 'User ID: ' . $user->ID . "<br/>";
            $userinfo .= '<br />===End of User information=== <br />';
        }

        $body = "Dear Admin, <br /> "
                . "<br/><p><br/>"
                . "A new payment has been processed successfully: <br />"
                . "<br />===Bank/Transection information=== <br />"
                . "Merchant Transaction Reference: " . $bpl->merchtxnref . " <br />"
                . "Order information: " . $bpl->orderinfo . " <br/>"
                . "Receipt Number: " . $bpl->receiptnumber . " <br/>"
                . "Transection Number: " . $bpl->transectionnumber . " <br />"
                . "Batch Number: " . $bpl->batchnumber . " <br />"
                . "Transaction Response Code Description: " . $bpl->bankresponsedesc . " <br />";
        if (!empty($post)) {
            $body .= "<br />===Product information=== <br />"
                    . "Product name: " . $post->post_title . " <br/>"
                    . "Product url: " . $permalink . " <br/>";
        }
        $body .= $userinfo;
        $body .= "<br />===Charges=== <br />"
                . "Paid amount: " . MigsPaymentGatewaySettings::getCurrency() . " " . number_format($bpl->amount, 2) . " <br/>"
                . "Date time of order: " . $bpl->dateoforder . " <br/>"
                . "Client IP address: " . $bpl->ip . " <br />"
                . "<br /><em>This is an automated message from " . $url . ".</em></p>";


        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ' . $blogname . ' <' . $admin_email . '>' . "\r\n";
        $result = wp_mail($admin_email, $subject, $body, $headers);
        if (!$result) {
            mail($admin_email, $subject, $body, $headers);
        }
        remove_filter('wp_mail_content_type', 'set_html_content_type_for_transaction_email');
    }

    public static function sendRequestToMigsPaymentGateway($bs) {
        $appendAmp = 0;
        $vpcURL = "";
        $md5HashData = $bs->secure_secret;
        $newHash = "";
        ksort($_POST);
        foreach ($_POST as $key => $value) {
            if (strlen($value) > 0 && ($key == 'accessCode' || $key == 'merchTxnRef' || $key == 'merchant' || $key == 'orderInfo' || $key == 'amount' || $key == 'returnURL')) {
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
                $md5HashData .= $value;
            }
        }
        $newHash .= $vpcURL . "&vpc_SecureHash=" . strtoupper(md5($md5HashData));
        $migs_url = get_option(MyConstants::PREFIX . '_url');
        echo "<script language=\"javascript\">top.location.href='$migs_url$newHash'</script>";
    }

    public static function getMigsPaymentGatewayPostFromMerchantId($merchTxnRef) {
        $pieces = explode("-", $merchTxnRef);
        return intval($pieces[0]);
    }

}

if (!function_exists('wp_get_current_user')) {

    function wp_get_current_user() {
        require (ABSPATH . WPINC . '/pluggable.php');
        global $current_user;
        get_currentuserinfo();
        return $current_user;
    }

}

//function to display a No Value Returned message if value of field is empty
if (!function_exists('null2unknown')) {

    function null2unknown($data) {
        if (($data == "") && (!isset($data)))
            return "No Value Returned";
        else
            return $data;
    }

}

if (!function_exists('knownGetKeyValue')) {

    function knownGetKeyValue($key) {
        /* @var $GET type */
        if (isset($GET[$key])) {
            return $GET[$key];
        }
        return "";
    }

}
if (!function_exists('getBrowserValue')) {

    function getBrowserValue($key) {
        $val = "";
        if (isset($_GET[$key])) {
            $val = null2unknown(addslashes($_GET[$key]));
        }
        return $val;
    }

}

/*
 * This function for the submit form and reassamble data before sending it to the Bank
 */
if (($_SERVER["REQUEST_METHOD"] == "POST") &&
        ((isset($_POST['formba'])) && (isset($_POST['formbc'])) && (isset($_POST['ba'])) && (isset($_POST['amount'])) && (isset($_POST['post_id'])) && (isset($_POST['merchTxnRef'])))
) {
    $bs = new MigsPaymentGatewaySettings();
    $bs = $bs->getBankSettings();
    $t = intval($_POST['formba']);
    $enc = $bs->getHashedPassword($t);
    if ($enc == $_POST['ba']) {
        $amount = $_POST['amount'];
        $post_id = intval($_POST['post_id']);
        $merchTxnRef = $_POST['merchTxnRef'];
        $formc = $_POST['formbc'];
        //Lets check if during the post the amount or the post id was changed
        if ($formc == $bs->getHashedPassword($t . $amount . $post_id . $merchTxnRef)) {

            $user_id = 0;
            $loggedInUser = wp_get_current_user();
            if (!empty($loggedInUser)) {
                $user_id = $loggedInUser->ID;
            }

            $ip = $_SERVER['REMOTE_ADDR'];
            $bpl = new MigsPaymentGatewayPaymentLogs();
            $bpl->post_id = $post_id;
            $bpl->user_id = $user_id;
            $bpl->amount = null2unknown(addslashes($_POST["amount"]) / 100);
            $bpl->merchant_id = $bs->merchant;
            $bpl->merchtxnref = $merchTxnRef;
            $bpl->orderinfo = $_POST['orderInfo'];
            $bpl->ip = $ip;
            $bpl->insertLogs();

            MigsUtilities::sendRequestToMigsPaymentGateway($bs);
        }
    }
}

class returnValidationProcess {

    function firstStepValid() {
        return (isset($_GET['formbc'])) && (isset($_GET['t'])) && (isset($_GET['vpc_TxnResponseCode']));
    }

    function secondStepValid($bs) {
        return (strlen($bs->secure_secret) > 0 && addslashes($_GET["vpc_TxnResponseCode"]) != "7" && addslashes($_GET["vpc_TxnResponseCode"]) != "No Value Returned");
    }

    function thirdStepValid($bs, $vpc_Txn_Secure_Hash) {
        $md5HashData = $bs->secure_secret;
        $md5HashData_2 = $bs->secure_secret;
        foreach ($_GET as $key => $value) {
            if ($key != "vpc_SecureHash" && strlen($value) > 0 && $key != 'action' && $key != 't' && $key != 'formbc') {
                $hash_value = str_replace(" ", '+', $value);
                $hash_value = str_replace("%20", '+', $hash_value);
                $md5HashData_2 .= $value;
                $md5HashData .= $hash_value;
            }
        }
        return (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData)) || strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData_2)));
    }

}
