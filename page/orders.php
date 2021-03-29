<?php
class Orders
{
    private $query;
    private $pdo;
    private $log;

    private $position;

    const CONTACTS = [
        35 => [
            'role' => 'Руководитель отдела закупок ООО "ПРОМОБОТ"',
            'name' => 'Лобачева Татьяна',
            'mobile' => '+7-952-644-32-55',
            'phone' => '+7(342)257-80-85 доб.402',
            'email' => 't.lobacheva@promo-bot.ru',
            'signature' => '/img/podpis_lobacheva.png',
        ],
        43 => [
            'role' => 'Менеджер по внешней кооперации ООО "ПРОМОБОТ"',
            'name' => 'Бекбулатова Мария',
            'mobile' => '+7-902-837-94-53',
            'phone' => '+7(342)257-80-85 доб.403',
            'email' => 'm.bekbulatova@promo-bot.ru',
            'signature' => '/img/podpis_bekbulatova.png',
        ],
        /*101 => [
            'role' => 'Специалист отдела закупок ООО "ПРОМОБОТ"',
            'name' => 'Бусовикова Надежда',
            'mobile' => '+7-950-463-17-00',
            'phone' => '+7(342)257-80-85 доб.405',
            'email' => 'n.biusovikova@promo-bot.ru',
            'signature' => '/img/podpis_biusovikova.png',
        ],*/
        112 => [
            'role' => 'Специалист отдела закупок ООО "ПРОМОБОТ"',
            'name' => 'Дарья Драчева',
            'mobile' => '+7-929-232-97-07',
            'phone' => '+7(342)257-80-85 доб.405',
            'email' => 'd.dracheva@promo-bot.ru',
            'signature' => '/img/podpis_dracheva.png',
        ],
        0 => [
            'role' => 'отдела закупок ООО "ПРОМОБОТ"',
            'name' => '',
            'mobile' => '',
            'phone' => '+7(342)257-80-85',
            'email' => 'info@promo-bot.ru',
            'signature' => '',
        ],
    ];

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
        global $log, $position;

