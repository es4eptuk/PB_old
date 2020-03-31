<?php
include 'include/class.inc.php';
$id = $_GET['id'];

$query = "SELECT * FROM `writeoff` WHERE `robot` = $id AND `update_date` > '2019-10-01 00:00:00' AND `update_date` < '2019-11-30 00:00:00'";
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());


while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
    echo $line['id']." ";
    $writeoff->del_writeoff($line['id']);

}







?>