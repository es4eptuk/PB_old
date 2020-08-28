<?php
include 'include/class.inc.php';

$arr_pos = [];
$robot_option_id = 0;

if (isset($_GET['option'])) {
    $robot_option_id = (isset($_GET['option']) && !empty($_GET['option'])) ? $_GET['option'] : 0;
    if ($robot_option_id != 0) {
        $arr_pos = $robots->get_composition_option($robot_option_id);
    }
}

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
            <h1>Комплектация устройства</h1>
        </section><!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <div style="width:300px;margin:10px 20px;">
                                <div class="form-group">
                                    <select class="form-control select2" name="option" id="option">
                                        <option value="0">Выбирите опцию</option>
                                        <?php
                                        $arr = $robots->getOptions;
                                        foreach ($arr as $option) {
                                            $selected = ($option['id_option'] == $robot_option_id) ? "selected" : "";
                                            echo "<option value='" . $option['id_option'] . " '".$selected.">" . $option['title'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <?php if ($robot_option_id != 0) {?>
                                <h3 class="box-title">Подверсия: <span id="writeoff_id"><?= $robots->getOptions[$robot_option_id]['title'] ?></span></h3>
                                <br>
                                <table class="table table-hover" id="listPos">
                                    <tbody>
                                    <tr>
                                        <th>posID</th>
                                        <th>Сборка</th>
                                        <th>Артикул</th>
                                        <th>Наименование</th>
                                        <th>Количество</th>
                                        <th>На складе</th>
                                        <th>Цена</th>
                                        <th>Стоимость</th>
                                        <th>Удаление</th>
                                    </tr>
                                    <?php

                                    usort($arr_pos, function ($a,$b) {
                                        return strcmp($a["title"], $b["title"]);
                                    });
                                    foreach ($arr_pos as $pos) {
                                        $assembly = ($pos['assembly'] == 0) ? '' :  '<i class="fa fa-check"></i>';
                                        echo '   
                                    <tr> 
                                        <td>'.$pos['id_pos'].'</td>
                                        <td>'.$assembly.'</td>
                                        <td>'.$pos['vendor_code'].'</td>
                                        <td>'.$pos['title'].'</td> 
                                        <td class="quant">'.$pos['count'].'</td>                                         
                                        <td>'.$pos['total'].'</td>                                          
                                        <td>'.$pos['price'].'</td>                                        
                                        <td>'. ($pos['price']*$pos['count']).'</td>
                                        <td><i class="fa fa-2x fa-remove" style="cursor:pointer;"></i></td> 
                                    </tr>
                                ';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            <?php }?>
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

        $("#option").change(function () {
            var option = $("#option").val();
            if (option != 0) {
                window.location.href = "./composition_option.php?option=" + option;
            } else {
                window.location.href = "./composition_option.php";
            }
        });

        $("#listPos").on("click", ".fa-remove", function () {
            $(this).parent().parent().fadeOut("normal", function () {
                $(this).remove();
            });
        });

    });

</script>
</body>
</html>