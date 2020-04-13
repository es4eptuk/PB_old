<?php

class Plan
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

    function get_ordered_items($id)
    {

        $query = "SELECT `pos_count_finish`, `pos_count`, `pos_id` FROM `orders_items` WHERE `pos_id` = $id";
        $result = $this->pdo->query($query);
        $total = 0;
        $total_finish = 0;
        while ($line = $result->fetch()) {
            //echo $line['pos_count'];
            $total = $total + $line['pos_count'];
            if ($line['pos_count_finish'] > $line['pos_count']) {
                $count_finish = $line['pos_count'];
            } else {
                $count_finish = $line['pos_count_finish'];

            }

            $total_finish = $total_finish + $count_finish;

        }
        $count = $total - $total_finish;

        return $count;

    }

    function get_ordered_items_info($id)
    {
        $query = "SELECT * FROM `orders_items` WHERE `pos_id` = $id AND pos_count_finish < pos_count ORDER BY `pos_date` ASC";
        $result = $this->pdo->query($query);
        $total = 0;
        $total_finish = 0;
        while ($line = $result->fetch()) {

            if ($line['pos_count_finish'] != $line['pos_count']) {
                $line['pos_count'] = $line['pos_count'] - $line['pos_count_finish'];
                $ordered_info[] = $line;
            }
        }


        if (isset($ordered_info))
            return $ordered_info;

    }

    function get_robot_inprocess()
    {
        $query = "SELECT * FROM `robots` WHERE `writeoff` = 0 AND `remont` = 0 AND `delete` = 0 AND `progress` != 100";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $year = date('Y', strtotime($line['date']));
            $month = date('m', strtotime($line['date']));
            $day = date('d', strtotime($line['date']));
            $id = $line['id'];
            $version = $line['version'];
            $date = $year . "." . $month;
            if (!isset($robot[$date][$version])) {
                $robot[$date][$version] = 0;
            }
            $robot[$date][$version]++;
        }
        return $robot;
    }


    function get_operation($id_pos)
    {
        //ищем комплекты в которых состоят ид_поз
        $query = "SELECT id_kit, count FROM `pos_kit_items` WHERE `id_pos` = $id_pos";
        $result = $this->pdo->query($query);

        while ($line = $result->fetch()) {
            $id_kit0 = $line['id_kit'];
            //создаем массив [ид_поз][ид_набора]=>количество
            $kit_array_count[$id_pos][$id_kit0] = $line['count'];
            //создается массив [ид_набора, количество
            $kit_array[] = $line;
        }

        if (isset($kit_array)) {

            //$cnt = 0;
            //ищем комплекты в текущих работах
            foreach ($kit_array as $value) {
                $id = $value['id_kit'];
                $query = "SELECT robots.version, robots.number, robots.date,  robots.remont, check.operation FROM `check` JOIN robots ON check.robot = robots.id WHERE `id_kit` = $id AND `check` = 0 AND robots.remont = 0 AND robots.delete = 0 AND robots.progress != 100 ORDER BY `robots`.`date` ASC";
                $result = $this->pdo->query($query);

                while ($line = $result->fetch()) {
                    $year = date('Y', strtotime($line['date']));
                    $month = date('m', strtotime($line['date']));
                    $day = date('d', strtotime($line['date']));
                    $date = $year . "." . $month;
                    //создаем массив [общая потребность,[все работы]]
                    if (!isset($operation_array[$date]['count'])) {
                        $operation_array[$date]['count'] = 0;
                    }
                    $operation_array[$date]['count'] += $kit_array_count[$id_pos][$id];
                    $operation_array[$date]['robots'][] = $line['operation'] . " - " . $line['version'] . "." . $line['number'] . "(" . $kit_array_count[$id_pos][$id] . ")";
                    //$cnt++;
                }

            }
            //if (isset($operation_array)) return $operation_array;

        }
        return (isset($operation_array)) ? $operation_array : null;

    }

    function get_operation_assembly($id_pos)
    {

        //ищем в каких ид (сборках-позициях) состоит ид_поз
        $query = "SELECT * FROM `pos_assembly_items` JOIN pos_items ON pos_assembly_items.id_assembly = pos_items.assembly WHERE `id_pos` = $id_pos";
        $result = $this->pdo->query($query);

        while ($line = $result->fetch()) {
            $assembly_array[] = $line;
        }
        //если ид_поз состоит в какой то позиции-сборке, то массив существует
        if (isset($assembly_array)) {
            //определяем потребность сколько нужно позиций ид_поз для каждой позиции сборки
            foreach ($assembly_array as $value) {
                $id_pos_ass = $value['id']; //ид (сборки-позиции)
                $count = $value['count']; //потребность ид_поз в сборке ид
                //ищем комплекты в которых состоят ид (сборки-позиции)
                $query = "SELECT id_kit, count FROM `pos_kit_items` WHERE `id_pos` = $id_pos_ass";
                $result = $this->pdo->query($query);

                while ($line = $result->fetch()) {
                    $id_kit0 = $line['id_kit'];

                    $line['count'] = $line['count'] * $count;
                    //создаем массив [ид_поз][ид_набора]=>количество
                    if (!isset($kit_array_count[$id_pos][$id_kit0])) $kit_array_count[$id_pos][$id_kit0] = 0;
                    $kit_array_count[$id_pos][$id_kit0] = $line['count'];
                    //создается массив [ид_набора, количество]
                    $kit_array[] = $line;
                }
            }
        }

        if (isset($kit_array)) {

            //$cnt = 0;
            //ищем комплекты в текущих работах
            foreach ($kit_array as $value) {
                $id = $value['id_kit'];
                $query = "SELECT robots.version, robots.number, robots.date, robots.remont, check.operation FROM `check` JOIN robots ON check.robot = robots.id WHERE `id_kit` = $id AND `check` = 0 AND robots.remont = 0 AND robots.delete = 0 AND robots.progress != 100 ORDER BY `robots`.`date` ASC";
                $result = $this->pdo->query($query);

                while ($line = $result->fetch()) {
                    $year = date('Y', strtotime($line['date']));
                    $month = date('m', strtotime($line['date']));
                    $day = date('d', strtotime($line['date']));
                    $date = $year . "." . $month;
                    //создаем массив [общая потребность,[все работы]]
                    if (!isset($operation_array[$date]['count'])) {
                        $operation_array[$date]['count'] = 0;
                    }
                    $operation_array[$date]['count'] += $kit_array_count[$id_pos][$id];
                    $operation_array[$date]['robots'][] = $line['operation'] . " - " . $line['version'] . "." . $line['number'] . "(" . $kit_array_count[$id_pos][$id] . ")";
                    //$operation_array[$cnt]['version'] = $line['version'];
                    // $operation_array[$cnt]['number'] = $line['number'];
                    //$operation_array[$cnt]['operation'] = $line['operation'];
                    //$operation_array[$cnt]['date'] = $line['date'];
                    //$operation_array[$cnt]['count'] = $kit_array_count[$id_pos][$id];
                    //$cnt++;
                }

            }
            //if (isset($operation_array)) return($operation_array);
        }

        return (isset($operation_array)) ? $operation_array : null;

    }


    function __destruct()
    {
        //echo "plan - ";
        //print_r($this ->link_plan);
        //echo "<br>";
        //mysql_close($this ->link_plan);
    }

}