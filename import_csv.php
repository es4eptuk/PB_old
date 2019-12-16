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
$path = "import/izgotovlenie.xlsx";
$objPHPExcel = PHPExcel_IOFactory::load($path);
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;
   
    for ($row = 2; $row <= 190; ++ $row) {
        $cell = $worksheet->getCellByColumnAndRow(0, $row);
        $val = $cell->getValue();
            if((substr($val,0,1) === '=' ) && (strlen($val) > 1)){
                $val = $cell->getOldCalculatedValue();
               
            } 
           $title = $val;
            
            
         //echo $val." || ";
         
        $cell = $worksheet->getCellByColumnAndRow(1, $row);
        $val = $cell->getValue();
            if((substr($val,0,1) === '=' ) && (strlen($val) > 1)){
                $val = $cell->getOldCalculatedValue();
            }

            $count = $val;

        $cell = $worksheet->getCellByColumnAndRow(2, $row);
        $val = $cell->getValue();
        if((substr($val,0,1) === '=' ) && (strlen($val) > 1)){
            $val = $cell->getOldCalculatedValue();
        }

        $longtitle = $val;

        $cell = $worksheet->getCellByColumnAndRow(3, $row);
        $val = $cell->getValue();
        if((substr($val,0,1) === '=' ) && (strlen($val) > 1)){
            $val = $cell->getOldCalculatedValue();
        }

        $cat = $val;

        if ($cat == "Токарка, фрезеровка") $cat = 2;
        if ($cat == "Резка, гибка металл") $cat = 3;
        if ($cat == "Поставка изделий") $cat = 1;

        $maxId = generate_art();
        $art = "MH-".$maxId['max(id)'];
        $price = 0;
        //echo $val." <br> ";
        $date    = date("Y-m-d H:i:s");
        $query = "UPDATE `promobot_db2`.`pos_items` SET `longtitle` = '$longtitle' WHERE `pos_items`.`title` LIKE '$title'";
        // $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

        echo $query."<br> ";
       
    }
  
}

function generate_art()
{
    $query = "SELECT max(id) FROM `pos_items`";
    $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $art_array = $line;
    }
    // Освобождаем память от результата
    mysql_free_result($result);
    if (isset($art_array))
        return $art_array;
}