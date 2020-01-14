<?
include 'include/class.inc.php';
$arr_writeoff_pos = Array();
$arr_total_pos = Array();

$query = "SELECT robots.id, robots.version, robots.number FROM `check` INNER JOIN robots ON check.robot = robots.id WHERE check.id_check = 105 AND check.check = 0 AND robots.delete = 0  AND (robots.progress > 0 AND robots.progress < 100) ";
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
    //echo "<b><br>".$line['version'].".".$line['number']."</b><br>";
    $id_robot = $line['id'];
    $query2 = "SELECT id,description FROM `writeoff` WHERE `robot` = $id_robot";
    $result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
    while( $line2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
        //echo $line2['description']. "(".$line2['id'].") <br>";
        $id_writeoff = $line2['id'];
        $query3 = "SELECT pos_id, pos_count FROM `writeoff_items` WHERE `writeoff_id` = $id_writeoff";
        $result3 = mysql_query($query3) or die('Запрос не удался: ' . mysql_error());
        while( $line3 = mysql_fetch_array($result3, MYSQL_ASSOC)) {
        $id_pos =    $line3['pos_id'];
        $count_pos = $line3['pos_count'];

        if(!isset( $arr_writeoff_pos[$id_pos]))  $arr_writeoff_pos[$id_pos] = 0;
        $arr_writeoff_pos[$id_pos] += $count_pos;
        // echo $line3['pos_id']. " - ".$count_pos." - ".$arr_writeoff_pos[$id_pos]."<br>";
        }
        //print_r($arr_writeoff_pos);
    }
    //$writeoff->del_writeoff($line['id']);

}

$query4 = "SELECT id,title,vendor_code,total FROM `pos_items` ORDER BY `id` ASC";
$result4 = mysql_query($query4) or die('Запрос не удался: ' . mysql_error());
echo "<table border='1'>";
echo "<tr><td><b>Артикул</b></td><td><b>Наименование</b></td><td><b>На складе</b></td><td><b>С учетом незавершенных роботов</b></td></tr>";
while( $line4 = mysql_fetch_array($result4, MYSQL_ASSOC)){
   $id_pos =  $line4['id'];
   $title = $line4['title'];
   $vendor_code = $line4['vendor_code'];
   $total = $line4['total'];
   $delta = 0;
   if(isset($arr_writeoff_pos[$id_pos])) $delta = $arr_writeoff_pos[$id_pos];
   $style = "";
   $unfinish = $total+$delta;

   if ($unfinish!=$total) $style = "background-color: #ff00b147;";

   echo "<tr style='".$style."'><td>$vendor_code</td><td>$title</td><td>$total</td><td>$unfinish</td></tr>";
}
echo "</table>";
print_r($arr_writeoff_pos);
//echo count($arr_writeoff_pos);