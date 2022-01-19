<?php 
include 'include/class.inc.php';
global $userdata;

$today = date('Y-m-d');
$subversions = $robots->getSubVersion;

$arr_eq = $robots->getEquipment;

$v_filtr = [];
foreach ($arr_eq as $eq) {
    if (isset($_POST[$eq['id']])) {
        array_push($v_filtr, $eq['id']);
    }
}
if (isset($_POST['check_show_all'])) {
    $check_show_all = 1;
} else {
    $check_show_all = 0;
}

?>

<?php include 'template/head.php' ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include 'template/header.php' ?>
  <!-- Left side column. contains the logo and sidebar -->
  <?php include 'template/sidebar.php';?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Роботы
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">в производстве</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <div class="">
                    <form action="./robots.php" method="post">
                        <div class="form-group">
                            <?php
                            if (isset($_POST['check_show_all'])) {
                                $checked = 'checked';
                            } else {
                                $checked = '';
                            }
                            echo '
                            <div class="checkbox">
                                <label><input type="checkbox" id="check_show_all" name="check_show_all" '.$checked.'>Отображать завершенных роботов</label>
                            </div>
                            ';
                            foreach ($arr_eq as $eq) {
                                if (isset($_POST[$eq['id']])) {
                                    $checked = 'checked';
                                } else {
                                    $checked = '';
                                }
                                echo '<div class="checkbox">';
                                echo '<label><input type="checkbox" id="'.$eq['id'].'" name="'.$eq['id'].'" '.$checked.'> '.$eq['title'].'</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="add_filtr" name="">Применить</button>
                            <button type="reset" class="btn btn-default" id="del_filtr" name="" onclick="javascript:document.location = './robots.php'">Сбросить</button>
                        </div>
                    </form>
                </div>
                <br>
              <table id="robots" class="table  table-hover">
                <thead>
                <tr>
                  <th>Номер</th>
                  <th>Подверсия</th>
                  <th>Владелец</th>
                  <th>Кодовое имя</th>
                  <th style="width:100px;">Готовность, %</th>
                  <th style="width:100px;">Этап</th>
                  <th style="width:150px;">Начало производства</th>
                  <th style="width:150px;">Дата отгрузки</th>
                  <th style="width:120px;"></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $robots->get_robots();
                
                if (isset($arr)) {
                foreach ($arr as &$robot) {
                    if (!in_array($robot['version'], $v_filtr) && $v_filtr != []) {
                        continue;
                    }
                    if ($robot['progress']!=100 || $check_show_all== 1) {

                        $color = "#fff";
                        if ($robot['progress']>0) {$color = "#f1f7c1";}
                        if ($robot['date_send'] != null && $robot['date_send'] <= $today) {$color = "#c5d6f5;";}
                        if ($robot['progress']==100) {$color = "#c1f7cc";}
                        if ($robot['delete']==2) {$color = "#f5c5dd;";}


                        //$user_info = $user->get_info_user($robot['update_user']);
                        $date = new DateTime($robot['date']);
                        $robot_date = $date->format('d.m.Y');
                        //$robot_date_test = new DateTime($robot['date_test']);
                        if ($robot['date_send'] != null) {
                            $date = new DateTime($robot['date_send']);
                            $robot_date_send = $date->format('d.m.Y');
                            //$robot_date_send = $robot['date_send'];
                        } else {
                            $robot_date_send = '';
                        }
                        unset($date);
                         $num = str_pad($robot['number'], 4, "0", STR_PAD_LEFT);
                         $remont = "";
                         if ($robot['remont']>0) {$remont = '<br><small class="label bg-red">Модернизация</small>';}
                         $edit = ($userdata['user_id'] == 75 || $userdata['user_id'] == 14 || $userdata['user_id'] == 17 || $userdata['user_id'] == 77 || $userdata['user_id'] == 106 || $userdata['user_id'] == 104) ? "<a href='./edit_robot.php?id=".$robot['id']."'><i class='fa fa-2x fa-pencil' style='cursor: pointer;'></i></a>" : "";
                         if ($robot['delete']==2) {
                             $print = "";
                             $check = "";
                             if ($userdata['user_id'] == 75 || $userdata['user_id'] == 14 || $userdata['user_id'] == 17) {
                                 $check = "<i class='fa fa-2x fa-play' style='cursor:pointer;color:#337ab7;' data-id='".$robot['id']."'></i>";
                             }
                         } else {
                             $print = "<i class='fa fa-2x fa-print' style='cursor:pointer;color:#337ab7;' data-id='".$robot['id']."'></i>";
                             $check = "<a href='./robot.php?id=".$robot['id']."'><i class='fa fa-2x fa-align-justify' style='cursor: pointer;'></i></a>";
                         }
                         if ($robot['owner'] != 0) {
                             $owner = $robots->get_customers()[$robot['owner']];
                             $name = $owner['name'];
                             $ident = $owner['ident'];
                         } else {
                             $name = '';
                             $ident = '';
                         }
                         //искл для в3 подв3
                         if ($robot['subversion'] == 3) {
                             $sub = 'v3';
                         } else{
                             $sub = $subversions[$robot['subversion']]['title'];
                         }
                        $writeoff_link = "<a href='./edit_writeoff_on_robot.php?id=".$robot['id']."'><i class='fa fa-2x fa-dropbox' style='cursor: pointer;'></i></a>";
                         echo "
                            <tr class='edit' id='".$robot['id']."' style='cursor: pointer; background: ".$color.";'>
                                <td>".$robot['version'].".".$num."</td>
                                <td>".$sub."</td>
                                <td><span data-toggle='tooltip' data-html='true' data-delay='{\"show\":\"100\", \"hide\":\"300\"}' data-placement='bottom' title='".$ident."'>".$name."</span></td>
                                <td>".$robot['name']." ".$remont." </td>
                                <td>".$robot['progress']."</td>
                                <td>".$position->getCategoryes[$robot['stage']]['title']."</td>
                                <td>".$robot_date."</td>
                                <td>".$robot_date_send."</td>                                
                                <td>"
                                    .$check."&nbsp;&nbsp;&nbsp;&nbsp;"
                                    .$print."&nbsp;&nbsp;&nbsp;&nbsp;"
                                    .$edit."&nbsp;&nbsp;&nbsp;&nbsp;"
                                    .$writeoff_link."
                                </td>
                            </tr>
                         ";

                    }
                }
                }
               
                ?>
              </table>
              
              <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="btn_add_robot">Добавить робота</button>
                </div>
                
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 
 
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<!-- Modal tabindex="-1"  -->
<div class="modal fade" id="add_robot" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавление робота<span id="operation_id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
            <?php if ($userdata['user_id'] == 75 || $userdata['user_id'] == 14 || $userdata['user_id'] == 17) { /*вывод полной формы для избранных*/ ?>
                <form role="form" data-toggle="validator" id="add_pos">
                    <!-- text input -->
                    <!-- select -->
                    <div class="form-group">
                        <label>Подверсия робота</label>
                        <select class="form-control" name="version" id="version" required="required">
                            <option value="0">Выберите подверсию...</option>
                            <?php
                            foreach ($subversions as &$version) {
                                echo "<option value='" . $version['id'] . "'>" . $version['title'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Номер робота</label></label>
                        <input type="text" class="form-control" name="number" id="number" required="required">
                    </div>
                    <div class="form-group">
                        <label>Кодовое имя</label>
                        <input type="text" class="form-control" name="name" id="name">
                    </div>
                    <div class="form-group">
                        <label>Покупатель <small>(<a href="#" data-toggle="modal" data-target="#add_customer">Добавить</a>)</small></label>
                        <select class="form-control select2" name="customer" id="customer">
                            <option value="0">Выберите покупателя...</option>
                            <?php
                            $arr = $robots->get_customers();
                            foreach ($arr as &$customer) {
                                echo "<option value='" . $customer['id'] . "'>" . $customer['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Владелец <small>(<a href="#" data-toggle="modal" data-target="#add_customer">Добавить</a>)</small></label>
                        <select class="form-control select2" name="owner" id="owner">
                            <option value="0">Выберите владельца...</option>
                            <?php
                            $arr = $robots->get_customers();
                            foreach ($arr as &$customer) {
                                echo "<option value='" . $customer['id'] . "'>" . $customer['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Язык на роботе</label>
                        <select class="form-control" name="language_robot" id="language_robot">
                            <option value="russian">Русский</option>
                            <option value="english">Английский</option>
                            <option value="spanish">Испаниский</option>
                            <option value="turkish">Турецкий</option>
                            <option value="arab">Арабский</option>
                            <option value="portuguese">Португальский</option>
                            <option value="german">Немецкий</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Язык инструкции</label>
                        <select class="form-control" name="language_doc" id="language_doc">
                            <option value="russian">Русский</option>
                            <option value="english">Английский</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Напряжение зарядной станции</label>
                        <select class="form-control" name="charger" id="charger">
                            <option value="220">220</option>
                            <option value="110">110</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputFile">Комплектация</label>
                        <?php
                        $options = $robots->get_robot_options();
                        foreach ($options as &$value) {
                            $check = ($value['check'] == 1) ? "checked" : "";
                            echo '<div class="checkbox"><label><input type="checkbox" class="check" id="'.$value['id'].'" '.$check.'name="options" value='.$value['id'].'>'.$value['title'].'</label></div>';
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <label>Цвет</label>
                        <input type="text" class="form-control" name="color" id="color">
                    </div>
                    <div class="form-group">
                        <label>Брендирование </label>
                        <input type="text" class="form-control" name="brand" id="brand">
                    </div>
                    <div class="form-group">
                        <label>ИКП</label>
                        <input type="text" class="form-control" name="ikp" id="ikp">
                    </div>
                    <div class="form-group">
                        <label>Наличие АКБ</label>
                        <select class="form-control" name="battery" id="battery">
                            <option value="0">Нет</option>
                            <option value="1">Да</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дополнительная информация</label>
                        <input type="text" class="form-control" name="dop" id="dop">
                    </div>
                    <div class="form-group">
                        <label>Информация от производства</label>
                        <textarea rows="5" cols="45" class="form-control" name="dop_manufactur" id="dop_manufactur"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Пусконаладка</label>
                        <select class="form-control" name="commissioning" id="commissioning">
                            <option value="0">Нет</option>
                            <option value="1">Да</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Информация по доставке (наличие колёс на кофре, адрес доставки, телефон и имя получателя, плательщик по доставке, аэропорт доставки)</label>
                        <textarea rows="5" cols="45" class="form-control" name="delivery" id="delivery"></textarea>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" id="send">Отправленный</label>
                    </div>
                    <div class="form-group">
                        <label>Начало производства:</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" class="form-control pull-right" class="datepicker" id="datepicker" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Первый тест:</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" class="form-control pull-right" class="datepicker" id="datepicker2" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Дата отгрузки:</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" class="form-control pull-right" class="datepicker" id="datepicker3" value="">
                        </div>
                    </div>
                    <div id="update"></div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                        <button type="button" class="btn btn-primary btn-danger pull-right" name="" data-dismiss="modal" aria-label="Close">Закрыть</button>
                    </div>
                </form>
            <?php } else { /*вывод формы для остальных*/?>
                <form role="form" data-toggle="validator" id="add_pos">
                    <div class="form-group">
                        <label>Подверсия робота</label>
                        <select class="form-control" name="version" id="version" required="required">
                            <option value="0">Выберите подверсию...</option>
                            <?php
                            foreach ($subversions as &$version) {
                                echo "<option value='" . $version['id'] . "'>" . $version['title'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Кодовое имя</label>
                        <input type="text" class="form-control" name="name" id="name">
                    </div>
                    <div class="form-group">
                        <label>Покупатель <small>(<a href="#" data-toggle="modal" data-target="#add_customer">Добавить</a>)</small></label>
                        <select class="form-control select2" name="customer" id="customer">
                            <option value="0">Выберите покупателя...</option>
                            <?php
                            $arr = $robots->get_customers();
                            foreach ($arr as &$customer) {
                                echo "<option value='" . $customer['id'] . "'>" . $customer['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Владелец <small>(<a href="#" data-toggle="modal" data-target="#add_customer">Добавить</a>)</small></label>
                        <select class="form-control select2" name="owner" id="owner">
                            <option value="0">Выберите владельца...</option>
                            <?php
                            $arr = $robots->get_customers();
                            foreach ($arr as &$customer) {
                                echo "<option value='" . $customer['id'] . "'>" . $customer['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Язык на роботе</label>
                        <select class="form-control" name="language_robot" id="language_robot">
                            <option value="russian">Русский</option>
                            <option value="english">Английский</option>
                            <option value="spanish">Испаниский</option>
                            <option value="turkish">Турецкий</option>
                            <option value="arab">Арабский</option>
                            <option value="portuguese">Португальский</option>
                            <option value="german">Немецкий</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Язык инструкции</label>
                        <select class="form-control" name="language_doc" id="language_doc">
                            <option value="russian">Русский</option>
                            <option value="english">Английский</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Напряжение зарядной станции</label>
                        <select class="form-control" name="charger" id="charger">
                            <option value="220">220</option>
                            <option value="110">110</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputFile">Комплектация</label>
                        <?php
                        $options = $robots->get_robot_options();
                        foreach ($options as &$value) {
                            $check = ($value['check'] == 1) ? "checked" : "";
                            echo '<div class="checkbox"><label><input type="checkbox" class="check" id="'.$value['id'].'" '.$check.'name="options" value='.$value['id'].'>'.$value['title'].'</label></div>';
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <label>Цвет</label>
                        <input type="text" class="form-control" name="color" id="color">
                    </div>
                    <div class="form-group">
                        <label>Брендирование </label>
                        <input type="text" class="form-control" name="brand" id="brand">
                    </div>
                    <div class="form-group">
                        <label>ИКП</label>
                        <input type="text" class="form-control" name="ikp" id="ikp">
                    </div>
                    <div class="form-group">
                        <label>Наличие АКБ</label>
                        <select class="form-control" name="battery" id="battery">
                            <option value="0">Нет</option>
                            <option value="1">Да</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дополнительная информация</label>
                        <input type="text" class="form-control" name="dop" id="dop">
                    </div>
                    <div class="form-group">
                        <label>Пусконаладка</label>
                        <select class="form-control" name="commissioning" id="commissioning">
                            <option value="0">Нет</option>
                            <option value="1">Да</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Информация по доставке (наличие колёс на кофре, адрес доставки, телефон и имя получателя, плательщик по доставке, аэропорт доставки)</label>
                        <textarea rows="5" cols="45" class="form-control" name="delivery" id="delivery"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Дата отгрузки:</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" class="form-control pull-right" class="datepicker" id="datepicker3" value="">
                        </div>
                    </div>
                    <div id="update"></div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                        <button type="button" class="btn btn-primary btn-danger pull-right" name="" data-dismiss="modal" aria-label="Close">Закрыть</button>
                    </div>
                </form>
            <?php } ?>

            </div>
        </div>
    </div>
</div>
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
<div id="print-content"></div>
<?php include 'template/scripts.php'; ?>
<script src="./bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script src="./bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js"></script>
<!-- Select2 -->
<script src="./bower_components/select2/dist/js/select2.full.min.js"></script>

<script>
    $('body').on('shown.bs.modal', '.modal', function() {
        $(this).find('.select2').each(function() {
            var dropdownParent = $(document.body);
            if ($(this).parents('.modal.in:first').length !== 0) {
                dropdownParent = $(this).parents('.modal.in:first');
            }
            $(this).select2();
        });
    });

    $(document).ready(function() {
        //Date picker
        $('#datepicker').datepicker({
            format: 'dd.mm.yyyy',
            language: 'ru-Ru',
            startDate: '-3d',
            autoclose: true
        })
        $('#datepicker2').datepicker({
            format: 'dd.mm.yyyy',
            language: 'ru-Ru',
            startDate: '-3d',
            autoclose: true
        })
        $('#datepicker3').datepicker({
            format: 'dd.mm.yyyy',
            language: 'ru-Ru',
            startDate: 'd',
            autoclose: true
        })
        //Select2

        //$('.select2').select2();

        //-- костыль Control на опции/псевдоопции
        addClassControl();
        checkControl();
        function addClassControl() {
            $('#28').addClass( "option-control" );
            $('#29').addClass( "option-control" );
            $('#30').addClass( "option-control" );
            $('#31').addClass( "option-control" );
            $('#32').addClass( "option-control" );
            $('#33').addClass( "option-control" );
            $('#34').addClass( "option-control" );
            $('#35').addClass( "option-control" );
            $('#36').addClass( "option-control" );
            $('#47').addClass( "option-control" );
            $('#48').addClass( "corob-control" );
        }
        function checkControl() {
            if ($('.corob-control').is(':checked')) {
                $('.option-control').attr("disabled", true);
                $('.option-control').parent().parent().hide();
            }
            var option = 0;
            $('.option-control').each(function(){
                if (this.checked) {
                    option = 1;
                }
            });
            if (option == 1) {
                $('.corob-control').attr("disabled", true);
                $('.corob-control').parent().parent().hide();
            }
        }
        $('.corob-control').change(function() {
            if (this.checked) {
                $('.option-control').attr("disabled", true);
                $('.option-control').parent().parent().hide();
            } else {
                $('.option-control').attr("disabled", false);
                $('.option-control').parent().parent().show();
            }
        });
        $('.option-control').change(function() {
            var option = 0;
            $('.option-control').each(function(){
                if (this.checked) {
                    option = 1;
                }
            });
            if (option == 1) {
                $('.corob-control').attr("disabled", true);
                $('.corob-control').parent().parent().hide();
            } else {
                $('.corob-control').attr("disabled", false);
                $('.corob-control').parent().parent().show();
            }
        });
        //--//


        //создать покупателя
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
                        $('#owner').append("<option value='" + data + "' selected>" + name_cust + "<\/option>");
                        $('#add_customer').modal('hide');
                    }
                });
            }
        });

        //отправляемся в робота
        /*$("#robots.fa-align-justify").click(function () {
            var id = $(this).data("id");
            window.location.href = "./robot.php?id=" + id;
        });*/

        //отправляемся в робота
        $("#robots").on('click', '.fa-print', function () {
            var id = $(this).data("id");
            CallPrint(id);
        });

        //запускаем робота в производство
        $("#robots").on('click', '.fa-play', function () {
            var id = $(this).data("id");
            $.post("./api.php", {
                action: "launch_production_robot",
                id: id
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    window.location.href = "./robots.php";
                }
            });
        });

        //открывает модальное окно создать робота
        $("#btn_add_robot").click(function () {
            $('#add_robot').modal('show');
        });

        //кнопка запуска создания робота
        $("#save_close").click(function () {
            save_close();
            return false;
        });

        //функция создания робота
        function save_close() {
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
            var dop = $('#dop').val();
            var dop_manufactur = $('#dop_manufactur').val();
            var delivery = $('#delivery').val();
            var date_start = $('#datepicker').val();
            var date_test = $('#datepicker2').val();
            var date_send = $('#datepicker3').val();
            var send = $('#send').is(':checked') ? 1 : 0;
            var commissioning = $('#commissioning').val();
            //собираем отмеченные опции
            $('input[name=options]').each(function () {
                if (this.checked) {
                    options.push($(this).val());
                }
            });
            if (number === '' || version == 0) {
                return false;
            }
            if (number === undefined) {
                number = '';
                dop_manufactur = '';
                date_start = null;
                date_test = null;
                send = 0;
            }
            if (date_send === '') {
                date_send = null;
            }
            //console.log(date_start);
            //return false;
            $.post("./api.php", {
                action: "add_robot",
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
        }

        //отображать завершенных роботов
        /*$('#check_show_all').change(function () {
            if ($(this).is(":checked")) {
                //var returnVal = confirm("Are you sure?");
                $(this).attr("checked", true);
            }
            // alert($(this).is(':checked'));
            $("#show_all").submit();

        });*/

        //??? вроде нет ничего
        $("#robots1 tbody").sortable({
            stop: function (event, ui) {
                var arr_robot = [];
                var id_ = 'robots tbody';
                var cols_ = document.querySelectorAll('#' + id_ + ' tr');
                $.each(cols_, function (key, value) {
                    var idd = $(value).attr('id');
                    arr_robot.push(idd);
                });
                JSON.stringify(arr_robot);
                $.post("./api.php", {
                    action: "sortable",
                    json: arr_robot
                }).done(function (data) {
                    if (data == "false") {
                        alert("Data Loaded: " + data);
                    } else {

                    }
                });
                //console.log(arr_robot);
            },
            connectWith: ".connectedSortable"
        }).disableSelection();

        //??? вроде нет ничего
        $('#robots').DataTable({
            "iDisplayLength": 100,
            "order": [[0, "desc"]]
        });

        //функция печати
        function CallPrint(id) {
            $.post("./api.php", {action: "print_info_robot", id: id})
                .done(function (data) {
                    //console.log(data);
                    //return false;
                    var robot_info = jQuery.parseJSON(data);
                    var table = '<table class="robot-info" border="1" cellspacing="0" style="width:100%;font-size:12px">' +
                        '<tr><td style="width:40%"><b>Версия</b></td><td>' + robot_info['version'] + '</td></tr>' +
                        '<tr><td><b>Номер робота</b></td><td>' + robot_info['number'] + '</td></tr>' +
                        '<tr><td><b>Кодовое имя</b></td><td>' + robot_info['name'] + '</td></tr>' +
                        '<tr><td><b>Покупатель</b></td><td>' + robot_info['customer'] + '</td></tr>' +
                        '<tr><td><b>Владелец</b></td><td>' + robot_info['owner'] + '</td></tr>' +
                        '<tr><td><b>Чат, лингва, производство</b></td><td>' + robot_info['ident'] + '</td></tr>' +
                        '<tr><td colspan="2" style="padding-left:150px"><b>Комплектация</b></td></tr>' +
                        '<tr><td>Опции</td><td>' + robot_info['options'] + '</td></tr>' +
                        '<tr><td>Цвет</td><td>' + robot_info['color'] + '</td></tr>' +
                        '<tr><td>Брендирование</td><td>' + robot_info['brand'] + '</td></tr>' +
                        '<tr><td>ИКП</td><td>' + robot_info['ikp'] + '</td></tr>' +
                        '<tr><td>Дополнительная информация</td><td>' + robot_info['dop'] + '</td></tr>' +
                        '<tr><td colspan="2" style="padding-left:150px"><b>Информация о Покупателе</b></td></tr>' +
                        '<tr><td>ФИО</td><td>' + robot_info['fio'] + '</td></tr>' +
                        '<tr><td>e-mail</td><td>' + robot_info['email'] + '</td></tr>' +
                        '<tr><td>Телефон</td><td>' + robot_info['phone'] + '</td></tr>' +
                        '<tr><td colspan="2" style="padding-left:150px"><b>Информация для отгрузки</b></td></tr>' +
                        '<tr><td>Наличие АКБ</td><td>' + robot_info['battery'] + '</td></tr>' +
                        '<tr><td>Напряжение зарядной станции</td><td>' + robot_info['charger'] + '</td></tr>' +
                        '<tr><td>Язык (робота)</td><td>' + robot_info['language_robot'] + '</td></tr>' +
                        '<tr><td>Язык (инструкции)</td><td>' + robot_info['language_doc'] + '</td></tr>' +
                        '<tr><td>Дата отгрузки</td><td>' + robot_info['date_send'] + '</td></tr>' +
                        '<tr><td>Пусконаладка</td><td>' + robot_info['commissioning'] + '</td></tr>' +
                        '<tr><td>Наименование получателя</td><td>' + robot_info['customer'] + '</td></tr>' +
                        '<tr><td>Юридич. адрес получателя</td><td>' + robot_info['address'] + '</td></tr>' +
                        '<tr><td>ИНН получателя</td><td>' + robot_info['inn'] + '</td></tr>' +
                        '<tr><td>Информация по доставке:<br>-наличие колёс на кофре<br>-адрес доставки<br>-телефон и имя получателя<br>-плательщик по доставке<br>-аэропорт доставки</td><td>' + robot_info['delivery'] + '</td></tr>' +
                        '</table>';
                    var prtContent = document.getElementById('print-content');
                    //var prtCSS = '<link rel="stylesheet" href="./dist/css/print.css" type="text/css" />';
                    var prtCSS = '<style>' +
                        'td {padding:5px}' +
                        '</style>';
                    var WinPrint = window.open('', '', 'left=50,top=50,width=800,height=640,toolbar=0,scrollbars=1,status=0');
                    //console.log(table);
                    WinPrint.document.write('');
                    WinPrint.document.write(prtCSS);
                    WinPrint.document.write(prtContent.innerHTML = table);
                    WinPrint.document.write('');
                    WinPrint.document.close();
                    WinPrint.focus();
                    WinPrint.print();
                    WinPrint.close();
                    prtContent.innerHTML = '';
                });
        }
    });
</script>
</body>
</html>
