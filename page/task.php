<?php 

class Task { 
  
    function __construct()
        {
            
        global $database_server, $database_user, $database_password, $dbase;
    	
        $this->link_task = mysql_connect($database_server, $database_user, $database_password)
        or die('Не удалось соединиться: ' . mysql_error());
        
        
        mysql_set_charset('utf8',$this->link_task);
                //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
     
        }
        
        function add_task($param) {
            $title = $param['title'];
            
            $date = date("Y-m-d H:i:s");
            $user_id = intval($_COOKIE['id']);

            $query = "INSERT INTO `task` (`id`, `task_title`, `update_date`, `update_user`) VALUES (NULL, '$title', '$date', $user_id)";
            $result = mysql_query($query) or die($query);
            return $result;
        }
    
     function get_tasks() {

        $query = "SELECT * FROM task ORDER BY `task_title` ASC";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $task_array[] = $line; 
        }
        
    	if (isset($task_array))
    	return $task_array;
    }

     function edit_task($param) {
        $id = $param['id'];
        $title = $param['title'];

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        
        $query = "UPDATE `task` SET `task_title`= '$title', `update_date` = '$date',`update_user` = '$user_id'  WHERE `id` = $id;";
        $result = mysql_query($query) or die(mysql_error());

    	return $result;
    }

    function __destruct() {
       
    }
} 

$task = new Task; 
