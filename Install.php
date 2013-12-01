<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}
global $migs_payment_gateway_db_version;
$migs_payment_gateway_db_version = "1.0";

class MyMigsPaymentGatewayPlugin {

    static function install() {
        global $wpdb;
        global $migs_payment_gateway_db_version;
        add_option(MyConstants::PREFIX . "_db_version", $migs_payment_gateway_db_version);
        add_option(MyConstants::PREFIX . "_currency", "$");

        $table_name = $wpdb->prefix . "" . MigsPaymentGatewayPaymentLogs::table_name;
        $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
        `ID` bigint(20) NOT NULL AUTO_INCREMENT,
        `post_id` bigint(20) NOT NULL,
        `user_id` bigint(20) NOT NULL,
        `amount` int(11) NOT NULL,
        `merchant_id` varchar(25) NOT NULL,
        `merchtxnref` varchar(255) NOT NULL,
        `orderinfo` varchar(255) NOT NULL,
        `ip` varchar(255) NOT NULL,
        `paid` tinyint(4) NOT NULL DEFAULT '0',
        `receiptnumber` bigint(20) NOT NULL,
        `transectionnumber` bigint(20) NOT NULL,
        `batchnumber` bigint(20) NOT NULL,
        `bankresponsedesc` varchar(255) NOT NULL,
        `dateoforder` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`ID`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    static function uninstall() {
        global $wpdb;
        $table_name = $wpdb->prefix . "" . MigsPaymentGatewayPaymentLogs::table_name;
        $sql = "DROP TABLE IF EXISTS " . $table_name;
        $wpdb->query($sql);
        delete_option(MyConstants.PREFIX . "_db_version");
        delete_option(MyConstants::PREFIX . "_secure_secret");
        delete_option(MyConstants::PREFIX . "_accesscode");
        delete_option(MyConstants::PREFIX . "_merchant");
        delete_option(MyConstants::PREFIX . "_salt_1");
        delete_option(MyConstants::PREFIX . "_salt_2");
        delete_option(MyConstants::PREFIX . "_empty_data");
        delete_option(MyConstants::PREFIX . "_noerror_data");
        delete_option(MyConstants::PREFIX . "_return_post_id");
        delete_option(MyConstants::PREFIX . "_url");
    }
}
