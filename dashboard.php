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

                            //формируем массив дат, где 0 сегодня.
                            $date_arr = array();

                            for ($i = 0; $i <= 30; $i++) {
                                $date_arr[$i] = date("Y-m-d",strtotime("- $i days"));
                            }

                            //разворачиваем массив в обратном порядке для графика
                            $date_arr_reversed = array_reverse($date_arr);

                            //формируем labels для графика
                            $labels = [];

                            $date_begin =  date("Y-m-d", strtotime('-30 days'));
                            $date_end = date("Y-m-d", strtotime('+1 day'));

                            $period = new DatePeriod(
                                new DateTime("$date_begin"),
                                new DateInterval('P1D'),
                                new DateTime("$date_end")
                            );

                            foreach ($period as $date) {
                                $date_formated = $date->format("Y-m-d D");
                                $date_formated_str = '"'. strval($date_formated) .'"';
                                array_push($labels, $date_formated_str);
                            }

                            $labels_str = implode(",", $labels);



                            //считаем количество ответов по дням за последние 31 день $date_arr
                            $totalAnswers_arr = array();
                            foreach ($date_arr_reversed as $value) {
                                $totalAnswers_arr[] = $dashboard->getCountAnswers(0, 999999,$value . " 00:00:00",  $value. " 23:59:59");
                            }
                            $totalAnswers_arr_str = implode(",", $totalAnswers_arr);


                            //old
                            $totalAnswers = $dashboard->getCountAnswers(0, 999999);



                            //считаем количество ответов от 0 до 1 минуты, по дням за последние 31 день $date_arr_reversed
                            $countAnswers01_arr = array();
                            foreach ($date_arr_reversed as $value) {
                                $countAnswers01_arr[] = $dashboard->getCountAnswers(0, 1,$value . " 00:00:00",  $value. " 23:59:59");
                            }
                            $countAnswers01_arr_str = implode(",", $countAnswers01_arr);

                            //old
                            $countAnswers01 = $dashboard->getCountAnswers(0,1);



                            //считаем количество ответов от 1 до 2 минут, по дням за последние 31 день $date_arr_reversed
                            $countAnswers12_arr = array();
                            foreach ($date_arr_reversed as $value) {
                                $countAnswers12_arr[] = $dashboard->getCountAnswers(1, 2,$value . " 00:00:00",  $value. " 23:59:59");
                            }
                            $countAnswers12_arr_str = implode(",", $countAnswers12_arr);

                            $countAnswers12 = $dashboard->getCountAnswers(1,2);



                            //считаем количество ответов от 2 до 3 минут, по дням за последние 31 день $date_arr_reversed
                            $countAnswers23_arr = array();
                            foreach ($date_arr_reversed as $value) {
                                $countAnswers23_arr[] = $dashboard->getCountAnswers(2, 3,$value . " 00:00:00",  $value. " 23:59:59");
                            }
                            $countAnswers23_arr_str = implode(",", $countAnswers23_arr);


                            $countAnswers23 = $dashboard->getCountAnswers(2,3);



                            //считаем количество ответов от 3 до 5 минут, по дням за последние 31 день $date_arr_reversed
                            $countAnswers35_arr = array();
                            foreach ($date_arr_reversed as $value) {
                                $countAnswers35_arr[] = $dashboard->getCountAnswers(3, 5,$value . " 00:00:00",  $value. " 23:59:59");
                            }
                            $countAnswers35_arr_str = implode(",", $countAnswers35_arr);

                            $countAnswers35 = $dashboard->getCountAnswers(3,5);



                            //считаем количество ответов от 5 до 9999999 минут, по дням за последние 31 день $date_arr_reversed
                            $countAnswers15_arr = array();
                            foreach ($date_arr_reversed as $value) {
                                $countAnswers15_arr[] = $dashboard->getCountAnswers(15, 999999,$value . " 00:00:00",  $value. " 23:59:59");
                            }
                            $countAnswers15_arr_str = implode(",", $countAnswers15_arr);

                            $countAnswers15 = $dashboard->getCountAnswers(15,999999);



                            //считаем количество арушений по дням за последние 31 день $date_arr_reversed
                            $countViolation_arr = array();
                            foreach ($date_arr_reversed as $value) {
                                $countViolation_arr[] = $dashboard->getViolation($value . " 00:00:00", $value. " 23:59:59");
                            }
                            $countViolation_arr_str = implode(",", $countViolation_arr);

                            $countViolation = $dashboard->getViolation();
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
                                    <td><?php echo "<b>".$countViolation_arr[30]." (". round(($countViolation_arr[30]/$totalAnswers_arr[30])*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countViolation_arr[29]." (". round(($countViolation_arr[29]/$totalAnswers_arr[29])*100, 2) ."%)" . "</b>" ?></td>

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
                                    <td><?php echo "<b>".$countAnswers01_arr[30]." (". round(($countAnswers01_arr[30]/$totalAnswers_arr[30])*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers01_arr[29]." (". round(($countAnswers01_arr[29]/$totalAnswers_arr[29])*100, 2) ."%)" . "</b>" ?></td>

                                </tr>
                                <tr>
                                    <th scope="row">От 1 до 2 минут:</th>
                                    <td><?php echo "<b>".$countAnswers12." (". round(($countAnswers12/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers12_arr[30]." (". round(($countAnswers12_arr[30]/$totalAnswers_arr[30])*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers12_arr[29]." (". round(($countAnswers12_arr[29]/$totalAnswers_arr[29])*100, 2) ."%)" . "</b>" ?></td>

                                </tr>
                                <tr>
                                    <th scope="row">От 2 до 3 минут:</th>
                                    <td><?php echo "<b>".$countAnswers23." (". round(($countAnswers23/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers23_arr[30]." (". round(($countAnswers23_arr[30]/$totalAnswers_arr[30])*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers23_arr[29]." (". round(($countAnswers23_arr[29]/$totalAnswers_arr[29])*100, 2) ."%)" . "</b>" ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">От 3 до 5 минут:</th>
                                    <td><?php echo "<b>".$countAnswers35." (". round(($countAnswers35/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers35_arr[30]." (". round(($countAnswers35_arr[30]/$totalAnswers_arr[30])*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers35_arr[29]." (". round(($countAnswers35_arr[29]/$totalAnswers_arr[29])*100, 2) ."%)" . "</b>" ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Более 15 минут:</th>
                                    <td><?php echo "<b>".$countAnswers15." (". round(($countAnswers15/$totalAnswers)*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers15_arr[30]." (". round(($countAnswers15_arr[30]/$totalAnswers_arr[30])*100, 2) ."%)" . "</b>" ?></td>
                                    <td><?php echo "<b>".$countAnswers15_arr[29]." (". round(($countAnswers15_arr[29]/$totalAnswers_arr[29])*100, 2) ."%)" . "</b>" ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Всего ответов</th>
                                    <td><?php echo "<b>".$totalAnswers . "</b>" ?></td>
                                    <td><?php echo "<b>".$totalAnswers_arr[30] . "</b>" ?></td>
                                    <td><?php echo "<b>".$totalAnswers_arr[29] . "</b>" ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>




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
            labels: [<? echo "$labels_str" ?>],
            datasets: [{
                label: 'До 1 минуты',
                data: [<? echo "$countAnswers01_arr_str" ?>],
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
                data: [<? echo "$countAnswers12_arr_str" ?>],
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
                data: [<? echo "$countAnswers23_arr_str" ?>],
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
                data: [<? echo "$countAnswers35_arr_str" ?>],
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
                data: [<? echo "$countAnswers15_arr_str" ?>],
                backgroundColor: [
                    'rgb(204,92,7)'
                ],
                borderColor: [
                    'rgb(204,92,7)',
                ],
                borderWidth: 1,
                fill: false
            }, {
                label: 'Всего ответов',
                data: [<? echo "$totalAnswers_arr_str" ?>],
                backgroundColor: [
                    'rgba(34,45,50,1)'
                ],
                borderColor: [
                    'rgba(34,45,50,1)',
                ],
                borderWidth: 2,
                fill: false
            }, {
                label: 'НАРУШЕНИЯ',
                data: [<? echo "$countViolation_arr_str" ?>],
                backgroundColor: [
                    'rgba(204,51,0,1)'
                ],
                borderColor: [
                    'rgba(204,51,0,1)',
                ],
                borderWidth: 3,
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
                text: "31 день включая сегодня",
                fontSize: 25
            }
        }
    });

</script>


</body>
</html>
