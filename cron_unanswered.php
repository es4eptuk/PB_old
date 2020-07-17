<?php
$dd = date('Y-m-d H:i:s');
$log = print_r($dd, true);
file_put_contents( 'log__crone.txt', $log . PHP_EOL, FILE_APPEND);

include 'include/class.inc.php';

$telegramAPI->getUnanswered();