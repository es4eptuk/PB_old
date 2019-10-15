<?php
include 'include/class.inc.php';
include 'page/dashboard.php';



?>

<?php include 'template/head.html' ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js"></script>
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

                            $labels = [];

                            $date_begin =  date("Y-m-d", strtotime('-7 days'));
                            $date_end = date("Y-m-d");

                            $period = new DatePeriod(
                                new DateTime("$date_begin"),
                                new DateInterval('P1D'),
                                new DateTime("$date_end")
                            );

                            foreach ($period as $date) {
                                $date_formated = $date->format("Y-m-d D");
                                array_push($labels,$date_formated);
                            }



                            $date_Today = date("Y-m-d");
                            $date_1 = date("Y-m-d",strtotime('-1 days'));
                            $date_2 = date("Y-m-d",strtotime('-2 days'));
                            $date_3 = date("Y-m-d",strtotime('-3 days'));
                            $date_4 = date("Y-m-d",strtotime('-4 days'));
                            $date_5 = date("Y-m-d",strtotime('-5 days'));
                            $date_6 = date("Y-m-d",strtotime('-6 days'));
                            $date_7 = date("Y-m-d",strtotime('-7 days'));

                            $totalAnswers = $dashboard->getCountAnswers(0, 999999);
                            $totalAnswersToday = $dashboard->getCountAnswers(0, 999999, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                            $totalAnswers_1 = $dashboard->getCountAnswers(0, 999999,$date_1 . " 00:00:00",  $date_1. " 23:59:59");
                            $totalAnswers_2 = $dashboard->getCountAnswers(0, 999999,$date_2 . " 00:00:00",  $date_2. " 23:59:59");
                            $totalAnswers_3 = $dashboard->getCountAnswers(0, 999999,$date_3 . " 00:00:00",  $date_3. " 23:59:59");
                            $totalAnswers_4 = $dashboard->getCountAnswers(0, 999999,$date_4 . " 00:00:00",  $date_4. " 23:59:59");
                            $totalAnswers_5 = $dashboard->getCountAnswers(0, 999999,$date_5 . " 00:00:00",  $date_5. " 23:59:59");
                            $totalAnswers_6 = $dashboard->getCountAnswers(0, 999999,$date_6 . " 00:00:00",  $date_6. " 23:59:59");
                            $totalAnswers_7 = $dashboard->getCountAnswers(0, 999999,$date_7 . " 00:00:00",  $date_7. " 23:59:59");

                            $countAnswers01 = $dashboard->getCountAnswers(0,1);
                            $countAnswers01Today = $dashboard->getCountAnswers(0,1, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                            $countAnswers01_1 = $dashboard->getCountAnswers(0,1,$date_1 . " 00:00:00",  $date_1. " 23:59:59");
                            $countAnswers01_2 = $dashboard->getCountAnswers(0,1,$date_2 . " 00:00:00",  $date_2. " 23:59:59");
                            $countAnswers01_3 = $dashboard->getCountAnswers(0,1,$date_3 . " 00:00:00",  $date_3. " 23:59:59");
                            $countAnswers01_4 = $dashboard->getCountAnswers(0,1,$date_4 . " 00:00:00",  $date_4. " 23:59:59");
                            $countAnswers01_5 = $dashboard->getCountAnswers(0,1,$date_5 . " 00:00:00",  $date_5. " 23:59:59");
                            $countAnswers01_6 = $dashboard->getCountAnswers(0,1,$date_6 . " 00:00:00",  $date_6. " 23:59:59");
                            $countAnswers01_7 = $dashboard->getCountAnswers(0,1,$date_7 . " 00:00:00",  $date_7. " 23:59:59");

                            $countAnswers12 = $dashboard->getCountAnswers(1,2);
                            $countAnswers12Today = $dashboard->getCountAnswers(1,2, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                            $countAnswers12_1 = $dashboard->getCountAnswers(1,2,$date_1 . " 00:00:00",  $date_1. " 23:59:59");
                            $countAnswers12_2 = $dashboard->getCountAnswers(1,2,$date_2 . " 00:00:00",  $date_2. " 23:59:59");
                            $countAnswers12_3 = $dashboard->getCountAnswers(1,2,$date_3 . " 00:00:00",  $date_3. " 23:59:59");
                            $countAnswers12_4 = $dashboard->getCountAnswers(1,2,$date_4 . " 00:00:00",  $date_4. " 23:59:59");
                            $countAnswers12_5 = $dashboard->getCountAnswers(1,2,$date_5 . " 00:00:00",  $date_5. " 23:59:59");
                            $countAnswers12_6 = $dashboard->getCountAnswers(1,2,$date_6 . " 00:00:00",  $date_6. " 23:59:59");
                            $countAnswers12_7 = $dashboard->getCountAnswers(1,2,$date_7 . " 00:00:00",  $date_7. " 23:59:59");

                            $countAnswers23 = $dashboard->getCountAnswers(2,3);
                            $countAnswers23Today = $dashboard->getCountAnswers(2,3, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                            $countAnswers23_1 = $dashboard->getCountAnswers(2,3,$date_1 . " 00:00:00",  $date_1. " 23:59:59");
                            $countAnswers23_2 = $dashboard->getCountAnswers(2,3,$date_2 . " 00:00:00",  $date_2. " 23:59:59");
                            $countAnswers23_3 = $dashboard->getCountAnswers(2,3,$date_3 . " 00:00:00",  $date_3. " 23:59:59");
                            $countAnswers23_4 = $dashboard->getCountAnswers(2,3,$date_4 . " 00:00:00",  $date_4. " 23:59:59");
                            $countAnswers23_5 = $dashboard->getCountAnswers(2,3,$date_5 . " 00:00:00",  $date_5. " 23:59:59");
                            $countAnswers23_6 = $dashboard->getCountAnswers(2,3,$date_6 . " 00:00:00",  $date_6. " 23:59:59");
                            $countAnswers23_7 = $dashboard->getCountAnswers(2,3,$date_7 . " 00:00:00",  $date_7. " 23:59:59");

                            $countAnswers35 = $dashboard->getCountAnswers(3,5);
                            $countAnswers35Today = $dashboard->getCountAnswers(3,5, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                            $countAnswers35_1 = $dashboard->getCountAnswers(3,5,$date_1 . " 00:00:00",  $date_1. " 23:59:59");
                            $countAnswers35_2 = $dashboard->getCountAnswers(3,5,$date_2 . " 00:00:00",  $date_2. " 23:59:59");
                            $countAnswers35_3 = $dashboard->getCountAnswers(3,5,$date_3 . " 00:00:00",  $date_3. " 23:59:59");
                            $countAnswers35_4 = $dashboard->getCountAnswers(3,5,$date_4 . " 00:00:00",  $date_4. " 23:59:59");
                            $countAnswers35_5 = $dashboard->getCountAnswers(3,5,$date_5 . " 00:00:00",  $date_5. " 23:59:59");
                            $countAnswers35_6 = $dashboard->getCountAnswers(3,5,$date_6 . " 00:00:00",  $date_6. " 23:59:59");
                            $countAnswers35_7 = $dashboard->getCountAnswers(3,5,$date_7 . " 00:00:00",  $date_7. " 23:59:59");

                            $countAnswers15 = $dashboard->getCountAnswers(15,999999);
                            $countAnswers15Today = $dashboard->getCountAnswers(15,999999, $date_Today . " 00:00:00", $date_Today . " 23:59:59");
                            $countAnswers15_1 = $dashboard->getCountAnswers(15,999999,$date_1 . " 00:00:00",  $date_1. " 23:59:59");
                            $countAnswers15_2 = $dashboard->getCountAnswers(15,999999,$date_2 . " 00:00:00",  $date_2. " 23:59:59");
                            $countAnswers15_3 = $dashboard->getCountAnswers(15,999999,$date_3 . " 00:00:00",  $date_3. " 23:59:59");
                            $countAnswers15_4 = $dashboard->getCountAnswers(15,999999,$date_4 . " 00:00:00",  $date_4. " 23:59:59");
                            $countAnswers15_5 = $dashboard->getCountAnswers(15,999999,$date_5 . " 00:00:00",  $date_5. " 23:59:59");
                            $countAnswers15_6 = $dashboard->getCountAnswers(15,999999,$date_6 . " 00:00:00",  $date_6. " 23:59:59");
                            $countAnswers15_7 = $dashboard->getCountAnswers(15,999999,$date_7 . " 00:00:00",  $date_7. " 23:59:59");



                            $countViolation = $dashboard->getViolation();
                            $countViolationToday = $dashboard->getViolation($date_Today . " 00:00:00", $date_Today . " 23:59:59");
                            $countViolation_1 = $dashboard->getViolation($date_1 . " 00:00:00",  $date_1. " 23:59:59");
                            $countViolation_2 = $dashboard->getViolation($date_2 . " 00:00:00",  $date_2. " 23:59:59");
                            $countViolation_3 = $dashboard->getViolation($date_3 . " 00:00:00",  $date_3. " 23:59:59");
                            $countViolation_4 = $dashboard->getViolation($date_4 . " 00:00:00",  $date_4. " 23:59:59");
                            $countViolation_5 = $dashboard->getViolation($date_5 . " 00:00:00",  $date_5. " 23:59:59");
                            $countViolation_6 = $dashboard->getViolation($date_6 . " 00:00:00",  $date_6. " 23:59:59");
                            $countViolation_7 = $dashboard->getViolation($date_7 . " 00:00:00",  $date_7. " 23:59:59");
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
                                    <td><?php echo "<b>".$countViolation_1." (". round(($countViolation_1/$totalAnswers_1)*100, 2) ."%)" . "</b>" ?></td>

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
                                    <td><?php echo "<b>".$countAnswers01_1." (". round(($countAnswers01_1/$totalAnswers_1)*100, 2) ."%)" . "</b>" ?></td>

                                </tr>
                                <tr>
                                    <th scope="row">От 1 до 2 минут:</th>
                                    <td><?php echo "<b>".$countAnswers12." (". round(($countAnswers12/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers12Today." (". round(($countAnswers12Today/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers12_1." (". round(($countAnswers12_1/$totalAnswers_1)*100, 2) ."%)" . "</b>" ?></td>

                                </tr>
                                <tr>
                                    <th scope="row">От 2 до 3 минут:</th>
                                    <td><?php echo "<b>".$countAnswers23." (". round(($countAnswers23/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers23Today." (". round(($countAnswers23Today/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers23_1." (". round(($countAnswers23_1/$totalAnswers_1)*100, 2) ."%)" . "</b>" ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">От 3 до 5 минут:</th>
                                    <td><?php echo "<b>".$countAnswers35." (". round(($countAnswers35/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers35Today." (". round(($countAnswers35Today/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers35_1." (". round(($countAnswers35_1/$totalAnswers_1)*100, 2) ."%)" . "</b>" ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Более 15 минут:</th>
                                    <td><?php echo "<b>".$countAnswers15." (". round(($countAnswers15/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers15Today." (". round(($countAnswers15Today/$totalAnswersToday)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers15_1." (". round(($countAnswers15_1/$totalAnswers_1)*100, 2) ."%)" . "</b>" ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Всего ответов</th>
                                    <td><?php echo "<b>".$totalAnswers . "</b>" ?></td>
                                    <td><?php echo "<b>".$totalAnswersToday . "</b>" ?></td>
                                    <td><?php echo "<b>".$totalAnswers_1 . "</b>" ?></td>
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
            <div class="box">
                <!-- AREA CHART -->
                <canvas id="myChart"></canvas>
            </div>

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
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["<? echo $labels[0] ?>", "<? echo $labels[1] ?>", "<? echo $labels[2] ?>", "<? echo $labels[3] ?>", "<? echo $labels[4] ?>", "<? echo $labels[5] ?>", "<? echo $labels[6] ?>"],
            datasets: [{
                label: 'До 1 минуты',
                data: [<? echo "$countAnswers01_7, $countAnswers01_6, $countAnswers01_5, $countAnswers01_4, $countAnswers01_3, $countAnswers01_2, $countAnswers01_1"?>],
                backgroundColor: [
                    'rgba(51, 153, 0, 1)'
                ],
                borderColor: [
                    'rgba(51, 153, 0, 1)'
                ],
                borderWidth: 1,
                fill: false
            }, {
                label: 'От 1 до 2 минут',
                data: [<? echo "$countAnswers12_7, $countAnswers12_6, $countAnswers12_5, $countAnswers12_4, $countAnswers12_3, $countAnswers12_2, $countAnswers12_1"?>],
                backgroundColor: [
                    'rgba(153, 204, 51, 1)'
                ],
                borderColor: [
                    'rgba(153, 204, 51, 1)',
                ],
                borderWidth: 1,
                fill: false
            }, {
                label: 'От 2 до 3 минут',
                data: [<? echo "$countAnswers23_7, $countAnswers23_6, $countAnswers23_5, $countAnswers23_4, $countAnswers23_3, $countAnswers23_2, $countAnswers23_1"?>],
                backgroundColor: [
                    'rgba(255, 204, 0, 1)'
                ],
                borderColor: [
                    'rgba(255, 204, 0, 1)',
                ],
                borderWidth: 1,
                fill: false
            }, {
                label: 'От 3 до 5 минут',
                data: [<? echo "$countAnswers35_7, $countAnswers35_6, $countAnswers35_5, $countAnswers35_4, $countAnswers35_3, $countAnswers35_2, $countAnswers35_1"?>],
                backgroundColor: [
                    'rgba(255, 153, 102, 1)'
                ],
                borderColor: [
                    'rgba(255, 153, 102, 1)',
                ],
                borderWidth: 1,
                fill: false
            },{
                label: 'Более 15 минут',
                data: [<? echo "$countAnswers15_7, $countAnswers15_6, $countAnswers15_5, $countAnswers15_4, $countAnswers15_3, $countAnswers15_2, $countAnswers15_1"?>],
                backgroundColor: [
                    'rgba(204,51,0,1)'
                ],
                borderColor: [
                    'rgba(204,51,0,1)',
                ],
                borderWidth: 2,
                fill: false
            }, {
                label: 'Всего ответов',
                data: [<? echo "$totalAnswers_7, $totalAnswers_6, $totalAnswers_5, $totalAnswers_4, $totalAnswers_3, $totalAnswers_2, $totalAnswers_1"?>],
                backgroundColor: [
                    'rgba(34,45,50,1)'
                ],
                borderColor: [
                    'rgba(34,45,50,1)',
                ],
                borderWidth: 2,
                fill: false
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            title: {
                display: true,
                text: "Предыдущие 7 дней",
                fontSize: 18
            }
        }
    });

</script>


</body>
</html>
