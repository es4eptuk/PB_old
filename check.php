<?php 
include 'include/class.inc.php';

$robot_info = $robots->get_info_robot($_GET['robot']);
$robot_number = $robot_info['number'];
$robot_version = $robot_info['version'];
$robot_name= $robot_info['name'];
$robot_id= $robot_info['id'];
$robot_remont= $robot_info['remont'];
//$finish_mh = $checks->get_progress($robot_id, 1);
//$finish_hp = $checks->get_progress($robot_id, 2);
//$finish_hs = $checks->get_progress($robot_id, 5);
$category_id = $_GET['category'];


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
       Promobot <?php echo $robot_version.".".$robot_number; ?>
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $position->getCategoryes[$_GET['category']]['title'] ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                
                <?php 
                $arr = $checks->get_checks_on_robot($_GET['category'],$robot_id);
                $arr_option = $checks->get_checks_on_robot_option($_GET['category'],$robot_id);
               // print_r($arr_option);
                 if (isset($arr_option)) {
                     $count = 0;
                foreach ($arr_option as &$option) {
                    $title_category = $option['title'];
                    $table[$title_category][$count]['id_row'] = $option['id'];
                    $table[$title_category][$count]['id_check'] = $option['id_check'];
                    $table[$title_category][$count]['check'] = $option['check'];
                    $table[$title_category][$count]['operation'] = $option['operation'];
                    $table[$title_category][$count]['comment'] = $option['comment'];
                    $table[$title_category][$count]['kit'] = $option['id_kit'];
                    $table[$title_category][$count]['update_user'] = $option['update_user'];
                    $table[$title_category][$count]['update_date'] = $option['update_date'];
                    $count++;
                }
                 }
                
                
                //print_r($arr_option);
               // print_r($table);
                ?>
                
            <table id="orders" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>#</th>
                  <th style="width: 40%;">Наименование</th>
                  <th>Коментарий</th>
                  <th style=" width: 15%;">Комплект деталей</th>
                  <th style=" width: 15%;">Исполнитель</th>
                  <th style="width: 10%;">Дата</th>
                </tr>
                </thead>
                <tbody>
               <?php 
                
               
               //print_r($arr);
                
                if (isset($arr)) {
                foreach ($arr as &$check) {

                       $user_info = $user->get_info_user($check['update_user']);
                       //$check_info = $checks->get_info_check($check["id_check"]);
                       //$user_info = $user->get_info_user($pos['order_responsible']);
                       //$order_date = new DateTime($pos['order_date']);
                       //$order_delivery = new DateTime($pos['order_delivery']);
                       if($check["check"]==1) {
                           $checked = "checked";
                           $disabled = "disabled";
                       } else {
                           $checked = "";
                           $disabled = "";
                       }
                       if ( $check["update_date"] == "0000-00-00 00:00:00") {$check["update_date"] = "";}

                       //".$check["id_kit"]." - это старый набор - поменять вместо ".$kit_options."
                       //создание формы выбора набора
                       if ($check["id_kit"] != 0) {
                           $options = "";
                           $arr_kits = $position->get_all_kits_by_id($check["id_kit"]); //$check_info["kit"]
                           if (isset($arr_kits)) {
                               foreach($arr_kits as $kit) {
                                   if ($check["id_kit"] == $kit['id_kit']) {
                                       $option = "<option value='".$kit['id_kit']."' selected>".$kit['id_kit'].'::'.$kit['kit_title']."</option>";
                                   } else {
                                       $option = "<option value='".$kit['id_kit']."'>".$kit['id_kit'].'::'.$kit['kit_title']."</option>";
                                   }
                                   $options = $options.$option;
                               }
                           }
                           $kit_options = "
                                <form class='kit-change'>
                                    <div class='form-group'>
                                        <select class='form-control kit' id='kit_change_".$check["id"]."' data-id_row='".$check["id"]."' ".$disabled.">
                                            ".$options." 
                                        </select>
                                    </div>
                                </form>                        
                           ";
                       } else {
                           $kit_options = "";
                       }

                       echo "
                        <tr >
                            <td>".$check['sort']."</td>
                            <td>
                                <div class='checkbox'>
                                    <label>
                                        <input type='checkbox' id='".$check["id_check"]."' class='check' ".$checked." data-kit='".$check["id_kit"]."' data-id_row='".$check["id"]."'>".$check["operation"]."</label>
                                </div>
                            </td>
                            <td>
                                <form class='comment' id='comment_".$check["id"]."'>
                                    <div class='form-group'>
                                        <textarea class='form-control' rows='1' placeholder='Комментарий ...'>".$check["comment"]."</textarea>
                                    </div>
                                    <button type='submit' class='btn btn-primary pull-right' id='btn_".$check["id"]."'>Ок</button>
                                </form>
                            </td>
                            <td>
                            ".$kit_options."
                            </td>
                            <td>".$user_info['user_name']."</td>
                            <td>".$check["update_date"]."</td>

                    </tr>
                       
                       ";
                    }
                }
                
                ?>
             
               
               </table> <br><br>
               
               <?php 
               
                if (isset($table)) {
                    foreach ($table as $key => $value) {
                        echo '
                            <div class="box box-danger">
                                <div class="box-header with-border">
                                  <h3 class="box-title">'.$key.'</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <table id="orders" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th style="width: 40%;">Наименование</th>
                                                <th>Коментарий</th>
                                                <th style=" width: 15%;">Комплект деталей</th>
                                                <th style=" width: 15%;">Исполнитель</th>
                                                <th style="width: 10%;">Дата</th>                                            
                                            </tr>
                                        </thead>
                                        <tbody>
                        ';
                        foreach ($value as $key2 => $opt) {
                            $user_info = $user->get_info_user($opt['update_user']);
                            //$check_info = $checks->get_info_check($opt["id_check"]);
                            //$user_info = $user->get_info_user($pos['order_responsible']);
                            //$order_date = new DateTime($pos['order_date']);
                            //$order_delivery = new DateTime($pos['order_delivery']);
                            //if($opt["check"]==1) {$checked = "checked";} else {$checked = "";}
                            if ($opt["update_date"] == "0000-00-00 00:00:00") {$opt["update_date"] = "";}
                            if($opt["check"]==1) {
                                $checked = "checked";
                                $disabled = "disabled";
                            } else {
                                $checked = "";
                                $disabled = "";
                            }
                            //создание формы выбора набора
                            if ($opt['kit'] != 0) {
                                $options = "";
                                $arr_kits = $position->get_all_kits_by_id($opt['kit']);
                                if (isset($arr_kits)) {
                                    foreach($arr_kits as $kit) {
                                        if ($opt['kit'] == $kit['id_kit']) {
                                            $option = "<option value='".$kit['id_kit']."' selected>".$kit['id_kit'].'::'.$kit['kit_title']."</option>";
                                        } else {
                                            $option = "<option value='".$kit['id_kit']."'>".$kit['id_kit'].'::'.$kit['kit_title']."</option>";
                                        }
                                        $options = $options.$option;
                                    }
                                }
                                $kit_options = "
                                <form class='kit-change'>
                                    <div class='form-group'>
                                        <select class='form-control kit' id='kit_change_".$opt["id_row"]."' data-id_row='".$opt["id_row"]."' ".$disabled.">
                                            ".$options." 
                                        </select>
                                    </div>
                                </form>                        
                           ";
                            } else {
                                $kit_options = "";
                            }

                            echo "
                                <tr>
                                    <td></td>                                
                                    <td>
                                        <div class='checkbox'>
                                            <label>
                                              <input type='checkbox' id='".$opt["id_check"]."' class='check' ".$checked." data-kit='".$opt["kit"]."' data-id_row='".$opt["id_row"]."'>
                                              ".$opt["operation"]."
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <form class='comment' id='comment_".$opt["id_row"]."'>
                                            <div class='form-group'>
                                                <textarea class='form-control' rows='1' placeholder='Комментарий ...'>".$opt["comment"]."</textarea>
                                            </div>
                                            <button type='submit' class='btn btn-primary pull-right' id='btn_".$opt["id_row"]."' >Ок</button>
                                        </form>
                                    </td>
                                    <td>
                                    ".$kit_options."
                                    </td>
                                    <td>".$user_info['user_name']."</td>
                                    <td>".$opt["update_date"]."</td>
                                </tr>
                            ";
                        }
                
                
                echo '</table>   </div>
            <!-- /.box-body -->
           
          </div> ';
                    
                }
                
                }
               
               ?>
               
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
<!-- Modal -->
<div class="modal fade" id="add_operation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Добавление операции<span id="operation_id"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
          <form role="form" data-toggle="validator" id="add_pos">
                <!-- text input -->
                <!-- select -->
                <div class="form-group">
                  <label>Группа</label>
                  <select class="form-control" name="group" placeholder="Веберите группу" id="group" required="required">
                   <option>Веберите группу...</option>
                   <?php 
                  /* $arr = $position->getCategoryes;
                
                    foreach ($arr as &$category) {
                       echo "
                       <option value='".$category['id']."'>".$category['title']."</option>
                       
                       ";
                    }*/
                   
                   ?>
                  </select>
                </div>

                
                 <div class="form-group">
                  <label>Наименование</label>
                  <input type="text" class="form-control" name="title" required="required" id="title">
                </div>
                <div class="form-group">
                  <label>Порядковый номер</label>
                  <input type="text" class="form-control" name="sort" id="sort">
                </div>
                
                
                
                <div id="update"></div>
                
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                    <button type="button" class="btn btn-primary btn-danger pull-right" id="Close" name="">Закрыть</button>
                </div>
              </form>
         
      </div>
      
    </div>
  </div>
