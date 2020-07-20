<?php
$path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']); // определяем директорию скрипта
chdir($path_parts['dirname']); // задаем директорию выполнение скрипта

//
$dd = date('Y-m-d H:i:s --- '. $path_parts['dirname']);
//$log = print_r($_SERVER, true);
//file_put_contents( 'log__crone.txt', $log . PHP_EOL, FILE_APPEND);
//
/*$i = $_SERVER['REQUEST_URI'];
$i2 = stristr($i, '?', true);
$log = print_r($i.'/'.$i2, true);
file_put_contents( 'log__crone.txt', $log . PHP_EOL, FILE_APPEND);*/

include 'include/class.inc.php';

$log = print_r($dd, true);
file_put_contents( 'log__crone.txt', $log . PHP_EOL, FILE_APPEND);

$telegramAPI->getUnanswered();

