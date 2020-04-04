<?php
class Admissions
{
    private $query;
    private $pdo;
    private $log;
    private $orders;
    private $writeoff;
    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase, $writeoff, $log, $orders;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);

        $this->log = $log;//new Log;
        $this->orders = $orders;//new Orders;
        $this->writeoff = $writeoff;//new Writeoff;
    }
    function add_admission($order_id, $json, $category, $provider)
    {
        //echo $order_id;
        //массив данных переданный по пост
        //
        $pos_arr  = json_decode($json);

        //$log = date('Y-m-d H:i:s') . ' ' . print_r($pos_arr, true);
        //file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
        //die;


        $p_finish = 0;
        $p_admis  = 0;
        $date     = date("Y-m-d H:i:s");
        $user_id  = intval($_COOKIE['id']);

        $current_month = date('m');
        $current_year = date('y');
        $tmp_date = "25.".$current_month.".".$current_year;

        $order_date =  date('d.m.Y',strtotime("$tmp_date +1 month"));

        $order_date = new DateTime($order_date);
        $order_date = $order_date->format('Y-m-d H:i:s');
        $i = 0;
        foreach ($pos_arr as &$value) {

            $pos_id       = $value['0']; //ид позиции
            $count_pos    = $value['3']; //заказанное количество
            $finish_pos   = $value['4']; //отгруженное количество
            $admis_pos    = $value['5']; //поступаемое количество
            $return_pos   = $value['6']; //возврат количество
            $painting   = $value['7']; //?
            $drilling   = $value['8']; //?

            //зачем это
            $order_paint['0']['0'] = 998; //?
            $order_drill['0']['0'] = 997; //?
            //зачем это
            $order_paint['0']['1'] = 49; //?
            $order_drill['0']['1'] = 49; //?

            //мы это не !!!
            if($painting>0) {
                $order_paint[$pos_id]['0'] = $pos_id;
                $order_paint[$pos_id]['1'] = $value['1'];
                $order_paint[$pos_id]['2'] = $value['2'];
                $order_paint[$pos_id]['3'] = $painting;
                $order_paint[$pos_id]['4'] = 0;
                $order_paint[$pos_id]['5'] = 0;
                $order_paint[$pos_id]['6'] = $order_date;
            }
            //мы это не делаем
            if($drilling>0) {
                $order_drill[$pos_id]['0'] = $pos_id;
                $order_drill[$pos_id]['1'] = $value['1'];
                $order_drill[$pos_id]['2'] = $value['2'];
                $order_drill[$pos_id]['3'] = $drilling;
                $order_drill[$pos_id]['4'] = 0;
                $order_drill[$pos_id]['5'] = 0;
                $order_drill[$pos_id]['6'] = $order_date;
            }


            $total_finish = $finish_pos + $admis_pos; //итоговое количесвто поступившией позиций
            $p_finish     = $p_finish + $count_pos; //суммирование общего колличества заказанных позиций
            $p_admis      = $p_admis + $total_finish; //суммирование общего колличества поступивших позиций

            //считам сумарное поступление для процента выполнения заказа
            if (($finish_pos + $admis_pos) <= $count_pos) {
                $finish_pos_percent = $finish_pos_percent + $finish_pos + $admis_pos;
            } else {
                $finish_pos_percent = $finish_pos_percent + $count_pos;
            }

            //создаем массив $arr_finish
            //проверяем уже поступило + поступило сейчас >= заказанному количеству
            if ($finish_pos + $admis_pos >= $count_pos) {
                $arr_finish[] = 1;
            } else {
                $arr_finish[] = 0;
            }

            //ищем позицию в базе и записываем ее в массив $pos_array2
            $query2 = "SELECT * FROM pos_items WHERE id=$pos_id";
            $result2 = $this->pdo->query($query2);
            while ($line2 = $result2->fetch()) {
                $pos_array2[] = $line2;
            }

            //print_r($pos_array2);
            $count_total = $pos_array2['0']['total']; //количество текущей позиции на складе
            unset($pos_array2);
            $new_total = $count_total + $admis_pos; //добавляем поступление
            //echo $new_total."\n";
            //доавляем изменения в базу (позиции)
            $query     = "UPDATE `pos_items` SET `total` = '$new_total', `update_date` = '$date' WHERE `id` = '$pos_id'";
            $result = $this->pdo->query($query);
            //если изменения есть, добавляем в лог
            if ($result && $admis_pos != 0) {
                $param['id']    = $pos_id;
                $param['type']  = "addmission";
                $param['count'] = $admis_pos;
                $param['title'] = "Поступление по заказу №$order_id";
                $this->add_log($param);
            }
            //добовляем изменения в базу (позиции в текущем заказе)
            if ($order_id != 0) {
                $return_pos = $return_pos - $admis_pos;
                $query      = "UPDATE IGNORE `orders_items` SET `pos_count_finish` = $total_finish,`pos_return` = $return_pos WHERE `order_id` = $order_id AND `pos_id` = $pos_id";
                $result = $this->pdo->query($query);
            }
        }

        //print_r($order_paint);
        //print_r($order_drill);
        //мы это не делаем
        if (count($order_paint) > 1 )  {
            $this->orders->add_order(json_encode($order_paint),0,true);
            $order_paint['0']['0'] = "Склад";
            $order_paint['0']['1'] = "Списание на покраску";
            $order_paint['0']['4'] = 49;
            $this->writeoff->add_writeoff(json_encode($order_paint));
        }
        //мы это не делаем
        if (count($order_drill) > 1 ) {
            $this->orders->add_order(json_encode($order_drill),0,true);
            $order_drill['0']['0'] = "Склад";
            $order_drill['0']['1'] = "Списание на сварку/зенковку";
            $order_drill['0']['4'] = 49;
            $this->writeoff->add_writeoff(json_encode($order_drill));
        }
        //unset($pos_array);

        if ($order_id != 0) {

            //пробуем задать % выполнения заказа
            //$pos_arr - исходный массив данных
            //

            //выводим сколько позиций полностью закрытых из общего количества
            //echo "Total: " . array_sum($arr_finish) . " из " . count($pos_arr) . " ";

            //
            //старый просчет % выполнения заказа
            /*if (count($pos_arr) > 1) {
                $percent = round((array_sum($arr_finish) * 100) / count($pos_arr));
            } else {
                $percent = round((($finish_pos + $admis_pos) * 100) / $count_pos);
            }*/

            //высчитываем процент выполнения по новой логике
            $percent = round($finish_pos_percent * 100/ $p_finish, 0, PHP_ROUND_HALF_DOWN);

            //выводем процент, зачем?
            //echo $percent;

            //непонятные телодвижения
            $query = "SELECT * FROM orders WHERE order_id='$order_id'";
            $result = $this->pdo->query($query);
            while ($line = $result2->fetch()) {
                $order_array[] = $line;
            }
            $result = $this->pdo->query($query);

            //меняем статус при выполнении заказа на 100%
            if ($percent >= 100) {
                $status = 2;
            } else {
                $status = 1;
            }
            //выставление просрочки в днях
            if ($status == 2) {
                $prosecution = 0;
            } else {
                $prosecution = intval($order_array['0']['order_prosecution']);
            }
            //сохраняем в базу
            $query = "UPDATE `orders` SET `order_prosecution`= '$prosecution', `order_status` = $status,`order_completion` = $percent , `update_date` = '$date' WHERE `order_id` = $order_id";
            $result = $this->pdo->query($query);
        }

        $query = "INSERT INTO `admissions` (
            `id`, 
            `order_id`,
            `date`, 
            `category`, 
            `provider`, 
            `price`, 
            `responsible`, 
            `update_date`, 
            `update_user`) VALUES (
                NULL, 
                '$order_id',
                '$date', 
                '$category',
                '$provider', 
                '0',
                '$user_id', 
                '$date', 
                '$user_id');";
        $result = $this->pdo->query($query);
        $idd = $this->pdo->lastInsertId();
        if ($result) {
            $this->log->add(__METHOD__, "Добавление нового поступления №$idd");
        }


        return 1;
    }

    function get_admission($admiss_category)
    {
        $query = "SELECT * FROM admissions WHERE category='$admiss_category' ORDER BY id DESC LIMIT 50";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $admiss_array[] = $line;
        }

        if (isset($admiss_array))
            return $admiss_array;
    }
    function add_log($param)
    {
        $id      = $param['id'];
        $type    = $param['type'];
        $count   = $param['count'];
        $title   = $param['title'];
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "SELECT * FROM `pos_items` WHERE id = $id";
        $result = $this->pdo->query($query);
        $line       = $result->fetch();
        $old_count  = $line['total'];
        $old_reserv = $line['reserv'];
        switch ($type) {
            case "edit":
                $title = $title . ": $old_count -> $count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$count', '$title', '$date', '$user_id')";
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
                $tmp   = $old_count - $count;
                $title = $title . ": $count шт. $old_count -> $tmp";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$count', '$title', '$date', '$user_id')";
                break;
            case "addmission":
                $tmp   = $old_count + $count;
                $title = $title . ": $count шт. Новое значение -> $old_count";
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `old_count`, `new_count`, `title`, `old_reserv`, `new_reserv`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$tmp', '$title', '$old_reserv', '$old_reserv', '$date', '$user_id')";
                break;
        }
        $result = $this->pdo->query($query);
    }
    function __destruct()
    {
        //echo "admis - ";
        // print_r($this ->link_admis);
        //echo "<br>";
        //mysql_close($this ->link_admis);
    }
}