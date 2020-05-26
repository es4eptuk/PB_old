<?php
include 'include/class.inc.php';

$robot_id = $_GET['id'];
$robot_info = $robots->get_info_robot($robot_id);
$robot_version = $robot_info['version'];
$robot_number = $robot_info['number'];
?>
<?php include 'template/head.php' ?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'template/header.php' ?>
    <!-- Left side column. contains the logo and sidebar -->
    <?php include 'template/sidebar.php';?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Списания</h1>
        </section><!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Списание на робота <span id="writeoff_id"><?php echo $robot_version.".".$robot_number; ?></span></h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">



                            <table class="table table-hover" id="listPos">
                                <tbody>
                                <tr>
                                    <th>posID</th>
                                    <th>Сборка</th>
                                    <th>Артикул</th>
                                    <th>Наименование</th>
                                    <th>Количество</th>
                                    <th>Удаление</th>
                                </tr>
                                <?php
                                $arr_pos = $writeoff->get_writeoff_on_robot($robot_id);
                                usort($arr_pos, function ($a,$b) {
                                    return strcmp($a["title"], $b["title"]);
                                });
                                foreach ($arr_pos as $pos) {
                                    $assembly = ($pos['assembly'] == 0) ? '' :  '<i class="fa fa-check"></i>';
                                    echo '   
                                    <tr> 
                                        <td>'.$pos['pos_id'].'</td>
                                        <td>'.$assembly.'</td>
                                        <td>'.$pos['vendor_code'].'</td>
                                        <td>'.$pos['title'].'</td> 
                                        <td class="quant"><span style="position:absolute;font-size:14px;font-weight:700;color:#c71616;">'.$pos['count'].'</span></td>
                                        <td><i class="fa fa-2x fa-remove" style="cursor:pointer;"></i></td> 
                                    </tr>
                                ';
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
<!-- Modal -->
<?php include 'template/scripts.php'; ?>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script>

    $(document).ready(function () {

        $("#listPos").on("click", ".fa-remove", function () {
            $(this).parent().parent().fadeOut("normal", function () {
                $(this).remove();
            });
        });

    });

</script>
</body>
</html>