<?php
$path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']); // определяем директорию скрипта
chdir($path_parts['dirname']); // задаем директорию выполнение скрипта

include 'include/class.inc.php';

$telegramAPI->getUnanswered();

