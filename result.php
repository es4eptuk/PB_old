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


$id_pos = 125;
//$pos_assambly = $position->get_pos_in_assembly($id_pos);

$date_robots = date('Y-m-d H:i:s', mktime(0,0,0,date('m')-3,1,date('Y')));
$date_tikets = date('Y-m-d H:i:s', mktime(0,0,0,date('m')-1,1,date('Y')));

$query = "SELECT `robots`.`id` FROM `check` INNER JOIN `robots` ON `check`.`robot` = `robots`.id WHERE `check`.`update_date` >= '$date_robots' AND `id_check` IN (105,314,553,548)";
$result = $pdo->query($query);
echo "<table border=1>";
while ($line = $result->fetch()) {
           // $id_assembly = $line['id_assembly'];
            //$kit_array_count[$id_pos][$id_kit0] = $line['count'];
            $id = $line['id'];
            $query2 = "SELECT `robots`.`version`, `robots`.`number`, `robots`.`name`, `tickets`.`description`, `tickets`.`result_description`, `tickets`.`date_create` FROM `tickets` INNER JOIN `robots` ON `tickets`.`robot` = `robots`.`id` WHERE `tickets`.`robot` = $id AND `tickets`.`date_create` > '$date_tikets' AND `tickets`.`assign_time` != 'NULL'";
            $result2 = $pdo->query($query2);
            while ($line2 = $result2->fetch()) {
                $ticket_array[] = $line2;

                echo "<tr><td>".$line2['version'].".".$line2['number']."</td><td>".$line2['name']."</td><td>".$line2['description']."</td><td>".$line2['result_description']."</td><td>".$line2['date_create']."</td></tr>";
            }

            $robot_array[] = $line;
        }
echo "</table>";
//print_r($robot_array);
//print_r($ticket_array);
    
?>