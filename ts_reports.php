<?php
include 'include/class.inc.php';

function file_force_download($file) {
    if (file_exists($file)) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        unlink($file);
        exit;
    }
}

if (isset($_POST['owner'])) {
    $file = $tickets->get_report_owner($_POST['owner_id'], $_POST['owner_date']);
    file_force_download($file);
}

if (isset($_POST['resolved'])) {
    $date = explode(' - ', $_POST['resolved_date']);
    $date_start = $date[0];
    $date_end = $date[1];
    $resolved_name = (isset($_POST['resolved_name']) && !empty($_POST['resolved_name'])) ? $_POST['resolved_name'] : null;
    $resolved_number = (isset($_POST['resolved_number']) && !empty($_POST['resolved_number'])) ? $_POST['resolved_number'] : null;
    $resolved_version = (isset($_POST['resolved_version']) && !empty($_POST['resolved_version'])) ? $_POST['resolved_version'] : null;
    $file = $tickets->get_report_resolved($date_start, $date_end, $resolved_name, $resolved_number, $resolved_version);
    file_force_download($file);
}

?>
<?php include 'template/head.php' ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'template/header.php' ?>
    <?php include 'template/sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Отчеты</h1>
        </section>
        <section class="content">

            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">По владельцу</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post" name="owner">
                                <div style="width:300px;float:left;margin-right:20px;">
                                    <div class="form-group">
                                        <select class="form-control select2" name="owner_id" id="owner_id">
                                            <option value="0">Выберите владельца...</option>
                                            <?php
                                            $arr = $robots->get_customers();
                                            foreach ($arr as $customer) {
                                                echo "<option value='" . $customer['id'] . "'>" . $customer['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div style="float:left;margin-right:20px;">
                                    <div class="form-group">
                                        <input type="text" name="owner_date" id="owner_date" value="<?= date('Y-m-d').' - '.date('Y-m-d') ?>" hidden>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-default pull-right" name="owner_daterange" id="owner_daterange">
                                                <span><i class="fa fa-calendar"></i> Период</span> <i class="fa fa-caret-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div style="float:right;margin-right:20px;">
                                    <button class="btn btn-primary" type="submit" id="owner" name="owner">Сформировать</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">По закрытм тикетам</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post" name="resolved">
                                <div style="float:left;margin-right:20px;">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="resolved_name" id="resolved_name" placeholder="Имя">
                                    </div>
                                </div>
                                <div style="float:left;margin-right:20px;">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="resolved_number" id="resolved_number" placeholder="Номер">
                                    </div>
                                </div>
                                <div style="float:left;margin-right:20px;">
                                    <div class="form-group">
                                        <select class="form-control select2" name="resolved_version" id="resolved_version">
                                            <option value="0">Версия</option>
                                            <?php
                                            $arr = $robots->getEquipment;
                                            foreach ($arr as $version) {
                                                echo "<option value='" . $version['id'] . "'>" . $version['title'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div style="float:left;margin-right:20px;">
                                    <div class="form-group">
                                        <input type="text" name="resolved_date" id="resolved_date" value="<?= date('Y-m-d').' - '.date('Y-m-d') ?>" hidden>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-default pull-right" name="resolved_daterange" id="resolved_daterange">
                                                <span><i class="fa fa-calendar"></i> Период</span> <i class="fa fa-caret-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div style="float:right;margin-right:20px;">
                                    <button class="btn btn-primary" type="submit" id="resolved" name="resolved">Сформировать</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
    <div class="control-sidebar-bg"></div>
</div>
<?php include 'template/scripts.php'; ?>
<!-- date-range-picker -->
<script src="./bower_components/moment/min/moment.min.js"></script>
<script src="./bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="./bower_components/bootstrap-daterangepicker/daterangepicker.css" />
<!-- Select2 -->
<script src="./bower_components/select2/dist/js/select2.full.min.js"></script>
<script>

    $(document).ready(function() {

        //Select2
        $('#owner_id').select2();

        //Date range as a button
        $('#owner_daterange').daterangepicker({
                //opens: 'right',
                locale: {
                    format: 'DD/MM/YYYY',
                    firstDay: 1
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment(), /*moment().subtract(29, 'days'),*/
                endDate: moment()
            },
            function (start, end) {
                $('#owner_daterange span').html(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));
                $('input#owner_date').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            }
        )

        //Date range as a button
        $('#resolved_daterange').daterangepicker({
                opens: 'right',
                locale: {
                    format: 'DD/MM/YYYY',
                    firstDay: 1
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment(),
                endDate: moment()
            },
            function (start, end) {
                $('#resolved_daterange span').html(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));
                $('input#resolved_date').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            }
        )
        //
        /*$("body").on('click', '#owner',function () {
            var id = $('#owner_id').val();
            var date = $('#owner_date').val();
            console.log(id);
            console.log(date);
        });*/
    });
</script>
</body>
</html>
