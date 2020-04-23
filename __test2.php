<?php
include 'include/class.inc.php';
$query = "SELECT * FROM `check_items`";
 $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $id_check = $line['id'];
            $id_kit= $line['kit'];
            if ($id_kit != 0) {
            $query2 = "UPDATE `check` SET `id_kit` = $id_kit WHERE `id_check` = $id_check";
            echo $query2."<br>";
            $result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
            }
           
        }