<?php
class Robots

{
    private $telegram;
    private $sklad;
    private $link_robots;
    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $this->link_robots = mysql_connect($database_server, $database_user, $database_password) or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8', $this->link_robots);

        // echo 'Соединение успешно установлено';

        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
        $this->telegram = new TelegramAPI;
        $this->sklad = new Position;

        // $this -> robot = new Robots;

    }

    function get_robots()
    {
        $query = "SELECT * FROM robots WHERE `delete` != 1  ORDER BY `number` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $robots_array[] = $line;
        }
        //echo __METHOD__;
        // Освобождаем память от результата
        // mysql_free_result($result);

        if (isset($robots_array)) return $robots_array;
    }

    function del_robot($id)
    {
        $query = "UPDATE `robots` SET `delete` = '1' WHERE `id` = $id";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $query = "SELECT version FROM `pos_items` WHERE `id` = $id";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $robots_array[] = $line;
        }

        $this->sklad->unset_reserv($robots_array[0]['version']);
        return $result;
    }

    function get_customers()
    {
        $query = "SELECT * FROM customers  ORDER BY `name` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $customer_array[] = $line;
        }

        // Освобождаем память от результата
        // mysql_free_result($result);

        if (isset($customer_array)) return $customer_array;
    }

    function get_log($id_robot)
    {
        if ($id_robot == 0)
        {
            $query = "SELECT * FROM robot_log ORDER BY `update_date` DESC LIMIT 200";
        }
        else
        {
            $query = "SELECT * FROM robot_log WHERE robot_id='$id_robot' ORDER BY `update_date` DESC";
        }

        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $log_array[] = $line;
        }

        // Освобождаем память от результата
        // mysql_free_result($result);

        if (isset($log_array)) return $log_array;
    }

    function add_log($id_robot, $level, $comment, $number)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "INSERT INTO `robot_log` (`id`, `robot_id`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $id_robot, '$level', '$comment', $user_id, '$date')";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        //echo $query;
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

        // Освобождаем память от результата
        // mysql_free_result($result);

    }

    function add_log_width_zabbix($host, $time, $type, $problem, $total_uptime, $type_message = 0, $id = 0, $status = "0", $client = 0)
    {
        $date = date("Y-m-d H:i:s");

        // $user_id = intval($_COOKIE['id']);

        $number_str = substr(strstr($host, '_') , 1, strlen($host));
        $number = $number_str;


        $number_0 = ltrim($number,'0');

        $query = "SELECT * FROM robots WHERE (number='$number' OR number='$number_0')  AND version = 4";
        //	echo $query;
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

        // echo $query;

        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $robot_array[] = $line;
        }

        if (isset($robot_array))
        {
            $robot_id = $robot_array[0]['id'];
            $t_chat_id = $robot_array[0]['telegram'];
            // echo $query;

            $query = "SELECT * FROM `robot_zabbix` WHERE `id_event` = $id";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

            while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                $event_array[] = $line;
            }

            if (isset($event_array))
            {
                $query = "UPDATE `robot_zabbix` SET `status` = '$status' WHERE `id_event` = $id";
                //echo $query;
                $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            }else {
                $query = "INSERT INTO `robot_zabbix` (`id`, `id_robot`,`number_robot`,`host`, `time`, `problem`,`total_uptime`, `update_date`,`id_event`, `status`) VALUES (NULL, $robot_id, $number, '$host', '$time', '$problem','$total_uptime', '$date', '$id', '$status')";
                $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            }




            $query = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $robot_id, 'ZABBIX','WARNING', '$problem', 33, '$date')";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        }
        else
        {
            $query = "INSERT INTO `robot_zabbix` (`id`, `id_robot`,`number_robot`,`host`, `time`, `problem`,`total_uptime`, `update_date`,`id_event`, `status`) VALUES (NULL, 0, $number, '$host', '$time', '$problem', '$total_uptime', '$date', '$id', '$status')";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
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

            $query = "SELECT * FROM `tickets` WHERE `description` LIKE '%$problem%' AND `robot` = $robot_id AND ( `status` != 6 OR `status` != 3) AND `date_create` >= date_sub(now(), INTERVAL 1 HOUR)";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            $line = mysql_fetch_array($result, MYSQL_ASSOC);
            //print_r($line);
            if ($line==null)
            {
                $cat_zabbix = stristr($problem, '.', true);
                $query = "SELECT id FROM `tickets_category` WHERE `zabbix` LIKE '$cat_zabbix'";
                $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
                $line = mysql_fetch_array($result, MYSQL_ASSOC);
                $cat_id = $line['id'];
                $robot = $robot_id;
                $date = date("Y-m-d H:i:s");
                $user_id = 33;

                if ($status!="OK" and preg_match("(2048|640|136|138)", "$problem") != true ) {
                    $query = "INSERT INTO `tickets` (
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
                    $result = mysql_query($query) or die('false');
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
        $query = "DELETE FROM `robot_log` WHERE `id` = $id";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

        // echo $query;
        // Освобождаем память от результата
        // mysql_free_result($result);

    }

    public

    function get_info_robot($id)
    {
        $query = "SELECT * FROM robots WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $robot_array[] = $line;
        }

        // Освобождаем память от результата

        mysql_free_result($result);
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

        $query1 = "INSERT INTO `robots` (
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
        $result1 = mysql_query($query1) or die($query1);
        $idd = mysql_insert_id();

        // Освобождаем память от результата
        // mysql_free_result($result);
        $arr_mh = Array();
        $arr_hp = Array();
        $arr_bd = Array();
        $arr_up = Array();
        $arr_hs = Array();

        $query = "SELECT * FROM check_items WHERE category='1' AND version=$version ORDER BY `sort` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $arr_mh[] = $line;
        }

        $query = "SELECT * FROM check_items WHERE category='2' AND version=$version ORDER BY `sort` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $arr_hp[] = $line;
        }

        $query = "SELECT * FROM check_items WHERE category='3' AND version=$version ORDER BY `sort` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $arr_bd[] = $line;
        }

        $query = "SELECT * FROM check_items WHERE category='4' AND version=$version ORDER BY `sort` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $arr_up[] = $line;
        }

        $query = "SELECT * FROM check_items WHERE category='5' AND version=$version ORDER BY `sort` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
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
            $query = "INSERT INTO `check` (
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
            $result = mysql_query($query) or die('false');
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

        $query = "UPDATE `robots` SET 
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
        $result = mysql_query($query) or die($query);
        $idd = mysql_insert_id();

        $query = "DELETE FROM `robot_options_items` WHERE `id_robot` = $id";
        $result = mysql_query($query) or die($query);

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

        // Освобождаем память от результата
        // mysql_free_result($result);

    }

    function sortable($json)
    {
        foreach($json as $key => $value)
        {
            $query = "UPDATE `robots` SET `sort` = '$key' WHERE `robots`.`id` = $value";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        }

        if (isset($robots_array)) return $robots_array;
    }

    function add_customer($name, $fio, $phone, $email, $address)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "INSERT INTO `customers` (`id`, `name`,  `fio`,  `phone`,  `email`,  `address` ) VALUES (NULL,  '$name' ,  '$fio',  '$phone',  '$email',  '$address')";
        $result = mysql_query($query) or die('false');
        $idd = mysql_insert_id();
        return $idd;
    }

    function onRemont($robot)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "UPDATE `robots` SET `remont` = remont+1, `progress` = 0, `last_operation` = '', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $robot";
        $result = mysql_query($query) or die('false');
        $query = "UPDATE `check` SET `check` = '0' WHERE `robot` = $robot";
        $result = mysql_query($query) or die('false');
        $query = "INSERT INTO `robot_log` (`id`, `robot_id`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $robot, 'MODERN', 'Робот прибыл на ремонт', $user_id, '$date')";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        return $result;
    }

    function countRemont($robot)
    {
        $query = "SELECT remont FROM `robots` WHERE id=$robot";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $row = mysql_fetch_array($result);
        return $row['remont'];
    }

    function get_options()
    {
        $query = "SELECT * FROM robot_options ORDER BY `title` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $option_array[] = $line;
        }
        if (isset($option_array))
            return $option_array;
    }

    function add_options_on_robot($option,$robot)
    {
        $query = "INSERT INTO `robot_options_items` (`id_row`, `id_option`, `id_robot`) VALUES (NULL, $option, $robot)";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());






        return $result;
    }

    function get_robot_options($robot = 0)
    {
        $query = "SELECT * FROM `robot_options`";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $n = 0;
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $options_array[$n]['id'] = $line['id_option'];
            $options_array[$n]['title'] = $line['title'];
            $id = $line['id_option'];
            $query_t = "SELECT COUNT(*) FROM `robot_options_items` WHERE `id_robot` = $robot AND id_option = $id";
            $result_t = mysql_query($query_t) or die('Запрос не удался: ' . mysql_error());
            $line_t = mysql_fetch_array($result_t, MYSQL_ASSOC);
            //print_r($line_t);
            if ($line_t['COUNT(*)'] != 0) {
                $options_array[$n]['check']   = 1;
            } else { $options_array[$n]['check']   = 0; }
            $n++;

        }

        mysql_free_result($result);
        if (isset($options_array)) return $options_array;
    }

    function get_info_option($id)
    {
        $query = "SELECT * FROM robot_options WHERE id_option='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $option_array[] = $line;
        }

        // Освобождаем память от результата

        mysql_free_result($result);
        if (isset($option_array)) return $option_array['0'];
    }

    function edit_option ($id,$title) {

        $query = "UPDATE `robot_options` SET `title` = '$title' WHERE `id_option` = $id";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        return $result;
    }

    function telegram_get_id ($chat) {

        $query = "SELECT * FROM `robots`  WHERE telegram LIKE '$chat'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
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

$robots = new Robots;