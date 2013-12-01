<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

function getSpecialFieldName($aCol, $emptyField = false) {
    $aNewCol = $aCol;
    if (strpos($aCol, 'AS ') !== false) {
        $a = explode('AS', $aCol);
        $aNewCol = trim(str_replace(",", " ", $a[1]));
        if ($emptyField) {
            $aNewCol = "";
        }
    }
    return $aNewCol;
}

global $wpdb;
$aColumns = $grid->grid_columns;
$sIndexColumn = $grid->grid_index;
$sTable = $grid->table_name;
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
 */

/*
 * Local functions
 */

function fatal_error($sErrorMessage = '') {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
    die($sErrorMessage);
}

/*
 * Paging
 */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . intval($_POST['iDisplayStart']) . ", " .
            intval($_POST['iDisplayLength']);
}


/*
 * Ordering
 */
$sOrder = "";
if (isset($_POST['iSortCol_0'])) {
    $sOrder = "ORDER BY  ";
    for ($i = 0; $i < intval($_POST['iSortingCols']); $i++) {
        if ($_POST['bSortable_' . intval($_POST['iSortCol_' . $i])] == "true") {
            $sOrder .= "`" . getSpecialFieldName($aColumns[intval($_POST['iSortCol_' . $i])], false) . "` " .
                    ($_POST['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc') . ", ";
        }
    }

    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = "";
    }
}


/*
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sWhere = "";
if (isset($_POST['sSearch']) && $_POST['sSearch'] != "") {
    $sWhere = "WHERE (";
    for ($i = 0; $i < count($aColumns); $i++) {
        if (isset($_POST['bSearchable_' . $i]) && $_POST['bSearchable_' . $i] == "true") {
            $fieldName = getSpecialFieldName($aColumns[$i], true);
            if (!empty($fieldName)) {
                $sWhere .= "`" . $fieldName . "` LIKE '%" . mysql_real_escape_string($_POST['sSearch']) . "%' OR ";
            }
        }
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < count($aColumns); $i++) {
    if (isset($_POST['bSearchable_' . $i]) && $_POST['bSearchable_' . $i] == "true" && $_POST['sSearch_' . $i] != '') {
        if ($sWhere == "") {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        $fieldName = getSpecialFieldName($aColumns[$i], true);
        if (!empty($fieldName)) {
            $sWhere .= "`" . $fieldName . "` LIKE '%" . mysql_real_escape_string($_POST['sSearch_' . $i]) . "%' ";
        }
    }
}


/*
 * SQL queries
 * Get data to display
 */
$sQuery = "SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . " FROM $sTable $sWhere $sOrder $sLimit";
$rResult = $wpdb->get_results($sQuery, ARRAY_A);

/* Data set length after filtering */
$sQuery = "SELECT FOUND_ROWS()";
$iFilteredTotal = $wpdb->get_var($sQuery);

/* Total data set length */
$sQuery = "SELECT COUNT(`" . $sIndexColumn . "`) FROM   $sTable";
$iTotal = $wpdb->get_var($sQuery);

/*
 * Output
 */
$output = array(
    "sEcho" => intval($_POST['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $aCol = trim($aColumns[$i]);
        if (strpos($aCol, 'AS ') !== false) {
            $a = explode('AS', $aCol);
            $aNewCol = trim(str_replace(",", " ", $a[1]));
            $row[] = $aRow[$aNewCol];
        } else if ($aCol == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aCol] == "0") ? '-' : $aRow[$aCol];
        } else if ($aCol != ' ') {
            /* General output */
            $row[] = $aRow[$aCol];
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>