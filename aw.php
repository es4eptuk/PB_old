<?php
include 'include/class.inc.php';

//$position->set_writeoff(4,'0000');


$query = "SELECT * FROM `writeoff_items` WHERE `writeoff_id` = 175 AND `pos_count` = 0";
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

$json['0']['0'] = "Производство";
$json['0']['1'] = "Робот 4.111";
 $count          = 1;
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
           // $pos_array[] = $line;
           $idd=$line['pos_id'];
           $query2 = "SELECT count FROM `robot_equipment_items` WHERE `pos_id` = $idd AND `equipment_id` = 4";
           $result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
           $line_t = mysql_fetch_array($result2, MYSQL_ASSOC);

            $json[$count]['0'] = $line['pos_id'];
            $json[$count]['1'] = $line['vendor_code'];
            $json[$count]['2'] = $line['pos_title'];
            $json[$count]['3'] = $line_t['count'];
            $json[$count]['4'] = $line['pos_price'];
            $count++;
        }
echo count ($json);        
//$writeoff->add_writeoff(json_encode($json));       
print_r($json);
?>