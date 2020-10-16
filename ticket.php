<?php 
include 'include/class.inc.php';

function getNumEnding($number, $endingArray)
{
    $number = $number % 100;
    if ($number>=11 && $number<=19) {
        $ending=$endingArray[2];
    }
    else {
        $i = $number % 10;
        switch ($i)
        {
            case (1): $ending = $endingArray[0]; break;
            case (2):
            case (3):
            case (4): $ending = $endingArray[1]; break;
            default: $ending=$endingArray[2];
        }
    }
    return $ending;
} 

$ticket_info = $tickets->info($_GET['id']);
$ticket_id = $ticket_info['id'];
$ticket_author = $ticket_info['user_create'];
$user_info = $user->get_info_user($ticket_author);
$ticket_author = $user_info['user_name'];
$ticket_author_img = $user_info['avatar'];

$user_info = $user->get_info_user($ticket_info['assign']);
$ticket_assign = $user_info['user_name'];
$ticket_assign_id = $user_info['user_id'];
$ticket_assign_img = $user_info['avatar'];

$ticket_robot = $ticket_info['robot'];
$ticket_class = $ticket_info['class'];
$ticket_category_id = $ticket_info['category'];
$category_info = $tickets->get_info_category($ticket_category_id);
$ticket_category = $category_info['title'];
$ticket_subcategory_id = $ticket_info['subcategory'];
$subcategory_info = $tickets->get_info_subcategory($ticket_subcategory_id);
$ticket_subcategory = $subcategory_info['title'];
$ticket_description =  $ticket_info['description'];
$ticket_update =  $ticket_info['update_date'];
$ticket_update = new DateTime($ticket_update);

$ticket_create =  $ticket_info['date_create'];
$ticket_create = new DateTime($ticket_create);

if ($ticket_info['assign_time'] != null) {
    $ticket_assign_date = new DateTime($ticket_info['assign_time']);
    $ticket_assign_date = $ticket_assign_date->format('d.m.Y H:i');
} else {
    $ticket_assign_date = '-';
}

$ticket_inwork =  $ticket_info['inwork'];
$ticket_inwork = new DateTime($ticket_inwork);

$ticket_status =  $ticket_info['status'];

$ticket_work = date_diff($ticket_create, $ticket_inwork);

$days = $ticket_work->format('%d');
$days = (int)$days;
$days_str = getNumEnding($days, array('день', 'дня', 'дней'));

$hours = $ticket_work->format('%H');
$hours = (int)$hours;

$minutes = $ticket_work->format('%I');
$hours_str = getNumEnding($hours, array('час', 'часа', 'часов'));
$minutes_str = getNumEnding($minutes, array('минута', 'минуты', 'минут'));
$ticket_work_str = $days." ".$days_str." ". $hours. " ".$hours_str. " " .$minutes. " " .$minutes_str;

$ticket_result =  $ticket_info['result_description'];

$status_info = $tickets->get_info_status($ticket_status);
$ticket_status_str = $status_info['title'];
$ticket_color = $status_info['color'];
$ticket_font = $status_info['font'];

