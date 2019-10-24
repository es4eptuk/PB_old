<?php
class Position
{
    private $link_pos;
    private $writeoff;
    private $log;
    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $this->link_pos = mysql_connect($database_server, $database_user, $database_password) or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8', $this->link_pos);
        //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
        //$this -> telegram = new TelegramAPI;
        //$this -> robot = new Robots;
        $this->writeoff = new Writeoff;
        $this->log = new Log;
    }
   
   
    function invent($id,$new_total) {
        $query   = "UPDATE `pos_items` SET  `total` = '$new_total' WHERE `pos_items`.`id` = $id;";
        $result = mysql_query($query) or die(mysql_error());
        $info_pos = $this->get_info_pos($id);
        $title = $info_pos['title'];
        $vendorcode = $info_pos['vendor_code'];
        
         if ($result) {
            $log_title      = "Инвентаризация ";
            $param['id']    = $id;
            $param['type']  = "edit";
            $param['count'] = $new_total;
            $param['title'] = $log_title;
            $this->add_log($param);
            $this->log->add(__METHOD__,"Инвентаризация  $vendorcode - $title -> $new_total");
        
        }
        
        return $result;
    }
    
    
    
    //Получение списка категорий
    function get_pos_category()
    {
        $query = 'SELECT * FROM pos_category';
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($cat_array))
            return $cat_array;
    }
    //Получение списка категорий
    function get_pos_subcategory($category)
    {
        $query = "SELECT * FROM pos_sub_category WHERE parent = $category";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $subcat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($subcat_array))
            return $subcat_array;
    }
    function get_pos_sub_category($id)
    {
        $query = "SELECT * FROM pos_sub_category WHERE parent='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($cat_array))
            return $cat_array;
    }
    function get_pos_in_sub_category($sub_category)
    {
        $query = "SELECT * FROM pos_items WHERE subcategory='$sub_category'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($pos_array))
            return $pos_array;
    }
    function get_pos_in_category($category, $subcategory = 0, $version = 0)
    {
        $where = "";
        if ($subcategory != 0) {
            $where .= " AND subcategory = $subcategory ";
        }
        if ($version != 0) {
            $where .= " AND version = $version ";
        }
        $query = "SELECT * FROM pos_items WHERE category='$category'" . $where;
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($pos_array))
            return $pos_array;
    }
    function get_name_subcategory($id)
    {
        $query = "SELECT * FROM pos_sub_category WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($pos_array))
            return $pos_array['0']['title'];
    }
    function get_name_category($id)
    {
        $query = "SELECT * FROM pos_category WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($pos_array))
            return $pos_array['0']['title'];
    }
    function get_info_pos_provider($id)
    {
        $query = "SELECT * FROM pos_provider WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        //echo $query;
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($pos_array))
            return $pos_array['0'];
    }
    function get_info_pos($id)
    {
        $query = "SELECT * FROM pos_items WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($pos_array))
            return $pos_array['0'];
    }
    function add_pos($title, $longtitle, $category, $subcategory, $vendorcode, $provider, $price, $quant_robot, $quant_total)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `pos_items` (`id`, `category`, `subcategory`,`title`, `vendor_code`, `provider`, `price`, `longtitle`, `quant_robot`, `total`, `update_date`, `update_user` ) VALUES (NULL, '$category', '$subcategory', '$title', '$vendorcode', '$provider', '$price', '$longtitle', '$quant_robot', '$quant_total', '$date', '$user_id')";
        $result = mysql_query($query) or die('false');
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлена новая позиция $vendorcode $title");
        }
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    function edit_pos($id, $title, $longtitle, $category, $subcategory, $vendorcode, $provider, $price, $quant_robot, $quant_total,$min_balance, $assembly, $summary)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `pos_items` SET `title` = '$title', `longtitle` = '$longtitle',`category` = '$category',`subcategory` = '$subcategory', `provider` = '$provider', `price` = '$price',`quant_robot` = '$quant_robot', `total` = '$quant_total', `min_balance` = '$min_balance',`vendor_code` = '$vendorcode',`assembly` = '$assembly',`summary` = '$summary', `update_date` = '$date' , `update_user` = '$user_id' WHERE `pos_items`.`id` = $id;";
        $result = mysql_query($query) or die(mysql_error());
        if ($result && $quant_total != 0) {
            $log_title      = "Редактирвоание информации о позиции";
            $param['id']    = $id;
            $param['type']  = "edit";
            $param['count'] = $quant_total;
            $param['title'] = $log_title;
            $this->add_log($param);
            $this->log->add(__METHOD__,"Редактирование позиции  $vendorcode - $title");
        
        }
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    function edit_provider($id, $title, $type, $phone, $email, $address, $contact)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `pos_provider` SET `title` = '$title', `type` = '$type',`phone` = '$phone',`email` = '$email', `address` = '$address', `contact` = '$contact', `update_date` = '$date' , `update_user` = '$user_id' WHERE `id` = $id;";
        $result = mysql_query($query) or die(mysql_error());
        if ($result) {
             $this->log->add(__METHOD__,"Редактирование поставщика $type $title");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    function del_pos($id)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `pos_items` WHERE `pos_items`.`id` = $id";
        $result = mysql_query($query) or die(mysql_error());
        
        if ($result) {
             $this->log->add(__METHOD__,"Удаление позиции с ID $id");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    function del_pos_equipment($id, $id_row)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `robot_equipment_items` WHERE `id` = $id_row";
        $result = mysql_query($query) or die(mysql_error());
        
        if ($result) {
             $this->log->add(__METHOD__,"Удаление позиции из комплектации №$id");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    function get_pos_provider()
    {
        $query = 'SELECT * FROM pos_provider ORDER BY `title` ASC';
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $provider_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($provider_array))
            return $provider_array;
    }
    function add_pos_provider($type, $title)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `pos_provider` (`id`, `type`,  `title` ) VALUES (NULL, '$type',  '$title' )";
        $result = mysql_query($query) or die('false');
        $idd = mysql_insert_id();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлен поставщик $type $title");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $idd;
    }
    
     function add_full_provider($type, $title, $phone, $email, $address, $contact)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `pos_provider` (`id`, `type`, `title`, `phone`, `email`, `address`, `contact`, `update_date`, `update_user`) VALUES (NULL, '$type', '$title', '$phone', '$email', '$address', '$contact', '$date', $user_id);";
        $result = mysql_query($query) or die($query);
        $idd = mysql_insert_id();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлен поставщик $type $title");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    function search($term)
    {
        $output  = '';
        $str_arr = array();
        $query   = "SELECT id,title,vendor_code FROM pos_items WHERE (title LIKE '%$term%' OR vendor_code LIKE '%$term%')";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        if (isset($pos_array)) {
            foreach ($pos_array as $row) {
                array_push($str_arr, "\"" . $row['id'] . "::" . $row['vendor_code'] . "::" . $row['title'] . "\"");
                //array_push($str_arr, $row['title']);
                //$str_arr[] =  "1111";
            }
            $s = "[" . implode(",", $str_arr) . "]";
            return $s;
        }
    }
    function get_name_pos_category($id)
    {
        $query = "SELECT * FROM pos_category WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($pos_array))
        //print_r($pos_array);
            
        //return 123;
            return $pos_array['0']['title'];
    }
    function get_name_pos_subcategory($id)
    {
        $query = "SELECT * FROM pos_sub_category WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($pos_array))
            return $pos_array['0']['title'];
    }
    function generate_art()
    {
        $query = "SELECT max(id) FROM `pos_items`";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $art_array = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($art_array))
            return $art_array;
    }
    function set_reserv($version)
    {
        $arr_pos = $this->get_pos_in_equipment($version);
        foreach ($arr_pos as &$value) {
            $pos_id = $value['pos_id'];
            $count  = $value['count'];
            $query  = "UPDATE `pos_items` SET `reserv` = reserv+$count WHERE `id` = $pos_id";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            if ($result && $count != 0) {
                $param['id']    = $pos_id;
                $param['type']  = "reserv";
                $param['count'] = $count;
                $param['title'] = "Постановка в резерв";
                $this->add_log($param);
                
                
                
            }
        }
        return $result;
    }
    function unset_reserv($version)
    {
        $arr_pos = $this->get_pos_in_equipment($version);
        foreach ($arr_pos as &$value) {
            $pos_id = $value['pos_id'];
            $count  = $value['count'];
            $query  = "UPDATE `pos_items` SET `reserv` = reserv-$count WHERE `id` = $pos_id";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            if ($result && $count != 0) {
                $param['id']    = $pos_id;
                $param['type']  = "unreserv";
                $param['count'] = $count;
                $param['title'] = "Снятие с резерва";
                $this->add_log($param);
            }
        }
        return $result;
    }
    function set_writeoff($version, $robot)
    {
        // $query = "UPDATE `pos_items` SET `total` = total-quant_robot WHERE `version` = $version";
        // $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $arr_pos = $this->get_pos_in_equipment($version);
        
        foreach ($arr_pos as &$value) {
           
            $cnt_k = 0;
            $pos_id = $value['pos_id'];
            $count  = $value['count'];
            
            
            $query = "SELECT COUNT(*) FROM `pos_assembly_items` WHERE `id_pos` = $pos_id";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            $line = mysql_fetch_array($result, MYSQL_ASSOC);
            $cnt_a =  $line['COUNT(*)']; 
            
            
             $query = "SELECT * FROM `pos_kit_items` WHERE `id_pos` =  $pos_id";
            $result_kit = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            while ($line_kit = mysql_fetch_array($result_kit, MYSQL_ASSOC)) {
            $kit_array[] = $line_kit;
            $id_kit = $line_kit['id_kit'];
               $query_check = "SELECT count(*) FROM `check_items` WHERE `kit` = $id_kit";
               $result_check = mysql_query($query_check) or die('Запрос не удался: ' . mysql_error());
               $line_check = mysql_fetch_array($result_check, MYSQL_ASSOC);
               //print_r($line_check);
               $cnt_k +=  $line_kit['count']; 

             }

            if ($cnt_a == 0) {
            
            $query  = "UPDATE `pos_items` SET `reserv` = reserv-$count+$cnt_k WHERE `id` = $pos_id";
            //echo $query;
             $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            }
        }
        
        
        
        $query = "SELECT * FROM `robot_equipment_items` WHERE `equipment_id` = $version";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pos_array[] = $line;
        }
        $json['0']['0'] = "Производство";
        $json['0']['1'] = "Робот " . $version . "." . $robot;
        $count          = 1;
        foreach ($pos_array as $key => $value) {
            $cnt_k = 0;
            $pos_id = $value['pos_id'];
            $query = "SELECT COUNT(*) FROM `pos_assembly_items` WHERE `id_pos` = $pos_id";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            $line = mysql_fetch_array($result, MYSQL_ASSOC);
            $cnt_a =  $line['COUNT(*)']; 
            
            $query = "SELECT * FROM `pos_kit_items` WHERE `id_pos` =  $pos_id";
            $result_kit = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            while ($line_kit = mysql_fetch_array($result_kit, MYSQL_ASSOC)) {
            $kit_array[] = $line_kit;
            $id_kit = $line_kit['id_kit'];
               $query_check = "SELECT count(*) FROM `check_items` WHERE `kit` = $id_kit";
               $result_check = mysql_query($query_check) or die('Запрос не удался: ' . mysql_error());
               $line_check = mysql_fetch_array($result_check, MYSQL_ASSOC);
               //print_r($line_check);
               $cnt_k +=  $line_kit['count'];

             }
            //echo $cnt_k;
            
            //$query = "SELECT COUNT(*) FROM `pos_kit` WHERE `id_kit` = 4";
            
            $minus_kit = $value['count']-$cnt_k;
            
             if ($cnt_a == 0 && $minus_kit > 0) {
            $info_pos          = $this->get_info_pos($pos_id);
            $json[$count]['0'] = $info_pos['id'];
            $json[$count]['1'] = $info_pos['vendor_code'];
            $json[$count]['2'] = $info_pos['title'];
            $json[$count]['3'] = $minus_kit;
            $json[$count]['4'] = $info_pos['price'];
            $count++;
             }
        }
        //print_r($json);
        $this->writeoff->add_writeoff(json_encode($json));
        return $result;
    }
    
     function set_writeoff_kit($version, $number, $kit, $check,$robot)
    {
        // $query = "UPDATE `pos_items` SET `total` = total-quant_robot WHERE `version` = $version";
        // $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $arr_pos = $this->get_pos_in_kit($kit);
        //print_r($arr_pos);
        foreach ($arr_pos as &$value) {

            $pos_id = $value['id_pos'];
            $count  = $value['count'];

            $query  = "UPDATE `pos_items` SET `reserv` = reserv-$count WHERE `id` = $pos_id";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            
        }
        
        $info_kit = $this->get_info_kit($kit);
        $title_kit = $info_kit['kit_title'];
        $json['0']['0'] = "Производство";
        $json['0']['1'] = "Робот  $version.$number комплект - $title_kit";
        $json['0']['2'] = $check;
        $json['0']['3'] = $robot;
        $count          = 1;
        //print_r($arr_pos);
        foreach ($arr_pos as &$value) {
            //print_r($value);
            $pos_id2 = $value['id_pos'];
            $info_pos          = $this->get_info_pos($pos_id2);
            $json[$count]['0'] = $pos_id2;
            $json[$count]['1'] = $info_pos['vendor_code'];
            $json[$count]['2'] = $info_pos['title'];
            $json[$count]['3'] = $value['count'];
            $json[$count]['4'] = $info_pos['price'];
            $count++;
             
        }
        //print_r($json);
        $this->writeoff->add_writeoff(json_encode($json));
        return $result;
    }
    
    function unset_writeoff_kit($version, $robot, $kit, $check,$robot) {
     $query = "SELECT id FROM `writeoff` WHERE `check` = $check AND `robot` = $robot";  
     $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
     $line = mysql_fetch_array($result, MYSQL_ASSOC);
     
     $this->writeoff->del_writeoff($line['id']);
        
    }
    
     /*Списание доп опций */
     
    function set_writeoff_options($version, $number, $kit, $check, $robot)
    {
      $query = "SELECT * FROM robot_options_items JOIN robot_options ON robot_options.id_option = robot_options_items.id_option WHERE `id_robot` =  $robot";  
      $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
      
      while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
           $id_kit = $line['id_kit'];
            
            if ($id_kit != 0){
            $this->set_writeoff_kit($version, $number, $id_kit, $check,$robot);
            }
        }
        
      return $result;
    }
    
    /*Комплектации */
    
    function add_equipment($json)
    {
        $equipment_arr = json_decode($json);
        $title         = $equipment_arr['0']['0'];
        array_shift($equipment_arr);
        $query = "INSERT INTO `robot_equipment` (`id`, `title`) VALUES (NULL, '$title')";
        $result = mysql_query($query) or die($query);
        $idd = mysql_insert_id();
        foreach ($equipment_arr as &$value) {
            $pos_id = $value['0'];
            $count  = $value['3'];
            $query  = "INSERT INTO `robot_equipment_items` (`equipment_id`, `pos_id`, `count`) VALUES ( $idd, $pos_id,$count);";
            $result = mysql_query($query) or die($query);
        }
        
          if ($result) {
             $this->log->add(__METHOD__,"Добавлена комплектация $title");
        }
        
        return $result;
    }
    function edit_equipment($id, $json)
    {
        $equipment_arr = json_decode($json);
        $title         = $equipment_arr['0']['0'];
        array_shift($equipment_arr);
        $query = "UPDATE `robot_equipment` SET `title` = '$title' WHERE `id` = $id";
        $result = mysql_query($query) or die($query);
        
        if ($result) {
             $this->log->add(__METHOD__,"Редактирование комплектации №$id");
        }
        
        $num_rows['4']   = 0;
        $num_rows['2']   = 0;
        $reserv_sVersion = 0;
        $query           = "SELECT * FROM `robots` WHERE `progress` != 100 AND `remont` = 0 AND `version` = 4 AND `delete` = 0";
        $result = mysql_query($query) or die($query);
        $num_rows['4'] = mysql_num_rows($result);
        $query         = "SELECT * FROM `robots` WHERE `progress` != 100 AND `remont` = 0 AND `version` = 2 AND `delete` = 0";
        $result = mysql_query($query) or die($query);
        $num_rows['2'] = mysql_num_rows($result);
        foreach ($equipment_arr as &$value) {
            $row_id = $value['0'];
            $pos_id = $value['1'];
            $count  = $value['4'];
            if ($row_id == "")
                $row_id = "''";
            //$query = "INSERT INTO `robot_equipment_items` (`equipment_id`, `pos_id`, `count`) VALUES ( $id, $pos_id,$count);";
            $query = "INSERT INTO `robot_equipment_items` (`id`,`pos_id`,`equipment_id`,`count`) VALUES ($row_id,$pos_id,$id,$count) ON DUPLICATE KEY UPDATE `count` = $count";
            $result = mysql_query($query) or die($query);
            if ($id == 4) {
                $sVersion = 2;
            }
            if ($id == 2) {
                $sVersion = 4;
            }
            $query = "SELECT * FROM `robot_equipment_items` WHERE `pos_id` = $pos_id AND `equipment_id` = $sVersion";
            $result = mysql_query($query) or die($query);
            unset($sVersion_array);
            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $sVersion_array[] = $line;
            }
            $reserv_sVersion = 0;
            if (isset($sVersion_array)) {
                $count_sVersion  = $sVersion_array[0]['count'];
                $reserv_sVersion = $count_sVersion * $num_rows[$sVersion];
            }
            //echo $reserv_sVersion;
            $reserv = ($count * $num_rows[$id]) + $reserv_sVersion;
            //echo $pos_id." ".$count * $num_rows[$id]." ".$reserv_sVersion."<br>";
            $query  = "UPDATE `pos_items` SET `reserv` = '$reserv' WHERE `id` = $pos_id";
            $result = mysql_query($query) or die($query);
        }
        return $result;
    }
    function get_equipment()
    {
        $query = "SELECT * FROM robot_equipment ORDER BY `title` DESC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $equipment_array[] = $line;
        }
        if (isset($equipment_array))
            return $equipment_array;
    }
    function get_info_equipment($id)
    {
        $query = "SELECT * FROM robot_equipment WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $equipment_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($equipment_array))
            return $equipment_array['0'];
    }
    function get_pos_in_equipment($id = 0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND robot_equipment_items.equipment_id='$id'";
        }
        $query = "SELECT robot_equipment_items.id, robot_equipment_items.equipment_id, robot_equipment_items.pos_id, pos_items.title, pos_items.vendor_code, robot_equipment_items.count, pos_items.total, pos_items.subcategory, pos_items.price FROM `robot_equipment_items` INNER JOIN pos_items ON pos_items.id = robot_equipment_items.pos_id WHERE robot_equipment_items.id >0 $where";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $equipment_array[] = $line;
        }
        //print_r ($query);
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($equipment_array))
            return $equipment_array;
    }
    function get_pos_in_equipment_cat($id = 0, $version=0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND pos_items.category=$id";
        }
        
        if ($version != 0) {
            $where .= " AND robot_equipment_items.equipment_id=$version";
        }
        $query = "SELECT robot_equipment_items.id, robot_equipment_items.equipment_id, robot_equipment_items.pos_id, pos_items.title, pos_items.vendor_code, robot_equipment_items.count, pos_items.total, pos_items.subcategory, pos_items.category, pos_items.provider, pos_items.price, pos_items.summary FROM `robot_equipment_items` INNER JOIN pos_items ON pos_items.id = robot_equipment_items.pos_id WHERE robot_equipment_items.id >0 $where";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $equipment_array[] = $line;
        }
        //print_r ($query);
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($equipment_array))
            return $equipment_array;
    }
    function get_pos_in_equipment_test($id = 0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND robot_equipment_items.equipment_id='$id'";
        }
        $query = "SELECT robot_equipment_items.id, robot_equipment_items.equipment_id, robot_equipment_items.pos_id, pos_items.title, pos_items.vendor_code, robot_equipment_items.count, pos_items.total, pos_items.subcategory FROM `robot_equipment_items` INNER JOIN pos_items ON pos_items.id = robot_equipment_items.pos_id WHERE (robot_equipment_items.pos_id  = 269 OR robot_equipment_items.pos_id  = 862) $where LIMIT 10";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $equipment_array[] = $line;
        }
        //print_r ($query);
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($equipment_array))
            return $equipment_array;
    }
    
    
    
    /*Сборки */
    
     function add_assembly($json)
    {
        $equipment_arr = json_decode($json);
        $title         = $equipment_arr['0']['0'];
        array_shift($equipment_arr);
        $query = "INSERT INTO `pos_assembly` (`id_assembly`, `title`) VALUES (NULL, '$title')";
        $result = mysql_query($query) or die($query);
        
        
        
        $idd = mysql_insert_id();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлена новая сборка №$idd - $title");
        }
        
        foreach ($equipment_arr as &$value) {
            $pos_id = $value['0'];
            $count  = $value['3'];
            $query  = "INSERT INTO `pos_assembly_items` (`id_assembly`, `id_pos`, `count`) VALUES ( $idd, $pos_id,$count);";
            $result = mysql_query($query) or die($query);
        }
        return $result;
    }
    function edit_assembly($id, $json)
    {
        $equipment_arr = json_decode($json);
        $title         = $equipment_arr['0']['0'];
        array_shift($equipment_arr);
        $query = "UPDATE `pos_assembly` SET `title` = '$title' WHERE `id_assembly` = $id";
        $result = mysql_query($query) or die($query);
        
        if ($result) {
             $this->log->add(__METHOD__,"Редактирование сборки №$id - $title");
        }
        
        foreach ($equipment_arr as &$value) {
            $row_id = $value['0'];
            $pos_id = $value['1'];
            $count  = $value['4'];
            if ($row_id == "")
                $row_id = "''";
            //$query = "INSERT INTO `robot_equipment_items` (`equipment_id`, `pos_id`, `count`) VALUES ( $id, $pos_id,$count);";
            $query = "INSERT INTO `pos_assembly_items` (`id_row`,`id_pos`,`id_assembly`,`count`) VALUES ($row_id,$pos_id,$id,$count) ON DUPLICATE KEY UPDATE `count` = $count";
            $result = mysql_query($query) or die($query);
        }
        return $result;
    }
    function get_assembly()
    {
        $query = "SELECT * FROM pos_assembly ORDER BY `title` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $equipment_array[] = $line;
        }
        if (isset($equipment_array))
            return $equipment_array;
    }
    function get_info_assembly($id)
    {
        $query = "SELECT * FROM pos_assembly WHERE id_assembly='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $equipment_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($equipment_array))
            return $equipment_array['0'];
    }
    function get_pos_in_assembly($id = 0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND pos_assembly_items.id_assembly='$id'";
        }
        $query = "SELECT pos_assembly_items.id_row, pos_assembly_items.id_assembly, pos_assembly_items.id_pos, pos_items.title, pos_items.vendor_code, pos_assembly_items.count, pos_items.total, pos_items.reserv, pos_items.subcategory, pos_items.price, pos_items.provider, pos_items.summary, pos_items.version, pos_items.assembly, pos_items.min_balance FROM `pos_assembly_items` INNER JOIN pos_items ON pos_items.id = pos_assembly_items.id_pos WHERE pos_assembly_items.id_row >0 $where";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $equipment_array[] = $line;
        }
        //print_r ($query);
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($equipment_array))
            return $equipment_array;
    }
    function get_pos_in_assambly_cat($id = 0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND pos_items.category=$id";
        }
        $query = "SELECT pos_assembly_items.id_row, pos_assembly_items.id_assembly, pos_assembly_items.id_pos, pos_items.title, pos_items.vendor_code, pos_assembly_items.count, pos_items.total, pos_items.subcategory, pos_items.category, pos_items.provider, pos_items.price FROM `pos_assembly_items` INNER JOIN pos_items ON pos_items.id =  pos_assembly_items.id_pos WHERE  pos_assembly_items.id_row >0 $where";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $equipment_array[] = $line;
        }
        //print_r ($query);
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($equipment_array))
            return $equipment_array;
    }
    
    function del_pos_assembly($id, $id_row)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `pos_assembly_items` WHERE `id_row` = $id_row";
        $result = mysql_query($query) or die(mysql_error());
        
        if ($result) {
             $this->log->add(__METHOD__,"Удаление позиции из сборки №$id");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    
    
    
    
    
    
    
    
    /*Комплекты */
    
     function add_kit($json)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $kit_arr = json_decode($json);
        $title         = $kit_arr['0']['0'];
        $category         = $kit_arr['0']['1'];
        $version         = $kit_arr['0']['2'];
        array_shift($kit_arr);
        $query = "INSERT INTO `pos_kit` (`id_kit`, `kit_title`, `kit_category`, `version`, `update_user`, `update_date`) VALUES (NULL, '$title', '$category', '$version', $user_id, '$date')";
        $result = mysql_query($query) or die($query);
 
        $idd = mysql_insert_id();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлена новый комплект №$idd - $title");
        }
        
        foreach ($kit_arr as &$value) {
            $pos_id = $value['0'];
            $count  = $value['3'];
            $query  = "INSERT INTO `pos_kit_items` (`id_kit`, `id_pos`, `count`, `version`, `update_user`, `update_date`) VALUES ( $idd, $pos_id,$count,$version, $user_id, '$date');";
            $result = mysql_query($query) or die($query);
        }
        return $result;
    }
    function edit_kit($id, $json)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $kit_arr = json_decode($json);
        $title         = $kit_arr['0']['0'];
        $category         = $kit_arr['0']['1'];
        $version         = $kit_arr['0']['2'];
        array_shift($kit_arr);
        $query = "UPDATE `pos_kit` SET `kit_title` = '$title', `kit_category` = '$category' , `version` = '$version', `update_date` = '$date' , `update_user` = '$user_id' WHERE `id_kit`  = $id";
        $result = mysql_query($query) or die($query);
        
        if ($result) {
             $this->log->add(__METHOD__,"Редактирование комплекта №$id - $title");
        }
        
        foreach ($kit_arr as &$value) {
            $row_id = $value['0'];
            $pos_id = $value['1'];
            $count  = $value['4'];
            if ($row_id == "")
                $row_id = "''";
            //$query = "INSERT INTO `robot_equipment_items` (`equipment_id`, `pos_id`, `count`) VALUES ( $id, $pos_id,$count);";
            
            
            $query = "INSERT INTO `pos_kit_items` (`id_row`,`id_pos`,`id_kit`,`count`,`version`, `update_user`, `update_date`) VALUES ($row_id,$pos_id,$id,$count,$version, $user_id, '$date') ON DUPLICATE KEY UPDATE `count` = $count, `version` = $version, `update_user` = $user_id, `update_date` = '$date'  ";
            //echo $query;
            
            $result = mysql_query($query) or die($query);
        }
        return $result;
    }
    function get_kit($category=0,$version = -1,$option = -1)
    {
        $where = "";
        
        if ($category != 0) $where .= " AND pos_kit.kit_category = $category";
        if ($version != -1) $where .= " AND pos_kit.version = $version";
        if ($option != -1) $where .= " AND pos_kit.option = $option";
        
        
        $query = "SELECT pos_kit.kit_title, pos_kit.id_kit, pos_kit.version,  pos_category.title FROM pos_kit JOIN pos_category ON pos_kit.kit_category = pos_category.id WHERE pos_kit.id_kit > 0  $where  ORDER BY pos_kit.version ASC, pos_kit.kit_title ASC";
      // echo $query;
       
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $cnt=0;
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $idd = $line['id_kit'];
            $query2 = "SELECT COUNT(*) FROM `check` WHERE `id_kit` = $idd";
            $result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
            $line2 = mysql_fetch_array($result2, MYSQL_ASSOC);
            $count = $line2['COUNT(*)'];
            
            $kit_array[$cnt]['id_kit'] = $line['id_kit'];
            $kit_array[$cnt]['kit_title'] = $line['kit_title'];
            $kit_array[$cnt]['title'] = $line['title'];
             $kit_array[$cnt]['version'] = $line['version'];
            
            $kit_array[$cnt]['count'] = $count;
            
            $cnt++;
            
            //$kit_array[] = $line;
        }
        if (isset($kit_array))
            return $kit_array;
    }
    function get_info_kit($id)
    {
        $query = "SELECT * FROM pos_kit WHERE id_kit='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $cnt=0;
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $idd = $line['id_kit'];
            $query2 = "SELECT COUNT(*) FROM `check` WHERE `id_kit` = $idd";
            $result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
            $line2 = mysql_fetch_array($result2, MYSQL_ASSOC);
            $count = $line2['COUNT(*)'];
            
            $kit_array[$cnt]['id_kit'] = $line['id_kit'];
            $kit_array[$cnt]['kit_title'] = $line['kit_title'];
            $kit_array[$cnt]['kit_category'] = $line['kit_category'];
            $kit_array[$cnt]['kit_version'] = $line['version'];
            $kit_array[$cnt]['count'] = $count;
            
            $cnt++;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($kit_array))
            return $kit_array['0'];
    }
    function get_pos_in_kit($id = 0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND pos_kit_items.id_kit='$id'";
        }
        $query = "SELECT pos_kit_items.id_row, pos_kit_items.id_kit, pos_kit_items.id_pos, pos_items.title, pos_items.vendor_code, pos_kit_items.count, pos_items.total, pos_items.reserv, pos_items.subcategory, pos_items.price FROM `pos_kit_items` INNER JOIN pos_items ON pos_items.id = pos_kit_items.id_pos WHERE pos_kit_items.id_row >0 $where";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $kit_array[] = $line;
        }
        //print_r ($query);
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($kit_array))
            return $kit_array;
    }
    
    function del_pos_kit($id, $id_row)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `pos_kit_items` WHERE `id_row` = $id_row";
        $result = mysql_query($query) or die(mysql_error());
       
        if ($result) {
             $this->log->add(__METHOD__,"Удаление позиции из комплекта №$id");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    /*Логирование */
    function add_log($param)
    {
        $id      = $param['id'];
        $type    = $param['type'];
        $count   = $param['count'];
        $title   = $param['title'];
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "SELECT * FROM `pos_items` WHERE id = $id";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $line       = mysql_fetch_array($result, MYSQL_ASSOC);
        $old_count  = $line['total'];
        $old_reserv = $line['reserv'];
        mysql_free_result($result);
        switch ($type) {
            case "edit":
                $title = $title . ": Новое значение -> $old_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_count`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$title', '$date', '$user_id')";
                break;
            case "reserv":
                $title = $title . ": $count шт. Всего в резерве: $old_reserv";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_reserv`, `title`,`update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv',  '$title', '$date', '$user_id')";
                break;
            case "unreserv":
                $title = $title . ": $count шт. Всего в резерве: $old_reserv";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_reserv`, `title`,`update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv', '$title', '$date', '$user_id')";
                break;
            case "writeoff":
                $tmp   = $old_count - $count;
                $title = $title . ": $count шт. $old_count -> $tmp";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`,`update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$count', '$title', '$date', '$user_id')";
                break;
        }
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    }
    function get_log($id)
    {
        $query = "SELECT * FROM `pos_log` WHERE `id_pos` = $id";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $log_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($log_array))
            return $log_array;
    }
    
    
        function get_pos_in_kit_cat($id = 0, $version=0, $positive = 0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND pos_items.category=$id";
        }
        
        if ($version != 0) {
            $where .= " AND pos_kit_items.version=$version";
        }

        if ($positive != 0) {
            $where .= " AND pos_kit_items.count>0";
        }
        $query = "SELECT pos_items.id, pos_items.title, pos_items.category, pos_items.vendor_code,SUM(pos_kit_items.count), pos_kit_items.version, pos_items.total, pos_items.subcategory, pos_items.provider, pos_items.price, pos_items.summary, pos_items.assembly , pos_items.min_balance FROM pos_kit_items JOIN pos_items ON pos_kit_items.id_pos = pos_items.id WHERE pos_kit_items.id_pos  = 80   AND pos_kit_items.delete = 0 $where GROUP BY pos_kit_items.id_pos ";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $id = $line['id'];
            $equipment_array[$id] = $line;
        }
        //print_r ($query);
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($equipment_array))
            return $equipment_array;
    }
    
    function del_provider($id)
    {
        
        $query   = "DELETE FROM `pos_provider` WHERE `id` = $id";
        $result = mysql_query($query) or die(mysql_error());
       
        if ($result) {
             $this->log->add(__METHOD__,"Удаление поставщика №$id");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    
    function __destruct()
    {
    }
}
$position = new Position;