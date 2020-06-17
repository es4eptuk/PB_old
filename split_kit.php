<?php 
include 'include/class.inc.php';

$kit = $position->get_info_kit($_GET['id']);
$kit_id = $kit['id_kit'];
$kit_title = $kit['kit_title'];
$kit_category = $kit['kit_category'];
$kit_version = $kit['kit_version'];
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
				<h1>Комплекты</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Разделение Комлекта #<span id="kit_id"><?php echo $kit_id; ?></span> - <?php echo $kit_title; ?></h3>
							</div><!-- /.box-header -->
							<div class="box-body">
									<div class="form-group">
										<label>Название Комплекта №1</label>
										<input type="text" class="form-control" name="title1" required="required" id="title1" value="№1 <?php echo $kit_title; ?>">
									</div>
                                    <div class="form-group">
                                        <label>Название Комплекта №2</label>
                                        <input type="text" class="form-control" name="title2" required="required" id="title2" value="№2 <?php echo $kit_title; ?>">
                                    </div>
									<div class="form-group">
                                      <label>Версия робота</label>
                                      <select class="form-control" name="version" placeholder="Выберите версию" id="version_edit" required="required">
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
										<label>Категория</label><select class="form-control" id="category" name="category" required="required" >
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
                                      <th>posID</th>
                                      <th>Артикул</th>
                                      <th>Наименование</th>
                                      <th>Исход кол.</th>
                                      <th>Кол. компл. №1</th>
                                      <th>Кол. компл. №2</th>
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
                                            <td>' . $value['id_pos'] . '</td>
                                            <td>' . $vendor_code . '</td> 
                                            <td>' . $title . '</td> 
                                            <td>' . $value['count'] . '</td>                                             
                                            <td class="quant"><span style="position:absolute;">0</span><input type="text" class="form-control quant_inp" style="position:relative;height:20px;width:75px;text-align:center;" placeholder="0" value="0"></td>
                                            <td class="quant"><span style="position:absolute;">0</span><input type="text" class="form-control quant_inp" style="position:relative;height:20px;width:75px;text-align:center;" placeholder="0" value="0"></td>                                             
                                            <td><i class="fa fa-2x fa-remove" style="cursor: pointer;" id="' . $value['id_row'] . '"></i></td> 
                                            </tr>
                                            ';
                                   }
                                   ?>
                                    </tbody>
                                    </table>
									<div class="box-footer">
                                        <button class="btn btn-primary" id="save_close" type="submit">Разделить комплект</button>
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
                $.post("./api.php", {
                    action: "get_info_pos",
                    id: id
                }).done(function (data1) {
                    pos_info = jQuery.parseJSON(data1);
                    $('#listPos tr:eq(0)').after('<tr> \
                        <td>' + pos_info['id'] + '</td> \
                        <td>' + pos_info['vendor_code'] + '</td> \
                        <td>' + pos_info['title'] + '</td> \
                        <td>0</td> \
                        <td class="quant"><span style="position:absolute;">0</span><input type="text" class="form-control quant_inp"  style="position:relative;height:20px;width:75px;text-align:center;" placeholder="0" value="0"></td> \
                        <td class="quant"><span style="position:absolute;">0</span><input type="text" class="form-control quant_inp"  style="position:relative;height:20px;width:75px;text-align:center;" placeholder="0" value="0"></td> \
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
                $(this).parent().parent().remove();
            });

            function save_close() {
                $(this).prop('disabled', true);
                var title1 = $("#title1").val();
                var title2 = $("#title2").val();
                var category = $("#category").val();
                var version = $("#version_edit").val();
                var TableArray1 = [];
                var TableArray2 = [];
                TableArray1.push([title1, category, version,0]);
                TableArray2.push([title2, category, version,0]);
                $("#listPos tr").each(function() {
                    var pos_id = $(this).find('td:eq(0)').text();
                    if (pos_id != undefined) {
                        var count1 = $(this).find('td:eq(4) span').text();
                        var count2 = $(this).find('td:eq(5) span').text();
                        if (count1 > 0) {
                            TableArray1.push([pos_id, 0, 0, count1]);
                        }
                        if (count2 > 0) {
                            TableArray2.push([pos_id, 0, 0, count2]);
                        }
                    }
                });
                var JsonString1 = JSON.stringify(TableArray1);
                var JsonString2 = JSON.stringify(TableArray2);
                if (title1 != '' && title2 != '')  {
                    $.post("./api.php", {
                        action: "add_split_kit",
                        kit1: JsonString1,
                        kit2: JsonString2
                    }).done(function(data) {
                        window.location.href = "./kit.php?category="+category;
                    });
                }
                return false;
            }
        });
    </script>
</body>
</html>