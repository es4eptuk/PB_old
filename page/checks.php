<?php
class Checks
{
    public $auth;
    private $query;
    private $pdo;

    private $telegram;
    private $robot;
    private $sklad;
    private $mail;
    private $plan;
    private $statistics;

    const ZABIX = [
        '2' => ['url' => '', 'user' => '', 'password' => '', 'Manufacture' => '', 'Manufacture_test' => '', 'host' => ''],
        '5' => ['url' => '', 'user' => '', 'password' => '', 'Manufacture' => '', 'Manufacture_test' => '', 'host' => ''],
        '4' => ['url' => 'https://pb2.icmm.ru/zabbix/api_jsonrpc.php', 'user' => 'manufacture', 'password' => 'queetoh6Ace', 'Manufacture' => '32', 'Manufacture_test' => '31', 'host' => 'promobotv4_'],
        '6' => ['url' => 'https://195.69.158.137/zabbix/api_jsonrpc.php', 'user' => 'manufacture', 'password' => 'queetoh6Ace', 'Manufacture' => '15', 'Manufacture_test' => '19', 'host' => 'promobotv4_'],
        '7' => ['url' => 'https://195.69.158.137/zabbix/api_jsonrpc.php', 'user' => 'manufacture', 'password' => 'queetoh6Ace', 'Manufacture' => '15', 'Manufacture_test' => '19', 'host' => 'promobotv4_'],
        '8' => ['url' => 'https://195.69.158.137/zabbix/api_jsonrpc.php', 'user' => 'manufacture', 'password' => 'queetoh6Ace', 'Manufacture' => '15', 'Manufacture_test' => '19', 'host' => 'promobotv4_'],
        //дубль
        '21' => ['url' => '', 'user' => '', 'password' => '', 'Manufacture' => '', 'Manufacture_test' => '', 'host' => ''],
        '51' => ['url' => '', 'user' => '', 'password' => '', 'Manufacture' => '', 'Manufacture_test' => '', 'host' => ''],
        '41' => ['url' => 'https://pb-srv8.promo-bot.ru/zabbix/api_jsonrpc.php', 'user' => 'manufacture', 'password' => 'queetoh6Ace', 'Manufacture' => '32', 'Manufacture_test' => '31', 'host' => 'promobotv4_'],
    ];




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
        global $telegramAPI, $robots, $position, $mail, $plan, $statistics;

