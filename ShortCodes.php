<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

function set_html_content_type_for_transaction_email() {
    return 'text/html';
}

function my_migs_payment_gateway_check_page_hooks() {
    if (isset($_GET['action']) && ($_GET['action'] === "testcase")) {
        $canedit = MigsUtilities::userCanEdit();
        if ($canedit) {
            get_template_part('header');
            echo '<div id="container">';
            require_once(MYMIGSPAYMENTGATEWAYPATH . "/admin/view/MigsTestCase.php");
            echo '</div>';
            get_template_part('footer');
            die();
        }
    }
    $v = new returnValidationProcess();
    if ($v->firstStepValid()) {


        $bs = new MigsPaymentGatewaySettings();
        $bs = $bs->getBankSettings();
        $permalinks = $bs->getReturnConfigPermalinks();
        ksort($_GET);
        if (($bs->return_post_id == 0) && ($permalinks == $bs->getDefaultConfigPermalinks())) {
            get_template_part('header');
            echo '<div id="container">';
            do_shortcode('[my_migs_return_url]');
            echo '</div>';
            get_template_part('footer');
            die();
        }
    }
}

add_action('template_redirect', 'my_' . MyConstants::PREFIX . '_check_page_hooks');

function get_migs_payment_gateway_return_url($atts) {
    $v = new returnValidationProcess();
    $bs = new MigsPaymentGatewaySettings();
    $bs = $bs->getBankSettings();
    if ($v->firstStepValid()) {
        $hashValidated = "";
        $errorExists = false;
        $vpc_Txn_Secure_Hash = addslashes($_GET["vpc_SecureHash"]);
        unset($_GET["vpc_SecureHash"]);
        ksort($_GET);
        if ($v->secondStepValid($bs)) {
            //if transaction secure hash is the same as the md5 variable created 
            if ($v->thirdStepValid($bs, $vpc_Txn_Secure_Hash)) {

                $txnResponseCode = getBrowserValue("vpc_TxnResponseCode");
                $merchTxnRef = getBrowserValue("merchTxnRef");
                $amount = (getBrowserValue("amount") / 100);
                $locale = getBrowserValue("vpc_Locale");
                $batchNo = getBrowserValue("vpc_BatchNo");
                $command = getBrowserValue("vpc_Command");
                $message = getBrowserValue("vpc_Message");
                $version = getBrowserValue("vpc_Version");
                $cardType = getBrowserValue("vpc_Card");
                $orderInfo = getBrowserValue("orderInfo");
                $receiptNo = getBrowserValue("vpc_ReceiptNo");
                $merchantID = getBrowserValue("merchant");
                $authorizeID = getBrowserValue("vpc_AuthorizeId");
                $transactionNo = getBrowserValue("vpc_TransactionNo");
                $acqResponseCode = getBrowserValue("vpc_AcqResponseCode");
                $textResponse = MigsUtilities::getMigsPaymentGatewayResponseDescription($txnResponseCode);

                $post_id = MigsUtilities::getMigsPaymentGatewayPostFromMerchantId($merchTxnRef);
                $bpl = new MigsPaymentGatewayPaymentLogs();
                $bpl->receiptnumber = $receiptNo;
                $bpl->transectionnumber = $transactionNo;
                $bpl->batchnumber = $batchNo;
                $bpl->bankresponsedesc = $textResponse;
                $bpl->post_id = $post_id;
                $bpl->merchtxnref = $merchTxnRef;
                $bpl->orderinfo = $orderInfo;

                if ($txnResponseCode == "0") {
                    $t = intval($_GET['t']);
                    $formc = $_GET['formbc'];
                    if ($formc == $bs->getHashedPassword($t . intval($_GET["amount"]) . $post_id . $merchTxnRef)) {
                        $data = MigsPaymentGatewayPaymentLogs::updateOnReturn(1, $bpl);
                        if ($data) {
                            MigsUtilities::sendMigsPaymentGatewayEmailToAdmin($bpl);
                        }
                    } else {
                        MigsPaymentGatewayPaymentLogs::updateOnReturn(0, $bpl);
                        $hashValidated .= "Incorrect product!";
                        $hashValidated .= "<br /> " . $textResponse;
                        $errorExists = true;
                    }
                } else {
                    MigsPaymentGatewayPaymentLogs::updateOnReturn(0, $bpl);
                    $hashValidated .= $textResponse;
                    $errorExists = true;
                }
            } else {
                $hashValidated .= "<b>INVALID HASH</b>";
                $errorExists = true;
            }
        } else {
            $hashValidated .= "<font color='orange'><b>Not Calculated - No 'SECURE_SECRET' present.</b></font>";
            $errorExists = true;
        }
        if ($errorExists) {
            echo '<div class="bank-migs-error"><b> The transaction was rejected.</b><br /><br />' . $hashValidated . '</div>';
            if (isset($_GET['vpc_Message'])) {
                echo "Message: <br />" . $_GET['vpc_Message'] . "<br /><br />";
            }
        } else {
            echo '<div class="bank-migs-approved">' . $hashValidated . '</div>';
        }
        echo "<br /><br />";
        if ($errorExists) {
            echo $bs->empty_data;
        } else {
            echo $bs->noerror_data;
        }
    }
}

