<?php
class Sales
{
    private $link_sale;
    
    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $this->link_sale = mysql_connect($database_server, $database_user, $database_password) or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8', $this->link_sale);
        //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
        //$this -> telegram = new TelegramAPI;
        //$this -> robot = new Robots;
    }
    
    function get_items($param) {
        $query = "SELECT * FROM sales INNER JOIN sales_category ON sales.typeSale = sales_category.idCatSale JOIN customers ON sales.contragent = customers.id JOIN 1c_currancy ON sales.currancy = 1c_currancy.idCurrancy "; 
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $items_array[] = $line;
        }
        
        if (isset($items_array))
            return $items_array;
        
    }
   
    function __destruct()
    {
        //echo "orders - ";
        // print_r($this ->link_order);
        //echo "<br>";
        // mysql_close($this ->link_order);
    }
}
$sales = new Sales;