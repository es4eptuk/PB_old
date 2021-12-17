<?php

class Plan
{
    private $query;
    private $pdo;
    private $orders;
    private $position;
    private $robots;

    private $temp_assembly;
    private $arr_plan;
    private $arr_plan_dop;


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
        global $orders, $position, $robots;

        $this->orders = $orders;
        $this->position = $position;
        $this->robots = $robots;

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
            //$day = date('d', strtotime($line['date']));
            //$id = $line['id'];
            //$number = $line['number'];
            $version = $line['version'];
            $date = $year . "." . $month;
            if (!isset($robot[$date][$version])) {
                $robot[$date][$version] = 0;
            }
            $robot[$date][$version]++;
        }
        return $robot;
    }

    function get_robot_inprocess_new()
    {
        $query = "SELECT * FROM `robots` WHERE `writeoff` = 0 AND `remont` = 0 AND `delete` = 0 AND `progress` != 100";
        $result = $this->pdo->query($query);
        $robot = [];
        while ($line = $result->fetch()) {
            $version = $line['version'];
            if (isset($robot[$version])) {
                $robot[$version] = $robot[$version] + 1;
            } else {
                $robot[$version] = 0;
            }
        }
        return $robot;
    }

    //собирает номера роботов
    function get_robot_inprocess_num()
    {
        $query = "SELECT * FROM `robots` WHERE `writeoff` = 0 AND `remont` = 0 AND `delete` = 0 AND `progress` != 100";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $year = date('Y', strtotime($line['date']));
            $month = date('m', strtotime($line['date']));
            //$day = date('d', strtotime($line['date']));
            //$id = $line['id'];
            $number = $line['number'];
            $version = $line['version'];
            $date = $year . "." . $month;
            /*if (!isset($robot[$date][$version])) {
                $robot[$date][$version] = [];
            }*/
            $robot[$date][$version][]=$number;
        }
        return $robot;
    }
    function get_robot_inprocess_num_new()
    {
        $query = "SELECT * FROM `robots` WHERE `writeoff` = 0 AND `remont` = 0 AND `delete` = 0 AND `progress` != 100";
        $result = $this->pdo->query($query);
        $robot = [];
        while ($line = $result->fetch()) {
            $number = $line['number'];
            $version = $line['version'];
            $robot[$version][]=$number;
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

    /** NEW PLAN **/
    //собираем все активные чеклисты у которых есть кит (дата, версия)
    function get_check_in_process_by_version($date, $version)
    {
        $date = str_replace('.', '-', $date.'.01');
        $y = date('Y', strtotime($date));
        $m = date('n', strtotime($date));
        $query = "
            SELECT * FROM `check` 
            JOIN `robots` ON `check`.`robot` = `robots`.`id`
            WHERE DATE_FORMAT(`robots`.`date`, '%Y')=$y
                AND DATE_FORMAT(`robots`.`date`, '%c')=$m  
                AND `robots`.`progress`!=100 
                AND `robots`.`version`=$version 
                AND `robots`.`remont`=0 
                AND `robots`.`delete`=0 
                AND `robots`.`writeoff`=0 
                AND `check`.`check`=0 
                AND `check`.`id_kit`!=0
        ";
        $result = $this->pdo->query($query);
        $checks = [];
        while ($line = $result->fetch()) {
            //$checks[] = $line;
            $checks[] = [
                'id_kit' => $line['id_kit'],
                'operation' => $line['operation'] . ' - ' . $line['version'] . '.' . $line['number'],
            ];

        }
        return $checks;
    }
    function get_check_in_process_by_version_new($version)
    {
        $query = "
            SELECT * FROM `check` 
            JOIN `robots` ON `check`.`robot` = `robots`.`id`
            WHERE `robots`.`progress`!=100 
                AND `robots`.`version`=$version 
                AND `robots`.`remont`=0 
                AND `robots`.`delete`=0 
                AND `robots`.`writeoff`=0 
                AND `check`.`check`=0 
                AND `check`.`id_kit`!=0
        ";
        $result = $this->pdo->query($query);
        $checks = [];
        while ($line = $result->fetch()) {
            //$checks[] = $line;
            $checks[] = [
                'id_kit' => $line['id_kit'],
                'operation' => $line['operation'] . ' - ' . $line['version'] . '.' . $line['number'],
            ];

        }
        return $checks;
    }

    //собираем массив китов
    function get_kits_items()
    {
        $query = "SELECT * FROM `pos_kit_items` JOIN `pos_items` ON `pos_items`.`id` = `pos_kit_items`.`id_pos` WHERE `pos_items`.`archive` = 0"; //AND `pos_kit_items`.`delete` = 0
        $result = $this->pdo->query($query);
        $kits = [];
        while ($line = $result->fetch()) {
            if (isset($kits[$line['id_kit']][$line['id_pos']])) {
                $tmp = $kits[$line['id_kit']][$line['id_pos']]['count'];
                $kits[$line['id_kit']][$line['id_pos']]['count'] = $tmp + $line['count'];
            } else {
                $kits[$line['id_kit']][$line['id_pos']] = [
                    'count' => $line['count'],
                    'assembly' => $line['assembly'],
                ];
            }
        }
        return $kits;
    }

    //собираем массив сборок
    function get_assemblyes_items()
    {
        $query = "SELECT * FROM `pos_assembly_items`";
        $result = $this->pdo->query($query);
        $assemblyes = [];
        while ($line = $result->fetch()) {
            $assemblyes[$line['id_assembly']][$line['id_pos']] = $line['count'];
        }
        return $assemblyes;
    }

    //возвращает обработанный масив с кит (разложив сборки)
    function get_kits()
    {
        //$assemblyes = $this->get_assemblyes_items();
        $kits = $this->get_kits_items();
        $result = [];
        foreach ($kits as $id_kit => $pos) {
            foreach ($pos as $pos_id => $data) {
                //if ($data['assembly'] == 0) {
                if (isset($result[$id_kit][$pos_id])) {
                    $tmp = $result[$id_kit][$pos_id];
                    $result[$id_kit][$pos_id] = $tmp + $data['count'];
                } else {
                    $result[$id_kit][$pos_id] = $data['count'];
                }
                //} else {
                /*if ($data['assembly'] != 0) {
                    foreach ($assemblyes[$data['assembly']] as $id => $count) {
                        if (isset($result[$id_kit][$id])) {
                            $tmp = $result[$id_kit][$id];
                            $result[$id_kit][$id] = $tmp + $count * $data['count'];
                        } else {
                            $result[$id_kit][$id] = $count * $data['count'];
                        }
                    }
                }*/
                //}
            }
        }

        return $result;
    }

    function get_kits_new()
    {
        $assemblyes = $this->get_assemblyes_items_new();
        $kits = $this->get_kits_items();
        $result = [];
        foreach ($kits as $id_kit => $pos) {
            foreach ($pos as $pos_id => $data) {
                //if ($data['assembly'] == 0) {
                if (isset($result[$id_kit][$pos_id])) {
                    $tmp = $result[$id_kit][$pos_id];
                    $result[$id_kit][$pos_id] = $tmp + $data['count'];
                } else {
                    $result[$id_kit][$pos_id] = $data['count'];
                }
                //} else {
                if ($data['assembly'] != 0) {
                    foreach ($assemblyes[$data['assembly']] as $id => $count) {
                        if (isset($result[$id_kit][$id])) {
                            $tmp = $result[$id_kit][$id];
                            $result[$id_kit][$id] = $tmp + $count * $data['count'];
                        } else {
                            $result[$id_kit][$id] = $count * $data['count'];
                        }
                    }
                }
                //}
            }
        }

        return $result;
    }
    function get_assemblyes_items_new()
    {
        $query = "SELECT * FROM `pos_assembly_items` JOIN `pos_items` ON `pos_assembly_items`.`id_pos` = `pos_items`.`id`";
        $result = $this->pdo->query($query);
        $array = [];
        while ($line = $result->fetch()) {
            $array[$line["id_assembly"]][$line["id_pos"]] = $line;
        }
        $this->temp_assembly = $array;
        unset($array);
        $assemblyes = [];
        foreach ($this->temp_assembly as $id_assembly_line => $lines) {
            foreach ($lines as $id_pos => $line) {
                $assemblyes[$line['id_assembly']][$line['id_pos']] = $line['count'];
                if ($line['assembly'] != 0) {
                    $inner = $this->get_recursive_assemblyes_items_new($line['assembly']);
                    foreach ($inner as $id_assembly => $poss) {
                        foreach ($poss as $id_pos => $count) {
                            if (isset($assemblyes[$line['id_assembly']][$id_pos])) {
                                $assemblyes[$line['id_assembly']][$id_pos] = $assemblyes[$line['id_assembly']][$id_pos] + $count * $line['count'];
                            } else {
                                $assemblyes[$line['id_assembly']][$id_pos] = $count * $line['count'];
                            }
                        }
                    }
                }
            }
        }

        unset($this->temp_assembly);
        return $assemblyes;
    }
    function get_recursive_assemblyes_items_new($id)
    {
        $lines = (isset($this->temp_assembly[$id])) ? $this->temp_assembly[$id] : [];

        $assemblyes = [];
        foreach ($lines as $id_pos => $line) {
            $assemblyes[$line['id_assembly']][$line['id_pos']] = $line['count'];
            if ($line['assembly'] != 0) {
                $inner = $this->get_recursive_assemblyes_items_new($line['assembly']);
                foreach ($inner as $id_assembly => $poss) {
                    foreach ($poss as $id_pos => $count) {
                        if (isset($assemblyes[$line['id_assembly']][$id_pos])) {
                            $assemblyes[$line['id_assembly']][$id_pos] = $assemblyes[$line['id_assembly']][$id_pos] + $count * $line['count'];
                        } else {
                            $assemblyes[$line['id_assembly']][$id_pos] = $count * $line['count'];
                        }
                    }
                }
            }
        }
        return $assemblyes;
    }

    //перебор массива со вставкой сборок
    /* НЕ ВЫЧИТАЕМ ИЗ ЗАКАЗ */
    function prepare_array_items($arr)
    {
        $this->arr_plan = $arr;
        unset($arr);
        $this->set_assemblyes_plan();
        $this->arr_plan_dop = [];

        foreach ($this->arr_plan as $pos_id => $item) {
            if ($item['assembly'] != 0 && $item['need'] < 0) {
                $this->add_in_arr_plan($item['assembly'], abs($item['need']), $item['in']['inneed'] - $item['in']['instock'], $pos_id, $item['in']['inneed'] - $item['in']['instock']);
            }
        }
        foreach ($this->arr_plan_dop as $pos_id => $item) {
            if (array_key_exists($pos_id, $this->arr_plan)) {
                $this->arr_plan[$pos_id]['need'] = $item['need'];
                $incount = $item['in']['inneed'];
                $this->arr_plan[$pos_id]['inorder'] = ($this->arr_plan[$pos_id]['need']<0 && (abs($this->arr_plan[$pos_id]['need'])-$this->arr_plan[$pos_id]['order'])>0)
                    ? abs($this->arr_plan[$pos_id]['need']) - $this->arr_plan[$pos_id]['order']
                    : 0;
                //считаем занчения
                $instock = ($this->arr_plan[$pos_id]['stock'] - $incount >= 0) ? $incount : $this->arr_plan[$pos_id]['stock'];
                $inorder = ($incount - $instock - $this->arr_plan[$pos_id]['order'] <= 0) ? 0 : $incount - $instock - $this->arr_plan[$pos_id]['order'];
                //присваеваем значения
                $this->arr_plan[$pos_id]['in']['inneed'] = $incount;
                $this->arr_plan[$pos_id]['in']['instock'] = $instock;
                $this->arr_plan[$pos_id]['in']['inorder'] = $inorder;
                $this->arr_plan[$pos_id]['in']['operation'] = array_merge($this->arr_plan[$pos_id]['in']['operation'], $item['operation']);
            }
        }
        $arr = $this->arr_plan;
        unset($this->arr_plan);
        unset($this->arr_plan_dop);
        unset($this->temp_assembly);
        return $arr;
    }
    function add_in_arr_plan($id_assembly, $count, $incount, $pos_out_id, $col)
    {
        $assembly_items = $this->temp_assembly[$id_assembly];
        foreach ($assembly_items as $pos_id => $item) {

            $need = 0;
            $in = [
                'inneed' => 0,
                'instock' => 0,
                'inorder' => 0,
            ];
            $inorder_old = 0;
            $ininorder_old = 0;
            if (array_key_exists($pos_id, $this->arr_plan)) {
                $need = $this->arr_plan[$pos_id]['need'];
                $in = $this->arr_plan[$pos_id]['in'];
                $inorder_old = $this->arr_plan[$pos_id]['inorder'];;
                $ininorder_old = $this->arr_plan[$pos_id]['in']['inorder'];
                unset($in['operation']);
            }
            if (array_key_exists($pos_id, $this->arr_plan_dop)) {
                $need_dop = $this->arr_plan_dop[$pos_id]['need'];
                $in_dop = $this->arr_plan_dop[$pos_id]['in'];
                $inorder_dop_old = $this->arr_plan_dop[$pos_id]['inorder'];
                $ininorder_dop_old = $this->arr_plan_dop[$pos_id]['in']['inorder'];
            } else {
                $need_dop = $need;
                $in_dop = $in;
                $inorder_dop_old = $inorder_old;
                $ininorder_dop_old = $ininorder_old;
            }
            $need_dop = $need_dop - $item['count'] * $count;
            $inorder_dop = ($need_dop<0 && (abs($need_dop)-$this->arr_plan[$pos_id]['order'])>0)
                ? abs($need_dop) - $this->arr_plan[$pos_id]['order']
                : 0;

            $incount_dop = $in_dop['inneed'] + $item['count'] * $incount;
            //считаем занчения
            $instock_dop = ($this->arr_plan[$pos_id]['stock'] - $incount_dop >= 0) ? $incount_dop : $this->arr_plan[$pos_id]['stock'];
            $ininorder_dop = ($incount_dop - $instock_dop - $this->arr_plan[$pos_id]['order'] <= 0) ? 0 : $incount_dop - $instock_dop - $this->arr_plan[$pos_id]['order'];
            //присваеваем значения
            $in_dop['inneed'] = $incount_dop;
            $in_dop['instock'] = $instock_dop;
            $in_dop['inorder'] = $ininorder_dop;

            $this->arr_plan_dop[$pos_id]['need'] = $need_dop;
            $this->arr_plan_dop[$pos_id]['inorder'] = $inorder_dop;
            $this->arr_plan_dop[$pos_id]['in'] = $in_dop;
            $this->arr_plan_dop[$pos_id]['operation'][] = $this->arr_plan[$pos_out_id]['vendor_code'].' '.$this->arr_plan[$pos_out_id]['title'].' ('.$col.'*'.$item['count'].')';

            if ($item['assembly'] != 0 && $need_dop < 0) {
                $this->add_in_arr_plan($item['assembly'], abs($need_dop), $incount_dop - $instock_dop, $pos_id, $incount_dop - $instock_dop);
            }
        }
    }
    /* ВЫЧИТАЕМ ИЗ ЗАКАЗА
    function prepare_array_items($arr)
    {
        $this->arr_plan = $arr;
        unset($arr);
        $this->set_assemblyes_plan();
        $this->arr_plan_dop = [];

        foreach ($this->arr_plan as $pos_id => $item) {
            if ($item['assembly'] != 0 && $item['inorder'] != 0) {
                $this->add_in_arr_plan($item['assembly'], $item['inorder'], $item['in']['inorder'], $pos_id, $item['in']['inneed']);
            }
        }
        foreach ($this->arr_plan_dop as $pos_id => $item) {
            if (array_key_exists($pos_id, $this->arr_plan)) {
                $this->arr_plan[$pos_id]['need'] = $item['need'];
                $incount = $item['in']['inneed'];
                $this->arr_plan[$pos_id]['inorder'] = ($this->arr_plan[$pos_id]['need']<0 && (abs($this->arr_plan[$pos_id]['need'])-$this->arr_plan[$pos_id]['order'])>0)
                    ? abs($this->arr_plan[$pos_id]['need']) - $this->arr_plan[$pos_id]['order']
                    : 0;
                //считаем занчения
                $instock = ($this->arr_plan[$pos_id]['stock'] - $incount >= 0) ? $incount : $this->arr_plan[$pos_id]['stock'];
                $inorder = ($incount - $instock - $this->arr_plan[$pos_id]['order'] <= 0) ? 0 : $incount - $instock - $this->arr_plan[$pos_id]['order'];
                //присваеваем значения
                $this->arr_plan[$pos_id]['in']['inneed'] = $incount;
                $this->arr_plan[$pos_id]['in']['instock'] = $instock;
                $this->arr_plan[$pos_id]['in']['inorder'] = $inorder;
                $this->arr_plan[$pos_id]['in']['operation'] = array_merge($this->arr_plan[$pos_id]['in']['operation'], $item['operation']);
            }
        }
        $arr = $this->arr_plan;
        unset($this->arr_plan);
        unset($this->arr_plan_dop);
        unset($this->temp_assembly);
        return $arr;
    }
    function add_in_arr_plan($id_assembly, $count, $incount, $pos_out_id, $col)
    {
        $assembly_items = $this->temp_assembly[$id_assembly];
        foreach ($assembly_items as $pos_id => $item) {

            $need = 0;
            $in = [
                'inneed' => 0,
                'instock' => 0,
                'inorder' => 0,
            ];
            $inorder_old = 0;
            $ininorder_old = 0;
            if (array_key_exists($pos_id, $this->arr_plan)) {
                $need = $this->arr_plan[$pos_id]['need'];
                $in = $this->arr_plan[$pos_id]['in'];
                $inorder_old = $this->arr_plan[$pos_id]['inorder'];;
                $ininorder_old = $this->arr_plan[$pos_id]['in']['inorder'];
                unset($in['operation']);
            }
            if (array_key_exists($pos_id, $this->arr_plan_dop)) {
                $need_dop = $this->arr_plan_dop[$pos_id]['need'];
                $in_dop = $this->arr_plan_dop[$pos_id]['in'];
                $inorder_dop_old = $this->arr_plan_dop[$pos_id]['inorder'];
                $ininorder_dop_old = $this->arr_plan_dop[$pos_id]['in']['inorder'];
            } else {
                $need_dop = $need;
                $in_dop = $in;
                $inorder_dop_old = $inorder_old;
                $ininorder_dop_old = $ininorder_old;
            }
            $need_dop = $need_dop - $item['count'] * $count;
            $inorder_dop = ($need_dop<0 && (abs($need_dop)-$this->arr_plan[$pos_id]['order'])>0)
                ? abs($need_dop) - $this->arr_plan[$pos_id]['order']
                : 0;

            $incount_dop = $in_dop['inneed'] + $item['count'] * $incount;
            //считаем занчения
            $instock_dop = ($this->arr_plan[$pos_id]['stock'] - $incount_dop >= 0) ? $incount_dop : $this->arr_plan[$pos_id]['stock'];
            $ininorder_dop = ($incount_dop - $instock_dop - $this->arr_plan[$pos_id]['order'] <= 0) ? 0 : $incount_dop - $instock_dop - $this->arr_plan[$pos_id]['order'];
            //присваеваем значения
            $in_dop['inneed'] = $incount_dop;
            $in_dop['instock'] = $instock_dop;
            $in_dop['inorder'] = $ininorder_dop;

            $this->arr_plan_dop[$pos_id]['need'] = $need_dop;
            $this->arr_plan_dop[$pos_id]['inorder'] = $inorder_dop;
            $this->arr_plan_dop[$pos_id]['in'] = $in_dop;
            $this->arr_plan_dop[$pos_id]['operation'][] = $this->arr_plan[$pos_out_id]['vendor_code'].' '.$this->arr_plan[$pos_out_id]['title'].' ('.$col.'*'.$item['count'].')';

            if ($item['assembly'] != 0 && $item['count'] * $count !=0) {
                $this->add_in_arr_plan($item['assembly'], $inorder_dop-$inorder_dop_old, $ininorder_dop-$ininorder_dop_old, $pos_id, $col * $item['count']);
            }
        }
    }
    */
    function set_assemblyes_plan()
    {
        $query = "SELECT * FROM `pos_assembly_items` JOIN `pos_items` ON `pos_assembly_items`.`id_pos` = `pos_items`.`id` WHERE `pos_items`.`archive` = 0 ";
        $result = $this->pdo->query($query);
        $array = [];
        while ($line = $result->fetch()) {
            $array[$line["id_assembly"]][$line["id_pos"]] = $line;
        }
        $this->temp_assembly = $array;
        unset($array);
    }

    //создание файлов по плану заказов
    function add_order_plan_new($id_category, $id_version, $id_month, $v_filtr)
    {
        $v_filtr = json_decode($v_filtr);
        //print_r($v_filtr);
        //die;


        $arr_robot = $this->get_robot_inprocess();
        $arr_robot = (isset($arr_robot)) ? $arr_robot : [];

        //подготовка потребностей
        $arr_kit_items = $this->get_kits();
        $arr_need = [];
        foreach ($arr_robot as $k => $v) {
            if (isset($v)) {
                foreach ($v as $kv => $vv) {
                    if (!in_array($kv, $v_filtr) && $v_filtr != []) {
                        continue;
                    }
                    foreach ($this->get_check_in_process_by_version($k,$kv) as $chesk) {
                        foreach ($arr_kit_items[$chesk['id_kit']] as $id_pos => $count) {
                            $arr_need[$k][$kv][] = [
                                'id_pos' => $id_pos,
                                'count' => $count,
                                'operation' => $chesk['operation'].' ('.$count.')',
                            ];
                        }
                    }
                }
            }
        }
        $arr_inneed = [];
        foreach ($arr_need as $month => $versions) {
            foreach ($versions as $version => $positions) {
                foreach ($positions as $pos) {
                    $count = $pos['count'];
                    $operation = $pos['operation'];
                    if (isset($arr_inneed[$month][$version][$pos['id_pos']])) {
                        $arr_inneed[$month][$version][$pos['id_pos']]['count'] = $arr_inneed[$month][$version][$pos['id_pos']]['count'] + $count;
                        $arr_inneed[$month][$version][$pos['id_pos']]['operation'][] = $operation;
                    } else {
                        $arr_inneed[$month][$version][$pos['id_pos']] = [
                            'count' => $count,
                            'operation' => [$operation],
                        ];
                    }
                }
            }
        }
        unset($arr_need);
        unset($arr_kit_items);

        //собираем все позиции в заказе, пока без категории $_GET['id']
        $orders = $this->orders->get_orders_items_inprocess();
        //создаем массив заказов
        foreach ($orders as $v) {
            $in_order = $v['pos_count'] - $v['pos_count_finish'];
            $pos_date = date('d.m.Y', strtotime($v['pos_date']));
            if ($in_order > 0) {
                $arr_orders[$v['pos_id']][$v['order_id']] = [
                    'count' => $in_order,
                    'date' => $pos_date,
                ];
            }
        }
        unset($orders);

        //создаем массив позиций по категории (без архивных и сборных позиций)
        $arr_pos = $this->position->get_pos_in_category($id_category);
        $arr_pos = (isset($arr_pos)) ? $arr_pos : [];
        //подготовка массива
        foreach ($arr_pos as $k => $v) {
            //удаляем сборки
            /*if ($v['assembly'] != 0) {
                unset($arr_pos[$k]);
                continue;
            }*/
            //удаляем лишние поля
            unset($arr_pos[$k]['longtitle']);
            unset($arr_pos[$k]['version']);
            unset($arr_pos[$k]['quant_robot']);
            unset($arr_pos[$k]['assembly']);
            unset($arr_pos[$k]['summary']);
            unset($arr_pos[$k]['apply']);
            unset($arr_pos[$k]['ow']);
            unset($arr_pos[$k]['img']);
            unset($arr_pos[$k]['archive']);
            unset($arr_pos[$k]['update_date']);
            unset($arr_pos[$k]['update_user']);
            //редактируем поля
            $arr_pos[$k]['subcategory'] = $this->position->getSubcategoryes[$v['subcategory']]['title'];
            //добавляем поля
            $arr_pos[$k]['need'] = $v['total'] - $v['reserv'] - $v['min_balance']; //общая потребность
            $arr_pos[$k]['orders'] = (isset($arr_orders[$k])) ? $arr_orders[$k] : [];
            $stock = ($v['total'] > 0) ? $v['total'] : 0;
            $order = array_sum (array_column($arr_pos[$k]['orders'], 'count'));
            $arr_pos[$k]['inorder'] = ($arr_pos[$k]['need']<0 && (abs($arr_pos[$k]['need'])-$order)>0) ? abs($arr_pos[$k]['need']) - $order : 0;
            $deleting = 0;
            $sum_inorder = 0;
            foreach ($arr_robot as $month => $versions) {
                $incount = 0;
                //если есть фильтр по версиям
                if ($id_version != 0) {
                    if (isset($arr_inneed[$month][$id_version][$k])) {
                        $incount = $arr_inneed[$month][$id_version][$k]['count'];
                    }
                //если нет фильтра по версиям
                } else {
                    foreach ($versions as $version => $count) {
                        if (isset($arr_inneed[$month][$version][$k])) {
                            $incount = $incount + $arr_inneed[$month][$version][$k]['count'];
                        }
                    }
                }
                //считаем занчения
                $instock = ($stock - $incount >= 0) ? $incount : $stock;
                $stock = ($stock - $instock > 0) ? $stock - $instock : 0;
                $inorder = ($incount - $instock - $order <= 0) ? 0 : $incount - $instock - $order;
                $order = (($order - ($incount - $instock)) <= 0) ? 0 : ($order - ($incount - $instock));
                $sum_inorder = $sum_inorder + $inorder;
                //присваеваем значения
                $arr_pos[$k]['month'][$month]['inneed'] = $incount;
                $arr_pos[$k]['month'][$month]['instock'] = $instock;
                $arr_pos[$k]['month'][$month]['inorder'] = ($inorder == 0) ? 0 : $sum_inorder;
                $deleting = $deleting + $incount;
            }
            if ($id_version!=0 && $deleting==0) {
                unset($arr_pos[$k]);
                continue;
            }
        }
        ksort($arr_pos);
        unset($arr_inneed);
        unset($arr_orders);

        //предварительная обработка массива для создания заказа и файла
        $result = [];
        foreach ($arr_pos as $id => $pos) {

            /*новая логика*/
            if ($id_month != 0) {
                //если месяц
                $inorder = $pos['month'][$id_month]['inorder'];
            } else {
                //если не месяц
                if ($v_filtr != []) {
                    //есть фильтр
                    $sum = [0];
                    foreach ($pos['month'] as $month) {
                        $sum[] = $month['inorder'];
                    }
                    $inorder = max($sum);
                } else {
                    //нет фильтра
                    $inorder = ($pos['total']<0) ? 0 : $pos['inorder'];
                }
            }


            /* старая
            //если месяц
            if ($id_month != 0) {
                $inorder = $pos['month'][$id_month]['inorder'];
            } else {
                //если остатки в -, то автозаказа по позиции не создавать
                $inorder = ($pos['total']<0) ? 0 : $pos['inorder'];
            }
            */

            if ($inorder == 0) {
                continue;
            }
            $result[$pos['provider']][$id] = [
                'id' => $id,
                'vendor_code' => $pos['vendor_code'],
                'title' => $pos['title'],
                'count' => $inorder,
                'price' => $pos['price'],
            ];
        }
        unset($arr_pos);

        /*$log = date('Y-m-d H:i:s') . ' ' . print_r($result, true);
        file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
        die;*/

        //создание заказа и файла

        /* старое
        $current_month = date('m');
        $current_year = date('y');
        $tmp_date = "25.".$current_month.".".$current_year;
        */
        //новое
        $tmp_date = date('Y-m-d');

        $order_date = date('d.m.Y',strtotime("$tmp_date +2 week")); //$tmp_date +1 month
        $order_date = new DateTime($order_date);
        $order_date = $order_date->format('Y-m-d H:i:s');

        $orders_id = '';
        foreach ($result as $key_provider => $orders) {
            $json = [];
            $json['0']['0'] = $id_category;
            if ($key_provider == 0) {
                $key_provider = 1;
            }
            $json['0']['1'] = $key_provider;
            $i = 1;
            foreach ($orders as $id_pos => $order) {
                $json[$i]['0'] = $order['id'];
                $json[$i]['1'] = $order['vendor_code'];
                $json[$i]['2'] = $order['title'];
                $json[$i]['3'] = $order['count'];
                $json[$i]['5'] = $order['price'];
                $json[$i]['6'] = $order_date;
                $i++;
            }
            $orders_id = $orders_id . ', ' . $this->orders->add_order(json_encode($json),0,true);
        }

        $date_folder = new DateTime($order_date);
        $date_folder = $date_folder->format('d_m_y');

        //return "orders/".$date_folder."/orders_".$id_category.".zip";

        $orders_id = substr($orders_id, 2);
        return $orders_id;

    }



    /**
     * НАДО ПЕРЕПИСАТЬ!!!! - ПЕРЕПИСАН
     */
    function add_order_plan_new_new($id_category, $id_version, $id_month, $v_filtr)
    {
        $v_filtr = json_decode($v_filtr);

        $arr_robot = $this->get_robot_inprocess_new();
        $arr_robot = (isset($arr_robot)) ? $arr_robot : [];

        //подготовка потребностей
        $arr_kit_items = $this->get_kits();
        $arr_need = [];
        foreach ($arr_robot as $v => $c) {
            if (isset($v)) {
                if (!in_array($v, $v_filtr) && $v_filtr != []) {
                    continue;
                }
                foreach ($this->get_check_in_process_by_version_new($v) as $chesk) {
                    foreach ($arr_kit_items[$chesk['id_kit']] as $id_pos => $count) {
                        $arr_need[$v][] = [
                            'id_pos' => $id_pos,
                            'count' => $count,
                            'operation' => $chesk['operation'].' ('.$count.')',
                        ];
                    }
                }
            }
        }
        unset($arr_kit_items);

        $arr_inneed = [];
        foreach ($arr_need as $version => $positions) {
            foreach ($positions as $pos) {
                $count = $pos['count'];
                $operation = $pos['operation'];
                if (isset($arr_inneed[$version][$pos['id_pos']])) {
                    $arr_inneed[$version][$pos['id_pos']]['count'] = $arr_inneed[$version][$pos['id_pos']]['count'] + $count;
                    $arr_inneed[$version][$pos['id_pos']]['operation'][] = $operation;
                } else {
                    $arr_inneed[$version][$pos['id_pos']] = [
                        'count' => $count,
                        'operation' => [$operation],
                    ];
                }
            }
        }
        unset($arr_need);

        //собираем все позиции в заказе, пока без категории $_GET['id']
        $orderss = $this->orders->get_orders_items_inprocess();
        //создаем массив заказов [id_pos => [id_order => in_order]]
        foreach ($orderss as $v) {
            $in_order = $v['pos_count'] - $v['pos_count_finish'];
            $pos_date = date('d.m.Y', strtotime($v['pos_date']));
            if ($in_order > 0) {
                $arr_orders[$v['pos_id']][$v['order_id']] = [
                    'count' => $in_order,
                    'date' => $pos_date,
                ];
            }
        }
        unset($orderss);

        //создаем массив позиций по категории (без архивных и сборных позиций)
        $arr_pos = $this->position->get_pos_in_category();
        $arr_pos = (isset($arr_pos)) ? $arr_pos : [];
        //подготовка массива
        foreach ($arr_pos as $k => $v) {
            //удаляем лишние поля
            unset($arr_pos[$k]['longtitle']);
            unset($arr_pos[$k]['version']);
            unset($arr_pos[$k]['quant_robot']);
            unset($arr_pos[$k]['summary']);
            unset($arr_pos[$k]['apply']);
            unset($arr_pos[$k]['ow']);
            unset($arr_pos[$k]['img']);
            unset($arr_pos[$k]['archive']);
            unset($arr_pos[$k]['update_date']);
            unset($arr_pos[$k]['update_user']);
            //редактируем поля
            $arr_pos[$k]['subcategory'] = $this->position->getSubcategoryes[$v['subcategory']]['title'];
            //добавляем поля
            $arr_pos[$k]['need'] = $v['total'] - $v['reserv'] - $v['min_balance']; //общая потребность
            $arr_pos[$k]['orders'] = (isset($arr_orders[$k])) ? $arr_orders[$k] : [];
            $arr_pos[$k]['stock'] = ($v['total'] > 0) ? $v['total'] : 0;
            $arr_pos[$k]['order'] = array_sum (array_column($arr_pos[$k]['orders'], 'count'));
            $arr_pos[$k]['inorder'] = ($arr_pos[$k]['need']<0 && (abs($arr_pos[$k]['need'])-$arr_pos[$k]['order'])>0)
                ? abs($arr_pos[$k]['need']) - $arr_pos[$k]['order']
                : 0;
            //
            $arr_pos[$k]['deleting_post'] = 0;
            $incount = 0;
            $operation = [];
            foreach ($arr_robot as $version => $count) {
                if (isset($arr_inneed[$version][$k])) {
                    $incount = $incount + $arr_inneed[$version][$k]['count'];
                    $operation = array_merge ($operation, $arr_inneed[$version][$k]['operation']);
                }
            }
            //считаем занчения
            $instock = ($arr_pos[$k]['stock'] - $incount >= 0) ? $incount : $arr_pos[$k]['stock'];
            $inorder = ($incount - $instock - $arr_pos[$k]['order'] <= 0) ? 0 : $incount - $instock - $arr_pos[$k]['order'];
            //присваеваем значения
            $arr_pos[$k]['in']['inneed'] = $incount;
            $arr_pos[$k]['in']['instock'] = $instock;
            $arr_pos[$k]['in']['inorder'] = $inorder;
            $arr_pos[$k]['in']['operation'] = $operation;
        }


        //обработка вхождений
        $arr_pos = $this->prepare_array_items($arr_pos);

        //удаляем лишнее
        foreach ($arr_pos as $k => $v) {
            if ($v['category'] == $id_category) {
                $arr_pos[$k]['in']['operation'] = implode("<br>", $arr_pos[$k]['in']['operation']);
                //определяем статус
                $arr_pos[$k]['in']['status'] = 0;
                if ($arr_pos[$k]['in']['inneed'] == $arr_pos[$k]['in']['instock'] && $arr_pos[$k]['in']['inneed'] != 0) {
                    $arr_pos[$k]['in']['status'] = 3;
                }
                if ($arr_pos[$k]['in']['inneed'] != $arr_pos[$k]['in']['instock'] && $arr_pos[$k]['in']['inorder'] == 0) {
                    $arr_pos[$k]['in']['status'] = 2;
                }
                if ($arr_pos[$k]['in']['inneed'] != $arr_pos[$k]['in']['instock'] && $arr_pos[$k]['in']['inorder'] != 0) {
                    $arr_pos[$k]['in']['status'] = 1;
                }
                $arr_pos[$k]['deleting_post'] = $arr_pos[$k]['deleting_post'] + $arr_pos[$k]['in']['inneed'];
                if (($id_version != 0 && $arr_pos[$k]['deleting_post']==0) || ($v_filtr != [] && $arr_pos[$k]['deleting_post']==0)) {
                    unset($arr_pos[$k]);
                    continue;
                }
            } else {
                unset($arr_pos[$k]);
            }
        }
        ksort($arr_pos);
        unset($arr_inneed);
        unset($arr_orders);

        //предварительная обработка массива для создания заказа и файла
        $arr_pos = (isset($arr_pos)) ? $arr_pos : [];
        $result = [];
        foreach ($arr_pos as $id => $pos) {

            /*новая логика*/
            if ($id_month != 0) {
                //если потребность
                $inorder = $pos['in']['inorder'];
            } else {
                //если склад
                $inorder = $pos['inorder'];
            }

            if ($inorder == 0) {
                continue;
            }
            $result[$pos['provider']][$id] = [
                'id' => $id,
                'vendor_code' => $pos['vendor_code'],
                'title' => $pos['title'],
                'count' => $inorder,
                'price' => $pos['price'],
            ];
        }
        unset($arr_pos);

        //новое
        $tmp_date = date('Y-m-d');

        $order_date = date('d.m.Y',strtotime("$tmp_date +2 week")); //$tmp_date +1 month
        $order_date = new DateTime($order_date);
        $order_date = $order_date->format('Y-m-d H:i:s');

        $orders_id = '';
        foreach ($result as $key_provider => $orders) {
            $json = [];
            $json['0']['0'] = $id_category;
            if ($key_provider == 0) {
                $key_provider = 1;
            }
            $json['0']['1'] = $key_provider;
            $i = 1;
            foreach ($orders as $id_pos => $order) {
                $json[$i]['0'] = $order['id'];
                $json[$i]['1'] = $order['vendor_code'];
                $json[$i]['2'] = $order['title'];
                $json[$i]['3'] = $order['count'];
                $json[$i]['5'] = $order['price'];
                $json[$i]['6'] = $order_date;
                $i++;
            }
            $orders_id = $orders_id . ', ' . $this->orders->add_order(json_encode($json),0,true);
        }

        //$date_folder = new DateTime($order_date);
        //$date_folder = $date_folder->format('d_m_y');

        $orders_id = substr($orders_id, 2);
        return $orders_id;
    }

    /**
     *
     */

    /** ОПЕРАТИВНЫЙ ПЛАН **/
    //сбор роботов по дням
    function get_robot_operational_plan($days, $start = 0) {
        $day_now = date('d');
        $month_now = date('m');
        $year_now = date('Y');
        $date_start = date('Y-m-d', mktime(0,0,0, $month_now, $day_now + $start, $year_now));
        $date_end = date('Y-m-d', mktime(0,0,0, $month_now, $day_now + $start + $days - 1, $year_now));
        $versions = $this->robots->getEquipment;
        //собираем даты и версии
        $arr_robots = [];
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', mktime(0,0,0, $month_now, $day_now + $start + $i, $year_now));
            foreach ($versions as $id_ver => $value) {
                $arr_robots[$date][$id_ver] = [];
            }
        }
        //собираем потребность по версиям
        $query = "
            SELECT *, `robots`.`id` AS `rid` FROM `check` 
            JOIN `robots` ON `check`.`robot` = `robots`.`id`
            WHERE `robots`.`date` >= '$date_start'
                AND `robots`.`date` <= '$date_end'   
                AND `robots`.`progress`!=100 
                AND `robots`.`remont`=0 
                AND `robots`.`delete`=0 
                AND `robots`.`writeoff`=0 
                AND `check`.`check`=0 
                AND `check`.`id_kit`!=0
        ";
        $result = $this->pdo->query($query);
        // + то из чего состоит сборка
        /*$arr_kit_items = $this->get_kits();
        while ($line = $result->fetch()) {
            foreach ($arr_kit_items[$line['id_kit']] as $pos_id => $count ) {
                if (isset($arr_robots[$line['date']][$line['version']][$pos_id])) {
                    $arr_robots[$line['date']][$line['version']][$pos_id] = $arr_robots[$line['date']][$line['version']][$pos_id] + $count;
                } else {
                    $arr_robots[$line['date']][$line['version']][$pos_id] = $count;
                }
            }
        }*/
        //только комплект раскладывает
        $arr_kit_items = $this->get_kits_items();
        while ($line = $result->fetch()) {
            foreach ($arr_kit_items[$line['id_kit']] as $pos_id => $count ) {
                if (isset($arr_robots[$line['date']][$line['version']][$pos_id])) {
                    $arr_robots[$line['date']][$line['version']][$pos_id] = $arr_robots[$line['date']][$line['version']][$pos_id] + $count['count'];
                } else {
                    $arr_robots[$line['date']][$line['version']][$pos_id] = $count['count'];
                }
            }
        }

        return $arr_robots;
    }

    /** КОНЕЦ ОПЕРАТИВНЫЙ ПЛАН **/

    /** ДЛЯ СНЯТИЯ ОСТАТКОВ **/
    function get_kits_by_version($versions = [])
    {
        if ($versions == []) {
            return [];
        }
        $arr = implode(',', $versions);
        $query = "SELECT * FROM `check_items` WHERE `version` IN (".$arr.") AND `kit` != 0";
        $result = $this->pdo->query($query);
        $kits = [];
        while ($line = $result->fetch()) {
            $kits[] = $line['kit'];
        }
        return $kits;
    }

    function get_kits_by_version2($versions = [])
    {
        if ($versions == []) {
            return [];
        }
        $arr = implode(',', $versions);
        $query = "SELECT * FROM `pos_kit` WHERE `version` IN (".$arr.")";
        $result = $this->pdo->query($query);
        $kits = [];
        while ($line = $result->fetch()) {
            $kits[] = $line['id_kit'];
        }
        return $kits;
    }

    function get_pos_by_kits($kits)
    {
        $query = "SELECT * FROM `pos_items`";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $positions[$line['id']] = $line;
        }

        $arr_kit_items = $this->get_kits_items();
        $arr_assemble_items = $this->get_assemblyes_items();//plan->get_assemblyes_items();

        $res = [];
        foreach ($kits as $kit) {
            if (array_key_exists($kit, $arr_kit_items)) {
                foreach ($arr_kit_items[$kit] as $id_pos => $info) {
                    if (!array_key_exists($id_pos, $res)) {
                        $res[$id_pos] = $positions[$id_pos];
                        if ($res[$id_pos]['assembly'] != 0) {
                            foreach ($arr_assemble_items[$res[$id_pos]['assembly']] as $id_pos_a => $count_a) {
                                if (!array_key_exists($id_pos_a, $res) && array_key_exists($id_pos_a, $positions)) {
                                    $res[$id_pos_a] = $positions[$id_pos_a];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $res;
    }

    function get_inventory($date_start, $date_end)
    {
        $query = "SELECT * FROM `pos_log` WHERE `title` LIKE '%инвентаризация%' 
            AND `update_date` >= '".$date_start."' AND `update_date` <= '".$date_end."' ORDER BY `update_date` ASC";
        $result = $this->pdo->query($query);
        $res = [];
        while ($line = $result->fetch()) {
            $res[$line['id_pos']] = $line;
        }
        return $res;
    }

    //собрать всю комплектуху которая использовалась (отмечены чек листы) в незавершенных роботах
    function get_in_process()
    {
        $query = "
            SELECT * FROM `check` 
            JOIN `robots` ON `check`.`robot` = `robots`.`id`
            WHERE `check`.`check` = 1
                AND `check`.`id_kit` != 0
                AND `robots`.`progress` != 100
                AND `robots`.`remont` = 0 
                AND `robots`.`delete` = 0 
                AND `robots`.`writeoff` = 0 
        "; //                AND `robots`.`version` IN (".$arr.")
        $result = $this->pdo->query($query);
        $kits = [];
        while ($line = $result->fetch()) {
            if (array_key_exists($line['id_kit'], $kits)) {
                $kits[$line['id_kit']] = $kits[$line['id_kit']] + 1;
            } else {
                $kits[$line['id_kit']] = 1;
            }
        }

        //собираем позиции
        $arr_kit_items = $this->get_kits_items();
        $arr_assemble_items = $this->get_assemblyes_items();//plan->get_assemblyes_items();

        $pos = [];
        foreach ($kits as $id_kit => $count) {
            foreach ($arr_kit_items[$id_kit] as $id_pos => $info) {
                if (array_key_exists($id_pos, $pos)) {
                    $pos[$id_pos] = $pos[$id_pos] + $info['count'] * $count;
                } else {
                    $pos[$id_pos] = $info['count'] * $count;
                }
                if ($info['assembly'] != 0) {
                    foreach ($arr_assemble_items[$info['assembly']] as $id_pos_a => $count_a) {
                        if (array_key_exists($id_pos_a, $pos)) {
                            $pos[$id_pos_a] = $pos[$id_pos_a] + $count_a * $info['count'] * $count;
                        } else {
                            $pos[$id_pos_a] = $count_a * $info['count'] * $count;
                        }
                    }
                }
            }
        }

        return $pos;
    }

    //отчет в эксель
    function get_report_inventory($arr_pos) {
        //создаем файлы
        //для папок
        $f_date = date('Y-m-d_H:i:s');
        //$folder = $owner_id;
        if (!file_exists(PATCH_DIR."/report/")) {
            mkdir(PATCH_DIR."/report/", 0777);
        }
        $excel_name = PATCH_DIR."/report/".$f_date.".xlsx";
        require_once ('excel/Classes/PHPExcel.php');
        require_once ('excel/Classes/PHPExcel/IOFactory.php');
        $objPHPExcel = new PHPExcel();
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        //задаем заголовки
        $objPHPExcel->getActiveSheet()->setCellValue("A1", 'Сборка');
        $objPHPExcel->getActiveSheet()->setCellValue("B1", 'posID');
        $objPHPExcel->getActiveSheet()->setCellValue("C1", 'Категория');
        $objPHPExcel->getActiveSheet()->setCellValue("D1", 'Артикул');
        $objPHPExcel->getActiveSheet()->setCellValue("E1", 'Наименование');
        $objPHPExcel->getActiveSheet()->setCellValue("F1", 'Инвентаризация');
        $objPHPExcel->getActiveSheet()->setCellValue("G1", 'Кол-во');
        $objPHPExcel->getActiveSheet()->setCellValue("H1", 'На складе СЕЙЧАС');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold(true);

        $categoryes = $this->position->getCategoryes;
        $row = 1;
        foreach ($arr_pos as $pos) {
            $row++;
            $assembly = ($pos['assembly'] == 0) ? 'Нет' :  'Да';
            $inventory = ($pos['inventory'] == 0) ? 'Нет' :  'Да';
            $objPHPExcel->getActiveSheet()->setCellValue("A" . $row, $assembly);
            $objPHPExcel->getActiveSheet()->setCellValue("B" . $row, $pos['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("C" . $row, $categoryes[$pos['category']]['title']);
            $objPHPExcel->getActiveSheet()->setCellValue("D" . $row, $pos['vendor_code']);
            $objPHPExcel->getActiveSheet()->setCellValue("E" . $row, $pos['title']);
            $objPHPExcel->getActiveSheet()->setCellValue("F" . $row, $inventory);
            $objPHPExcel->getActiveSheet()->setCellValue("G" . $row, $pos['count']);
            $objPHPExcel->getActiveSheet()->setCellValue("H" . $row, $pos['total']);
        }

        $styleArray = [
            'borders' => [
                'outline' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
                'inside' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $objPHPExcel->getActiveSheet()->getStyle("A1:H".$row)->applyFromArray($styleArray);


        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($excel_name);

        return $excel_name;
    }

    /** ДЛЯ СНЯТИЯ ОСТАТКОВ **/

}