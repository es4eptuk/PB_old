<?php 
include 'include/class.inc.php';

$writeoff1 = $writeoff->get_info_writeoff($_GET['id']);
$writeoff_id = $writeoff1['id'];
$writeoff_date = $writeoff1['update_date'];
$writeoff_category = $writeoff1['category'];
$writeoff_description = $writeoff1['description'];
$writeoff_price = $writeoff1['total_price'];
$writeoff_user_id = $writeoff1['update_user'];
$writeoff_provider = $writeoff1['provider_id'];
$disabled = ($writeoff1['written'] == 0) ? '' : 'disabled';
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
                                                    "Не актуально",
                                                    "Производство",
                                                    "ThermoControl",
                                                    "Удаленный склад",
                                                    "Медкейс",
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
                                        <label>Контрагент <small>(<a data-target="#add_provider" data-toggle="modal" href="#">Добавить</a>)</small></label>
                                        <select class="form-control select2" id="provider" name="provider" required="required">
                                            <option value="0">Выберите контррагента...</option>
                                            <?php
                                            $arr = $position->get_pos_provider();
                                            foreach ($arr as &$provider) {
                                                if ( $provider['id'] == $writeoff_provider ) {
                                                    echo "<option value='".$provider['id']."' selected>".$provider['type']." ".$provider['title']."</option>";
                                                } else {
                                                    echo "<option value='".$provider['id']."'>".$provider['type']." ".$provider['title']."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
							   	    <div class="form-group">
										<label>Описание</label> 
										 <input type="text" class="form-control" name="description" required="required" id="description" value="<?= $writeoff_description ?>">
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
                                        $rm = ($disabled == '') ? '<i class="fa fa-2x fa-remove" style="cursor: pointer;" id="'.$value['pos_id'].'" data-target="'.$value['pos_count'].'"></i>' : '';
                                        echo '   
                                            <tr> 
                                            <td>'.$value['id'].'</td>
                                            <td>'.$value['pos_id'].'</td>
                                            <td>'.$vendor_code.'</td> 
                                            <td>'.$title.'</td> 
                                            <td class="quant"><span style="position: absolute;">'.$value['pos_count'].'</span><input type="text" class="form-control quant_inp"  style="position: relative;  width: 55px; text-align: center;" placeholder="'.$value['pos_count'].'" value="'.$value['pos_count'].'" '.$disabled.'></td>
                                            <td class="price">'.$value['pos_price'].'</td>
                                            <td class="sum">'.$sum.'</td>  
                                            <td>'.$rm.'</td> 
                                            </tr>
                                        ';
                                    }
                                    ?>
                                    
                                    </tbody>
                                    </table>
									
							

							    	<div class="box-footer">
                                        <?= (/*$disabled == '' && */($userdata['group'] == 1 || $userdata['group'] == 4)/*($userdata['user_id'] == 35 || $userdata['user_id'] == 14 || $userdata['user_id'] == 75)*/) ? '<button class="btn btn-primary" id="save_close" type="submit">Сохранить</button>' : '';?>
                                        <?= ($disabled == '' && ($userdata['group'] == 1 || $userdata['group'] == 4)/*($userdata['user_id'] == 35 || $userdata['user_id'] == 14 || $userdata['user_id'] == 75)*/) ? '<button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>' : '';?>
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
    <!-- Modal -->
    <div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_provider" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить поставщика</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form data-toggle="validator" id="add_provider_form" name="add_provider_form" role="form">
                        <!-- select -->
                        <div class="form-group">
                            <label>Форма собственности</label> <select class="form-control" id="provider_type" name="provider_type" required="required">
                                <option value="ИП">
                                    ИП
                                </option>
                                <option value="ООО">
                                    ООО
                                </option>
                                <option value="ОАО">
                                    ОАО
                                </option>
                                <option value="ЗАО">
                                    Ltd.
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Наименование</label> <input class="form-control" id="provider_title" name="provider_title" required="required" type="text">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_add_provider" type="button">Добавить</button>
                </div>
            </div>
        </div>
    </div>
	<?php include 'template/scripts.php'; ?>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- Select2 -->
    <script src="./bower_components/select2/dist/js/select2.full.min.js"></script>

    <script>
        $(document).ready(function () {

            $('.select2').select2();

            $("#btn_add_provider").click(function() {
                var type = $('#provider_type').val();
                var title = $('#provider_title').val();
                //alert("123");
                if (title != "") {
                    $.post("./api.php", {
                        action: "add_pos_provider",
                        type: type,
                        title: title
                    }).done(function(data) {
                        console.log(data);
                        if (data == "false") {
                            alert("Data Loaded: " + data);
                            return false;
                        } else {
                            $('#provider').append("<option value='" + data + "' selected>" + title + "<\/option>");
                            $('#add_provider').modal('hide');
                            //return false;
                        }
                    });
                }
            });

            <?php if ($disabled == '') {?>
            change_total_price();

            $("#save_close").click(function () {
                $(this).last().addClass("disabled");
                save_close();
                return false;
            });

            $("#listPos").on("keyup", ".quant_inp", function () {
                var val = $(this).val();
                $(this).parent().find("span").text(val);
            });

            $("#listPos").on("keyup", ".quant_inp", function () {
                var price = $(this).parent().parent().find(".price").text();
                var quant = $(this).val();
                var sum = (price * quant).toFixed(2);
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
                //all_sum = Math.round(all_sum).toFixed(2);
                //console.log(all_sum);
                $("#total_price").text(all_sum.toFixed(2));
            }

            function save_close() {
                $(this).prop('disabled', true);
                var id = $("#writeoff_id").text();
                var title = "<?php echo $writeoff_category; ?>";
                var description = $("#description").val();
                var provider = $("#provider").val();
                var TableArray = [];
                TableArray.push([title, description, provider]);
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
            <?php } else {?>

            $("#save_close").click(function () {
                $(this).last().addClass("disabled");
                save_close();
                return false;
            });

            /*function save_close() {
                $(this).prop('disabled', true);
                var id = $("#writeoff_id").text();
                var description = $("#description").val();
                $.post("./api.php", {
                    action: "edit_description_writeoff",
                    id: id,
                    description: description
                }).done(function (data) {
                    console.log(data);
                    window.location.href = "./writeoff.php";
                    return false;
                });
                return false;
            }*/

            <?php } ?>
        });
    </script>

</body>
</html>