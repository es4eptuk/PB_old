<?php
class Settings
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
    
    function get_param($name) {
        
        $query = "SELECT * FROM `system_settings` WHERE `name` = '$name'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $items_array[] = $line;
        }
        
        if (isset($items_array))
            return $items_array[0];
        
    }
   
    function __destruct()
    {
        //echo "orders - ";
        // print_r($this ->link_order);
        //echo "<br>";
        // mysql_close($this ->link_order);
    }
}