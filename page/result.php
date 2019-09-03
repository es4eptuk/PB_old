<?php 


class Results { 

   private $telegram;
   private $link_results;
   private $robot;
   function __construct()
        {
            
        global $database_server, $database_user, $database_password, $dbase;
    	
       $this->link_results  = mysql_connect($database_server, $database_user, $database_password)
        or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8',$this->link_results);
                //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
         $this -> telegram = new TelegramAPI;
         $this -> robot = new Robots;
        }
        
    function sendRobot($dateStart,$dateEnd,$version) {
        $query = "SELECT robots.id, robots.version, robots.number, robots.name FROM `robots` INNER JOIN `check` ON robots.id = check.robot WHERE check.id_check = 105 AND check.update_date >= '$dateStart' AND check.update_date <= '$dateEnd' AND robots.remont = 0  AND version = $version";
        //echo $query;
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $n = 0;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
         
        $send_robot_array[] = $line; 
        
        }

    	if (isset($send_robot_array))
    	return $send_robot_array;
    }   
    
    function finishRobot($month) {
        $query = "SELECT * FROM `check` WHERE `id_check` = 104 AND `check` = 1 AND `update_date` >= '2018-$month-01 00:00:00'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $n = 0;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $n++;    
       // $send_robot_array[] = $line; 
        $info_robot = $this->robot->get_info_robot($line['robot']);
        $finish_robot_array[$n]['robot'] = $info_robot['version'].".".$info_robot['number'];
        $finish_robot_array[$n]['date'] = $line['update_date'];
        }

    	if (isset($finish_robot_array))
    	return $finish_robot_array;
    }   
    
    
    function finishMH($month) {
        $query = "SELECT * FROM `check` WHERE `id_check` = 49 AND `check` = 1 AND `update_date` >= '2018-$month-01 00:00:00' ORDER BY `robot` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $n = 0;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $n++;    
       // $send_robot_array[] = $line; 
        $info_robot = $this->robot->get_info_robot($line['robot']);
        $finish_MH_karkas_array[$line['robot']]['robot'] = $info_robot['version'].".".$info_robot['number'];
        $finish_MH_karkas_array[$line['robot']]['date'] = $line['update_date'];
        $finish_karkas[] = $info_robot['version'].".".$info_robot['number'];
        }
        
        $query = "SELECT * FROM `check` WHERE `id_check` = 114 AND `check` = 1 AND `update_date` >= '2018-$month-01 00:00:00' ORDER BY `robot` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $n = 0;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $n++;    
       // $send_robot_array[] = $line; 
        $info_robot = $this->robot->get_info_robot($line['robot']);
        $finish_MH_ruki_array[$line['robot']]['robot'] = $info_robot['version'].".".$info_robot['number'];
        $finish_MH_ruki_array[$line['robot']]['date'] = $line['update_date'];
        $finish_ruki[] = $info_robot['version'].".".$info_robot['number'];
        }
        
        $query = "SELECT * FROM `check` WHERE `id_check` = 115 AND `check` = 1 AND `update_date` >= '2018-$month-01 00:00:00' ORDER BY `robot` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $n = 0;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $n++;    
       // $send_robot_array[] = $line; 
        $info_robot = $this->robot->get_info_robot($line['robot']);
        $finish_MH_kofr_array[$line['robot']]['robot'] = $info_robot['version'].".".$info_robot['number'];
        $finish_MH_kofr_array[$line['robot']]['date'] = $line['update_date'];
        $finish_kofr[] = $info_robot['version'].".".$info_robot['number'];
        }


        $result = array_intersect($finish_karkas, $finish_ruki,$finish_kofr);
        
    	
    	return $result;
    }   
    
     function finishHP($month) {
        $query = "SELECT * FROM `check` WHERE `id_check` = 50 AND `check` = 1 AND `update_date` >= '2018-$month-01 00:00:00'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $n = 0;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $n++;    
       // $send_robot_array[] = $line; 
        $info_robot = $this->robot->get_info_robot($line['robot']);
        $finish_HP_array[$n]['robot'] = $info_robot['version'].".".$info_robot['number'];
        $finish_HP_array[$n]['date'] = $line['update_date'];
        }

    	if (isset($finish_HP_array))
    	return $finish_HP_array;
    }  
    
    function finishBD($month) {
        $query = "SELECT * FROM `check` WHERE `id_check` = 52 AND `check` = 1 AND `update_date` >= '2018-$month-01 00:00:00'";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        $n = 0;
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $n++;    
       // $send_robot_array[] = $line; 
        $info_robot = $this->robot->get_info_robot($line['robot']);
        $finish_BD_array[$n]['robot'] = $info_robot['version'].".".$info_robot['number'];
        $finish_BD_array[$n]['date'] = $line['update_date'];
        }

    	if (isset($finish_BD_array))
    	return $finish_BD_array;
    }  
    
    
    
    function __destruct() {
       // echo "robots - ";
        //print_r($this ->link_robots);
        //echo "<br>";
        //mysql_close($this ->link_robots);
    }
}


$results = new Results; 