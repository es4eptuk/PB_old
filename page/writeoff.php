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
            $written = ($robot != 0) ? 1 : 0;
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
        $provider_ins = ($provider != 0) ? $provider : "NULL";
        $this->query = "INSERT INTO `writeoff` (`id`, `category`, `description`,`total_price`,`check`,`robot`, `option`, `written`, `update_date`, `update_user`, `provider_id`) VALUES (NULL, '$category','$description','$total_price','$check','$robot', '0', $written, '$date', $user_id, $provider_ins)";
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

        /*if ($category == "Не актуально") {

            $this->mail->send('Светлана Орлова', 's.orlova@promo-bot.ru', 'Списание на разработку №' . $idd, 'Пройдите по ссылке для просмотра списания https://db.promo-bot.ru/new/edit_writeoff.php?id=' . $idd);

        } else {
            // $this->mail->send('Екатерина Старцева',  'startceva@promo-bot.ru', 'Списание №'.$idd, 'Пройдите по ссылке для просмотра списания https://db.promo-bot.ru/new/edit_writeoff.php?id='.$idd);

        }*/

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
        $category = $pos_arr['0']['0'];
        $description = $pos_arr['0']['1'];
        $provider = $pos_arr['0']['2'];
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
        $query = "UPDATE `writeoff` SET  `description` = '$description', `update_date` = '$date', `total_price` = '$total_price', `provider_id` = '$provider'  WHERE `id` = $id;";
        //echo $query;
        $result = $this->pdo->query($query);

        if ($result) {
            $this->log->add(__METHOD__, "Редактирование списание №$id");
        }

        return $result;
    }

    //редактирование списания
    function edit_description_writeoff($id, $description)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "UPDATE `writeoff` SET  `description` = '$description', `update_date` = '$date' WHERE `id` = $id;";
        $result = $this->pdo->query($query);

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

    //провести списание
    function conduct_writeoff($id)
    {
        $this->query   = "UPDATE `writeoff` SET `written` = 1 WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        return true;
    }

    //провести списание
    function unconduct_writeoff($id)
    {
        $this->query   = "UPDATE `writeoff` SET `written` = 0 WHERE `id` = $id";
        $result = $this->pdo->query($this->query);
        return true;
    }

    function createFileDeliveryNote($writeoff_id)
    {
        $query = "SELECT * FROM `writeoff` WHERE `id` = $writeoff_id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $writeoffs[] = $line;
        }
        $writeoff = isset($writeoffs) ? $writeoffs[0] : null;
        unset($writeoffs);

        $items = [];
        $query = "SELECT * FROM `writeoff_items` JOIN `pos_items` ON `pos_items`.`id` = `writeoff_items`.`pos_id` WHERE `writeoff_items`.`writeoff_id` = $writeoff_id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $items[] = $line;
        }

        if ($writeoff == null || $items == []) {
            return null;
        }

        //создаем файлы
        $f_name = 'writeoff_'.$writeoff_id;
        if (!file_exists(PATCH_DIR . "/writeoffs/")) {
            mkdir(PATCH_DIR . "/writeoffs/", 0777);
        }
        $excel_name = PATCH_DIR . "/writeoffs/" . $f_name . ".xls";
        require_once('excel/Classes/PHPExcel.php');
        require_once('excel/Classes/PHPExcel/IOFactory.php');
        $objPHPExcel = new PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        //настройки
        $sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        // ORIENTATION_PORTRAIT — книжная
        // ORIENTATION_LANDSCAPE — альбомная
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageMargins()->setTop(0.3);
        $sheet->getPageMargins()->setRight(0.3);
        $sheet->getPageMargins()->setLeft(0.3);
        $sheet->getPageMargins()->setBottom(0.3);

        //размеры ячеек
        $width = 4.08;
        $height = 28.57;
        $sheet->getColumnDimension('A')->setWidth($width*0.18);
        $sheet->getColumnDimension('B')->setWidth($width*1.65);
        $sheet->getColumnDimension('C')->setWidth($width*1.38);
        $sheet->getColumnDimension('D')->setWidth($width*0.18);
        $sheet->getColumnDimension('E')->setWidth($width*0.52);
        $sheet->getColumnDimension('F')->setWidth($width*1.59);
        $sheet->getColumnDimension('G')->setWidth($width*0.63);
        $sheet->getColumnDimension('H')->setWidth($width*0.18);
        $sheet->getColumnDimension('I')->setWidth($width*0.73);
        $sheet->getColumnDimension('J')->setWidth($width*1.52);
        $sheet->getColumnDimension('K')->setWidth($width*0.35);
        $sheet->getColumnDimension('L')->setWidth($width*0.17);
        $sheet->getColumnDimension('M')->setWidth($width*0.17);
        $sheet->getColumnDimension('N')->setWidth($width*0.47);
        $sheet->getColumnDimension('O')->setWidth($width*0.44);
        $sheet->getColumnDimension('P')->setWidth($width*1.18);
        $sheet->getColumnDimension('Q')->setWidth($width*0.46);
        $sheet->getColumnDimension('R')->setWidth($width*0.91);
        $sheet->getColumnDimension('S')->setWidth($width*0.29);
        $sheet->getColumnDimension('T')->setWidth($width*0.05);
        $sheet->getColumnDimension('U')->setWidth($width*0.86);
        $sheet->getColumnDimension('V')->setWidth($width*0.65);
        $sheet->getColumnDimension('W')->setWidth($width*0.31);
        $sheet->getColumnDimension('X')->setWidth($width*1.02);
        $sheet->getColumnDimension('Y')->setWidth($width*0.10);
        $sheet->getColumnDimension('Z')->setWidth($width*1.36);
        $sheet->getColumnDimension('AA')->setWidth($width*0.18);
        $sheet->getColumnDimension('AB')->setWidth($width*0.29);
        $sheet->getColumnDimension('AC')->setWidth($width*0.10);
        $sheet->getColumnDimension('AD')->setWidth($width*1.28);
        $sheet->getColumnDimension('AE')->setWidth($width*0.26);
        $sheet->getColumnDimension('AF')->setWidth($width*0.81);
        $sheet->getColumnDimension('AG')->setWidth($width*0.18);
        $sheet->getColumnDimension('AH')->setWidth($width*0.65);
        $sheet->getColumnDimension('AI')->setWidth($width*0.26);
        $sheet->getColumnDimension('AJ')->setWidth($width*1.04);
        $sheet->getColumnDimension('AK')->setWidth($width*0.34);
        $sheet->getColumnDimension('AL')->setWidth($width*0.44);
        $sheet->getColumnDimension('AM')->setWidth($width*0.18);
        $sheet->getColumnDimension('AN')->setWidth($width*0.73);
        $sheet->getColumnDimension('AO')->setWidth($width*0.34);
        $sheet->getColumnDimension('AP')->setWidth($width*1.07);
        $sheet->getColumnDimension('AQ')->setWidth($width*1.80);
        $sheet->getColumnDimension('AR')->setWidth($width*0.39);

        //задаем шапку
        //
        $sheet->mergeCells("B1:AR1");
        $sheet->setCellValue("B1", "Типовая межотраслевая форма № М-15\nУтверждена постановлением Госкомстата России\nот 30.10.97 № 71а");
        $styleArray = [
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle("B1:AR1")->applyFromArray($styleArray);
        $sheet->getRowDimension(1)->setRowHeight($height*1.14);
        //
        $sheet->mergeCells("B2:AR2");
        $sheet->setCellValue("B2", "НАКЛАДНАЯ № $writeoff_id\nна отпуск материалов на сторону");
        $styleArray = [
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
            ],
        ];
        $sheet->getStyle("B2:AR2")->applyFromArray($styleArray);
        $sheet->getRowDimension(2)->setRowHeight($height*0.87);
        //
        $sheet->mergeCells("AQ3:AR3");$sheet->setCellValue("AQ3", "Коды");
        $sheet->getRowDimension(3)->setRowHeight($height*0.45);
        $sheet->mergeCells("AQ4:AR4");$sheet->setCellValue("AQ4", "0315007 ");
        $sheet->getRowDimension(4)->setRowHeight($height*0.45);
        $sheet->mergeCells("AQ5:AR5");$sheet->setCellValue("AQ5", "40897141 ");
        $sheet->getRowDimension(5)->setRowHeight($height*0.83);
        $styleArray = [
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
            ],
            'borders' => ['outline' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000'],], 'inside' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000'],],],
        ];
        $sheet->getStyle("AQ3:AR5")->applyFromArray($styleArray);
        //
        $sheet->setCellValue("AP4", "Форма по ОКУД  ");
        $sheet->setCellValue("AP5", "по ОКПО  ");
        $styleArray = [
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
            ],
            'font' => ['size' => 8,],
        ];
        $sheet->getStyle("AP4:AP45")->applyFromArray($styleArray);
        //
        $sheet->getStyle("A5:AR5")->applyFromArray(['font' => ['name' => 'Arial','size' => 8,],]);

        $sheet->mergeCells("B5:E5");$sheet->setCellValue("B5", "Организация");
        $sheet->getStyle("B5:E5")->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,],'font' => ['size' => 9,],]);
        $sheet->mergeCells("F5:AK5");$sheet->setCellValue("F5", "ООО \"ПРОМОБОТ\", ИНН 5903113639, 614990, Пермский край, Пермь г, Космонавтов ш, дом 111, корпус 2, тел.: (342) 257-80-\n85, р/с 40702810600220178520, в банке АО \"МСП БАНК\", БИК 044525108, к/с 30101810200000000108");
        $styleArray = [
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,],
            'font' => ['size' => 9,],
            'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],
        ];
        $sheet->getStyle("F5:AK5")->applyFromArray($styleArray);
        //
        $sheet->getRowDimension(6)->setRowHeight($height*0.40);
        //
        $styleArray = [
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => true,],
            'borders' => ['outline' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000'],], 'inside' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000'],],],
        ];
        $sheet->getStyle("F7:AR9")->applyFromArray($styleArray);

        $sheet->mergeCells("F7:F8");$sheet->setCellValue("F7", "Дата\nсостав-\nления"); //$sheet->setCellValue("F7", "Дата");
        $sheet->mergeCells("G7:I8");$sheet->setCellValue("G7", "Код\nвида\nоперации");//$sheet->mergeCells("G7:I7");$sheet->setCellValue("G7", "Код");
        $sheet->mergeCells("J7:T7");$sheet->setCellValue("J7", "Отправитель");
        $sheet->mergeCells("U7:AE7");$sheet->setCellValue("U7", "Получатель");
        $sheet->mergeCells("AF7:AR7");$sheet->setCellValue("AF7", "Ответственный за поставку");
        $sheet->getRowDimension(7)->setRowHeight($height*0.40);
        //
        //$sheet->setCellValue("F8", "состав-\nления");
        //$sheet->mergeCells("G8:I8");$sheet->setCellValue("G8", "вида\nоперации");
        $sheet->mergeCells("J8:O8");$sheet->setCellValue("J8", "структурное\nподразделение");
        $sheet->mergeCells("P8:T8");$sheet->setCellValue("P8", "вид\nдеятельности");
        $sheet->mergeCells("U8:Y8");$sheet->setCellValue("U8", "структурное\nподразделение");
        $sheet->mergeCells("Z8:AE8");$sheet->setCellValue("Z8", "вид\nдеятельности");
        $sheet->mergeCells("AF8:AK8");$sheet->setCellValue("AF8", "структурное\nподразделение");
        $sheet->mergeCells("AL8:AP8");$sheet->setCellValue("AL8", "вид\nдеятельности");
        $sheet->mergeCells("AQ8:AR8");$sheet->setCellValue("AQ8", "код\nиспол-\nнителя");
        $sheet->getRowDimension(8)->setRowHeight($height*1.23);
        //
        $writeoff_date = new DateTime($writeoff['update_date']);
        $writeoff_date = $writeoff_date->format('d.m.Y');
        $sheet->setCellValue("F9", $writeoff_date);
        $sheet->mergeCells("G9:I9");$sheet->setCellValue("G9", " ");
        $sheet->mergeCells("J9:O9");$sheet->setCellValue("J9", "Склад основной");
        $sheet->mergeCells("P9:T9");$sheet->setCellValue("P9", " ");
        $sheet->mergeCells("U9:Y9");$sheet->setCellValue("U9", " ");
        $sheet->mergeCells("Z9:AE9");$sheet->setCellValue("Z9", " ");
        $sheet->mergeCells("AF9:AK9");$sheet->setCellValue("AF9", " ");
        $sheet->mergeCells("AL9:AP9");$sheet->setCellValue("AL9", " ");
        $sheet->mergeCells("AQ9:AR9");$sheet->setCellValue("AQ9", " ");
        $sheet->getRowDimension(9)->setRowHeight($height*0.42);

        //
        $sheet->getRowDimension(10)->setRowHeight($height*0.24);
        //
        $sheet->mergeCells("B11:E11");$sheet->setCellValue("B11", "Основание");
        $sheet->getStyle("B11:E11")->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT],]);
        $sheet->mergeCells("F11:AR11");$sheet->setCellValue("F11", " ");
        $sheet->getStyle("F11:AR11")->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->getRowDimension(11)->setRowHeight($height*0.42);
        //
        $sheet->mergeCells("B12:E12");$sheet->setCellValue("B12", "Кому");
        $sheet->getStyle("B12:E12")->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT],]);
        $provider = ($writeoff['provider_id']) ? $this->position->get_info_pos_provider($writeoff['provider_id']) : null;
        $provider = $provider ? $provider['type']." ".$provider['title'] : "";
        $sheet->mergeCells("F12:Y12");$sheet->setCellValue("F12", $provider);
        $sheet->getStyle("F12:Y12")->applyFromArray([
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM, 'wrap' => true,],
            'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],
            ]);

        $sheet->mergeCells("Z12:AB12");$sheet->setCellValue("Z12", "Через кого");
        $sheet->getStyle("Z12:AB12")->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT],]);
        $sheet->mergeCells("AC12:AR12");$sheet->setCellValue("AC12", " ");
        $sheet->getStyle("AC12:AR12")->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],]);
        $sheet->getRowDimension(12)->setRowHeight($height*0.83);
        //
        $sheet->getRowDimension(13)->setRowHeight($height*0.40);
        $sheet->getStyle("A10:AR13")->applyFromArray(['font' => ['name' => 'Arial', 'size' => 9,],]);

        $row = 14;
        $sheet->mergeCells("B".$row.":E".$row);$sheet->setCellValue("B".$row, "Корреспондирующий счет");
        $sheet->mergeCells("F".$row.":O".$row);$sheet->setCellValue("F".$row, "Материальные ценности");
        $sheet->mergeCells("P".$row.":T".$row);$sheet->setCellValue("P".$row, "Единица измерения");
        $sheet->mergeCells("U".$row.":Y".$row);$sheet->setCellValue("U".$row, "Количество");
        $sheet->mergeCells("Z".$row.":AB".($row+1));$sheet->setCellValue("Z".$row, "Цена,\nруб. коп.");//$sheet->mergeCells("Z".$row.":AB".$row);$sheet->setCellValue("Z".$row, "Цена,");
        $sheet->mergeCells("AC".$row.":AE".($row+1));$sheet->setCellValue("AC".$row, "Сумма\nбез учета\nНДС,\nруб. коп.");//$sheet->mergeCells("AC".$row.":AE".$row);$sheet->setCellValue("AC".$row, "Сумма");
        $sheet->mergeCells("AF".$row.":AH".($row+1));$sheet->setCellValue("AF".$row, "Сумма\nНДС,\nруб. коп.");//$sheet->mergeCells("AF".$row.":AH".$row);$sheet->setCellValue("AF".$row, "Сумма");
        $sheet->mergeCells("AI".$row.":AK".($row+1));$sheet->setCellValue("AI".$row, "Всего\nс учетом\nНДС,\nруб. коп.");//$sheet->mergeCells("AI".$row.":AK".$row);$sheet->setCellValue("AI".$row, "Всего");
        $sheet->mergeCells("AL".$row.":AP".$row);$sheet->setCellValue("AL".$row, "Номер");
        $sheet->mergeCells("AQ".$row.":AR".($row+1));$sheet->setCellValue("AQ".$row, "Порядковый\nномер\nзаписи по\nскладской\nкартотеке");//$sheet->mergeCells("AQ".$row.":AR".$row);$sheet->setCellValue("AQ".$row, "Порядковый");
        $sheet->getRowDimension($row)->setRowHeight($height*0.40);
        $row = 15;
        $sheet->setCellValue("B" . $row, "счет,\nсубсчет");
        $sheet->mergeCells("C".$row.":E".$row);$sheet->setCellValue("C".$row, "код аналити-\nческого учета");
        $sheet->mergeCells("F".$row.":J".$row);$sheet->setCellValue("F".$row, "наименование, сорт, марка,\nразмер");
        $sheet->mergeCells("K".$row.":O".$row);$sheet->setCellValue("K".$row, "номенкла-\nтурный номер");
        $sheet->setCellValue("P".$row, "код");
        $sheet->mergeCells("Q".$row.":T".$row);$sheet->setCellValue("Q".$row, "наиме-\nнование");
        $sheet->mergeCells("U".$row.":V".$row);$sheet->setCellValue("U".$row, "надлежит\nотпус-\nтить");
        $sheet->mergeCells("W".$row.":Y".$row);$sheet->setCellValue("W".$row, "отпу-\nщено");
        //$sheet->mergeCells("Z".$row.":AB".$row);$sheet->setCellValue("Z".$row, "руб. коп.");
        //$sheet->mergeCells("AC".$row.":AE".$row);$sheet->setCellValue("AC".$row, "без учета\nНДС,\nруб. коп.");
        //$sheet->mergeCells("AF".$row.":AH".$row);$sheet->setCellValue("AF".$row, "НДС,\nруб. коп.");
        //$sheet->mergeCells("AI".$row.":AK".$row);$sheet->setCellValue("AI".$row, "с учетом\nНДС,\nруб. коп.");
        $sheet->mergeCells("AL".$row.":AN".$row);$sheet->setCellValue("AL".$row, "инвен-\nтар-\nный");
        $sheet->mergeCells("AO".$row.":AP".$row);$sheet->setCellValue("AO".$row, "паспорта");
        $sheet->mergeCells("AQ".$row.":AR".$row);$sheet->setCellValue("AQ".$row, "номер\nзаписи по\nскладской\nкартотеке");
        $sheet->getRowDimension($row)->setRowHeight($height*1.59);
        $styleArray = ['font' => ['name' => 'Arial', 'size' => 8,],];
        $sheet->getStyle("A1:AR4")->applyFromArray($styleArray);
        $sheet->getStyle("A6:AR9")->applyFromArray($styleArray);
        $sheet->getStyle("A".($row-1).":AR".$row)->applyFromArray($styleArray);
        $row = 16;
        $sheet->setCellValue("B" . $row, "1");
        $sheet->mergeCells("C".$row.":E".$row);$sheet->setCellValue("C".$row, "2");
        $sheet->mergeCells("F".$row.":J".$row);$sheet->setCellValue("F".$row, "3");
        $sheet->mergeCells("K".$row.":O".$row);$sheet->setCellValue("K".$row, "4");
        $sheet->setCellValue("P".$row, "5");
        $sheet->mergeCells("Q".$row.":T".$row);$sheet->setCellValue("Q".$row, "6");
        $sheet->mergeCells("U".$row.":V".$row);$sheet->setCellValue("U".$row, "7");
        $sheet->mergeCells("W".$row.":Y".$row);$sheet->setCellValue("W".$row, "8");
        $sheet->mergeCells("Z".$row.":AB".$row);$sheet->setCellValue("Z".$row, "9");
        $sheet->mergeCells("AC".$row.":AE".$row);$sheet->setCellValue("AC".$row, "10");
        $sheet->mergeCells("AF".$row.":AH".$row);$sheet->setCellValue("AF".$row, "11");
        $sheet->mergeCells("AI".$row.":AK".$row);$sheet->setCellValue("AI".$row, "12");
        $sheet->mergeCells("AL".$row.":AN".$row);$sheet->setCellValue("AL".$row, "13");
        $sheet->mergeCells("AO".$row.":AP".$row);$sheet->setCellValue("AO".$row, "14");
        $sheet->mergeCells("AQ".$row.":AR".$row);$sheet->setCellValue("AQ".$row, "15");
        $sheet->getRowDimension($row)->setRowHeight($height*0.36);
        $styleArray = ['font' => ['name' => 'Arial', 'size' => 6,],];
        $sheet->getStyle("A".$row.":AR".$row)->applyFromArray($styleArray);
        $styleArray = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,],];
        $sheet->getStyle("A".($row-2).":AR".$row)->applyFromArray($styleArray);


        $arr_units = $this->position->getUnits;
        $summ_cost = 0;
        $nn = 0;
        foreach ($items as $item) {
            $row++;
            $nn++;
            $sheet->setCellValue("B" . $row, "10.07 ");
            $sheet->mergeCells("C".$row.":E".$row);$sheet->setCellValue("C".$row, " ");
            $sheet->mergeCells("F".$row.":J".$row);$sheet->setCellValue("F".$row, $item['vendor_code']." ".$item['title']);
            $sheet->mergeCells("K".$row.":O".$row);$sheet->setCellValue("K".$row, " ");
            $sheet->setCellValue("P".$row, " ");
            $sheet->mergeCells("Q".$row.":T".$row);$sheet->setCellValue("Q".$row, $arr_units[$item['unit']]['title']);
            $amount = number_format($item['pos_count'],3,",", "");
            $sheet->mergeCells("U".$row.":V".$row);$sheet->setCellValue("U".$row, $amount);
            $sheet->mergeCells("W".$row.":Y".$row);$sheet->setCellValue("W".$row, $amount);
            $cost = number_format($item['pos_price'],2,",", "");
            $sheet->mergeCells("Z".$row.":AB".$row);$sheet->setCellValue("Z".$row, $cost);
            $cost = $item['pos_count'] * $item['pos_price'];
            $summ_cost += $cost;
            $cost = number_format($cost,2,",", "");
            $sheet->mergeCells("AC".$row.":AE".$row);$sheet->setCellValue("AC".$row, $cost);
            $sheet->mergeCells("AF".$row.":AH".$row);$sheet->setCellValue("AF".$row, " ");
            $sheet->mergeCells("AI".$row.":AK".$row);$sheet->setCellValue("AI".$row, " ");
            $sheet->mergeCells("AL".$row.":AN".$row);$sheet->setCellValue("AL".$row, " ");
            $sheet->mergeCells("AO".$row.":AP".$row);$sheet->setCellValue("AO".$row, " ");
            $sheet->mergeCells("AQ".$row.":AR".$row);$sheet->setCellValue("AQ".$row, " ");
            $sheet->getRowDimension($row)->setRowHeight($height*0.74);
        }
        //для всей таблицы
        $styleArray = [
            'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => true,],
            'borders' => ['outline' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000'],], 'inside' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000'],],],
        ];
        $sheet->getStyle("B14:AR".$row)->applyFromArray($styleArray);
        $styleArray = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,],];
        $sheet->getStyle("B17:B".$row)->applyFromArray($styleArray);
        $sheet->getStyle("Q17:T".$row)->applyFromArray($styleArray);
        $styleArray = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,],];
        $sheet->getStyle("U17:AE".$row)->applyFromArray($styleArray);
        $styleArray = ['font' => ['name' => 'Arial','size' => 8,], 'alignment' => ['wrap' => true,],];
        $sheet->getStyle("A17:AR".($row))->applyFromArray($styleArray);

        //подвал
        $sheet->getStyle("A".($row+1).":AR".($row+10))->applyFromArray(['font' => ['name' => 'Arial','size' => 8,]]);
        //
        $row++;
        $sheet->getRowDimension($row)->setRowHeight($height*0.40);
        //
        $number = $this->number2string($nn);
        $strL = mb_strtoupper(mb_substr($number,0,1));
        $number = $strL.mb_strcut($number,2);
        $row++;
        $sheet->mergeCells("B".$row.":AR".$row);$sheet->setCellValue("B".$row, "Всего отпущено ".$number);
        $sheet->getStyle("B".$row.":AR".$row)->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],'font' => ['size' => 9,],]);
        $sheet->getRowDimension($row)->setRowHeight($height*0.42);
        //
        $row++;
        $sheet->getRowDimension($row)->setRowHeight($height*0.40);
        //
        $summ_cost = round($summ_cost, 2);
        $coins = intval(($summ_cost - floor($summ_cost))*100);
        $coins = ($coins == 0) ? "00" : $coins;
        $summ = $this->number2string(floor($summ_cost), "rub");
        $strL = mb_strtoupper(mb_substr($summ,0,1));
        $summ = $strL.mb_strcut($summ,2);
        $string = "на сумму ".$summ." ".$coins." копеек, в том числе сумма НДС Ноль рублей 00 копеек";
        $row++;
        $sheet->mergeCells("B".$row.":AR".$row);$sheet->setCellValue("B".$row, $string);
        $sheet->getStyle("B".$row.":AR".$row)->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],'font' => ['size' => 9,],]);
        $sheet->getRowDimension($row)->setRowHeight($height*0.42);
        //
        $row++;
        $sheet->getRowDimension($row)->setRowHeight($height*0.40);
        //
        $row++;
        $sheet->mergeCells("B".$row.":C".$row);$sheet->setCellValue("B".$row, "Отпуск разрешил");
        $sheet->getStyle("B".$row.":C".$row)->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],'font' => ['size' => 9,],]);
        $sheet->mergeCells("E".$row.":G".$row);$sheet->setCellValue("E".$row, "Генеральный\nдиректор");
        $sheet->getStyle("E".$row.":G".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->mergeCells("I".$row.":L".$row);$sheet->setCellValue("I".$row, " ");
        $sheet->getStyle("I".$row.":L".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->mergeCells("N".$row.":S".$row);$sheet->setCellValue("N".$row, "Чугунов М. П.");
        $sheet->getStyle("N".$row.":S".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->mergeCells("V".$row.":Z".$row);$sheet->setCellValue("V".$row, "Главный бухгалтер");
        $sheet->getStyle("V".$row.":Z".$row)->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],'font' => ['size' => 9,],]);
        $sheet->mergeCells("AB".$row.":AF".$row);$sheet->setCellValue("AB".$row, " ");
        $sheet->getStyle("AB".$row.":AF".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->mergeCells("AH".$row.":AQ".$row);$sheet->setCellValue("AH".$row, "Чернова А. Ю.");
        $sheet->getStyle("AH".$row.":AQ".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->getRowDimension($row)->setRowHeight($height*0.77);
        //
        $row++;
        $sheet->mergeCells("E".$row.":G".$row);$sheet->setCellValue("E".$row, "должность");
        $sheet->mergeCells("I".$row.":L".$row);$sheet->setCellValue("I".$row, "подпись");
        $sheet->mergeCells("N".$row.":S".$row);$sheet->setCellValue("N".$row, "расшифровка подписи");
        $sheet->mergeCells("AB".$row.":AF".$row);$sheet->setCellValue("AB".$row, "подпись");
        $sheet->mergeCells("AH".$row.":AQ".$row);$sheet->setCellValue("AH".$row, "расшифровка подписи");
        $sheet->getStyle("A".$row.":AR".$row)->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],]);
        $sheet->getRowDimension($row)->setRowHeight($height*0.40);
        //
        $row++;
        $sheet->getRowDimension($row)->setRowHeight($height*0.40);
        //
        $row++;
        $sheet->mergeCells("B".$row.":C".$row);$sheet->setCellValue("B".$row, "Отпустил");
        $sheet->getStyle("B".$row.":C".$row)->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],'font' => ['size' => 9,],]);
        $sheet->mergeCells("E".$row.":G".$row);$sheet->setCellValue("E".$row, "Заместитель\nтехнического\nдиректора");
        $sheet->getStyle("E".$row.":G".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->mergeCells("I".$row.":L".$row);$sheet->setCellValue("I".$row, " ");
        $sheet->getStyle("I".$row.":L".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->mergeCells("N".$row.":S".$row);$sheet->setCellValue("N".$row, "Вдовин А. В.");
        $sheet->getStyle("N".$row.":S".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->mergeCells("V".$row.":Z".$row);$sheet->setCellValue("V".$row, "Получил");
        $sheet->getStyle("V".$row.":Z".$row)->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],'font' => ['size' => 9,],]);
        $sheet->mergeCells("AB".$row.":AF".$row);$sheet->setCellValue("AB".$row, " ");
        $sheet->getStyle("AB".$row.":AF".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->mergeCells("AH".$row.":AL".$row);$sheet->setCellValue("AH".$row, " ");
        $sheet->getStyle("AH".$row.":AL".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],]);
        $sheet->mergeCells("AN".$row.":AQ".$row);$sheet->setCellValue("AN".$row, " ");
        $sheet->getStyle("AN".$row.":AQ".$row)->applyFromArray(['borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],],]);

        $sheet->getRowDimension($row)->setRowHeight($height*1.14);
        //
        $row++;
        $sheet->mergeCells("E".$row.":G".$row);$sheet->setCellValue("E".$row, "должность");
        $sheet->mergeCells("I".$row.":L".$row);$sheet->setCellValue("I".$row, "подпись");
        $sheet->mergeCells("N".$row.":S".$row);$sheet->setCellValue("N".$row, "расшифровка подписи");
        $sheet->mergeCells("AB".$row.":AF".$row);$sheet->setCellValue("AB".$row, "должность");
        $sheet->mergeCells("AH".$row.":AL".$row);$sheet->setCellValue("AH".$row, "подпись");
        $sheet->mergeCells("AN".$row.":AQ".$row);$sheet->setCellValue("AN".$row, "расшифровка подписи");
        $sheet->getStyle("A".$row.":AR".$row)->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],]);
        $sheet->getRowDimension($row)->setRowHeight($height*0.40);

        /*
        $sheet->mergeCells("B11:E11");$sheet->setCellValue("B11", "Основание");
        $sheet->getStyle("B11:E11")->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT],]);
        $sheet->mergeCells("F11:AR11");$sheet->setCellValue("F11", "!!!!!Договор поставки №19-2018 от 16.04.2018 г.");
        $sheet->getStyle("F11:AR11")->applyFromArray(['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT],]);
        $sheet->getRowDimension(11)->setRowHeight($height*0.42);
        */


        // Save
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save($excel_name);

        return $excel_name;
    }

    function __destruct() {
       
    }

    //
    function number2string($number, $type = null) {

        // обозначаем словарь в виде статической переменной функции, чтобы
        // при повторном использовании функции его не определять заново
        static $dic = [
            // словарь необходимых чисел
            0 => [
                -2	=> 'две',
                -1	=> 'одна',
                1	=> 'одно',
                2	=> 'два',
                3	=> 'три',
                4	=> 'четыре',
                5	=> 'пять',
                6	=> 'шесть',
                7	=> 'семь',
                8	=> 'восемь',
                9	=> 'девять',
                10	=> 'десять',
                11	=> 'одиннадцать',
                12	=> 'двенадцать',
                13	=> 'тринадцать',
                14	=> 'четырнадцать' ,
                15	=> 'пятнадцать',
                16	=> 'шестнадцать',
                17	=> 'семнадцать',
                18	=> 'восемнадцать',
                19	=> 'девятнадцать',
                20	=> 'двадцать',
                30	=> 'тридцать',
                40	=> 'сорок',
                50	=> 'пятьдесят',
                60	=> 'шестьдесят',
                70	=> 'семьдесят',
                80	=> 'восемьдесят',
                90	=> 'девяносто',
                100	=> 'сто',
                200	=> 'двести',
                300	=> 'триста',
                400	=> 'четыреста',
                500	=> 'пятьсот',
                600	=> 'шестьсот',
                700	=> 'семьсот',
                800	=> 'восемьсот',
                900	=> 'девятьсот'
            ],
            // словарь порядков со склонениями для плюрализации
            1 => [
                0 => ['наименование', 'наименования', 'наименований'],
                1 => ['тысяча', 'тысячи', 'тысяч'],
                2 => ['миллион', 'миллиона', 'миллионов'],
                3 => ['миллиард', 'миллиарда', 'миллиардов'],
                4 => ['триллион', 'триллиона', 'триллионов'],
                5 => ['квадриллион', 'квадриллиона', 'квадриллионов'],
                // квинтиллион, секстиллион и т.д.
            ],
            // карта плюрализации
            2 => [2, 0, 1, 1, 1, 2],
        ];

        if ($type == "rub") {
            $dic[0][1] = 'один';
            $dic[1][0] = ['рубль', 'рубля', 'рублей'];
        }

        // обозначаем переменную в которую будем писать сгенерированный текст
        $string = array();
        // дополняем число нулями слева до количества цифр кратного трем,
        // например 1234, преобразуется в 001234
        $number = str_pad($number, ceil(strlen($number)/3)*3, 0, STR_PAD_LEFT);
        // разбиваем число на части из 3 цифр (порядки) и инвертируем порядок частей,
        // т.к. мы не знаем максимальный порядок числа и будем бежать снизу
        // единицы, тысячи, миллионы и т.д.
        $parts = array_reverse(str_split($number,3));
        // бежим по каждой части
        foreach($parts as $i=>$part) {
            // если часть не равна нулю, нам надо преобразовать ее в текст
            if($part>0) {
                // обозначаем переменную в которую будем писать составные числа для текущей части
                $digits = array();
                // если число треххзначное, запоминаем количество сотен
                if($part>99) {
                    $digits[] = floor($part/100)*100;
                }
                // если последние 2 цифры не равны нулю, продолжаем искать составные числа
                // (данный блок прокомментирую при необходимости)
                if($mod1=$part%100) {
                    $mod2 = $part%10;
                    $flag = $i==1 && $mod1!=11 && $mod1!=12 && $mod2<3 ? -1 : 1;
                    if($mod1<20 || !$mod2) {
                        $digits[] = $flag*$mod1;
                    } else {
                        $digits[] = floor($mod1/10)*10;
                        $digits[] = $flag*$mod2;
                    }
                }
                // берем последнее составное число, для плюрализации
                $last = abs(end($digits));
                // преобразуем все составные числа в слова
                foreach($digits as $j=>$digit) {
                    $digits[$j] = $dic[0][$digit];
                }
                // добавляем обозначение порядка или валюту
                $digits[] = $dic[1][$i][(($last%=100)>4 && $last<20) ? 2 : $dic[2][min($last%10,5)]];
                // объединяем составные числа в единый текст и добавляем в переменную, которую вернет функция
                array_unshift($string, join(' ', $digits));
            }
        }
        // преобразуем переменную в текст и возвращаем из функции, ура!
        return join(' ', $string);
    }
} 


