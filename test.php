<?php
include 'include/class.inc.php';




$robots = [553,558,559,560,561,562,563,564,565,566,567,568,568,569,571,582];
$robots = [206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221];


$robot_id = 570;
$robot_number = 218;

$query = "SELECT * FROM `check` WHERE `robot` = $robot_id AND `id_kit` != 0";
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());


while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
    //echo $line['id']." ";
    //$writeoff->del_writeoff($line['id']);
    echo $robot_id."<br>";
    echo $line['id_kit']." ";


    //$position->set_writeoff_kit(4,$robot_number,$line['id_kit'],$line['id_check'],$robot_id);
}

$mail->send('Екатерина Старцева',  'cto@promo-bot.ru', 'Списание на робота ', 'Пройдите по ссылке для просмотра списания https://db.promo-bot.ru/new/edit_writeoff_on_robot.php?id=');


?>

