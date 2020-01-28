<?php
class Log
{
    private $query;
    private $pdo;
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
    function add($class, $message)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `system_log` (`id`, `method`, `log`, `update_user`, `update_date`) VALUES (NULL, '$class', '$message', $user_id, '$date')";
        $result = $this->pdo->query($query);
        return $result;
    }
    function get_all()
    {
        $query = "SELECT * FROM `system_log`";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $log_array[] = $line;
        }

        if (isset($log_array))
            return $log_array;
    }
    function __destruct()
    {
    }
}
$log = new Log;