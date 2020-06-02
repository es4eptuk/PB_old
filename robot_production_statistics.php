<?php
include_once 'include/class.inc.php';

$arr_eq = $robots->getEquipment;

$v_filtr = [];
foreach ($arr_eq as $eq) {
    if (isset($_POST[$eq['id']])) {
        array_push($v_filtr, $eq['id']);
    }
}
?>

<?php include 'template/head.php' ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'template/header.php' ?>
    <!-- Left side column. contains the logo and sidebar -->
    <?php include 'template/sidebar.php';?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Статистика по роботам</h1>
        </section><!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Время учета сборки</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div>
                                <div class="" style="float:left;width:20%">
                                    <form action="" method="post">
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
                                            <button type="reset" class="btn btn-default" id="del_filtr" name="" onclick="javascript:document.location = 'robot_production_statistics.php'">Сбросить</button>
                                        </div>
                                    </form>
                                </div>
                                <div style="width:80%;margin-left:20%">
                                    <div>
                                        <canvas id="lineChart" height="130" width="600"></canvas>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <br>
                            <table id="statistics" class="table table-responsive">
                                <thead>
                                <tr>
                                    <th>Робот Id</th>
                                    <th>Версия</th>
                                    <th>Номер</th>
                                    <th>%</th>
                                    <th>Статус</th>
                                    <th>В паузе</th>
                                    <th>Начало сборки</th>
                                    <th>Конец сборки</th>
                                    <th>Пауза</th>
                                    <th>Итого</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $arr = $statistics->lists_robot_production_statistics();
                                    foreach ($arr as $id => $value) {
                                        if (!in_array($value['version'], $v_filtr) && $v_filtr != []) {
                                            continue;
                                        }
                                        if ($value['status'] == 'play') {
                                            $icon = 'fa fa-play';
                                        }
                                        if ($value['status'] == 'stop') {
                                            $icon = 'fa fa-stop';
                                        }
                                        if ($value['status'] == 'pause') {
                                            $icon = 'fa fa-pause';
                                        }
                                        echo "
                                            <tr>
                                                <td>" . $id . "</td>
                                                <td>" . $arr_eq[$value['version']]['title'] . "</td>
                                                <td><a href='./robot.php?id=".$id."'>" . $value['number'] . "</a></td>
                                                <td>" . $value['progress'] . "</td>
                                                <td><i class='" . $icon . "' style='cursor:pointer;font-size:16px;' data-id='" . $id . "'></i></td>
                                                <td>" . $value['current_pause'] . "</td>
                                                <td>" . $value['start'] . "</td>
                                                <td>" . $value['end'] . "</td>
                                                <td>" . $value['pause'] . "</td>
                                                <td>" . $value['time'] . "</td>
                                                <td><i class='fa fa-2x fa-remove' style='cursor: pointer;' data-id='" . $id . "'></i></td>
                                            </tr>
                                        ";
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

<?php include 'template/scripts.php'; ?>
<script src="./bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script src="./bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js"></script>
<!-- date-range-picker -->
<script src="./bower_components/moment/min/moment.min.js"></script>
<script src="./bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="./bower_components/bootstrap-daterangepicker/daterangepicker.css" />
<!-- Chart.JS -->
<script src="./bower_components/chart.js/Chart.js"></script>

<script>
    $(document).ready(function() {
        //для тестов
        $('#reservationtime').daterangepicker({
            opens: 'right',
            timePicker: true,
            timePickerIncrement: 30,
            timePicker24Hour: true,
            locale: {
                format: 'DD/MM/YYYY HH:mm',
                firstDay: 1
            }
        })
        //для таблицы
        var table = $('#statistics').DataTable({
            "iDisplayLength": 100,
            "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "All"]],
            "order": [[0, 'desc']],
            stateSave: false
        });
        //удалить
        $("#statistics").on('click', '.fa-remove', function () {
            var id = $(this).data("id");
            $.post("./api.php", {
                action: "del_robot_production_statistics",
                id: id
            }).done(function (data) {
                window.location.reload(true);
            });
        });
        //сменить статус
        $("#statistics").on('click', '.fa-play, .fa-pause', function () {
            var id = $(this).data("id");
            $.post("./api.php", {
                action: "change_status_robot_production_statistics",
                id: id
            }).done(function (data) {
                console.log(data);
                if (data == 'true') {
                    window.location.reload(true);
                } else {
                    return false;
                }
            });
        });

        //график
        /*var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
        var lineChartData = {
            labels : ["January","February","March","April","May","June","July"],
            datasets : [
                {
                    label: "My First dataset",
                    fillColor : "rgba(220,220,220,0.2)",
                    strokeColor : "rgba(220,220,220,1)",
                    pointColor : "rgba(220,220,220,1)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "rgba(220,220,220,1)",
                    data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
                },
                {
                    label: "My Second dataset",
                    fillColor : "rgba(151,187,205,0.2)",
                    strokeColor : "rgba(151,187,205,1)",
                    pointColor : "rgba(151,187,205,1)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "rgba(151,187,205,1)",
                    data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
                }
            ]

        }

        window.onload = function(){
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx).Line(lineChartData, {
                showScale               : true,
                scaleShowGridLines      : false,
                scaleGridLineColor      : 'rgba(0,0,0,.05)',
                backgroundColor : "rgba(0,0,0,0.0)",
                scaleGridLineWidth      : 1,
                scaleShowHorizontalLines: true,
                scaleShowVerticalLines  : true,
                bezierCurve             : true,
                bezierCurveTension      : 0.3,
                pointDot                : false,
                pointDotRadius          : 4,
                pointDotStrokeWidth     : 1,
                pointHitDetectionRadius : 20,
                datasetStroke           : true,
                datasetStrokeWidth      : 2,
                datasetFill             : true,
                maintainAspectRatio     : true,
                responsive              : true
            });
        }*/

        $(function () {
            //var areaChart       = new Chart(areaChartCanvas)
            var areaChartData = {
                labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [
                    {
                        label               : 'Electronics',
                        fillColor           : 'rgba(210, 214, 222, 1)',
                        strokeColor         : 'rgba(210, 214, 222, 1)',
                        pointColor          : 'rgba(210, 214, 222, 1)',
                        pointStrokeColor    : '#c1c7d1',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [65, 59, 80, 81, 56, 55, 40]
                    },
                ]
            }

            var areaChartOptions = {
                //Boolean - If we should show the scale at all
                showScale               : true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines      : false,
                //String - Colour of the grid lines
                scaleGridLineColor      : 'rgba(0,0,0,.05)',
                //Number - Width of the grid lines
                scaleGridLineWidth      : 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines  : true,
                //Boolean - Whether the line is curved between points
                bezierCurve             : true,
                //Number - Tension of the bezier curve between points
                bezierCurveTension      : 0.3,
                //Boolean - Whether to show a dot for each point
                pointDot                : false,
                //Number - Radius of each point dot in pixels
                pointDotRadius          : 4,
                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth     : 1,
                //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius : 20,
                //Boolean - Whether to show a stroke for datasets
                datasetStroke           : true,
                //Number - Pixel width of dataset stroke
                datasetStrokeWidth      : 2,
                //Boolean - Whether to fill the dataset with a color
                datasetFill             : true,
                //String - A legend template
                legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
                //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio     : true,
                //Boolean - whether to make the chart responsive to window resizing
                responsive              : true
            }

            //Create the line chart
            //areaChart.Line(areaChartData, areaChartOptions)

            //-------------
            //- LINE CHART -
            //--------------
            var lineChartCanvas          = $('#lineChart').get(0).getContext('2d')
            var lineChart                = new Chart(lineChartCanvas)
            var lineChartOptions         = areaChartOptions
            lineChartOptions.datasetFill = false
            lineChart.Line(areaChartData, lineChartOptions)

        });
    });

</script>

</body>
</html>