/*
 * This function for the shortcode
 */

function get_migs_payment_gateway_button($atts) {
    $post_id = 0;
    if (isset($atts["loggedinonly"])) {
        if (!is_user_logged_in()) {
            return;
        }
    }
    if (!isset($atts["post_id"])) {
        if (is_page() || is_single()) {
            $post_id = get_the_ID();
        }
    } else {
        $post_id = intval($atts["post_id"]);
    }

    if (!(isset($atts["amount"]) && ($post_id != 0))) {
        echo "Please enter an amount & post id.";
        return;
    }
    $amount = floatval($atts["amount"]);
    $amount = (round($amount, 0) * 100);

    if ($amount == 0) {
        echo "Please enter an amount.";
        return;
    }

    //Get settings
    $bSettings = new MigsPaymentGatewaySettings();
    $bs = $bSettings->getBankSettings();

    $text = "Buy now";
    if (isset($atts["text"])) {
        $text = $atts["text"];
    }

    if (isset($atts["showprice"])) {
        $text .= " (" . ($amount / 100) . " USD)";
    }

    $cssclass = "";
    if (isset($atts["cssclass"])) {
        $cssclass = ' class="' . $atts["cssclass"] . '" ';
    }

    $htmlButton = '<button name="paymentbutton"' . $cssclass . '>' . $text . '</button>';
    $myBankButton = apply_filters(MyConstants::PREFIX . '_modify_button', $htmlButton, $atts);

    $encUtil = MigsUtilities::getEncUtility($post_id, $amount, $bs);
    $form = '<form id="' . $t . '" name="' . MyConstants::PREFIX . '_' . $t . '" method="post">'
            . $myBankButton
            . '<input type="hidden" name="merchant" value="' . $bs->merchant . '" />'
            . '<input type="hidden" name="accessCode" value="' . $bs->accesscode . '" />'
            . '<input type="hidden" name="merchTxnRef" value="' . $encUtil->merchTxnRef . '" />'
            . '<input type="hidden" name="returnURL" value="' . $encUtil->return_url . '" />'
            . '<input type="hidden" name="orderInfo" value="' . $encUtil->formba . '" />'
            . '<input type="hidden" name="post_id" value="' . $post_id . '" />'
            . '<input type="hidden" name="amount" value="' . $amount . '" />'
            . '<input type="hidden" name="formba" value="' . $encUtil->formba . '" />'
            . '<input type="hidden" name="formbc" value="' . $encUtil->formc . '" />'
            . '<input type="hidden" name="ba" value="' . $encUtil->enc . '" />'
            . '</form>';
    $form .= MigsUtilities::getMigsCachingInvaidation($post_id, $amount);
    echo $form;
}

function migs_load_my_script() {
    wp_enqueue_script('jquery');
}

add_action('wp_enqueue_scripts', 'migs_load_my_script');

add_shortcode('my_migs_button', 'get_' . MyConstants::PREFIX . '_button');
add_shortcode('my_migs_return_url', 'get_' . MyConstants::PREFIX . '_return_url');
