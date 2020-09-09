<?php
include 'include/class.inc.php';

//debug helper output
function print_r2($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}
$arr_ticket_status = $tickets->get_status();
$arr = $robots->get_robots();
//$paramRobot = (isset($_GET['robot']) ? $_GET['robot'] : 0);
$arr_assign = $tickets->get_assign_tickets();
$arr_tickets = $tickets->get_tickets();
$filtr_robot = array();
$finish = array();
$inProcess = array();
$awaitingRepair = array();
$process = array();
$unAssigned = array();
$wait = array();
$robot_problem = 0;
$open_tickets = 0;
$awaitingRepair_tickets = 0;
$process_tickets = 0;
$unAssigned_tickets = 0;
$assign_Kostya = 0;
$assign_Danil = 0;
$assign_Eldar = 0;
$currentDate = date('d.m.Y');
$date_Today = date("Y-m-d");



//считаем созданные сегодня
$ticketsTodayArr = $tickets->get_tickets(0, null, 0, 'update_date', 'DESC', 'date_create', $date_Today . " 00:00:00", $date_Today . " 23:59:59");
if (!isset($ticketsTodayArr)) {
    $ticketsTodayArr = 0;
} else {
    $ticketsToday = count($ticketsTodayArr);
}
//print_r2($arr_tickets[0]);

foreach ($arr_tickets as &$ticket) {
    $ticket_status = $ticket['status'];
    $ticket_robot = $ticket['robot'];
    $ticket_assign = $ticket['assign'];
    //считаем уникальных роботов для фильтра
    if ($ticket_status != 6 && $ticket_status != 8) {
        if (!array_key_exists($ticket_robot, $filtr_robot)) {
            $robot_info = $robots->get_info_robot($ticket_robot);
            $filtr_robot[$ticket_robot] = [
                'id' => $ticket_robot,
                'number' => $robot_info['version'] . "." . $robot_info['number'],
                'name' => $robot_info['name'],
            ];

        }
    }
    //завершенных
    if ($ticket_status == 3 || $ticket_status == 6) {
        //$finish[$ticket_robot] = isset($finish[$ticket_robot] ) + 1;
        //если по ключу массив пустой, задаем ему 0
        if (isset($finish[$ticket_robot]) == false) {
            $finish[$ticket_robot] = 0;
        }
        $finish[$ticket_robot]++;
    }
    //открытые тикеты
    if ($ticket_status == 1 || $ticket_status == 2 || $ticket_status == 4 || $ticket_status == 5) {
        //$inprocess[$ticket_robot] = isset($inprocess[$ticket_robot] ) + 1;
        if (isset($inProcess[$ticket_robot]) == false) {
            $inProcess[$ticket_robot] = 0;
        }
        $inProcess[$ticket_robot]++;
        $open_tickets++;
    }
    //не назначенные
    if ($ticket_status == 4) {
        //$remont[$ticket_robot]['count'] = isset($remont[$ticket_robot] ) + 1;
        //если по ключу массив пустой, задаем ему 0
        if (isset($awaitingRepair[$ticket_robot]['count']) == false) {
            $awaitingRepair[$ticket_robot]['count'] = 0;
        }
        $awaitingRepair[$ticket_robot]['count']++;
        if (isset($awaitingRepair[$ticket_robot]['date']) == false) {
            $awaitingRepair[$ticket_robot]['date'] = "";
        }
        $date_finish = new DateTime($ticket['finish_date']);
        $awaitingRepair[$ticket_robot]['date'] = $date_finish->format('d.m.Y');
        $awaitingRepair_tickets++;
    }
    //в процессе
    if ($ticket_status == 2) {
        //$process[$ticket_robot] = isset($process[$ticket_robot] ) + 1;
        //если по ключу массив пустой, задаем ему 0
        if (isset($process[$ticket_robot]) == false) {
            $process[$ticket_robot] = 0;
        }
        $process[$ticket_robot]++;
        $process_tickets++;
    }
    //уже вроде как не используется!!!
    /*if ($ticket_status == 7) {
        //$wait[$ticket_robot] = isset($wait[$ticket_robot] ) + 1;
        //если по ключу массив пустой, задаем ему 0
        if (isset($wait[$ticket_robot]) == false) {
            $wait[$ticket_robot] = 0;
        }
        $wait[$ticket_robot]++;
    }*/
    //не назначенных
    if ($ticket_assign == 0 and ($ticket_status == 1 || $ticket_status == 2 || $ticket_status == 4 || $ticket_status == 5)) {
        //если по ключу массив пустой, задаем ему 0
        if (isset($unAssigned[$ticket_robot]) == false) {
            $unAssigned[$ticket_robot] = 0;
        }
        $unAssigned[$ticket_robot]++;
        $unAssigned_tickets++;
    }
    //назначенные Косте
    if ($ticket_assign == 51 && ($ticket_status == 1 || $ticket_status == 2 || $ticket_status == 4 || $ticket_status == 5) && $ticket['finish_date'] != null) {
        $date_finish = new DateTime($ticket['finish_date']);
        $date_finish_formatted = $date_finish->format('d.m.Y');
        if ($date_finish_formatted === $currentDate) {
            $assign_Kostya++;
        }
    }
    //назначенные дане
    if ($ticket_assign == 32 && ($ticket_status == 1 || $ticket_status == 2 || $ticket_status == 4 || $ticket_status == 5) && $ticket['finish_date'] != null) {
        $date_finish = new DateTime($ticket['finish_date']);
        $date_finish_formatted = $date_finish->format('d.m.Y');
        if ($date_finish_formatted === $currentDate) {
            $assign_Danil++;
        }
    }
    //назначенные эдьдару
    if ($ticket_assign == 44 && ($ticket_status == 1 || $ticket_status == 2 || $ticket_status == 4 || $ticket_status == 5) && $ticket['finish_date'] != null) {
        $date_finish = new DateTime($ticket['finish_date']);
        $date_finish_formatted = $date_finish->format('d.m.Y');
        if ($date_finish_formatted === $currentDate) {
            $assign_Eldar++;
        }
    }
}
//                print_r($unAsighned);
//print_r($finish);
//print_r($inprocess);
?>

