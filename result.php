<?
include 'include/class.inc.php';
$id_pos = 125;
//$pos_assambly = $position->get_pos_in_assembly($id_pos);

$query = "SELECT robots.id FROM `check` INNER JOIN robots ON check.robot = robots.id WHERE check.update_date >= '2019-09-01 00:00:00' AND id_check = 105";
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
echo "<table border=1>";
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
           // $id_assembly = $line['id_assembly'];
            //$kit_array_count[$id_pos][$id_kit0] = $line['count'];
            $id = $line['id'];
            $query2 = "SELECT robots.version, robots.number, robots.name, tickets.description, tickets.result_description, tickets.date_create FROM tickets INNER JOIN robots ON tickets.robot = robots.id WHERE tickets.robot = $id AND tickets.date_create > '2019-09-05 00:00:00' AND tickets.assign_time != '0000-00-00 00:00:00'";
            $result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
            while ($line2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
                $ticket_array[] = $line2;
                
                echo "<tr><td>".$line2['version'].".".$line2['number']."</td><td>".$line2['name']."</td><td>".$line2['description']."</td><td>".$line2['result_description']."</td><td>".$line2['date_create']."</td></tr>";
            }
            
            $robot_array[] = $line;
        }
echo "</table>";
//print_r($robot_array);
//print_r($ticket_array);
    
?>