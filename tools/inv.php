<?php
include '../include/class.inc.php';


$query = "SELECT robots.id FROM `check` INNER JOIN robots ON check.robot = robots.id WHERE id_check != 105";
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
    echo $line['id']." ";
    //$writeoff->del_writeoff($line['id']);

}