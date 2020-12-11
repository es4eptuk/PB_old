<?php
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', '1');

include_once ('include/config.inc.php');
include_once (PATCH_DIR . '/vendor/autoload.php');
include_once ('page/dbconnect.php');
include_once ('page/log.php');
include_once ('page/telegram.php');
include_once ('page/mail.php');
include_once ('page/orders.php');
include_once ('page/writeoff.php');
include_once ('page/position.php');
include_once ('page/position_warehouse.php');
include_once ('page/users.php');
include_once ('page/admissions.php');
include_once ('page/robots.php');
include_once ('page/checks.php');
include_once ('page/task.php');
include_once ('page/tickets.php');
include_once ('page/plan.php');
include_once ('page/1c.php');
include_once ('page/settings.php');
include_once ('page/bitrix.php');
include_once ('page/bitrixForm.php');
include_once ('page/statistics.php');

//создание экземпляров
$dbconnect = new Dbconnect();
$log = new Log();
$telegramAPI = new TelegramAPI();
$mail = new Mail();
$orders = new Orders();
$writeoff = new Writeoff();
$position = new Position();
$position_warehouse = new PositionWarehouse();
$user = new User();
$admission = new Admissions();
$robots = new Robots();
$checks = new Checks();
$task = new Task();
$tickets = new Tickets();
$plan = new Plan();
$oneC = new OneC();
$settings = new Settings();
$bitrixAPI = new Bitrix();
$bitrixForm = new BitrixForm();
$statistics = new Statistics();

//инициализация
$log->init();
$telegramAPI->init();
$mail->init();
$orders->init();
$writeoff->init();
$position->init();
$position_warehouse->init();
$user->init();
$admission->init();
$robots->init();
$checks->init();
$task->init();
$tickets->init();
$plan->init();
$oneC->init();
$settings->init();
$bitrixAPI->init();
$bitrixForm->init();
$statistics->init();

if (array_key_exists('REQUEST_URI', $_SERVER)) {
    $i = $_SERVER['REQUEST_URI'];
    //echo $i;
    $i2 = stristr($i, '?', true);
    //echo $i2;
    if ($i2 != "") {
        $i = $i2;
    }
    //echo $i;

    switch ($i) {
        case "/new/api.php":
            break;
        case "/new/telegram.php":
            break;
        case "/new/telegram_r.php":
            break;
        case "/new/self_tester.php":
            break;
        case "/new/b_handler.php":
            break;
        default:
            include 'include/auth.php';
            break;
    }
}

?>