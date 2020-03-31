<?php
class Robots

{
    private $telegram;
    private $sklad;
    private $query;
    private $pdo;

    function __construct()
    {

        global $database_server, $database_user, $database_password, $dbase, $telegramAPI, $position;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);

        $this->telegram = $telegramAPI; //new TelegramAPI;
        $this->sklad = $position; //new Position;


    }

    function get_robots()
{
    $this->query  = "SELECT * FROM robots WHERE `delete` != 1  ORDER BY `number` ASC";
    $result = $this->pdo->query($this->query);
    while ($line = $result->fetch())
    {
        $robots_array[] = $line;
    }

    if (isset($robots_array)) return $robots_array;
}

    function del_robot($id)
    {
        $this->query  = "UPDATE `robots` SET `delete` = '1' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);

        $this->query  = "SELECT version FROM `pos_items` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $robots_array[] = $line;
        }

        $this->sklad->unset_reserv($robots_array[0]['version']);
        return $result;
    }

    function get_customers()
    {
        $this->query  = "SELECT * FROM customers  ORDER BY `name` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $customer_array[] = $line;
        }

        if (isset($customer_array)) return $customer_array;
    }

    function get_log($id_robot)
    {
        if ($id_robot == 0)
        {
            $this->query  = "SELECT * FROM robot_log ORDER BY `update_date` DESC LIMIT 200";
        }
        else
        {
            $this->query  = "SELECT * FROM robot_log WHERE robot_id='$id_robot' ORDER BY `update_date` DESC";
        }

        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $log_array[] = $line;
        }

        if (isset($log_array)) return $log_array;
    }

    function add_log($id_robot, $level, $comment, $number)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query  = "INSERT INTO `robot_log` (`id`, `robot_id`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $id_robot, '$level', '$comment', $user_id, '$date')";
        $result = $this->pdo->query($this->query);

        $icon = '⚠️';
        $telegram_str = $icon . " #" . $number . " " . $comment;
        $botToken = "693003240:AAHXZrBJTj77IJEkijGjc3cktCTNMiOwo4o";
        $website = "https://api.telegram.org/bot" . $botToken;
        $chatId = 123622748; //Receiver Chat Id
        $params = ['chat_id' => $chatId, 'text' => $telegram_str, ];
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

    }

    function add_log_width_zabbix($host, $time, $type, $problem, $total_uptime, $type_message = 0, $id = 0, $status = "0", $client = 0)
    {
        $date = date("Y-m-d H:i:s");

        // $user_id = intval($_COOKIE['id']);

        $number_str = substr(strstr($host, '_') , 1, strlen($host));
        $number = $number_str;


        $number_0 = ltrim($number,'0');

        $this->query  = "SELECT * FROM robots WHERE (number='$number' OR number='$number_0')  AND version = 4";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $robot_array[] = $line;
        }

        if (isset($robot_array))
        {
            $robot_id = $robot_array[0]['id'];
            $t_chat_id = $robot_array[0]['telegram'];
            // echo $query;

            $this->query  = "SELECT * FROM `robot_zabbix` WHERE `id_event` = $id";
            $result = $this->pdo->query($this->query);

            while ($line = $result->fetch())
            {
                $event_array[] = $line;
            }

            if (isset($event_array))
            {
                $this->query  = "UPDATE `robot_zabbix` SET `status` = '$status' WHERE `id_event` = $id";
                $result = $this->pdo->query($this->query);
            }else {
                $this->query  = "INSERT INTO `robot_zabbix` (`id`, `id_robot`,`number_robot`,`host`, `time`, `problem`,`total_uptime`, `update_date`,`id_event`, `status`) VALUES (NULL, $robot_id, $number, '$host', '$time', '$problem','$total_uptime', '$date', '$id', '$status')";
                $result = $this->pdo->query($this->query);
            }

            $this->query  = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $robot_id, 'ZABBIX','WARNING', '$problem', 33, '$date')";
            $result = $this->pdo->query($this->query);
        }
        else
        {
            $this->query  = "INSERT INTO `robot_zabbix` (`id`, `id_robot`,`number_robot`,`host`, `time`, `problem`,`total_uptime`, `update_date`,`id_event`, `status`) VALUES (NULL, 0, $number, '$host', '$time', '$problem', '$total_uptime', '$date', '$id', '$status')";
            $result = $this->pdo->query($this->query);
        }

        switch ($type_message)
        {
            case "Disaster":
                if ($status=="OK") {$icon = '✅';} else {$icon = '🆘';}

                $telegram_str = $icon . " #" . $number . " " . $problem;
                break;

            case "High":
                $icon = '🆘';
                $telegram_str = $icon . " #" . $number . " " . $problem;
                break;

            case "Average":
                $icon = '🆘';
                $telegram_str = $icon . " #" . $number . " " . $problem;
                break;

            case "Warning":
                $icon = '⚠️';
                $telegram_str = $icon . " #" . $number . " " . $problem;
                break;

            case "Information":
                $icon = 'ℹ️';
                $telegram_str = $icon . " #" . $number . " " . $problem;
                break;

            default:
                $icon = $type_message;
                $telegram_str = $icon . " #" . $number . " " . $problem;
        }


        if ($type == 0 && $client!=1)
        {

            $this->query = "SELECT * FROM `tickets` WHERE `description` LIKE '%$problem%' AND `robot` = $robot_id AND ( `status` != 6 OR `status` != 3) AND `date_create` >= date_sub(now(), INTERVAL 1 HOUR)";
            $result = $this->pdo->query($this->query);
            $line = $result->fetch();
            if ($line==null)
            {
                $cat_zabbix = stristr($problem, '.', true);
                $this->query = "SELECT id FROM `tickets_category` WHERE `zabbix` LIKE '$cat_zabbix'";
                $result = $this->pdo->query($this->query);
                $line = $result->fetch();
                $cat_id = $line['id'];
                $robot = $robot_id;
                $date = date("Y-m-d H:i:s");
                $user_id = 33;

                if ($status!="OK" and preg_match("(2048|640|136|138)", "$problem") != true ) {
                    $this->query = "INSERT INTO `tickets` (
                                                `id`, 
                                                `robot`, 
                                                `class`,
                                                `category`, 
                                                `subcategory`, 
                                                `description`, 
                                                `status`, 
                                                `user_create`,
                                                `date_create`,
                                                `update_user`, 
                                                `update_date`) 
                                                VALUES (
                                                    NULL, 
                                                    '$robot_id', 
                                                    'P', 
                                                    '$cat_id', 
                                                    '0', 
                                                    '$problem', 
                                                    '1',
                                                    '$user_id',
                                                    '$date',
                                                    '$user_id', 
                                                    '$date')";
                    $result = $this->pdo->query($this->query);
                }
                if ($client!=1 and preg_match("(2048|640|136|138)", "$problem") != true) {$this->telegram->sendNotify("tehpod", $telegram_str." - ".$status);}

            } else {
                if ($status=="OK" and preg_match("(2048|640|136|138)", "$problem") != true) {
                    if ($client!=1) {$this->telegram->sendNotify("tehpod", $telegram_str." - ".$status);}
                }
            }
        }
        else
        {

            if ($client==1) {
                $this->telegram->sendNotify("client", $telegram_str,$t_chat_id);
            } else {
                $this->telegram->sendNotify("test", $telegram_str);
            }


        }



    }

    function delete_log($id)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "DELETE FROM `robot_log` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);

    }

    public

    function get_info_robot($id)
    {
        $this->query = "SELECT * FROM robots WHERE id='$id'";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $robot_array[] = $line;
        }
        if (isset($robot_array)) return $robot_array['0'];
    }

    function add_robot($number, $name, $version, $photo, $termo, $dispenser, $terminal, $kaznachey, $lidar, $other, $customer, $language_robot, $language_doc, $charger, $color, $brand, $ikp, $battery, $dop,$dop_manufactur, $send,$date_start,$date_test)
    {
        $date_start = new DateTime($date_start);
        $date_start = $date_start->format('Y-m-d H:i:s');

        $date_test = new DateTime($date_test);
        $date_test = $date_test->format('Y-m-d H:i:s');

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $number = str_pad($number, 4, "0", STR_PAD_LEFT);
        if ($send == 1)
        {
            $progress = 100;
        }
        else
        {
            $progress = 0;
        }

        $this->query = "INSERT INTO `robots` (
        `id`, 
        `version`,
        `number`,
        `photo`, 
        `termo`,
        `dispenser`,
        `terminal`,
        `kaznachey`,
        `lidar`,
        `other`, 
        `name`,
        `customer`,
        `language_robot`,
        `language_doc`,
        `charger`,
        `color`,
        `brand`,
        `ikp`, 
        `battery`,
        `dop`, 
        `dop_manufactur`,
        `progress`,
        `date`,
        `date_test`,
        `update_date`,
        `update_user` ) VALUES (
            NULL, 
            '$version', 
            '$number',
            '$photo', 
            '$termo',
            '$dispenser',
            '$terminal',
            '$kaznachey',
            '$lidar',
            '$other',
            '$name',
            '$customer',
            '$language_robot',
            '$language_doc',
            '$charger',
            '$color',
            '$brand',
            '$ikp',
            '$battery',
            '$dop',
            '$dop_manufactur',
            '$progress',
            '$date_start',
            '$date_test',
            '$date', 
            '$user_id')";
        $result = $this->pdo->query($this->query);
        $idd =  $this->pdo->lastInsertId();

        $arr_mh = Array();
        $arr_hp = Array();
        $arr_bd = Array();
        $arr_up = Array();
        $arr_hs = Array();

        $this->query = "SELECT * FROM check_items WHERE category='1' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $arr_mh[] = $line;
        }

        $this->query = "SELECT * FROM check_items WHERE category='2' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $arr_hp[] = $line;
        }

        $this->query = "SELECT * FROM check_items WHERE category='3' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $arr_bd[] = $line;
        }

        $this->query = "SELECT * FROM check_items WHERE category='4' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $arr_up[] = $line;
        }

        $this->query = "SELECT * FROM check_items WHERE category='5' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $arr_hs[] = $line;
        }

        $result_arr = array_merge($arr_mh, $arr_hp, $arr_bd, $arr_up, $arr_hs);

        // print_r($result_arr);

        foreach($result_arr as & $value)
        {
            $operation = $value['title'];
            $category = $value['category'];
            $group = $value['group'];
            $sort = $value['sort'];
            $id_check = $value['id'];
            $id_kit = $value['kit'];
            $this->query = "INSERT INTO `check` (
                     `id`, 
                     `id_check`, 
                     `robot`, 
                     `operation`,
                     `category`,
                     `group`, 
                     `check`,
                     `sort`,
                     `id_kit`,
                     `update_date`, 
                     `update_user` ) VALUES (
                         NULL, 
                         '$id_check',
                         '$idd', 
                         '$operation', 
                         '$category',
                         '$group',
                         '0',
                         '$sort',
                         '$id_kit',
                         '', 
                         '')";
            $result = $this->pdo->query($this->query);
        }

        $this->sklad->set_reserv($version);
    }

    function edit_robot($id, $number, $name, $version, $options, $customer, $language_robot, $language_doc, $charger, $color, $brand, $ikp, $battery, $dop,$dop_manufactur, $date_start,$date_test, $send)
    {
//	print_r($options);
        $robot_info= $this->get_info_robot($id);

        $date_start = new DateTime($date_start);
        $date_start = $date_start->format('Y-m-d H:i:s');

        $date_test = new DateTime($date_test);
        $date_test = $date_test->format('Y-m-d H:i:s');

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $number = str_pad($number, 4, "0", STR_PAD_LEFT);
        if ($send == 1)
        {
            $progress = 100;
        }
        else
        {
            $progress = 0;
        }

        $this->query = "UPDATE `robots` SET 
        `version` = '$version', 
        `number` = $number, 
        `name` = '$name', 
        `customer` = '$customer', 
        `language_robot` = '$language_robot', 
        `language_doc` = '$language_doc', 
        `charger` = '$charger', 
        `color` = '$color', 
        `brand` = '$brand', 
        `ikp` = '$ikp', 
        `battery` = '$battery', 
        `dop` = '$dop', 
        `dop_manufactur` = '$dop_manufactur', 
        `date` = '$date_start',
        `date_test` = '$date_test',
        `update_user` = '$user_id', 
        `update_date` = '$date' 
        
        WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        $idd = $this->pdo->lastInsertId();

        $this->query = "DELETE FROM `robot_options_items` WHERE `id_robot` = $id";
        $result = $this->pdo->query($this->query);

        if ($result) {
            foreach ($options as &$value) {
                $this->add_options_on_robot($value,$id);
            }

        }


        $old_name = $robot_info['name'];
        if($name!=$old_name) {
            $comment      = "У робота $version.$number изменен заказчик на - $name";
            $telegram_str = $comment;
            $this->telegram->sendNotify("sale", $telegram_str);
        }

        $old_date = $robot_info['name'];
        if($name!=$old_name) {
            $comment      = "У робота $version.$number изменен заказчик на - $name";
            $telegram_str = $comment;
            $this->telegram->sendNotify("sale", $telegram_str);
        }


    }

    function sortable($json)
    {
        foreach($json as $key => $value)
        {
            $this->query = "UPDATE `robots` SET `sort` = '$key' WHERE `robots`.`id` = $value";
            $result = $this->pdo->query($this->query);
        }

        if (isset($robots_array)) return $robots_array;
    }

    function add_customer($name, $fio, $phone, $email, $address)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "INSERT INTO `customers` (`id`, `name`,  `fio`,  `phone`,  `email`,  `address` ) VALUES (NULL,  '$name' ,  '$fio',  '$phone',  '$email',  '$address')";
        $result = $this->pdo->query($this->query);
        $idd = $this->pdo->lastInsertId();
        return $idd;
    }

    function onRemont($robot)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "UPDATE `robots` SET `remont` = remont+1, `progress` = 0, `last_operation` = '', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $robot";
        $result = $this->pdo->query($this->query);
        $this->query = "UPDATE `check` SET `check` = '0' WHERE `robot` = $robot";
        $result = $this->pdo->query($this->query);
        $this->query = "INSERT INTO `robot_log` (`id`, `robot_id`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $robot, 'MODERN', 'Робот прибыл на ремонт', $user_id, '$date')";
        $result = $this->pdo->query($this->query);
        return $result;
    }

    function countRemont($robot)
    {
        $this->query = "SELECT remont FROM `robots` WHERE id=$robot";
        $result = $this->pdo->query($this->query);
        $line = $result->fetch();
        return $line['remont'];
    }

    function get_options()
    {
        $this->query = "SELECT * FROM robot_options ORDER BY `title` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $option_array[] = $line;
        }
        if (isset($option_array))
            return $option_array;
    }

    function add_options_on_robot($option,$robot)
    {
        $this->query = "INSERT INTO `robot_options_items` (`id_row`, `id_option`, `id_robot`) VALUES (NULL, $option, $robot)";
        $result = $this->pdo->query($this->query);
        return $result;
    }

    function get_robot_options($robot = 0)
    {
        $this->query = "SELECT * FROM `robot_options`";
        $result = $this->pdo->query($this->query);
        $n = 0;
        while ($line = $result->fetch())
        {
            $options_array[$n]['id'] = $line['id_option'];
            $options_array[$n]['title'] = $line['title'];
            $id = $line['id_option'];
            $query_t = "SELECT COUNT(*) FROM `robot_options_items` WHERE `id_robot` = $robot AND id_option = $id";
            $result_t = $this->pdo->query($query_t);
            $line_t = $result_t->fetch();
            if ($line_t['COUNT(*)'] != 0) {
                $options_array[$n]['check']   = 1;
            } else { $options_array[$n]['check']   = 0; }
            $n++;

        }

        if (isset($options_array)) return $options_array;
    }

    function get_info_option($id)
    {
        $this->query = "SELECT * FROM robot_options WHERE id_option='$id'";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $option_array[] = $line;
        }
        if (isset($option_array)) return $option_array['0'];
    }

    function edit_option ($id,$title) {

        $this->query = "UPDATE `robot_options` SET `title` = '$title' WHERE `id_option` = $id";
        $result = $this->pdo->query($this->query);
        return $result;
    }

    function telegram_get_id ($chat) {

        $this->query = "SELECT * FROM `robots`  WHERE telegram LIKE '$chat'";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch())
        {
            $robots_array[] = $line;
        }
        if (isset($robots_array)) return $robots_array;
    }


    function get_db_info ($robot) {
        $ch = curl_init('https://pb2.icmm.ru/robotm/viewm1?robot='.$robot);


        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 0);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result,true);
    }



    function __destruct()
    {

        // echo "robots - ";
        // print_r($this ->link_robots);
        // echo "<br />";
        // mysql_close($this ->link_robots);

    }
}