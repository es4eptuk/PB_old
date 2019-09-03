<?
include 'include/class.inc.php';
$id_pos = 364;
$query = "SELECT check.id, check.operation, check.id_kit, check_items.kit FROM `check` JOIN check_items ON check.id_check = check_items.id WHERE check.category = 5 AND check.id_kit != check_items.kit";
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
       
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $id = $line['id'];
            $kit = $line['kit'];
            $query2 = "UPDATE `check` SET `id_kit` = $kit WHERE `id` = $id ";
            echo $query2;
            //$result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
        }
       

?>