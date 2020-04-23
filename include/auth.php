<?php
include_once ('include/config.inc.php');


// Соединямся с БД
$link=mysqli_connect($database_server , $database_user , $database_password , $dbase );
$link->set_charset("utf8");
if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
{
    $query = mysqli_query($link, "SELECT *,INET_NTOA(user_ip) AS user_ip FROM users WHERE user_id = '".intval($_COOKIE['id'])."' LIMIT 1");
    $userdata = mysqli_fetch_assoc($query);

    if(($userdata['user_hash'] !== $_COOKIE['hash']) or ($userdata['user_id'] !== $_COOKIE['id']))
    {
        setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie("hash", "", time() - 3600*24*30*12, "/");
        print "Хм, что-то не получилось";
		header("Location: login.php"); exit();
    }
    
}
else
{
    header("Location: login.php"); exit();
}
?>