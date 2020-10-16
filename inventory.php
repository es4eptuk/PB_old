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

if (isset($_POST['unload'])) {
    $category = ($_POST['unload_category'] != 0) ? $_POST['unload_category'] : null;
    $subcategory = ($_POST['unload_subcategory'] != 0) ? $_POST['unload_subcategory'] : null;
    $file = $position->get_file_pos_item($category, $subcategory);
    file_force_download($file);
}

?>

<?php include 'template/head.php' ?>

<style>
    .assembly {
        font-size: 10px;
        margin-top: 10px;
    }
    
</style>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<?php include 'template/header.php' ?>
		<!-- Left side column. contains the logo and sidebar -->
		<?php include 'template/sidebar.php';?>
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>Инвентаризация</h1>
			</section><!-- Main content -->
			<section class="content">

				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-body">
								    <div class="form-group input-group" id="pos">
                                      <input type="text" class="form-control" name="pos" id="search_pos" placeholder="Введите название позиции...">
                                      <span class="input-group-btn">
                                          <button type="button" class="btn btn-info btn-flat" id="add">+</button>
                                      </span>
                                    </div>
								    <h2 id="title"></h2>
								    <h4>Артикул: <span id="vendor_code"></span></h4>
							     	<h4>На складе: <b id="total"></b></h4>
							     	<div class="form-group">
                                      <label>Новое количество</label>
                                      <input type="text" class="form-control" name="quant_total" placeholder="0" id="quant_total">
                                    </div>
                                      <input type="hidden" class="form-control" name="id_pos" placeholder="0" id="id_pos">
								    <h5>Результат: <b id="result" class="text-red"></b></h5>
									<div class="box-footer">
										<button class="btn btn-primary" id="save_close" type="submit">Обновить данные</button> 
									</div>
							</div><!-- /.box-body -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->

                <div class="box box-default collapsed-box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Выгрузить шаблон для инвенторизации</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="post" name="unload">
                                    <div style="float:left;margin-right:20px;">
                                        <div class="form-group">
                                            <select class="form-control" name="unload_category" id="unload_category">
                                                <option value="0">Категория</option>
                                                <?php
                                                $arr = $position->getCategoryes;
                                                foreach ($arr as $category) {
                                                    if ($category['id'] == 0) {continue;}
                                                    echo "<option value='" . $category['id'] . "'>" . $category['title'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div style="float:left;margin-right:20px;">
                                        <div class="form-group">
                                            <select class="form-control" name="unload_subcategory" id="unload_subcategory">
                                                <option value="0">Подкатегория</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div style="float:right;margin-right:20px;">
                                        <button class="btn btn-primary" type="submit" id="unload" name="unload">Выгрузить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-default collapsed-box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Пакетная загрузка</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                    <div style="float:left;margin-right:20px;">
                                        <div class="form-group">
                                            <input type="file" id="upload_file" name="upload_file" accept=".xlsx">
                                            <p class="help-block"></p>
                                        </div>
                                    </div>
                                    <div style="float:right;margin-right:20px;">
                                        <button class="btn btn-primary" type="submit" id="upload" name="upload">Загрузить</button>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <table id="invent-upload" class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>Изображение</th>
                                    <th>ID</th>
                                    <th>Артикул</th>
                                    <th>Наименование</th>
                                    <th>Ед.изм.</th>
                                    <th>Категория</th>
                                    <th>Подкатегория</th>
                                    <th>Сборная позиция</th>
                                    <th>Изменение кол-ва</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <p id="error" style="color:#dc322f;font-size:14px;font-weight:bold;"></p>
                    </div>
                </div>

            </section><!-- /.content -->
		</div><!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
		<div class="control-sidebar-bg"></div>
	</div><!-- ./wrapper -->
	<!-- Modal -->

	<?php include 'template/scripts.php'; ?>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- Select2 -->
    <script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
	<script>

    $(document).ready(function() {

        $('.select2').select2();

        var arr_str = [];
        var pos_info = [];

        $("#save_close").click(function () {
            $(this).last().addClass("disabled");
            save_close();
            return false;
        });

        $("#search_pos").autocomplete({
            source: "./tt.php", // url-адрес
            minLength: 2 // минимальное количество для совершения запроса
        });

        $("#add").click(function () {
            var str = $('#search_pos').val();
            arr_str = str.split('::');
            var id = arr_str[0];
            var vendor_code = arr_str[1];
            var title = arr_str[2];
            var subcategory = "";
            var assembly = 0;
            var out_assembly = "";
            $.post("./api.php", {
                action: "get_info_pos",
                id: id
            }).done(function (data1) {
                pos_info = jQuery.parseJSON(data1);
                title = pos_info['title'];
                $('#title').text(title);
                $('#id_pos').val(pos_info['id']);
                $('#vendor_code').text(pos_info['vendor_code']);
                $('#total').text(pos_info['total']);
                $('#result').text("");
            });
            //arr_ids.push([arr_str[0], arr_str[1]]);
            return false;
        });

        $("#upload").click(function () {
            var file = $('#upload_file').prop('files')[0];
            var form_data = new FormData();
            form_data.append('upload_file', file);
            form_data.append('action', 'upload_inventory_file');
            $.ajax({
                url: './api.php',
                //dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (data) {
                    $('#invent-upload>tbody').html('');
                    $('#upload_file').val('');
                    $('#error').text('');
                    var result = jQuery.parseJSON(data);
                    var pos_items = result['result'];
                    var status = result['status'];
                    if (status == '200') {
                        $.each(pos_items, function (index, value) {
                            $("#invent-upload>tbody")
                                .append('<tr>\
                                    <td>' + value['img'] + '</td>\
                                    <td>' + value['id'] + '</td>\
                                    <td>' + value['vendor_code'] + '</td>\
                                    <td>' + value['title'] + '</td>\
                                    <td>' + value['unit'] + '</td>\
                                    <td>' + value['category'] + '</td>\
                                    <td>' + value['subcategory'] + '</td>\
                                    <td>' + value['assembly'] + '</td>\
                                    <td>' + value['old_total'] + ' -> ' + value['new_total'] + '</td>\
                                </tr>');
                        });
                    } else {
                        $('#error').text(pos_items);
                    }
                }
            });
            return false;
        });

        function save_close() {
            var new_total = $("#quant_total").val();
            var id = $("#id_pos").val();
            //console.log(new_total);
            if (id != 0) {
                $.post("./api.php", {
                    action: "invent",
                    id: id,
                    new_total: new_total
                }).done(function (data) {
                    $('#result').text(data);
                    $('#quant_total').val('0');
                    $('#search_pos').val("");
                    $('#id_pos').val('');
                    $('#title').text('');
                    $('#vendor_code').text('');
                    $('#total').text('');
                });
            }
            return false;
        }

        //смена источника
        $("#unload_category").change(function () {
            var category_id = $('#unload_category').val();
            $.post("./api.php", {
                action: "get_pos_sub_category",
                subcategory: category_id,
            }).done(function (data) {
                $('option', $("#unload_subcategory")).remove();
                var list = jQuery.parseJSON(data);
                //console.log(list);
                if (list != null) {
                    $('#unload_subcategory').append($('<option id="0">Подкатегория</option>'));
                    $.each(list, function (key, value) {
                        $('#unload_subcategory')
                            .append($("<option></option>")
                                .attr("value", value['id'])
                                .text(value['title']));
                    });
                } else {
                    $('#unload_subcategory').append($('<option id="0">Подкатегория</option>'));
                }
            });
        });
    });

    </script>
</body>
</html>