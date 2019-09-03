<?php 

class Plan { 
    
   private $link_plan;  
   function __construct()
        {
            
        global $database_server, $database_user, $database_password, $dbase;
    	
        $this->link_plan = mysql_connect($database_server, $database_user, $database_password)
        or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8',$this->link_plan);
                //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
         //$this -> telegram = new TelegramAPI;
         //$this -> robot = new Robots;
        }

    function get_head () {
       
        global $out, $out2;
    	$monthes = array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
    
       
    
        $query = "SELECT * FROM `plan` WHERE `hidden` != 1";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $out = "";
        $out2 = "";
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
         $out .= '<th colspan="4"><b>'.$monthes[$line['month']].'</b></th>';
         $out2 .= '<th><b>к-во роботов</b></td>
                    <th><b>надо</b></th>
                    <th><b>есть</b></th>
                    
                    <th><b>статус</b></th>';
        }
         
      
    
    }
    
    function get_month () {
       
        global $out, $out2;
    	$monthes = array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
    	
    
        $query = "SELECT * FROM `plan` ";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_month[] = $line;
        
        }
         
      
        if (isset($pos_month))
        return $pos_month;
    
    }
    
    function get_month_hidden () {
        
        global $out, $out2;
    	$monthes = array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
    	
        $query = "SELECT * FROM `plan` WHERE `hidden` != 1 ";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_month[] = $line;
        
        }
         
        
        if (isset($pos_month))
        return $pos_month;
    
    }
    
    function get_ordered_items($id) {
        
    
        $query = "SELECT `pos_count_finish`, `pos_count`, `pos_id` FROM `orders_items` WHERE `pos_id` = $id";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $total = 0;
        $total_finish = 0;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        //echo $line['pos_count'];
        $total = $total + $line['pos_count'];
        if ($line['pos_count_finish'] >$line['pos_count']) {
            $count_finish = $line['pos_count'];
        } else {
             $count_finish = $line['pos_count_finish'];
            
        }
        
        $total_finish = $total_finish + $count_finish;
 
        }
       // echo $total;
        $count = $total - $total_finish;
        
        return $count;
        
    }
    
    function get_ordered_items_info($id) {
        $query = "SELECT * FROM `orders_items` WHERE `pos_id` = $id AND pos_count_finish < pos_count ORDER BY `pos_date` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $total = 0;
        $total_finish = 0;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
           
                if ($line['pos_count_finish'] != $line['pos_count']) {
                  $line['pos_count'] =  $line['pos_count'] - $line['pos_count_finish'];
                  $ordered_info[] = $line;
                }
        }
      
       
        if (isset($ordered_info))
        return $ordered_info;
        
    }
    
     function get_robot_inprocess() {
        $query = "SELECT * FROM `robots` WHERE `writeoff` = 0 AND `remont` = 0 AND `delete` = 0 AND `progress` != 100";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
          while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
              $year = date('Y', strtotime($line['date']));
              $month = date('m', strtotime($line['date']));
              $day = date('d', strtotime($line['date']));
              $id = $line['id'];
              $version = $line['version'];
              $date = $year.".".$month;
              if (!isset($robot[$date][$version])){$robot[$date][$version] = 0;}
              $robot[$date][$version]++;
        }
            return $robot;
    }


