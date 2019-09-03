<?php 


class User { 
    private $link_user;
    
    function __construct() {
        global $database_server, $database_user, $database_password, $dbase;
    	
        $this->link_user  = mysql_connect($database_server, $database_user, $database_password)
        or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8',$this->link_user);
                //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
             
    }
    
   public function get_info_user($id) {
     
        
        $query_user = "SELECT * FROM users WHERE user_id='$id'";
        $result_user = mysql_query($query_user) or die('Запрос не удался: ' . mysql_error());
        
        while( $line_user = mysql_fetch_array($result_user, MYSQL_ASSOC)){
        $usr_array[] = $line_user; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result_user);
        //print_r($link_user);
        
    	if (isset($usr_array))
    	return $usr_array['0'];  
        
        
    }
    
    function get_users($group) {
        
            if($group!=0) {
                $where = "WHERE `group` = $group";
            } else {
                
                $where = "";
            }
            $query = 'SELECT * FROM users '.$where.' ORDER BY `user_name` ASC';
            $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
            
            while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
            $users_array[] = $line; 
            }
            
            // Освобождаем память от результата
            mysql_free_result($result);
          
        	if (isset($users_array))
        	return $users_array;
        }
    
}

    function __destruct() {
        //echo "user - ";
        //print_r($this ->link_user);
        //echo "<br>";
        //mysql_close($this ->link_user);
    }

$user = new User; 