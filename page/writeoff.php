<?php 


class Writeoff { 
   private $link_writeoff; 
   private $orders; 
   private $mail; 
   private $log; 
    function __construct()
        {
            
        global $database_server, $database_user, $database_password, $dbase;
    	
        $this->link_writeoff = mysql_connect($database_server, $database_user, $database_password)
        or die('Не удалось соединиться: ' . mysql_error());
        
        
        mysql_set_charset('utf8',$this->link_writeoff);
                //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
        $this -> orders = new Orders;
        $this -> mail = new Mail;
        $this -> log = new Log;
        }
        
        function add_writeoff($json) {
            $writeoff_arr = json_decode($json);
            //print_r($writeoff_arr);
            $check = 0;
            $robot = 0;
            $category = $writeoff_arr['0']['0'];
            $description = $writeoff_arr['0']['1'];
            $provider = $writeoff_arr['0']['4'];
            
            if (isset($writeoff_arr['0']['2']))  {$check = $writeoff_arr['0']['2'];}
            if (isset($writeoff_arr['0']['3']))  {$robot = $writeoff_arr['0']['3'];}
            
              if ($category=="Возврат поставщику") {
                  $writeoff_arr['0']['0'] = 999;
                  $writeoff_arr['0']['1'] = $provider;
                  $json = json_encode($writeoff_arr);
                  $this -> orders -> add_order($json,0,1);
                 
             }
            
            array_shift($writeoff_arr);
            
            $date = date("Y-m-d H:i:s");
            $user_id = intval($_COOKIE['id']);
             $total_price = 0;
             
             foreach ($writeoff_arr as &$value) {
                 $price = $value['4']*$value['3'];
                 $total_price = $total_price + $price;
             }
             
           
             
            
            $query = "INSERT INTO `writeoff` (`id`, `category`, `description`,`total_price`,`check`,`robot`, `update_date`, `update_user`) VALUES (NULL, '$category','$description','$total_price','$check','$robot', '$date', $user_id)";
            $result = mysql_query($query) or die($query);
            
            
            
            $idd = mysql_insert_id();
            
            if ($result) {
             $this->log->add(__METHOD__,"Добавлено новое списание №$idd - $category: $description");
             }
           
            
            foreach ($writeoff_arr as &$value) {
            $pos_id = $value['0'];
            $vendor_code = $value['1'];
            $title = $value['2'];
            $count = $value['3'];
            $price = $value['4']*$value['3'];
            
            $subcategory = 0;
            
            if($pos_id!="") {
                $query = "INSERT INTO `writeoff_items` (`id`,`writeoff_id`, `pos_id`, `vendor_code`,`pos_title`, `pos_count`,`pos_price`) VALUES (NULL, $idd, $pos_id, '$vendor_code', '$title', $count, $price);";
                $result = mysql_query($query) or die(mysql_error());   
                $query = "UPDATE `pos_items` SET `total` = total - $count WHERE `id` = $pos_id";
                $result = mysql_query($query) or die($query);  
                
                if ($result && $count!=0) {
                $log_title = "Списание - $category ($description)";
                $param['id'] = $pos_id;
                $param['type'] = "writeoff";
                $param['count'] = $count;
                $param['title'] = $log_title;
                $this->add_log($param);
            }
        }
                    
        }
           $this->mail->send('Екатерина Старцева', 'startceva@promo-bot.ru', 'Списание №'.$idd, 'Пройдите по ссылке для просмотра списания https://db.promo-bot.ru/new/edit_writeoff.php?id='.$idd);  
           return $result;
            
            
        }
    
     function get_writeoff() {
    	
    
        $query = "SELECT * FROM writeoff ORDER BY `update_date` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $orders_array[] = $line; 
        }
        
