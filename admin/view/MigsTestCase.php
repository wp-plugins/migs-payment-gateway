<?php
/*
  -	npaymentTestForm.php
  -		Description:
  - Submit a payment transaction to the BANQUE AUDI PAYMENT SERVER.
  - Retrieves a payment transaction status from the BANQUE AUDI PAYMENT SERVER.
  -
  -
  - @AUTHOR TAGLOGIC OFFSHORE
  -
  - NOTE: IMPROPER USE OF THIS CODE MIGHT LEAD TO SYSTEM MALFUNCTION AND INSTABILITY.
  -		TAGLOGIC OFFSHORE IS NOT HELD RESPONSIBLE FOR ANY MISUSE OF THIS CODE.
  -		PLEASE CONSULT WITH TAGLOGIC OFFSHORE FOR ANY QUESTIONS / SUGGESTIONS / CHANGES to this code.
  - COPYRIGHT 2007
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

/*
 * This test case intend to minimize the issues and make sure the user account is running smoothly.
 */
$bs = new MigsPaymentGatewaySettings;
$bs = $bs->getBankSettings();

$SECURE_SECRET = $bs->secure_secret;
$appendAmp = 0;
$vpcURL = "";
$newHash = "";

// if the form is submitted undergo the below procedures
if (isset($_POST['accessCode'])) {
    ksort($_POST);
    $md5HashData = $SECURE_SECRET;

    foreach ($_POST as $key => $value) {
        // create the md5 input and URL leaving out any fields that have no value
        if (strlen($value) > 0 && ($key == 'accessCode' || $key == 'merchTxnRef' || $key == 'merchant' || $key == 'orderInfo' || $key == 'amount' || $key == 'returnURL')) {
            print 'Key: ' . $key . '  Value: ' . $value . "<br>";
            // this ensures the first paramter of the URL is preceded by the '?' char
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
    echo "<script language=\"javascript\">top.location.href='https://gw1.audicards.com/TPGWeb/payment/prepayment.action?$newHash'</script>";
    //exit;
}
?>
<h4>If your using a production account this is <span style="color:red;weight:bold;">NOT TEST</span></h4>
<!-- The "Pay Now!" button submits the form, transferring control to the page detailed below -->
<form action="" method="post">
    <!-- The secure hash hidden field -->

    <!-- get user input -->
    <table border="0" cellpadding='0' cellspacing='0' align="center">
        <tr>
            <td align="right"><strong><em>Merchant AccessCode: </em></strong></td>
            <td><input name="accessCode" value="<?php echo $bs->accesscode; ?>" size="20" maxlength="8"/></td>
        </tr>
        <tr class="shade">
            <td align="right"><strong><em>Merchant Transaction Reference: </em></strong></td>
            <td><input name="merchTxnRef" value="<?php echo time(); ?>" size="20" maxlength="40"/></td>
        </tr>
        <tr>
            <td align="right"><strong><em>MerchantID: </em></strong></td>
            <td><input name="merchant" value="<?php echo $bs->merchant; ?>"  size="20" maxlength="16"/></td>
        </tr>
        <tr class="shade">
            <td align="right"><strong><em>Transaction OrderInfo: </em></strong></td>
            <td><input name="orderInfo" value="<?php echo time(); ?>" size="20" maxlength="34"/></td>
        </tr>
        <tr>
            <td align="right"><strong><em>Purchase Amount: </em></strong></td>
            <td><input name="amount" value="500" maxlength="10"/></td>
        </tr>
        <tr class="shade">
            <td align="right"><strong><em>Receipt ReturnURL: </em></strong></td>
            <td><input name="returnURL" size="65" value="<?php echo get_home_url(); ?>/?action=testcase" maxlength="250"/></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" NAME="SubButL" value="Pay Now!"></td>
        </tr>  
    </table>
<?php
//check if this page is being redirected from payment client thus carrying the field vpc_TxnResponseCode
if (isset($_GET['vpc_TxnResponseCode'])) {

    //function to map each response code number to a text message	
    function getResponseDescription($responseCode) {
        return MigsUtilities::getMigsPaymentGatewayResponseDescription($responseCode);
    }

    //get secure hash value of merchant	
    //get the secure hash sent from payment client
    $vpc_Txn_Secure_Hash = addslashes($_GET["vpc_SecureHash"]);
    unset($_GET["vpc_SecureHash"]);
    ksort($_GET);
    // set a flag to indicate if hash has been validated
    $errorExists = false;
    //check if the value of response code is valid
    if (strlen($SECURE_SECRET) > 0 && addslashes($_GET["vpc_TxnResponseCode"]) != "7" && addslashes($_GET["vpc_TxnResponseCode"]) != "No Value Returned") {
        //creat an md5 variable to be compared with the passed transaction secure hash to check if url has been tampered with or not
        $md5HashData = $SECURE_SECRET;

        //creat an md5 variable to be compared with the passed transaction secure hash to check if url has been tampered with or not
        $md5HashData_2 = $SECURE_SECRET;

        // sort all the incoming vpc response fields and leave out any with no value
        foreach ($_GET as $key => $value) {
            if ($key != "vpc_SecureHash" && strlen($value) > 0 && $key != 'action') {
                $hash_value = str_replace(" ", '+', $value);
                $hash_value = str_replace("%20", '+', $hash_value);
                $md5HashData_2 .= $value;
                $md5HashData .= $hash_value;
            }
        }

        //if transaction secure hash is the same as the md5 variable created 
        if ((strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData)) || strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData_2)))) {
            $hashValidated = "<b>CORRECT</b>";
        } else {
            $hashValidated = "<b>INVALID HASH</b>";
            $errorExists = true;
        }
    } else {
        $hashValidated = "<FONT color='orange'><b>Not Calculated - No 'SECURE_SECRET' present.</b></FONT>";
    }
    //the the fields passed from the url to be displayed
    $amount = null2unknown(addslashes($_GET["amount"]) / 100);
    $locale = null2unknown(addslashes($_GET["vpc_Locale"]));
    $batchNo = null2unknown(addslashes($_GET["vpc_BatchNo"]));
    $command = null2unknown(addslashes($_GET["vpc_Command"]));
    $message = null2unknown(addslashes($_GET["vpc_Message"]));
    $version = null2unknown(addslashes($_GET["vpc_Version"]));
    $cardType = null2unknown(addslashes($_GET["vpc_Card"]));
    $orderInfo = null2unknown(addslashes($_GET["orderInfo"]));
    $receiptNo = null2unknown(addslashes($_GET["vpc_ReceiptNo"]));
    $merchantID = null2unknown(addslashes($_GET["merchant"]));
    $authorizeID = null2unknown(addslashes($_GET["vpc_AuthorizeId"]));
    $merchTxnRef = null2unknown(addslashes($_GET["merchTxnRef"]));
    $transactionNo = null2unknown(addslashes($_GET["vpc_TransactionNo"]));
    $acqResponseCode = null2unknown(addslashes($_GET["vpc_AcqResponseCode"]));
    $txnResponseCode = null2unknown(addslashes($_GET["vpc_TxnResponseCode"]));

    // Show 'Error' in title if an error condition
    $errorTxt = "";

    // Show this page as an error page if vpc_TxnResponseCode equals '7'
    if ($txnResponseCode == "7" || $txnResponseCode == "No Value Returned" || $errorExists) {
        $errorTxt = "Error ";
    }
    // This is the display title for 'Receipt' page 
    ?>
        <!-- end branding table -->
        <!-- End Branding Table -->
        <table width="85%" align="center" cellpadding="5" border="0">
            <tr>
                <td align="right"><b>Hash Validity:</b></td>
                <td class="errorMsg"><?php echo $hashValidated ?></td>
            </tr>

            <tr>
                <td align="right"><b>Merchant Transaction Reference: </b></td>
                <td><?php echo $merchTxnRef ?></td>
            </tr>
            <tr>
                <td align="right"><b>Merchant ID: </b></td>
                <td><?php echo $merchantID ?></td>
            </tr>
            <tr>
                <td align="right"><b>Order Information: </b></td>
                <td><? echo $orderInfo ?></td>
            </tr>
            <tr>
                <td align="right"><b>Purchase Amount: </b></td>
                <td><? echo $amount ?></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <hr />
                </td>
            </tr>

            <tr>
                <td colspan="2" align="center">
                    Fields above are the request values returned.<br>
                    Fields below are the response fields for a Standard Transaction.<br>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <hr />
                </td>
            </tr>            
            <tr>
                <td align="right"><b>VPC Transaction Response Code: </b></td>
                <td><? echo $txnResponseCode ?></td>
            </tr>
            <tr>
                <td align="right"><b>Transaction Response Code Description:</b></td>
                <td class="errorMsg"><? echo getResponseDescription($txnResponseCode) ?></td>
            </tr>
            <tr>
                <td align="right"><b>Message: </b></td>
                <td><? echo $message ?></td>
            </tr>
    <?php
    // only display the following fields if not an error condition
    if ($txnResponseCode != "7" && $txnResponseCode != "No Value Returned") {
        ?>
                <tr>
                    <td align="right"><b>Receipt Number: </b></td>
                    <td><?php echo $receiptNo ?></td>
                </tr>
                <tr>
                    <td align="right"><b>Transaction Number: </b></td>
                    <td><? echo $transactionNo ?></td>
                </tr>
                <tr>
                    <td align="right"><b>Acquirer Response Code: </b></td>
                    <td><? echo $acqResponseCode ?></td>
                </tr>
                <tr>
                    <td align="right"><b>Bank Authorization ID: </b></td>
                    <td><? echo $authorizeID ?></td>
                </tr>
                <tr>
                    <td align="right"><b>Batch Number: </b></td>
                    <td><? echo $batchNo ?></td>
                </tr>
                <tr>
                    <td align="right"><b>Card Type: </b></td>
                    <td><? echo $cardType ?></td>
                </tr>	
                <tr>
                    <td colspan="2"><HR /></td>
                </tr>
        <?php
    }
    ?>    
        </table>
    <?php
}
?>


