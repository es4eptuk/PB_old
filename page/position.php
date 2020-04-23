<?php
class Position
{
    private $query;
    private $pdo;
    private $writeoff;
    private $log;

    //списки
    public $getCategoryes;
    public $getSubcategoryes;

    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='';",
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);
    }

    function init()
    {
        global $writeoff, $log;

        $this->writeoff = $writeoff; //new Writeoff;
        $this->log = $log; //new Log;

        //список категорий
        $query = "SELECT * FROM `pos_category`";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $categoryes[$line['id']] = $line;
        }
        $this->getCategoryes = (isset($categoryes)) ? $categoryes : [];

        //список субкатегорий
        $query = "SELECT * FROM `pos_sub_category`";
        $result = $this->pdo->query($query);
        $subcategoryes[0]= ['id'=> 0, 'parent' => 0, 'title' => ''];
        while ($line = $result->fetch()) {
            $subcategoryes[$line['id']] = $line;
        }

        $this->getSubcategoryes = (isset($subcategoryes)) ? $subcategoryes : [];

    }

    /**
     * @param $id
     * @param $new_total
     * @return false|PDOStatement
     * Инвентаризация позиций
     */
    function invent($id, $new_total) {
        $query   = "UPDATE `pos_items` SET  `total` = '$new_total' WHERE `pos_items`.`id` = $id;";
        $result = $this->pdo->query($query);
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


    /**
     * @return array
     * Получение списка категорий
     */
    /*function get_pos_category()
    {
        $query = 'SELECT * FROM pos_category';
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $cat_array[] = $line;
        }

        if (isset($cat_array))
            return $cat_array;
    }*/



    /**
     * @param $category
     * @return array
     * Получение списка категорий
     */
    function get_pos_subcategory($category)
    {
        $query = "SELECT * FROM pos_sub_category WHERE parent = $category";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $subcat_array[] = $line;
        }

        if (isset($subcat_array))
            return $subcat_array;
    }


    /**
     * @param $id
     * @return array
     */
    function get_pos_sub_category($id)
    {
        $query = "SELECT * FROM pos_sub_category WHERE parent='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $cat_array[] = $line;
        }

        if (isset($cat_array))
            return $cat_array;
    }

    /**
     * @param $sub_category
     * @return array
     */
    function get_pos_in_sub_category($sub_category)
    {
        $query = "SELECT * FROM pos_items WHERE subcategory='$sub_category'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }

        if (isset($pos_array))
            return $pos_array;
    }

    function get_pos_in_category($category, $subcategory = 0, $version = 0, $archive = 0)
    {
        $where = "";
        if ($subcategory != 0) {
            $where .= " AND subcategory = $subcategory ";
        }
        if ($version != 0) {
            $where .= " AND version = $version ";
        }
        if ($archive == 0) {
            $where .= " AND archive = 0 ";
        }
        $query = "SELECT * FROM pos_items WHERE category='$category'" . $where;
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }

        return (isset($pos_array)) ? $pos_array : [];
    }

    //взять название субкатегории
    function get_name_subcategory($id)
    {
        $query = "SELECT * FROM pos_sub_category WHERE id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }

        if (isset($pos_array))
            return $pos_array['0']['title'];
    }

    /*
    function get_name_category($id)
    {
        $query = "SELECT * FROM pos_category WHERE id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }

        if (isset($pos_array))
            return $pos_array['0']['title'];
    }
    */
    function get_info_pos_provider($id)
    {
        $query = "SELECT * FROM pos_provider WHERE id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }

        if (isset($pos_array))
            return $pos_array['0'];
    }

    function get_info_pos($id)
    {
        $query = "SELECT * FROM pos_items WHERE id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }

        return (isset($pos_array)) ? $pos_array['0']: [];
        /*if (isset($pos_array))
            return $pos_array['0'];*/
    }

    function add_pos($title, $longtitle, $category, $subcategory, $vendorcode, $provider, $price, $quant_robot, $quant_total)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $title = quotemeta($title);
        $query   = "INSERT INTO `pos_items` (`id`, `category`, `subcategory`,`title`, `vendor_code`, `provider`, `price`, `longtitle`, `quant_robot`, `total`, `update_date`, `update_user` ) VALUES (NULL, '$category', '$subcategory', '$title', '$vendorcode', '$provider', '$price', '$longtitle', '$quant_robot', '$quant_total', '$date', '$user_id')";
        $result = $this->pdo->query($query);
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлена новая позиция $vendorcode $title через перемещение");
        }

        return $result;
    }
    function edit_pos($id, $title, $longtitle, $category, $subcategory, $vendorcode, $provider, $price, $quant_robot, $quant_total, $min_balance, $assembly, $summary, $archive)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `pos_items` SET `title` = '$title', `longtitle` = '$longtitle', `category` = '$category', `subcategory` = '$subcategory', `provider` = '$provider', `price` = '$price', `quant_robot` = '$quant_robot', `total` = '$quant_total', `min_balance` = '$min_balance', `vendor_code` = '$vendorcode', `assembly` = '$assembly', `summary` = '$summary', `archive` = '$archive', `update_date` = '$date', `update_user` = '$user_id' WHERE `pos_items`.`id` = $id;";
        $result = $this->pdo->query($query);
        if ($result && $quant_total != 0) {
            $log_title      = "Редактирвоание информации о позиции";
            $param['id']    = $id;
            $param['type']  = "edit";
            $param['count'] = $quant_total;
            $param['title'] = $log_title;
            $this->add_log($param);
            $this->log->add(__METHOD__,"Редактирование позиции  $vendorcode - $title");
        
        }

        return $result;
    }
    function edit_provider($id, $title, $type, $phone, $email, $address, $contact)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `pos_provider` SET `title` = '$title', `type` = '$type',`phone` = '$phone',`email` = '$email', `address` = '$address', `contact` = '$contact', `update_date` = '$date' , `update_user` = '$user_id' WHERE `id` = $id;";
        $result = $this->pdo->query($query);
        if ($result) {
             $this->log->add(__METHOD__,"Редактирование поставщика $type $title");
        }
        

        return $result;
    }
    function del_pos($id)
    {
        //$date    = date("Y-m-d H:i:s");
        //$user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `pos_items` WHERE `pos_items`.`id` = $id";
        $result = $this->pdo->query($query);
        
        if ($result) {
             $this->log->add(__METHOD__,"Удаление позиции с ID $id");
        }
        
        return $result;
    }
    function del_pos_equipment($id, $id_row)
    {
        //$date    = date("Y-m-d H:i:s");
        //$user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `robot_equipment_items` WHERE `id` = $id_row";
        $result = $this->pdo->query($query);
        
        if ($result) {
             $this->log->add(__METHOD__,"Удаление позиции из комплектации №$id");
        }
        

        return $result;
    }
    function get_pos_provider()
    {
        $query = 'SELECT * FROM pos_provider ORDER BY `title` ASC';
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $provider_array[] = $line;
        }

        if (isset($provider_array))
            return $provider_array;
    }
    function add_pos_provider($type, $title)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `pos_provider` (`id`, `type`,  `title` ) VALUES (NULL, '$type',  '$title' )";
        $result = $this->pdo->query($query);
        $idd   = $this->pdo->lastInsertId();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлен поставщик $type $title");
        }
        

        return $idd;
    }
    
     function add_full_provider($type, $title, $phone, $email, $address, $contact)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `pos_provider` (`id`, `type`, `title`, `phone`, `email`, `address`, `contact`, `update_date`, `update_user`) VALUES (NULL, '$type', '$title', '$phone', '$email', '$address', '$contact', '$date', $user_id);";
        $result = $this->pdo->query($query);
        $idd   = $this->pdo->lastInsertId();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлен поставщик $type $title");
        }
        

        return $result;
    }
    function search($term)
    {
        $output  = '';
        $str_arr = array();
        $query   = "SELECT id,title,vendor_code FROM pos_items WHERE (title LIKE '%$term%' OR vendor_code LIKE '%$term%')";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
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
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }

        if (isset($pos_array))
         return $pos_array['0']['title'];
    }
    function get_name_pos_subcategory($id)
    {
        $query = "SELECT * FROM pos_sub_category WHERE id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }

        if (isset($pos_array))
            return $pos_array['0']['title'];
    }

    function generate_art()
    {
        //$query = "SELECT max(id) FROM `pos_items`";
        $query = "SHOW TABLE STATUS LIKE 'pos_items'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $art_array = $line;
        }

        if (isset($art_array))
            return $art_array;
    }

    //постановка деталей в резерв
    /*function set_reserv($version)
    {
        $arr_pos = $this->get_pos_in_equipment($version);
        if (isset($arr_pos)){
            foreach ($arr_pos as &$value) {
                $pos_id = $value['pos_id'];
                $count = $value['count'];
                $query = "UPDATE `pos_items` SET `reserv` = reserv+$count WHERE `id` = $pos_id";
                $result = $this->pdo->query($query);
                if ($result && $count != 0) {
                    $param['id'] = $pos_id;
                    $param['type'] = "reserv";
                    $param['count'] = $count;
                    $param['title'] = "Постановка в резерв";
                    $this->add_log($param);
                }
            }
        return $result;
        }
    }*/

    //списание деталей из резерва
    /*function unset_reserv($version)
    {
        $arr_pos = $this->get_pos_in_equipment($version);
        foreach ($arr_pos as &$value) {
            $pos_id = $value['pos_id'];
            $count  = $value['count'];
            $query  = "UPDATE `pos_items` SET `reserv` = reserv-$count WHERE `id` = $pos_id";
            $result = $this->pdo->query($query);
            if ($result && $count != 0) {
                $param['id']    = $pos_id;
                $param['type']  = "unreserv";
                $param['count'] = $count;
                $param['title'] = "Снятие с резерва";
                $this->add_log($param);
            }
        }
        return $result;
    }*/

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
            $result = $this->pdo->query($query);
            $line = $result->fetch();
            $cnt_a =  $line['COUNT(*)']; 
            
            
             $query = "SELECT * FROM `pos_kit_items` WHERE `id_pos` =  $pos_id";
            $result_kit = $this->pdo->query($query);
            while ($line_kit = $result_kit->fetch()) {
            $kit_array[] = $line_kit;
            $id_kit = $line_kit['id_kit'];
               $query_check = "SELECT count(*) FROM `check_items` WHERE `kit` = $id_kit";
               $result_check = $this->pdo->query($query_check);
               $line_check = $result_check->fetch();
               //print_r($line_check);
               $cnt_k +=  $line_kit['count']; 

             }

            if ($cnt_a == 0) {
            
            //$query  = "UPDATE `pos_items` SET `reserv` = reserv-$count+$cnt_k WHERE `id` = $pos_id";
            //echo $query;
            //$result = $this->pdo->query($query);
            }
        }
        
        
        
        $query = "SELECT * FROM `robot_equipment_items` WHERE `equipment_id` = $version";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }
        $json['0']['0'] = "Производство";
        $json['0']['1'] = "Робот " . $version . "." . $robot;
        $count          = 1;
        foreach ($pos_array as $key => $value) {
            $cnt_k = 0;
            $pos_id = $value['pos_id'];
            $query = "SELECT COUNT(*) FROM `pos_assembly_items` WHERE `id_pos` = $pos_id";
            $result = $this->pdo->query($query);
            $line = $result->fetch();
            $cnt_a =  $line['COUNT(*)']; 
            
            $query = "SELECT * FROM `pos_kit_items` WHERE `id_pos` =  $pos_id";
            $result_kit = $this->pdo->query($query);
            while ($line_kit = $result_kit->fetch()) {
            $kit_array[] = $line_kit;
            $id_kit = $line_kit['id_kit'];
               $query_check = "SELECT count(*) FROM `check_items` WHERE `kit` = $id_kit";
               $result_check = $this->pdo->query($query_check);
               $line_check = $result_check->fetch();
               //print_r($line_check);
               $cnt_k +=  $line_kit['count'];

             }

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
    
    function set_writeoff_kit($version, $number, $kit, $check, $robot)
    {
        // $query = "UPDATE `pos_items` SET `total` = total-quant_robot WHERE `version` = $version";
        // $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $arr_pos = $this->get_pos_in_kit($kit);
        //print_r($arr_pos);
        foreach ($arr_pos as &$value) {

            $pos_id = $value['id_pos'];
            $count  = $value['count'];

            //$query  = "UPDATE `pos_items` SET `reserv` = reserv-$count WHERE `id` = $pos_id";
            //$result = $this->pdo->query($query);
            
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
    
    function unset_writeoff_kit($version, $number, $kit, $check, $robot) {
     $query = "SELECT id FROM `writeoff` WHERE `check` = $check AND `robot` = $robot";
     $result = $this->pdo->query($query);
     $line = $result->fetch();
     $this->writeoff->del_writeoff($line['id']);
    }
    
     /*Списание доп опций */
    function set_writeoff_options($version, $number, $kit, $check, $robot)
    {
      $query = "SELECT * FROM robot_options_items JOIN robot_options ON robot_options.id_option = robot_options_items.id_option WHERE `id_robot` =  $robot";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
           $id_kit = $line['id_kit'];
            
            if ($id_kit != 0){
            $this->set_writeoff_kit($version, $number, $id_kit, $check,$robot);
            }
        }
        
      return $result;
    }
    
    /*Комплектации */
    //создать новую версию робота
    function add_equipment($json)
    {
        $equipment_arr = json_decode($json);
        $title         = $equipment_arr['0']['0'];
        array_shift($equipment_arr);
        $query = "INSERT INTO `robot_equipment` (`id`, `title`) VALUES (NULL, '$title')";
        $result = $this->pdo->query($query);
        $idd   = $this->pdo->lastInsertId();
        foreach ($equipment_arr as &$value) {
            $pos_id = $value['0'];
            $count  = $value['3'];
            $query  = "INSERT INTO `robot_equipment_items` (`equipment_id`, `pos_id`, `count`) VALUES ( $idd, $pos_id,$count);";
            $result = $this->pdo->query($query);
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
        $result = $this->pdo->query($query);
        
        if ($result) {
             $this->log->add(__METHOD__,"Редактирование комплектации №$id");
        }
        
        $num_rows['4']   = 0;
        $num_rows['2']   = 0;
        $reserv_sVersion = 0;
        $query           = "SELECT COUNT(0) AS ROW_COUNT FROM `robots` WHERE `progress` != 100 AND `remont` = 0 AND `version` = 4 AND `delete` = 0";
        $result = $this->pdo->query($query);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        $num_rows['4'] = count($rows);

        $query         = "SELECT COUNT(0) AS ROW_COUNT FROM `robots` WHERE `progress` != 100 AND `remont` = 0 AND `version` = 2 AND `delete` = 0";
        $result = $this->pdo->query($query);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        $num_rows['2'] = count($rows);

        foreach ($equipment_arr as &$value) {
            $row_id = $value['0'];
            $pos_id = $value['1'];
            $count  = $value['4'];
            if ($row_id == "")
                $row_id = "''";
            //$query = "INSERT INTO `robot_equipment_items` (`equipment_id`, `pos_id`, `count`) VALUES ( $id, $pos_id,$count);";
            $query = "INSERT INTO `robot_equipment_items` (`id`,`pos_id`,`equipment_id`,`count`) VALUES ($row_id,$pos_id,$id,$count) ON DUPLICATE KEY UPDATE `count` = $count";
            $result = $this->pdo->query($query);
            if ($id == 4) {
                $sVersion = 2;
            }
            if ($id == 2) {
                $sVersion = 4;
            }
            $query = "SELECT * FROM `robot_equipment_items` WHERE `pos_id` = $pos_id AND `equipment_id` = $sVersion";
            $result = $this->pdo->query($query);
            unset($sVersion_array);
            while ($line = $result->fetch()) {
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
            //$query  = "UPDATE `pos_items` SET `reserv` = '$reserv' WHERE `id` = $pos_id";
            //$result = $this->pdo->query($query);
        }
        return $result;
    }

    //надо переносить в роботы там уже есть список
    /*function get_equipment()
    {
        $query = "SELECT * FROM robot_equipment ORDER BY `title` DESC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment_array[] = $line;
        }
        if (isset($equipment_array))
            return $equipment_array;
    }*/

    function get_info_equipment($id)
    {
        $query = "SELECT * FROM robot_equipment WHERE id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment_array[] = $line;
        }

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
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment_array[] = $line;
        }

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
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment_array[] = $line;
        }

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
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment_array[] = $line;
        }

        if (isset($equipment_array))
            return $equipment_array;
    }

    
    /*Сборки */

    //создание сборки
    function add_assembly($json)
    {
        $equipment_arr = json_decode($json);
        $title         = $equipment_arr['0']['0'];
        array_shift($equipment_arr);
        $query = "INSERT INTO `pos_assembly` (`id_assembly`, `title`) VALUES (NULL, '$title')";
        $result = $this->pdo->query($query);

        $idd   = $this->pdo->lastInsertId();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлена новая сборка №$idd - $title");
        }
        
        foreach ($equipment_arr as &$value) {
            $pos_id = $value['0'];
            $count  = $value['3'];
            $query  = "INSERT INTO `pos_assembly_items` (`id_assembly`, `id_pos`, `count`) VALUES ( $idd, $pos_id,$count);";
            $result = $this->pdo->query($query);
        }
        return $result;
    }

    //редактирование сборки
    function edit_assembly($id, $json)
    {
        $equipment_arr = json_decode($json);
        $title         = $equipment_arr['0']['0'];
        array_shift($equipment_arr);
        $query = "UPDATE `pos_assembly` SET `title` = '$title' WHERE `id_assembly` = $id";
        $result = $this->pdo->query($query);
        
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
            $result = $this->pdo->query($query);
        }
        return $result;
    }

    //отдает массив данных по всем сборкам
    function get_assembly()
    {
        //$query = "SELECT * FROM pos_assembly ORDER BY `title` ASC";
        $query = "SELECT `pos_assembly`.*,`pos_items`.`vendor_code` FROM `pos_assembly` LEFT JOIN `pos_items` ON pos_assembly.id_assembly = pos_items.assembly ORDER BY `title` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment_array[] = $line;
        }
        if (isset($equipment_array))
            return $equipment_array;
    }

    //информация по сборке
    function get_info_assembly($id)
    {
        $query = "SELECT * FROM pos_assembly WHERE id_assembly='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment_array[] = $line;
        }

        if (isset($equipment_array))
            return $equipment_array['0'];
    }

    //список позиций в сборке
    function get_pos_in_assembly($id = 0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND pos_assembly_items.id_assembly='$id'";
        }
        $query = "SELECT pos_assembly_items.id_row, pos_assembly_items.id_assembly, pos_assembly_items.id_pos, pos_items.title, pos_items.vendor_code, pos_assembly_items.count, pos_items.total, pos_items.reserv, pos_items.category, pos_items.subcategory, pos_items.price, pos_items.provider, pos_items.summary, pos_items.version, pos_items.assembly, pos_items.min_balance FROM `pos_assembly_items` INNER JOIN pos_items ON pos_items.id = pos_assembly_items.id_pos WHERE pos_assembly_items.id_row >0 $where";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment_array[] = $line;
        }

        if (isset($equipment_array))
            return $equipment_array;
    }

    //
    function get_pos_in_assambly_cat($id = 0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND pos_items.category=$id";
        }
        $query = "SELECT pos_assembly_items.id_row, pos_assembly_items.id_assembly, pos_assembly_items.id_pos, pos_items.title, pos_items.vendor_code, pos_assembly_items.count, pos_items.total, pos_items.subcategory, pos_items.category, pos_items.provider, pos_items.price FROM `pos_assembly_items` INNER JOIN pos_items ON pos_items.id =  pos_assembly_items.id_pos WHERE  pos_assembly_items.id_row >0 $where";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment_array[] = $line;
        }

        if (isset($equipment_array))
            return $equipment_array;
    }

    //удаление позиции из сборки
    function del_pos_assembly($id, $id_row)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `pos_assembly_items` WHERE `id_row` = $id_row";
        $result = $this->pdo->query($query);
        
        if ($result) {
             $this->log->add(__METHOD__,"Удаление позиции из сборки №$id");
        }
        

        return $result;
    }

    //выбрать все сборки в которых состоит позиция
    function get_assembly_by_pos($id)
    {
        $query = "SELECT id_assembly FROM `pos_assembly_items` WHERE id_pos = $id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $kit_array[] = $line;
        }
        return (isset($kit_array)) ? $kit_array : null;
    }


    /*Комплекты */

    //создание комплекта
    function add_kit($json)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $kit_arr = json_decode($json);
        $title         = $kit_arr['0']['0'];
        $category         = $kit_arr['0']['1'];
        $version         = $kit_arr['0']['2'];
        $parent         = $kit_arr['0']['3'];
        array_shift($kit_arr);
        $query = "INSERT INTO `pos_kit` (`id_kit`,`parent_kit`, `kit_title`, `kit_category`, `version`, `update_user`, `update_date`) VALUES (NULL, '$parent','$title', '$category', '$version', $user_id, '$date')";
        $result = $this->pdo->query($query);

        $idd   = $this->pdo->lastInsertId();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлена новый комплект №$idd - $title");
        }
        
        foreach ($kit_arr as &$value) {
            $pos_id = $value['0'];
            $count  = $value['3'];
            $query  = "INSERT INTO `pos_kit_items` (`id_kit`, `id_pos`, `count`, `version`, `update_user`, `update_date`) VALUES ( $idd, $pos_id,$count,$version, $user_id, '$date');";
            $result = $this->pdo->query($query);
        }
        return $result;
    }

    //редактирование комплекта
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
        $result = $this->pdo->query($query);
        
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
            $result = $this->pdo->query($query);
        }
        return $result;
    }

    //список комплектов по категории, версии, опции
    function get_kit($category=0,$version = -1,$option = -1)
    {
        $where = "";
        
        if ($category != 0) $where .= " AND pos_kit.kit_category = $category";
        if ($version != -1) $where .= " AND pos_kit.version = $version";
        if ($option != -1) $where .= " AND pos_kit.option = $option";
        
        
        $query = "SELECT pos_kit.kit_title, pos_kit.id_kit, pos_kit.version,  pos_category.title FROM pos_kit JOIN pos_category ON pos_kit.kit_category = pos_category.id WHERE pos_kit.id_kit > 0  $where  ORDER BY pos_kit.version ASC, pos_kit.kit_title ASC";
      // echo $query;

        $result = $this->pdo->query($query);
        $cnt=0;
            while ($line = $result->fetch()) {
            $idd = $line['id_kit'];
            $query2 = "SELECT COUNT(*) FROM `check` WHERE `id_kit` = $idd";
            $result2 = $this->pdo->query($query2);
            $line2 = $result2->fetch();
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

    //информация по комплекту
    function get_info_kit($id)
    {
        $query = "SELECT * FROM pos_kit WHERE id_kit='$id'";
        $result = $this->pdo->query($query);
        $cnt=0;
        while ($line = $result->fetch()) {
            $idd = $line['id_kit'];
            $query2 = "SELECT COUNT(*) FROM `check` WHERE `id_kit` = $idd";
            $result2  = $this->pdo->query($query2);
            $line2 = $result2->fetch();
            $count = $line2['COUNT(*)'];
            
            $kit_array[$cnt]['id_kit'] = $line['id_kit'];
            $kit_array[$cnt]['kit_title'] = $line['kit_title'];
            $kit_array[$cnt]['kit_category'] = $line['kit_category'];
            $kit_array[$cnt]['kit_version'] = $line['version'];
            $kit_array[$cnt]['count'] = $count;
            
            $cnt++;
        }

        if (isset($kit_array))
            return $kit_array['0'];
    }

    //взять позиции в коамплекте
    function get_pos_in_kit($id = 0)
    {
        $where = "";
        if ($id != 0) {
            $where .= " AND pos_kit_items.id_kit='$id'";
        }
        $query = "SELECT pos_kit_items.id_row, pos_kit_items.id_kit, pos_kit_items.id_pos, pos_items.title, pos_items.vendor_code, pos_kit_items.count, pos_items.total, pos_items.reserv, pos_items.subcategory, pos_items.price FROM `pos_kit_items` INNER JOIN pos_items ON pos_items.id = pos_kit_items.id_pos WHERE pos_kit_items.id_row >0 $where";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $kit_array[] = $line;
        }

        if (isset($kit_array))
            return $kit_array;
    }

    //удалить позицию в комплекте
    function del_pos_kit($id, $id_row)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `pos_kit_items` WHERE `id_row` = $id_row";
        $result = $this->pdo->query($query);
       
        if ($result) {
             $this->log->add(__METHOD__,"Удаление позиции из комплекта №$id");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }

    //выбрать все комплекты в которых состоит позици
    function get_kit_by_pos($id)
    {
        $query = "SELECT id_kit FROM `pos_kit_items` WHERE id_pos = $id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $kit_array[] = $line;
        }
        return (isset($kit_array)) ? $kit_array : null;
    }

    //
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

        $query = "SELECT pos_items.id, pos_items.title, pos_items.category, pos_items.vendor_code,SUM(pos_kit_items.count), pos_kit_items.version, pos_items.total, pos_items.subcategory, pos_items.provider, pos_items.price, pos_items.summary, pos_items.assembly , pos_items.min_balance FROM pos_kit_items JOIN pos_items ON pos_kit_items.id_pos = pos_items.id WHERE pos_kit_items.id_pos  > 0   AND pos_kit_items.delete = 0 $where GROUP BY pos_kit_items.id_pos ";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $id = $line['id'];
            $equipment_array[$id] = $line;
        }

        if (isset($equipment_array))
            return $equipment_array;
    }

    //собирает рекурсивно все дочерние наборы по ид первоночального родителя
    function get_all_children_kits_by_id($id)
    {
        $query = "SELECT * FROM `pos_kit` WHERE parent_kit = $id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $kit_array[] = $line;
        }
        if (isset($kit_array)) {
            foreach ($kit_array as $kit) {
                $kit_array_children = $this->get_all_children_kits_by_id($kit['id_kit']);
                if (isset($kit_array_children)) {
                    $kit_array = array_merge($kit_array, $kit_array_children);
                }
            }
            return $kit_array;
        }

        return null;
    }

    //собирает рекурсивно все родительские наборы по ид первоночального родителя
    function get_all_parents_kits_by_id($id)
    {
        $query = "SELECT * FROM `pos_kit` WHERE id_kit = $id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $kit_array[] = $line;
        }

        if (isset($kit_array)) {
            foreach ($kit_array as $kit) {
                $kit_array_parent = $this->get_all_parents_kits_by_id($kit['parent_kit']);
                if (isset($kit_array_parent)) {
                    $kit_array = array_merge($kit_array, $kit_array_parent);
                }
            }
            return $kit_array;
        }

        return null;
    }

    //выдает массив набора/ов в том числе с дочерними начиная с текущего
    function get_all_kits_by_id($id)
    {
        $kit_array_parents = $this->get_all_parents_kits_by_id($id);
        //$kit_array_children = $this->get_all_children_kits_by_id($id);
        $kit_array_children = [];
        if (!is_array($kit_array_parents)) {
            $kit_array_parents = [];
        } else {
            foreach ($kit_array_parents as $kit) {
                $kit_array = $this->get_all_children_kits_by_id($kit['id_kit']);
                if (isset($kit_array)) {
                    $kit_array_children = array_merge($kit_array_children, $kit_array);
                }
            }
        }
        /*if (!is_array($kit_array_children)) {
            $kit_array_children = [];
        }*/

        $kit_array = array_merge($kit_array_parents, $kit_array_children);
        if (is_array($kit_array)) {
            //удаляем повторы
            $result = [];
            $id_kits = [];
            foreach ($kit_array as $kit) {
                if (!in_array($kit['id_kit'], $id_kits)) {
                    $result[] = $kit;
                    $id_kits[] = $kit['id_kit'];
                }
            }
            //сортеруем по полю id_kit
            uasort($result, function($a, $b){
                return ($a['id_kit'] - $b['id_kit']);
            });
        }

        return (isset($result)) ? $result : null;
    }

    /*Логирование */

    //создать лог в пос_лог
    function add_log($param)
    {
        $id      = $param['id'];
        $type    = $param['type'];
        $count   = $param['count'];
        $title   = $param['title'];
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "SELECT * FROM `pos_items` WHERE id = $id";
        $result = $this->pdo->query($query);
        $line       = $result->fetch();
        $old_count  = $line['total'];
        $old_reserv = $line['reserv'];
        switch ($type) {
            case "edit":
                $title = $title . ": Новое значение -> $old_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_count`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$title', '$date', '$user_id')";
                break;
            /*
            case "reserv":
                $title = $title . ": $count шт. Всего в резерве: $old_reserv";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_reserv`, `title`,`update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv',  '$title', '$date', '$user_id')";
                break;
            case "unreserv":
                $title = $title . ": $count шт. Всего в резерве: $old_reserv";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`,  `new_reserv`, `title`,`update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv', '$title', '$date', '$user_id')";
                break;
            */
            case "writeoff":
                $tmp   = $old_count - $count;
                $title = $title . ": $count шт. $old_count -> $tmp";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`,`update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$count', '$title', '$date', '$user_id')";
                break;
        }
        $result = $this->pdo->query($query);
    }
    
    //взять лог в пос_лог
    function get_log($id)
    {
        $query = "SELECT * FROM `pos_log` WHERE `id_pos` = $id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $log_array[] = $line;
        }

        if (isset($log_array))
            return $log_array;
    }

    
    /*Поставщики*/
    
    //удалить поставщика
    function del_provider($id)
    {
        
        $query   = "DELETE FROM `pos_provider` WHERE `id` = $id";
        $result = $this->pdo->query($query);
       
        if ($result) {
             $this->log->add(__METHOD__,"Удаление поставщика №$id");
        }
        

        return $result;
    }

    
    /*Удаленный склад*/
    
    //Перемещение позиции с основного на удаленный склад
    function to_warehouse($pos_id)
    {
        $pos = $this->get_info_pos($pos_id);
        if (isset($pos)) {
            $category = $pos['category'];
            $subcategory = $pos['subcategory'];
            $title = $pos['title'];
            $vendorcode = $pos['vendor_code'];
            $provider = $pos['provider'];
            $price = $pos['price'];
            $longtitle = $pos['longtitle'];
            $version = $pos['version'];
            $quant_robot = $pos['quant_robot'];
            $total = $pos['total'];
            $reserv = $pos['reserv'];
            $assembly = $pos['assembly'];
            $summary = $pos['summary'];
            $apply = $pos['apply'];
            $ow = $pos['ow'];
            $min_balance = $pos['min_balance'];
            $img = $pos['img'];
            $archive = $pos['archive'];
            $date = $pos['update_date'];
            $user_id = $pos['update_user'];

            $query   = "INSERT INTO `pos_items_warehouse` (`id`, `category`, `subcategory`,`title`, `vendor_code`, `provider`, `price`, `longtitle`, `version`, `quant_robot`, `total`, `reserv`, `assembly`, `summary`,`apply`, `ow`, `min_balance`, `img`, `archive`, `update_date`, `update_user` ) VALUES ('$pos_id', '$category', '$subcategory', '$title', '$vendorcode', '$provider', '$price', '$longtitle', '$version', '$quant_robot', '$total', '$reserv', '$assembly', '$summary', '$apply', '$ow', '$min_balance', '$img', '$archive', '$date', '$user_id')";
            $result = $this->pdo->query($query);

            if ($result) {
                $this->del_pos($pos_id);
                $this->log->add(__METHOD__,"Создана новая позиция на удаленном складе $vendorcode $title");
                return true;
            }
        }

        return null;
    }

    function __destruct()
    {
    }
}
