<?php 
include_once 'include/class.inc.php';
//для фильтра по роботам
$arr_eq = $robots->getEquipment;
$v_filtr = [];
foreach ($arr_eq as $eq) {
    if (isset($_POST[$eq['id']])) {
        array_push($v_filtr, $eq['id']);
    }
}

//

$arr_robots = $robots->get_robots();
//$paramRobot = (isset($_GET['robot']) ? $_GET['robot'] : 0);

//даты
$period_day = 30;
$date_now = date('Y-m-d', time());
$date_befo = date('Y-m-d', time()-$period_day*24*3600);
$date_after = date('Y-m-d', time()+$period_day*24*3600);

//собираем роботов
$now_robots = [0 => [], 1 => []];
$after_robots = [0 => [], 1 => []];
$befor_robots = [0 => [], 1 => []];
foreach ($arr_robots as $robot) {
    if ($robot['version'] == 3) {
        continue;
    }
    if (!in_array($robot['version'], $v_filtr) && $v_filtr != []) {
        continue;
    }
    if ($robot['date_send'] == null) {continue;}
    //сегодня
    if ($robot['date_send'] == $date_now) {
        $now_robots[$robot['commissioning']][] = $robot;
    }
    //следующая неделя
    if ($robot['date_send'] > $date_now && $robot['date_send'] <= $date_after) {
        $after_robots[$robot['commissioning']][] = $robot;
    }
    //прошедшая неделя
    if ($robot['date_send'] < $date_now && $robot['date_send'] >= $date_befo) {
        $befor_robots[$robot['commissioning']][] = $robot;
    }
}

?>

