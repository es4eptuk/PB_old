<?php

include 'include/class.inc.php';

global $database_server, $database_user, $database_password, $dbase;
$dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
$pdo = new PDO($dsn, $database_user, $database_password, $opt);


$arr_writeoff_pos = Array();
$arr_total_pos = Array();

$query = "SELECT robots.id, robots.version, robots.number FROM `check` INNER JOIN robots ON check.robot = robots.id WHERE (check.id_check = 105 OR check.id_check = 314 ) AND check.check = 0 AND robots.delete = 0  AND (robots.progress > 0 AND robots.progress < 100) ORDER BY robots.version ASC";
$result = $pdo->query($query);
$total_price = 0;
while ($line = $result->fetch()) {
    echo "<b><br>".$line['version'].".".$line['number']."</b><br>";
    $id_robot = $line['id'];
    $query2 = "SELECT id,description FROM `writeoff` WHERE `robot` = $id_robot";
    $result2 = $pdo->query($query2);
    echo "<table border='1'>";
    echo "<tr><td><b>Артикул</b></td><td><b>Наименование</b></td><td><b>Количество</b></td><td><b>Сумма</b></td></tr>";
    while ($line2 = $result2->fetch()) {
        //echo $line2['description']. "(".$line2['id'].") <br>";
        $id_writeoff = $line2['id'];
        $query3 = "SELECT writeoff_items.pos_id, writeoff_items.pos_count, pos_items.vendor_code, pos_items.title , pos_items.price FROM `writeoff_items` INNER JOIN pos_items ON writeoff_items.pos_id = pos_items.id WHERE `writeoff_id` = $id_writeoff";
        $result3 = $pdo->query($query3);



        while ($line3 = $result3->fetch()) {
            $id_pos = $line3['pos_id'];
            $count_pos = $line3['pos_count'];
            $price_pos = $line3['price'];
            $price = $count_pos*$price_pos;
            $total_price += $price;

            $title = $line3['title'];
            $vendor_code = $line3['vendor_code'];
            if (!isset($arr_writeoff_pos[$id_pos])) $arr_writeoff_pos[$id_pos] = 0;
            $arr_writeoff_pos[$id_pos] += $count_pos;
            // echo $line3['pos_id']. " - ".$count_pos." - ".$arr_writeoff_pos[$id_pos]."<br>";
            echo "<tr ><td>$vendor_code</td><td>$title</td><td>$count_pos</td><td>$price</td></tr>";
        }
        //print_r($arr_writeoff_pos);
    }
    //$writeoff->del_writeoff($line['id']);

}
echo "</table>";

echo "Общая сумма: ".number_format($total_price, 2, ',', ' ')." рублей";
/*$query4 = "SELECT id,title,vendor_code,total FROM `pos_items` ORDER BY `id` ASC";
$result4 = mysql_query($query4) or die('Запрос не удался: ' . mysql_error());
echo "<table border='1'>";
echo "<tr><td><b>Артикул</b></td><td><b>Наименование</b></td><td><b>На складе</b></td><td><b>С учетом незавершенных роботов</b></td></tr>";
while ($line4 = mysql_fetch_array($result4, MYSQL_ASSOC)) {
    $id_pos = $line4['id'];
    $title = $line4['title'];
    $vendor_code = $line4['vendor_code'];
    $total = $line4['total'];
    $delta = 0;
    if (isset($arr_writeoff_pos[$id_pos])) $delta = $arr_writeoff_pos[$id_pos];
    $style = "";
    $unfinish = $total + $delta;

    if ($unfinish != $total) $style = "background-color: #ff00b147;";

    echo "<tr style='" . $style . "'><td>$vendor_code</td><td>$title</td><td>$total</td><td>$unfinish</td></tr>";
}
echo "</table>";
print_r($arr_writeoff_pos);
//echo count($arr_writeoff_pos);
*/