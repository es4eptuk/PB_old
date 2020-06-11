<?php 
include 'include/class.inc.php';

$id = $_GET['id'];
$robot_info = $robots->get_info_robot($id);
$robot_version = $robot_info['subversion'];
$robot_number = $robot_info['number'];
$robot_name = $robot_info['name'];
$robot_customer = $robot_info['customer'];
$robot_owner = $robot_info['owner'];
$robot_language_robot = $robot_info['language_robot'];
$robot_language_doc = $robot_info['language_doc'];
$robot_charger = $robot_info['charger'];
$robot_color = $robot_info['color'];
$robot_brand = $robot_info['brand'];
$robot_ikp = $robot_info['ikp'];
$robot_battery = $robot_info['battery'];
$robot_commissioning = $robot_info['commissioning'];
$robot_dop = $robot_info['dop'];
$robot_dop_manufactur = $robot_info['dop_manufactur'];
$robot_progress = $robot_info['progress'];
$delete = $robot_info['delete'];
$delivery = $robot_info['delivery'];
$robot_date = new DateTime($robot_info['date']);
$robot_date = $robot_date->format('d.m.Y');
$robot_test = new DateTime($robot_info['date_test']);
$robot_test = $robot_test->format('d.m.Y');
$robot_date_send = new DateTime($robot_info['date_send']);
$robot_date_send = ($robot_info['date_send'] == null) ? '' : $robot_date_send->format('d.m.Y');
$subversions = $robots->getSubVersion;
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
				<h1>Роботы</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Редактирование робота <?php echo $robot_version.".".$robot_number; ?></h3>
							</div><!-- /.box-header -->
							<div class="box-body">
                                <div class="form-group">
                                    <label>Версия</label>
                                    <select class="form-control" name="version" id="version" disabled>
                                        <?php
                                        foreach ($subversions as &$version) {
                                            if ($version['id'] == $robot_version) {
                                                echo "<option value='" . $version['id'] . "' selected>" . $version['title'] . "</option>";
                                            } else {
                                                echo "<option value='" . $version['id'] . "'>" . $version['title'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Номер робота</label></label>
                                    <input type="text" class="form-control" name="number" required="required" id="number" value="<?php echo $robot_number ?>" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                </div>
                                <div class="form-group">
                                    <label>Кодовое имя</label>
                                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $robot_name ?>">
                                </div>
                                <div class="form-group">
                                    <label>Покупатель <small>(<a href="#" data-toggle="modal" data-target="#add_customer">Добавить</a>)</small></label>
                                    <select class="form-control select2" name="customer" id="customer" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                        <option value="0">Веберите покупателя...</option>
                                        <?php
                                        $arr = $robots->get_customers();
                                        foreach ($arr as &$customer) {
                                            if ($customer['id'] == $robot_customer) {
                                                echo "<option value='" . $customer['id'] . "' selected>" . $customer['name'] . "</option>";
                                            } else {
                                                echo "<option value='" . $customer['id'] . "'>" . $customer['name'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Владелец <small>(<a href="#" data-toggle="modal" data-target="#add_customer">Добавить</a>)</small></label>
                                    <select class="form-control select2" name="owner" id="owner" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                        <option value="0">Веберите владельца...</option>
                                        <?php
                                        $arr = $robots->get_customers();
                                        foreach ($arr as &$customer) {
                                            if ($customer['id'] == $robot_owner) {
                                                echo "<option value='" . $customer['id'] . "' selected>" . $customer['name'] . "</option>";
                                            } else {
                                                echo "<option value='" . $customer['id'] . "'>" . $customer['name'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Язык на роботе</label>
                                    <select class="form-control" name="language_robot" id="language_robot" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                        <?php
                                        $language_robot = [
                                            "russian" => "Русский",
                                            "english" => "Английский",
                                            "spanish" => "Испаниский",
                                            "turkish" => "Турецкий",
                                            "arab" => "Арабский",
                                            "portuguese" => "Португальский",
                                            "german" => "Немецкий"
                                        ];
                                        foreach ($language_robot as $key => $value) {
                                            if ($key == $robot_language_robot) {
                                                echo "<option value='" . $key . "' selected>" . $value . "</option>";
                                            } else {
                                                echo "<option value='" . $key . "'>" . $value . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Язык инструкции</label>
                                    <select class="form-control" name="language_doc" id="language_doc" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                        <?php
                                        $language_doc = ["russian" => "Русский", "english" => "Английский"];
                                        foreach ($language_doc as $key => $value) {
                                            if ($key == $robot_language_doc) {
                                                echo "<option value='" . $key . "' selected>" . $value . "</option>";
                                            } else {
                                                echo "<option value='" . $key . "'>" . $value . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Напряжение зарядной станции</label>
                                    <select class="form-control" name="charger" id="charger" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                        <?php
                                        $charger = ["220" => "220", "110" => "110"];
                                        foreach ($charger as $key => $value) {
                                            if ($key == $robot_charger) {
                                                echo "<option value='" . $key . "' selected>" . $value . "</option>";
                                            } else {
                                                echo "<option value='" . $key . "'>" . $value . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group" id="allOptions">
                                    <label for="exampleInputFile">Комплектация</label>
                                    <?php
                                    $options = $robots->get_robot_options($id);
                                    foreach ($options as &$value) {
                                        $check = ($value['check'] == 1) ? "checked" : "";
                                        $disabled = ($robots->get_dis_check_option($value['id'], $id) || $robot_progress == 100) ? "disabled" : "";
                                        echo '<div class="checkbox"><label><input type="checkbox" class="check" id="' . $value['id'] . '" name="options" value=' . $value['id'] . ' ' . $check . ' ' . $disabled . '>' . $value['title'] . '</label></div>';
                                    }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Цвет</label>
                                    <input type="text" class="form-control" name="color" id="color" value="<?php echo $robot_color ?>" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                </div>
                                <div class="form-group">
                                    <label>Брендирование </label>
                                    <input type="text" class="form-control" name="brand" id="brand" value="<?php echo $robot_brand ?>" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                </div>
                                <div class="form-group">
                                    <label>ИКП</label>
                                    <input type="text" class="form-control" name="ikp" id="ikp" value="<?php echo $robot_ikp ?>" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                </div>
                                <div class="form-group">
                                    <label>Наличие АКБ</label>
                                    <select class="form-control" name="battery" id="battery" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                        <?php
                                        $battery = [0 => "Нет", 1 => "Да"];
                                        foreach ($battery as $key => $value) {
                                            if ($key == $robot_battery) {
                                                echo "<option  value='" . $key . "' selected>" . $value . "</option>";
                                            } else {
                                                echo "<option  value='" . $key . "'>" . $value . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Дополнительная информация</label>
                                    <input type="text" class="form-control" name="dop" id="dop" value="<?php echo $robot_dop ?>" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                </div>
                                <div class="form-group">
                                    <label>Информация от производства</label>
                                    <textarea rows="5" cols="45" class="form-control" name="dop_manufactur" id="dop_manufactur"><?php echo $robot_dop_manufactur ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Пусконаладка</label>
                                    <select class="form-control" name="commissioning" id="commissioning" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                        <?php
                                        $commissioning = [0 => "Нет", 1 => "Да"];
                                        foreach ($commissioning as $key => $value) {
                                            if ($key == $robot_commissioning) {
                                                echo "<option  value='" . $key . "' selected>" . $value . "</option>";
                                            } else {
                                                echo "<option  value='" . $key . "'>" . $value . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Информация по доставке (наличие колёс на кофре, адрес доставки, телефон и имя получателя, плательщик по доставке, аэропорт доставки)</label>
                                    <textarea rows="5" cols="45" class="form-control" name="delivery" id="delivery"><?php echo $delivery ?></textarea>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="send" <?php echo ($robot_progress == 100) ? "checked" : ""; ?> <?php echo ($robot_progress == 0 && $delete == 0) ? "" : "disabled"; ?> >
                                        Отправленный
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>Начало производства:</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" class="form-control pull-right" id="datepicker" class="datepicker" value="<?php echo $robot_date ?>" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Первый тест:</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" class="form-control pull-right" id="datepicker2" class="datepicker" value="<?php echo $robot_test ?>" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Первый тест:</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" class="form-control pull-right" id="datepicker3" class="datepicker" value="<?php echo $robot_date_send ?>" <?php echo ($robot_progress == 100) ? "disabled" : ""; ?>>
                                    </div>
                                </div>
                                <div id="update"></div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                                    <button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>
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
    <div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_customer" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить клиента</h5>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form data-toggle="validator" id="add_provider_form" name="add_provider_form" role="form">
                        <!-- select -->
                        <div class="form-group">
                            <label>Наименование</label> <input class="form-control" id="name_cust" name="name_cust" required="required" type="text">
                        </div>
                        <div class="form-group">
                            <label>ФИО</label> <input class="form-control" id="fio" name="fio" required="required" type="text">
                        </div>
                        <div class="form-group">
                            <label>Email</label> <input class="form-control" id="email" name="email" required="required" type="text">
                        </div>
                        <div class="form-group">
                            <label>Телефон</label> <input class="form-control" id="phone" name="phone" required="required" type="text">
                        </div>
                        <div class="form-group">
                            <label>Адрес</label> <input class="form-control" id="address" name="address" required="required" type="text">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button>
                    <button class="btn btn-primary" id="btn_add_customer" type="button">Добавить</button>
                </div>
            </div>
        </div>
    </div>

	<?php include 'template/scripts.php';?>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="./bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
    <script src="./bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js"></script>
    <!-- Select2 -->
    <script src="./bower_components/select2/dist/js/select2.full.min.js"></script>

    <script>
        //Date picker
        $('#datepicker').datepicker({
            format: 'dd.mm.yyyy',
            language: 'ru-Ru',
            autoclose: true
        })
        $('#datepicker2').datepicker({
            format: 'dd.mm.yyyy',
            language: 'ru-Ru',
            autoclose: true
        })
        $('#datepicker3').datepicker({
            format: 'dd.mm.yyyy',
            language: 'ru-Ru',
            autoclose: true
        })
        //Select2
        $('.select2').select2();

        //создать клиента
        $("#btn_add_customer").click(function () {
            var name_cust = $('#name_cust').val();
            var fio = $('#fio').val();
            var email = $('#email').val();
            var phone = $('#phone').val();
            var address = $('#address').val();
            if (name_cust != "") {
                $.post("./api.php", {
                    action: "add_customer",
                    name: name_cust,
                    fio: fio,
                    email: email,
                    phone: phone,
                    address: address
                }).done(function (data) {
                    console.log(data);
                    if (data == "false") {
                        alert("Data Loaded: " + data);
                        return false;
                    } else {
                        $('#customer').append("<option value='" + data + "' selected>" + name_cust + "<\/option>");
                        $('#add_customer').modal('hide');
                    }
                });
            }
        });

        $(document).ready(function () {
            //удаление робота
            $("#delete").click(function () {
                $(this).last().addClass("disabled");
                $.post("./api.php", {
                    action: "del_robot",
                    id: <?php echo $id ?>
                }).done(function (data) {
                    data = jQuery.parseJSON(data);
                    if (data['result'] == false) {
                        alert(data['err']);
                    } else {
                        window.location.href = "./robots.php";
                    }
                });
            });

            //сохранить изменения
            $("#save_close").click(function () {
                $(this).last().addClass("disabled");
                var options = [];
                var number = $('#number').val();
                var name = $('#name').val();
                var version = $('#version').val();
                var customer = $('#customer').val();
                var owner = $('#owner').val();
                var language_robot = $('#language_robot').val();
                var language_doc = $('#language_doc').val();
                var charger = $('#charger').val();
                var color = $('#color').val();
                var brand = $('#brand').val();
                var ikp = $('#ikp').val();
                var battery = $('#battery').val();
                var commissioning = $('#commissioning').val();
                var dop = $('#dop').val();
                var dop_manufactur = $('#dop_manufactur').val();
                var delivery = $('#delivery').val();
                var date_start = $('#datepicker').val();
                var date_test = $('#datepicker2').val();
                var date_send = $('#datepicker3').val();
                var send = $('#send').is(':checked') ? 1 : 0;
                //собираем отмеченные опции
                $('input[name=options]').each(function () {
                    if (this.checked) {
                        options.push($(this).val());
                    }
                });
                if (date_send === '') {
                    date_send = null;
                }
                $.post("./api.php", {
                    action: "edit_robot",
                    id: <?php echo $id ?>,
                    number: number,
                    version: version,
                    name: name,
                    options: options,
                    customer: customer,
                    owner: owner,
                    language_robot: language_robot,
                    language_doc: language_doc,
                    charger: charger,
                    color: color,
                    brand: brand,
                    ikp: ikp,
                    battery: battery,
                    dop: dop,
                    dop_manufactur: dop_manufactur,
                    date_start: date_start,
                    date_test: date_test,
                    date_send: date_send,
                    send: send,
                    delivery: delivery,
                    commissioning: commissioning
                }).done(function (data) {
                    if (data == "false") {
                        alert("Data Loaded: " + data);
                    } else {
                        window.location.href = "./robots.php";
                    }
                });
            });
        });

    </script>
</body>
</html>