<?php
class Tickets
{
    private $user;
    private $robot;
    private $link_ticket;
    private $query;
    private $pdo;
    /**
     * @var string
     */


    public function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);
        //Подключение внешних классов
        $this->robot = new Robots;
        $this->user  = new User;
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
            $where = 'WHERE `id` !=6';
        }
        $this->query = "SELECT * FROM tickets_status $where ORDER BY `sort` ASC ";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $ticket_status_array[] = $line;
        }
        
        if (isset($ticket_status_array))
            return $ticket_status_array;
    }
    
    //Получение списка категорий тикетов, в качестве параметров можно указать тип $type (P - проблема (по умолчанию), I - консультация, FR - пожелание)

    /**
     * @param string $type
     * @return array
     */
    public function get_category($type = "P")
    {
        $this->query = "SELECT * FROM tickets_category WHERE `class` = '$type' ORDER BY `title` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $ticket_category_array[] = $line;
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
        $this->query  = "SELECT * FROM tickets_subcategory $where  ORDER BY `title` ASC";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $ticket_subcategory_array[] = $line;
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
    function add($robot, $class, $category, $subcategory, $status, $comment)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query   = "INSERT INTO `tickets` (
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
                '$robot', 
                '$class', 
                '$category', 
                '$subcategory', 
                '$comment', 
                '$status',
                '$user_id',
                '$date',
                '$user_id', 
                '$date');";
        $result = $this->pdo->query($this->query);
        $idd   = $this->pdo->lastInsertId();

        $this->query  = "INSERT INTO `robot_log` (`id`, `robot_id`,`source`, `level`, `comment`, `ticket_id`,`update_user`, `update_date`) VALUES (NULL, $robot, 'TICKET',1, '$comment', $idd,  $user_id, '$date')";
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

        if (isset($ticket_comments))
            return $ticket_comments;
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
    
    //Добавление комментария к тикету
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
            $where .= " AND `status` != 3 AND  `status` != 6 AND `status` != 7";
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
        }

        if (isset($ticket_array))
            return $ticket_array;
    }
    
    //Изменение статуса тикета
    //$id - id тикета
    //$status - id статуса
    public function ticket_change_status($id, $status)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        if ($status == 3) {
            $this->query = "UPDATE `tickets` SET `status` = '$status', `inwork` = '$date', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        } else {
            $this->query = "UPDATE `tickets` SET `status` = '$status', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        }
        //echo $query;
        // $query = "UPDATE `tickets` SET `status` = '$status', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        $this->query = "SELECT * FROM `tickets_status` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $status_array[] = $line;
        }
        if (isset($status_array)) {
            $status_str = $status_array[0]['title'];
            $color      = $status_array[0]['color'];
        }
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
    
    
    //Добавление результата тикета
    //$id - id тикета
    //$result - текст резултата
    public function ticket_add_result($id, $result)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query = "UPDATE `tickets` SET `status` = '3', `inwork` = '$date',`result_description` = '$result', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        // $query = "UPDATE `tickets` SET `status` = '$status', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        $this->query = "SELECT * FROM `tickets_status` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $status_array[] = $line;
        }
        if (isset($status_array)) {
            $status_str = $status_array[0]['title'];
            $color      = $status_array[0]['color'];
        }
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
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $newDate = date("Y-m-d", strtotime($date_finish));
        $this->query = "UPDATE `tickets` SET `status` = '4', `finish_date` = '$newDate', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";

        // $query = "UPDATE `tickets` SET `status` = '$status', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        $this->query = "SELECT * FROM `tickets_status` WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $status_array[] = $line;
        }
        if (isset($status_array)) {
            $status_str = $status_array[0]['title'];
            $color      = $status_array[0]['color'];
        }
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
        if ($assign_time == "0000-00-00 00:00:00") {
            $assign_time = $date;
        }
        $this->query = "UPDATE `tickets` SET `assign` = '$assign',`assign_time` = '$assign_time', `update_user` = $user_id, `update_date` = '$date' WHERE `id` = $id";
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
    
    //херня какая то непомню зачем
    function get_tickets_live()
    {
        $this->query = "SELECT * from ticket_log WHERE id_row=( SELECT max(id_row) FROM ticket_log )";
       $result = $this->pdo->query($this->query);
        $line = $result->fetch();
        return $line['id_row'];
        //echo "11";
    }
    function __destruct()
    {

    }
}
$tickets = new Tickets;