<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

Class MigsPaymentGatewayServlet {

    const GETAPPLICATIONSBYADMIN = 1;

}

add_action('wp_ajax_my_bank_action', 'my_bank_action_callback');

function my_bank_action_callback() {
    global $wpdb;
    global $userdata;
    $specialnonce = $userdata->user_login . $userdata->user_pass;
    check_ajax_referer($specialnonce, 'security');
    get_currentuserinfo();
    $caneditmigspaymentgatewaysettings = user_can($userdata, 'manage_options');

    if ($caneditmigspaymentgatewaysettings) {
        if (isset($_GET['getexport'])) {
            $aColumns = "";
            $order = "";
            switch (intval($_GET['getexport'])) {
                case 1:
                    $grid = MigsPaymentGatewayPaymentLogs::getGridOptions();
                    $aColumns = $grid->grid_columns;
                    $table_name = $grid->table_name;
                    $title = "exporting_paymentlogs";
                    $order = "ORDER BY id DESC";
                    break;
            }
            if (!empty($aColumns)) {
                $filename = "excelreport" . $title . ".xls";
                $t_separated = "";
                for ($i = 0; $i < count($aColumns); $i++) {
                    $row = "";
                    $aCol = trim($aColumns[$i]);
                    if (strpos($aCol, 'AS ') !== false) {
                        $a = explode('AS', $aCol);
                        $aNewCol = trim(str_replace(",", " ", $a[1]));
                        $row = $aNewCol;
                    } else if ($aCol != ' ') {
                        /* General output */
                        $row = $aCol;
                    }
                    $t_separated .= "\t" . $row;
                }
                $contents = $t_separated . "\r\n";
                $sQuery = "SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . " FROM " . $table_name . " " . $order;
                $rResult = $wpdb->get_results($sQuery, ARRAY_A);
                $x = 0;
                foreach ($rResult as $row) {
                    $contents .= implode("\t", array_values($row)) . "\r\n";
                }
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename=' . $filename);
                echo $contents;
                exit();
            }
        }
    }
    $type = $_POST['typeofcall'];
    if (intval($type)) {
        switch ($type) {
            case MigsPaymentGatewayServlet::GETAPPLICATIONSBYADMIN:
                if ($caneditmigspaymentgatewaysettings) {
                    $grid = MigsPaymentGatewayPaymentLogs::getGridOptions();
                    include(MYMIGSPAYMENTGATEWAYPATH . "/datatables/ServerSideWrapper.php");
                    exit;
                }
                break;
        }
    }
}
