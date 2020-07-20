<?php


class User
{
    private $query;
    private $pdo;

    public $getGroups;

    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase, $dbconnect;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        //$this->pdo = new PDO($dsn, $database_user, $database_password, $opt);
        $this->pdo = &$dbconnect->pdo;
    }

    function init()
    {
        //список групп
        $query = "SELECT * FROM `users_group` ORDER BY `id` ASC";
        $result = $this->pdo->query($query);
        $groups[0] = [
            'id' => 0,
            'title' => 'Не в группе',
        ];
        while ($line = $result->fetch()) {
            $groups[$line['id']] = $line;
        }
        $this->getGroups = (isset($groups)) ? $groups : [];
    }

    public function get_info_user($id)
    {
        $query = "SELECT * FROM users WHERE user_id='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $users[] = $line;
        }
        return (isset($users)) ? $users['0'] : [];
    }

    //все пользователи
    function get_users($group = 0)
    {
        if ($group != 0) {
            $where = "WHERE `group` = $group";
        } else {
            $where = "";
        }
        $query = 'SELECT * FROM users ' . $where . ' ORDER BY `user_name` ASC';
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $users_array[$line['user_id']] = $line;
        }

        return (isset($users_array)) ? $users_array : [];
    }

    //создать пользователя
    function add_user($login, $password, $name, $email, $group)
    {
        $login = trim($login);
        $password = md5(md5(trim($password)));
        $err = '';
        // проверям поля
        if(!preg_match("/^[a-zA-Z0-9]+$/",$login)) {
            $err = "Логин может состоять только из букв английского алфавита и цифр";
        }
        if(!(strlen($login) > 3 && strlen($login) < 30)) {
            $err = "Логин должен быть не меньше 3-х символов и не больше 30";
        }
        //проверка плогина
        $users = [];
        $query = "SELECT user_id FROM users WHERE user_login='$login'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $users[] = $line;
        }
        if (count($users) != 0) {
            $err = "Пользователь с таким логином уже существует в базе данных";
        }
        if ($err != '') {
            return ['result' => false, 'err' => $err];
        }

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "INSERT INTO `users` (`user_id`, `user_login`, `user_password`, `user_name`, `user_email`, `group`, `update_user`, `update_date` ) VALUES (NULL, '$login', '$password', '$name', '$email', '$group', '$user_id', '$date')";
        $result = $this->pdo->query($query);
        //$idd = $this->pdo->lastInsertId();
        return ['result' => true, 'err' => ''];
    }

    //редактировать пользователя
    function edit_user($id, $name, $email, $telegram, $group)
    {
        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);
        $query = "UPDATE `users` SET `user_name` = '$name', `user_email` = '$email', `telegramId` = '$telegram', `group` = '$group', `update_user` = '$user_id', `update_date` = '$date' WHERE `user_id` = $id;";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    //удалить пользователя
    function del_user($id)
    {
        $query = "DELETE FROM `users` WHERE `user_id` = $id";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    function __destruct()
    {

    }

}


