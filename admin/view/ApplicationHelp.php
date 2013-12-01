<?php
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/css/main.css" />
<div class="headlineinfo bold">
    Greetings everyone,<br/><br/>

    It's such a pleasure to see you using WordPress and this plugin, nevertheless I do have to remind you that this plugin is <span class="red">"NOT A MIGS OFFICIAL PLUGIN"</span> and it's shared under "GNU GENERAL PUBLIC LICENSE".
    <br/>
    So please take your time and read the license.txt especially the “NO WARRANTY” section, and remember to check your Migs portal for each transaction before processing any shipment.
    If you find any bugs please report them on wordpress.org so I can resolve them.
    <br /><br/>
    Regards,
    Gabriel 

</div>
<div class="headlineinfo">
    <h3>Before Starting!!</h3>
    <h5>Please make sure your account is working, use the bank test page or go to <a href="admin.php?page=bank-migs-testcase">Test-Case page</a>...
    If test case is returning "Not Calculated - No 'SECURE_SECRET' present." Please retry it on your bank test page if both gives this then contact the bank.
    <br />
    (Small TIP) Ask the bank to run a test of your account... (I heard a lot of "Check your encryption and other advises")
    </h5>
    <h3>How to add paying button to the website:</h3>
    <h5>To add a new button to the website you can add this button to any page/post:</h5>
    <textarea cols="100" rows="1">[my_migs_button amount="100"]</textarea>
    <br/>In php code:<br/>
    <textarea cols="100" rows="1">do_shortcode('[my_migs_button amount="100"]')</textarea>
    <h5>As you notice the default text is "Buy now" to change this:</h5>
    <textarea cols="100" rows="1">[my_migs_button amount="100" text="Buy new item!"]</textarea>
    <br/>In php code:<br/>
    <textarea cols="100" rows="1">do_shortcode('[my_migs_button amount="100" text="Buy new item!"]');</textarea>
    <h5>To add a css class:</h5>
    <textarea cols="100" rows="1">[my_migs_button amount="100" cssclass="somecolors"]</textarea>
    <br/>In php code:<br/>
    <textarea cols="100" rows="1">do_shortcode('[my_migs_button amount="100" cssclass="somecolors"]');</textarea>
    <h5>To add a predefined post id (This can be used to link multiple buttons under one button):</h5>
    <textarea cols="100" rows="1">[my_migs_button amount="100" post_id="1"]</textarea>
    <br/>In php code:<br/>
    <textarea cols="100" rows="1">do_shortcode('[my_migs_button amount="100" post_id="1"]');</textarea>
    <h5>To show price in button:</h5>
    <textarea cols="100" rows="1">[my_migs_button amount="100" showprice="1"]</textarea>
    <br/>In php code:<br/>
    <textarea cols="100" rows="1">do_shortcode('[my_migs_button amount="100" showprice="1"]');</textarea>
    <h5>Show button for logged in only in users:</h5>
    <textarea cols="100" rows="1">[my_migs_button amount="100" loggedinonly="1"]</textarea>
    <br/>In php code:<br/>
    <textarea cols="100" rows="1">do_shortcode('[my_migs_button amount="100" loggedinonly="1"]');</textarea>
    <div class="red bold">The amount is required!!!</div>
    <div class="bold">For more advanced options with the button please use the "migs_payment_gateway_modify_button" filter, you can find a sample code in <a href="#tips">this page</a>.</div>
</div>

<div class="headlineinfo">
    <h3>How to set a custom return url: (this is not obligatory)</h3>
    <ul>
        <li>1- Create a new page/post (Depends on your needs and design).</li>
        <li>2- Paste this code in the page <b> [my_migs_return_url] </b> (You can use php shortcode for this <strong>&#60;?php echo do_shortcode("[my_migs_return_url]"); ?&#62;</strong> )</li>
        <li>3- Copy the page ID to <a href="admin.php?page=bank-migs-settings#pageid">Settings page</a></li>
    </ul>
</div>

<div class="headlineinfo">
    <h3>List of action hooks:</h3>
    <ul>
        <li>migs_payment_gateway_update_on_return</li>
        <li>migs_payment_gateway_send_admin_email</li>
        <li>migs_payment_gateway_insert_to_logs</li>
        <li>migs_payment_gateway_settings_updated</li>
        <li>migs_payment_gateway_get_settings</li>
    </ul>


    <h4>Example: How to add action hooks to this plugin?</h4>
    <h5>This code can be added in functions.php in the theme folder.</h5>
    <textarea cols="150" rows="10">
    function bank_transection_return($obj) {
        if ($obj->paid == 1){
            //When it comes here it means the product was successfully updated and paid.
            print_r($obj);
        }else{
            //When it comes here it means the product was not updated but that does not mean the product was not paid.
            echo "Did not update? ";
        }
    }
    add_action('migs_payment_gateway_update_on_return', 'bank_transection_return', 10 , 1);
    </textarea>
</div>
<div class="headlineinfo">
    <h3><a id="tips"></a>List of filter hooks:</h3>
    <ul>
        <li>migs_payment_gateway_modify_button</li>
    </ul>

    <h4>How to add filter hooks to the plugin?</h4>
    <h5>This code can be added in functions.php in the theme folder.</h5>
    <textarea cols="150" rows="8">
    function migs_payment_gateway_modify_button_html( $html, $atts ) {
        //This filter you can change the buttons on the site
        //print_r($atts) Will return Array ( [amount] => 100 [text] => Buy new shoes ) 
        return '<div class="extra_div">' . $html . '</div>';
    }
    add_filter( 'migs_payment_gateway_modify_button', 'migs_payment_gateway_modify_button_html', 10, 2);
    </textarea>
</div>
<?php
?>