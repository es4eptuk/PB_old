<?php
class Settings
{
    private $link_sale;
    
    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $this->link_settings = mysql_connect($database_server, $database_user, $database_password) or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8', $this->link_settings);
        //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
        //$this -> telegram = new TelegramAPI;
        //$this -> robot = new Robots;
    }
    
    function get_param($name) {
        
        $query = "SELECT * FROM `system_settings` WHERE `name` = '$name'"; 
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
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
$settings = new Settings;