<?php include 'template/head.php' ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
 <?php include 'template/header.php' ?>
  <?php include 'template/sidebar.php';?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Пусконаладка</h1>
    </section>

    <!-- Main content -->
    <section class="content">
       <div class="box box-info">
            <div class="box-header"><h3 class="box-title">Статистика</h3></div>
            <div class="box-body">

                <div class="col-xs-3">
                    <p class="lead">Фильтр по роботам</p>
                    <div class="">
                            <form action="" method="post">
                                <div class="form-group">
                                    <?php
                                    foreach ($arr_eq as $eq) {
                                        if (isset($_POST[$eq['id']])) {
                                            $checked = 'checked';
                                        } else {
                                            $checked = '';
                                        }
                                        echo '
                                            <div class="checkbox">
                                            <label><input type="checkbox" id="'.$eq['id'].'" name="'.$eq['id'].'" '.$checked.'> '.$eq['title'].'</label>
                                            </div>
                                        ';
                                    }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" id="add_filtr" name="">Применить</button>
                                    <button type="reset" class="btn btn-default" id="del_filtr" name="" onclick="javascript:document.location = 'commissioning.php'">Сбросить</button>
                                </div>
                            </form>
                    </div>
                </div>

                <div class="col-xs-3">
                    <p class="lead">На сегодня</p>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:50%">Всего к отгрузке</th>
                                <td><?= count($now_robots[0])+count($now_robots[1]) ?></td>
                            </tr>
                            <tr>
                                <th>С пусконаладкой</th>
                                <td class="dop"><?= count($now_robots[1]) ?>
                                    <?php if (count($now_robots[1]) > 0) { ?>
                                    <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                    <div class="robots" style="display: none">
                                        <ul>
                                            <?php
                                            foreach ($now_robots[1] as $robot) {
                                                $version = $arr_eq[$robot['version']]['title'];
                                                echo "<li><a class='search' style='cursor: pointer;' data-id='".$robot['id']."'>".$robot['number']."::".$version."<br>".$robot['name']."</a></li>";
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Без пусконаладки</th>
                                <td class="dop"><?= count($now_robots[0]) ?>
                                    <?php if (count($now_robots[0]) > 0) { ?>
                                        <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                foreach ($now_robots[0] as $robot) {
                                                    $version = $arr_eq[$robot['version']]['title'];
                                                    echo "<li><a class='search' style='cursor: pointer;' data-id='".$robot['id']."'>".$robot['number']."::".$version."<br>".$robot['name']."</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-xs-3">
                    <p class="lead">На ближайшую неделю</p>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:50%">Всего к отгрузке</th>
                                <td><?= count($after_robots[0])+count($after_robots[1]) ?></td>
                            </tr>
                            <tr>
                                <th>С пусконаладкой</th>
                                <td class="dop"><?= count($after_robots[1]) ?>
                                    <?php if (count($after_robots[1]) > 0) { ?>
                                        <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                foreach ($after_robots[1] as $robot) {
                                                    $version = $arr_eq[$robot['version']]['title'];
                                                    echo "<li><a class='search' style='cursor: pointer;' data-id='".$robot['id']."'>".$robot['number']."::".$version."<br>".$robot['name']."</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Без пусконаладки</th>
                                <td class="dop"><?= count($after_robots[0]) ?>
                                    <?php if (count($after_robots[0]) > 0) { ?>
                                        <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                foreach ($after_robots[0] as $robot) {
                                                    $version = $arr_eq[$robot['version']]['title'];
                                                    echo "<li><a class='search' style='cursor: pointer;' data-id='".$robot['id']."'>".$robot['number']."::".$version."<br>".$robot['name']."</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-xs-3">
                    <p class="lead">Прошедшая неделя</p>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th style="width:50%">Всего отгружено</th>
                                <td><?= count($befor_robots[0])+count($befor_robots[1]) ?></td>
                            </tr>
                            <tr>
                                <th>С пусконаладкой</th>
                                <td class="dop"><?= count($befor_robots[1]) ?>
                                    <?php if (count($befor_robots[1]) > 0) { ?>
                                        <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                foreach ($befor_robots[1] as $robot) {
                                                    $version = $arr_eq[$robot['version']]['title'];
                                                    echo "<li><a class='search' style='cursor: pointer;' data-id='".$robot['id']."'>".$robot['number']."::".$version."<br>".$robot['name']."</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Без пусконаладки</th>
                                <td class="dop"><?= count($befor_robots[0]) ?>
                                    <?php if (count($befor_robots[0]) > 0) { ?>
                                        <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                                        <div class="robots" style="display: none">
                                            <ul>
                                                <?php
                                                foreach ($befor_robots[0] as $robot) {
                                                    $version = $arr_eq[$robot['version']]['title'];
                                                    echo "<li><a class='search' style='cursor: pointer;' data-id='".$robot['id']."'>".$robot['number']."::".$version."<br>".$robot['name']."</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
       </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body table-responsive">
                        <button type="reset" class="btn btn-default search" id="all_robots" name="" data-id="">Показать всех роботов</button>
                        <br>
                        <br>
                        <table id="robots" class="table  table-hover">
                            <thead>
                                <tr>
                                    <th class="ids">Id</th>
                                    <th>Версия</th>
                                    <th>Номер</th>
                                    <th>Кодовое имя</th>
                                    <th>%</th>
                                    <th>Пусконаладка</th>
                                    <th>Дата отгрузки</th>
                                    <th>Владелец</th>
                                    <th>Покупатель</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($arr_robots as $id => $value) {
                                if (!in_array($value['version'], $v_filtr) && $v_filtr != []) {
                                    continue;
                                }
                                $owner = ($value['owner'] != 0) ? $robots->get_customers()[$value['owner']]['name'] : '';
                                $customer = ($value['customer'] != 0) ? $robots->get_customers()[$value['customer']]['name'] : '';
                                $commissioning = ($value['commissioning'] == 1) ? 'Да' : '';
                                $version = ($value['version'] == 3) ? '3' : $arr_eq[$value['version']]['title'];
                                $print = "<i class='fa fa-2x fa-print' style='cursor:pointer;color:#337ab7;' data-id='".$value['id']."'></i>";
                                echo "
                                    <tr>
                                        <td>" . $value['id'] . "</td>
                                        <td>" . $version . "</td>
                                        <td>" . $value['number'] . "</td>
                                        <td>" . $value['name'] . "</td>  
                                        <td>" . $value['progress'] . "</td>
                                        <td>" . $commissioning . "</td>
                                        <td>" . $value['date_send'] . "</td>
                                        <td>" . $owner . "</td>                                                                                  
                                        <td>" . $customer . "</td>                                         
                                        <td>" . $print . "</td>
                                    </tr>
                                ";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </section>
  </div>
  <div class="control-sidebar-bg"></div>
</div>
<div id="print-content"></div>

<?php include 'template/scripts.php';?>

<script>

    $(document).ready(function() {

        $(".dop").click(function () {
            $(this).find(".robots").toggle("slow");
        });

        var table = $('#robots').DataTable({
            "iDisplayLength": 100,
            "order": [[0, "desc"]]
        });

        // поиск по столбцу при нажатии
        $('body').on('click', '.search', function () {
            var id = $(this).data("id");
            table.columns('.ids').search( id ).draw();
            //table.search( id ).draw();
        } );

        //активация кнопки печати
        $("#robots").on('click', '.fa-print', function () {
            var id = $(this).data("id");
            CallPrint(id);
        });

        //функция печати
        function CallPrint(id) {
            $.post("./api.php", {action: "print_info_robot", id: id})
                .done(function (data) {
                    //console.log(data);
                    //return false;
                    var robot_info = jQuery.parseJSON(data);
                    var table = '<table class="robot-info" border="1" cellspacing="0" style="width:100%;font-size:12px">' +
                        '<tr><td style="width:40%"><b>Версия</b></td><td>' + robot_info['version'] + '</td></tr>' +
                        '<tr><td><b>Номер робота</b></td><td>' + robot_info['number'] + '</td></tr>' +
                        '<tr><td><b>Кодовое имя</b></td><td>' + robot_info['name'] + '</td></tr>' +
                        '<tr><td><b>Покупатель</b></td><td>' + robot_info['customer'] + '</td></tr>' +
                        '<tr><td><b>Владелец</b></td><td>' + robot_info['owner'] + '</td></tr>' +
                        '<tr><td><b>Чат, лингва, производство</b></td><td>' + robot_info['ident'] + '</td></tr>' +
                        '<tr><td colspan="2" style="padding-left:150px"><b>Комплектация</b></td></tr>' +
                        '<tr><td>Опции</td><td>' + robot_info['options'] + '</td></tr>' +
                        '<tr><td>Цвет</td><td>' + robot_info['color'] + '</td></tr>' +
                        '<tr><td>Брендирование</td><td>' + robot_info['brand'] + '</td></tr>' +
                        '<tr><td>ИКП</td><td>' + robot_info['ikp'] + '</td></tr>' +
                        '<tr><td>Дополнительная информация</td><td>' + robot_info['dop'] + '</td></tr>' +
                        '<tr><td colspan="2" style="padding-left:150px"><b>Информация о Покупателе</b></td></tr>' +
                        '<tr><td>ФИО</td><td>' + robot_info['fio'] + '</td></tr>' +
                        '<tr><td>e-mail</td><td>' + robot_info['email'] + '</td></tr>' +
                        '<tr><td>Телефон</td><td>' + robot_info['phone'] + '</td></tr>' +
                        '<tr><td colspan="2" style="padding-left:150px"><b>Информация для отгрузки</b></td></tr>' +
                        '<tr><td>Наличие АКБ</td><td>' + robot_info['battery'] + '</td></tr>' +
                        '<tr><td>Напряжение зарядной станции</td><td>' + robot_info['charger'] + '</td></tr>' +
                        '<tr><td>Язык (робота)</td><td>' + robot_info['language_robot'] + '</td></tr>' +
                        '<tr><td>Язык (инструкции)</td><td>' + robot_info['language_doc'] + '</td></tr>' +
                        '<tr><td>Дата отгрузки</td><td>' + robot_info['date_send'] + '</td></tr>' +
                        '<tr><td>Пусконаладка</td><td>' + robot_info['commissioning'] + '</td></tr>' +
                        '<tr><td>Наименование получателя</td><td>' + robot_info['customer'] + '</td></tr>' +
                        '<tr><td>Юридич. адрес получателя</td><td>' + robot_info['address'] + '</td></tr>' +
                        '<tr><td>ИНН получателя</td><td>' + robot_info['inn'] + '</td></tr>' +
                        '<tr><td>Информация по доставке:<br>-наличие колёс на кофре<br>-адрес доставки<br>-телефон и имя получателя<br>-плательщик по доставке<br>-аэропорт доставки</td><td>' + robot_info['delivery'] + '</td></tr>' +
                        '</table>';
                    var prtContent = document.getElementById('print-content');
                    //var prtCSS = '<link rel="stylesheet" href="./dist/css/print.css" type="text/css" />';
                    var prtCSS = '<style>' +
                        'td {padding:5px}' +
                        '</style>';
                    var WinPrint = window.open('', '', 'left=50,top=50,width=800,height=640,toolbar=0,scrollbars=1,status=0');
                    //console.log(table);
                    WinPrint.document.write('');
                    WinPrint.document.write(prtCSS);
                    WinPrint.document.write(prtContent.innerHTML = table);
                    WinPrint.document.write('');
                    WinPrint.document.close();
                    WinPrint.focus();
                    WinPrint.print();
                    WinPrint.close();
                    prtContent.innerHTML = '';
                });
        }
    });

</script>

</body>
</html>
