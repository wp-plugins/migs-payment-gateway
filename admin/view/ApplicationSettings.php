<?php
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}
if (is_admin()) {
    $bs = new MigsPaymentGatewaySettings;
    $bsSettings = $bs->getBankSettings();
    $nonce = wp_create_nonce("settings_" . $bs->getHashedPassword());
    if (($_SERVER["REQUEST_METHOD"] == "POST") && ((isset($_POST['banksettingsform'])))) {
        if ((!empty($_POST['secure_secret'])) && (!empty($_POST['accesscode'])) && (!empty($_POST['merchant']))) {
            $bs->secure_secret = $_POST['secure_secret'];
            $bs->accesscode = $_POST['accesscode'];
            $bs->merchant = $_POST['merchant'];
            $salt_1 = $_POST['salt_1'];
            $salt_2 = $_POST['salt_2'];
            $return_post_id = 0;
            if (isset($_POST['return_post_id']))
                $return_post_id = intval($_POST['return_post_id']);

            $t = time();
            if (empty($salt_1)) {
                $salt_1 = strtoupper(md5($bs->secure_secret . $t));
            }
            if (empty($salt_2)) {
                $salt_2 = strtoupper(md5($t . $bs->secure_secret));
            }
            $bs->salt_1 = $salt_1;
            $bs->salt_2 = $salt_2;
            $bs->empty_data = $_POST[''.MyConstants::PREFIX.'_empty_data'];
            $bs->noerror_data = $_POST[''.MyConstants::PREFIX.'_noerror_data'];
            $bs->return_post_id = $return_post_id;
            $bs->url = $_POST[''.MyConstants::PREFIX.'_url'];
            $bs->currency = $_POST['currency'];;
            $bs->updateSettings();
            MigsUtilities::reloadMigsPaymentGatewayPage(MigsUtilities::curPageURL(), "Your settings are successfully updated!");
        } else {
            echo Notification::Notify("All fields are required...");
        }
    } else {
        if (isset($_POST['banksettingsform'])) {
            echo Notification::Notify("Something went wrong...");
        }
    }
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/css/main.css" />
    <script type="text/javascript" charset="utf-8" src="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/js/validetta-min.js"></script>
    <script type="text/javascript" charset="utf-8">
        jQuery(function(){
            jQuery('#migs-settings').validetta({realTime : true});
        });
    </script>
    <h2>Migs settings</h2>
    <form id="migs-settings" method="post" action="">
        <table class="headlineinfo">
            <tr valign="top">
                <th scope="row">Migs URL: * <br />(For example: https://gw1.audicards.com/TPGWeb/payment/prepayment.action?)</th>
                <td><input type="text" name="<?php echo MyConstants::PREFIX; ?>_url" value="<?php echo $bsSettings->url; ?>" data-validetta="required" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Merchant ID: *</th>
                <td><input type="text" name="merchant" value="<?php echo $bsSettings->merchant; ?>" data-validetta="required" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Access code: *</th>
                <td><input type="text" name="accesscode" value="<?php echo $bsSettings->accesscode; ?>" data-validetta="required" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Secure secret: *</th>
                <td><input type="text" name="secure_secret" value="<?php echo $bsSettings->secure_secret; ?>" data-validetta="required" size="50" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Salt 1: (For better security)</th>
                <td><input type="text" name="salt_1" value="<?php echo $bsSettings->salt_1; ?>" size="50" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Salt 2: (For better security)</th>
                <td><input type="text" name="salt_2" value="<?php echo $bsSettings->salt_2; ?>" size="50" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Payment page <br /> (Payment successfully)</th>
                <td><?php wp_editor($bsSettings->noerror_data, MyConstants::PREFIX . "_noerror_data"); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">Payment page <br /> (Error exists)</th>
                <td><?php wp_editor($bsSettings->empty_data, MyConstants::PREFIX . "_empty_data"); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">Return page/post ID <br /> (Leave empty or 0 if you do not have a return page)</th>
                <td><a href="#pageid"></a><input type="text" name="return_post_id" value="<?php echo $bsSettings->return_post_id; ?>" size="5" />
                    <h5>To setup a custom return page, Add new page and paste in it <span class="red">[my_migs_return_url]</span>
                        The post/page ID can be found by clicking on edit post/page in the link you can find it:<br/>
                        <img src="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/images/screenpost.jpg" alt="" />
                    </h5>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Currency sign (default $)</th>
                <td>
                    <input name="currency" type="text" maxlength="2" size="2" value="<?php echo get_option(MyConstants::PREFIX . "_currency"); ?>"></input>
                </td>
            </tr>
        </table>
        <input type="hidden" name="banksettingsform" value="<?php echo $nonce; ?>" />
        <?php submit_button(); ?>
    </form>
    <?php
}
