<?php

return function() {
    $db_conn = require_once('database.php');

    $query  = "CREATE TABLE IF NOT EXISTS `files` (";
    $query .= "  `pathname` varchar(400) NOT NULL,";
    $query .= "  `pad_id` varchar(400) NOT NULL,";
    $query .= "  `status` int(11) NOT NULL DEFAULT '0',";
    $query .= "  PRIMARY KEY (`pathname`),";
    $query .= "  UNIQUE KEY `pad_id` (`pad_id`)";
    $query .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1;";

    $res = mysqli_query($db_conn, $query);
    if ($res === false) {
        return mysqli_error($db_conn);
    }
    return true;
};
