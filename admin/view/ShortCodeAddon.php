<?php
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

/**
Hook into WordPress
*/
add_action('init', 'mymigspaymentgatewaylink_button');

/**
Create Our Initialization Function
*/
 
function mymigspaymentgatewaylink_button() {
 
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
     return;
   }
 
   if ( get_user_option('rich_editing') == 'true' ) {
     add_filter( 'mce_external_plugins', 'add_plugin' );
     add_filter( 'mce_buttons', 'register_button' );
   }
 
}

/**
Register Button
*/
 
function register_button( $buttons ) {
 array_push( $buttons, "|", "mymigspaymentgatewaylink" );
 return $buttons;
}

/**
Register TinyMCE Plugin
*/
 
function add_plugin( $plugin_array ) {
   $plugin_array['mymigspaymentgatewaylink'] = MYMIGSPAYMENTGATEWAYURL . '/static/js/mybuttons.js';
   return $plugin_array;
}