<?php
//базовая модель для расшерения других моделей

class BaseModel
{
    private $pdo;

    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='';",
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);

    }

    private function addLog($class, $message, $user_id)
    {
        $date = date("Y-m-d H:i:s");
        $query = "INSERT INTO `system_log` (`id`, `method`, `log`, `update_user`, `update_date`) VALUES (NULL, '$class', '$message', $user_id, '$date')";
        $result = $this->pdo->query($query);

        return $result;
    }

    private function getAllLog()
    {
        $query = "SELECT * FROM `system_log`";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $log_array[] = $line;
        }

        return (isset($log_array)) ? $log_array : null;
    }

    function __destruct()
    {

    }
}