<?php include 'template/head.php' ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php include 'template/header.php' ?>
    <!-- Left side column. contains the logo and sidebar -->
    <?php include 'template/sidebar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>KANBAN</h1>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Статистика</h3>
                </div>
                <div class="box-body">

                    <div class="col-xs-6">
                        <p class="lead">На сегодня <?php echo date("d.m.Y"); ?></p>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th style="width:50%">Созданных тикетов:</th>
                                    <td><?php
                                        if (!isset($ticketsToday))
                                        {echo 0;}
                                        else
                                        {echo $ticketsToday;}
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:50%">Открытых тикетов:</th>
                                    <td><?php echo array_sum($inProcess); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Проблемных роботов:</th>
                                    <td class="dop"><?php echo count($inProcess); ?> <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                $count = 0;
                                                foreach ($inProcess as $key => $value) {
                                                    $robot_info = $robots->get_info_robot($key);
                                                    $number = $robot_info['version'] . "." . $robot_info['number'];
                                                    $name = $robot_info['name'];
                                                    $inprocess_sort[$count]['number'] = $robot_info['version'] . "." . $robot_info['number'];
                                                    $inprocess_sort[$count]['id'] = $key;
                                                    $inprocess_sort[$count]['sum'] = $value;
                                                    $inprocess_sort[$count]['name'] = $name;
                                                    $count++;
                                                }
                                                function cmp($a, $b)
                                                {
                                                    return strcmp($a["number"], $b["number"]);
                                                }
                                                usort($inprocess_sort, "cmp");
                                                foreach ($inprocess_sort as $key => $value) {
                                                    echo "<li><a href='./robot_card.php?id=" . $value['id'] . "'  >" . $value['number'] . " (" . $value['sum'] . ")</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <!--                                --><?php /*print_r($robot_info);*/ ?>

                                <tr>
                                    <th><?= $arr_ticket_status[2]['title'] ?>:</th>
                                    <td class="dop"><?php echo $process_tickets; ?> <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                foreach ($process as $key => $value) {
                                                    $robot_info = $robots->get_info_robot($key);
                                                    $number = $robot_info['version'] . "." . $robot_info['number'];
                                                    echo "<li><a href='./robot_card.php?id=" . $key . "'>" . $number . " (" . $value . ")</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?= $arr_ticket_status[4]['title'] ?>:</th>
                                    <td class="dop"> <?php echo $awaitingRepair_tickets; ?> <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                //print_r($remont);
                                                foreach ($awaitingRepair as $key => $value) {
                                                    $robot_info = $robots->get_info_robot($key);
                                                    $number = $robot_info['version'] . "." . $robot_info['number'];
                                                    $date_color = "";
                                                    if (strtotime($value['date']) == strtotime(date("d.m.Y"))) {
                                                        $date_color = "text-yellow";
                                                    }
                                                    if (strtotime($value['date']) < strtotime(date("d.m.Y"))) {
                                                        $date_color = "text-red";
                                                    }
                                                    echo "<li><a href='./robot_card.php?id=" . $key . "'>" . $number . " (" . $value['count'] . ")</a> - <span class='" . $date_color . "'>" . $value['date'] . "</span></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Не назначенных</th>
                                    <td class="dop"><?php echo $unAssigned_tickets; ?> <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                //print_r($unAsighned);
                                                //$count = 0;
                                                foreach ($unAssigned as $key => $value) {
                                                    $robot_info = $robots->get_info_robot($key);
                                                    $number = $robot_info['version'] . "." . $robot_info['number'];
                                                    //$count++;
                                                    echo "<li><a href='./robot_card.php?id=" . $key . "'>" . $number . " (" . $value . ")</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <!--<tr>
                                    <th>Назначенных Косте</th>
                                    <td class="dop"><?php echo $assign_Kostya; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Назначенных Данилу</th>
                                    <td class="dop"><?php echo $assign_Danil; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Назначенных Эльдару</th>
                                    <td class="dop"><?php echo $assign_Eldar; ?>
                                    </td>
                                </tr>
                                <tr>-->
                                </tbody>
                            </table>
                        </div>
                        <p class="lead">Назначенных</p>
                        <div class="table-responsive">
                            <table class="table" id="arr_assign">
                                <tbody>
                                <?php
                                foreach ($arr_assign as $id_user => $info) {
                                    $user_info = $user->get_info_user($id_user);
                                    $color = "#00a65a";

                                    $button_class = "fa fa-fw fa-toggle-on pull-right tumb";
                                    if ($user_info['auto_assign_ticket'] == 0) {
                                        $color = "#af3124";
                                        $button_class = "fa fa-fw fa-toggle-off pull-right tumb";
                                    }
                                    $i_button = ($user_info['group'] == 4) ? '<i class="'.$button_class.'" style="cursor: pointer;" data-user="'.$id_user.'"></i>' : '';
                                    echo '
                                    <tr style="color:'.$color.'">
                                        <th style="width:50%">'.$user_info['user_name'].'</th>
                                        <td class="dop">'.$info['count'].' '.$i_button.'</td>
                                    </tr>
                                    ';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <p class="lead">За вчера</p>
                        <?php
                        $date_min = date("Y-m-d", strtotime("yesterday"));
                        $date_max = date("Y-m-d");
                        $date_minPr = date("Y-m-d", time() - 86400 * 1);
                        $date_maxPr = date("Y-m-d", time() - 86400 * 1);
                        $date_Today = date("Y-m-d");
                        //             echo $date_minPr . PHP_EOL;
                        //             echo $date_Today . PHP_EOL;
                        $countNew24Pr = 0;
                        $countNew24 = 0;
                        $countResh24 = 0;
                        $arr_new24 = $tickets->get_tickets(0, 0, 0, "update_date", "DESC", "date_create", $date_min, $date_max, "P");
                        //print_r($arr_new24);
                        if (isset($arr_new24)) {
                            foreach ($arr_new24 as &$value) {
                                $arr_new24_sort[] = $value['robot'];
                                //echo $value['robot']." ";
                            }
                            $arr_new24_sort = array_unique($arr_new24_sort);
                            $countNew24 = count($arr_new24_sort);
                        }
                        $arr_new24Pr = $tickets->get_tickets(0, 0, 0, "update_date", "DESC", "date_create", $date_minPr, $date_maxPr, "P");
                        if (isset($arr_new24Pr)) {
                            foreach ($arr_new24Pr as &$value) {
                                $arr_new24Pr_sort[] = $value['robot'];
                                //echo $value['robot']." ";
                            }
                            $arr_new24Pr_sort = array_unique($arr_new24Pr_sort);
                            $countNew24Pr = count($arr_new24Pr_sort);
                        }
                        $rNew = $countNew24 - $countNew24Pr;
                        $arr_Resh24 = $tickets->get_tickets(0, 0, 0, "update_date", "DESC", "inwork", $date_minPr . " 00:00:00", $date_maxPr . " 23:59:59", "P");
                        //print_r($arr_Resh24);
                        if (isset($arr_Resh24)) {
                            foreach ($arr_Resh24 as &$value) {
                                $robot_info = $robots->get_info_robot($value['robot']);
                                $number = $robot_info['version'] . "." . $robot_info['number'];
                                $arrTicketRobotNoProblem[$value['robot']] = $number;

                            }
                            $countResh24 = count($arr_Resh24);
                            // print_r($arr_Resh24);
                        }
                        //решенные сегодня
                        $arr_ReshToday = $tickets->get_tickets(0, 0, 0, "update_date", "DESC", "inwork", $date_Today . " 00:00:00", $date_Today . " 23:59:59", "P");
                        //print_r($arr_Resh24);
                        if (isset($arr_ReshToday)) {
                            foreach ($arr_ReshToday as &$value) {
                                $robot_info = $robots->get_info_robot($value['robot']);
                                $number = $robot_info['version'] . "." . $robot_info['number'];
                                $arrTicketRobotNoProblemToday[$value['robot']] = $number;
                            }
                            $countReshToday = count($arr_ReshToday);
                            // print_r($arr_Resh24);
                        }
                        /*$arr_new24 = $tickets->get_tickets(0,0,0,"update_date","DESC","date_create",$date_min,$date_max);
                        $arr_new24Pr = $tickets->get_tickets(0,0,0,"update_date","DESC","date_create",$date_minPr,$date_maxPr);
                        $countNew24 = count($arr_new24);
                        $countNew24Pr = count($arr_new24Pr);
                        $procNew = $countNew24Pr*100/$countNew24;
                        $arr_finish24 = $tickets->get_tickets(0,0,3,"update_date","DESC","update_date",$date_min,$date_max);
                        $arr_finish24Pr = $tickets->get_tickets(0,0,3,"update_date","DESC","update_date",$date_minPr,$date_maxPr);
                        $countfinish24 = count($arr_finish24);
                        $countfinish24Pr = count($arr_finish24Pr);
                        $procFinish = $countfinish24Pr*100/$countfinish24;
                       */
                        // $arr_finish24 = $tickets->get_tickets(0,0,3,"update_date","DESC",$date_min,$date_max);
                        // $arr_process24 = $tickets->get_tickets(0,0,3,"update_date","DESC",$date_min,$date_max);
                        ?>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th style="width:50%">Проблемных роботов:</th>
                                    <td class="dop"><?php echo $countNew24 . " (" . ($rNew < 0 ? '' : '+') . $rNew . ")";
                                        ?>
                                        <i class="fa fa-fw fa-plus-circle pull-right text-green"
                                           style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                $count = 0;
                                                //print_r($remont);
                                                foreach ($arr_new24_sort as $value) {
                                                    $robot_info = $robots->get_info_robot($value);
                                                    $number = $robot_info['version'] . "." . $robot_info['number'];
                                                    echo "<li><a href='./robot_card.php?id=" . $value . "'>" . $number . "</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>

                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:50%">Исправленных роботов:</th>
                                    <td class="dop">
                                        <?php
                                        if (!isset($arrTicketRobotNoProblem)) {
                                            $arrTicketRobotNoProblem = [];
                                        } else {
                                            echo count($arrTicketRobotNoProblem);
                                        }
                                        ?>
                                        <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                //print_r($remont);
                                                if (isset($arrTicketRobotNoProblem)) {
                                                    foreach ($arrTicketRobotNoProblem as $key => $value) {
                                                        echo "<li><a href='./robot_card.php?id=" . $key . "'>" . $value . "</a></li>";
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!--solved today-->
                    <div class="col-xs-6">
                        <p class="lead">За сегодня</p>
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:50%">Исправленных роботов:</th>
                                <td class="dop">
                                    <?php
                                    if (!isset($arrTicketRobotNoProblemToday)) {
                                        $arrTicketRobotNoProblemToday = [];
                                    } else {
                                        echo count($arrTicketRobotNoProblemToday);
                                    }
                                    if (!isset($arr_comments)) {
                                        $arr_count_comments = 0;
                                    } else {
                                        $arr_count_comments = count($arr_comments);
                                    }
                                    ?>
                                    <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                    <div class="robots" style="display: none">
                                        <ul>
                                            <?php
                                            if (isset($arrTicketRobotNoProblemToday)) {
                                                foreach ($arrTicketRobotNoProblemToday as $key => $value) {
                                                    echo "<li><a href='./robot_card.php?id=" . $key . "'>" . $value . "</a></li>";
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>

            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Фильтр</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Исполнитель</label>
                                <select class="form-control select3" style="width: 100%;" id="filter_user">
                                    <option value="0">Выберите исполнителя</option>
                                    <?php
                                    $arr_user = $user->get_users(4);
                                    //echo $ticket_assign_id;
                                    foreach ($arr_user as &$user_assign) {
                                        echo "<option value='" . $user_assign['user_id'] . "' >" . $user_assign['user_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <a href="./kanban.php">Сбросить фильтр</a>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Номер робота</label>
                                <select class="form-control select3" style="width: 100%;" id="filter_robot">
                                    <option value="0">Выберите робота</option>
                                    <?php
                                    /*if (isset($inprocess_sort)) {
                                        foreach ($inprocess_sort as &$robot) {
                                                echo '<option value="' . $robot['id'] . '"> ' . $robot['number'] . '( ' . $robot['name'] . ')'.'</option>';
                                        }
                                    }*/
                                    if (isset($filtr_robot)) {
                                        usort($filtr_robot, function ($a, $b) {
                                            return strcmp($a["number"], $b["number"]);
                                        });
                                        foreach ($filtr_robot as &$robot) {
                                            echo '<option value="' . $robot['id'] . '">' . $robot['number'] . ' (' . $robot['name'] . ')'.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>

            <div class="row">
                <?php
                $arr = $tickets->get_status(0);
                if (isset($arr)) {
                    foreach ($arr as &$status) {
                        $bg_color = $status['color'];
                        $font_color = $status['font'];
                        $title = $status['title'];
                        $id = $status['id'];
                        $arhiv = $id == 2 ? 'exp1' : 'exp2';
                        $arr_tickets = $tickets->get_tickets();
                        //$count[$id] = count($arr_tickets);
                        //echo count($arr_tickets);
                        if (isset($arr_tickets)) {
                            //var_dump(count($arr_tickets));
                            //print_r($arr_tickets);
                            $out = "";
                            $cnt[$id] = 0;
                            foreach ($arr_tickets as &$ticket) {
                                if ($ticket['status'] == $id) {
                                    $user_info = $user->get_info_user($ticket['assign']);
                                    $username_assign = $user_info['user_name'];
                                    $ticket_date = new DateTime($ticket['update_date']);
                                    $status = $ticket['status'];
                                    $status_info = $tickets->get_info_status($status);
                                    $status = $status_info['title'];
                                    $robot_info = $robots->get_info_robot($ticket['robot']);
                                    $robot_number = $robot_info['number'];
                                    $robot_version = $robot_info['version'];
                                    $ticket_category = $ticket['category'];
                                    $category_info = $tickets->get_info_category($ticket_category);
                                    $ticket_category = $category_info['title'];
                                    $ticket_subcategory = $ticket['subcategory'];
                                    $subcategory_info = $tickets->get_info_subcategory($ticket_subcategory);
                                    $ticket_subcategory = $subcategory_info['title'];
                                    $ticket_description = $ticket['description'];
                                    $ticket_class = $ticket['class'];
                                    $arr_comments = $tickets->get_comments($ticket['id']);
                                    if (!isset($arr_comments)) {
                                        $arr_count_comments = 0;
                                    } else {
                                        $arr_count_comments = count($arr_comments);
                                    }
                                    $arr_comments_customers = $tickets->get_comments_customers($ticket['id']);
                                    if (!isset($arr_comments_customers)) {
                                        $arr_count_comments_customers = 0;
                                    } else {
                                        $arr_count_comments_customers = count($arr_comments_customers);
                                    }
                                    $lng = mb_strlen($ticket_description, 'UTF-8');
                                    if ($lng > 100) {
                                        $ticket_description = mb_substr($ticket_description, 0, 100) . "...";
                                    }
                                    $str_date_finish = "";
                                    if ($ticket['finish_date'] != '0000-00-00' && $ticket['finish_date'] != null) {
                                        $date_finish = new DateTime($ticket['finish_date']);
                                        $str_date_finish = 'Ремонт назначен на <b>' . $date_finish->format('d.m.Y') . '</b><br><br>';
                                    }
                                    $tiket_color = '#e6e8ff'; //fffcd8 - желт для 2, e6e8ff
                                    if ($ticket['priority'] == 2) {$tiket_color = '#f9f9f9';}
                                    if ($ticket['priority'] == 3) {$tiket_color = '#ffd5d5';}
                                    $out .= '
                                        <div class="box box-solid" style="background-color: '.$tiket_color.'" id="' . $ticket['id'] . '" data-robot="' . $ticket['robot'] . '" data-status="' . $ticket['status'] . '">
                                            <div class="box-body">
                                              <b>' . $username_assign . '</b> <span class="pull-right text-muted">' . $robot_version . '.' . $robot_number . ' </span></br>
                                              <b><a href="./ticket.php?id=' . $ticket['id'] . '"><span class="ticket_class">' . $ticket_class . '</span>-' . $ticket['id'] . ' ' . $ticket_category . ':<span class="subcategory"> ' . $ticket_subcategory . '</span></a></b> 
                                              <p>' . $ticket_description . '</p>
                                              ' . $str_date_finish . '
                                              <span class="pull-right text-muted"><i class="fa fa-paperclip" style="margin-right:2px;"></i>0&nbsp;&nbsp;<i class="fa fa-commenting-o" style="margin-right:2px;"></i>' . $arr_count_comments . '&nbsp;&nbsp;<i class="fa fa-comments-o" style="margin-right:2px;"></i>' . $arr_count_comments_customers . '</span>
                                              <span class="pull-left text-muted"><i class="fa fa-calendar-o"></i> ' . $ticket_date->format('d.m.y H:i') . '</span>
                                            </div>
                                            </div>
                                    ';
                                    $cnt[$id]++;
                                }
                            }
                        }
                        echo '
                            <div class="col-md-2">
                              <div class="box box-default box-solid" style="border-color: ' . $bg_color . ';" id="overlay' . $id . '">
                                <div class="box-header with-border" style="background-color: ' . $bg_color . '; background: ' . $bg_color . '; color: ' . $font_color . ';" >
                                  <h4 class="box-title" style="font-size: 14px;">' . $title . ' (' . $cnt[$id] . ')</h4>
                                  <span class="pull-right dropdown"><i class="fa fa-ellipsis-h" data-toggle="dropdown"></i>
                                  <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">По дате создания карточки</li>
                                    <li class="divider"></li>
                                    <li><a href="#" class="sort" data-sortby="date_create" data-sortdir="ASC" data-status="' . $id . '">сначала старые</a></li>
                                    <li><a href="#" class="sort" data-sortby="date_create" data-sortdir="DESC" data-status="' . $id . '">сначала новые</a></li>
                                    <li class="divider"></li>
                                    <li class="dropdown-header">По дате изменения карточки</li>
                                    <li class="divider"></li>
                                     <li><a href="#" class="sort" data-sortby="update_date" data-sortdir="ASC" data-status="' . $id . '">сначала старые</a></li>
                                    <li><a href="#" class="sort" data-sortby="update_date" data-sortdir="DESC" data-status="' . $id . '">сначала новые</a></li>
                                    <li class="dropdown-header"></li>
                                    <li class="divider"></li>
                                    <li><a data-status="' . $id . '" href="#" class="' . ($id == 3 ? 'arhiv' : '') . '">Архивировать все карточки списка</a></li>
                                  </ul>
                                  </span>
                                </div>
                                <div class="box-body connectedSortable sortable" id="' . $id . '">
                        ';
                        echo $out;
                        echo '  
                                </div>
                              </div>
                            </div>
                        ';
                    }
                }
                ?>
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>

    <!-- Modal -->
    <div class="modal fade" id="add_result" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Опишите решение проблемы</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Решение:</label>
                        <textarea class="form-control" rows="5" id="result_description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" id="btn_add_reuslt">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное add_comment -->
    <div class="modal fade" id="add_comment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Опишите причину переноса карточки</h5>
                    <!--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
                    <!--                        <span aria-hidden="true">&times;</span>-->
                    <!--                    </button>-->
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Комментарий:</label>
                        <textarea class="form-control" rows="5" id="comment"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>-->
                    <button type="button" class="btn btn-primary" id="btn_add_comment">Добавить</button>
                </div>
            </div>
        </div>
    </div>

    <!--    модальное assign-->
    <div class="modal fade" id="assign" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Выберете сотрудника</h5>
                    <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>-->
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <select class="form-control" id="ticket_assign">
                            <option value="0">Не назначен</option>
                            <?php
                            $user_info = $user->get_info_user($ticket_info['assign']);
                            $ticket_assign_id = $user_info['user_id'];
                            $arr_user = $user->get_users(4);
                            //echo $ticket_assign_id;
                            foreach ($arr_user as &$user_assign) {
                                if ($user_assign['user_id']==$ticket_assign_id) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }
                                echo "<option value='".$user_assign['user_id']."' ".$selected.">".$user_assign['user_name']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_date" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Укажите дату назначенного ремонта</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                     </button>-->
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Дата:</label>

                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="datepicker"
                                   value= <?php echo $currentDate ?>>
                        </div>
                        <!-- /.input group -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" id="btn_add_date">Сохранить</button>
                </div>
            </div>
        </div>
    </div>


</div>

<audio controls id="notify">
    <!--<source src="notify.mp3" type="audio/mp3">-->
</audio>

<!-- ./wrapper -->
<?php include 'template/scripts.php'; ?>
<script src="../../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Select2 -->
<script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
<script>

    //get today in correct format
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    today = dd + '.' + mm + '.' + yyyy;
    // console.log(today);

    //Date picker
    $('#datepicker').datepicker({
        format: 'dd.mm.yyyy',
        startDate: today,
        todayHighlight: true,
        endDate: '',
        autoclose: true
    });

    var ticket_count = 0;
    var ticket_count_old = 0;
    var id_s = 0;
    var robot_t = 0;

    //кнопка
    $("#btn_add_reuslt").click(function () {
        var id = id_s;
        var result = $('#result_description').val();
        $.post("./api.php", {
            action: "ticket_add_result",
            id: id,
            result: result
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                $("#btn_add_reuslt").hide();
                window.location.reload(true);
            }
        });
    });

    // кнопка добавить комментарий
    $("#btn_add_comment").click(function () {
        var robot = robot_t;
        var id = id_s;
        var comment = $('#comment').val();
        if (comment === '') {
            console.log("пустая строка");
            alert("заполни комментАААААриЙ");
        } else {
            $("#btn_add_comment").hide();
            $.post("./api.php", {
                action: "ticket_add_comment",
                robot: robot,
                id: id,
                comment: comment
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    window.location.reload(true);
                }
            });
        }
    });

    //кнопка
    $("#btn_add_date").click(function () {
        var id = id_s;
        var date = $('#datepicker').val();
        /*if (typeof date !== 'undefined') {
            console.log('задана');
            console.log(date);
        } else {
            console.log('не задана');
        }*/
        $.post("./api.php", {
            action: "ticket_add_date",
            id: id,
            date: date
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                $("#btn_add_date").hide();
                $('#add_date').modal('hide');
                $('#assign').modal({backdrop: 'static', keyboard: false, show: true});
                id_s = id;
                // window.location.reload(true);
            }
        });
    });

    //кнопка
    $(".sort").click(function () {

        var sortBy = $(this).data("sortby");
        var sortDir = $(this).data("sortdir");
        var statusId = $(this).data("status");
        $("#" + statusId).empty();
        $("#overlay" + statusId).append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $.post("./api.php", {
            action: "ticket_get",
            robot: 0,
            user: 0,
            status: statusId,
            sortby: sortBy,
            sortdir: sortDir
        }).done(function (data) {
            var tickets = jQuery.parseJSON(data);
            //window.location.reload(true);
            $.each(tickets, function (index, value) {
                //console.log(statusId);
                $("#" + statusId).append(' <div class="box box-solid" style="background-color: #f9f9f9;" id="' + value['id'] + '" data-robot="' + value['robot'] + '" data-status="' + value['status'] + '"> \
                                    <div class="box-body"> \
                                    <b>' + value['assign'] + '</b> <span class="pull-right text-muted">' + value['robot'] + '</span></br> \
                                      <b><a href="./ticket.php?id=' + value['id'] + '">' + value['class'] + '-' + value['id'] + ' ' + value['category'] + ': ' + value['subcategory'] + '</a></b> \
                                      <p>' + value['description'] + '</p> \
                                      ' + value['str_finish_date'] + '\
                                      <span class="pull-right text-muted"><i class="fa fa-paperclip" style="margin-right:2px;"></i>0&nbsp;&nbsp;<i class="fa fa-commenting-o" style="margin-right:2px;"></i>' + value['comments'] + '&nbsp;&nbsp;<i class="fa fa-comments-o" style="margin-right:2px;"></i>' + value['comments_customers'] + '</span> \
                                      <span class="pull-left text-muted"><i class="fa fa-calendar-o"></i> ' + value['update_date'] + '</span> \
                                    </div>\
                            </div>');
                $("#overlay" + statusId).find(".overlay").remove();
                //console.log(value);
            });
        });
    });

    //кнопка
    $(".arhiv").click(function () {
        var statusId = $(this).data("status");
        $("#" + statusId).empty();
        $("#overlay" + statusId).append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $.post("./api.php", {
            action: "ticket_arhiv",
            id: statusId
        }).done(function (data) {
            $("#overlay" + statusId).find(".overlay").remove();
        });
    });

    //при смене
    $("#ticket_assign").change(function () {
        var id = id_s;
        var assign = $('#ticket_assign').val();
        $.post("./api.php", {
            action: "ticket_change_assign",
            id: id,
            assign: assign
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
    });

    // $('.comment').validator();
    $('.select2').select2();
    $(".fa-align-justify").click(function () {
        var id = $(this).attr("id");
        window.location.href = "./ticket.php?id=" + id;
    });

    //кнопка
    $(".fa-times").click(function () {
        id_log = $(this).attr("id");
        $.post("./api.php", {
            action: "delete_log",
            id: id_log
        }).done(function (data) {
            window.location.reload(true);
        });
    });

    //вкл авто распределения для сотрудника
    $("#arr_assign").on("click", ".fa-toggle-off, .fa-toggle-on", function() {
        $(".fa-toggle-off, .fa-toggle-on").hide();
        var user_id = $(this).data("user");
        var this_class = $(this).attr('class');
        //console.log(this_class);
        $.post("./api.php", {
            action: "change_auto_assign_for_user",
            id: user_id
        }).done(function (data) {
            var obj = jQuery.parseJSON(data)
            var el = $('i.tumb[data-user='+user_id+']');
            //console.log(obj.status);
            /*this_class.match(/(fa-toggle-on)/ig)*/
            if (obj.status == 0) {
                el.removeClass( "fa-toggle-on" ).removeClass( "fa-toggle-off" ).addClass( "fa-toggle-off" ).parent().parent().css( 'color', '#af3124' );
            } else {
                el.removeClass( "fa-toggle-off" ).removeClass( "fa-toggle-on" ).addClass( "fa-toggle-on" ).parent().parent().css( 'color', '#00a65a' );
            }
            $(".fa-toggle-off, .fa-toggle-on").show();
            //window.location.reload(true);
        });
    });

    //генерация фильтра в таблице
    $('#orders').DataTable({
        "iDisplayLength": 100,
        "order": [[0, "desc"]]
    });

    //Что это?
    <?php /*if ($userdata['user_id']==29) {
    echo "
    //setTimeout(function() {window.location.reload();}, 20000);

    ";
    }*/ ?>
    $(document).ready(function () {
        //$("#filter_user").val(<?php if (isset($_GET['user'])) echo $_GET['user']; ?>);
        $("#filter_robot").val(<?php if (isset($_GET['robot'])) echo $_GET['robot']; ?>);
        //$('#filter_user').val(<?php if (isset($_GET['user'])) echo $_GET['user']; ?>).trigger('change');

    });

    $(function () {
        $('[data-toggle="popover"]').popover();
        $(".sortable").sortable({
            stop: function (event, ui) {
                var out = ui;
                var id = ui['item'][0]['id'];
                var status = ui['item'][0]['parentElement']['id'];
                var robot = ui['item'][0]['dataset']['robot'];
                var old_status = ui['item'][0]['dataset']['status'];
                //console.log(ui['item'][0]);
                //console.log(id);
                //console.log(robot);
                //console.log(out);
                console.log(old_status);
                if (old_status == 3) {
                    return false;
                }
                // var robot = $("#"+id).find(".robot").text();
                var subcategory = $("#" + id).find(".subcategory").text();
                var ticket_class = $("#" + id).find(".ticket_class").text();
                // console.log(ticket_class);
                // console.log(robot);
                if ((subcategory == 0 || subcategory == "" || subcategory == null) && (status != 2 && status != 4 && status != 1)) {
                    if (ticket_class == "P") {
                        //$("#ticket_status option[value='0']").attr("selected","selected");
                        alert("Не заполнена подкатегория!");
                        return false;
                    }
                }
                if (status == 2 || status == 5 || status == 7) {
                    $('#add_comment').modal({backdrop: 'static', keyboard: false, show: true});
                    robot_t = robot;
                    id_s = id;
                }
                if (status == 3) {
                    $('#add_result').modal({backdrop: 'static', keyboard: false, show: true});
                    id_s = id;
                }
                if (status == 4) {
                    $('#add_date').modal({backdrop: 'static', keyboard: false, show: true});
                    id_s = id;
                    // $('#assign').modal({backdrop: 'static', keyboard: false, show: true});
                }
                if (status == 0 || status == 1 || status == 2 || status == 5 || status == 6 || status == 7 || status == 9) {
                    $.post("./api.php", {
                        action: "ticket_change_status",
                        id: id,
                        status: status
                    }).done(function (data) {
                        if (data == "false") {
                            alert("Data Loaded: " + data);
                        } else {
                            //window.location.href = "./robots.php";
                        }
                    });
                }
            },
            connectWith: ".connectedSortable"
        }).disableSelection();
    });

    //при смене
    $("#filter_user").change(function () {
        var user = $("#filter_user").val();
        var robot = $("#filter_robot").val();
        if (robot == 0) {
            window.location.href = "./kanban.php?user=" + user;
        } else {
            window.location.href = "./kanban.php?user=" + user + "&robot=" + robot;
        }
    });

    //при смене
    $("#filter_robot").change(function () {
        var user = $("#filter_user").val();
        var robot = $("#filter_robot").val();
        if (user == 0) {
            window.location.href = "./kanban.php?robot=" + robot;
        } else {
            window.location.href = "./kanban.php?user=" + user + "&robot=" + robot;
        }
    });

    //кнопка
    $(".dop").click(function () {
        $(this).find(".robots").toggle("slow");
    });

    //
    function live() {
        $.post("./api.php", {
            action: "tickets_get_live"
        }).done(function (data) {
            if (ticket_count == 0) {
                ticket_count = data;
                ticket_count_old = data;
                console.log("first" + " " + ticket_count + " " + ticket_count_old)
            } else {
                ticket_count = data;
                console.log("step" + " " + ticket_count + " " + ticket_count_old)
                if (ticket_count != ticket_count_old) {
                    //$('#norify')[0].play();
                    var sound = document.getElementById("notify");
                    sound.play();
                }
                ticket_count_old = ticket_count;
            }
        });
    }

    var timerId = setInterval(function () {
        //live();
    }, 2000);

</script>
</body>
</html>