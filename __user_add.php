<?php
include 'include/config.inc.php';
// Страница регистрации нового пользователя

// Соединямся с БД
$link=mysqli_connect($database_server , $database_user , $database_password , $dbase );
$link->set_charset("utf8");
if(isset($_POST['submit']))
{
    $err = [];

    // проверям логин
    if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login']))
    {
    	echo $_POST['login'];
        $err[] = "Логин может состоять только из букв английского алфавита и цифр";
    }

    if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)
    {
        $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
    }

    // проверяем, не сущестует ли пользователя с таким именем
    $query = mysqli_query($link, "SELECT user_id FROM users WHERE user_login='".mysqli_real_escape_string($link, $_POST['login'])."'");
    if(mysqli_num_rows($query) > 0)
    {
        $err[] = "Пользователь с таким логином уже существует в базе данных";
    }

    // Если нет ошибок, то добавляем в БД нового пользователя
    if(count($err) == 0)
    {

        $login = $_POST['login'];
        $name = $_POST['name'];
		$email = $_POST['email'];
		
        // Убераем лишние пробелы и делаем двойное хеширование
        $password = md5(md5(trim($_POST['password'])));

        mysqli_query($link,"INSERT INTO users SET user_login='".$login."', user_password='".$password."', user_name='".$name."', user_email='".$email."'");
        header("Location: login.php"); exit();
    }
    else
    {
        print "<b>При регистрации произошли следующие ошибки:</b><br>";
        foreach($err AS $error)
        {
            print $error."<br>";
        }
    }
}
?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<title>Регистрация пользователя</title>
</head>
<body>
<form method="POST" accept-charset="UTF-8">
Логин <input name="login" type="text" required><br>
Пароль <input name="password" type="password" required><br>
Имя <input name="name" type="text" required><br>
Email <input name="email" type="text" required><br>
<input name="submit" type="submit" value="Зарегистрироваться">
</form>
</body>
</html>