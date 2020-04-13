<?php

class Task
{
    private $query;
    private $pdo;

    function __construct()
    {

        global $database_server, $database_user, $database_password, $dbase;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $database_user, $database_password, $opt);

    }

    function init()
    {

    }

    function add_task($param)
    {
        $title = $param['title'];

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);

        $query = "INSERT INTO `task` (`id`, `task_title`, `update_date`, `update_user`) VALUES (NULL, '$title', '$date', $user_id)";
        $result = $this->pdo->query($query);
        return $result;
    }

    function get_tasks()
    {

        $query = "SELECT * FROM task ORDER BY `task_title` ASC";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $task_array[] = $line;
        }

        if (isset($task_array))
            return $task_array;
    }

    function edit_task($param)
    {
        $id = $param['id'];
        $title = $param['title'];

        $date = date("Y-m-d H:i:s");
        $user_id = intval($_COOKIE['id']);

        $query = "UPDATE `task` SET `task_title`= '$title', `update_date` = '$date',`update_user` = '$user_id'  WHERE `id` = $id;";
        $result = $this->pdo->query($query);

        return $result;
    }

    function __destruct()
    {

    }
}
