<?php
class Robots

{
    private $query;
    private $pdo;
    private $telegram;
    private $sklad;
    private $checks;
    private $plan;

    //списки
    public $getEquipment;
    public $getOptions;

    const LANGUAGE =[
        "russian" => "Русский",
        "russian" => "Русский",
        "english" => "Английский",
        "spanish" => "Испаниский",
        "turkish" => "Турецкий",
        "arab" => "Арабский",
        "portuguese" => "Португальский",
        "german" => "Немецкий",
    ];



    function __construct()
    {

        global $database_server, $database_user, $database_password, $dbase;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);
    }

    function init()
    {
        global $telegramAPI, $position, $checks, $plan;

        $this->telegram = $telegramAPI; //new TelegramAPI;
        $this->sklad = $position; //new Position;
        $this->checks = $checks;
        $this->plan = $plan;

        //список версий роботов
        $query = "SELECT * FROM `robot_equipment` ORDER BY `title` DESC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $equipment[$line['id']] = $line;
        }
        $this->getEquipment = (isset($equipment)) ? $equipment : [];

        //список опций
        $query = "SELECT * FROM `robot_options` ORDER BY `title` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $options[$line['id_option']] = $line;
        }
        $this->getOptions = (isset($options)) ? $options : [];

    }

    function get_robots()
    {
        $this->query = "SELECT * FROM robots WHERE `delete` != 1  ORDER BY `number` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $robots_array[] = $line;
        }

        if (isset($robots_array)) return $robots_array;
    }

    //удаление роботоа
    function del_robot($id)
    {
        //получение данных по роботу
        $this->query  = "SELECT * FROM `robots` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        $robot = $line = $result->fetch();

        //проверка отмеченных чек-листов
        $query = "SELECT COUNT(*) FROM `check` WHERE `robot` = $id AND `check` = 1";
        $result = $this->pdo->query($query);
        $count = $result->fetch()['COUNT(*)'];

        if ($count == 0) {
            //удаляем все опции
            $query = "DELETE FROM `robot_options_items` WHERE `id_robot` = $id";
            $result = $this->pdo->query($query);
            //списываем резервы
            $arr_kits = $this->plan->get_kits();
            $query = "
            SELECT `id_kit` FROM `check` WHERE `robot` = $id AND `id_kit` != 0";
            $result = $this->pdo->query($query);
            $kits = [];
            while ($line = $result->fetch()) {
                $kits[] = $line['id_kit'];
            }
            foreach ($kits as $kit) {
                $this->sklad->del_reserv($arr_kits[$kit]);
            }
            //удаляем все чек-листы
            $query = "DELETE FROM `check` WHERE `robot` = $id";
            $result = $this->pdo->query($query);
            //удаляем самого робота
            $query = "DELETE FROM `robots` WHERE `id` = $id";
            $result = $this->pdo->query($query);
            return ['result' => true, 'err' => 'Ошибок нет!'];
        } else {
            return ['result' => false, 'err' => 'Удалить невозможно: есть завершенные чек-листы - снемите отметку!'];
        }

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

        $number_str = substr(strstr($host, '_'), 1, strlen($host));
        $number = $number_str;
        $number_0 = ltrim($number, '0');
        $version = substr($number,0,1);
        if ($version != '5' && $version != '6' && $version != '7') {
            $version = '4';
        }

        $this->query = "SELECT * FROM robots WHERE (number='$number' OR number='$number_0') AND version = '$version'";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $robot_array[] = $line;
        }

        if (isset($robot_array)) {
            $robot_id = $robot_array[0]['id'];
            $t_chat_id = $robot_array[0]['telegram'];
            // echo $query;

            $this->query = "SELECT * FROM `robot_zabbix` WHERE `id_event` = $id";
            $result = $this->pdo->query($this->query);

            while ($line = $result->fetch()) {
                $event_array[] = $line;
            }

            if (isset($event_array)) {
                $this->query = "UPDATE `robot_zabbix` SET `status` = '$status' WHERE `id_event` = $id";
                $result = $this->pdo->query($this->query);
            } else {
                $this->query = "INSERT INTO `robot_zabbix` (`id`, `id_robot`,`number_robot`,`host`, `time`, `problem`,`total_uptime`, `update_date`,`id_event`, `status`) VALUES (NULL, $robot_id, $number, '$host', '$time', '$problem','$total_uptime', '$date', '$id', '$status')";
                $result = $this->pdo->query($this->query);
            }

            $this->query = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `update_user`, `update_date`) VALUES (NULL, $robot_id, 'ZABBIX','WARNING', '$problem', 33, '$date')";
            $result = $this->pdo->query($this->query);
        } else {
            $this->query = "INSERT INTO `robot_zabbix` (`id`, `id_robot`,`number_robot`,`host`, `time`, `problem`,`total_uptime`, `update_date`,`id_event`, `status`) VALUES (NULL, 0, $number, '$host', '$time', '$problem', '$total_uptime', '$date', '$id', '$status')";
            $result = $this->pdo->query($this->query);
        }

        switch ($type_message) {
            case "Disaster":
                if ($status == "OK") {
                    $icon = '✅';
                } else {
                    $icon = '🆘';
                }

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


        if ($type == 0 && $client != 1) {

            $this->query = "SELECT * FROM `tickets` WHERE `description` LIKE '%$problem%' AND `robot` = $robot_id AND ( `status` != 6 OR `status` != 3) AND `date_create` >= date_sub(now(), INTERVAL 1 HOUR)";
            $result = $this->pdo->query($this->query);
            $line = $result->fetch();
            if ($line == null) {
                $cat_zabbix = stristr($problem, '.', true);
                $this->query = "SELECT id FROM `tickets_category` WHERE `zabbix` LIKE '$cat_zabbix'";
                $result = $this->pdo->query($this->query);
                $line = $result->fetch();
                $cat_id = $line['id'];
                $robot = $robot_id;
                $date = date("Y-m-d H:i:s");
                $user_id = 33;

                if ($status != "OK" and preg_match("(2048|640|136|138)", "$problem") != true) {
                    //не создавать тикеты для версии для 6 и 7 версии роботов
                    if ($version != 6 && $version != 7) {
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
                }
                if ($client != 1 and preg_match("(2048|640|136|138)", "$problem") != true) {
                    $this->telegram->sendNotify("tehpod", $telegram_str . " - " . $status);
                }

            } else {
                if ($status == "OK" and preg_match("(2048|640|136|138)", "$problem") != true) {
                    if ($client != 1) {
                        $this->telegram->sendNotify("tehpod", $telegram_str . " - " . $status);
                    }
                }
            }
        } else {
            if ($client == 1) {
                $this->telegram->sendNotify("client", $telegram_str, $t_chat_id);
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

    function print_info_robot($id)
    {
        $robot = $this->get_info_robot($id);
        if (isset($robot)) {
            $robot['version'] = $this->getEquipment[$robot['version']]['title'];
            //заполняем покупателя
            if ($robot['customer'] != 0) {
                $customer = $this->get_customers()[$robot['customer']];
                $robot['customer'] = $customer['name'];
                $robot['fio'] = $customer['fio'];
                $robot['email'] = $customer['email'];
                $robot['phone'] = $customer['phone'];
                $robot['address'] = $customer['address'];
                $robot['inn'] = $customer['inn'];
            } else {
                $robot['customer'] = '';
                $robot['fio'] = '';
                $robot['email'] = '';
                $robot['phone'] = '';
                $robot['address'] = '';
                $robot['inn'] = '';
            }
            //заполняем владельца
            if ($robot['owner'] != 0) {
                $owner = $this->get_customers()[$robot['owner']];
                $robot['owner'] = $owner['name'];
                $robot['ident'] = $owner['ident'];
            } else {
                $robot['owner'] = '';
                $robot['ident'] = '';
            }

            $robot['brand'] = ($robot['brand'] == '') ? 'Нет' : $robot['brand'];
            $robot['ikp'] = ($robot['ikp'] == '') ? 'Нет' : $robot['ikp'];
            $robot['dop'] = ($robot['dop'] == '') ? 'Нет' : $robot['dop'];
            $robot['battery'] = ($robot['battery'] == 1) ? 'Есть' : 'Нет';
            $robot['language_robot'] = ($robot['language_robot'] != '') ? self::LANGUAGE[$robot['language_robot']] : '';
            $robot['language_doc'] = ($robot['language_doc'] != '') ? self::LANGUAGE[$robot['language_doc']] : '';

            $options = '';
            foreach ($this->get_robot_options($robot['id']) as $option) {
                if ($option['check'] == 1) {
                    $options .= '+'.$option['title'].'<br>';
                }
            }
            $robot['options'] = ($options == '') ? 'Нет' : $options;

        }

        return (isset($robot)) ? $robot : null;
    }

    //запуск в производство робота
    function launch_production_robot($id) {
        $robot = $this->get_info_robot($id);
        if ($robot['delete'] != 2) {
            return false;
        }
        $query = "UPDATE `robots` SET `delete` = '0' WHERE `id` = $id";
        $result = $this->pdo->query($query);
        $options =[];
        foreach ($this->get_robot_options($robot['id']) as $option) {
            if ($option['check'] == 1) {
                $options[] = $option['id'];
            }
        }
        //собираем чеклисты привязанные к роботу
        if ($result) {
            //категории от 1 до 5
            for ($i=1; $i<=5; $i++) {
                $this->checks->add_robot_check($robot['version'], $i, $id);
            }
            //добавлем опции
            foreach ($options as $option) {
                $this->checks->add_option_check($option, $id, 1);
            }
            return true;
        }

        return false;
    }

    //добавить робота
    function add_robot($number, $name, $version, $options, $customer, $owner, $language_robot, $language_doc, $charger, $color, $brand, $ikp, $battery, $dop, $dop_manufactur, $date_start, $date_test, $send, $delivery)
    {
        $date_start = new DateTime($date_start);
        $date_start = $date_start->format('Y-m-d H:i:s');
        if ($date_test == null) {
            $date_test = new DateTime($date_test);
            $date_test->modify('+1 month');
        } else {
            $date_test = new DateTime($date_test);
        }
        $date_test = $date_test->format('Y-m-d H:i:s');
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $delete = 0;
        if ($number == '') {
            $number = '9999';
            $delete = 2;
        }
        $number = str_pad($number, 4, "0", STR_PAD_LEFT);
        if (!is_array($options)) {
            $options = [];
        }
        if ($send == 1) {
            $progress = 100;
        } else {
            $progress = 0;
        }

        $this->query = "INSERT 
            INTO `robots` (`id`, `version`, `number`, `name`, `customer`, `owner`, `language_robot`, `language_doc`, `charger`, `color`, `brand`, `ikp`, `battery`, `dop`, `dop_manufactur`, `progress`, `date`, `date_test`, `update_date`, `update_user`, `delete`, `delivery`) 
            VALUES (NULL, '$version', '$number', '$name', '$customer', '$owner', '$language_robot', '$language_doc', '$charger', '$color', '$brand', '$ikp', '$battery', '$dop', '$dop_manufactur', '$progress', '$date_start', '$date_test', '$date', '$user_id', '$delete', '$delivery')
        ";
        $result = $this->pdo->query($this->query);

        //собираем чеклисты привязанные к роботу
        if ($result) {
            $idd = $this->pdo->lastInsertId();
            //категории от 1 до 5
            for ($i=1; $i<=5; $i++) {
                if ($delete == 2) {break;}
                $this->checks->add_robot_check($version, $i, $idd);
            }
            //добавлем опции
            if (isset($options)) {
                foreach ($options as $option) {
                    $this->add_options_on_robot($option, $idd);
                    if ($delete == 2) {continue;}
                    $this->checks->add_option_check($option, $idd,1);
                }
            }
            //простовляем отметки по чеклистам без списания для старых роботов
            if ($send == 1) {
                $this->checks->checked_all_check_in_robot($idd);
            }

            return true;
        }

        return false;
    }

    /*старый код
    function add_robot($number, $name, $version, $photo, $termo, $dispenser, $terminal, $kaznachey, $lidar, $other, $customer, $language_robot, $language_doc, $charger, $color, $brand, $ikp, $battery, $dop, $dop_manufactur, $send, $date_start, $date_test)
    {
        $date_start = new DateTime($date_start);
        $date_start = $date_start->format('Y-m-d H:i:s');

        $date_test = new DateTime($date_test);
        $date_test = $date_test->format('Y-m-d H:i:s');

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $number = str_pad($number, 4, "0", STR_PAD_LEFT);

        if ($send == 1) {
            $progress = 100;
        } else {
            $progress = 0;
        }

        $this->query = "INSERT
            INTO `robots` (`id`, `version`, `number`, `photo`, `termo`, `dispenser`,  `terminal`, `kaznachey`, `lidar`, `other`, `name`, `customer`, `language_robot`, `language_doc`, `charger`, `color`, `brand`, `ikp`, `battery`, `dop`, `dop_manufactur`, `progress`, `date`, `date_test`, `update_date`, `update_user`)
            VALUES (NULL, '$version', '$number', '$photo', '$termo', '$dispenser', '$terminal', '$kaznachey', '$lidar', '$other', '$name', '$customer', '$language_robot', '$language_doc', '$charger', '$color', '$brand', '$ikp', '$battery', '$dop', '$dop_manufactur', '$progress', '$date_start', '$date_test', '$date', '$user_id')
        ";
        $result = $this->pdo->query($this->query);
        $idd = $this->pdo->lastInsertId();

        //собираем чеклисты привязанные к роботу
        $arr_mh = Array();
        $arr_hp = Array();
        $arr_bd = Array();
        $arr_up = Array();
        $arr_hs = Array();
        $this->query = "SELECT * FROM check_items WHERE category='1' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $arr_mh[] = $line;
        }
        $this->query = "SELECT * FROM check_items WHERE category='2' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $arr_hp[] = $line;
        }
        $this->query = "SELECT * FROM check_items WHERE category='3' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $arr_bd[] = $line;
        }
        $this->query = "SELECT * FROM check_items WHERE category='4' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $arr_up[] = $line;
        }
        $this->query = "SELECT * FROM check_items WHERE category='5' AND version=$version ORDER BY `sort` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $arr_hs[] = $line;
        }
        $result_arr = array_merge($arr_mh, $arr_hp, $arr_bd, $arr_up, $arr_hs);

        //создаем чеклисты по новому роботу
        foreach ($result_arr as & $value) {
            $operation = $value['title'];
            $category = $value['category'];
            $group = $value['group'];
            $sort = $value['sort'];
            $id_check = $value['id'];
            $id_kit = $value['kit'];
            $this->query = "INSERT INTO `check` (`id`, `id_check`, `robot`, `operation`, `category`, `group`, `check`, `sort`, `id_kit`, `update_user` ) VALUES (NULL, '$id_check', '$idd', '$operation', '$category', '$group', '0', '$sort', '$id_kit', '0')";
            $result = $this->pdo->query($this->query);
        }
        //$this->sklad->set_reserv($version);
    }*/

    function edit_robot($id, $number, $name, $version, $options, $customer, $owner, $language_robot, $language_doc, $charger, $color, $brand, $ikp, $battery, $dop, $dop_manufactur, $date_start, $date_test, $send, $delivery)
    {
        $date_start = new DateTime($date_start);
        $date_start = $date_start->format('Y-m-d H:i:s');

        $date_test = new DateTime($date_test);
        $date_test = $date_test->format('Y-m-d H:i:s');

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $number = str_pad($number, 4, "0", STR_PAD_LEFT);
        $progress = $this->get_info_robot($id)['progress'];

        //старый робот или нет, если старый то 100% и выполняем отметку чек листов в самом низу
        if ($send == 1 && $progress == 0) {
            $robot_old = true;
            $progress = 100;
        } else {
            $robot_old = false;
        }

        /*if ($send == 1) {
            $progress = 100;
        } else {
            $progress = $this->get_info_robot($id)['progress'];
        }*/

        //вносим изменения в робота
        $this->query = "UPDATE `robots` SET 
        `version` = '$version', 
        `number` = '$number', 
        `name` = '$name', 
        `customer` = '$customer',
        `owner` = '$owner',
        `language_robot` = '$language_robot', 
        `language_doc` = '$language_doc', 
        `charger` = '$charger', 
        `color` = '$color', 
        `brand` = '$brand', 
        `ikp` = '$ikp', 
        `battery` = '$battery', 
        `dop` = '$dop', 
        `dop_manufactur` = '$dop_manufactur',
        `progress`  = $progress,
        `date` = '$date_start',
        `date_test` = '$date_test',
        `delivery` = '$delivery',
        `update_user` = '$user_id', 
        `update_date` = '$date' 
        WHERE `id` = $id";
        $result = $this->pdo->query($this->query);

        //собираем массив опций которые были
        $this->query = "SELECT id_option FROM `robot_options_items` WHERE `id_robot` = $id";
        $result = $this->pdo->query($this->query);
        $options_old = [];
        while ($line = $result->fetch())
        {
            $options_old[] = $line['id_option'];
        }
        if (!is_array($options)) {
            $options = [];
        }
        $robot = $this->get_info_robot($id);
        //удаляем опции
        $del = array_diff($options_old, $options);
        if (isset($del)) {
            foreach ($del as $option) {
                $this->del_options_on_robot($option, $id);
                if ($robot['delete'] == 2) {continue;}
                $this->checks->add_option_check($option, $id,0);
            }
        }
        //добавлем опции
        $add = array_diff($options, $options_old);
        if (isset($add)) {
            foreach ($add as $option) {
                $this->add_options_on_robot($option, $id);
                if ($robot['delete'] == 2) {continue;}
                $this->checks->add_option_check($option, $id,1);
            }
        }

        //простовляем отметки по чеклистам без списания для старых роботов
        if ($robot_old) {
            $this->checks->checked_all_check_in_robot($id);
        }

        /*$text = 'add -'.$option;
        $log = date('Y-m-d H:i:s') . ' ' . print_r($text, true);
        file_put_contents(__DIR__ . '/log111.txt', $log . PHP_EOL, FILE_APPEND);
        die;*/

        //это старый код

        //$idd = $this->pdo->lastInsertId();
        //удаляем все опции у робота и если что то удалилось переписываем опции
        /*$this->query = "DELETE FROM `robot_options_items` WHERE `id_robot` = $id";
        $result = $this->pdo->query($this->query);
        if ($result) {
            foreach ($options as &$value) {
                $this->add_options_on_robot($value,$id);
            }
        }*/

        //$robot_info= $this->get_info_robot($id);
        //если сменился заказчик отправляем сообщение телеграм не работает
        /*$old_name = $robot_info['name'];
        if($name!=$old_name) {
            $comment      = "У робота $version.$number изменен заказчик на - $name";
            $telegram_str = $comment;
            $this->telegram->sendNotify("sale", $telegram_str);
        }*/
        //пока не работает по этому выключил
        /*$old_date = $robot_info['name'];
        if($name!=$old_name) {
            $comment      = "У робота $version.$number изменен заказчик на - $name";
            $telegram_str = $comment;
            $this->telegram->sendNotify("sale", $telegram_str);
        }*/
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

    //создать покупателя
    function add_customer($name, $fio, $phone, $email, $address)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "INSERT INTO `customers` (`id`, `name`,  `fio`,  `phone`,  `email`,  `address` ) VALUES (NULL,  '$name' ,  '$fio',  '$phone',  '$email',  '$address')";
        $result = $this->pdo->query($this->query);
        $idd = $this->pdo->lastInsertId();
        return $idd;
    }

    //создать покупателя все данные
    function add_full_customer($name, $fio, $phone, $email, $address, $inn, $ident)
    {
        $query = "INSERT INTO `customers` (`id`, `name`, `fio`, `phone`, `email`, `address`, `inn`, `ident`) VALUES (NULL, '$name', '$fio', '$phone', '$email', '$address', '$inn', '$ident')";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    //редактировать покупателя
    function edit_customer($id, $name, $fio, $phone, $email, $address, $inn, $ident)
    {
        $query = "UPDATE `customers` SET `name` = '$name', `fio` = '$fio', `phone` = '$phone', `email` = '$email', `address` = '$address', `inn` = '$inn', `ident` = '$ident' WHERE `id` = $id;";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    //удалить поставщика
    function del_customer($id)
    {
        $query = "DELETE FROM `customers` WHERE `id` = $id";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    //все покупатели
    function get_customers()
    {
        $query = "SELECT * FROM customers  ORDER BY `name` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $customer_array[$line['id']] = $line;
        }

        if (isset($customer_array)) return $customer_array;
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

    //создать опцию
    function add_option($version = 0, $title)
    {
        $this->query = "INSERT INTO `robot_options` (`id_option`, `version`, `title`, `id_kit`) VALUES (NULL, $version, '$title', '0')";
        $result = $this->pdo->query($this->query);
        return true;
    }

    //удалить опцию
    function del_option($id)
    {
        $this->query = "DELETE FROM `robot_options_checks` WHERE `id_option` = $id";
        $result = $this->pdo->query($this->query);
        $this->query = "DELETE FROM `robot_options` WHERE `id_option` = $id";
        $result = $this->pdo->query($this->query);
        return true;
    }

    //собрать все опции
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

    //добавление опции к роботу
    function add_options_on_robot($option,$robot)
    {
        $this->query = "INSERT INTO `robot_options_items` (`id_row`, `id_option`, `id_robot`) VALUES (NULL, $option, $robot)";
        $result = $this->pdo->query($this->query);
        return $result;
    }

    //удаление опции у робота
    function del_options_on_robot($option,$robot)
    {
        $this->query = "DELETE FROM `robot_options_items` WHERE `id_robot` = $robot AND `id_option` = $option";
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

    function edit_option ($id, $version, $title) {

        $this->query = "UPDATE `robot_options` SET `title` = '$title', `version` = '$version' WHERE `id_option` = $id";
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

    //проверка есть ли отмеченые чеклисты по опции
    function get_dis_check_option($option_id, $robot_id) {
        //var_dump($robot_id);die;
        $this->query = "SELECT `id` FROM `check` WHERE `robot`=$robot_id AND `option`=$option_id AND `check`=1";
        $result = $this->pdo->query($this->query);
        $check = [];
        while ($line = $result->fetch()) {
            $check[] = $line;
        }

        return (count($check) == 0) ? false : true;
    }


    function __destruct()
    {

        // echo "robots - ";
        // print_r($this ->link_robots);
        // echo "<br />";
        // mysql_close($this ->link_robots);

    }
}