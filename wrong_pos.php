<?php 
error_reporting(0); 
include 'include/class.inc.php';
 $query = "SELECT * FROM pos_kit_items WHERE 1";
 $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
 $cnt = 0;
 while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
    $id1 =  $line['id_pos'];
    $pos_kit_array[$id1]['count'] += $line['count']; 
    $pos_kit_array[$id1]['idd'] = $id1; 
    
 }
 
 //print_r($pos_kit_array);
 $query = "SELECT pos_items.id, pos_items.title, pos_items.category, pos_items.vendor_code,robot_equipment_items.count  FROM robot_equipment_items JOIN pos_items ON robot_equipment_items.pos_id = pos_items.id WHERE `equipment_id` = 4";
 $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
 $cnt = 0;
 while( $line = mysql_fetch_array($result, MYSQL_ASSOC)){
    $pos_eq_array[] = $line; 
 }
 
 
  foreach ($pos_eq_array as $key => $value) {
      $id = $value['id'];
      
      $info_pos = $position->get_info_pos($id);
      
      $query3 = "SELECT * FROM `pos_kit_items` WHERE `id_pos` = $id";
      $result3 = mysql_query($query3) or die('Запрос не удался: ' . mysql_error());
      $str = "";
      
      while( $line3 = mysql_fetch_array($result3, MYSQL_ASSOC)){
            $k_arr[] = $line3; 
            //print_r($line3);
            $link = "<a href='edit_kit.php?id=".$line3['id_kit']."' target='_blank'>Комлект №".$line3['id_kit']."</a><br>   ";
            //echo $link;
            $str .= $link;
         }
         //echo $str;
      $pos_new[$id]['str_kit'] = $str;
      $pos_new[$id]['code'] = $info_pos['vendor_code'];
      $pos_new[$id]['title'] = $info_pos['title'];
      $pos_new[$id]['category'] = $info_pos['category'];
      
      if ( $pos_new[$id]['title']== "") {
          //echo $pos_kit_array[$key]['idd']."==";
          $iddd= $pos_kit_array[$key]['idd'];
          $info_pos2 = $position->get_info_pos($iddd);
          $pos_new[$id]['code'] = $info_pos2['vendor_code'];
          $pos_new[$id]['title'] = $info_pos2['title'];
      };
      
      //$pos_new[$id]['title2'] = $value['title'];
      $pos_new[$id]['eq'] = $value['count'];
      $pos_new[$id]['kit'] = $pos_kit_array[$id]['count'];
      $pos_new[$id]['idd_kit'] = $pos_kit_array[$id]['idd'];
      $pos_new[$id]['idd_eq'] = $id;
  }
  
  //print_r($pos_new);
  
        echo "<table border=1>";
        echo "<tr><td><b>ID</b></td><td><b>Артикул</b></td><td><b>Наименование</b></td><td><b>В комплектах</b></td><td><b>В комплектации</b></td></tr>";

        //print_r($pos_kit_array);
        foreach ($pos_new as $key => $value) {
           // echo $value['eq']." ";
           $class = "";
           if ($value['kit']!=$value['eq']) {
               //$class = "color: #ff0404; font-weight: bold;";
          
               echo "<tr style='$class'><td>".$value['idd_eq']."</td><td>".$value['code']."</td><td>".$value['title']."</td><td>".$value['kit']."</td><td>".$value['eq']."</td><td>".$value['str_kit']."</td></tr>";
        }
        }
       
        echo "</table>";
 
 $ar = $position->get_info_pos(202);
 print_r ($ar);
 
?>