        $this->log = $log; //new Log;
        $this->position = $position;
        //$this -> robot = new Robots;
    }

    function add_order($json,$version,$auto = false)
    {
        $order_arr = json_decode($json, true);
        $category  = $order_arr['0']['0'];
        $provider  = $order_arr['0']['1'];
        array_shift($order_arr);
        $sum = 0;

        foreach ($order_arr as &$value) {
            $sum        = $sum + $value['5'];
            $date       = new DateTime($value['6']);
            $date_arr[] = $date->format('Y-m-d  H:i:s');
        }

        $max_date  = max($date_arr);
        $min_date  = min($date_arr);
        $datetime1 = new DateTime($max_date);
        $datetime2 = new DateTime('NOW');
        $interval  = $datetime1->diff($datetime2);
        $znak      = $interval->format('%R');
        if ($znak == "+") {
            $prosecution = $interval->format('%a');
        } else {
            $prosecution = 0;
        }
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);


        $auto = intval($auto);
        $query   = "INSERT INTO `orders` (
            `order_id`, 
            `order_date`, 
            `order_user`, 
            `order_category`, 
            `order_provider`, 
            `order_delivery`, 
            `order_completion`, 
            `order_price`, 
            `order_status`, 
            `order_prosecution`, 
            `order_responsible`,
            `version`,
            `auto`,
            `update_user`,
            `update_date`
            ) 
            VALUES (
                NULL, 
                '$date', 
                '$user_id', 
                '$category', 
                '$provider', 
                '$max_date', 
                '0', 
                '$sum', 
                '0', 
                '$prosecution',
                '$user_id',
                '$version',
                '$auto',
                '$user_id',
                '$date'
                );";
        $result = $this->pdo->query($query);
        $idd = $this->pdo->lastInsertId();
        
        if ($result) {
             $this->log->add(__METHOD__,"Добавлен новый заказ №$idd");
        }

        //в автомате создает файлы
        if ($auto) {
            $date_folder= date("m.d.y"); 
            $date_folder = new DateTime($max_date);
            $date_folder = $date_folder->format('d_m_y');
            if (!file_exists(PATCH_DIR."/orders/".$date_folder)) {
                mkdir(PATCH_DIR."/orders/".$date_folder, 0777);
            }
            $excel_name = PATCH_DIR."/orders/".$date_folder."/Order_".$idd;
            $zip = new ZipArchive();
            $zip->open(PATCH_DIR."/orders/".$date_folder."/orders_".$category.".zip", ZipArchive::CREATE);
            require_once ('excel/Classes/PHPExcel.php');
            require_once ('excel/Classes/PHPExcel/IOFactory.php');
            $objPHPExcel = new PHPExcel();
            $objRichText = new PHPExcel_RichText();
            // Set properties
            $objPHPExcel->getProperties()->setCreator("SAMPLE1");
              $objPHPExcel->getProperties()->setLastModifiedBy("SAMPLE1");
              $objPHPExcel->getProperties()->setTitle("SAMPLE1");
              $objPHPExcel->getProperties()->setSubject("SAMPLE1");
              $objPHPExcel->getProperties()->setDescription("SAMPLE1");
              
                          // Add some data
              $objPHPExcel->setActiveSheetIndex(0);
            
              $letters = range('A','Z');
              $count_row =5;
              $cell_name="";
              
              $query = "SELECT * FROM pos_provider WHERE id='$provider'";
             $result = $this->pdo->query($query);
             while ($line = $result->fetch()) {
                    $provider_array[] = $line;
                }

                if (isset($provider_array))
                    $provider =  $provider_array['0']['type']." ".$provider_array['0']['title'];
              
              $objPHPExcel->getActiveSheet()->setCellValue("A1", 'Заказ поставщику №'.$idd.' от '.$date );
              //$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
              $objPHPExcel->getActiveSheet()->setCellValue("A2", 'Поставщик:');
              
              $objPHPExcel->getActiveSheet()->setCellValue("B2", $provider);
              
              $objPHPExcel->getActiveSheet()->setCellValue("A4", '№');
              $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setBold(true);
              $objPHPExcel->getActiveSheet()->setCellValue("B4", 'Артикул');
              $objPHPExcel->getActiveSheet()->getStyle("B4")->getFont()->setBold(true);
              $objPHPExcel->getActiveSheet()->setCellValue("C4", 'Наименование');
              $objPHPExcel->getActiveSheet()->getStyle("C4")->getFont()->setBold(true);
              $objPHPExcel->getActiveSheet()->setCellValue("D4", 'Количество');
              $objPHPExcel->getActiveSheet()->getStyle("D4")->getFont()->setBold(true);
              $objPHPExcel->getActiveSheet()->setCellValue("E4", 'Дата');
            $objPHPExcel->getActiveSheet()->getStyle("E4")->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            
             $objPHPExcel->getActiveSheet()->mergeCells("A1:E1");
            $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
            
            $styleArray = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => 'FF000000'),
                    ),
                    'inside' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => 'FF000000'),
                    ),
                ),
            );
            
            $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->applyFromArray($styleArray);

        }

        //вставляет позиции из заказа
        $cnt = 1;
        foreach ($order_arr as &$value) {
            $pos_id      = $value['0'];
            $vendor_code = $value['1'];
            $title       = $value['2'];
            $count       = $value['3'];
            $price       = $value['5'];
            $date        = new DateTime($value['6']);
            $date_r      = $date->format('Y-m-d  H:i:s');
            $subcategory = 0;
            $query       = "INSERT INTO `orders_items` (
                `id`, 
                `order_id`, 
                `pos_id`, 
                `pos_category`, 
                `pos_subcategory`, 
                `pos_title`, 
                `pos_vendorcode`,
                `pos_count`,
                `pos_count_finish`,
                `pos_return`,                 
                `pos_price`,
                `pos_date`) VALUES (
                    NULL, 
                    '$idd', 
                    '$pos_id', 
                    '$category', 
                    '$subcategory', 
                    '$title', 
                    '$vendor_code', 
                    '$count',
                    '0', 
                    '0',                    
                    '$price', 
                    '$date_r');";
             $result = $this->pdo->query($query);

             //только если авто
             if ($auto) {
               $count_row++;
               $objPHPExcel->getActiveSheet()->setCellValue("A".$count_row, $cnt);
                $objPHPExcel->getActiveSheet()->getStyle("A".$count_row)->applyFromArray($styleArray);
               $objPHPExcel->getActiveSheet()->setCellValue("B".$count_row, $vendor_code);
                $objPHPExcel->getActiveSheet()->getStyle("B".$count_row)->applyFromArray($styleArray);
               $objPHPExcel->getActiveSheet()->setCellValue("C".$count_row, $title);
                $objPHPExcel->getActiveSheet()->getStyle("C".$count_row)->applyFromArray($styleArray);
               $objPHPExcel->getActiveSheet()->setCellValue("D".$count_row, $count);
                $objPHPExcel->getActiveSheet()->getStyle("D".$count_row)->applyFromArray($styleArray);
                $date_r        = new DateTime($date_r);
                $date_r      = $date->format('d.m.Y');
               $objPHPExcel->getActiveSheet()->setCellValue("E".$count_row, $date_r);
                $objPHPExcel->getActiveSheet()->getStyle("E".$count_row)->applyFromArray($styleArray);
              
             }
            
            $cnt++;
        }

        //только если авто
         if ($auto) {
            // Save Excel 2007 file
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
            $objWriter->save($excel_name.".xlsx");
            $zip->addFile($excel_name.".xlsx", "Заказ_".$idd."_".str_replace(' ', '_', $provider).".xlsx");
            $zip->close();
            //echo "orders/".$date_folder."/orders.zip";
             return $idd;
         } else {
            // Освобождаем память от результата
            //mysql_free_result($result);

            return true;//$result;
         }
    }
    
    function get_orders_pos($pos)
    {
        $query = "SELECT * FROM orders WHERE `pos_id`='$pos' ORDER BY `order_delivery` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $orders_array[] = $line;
        }

        if (isset($orders_array))
            return $orders_array;
    }
    
    function get_orders($order_category,$status=1)
    {
        $where = "";
        if ($status!=1) {$where.=" AND order_status != 2";}
        
        if ($order_category==999 || $order_category==998 || $order_category==997) {
             $query = "SELECT * FROM orders WHERE orders.order_category = '$order_category' ORDER BY `order_delivery` ASC";
        }
        else {
             $query = "SELECT * FROM orders INNER JOIN pos_provider ON orders.order_provider = pos_provider.id WHERE orders.order_category = '$order_category' ".$where." ORDER BY `order_delivery` ASC"; 
        }
        
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $date = new DateTime($line['order_delivery']);
            $line['order_delivery'] = $date->format('d.n.Y');
            $orders_array[] = $line;
        }
        if (isset($orders_array))
            return $orders_array;
    }
    
    function get_info_order($id)
    {
        $query = "SELECT * FROM orders WHERE order_id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $order_array[] = $line;
        }
        if (isset($order_array))
            return $order_array['0'];
    }
    
    function edit_order($id, $category, $provider, $status, $responsible, $version, $json)
    {
        $pos_arr = json_decode($json);
        array_shift($pos_arr);
        $sum      = 0;
        //$p_finish = 0;
        //$p_admis  = 0;
        foreach ($pos_arr as &$value) {
            $pos_id     = $value['0'];
            $count_pos  = $value['3'];
            $finish_pos = $value['4'];
            $sum        = $sum + $value['7'];
            //echo $sum." ";
            //$trim_date = trim($value['7']);
            $date       = new DateTime(trim($value['8']));
            $date_arr[] = $date->format('Y-m-d  H:i:s');
        }
        $max_date  = max($date_arr);
        $min_date  = min($date_arr);
        $datetime1 = new DateTime($max_date);
        $datetime2 = new DateTime('NOW');
        $interval  = $datetime1->diff($datetime2);
        $znak      = $interval->format('%R');
        if ($znak == "+" && $status != 2) {
            $prosecution = $interval->format('%a');
        } else {
            $prosecution = 0;
        }
        //    print_r( $cur_date);
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `orders_items` WHERE `order_id`=$id";
        $result = $this->pdo->query($query);
        $p_finish = 0;
        $p_admis  = 0;
        $finish_pos_percent = 0;

        foreach ($pos_arr as &$value) {
            $pos_id      = $value['0'];
            $subcategory = 0;
            $title       = $value['2'];
            $vendor_code = $value['1'];
            $count       = $value['3'];
            $return       = $value['5'];
            $price       = $value['7'];
            $finish_pos  = $value['4'];
            $p_finish    = $p_finish + $count;
            $p_admis     = $p_admis + $finish_pos;
            // echo $count." ".$finish_pos. " ||| ";

            //считам сумарное поступление для процента выполнения заказа
            if ($finish_pos <= $count) {
                $finish_pos_percent = $finish_pos_percent + $finish_pos;
            } else {
                $finish_pos_percent = $finish_pos_percent + $count;
            }

            /*
            if ($finish_pos >= $count) {
                $arr_finish[] = 1;
            } else {
                $arr_finish[] = 0;
            }*/
           
            $date   = new DateTime($value['8']);
            //print_r( $value );
            $date_r = $date->format('Y-m-d  H:i:s');
            $query  = "INSERT INTO `orders_items` (
                `id`, 
                `order_id`, 
                `pos_id`, 
                `pos_category`, 
                `pos_subcategory`, 
                `pos_title`, 
                `pos_vendorcode`,
                `pos_count`, 
                `pos_count_finish`,
                `pos_return`,                
                `pos_price`,
                `pos_date`) VALUES (
                    NULL, 
                    '$id', 
                    '$pos_id', 
                    '$category', 
                    '$subcategory', 
                    '$title', 
                    '$vendor_code', 
                    '$count', 
                    '$finish_pos',
                    '$return',
                    '$price', 
                    '$date_r');";
            $result = $this->pdo->query($query);

        }
        
        
        //echo "Total: ".array_sum($arr_finish)." из ".count($pos_arr)." ";
        
        $date = date("Y-m-d H:i:s");

        //пересчитываем процент выполнения
        /*if ( count($pos_arr) > 1) {
            $percent = round((array_sum($arr_finish) * 100) / count($pos_arr),PHP_ROUND_HALF_DOWN);
        } else {
            $percent = round(($finish_pos * 100) / $count,PHP_ROUND_HALF_DOWN );
        }
        echo $percent;*/
        //$percent =  round(($p_admis * 100) / $p_finish);

        //высчитываем процент выполнения по новой логике
        $percent = floor(round($finish_pos_percent * 100/ $p_finish, 2, PHP_ROUND_HALF_DOWN));
        
        if ($percent > 0) {
            $status = 1;
        } else {
            $status = $status;
        }
        
        if ($percent >= 100) {
            $status = 2;
        }
        echo $percent;
        $query = "UPDATE `orders` SET `order_prosecution`= $prosecution, `order_status` = $status,  `order_completion` = $percent,`order_prosecution` = $prosecution, `order_price` = '$sum',`order_category` = '$category', `order_delivery` = '$max_date', `order_provider` = '$provider',`order_status` = '$status',`order_responsible` = '$responsible', `version` = '$version', `update_date` = '$date' , `update_user` = '$user_id' WHERE `order_id` = $id;";
        $result = $this->pdo->query($query);
        
        if ($result) {
             $this->log->add(__METHOD__,"Редактирование заказа №$id");
        }
        
        // Освобождаем память от результата
        // mysql_free_result($result);
        return $result;
    }
    
    function get_pos_in_order($id)
    {
        $query = "SELECT pos_items.id, pos_items.vendor_code, pos_items.price, pos_items.title, pos_items.assembly, orders_items.pos_count, orders_items.pos_date, orders_items.pos_count_finish, orders_items.pos_return   FROM orders_items JOIN pos_items ON orders_items.pos_id = pos_items.id WHERE order_id=$id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            if($line['pos_date'] == '0000-00-00 00:00:00'){
            $line['pos_date'] = '2019-01-01 00:00:00';
            }
              $pos_array[] = $line;
        }
        if (isset($pos_array))
            return $pos_array;
    }
    
    function del_order($id)
    {
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query   = "DELETE FROM `orders` WHERE `order_id` = $id";
        $result = $this->pdo->query($query);

        if ($result) {
             $this->log->add(__METHOD__,"Удаление заказа №$id");
        }
        
        
        $query = "DELETE FROM `orders_items` WHERE `order_id` = $id";
        $result = $this->pdo->query($query);
        return $result;
    }
    
    function update_order($id)
    {
        $sum       = 0;
        $p_finish  = 0;
        $p_admis   = 0;
        $max_date  = max($date_arr);
        $min_date  = min($date_arr);
        $datetime1 = new DateTime($max_date);
        $datetime2 = new DateTime('NOW');
        $interval  = $datetime1->diff($datetime2);
        $znak      = $interval->format('%R');
        if ($znak == "+" && $status != 2) {
            $prosecution = $interval->format('%a');
        } else {
            $prosecution = 0;
        }
        //    print_r( $cur_date);
        $date    = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $date    = date("Y-m-d H:i:s");
        $percent = round(($p_admis * 100) / $p_finish, PHP_ROUND_HALF_DOWN);
        if ($percent >= 100) {
            $status = 2;
        } else {
            $status = $status;
        }
        //echo $date;
        $query = "UPDATE `orders` SET `order_prosecution`= $prosecution, `order_status` = $status,  `order_completion` = $percent,`order_prosecution` = $prosecution, `order_price` = '$sum',`order_category` = '$category', `order_delivery` = '$max_date', `order_provider` = '$provider',`order_status` = '$status',`order_responsible` = '$responsible', `update_date` = '$date' , `update_user` = '$user_id' WHERE `orders`.`id` = $id;";
        $result = $this->pdo->query($query);
        
        
        if ($result) {
             $this->log->add(__METHOD__,"Обновление заказа №$id");
        }
        
        return $result;
    }
    
    function orderDate($id) {
        $query = "SELECT * FROM `orders_items` WHERE `pos_id` = $id AND pos_count_finish<pos_count ORDER BY `orders_items`.`pos_date` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }
        if (isset($pos_array)) {
            $date = new DateTime($pos_array[0]['pos_date']);
            return $date->format('d.m.Y');
        } else {
            return "<center class='text-red'>Не заказано</center>";
            
        }
    }
    
    
    function orderDateStr($id) {
        $query = "SELECT * FROM `orders_items` WHERE `pos_id` = $id AND pos_count_finish<pos_count ORDER BY `orders_items`.`pos_date` ASC";
        $result = $this->pdo->query($query);

        while ($line = $result->fetch()) {
            $pos_array[] = $line;
        }

        if (isset($pos_array)) {
            $date = new DateTime($pos_array[0]['pos_date']);
            $count = $pos_array[0]['pos_count'] - $pos_array[0]['pos_count_finish'];
            return $date->format('d.m.Y')." (".$count." шт.)";
        } else {
            return "Не заказано";
            
        }
    }
    
    function add_order_plan($arr,$date) {
        $current_month = date('m');
        $current_year = date('y');
        $tmp_date = "25.".$current_month.".".$current_year;
        
        $order_date =  date('d.m.Y',strtotime("$tmp_date +1 month"));
        
        $order_date = new DateTime($order_date);
		$order_date = $order_date->format('Y-m-d H:i:s');
        //echo $order_date;
        $arr = json_decode($arr, true);



        $i = 0;
        $category = $arr['category'];
        
            
            
        
            foreach ( $arr[$date] as $key => $value) {
              $orders[$value['provider']][$i]['id'] = $value['id'];  
              //$orders[$value['provider']][$i]['category'] = $value['category'];  
              $orders[$value['provider']][$i]['vendor_code'] = $value['vendor_code']; 
              $orders[$value['provider']][$i]['title'] = $value['title']; 
              $orders[$value['provider']][$i]['price'] = $value['price'];
              $orders[$value['provider']][$i]['count'] = $value['count'];
              $orders[$value['provider']][$i]['summ'] = $value['price']*$value['count'];
              $i++;
            }

        /*$log = date('Y-m-d H:i:s') . ' ' . print_r($orders, true);
        file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
        die;*/

        //print_r($orders);
         foreach ($orders as $key_order => $value_order) {
             unset($json);
            $json['0']['0'] = $category;
            if($key_order==0)$key_order=1;
            $json['0']['1'] = $key_order;
            $i = 1;
            //print_r($value_order);
            foreach ($value_order as $key_order2 => $value_order2) {
             $json[$i]['0'] = $value_order2['id']; 
             $json[$i]['1'] = $value_order2['vendor_code'];
             $json[$i]['2'] = $value_order2['title'];
             $json[$i]['3'] = $value_order2['count'];
             $json[$i]['5'] = $value_order2['price'];
             $json[$i]['6'] = $order_date;
             $i++;
            }   
            
         
            
             $this->add_order(json_encode($json),0,true);
             //print_r($json);
             
            $date_folder = new DateTime($order_date);
           
            $date_folder      = $date_folder->format('d_m_y');
             
           
              
         }
         
          echo "orders/".$date_folder."/orders_".$category.".zip";
        
       
    }

    function setPaymentStatus($id, $value) {
        $query = "UPDATE `orders` SET `order_payment` = $value WHERE `order_id` = $id";
        $result = $this->pdo->query($query);
        return $result;
    }

    function add_order_dop($json,$category,$provider) {
        print_r($json);
    }
   
    //поиск всех неотгруженных позиций
    function get_orders_items_inprocess() {
        $query = "SELECT `orders`.`order_category`, `orders_items`.`pos_id`, `orders_items`.`order_id`, `orders_items`.`pos_count`, `orders_items`.`pos_count_finish`, `orders_items`.`pos_date` FROM `orders_items` JOIN `orders` ON `orders_items`.`order_id` = `orders`.`order_id` WHERE (`orders`.`order_status` = 1 || `orders`.`order_status` = 0)"; //пока без категории AND `orders_items`.`pos_category` = $id
        $result = $this->pdo->query($query);

        while ($line = $result->fetch()) {
            $array[] = $line;
        }

        return (isset($array)) ? $array : [];
    }

    function createFileOrder($order_id)
    {
        $query = "SELECT * FROM `orders` WHERE `order_id` = $order_id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $orders[] = $line;
        }
        $order = isset($orders) ? $orders[0] : null;
        unset($orders);
        if ($order == null) {
            return null;
        }

        $query = "SELECT * FROM `orders_items` JOIN `pos_items` ON `pos_items`.`id` = `orders_items`.`pos_id` WHERE `orders_items`.`order_id` = $order_id";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $order_items[] = $line;
        }
        if (!isset($order_items)) {
            return null;
        }

        $user_id = intval($_COOKIE['id']);

        //создаем файлы
        $f_name = 'order_'.$order_id;
        if (!file_exists(PATCH_DIR . "/orders/")) {
            mkdir(PATCH_DIR . "/orders/", 0777);
        }
        $excel_name = PATCH_DIR . "/orders/" . $f_name . ".xlsx";
        require_once('excel/Classes/PHPExcel.php');
        require_once('excel/Classes/PHPExcel/IOFactory.php');
        $objPHPExcel = new PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        $imagePath = PATCH_DIR . "/img/head_letter.png";
        $logo = new PHPExcel_Worksheet_Drawing();
        $logo->setPath($imagePath);
        $width = PHPExcel_Shared_Drawing::pixelsToCellDimension($logo->getWidth(),$sheet->getStyle("B2:G2")->getFont());

        //
        $sheet->getColumnDimension('A')->setWidth($width*0.2);
        $sheet->getColumnDimension('B')->setWidth($width*0.1);
        $sheet->getColumnDimension('C')->setWidth($width*0.12);
        $sheet->getColumnDimension('D')->setWidth($width*0.43);
        $sheet->getColumnDimension('E')->setWidth($width*0.1);
        $sheet->getColumnDimension('F')->setWidth($width*0.1);
        $sheet->getColumnDimension('G')->setWidth($width*0.15);
        $sheet->getColumnDimension('H')->setWidth($width*0.2);

        //задаем реквизиты
        $sheet->mergeCells("B2:G2");
        $logo->setCoordinates("B2");
        $logo->setOffsetX(0);
        $logo->setOffsetY(0);
        $logo->setWorksheet($sheet);
        unset($logo);

        //задаем номер заказа
        $sheet->mergeCells("B14:G14");
        $order_date = new DateTime($order['order_date']);
        $order_date = $order_date->format('d.m.Y');
        $sheet->setCellValue("B14", 'Заказ поставщику №' . $order['order_id'] . ' от ' . $order_date . 'г.');
        $styleArray = [
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ]
        ];
        $sheet->getStyle("B14")->applyFromArray($styleArray);

        $sheet->setCellValue("B16", "Номер\nп/п");
        $sheet->setCellValue("C16", "Артикул");
        $sheet->setCellValue("D16", "Наименование");
        $sheet->setCellValue("E16", "Кол-во");
        $sheet->setCellValue("F16", "Ед.изм.");
        $sheet->setCellValue("G16", "Желаемый\nсрок\nпоставки");
        $styleArray = [
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ],
            'borders' => [
                'outline' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'inside' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle("B16:G16")->applyFromArray($styleArray);
        $sheet->getRowDimension(16)->setRowHeight($width*0.52);

        $arr_units = $this->position->getUnits;
        $row = 16;
        $nn = 0;
        foreach ($order_items as $item) {
            $row++;
            $nn++;
            $sheet->setCellValue("B" . $row, $nn);
            $sheet->setCellValue("C" . $row, $item['vendor_code']);
            $sheet->setCellValue("D" . $row, $item['title']);
            $sheet->setCellValue("E" . $row, $item['pos_count']);
            $sheet->setCellValue("F" . $row, $arr_units[$item['unit']]['title']);
            $order_date = new DateTime($item['pos_date']);
            $order_date = $order_date->format('d.m.Y');
            $sheet->setCellValue("G" . $row, $order_date);
            $sheet->getRowDimension($row)->setRowHeight($width*0.4);
        }
        //для всей таблицы
        $styleArray = [
            'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => true,],
            'borders' => ['outline' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000'],], 'inside' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000'],],],
        ];
        $sheet->getStyle("B17:G" . $row)->applyFromArray($styleArray);
        //
        $styleArray = [
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,],
        ];
        $sheet->getStyle("C17:C" . $row)->applyFromArray($styleArray);
        $sheet->getStyle("F17:F" . $row)->applyFromArray($styleArray);
        $sheet->getStyle("G17:G" . $row)->applyFromArray($styleArray);
        //
        //$user_id = 43;
        $contacts = self::CONTACTS;
        $contact = (isset($contacts[$user_id])) ? $contacts[$user_id] : $contacts[0];
        if ($contact['signature'] != '') {
            $imagePath = PATCH_DIR . $contact['signature'];
            $logo = new PHPExcel_Worksheet_Drawing();
            $logo->setPath($imagePath);
            $logo->setCoordinates("F". ($row+4));
            $logo->setOffsetX(0);
            $logo->setOffsetY(0);
            $logo->setHeight($width*0.50);
            $logo->setWorksheet($sheet);
            unset($logo);
        }
        $sheet->setCellValue("B". ($row+4), 'С наилучшими пожеланиями,');
        $sheet->setCellValue("B". ($row+5), $contact['role']);
        $sheet->setCellValue("B". ($row+6), $contact['name']);
        $sheet->setCellValue("B". ($row+7), $contact['mobile']);
        $sheet->setCellValue("B". ($row+8), $contact['phone']);
        $sheet->setCellValue("B". ($row+9), $contact['email']);
        $sheet->setCellValue("B". ($row+10), 'www.promo-bot.ru');
        $sheet->getRowDimension($row+4)->setRowHeight($width*0.20);
        $sheet->getRowDimension($row+5)->setRowHeight($width*0.20);
        $sheet->getRowDimension($row+6)->setRowHeight($width*0.20);
        $sheet->getRowDimension($row+7)->setRowHeight($width*0.20);
        $sheet->getRowDimension($row+8)->setRowHeight($width*0.20);
        $sheet->getRowDimension($row+9)->setRowHeight($width*0.20);
        $sheet->getRowDimension($row+10)->setRowHeight($width*0.20);

        $styleArray = [
            'font' => [
                'name' => 'Calibri',
                'size' => 13,
            ],
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFFFF'],
            ],
        ];
        $sheet->getStyle("A1:H".($row+14))->applyFromArray($styleArray);

        // Save
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($excel_name);

        return $excel_name;
    }

    function __destruct()
    {
        //echo "orders - ";
        // print_r($this ->link_order);
        //echo "<br>";
        // mysql_close($this ->link_order);
    }
}