<?php
class Tickets
{
    const TIME_TEXPOD = [
        'time_start' => '09:00',
        'time_end' => '21:00',
        'days_week' => [1,2,3,4,5,6,7],
    ];

    const TIME_TEXPOD_NEW = [
        1 => ['time_start' => '09:00', 'time_end' => '21:00'],
        2 => ['time_start' => '09:00', 'time_end' => '21:00'],
        3 => ['time_start' => '09:00', 'time_end' => '21:00'],
        4 => ['time_start' => '09:00', 'time_end' => '21:00'],
        5 => ['time_start' => '09:00', 'time_end' => '21:00'],
        6 => ['time_start' => '12:00', 'time_end' => '21:00'],
        7 => ['time_start' => '12:00', 'time_end' => '21:00'],
    ];

    const CLASS_TICKET = [
        "I" => "Консультация",
        "P" => "Проблема",
        "FR" => "Пожелание",
    ];
    const SOURCE_TICKET = [
        "0" => "Неизвестно",
        //11-19 - системные источники (автомат)
        "11" => "Система",
        "12" => "Zabbix",
        "13" => "Кабинет клиента",
        //21-29 - выбор источника (ручной)
        "21" => "Телефон",
        "22" => "Telegram",
        "23" => "Email",
        "24" => "Техпод Zabbix",
        "25" => "Сотрудник PromoBot",
    ];
    const PRIORITY_TICKET = [
        "1" => "Низкий",
        "2" => "Средний",
        "3" => "Высокий",
    ];

    private $user;
    private $robot;
    private $statistics;
    private $link_ticket;
    private $query;
    private $pdo;

    public $listClassTikets;
    public $listSourceTikets;
    public $listPriorityTikets;
    public $listStatusTikets;


    /**
     * @var string
     */


    public function __construct()
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
        global $user, $robots, $statistics;

        //Подключение внешних классов
        $this->robot = $robots; //new Robots;
        $this->user = $user; //new User;
        $this->statistics = $statistics;

