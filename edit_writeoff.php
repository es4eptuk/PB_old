<?php 
include 'include/class.inc.php';

$writeoff1 = $writeoff->get_info_writeoff($_GET['id']);
$writeoff_id = $writeoff1['id'];
$writeoff_date = $writeoff1['update_date'];
$writeoff_category = $writeoff1['category'];
$writeoff_description = $writeoff1['description'];
$writeoff_price = $writeoff1['total_price'];
$writeoff_user_id = $writeoff1['update_user'];
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
				<h1>Списания</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Списание № <span id="writeoff_id"><?php echo $writeoff_id; ?></span></h3>
							</div><!-- /.box-header -->
							<div class="box-body">
                                <p class="p-label" style="font-size:16px;">Общая сумма списания: <span id="total_price"><?php echo number_format($writeoff_price, 2, ',', ' ') ; ?> </span></p>
							    <br>
                                <div class="form-group">
										<label>Категория</label> <select class="form-control" id="category" name="category" required="required" disabled>
											<?php $arr = [
											        "Модернизация",
                                                    "Брак",
                                                    "Сервис",
                                                    "Разработка",
                                                    "Продажа",
                                                    "Маркетинг",
                                                    "Содержание офиса",
                                                    "Давальческие материалы",
                                                    "Возврат поставщику",
                                                    "Покраска/Покрытие",
                                                    "Сварка/Зенковка",
                                                    "Не прокатило",
                                                    "Производство",
                                                    "ThermoControl",
                                                ];
											foreach ($arr as &$value) {
                                               if ($writeoff_category == $value) {
                                                   echo '<option value="'.$value.'" selected>'.$value.'</option>';
                                               } else {
                                                  echo '<option value="'.$value.'">'.$value.'</option>';
                                               }
                                            }
											
											?>
										</select>
									</div>
							   
							   	    <div class="form-group">
										<label>Описание</label> 
										 <input type="text" class="form-control" name="description" required="required" id="description" value="<?php echo $writeoff_description; ?> ">
									</div>
									
									<div class="print"><b>Категория: </b><?php echo $writeoff_category;?></div>
									<div class="print"><b>Описание: </b><?php echo $writeoff_description;?></div>
							   
									<table class="table table-hover" id="listPos">
                                    <tbody><tr>
                                      <th>row</th>
                                      <th>ID</th>
                                      <th>Артикул</th>
                                      <th>Наименование</th>
                                      <th>Количество</th>
                                      <th>Цена</th>
                                      <th>Сумма</th>
                                      <th>Удаление</th>
                                    </tr>
                                   
                                    <?php 
                                    $arr_pos = $writeoff->get_pos_in_writeoff($writeoff_id);
                                    $arr_pos = ($arr_pos) ? $arr_pos : [];
                                    foreach ($arr_pos as &$value) {
                                        $title = $value['pos_title'];
                                        $vendor_code = $value['vendor_code'];
                                        $sum = $value['pos_price'] * $value['pos_count'];
                                        echo '   
                                            <tr> 
                                            <td>'.$value['id'].'</td>
                                            <td>'.$value['pos_id'].'</td>
                                            <td>'.$vendor_code.'</td> 
                                            <td>'.$title.'</td> 
                                            <td class="quant"><span style="position: absolute;">'.$value['pos_count'].'</span><input type="text" class="form-control quant_inp"  style="position: relative; height: 20px; width: 55px; text-align: center;" placeholder="'.$value['pos_count'].'" value="'.$value['pos_count'].'"></td>
                                            <td class="price">'.$value['pos_price'].'</td>
                                            <td class="sum">'.$sum.'</td>  
                                            <td><i class="fa fa-2x fa-remove" style="cursor: pointer;" id="'.$value['pos_id'].'" data-target="'.$value['pos_count'].'"></i></td> 
                                            </tr>
                                        ';
                                    }
                                    ?>
                                    
                                    </tbody>
                                    </table>
									
							
							<?php 
							if ($userdata['user_id'] == 35 || $userdata['user_id'] == 14 || $userdata['user_id'] == 75 ) {
							    echo '
							    
							    	<div class="box-footer">
										<button class="btn btn-primary" id="save_close" type="submit">Сохранить</button> 
										<button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>
							        </div>
							    ';
							}
							
							?>
								
						
									
									
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
            /*
            var arr_str = [];
            var arr_ids = [];
            var arr_pos = [];
            var pos_info = [];
            var category_data = [];
            var category1 = "---";
            */
            change_total_price();

            $("#save_close").click(function () {
                $(this).last().addClass("disabled");
                save_close();
                return false;
            });
            /*
            function set_category(data) {
                category1 = data;
                console.log(category1);
            }

            function set_subcategory(data) {
                subcategory1 = data;
            }
            */
            $("#listPos").on("keyup", ".quant_inp", function () {
                var val = $(this).val();
                $(this).parent().find("span").text(val);
            });

            $("#listPos").on("keyup", ".quant_inp", function () {
                var price = $(this).parent().parent().find(".price").text();
                var quant = $(this).val();
                var sum = Math.round(price * quant).toFixed(2);
                $(this).parent().parent().find(".sum").text(sum);
                change_total_price();
            });

            $("#listPos").on("click", ".fa-remove", function () {
                var count = $(this).data("target");
                var id = $(this).attr('id');
                $.post("./api.php", {
                    action: "del_pos_writeoff",
                    id: <?php echo $writeoff_id; ?>,
                    pos_id: id,
                    count: count,
                }).done(function (data) {
                    window.location.reload(true);
                });
            });

            function change_total_price() {
                var all_sum = 0;
                $("#listPos tr").each(function () {
                    let isum = Number($(this).find('.sum').text());
                    all_sum = all_sum + isum;
                });
                all_sum = Math.round(all_sum).toFixed(2);
                //console.log(all_sum);
                $("#total_price").text(all_sum);
            }

            function save_close() {
                $(this).prop('disabled', true);
                var id = $("#writeoff_id").text();
                var title = "<?php echo $writeoff_category; ?>";
                var description = $("#description").val();
                var TableArray = [];
                TableArray.push([title, description]);
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
                //console.log(JsonString);
                $.post("./api.php", {
                    action: "edit_writeoff",
                    id: id,
                    json: JsonString
                }).done(function (data) {
                    console.log(data);
                    window.location.href = "./writeoff.php";
                    return false;
                });
                return false;
            }

            $("#delete").click(function () {
                $(this).last().addClass("disabled");
                delete_writeoff();
                return false;
            });

            function delete_writeoff() {
                var id = $("#writeoff_id").text();
                $.post("./api.php", {
                    action: "del_writeoff",
                    id: id
                }).done(function (data) {
                    if (data == "false") {
                        alert("Data Loaded: " + data);
                    } else {
                        // console.log(data);
                        window.location.href = "./writeoff.php";
                    }
                });
            }
        });
    </script>
</body>
</html>