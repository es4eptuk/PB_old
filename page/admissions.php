<?php
class Admissions
{
    private $link_admis;
    private $log;
    private $orders;
    private $writeoff;
    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase;
        $this->link_admis = mysql_connect($database_server, $database_user, $database_password) or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8', $this->link_admis);
        //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
        $this->log = new Log;
        $this -> orders = new Orders;
        $this -> writeoff = new Writeoff;
    }
    function add_admission($order_id, $json, $category, $provider)
    {
        //echo $order_id;
        $pos_arr  = json_decode($json);
        //print_r($pos_arr);
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

            $pos_id       = $value['0'];
            $count_pos    = $value['3'];
            $finish_pos   = $value['4'];
            $admis_pos    = $value['5'];
            $return_pos   = $value['6'];
            $painting   = $value['7'];
            $drilling   = $value['8'];
            $order_paint['0']['0'] = 998;
            $order_drill['0']['0'] = 997;

            $order_paint['0']['1'] = 49;
            $order_drill['0']['1'] = 49;


            if($painting>0) {
                $order_paint[$pos_id]['0'] = $pos_id;
                $order_paint[$pos_id]['1'] = $value['1'];
                $order_paint[$pos_id]['2'] = $value['2'];
                $order_paint[$pos_id]['3'] = $painting;
                $order_paint[$pos_id]['4'] = 0;
                $order_paint[$pos_id]['5'] = 0;
                $order_paint[$pos_id]['6'] = $order_date;
            }



            if($drilling>0) {
                $order_drill[$pos_id]['0'] = $pos_id;
                $order_drill[$pos_id]['1'] = $value['1'];
                $order_drill[$pos_id]['2'] = $value['2'];
                $order_drill[$pos_id]['3'] = $drilling;
                $order_drill[$pos_id]['4'] = 0;
                $order_drill[$pos_id]['5'] = 0;
                $order_drill[$pos_id]['6'] = $order_date;
            }




            $total_finish = $finish_pos + $admis_pos;
            $p_finish     = $p_finish + $count_pos;
            $p_admis      = $p_admis + $total_finish;
            if ($finish_pos + $admis_pos >= $count_pos) {
                $arr_finish[] = 1;
            } else {
                $arr_finish[] = 0;
            }
            $query2 = "SELECT * FROM pos_items WHERE id=$pos_id";
            $result2 = mysql_query($query2) or die(mysql_error());
            while ($line2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
                $pos_array2[] = $line2;
            }
            //print_r($pos_array2);
            $count_total = $pos_array2['0']['total'];
            unset($pos_array2);
            $new_total = $count_total + $admis_pos;
            //echo $new_total."\n";
            $query     = "UPDATE `pos_items` SET `total` = '$new_total', `update_date` = '$date' WHERE `id` = '$pos_id'";
            //echo $query;
            $result = mysql_query($query) or die(mysql_error());
            if ($result && $admis_pos != 0) {
                $param['id']    = $pos_id;
                $param['type']  = "addmission";
                $param['count'] = $admis_pos;
                $param['title'] = "Поступление по заказу №$order_id";
                $this->add_log($param);
            }
            //echo "1";
            //echo $total_finish." ";
           // echo $order_id;
            if ($order_id != 0) {
                $return_pos = $return_pos - $admis_pos;
                $query      = "UPDATE IGNORE `orders_items` SET `pos_count_finish` = $total_finish,`pos_return` = $return_pos WHERE `order_id` = $order_id AND `pos_id` = $pos_id";
                echo $query;
               $result = mysql_query($query) or die(mysql_error());
            }
        }

        print_r($order_paint);
        print_r($order_drill);

        if (count($order_paint) > 1 )  {
            $this->orders->add_order(json_encode($order_paint),0,true);
            $order_paint['0']['0'] = "Склад";
            $order_paint['0']['1'] = "Списание на покраску";
            $order_paint['0']['4'] = 49;
            $this->writeoff->add_writeoff(json_encode($order_paint));
        }

        if (count($order_drill) > 1 ) {
            $this->orders->add_order(json_encode($order_drill),0,true);
            $order_drill['0']['0'] = "Склад";
            $order_drill['0']['1'] = "Списание на сварку/зенковку";
            $order_drill['0']['4'] = 49;
            $this->writeoff->add_writeoff(json_encode($order_drill));
        }




        unset($pos_array);
        if ($order_id != 0) {
            echo "Total: " . array_sum($arr_finish) . " из " . count($pos_arr) . " ";
            if (count($pos_arr) > 1) {
                $percent = round((array_sum($arr_finish) * 100) / count($pos_arr));
            } else {
                $percent = round((($finish_pos + $admis_pos) * 100) / $count_pos);
            }
            echo $percent;
            $query = "SELECT * FROM orders WHERE order_id='$order_id'";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $order_array[] = $line;
            }
            $result = mysql_query($query) or die(mysql_error());
            if ($percent >= 100) {
                $status = 2;
            } else {
                $status = 1;
            }
            if ($status == 2) {
                $prosecution = 0;
            } else {
                $prosecution = $order_array['0']['order_prosecution'];
            }
            $query = "UPDATE `orders` SET `order_prosecution`= $prosecution, `order_status` = $status,`order_completion` = $percent , `update_date` = '$date' WHERE `order_id` = $order_id";
            $result = mysql_query($query) or die(mysql_error());
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
        $result = mysql_query($query) or die(mysql_error());
        $idd = mysql_insert_id();
        if ($result) {
            $this->log->add(__METHOD__, "Добавление нового поступления №$idd");
        }


        return 1;
    }
    function get_admission($admiss_category)
    {
        $query = "SELECT * FROM admissions WHERE category='$admiss_category' ORDER BY id DESC LIMIT 50";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $admiss_array[] = $line;
        }
        // Jcdj,j;lftv gfvznm jn htpekmnfnf
        // mysql_free_result($result);
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
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $line       = mysql_fetch_array($result, MYSQL_ASSOC);
        $old_count  = $line['total'];
        $old_reserv = $line['reserv'];
        mysql_free_result($result);
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
                $query = "INSERT INTO `pos_log` (`id`, `id_pos`, `new_count`, `title`, `update_date`, `update_user`) VALUES (NULL, '$id', '$old_count', '$title', '$date', '$user_id')";
                break;
        }
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    }
    function __destruct()
    {
        //echo "admis - ";
        // print_r($this ->link_admis);
        //echo "<br>";
        //mysql_close($this ->link_admis);
    }
}
$admission = new Admissions;