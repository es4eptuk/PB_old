<?php
include 'include/class.inc.php';

//фильтр по версиям
$arr_eq = $robots->getEquipment;
$v_filtr = [];
foreach ($arr_eq as $eq) {
    if (isset($_POST[$eq['id']])) {
        array_push($v_filtr, $eq['id']);
    }
}

$date = date('Y-m-d');

$categoryes = $position->getCategoryes;
//собираем все комплекты по версиям
$arr_kit = $plan->get_kits_by_version($v_filtr);
//собираем все зависимые комплекты
$arr_sub_kit = [];
foreach ($arr_kit as $kit) {
    $arr = $position->get_all_kits_by_id($kit, 1);
    if ($arr != null) {
        $arr = array_column($arr, 'id_kit');
        $arr_sub_kit = array_merge($arr_sub_kit, $arr);
    }
}
//объединяем и уникализируем
$arr_kit = array_merge($arr_kit, $arr_sub_kit);
$arr_kit = array_unique($arr_kit);
//собираем комплекты 2 вариант
$arr_kit_2 = $plan->get_kits_by_version2($v_filtr);
$arr_kit = array_merge($arr_kit, $arr_kit_2);
$arr_kit = array_unique($arr_kit);
//расписываем в позиции
$arr_pos = $plan->get_pos_by_kits($arr_kit);
//собрать инвенторизацию
$arr_inventory = $plan->get_inventory($date.' 00:00:00', $date.' 23:59:59');
//собрать количество позиций в собираемых роботах
$arr_in_process = $plan->get_in_process();
//ставим инвенторизацию
foreach ($arr_inventory as $id_pos => $info) {
    if (array_key_exists($id_pos, $arr_pos)) {
        $arr_pos[$id_pos]['count'] = $info['new_count'];
        $arr_pos[$id_pos]['inventory'] = 1;
    }
}
//ставим пустоту если нет инвентаризации
foreach ($arr_pos as $id_pos => $info) {
    if (!array_key_exists('inventory', $info)) {
        $arr_pos[$id_pos]['count'] = $info['total'];
        $arr_pos[$id_pos]['inventory'] = 0;
    }
}
//прибавляем отмеченные чеклисты, в процессе, не списанные в 1С
foreach ($arr_pos as $id_pos => $info) {
    if (array_key_exists($id_pos, $arr_in_process)) {
        $arr_pos[$id_pos]['count'] = $arr_pos[$id_pos]['count'] + $arr_in_process[$id_pos];
    }
}

/*foreach ($arr_in_process as $id_pos => $count) {
    if (array_key_exists($id_pos, $arr_pos)) {
        $arr_pos[$id_pos]['count'] = $arr_pos[$id_pos]['count'] + $count;
    }
}*/

function file_force_download($file) {
    if (file_exists($file)) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        unlink($file);
        exit;
    }
}
if (isset($_POST['file']) && $_POST['file'] == 1) {
    $file = $plan->get_report_inventory($arr_pos);
    file_force_download($file);
}

?>
<?php include 'template/head.php' ?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'template/header.php' ?>
    <!-- Left side column. contains the logo and sidebar -->
    <?php include 'template/sidebar.php';?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Состав устройств</h1>
        </section><!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <form action="one_day.php" method="post">
                                    <input type="hidden" name="file" value="1" />
                                    <?php
                                    foreach ($arr_eq as $eq) {
                                        if (isset($_POST[$eq['id']])) {
                                            $checked = 'checked';
                                        } else {
                                            $checked = '';
                                        }
                                        echo '<input type="checkbox" id="'.$eq['id'].'" name="'.$eq['id'].'" '.$checked.' style="display:none">';
                                    }
                                    ?>
                                    <button type="submit" class="btn btn-success">Выгрузить в EXEL</button>
                                </form>
                                <!--<a href="./one_day.php?file=1">Выгрузить в EXEL</a>-->
                            </h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">

                            <div class="">
                                <form action="one_day.php" method="post">
                                    <div class="form-group">
                                        <?php
                                        foreach ($arr_eq as $eq) {
                                            if (isset($_POST[$eq['id']])) {
                                                $checked = 'checked';
                                            } else {
                                                $checked = '';
                                            }
                                            echo '<div class="checkbox">';
                                            echo '<label><input type="checkbox" id="'.$eq['id'].'" name="'.$eq['id'].'" '.$checked.'> '.$eq['title'].'</label>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary" id="add_filtr" name="">Применить</button>
                                        <button type="reset" class="btn btn-default" id="del_filtr" name="" onclick="javascript:document.location = './one_day.php';">Сбросить</button>
                                    </div>
                                </form>
                            </div>

                            <?php
                            /*print_r('<pre>');
                            print_r($arr_kit);
                            print_r('</pre>');
                            print_r('<hr>');
                            print_r('<pre>');
                            print_r($arr_pos);
                            print_r('</pre>');*/
                            ?>


                            <table class="table table-hover" id="listPos">
                                <tbody>
                                <tr>
                                    <th>Сборка</th>
                                    <th>posID</th>
                                    <th>Категория</th>
                                    <th>Артикул</th>
                                    <th>Наименование</th>
                                    <th>Инвентаризация</th>
                                    <th>Кол</th>
                                    <th>На складе СЕЙЧАС</th>
                                    <th>Удаление</th>
                                </tr>
                                <?php
                                usort($arr_pos, function ($a,$b) {
                                    return strcmp($a["title"], $b["title"]);
                                });
                                foreach ($arr_pos as $pos) {
                                    $color = ($pos['inventory'] == 0) ? "background-color:#f5c5dd" : "";

                                    $assembly = ($pos['assembly'] == 0) ? '' :  '<i class="fa fa-check"></i>';
                                    $inventory = ($pos['inventory'] == 0) ? '' :  '<i class="fa fa-check"></i>';
                                    echo '   
                                    <tr style="'.$color.'">
                                        <td>'.$assembly.'</td>                                    
                                        <td>'.$pos['id'].'</td>
                                        <td>'.$categoryes[$pos['category']]['title'].'</td>                                         
                                        <td>'.$pos['vendor_code'].'</td>
                                        <td>'.$pos['title'].'</td>
                                        <td>'.$inventory.'</td>                                          
                                        <td><b>'.$pos['count'].'</b></td>
                                        <td style="color:#999">'.$pos['total'].'</td>
                                        <td><i class="fa fa-2x fa-remove" style="cursor:pointer;"></i></td> 
                                    </tr>
                                ';
                                }
                                ?>
                                </tbody>
                            </table>

                        </div><!-- /.box-body -->
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </section><!-- /.content -->
    </div><!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div><!-- ./wrapper -->
<!-- Modal -->
<?php include 'template/scripts.php'; ?>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script>

    $(document).ready(function () {

        $("#listPos").on("click", ".fa-remove", function () {
            $(this).parent().parent().fadeOut("normal", function () {
                $(this).remove();
            });
        });

    });

</script>
</body>
</html>
<?php


?>