</div>
<!-- jQuery 3 -->
<script src="./bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="./bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="./bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="./bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="./bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="./bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="./dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="./dist/js/demo.js"></script>


<!-- page script -->


<script>
    //отправка коментария
    $('.comment').submit(function () {
        var robot =  <?php echo $robot_id; ?>;
        var id = $(this).attr("id");
        var str = id.split('_');
        var idd = str[1];
        var id_row = idd;
        var comment = $(this).find('textarea').val();
        var value = $("#" + idd).val();

        if ($("#" + idd).is(':checked')) {
            val = 1;
        } else {
            val = 0;
        }

        //alert(val);
        if (comment != "") {

            $.post("./api.php", {
                action: "add_comment_on_check",
                id_row: id_row,
                robot: robot,
                id: idd,
                value: val,
                comment: comment,
                number: <?php echo (int)$robot_number; ?>
            })
                .done(function (data) {
                    if (data == "false") {
                        alert("Data Loaded: " + data);
                    } else {
                        //window.location.href = "./robots.php";
                        $('#btn_' + idd).hide();
                        //console.log('btn_'+idd);
                    }
                });
        }
        return false;
    });

    //отправка чеклистов
    $(".check").change(function () {
        $(".check").prop('disabled', true);
        var category = <?php echo $category_id; ?> ;

        //if (category == 4 && (finish_hs!=100  ||  finish_mh!=100 || finish_hp!=100 )) {alert("Не выполнены все операции в предыдущих отделах!"); $(this).prop( "checked", false ); return false;}

        var id = $(this).attr("id");
        var id_row = $(this).data('id_row');
        var kit = $('select[data-id_row=' + id_row + ']').val(); //$(this).data('kit');

        if (kit == undefined) {
            kit = 0;
        }

        //alert(id);
        //console.log(kit);

        if (this.checked) {
            val = 1;
            //отключает смену набора
            $('select[data-id_row=' + id_row + ']').prop('disabled', true);
        } else {
            val = 0;
            //включает смену набора
            $('select[data-id_row=' + id_row + ']').prop('disabled', false);
        }

        var robot =  <?php echo $robot_id; ?>;
        var remont = <?php echo $robot_remont; ?>;


        $.post("./api.php", {
            action: "add_check_on_robot",
            id_row: id_row,
            robot: robot,
            id: id,
            value: val,
            number: <?php echo (int)$robot_number; ?>,
            remont: remont,
            kit: kit

        })
            .done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    //window.location.href = "./robots.php";
                    $(".check").prop('disabled', false);
                }
            });

    });

    //смена комплекта
    $(".kit").on('change', function () {
        var id_row = $(this).data('id_row');
        var id_kit = $(this).val();

        $('input[data-id_row=' + id_row + ']').attr('data-kit', id_kit);
        //console.log(th);
    });


</script>
</body>
</html>
