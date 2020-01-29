<?
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', '1');


include 'include/config.inc.php';
include 'page/log.php';
include 'page/telegram.php';
include 'page/mail.php';
include 'page/orders.php';
include 'page/writeoff.php';
include 'page/position.php';
include 'page/users.php';
include 'page/admissions.php';
include 'page/robots.php';
include 'page/checks.php';
include 'page/task.php';
include 'page/tickets.php';
include 'page/plan.php';
include 'page/1c.php';
include 'page/settings.php';

$i = $_SERVER['REQUEST_URI'];
//echo $i;
$i2 = stristr($i, '?', true);


if ($i2 = "") {$i = $i2;}
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
    default:
        include 'include/auth.php'; 
        break;
}


?>