        $this->listClassTikets = self::CLASS_TICKET;
        $this->listSourceTikets = self::SOURCE_TICKET;
        $this->listPriorityTikets = self::PRIORITY_TICKET;
        $this->listStatusTikets = $this->get_status();

    }

    //Получение списка возможных статусов тикетов
    /**
     * @param int $id
     * @return array
     */
    public function get_status($id = 999)
    {
        $where = "";
        if ($id == 0) {
            $where = 'WHERE `id` NOT IN (6,8)';
        }
        $this->query = "SELECT * FROM tickets_status $where ORDER BY `sort` ASC ";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $ticket_status_array[$line['id']] = $line;
        }
        
        if (isset($ticket_status_array))
            return $ticket_status_array;
    }

    //Получение списка статусов на которые можно сменить
    /**
     * @param int $id
     * @return array
     */
    public function get_status_list_change($my_statys = null)
    {
        $where = "";
        if ($my_statys == 3) {
            $where = 'WHERE `id` IN (3,6)';
        }
        if ($my_statys == 8) {
            $where = 'WHERE `id` NOT IN (8,6)';
        }
        if ($my_statys == 6) {
            $where = 'WHERE `id` = 6';
        }

        $query = "SELECT * FROM tickets_status $where ORDER BY `sort` ASC ";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $ticket_status_array[$line['id']] = $line;
        }

        if (isset($ticket_status_array))
            return $ticket_status_array;
    }

    //Получение списка категорий тикетов, в качестве параметров можно указать тип $type (P - проблема (по умолчанию), I - консультация, FR - пожелание)

    /**
     * @param string $type
     * @return array
     */
    public function get_category($type = "")
    {
        $where = ($type == "") ? "" : "WHERE `class` = '$type'";
        $query = "SELECT * FROM `tickets_category` ".$where." ORDER BY `title` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $ticket_category_array[$line['id']] = $line;
        }

        if (isset($ticket_category_array))
            return $ticket_category_array;
    }
    
    //Получение списка подкатегорий тикетов

    /**
     * @param $category
     * @return array
     */
    public function get_subcategory($category)
    {
        if ($category != 0) {
            $where = "WHERE `category` =  $category";
        } else {
            $where = "";
        }
        $this->query  = "SELECT * FROM `tickets_subcategory` $where ORDER BY `title` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $ticket_subcategory_array[$line['id']] = $line;
        }

        if (isset($ticket_subcategory_array))
            return $ticket_subcategory_array;
    }
    
    //Добавление нового тикета
    // $robot - id робота
    // $class - класс тикета
    // $category - категория тикета
    // $subcategory - подкатегория тикета
    // $status - статус тикета
    // $comment - описание проблемы
    /**
     * @param $robot
     * @param $class
     * @param $category
     * @param $subcategory
     * @param $status
     * @param $comment
     * @return false|PDOStatement
     */
    function add($robot, $source, $priority, $class, $category, $subcategory, $status, $comment, $user_id = 0)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = ($user_id == 0) ? intval($_COOKIE['id']) : $user_id;
        $user_info = $this->user->get_info_user($user_id);
        if ($user_info['group'] == 4) {
            $assign = $user_id;
            $assign_time = "'$date'";
        } else {
            $auto_assign = $this->auto_assign_user();
            $assign = ($auto_assign) ? $auto_assign : 0;
            $assign_time = ($assign) ? "'$date'" : "NULL";
        }
        $this->query   = "INSERT INTO `tickets` (
            `id`, 
            `robot`, 
            `source`,
            `priority`,
            `class`,                        
            `category`, 
            `subcategory`, 
            `description`, 
            `status`,
            `assign`,
            `assign_time`,                        
            `user_create`,
            `date_create`,
            `update_user`, 
            `update_date`) 
            VALUES (
                NULL, 
                '$robot',
                '$source',
                '$priority',                                
                '$class', 
                '$category', 
                '$subcategory', 
                '$comment', 
                '$status',
                '$assign',
                 $assign_time,                                
                '$user_id',
                '$date',
                '$user_id', 
                '$date');";
        $result = $this->pdo->query($this->query);
        $idd   = $this->pdo->lastInsertId();

        $this->query  = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `ticket_id`,`update_user`, `update_date`) VALUES (NULL, $robot, 'TICKET', 1, '$comment', $idd,  $user_id, '$date')";
        $result = $this->pdo->query($this->query);

        return $result;
    }
    
    //Получение информации о тикете по ID

    /**
     * @param $id
     * @return mixed
     */
    public function info($id)
    {
        $this->query = "SELECT * FROM tickets WHERE id='$id'";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $ticket_array[] = $line;
        }

        if (isset($ticket_array))
            return $ticket_array['0'];
    }
    
    //Получение списка комментариев тикета, в качестве параметра ID тикета

    /**
     * @param $id
     * @return array
     */
    public function get_comments($id)
    {
        $this->query = "SELECT * FROM tickets_comments WHERE `ticket` =  $id ORDER BY `update_date` DESC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $ticket_comments[] = $line;
        }
        return (isset($ticket_comments)) ? $ticket_comments : [];
    }

    /**
     * @param $id
     * @return array
     */
    public function get_comments_customers($id)
    {
        $this->query = "SELECT * FROM tickets_comments_customers WHERE `ticket` =  $id ORDER BY `update_date` DESC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $ticket_comments[] = $line;
        }
        return (isset($ticket_comments)) ? $ticket_comments : [];
    }
    //Получение информации о категории тикета по ее ID
    public function get_info_category($id)
    {
        $this->query = "SELECT * FROM tickets_category WHERE id='$id'";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $category_array[] = $line;
        }

        if (isset($category_array))
            return $category_array['0'];
    }
    
    //Получение информации о подкатегории тикета по ее ID
    public function get_info_subcategory($id)
    {
        $this->query = "SELECT * FROM tickets_subcategory WHERE id='$id'";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $subcategory_array[] = $line;
        }

        if (isset($subcategory_array))
            return $subcategory_array['0'];
    }
    
    //Получение информации о статусе тикета по его ID
    public function get_info_status($id)
    {
        $this->query = "SELECT * FROM tickets_status WHERE id='$id'";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $status_array[] = $line;
        }

        if (isset($status_array))
            return $status_array['0'];
    }
    
    //Добавление комментария к тикету для техпод
    // $robot - id робота
    // $ticket - id тикета
    // $comment - комментарий
    function add_comment($robot, $ticket, $comment)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "INSERT INTO `tickets_comments` (
            `id`, 
            `ticket`, 
            `comment`, 
            `update_user`, 
            `update_date`) VALUES (
                NULL, 
                $ticket, 
                '$comment', 
                $user_id, 
                '$date');";
        $result = $this->pdo->query($this->query);
        $idd   = $this->pdo->lastInsertId();
        $this->query = "UPDATE `tickets` SET  `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $ticket";
        $result = $this->pdo->query($this->query);
        //echo $query;
        return $result;
    }

    //Добавление комментария к тикету для клиента
    // $robot - id робота
    // $ticket - id тикета
    // $comment - комментарий
    function add_comment_customers($robot, $ticket, $comment)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "INSERT INTO `tickets_comments_customers` (
            `id`, 
            `ticket`, 
            `comment`, 
            `update_user`, 
            `update_date`) VALUES (
                NULL, 
                $ticket, 
                '$comment', 
                $user_id, 
                '$date');";
        $result = $this->pdo->query($this->query);
        return $result;
    }

    // Получение списка тикетов
    // $robot - id робота 
    // $user - пользовтель (создатель)
    // $status - статус тикета
    // $sortBy - сортировка (имя столбца таблицы MYSQL)
    // $sortDir - направление сортировки (ASC, DESC)
    // $sourceDate - источник даты (date_create, assign_time, inwork, finish_date, update_date)
    // $date_min и $date_max - диапазон дат
    // $class - класс тикета
    // $problem - только закрытые тикеты (1 или 0)
    // $connect - только активные тикеты (1 или 0)
    
    public function get_tickets($robot = 0, $user = NULL, $status = 0, $sortBy = "update_date", $sortDir = "DESC", $sourceDate = 0, $date_min = 0, $date_max = 0, $class = "", $problem = 0, $connect = 0)
    {
        $where = '';
        if (isset($_GET['user']) && $_GET['user'] !== NULL) {
            $user = $_GET['user'];
            $where .= " AND `assign` = $user ";
        }
        if (isset($_GET['robot']) && $_GET['robot'] != 0) {
            $robot = $_GET['robot'];
            $where .= " AND `robot` = $robot ";
        }
        if ($robot != 0) {
            $where .= " AND `robot` = $robot ";
        }
        if ($problem != 0) {
            $where .= " AND `status` = 3 AND  `status` = 6 ";
        }
        if ($connect != 0) {
            $where .= " AND `status` != 3 AND  `status` != 6 ";
        }
        if ($date_min != 0 && $date_max != 0) {
            $where .= " AND `$sourceDate` >= '$date_min' AND `$sourceDate` <= '$date_max' ";
        }
        if ($status != 0) {
            $where .= " AND `status` = $status ";
        }
        if ($class != "") {
            $where .= " AND `class` = '$class' ";
        }
        $this->query = "SELECT * FROM tickets WHERE `id` > 0 $where ORDER BY `$sortBy` $sortDir";
        //echo $query."<br>";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $ticket_array[] = $line;
        }

        if (isset($ticket_array))
            return $ticket_array;
    }
    
    
    //
    public function get_tickets_kanban($robot = 0, $user = 0, $status = 0, $sortBy = "update_date", $sortDir = "DESC")
    {
        $where = '';
        if (isset($_GET['user']) && $_GET['user'] != 0) {
            $user = $_GET['user'];
            $where .= " AND `assign` = $user ";
        }
        if (isset($_GET['robot']) && $_GET['robot'] != 0) {
            $robot = $_GET['robot'];
            $where .= "AND `robot` = $robot ";
        }
        if ($status != 0) {
            $where .= "AND `status` = $status ";
        }
        $this->query = "SELECT * FROM tickets WHERE `id` > 0 $where ORDER BY `$sortBy` $sortDir";
        //echo $query_kanban;
        $result = $this->pdo->query($this->query);
        $i = 0;
        while ($line_kanban = $result->fetch()) {
            $i++;
            $ticket_array[$i]['id']          = $line_kanban['id'];
            $ticket_array[$i]['status']      = $line_kanban['status'];
            // $ticket_array[]['robot_version'] = $line['id'];
            $info_robot                      = $this->robot->get_info_robot($line_kanban['robot']);
            $ticket_array[$i]['robot']       = $info_robot['version'] . "." . $info_robot['number'];
            $ticket_array[$i]['class']       = $line_kanban['class'];
            $info_category                   = $this->get_info_category($line_kanban['category']);
            $ticket_array[$i]['category']    = $info_category['title'];
            $info_subcategory                = $this->get_info_subcategory($line_kanban['subcategory']);
            $ticket_array[$i]['subcategory'] = $info_subcategory['title'];
            $ticket_array[$i]['description'] = $line_kanban['description'];
            $lng                             = mb_strlen($ticket_array[$i]['description'], 'UTF-8');
            if ($lng > 100) {
                $ticket_array[$i]['description'] = mb_substr($ticket_array[$i]['description'], 0, 100) . "...";
            }
            $date_create                     = new DateTime($line_kanban['update_date']);
            $ticket_array[$i]['update_date'] = $date_create->format('d.m.y H:i');
            $user_info                       = $this->user->get_info_user($line_kanban['assign']);
            $ticket_array[$i]['assign']      = $user_info['user_name'];
            // if ($line_kanban['assign']==0) {$ticket_array[$i]['assign']  = "<span class='text-red'>".$user_info['user_name']."</span>";}
            $ticket_commets                  = $this->get_comments($line_kanban['id']);
            $ticket_array[$i]['comments']    = count($ticket_commets);
            $ticket_commets_customers        = $this->get_comments_customers($line_kanban['id']);
            $ticket_array[$i]['comments_customers'] = count($ticket_commets_customers);
            if ($line_kanban['finish_date'] != '0000-00-00' && $line_kanban['finish_date'] != null) {
                $date_finish = new DateTime($line_kanban['finish_date']);
                $ticket_array[$i]['str_finish_date'] = 'Ремонт назначен на <b>' . $date_finish->format('d.m.Y') . '</b><br><br>';
            } else {
                $ticket_array[$i]['str_finish_date'] = "";
            }
        }

        if (isset($ticket_array))
            return $ticket_array;
    }
    
    //Изменение статуса тикета
    //$id - id тикета
    //$status - id статуса
    public function ticket_change_status($id, $status)
    {
        //проверка
        $query = "SELECT * FROM `tickets` WHERE `id` = $id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $tickets[] = $line;
        }
        $old_status  = $tickets[0]['status'];
        //из решено/не решено только в архив
        if (($old_status == 3 || $old_status == 8) && $status != 6) {
            return false;
        }
        //из архива никуда
        if ($old_status == 6) {
            return false;
        }

        //добавить метку смены статуса
        $this->set_time_change_status($id, $status);

        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        if ($status == 3) {
            $query = "UPDATE `tickets` SET `status` = '$status', `inwork` = '$date', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        } else {
            $query = "UPDATE `tickets` SET `status` = '$status', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        }
        $result = $this->pdo->query($query);

        /*$query = "SELECT * FROM `tickets_status` WHERE `id` = $status";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $status_array[] = $line;
        }
        if (isset($status_array)) {
            $status_str = $status_array[0]['title'];
            $color      = $status_array[0]['color'];
        }*/
        /*$query = "SELECT * FROM `tickets` WHERE `id` = $id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $tickets_array[] = $line;
        }*/

        $robot   = $tickets[0]['robot'];
        $idd     = $tickets[0]['id'];
        $comment = $tickets[0]['description'];
        //$new_status  = $status;
        $query = "INSERT INTO `robot_log` (`id`, `robot_id`, `source`, `level`, `comment`, `ticket_id`,`update_user`, `update_date`) VALUES (NULL, $robot,  'TICKET', $status, '$comment', $idd,  $user_id, '$date')";
        $result = $this->pdo->query($query);

        return $result;
    }
    
    
    //Добавление результата тикета
    //$id - id тикета
    //$result - текст резултата
    public function ticket_add_result($id, $result)
    {
        //добавить метку смены статуса
        $this->set_time_change_status($id, 3);

        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "UPDATE `tickets` SET `status` = '3', `inwork` = '$date',`result_description` = '$result', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        // $query = "UPDATE `tickets` SET `status` = '$status', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);

        /*$this->query = "SELECT * FROM `tickets_status` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $status_array[] = $line;
        }
        if (isset($status_array)) {
            $status_str = $status_array[0]['title'];
            $color      = $status_array[0]['color'];
        }*/

        $this->query = "SELECT * FROM `tickets` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $tickets_array[] = $line;
        }
        $robot   = $tickets_array[0]['robot'];
        $idd     = $tickets_array[0]['id'];
        $comment = $tickets_array[0]['description'];
        $status  = $tickets_array[0]['status'];
        $this->query = "INSERT INTO `robot_log` (`id`, `robot_id`,  `source`, `level`, `comment`, `ticket_id`,`update_user`, `update_date`) VALUES (NULL, $robot,  'TICKET', $status, '$comment', $idd,  $user_id, '$date')";
        $result = $this->pdo->query($this->query);

        return $result;
    }
    
    //Добавление даты закрытия тикета
    //$id - id тикета
    //$date_finish - дата в формате 01.01.2019
    public function ticket_add_date($id, $date_finish)
    {
        //добавить метку смены статуса
        $this->set_time_change_status($id, 4);

        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $newDate = date("Y-m-d", strtotime($date_finish));
        $this->query = "UPDATE `tickets` SET `status` = '4', `finish_date` = '$newDate', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";

        // $query = "UPDATE `tickets` SET `status` = '$status', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);

        /*$this->query = "SELECT * FROM `tickets_status` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $status_array[] = $line;
        }
        if (isset($status_array)) {
            $status_str = $status_array[0]['title'];
            $color      = $status_array[0]['color'];
        }*/

        $this->query = "SELECT * FROM `tickets` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $tickets_array[] = $line;
        }
        $robot   = $tickets_array[0]['robot'];
        $idd     = $tickets_array[0]['id'];
        $comment = $tickets_array[0]['description'];
        $status  = $tickets_array[0]['status'];
        $this->query = "INSERT INTO `robot_log` (`id`, `robot_id`,  `source`, `level`, `comment`, `ticket_id`,`update_user`, `update_date`) VALUES (NULL, $robot,  'TICKET', $status, '$comment', $idd,  $user_id, '$date')";
        $result = $this->pdo->query($this->query);

        return $result;
    }
    
    //Изменение отвественного
    //$id - id тикета
    //$assign - id пользователя ответственного 
    public function change_assign($id, $assign)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "SELECT assign_time FROM `tickets` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        $line        = $result->fetch();
        $assign_time = $line['assign_time'];
        if ($assign_time == "0000-00-00 00:00:00" || $assign_time == null) {
            $assign_time = $date;
        }
        $this->query = "UPDATE `tickets` SET `assign` = '$assign',`assign_time` = '$assign_time', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        return $result;
    }
    //
    public function change_priority($id, $priority)
    {
        $this->query = "UPDATE `tickets` SET `priority` = '$priority' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        return $result;
    }
    //
    public function change_source($id, $source)
    {
        $this->query = "UPDATE `tickets` SET `source` = '$source' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        return $result;
    }

    //Добавление категории тикетов в справочник
    //$title - название
    //$cat_class - класс категории 
    
    function add_category($title, $cat_class)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "INSERT INTO `tickets_category` (`id`, `title`, `class`) VALUES (NULL, '$title', '$cat_class');";
        $result = $this->pdo->query($this->query);
        $idd = $this->link_ticket->insert_id;
        return $idd;
    }
    
    //Добавление подкатегрии тикетов в справочник
    //$id - id категории
    //$title - название
    function add_subcategory($id, $title)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "INSERT INTO `tickets_subcategory` (`id`, `category`, `title`) VALUES (NULL, $id, '$title');";
        $result = $this->pdo->query($this->query);
        $idd = $this->pdo->lastInsertId();;
        return $idd;
    }
    
    //Изменение тикета
    //$id - id тикета
    //$category - id категории тикета
    //$subcategory - id подкатегории тикета
    //$description - result description 
    function edit($id, $category = 0, $subcategory = 0, $description)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "UPDATE `tickets` SET `category` = $category,`subcategory` = $subcategory, `description` = '$description', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        return $result;
    }
    
    //Отправить колонку из канбана в архив
    //$id - колонки (статуса) канбан
    function arhiv($id)
    {
        //добавить метку смены статуса
        $this->query = "SELECT * FROM `tickets` WHERE `status` = $id";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $this->set_time_change_status($line['id'], 6);
        }

        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "UPDATE `tickets` SET `status` = 6, `update_user` = $user_id, `update_date` = '$date' WHERE `status` = $id";
        $result = $this->pdo->query($this->query);
        return $result;
    }
    
    //Логирование статичтики по тикетам в базу
    //$total_robots - количество роботов в базе
    //$open_tickets - количчество открытых тикетов
    //$robot_problem - количество проблемных роботов
    function write_stat($total_robots, $open_tickets, $robot_problem)
    {
        $date  = date("Y-m-d H:i:s");
        $this->query = "INSERT INTO `tickets_stat` (`date`, `problem_robots`, `open_tickets`, `count_robots`) VALUES ('$date', '$robot_problem', '$open_tickets', '$total_robots');";
        $result = $this->pdo->query($this->query);
        return $result;
    }

    //отчет по владельцу ***Нужно переписывать с учетом НОВОГО графика работы и новой функции подсчета рабочего времени
    function get_report_owner($owner_id, $interval) {

        //создаем файлы
        //для папок
        $f_date = date('Y-m-d_H:i:s');
        $folder = $owner_id;
        if (!file_exists(PATCH_DIR."/report/")) {
            mkdir(PATCH_DIR."/report/", 0777);
        }
        $excel_name = PATCH_DIR."/report/".$f_date.".xlsx";
        require_once ('excel/Classes/PHPExcel.php');
        require_once ('excel/Classes/PHPExcel/IOFactory.php');
        $objPHPExcel = new PHPExcel();
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        //задаем заголовки
        $objPHPExcel->getActiveSheet()->setCellValue("A1", 'ID');
        $objPHPExcel->getActiveSheet()->setCellValue("B1", 'Дата создания');
        $objPHPExcel->getActiveSheet()->setCellValue("C1", 'Дата решения');
        $objPHPExcel->getActiveSheet()->setCellValue("D1", 'Дата ремонта');
        $objPHPExcel->getActiveSheet()->setCellValue("E1", 'Время в работе');
        $objPHPExcel->getActiveSheet()->setCellValue("F1", 'Источник');
        $objPHPExcel->getActiveSheet()->setCellValue("G1", 'Робот');
        $objPHPExcel->getActiveSheet()->setCellValue("H1", 'Статус');
        $objPHPExcel->getActiveSheet()->setCellValue("I1", 'Класс');
        $objPHPExcel->getActiveSheet()->setCellValue("J1", 'Категория');
        $objPHPExcel->getActiveSheet()->setCellValue("K1", 'Подкатегория');
        $objPHPExcel->getActiveSheet()->setCellValue("L1", 'Приоритет');
        $objPHPExcel->getActiveSheet()->setCellValue("M1", 'Исполнитель');
        $objPHPExcel->getActiveSheet()->setCellValue("N1", 'Описание');
        $objPHPExcel->getActiveSheet()->setCellValue("O1", 'Решение');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle("A1:O1")->getFont()->setBold(true);

        $where = '';
        if ($interval != '') {
            $date = explode(' - ', $interval);
            $date_start = $date[0] . ' 00:00:00';
            $date_end = $date[1] . ' 23:59:59';
            $where = $where . "AND `t`.`date_create` >= '$date_start' AND `t`.`date_create` <= '$date_end'";
        }
        $query = "SELECT *, `t`.`id` AS `tid`, `t`.`status` AS `tstatus`, `t`.`update_date` AS `tupdate_date` FROM `tickets` t
            JOIN `robots` r ON `t`.`robot` = `r`.`id`
            WHERE `r`.`owner` = '$owner_id'
                AND `t`.`id` > 0 $where ORDER BY `t`.`date_create` DESC
        ";
        $result = $this->pdo->query($query);
        $listCategoryTikets = $this->get_category(0);
        $listSubCategoryTikets = $this->get_subcategory(0);
        $listUsers = $this->user->get_users();
        $schedule =  self::TIME_TEXPOD;
        $time_work_start = explode(':', $schedule['time_start']);
        $time_work_end = explode(':', $schedule['time_end']);
        $work_time = (($time_work_end[0]*60+$time_work_end[1])-($time_work_start[0]*60+$time_work_start[1]))*60;
        $row = 1;
        while ($line = $result->fetch()) {
            $row++;
            $objPHPExcel->getActiveSheet()->setCellValue("A" . $row, $line['tid']);
            $objPHPExcel->getActiveSheet()->setCellValue("B" . $row, $line['date_create']);
            $objPHPExcel->getActiveSheet()->setCellValue("C" . $row, $line['assign_time']);
            $objPHPExcel->getActiveSheet()->setCellValue("D" . $row, $line['finish_date']);
            if ($line['assign_time'] != null) {
                $date_start = strtotime($line['date_create']);
                $date_end = strtotime($line['assign_time']);
                $time = $this->statistics->get_time_spent($date_start, $date_end, self::TIME_TEXPOD);
                $time_d = intval($time/$work_time);
                $time_h = intval(($time - $time_d*$work_time)/3600);
                $time_m = intval(($time - $time_d*$work_time - $time_h*3600)/60);
                $time_inwork = $time_d."д ".$time_h."ч ".$time_m."м";
            } else {
                $time_inwork = "";
            }
            $objPHPExcel->getActiveSheet()->setCellValue("E" . $row, $time_inwork);
            $source = self::SOURCE_TICKET[$line['source']];
            $objPHPExcel->getActiveSheet()->setCellValue("F" . $row, $source);
            $robot_number = str_pad($line['number'], 4, "0", STR_PAD_LEFT);
            $robot = $line['version'] . "_" . $robot_number;
            $objPHPExcel->getActiveSheet()->setCellValue("G" . $row, $robot);
            $status = $this->listStatusTikets[$line['tstatus']]['title'];
            $objPHPExcel->getActiveSheet()->setCellValue("H" . $row, $status);
            $class = self::CLASS_TICKET[$line['class']];
            $objPHPExcel->getActiveSheet()->setCellValue("I" . $row, $class);
            $category = ($line['category'] != 0) ? $listCategoryTikets[$line['category']]['title'] : "";
            $objPHPExcel->getActiveSheet()->setCellValue("J" . $row, $category);
            $subcategory = ($line['subcategory'] != 0) ? $listSubCategoryTikets[$line['subcategory']]['title'] : "";
            $objPHPExcel->getActiveSheet()->setCellValue("K" . $row, $subcategory);
            $priority = self::PRIORITY_TICKET[$line['priority']];
            $objPHPExcel->getActiveSheet()->setCellValue("L" . $row, $priority);
            $user = ($line['assign'] != 0) ? $listUsers[$line['assign']]['user_name'] : "";
            $objPHPExcel->getActiveSheet()->setCellValue("M" . $row, $user);
            $description = ($line['description'] != "") ? $line['description'] : "";
            $objPHPExcel->getActiveSheet()->setCellValue("N" . $row, $description);
            $result_description = ($line['result_description'] != "") ? $line['result_description'] : "";
            $objPHPExcel->getActiveSheet()->setCellValue("O" . $row, $result_description);
        }

        $styleArray = [
            'borders' => [
                'outline' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
                'inside' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $objPHPExcel->getActiveSheet()->getStyle("A1:O".$row)->applyFromArray($styleArray);


        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($excel_name);

        return $excel_name;
    }

    //херня какая то непомню зачем
    function get_tickets_live()
    {
        $this->query = "SELECT * from ticket_log WHERE id_row=( SELECT max(id_row) FROM ticket_log )";
        $result = $this->pdo->query($this->query);
        $line = $result->fetch();
        return $line['id_row'];
        //echo "11";
    }

    //запись смены статуса
    function set_time_change_status($id_ticket, $id_new_status) {
        date_default_timezone_set('Asia/Yekaterinburg');
        $query = "SELECT * FROM `tickets_statistics` WHERE `id_ticket` = $id_ticket ORDER BY `date_change` DESC LIMIT 1";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $tickets_statistics[] = $line;
        }
        if (isset($tickets_statistics)) {
            $date_start = $tickets_statistics[0]['date_change'];
            $id_old_status = $tickets_statistics[0]['new_status'];
        } else {
            $query = "SELECT * FROM `tickets` WHERE id = $id_ticket LIMIT 1";
            $result = $this->pdo->query($query);
            $line = $result->fetch();
            $date_start = strtotime($line['date_create']);
            $id_old_status = $line['status'];
        }
        $date_end = time();
        $in_work = $this->statistics->get_new_time_spent($date_start, $date_end, self::TIME_TEXPOD_NEW, 0);
        $in_time = $date_end - $date_start;

        $this->query = "INSERT INTO `tickets_statistics` (`id`, `id_ticket`, `old_status`, `new_status`, `date_change`, `in_work`, `in_time`) VALUES (NULL, '$id_ticket', '$id_old_status', '$id_new_status', '$date_end', '$in_work', '$in_time')";
        $result = $this->pdo->query($this->query);
        return true;
    }

    //назначенные тикеты
    function get_assign_tickets() {
        $date_now = date('Y-m-d');
        $query = "SELECT * from `tickets` WHERE `status` IN (1,2,4,5,7,9) AND `assign` != 0  ORDER BY `id` ASC";
        $result = $this->pdo->query($query);
        $assign = [];
        while ($line = $result->fetch()) {
            if ($line['finish_date'] == null || $line['finish_date'] <= $date_now) {
                if (array_key_exists($line['assign'], $assign)) {
                    $assign[$line['assign']]['count'] = $assign[$line['assign']]['count'] + 1;
                } else {
                    $assign[$line['assign']]['count'] = 1;
                }
                $assign[$line['assign']]['tickets'][$line['id']] = $line;
            }
        }
        $arr_user = $this->user->get_users(4);
        foreach ($arr_user as $user_id => $user) {
            if (!array_key_exists($user_id, $assign)) {
                $assign[$user_id]['count'] = 0;
                $assign[$user_id]['tickets'] = [];
            }
        }

        return $assign;
    }

    //вкл/выкл автораспределения тикетов у пользователя
    function change_auto_assign_for_user($user_id) {
        $old_status = $this->user->get_info_user($user_id)['auto_assign_ticket'];
        $status = ($old_status == 1) ? 0 : 1;
        $query = $query = "UPDATE `users` SET `auto_assign_ticket` = $status WHERE `user_id` = $user_id;";
        $result = $this->pdo->query($query);
        return true;
    }

    //автоназночение исполнителя
    function auto_assign_user() {
        $tickets = $this->get_assign_tickets();
        $users = $this->user->get_users(4);
        //проверка если не из кого выбирать
        if ($users == []) {
            return false;
        }
        //пользователи кому можно назначать
        $users_on = [];
        foreach ($users as $user_id => $user) {
            if ($user['auto_assign_ticket'] == 1) {
                if (array_key_exists($user_id, $tickets)) {
                    $users_on[$user_id] = $tickets[$user_id]['count'];
                } else {
                    $users_on[$user_id] = 0;
                }
            }
        }
        //проверка если некому назначать
        if ($users_on == []) {
            return false;
        }
        //логика кому назначить
        $user_assign = array_keys($users_on, min($users_on))[0];

        return $user_assign;
    }

    //собираем статистику по тикетам решенным
    function get_resolved_ticket_status_time($date_start = null, $date_end = null, $robot_name = null, $robot_number = null, $robot_version = null)
    {
        date_default_timezone_set('Asia/Yekaterinburg');
        $date_start = ($date_start != null) ? $date_start : date('Y-m-d');
        $date_start .= ' 00:00:00';
        $date_end = ($date_end != null) ? $date_end : date('Y-m-d');
        $date_end .= ' 23:59:59';
        //если дата начала больше даты конца
        if ($date_start > $date_end) {
            return false;
        }
        $date_start_str = strtotime($date_start);
        $date_end_str = strtotime($date_end);
        //собираем решнные тикеты
        $query = "
            SELECT * FROM `tickets_statistics`
            JOIN `tickets` ON `tickets_statistics`.`id_ticket` = `tickets`.`id`
                WHERE `tickets_statistics`.`date_change` >= $date_start_str AND `tickets_statistics`.`date_change` <= $date_end_str
                AND `tickets_statistics`.`new_status` = 3
            ORDER BY `tickets`.`date_create` ASC
        ";
        $result = $this->pdo->query($query);
        $min_date = null;
        $max_date = '2000-01-01 00:00:00';
        $tikets = [];
        while ($line = $result->fetch()) {
            //задаем мин дату поиска для лога
            if ($min_date == null) {
                $min_date = $line['date_create'];
            }
            //задаем макс дату для поиска
            if ($max_date < $line['update_date']) {
                $max_date = $line['update_date'];
            }
            $tikets[$line['id_ticket']] = $line;
        }
        //если нет решенных тикетов
        if ($tikets == []) {
            return false;
        }
        $min_date_str = strtotime($min_date);
        $max_date_str = strtotime($max_date);
        //собираем полный лог смены статусов
        $query = "SELECT * FROM `tickets_statistics` WHERE `date_change` >= $min_date_str AND `date_change` <= $max_date_str ORDER BY `date_change` ASC";
        $result = $this->pdo->query($query);
        $log_status = [];
        while ($line = $result->fetch()) {
            if (!array_key_exists($line['id_ticket'], $log_status)) {
                $log_status[$line['id_ticket']] = [
                    'in_work' => [
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                        6 => 0,
                        7 => 0,
                        8 => 0,
                    ],
                    'before_status' => 1,
                ];
            }
            $log_status[$line['id_ticket']]['in_work'][$line['old_status']] += intval($line['in_work']/60);
            $log_status[$line['id_ticket']]['before_status'] = $line['old_status'];
        }
        //собираем роботов по условиям
        $and = "";
        if ($robot_name != null) {
            $and .= " AND `name` LIKE '%$robot_name%'";
        }
        if ($robot_number != null) {
            $and .= " AND `number` = $robot_number";
        }
        if ($robot_version != null) {
            $and .= " AND `version` = $robot_version";
        }
        $query = "SELECT * FROM `robots` WHERE `id` > 0".$and;
        $result = $this->pdo->query($query);
        $robots = [];
        while ($line = $result->fetch()) {
            $robots[$line['id']] = $line;
        }
        //если нет роботов удовлетворяющих условиям
        if ($robots == []) {
            return false;
        }
        //обработка данных
        $listCategoryTikets = $this->get_category(0);
        $listSubCategoryTikets = $this->get_subcategory(0);
        $listUsers = $this->user->get_users();
        $time_status_1 = 0;
        $time_status_2 = 0;
        $time_status_4 = 0;
        $time_status_5 = 0;
        $time_status_7 = 0;
        $time_resolved = 0;
        $time_count = 0;
        $arr = [];
        foreach ($tikets as $id => $info) {
            if (array_key_exists($info['robot'], $robots) && array_key_exists($id, $log_status)) {
                $arr[$id]['id'] = $id; // ID
                $arr[$id]['date_create'] = $info['date_create']; //дата создания
                $arr[$id]['date_resolved'] = date('Y-m-d H:i:s', $info['date_change']); //дата решения
                $arr[$id]['date_repair'] = $info['finish_date']; //дата ремонта
                $arr[$id]['source'] = self::SOURCE_TICKET[$info['source']]; //источник
                $robot_number = str_pad($robots[$info['robot']]['number'], 4, "0", STR_PAD_LEFT);
                $arr[$id]['robot'] = $robots[$info['robot']]['version'] . "." . $robot_number; //робот
                $arr[$id]['time_in_status'] = [
                    1 => $log_status[$id]['in_work'][1],
                    2 => $log_status[$id]['in_work'][2],
                    3 => $log_status[$id]['in_work'][3],
                    4 => $log_status[$id]['in_work'][4],
                    5 => $log_status[$id]['in_work'][5],
                    6 => $log_status[$id]['in_work'][6],
                    7 => $log_status[$id]['in_work'][7],
                    8 => $log_status[$id]['in_work'][8],
                ]; //время в статусе только рабочее (мин)
                $arr[$id]['time_resolved'] = $log_status[$id]['in_work'][1] + $log_status[$id]['in_work'][2] + $log_status[$id]['in_work'][4] + $log_status[$id]['in_work'][5]; //время решения
                $arr[$id]['before_status'] = $this->listStatusTikets[$log_status[$id]['before_status']]['title']; //преведущий статус
                $arr[$id]['status'] = $this->listStatusTikets[$info['status']]['title']; //статус
                $arr[$id]['class'] = self::CLASS_TICKET[$info['class']]; //класс
                $arr[$id]['category'] = ($info['category'] != 0) ? $listCategoryTikets[$info['category']]['title'] : "";
                $arr[$id]['subcategory'] = ($info['subcategory'] != 0) ? $listSubCategoryTikets[$info['subcategory']]['title'] : "";
                $arr[$id]['priority'] = self::PRIORITY_TICKET[$info['priority']];
                $arr[$id]['user'] = ($info['assign'] != 0) ? $listUsers[$info['assign']]['user_name'] : "";
                $arr[$id]['description'] = ($info['description'] != "") ? $info['description'] : "";
                $arr[$id]['result_description'] = ($info['result_description'] != "") ? $info['result_description'] : "";

                //подсчет времени для статусов среднее
                $time_status_1 += $log_status[$id]['in_work'][1];
                $time_status_2 += $log_status[$id]['in_work'][2];
                $time_status_4 += $log_status[$id]['in_work'][4];
                $time_status_5 += $log_status[$id]['in_work'][5];
                $time_status_7 += $log_status[$id]['in_work'][7];
                $time_resolved += $arr[$id]['time_resolved'];
                $time_count++;
            }
        }
        //если вдруг не сраслось
        if ($arr == []) {
            return false;
        }
        $time = [
            'status' => [
                1 => intval($time_status_1/$time_count),
                2 => intval($time_status_2/$time_count),
                4 => intval($time_status_4/$time_count),
                5 => intval($time_status_5/$time_count),
                7 => intval($time_status_7/$time_count),
            ],
            'resolved' => intval($time_resolved/$time_count),
            'count' => $time_count,
        ];
        unset($tikets);
        unset($log_status);
        unset($robots);
        $result = ['result' => $arr, 'average_time' => $time];

        return $result;
    }

    function get_report_resolved($date_start = null, $date_end = null, $robot_name = null, $robot_number = null, $robot_version = null)
    {

        $arr = $this->get_resolved_ticket_status_time($date_start, $date_end, $robot_name, $robot_number, $robot_version);
        if (!$arr) {
            $arr['result'] = [];
        }

        //создаем файлы
        //для папок
        $f_date = date('Y-m-d_H:i:s');
        if (!file_exists(PATCH_DIR."/report/")) {
            mkdir(PATCH_DIR."/report/", 0777);
        }
        $excel_name = PATCH_DIR."/report/".$f_date.".csv";
        require_once ('excel/Classes/PHPExcel.php');
        require_once ('excel/Classes/PHPExcel/IOFactory.php');
        $objPHPExcel = new PHPExcel();
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        $arr_ticket_status = $this->get_status();
        //задаем заголовки
        $objPHPExcel->getActiveSheet()->setCellValue("A1", 'ID');
        $objPHPExcel->getActiveSheet()->setCellValue("B1", 'Дата создания');
        $objPHPExcel->getActiveSheet()->setCellValue("C1", 'Дата решения');
        $objPHPExcel->getActiveSheet()->setCellValue("D1", 'Дата ремонта');
        $objPHPExcel->getActiveSheet()->setCellValue("E1", 'Источник');
        $objPHPExcel->getActiveSheet()->setCellValue("F1", 'Робот');
        $objPHPExcel->getActiveSheet()->setCellValue("G1", $arr_ticket_status[1]['title']);
        $objPHPExcel->getActiveSheet()->setCellValue("H1", $arr_ticket_status[4]['title']);
        $objPHPExcel->getActiveSheet()->setCellValue("I1", $arr_ticket_status[2]['title']);
        $objPHPExcel->getActiveSheet()->setCellValue("J1", $arr_ticket_status[5]['title']);
        $objPHPExcel->getActiveSheet()->setCellValue("K1", 'Время решения');
        $objPHPExcel->getActiveSheet()->setCellValue("L1", 'Предпоследний статус');
        $objPHPExcel->getActiveSheet()->setCellValue("M1", 'Статус');
        $objPHPExcel->getActiveSheet()->setCellValue("N1", 'Класс');
        $objPHPExcel->getActiveSheet()->setCellValue("O1", 'Категория');
        $objPHPExcel->getActiveSheet()->setCellValue("P1", 'Подкатегория');
        $objPHPExcel->getActiveSheet()->setCellValue("Q1", 'Приоритет');
        $objPHPExcel->getActiveSheet()->setCellValue("R1", 'Исполнитель');
        $objPHPExcel->getActiveSheet()->setCellValue("S1", 'Описание');
        $objPHPExcel->getActiveSheet()->setCellValue("T1", 'Решение');

        $row = 1;
        foreach ($arr['result'] as $ticket) {
            $row++;
            $objPHPExcel->getActiveSheet()->setCellValue("A" . $row, $ticket['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B" . $row, $ticket['date_create']);
            $objPHPExcel->getActiveSheet()->setCellValue("C" . $row, $ticket['date_resolved']);
            $objPHPExcel->getActiveSheet()->setCellValue("D" . $row, $ticket['date_repair']);
            $objPHPExcel->getActiveSheet()->setCellValue("E" . $row, $ticket['source']);
            $objPHPExcel->getActiveSheet()->setCellValue("F" . $row, $ticket['robot']);
            $objPHPExcel->getActiveSheet()->setCellValue("G" . $row, $ticket['time_in_status'][1]);
            $objPHPExcel->getActiveSheet()->setCellValue("H" . $row, $ticket['time_in_status'][4]);
            $objPHPExcel->getActiveSheet()->setCellValue("I" . $row, $ticket['time_in_status'][2]);
            $objPHPExcel->getActiveSheet()->setCellValue("J" . $row, $ticket['time_in_status'][5]);
            $objPHPExcel->getActiveSheet()->setCellValue("K" . $row, $ticket['time_resolved']);
            $objPHPExcel->getActiveSheet()->setCellValue("L" . $row, $ticket['before_status']);
            $objPHPExcel->getActiveSheet()->setCellValue("M" . $row, $ticket['status']);
            $objPHPExcel->getActiveSheet()->setCellValue("N" . $row, $ticket['class']);
            $objPHPExcel->getActiveSheet()->setCellValue("O" . $row, $ticket['category']);
            $objPHPExcel->getActiveSheet()->setCellValue("P" . $row, $ticket['subcategory']);
            $objPHPExcel->getActiveSheet()->setCellValue("Q" . $row, $ticket['priority']);
            $objPHPExcel->getActiveSheet()->setCellValue("R" . $row, $ticket['user']);
            $objPHPExcel->getActiveSheet()->setCellValue("S" . $row, $ticket['description']);
            $objPHPExcel->getActiveSheet()->setCellValue("T" . $row, $ticket['result_description']);
        }

        // Save CSV file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->setDelimiter(';');
        $objWriter->save($excel_name);

        return $excel_name;
    }

    function __destruct()
    {

    }
}