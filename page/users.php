<?php 


class User {
    private $query;
    private $pdo;
    
    function __construct() {
        global $database_server, $database_user, $database_password, $dbase;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);
             
    }
    
   public function get_info_user($id) {
     
        
        $query_user = "SELECT * FROM users WHERE user_id='$id'";


        $result_user = $this->pdo->query($query_user);
        while ($line_user = $result_user->fetch()) {
        $usr_array[] = $line_user; 
        }
        
    	if (isset($usr_array))
    	return $usr_array['0'];  
        
        
    }
    
    function get_users($group=0) {
        
            if($group!=0) {
                $where = "WHERE `group` = $group";
            } else {
                
                $where = "";
            }
            $query = 'SELECT * FROM users '.$where.' ORDER BY `user_name` ASC';
            $result = $this->pdo->query($query);
            while ($line = $result->fetch()) {
            $users_array[] = $line; 
            }

          
        	if (isset($users_array))
        	return $users_array;
    }

    function __destruct() {

    }
    
}


