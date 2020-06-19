<?php include 'include/class.inc.php';
//списки
$versions = $robots->getEquipment;
$subversions = $robots->getSubVersion;
$category = $position->getCategoryes;
$subcategory = $position->getSubcategoryes;
//фильтр по версиям
$v_filtr = [6,7];
//двигаем роботов
$robots->change_date_robot($v_filtr);
//фильтр по категориям
$cat_filtr = [];
foreach ($category as $cat) {
    if (isset($_POST[$cat['id']])) {
        array_push($cat_filtr, $cat['id']);
    }
}
//сбор плана (кол дней, с какого дня начинать)
$arr_need = $plan->get_robot_operational_plan(5, 0);
//собираем все позиции в заказе, пока без категории $_GET['id']
$orders = $orders->get_orders_items_inprocess();
//создаем массив заказов [id_pos => [id_order => in_order]]
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
//массив всех позиций
$arr_pos = $position->get_pos_all();
//обработка массивов для вывода
foreach ($arr_pos as $k => $v) {
    unset($arr_pos[$k]['longtitle']);
    unset($arr_pos[$k]['subversion']);
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
    $arr_pos[$k]['orders'] = (isset($arr_orders[$k])) ? $arr_orders[$k] : [];
    //-на складе как 0 $stok = ($arr_pos[$k]['total']>0) ? $arr_pos[$k]['total'] : 0;
    //- на складе как -
    $stok = $arr_pos[$k]['total'];
    $need_summ = 0;
    foreach ($arr_need as $date => $versions) {
        $need_summ_all_vers = 0;
        foreach ($versions as $version_id => $posits) {
            if (in_array($version_id, $v_filtr)) {
                if (isset($posits[$k])) {
                    //$arr_pos[$k]['need_invers'][$date][$version_id] = $posits[$k];
                    $need_summ_all_vers = $need_summ_all_vers + $posits[$k];
                } else {
                    //$arr_pos[$k]['need_invers'][$date][$version_id] = 0;
                }
            }
        }

        //потребность
        $arr_pos[$k]['need'][$date]['count'] = $need_summ_all_vers;
        $need_summ = $need_summ + $need_summ_all_vers;

        //нехватка
        $stok = $stok - $need_summ_all_vers;
        if ($need_summ_all_vers == 0) {
            $arr_pos[$k]['need'][$date]['not'] = 0;
        } else {
            $arr_pos[$k]['need'][$date]['not'] = ($stok < 0) ? $stok*(-1) : 0;
        }

    }
    if ($need_summ == 0 || $stok >= 0) {
        unset($arr_pos[$k]);
    } else {
        if (!(in_array($arr_pos[$k]['category'], $cat_filtr) || $cat_filtr == [])) {
            unset($arr_pos[$k]);
        }
    }

}
unset($arr_orders);

$out = '';
foreach ($arr_need as $date => $info) {
    $d = new DateTime($date);
    $d = $d->format('d.m.Y');
    $out .= '<th style="width:60px;">'.$d.'</th>';
}
?>



<?php include 'template/head.php' ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'template/header.php' ?>
    <?php include 'template/sidebar.php';?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Роботы</h1>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Оперативный план</h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="">
                                <form action="./plan_operational.php" method="post">
                                    <div class="form-group">
                                        <?php
                                        foreach ($category as $cat_id => $cat) {
                                            if ($cat_id > 10) {continue;}
                                            if (isset($_POST[$cat_id])) {
                                                $checked = 'checked';
                                            } else {
                                                $checked = '';
                                            }
                                            echo '<div class="checkbox">';
                                            echo '<label><input type="checkbox" id="'.$cat_id.'" name="'.$cat_id.'" '.$checked.'> '.$cat['title'].'</label>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary" id="add_filtr" name="">Применить</button>
                                        <button type="reset" class="btn btn-default" id="del_filtr" name="" onclick="javascript:document.location = './plan_operational.php'">Сбросить</button>
                                    </div>
                                </form>
                            </div>
                            <br><br>
                            <table id="pos" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Категория</th>
                                    <th>Подкатегория</th>
                                    <th>Артикул</th>
                                    <th>Наименование</th>
                                    <?=$out?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($arr_pos as $id_pos => $info) {
                                    echo '<tr>';
                                    echo '
                                        <td>' . $category[$info['category']]['title'] . '</td>
                                        <td>' . $subcategory[$info['subcategory']]['title'] . '</td>
                                        <td>' . $info['vendor_code'] . '</td>
                                        <td>' . $info['title'] . '</td>
                                    ';
                                    $orders = "";
                                    foreach ($info['orders'] as $id => $val) {
                                        $orders .= "<a href='./edit_order.php?id=".$id."'>".$val['date']." - ".$val['count']." шт.</a><br>";
                                    }
                                    foreach ($info['need'] as $date => $value) {
                                        $color = ($value['not']>0) ? '#de4e4e' : '#008000';
                                        $orders_in = ($value['not'] == 0) ? "" : $orders;
                                        echo '
                                            <td style="color:'.$color.'">
                                                <span style="font-weight:800;" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$orders_in.'">'
                                                . $value['not'] .
                                                '</span>
                                            </td>
                                        ';
                                    }
                                    echo '</tr>';
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
<?php include 'template/scripts.php'; ?>

<script>
    $('#pos').DataTable({
        "iDisplayLength": 100,
        "order": [[0, "asc"]]
    });

</script>

</body>
</html>
