<?php 
include 'include/config.inc.php';
global $database_server, $database_user, $database_password, $dbase;
    	
        $link_pos = mysql_connect($database_server, $database_user, $database_password)
        or die('Не удалось соединиться: ' . mysql_error());
        
        
        mysql_set_charset('utf8',$link_pos);
                //echo 'Соединение успешно установлено';
        mysql_select_db($dbase) or die('Не удалось выбрать базу данных');
// Подключаем класс для работы с excel
require_once('excel/Classes/PHPExcel.php');
require_once 'excel/Classes/PHPExcel/IOFactory.php';
$path = "inv/mh_07_12_18.xlsx";
$objPHPExcel = PHPExcel_IOFactory::load($path);
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;
   
    for ($row = 2; $row <= $highestRow; ++ $row) {
        $cell = $worksheet->getCellByColumnAndRow(1, $row);
        $val = $cell->getValue();
            if((substr($val,0,1) === '=' ) && (strlen($val) > 1)){
                $val = $cell->getOldCalculatedValue();
               
            } 
           $art = $val;  
            
            
         //echo $val." || ";
         
          $cell = $worksheet->getCellByColumnAndRow(8, $row);
        $val = $cell->getValue();
            if((substr($val,0,1) === '=' ) && (strlen($val) > 1)){
                $val = $cell->getOldCalculatedValue();
            } 
            
            $total = $val;
            
         //echo $val." <br> ";
         
        $query = "UPDATE `pos_items` SET  `total` = '$total' WHERE `vendor_code` LIKE \"%$art%\"   ";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
       // mysql_free_result($result);
        echo $query."<br> ";
       
    }
  
}