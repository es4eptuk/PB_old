<?php include 'include/class.inc.php';
$versions = $robots->getEquipment;
$subversions = $robots->getSubVersion;
$category = $position->getCategoryes;
$subcategory = $position->getSubcategoryes;
?>
<?php
    if (isset($_POST['count']) && isset($_POST['version'])) {
        $count = (!empty($_POST['count'])) ? $_POST['count'] : 0;
        $version = (!empty($_POST['version'])) ? $_POST['version'] : null;
        if (!empty($count) && !empty($version)) {
            $result = $checks->get_difference_pos_by_version($version, $count);
        }
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
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Выбор подверсии</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <form action="" method="post" data-toggle="validator">
                                <div class="form-group">
                                    <select class="form-control" id="version" name="version" required="required" >
                                        <option value="0">Выберите версию...</option>
                                        <?php
                                        foreach ($versions as &$version) {
                                            echo "<option value='" . $version['id'] . "'>" . $version['title'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="count" id="count" required="required" placeholder="Введите количество">
                                </div>
                                <div class="box-footer">
                                    <button class="btn btn-primary" id="save_close" type="submit">Запустить</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                    if (isset($result)) {
                        echo '
                            <div class="box box-danger">
                                <div class="box-body">
                        ';
                        foreach ($result['private'] as $id_subver => $arr_pos) {
                            echo '
                                    <h4 style="color:#dd4b39;font-weight:bold;">Подверсия: '.$subversions[$id_subver]['title'].'</h4>
                                    <table id="robots" class="table">
                                        <thead>
                                        <tr>
                                            <th style="width:5%">Id Pos</th>
                                            <th style="width:8%">Категория</th>
                                            <th style="width:12%">Подкатегория</th>
                                            <th style="width:6%">Артикул</th>
                                            <th style="width:30%">Наименование</th>
                                            <th>На роботов</th>
                                            <th>Нужное кол.</th>
                                            <th>На складе</th>
                                            <th>В заказах</th>
                                            <th>Свободно</th>
                                            <th>Не хватает</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                ';
                            foreach ($arr_pos as $id_pos => $info) {

                                //в заказах
                                $arr_inorders = (isset($arr_orders[$id_pos])) ? $arr_orders[$id_pos] : [];
                                $inorder = array_sum (array_column($arr_inorders, 'count'));
                                $info_inorders = "";
                                foreach ($arr_inorders as $id_order => $info_order) {
                                    $info_inorders .= "<a href='./edit_order.php?id=".$id_order."'>".$info_order['date']." - ".$info_order['count']." шт.</a><br>";
                                }

                                $still = $info['total'] + $inorder - $info['reserv'];
                                $still = ($still < 0) ? 0 : $still;
                                $order = ($still - $info['need']);
                                $order = ($order > 0) ? 0 : $order * (-1);
                                $onrobot = intval($still/($info['need']/$count));
                                $onrobot = ($onrobot > $count) ? $count : $onrobot;
                                if ($onrobot == 0) {
                                    $color="#f5c5dd;";
                                }
                                if ($onrobot == $count) {
                                    $color="#c1f7cc;";
                                    $info_inorders = "";
                                }
                                if ($onrobot < $count && $onrobot > 0) {
                                    $color="#f1f7c1;";
                                }
                                $onrobot = $onrobot.'/'.$count;
                                echo '
                                        <tr style="background-color:'.$color.'">
                                            <td>'.$id_pos.'</td>
                                            <td>'.$category[$info['category']]['title'].'</td>
                                            <td>'.$subcategory[$info['subcategory']]['title'].'</td>
                                            <td>'.$info['vendor_code'].'</td>
                                            <td>'.$info['title'].'</td>
                                            <td>'.$onrobot.'</td>                                            
                                            <td>'.$info['need'].'</td>
                                            <td>'.$info['total'].'</td>
                                            <td>'.$inorder.'</td>
                                            <td>'.$still.'</td>
                                            <td>
                                                <span style="font-weight:800;" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$info_inorders.'">
                                                '.$order.'
                                                </span>
                                            </td>   
                                        </tr>                                         
                                    ';
                            }
                            echo '                                        
                                        </tbody>
                                    </table>
                                ';
                        }
                        echo '
                                </div>
                            </div>
                        ';
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
    <div class="control-sidebar-bg"></div>
</div>
<?php include 'template/scripts.php'; ?>

<script>
</script>

</body>
</html>
