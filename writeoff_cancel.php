<? 
include 'include/class.inc.php';

$arr = $writeoff->get_pos_in_writeoff();

foreach ($arr as $key => $value) {
    $id = $value['pos_id'];
    $count = $value['pos_count'];
   
   $query = "UPDATE `pos_items` SET `total` = total + $count WHERE `id` = $id";
   $result = mysql_query($query) or die(mysql_error());
   
    $param['id'] = $id;
    $param['type'] = "addmission";
    $param['count'] = $count;
    $param['title'] = "Отмена списания ";
    add_log($param);
   
}

 function add_log($param) {
        $id = $param['id'];
        $type = $param['type'];
        $count = $param['count'];
        $title = $param['title'];
        
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        
        $query = "SELECT * FROM `pos_items` WHERE id = $id";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $line = mysql_fetch_array($result, MYSQL_ASSOC);
        $old_count = $line['total'];
        $old_reserv = $line['reserv'];
        mysql_free_result($result);
       
        switch ($type) {
            case "edit":
                $title = $title.": $old_count -> $count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$count', '$title', '$date', '$user_id')";
                break;
            case "reserv":
                $title = $title.": $count шт.";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_reserv`, `new_reserv`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv', '$old_reserv+$count', '$title', '$date', '$user_id')";
                break;
             case "unreserv":
                $title = $title.": $count шт.";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_reserv`, `new_reserv`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv', '$old_reserv-$count', '$title', '$date', '$user_id')";
                break;    
            case "writeoff":
                $tmp = $old_count-$count;
                $title = $title.": $count шт. Новое значение -> $old_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_count`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$title', '$date', '$user_id')";
                break;
            case "addmission":
                $tmp = $old_count+$count;
                $title = $title.": $count шт. Новое значение - > $old_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$count', '$title', '$date', '$user_id')";
                break;    
        }
        
       
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
       
    }

?>