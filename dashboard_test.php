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
                    <? $date_Today = date("Y-m-d");
                    $date_Today = date("Y-m-d");
                    $date_Yesterday = date("Y-m-d",strtotime('-1 days'));

                    $totalAnswers = $dashboard->getCountAnswers(0, 999999);
                    $totalAnswersToday = $dashboard->getCountAnswers(0, 999999, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                    $totalAnswersYesterday = $dashboard->getCountAnswers(0, 999999,$date_Yesterday . " 00:00:00",  $date_Yesterday. " 23:59:59");

                    $countAnswers01 = $dashboard->getCountAnswers(0,1);
                    $countAnswers01Today = $dashboard->getCountAnswers(0,1, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                    $countAnswers01Yesterday = $dashboard->getCountAnswers(0,1,$date_Yesterday . " 00:00:00",  $date_Yesterday. " 23:59:59");

                    $countAnswers12 = $dashboard->getCountAnswers(1,2);
                    $countAnswers12Today = $dashboard->getCountAnswers(1,2, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                    $countAnswers12Yesterday = $dashboard->getCountAnswers(1,2,$date_Yesterday . " 00:00:00",  $date_Yesterday. " 23:59:59");

                    $countAnswers23 = $dashboard->getCountAnswers(2,3);
                    $countAnswers23Today = $dashboard->getCountAnswers(2,3, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                    $countAnswers23Yesterday = $dashboard->getCountAnswers(2,3,$date_Yesterday . " 00:00:00",  $date_Yesterday. " 23:59:59");

                    $countAnswers35 = $dashboard->getCountAnswers(3,5);
                    $countAnswers35Today = $dashboard->getCountAnswers(3,5, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                    $countAnswers35Yesterday = $dashboard->getCountAnswers(3,5,$date_Yesterday . " 00:00:00",  $date_Yesterday. " 23:59:59");

                    $countAnswers15 = $dashboard->getCountAnswers(15,999999);
                    $countAnswers15Today = $dashboard->getCountAnswers(15,999999, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                    $countAnswers15Yesterday = $dashboard->getCountAnswers(15,999999,$date_Yesterday . " 00:00:00",  $date_Yesterday. " 23:59:59");



                    $countViolation = $dashboard->getViolation();
                    $countViolationToday = $dashboard->getViolation($date_Today . " 00:00:00", $date_Today . " 23:59:59");
                    $countViolationYesterday = $dashboard->getViolation($date_Yesterday . " 00:00:00",  $date_Yesterday. " 23:59:59");
                    ?>
                    <h4>Нарушения:</h4>
                    <table class="table table-striped w-auto">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">За месяц</th>
                                <th scope="col">За сегодня</th>
                                <th scope="col">За вчера</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">Количество</th>
                            <td><?php echo "<b>".$countViolation." (". round(($countViolation/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countViolationToday." (". round(($countViolationToday/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countViolationYesterday." (". round(($countViolationYesterday/$totalAnswersYesterday)*100, 2) ."%)" . "</b>" ?></td>

                        </tr>
                        </tbody>
                    </table>


                    <br>
                    <h4>Скорость ответа:</h4>

                    <table class="table table-striped w-auto">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">За месяц</th>
                            <th scope="col">За сегодня</th>
                            <th scope="col">За вчера</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">До 1 минуты:</th>
                            <td><?php echo "<b>".$countAnswers01." (". round(($countAnswers01/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers01Today." (". round(($countAnswers01Today/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers01Yesterday." (". round(($countAnswers01Yesterday/$totalAnswersYesterday)*100, 2) ."%)" . "</b>" ?></td>

                        </tr>
                        <tr>
                            <th scope="row">От 1 до 2 минут:</th>
                            <td><?php echo "<b>".$countAnswers12." (". round(($countAnswers12/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers12Today." (". round(($countAnswers12Today/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers12Yesterday." (". round(($countAnswers12Yesterday/$totalAnswersYesterday)*100, 2) ."%)" . "</b>" ?></td>

                        </tr>
                        <tr>
                            <th scope="row">От 2 до 3 минут:</th>
                            <td><?php echo "<b>".$countAnswers23." (". round(($countAnswers23/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers23Today." (". round(($countAnswers23Today/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers23Yesterday." (". round(($countAnswers23Yesterday/$totalAnswersYesterday)*100, 2) ."%)" . "</b>" ?></td>
                        </tr>
                        <tr>
                            <th scope="row">От 3 до 5 минут:</th>
                            <td><?php echo "<b>".$countAnswers35." (". round(($countAnswers35/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers35Today." (". round(($countAnswers35Today/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers35Yesterday." (". round(($countAnswers35Yesterday/$totalAnswersYesterday)*100, 2) ."%)" . "</b>" ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Более 15 минут:</th>
                            <td><?php echo "<b>".$countAnswers15." (". round(($countAnswers15/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers15Today." (". round(($countAnswers15Today/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                            <td><?php echo "<b>".$countAnswers15Yesterday." (". round(($countAnswers15Yesterday/$totalAnswersYesterday)*100, 2) ."%)" . "</b>" ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Всего ответов</th>
                            <td><?php echo "<b>".$totalAnswers . "</b>" ?></td>
                            <td><?php echo "<b>".$totalAnswersToday . "</b>" ?></td>
                            <td><?php echo "<b>".$totalAnswersYesterday . "</b>" ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                   <!-- --><?/*
                    $date_Today = date("Y-m-d");
                    $date_Yesterday = date("Y-m-d",strtotime('-1 days'));

                    $totalAnswers = $dashboard->getCountAnswers(0, 999999);

                    $countAnswers01 = $dashboard->getCountAnswers(0,1);
                    $countAnswers12 = $dashboard->getCountAnswers(1,2);
                    $countAnswers23 = $dashboard->getCountAnswers(2,3);
                    $countAnswers35 = $dashboard->getCountAnswers(3,5);
                    $countAnswers15 = $dashboard->getCountAnswers(15,999999);

                    $countViolation = $dashboard->getViolation();
                    $countViolationToday = $dashboard->getViolation($date_Today . " 00:00:00", $date_Today . " 23:59:49");
                    $countViolationYesterday = $dashboard->getViolation($date_Yesterday . " 00:00:00",  $date_Yesterday. " 23:59:49");

                    echo "Количество нарушений за месяц: <b>".$countViolation." (". round(($countViolation/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    echo "Всего ответов: <b>" . $totalAnswers . "</b><br>";

                    echo "Скорость ответа: <br>";
                    echo "<i style=\"margin-left: 40px\">До 1 минуты: </i><b>".$countAnswers01." (". round(($countAnswers01/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    echo "<i style=\"margin-left: 40px\">От 1 до 2 минут: </i><b>".$countAnswers12." (". round(($countAnswers12/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    echo "<i style=\"margin-left: 40px\">От 2 до 3 минут: </i><b>".$countAnswers23." (". round(($countAnswers23/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    echo "<i style=\"margin-left: 40px\">От 3 до 5 минут:</i> <b>".$countAnswers35." (". round(($countAnswers35/$totalAnswers)*100, 2) ."%)" . "</b><br>";

                    echo "<i style=\"margin-left: 40px\">Больее 15 минут: </i><b>".$countAnswers15." (". round(($countAnswers15/$totalAnswers)*100, 2) ."%)" . "</b><br>";



                    echo "Количество нарушений сегодня: <b>".$countViolationToday."</b><br>";

                    echo "Количество нарушений за вчера: <b>".$countViolationYesterday."</b><br>";

//                    echo $date_Today;
//                    echo $date_Yesterday;

                    //                    $totalAnswers = $countAnswers01 + $countAnswers12 + $countAnswers23 +$countAnswers35 + $countAnswers15;

                    */?>



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
