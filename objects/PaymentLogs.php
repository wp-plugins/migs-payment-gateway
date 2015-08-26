<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}
/*
 * Migs settings object
 */

class MigsPaymentGatewayPaymentLogs {

    const table_name = "migs_payment_gateway_payment_logs";

    var $id = 0;
    var $post_id = 0;
    var $user_id = 0;
    var $amount = 0;
    var $merchant_id = "";
    var $merchtxnref = "";
    var $orderinfo = "";
    var $ip = "";
    var $paid = 0;
    var $receiptnumber = 0;
    var $transectionnumber = 0;
    var $batchnumber = 0;
    var $bankresponsedesc = "";
    //Not gonna be used for insert or update
    var $dateoforder = "";
    //Not in database field
    var $updated = 0;

    static function getGridOptions() {
        global $wpdb;
        $gridObject = new GridObject();
        $tablename = $wpdb->prefix . "" . self::table_name;
        $userTable = $wpdb->prefix . "users";
        $userQuery = " (SELECT " . $userTable . ".user_login from " . $userTable . " WHERE " . $userTable . ".ID = " . $tablename . ".user_id) AS user_login, ";
        $postTable = $wpdb->prefix . "posts";
        $postQuery = " (SELECT " . $postTable . ".post_title from " . $postTable . " WHERE " . $postTable . ".ID = " . $tablename . ".post_id) AS post_title, ";
        $paidQuery = " if(" . $tablename . ".paid=1,'Paid','Not Paid') AS STATUS, ";
        return $gridObject->setGridObject("id", array('ID', $paidQuery, $postQuery, $userQuery, 'CONCAT("'.MigsPaymentGatewaySettings::getCurrency().'", FORMAT(amount, 2)) AS amount', 'merchtxnref', 'ip', 'receiptnumber', 'transectionnumber', 'batchnumber', 'bankresponsedesc', 'dateoforder'), $tablename);
    }

    static function updateOnReturn($paid, $bpl) {
        $bpl->paid = $paid;
        $updated = $bpl->update();
        $bpl = $bpl->getPaymentLogsByMerchentTxtnRef($bpl->merchtxnref);
        $bpl->updated = $updated;
        do_action(MyConstants::PREFIX . '_update_on_return', $bpl);
        return $bpl->updated;
    }

    function insertLogs() {
        global $wpdb;
        $wpdb->query($wpdb->prepare("INSERT INTO " . $wpdb->prefix . self::table_name . " "
                        . "(post_id,user_id,amount,merchant_id,merchtxnref,orderinfo,IP) VALUES (%d, %d, %d, %s, %s, %s, %s)", $this->post_id, $this->user_id, $this->amount, $this->merchant_id, $this->merchtxnref, $this->orderinfo, $this->ip));

        do_action(MyConstants::PREFIX.'_insert_to_logs', $this);
    }

    function update() {
        global $wpdb;
        $data = $wpdb->query("UPDATE " . $wpdb->prefix . self::table_name . " "
                . "SET paid = " . $this->paid . ",receiptnumber='" . $this->receiptnumber . "',transectionnumber='" . $this->transectionnumber . "',"
                . "batchnumber='" . $this->batchnumber . "',bankresponsedesc='" . $this->bankresponsedesc . "'  "
                . "WHERE post_id = " . $this->post_id . " AND merchtxnref = '" . $this->merchtxnref . "'");
        return $data;
    }

    function getPaymentLogsByMerchentTxtnRef($merchTxnRef) {
        global $wpdb;
        $mypayment = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . self::table_name . " WHERE merchtxnref = '" . $merchTxnRef . "'");
        return $mypayment;
    }
    
    public static function getPaymentLogsCharts($paid) {
        global $wpdb;
        $myrows = $wpdb->get_results("SELECT MONTHNAME(dateoforder) AS month, CONCAT('".MigsPaymentGatewaySettings::getCurrency()."', FORMAT(SUM(amount), 2)) AS monthsum
        FROM `" . $wpdb->prefix . self::table_name . "`
        WHERE dateoforder  <= LAST_DAY(CURDATE()) AND dateoforder >= LAST_DAY(CURDATE()) - INTERVAL 12 MONTH AND `paid` = ".$paid."
        GROUP BY YEAR(dateoforder), MONTH(dateoforder)
        ORDER BY dateoforder ASC", OBJECT);
        return $myrows;
    }

}
