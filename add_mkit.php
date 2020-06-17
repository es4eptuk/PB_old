<?php

include 'include/class.inc.php';

$kit = $position->get_info_kit($_GET['parent']);
$kit_id = $kit['id_kit'];
$kit_title = $kit['kit_title'];
$kit_category = $kit['kit_category'];
$kit_version = $kit['kit_version'];
?>
<?php include 'template/head.php' ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'template/header.php' ?>

    <style>
        @media print {
            .input-group .form-control, .input-group-addon, .input-group-btn {
                display: block;
            }
        }

    </style>
    <!-- Left side column. contains the logo and sidebar -->
    <?php include 'template/sidebar.php';?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Комлекты</h1>
        </section><!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Копия -  # <span id="kit_id"><?php echo $kit_id; ?></span> - <?php echo $kit_title; ?></h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="form-group">
                                <label>Название</label>
                                <input type="text" class="form-control" name="title" required="required" id="title" value="<?php echo $kit_title." (модернизированный)"; ?> ">
                            </div>
                            <div class="form-group">
                                <label>Версия робота</label>
                                <select class="form-control" name="version" placeholder="Выберите версию" id="version" required="required">
                                    <?php
                                    $versions = $robots->getEquipment;
                                    foreach ($versions as &$version) {
                                        if ($kit_version==$version['id']) {
                                            echo "<option selected value='".$version['id']."'>".$version['title']."</option>";
                                        } else {
                                            echo "<option  value='".$version['id']."'>".$version['title']."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Категория</label>
                                <select class="form-control" id="category" name="category" required="required" >
                                    <option>Выберите категорию...</option>
                                    <?php
                                    $arr = $position->getCategoryes;
                                    foreach ($arr as &$category) {
                                        if ( $category['id'] == $kit_category ) {
                                            echo "<option value='".$category['id']."' selected>".$category['title']."</option>";
                                        } else {
                                            echo "<option value='".$category['id']."'>".$category['title']."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <p class="label">Добавить позицию</p>
                            <div class="form-group input-group" id="pos">
                                <input type="text" class="form-control" name="pos" id="search_pos" placeholder="Введите название позиции...">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-info btn-flat" id="add">+</button>
                                </span>
                            </div>
                            <table class="table table-bordered table-striped" id="listPos">
                                <thead>
                                <tr>
                                    <th>PosID</th>
                                    <th>Артикул</th>
                                    <th>Наименование</th>
                                    <th>Количество</th>
                                    <th>Удаление</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $arr_pos = $position->get_pos_in_kit($kit_id);
                                foreach ($arr_pos as &$value) {
                                    $title = $value['title'];
                                    $vendor_code = $value['vendor_code'];
                                    echo '
                                    <tr>
                                        <td>'.$value['id_pos'].'</td>
                                        <td>'.$vendor_code.'</td>
                                        <td>'.$title.'</td>
                                        <td class="quant"><span style="position: absolute;">'.$value['count'].'</span><input type="text" class="form-control quant_inp"  style="position: relative; height: 20px; width: 55px; text-align: center;" placeholder="'.$value['count'].'" value="'.$value['count'].'"></td>
                                        <td><i class="fa fa-2x fa-remove" style="cursor: pointer;"></i></td>
                                    </tr>
                                    ';
                                }
                                ?>
                                </tbody>
                            </table>
                            <div class="box-footer">
                                <button class="btn btn-primary" id="save_close" type="submit">Сохранить</button>
                                <button class="btn btn-primary" onclick="history.back();" >Закрыть</button>
                            </div>
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
        var arr_str = [];
        var pos_info = [];

        /*var arr_ids = [];
        var arr_pos = [];
        var category_data = [];
        var category1 = "---";*/

        $("#save_close").click(function () {
            $(this).last().addClass("disabled");
            save_close();
            return false;
        });

        $("#search_pos").autocomplete({
            source: "./tt.php", // url-адрес
            minLength: 2 // минимальное количество для совершения запроса
        });

        /*function set_category(data) {
            category1 = data;
            console.log(category1);
        }*/

        function set_subcategory(data) {
            subcategory1 = data;
        }

        $("#add").click(function () {
            var str = $('#search_pos').val();
            arr_str = str.split('::');
            var id = arr_str[0];
            var vendor_code = arr_str[1];
            var title = arr_str[2];
            var subcategory = "";
            $.post("./api.php", {
                action: "get_info_pos",
                id: id
            })
                .done(function (data1) {
                    pos_info = jQuery.parseJSON(data1);
                    $('#listPos tr:eq(0)').after('<tr> \
                        <td>'+pos_info['id']+'</td> \
                        <td>'+pos_info['vendor_code']+'</td> \
                        <td>'+pos_info['title']+'</td> \
                        <td class="quant"><span style="display:none;">1</span><input type="text" class="form-control quant_inp"  style="position: relative; height: 20px; width: 55px; text-align: center;" placeholder="1"></td> \
                        <td><i class="fa fa-2x fa-remove" style="cursor: pointer;"></i></td> \
                        </tr>');
                    $('#search_pos').val("");
                });
            return false;
        });

        $("#listPos").on("keyup", ".quant_inp", function () {
            var val = $(this).val();
            $(this).parent().find("span").text(val);
        });

        $("#listPos").on("click", ".fa-remove", function () {
            $(this).parent().parent().fadeOut("normal", function () {
                $(this).remove();
            });
        });

        function save_close() {
            var title = $("#title").val();
            var category = $("#category").val();
            var version = $("#version").val();
            var parent = <?php echo $kit_id; ?>;
            var TableArray = [];
            TableArray.push([title, category, version, parent]);
            $("#listPos tr").each(function () {
                var arrayOfThisRow = [];
                var tableData = $(this).find('td');
                if (tableData.length > 0) {
                    tableData.each(function () {
                        arrayOfThisRow.push($(this).text());
                    });
                    TableArray.push(arrayOfThisRow);
                }
            });
            var JsonString = JSON.stringify(TableArray);
            console.log(JsonString);
            if (title != "") {
                $.post("./api.php", {
                    action: "add_kit",
                    json: JsonString
                }).done(function (data) {
                    console.log(data);
                    window.location.href = "./kit.php";
                });
            }
            return false;
        }
    });
</script>
</body>
</html>