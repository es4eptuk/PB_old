<?php 
include 'include/class.inc.php';
include 'page/dashboard.php';



?>

<?php include 'template/head.html' ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include 'template/header.html' ?>
  <!-- Left side column. contains the logo and sidebar -->
  <?php include 'template/sidebar.html';?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      DASHBOARD
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
            <div class="box-body table-responsive">

           <?
               $robotsComplete = $dashboard->getRobotCompleteCount();
               echo "Количество готовых роботов: <b>".$robotsComplete."</b><br>";

               $defectSummCurrent = number_format($dashboard->getDefectSummCurent(), 2, ',', ' ');
               $defectSummLast = number_format($dashboard->getDefectSummLast(), 2, ',', ' ');
               echo "Количество брака за текущий месяц, сумма: <b>".$defectSummCurrent."</b> руб. <i>(в прошлом месяце $defectSummLast руб.)</i><br>";

               $serviceSummCurrent = number_format($dashboard->getServiceSummCurent(), 2, ',', ' ');
               $serviceSummLast = number_format($dashboard->getServiceSummLast(), 2, ',', ' ');
               echo "Затраты на сервис за текущий месяц , сумма: <b>".$serviceSummCurrent."</b> руб. <i>(в прошлом месяце $serviceSummLast руб.)</i><br>";

               $warehouseSum = number_format($dashboard->getSumWarehouse(), 2, ',', ' ');
               echo "Объем склада деталей, сумма: <b>".$warehouseSum."</b> руб.<br>";


               echo "Кредиторская задолженность, сумма: <br>";
               $creditSum = $dashboard->getSumDebet();
               $creditSumAll = 0;
               //print_r($creditSum);
              foreach ($creditSum as &$value) {
                  $date       = new DateTime($value['order_delivery']);
                  $date = $date->format('d.m.Y');
                    echo "<i style=\"margin-left: 40px\">".$date."</i> - <b>". number_format($value['SUM(order_price)'], 2, ',', ' ')."</b><br>";
                    $creditSumAll += $value['SUM(order_price)'];
              }
           echo "<i style=\"margin-left: 40px\">Итого:</i> <b>". number_format($creditSumAll, 2, ',', ' ')."</b>";

           ?>
               
               
               
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

<?php include "./template/scripts.html";?>

<script>

  
</script>
</body>
</html>
