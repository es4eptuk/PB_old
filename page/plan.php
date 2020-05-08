<?php

class Plan
{
    private $query;
    private $pdo;
    private $orders;
    private $position;


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
        global $orders, $position;

        $this->orders = $orders;
        $this->position = $position;
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

    //собираем массив китов
    function get_kits_items()
    {
        $query = "SELECT * FROM `pos_kit_items` JOIN `pos_items` ON `pos_items`.`id` = `pos_kit_items`.`id_pos` WHERE `pos_items`.`archive` = 0"; //AND `pos_kit_items`.`delete` = 0
        $result = $this->pdo->query($query);
        $kits = [];
        while ($line = $result->fetch()) {
            $kits[$line['id_kit']][$line['id_pos']] = [
                'count' => $line['count'],
                'assembly' => $line['assembly'],
            ];
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
        $assemblyes = $this->get_assemblyes_items();
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

    //создание файлов по плану заказов
    function add_order_plan_new($id_category, $id_version, $id_month)
    {
        $arr_robot = $this->get_robot_inprocess();
        $arr_robot = (isset($arr_robot)) ? $arr_robot : [];

        //подготовка потребностей
        $arr_kit_items = $this->get_kits();
        $arr_need = [];
        foreach ($arr_robot as $k => $v) {
            if (isset($v)) {
                foreach ($v as $kv => $vv) {
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

        //предварительная обработка массива для создания заказа и фала
        $result = [];
        foreach ($arr_pos as $id => $pos) {
            if ($id_month != 0) {
                $inorder = $pos['month'][$id_month]['inorder'];
            } else {
                //если остатки в -, то автозаказа по позиции не создавать
                $inorder = ($pos['total']<0) ? 0 : $pos['inorder'];
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

        /*$log = date('Y-m-d H:i:s') . ' ' . print_r($result, true);
        file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
        die;*/

        //создание заказа и файла
        $current_month = date('m');
        $current_year = date('y');
        $tmp_date = "25.".$current_month.".".$current_year;
        $order_date = date('d.m.Y',strtotime("$tmp_date +1 month"));
        $order_date = new DateTime($order_date);
        $order_date = $order_date->format('Y-m-d H:i:s');

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
            $this->orders->add_order(json_encode($json),0,true);
        }

        $date_folder = new DateTime($order_date);
        $date_folder = $date_folder->format('d_m_y');

        return "orders/".$date_folder."/orders_".$id_category.".zip";

    }
}