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
      <h1>Статистика по роботам</h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Прогресс сборки</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <div class="">

                </div>
                <br>
              <table id="robots" class="table  table-hover">
                <thead>
                <tr>
                  <th>Версия</th>
                  <th>Кол-во всего</th>
                  <th>Кол-во в процессе</th>
                  <th>Суммарный процент</th>
                  <th>Общий процент</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    $arr = $robots->get_progress_statistics();
                    $versionsList = $robots->getEquipment;
                    $version = 0;
                    $total_count = 0;
                    $in_progress = 0;
                    $summary_progress = 0;
                    $total_progress = 0;
                    foreach ($arr as $robot) {
                        $version++;
                        $total_count += $robot['total_count'];
                        $in_progress += $robot['in_progress'];
                        $summary_progress += $robot['summary_progress'];
                        //$total_progress += $robot['total_progress'];
                        echo "
                            <tr>
                                <td>".$versionsList[$robot['version']]['title']."</td>
                                <td>".$robot['total_count']." шт</td>
                                <td>".$robot['in_progress']." шт</td>
                                <td>".$robot['summary_progress']."%</td>
                                <td>".$robot['total_progress']."%</td>                              
                            </tr>
                        ";
                    }
                    if ($version > 1 ) {
                        echo "
                            <tr style='color: #dc322f;'>
                                <td>ИТОГО:</td>
                                <td>".$total_count." шт</td>
                                <td>".$in_progress." шт</td>
                                <td>".$summary_progress."%</td>
                                <td>".intval($summary_progress/$total_count)."%</td>                              
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

<?php include 'template/scripts.php'; ?>
<script src="./bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script src="./bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js"></script>
<!-- Select2 -->
<script src="./bower_components/select2/dist/js/select2.full.min.js"></script>

<script>


    $(document).ready(function() {


        //??? вроде нет ничего
        /*$('#robots').DataTable({
            "iDisplayLength": 100,
            "order": [[0, "desc"]]
        });*/

    });
</script>
</body>
</html>
