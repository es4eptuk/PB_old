<?php
class OneC
{
    private $link_1c;
    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $this->link_1c = mysql_connect($database_server, $database_user, $database_password) or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8', $this->link_1c);
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
    }
    function add_PP($data)
    {
        //print_r($data);
        $number      = $data->Номер;
        $date        = $data->Дата;
        $date        = new DateTime($date);
        $date        = $date->format('Y-m-d H:i:s');
        $innPl       = $data->ИННПлательщика;
        $kppPl       = $data->КПППлательщика;
        $contragent  = $data->Контрагент;
        $innPol      = $data->ИННПолучателя;
        $kppPol      = $data->КПППолучателя;
        $bank        = $data->Банк;
        $bik         = $data->БИК;
        $korr        = $data->КоррСчет;
        $schet       = $data->НомерСчета;
        $summ        = $data->СуммаДокумента;
        $summ        = str_replace("\xc2\xa0", '', $summ);
        $summ        = str_replace(',', '.', $summ);
        $summ        = (float) $summ;
        $nds         = $data->СтавкаНДС;
        $summnds     = $data->СуммаНДС;
        $summnds     = str_replace("\xc2\xa0", '', $summnds);
        $summnds     = str_replace(',', '.', $summnds);
        $summnds     = (float) $summnds;
        $description = $data->Назначение;
        $query       = "INSERT IGNORE INTO `1c_PP` (
            `id`, 
            `number`, 
            `date`, 
            `innPl`, 
            `kppPl`, 
            `contragent`, 
            `innPol`, 
            `kppPol`, 
            `bank`, 
            `bik`, 
            `korr`, 
            `schet`, 
            `summ`, 
            `nds`, 
            `summnds`, 
            `description`) VALUES (
                NULL, 
                '$number',
                '$date', 
                '$innPl', 
                '$kppPl', 
                '$contragent', 
                '$innPol', 
                '$kppPol', 
                '$bank', 
                '$bik', 
                '$korr', 
                '$schet', 
                '$summ', 
                '$nds', 
                '$summnds', 
                '$description'
                )";
        // echo $query."<br>";        
        $result = mysql_query($query) or die(mysql_error());
        return $result;
    }
    function add_VP($data)
    {
        //print_r($data);
        $number      = $data->Номер;
        $date        = $data->Дата;
        $date        = new DateTime($date);
        $date        = $date->format('Y-m-d H:i:s');
        $summ        = $data->СуммаДокумента;
        $summ        = str_replace("\xc2\xa0", '', $summ);
        $summ        = str_replace(',', '.', $summ);
        $summ        = (float) $summ;
        $currency    = $data->Валюта;
        $contragent  = $data->Плательщик;
        $description = $data->НазначениеПлатежа;
        //$description = htmlspecialchars($description);
        $query       = "INSERT IGNORE INTO `1c_income` (
        `id`, 
        `number`, 
        `date`, 
        `summ`, 
        `currency`, 
        `contragent`, 
        `category`, 
        `description`
        ) VALUES (
                NULL, 
                '$number',
                '$date', 
                '$summ', 
                '$currency', 
                '$contragent',
                1, 
                '$description'
                )";
        // echo $query."<br>";        
        $result = mysql_query($query) or die(mysql_error());
        return $result;
    }
    function get_PP($param)
    {
        $startDate  = isset($param['startDate']) ? $param['startDate'] : "0";
        $endDate    = isset($param['endDate']) ? $param['endDate'] : "0";
        $contragent = isset($param['contragent']) ? $param['contragent'] : "0";
        $purpose    = isset($param['purpose']) ? $param['purpose'] : "0";
        $group      = isset($param['group']) ? $param['group'] : "0";
        $where      = "";
        if ($startDate != "0") {
            $startDate = new DateTime($startDate);
            $startDate = $startDate->format('Y-m-d H:i:s');
            $where .= " AND 1c_PP.date >= '$startDate'";
        }
        if ($endDate != "0") {
            $endDate = new DateTime($endDate);
            $endDate = $endDate->format('Y-m-d H:i:s');
            $where .= " AND 1c_PP.date <= '$endDate'";
        }
        if ($contragent != "0") {
            $where .= " AND 1c_PP.contragent LIKE '%$contragent%'";
        }
        if ($group != "0") {
            $where .= "";
        }
        if ($purpose != "0") {
            if ($purpose == "empty") {
                $where .= " AND 1c_PP.description = ''";
            } else {
                $where .= " AND 1c_PP.description = '$purpose'";
            }
        }
        // $query = "SELECT number, date, contragent, summ, description FROM `1c_PP` WHERE `id` > 0".$where." ORDER BY `number` ASC";
        $query = "SELECT * FROM 1c_PP WHERE 1c_PP.id > 0 " . $where . " ORDER BY `number` ASC";
        //echo $query;
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $PP_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($PP_array))
            return $PP_array;
    }
    function get_income($param)
    {
        $startDate  = isset($param['startDate']) ? $param['startDate'] : "0";
        $endDate    = isset($param['endDate']) ? $param['endDate'] : "0";
        $contragent = isset($param['contragent']) ? $param['contragent'] : "0";
        $category   = isset($param['category']) ? $param['category'] : "0";
        $where      = "";
        if ($startDate != "0") {
            $startDate = new DateTime($startDate);
            $startDate = $startDate->format('Y-m-d H:i:s');
            $where .= " AND `date` >= '$startDate'";
        }
        if ($endDate != "0") {
            $endDate = new DateTime($endDate);
            $endDate = $endDate->format('Y-m-d H:i:s');
            $where .= " AND `date` <= '$endDate'";
        }
        if ($contragent != "0") {
            $where .= " AND `contragent` LIKE '%$contragent%'";
        }
        if ($category != "0") {
            $where .= " AND `category` = '$category'";
        }
        $query = "SELECT * FROM `1c_income` WHERE `id` > 0" . $where . " ORDER BY `date` ASC";
        //echo $query;
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat                 = $this->get_info_income_cat($line['category']);
            $line['category']    = $cat['category_title'];
            $line['category_id'] = $cat['id'];
            $income_array[]      = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($income_array))
            return $income_array;
    }
    function get_cat_income()
    {
        $query = "SELECT * FROM 1c_income_category ";
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($cat_array))
            return $cat_array;
    }
    function get_cat_PP($param)
    {
        $where = "";
        $group = isset($param['group']) ? $param['group'] : "0";
        if ($group != "0") {
            $where .= " AND 1c_PP_subcategory.group = 1";
        }
        $query = "SELECT description FROM 1c_PP group by 1 order by description ASC";
        echo $query;
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($cat_array))
            return $cat_array;
    }
    function get_stat_incom($param)
    {
        $startDate = isset($param['startDate']) ? $param['startDate'] : "0";
        $endDate   = isset($param['endDate']) ? $param['endDate'] : "0";
        $purpose   = isset($param['purpose']) ? $param['purpose'] : "0";
        $where     = "";
        if ($startDate != "0") {
            $startDate = new DateTime($startDate);
            $startDate = $startDate->format('Y-m-d H:i:s');
            $where .= " AND `date` >= '$startDate'";
        }
        if ($endDate != "0") {
            $endDate = new DateTime($endDate);
            $endDate = $endDate->format('Y-m-d H:i:s');
            $where .= " AND `date` <= '$endDate'";
        }
        $query = "SELECT category, SUM(summ) FROM 1c_income WHERE `id`>0 $where GROUP BY category";
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($cat_array))
            return $cat_array;
    }
    function get_stat($param)
    {
        $startDate = isset($param['startDate']) ? $param['startDate'] : "0";
        $endDate   = isset($param['endDate']) ? $param['endDate'] : "0";
        $purpose   = isset($param['purpose']) ? $param['purpose'] : "0";
        $where     = "";
        if ($startDate != "0") {
            $startDate = new DateTime($startDate);
            $startDate = $startDate->format('Y-m-d H:i:s');
            $where .= " AND `date` >= '$startDate'";
        }
        if ($endDate != "0") {
            $endDate = new DateTime($endDate);
            $endDate = $endDate->format('Y-m-d H:i:s');
            $where .= " AND `date` <= '$endDate'";
        }
        $query = "SELECT description, SUM(summ) FROM 1c_PP WHERE `id`>0 $where GROUP BY description";
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($cat_array))
            return $cat_array;
    }
    function get_stat_PP($param)
    {
        $startDate = isset($param['startDate']) ? $param['startDate'] : "0";
        $endDate   = isset($param['endDate']) ? $param['endDate'] : "0";
        $purpose   = isset($param['purpose']) ? $param['purpose'] : "0";
        $where     = "";
        if ($startDate != "0") {
            $startDate = new DateTime($startDate);
            $startDate = $startDate->format('Y-m-d H:i:s');
            $where .= " AND `date` >= '$startDate'";
        }
        if ($endDate != "0") {
            $endDate = new DateTime($endDate);
            $endDate = $endDate->format('Y-m-d H:i:s');
            $where .= " AND `date` <= '$endDate'";
        }
        if ($purpose != "0") {
            $where .= " AND `description` = '$purpose'";
        }
        $query = "SELECT SUM(summ) FROM 1c_PP WHERE `id`>0 $where ";
        $result = mysql_query($query) or die(mysql_error());
        $line = mysql_fetch_array($result, MYSQL_ASSOC);
        // Освобождаем память от результата
        mysql_free_result($result);
        return $line['SUM(summ)'];
    }
    function get_info_income_cat($id)
    {
        $query = "SELECT * FROM 1c_income_category WHERE id='$id'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($cat_array))
            return $cat_array['0'];
    }
    function get_cost_category()
    {
        $query = "SELECT * FROM 1c_PP_category";
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($cat_array))
            return $cat_array;
    }
    function get_cost_subcategory($id)
    {
        $query = "SELECT * FROM 1c_PP_subcategory WHERE category_id = $id";
        $result = mysql_query($query) or die(mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $cat_array[] = $line;
        }
        // Освобождаем память от результата
        mysql_free_result($result);
        if (isset($cat_array))
            return $cat_array;
    }
    function add_cost_category($title)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `1c_PP_category` (`id`, `category_title`) VALUES (NULL, '$title');";
        $result = mysql_query($query) or die($query);
        $idd = mysql_insert_id();
        return $idd;
    }
    function add_cost_subcategory($id, $title)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `1c_PP_subcategory` (`id`,  `category_id`,`subcategory_title`) VALUES (NULL, $id, '$title');";
        $result = mysql_query($query) or die($query);
        $idd = mysql_insert_id();
        return $idd;
    }
    function add_income_category($title)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "INSERT INTO `1c_income_category` (`id`, `category_title`) VALUES (NULL, '$title');";
        $result = mysql_query($query) or die($query);
        $idd = mysql_insert_id();
        return $idd;
    }
    function change_income_cat($id, $value)
    {
        $query = "UPDATE `1c_income` SET `category` = $value WHERE `id` = $id";
        $result = mysql_query($query) or die($query);
        return $result;
    }
    function __destruct()
    {
    }
}
$oneC = new OneC;