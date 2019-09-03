<?php
class Log
{
    private $link_log;
    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $this->link_log = mysql_connect($database_server, $database_user, $database_password) or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8', $this->link_log);
        //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
    }
    function add($class, $message)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `system_log` (`id`, `method`, `log`, `update_user`, `update_date`) VALUES (NULL, '$class', '$message', $user_id, '$date')";
        $result = mysql_query($query) or die($query);
        return $result;
    }
    function get_all()
    {
        $query = "SELECT * FROM `system_log`";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $log_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($log_array))
            return $log_array;
    }
    function __destruct()
    {
    }
}
$log = new Log;