<?php



class Position { 
   private $link_pos; 
   private $writeoff;
    function __construct()
        {
            
        global $database_server, $database_user, $database_password, $dbase;
    	
        $this->link_pos = mysql_connect($database_server, $database_user, $database_password)
        or die('Не удалось соединиться: ' . mysql_error());
        
        
        mysql_set_charset('utf8',$this->link_pos);
                //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
         //$this -> telegram = new TelegramAPI;
         //$this -> robot = new Robots;
          $this -> writeoff = new Writeoff;
        }
    
    //Получение списка категорий
        /*function get_pos_category() {
            $query = 'SELECT * FROM pos_category';
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            
            while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
            $cat_array[] = $line; 
            }
            
            // Освобождаем память от результата
            mysql_free_result($result);
        	
        	if (isset($cat_array))
        	return $cat_array;
        }*/
        
        //Получение списка категорий
        function get_pos_subcategory($category) {
            $query = "SELECT * FROM pos_sub_category WHERE parent = $category";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            
            while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
            $subcat_array[] = $line; 
            }
            
            // Освобождаем память от результата
            mysql_free_result($result);
        	
        	if (isset($subcat_array))
        	return $subcat_array;
        }

    function get_pos_sub_category($id) {
    
    
        $query = "SELECT * FROM pos_sub_category WHERE parent='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $cat_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        
       
        
    	if (isset($cat_array))
    	return $cat_array;
    }

    function get_pos_in_sub_category($sub_category) {
    
    
        $query = "SELECT * FROM pos_items WHERE subcategory='$sub_category'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        
        
    	
    	if (isset($pos_array))
    	return $pos_array;
    }
    
    
    function get_pos_in_category($category, $subcategory = 0, $version = 0) {
    	$where = "";
        if ($subcategory!=0) {
            $where.= " AND subcategory = $subcategory ";
        }
         if ($version!=0) {
            $where.= " AND version = $version ";
        }
        $query = "SELECT * FROM pos_items WHERE category='$category'".$where;
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        
       
    	if (isset($pos_array))
    	return $pos_array;
    }
    
    function get_name_subcategory($id) {
    
    
        $query = "SELECT * FROM pos_sub_category WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        
       
    	
    	if (isset($pos_array))
    	return $pos_array['0']['title'];
    }
    
    function get_name_category($id) {
    	
        
        $query = "SELECT * FROM pos_category WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        
       
    	if (isset($pos_array))
    	return $pos_array['0']['title'];
    }
    
    function get_info_pos_provider($id) {
    	
        
        $query = "SELECT * FROM pos_provider WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        //echo $query;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
       
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        
       

    	if (isset($pos_array))
    	return $pos_array['0'];
    }
    
    
    function get_info_pos($id) {
    
        
        $query = "SELECT * FROM pos_items WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
       
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
       
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        
       

    	if (isset($pos_array))
    	return $pos_array['0'];
    }
    
     function add_pos($title,$longtitle,$category,$subcategory,$vendorcode,$provider,$price,$quant_robot,$quant_total) {
    
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "INSERT INTO `pos_items` (`id`, `category`, `subcategory`,`title`, `vendor_code`, `provider`, `price`, `longtitle`, `quant_robot`, `total`, `update_date`, `update_user` ) VALUES (NULL, '$category', '$subcategory', '$title', '$vendorcode', '$provider', '$price', '$longtitle', '$quant_robot', '$quant_total', '$date', '$user_id')";
        $result = mysql_query($query) or die('false');
        
        // Освобождаем память от результата
       // mysql_free_result($result);
        
    	
    	return $result;
    }
    
    function edit_pos($id,$title,$longtitle,$category,$subcategory,$vendorcode,$provider,$price,$quant_robot,$quant_total) {
    	
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "UPDATE `pos_items` SET `title` = '$title', `longtitle` = '$longtitle',`category` = '$category',`subcategory` = '$subcategory', `provider` = '$provider', `price` = '$price',`quant_robot` = '$quant_robot', `total` = '$quant_total', `vendor_code` = '$vendorcode', `update_date` = '$date' , `update_user` = '$user_id' WHERE `pos_items`.`id` = $id;";
        $result = mysql_query($query) or die(mysql_error());
        
        if ($result && $quant_total!=0) {
            $log_title = "Редактирвоание информации о позиции";
            $param['id'] = $id;
            $param['type'] = "edit";
            $param['count'] = $quant_total;
            $param['title'] = $log_title;
            $this->add_log($param);
        }
        
        // Освобождаем память от результата
       // mysql_free_result($result);

    	
    	return $result;
    }
    
    
    function edit_provider($id,$title,$type,$phone,$email,$address,$contact) {
    	
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "UPDATE `pos_provider` SET `title` = '$title', `type` = '$type',`phone` = '$phone',`email` = '$email', `address` = '$address', `contact` = '$contact', `update_date` = '$date' , `update_user` = '$user_id' WHERE `id` = $id;";
        $result = mysql_query($query) or die(mysql_error());
        
        // Освобождаем память от результата
       // mysql_free_result($result);
        
       
    	
    	return $result;
    }
    
    
    function del_pos($id) {
    	
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "DELETE FROM `pos_items` WHERE `pos_items`.`id` = $id";
        $result = mysql_query($query) or die(mysql_error());
        
        // Освобождаем память от результата
       // mysql_free_result($result);
        
       
    	
    	return $result;
    }
    
    function get_pos_provider() {
        	
        
            $query = 'SELECT * FROM pos_provider ORDER BY `title` ASC';
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            
            while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
            $provider_array[] = $line; 
            }
            
            // Освобождаем память от результата
            mysql_free_result($result);
            
            
        	
        	if (isset($provider_array))
        	return $provider_array;
        }
    
      function add_pos_provider($type,$title) {
    	
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "INSERT INTO `pos_provider` (`id`, `type`,  `title` ) VALUES (NULL, '$type',  '$title' )";
        $result = mysql_query($query) or die('false');
        $idd = mysql_insert_id();
        
        
        // Освобождаем память от результата
       // mysql_free_result($result);
        
        
    	return $idd;
    
    }
    
    function search($term) {
        $output = '';
        $str_arr = array();
        
        $query = "SELECT id,title,vendor_code FROM pos_items WHERE (title LIKE '%$term%' OR vendor_code LIKE '%$term%')";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
            $pos_array[] = $line; 
            }
         
        if (isset($pos_array)) {  
            foreach ($pos_array as $row) {
                array_push($str_arr, "\"". $row['id'] ."::" . $row['vendor_code'] ."::" . $row['title'] . "\"");
                
                //array_push($str_arr, $row['title']);
                 //$str_arr[] =  "1111";
            }
            
            
            
            $s = "[".implode(",", $str_arr)."]";
            return $s;
        }
        
    }
    
    
    
    function get_name_pos_category($id) {
    	
        
        $query = "SELECT * FROM pos_category WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        
       

    	if (isset($pos_array))
    	//print_r($pos_array);
    	//return 123;
    	return $pos_array['0']['title'];
    }
    
    function get_name_pos_subcategory($id) {
    	
        
        $query = "SELECT * FROM pos_sub_category WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        
       

    	if (isset($pos_array))
    	return $pos_array['0']['title'];
    }
    
    function generate_art() {
    	
        
        $query = "SELECT max(id) FROM `pos_items`";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $art_array = $line; 
        }
        // Освобождаем память от результата
        mysql_free_result($result);
    	if (isset($art_array))
    	return $art_array;
    }
    
    function set_reserv($version) {
        $arr_pos =  $this->get_pos_in_equipment($version);
        
        foreach ($arr_pos as &$value) {
            $pos_id = $value['pos_id'];
            $count = $value['count'];
            $query = "UPDATE `pos_items` SET `reserv` = reserv+$count WHERE `id` = $pos_id";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            
        if ($result && $count!=0) {
            $param['id'] = $pos_id;
            $param['type'] = "reserv";
            $param['count'] = $count; 
            $param['title'] = "Постановка в резерв";
            $this->add_log($param);
        }
        
        }
       
        
    	return $result;
    }
    
    
    
     function unset_reserv($version) {
        $arr_pos =  $this->get_pos_in_equipment($version);
        
        foreach ($arr_pos as &$value) {
            $pos_id = $value['pos_id'];
            $count = $value['count'];
            $query = "UPDATE `pos_items` SET `reserv` = reserv-$count WHERE `id` = $pos_id";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            
             if ($result && $count!=0) {
           $param['id'] = $pos_id;
            $param['type'] = "unreserv";
            $param['count'] = $count;
            $param['title'] = "Снятие с резерва";
            $this->add_log($param);
        }
        }
         
       
    	return $result;
    }
    
    function set_writeoff($version,$robot) {
       // $query = "UPDATE `pos_items` SET `total` = total-quant_robot WHERE `version` = $version";
       // $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        $arr_pos =  $this->get_pos_in_equipment($version);
        
        foreach ($arr_pos as &$value) {
          $pos_id = $value['pos_id'];
          $count = $value['count'];  
          $query = "UPDATE `pos_items` SET `reserv` = reserv-$count WHERE `id` = $pos_id";
          $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());  
        }

        $query = "SELECT * FROM `robot_equipment_items` WHERE `equipment_id` = $version";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
        }
        
        $json['0']['0'] = "Производство";
        $json['0']['1'] = "Робот ".$version.".".$robot;
        
        $count = 1;
        
        foreach ($pos_array as $key => $value) {
            $info_pos = $this->get_info_pos($value['pos_id']);
           
            
            $json[$count]['0'] = $info_pos['id'];
            $json[$count]['1'] = $info_pos['vendor_code'];
            $json[$count]['2'] = $info_pos['title'];
            $json[$count]['3'] = $info_pos['quant_robot'];
            $json[$count]['4'] = $info_pos['price'];
            $count++;
        }
       
        $this -> writeoff -> add_writeoff(json_encode($json));
        
    	return $result;
    }
    
     function add_equipment($json) {
            $equipment_arr = json_decode($json);
            $title = $equipment_arr['0']['0'];
           
            array_shift($equipment_arr);
            
            $query = "INSERT INTO `robot_equipment` (`id`, `title`) VALUES (NULL, '$title')";
            $result = mysql_query($query) or die($query);
            $idd = mysql_insert_id();
           
            
            foreach ($equipment_arr as &$value) {
            $pos_id = $value['0'];
            $count = $value['3'];
            $query = "INSERT INTO `robot_equipment_items` (`equipment_id`, `pos_id`, `count`) VALUES ( $idd, $pos_id,$count);";
                    
            $result = mysql_query($query) or die($query);   
            
            }
             
           return $result;
            
            
        }
        
          function edit_equipment($id,$json) {
            $equipment_arr = json_decode($json);
            $title = $equipment_arr['0']['0'];
           
            array_shift($equipment_arr);
            
            
            $query = "UPDATE `robot_equipment` SET `title` = '$title' WHERE `id` = $id";
            $result = mysql_query($query) or die($query);
            
            
            $query = "SELECT * FROM `robots` WHERE `progress` != 100 AND `remont` = 0 ORDER BY `number` ASC";
            $result = mysql_query($query) or die($query);
            
            $line = mysql_fetch_array($result, MYSQL_ASSOC);
            $count_robot_reserv =  count($line);
            print_r("321");

            foreach ($equipment_arr as &$value) {
            $row_id = $value['0'];
            $pos_id = $value['1'];
            $count = $value['4'];
            //$query = "INSERT INTO `robot_equipment_items` (`equipment_id`, `pos_id`, `count`) VALUES ( $id, $pos_id,$count);";
            $query = "INSERT INTO `robot_equipment_items` (`id`,`pos_id`,`equipment_id`,`count`) VALUES ($row_id,$pos_id,$id,$count) ON DUPLICATE KEY UPDATE `count` = $count";
            $result = mysql_query($query) or die($query);   
            
            }
             
           return $result;
            
            
        }
    
    
    function get_equipment() {
        $query = "SELECT * FROM robot_equipment ORDER BY `title` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $equipment_array[] = $line; 
        }

    	if (isset($equipment_array))
    	return $equipment_array;
    }
    
     function get_info_equipment($id) {
    
        
        $query = "SELECT * FROM robot_equipment WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $equipment_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
        


    	if (isset($equipment_array))
    	return $equipment_array['0'];
    }
    
    function get_pos_in_equipment($id) {
        echo $id;
        $query = "SELECT * FROM robot_equipment_items WHERE equipment_id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $equipment_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);

    	if (isset($equipment_array))
    	return $equipment_array;
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
                $title = $title.": Новое значение -> $old_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_count`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$title', '$date', '$user_id')";
                break;
            case "reserv":
                $title = $title.": $count шт. Всего в резерве: $old_reserv";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_reserv`, `title`,`update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv',  '$title', '$date', '$user_id')";
                break;
             case "unreserv":
                $title = $title.": $count шт. Всего в резерве: $old_reserv";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_reserv`, `title`,`update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv', '$title', '$date', '$user_id')";
                break;    
            case "writeoff":
                $tmp = $old_count-$count;
                $title = $title.": $count шт. $old_count -> $tmp";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`,`update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$count', '$title', '$date', '$user_id')";
                break;
        }
        
       
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
       
    }
    
     function get_log($id) {
            $query = "SELECT * FROM `pos_log` WHERE `id_pos` = $id";
           
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            
            while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
            $log_array[] = $line; 
            }
            
            // Освобождаем память от результата
            mysql_free_result($result);
        	
        	if (isset($log_array))
        	return $log_array;
        }
    
    function __destruct() {
        //echo "pos - ";
        //print_r($this ->link_pos);
        //echo "<br>";
        //mysql_close($this ->link_pos);
    }
} 

$position = new Position; 