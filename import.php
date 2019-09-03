<?php 
include 'include/class.inc.php';


 $query = "SELECT * FROM pos_items WHERE version=2";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
        
        while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos_array[] = $line; 
        }
        
        // Освобождаем память от результата
        mysql_free_result($result);
	
    	if (isset($pos_array)) {
    	    
    	    foreach ($pos_array as &$value) {
    	        $id = $value['id'];
                $count = $value['quant_robot'];
              	$query = "INSERT INTO `robot_equipment_items` (`id`, `pos_id`, `equipment_id`, `count`) VALUES (NULL, $id , '5', $count)";
              	//echo $query."<br>";
               // $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
              
            }
    	    
    
    	    
    	}
    

?>