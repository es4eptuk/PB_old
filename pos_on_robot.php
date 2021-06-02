<?php
include 'include/class.inc.php';

?>

<?php include 'template/head.php' ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php include 'template/header.php' ?>
    <!-- Left side column. contains the logo and sidebar -->
    <?php include 'template/sidebar.php';?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
               Складские остатки
            </h1>

        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">


                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><?php echo $position->getCategoryes[$_GET['id']]['title'] ?></h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="pos" class="table table-responsive">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Артикул</th>
                                    <th>Наименование</th>
                                    <th>Количество на складе</th>
                                    <th>На робота</th>
                                    <th>Роботов</th>


                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $arr = $position->get_pos_in_kit_cat($_GET['id'],4,1);


                                foreach ($arr as &$pos) {
if ($pos['SUM(pos_kit_items.count)'] < 0 ) $pos['SUM(pos_kit_items.count)'] = 0;
                                    if ($pos['SUM(pos_kit_items.count)'] !=0) {$pos['posOnRobot'] = floor($pos['total'] / $pos['SUM(pos_kit_items.count)']); } else {$pos['posOnRobot'] = 0;}

                                }

                                foreach ($arr as &$pos) {
                                    echo "
                       <tr>
                          <td>" . $pos['id'] . "</td>
                          <td>" . $pos['vendor_code'] . "</td>
                          <td>" . $pos['title'] . "</td>         
                          <td>" . $pos['total'] . "</td>
                          <td>" . $pos['SUM(pos_kit_items.count)'] . "</td>
                          <td><b>" . $pos['posOnRobot'] . "</b></td>
                         
                        </tr>
                       
                       
                       ";

                                }
                                ?>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->


<?php include 'template/scripts.php'; ?>
<!-- page script -->
<script>

    $('#pos').DataTable({
        "iDisplayLength": 500,
        "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "All"]],
        "order": [[ 5, "DESC" ]]
    } );
</script>


</body>
</html>
