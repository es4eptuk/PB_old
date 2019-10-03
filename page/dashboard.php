<?php
class Dashboard
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
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);
        //Подключение внешних классов
        $this->robot = new Robots;
        $this->user = new User;
    }

    public function getRobotCompleteCount()
    {

        $this->query = "SELECT robots.id, robots.version, robots.number FROM `robots` JOIN `check` ON robots.id = check.robot WHERE robots.progress != 100 AND robots.delete = 0 AND check.id_check = 72 AND check.check = 1";
        $result = $this->pdo->query($this->query);
        while ($line = $result->fetch()) {
            $robots_array[] = $line;
        }
        if (isset($robots_array))
            return count($robots_array);
    }

    public function getDefectSummCurent()
    {
        $month =  date("m",strtotime("now"));
        $year =  date("Y",strtotime("now"));
        $totalMonth = "$year-$month-01 00:00:00";
        $this->query = "SELECT * FROM `writeoff` WHERE `category` LIKE '%Брак%' AND `update_date` > '$totalMonth'";
        $result = $this->pdo->query($this->query);
        $summ = 0;
        while ($line = $result->fetch()) {
            $summ += $line['total_price'];
        }
        return $summ;
    }

    public function getDefectSummLast()
    {
        $month =  date("m",strtotime("-1 Months"));
        $year =  date("Y",strtotime("-1 Months"));
        $totalMonth = "$year-$month-01 00:00:00";
        $this->query = "SELECT * FROM `writeoff` WHERE `category` LIKE '%Брак%' AND `update_date` > '$totalMonth'";
        $result = $this->pdo->query($this->query);
        $summ = 0;
        while ($line = $result->fetch()) {
            $summ += $line['total_price'];
        }
        return $summ;
    }

    public function getServiceSummCurent()
    {
        $month =  date("m",strtotime("now"));
        $year =  date("Y",strtotime("now"));
        $totalMonth = "$year-$month-01 00:00:00";
        $this->query = "SELECT * FROM `writeoff` WHERE `category` LIKE '%Сервис%' AND `update_date` > '$totalMonth'";
        $result = $this->pdo->query($this->query);
        $summ = 0;
        while ($line = $result->fetch()) {
            $summ += $line['total_price'];
        }
        return $summ;
    }

    public function getServiceSummLast()
    {
        $month =  date("m",strtotime("-1 Months"));
        $year =  date("Y",strtotime("-1 Months"));
        $totalMonth = "$year-$month-01 00:00:00";
        $this->query = "SELECT * FROM `writeoff` WHERE `category` LIKE '%Сервис%' AND `update_date` > '$totalMonth'";
        $result = $this->pdo->query($this->query);
        $summ = 0;
        while ($line = $result->fetch()) {
            $summ += $line['total_price'];
        }
        return $summ;
    }

    public function getSumWarehouse() {
        $this->query = "SELECT SUM(price*total) FROM `pos_items`";
        $result = $this->pdo->query($this->query);
        $line = $result->fetch();
        $sum = $line['SUM(price*total)'];
        return $sum;
    }

    public function getSumDebet() {
        $this->query = "SELECT order_delivery, SUM(order_price) FROM `orders` WHERE `order_payment` = 0  AND order_price != 0  GROUP BY order_delivery";
        $result = $this->pdo->query($this->query);

        while ($line = $result->fetch()) {
            $arr_date[] = $line;
        }
        return $arr_date;
    }

    public function getCountAnswers($minMinutes=0, $maxMinutes = 1) {
        $month =  date("m",strtotime("now"));
        $year =  date("Y",strtotime("now"));
        $totalMonth = "$year-$month-01 00:00:00";
        $this->query = "SELECT COUNT(*) FROM `bot_message` WHERE `createDate` >= '$totalMonth' AND `responseMinutes` <= $maxMinutes AND `responseMinutes` >= $minMinutes AND `isNight` = 0 AND `isEmployee` = 1 AND `chatId` != -399291922";
        //echo  $this->query;
        $result = $this->pdo->query($this->query);
        $countAnswers = $result->fetchColumn();
        return $countAnswers;
    }

    public function getViolation() {
        $month =  date("m",strtotime("now"));
        $year =  date("Y",strtotime("now"));
        $totalMonth = "$year-$month-01 00:00:00";
        $this->query = "SELECT COUNT(*) FROM `bot_message` WHERE `createDate` >= '$totalMonth' AND `violation` = 1  AND `chatId` != -399291922";
        //echo  $this->query;
        $result = $this->pdo->query($this->query);
        $countViolation = $result->fetchColumn();
        return $countViolation;
    }

    //@todo переделать для промежутка дат
    /**
     * @param string $date
     * @example 2019-10-03 00:00:00
     * Отображает кол-во нарушений за дату >= указанной
     * @return mixed
     */
    public function getViolationTest($date = "") {
        if (isset($date) && $date != null) {
            $totalMonth = $date;
        } else {
            $month = date("m", strtotime("now"));
            $year = date("Y", strtotime("now"));
            $totalMonth = "$year-$month-01 00:00:00";
        }
        $this->query = "SELECT COUNT(*) FROM `bot_message` WHERE `createDate` >= '$totalMonth' AND `violation` = 1  AND `chatId` != -399291922";
        $result = $this->pdo->query($this->query);
        $countViolation = $result->fetchColumn();
        return $countViolation;
    }




    function __destruct()
    {

    }
}
$dashboard = new Dashboard;

    ?>