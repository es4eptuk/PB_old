<?php
class Checks
{
    private $telegram;
    private $robot;
    private $sklad;
    private $link_check;
    public $auth;
    private $mail;
    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $this->link_check = mysql_connect($database_server, $database_user, $database_password) or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8', $this->link_check);
        //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
        $this->telegram = new TelegramAPI;
        $this->robot    = new Robots;
        $this->sklad    = new Position;
        $this -> mail = new Mail;
        //$this -> robot = new Robots;
    }
    function get_checks_in_cat($category, $version = 4)
    {
        $query = "SELECT * FROM check_items WHERE category='$category' AND version = $version ORDER BY `sort` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $checks_array[] = $line;
        }
        // Освобождаем память от результата
        // mysql_free_result($result);
        if (isset($checks_array))
            return $checks_array;
    }
    function get_checks_group($category)
    {
        $query = "SELECT * FROM check_group WHERE parent='$category' ORDER BY `title` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $group_array[] = $line;
            //print_r($line) ;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($group_array))
            return $group_array;
    }
    function add_check($category, $title, $sort, $version = 4, $kit)
    {
        $title   = trim($title);
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `check_items` (`id`, `name`, `title`, `category`, `sort` , `version`, `kit`) VALUES (NULL, '', '$title', '$category',  '$sort', $version, $kit)";
        $result = mysql_query($query) or die('false');
        $idd   = mysql_insert_id();
        $query = "SELECT * FROM robots WHERE version = $version AND progress != 100 ORDER BY `sort` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $robots_array[] = $line;
        }
        foreach ($robots_array as &$value) {
            $id_robot = $value['id'];
            $query    = "INSERT INTO `check` (
                     `id`, 
                     `id_check`, 
                     `robot`, 
                     `operation`,
                     `category`,
                     `check`,
                     `sort`,
                     `id_kit`,
                     `update_date`, 
                     `update_user` ) VALUES (
                         NULL, 
                         '$idd',
                         '$id_robot', 
                         '$title', 
                         '$category',
                         
                         '0',
                         '$sort',
                         '$kit',
                         '', 
                         '')";
            $result = mysql_query($query) or die('false');
        }
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    function edit_check($id, $title, $kit, $version = 4)
    {
        $title   = trim($title);
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `check_items` SET `title` = '$title ', `kit` = $kit, `version` = $version  WHERE `id` = $id";
        $result = mysql_query($query) or die('false');
        $query = "UPDATE `check` SET `operation` = '$title', `id_kit` = '$kit'  WHERE `id_check` = $id";
        $result = mysql_query($query) or die('false');
        return $result;
    }
    function edit_check_on_option($id, $title, $category, $kit)
    {
        $title   = trim($title);
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `robot_options_checks` SET `check_title` = '$title ',  `check_category` = $category,  `id_kit` = $kit  WHERE `check_id` = $id";
        $result = mysql_query($query) or die('false');
        $query = "UPDATE `check` SET `operation` = '$title',`id_kit` = '$kit' WHERE `category` = $category AND `operation` LIKE '$title' ";
        // echo $query;
        $result = mysql_query($query) or die('false');
        return $result;
    }
    function get_checks_on_robot($category, $robot)
    {
        $query = "SELECT * FROM `check` WHERE `category` = $category AND `robot` = $robot AND `option` = 0 ORDER BY `sort` ASC";
        //echo $query;
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $checks_array[] = $line;
        }
        if (isset($checks_array))
            return $checks_array;
    }
    function get_checks_on_robot_option($category, $robot)
    {
        $query = "SELECT robot_options.title, check.id, check.id_check, check.check, check.operation, check.comment, check.id_kit, check.update_user, check.update_date  FROM `check` JOIN `robot_options` ON check.option = robot_options.id_option WHERE check.category = $category AND check.robot = $robot AND check.option != 0 ORDER BY check.sort ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $checks_array[] = $line;
        }
        if (isset($checks_array))
            return $checks_array;
    }

    function add_check_on_robot($id_row, $robot, $id, $value, $number, $remont, $kit)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        //echo $value;
        if ($id == 131 && $value == 0) {
            return false;
        }
        $query = "UPDATE `check` SET `check` = '$value', `update_user` = '$user_id', `update_date` = '$date' WHERE `id` = $id_row";
        //echo $query;
        $result = mysql_query($query) or die(mysql_error());
        $query = "SELECT * FROM `check` WHERE `id` = $id_row";
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $check_items_array[] = $line;
        }
        $title = $check_items_array['0']['operation'];
        $stage = $check_items_array['0']['category'];
        //$robot_info = $this->robot->get_info_robot($robot);
        //echo $robot_info['name'];
        if ($id == 54 && $value == 1) {
            $query = "SELECT * FROM robots WHERE id='$robot'";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $robot_array[] = $line;
            }
            $robot_name   = $robot_array['0']['name'];
            // echo $robot_name;
            $icon         = '🛠';
            $comment      = " Робот  #" . $number . "(" . $robot_name . ") готовится к отправке";
            $telegram_str = $icon . $comment;
            $this->telegram->sendNotify("sale", $telegram_str);

        }
        if (($id == 105 || $id == 314) && $value == 1) {
            $query = "SELECT * FROM robots WHERE id='$robot'";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $robot_array[] = $line;
            }
            $robot_name = $robot_array[0]['name'];
            $this->auth = $this->z_auth();
            $num        = str_pad($number, 4, "0", STR_PAD_LEFT);
            $z_host     = $this->z_get_hosts(array(
                'host' => 'promobotv4_' . $num
            ));
            $this->z_remove_group($z_host[0]['hostid'], '31');
            $this->z_remove_group($z_host[0]['hostid'], '32');
            $icon         = '🚚';
            $comment      = " Робот  #" . $number . "(" . $robot_name . ") отправлен";
            $telegram_str = $icon . $comment;
            $this->telegram->sendNotify("sale", $telegram_str);
        }
        if ($id == 104 && $value == 1) {
            $query = "SELECT * FROM robots WHERE id='$robot'";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $robot_array[] = $line;
            }
            $robot_name   = $robot_array['0']['name'];
            $icon         = '📦';
            $comment      = " Робот  #" . $number . "(" . $robot_name . ") упакован и готов к отправке";
            $telegram_str = $icon . $comment;
            $this->telegram->sendNotify("sale", $telegram_str);
            $this->mail->send('Екатерина Старцева',  'cto@promo-bot.ru', 'Списание на робота '.$number . "(" . $robot_name . ")", 'Пройдите по ссылке для просмотра списания https://db.promo-bot.ru/new/edit_writeoff_on_robot.php?id='.$robot);

        }
        $query = "SELECT * FROM robots WHERE id='$robot'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $robot_array[] = $line;
        }
        $robot_name    = $robot_array['0']['name'];
        $robot_version = $robot_array['0']['version'];
        if ($id == 131 && $value == 1) {
            if ($remont == 0) {
                $query = "SELECT * FROM robots WHERE id='$robot'";
                $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
                while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    $robot_array[] = $line;
                }
                $query = "UPDATE `robots` SET `writeoff` = '1' WHERE `id` = $robot";
                $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
                // $this->sklad->set_writeoff($robot_version,$number);
                // $this->sklad->set_writeoff_options($robot_version,$number,0,$id,$robot);
            }
        }
        if ($value == 1) {
            $level        = "GOOD";
            $icon         = '✅';
            $comment      = "Выполнено - <b>" . $title . " </b>";
            $telegram_str = $icon . " #" . $number . " - Выполнено - " . $title;
        }
        if ($value == 0) {
            $level        = "WARNING";
            $icon         = '❌';
            $comment      = "Отменено - <b>" . $title . " </b>";
            $telegram_str = $icon . " #" . $number . " - Отменено - " . $title;
        }
        $this->telegram->sendNotify("manafacture", $telegram_str);
        $query = 'INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, ' . $robot . ', "PRODUCTION", "' . $level . '", "' . $comment . '", ' . $user_id . ', "' . $date . '")';
        //echo $query;
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $query = "SELECT * FROM `check` WHERE `robot` = $robot";
        $result = mysql_query($query) or die(mysql_error());
        $count_check  = 0;
        $finish_check = 0;
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $check_array[] = $line;
            $count_check   = $count_check + 1;
            if ($line['check'] == 1) {
                $finish_check = $finish_check + 1;
            }
        }
        $progress = $finish_check * 100 / $count_check;
        $query    = "UPDATE `robots` SET `progress` = '$progress', `stage` = '$stage', `last_operation` = '$title', `update_user` = '$user_id', `update_date` = '$date' WHERE `robots`.`id` = $robot";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        if ($result && $kit != 0 && $value == 1 && $remont==0) {
            $this->sklad->set_writeoff_kit($robot_version, $number, $kit, $id, $robot);
        }
        if ($result && $kit != 0 && $value == 0) {
            $this->sklad->unset_writeoff_kit($robot_version, $number, $kit, $id, $robot);
        }
    }
    function add_comment_on_check($id_row, $robot, $id, $value, $comment_check, $number)
    {
        $date         = date("Y-m-d H:i:s");
        $user_id      = intval($_COOKIE['id']);
        //echo $value;
        $query        = "UPDATE `check` SET `comment` = '$comment_check', `update_user` = '$user_id', `update_date` = '$date' WHERE `id` = $id_row ";
        //echo $query;
        $icon         = '⚠';
        $telegram_str = $icon . " #" . $number . " - Комментарий - " . $comment_check;
        $this->telegram->sendNotify("manafacture", $telegram_str);
        $result = mysql_query($query) or die(mysql_error());
        $query = "SELECT * FROM `check` WHERE `id` = $id_row";
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $check_items_array[] = $line;
        }
        $title = $check_items_array['0']['operation'];
        $stage = $check_items_array['0']['category'];
        if ($value == 1) {
            $level   = "INFO";
            $comment = "Обновлено - <b>" . $title . " </b>. Комментарий: <i>" . $comment_check . "</i>";
        }
        if ($value == 0) {
            $level   = "WARNING";
            $comment = "Не выполнено - <b>" . $title . " </b>. Комментарий: <i>" . $comment_check . "</i>";
        }
        $query = 'INSERT INTO `robot_log` (`id`, `robot_id`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, ' . $robot . ', "' . $level . '", "' . $comment . '", ' . $user_id . ', "' . $date . '")';
        //echo $query;
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $query = "SELECT * FROM `check` WHERE `robot` = $robot";
        $result = mysql_query($query) or die(mysql_error());
        $count_check  = 0;
        $finish_check = 0;
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $check_array[] = $line;
            $count_check   = $count_check + 1;
            if ($line['check'] == 1) {
                $finish_check = $finish_check + 1;
            }
        }
        $progress = $finish_check * 100 / $count_check;
        $query    = "UPDATE `robots` SET `progress` = '$progress', `stage` = '$stage', `last_operation` = '$title', `update_user` = '$user_id', `update_date` = '$date' WHERE `robots`.`id` = $robot";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    }
    function get_progress($robot, $category)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "SELECT * FROM `check` WHERE `robot` = $robot AND `category` = $category";
        $result = mysql_query($query) or die(mysql_error());
        $count_check  = 0;
        $finish_check = 0;
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $check_array[] = $line;
            $count_check   = $count_check + 1;
            if ($line['check'] == 1) {
                $finish_check = $finish_check + 1;
            }
        }
        $progress = round($finish_check * 100 / $count_check);
        return $progress;
    }
    function sortable($json)
    {
        foreach ($json as $key => $value) {
            $query = "UPDATE `check_items` SET `sort` = '$key' WHERE `id` = $value";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        }
        //var_dump(json_decode($json));
        // Освобождаем память от результата
        // mysql_free_result($result);
        if (isset($robots_array))
            return $robots_array;
    }
    function my_curl_zabbix($arr)
    {
        $url            = 'https://pb2.icmm.ru/zabbix/api_jsonrpc.php';
        $arr['jsonrpc'] = '2.0';
        $arr['id']      = '1';
        $arr['auth']    = $this->auth;
        // echo $arr['auth'];
        $postfields     = json_encode($arr);
        $curl           = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json-rpc'
        ));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        $return = curl_exec($curl);
        curl_close($curl);
        return json_decode($return, true);
    }
    function z_auth()
    {
        $jsonData = array(
            "jsonrpc" => "2.0",
            "method" => "user.login",
            "params" => array(
                "user" => "manufacture",
                "password" => "queetoh6Ace"
            ),
            "id" => "1",
            "auth" => "null"
        );
        $auth_arr = $this->my_curl_zabbix($jsonData);
        //print_r($auth_arr);
        return $auth_arr['result'];
    }
    function z_get_hosts($filter_arr)
    {
        $jsonData = array(
            "method" => "host.get",
            "params" => array(
                "output" => array(
                    "hostid",
                    "host",
                    "name"
                ),
                "selectInventory" => "name"
            )
        );
        if ($filter_arr) {
            $jsonData['params']['filter'] = $filter_arr;
        }
        $result = $this->my_curl_zabbix($jsonData);
        //print_r($result);
        return $result['result'];
    }
    function z_update_hosts($host_id, $action)
    {
        $jsonData = array(
            "method" => "host.update",
            "params" => array(
                "hostid" => "{$host_id}"
            )
        );
        switch ($action) {
            case 'disable':
                $jsonData['params']['status'] = 1;
                break;
            case 'enable':
                $jsonData['params']['status'] = 0;
                break;
            default:
                return false;
        }
        $result = $this->my_curl_zabbix($jsonData);
        //print_r($result);
        //echo 123;
        return $result;
    }
    function z_add_group($host_id, $group)
    {
        $jsonData                                  = array(
            "method" => "hostgroup.massadd"
        );
        $jsonData['params']['groups'][]['groupid'] = $group;
        $jsonData['params']['hosts'][]['hostid']   = $host_id;
        $result                                    = $this->my_curl_zabbix($jsonData);
        //print_r($result) ;
        return $result;
    }
    function z_remove_group($host_id, $group)
    {
        $jsonData                       = array(
            "method" => "hostgroup.massremove"
        );
        $jsonData['params']['groupids'] = $group;
        $jsonData['params']['hostids']  = $host_id;
        $result                         = $this->my_curl_zabbix($jsonData);
        //print_r($result) ;
        return $result;
    }
    function get_info_check($id)
    {
        $query = "SELECT * FROM check_items WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $check_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($check_array))
            return $check_array['0'];
    }
    function get_checks_on_option($id)
    {
        $query = "SELECT * FROM `robot_options_checks` JOIN `pos_category` ON robot_options_checks.check_category = pos_category.id WHERE `id_option` = $id ORDER BY robot_options_checks.check_category ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $checks_array[] = $line;
        }
        // Освобождаем память от результата
        // mysql_free_result($result);
        if (isset($checks_array))
            return $checks_array;
    }
    function add_check_on_option($id_option, $title, $category, $version = 4, $kit)
    {
        $title = trim($title);
        $query = "INSERT INTO `robot_options_checks` (`check_id`, `id_option`, `check_title`, `check_category`, `id_kit`) VALUES (NULL, $id_option, '$title', $category, $kit);";
        $result = mysql_query($query) or die('false');
        $idd   = mysql_insert_id();
        $query = "SELECT robot_options_items.id_row, robot_options_items.id_option, robot_options_items.id_robot FROM `robot_options_items` JOIN `robots` ON robot_options_items.id_robot = robots.id WHERE robot_options_items.id_option = $id_option AND robots.version = $version AND robots.progress != 100";
        //echo $query;
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $robots_array[] = $line;
        }
        if (isset($robots_array)) {
            foreach ($robots_array as &$value) {
                $id_robot = $value['id_robot'];
                echo $id_robot;
                $query = "INSERT INTO `check` (
                     `id`, 
                     `id_check`, 
                     `robot`, 
                     `operation`,
                     `category`,
                     `check`,
                     `sort`,
                     `option`,
                     `id_kit`,
                     `update_date`, 
                     `update_user` ) VALUES (
                         NULL, 
                         '0',
                         '$id_robot', 
                         '$title', 
                         '$category',
                         
                         '0',
                         '0',
                         '$id_option',
                         '$kit',
                         '', 
                         '')";
                $result = mysql_query($query) or die($query);
            }
        }
        return $result;
    }
    function del_check($id, $version)
    {
        $query = "DELETE FROM `check_items` WHERE `id` = $id";
        $result = mysql_query($query) or die(mysql_error());
        $query = "SELECT * FROM robots WHERE version = $version AND progress != 100 ORDER BY `sort` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $robots_array[] = $line;
        }
        foreach ($robots_array as &$value) {
            $id_robot = $value['id'];
            $query    = "DELETE FROM `check` WHERE id_check = $id AND robot = $id_robot";
            $result = mysql_query($query) or die(mysql_error());
        }
        return $result;
    }
    
    
     function add_option_check ($id, $robot, $value) {
          $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
       $query = "SELECT robot_options_checks.check_id, robot_options_checks.id_option, robot_options_checks.check_title, robot_options_checks.check_category, robot_options_checks.id_kit, robot_options.id_option, robot_options.version, robot_options.title    FROM `robot_options_checks` JOIN robot_options ON robot_options_checks.id_option = robot_options.id_option WHERE robot_options_checks.id_option = $id";
       echo $query;
       $result = mysql_query($query) or die($query);
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            print_r($line);
            $operation = $line['check_title'];
           // echo $line['id_kit'];
            $kit = $line['id_kit'];
            $category = $line['check_category'];
            $option = $line['id_option'];
            $title = $line['title'];
            
            if ($value==1) {
            $query2 = "INSERT INTO `check` (`id_check`, `robot`, `operation`, `category`, `group`, `check`, `comment`, `sort`, `option`, `id_kit`) VALUES ('0', $robot, '$operation', $category, '0', '0', '', '0', $option, $kit)";    
            $result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
            //echo $query2;
            if ($result2) {
                $comment = "Добавлена опция ".$title;
                 $query_log = 'INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, ' . $robot . ', "PRODUCTION", "INFO", "' . $comment . '", ' . $user_id . ', "' . $date . '")';
                //echo $query;
                 $result_log = mysql_query($query_log) or die('Запрос не удался: ' . mysql_error());
                
            }
                
            } else {
              $query3 = "SELECT *  FROM `check` WHERE operation = '$operation' AND robot = $robot AND category = $category AND `check`=0" ;
              $result3 = mysql_query($query3) or die('Запрос не удался: ' . mysql_error());
              $num_rows = mysql_num_rows($result);
              if ($num_rows>0) {
                  $query2 = "DELETE FROM `check` WHERE operation = '$operation' AND robot = $robot AND category = $category"; 
                  $result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
                  if ($result2) {
                 $comment = "Удалена опция ".$title;
                 $query_log = 'INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, ' . $robot . ', "PRODUCTION", "INFO", "' . $comment . '", ' . $user_id . ', "' . $date . '")';
                //echo $query;
                 $result_log = mysql_query($query_log) or die('Запрос не удался: ' . mysql_error());
                
            }
              }
                
            //   
            }
            //$result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
            
            
        } 
        
        
    }
    
    
    function __destruct()
    {
        //echo "check - ";
        //print_r($this ->link_check);
        //echo "<br>";
        //mysql_close($this ->link_check);
    }
}
$checks = new Checks;