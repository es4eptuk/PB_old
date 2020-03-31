<?php
class Checks
{
    private $telegram;
    private $robot;
    private $sklad;
    public $auth;
    private $mail;
    private $query;
    private $pdo;
    function __construct()
    {

        global $database_server, $database_user, $database_password, $dbase, $telegramAPI, $robots, $position, $mail;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);

        $this->telegram = $telegramAPI; //new TelegramAPI;
        $this->robot = $robots; //new Robots;
        $this->sklad = $position; //new Position;
        $this->mail = $mail; //new Mail;
        //$this -> robot = new Robots;
    }
    function get_checks_in_cat($category, $version = 4)
    {
        $query = "SELECT * FROM check_items WHERE category='$category' AND version = $version ORDER BY `sort` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $checks_array[] = $line;
        }
        if (isset($checks_array))
            return $checks_array;
    }
    function get_checks_group($category)
    {
        $query = "SELECT * FROM check_group WHERE parent='$category' ORDER BY `title` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $group_array[] = $line;
        }
        if (isset($group_array))
            return $group_array;
    }
    function add_check($category, $title, $sort, $version = 4, $kit)
    {
        $title   = trim($title);
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `check_items` (`id`, `name`, `title`, `category`, `sort` , `version`, `kit`) VALUES (NULL, '', '$title', '$category',  '$sort', $version, $kit)";
        $result = $this->pdo->query($query);
        $idd   = $this->pdo->lastInsertId();
        $query = "SELECT * FROM robots WHERE version = $version AND progress != 100 ORDER BY `sort` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
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
            $result = $this->pdo->query($query);
        }

        return $result;
    }
    function edit_check($id, $title, $kit, $version = 4)
    {
        $title   = trim($title);
        //$date    = date("Y-m-d H:i:s");
        //$user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `check_items` SET `title` = '$title ', `kit` = $kit, `version` = $version  WHERE `id` = $id";
        $result = $this->pdo->query($query);
        $query = "UPDATE `check` SET `operation` = '$title', `id_kit` = '$kit'  WHERE `id_check` = $id";
        $result = $this->pdo->query($query);
        return $result;
    }
    function edit_check_on_option($id, $title, $category, $kit)
    {
        $title   = trim($title);
        //$date    = date("Y-m-d H:i:s");
        //$user_id = intval($_COOKIE['id']);
        $query   = "UPDATE `robot_options_checks` SET `check_title` = '$title ',  `check_category` = $category,  `id_kit` = $kit  WHERE `check_id` = $id";
        $result = $this->pdo->query($query);
        $query = "UPDATE `check` SET `operation` = '$title',`id_kit` = '$kit' WHERE `category` = $category AND `operation` LIKE '$title' ";
        $result = $this->pdo->query($query);
        return $result;
    }
    function get_checks_on_robot($category, $robot)
    {
        $query = "SELECT * FROM `check` WHERE `category` = $category AND `robot` = $robot AND `option` = 0 ORDER BY `sort` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $checks_array[] = $line;
        }
        if (isset($checks_array))
            return $checks_array;
    }
    function get_checks_on_robot_option($category, $robot)
    {
        $query = "SELECT robot_options.title, check.id, check.id_check, check.check, check.operation, check.comment, check.id_kit, check.update_user, check.update_date  FROM `check` JOIN `robot_options` ON check.option = robot_options.id_option WHERE check.category = $category AND check.robot = $robot AND check.option != 0 ORDER BY check.sort ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $checks_array[] = $line;
        }
        if (isset($checks_array))
            return $checks_array;
    }

    function add_check_on_robot($id_row, $robot, $id, $value, $number, $remont, $kit)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        if ($id == 131 && $value == 0) {
            return false;
        }
        $query = "UPDATE `check` SET `check` = '$value', `update_user` = '$user_id', `update_date` = '$date' WHERE `id` = $id_row";
        $result = $this->pdo->query($query);
        $query = "SELECT * FROM `check` WHERE `id` = $id_row";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $check_items_array[] = $line;
        }
        $title = $check_items_array['0']['operation'];
        $stage = $check_items_array['0']['category'];
        if ($id == 54 && $value == 1) {
            $query = "SELECT * FROM robots WHERE id='$robot'";
            $result = $this->pdo->query($query);
            while ($line = $result->fetch()) {
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
            $result = $this->pdo->query($query);
            while ($line = $result->fetch()) {
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
        if (($id == 104 || $id ==308) && $value == 1) {
            $query = "SELECT * FROM robots WHERE id='$robot'";
            $result = $this->pdo->query($query);
            while ($line = $result->fetch()) {
                $robot_array[] = $line;
            }
            $robot_name   = $robot_array['0']['name'];
            $icon         = '📦';
            $comment      = " Робот  #" . $number . "(" . $robot_name . ") упакован и готов к отправке";
            $telegram_str = $icon . $comment;
            $this->telegram->sendNotify("sale", $telegram_str);
            $this->mail->send('Екатерина Старцева',  'startceva@promo-bot.ru', 'Списание на робота '.$number . '(' . $robot_name . ')', 'Пройдите по ссылке для просмотра списания https://db.promo-bot.ru/new/edit_writeoff_on_robot.php?id='.$robot);

        }
        $query = "SELECT * FROM robots WHERE id='$robot'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $robot_array[] = $line;
        }
        $robot_name    = $robot_array['0']['name'];
        $robot_version = $robot_array['0']['version'];
        if ($id == 131 && $value == 1) {
            if ($remont == 0) {
                $query = "SELECT * FROM robots WHERE id='$robot'";
                $result = $this->pdo->query($query);
                while ($line = $result->fetch()) {
                    $robot_array[] = $line;
                }
                $query = "UPDATE `robots` SET `writeoff` = '1' WHERE `id` = $robot";
                $result = $this->pdo->query($query);
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
        $query = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $robot, 'PRODUCTION', '$level', '$comment', $user_id, '$date')";
        //echo $query;
        $result = $this->pdo->query($query);
        $query = "SELECT * FROM `check` WHERE `robot` = $robot";
        $result = $this->pdo->query($query);
        $count_check  = 0;
        $finish_check = 0;
        while ($line = $result->fetch()) {
            $check_array[] = $line;
            $count_check   = $count_check + 1;
            if ($line['check'] == 1) {
                $finish_check = $finish_check + 1;
            }
        }
        $progress = $finish_check * 100 / $count_check;
        $query    = "UPDATE `robots` SET `progress` = '$progress', `stage` = '$stage', `last_operation` = '$title', `update_user` = '$user_id', `update_date` = '$date' WHERE `robots`.`id` = $robot";
        $result = $this->pdo->query($query);
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
        $result = $this->pdo->query($query);
        $query = "SELECT * FROM `check` WHERE `id` = $id_row";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
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
        $query = "INSERT INTO `robot_log` (`id`, `robot_id`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $robot, '$level', '$comment', $user_id, '$date')";
        //echo $query;
        $result = $this->pdo->query($query);
        $query = "SELECT * FROM `check` WHERE `robot` = $robot";
        $result = $this->pdo->query($query);
        $count_check  = 0;
        $finish_check = 0;
        while ($line = $result->fetch()) {
            $check_array[] = $line;
            $count_check   = $count_check + 1;
            if ($line['check'] == 1) {
                $finish_check = $finish_check + 1;
            }
        }
        $progress = $finish_check * 100 / $count_check;
        $query    = "UPDATE `robots` SET `progress` = '$progress', `stage` = '$stage', `last_operation` = '$title', `update_user` = '$user_id', `update_date` = '$date' WHERE `robots`.`id` = $robot";
        $result = $this->pdo->query($query);
    }
    function get_progress($robot, $category)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "SELECT * FROM `check` WHERE `robot` = $robot AND `category` = $category";
        $result = $this->pdo->query($query);
        $count_check  = 0;
        $finish_check = 0;
        while ($line = $result->fetch()) {
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
            $result = $this->pdo->query($query);
        }

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
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $check_array[] = $line;
        }

        if (isset($check_array))
            return $check_array['0'];
    }
    function get_checks_on_option($id)
    {
        $query = "SELECT * FROM `robot_options_checks` JOIN `pos_category` ON robot_options_checks.check_category = pos_category.id WHERE `id_option` = $id ORDER BY robot_options_checks.check_category ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $checks_array[] = $line;
        }

        if (isset($checks_array))
            return $checks_array;
    }
    function add_check_on_option($id_option, $title, $category, $version = 4, $kit)
    {
        $title = trim($title);
        $query = "INSERT INTO `robot_options_checks` (`check_id`, `id_option`, `check_title`, `check_category`, `id_kit`) VALUES (NULL, $id_option, '$title', $category, $kit);";
        $result = $this->pdo->query($query);
        $idd   = $this->pdo->lastInsertId();
        $query = "SELECT robot_options_items.id_row, robot_options_items.id_option, robot_options_items.id_robot FROM `robot_options_items` JOIN `robots` ON robot_options_items.id_robot = robots.id WHERE robot_options_items.id_option = $id_option AND robots.version = $version AND robots.progress != 100";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
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
                $result = $this->pdo->query($query);
            }
        }
        return $result;
    }
    function del_check($id, $version)
    {
        $query = "DELETE FROM `check_items` WHERE `id` = $id";
        $result = $this->pdo->query($query);
        $query = "SELECT * FROM robots WHERE version = $version AND progress != 100 ORDER BY `sort` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $robots_array[] = $line;
        }
        foreach ($robots_array as &$value) {
            $id_robot = $value['id'];
            $query    = "DELETE FROM `check` WHERE id_check = $id AND robot = $id_robot";
            $result = $this->pdo->query($query);
        }
        return $result;
    }
    
    
     function add_option_check ($id, $robot, $value) {
       $date    = date("Y-m-d H:i:s");
       $user_id = intval($_COOKIE['id']);
       $query = "SELECT robot_options_checks.check_id, robot_options_checks.id_option, robot_options_checks.check_title, robot_options_checks.check_category, robot_options_checks.id_kit, robot_options.id_option, robot_options.version, robot_options.title    FROM `robot_options_checks` JOIN robot_options ON robot_options_checks.id_option = robot_options.id_option WHERE robot_options_checks.id_option = $id";
       $result = $this->pdo->query($query);
         while ($line = $result->fetch()) {
            print_r($line);
            $operation = $line['check_title'];
            $kit = $line['id_kit'];
            $category = $line['check_category'];
            $option = $line['id_option'];
            $title = $line['title'];
            
            if ($value==1) {
            $query2 = "INSERT INTO `check` (`id_check`, `robot`, `operation`, `category`, `group`, `check`, `comment`, `sort`, `option`, `id_kit`) VALUES ('0', $robot, '$operation', $category, '0', '0', '', '0', $option, $kit)";    
            $result2 = $this->pdo->query($query2);
            //echo $query2;
            if ($result2) {
                $comment = "Добавлена опция ".$title;
                 $query_log = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $robot, 'PRODUCTION', 'INFO', '$comment', $user_id, '$date ')";
                //echo $query;
                 $result_log = $this->pdo->query($query_log);
                
            }
                
            } else {
              $query3 = "SELECT COUNT(0) AS ROW_COUNT  FROM `check` WHERE operation = '$operation' AND robot = $robot AND category = $category AND `check`=0" ;
              $result3 = $this->pdo->query($query3);
              $rows = $result3->fetchAll(PDO::FETCH_ASSOC);
              $num_rows = count($rows);

              if ($num_rows>0) {
                  $query2 = "DELETE FROM `check` WHERE operation = '$operation' AND robot = $robot AND category = $category"; 
                  $result2 = $this->pdo->query($query2);
                  if ($result2) {
                 $comment = "Удалена опция ".$title;
                 $query_log = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $robot, 'PRODUCTION', 'INFO', '$comment', $user_id, '$date')";
                //echo $query;
                 $result_log = $this->pdo->query($query_log);
                
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
