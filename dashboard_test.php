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
              <div class="box-header with-border">
                  <h3 class="box-title">Финансы</h3>
              </div>
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
            <div class="box">
                <!-- /.box-header -->
                <div class="box-header with-border">
                    <h3 class="box-title">Техническая поддержка</h3>
                </div>
                <div class="box-body table-responsive">


                    <?

                    $totalAnswers = $dashboard->getCountAnswers(0, 999999);
                    echo "Всего ответов: <b>" . $totalAnswers . "</b><br>";

                    echo "Скорость ответа: <br>";
                    $countAnswers01 = $dashboard->getCountAnswers(0,1);
                    echo "<i style=\"margin-left: 40px\">До 1 минуты: </i><b>".$countAnswers01." (". round(($countAnswers01/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    $countAnswers12 = $dashboard->getCountAnswers(1,2);
                    echo "<i style=\"margin-left: 40px\">От 1 до 2 минут: </i><b>".$countAnswers12." (". round(($countAnswers12/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    $countAnswers23 = $dashboard->getCountAnswers(2,3);
                    echo "<i style=\"margin-left: 40px\">От 2 до 3 минут: </i><b>".$countAnswers23." (". round(($countAnswers23/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    $countAnswers35 = $dashboard->getCountAnswers(3,5);
                    echo "<i style=\"margin-left: 40px\">От 3 до 5 минут:</i> <b>".$countAnswers35." (". round(($countAnswers35/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    $countAnswers15 = $dashboard->getCountAnswers(15,999999);
                    echo "<i style=\"margin-left: 40px\">Больее 15 минут: </i><b>".$countAnswers15." (". round(($countAnswers15/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    $countViolation = $dashboard->getViolation();
                    echo "Количество нарушений: <b>".$countViolation." (". round(($countViolation/$totalAnswers)*100, 2) ."%)" . "</b><br>";


                    //                    $totalAnswers = $countAnswers01 + $countAnswers12 + $countAnswers23 +$countAnswers35 + $countAnswers15;

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