        $this->telegram = $telegramAPI; //new TelegramAPI;
        $this->robot = $robots; //new Robots;
        $this->sklad = $position; //new Position;
        $this->mail = $mail; //new Mail;
        $this->plan = $plan;
        $this->statistics = $statistics;
        //$this -> robot = new Robots;
    }

    function get_checks_in_cat($category, $subversion = 0)
    {
        $and = ($subversion != 0) ? "AND subversion = $subversion" : "";
        $query = "SELECT * FROM `check_items` WHERE `category` = '$category' $and ORDER BY `sort` ASC";
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

    function add_check($category, $title, $sort, $subversion, $kit)
    {
        $version = $this->robot->getSubVersion[$subversion]['id_version'];
        $title   = trim($title);
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `check_items` (`id`, `name`, `title`, `category`, `sort` , `version`, `subversion`, `kit`) VALUES (NULL, '', '$title', '$category',  '$sort', $version, $subversion, $kit)";
        $result = $this->pdo->query($query);
        $idd   = $this->pdo->lastInsertId();
        $query = "SELECT * FROM `robots` WHERE `subversion` = $subversion AND `progress` != 100 ORDER BY `sort` ASC";
        $result = $this->pdo->query($query);
        $robots_array = [];
        while ($line = $result->fetch()) {
            $robots_array[] = $line;
        }
        $arr_kits = $this->plan->get_kits();
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
                         NULL, 
                         '0')";
            $result = $this->pdo->query($query);
            //добавляем резерв
            $this->sklad->add_reserv($arr_kits[$kit]);
        }

        return $result;
    }

    function edit_check($id, $title, $kit, $subversion)
    {
        $version = $this->robot->getSubVersion[$subversion]['id_version'];
        $title   = trim($title);
        //$date    = date("Y-m-d H:i:s");
        //$user_id = intval($_COOKIE['id']);
        $query   = "SELECT * FROM `check_items` WHERE `id` = $id";
        $result = $this->pdo->query($query);
        $old_kit = $result->fetch()['kit'];

        $query   = "UPDATE `check_items` SET `title` = '$title ', `kit` = $kit, `version` = $version, `subversion` = $subversion  WHERE `id` = $id";
        $result = $this->pdo->query($query);

        if ($old_kit != $kit) {
            //кол чеков со старым китом
            $query = "
            SELECT COUNT(*) FROM `check` 
            JOIN `robots` ON `check`.`robot` = `robots`.`id` 
            WHERE `check`.`id_check` = $id
                AND `check`.`check` = 0 
                AND `robots`.`writeoff` = 0 
                AND `robots`.`remont` = 0 
                AND `robots`.`delete` = 0
            ";
            $result = $this->pdo->query($query);
            $count = $result->fetch()['COUNT(*)'];
            $arr_kits = $this->plan->get_kits();
            //убираем старое списание
            if ($old_kit != 0) {
                foreach ($arr_kits[$old_kit] as $id_pos => $total) {
                    $del_res[$id_pos] = $total * $count;
                }
                $this->sklad->del_reserv($del_res);
            }
            //добавляем новое списание
            if ($kit != 0) {
                foreach ($arr_kits[$kit] as $id_pos => $total) {
                    $new_res[$id_pos] = $total * $count;
                }
                $this->sklad->add_reserv($new_res);
            }
        }

        //меняем текущие чеклисты
        $query = "
            UPDATE `check` 
            JOIN `robots` ON `check`.`robot` = `robots`.`id`
            SET `operation` = '$title', `id_kit` = '$kit'
            WHERE `check`.`id_check` = $id
                AND `check`.`check` = 0 
                AND `robots`.`writeoff` = 0 
                AND `robots`.`remont` = 0 
                AND `robots`.`delete` = 0
        ";
        $result = $this->pdo->query($query);
        return true;
    }

    function edit_check_on_option($id, $title, $category, $kit)
    {
        $title   = trim($title);
        //$date    = date("Y-m-d H:i:s");
        //$user_id = intval($_COOKIE['id']);
        $query   = "SELECT * FROM `robot_options_checks` WHERE `check_id` = $id";
        $result = $this->pdo->query($query);
        $old_option = $result->fetch();
        $old_kit = $old_option['id_kit'];
        $id_option = $old_option['id_option'];
        $check_category = $old_option['check_category'];
        $check_title = trim($old_option['check_title']);

        $query   = "UPDATE `robot_options_checks` SET `check_title` = '$title ',  `check_category` = $category,  `id_kit` = $kit  WHERE `check_id` = $id";
        $result = $this->pdo->query($query);

        if ($old_kit != $kit) {
            //кол чеков со старым китом
            $query = "
            SELECT COUNT(*) FROM `check` 
            JOIN `robots` ON `check`.`robot` = `robots`.`id` 
            WHERE `check`.`check` = 0 
                AND `robots`.`writeoff` = 0 
                AND `robots`.`remont` = 0 
                AND `robots`.`delete` = 0
                AND `check`.`option` = '$id_option' 
                AND `check`.`category` = '$check_category' 
                AND `check`.`operation` LIKE '$check_title'
            ";
            $result = $this->pdo->query($query);
            $count = $result->fetch()['COUNT(*)'];
            $arr_kits = $this->plan->get_kits();
            //убираем старое списание
            if ($old_kit != 0) {
                foreach ($arr_kits[$old_kit] as $id_pos => $total) {
                    $del_res[$id_pos] = $total * $count;
                }
                $this->sklad->del_reserv($del_res);
            }
            //добавляем новое списание
            if ($kit != 0) {
                foreach ($arr_kits[$kit] as $id_pos => $total) {
                    $new_res[$id_pos] = $total * $count;
                }
                $this->sklad->add_reserv($new_res);
            }
        }

        $query = "
            UPDATE `check` 
            JOIN `robots` ON `check`.`robot` = `robots`.`id`
            SET `operation` = '$title', `id_kit` = '$kit', `category` = '$category'
            WHERE `check`.`check` = 0 
                AND `robots`.`writeoff` = 0 
                AND `robots`.`remont` = 0 
                AND `robots`.`delete` = 0
                AND `check`.`option` = '$id_option' 
                AND `check`.`category` = '$check_category' 
                AND `check`.`operation` LIKE '$check_title'
        ";
        $result = $this->pdo->query($query);
        return true;
    }

    //собирает чек лесты по выбранной категории и роботу (базовые)
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

    //собирает чек лесты по выбранной категории и роботу (только по опциям)
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

    //
    function add_check_on_robot($id_row, $robot, $id, $value, $number, $remont, $kit)
    {
        //запуск учета времени сборки
        if ($this->statistics->get_robot_production_statistics($robot) == null && $remont == 0) {
            $this->statistics->add_robot_production_statistics($robot);
        }

        $checks_array = [];
        $query = "SELECT * FROM `check` WHERE `id` = $id_row";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $checks_array[] = $line;
        }

        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $arr_kits = $this->plan->get_kits();
        if ($kit != 0) {
            if ($checks_array[0]['id_kit'] != $kit) {
                //списание старого резерва
                $this->sklad->del_reserv($arr_kits[$checks_array[0]['id_kit']]);
                //создание нового резерва
                $this->sklad->add_reserv($arr_kits[$kit]);
            }

        }
        /* при снятии чека 131(Проверка после первичного теста) ничего не делать!!! */
        /*if ($id == 131 && $value == 0) {
            return false;
        }*/
        $query = "UPDATE `check` SET `check` = '$value', `update_user` = '$user_id', `update_date` = '$date', `id_kit` = '$kit' WHERE `id` = $id_row";
        if ($value == 1 && $remont == 0 && $checks_array[0]['check_f_date'] == null) {
            $query = "UPDATE `check` SET `check` = '$value', `update_user` = '$user_id', `update_date` = '$date', `id_kit` = '$kit', `check_f_date` = '$date' WHERE `id` = $id_row";
        }
        $result = $this->pdo->query($query);
        $query = "SELECT * FROM `check` WHERE `id` = $id_row";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $check_items_array[] = $line;
        }
        $title = $check_items_array['0']['operation'];
        $stage = $check_items_array['0']['category'];

        /* при отметке чека 54(Включение/выключение робота) отправка сообщения в телегу */
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

        /* при отметке чека 105(отправка) смена состояния ПО */
        /* при отметке чека 314(отправка) смена состояния ПО */
        /* при отметке чека 553(отправка) смена состояния ПО */
        /* при отметке чека 548(отправка) смена состояния ПО */

        /* при отметке чека 671(отправка) смена состояния ПО */
        /* при отметке чека 687(отправка) смена состояния ПО */
        /* при отметке чека 789(отправка) смена состояния ПО */
        /* при отметке чека 807(отправка) смена состояния ПО */
        /* при отметке чека 962(отправка) смена состояния ПО */
        /* при отметке чека 980(отправка) смена состояния ПО */
        /* при отметке чека 1034(отправка) смена состояния ПО */
        /* при отметке чека 1061(отправка) смена состояния ПО */


        if (($id == 105 || $id == 314 || $id == 553 || $id == 548 || $id == 671 || $id == 687 || $id == 789 || $id == 807 || $id == 962 || $id == 980 || $id == 1034 || $id == 1061) && $value == 1) {
            $query = "SELECT * FROM robots WHERE id='$robot'";
            $result = $this->pdo->query($query);
            while ($line = $result->fetch()) {
                $robot_array[] = $line;
            }
            $version = $robot_array[0]['version'];
            if ($version != 5 && $version != 6 && $version != 7 && $version != 8) {
                $version = 4;
            }
            $robot_name = $robot_array[0]['name'];
            $num        = str_pad($number, 4, "0", STR_PAD_LEFT);
            $this->auth = $this->z_auth_new($version);
            $z_host     = $this->z_get_hosts_new(['host' => self::ZABIX[$version]['host'].$num], $version);
            $this->z_remove_group_new($z_host[0]['hostid'], self::ZABIX[$version]['Manufacture_test'], $version);
            $this->z_remove_group_new($z_host[0]['hostid'], self::ZABIX[$version]['Manufacture'], $version);

            //!!!временно для синхронизации
            if ($version == 4) {
                $this->auth = null;
                $versionD = $version . '1';
                $this->auth = $this->z_auth_new($versionD);
                $z_host     = $this->z_get_hosts_new(['host' => self::ZABIX[$versionD]['host'].$num], $versionD);
                $this->z_remove_group_new($z_host[0]['hostid'], self::ZABIX[$versionD]['Manufacture_test'], $versionD);
                $this->z_remove_group_new($z_host[0]['hostid'], self::ZABIX[$versionD]['Manufacture'], $versionD);
            }

            /* старый код
            $this->auth = $this->z_auth();
            $z_host     = $this->z_get_hosts(array(
                'host' => 'promobotv4_' . $num
            ));
            $this->z_remove_group($z_host[0]['hostid'], '31');
            $this->z_remove_group($z_host[0]['hostid'], '32');
            */

            $icon         = '🚚';
            $comment      = " Робот  #" . $number . "(" . $robot_name . ") отправлен";
            $telegram_str = $icon . $comment;
            $this->telegram->sendNotify("sale", $telegram_str);
        }

        /* при отметке чека 104(Упаковка V4) отправка сообщения в телегу и на почту*/
        /* при отметке чека 313(Упаковка v2) отправка сообщения в телегу и на почту*/
        /* при отметке чека 552(упаковка/наклеить транспортировочные наклейки) отправка сообщения в телегу и на почту*/
        /* при отметке чека 745(упаковка/наклеить транспортировочные наклейки) отправка сообщения в телегу и на почту*/
        /* при отметке чека 668(упаковка/наклеить транспортировочные наклейки) отправка сообщения в телегу и на почту*/
        /* при отметке чека 684(упаковка/наклеить транспортировочные наклейки) отправка сообщения в телегу и на почту*/
        /* при отметке чека 788(упаковка/наклеить транспортировочные наклейки) отправка сообщения в телегу и на почту*/
        /* при отметке чека 805(упаковка/наклеить транспортировочные наклейки) отправка сообщения в телегу и на почту*/
        /* при отметке чека 961(упаковка/наклеить транспортировочные наклейки) отправка сообщения в телегу и на почту*/
        /* при отметке чека 978(упаковка/наклеить транспортировочные наклейки) отправка сообщения в телегу и на почту*/

        if (($id == 104 || $id ==313 || $id ==552 || $id ==745 || $id ==668 || $id ==684 || $id ==788 || $id ==805 || $id ==961 || $id ==978 || $id ==1029 || $id ==547) && $value == 1) {
            //завершаем учет рабочего времени
            $statistics = $this->statistics->get_robot_production_statistics($robot);
            if ($statistics != null && $statistics['date_end'] == null) {
                $this->statistics->stop_robot_production_statistics($robot);
            }
            //теперь остальная логика
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

        /* при снятии чека 131(Проверка после первичного теста) робот списывается */
        /*if ($id == 131 && $value == 1) {
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
        }*/

        //создане лога
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
        $query = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `ticket_id`, `update_user`, `update_date`) VALUES (NULL, $robot, 'PRODUCTION', '$level', '$comment', '0', $user_id, '$date')";
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
        //$progress = $finish_check * 100 / $count_check;
        $progress = floor(round(($finish_check * 100) / $count_check, 2, PHP_ROUND_HALF_DOWN));

        $query    = "UPDATE `robots` SET `progress` = '$progress', `stage` = '$stage', `last_operation` = '$title', `update_user` = '$user_id', `update_date` = '$date' WHERE `robots`.`id` = $robot";
        $result = $this->pdo->query($query);
        if ($result && $kit != 0 && $value == 1 && $remont==0) {
            //списание комплекта
            $this->sklad->set_writeoff_kit($robot_version, $number, $kit, $id, $robot);
            //списание резерва
            $this->sklad->del_reserv($arr_kits[$kit]);
        }
        if ($result && $kit != 0 && $value == 0 && $remont==0) {
            //отмена списания комплекта
            $this->sklad->unset_writeoff_kit($robot_version, $number, $kit, $id, $robot);
            //восстановить резерв
            $this->sklad->add_reserv($arr_kits[$kit]);
        }
    }

    //
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
        $query = "INSERT INTO `robot_log` (`id`, `robot_id`, `level`, `comment`, `ticket_id`, `update_user`, `update_date`) VALUES (NULL, $robot, '$level', '$comment', '0', $user_id, '$date')";
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

    //показывает прогресс по роботу в текущей категории
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
        if ($count_check != 0) {
            $progress = floor(round(($finish_check * 100) / $count_check, 2, PHP_ROUND_HALF_DOWN));
            //$progress = round($finish_check * 100 / $count_check);
            return $progress;
        } else {
            return false;
        }
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

    /* CТАРЫЙ ЗАБИКС */
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
    /**/

    /* НОВЫЙ ЗАБИКС */
    function my_curl_zabbix_new($arr, $version)
    {
        $url            = self::ZABIX[$version]['url'];
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
    function z_auth_new($version)
    {
        $jsonData = array(
            "jsonrpc" => "2.0",
            "method" => "user.login",
            "params" => array(
                "user" => self::ZABIX[$version]['user'],
                "password" => self::ZABIX[$version]['password'],
            ),
            "id" => "1",
            "auth" => "null"
        );
        $auth_arr = $this->my_curl_zabbix_new($jsonData, $version);
        //print_r($auth_arr);
        return $auth_arr['result'];
    }
    function z_get_hosts_new($filter_arr, $version)
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
        $result = $this->my_curl_zabbix_new($jsonData, $version);
        //print_r($result);
        return $result['result'];
    }
    function z_update_hosts_new($host_id, $action, $version)
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
        $result = $this->my_curl_zabbix_new($jsonData, $version);
        //print_r($result);
        //echo 123;
        return $result;
    }
    function z_add_group_new($host_id, $group, $version)
    {
        $jsonData                                  = array(
            "method" => "hostgroup.massadd"
        );
        $jsonData['params']['groups'][]['groupid'] = $group;
        $jsonData['params']['hosts'][]['hostid']   = $host_id;
        $result = $this->my_curl_zabbix_new($jsonData, $version);
        //print_r($result) ;
        return $result;
    }
    function z_remove_group_new($host_id, $group, $version)
    {
        $jsonData                       = array(
            "method" => "hostgroup.massremove"
        );
        $jsonData['params']['groupids'] = $group;
        $jsonData['params']['hostids']  = $host_id;
        $result = $this->my_curl_zabbix_new($jsonData, $version);
        //print_r($result) ;
        return $result;
    }
    /**/

    //взять информацию по исходному чеклисту (ид чеклиста)
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

    //взять информацию по исходному чеклисту опции (ид чеклиста) не будет работать пока не добавить в таблицу чеклисты ид_чеклист_опции
    /*function get_check_on_option($id)
    {
        $query = "SELECT * FROM check_items WHERE id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $check_array[] = $line;
        }

        if (isset($check_array))
            return $check_array['0'];
    }*/

    //взять информацию по всем исходным чеклистам опции (ид_опции)
    function get_checks_on_option($id=0)
    {
        $query = "SELECT * FROM `robot_options_checks` JOIN `pos_category` ON `robot_options_checks`.`check_category` = `pos_category`.`id` WHERE `id_option` = $id ORDER BY `robot_options_checks`.`check_category` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $checks_array[] = $line;
        }

        if (isset($checks_array))
            return $checks_array;
    }

    //взять информацию по всем исходным чеклистам (ид_опции, ид_категории)
    function get_checks_on_option_in_cat($id=0, $cat)
    {
        $where = "WHERE `check_category` = $cat";
        if ($id != 0) {
            $where .= " AND `id_option` = $id";
        }
        $query = "SELECT * FROM `robot_options_checks` $where ORDER BY `check_title` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $checks_array[] = $line;
        }

        if (isset($checks_array))
            return $checks_array;
    }

    //добавления чек-листа к опции
    function add_check_on_option($id_option, $title, $category, $kit)
    {
        $title = trim($title);
        $query = "INSERT INTO `robot_options_checks` (`check_id`, `id_option`, `check_title`, `check_category`, `id_kit`) VALUES (NULL, $id_option, '$title', $category, $kit);";
        $result = $this->pdo->query($query);
        $idd   = $this->pdo->lastInsertId();
        $query = "SELECT robot_options_items.id_row, robot_options_items.id_option, robot_options_items.id_robot FROM `robot_options_items` JOIN `robots` ON robot_options_items.id_robot = robots.id WHERE robot_options_items.id_option = $id_option AND robots.progress != 100";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $robots_array[] = $line;
        }
        if (isset($robots_array)) {
            $arr_kits = $this->plan->get_kits();
            foreach ($robots_array as &$value) {
                $id_robot = $value['id_robot'];
                //echo $id_robot;
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
                         NULL, 
                         '0')";
                $result = $this->pdo->query($query);
                //добавляем резерв
                $this->sklad->add_reserv($arr_kits[$kit]);
            }
        }
        return $result;
    }

    //удаление чеклиста
    function del_check($id, $version)
    {
        $query   = "SELECT * FROM `check_items` WHERE `id` = $id";
        $result = $this->pdo->query($query);
        $kit = $result->fetch()['kit'];

        $query = "DELETE FROM `check_items` WHERE `id` = $id";
        $result = $this->pdo->query($query);

        $query = "
            SELECT * FROM `robots` 
            WHERE `version` = $version 
                AND `progress` != 100
                AND `writeoff` = 0 
                AND `remont` = 0 
                AND `delete` = 0
            ORDER BY `sort` ASC";
        $result = $this->pdo->query($query);
        $robots_array = [];
        while ($line = $result->fetch()) {
            $robots_array[] = $line['id'];
        }

        if ($robots_array != []) {
            if ($kit != 0) {
                $query = "
                    SELECT COUNT(*) FROM `check` 
                    JOIN `robots` ON `check`.`robot` = `robots`.`id` 
                    WHERE `check`.`id_check` = $id
                        AND `check`.`check` = 0 
                        AND `robots`.`progress` != 100
                        AND `robots`.`writeoff` = 0 
                        AND `robots`.`remont` = 0 
                        AND `robots`.`delete` = 0
                ";
                $result = $this->pdo->query($query);
                $count = $result->fetch()['COUNT(*)'];
                $arr_kits = $this->plan->get_kits();
                $del_res = [];
                foreach ($arr_kits[$kit] as $id_pos => $total) {
                    $del_res[$id_pos] = $total * $count;
                }
                $this->sklad->del_reserv($del_res);
            }
            //удаляем чеклисты
            $in_string = implode(',', $robots_array);
            $query    = "DELETE FROM `check` WHERE `id_check` = $id AND `check` = 0 AND `robot` IN ($in_string)";
            $result = $this->pdo->query($query);
        }

        return true;
    }

    //удаление чеклиста в опции
    function del_check_in_option($id)
    {
        $query   = "SELECT * FROM `robot_options_checks` WHERE `check_id` = $id";
        $result = $this->pdo->query($query);
        $options_checks = $result->fetch();
        $kit = $options_checks['id_kit'];
        $id_option = $options_checks['id_option'];
        $check_category = $options_checks['check_category'];
        $check_title = $options_checks['check_title'];

        $query = "DELETE FROM `robot_options_checks` WHERE `check_id` = $id";
        $result = $this->pdo->query($query);

        $query = "
            SELECT * FROM `robots` 
            WHERE `progress` != 100
                AND `writeoff` = 0 
                AND `remont` = 0 
                AND `delete` = 0
            ORDER BY `sort` ASC";
        $result = $this->pdo->query($query);
        $robots_array = [];
        while ($line = $result->fetch()) {
            $robots_array[] = $line['id'];
        }

        if ($robots_array != []) {
            if ($kit != 0) {
                $query = "
                SELECT COUNT(*) FROM `check` 
                JOIN `robots` ON `check`.`robot` = `robots`.`id` 
                WHERE `check`.`check` = 0 
                    AND `robots`.`writeoff` = 0 
                    AND `robots`.`remont` = 0 
                    AND `robots`.`delete` = 0
                    AND `check`.`option` = '$id_option' 
                    AND `check`.`category` = '$check_category' 
                    AND `check`.`operation` LIKE '$check_title'
                ";
                $result = $this->pdo->query($query);
                $count = $result->fetch()['COUNT(*)'];
                $arr_kits = $this->plan->get_kits();
                $del_res = [];
                foreach ($arr_kits[$kit] as $id_pos => $total) {
                    $del_res[$id_pos] = $total * $count;
                }
                $this->sklad->del_reserv($del_res);
            }
            //удаляем чеклисты
            $in_string = implode(',', $robots_array);
            $query = "
                DELETE FROM `check` 
                WHERE `option` = '$id_option' 
                  AND `check` = 0
                  AND `category` = '$check_category' 
                  AND `operation` LIKE '$check_title'                  
                  AND `robot` IN ($in_string)
            ";
            $result = $this->pdo->query($query);
        }

        return true;
    }

    //создание чеклистов для опций (ид_опции, ид_робота, добавление/удаление)
    public function add_option_check($id, $robot, $value)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        //выборка из бд чеклисты по ид опции
        $query = "SELECT robot_options_checks.check_id, robot_options_checks.id_option, robot_options_checks.check_title, robot_options_checks.check_category, robot_options_checks.id_kit, robot_options.id_option, robot_options.version, robot_options.title    FROM `robot_options_checks` JOIN robot_options ON robot_options_checks.id_option = robot_options.id_option WHERE robot_options_checks.id_option = $id";
        $result = $this->pdo->query($query);
        $arr_kits = $this->plan->get_kits();
        //проходимся по чеклистам
        while ($line = $result->fetch()) {
            //print_r($line);
            $operation = $line['check_title']; //заголовок чеклиста
            $kit = $line['id_kit']; //ид кита
            $category = $line['check_category']; //категория чеклиста
            $option = $line['id_option']; //ид опции, вообще не должно отличаться
            $title = $line['title']; //заголовок опции

            //если опция добавляется, то вставляем запись в бд
            if ($value == 1) {
                $query2 = "INSERT INTO `check` (`id_check`, `robot`, `operation`, `category`, `group`, `check`, `comment`, `sort`, `option`, `id_kit`, `update_user`) VALUES ('0', $robot, '$operation', $category, '0', '0', '', '0', $option, $kit, '0')";
                $result2 = $this->pdo->query($query2);
                //если запись прошла - логируем
                if ($result2) {
                    $comment = "Добавлена опция " . $title;
                    $query_log = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `ticket_id`, `update_user`, `update_date`) VALUES (NULL, $robot, 'PRODUCTION', 'INFO', '$comment', '0', $user_id, '$date ')";
                    $result_log = $this->pdo->query($query_log);
                }
                //добавляем резерв
                if ($kit != 0) {
                    $this->sklad->add_reserv($arr_kits[$kit]);
                }
            //если опция удаляется, то удоляем все чеклисты по опции
            } else {
                $query3 = "SELECT COUNT(0) AS ROW_COUNT  FROM `check` WHERE operation = '$operation' AND robot = $robot AND category = $category AND `check`=0";
                $result3 = $this->pdo->query($query3);
                $rows = $result3->fetchAll(PDO::FETCH_ASSOC);
                $num_rows = count($rows);

                if ($num_rows > 0) {
                    $query2 = "DELETE FROM `check` WHERE operation = '$operation' AND robot = $robot AND category = $category";
                    $result2 = $this->pdo->query($query2);
                    //если удаление прошло - логируем
                    if ($result2) {
                        $comment = "Удалена опция " . $title;
                        $query_log = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `ticket_id`, `update_user`, `update_date`) VALUES (NULL, $robot, 'PRODUCTION', 'INFO', '$comment', '0', $user_id, '$date ')";
                        $result_log = $this->pdo->query($query_log);
                    }
                }
                //удаляем резерв
                if ($kit != 0) {
                    $this->sklad->del_reserv($arr_kits[$kit]);
                }
            }
            //$result2 = mysql_query($query2) or die('Запрос не удался: ' . mysql_error());
        }
    }

    //создание чеклистов для робота (ид_подверсии, ид_категории, ид_робота)
    public function add_robot_check($subversion, $category, $robot)
    {
        //выборка из бд чеклисты по ид_категории и ид_версии
        $query = "SELECT * FROM `check_items` WHERE `category`=$category AND `subversion`=$subversion ORDER BY `sort` ASC";
        $result = $this->pdo->query($query);
        $arr = [];
        while ($line = $result->fetch()) {
            $arr[] = $line;
        }
        $arr_kits = $this->plan->get_kits();
        //добавляем в базу чеклисты
        foreach ($arr as & $value) {
            $operation = $value['title']; //заголовок чеклиста
            $group = $value['group']; //группа
            $sort = $value['sort']; //сортировка - порядковый номер
            $id_check = $value['id']; //ид базового чеклиста
            $id_kit = $value['kit']; //ид кита
            $this->query = "INSERT 
                INTO `check` (`id`, `id_check`, `robot`, `operation`, `category`, `group`, `check`, `sort`, `id_kit`, `update_user` ) 
                VALUES (NULL, '$id_check', '$robot', '$operation', '$category', '$group', '0', '$sort', '$id_kit', '0')
            ";
            $result = $this->pdo->query($this->query);
            //создаем резерв
            if ($id_kit != 0) {
                $this->sklad->add_reserv($arr_kits[$id_kit]);
            }
        }
    }

    //отмечает все чек листы для робота без списаний (ид_робота)
    function checked_all_check_in_robot($id)
    {
        //удаляем все резервы
        $query = "SELECT * FROM `check` WHERE `robot` = $id";
        $result = $this->pdo->query($query);
        $arr = [];
        while ($line = $result->fetch()) {
            $arr[] = $line;
        }
        $arr_kits = $this->plan->get_kits();
        foreach ($arr as $value) {
            if ($value['id_kit'] != 0) {
                $this->sklad->del_reserv($arr_kits[$value['id_kit']]);
            }
        }

        //отмечаем все чеки
        $query = "UPDATE `check` SET `check` = 1 WHERE `robot` = $id";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    /** ДЛЯ ВЫБОРА ПОДВЕРСИИ **/
    //
    function get_difference_pos_by_version ($version, $count) {
        $subversions = $this->get_pos_by_version($version);
        reset($subversions);
        $general = current($subversions);
        foreach ($subversions as $subversion) {
            $general = array_intersect_key($general, $subversion);
        }
        foreach ($general as $id => $vol) {
            $general[$id] = 0;
        }
        $result['general'] = $general;
        foreach ($subversions as $id => $subversion) {
            $result['private'][$id] = array_diff_key($subversion, $general);
        }
        $pos_arr = $this->sklad->get_pos_all();
        foreach ($result['private'] as $id => $pos) {
            foreach ($pos as $pos_id => $pos_count) {
                unset($result['private'][$id][$pos_id]);
                $result['private'][$id][$pos_id]['need'] = $pos_count * $count;
                $result['private'][$id][$pos_id]['category'] = $pos_arr[$pos_id]['category'];
                $result['private'][$id][$pos_id]['subcategory'] = $pos_arr[$pos_id]['subcategory'];
                $result['private'][$id][$pos_id]['vendor_code'] = $pos_arr[$pos_id]['vendor_code'];
                $result['private'][$id][$pos_id]['title'] = $pos_arr[$pos_id]['title'];
                $result['private'][$id][$pos_id]['total'] = $pos_arr[$pos_id]['total'];
                $result['private'][$id][$pos_id]['reserv'] = $pos_arr[$pos_id]['reserv'];
            }
        }
        unset($pos_arr);
        return $result;
    }
    //собрать общее по версии
    function get_pos_by_version($version) {
        $query = "SELECT * FROM `robot_subversion` WHERE `id_version` = $version";
        $result = $this->pdo->query($query);
        $arr = [];
        while ($line = $result->fetch()) {
            $arr[$line['id']] = $this->get_pos_by_subversion($line['id']);
        }

        return $arr;
    }
    //собрать частное по версии
    function get_pos_by_subversion($subversion) {
        $arr_kit_items = $this->plan->get_kits();
        $query = "SELECT * FROM `check_items` WHERE `subversion` = $subversion AND `kit` != 0";
        $result = $this->pdo->query($query);
        $arr_pos = [];
        while ($line = $result->fetch()) {
            foreach ($arr_kit_items[$line['kit']] as $id => $pos) {
                if (isset($arr_pos[$id])) {
                    $arr_pos[$id] = $arr_pos[$id] + $pos;
                } else {
                    $arr_pos[$id] = $pos;
                }
            }
        }

        return $arr_pos;
    }
    //
    function get_mass($sub) {
        $arr_kit_items = $this->plan->get_kits();
        return true;
    }
    /** КОНЕЦ ДЛЯ ВЫБОРА ПОДВЕРСИИ **/

    function __destruct()
    {
        //echo "check - ";
        //print_r($this ->link_check);
        //echo "<br>";
        //mysql_close($this ->link_check);
    }
}
