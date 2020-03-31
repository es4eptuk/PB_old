<?php
include '/var/www/promobot/data/www/db.promo-bot.ru/new/include/config.inc.php';
include '/var/www/promobot/data/www/db.promo-bot.ru/new/page/mail.php';


global $database_server, $database_user, $database_password, $dbase;
$dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
$pdo = new PDO($dsn, $database_user, $database_password, $opt);
$mail = new Mail;

$id_pos = 125;
//$pos_assambly = $position->get_pos_in_assembly($id_pos);

$query = "SELECT robots.number, tickets.description, tickets.status, tickets.update_date FROM `tickets` INNER JOIN robots ON tickets.robot = robots.id WHERE robots.name LIKE '%Advanced Robotics%' AND robots.progress = 100 AND tickets.status !=6";
$result = $pdo->query($query);
$out = "";

$out.= "<table border=1 cellpadding='7'>";
while ($line = $result->fetch()) {

    switch ($line['status']) {
        case 3:
            $line['status'] = "Solved";
            break;
        case 7:
            $line['status'] =  "Paused";
            break;
        case 1:
            $line['status'] =  "New";
            break;
        case 2:
            $line['status'] =  "In Process";
            break;
        case 4:
            $line['status'] =  "Awaiting Repair";
            break;
        case 5:
            $line['status'] =  "Shipping Parts";
            break;
    }

    $out.= "<tr><td>4.".$line['number']."</td><td>".$line['description']."</td><td>".$line['status']."</td><td>".$line['update_date']."</td></tr>";

}
$out.= "</table>";

echo $out;
$today = date("d.m.Y");

$toArray = [
    "cto@promo-bot.ru",
    "dega.ag@gmail.com",
    "andreas.valentini@advanced-robotics.ch",
    "dan.rotaru@advanced-robotics.ch",
    "support@promo-bot.ru",
    "dir@promo-bot.ru"
];




foreach ($toArray as &$to) {
    $mail->send('Users',  $to , 'Tickets on '.$today, $out);

}



?>