function get_operation($id_pos) {
$query = "SELECT id_kit, count FROM `pos_kit_items` WHERE `id_pos` = $id_pos";
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
       
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $id_kit0 = $line['id_kit'];
            $kit_array_count[$id_pos][$id_kit0] = $line['count'];
            $kit_array[] = $line;
        }

         if(isset($kit_array)) {
           
            $cnt = 0;
         foreach ($kit_array as $value) {
            $id = $value['id_kit'];
            $query = "SELECT robots.version, robots.number, robots.date, check.operation FROM `check` JOIN robots ON check.robot = robots.id WHERE `id_kit` = $id AND `check` = 0 AND robots.delete = 0 AND robots.progress != 100 ORDER BY `robots`.`date` ASC";
            //echo $query."<br><br>";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $year = date('Y', strtotime($line['date']));
                $month = date('m', strtotime($line['date']));
                $day = date('d', strtotime($line['date']));
                $date = $year.".".$month;
                if (!isset($operation_array[$date]['count'])){$operation_array[$date]['count'] = 0;}
                $operation_array[$date]['count'] += $kit_array_count[$id_pos][$id];
                $operation_array[$date]['robots'][] = $line['operation']." - ".$line['version'].".".$line['number']."(".$kit_array_count[$id_pos][$id].")";
                //$operation_array[$cnt]['version'] = $line['version'];
               // $operation_array[$cnt]['number'] = $line['number'];
                //$operation_array[$cnt]['operation'] = $line['operation'];
                //$operation_array[$cnt]['date'] = $line['date'];
                //$operation_array[$cnt]['count'] = $kit_array_count[$id_pos][$id];  
                $cnt++;
            }
            
         }   
if (isset($operation_array)) return $operation_array;

//print_r($operation_array); echo "<br><br>";


//print_r($pos_array);
}
    
}

function get_operation_assembly($id_pos) {
    
    $query = "SELECT * FROM `pos_assembly_items` JOIN pos_items ON pos_assembly_items.id_assembly = pos_items.assembly WHERE `id_pos` = $id_pos";
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
          
            $assembly_array[] = $line;
        }
if (isset($assembly_array)) {
foreach ($assembly_array as $value) {
    $id_pos_ass = $value['id'];
    $count = $value['count'];
    
    $query = "SELECT id_kit, count FROM `pos_kit_items` WHERE `id_pos` = $id_pos_ass";
    //echo $query."<Br> ";
     $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
       
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $id_kit0 = $line['id_kit'];
            
            $line['count'] = $line['count']*$count;
           if (!isset($kit_array_count[$id_pos][$id_kit0])) $kit_array_count[$id_pos][$id_kit0] = 0;
            $kit_array_count[$id_pos][$id_kit0] = $line['count'];
            $kit_array[] = $line;
        }
     
 }
}

         if(isset($kit_array)) {
           
            $cnt = 0;
         foreach ($kit_array as $value) {
            $id = $value['id_kit'];
            $query = "SELECT robots.version, robots.number, robots.date, check.operation FROM `check` JOIN robots ON check.robot = robots.id WHERE `id_kit` = $id AND `check` = 0 AND robots.delete = 0 AND robots.progress != 100 ORDER BY `robots`.`date` ASC";
            //echo $query."<br><br>";
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                //echo $id_pos." - ".$id. " ";
                $year = date('Y', strtotime($line['date']));
                $month = date('m', strtotime($line['date']));
                $day = date('d', strtotime($line['date']));
                $date = $year.".".$month;
                
                if (!isset($operation_array[$date]['count'])){$operation_array[$date]['count'] = 0;}
                $operation_array[$date]['count'] += $kit_array_count[$id_pos][$id];
                $operation_array[$date]['robots'][] = $line['operation']." - ".$line['version'].".".$line['number']."(".$kit_array_count[$id_pos][$id].")";
                //$operation_array[$cnt]['version'] = $line['version'];
               // $operation_array[$cnt]['number'] = $line['number'];
                //$operation_array[$cnt]['operation'] = $line['operation'];
                //$operation_array[$cnt]['date'] = $line['date'];
                //$operation_array[$cnt]['count'] = $kit_array_count[$id_pos][$id];  
                $cnt++;
            }
            
         }   
if (isset($operation_array)) return($operation_array);;

}
    
}

function get_summ_onrobot($id_pos) {
    $query = "SELECT SUM(count) FROM `pos_kit_items` WHERE `id_pos` = $id_pos";
    $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $line = mysql_fetch_array($result, MYSQL_ASSOC);
    
    $summ=$line['SUM(count)'];
    return $summ;
}



function __destruct() {
        //echo "plan - ";
        //print_r($this ->link_plan);
        //echo "<br>";
        //mysql_close($this ->link_plan);
    }
    
}
$plan = new Plan; 