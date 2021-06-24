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
											<?php
											foreach ($writeoff::TYPES as &$value) {
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
										 <input type="text" class="form-control" name="description" required="required" id="description" value="<?php echo $writeoff_description; ?> " disabled>
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
                                            <td class="quant"><span style="position: absolute;">'.$value['pos_count'].'</span><input type="text" class="form-control quant_inp"  style="position: relative;  width: 55px; text-align: center;" placeholder="'.$value['pos_count'].'" value="'.$value['pos_count'].'" disabled></td>
                                            <td class="price">'.$value['pos_price'].'</td>
                                            <td class="sum">'.$sum.'</td>  
                                            </tr>
                                        ';
                                    }
                                    ?>
                                    
                                    </tbody>
                                    </table>

                                <div class="box-footer">
                                    <?php
                                    if ($userdata['user_id'] == 42 || $userdata['user_id'] == 14 || $userdata['user_id'] == 75 ) {
                                        if ($writeoff1['written'] == 0) {
                                            echo '<button class="btn btn-primary" id="conduct" type="submit">Провести</button>';
                                        } else {
                                            echo '<button class="btn btn-danger" id="unconduct" type="submit">Отменить проведение</button>';
                                        }
                                    }
                                    ?>
                                    <button type="reset" class="btn btn-default" id="del_filtr" name="" onclick="javascript:document.location = './writeoff_b.php'">Вернуться</button>
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

            $(".content").on("click", "#conduct", function () {
                $.post("./api.php", {
                    action: "conduct_writeoff",
                    id: <?php echo $writeoff_id; ?>,
                }).done(function (data) {
                    window.location.reload(true);
                });
            });

            $(".content").on("click", "#unconduct", function () {
                $.post("./api.php", {
                    action: "unconduct_writeoff",
                    id: <?php echo $writeoff_id; ?>,
                }).done(function (data) {
                    window.location.reload(true);
                });
            });

        });
    </script>
</body>
</html>