<?
include 'include/class.inc.php';

$chat_id = "-1001265028666";
$name = 123622748;
$text = "dgjhg's";


$param['chatid'] = $chat_id;
$param['author'] = $name;
$param['message'] = $text;
$param['title'] = "13123";
//$telegramAPI->writeMessageDb($param);
//$telegramAPI->getUnanswered();
//$telegramAPI->writeLog("23435");
//$date    = date("d.m.Y");
//echo date("N")."<br>";
//$date = "14.09.2019";

//echo $telegramAPI->isNight("2019-09-16 21:43:25");
$str ='O:27:"Telegram\Bot\Objects\Update":1:{s:8:" * items";a:2:{s:9:"update_id";i:778346875;s:7:"message";O:28:"Telegram\Bot\Objects\Message":1:{s:8:" * items";a:7:{s:10:"message_id";i:12573;s:4:"from";O:25:"Telegram\Bot\Objects\User":1:{s:8:" * items";a:6:{s:2:"id";i:154985324;s:6:"is_bot";b:0;s:10:"first_name";s:5:"David";s:9:"last_name";s:10:"Chagalidze";s:8:"username";s:10:"chagalidze";s:13:"language_code";s:2:"ru";}}s:4:"chat";O:25:"Telegram\Bot\Objects\Chat":1:{s:8:" * items";a:4:{s:2:"id";i:-340755128;s:5:"title";s:4:"Test";s:4:"type";s:5:"group";s:30:"all_members_are_administrators";b:1;}}s:4:"date";i:1569309359;s:20:"new_chat_participant";O:25:"Telegram\Bot\Objects\User":1:{s:8:" * items";a:4:{s:2:"id";i:425739306;s:6:"is_bot";b:0;s:10:"first_name";s:5:"Danil";s:8:"username";s:6:"Dnlldn";}}s:15:"new_chat_member";a:4:{s:2:"id";i:425739306;s:6:"is_bot";b:0;s:10:"first_name";s:5:"Danil";s:8:"username";s:6:"Dnlldn";}s:16:"new_chat_members";a:1:{i:0;a:4:{s:2:"id";i:425739306;s:6:"is_bot";b:0;s:10:"first_name";s:5:"Danil";s:8:"username";s:6:"Dnlldn";}}}}}}';
$arr = unserialize($str);
print_r($arr);


?>