        // Освобождаем память от результата
       // mysql_free_result($result);
        
       
       
    	
    	if (isset($orders_array))
    	return $orders_array;
    }
   
   
    function get_info_writeoff($id) {
    
        
        $query = "SELECT * FROM writeoff WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $writeoff_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        


    	if (isset($writeoff_array))
    	return $writeoff_array['0'];
    }
    
    function get_pos_in_writeoff($id) {
        $query = "SELECT * FROM writeoff_items WHERE writeoff_id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $writeoff_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);

    	if (isset($writeoff_array))
    	return $writeoff_array;
    }
    
     function edit_writeoff($id,$json) {
        
    	$pos_arr = json_decode($json);
    	$description = $pos_arr['0']['1'];
    	array_shift($pos_arr);

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        
        $query = "SELECT * FROM `writeoff_items` WHERE `writeoff_id`=$id";
        $result = mysql_query($query) or die(mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
            $old_array[] = $line; 
        }
        
         foreach ($old_array as $key => $old) {
             
            $count_new = $pos_arr[$key]['4'];
            $count_old = $old['pos_count'];
            $delta = $count_old - $count_new;
            $row_id = $pos_arr[$key]['0'];
            $pos_id = $pos_arr[$key]['1'];
            if ($count_old!=$count_new) {
                
                $query = "UPDATE `writeoff_items` SET `pos_count` = $count_new WHERE `id` = $row_id";
                $result = mysql_query($query) or die(mysql_error());
                //echo $query;
               $query   = "UPDATE `pos_items` SET `total` = total + $delta WHERE `id` = $pos_id";
               echo $query;
               $result = mysql_query($query) or die(mysql_error());
               $param['id'] = $pos_id;
               $param['type'] = "addmission";
               $param['count'] = $delta;
               $param['title'] = "Изменение списания ";
               $this->add_log($param);
                
                
            }
             
         }
        

        //echo $date;
        $query = "UPDATE `writeoff` SET  `description` = '$description' ,`update_date` = '$date'  WHERE `id` = $id;";
        //echo $query;
        $result = mysql_query($query) or die(mysql_error());

        if ($result) {
             $this->log->add(__METHOD__,"Редактирование списание №$id");
             }

    	return $result;
    }
    
    function get_stat ($param) {
        $startDate = isset($param['startDate']) ? $param['startDate'] : "0";
        $endDate = isset($param['endDate']) ? $param['endDate'] : "0"; 
        $purpose = isset($param['purpose']) ? $param['purpose'] : "0";
        
        $where = "";
        if ($startDate!="0") {$startDate = new DateTime($startDate); $startDate =  $startDate->format('Y-m-d H:i:s'); $where.= " AND `update_date` >= '$startDate'";}
        if ($endDate!="0") {$endDate = new DateTime($endDate); $endDate =  $endDate->format('Y-m-d H:i:s'); $where.= " AND `update_date` <= '$endDate'";}
        if ($purpose!="0") {$where.= " AND `category` LIKE '%$purpose%'";}
        $query = "SELECT SUM(total_price) FROM writeoff WHERE `id`>0 $where ";
        $result = mysql_query($query) or die(mysql_error());
        
         $line = mysql_fetch_array($result, MYSQL_ASSOC);
            
            
            // Освобождаем память от результата
            mysql_free_result($result);
        	
        	if (isset($line))
        	return $line['SUM(total_price)'];
  
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
    
    
    
     function del_pos_writeoff($id, $pos_id, $count)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `writeoff_items` WHERE `pos_id` = $pos_id AND `writeoff_id` = $id";
        $result = mysql_query($query) or die(mysql_error());
        
        if ($result) {
           $query   = "UPDATE `pos_items` SET `total` = total + $count WHERE`id` = $pos_id";
           $result = mysql_query($query) or die(mysql_error());
           $param['id'] = $pos_id;
           $param['type'] = "addmission";
           $param['count'] = $count;
           $param['title'] = "Отмена списания ";
           $this->add_log($param);
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    
    
    function del_writeoff($id) {
        
        $query = "SELECT * FROM `writeoff_items` WHERE `writeoff_id`=$id";
        $result = mysql_query($query) or die(mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
            $item_array[] = $line; 
        }
        
         foreach ($item_array as $key => $item) {
            $pos_id =   $item['pos_id'];
            $count = $item['pos_count'];
            $query   = "UPDATE `pos_items` SET `total` = total + $count WHERE `id` = $pos_id";
            //echo $query."<br>";
            $result = mysql_query($query) or die(mysql_error());
            
            
            if ($result) {
               $param['id'] = $pos_id;
               $param['type'] = "addmission";
               $param['count'] = $count;
               $param['title'] = "Удаление списания ";
               $this->add_log($param);
                
            }
         }
         
        $query   = "DELETE FROM `writeoff_items` WHERE `writeoff_id`=$id";
        //echo $query."<br>";
        $result = mysql_query($query) or die(mysql_error());
        
        $query   = "DELETE FROM `writeoff` WHERE `id` = $id";
        //echo $query."<br>";
        $result = mysql_query($query) or die(mysql_error());
        
       
           
        
    }
    
    function __destruct() {
       
    }
} 

$writeoff = new Writeoff; 
