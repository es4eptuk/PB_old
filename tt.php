<? 
include 'include/config.inc.php';
$term = $_GET['term'];

$output = '';
        $str_arr = array();
        global $database_server, $database_user, $database_password, $dbase;
    	
    	$link = mysql_connect($database_server, $database_user, $database_password)
        or die('Не удалось соединиться: ' . mysql_error());
        mysql_set_charset('utf8',$link);
        //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
        $query = "SELECT id,title,vendor_code,assembly FROM pos_items WHERE (title LIKE '%$term%' OR vendor_code LIKE '%$term%')";
        
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
            $pos_array[] = $line; 
            }
         
        if (isset($pos_array)) {  
            
            foreach ($pos_array as $row) {
                array_push($str_arr, "\"". $row['id'] ."::" . $row['vendor_code'] ."::" . $row['title'] . "\"");
                
                //array_push($str_arr, $row['title']);
                 //$str_arr[] =  "1111";
            }
            
            
            
            $s = "[".implode(",", $str_arr)."]";
            echo $s;
        }
        
        ?>