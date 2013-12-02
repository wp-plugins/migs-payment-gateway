=== Plugin Name ===
Contributors: Gabriel82
Donate link: 
Tags: migs banking gateway,Master card gateway, banks gateway, visa mastercard plugin, migs
Requires at least: 3.7.1
Tested up to: 3.7.1
Stable tag: 3.7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Migs (Mastercard Internet Gateway Service) Payment Gateway gives you a chance to add Buy now buttons and create your own without coding knowledge, the plugin also has hooks and filters with reporting tools.

Greetings everyone,

It's such a pleasure to see you using WordPress and this plugin, nevertheless I do have to remind you that this plugin is "NOT A MIGS OFFICIAL PLUGIN"
and it's shared under "GNU GENERAL PUBLIC LICENSE".

So please take your time and read the license.txt especially the “NO WARRANTY” section, and remember to check your Migs portal for each transaction before processing any shipment.
If you find any bugs please report them on wordpress.org so I can try resolve them.

Regards,
Gabriel 

==

> Before Starting!!
Please make sure your account is working, use the bank test page or go to Test-Case page... If test case is returning "Not Calculated - No 'SECURE_SECRET' present." 
Please retry it on your bank test page if both gives this then contact the bank.
(Small TIP) Ask the bank to run a test of your account...

> How to add paying button to the website?
To add a new button to the website you can add this button to any page/post:
[my_migs_button amount="100"]
In php code:
`do_shortcode('[my_migs_button amount="100"]')`

> As you notice the default text is "Buy now" to change this:
[my_migs_button amount="100" text="Buy new item!"]
In php code:
`do_shortcode('[my_migs_button amount="100" text="Buy new item!"]')`

> To add a css class:
[my_migs_button amount="100" cssclass="somecolors"]
In php code:
do_shortcode('[my_migs_button amount="100" cssclass="somecolors"]')

> To add a predefined post id (This can be used):
[my_migs_button amount="100" post_id="1"]
In php code:
`do_shortcode('[my_migs_button amount="100" post_id="1"]')`

> To show price in button:
[my_migs_button amount="100" showprice="1"]
In php code:
`do_shortcode('[my_migs_button amount="100" showprice="1"]')`

> Show button for logged in only in users:
[my_migs_button amount="100" loggedinonly="1"]
In php code:
`do_shortcode('[my_migs_button amount="100" loggedinonly="1"]')`

> The amount is required!!!
For more advanced options with the button please use the "migs_payment_gateway_modify_button" filter.

How to set a custom return url: (this is not obligatory)
1- Create a new page/post (Depends on your needs and design).
2- Paste this code in the page [my_migs_return_url]
3- Copy the page ID to Settings page

The return url will be updated to the new page/post.

= List of action hooks: =
* migs_payment_gateway_update_on_return
* migs_payment_gateway_send_admin_email
* migs_payment_gateway_insert_to_logs
* migs_payment_gateway_settings_updated
* migs_payment_gateway_get_settings

= List of action hooks: =
* migs_payment_gateway_update_on_return
* migs_payment_gateway_send_admin_email
* migs_payment_gateway_insert_to_logs
* migs_payment_gateway_settings_updated
* migs_payment_gateway_get_settings

Example: How to add action hooks to this plugin?
This code can be added in functions.php in the theme folder.

function bank_transection_return($obj) {
    if ($obj->updated > 0){
        //When it comes here it means the product was successfully updated and paid.
        print_r($obj);
    }else{
        //When it comes here it means the product was not updated but that does not mean the product was not paid.
        echo "Did not update? ";
    }
}
add_action('migs_payment_gateway_update_on_return', 'bank_transection_return', 10 , 1);

> List of filter hooks:
migs_payment_gateway_modify_button
    
> How to add filter hooks to the plugin?
This code can be added in functions.php in the theme folder.

function migs_payment_gateway_modify_button_html( $html, $atts ) {
    //This filter you can change the buttons on the site
    //print_r($atts) Will return Array ( [amount] => 100 [text] => Buy new shoes ) 
    return '<div class="extra_div">' . $html . '</div>';
}
add_filter( 'migs_payment_gateway_modify_button', 'migs_payment_gateway_modify_button_html', 10, 2);

== Installation ==
You can download and install Migs payment gateway using the built in WordPress plugin installer. If you download Migs Payment Gateway manually, make sure it is uploaded to "/wp-content/plugins/migs_payment_gateway/".

Activate Migs payment gateway in the "Plugins" admin panel using the "Activate" link. You'll then see a message asking you to complete the Migs payment gateway Installation Settings, which will guide you through configuring your site for Migs payment gateway.

== Screenshots ==

1. This page in exists in wp-admin, Check transactions allows you to see every transaction made on your website.
2. Changing the settings of the Migs payment.
3. How to add shortcodes easily from the text editor.
4. Test your account.

== Shortcode ==

Migs Payment Gateway introduces shortcodes. 

If you do want to work with shortcodes. 

**<a href="http://www.yourwwwdesign.com/migs-payment-gateway/#shortcodes">Learn About Migs Payment Gateway Shortcodes</a>**

== Frequently Asked Questions ==

= Can this plug-in work as shopping card? =

Nop, this plugin can be used to add buttons to your wordpress.

= Can I retrieve the data after returning from the payment? =

Yes, you just have to use this action: add_action('migs_payment_gateway_update_on_return', 'your_bank_transection_return', 10 , 1), your_bank_transection_return is the function that you can customize.

= Does this plugin support shortcodes? =

Yes, its based on shortcodes.

== Changelog ==

= 1.0 =