$robot_info = $robots->get_info_robot($ticket_robot);
$robot_number = $robot_info['number'];
$robot_name= $robot_info['name'];
$robot_version= $robot_info['version'];
$robot_id= $robot_info['id'];

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
          <a href="./kanban.php"><i class="fa fa-long-arrow-left"></i> KANBAN</a>
          <?php echo "<span id='ticket_class_2'>".$ticket_class."</span>-".$ticket_id." (Promobot " . "<a href='./robot_card.php?id=$robot_id'>$robot_version.$robot_number</a>" ." ".$robot_name . ")"; ?>
      </h1>
      
    </section>
    <!-- Main content -->
    <section class="content">
       <div class="row">
        <div class="col-md-9">
          <div class="box box-widget">
            <div class="box-header with-border" >
            <h3 class="box-title ticket_data" ><?= $tickets::CLASS_TICKET[$ticket_class] ?><?= ($ticket_category != '') ? ": ".$ticket_category : '' ?><?= ($ticket_subcategory) ? ": ".$ticket_subcategory : '' ?></h3>
            <i class="fa fa-pencil pull-right" id="btn_edit_title"></i>
            <div class="row ticket_edit" >
                <div class="col-lg-6">
                    <select class="form-control" id="category">
                        <option value='0' selected>Неизвестно</option>
                        <?php
                        $arr_category = $tickets->get_category($ticket_class);
                        foreach ($arr_category as &$category) {
                            if ( $category['id'] == $ticket_category_id ) {
                                echo "<option value='".$category['id']."' selected>".$category['title']."</option>";
                            } else {
                                echo "<option value='".$category['id']."'>".$category['title']."</option>";
                            }
                        }
                        ?>
                    </select>
                  <!-- /input-group -->
                </div>
                <!-- /.col-lg-6 -->
                <div class="col-lg-6">
                    <select class='form-control' id='subcategory'>
                        <option value='0' selected>Неизвестно</option>
                        <?php
                        if ($ticket_class== "P") {
                            $arr_subcategory = $tickets->get_subcategory($ticket_category_id);
                            foreach ($arr_subcategory as &$category) {
                                if ( $category['id'] == $ticket_subcategory_id ) {
                                    echo "<option value='".$category['id']."' selected>".$category['title']."</option>";
                                } else {
                                    echo "<option value='".$category['id']."'>".$category['title']."</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <!-- /.col-lg-6 -->
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="min-height: 395px;">
              <!-- post text -->
              
             <p class="ticket_data"><?php echo $ticket_description; ?></p> 
             <!--Решение: <?php echo $ticket_result; ?>-->
             <div class="form-group ticket_edit">
              <label>Описание</label>
              <textarea class="form-control" rows="3" placeholder="Enter ..." id="descriprion"><?php echo $ticket_description; ?></textarea>
             </div>
             <?php
             if ($ticket_status==3) {
                echo '
                <div class="alert alert-success alert-dismissibl ticket_data">
                    <h4><i class="icon fa fa-check"></i> Решение</h4>
                    '.$ticket_result.'
                </div>
                <div class="form-group ticket_edit">
                  <label>Решение</label>
                  <textarea class="form-control" rows="3" placeholder="Enter ..." id="result">'.$ticket_result.'</textarea>
                </div>                
               ';
             }
             ?>
             <div class="form-group ticket_edit">
               <button type="submit" class="btn btn-primary" id="ticket_save">Сохранить</button>
               <button type="submit" class="btn btn-default" id="ticket_cancel_save">Отмена</button>
             </div>


              <!-- Attachment
             <div class="attachment-block clearfix">
                <img class="attachment-img" src="../dist/img/photo1.png" alt="Attachment Image">

                <div class="attachment-pushed">
                  <h4 class="attachment-heading"><a href="http://www.lipsum.com/">Lorem ipsum text generator</a></h4>

                  <div class="attachment-text">
                    Description about the attachment can be placed here.
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry... <a href="#">more</a>
                  </div>
                   /.attachment-text -->
                </div>
                <!-- /.attachment-pushed -->
              </div> 
              
            <div class="col-md-13 no-padding">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Связать с:</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="form-group">
                <label>Тикет</label>
                <select class="form-control select2" style="width: 100%;" id="selectConnect">
                    <option></option>
                     <?php
                     $arr = $tickets->get_tickets("$robot_id",0,0,"update_date","DESC",0,0,0,"P",0,1);
                     foreach ($arr as &$ticket) {
                        if ($ticket['id'] == $ticket_id) {continue;}
					    echo "<option value='".$ticket['id']."' >".$ticket['class']."-".$ticket['id']." ".$ticket['description']."</option>";
                     }
					 ?>
                </select>
              </div>
               <button type="submit" class="btn btn-primary pull-right" id="btnConnect">ОК</button>
            </div>
          </div>
        </div>

       </div>
            
       <div class="col-md-3" id="ticket_info_block">
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header" style="background-color: <?php echo $ticket_color ?>; color: <?php echo $ticket_font ?>">
              <div class="widget-user-image">
                  <?php
                  if ($ticket_assign_img!='') {
                      echo '<img class="img-circle" src="./img/avatar/'.$ticket_assign_img.'">';
                  }
                  ?>
              </div>
              <!-- /.widget-user-image -->
              <h3 class="widget-user-username"><?= ($ticket_assign != "") ? $ticket_assign : "Не назначено" ?></h3>
            </div>
            <?php
                $tiket_color = '#e6e8ff'; //fffcd8 - желт для 2, e6e8ff
                if ($ticket_info['priority'] == 2) {$tiket_color = '#f9f9f9';}
                if ($ticket_info['priority'] == 3) {$tiket_color = '#ffd5d5';}
            ?>
            <div class="box-body" style="background-color:<?=$tiket_color?>">
                <?php if ($userdata['group'] == 1) { ?>
                    <div class="form-group" >
                        <label>Источник</label>
                        <select class="form-control" id="ticket_source">
                            <?php
                            $arr = $tickets->listSourceTikets;
                            foreach ($arr as $id => $source) {
                                $selected = ($id == $ticket_info['source']) ? "selected" : "";
                                echo "<option value='".$id."' ".$selected.">".$source."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" >
                        <label>Приоритет</label>
                        <select class="form-control" id="ticket_priority">
                            <?php
                            $arr = $tickets->listPriorityTikets;
                            foreach ($arr as $id => $priority) {
                                $selected = ($id == $ticket_info['priority']) ? "selected" : "";
                                echo "<option value='".$id."' ".$selected.">".$priority."</option>";
                            }
                            ?>
                        </select>
                    </div>
                <?php } ?>
                <div class="form-group" >
                  <label>Статус</label>
                  <select class="form-control" id="ticket_status">
                    <?php
                        $arr_status = $tickets->get_status_list_change($ticket_status);
                        foreach ($arr_status as &$status) {
                            if ($status['id']==$ticket_status) {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }
                            echo "<option value='".$status['id']."' ".$selected.">".$status['title']."</option>";
                        }
                    ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Исполнитель</label>
                  <select class="form-control" id="ticket_assign">
                    <option value="0">Не назначен</option>
                    <?php
                        $arr_user = $user->get_users(4);
                        //echo $ticket_assign_id;
                        foreach ($arr_user as &$user_assign) {
                            if ($user_assign['user_id']==$ticket_assign_id) {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }
                            echo "<option value='".$user_assign['user_id']."' ".$selected.">".$user_assign['user_name']."</option>";
                        }
                    ?>
                  </select>
                </div>

                <ul class="nav nav-stacked">
                    <li><a>Приоритет <span class="pull-right"><?= $tickets::PRIORITY_TICKET[$ticket_info['priority']] ?></span></a></li>
                    <li><a>Источник <span class="pull-right"><?= $tickets::SOURCE_TICKET[$ticket_info['source']] ?></span></a></li>
                    <li><a>Дата создания <span class="pull-right"><?php echo $ticket_create->format('d.m.Y H:i'); ?></span></a></li>
                    <?php
                    if ($ticket_status==3 || $ticket_status ==6 || $ticket_status ==8) {
                        $time = ($ticket_info['inwork'] != null) ? $ticket_inwork->format('d.m.Y H:i') : '';
                        echo ' <li><a>Дата решения <span class="pull-right">'.$time.'</span></a></li>';
                    }
                    ?>
                    <li><a>Взято в работу <span class="pull-right"><?= $ticket_assign_date ?></span></a></li>
                    <li><a>Дата изменения <span class="pull-right"><?php echo $ticket_update->format('d.m.Y H:i'); ?></span></a></li>
                    <?php
                        if ($ticket_info['finish_date']!='0000-00-00' && $ticket_info['finish_date']!=null) {
                            $ticket_finish = new DateTime($ticket_info['finish_date']);
                            $finish_date = $ticket_finish->format('d.m.Y');
                            echo '<li><a>Дата ремонта <span class="pull-right">'.$finish_date.'</span></a></li>';
                        } else {
                            $finish_date = date('d.m.Y');
                        }
                    ?>
                    <?php
                        $arr_comments = $tickets->get_comments($ticket_id);
                        if (!isset($arr_comments)) {
                          $arr_count_comments = 0;
                        } else {
                          $arr_count_comments = count($arr_comments);
                        }
                        $arr_comments_customers = $tickets->get_comments_customers($ticket_id);
                        if (!isset($arr_comments_customers)) {
                            $arr_count_comments_customers = 0;
                        } else {
                            $arr_count_comments_customers = count($arr_comments_customers);
                        }
                    ?>
                    <li><a>Комментарии техпод <span class="pull-right badge bg-aqua"><?= $arr_count_comments ?></span></a></li>
                    <li><a>Комментарии клиент<span class="pull-right badge bg-aqua"><?= $arr_count_comments_customers ?></span></a></li>
                    <li><a>Вложения <span class="pull-right badge bg-green">0</span></a></li>
                    <li><a>В работе<span class="pull-right badge bg-red"><?= $ticket_work_str; ?></span></a></li>
                </ul>
            </div>
            <?php if ($userdata['group'] == 1) { ?>
            <div class="box-footer" style="background-color:<?=$tiket_color?>">
                <button type="submit" class="btn btn-danger btn-block" id="del_ticket" name="">Удалить</button>
            </div>
            <?php } ?>
          </div>
          <!-- /.widget-user -->
        </div>
           
            
            <!-- /.box-footer -->
        </div>
        
        <div class="row">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="false">Техпод</a></li>
                    <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="true">Клиент</a></li>
                </ul>
                <div class="tab-content">

                    <div class="tab-pane active" id="tab_1">
                        <!-- Техпод -->
                        <div class="box-footer">
                            <form action="#" method="post" id="add_comment">
                                <div class="input-group">
                                    <input type="text" name="message" placeholder="Введите комментарий ..." class="form-control" id="comment" required="required" maxlength="900" minlength="2">
                                    <span class="input-group-btn"><button type="submit" class="btn btn-primary btn-flat">Отправить</button></span>
                                </div>
                            </form>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer box-warning">
                            <div class="direct-chat-messages">
                                <!-- Message. Default to the left -->
                                <?php
                                if (isset($arr_comments)) {
                                    //print_r($arr_comments);
                                    foreach ($arr_comments as &$comment) {
                                        //echo $comment['comment'];
                                        $comment_user_info = $user->get_info_user($comment['update_user']);
                                        //$comment_user_info = $user->get_info_user(14);
                                        $comment_date = new DateTime($comment['update_date']);
                                        $src_avatar = ($comment_user_info['avatar'] != '') ? './img/avatar/' . $comment_user_info['avatar'] : './img/bot-32x32.png';
                                        echo "
                                         <div class='direct-chat-msg'>
                                          <div class='direct-chat-info clearfix'>
                                            <span class='direct-chat-name pull-left'> ".$comment_user_info['user_name']."</span>
                                            <span class='direct-chat-timestamp pull-right'>".$comment_date->format('d.m.Y H:i:s')."</span>
                                          </div>
                                          <img class='direct-chat-img' src='".$src_avatar."' alt='Message User Image'>
                                          <div class='direct-chat-text'>".$comment['comment']."</div>
                                         </div>
                                        ";
                                    }
                                }
                                ?>
                                <!-- /.direct-chat-msg -->
                            </div>
                            <!-- /.box-comment -->
                        </div>
                        <!-- -->
                    </div>

                    <div class="tab-pane" id="tab_2">
                        <!-- Клиент -->
                        <div class="box-footer">
                            <form action="#" method="post" id="add_comment_customers">
                                <div class="input-group">
                                    <input type="text" name="message_customers" placeholder="Введите комментарий ..." class="form-control" id="comment_customers" required="required" maxlength="1000" minlength="2">
                                    <span class="input-group-btn"><button type="submit" class="btn btn-primary btn-flat">Отправить</button></span>
                                </div>
                            </form>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer box-warning">
                            <div class="direct-chat-messages">
                                <!-- Message. Default to the left -->
                                <?php
                                if (isset($arr_comments_customers)) {
                                    foreach ($arr_comments_customers as &$comment) {
                                        $comment_user_info = $user->get_info_user($comment['update_user']);
                                        $comment_date = new DateTime($comment['update_date']);
                                        $src_avatar = ($comment_user_info['avatar'] != '') ? './img/avatar/' . $comment_user_info['avatar'] : './img/bot-32x32.png';
                                        echo "
                                         <div class='direct-chat-msg'>
                                          <div class='direct-chat-info clearfix'>
                                            <span class='direct-chat-name pull-left'> ".$comment_user_info['user_name']."</span>
                                            <span class='direct-chat-timestamp pull-right'>".$comment_date->format('d.m.Y H:i:s')."</span>
                                          </div>
                                          <img class='direct-chat-img' src='".$src_avatar."' alt='Message User Image'>
                                          <div class='direct-chat-text'>".$comment['comment']."</div>
                                         </div>
                                        ";
                                    }
                                }
                                ?>
                                <!-- /.direct-chat-msg -->
                            </div>
                            <!-- /.box-comment -->
                        </div>
                        <!-- -->
                    </div>

                </div>
            </div>

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
  
  <!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="add_result" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Опишите решение проблемы</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Решение:</label>
                    <textarea class="form-control" rows="3" id="result_description"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-change" data-dismiss="modal" data-status="" data-old_status="">Отменить перенос</button>
                <button type="button" class="btn btn-primary" id="btn_add_reuslt" data-id="">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="change_status" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Укажите информацию для смены статуса</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Дата ремонта:</label>
                    <div class="input-group date">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" class="form-control pull-right" class="datepicker" id="change_status_date" name="change_status_date" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label>Комментарий:</label>
                    <textarea class="form-control" rows="3" name="change_status_comment" id="change_status_comment"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-change" data-dismiss="modal" data-status="" data-old_status="">Отменить перенос</button>
                <button type="button" class="btn btn-primary" id="btn_change_status" data-id="">Сохранить</button>
            </div>
        </div>
    </div>
</div>

</div>
<!-- ./wrapper -->
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
<script src="./bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="./bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js"></script>
<!-- Select2 -->
<script src="./bower_components/select2/dist/js/select2.full.min.js"></script>

<!-- page script -->
<?php
    $today = date('d.m.Y');
?>
<script>
    //Date picker
    $('#change_status_date').datepicker({
        format: 'dd.mm.yyyy',
        language: 'ru-Ru',
        startDate: '<?= $today ?>',
        todayHighlight: true,
        autoclose: true
    })

    //Initialize Select2 Elements
    $('.select2').select2()

    $(".ticket_edit").hide();

    $("#btn_edit_title").click(function () {
        $(".ticket_data").hide();
        $(this).hide();
        $(".ticket_edit").show();
    });

    $("#ticket_cancel_save").click(function () {
        $(".ticket_data").show();
        $("#btn_edit_title").show();
        $(".ticket_edit").hide();
    });



    $('#btnConnect').click(function () {
        var idConnect = $("#selectConnect").val();
        $.post("./api.php", {
            action: "ticket_info",
            id: idConnect
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded Info: " + data);
            } else {
                var arr = JSON.parse(data);
                $.post("./api.php", {
                    action: "ticket_change_status",
                    id: <?php echo $ticket_id; ?>,
                    status: 6,
                    workin: 0
                }).done(function (data) {
                    if (data == "false") {
                        alert("Data Loaded Status: " + data);
                    } else {
                        var id =  <?php echo $ticket_id; ?>;
                        var robot =  <?php echo $robot_id; ?>;
                        var comment = "Связано с <a href=\"ticket.php?id=" + arr['id'] + "\">" + arr['class'] + "-" + arr['id'] + "</a>";
                        $.post("./api.php", {
                            action: "ticket_add_comment",
                            robot: robot,
                            id: id,
                            comment: comment
                        }).done(function (data) {
                            if (data == "false") {
                                alert("Data Loaded Comment: " + data);
                            } else {
                                var conId =  <?php echo $ticket_id; ?>;
                                var conClass = "<?php echo $ticket_class; ?>";
                                var conDescription = "<?php echo str_replace(array("\r\n", "\r", "\n"), '"+"',$ticket_description); ?>";
                                comment = "Прикреплено <a href=\"ticket.php?id=" + conId + "\">" + conClass + "-" + conId + "</a>" + " " + conDescription;
                                console.log(comment);
                                $.post("./api.php", {
                                    action: "ticket_add_comment",
                                    robot: arr['robot'],
                                    id: arr['id'],
                                    comment: comment
                                }).done(function (data) {
                                    if (data == "false") {
                                        alert("Data Loaded Comment Connect: " + data);
                                    } else {
                                        window.location.reload(true);

                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    });

    $('#ticket_save').click(function () {
        var category = 0;
        var subcategory = 0;
        var robot =  <?php echo $robot_id; ?>;
        var ticket_class = "<?php echo $ticket_class; ?>";
        var descriprion = $('#descriprion').val();
        var result = $('#result').val();
        //var status = $('#ticket_status').val();
        if (result === undefined) {
            result = null;
        }
        //comment = category_str + ': ' + subcategory_str;
        if (ticket_class == "P") {
            category = $('#category').val();
            subcategory = $('#subcategory').val();
        } else {
            category = $('#category').val();
            subcategory = 0;
        }
        $.post("./api.php", {
            action: "ticket_edit",
            id: <?php echo $ticket_id; ?>,
            category: category,
            subcategory: subcategory,
            description: descriprion,
            result: result
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
        return false;
    });

    <?php if ($userdata['group'] == 1) { ?>
    //смена приоритета
    $("#ticket_priority").change(function () {
        var id = <?php echo $ticket_id?>;
        var priority = $('#ticket_priority').val();
        $.post("./api.php", {
            action: "ticket_change_priority",
            id: id,
            priority: priority
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
    });
    //смена источника
    $("#ticket_source").change(function () {
        var id = <?php echo $ticket_id?>;
        var source = $('#ticket_source').val();
        $.post("./api.php", {
            action: "ticket_change_source",
            id: id,
            source: source
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
    });
    //кнопка удаления тикета
    $("#ticket_info_block").on('click', '#del_ticket', function () {
        var id = <?php echo $ticket_id?>;
        $.post("./api.php", {
            action: "delete_ticket",
            id: id,
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.href = "./kanban.php";
                //history.back();
            }
        });
    });
    <?php } ?>

    //кнопка отмены изменения статуса
    $("#change_status").on('click', '.cancel-change', function () {
        var old_status = $(this).data('old_status');
        $("#ticket_status").val(old_status);
    });

    //кнопка отмены изменения статуса
    $("#add_result").on('click', '.cancel-change', function () {
        var old_status = $(this).data('old_status');
        $("#ticket_status").val(old_status);
    });

    //кнопка сохранить изменения
    $("#change_status").on('click', '#btn_change_status', function () {
        var status = $('#change_status').find('.cancel-change').data('status');
        var old_status = $('#change_status').find('.cancel-change').data('old_status');
        var id = $('#btn_change_status').data('id');
        var date = $('#change_status_date').val();
        var comment = $('#change_status_comment').val();
        if (date == "" || comment == "") {
            alert("Заполните все поля!");
            return false;
        }
        $.post("./api.php", {
            action: "new_ticket_change_status",
            date: date,
            comment: comment,
            id: id,
            status: status
        }).done(function (data) {
            if (data == "false") {
                $("#ticket_status").val(old_status);
                alert("Data Loaded: " + data);
            } else {
                $("#change_status").hide();
                window.location.reload(true);
            }
        });
    });

    //кнопка сохранить изменения
    $("#add_result").on('click', '#btn_add_reuslt', function () {
        var status = $('#add_result').find('.cancel-change').data('status');
        var old_status = $('#add_result').find('.cancel-change').data('old_status');
        var id = $('#btn_add_reuslt').data('id');
        var result = $('#result_description').val();
        if (result == "") {
            alert("Заполните все поля!");
            return false;
        }
        $.post("./api.php", {
            action: "ticket_add_result",
            id: id,
            result: result
        }).done(function (data) {
            if (data == "false") {
                $("#ticket_status").val(old_status);
                alert("Data Loaded: " + data);
            } else {
                $("#add_result").hide();
                window.location.reload(true);
            }
        });
    });


    //смена статуса
    $("#ticket_status").change(function () {
        var id = <?= $ticket_id?>;
        var status = $('#ticket_status').val();
        var old_status = <?= $ticket_status?>;
        var date = '<?= $finish_date?>';
        var subcategory = $('#subcategory').val();
        var ticket_class_2 = $('#ticket_class_2').text();

        if (status == old_status) {
            return false;
        }

        if ((subcategory == 0 || subcategory == "" || subcategory == null)
            && (status != 2 && status != 4 && status != 1)
            && (ticket_class_2 == "P")) {
            $("#ticket_status").val(old_status);
            alert("Не заполнена подкатегория!");
            return false;
        }

        if (status == 6 && !(old_status == 3 || old_status == 8)) {
            $("#ticket_status").val(old_status);
            alert("Архивировать можно только из статусов Решено / Не решено");
            return false;
        }

        if (status == 3) {
            $('#add_result').modal({backdrop: 'static', keyboard: false, show: true});
            $('#add_result').find('.cancel-change').attr('data-status', status);
            $('#add_result').find('.cancel-change').attr('data-old_status', old_status);
            $('#add_result').find('#btn_add_reuslt').attr('data-id', id);
            $('#result_description').val('');
        }

        if (status == 1 || status == 2 || status == 4 || status == 5 || status == 6 || status == 7 || status == 8 || status == 9) {
            if (status == 6 || status == 8) {
                $('#change_status_date').parent().parent().hide();
            }
            $('#change_status').modal({backdrop: 'static', keyboard: false, show: true});
            date = (date == "") ? '<?= $today ?>' : date;
            $('#change_status').find('.cancel-change').attr('data-status', status);
            $('#change_status').find('.cancel-change').attr('data-old_status', old_status);
            $('#change_status').find('#btn_change_status').attr('data-id', id);
            $('#change_status_date').val(date);
            $('#change_status_comment').val('');
        }


        /*
        if (status == 3) {
            $('#add_result').modal('show');
        }
        if (status == 4) {
            $('#add_date').modal('show');
        }
        if (status == 0 || status == 1 || status == 2 || status == 5 || status == 6 || status == 7 || status == 8 || status == 9) {
            $.post("./api.php", {
                action: "ticket_change_status",
                id: id,
                status: status,
                workin: "<?php echo $ticket_work->format('%Y-%m-%d %H:%i:%s');  ?>"
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    window.location.reload(true);
                }
            });
        }*/
    });

    //смена исполнителя
    $("#ticket_assign").change(function () {
        var id = <?php echo $ticket_id?>;
        var assign = $('#ticket_assign').val();
        $.post("./api.php", {
            action: "ticket_change_assign",
            id: id,
            assign: assign
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
    });

    //смена категории
    $("#category").change(function () {
        var id = "";
        $("#category option:selected").each(function () {
            id = $(this).val();
        });
        $.post("./api.php", {action: "ticket_get_subcategory", category: id})
            .done(function (data) {
                $('option', $("#subcategory")).remove();
                $('#subcategory').append("<option value='0' selected>Неизвестно<\/option>");
                var obj = jQuery.parseJSON(data);
                //console.log(data);
                $.each(obj, function (key, value) {
                    $('#subcategory').append($("<option></option>").attr("value", value['id']).text(value['title']));
                });
            });
    });

    $('#add_comment').submit(function () {
        var id =  <?php echo $ticket_id; ?>;
        var robot =  <?php echo $robot_id; ?>;
        var comment = $('#comment').val();
        $.post("./api.php", {
            action: "ticket_add_comment",
            robot: robot,
            id: id,
            comment: comment
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
        return false;
    });

    $('#add_comment_customers').submit(function () {
        var id =  <?php echo $ticket_id; ?>;
        var robot =  <?php echo $robot_id; ?>;
        var comment = $('#comment_customers').val();
        $.post("./api.php", {
            action: "ticket_add_comment_customers",
            robot: robot,
            id: id,
            comment: comment
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
        return false;
    });

</script>
</body>
</html>
