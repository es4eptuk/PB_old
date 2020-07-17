<?php

include 'include/class.inc.php';


$log = print_r(print_r(time()), true);
file_put_contents( 'log__crone.txt', $log . PHP_EOL, FILE_APPEND);


$telegramAPI->getUnanswered();