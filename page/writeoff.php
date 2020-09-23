<?php 
class Writeoff
{
    private $query;
    private $pdo;
    private $log;
    private $orders;
    private $mail;
    private $position;
    private $plan;

    function __construct()
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
        global $log, $orders, $mail, $position, $plan;

        $this->log = $log;//new Log;
        $this->orders = $orders;//new Orders;
        $this->mail = $mail;//new Mail;
        $this->position = $position;
        $this->plan= $plan;
    }

    //создать списание
    function add_writeoff($json)
    {
        $writeoff_arr = json_decode($json, true);
        /*$log = date('Y-m-d H:i:s') . ' ' . print_r($writeoff_arr, true);
        file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
        die;*/
        //print_r($writeoff_arr);
        $check = 0;
        $robot = 0;
        $written = 0;
        $category = $writeoff_arr['0']['0'];
        $description = $writeoff_arr['0']['1'];
        $provider = (isset($writeoff_arr['0']['4'])) ? $writeoff_arr['0']['4'] : 0;

        if (isset($writeoff_arr['0']['2'])) {
            $check = $writeoff_arr['0']['2'];
        }
        if (isset($writeoff_arr['0']['3'])) {
            $robot = $writeoff_arr['0']['3'];
            $written = 1;
        }
        $total_price_is_null = false;
        if ($category == "Возврат поставщику") {
            $total_price_is_null = true;
            $writeoff_arr['0']['0'] = 999;
            $writeoff_arr['0']['1'] = $provider;
            $json = json_encode($writeoff_arr);
            $this->orders->add_order($json, 0, 1);
            $this->log->add(__METHOD__, "Добавлен новый заказ на Возврат постащику");
        }

        if ($category == "Покраска/Покрытие") {
            $total_price_is_null = true;
            $writeoff_arr['0']['0'] = 998;
            $writeoff_arr['0']['1'] = $provider;
            $json = json_encode($writeoff_arr);
            $this->orders->add_order($json, 0, 1);
            $this->log->add(__METHOD__, "Добавлен новый заказ на Покраска/Покрытие");
        }

        if ($category == "Сварка/Зенковка") {
            $total_price_is_null = true;
            $writeoff_arr['0']['0'] = 997;
            $writeoff_arr['0']['1'] = $provider;
            $json = json_encode($writeoff_arr);
            $this->orders->add_order($json, 0, 1);
            $this->log->add(__METHOD__, "Добавлен новый заказ на Сварка/Зенковка");
        }


        array_shift($writeoff_arr);
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $total_price = 0;

        foreach ($writeoff_arr as &$value) {
            $price = $value['4'] * $value['3'];
            $total_price = $total_price + $price;
        }

        $total_price = ($total_price_is_null) ? 0 : $total_price;
        $this->query = "INSERT INTO `writeoff` (`id`, `category`, `description`,`total_price`,`check`,`robot`, `option`, `written`, `update_date`, `update_user`) VALUES (NULL, '$category','$description','$total_price','$check','$robot', '0', $written, '$date', $user_id)";
        $result = $this->pdo->query($this->query);

        $idd = $this->pdo->lastInsertId();

        if ($result) {
            $this->log->add(__METHOD__, "Добавлено новое списание №$idd - $category: $description");
        }

        foreach ($writeoff_arr as &$value) {
            $pos_id = $value['0'];
            $vendor_code = $value['1'];
            $title = $value['2'];
            $count = $value['3'];
            $price = $value['4'];

            /*$log = date('Y-m-d H:i:s') . ' ' . print_r($value, true);
            file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
            die;*/

            $subcategory = 0;

            if ($pos_id != "") {
                $this->query = "INSERT INTO `writeoff_items` (`id`, `writeoff_id`, `pos_id`, `vendor_code`, `pos_title`, `pos_count`, `pos_price`) VALUES (NULL, '$idd', '$pos_id', '$vendor_code', '$title', '$count', '$price');";
                $result = $this->pdo->query($this->query);
                $this->query = "UPDATE `pos_items` SET `total` = total - $count WHERE `id` = $pos_id";
                $result = $this->pdo->query($this->query);

                if ($result && $count != 0) {
                    $log_title = "Списание - $category ($description)";
                    $param['id'] = $pos_id;
                    $param['type'] = "writeoff";
                    $param['count'] = $count;
                    $param['title'] = $log_title;
                    $this->position->add_log($param);
                }
            }

        }

        if ($category == "Не актуально") {

            $this->mail->send('Светлана Орлова', 's.orlova@promo-bot.ru', 'Списание на разработку №' . $idd, 'Пройдите по ссылке для просмотра списания https://db.promo-bot.ru/new/edit_writeoff.php?id=' . $idd);

        } else {
            // $this->mail->send('Екатерина Старцева',  'startceva@promo-bot.ru', 'Списание №'.$idd, 'Пройдите по ссылке для просмотра списания https://db.promo-bot.ru/new/edit_writeoff.php?id='.$idd);

        }

        return true;

    }

    function get_writeoff($robot = 0, $no_written = 0)
    {
        if ($robot == 0) {
            $where = "WHERE `robot` = 0";
            $where = ($no_written != 0) ? $where." AND `written` = 0" : $where;
        } else {
            $where = "WHERE `robot` = $robot";
        }

        $this->query = "SELECT * FROM `writeoff` $where ORDER BY `update_date`"; // DESC LIMIT 5000"; //
        //echo $query;
        $result = $this->pdo->query($this->query);

        while ($line = $result->fetch()) {
            $orders_array[] = $line;
        }

        // Освобождаем память от результата
        // mysql_free_result($result);


        if (isset($orders_array))
            return $orders_array;
    }


    function get_info_writeoff($id)
    {


        $this->query = "SELECT * FROM writeoff WHERE id='$id'";
        $result = $this->pdo->query($this->query);

        while ($line = $result->fetch()) {
            $writeoff_array[] = $line;
        }

        if (isset($writeoff_array))
            return $writeoff_array['0'];
    }
    
    function get_pos_in_writeoff($id) {
        $this->query = "SELECT * FROM writeoff_items WHERE writeoff_id='$id'";
        $result = $this->pdo->query($this->query);
        
        while($line = $result->fetch()){
        $writeoff_array[] = $line; 
        }

    	if (isset($writeoff_array))
    	return $writeoff_array;
    }

    //редактирование списания
    function edit_writeoff($id, $json)
    {
        $pos_arr = json_decode($json);
        $description = $pos_arr['0']['1'];
        $category = $pos_arr['0']['0'];
        if ($category == "Возврат поставщику" || $category == "Покраска/Покрытие" || $category == "Сварка/Зенковка") {
            $total_price_is_null = true;
        } else {
            $total_price_is_null = false;
        }

        array_shift($pos_arr);

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);

        $query = "SELECT * FROM `writeoff_items` WHERE `writeoff_id`=$id";
        $result = $this->pdo->query($query);

        while ($line = $result->fetch()) {
            $old_array[] = $line;
        }
        $total_price = 0;
        foreach ($old_array as $key => $old) {

            $count_new = $pos_arr[$key]['4'];
            $price = $pos_arr[$key]['5'];
            $count_old = $old['pos_count'];
            $delta = $count_old - $count_new;
            $row_id = $pos_arr[$key]['0'];
            $pos_id = $pos_arr[$key]['1'];
            if ($count_old != $count_new) {

                $this->query = "UPDATE `writeoff_items` SET `pos_count` = $count_new WHERE `id` = $row_id";
                $result = $this->pdo->query($this->query);
                //echo $query;
                $this->query = "UPDATE `pos_items` SET `total` = total + $delta WHERE `id` = $pos_id";
                //echo $query;
                $result = $this->pdo->query($this->query);
                $param['id'] = $pos_id;
                $param['type'] = "addmission";
                $param['count'] = $delta;
                $param['title'] = "Изменение списания ";
                $this->position->add_log($param);
            }
            $total_price = $total_price + $count_new * $price;

        }

        $total_price = ($total_price_is_null) ? 0 : $total_price;
        //echo $date;
        $query = "UPDATE `writeoff` SET  `description` = '$description', `update_date` = '$date', `total_price` = '$total_price'  WHERE `id` = $id;";
        //echo $query;
        $result = $this->pdo->query($query);

        if ($result) {
            $this->log->add(__METHOD__, "Редактирование списание №$id");
        }

        return $result;
    }

    function get_stat($param)
    {
        $startDate = isset($param['startDate']) ? $param['startDate'] : "0";
        $endDate = isset($param['endDate']) ? $param['endDate'] : "0";
        $purpose = isset($param['purpose']) ? $param['purpose'] : "0";

        $where = "";
        if ($startDate != "0") {
            $startDate = new DateTime($startDate);
            $startDate = $startDate->format('Y-m-d H:i:s');
            $where .= " AND `update_date` >= '$startDate'";
        }
        if ($endDate != "0") {
            $endDate = new DateTime($endDate);
            $endDate = $endDate->format('Y-m-d H:i:s');
            $where .= " AND `update_date` <= '$endDate'";
        }
        if ($purpose != "0") {
            $where .= " AND `category` LIKE '%$purpose%'";
        }
        $this->query = "SELECT SUM(total_price) FROM writeoff WHERE `id`>0 $where ";
        $result = $this->pdo->query($this->query);

        $line = $result->fetch();

        if (isset($line)) {
            return $line['SUM(total_price)'];
        }
    }

    //создает лог - лог позиции
    /*
    function add_log($param)
    {
        $id = $param['id'];
        $type = $param['type'];
        $count = $param['count'];
        $title = $param['title'];
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "SELECT * FROM `pos_items` WHERE id = $id";
        $result = $this->pdo->query($query);
        $line = $result->fetch();
        $new_count = $line['total'];
        $old_reserv = $line['reserv'];

        switch ($type) {
            case "edit":
                $new_reserv = $old_reserv;
                $old_count = 0;
                $title = $title . ": Новое значение -> $new_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `old_reserv`, `new_reserv`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$new_count', '$title', '$old_reserv', '$new_reserv', '$date', '$user_id')";
                break;
            case "reserv":
                $title = $title . ": $count шт.";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_reserv`, `new_reserv`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv', '$old_reserv+$count', '$title', '$date', '$user_id')";
                break;
            case "unreserv":
                $title = $title . ": $count шт.";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_reserv`, `new_reserv`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_reserv', '$old_reserv-$count', '$title', '$date', '$user_id')";
                break;
            case "writeoff":
                $new_reserv = $old_reserv;
                $old_count = $new_count + $count;
                $title = $title . ": $count шт. Новое значение -> $new_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `old_reserv`, `new_reserv`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$new_count', '$title', '$old_reserv', '$new_reserv', '$date', '$user_id')";
                break;
            case "addmission":
                $new_reserv = $old_reserv;
                $old_count = $new_count - $count;
                $title = $title . ": $count шт. Новое значение -> $new_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `old_reserv`, `new_reserv`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$new_count', '$title', '$old_reserv', '$new_reserv', '$date', '$user_id')";
                break;
        }
        $result = $this->pdo->query($query);
    }
    */
    
    
    function del_pos_writeoff($id, $pos_id, $count)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $this->query   = "SELECT * FROM `writeoff_items` WHERE `pos_id` = $pos_id AND `writeoff_id` = $id";
        $result = $this->pdo->query($this->query);
        while($line = $result->fetch()){
            $pos_writeoff[] = $line;
        }
        foreach ($pos_writeoff as $value) {
            $count = $value['pos_count'];
            $price = $value['pos_price'];
            $delta = $count * $price;
            $this->query   = "UPDATE `writeoff` SET `total_price` = total_price - $delta WHERE `id` = $id";
            $result = $this->pdo->query($this->query);

            $this->query   = "DELETE FROM `writeoff_items` WHERE `pos_id` = $pos_id AND `writeoff_id` = $id";
            $result = $this->pdo->query($this->query);

            if ($result) {
                $this->query   = "UPDATE `pos_items` SET `total` = total + $count WHERE`id` = $pos_id";
                $result = $this->pdo->query($this->query);
                $param['id'] = $pos_id;
                $param['type'] = "addmission";
                $param['count'] = $count;
                $param['title'] = "Отмена списания ";
                $this->position->add_log($param);
            }
        }


        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return true;
    }
    
    
    function del_writeoff($id) {
        
        $this->query = "SELECT * FROM `writeoff_items` WHERE `writeoff_id` = $id";
        $result = $this->pdo->query($this->query);
        
        while($line = $result->fetch()){
            $item_array[] = $line; 
        }
        
         foreach ($item_array as $key => $item) {
            $pos_id =   $item['pos_id'];
            $count = $item['pos_count'];
            $this->query   = "UPDATE `pos_items` SET `total` = total + $count WHERE `id` = $pos_id";
            //echo $query."<br>";
            $result = $this->pdo->query($this->query);
            
            
            if ($result) {
               $param['id'] = $pos_id;
               $param['type'] = "addmission";
               $param['count'] = $count;
               $param['title'] = "Удаление списания ";
               $this->position->add_log($param);
                
            }
         }
         
        $this->query   = "DELETE FROM `writeoff_items` WHERE `writeoff_id`=$id";
        //echo $query."<br>";
        $result = $this->pdo->query($this->query);
        
        $this->query   = "DELETE FROM `writeoff` WHERE `id` = $id";
        //echo $query."<br>";
        $result = $this->pdo->query($this->query);
        
       
           
        
    }

    //информация по списаниям для бухгалтера
    function get_writeoff_on_robot($robot) {
        //информация по сборкам
        $arr_assemble = $this->plan->get_assemblyes_items();
        //добавляем инфу по позициям
        $this->query = "SELECT * FROM `pos_items`";
        $result = $this->pdo->query($this->query);
        while($line = $result->fetch()){
            $arr_pos[$line['id']] = $line;
        }
        //набраем все списания по роботу
        $this->query = "SELECT * FROM `writeoff_items` 
            JOIN `writeoff` ON `writeoff_items`.`writeoff_id` = `writeoff`.`id`
            JOIN `pos_items` ON `writeoff_items`.`pos_id` = `pos_items`.`id` 
            WHERE `writeoff`.`robot` = $robot";
        $result = $this->pdo->query($this->query);
        $arr = [];
        while($line = $result->fetch()){
            if (isset($arr[$line['pos_id']])) {
                $arr[$line['pos_id']]['count'] += $line['pos_count'];
            } else {
                $arr[$line['pos_id']]['pos_id'] = $line['pos_id'];
                $arr[$line['pos_id']]['vendor_code'] = $line['vendor_code'];
                $arr[$line['pos_id']]['title'] = $line['title'];
                $arr[$line['pos_id']]['assembly'] = $line['assembly'];
                $arr[$line['pos_id']]['count'] = $line['pos_count'];
            }
            //если позиция сборка
            if ($line['assembly'] != 0) {
                foreach ($arr_assemble[$line['assembly']] as $pos_id => $count ) {
                    if (isset($arr[$pos_id])) {
                        $arr[$pos_id]['count'] += $count * $line['pos_count'];
                    } else {
                        $arr[$pos_id]['pos_id'] = $arr_pos[$pos_id]['id'];
                        $arr[$pos_id]['vendor_code'] = $arr_pos[$pos_id]['vendor_code'];
                        $arr[$pos_id]['title'] = $arr_pos[$pos_id]['title'];
                        $arr[$pos_id]['assembly'] = $arr_pos[$pos_id]['assembly'];
                        $arr[$pos_id]['count'] = $count * $line['pos_count'];
                    }
                }
            }
        }

        //добавляем инфу по позициям
        /*$this->query = "SELECT * FROM `pos_items`";
        $result = $this->pdo->query($this->query);
        while($line = $result->fetch()){
            $arr_pos[$line['id']] = $line;
        }*/
        //обрабатываем массив
        /*foreach ($arr as $id => $info) {
            $arr[$id]['vendor_code'] = $arr_pos[$id]['vendor_code'];
            $arr[$id]['title'] = $arr_pos[$id]['title'];
            $arr[$id]['assembly'] = $arr_pos[$id]['assembly'];
        }*/

        /*$writoff_robot = $this->get_writeoff($robot);

        foreach ($writoff_robot as $key => $item) {
            //$writeoff_price += $item['total_price'];
            $pos_arr = $this->get_pos_in_writeoff($item['id']);
            $writeoff_pos_arr = array_merge($writeoff_pos_arr, $pos_arr);
        }*/
        return $arr;
    }

    function __destruct() {
       
    }
} 


