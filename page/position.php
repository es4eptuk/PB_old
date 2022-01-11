<?php
class Position
{
    const STATUS_ACTIVE = 1;
    const STATUS_NOTACTIVE = 0;
    const ALLOWED = [
        "Nomenclature" => [17,112,101,35,43,14,75,124,130,134,123,118,128,102,137,28],
        "Assembly" => [17,112,101,35,43,14,75,124,130,134,123,118,128,102,137,28],
    ];

    private $query;
    private $pdo;
    private $writeoff;
    private $log;
    private $robots;
    private $plan;

    //списки
    public $getCategoryes;
    public $getSubcategoryes;
    public $getUnits;
    public $getBrends;
    public $getStatus;

    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase, $dbconnect;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        //$this->pdo = new PDO($dsn, $database_user, $database_password, $opt);
        $this->pdo = &$dbconnect->pdo;
    }

    function init()
    {
        global $writeoff, $log, $robots, $plan;

        $this->writeoff = $writeoff; //new Writeoff;
        $this->log = $log; //new Log;
        $this->robots = $robots;
        $this->plan = $plan;

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

        //список ед измерений
        $this->getUnits = [
            0 => ['id' => 0, 'title' => ''],
            1 => ['id' => 1, 'title' => 'шт'],
            2 => ['id' => 2, 'title' => 'кг'],
            3 => ['id' => 3, 'title' => 'м.кв'],
            4 => ['id' => 4, 'title' => 'л'],
            5 => ['id' => 5, 'title' => 'м'],
            6 => ['id' => 6, 'title' => 'т'],
            7 => ['id' => 7, 'title' => 'комп'],
            8 => ['id' => 8, 'title' => 'г'],
        ];

        //список брэндов
        $this->getBrends = $this->get_list_brend(self::STATUS_ACTIVE);

        //
        $this->getStatus[self::STATUS_NOTACTIVE] = "не активно";
        $this->getStatus[self::STATUS_ACTIVE] = "активно";
    }

    function getAllowedNomenclature($userId) {
        return (in_array($userId, self::ALLOWED["Nomenclature"])) ? true : false;
    }
    function getAllowedAssembly($userId) {
        return (in_array($userId, self::ALLOWED["Assembly"])) ? true : false;
    }

    /**
     * @param $id
     * @param $new_total
     * @return false|PDOStatement
     * Инвентаризация позиций
     */
    function invent($id, $new_total, $description = null, $check = false) {
        $info_pos = $this->get_info_pos($id);
        $o = ($check) ? $info_pos['total'] != $new_total : true;
        if ($o) {
            $query   = "UPDATE `pos_items` SET  `total` = '$new_total' WHERE `pos_items`.`id` = $id;";
            $result = $this->pdo->query($query);
            $description = ($description) ? "Инвентаризация ".$description." " : "Инвентаризация ";
            if ($info_pos != []) {
                $title = $info_pos['title'];
                $vendorcode = $info_pos['vendor_code'];
                if ($result) {
                    $log_title      = "$description ";
                    $param['id']    = $id;
                    $param['type']  = "edit";
                    $param['count'] = $new_total;
                    $param['title'] = $log_title;
                    $this->add_log($param);
                    $this->log->add(__METHOD__,"$description  $vendorcode - $title -> $new_total");
                }
            }
            return $result;
        } else {
            return true;
        }

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
     * Привязка позиции к комплектам
     */
    function get_pos_versions()
    {
        $query = "SELECT * FROM `check_items` WHERE `kit` != 0";
        $result = $this->pdo->query($query);
        $checks = [];
        while ($line = $result->fetch()) {
            $checks[$line['id']] = $line;
        }
        $kits = [];
        foreach ($checks as $check) {
            if (array_key_exists($check['kit'], $kits)) {
                $kits[$check['kit']] = array_merge($kits[$check['kit']], [$check['version']]);
            } else {
                $kits[$check['kit']] = [$check['version']];
            }
            $par_kits = $this->get_all_kits_by_id($check['kit']);
            if ($par_kits != null) {
                foreach ($par_kits as $kit) {
                    if (array_key_exists($kit['id_kit'], $kits)) {
                        $kits[$kit['id_kit']] = array_merge($kits[$kit['id_kit']], [$check['version']]);
                    } else {
                        $kits[$kit['id_kit']] = [$check['version']];
                    }
                }
            }
        }
        unset($checks);
        foreach ($kits as $id => $kit) {
            $kits[$id] = array_unique($kit);
        }

        $query = "SELECT * FROM `pos_kit_items` JOIN `pos_items` ON `pos_items`.`id` = `pos_kit_items`.`id_pos`";
        $result = $this->pdo->query($query);
        $kits_pos_items = [];
        while ($line = $result->fetch()) {
            if (array_key_exists($line['id_kit'], $kits)) {
                $kits_pos_items[$line['id_kit']][$line['id']] = $line;
            }
        }

        $assemblyes = $this->plan->get_assemblyes_items_new();
        $result = [];
        foreach ($kits_pos_items as $id_kit => $items) {
            foreach ($items as $item) {
                if (array_key_exists($item['id'], $result)) {
                    $result[$item['id']] = array_merge($result[$item['id']], $kits[$id_kit]);
                } else {
                    $result[$item['id']] = $kits[$id_kit];
                }
                if ($item['assembly'] != 0 && array_key_exists($item['assembly'], $assemblyes)) {
                    foreach ($assemblyes[$item['assembly']] as $item_dop => $count) {
                        if (array_key_exists($item_dop, $result)) {
                            $result[$item_dop] = array_merge($result[$item_dop], $kits[$id_kit]);
                        } else {
                            $result[$item_dop] = $kits[$id_kit];
                        }
                    }
                }
            }
        }
        unset($kits);
        unset($kits_pos_items);
        foreach ($result as $pos_id => $vers) {
            $result[$pos_id] = array_unique($vers);
        }

        if (isset($result))
            return $result;
    }

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

    //все позиции
    function get_pos_all()
    {
        $query = "SELECT * FROM `pos_items`";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[$line['id']] = $line;
        }

        return (isset($pos_array)) ? $pos_array : [];
    }

    function get_pos_in_category($category = 0, $subcategory = 0, $version = 0, $archive = 0)
    {
        $where = "";
        if ($category != 0) {
            $where .= " AND `category` = $category ";
        }
        if ($subcategory != 0) {
            $where .= " AND `subcategory` = $subcategory ";
        }
        if ($version != 0) {
            $where .= " AND `version` = $version ";
        }
        if ($archive == 0) {
            $where .= " AND `archive` = 0 ";
        }
        $query = "SELECT * FROM `pos_items` WHERE `id` > 0" . $where;
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[$line['id']] = $line;
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


        return (isset($pos_array)) ? $pos_array['0'] : null;
    }

    function get_info_pos($id)
    {
        $query = "SELECT * FROM pos_items WHERE id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }


        if (isset($pos_array['0'])) {
            $arr_ver = $this->get_pos_versions();
            if (array_key_exists($pos_array['0']['id'], $arr_ver)) {
                $versions = $arr_ver[$pos_array['0']['id']];
                $text_versions = [];
                foreach ($versions as $version) {
                    $text_versions[] = $this->robots->getEquipment[$version]['title'];
                }
                $pos_array['0']['versions'] = implode(", ", $text_versions);
            } else {
                $pos_array['0']['versions'] = "";
            }

        }

        return (isset($pos_array)) ? $pos_array['0']: [];
        /*if (isset($pos_array))
            return $pos_array['0'];*/
    }

    function add_pos($title, $longtitle, $category, $unit, $subcategory, $vendorcode, $provider, $price, $quant_robot, $quant_total, $development, $p_vendor, $p_vendor_code)
    {
        $query = "SELECT * FROM pos_items WHERE `title` LIKE '$title'";
        $result = $this->pdo->query($query);
        $pos = $result->fetch();
        if ($pos != []) {
            return ['status' => false, 'error' => 'Позиция с таким наименованием уже существует!'];
        }

        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $title = quotemeta($title);
        $query   = "INSERT INTO `pos_items` (`id`, `category`, `unit`, `subcategory`, `title`, `vendor_code`, `provider`, `price`, `longtitle`, `quant_robot`, `total`, `update_date`, `update_user`, `development`, `p_vendor`, `p_vendor_code`) VALUES (NULL, '$category', '$unit', '$subcategory', '$title', '$vendorcode', $provider, '$price', '$longtitle', '$quant_robot', '$quant_total', '$date', '$user_id', '$development', '$p_vendor', '$p_vendor_code')";
        $result = $this->pdo->query($query);
        
        if ($result) {
            $this->log->add(__METHOD__,"Добавлена новая позиция $vendorcode $title через перемещение");
            return ['status' => true, 'error' => ''];
        }

        return ['status' => false, 'error' => 'Что то пошло не так!'];
    }
    function edit_pos($id, $title, $longtitle, $unit, $category, $subcategory, $vendorcode, $provider, $price, $quant_robot, $quant_total, $min_balance, $assembly, $summary, $archive, $file=null, $development, $p_vendor, $p_vendor_code)
    {
        $query = "SELECT * FROM pos_items WHERE `title` LIKE '$title' AND `id` != $id";
        $result = $this->pdo->query($query);
        $pos = $result->fetch();
        if ($pos != []) {
            return ['status' => false, 'error' => 'Позиция с таким наименованием уже существует!'];
        }

        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `pos_items` SET `title` = '$title', `longtitle` = '$longtitle', `category` = '$category', `unit` = '$unit', `subcategory` = '$subcategory', `provider` = '$provider', `price` = '$price', `quant_robot` = '$quant_robot', `total` = '$quant_total', `min_balance` = '$min_balance', `vendor_code` = '$vendorcode', `assembly` = '$assembly', `summary` = '$summary', `archive` = '$archive', `update_date` = '$date', `update_user` = '$user_id', `development` = '$development', `p_vendor` = '$p_vendor', `p_vendor_code` = '$p_vendor_code' WHERE `pos_items`.`id` = $id;";
        $result = $this->pdo->query($query);

        if ($result) {
            if ($quant_total != 0) {
                $log_title      = "Редактирвоание позиции";
                $param['id']    = $id;
                $param['type']  = "edit";
                $param['count'] = $quant_total;
                $param['title'] = $log_title;
                $this->add_log($param);
                $this->log->add(__METHOD__,"Редактирование позиции  $vendorcode - $title");
            }
            return ['status' => true, 'error' => ''];
        } else {
            return ['status' => false, 'error' => 'Что то пошло не так!'];
        }
    }

    function edit_provider($id, $title, $name, $type, $phone, $email, $address, $contact)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `pos_provider` SET `title` = '$title', `name` = '$name', `type` = '$type',`phone` = '$phone',`email` = '$email', `address` = '$address', `contact` = '$contact', `update_date` = '$date' , `update_user` = '$user_id' WHERE `id` = $id;";
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
    
    function add_full_provider($type, $title, $name, $phone, $email, $address, $contact)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `pos_provider` (`id`, `type`, `title`, `name`, `phone`, `email`, `address`, `contact`, `update_date`, `update_user`) VALUES (NULL, '$type', '$title', '$name', '$phone', '$email', '$address', '$contact', '$date', $user_id);";
        $result = $this->pdo->query($query);
        $idd   = $this->pdo->lastInsertId();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлен поставщик $type $title");
        }
        

        return true;
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
    function add_reserv($arr_pos)
    {
        foreach ($arr_pos as $id_pos => $info) {
            $count = $info;
            $query = "UPDATE `pos_items` SET `reserv` = reserv + $count WHERE `id` = $id_pos";
            $result = $this->pdo->query($query);
        }
        return true;
    }

    //списание деталей из резерва
    function del_reserv($arr_pos)
    {
        foreach ($arr_pos as $id_pos => $info) {
            $count = $info;
            $query = "UPDATE `pos_items` SET `reserv` = reserv - $count WHERE `id` = $id_pos";
            $result = $this->pdo->query($query);
        }
        return true;
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
        return true;
    }

    function set_writeoff_kit0($version, $number, $kit, $check, $robot)
    {
        $arr_assemble = $this->plan->get_assemblyes_items();//plan->get_assemblyes_items();
        $arr_pos_kit = $this->get_pos_in_kit($kit);

        $info_kit = $this->get_info_kit($kit);
        $title_kit = $info_kit['kit_title'];
        $json['0']['0'] = "Производство";
        $json['0']['1'] = "Робот  $version.$number комплект - $title_kit";
        $json['0']['2'] = $check;
        $json['0']['3'] = $robot;
        $count          = 1;
        //print_r($arr_pos);
        foreach ($arr_pos_kit as &$value) {
            //print_r($value);
            $pos_id2 = $value['id_pos'];
            $value_count = $value['count'];
            $info_pos          = $this->get_info_pos($pos_id2);
            $json[$count]['0'] = $pos_id2;
            $json[$count]['1'] = $info_pos['vendor_code'];
            $json[$count]['2'] = $info_pos['title'];
            $json[$count]['3'] = $value_count;
            $json[$count]['4'] = $info_pos['price'];
            $count++;
            if ($info_pos['assembly'] != 0) {
                foreach ($arr_assemble[$info_pos['assembly']] as $id_pos_a => $count_a) {
                    $info_pos_a        = $this->get_info_pos($id_pos_a);
                    $json[$count]['0'] = $id_pos_a;
                    $json[$count]['1'] = $info_pos_a['vendor_code'];
                    $json[$count]['2'] = $info_pos_a['title'];
                    $json[$count]['3'] = $value_count * $count_a;
                    $json[$count]['4'] = $info_pos_a['price'];
                    $count++;
                }
            }

        }
        //print_r($json);
        $this->writeoff->add_writeoff(json_encode($json));
        return true;
    }

    //удаляет списание
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
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $equipment_arr = json_decode($json);
        $title         = $equipment_arr['0']['0'];
        array_shift($equipment_arr);
        $query = "INSERT INTO `pos_assembly` (`id_assembly`, `title`, `update_user`, `update_date`) VALUES (NULL, '$title', '$user_id', '$date')";
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
        return true;//$result;
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
    function get_assembly($archive=true, $assembly=true)
    {
        //весь список
        $query = "SELECT `pos_assembly`.*,`pos_items`.`vendor_code`, `pos_items`.`archive`  FROM `pos_assembly` LEFT JOIN `pos_items` ON pos_assembly.id_assembly = pos_items.assembly ORDER BY `title` ASC";
        $result = $this->pdo->query($query);
        $all = [];
        while ($line = $result->fetch()) {
            $all[$line['id_assembly']] = $line;
        }

        /*$where = "WHERE `pos_assembly`.`id_assembly` != 0";
        if (!$archive) {
            $where .= " AND `pos_items`.`archive` = 0";
        }*/

        //все привязанные
        /*$query = "SELECT `pos_assembly`.*,`pos_items`.* FROM `pos_assembly` LEFT JOIN `pos_items` ON `pos_assembly`.`id_assembly` = `pos_items`.`assembly` ORDER BY `pos_assembly`.`title` ASC";
        $result = $this->pdo->query($query);
        $arr_assembly = [];
        while ($line = $result->fetch()) {
            $arr_assembly[] = $line;
        }*/

        $result = [];
        //все в не зависимости от привязки (true, true)
        if ($archive && $assembly) {
            return $all;
        }

        //все привязанные (true, false)
        if ($archive && !$assembly) {
            foreach ($all as $id_assembly => $assembly) {
                if (isset($assembly['vendor_code'])) {
                    $result[$id_assembly] = $assembly;
                }
            }
            return $result;
        }

        //привязанные не архивные (false, false)
        if (!$archive && !$assembly) {
            foreach ($all as $id_assembly => $assembly) {
                if (isset($assembly['vendor_code'])) {
                    if ($assembly['archive'] == 0) {
                        $result[$id_assembly] = $assembly;
                    }
                }
            }
            return $result;
        }

        //все, но с условием что не в архивае (false, true)
        if (!$archive && $assembly) {
            foreach ($all as $id_assembly => $assembly) {
                if (isset($assembly['vendor_code'])) {
                    if ($assembly['archive'] == 0) {
                        $result[$id_assembly] = $assembly;
                    }
                } else {
                    $result[$id_assembly] = $assembly;
                }
            }
            return $result;
        }
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
            $kit_array[] = $line['id_assembly'];
        }
        return (isset($kit_array)) ? array_unique($kit_array) : null;
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
             $this->log->add(__METHOD__,"Добавлен новый комплект №$idd - $title");
        }
        
        foreach ($kit_arr as &$value) {
            $pos_id = $value['0'];
            $count  = $value['3'];
            $query  = "INSERT INTO `pos_kit_items` (`id_kit`, `id_pos`, `count`, `version`, `update_user`, `update_date`) VALUES ( $idd, $pos_id,$count,$version, $user_id, '$date');";
            $result = $this->pdo->query($query);
            $query  = "UPDATE `pos_items` SET `development` = 0 WHERE `id` = $pos_id";
            $result = $this->pdo->query($query);
        }
        return $result;
    }

    //удаление комплекта
    function del_kit($id)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);

        $query  = "UPDATE `pos_kit` SET `delete` = 1, `update_user` = '$user_id', `update_date` = '$date' WHERE `id_kit` = $id";
        $result = $this->pdo->query($query);

        return $result;
    }

    //разделение комплекта
    function add_split_kit($kit1, $kit2)
    {
        $this->add_kit($kit1);
        $this->add_kit($kit2);
        return true;
    }

    //редактирование комплекта
    function edit_kit($id, $json)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $kit_arr = json_decode($json);
        $title = $kit_arr['0']['0'];
        $category = $kit_arr['0']['1'];
        $version = $kit_arr['0']['2'];
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
            $query  = "UPDATE `pos_items` SET `development` = 0 WHERE `id` = $pos_id";
            $result = $this->pdo->query($query);
        }
        return $result;
    }

    //список комплектов по категории, версии, опции
    function get_kit($category=0,$version = -1,$option = -1, $del = 0)
    {
        $where = "";
        
        if ($category != 0) $where .= " AND `pos_kit`.`kit_category` = $category";
        if ($version != -1) $where .= " AND `pos_kit`.`version` = $version";
        if ($option != -1) $where .= " AND `pos_kit`.`option` = $option";
        if ($del == 1) $where .= " AND `pos_kit`.`delete` = 0";
        
        
        $query = "SELECT `pos_kit`.`kit_title`, `pos_kit`.`id_kit`, `pos_kit`.`version`, `pos_category`.`title`, `pos_kit`.`delete` FROM `pos_kit` JOIN `pos_category` ON `pos_kit`.`kit_category` = `pos_category`.`id` WHERE `pos_kit`.`id_kit` > 0 $where ORDER BY `pos_kit`.`version` ASC, `pos_kit`.`kit_title` ASC";
      // echo $query;

        $result = $this->pdo->query($query);
        $cnt=0;
        while ($line = $result->fetch()) {
            $idd = $line['id_kit'];
            $checks = [];
            $query1 = "SELECT * FROM `check_items` WHERE `kit` = $idd";
            $result1 = $this->pdo->query($query1);
            while ($line1 = $result1->fetch()) {
                $checks['check_items'][$line1['id']] = $line1['title'];
            }
            $query2 = "SELECT * FROM `robot_options_checks` WHERE `id_kit` = $idd";
            $result2 = $this->pdo->query($query2);
            while ($line2 = $result2->fetch()) {
                $checks['check_options'][$line2['check_id']] = $line2['check_title'];
            }
            /*
            $count = 0;
            $query2 = "SELECT COUNT(*) FROM `check_items` WHERE `kit` = $idd";
            $result2 = $this->pdo->query($query2);
            $line2 = $result2->fetch();
            $count = $count + $line2['COUNT(*)'];
            $query3 = "SELECT COUNT(*) FROM `robot_options_checks` WHERE `id_kit` = $idd";
            $result3 = $this->pdo->query($query3);
            $line3 = $result3->fetch();
            $count = $count + $line3['COUNT(*)'];
            */
            
            $kit_array[$cnt]['id_kit'] = $line['id_kit'];
            $kit_array[$cnt]['kit_title'] = $line['kit_title'];
            $kit_array[$cnt]['title'] = $line['title'];
            $kit_array[$cnt]['version'] = $line['version'];
            //$kit_array[$cnt]['count'] = $count;
            $kit_array[$cnt]['checks'] = $checks;
            $kit_array[$cnt]['delete'] = $line['delete'];

            
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

    //взять позиции в комплекте
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
            $kit_array[] = $line['id_kit'];
        }

        return (isset($kit_array)) ? array_unique($kit_array) : null;
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
    function get_all_kits_by_id($id, $delete = 0)
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
                    if ($delete == 0) {
                        if ($kit['delete'] == 0) {
                            $result[] = $kit;
                        }
                    } else {
                        $result[] = $kit;
                    }

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
        $new_count = $line['total'];
        $old_reserv = $line['reserv'];

        switch ($type) {
            case "edit":
                $new_reserv = $old_reserv;
                $old_count = 0;
                $title = $title . ": Новое значение -> $new_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `old_reserv`, `new_reserv`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$new_count', '$title', '$old_reserv', '$new_reserv', '$date', '$user_id')";
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
                $new_reserv = $old_reserv;
                $old_count = $new_count + $count;
                $title = $title . ": $count шт. Новое значение -> $new_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `old_reserv`, `new_reserv`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$new_count', '$title', '$old_reserv', '$new_reserv', '$date', '$user_id')";
                break;
            case "addmission":
                $new_reserv = $old_reserv;
                $old_count = $new_count - $count;
                $title = $title . ": $count шт. Новое значение -> $new_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `old_reserv`, `new_reserv`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$new_count', '$title', '$old_reserv', '$new_reserv', '$date', '$user_id')";
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

    //ограничения на изменение комплектов
    function get_edit_eneble_kit($id)
    {
            $query = "SELECT COUNT(*) FROM `check` WHERE `id_kit` = $id";
            $result  = $this->pdo->query($query);
            $line = $result->fetch();
            $count = $line['COUNT(*)'];

            return ($count > 0) ? false : true;
    }

    //ограничения на удаление комплекта
    function get_del_eneble_kit($id)
    {
        $query = "SELECT COUNT(*) FROM `check_items` WHERE `kit` = $id";
        $result  = $this->pdo->query($query);
        $line = $result->fetch();
        $count = $line['COUNT(*)'];

        return ($count > 0) ? false : true;
    }

    //отправить позицию к которой привязан комплект в архив
    function assembly_to_archive($assembly_id)
    {
        $query = "UPDATE `pos_items` SET `archive` = 1 WHERE `assembly` = $assembly_id";
        $result  = $this->pdo->query($query);

        return ($result) ? true : false;
    }

    //загрузка из файла инвенторизации
    function set_inventory_from_file($file)
    {
        require_once('excel/Classes/PHPExcel.php');
        require_once('excel/Classes/PHPExcel/IOFactory.php');
        if (file_exists($file))
        {
            $objReader = PHPExcel_IOFactory::createReaderForFile($file);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($file);
            $worksheet = $objPHPExcel->getSheet(0);
            $excelData = $worksheet->toArray();
            $name = array_shift($excelData);
            if ($name[0] == 'Фото'
                && $name[1] == "ID"
                && $name[2] == "Артикул"
                && $name[3] == "Наименование"
                && $name[4] == "Ед.\nизм."
                && $name[5] == "Категория"
                && $name[6] == "Подкатегория"
                && $name[7] == "Сборная\nпозиция"
                && $name[8] == "Текущее\nкол-во"
                && $name[9] == "Новое\nкол-во") {
                $result = [];
                foreach ($excelData as $pos_item) {
                    if ($pos_item[9] !== null && $this->invent($pos_item[1], $pos_item[9])) {
                        $category = array_search($pos_item[5], array_column($this->getCategoryes, 'title'));
                        $filename = 'img/catalog/'.$category.'/'.$pos_item[2].".jpg";
                        $filename_thumb = 'img/catalog/'.$category.'/thumb/'.$pos_item[2].".jpg";
                        if (file_exists(PATCH_DIR . '/' . $filename_thumb)) {
                            $img =  '<a class="fancybox" href="'.$filename.'" target="_blank"><img alt="'.$pos_item[2].'" src="'.$filename_thumb.'"/></a>';
                        } else {
                            $img = "<img src='/img/no-image.png' width='100'></img>";
                        }
                        $result[$pos_item[1]] = [
                            'id' => $pos_item[1],
                            'vendor_code' => $pos_item[2] ? $pos_item[2] : '',
                            'title' => $pos_item[3] ? $pos_item[3] : '',
                            'unit' => $pos_item[4] ? $pos_item[4] : '',
                            'category' => $pos_item[5] ? $pos_item[5] : '',
                            'subcategory' => $pos_item[6] ? $pos_item[6] : '',
                            'assembly' => $pos_item[7],
                            'old_total' => $pos_item[8],
                            'new_total' => $pos_item[9],
                            'img' => $img,
                        ];
                    }
                }
                return ['status' => '200', 'result' => $result];
            } else {
                return ['status' => '201', 'result' => 'Шаблон не прошел проверку!'];
            }
        }
        return ['status' => '202', 'result' => 'Файл не загружен!'];
    }

    //выгрузка в файл для подверсии
    function get_file_pos_item_subversion($subversion_id)
    {
        $pos_items = [];
        $arr_subver = $this->robots->get_composition_subversion($subversion_id);
        //print_r($arr_subver);die;

        $query = "SELECT * FROM `pos_items` WHERE `archive` = 0";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            if (array_key_exists($line['id'], $arr_subver)) {
                $pos_items[$line['id']] = $line;
            }
        }
        unset($arr_subver);

        //создаем файлы
        $f_name = time();
        if (!file_exists(PATCH_DIR . "/orders/")) {
            mkdir(PATCH_DIR . "/orders/", 0777);
        }
        $excel_name = PATCH_DIR . "/orders/" . $f_name . ".xlsx";
        require_once('excel/Classes/PHPExcel.php');
        require_once('excel/Classes/PHPExcel/IOFactory.php');
        $objPHPExcel = new PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        //ширина
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(6);
        $sheet->getColumnDimension('C')->setWidth(9);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(6);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(8);
        $sheet->getColumnDimension('I')->setWidth(8);
        $sheet->getColumnDimension('J')->setWidth(8);

        //колонки
        $sheet->setCellValue("A1", "Фото");
        $sheet->setCellValue("B1", "ID");
        $sheet->setCellValue("C1", "Артикул");
        $sheet->setCellValue("D1", "Наименование");
        $sheet->setCellValue("E1", "Ед.\nизм.");
        $sheet->setCellValue("F1", "Категория");
        $sheet->setCellValue("G1", "Подкатегория");
        $sheet->setCellValue("H1", "Сборная\nпозиция");
        $sheet->setCellValue("I1", "Текущее\nкол-во");
        $sheet->setCellValue("J1", "Новое\nкол-во");
        $sheet->getRowDimension(1)->setRowHeight(50);

        $row = 1;
        foreach ($pos_items as $item) {
            $row++;
            $filename_thumb = PATCH_DIR.'/img/catalog/'.$item['category'].'/thumb/'.$item['vendor_code'].".jpg";
            $filename_thumb = (file_exists($filename_thumb)) ? $filename_thumb : PATCH_DIR.'/img/no-image.png';
            $logo = new PHPExcel_Worksheet_Drawing();
            $logo->setPath($filename_thumb);
            $logo->setCoordinates("A" . $row);
            $logo->setOffsetX(5);
            $logo->setOffsetY(5);
            $width = $logo->getWidth();
            $height = $logo->getHeight();
            if ($width > $height) {
                $logo->setWidth(70);
            } else {
                $logo->setHeight(70);
            }
            $logo->setWorksheet($sheet);
            unset($logo);

            $sheet->setCellValue("B" . $row, $item['id']);
            $sheet->setCellValue("C" . $row, $item['vendor_code']);
            $sheet->setCellValue("D" . $row, $item['title']);
            $sheet->setCellValue("E" . $row, $this->getUnits[$item['unit']]['title']);
            $sheet->setCellValue("F" . $row, $this->getCategoryes[$item['category']]['title']);
            $sheet->setCellValue("G" . $row, $this->getSubcategoryes[$item['subcategory']]['title']);
            $assembly = ($item['assembly'] != 0) ? "Да" : "Нет";
            $sheet->setCellValue("H" . $row, $assembly);
            $sheet->setCellValue("I" . $row, $item['total']);
            $sheet->setCellValue("J" . $row, "");
            $sheet->getRowDimension($row)->setRowHeight(70);
        }
        //для всей таблицы
        $styleArray = [
            'font' => [
                'name' => 'Calibri',
                'size' => 10,
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ],
            'borders' => [
                'outline' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'inside' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle("A1:J".$row)->applyFromArray($styleArray);

        //для отдельных колонок

        // Save
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($excel_name);

        return $excel_name;
    }

    //выгрузка в файл
    function get_file_pos_item($category_id = null, $subcategory_id = null)
    {
        $pos_items = [];
        $where = "WHERE `id` > 0"; //`archive` = 0";
        if ($category_id != null) {
            $where .= " AND `category` = $category_id";
        }
        if ($subcategory_id != null) {
            $where .= " AND `subcategory` = $subcategory_id";
        }
        $query = "SELECT * FROM `pos_items` $where";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_items[$line['id']] = $line;
        }

        //создаем файлы
        $f_name = time();
        if (!file_exists(PATCH_DIR . "/orders/")) {
            mkdir(PATCH_DIR . "/orders/", 0777);
        }
        $excel_name = PATCH_DIR . "/orders/" . $f_name . ".xlsx";
        require_once('excel/Classes/PHPExcel.php');
        require_once('excel/Classes/PHPExcel/IOFactory.php');
        $objPHPExcel = new PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        //ширина
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(6);
        $sheet->getColumnDimension('C')->setWidth(9);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(6);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(8);
        $sheet->getColumnDimension('I')->setWidth(8);
        $sheet->getColumnDimension('J')->setWidth(8);

        //колонки
        $sheet->setCellValue("A1", "Фото");
        $sheet->setCellValue("B1", "ID");
        $sheet->setCellValue("C1", "Артикул");
        $sheet->setCellValue("D1", "Наименование");
        $sheet->setCellValue("E1", "Ед.\nизм.");
        $sheet->setCellValue("F1", "Категория");
        $sheet->setCellValue("G1", "Подкатегория");
        $sheet->setCellValue("H1", "Сборная\nпозиция");
        $sheet->setCellValue("I1", "Текущее\nкол-во");
        $sheet->setCellValue("J1", "Новое\nкол-во");
        $sheet->getRowDimension(1)->setRowHeight(50);

        $row = 1;
        foreach ($pos_items as $item) {
            $row++;
            $filename_thumb = PATCH_DIR.'/img/catalog/'.$item['category'].'/thumb/'.$item['vendor_code'].".jpg";
            $filename_thumb = (file_exists($filename_thumb)) ? $filename_thumb : PATCH_DIR.'/img/no-image.png';
            $logo = new PHPExcel_Worksheet_Drawing();
            $logo->setPath($filename_thumb);
            $logo->setCoordinates("A" . $row);
            $logo->setOffsetX(5);
            $logo->setOffsetY(5);
            $width = $logo->getWidth();
            $height = $logo->getHeight();
            if ($width > $height) {
                $logo->setWidth(70);
            } else {
                $logo->setHeight(70);
            }
            $logo->setWorksheet($sheet);
            unset($logo);

            $sheet->setCellValue("B" . $row, $item['id']);
            $sheet->setCellValue("C" . $row, $item['vendor_code']);
            $sheet->setCellValue("D" . $row, $item['title']);
            $sheet->setCellValue("E" . $row, $this->getUnits[$item['unit']]['title']);
            $sheet->setCellValue("F" . $row, $this->getCategoryes[$item['category']]['title']);
            $sheet->setCellValue("G" . $row, $this->getSubcategoryes[$item['subcategory']]['title']);
            $assembly = ($item['assembly'] != 0) ? "Да" : "Нет";
            $sheet->setCellValue("H" . $row, $assembly);
            $sheet->setCellValue("I" . $row, $item['total']);
            $sheet->setCellValue("J" . $row, "");
            $sheet->getRowDimension($row)->setRowHeight(70);
        }
        //для всей таблицы
        $styleArray = [
            'font' => [
                'name' => 'Calibri',
                'size' => 10,
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ],
            'borders' => [
                'outline' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'inside' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle("A1:J".$row)->applyFromArray($styleArray);

        //для отдельных колонок

        // Save
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($excel_name);

        return $excel_name;
    }

    //БРЕНД
    function get_list_brend($status=null)
    {
        $where = ($status != null) ? " WHERE `status` = $status" : "";
        $query   = "SELECT * FROM `pos_brand`".$where;
        $result = $this->pdo->query($query);
        $array = [];
        while ($line = $result->fetch()) {
            $array[$line["id"]] = $line;
        }
        return $array;
    }
    function add_brend($name, $status=self::STATUS_ACTIVE)
    {
        $query   = "INSERT INTO `pos_brand` (`id`, `name`, `status`) VALUES (NULL, '$name', $status);";
        $result = $this->pdo->query($query);
        return true;
    }
    function edit_brend($id, $name, $status)
    {
        $query   = "UPDATE `pos_brand` SET `name` = '$name', `status` = $status WHERE `id` = $id";
        $result = $this->pdo->query($query);
        return true;
    }
    function del_brend($id)
    {
        $query   = "DELETE FROM `pos_brand` WHERE `id` = $id";
        $result = $this->pdo->query($query);
        return true;
    }

    function __destruct()
    {
    }
}
