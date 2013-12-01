<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

class GridObject {

    var $grid_index = 0;
    var $grid_columns = array();
    var $table_name = "";

    function setGridObject($grid_index, $grid_columns, $table_name) {
        $this->grid_index = $grid_index;
        $this->grid_columns = $grid_columns;
        $this->table_name = $table_name;
        return $this;
    }

}
