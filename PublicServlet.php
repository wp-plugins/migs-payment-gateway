<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

add_action('wp_ajax_my_migs_action', 'PublicServlet::my_migs_action_callback');
add_action('wp_ajax_nopriv_my_migs_action', 'PublicServlet::my_migs_action_callback');

class PublicServlet {

    static function my_migs_action_callback() {
        global $wpdb; // this is how you get access to the database
        $bSettings = new MigsPaymentGatewaySettings();
        $bs = $bSettings->getBankSettings();
        $t = MigsUtilities::e_d($_POST['t'], MigsUtilities::decrypt);
        $post_id = MigsUtilities::e_d($_POST['pid'], MigsUtilities::decrypt);
        $amount = MigsUtilities::e_d($_POST['am'], MigsUtilities::decrypt);
        
        echo json_encode(MigsUtilities::getEncUtility($post_id, $amount, $bs));
        die(); // this is required to return a proper result
    }

}
