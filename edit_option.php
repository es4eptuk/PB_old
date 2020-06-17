<?php 
include 'include/class.inc.php';

$option = $robots->get_info_option($_GET['id']);
$option_id = $option['id_option'];
$option_title = $option['title'];
$option_version = $option['version'];
$option_kit = $option['id_kit'];

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
				<h1>Опции робота</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Опция # <span id="option_id"><?php echo $option_id; ?></span> - <?php echo $option_title; ?></h3>
							</div><!-- /.box-header -->
							<div class="box-body">
								<form data-toggle="validator" id="edit" name="edit" role="form" method="post">
                                    <div class="form-group">
                                        <label>Версия робота</label>
                                        <select class="form-control" name="version" placeholder="Выберите категорию" id="version" required="required">
                                            <option value="0" <?php echo ($option_version==0)? "selected": "";?>>Для любой версии</option>
                                            <?php
                                            $arr = $robots->getEquipment;
                                            foreach ($arr as $version) {
                                                $selected = ($option_version==$version['id']) ? "selected" : "";
                                                echo "<option value='".$version['id']."' ".$selected.">".$version['title']."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
									<div class="form-group">
										<label>Название</label> 
										 <input type="text" class="form-control" name="title" required="required" id="title" value="<?php echo $option_title; ?> ">
									</div>
									
									
								
									
									<div class="box-footer">
										<button class="btn btn-primary" id="save_close" type="submit">Сохранить</button> 
										<button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>
									</div>
								</form>
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
            //активация функции записи при нажатии кнопки
            $("#save_close").click(function () {
                $(this).last().addClass("disabled");
                save_close();
                return false;
            });
            //функция отправки данных на запись
            function save_close() {
                $(this).prop('disabled', true);
                var id = <?php echo $option_id;?>;
                var version = $("#version").val();
                var title = $("#title").val();
                //console.log(title.length);
                //return false;
                if (title.length <= 2) {
                    return false;
                }
                $.post("./api.php", {
                    action: "edit_option",
                    id: id,
                    version: version,
                    title: title
                }).done(function (data) {
                    console.log(data);
                    window.location.href = "./options.php";
                    return false;
                });
                return false;
            }
            //при нажатии на кнопку "удалить"
            $("#delete").click(function () {
                delete_pos();
                return false;
            });
            //функция удаления позиции
            function delete_pos() {
                var id = <?php echo $option_id;?>;
                $.post("./api.php", {
                    action: "del_option",
                    id: id
                }).done(function (data) {
                    if (data == "false") {
                        alert("Data Loaded: " + data);
                    } else {
                        window.location.href = "./options.php";
                    }
                });
            }
        });
	</script>
</body>
</html>