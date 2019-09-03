<?php 
include 'include/class.inc.php';

//$robot_info = $robots->get_info_robot($_GET['id']);
//$robot_number = $robot_info['number'];
//$robot_name= $robot_info['name'];
//$robot_id= $robot_info['id'];
?>

<?php include 'template/head.html' ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include 'template/header.html' ?>
  <!-- Left side column. contains the logo and sidebar -->
  <?php include 'template/sidebar.html';?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       KANBAN
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
     
    
        
        <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Фильтр</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Исполнитель</label>
                <select class="form-control select3" style="width: 100%;" id="filter_user">
                    <option value="0">Выберите исполнителя</option>
                    <?php 
											                   $arr_user = $user->get_users(0);
											                //echo $ticket_assign_id;
											                    foreach ($arr_user as &$user_assign) {
											                       
											                        
											                       echo "
											                       <option value='".$user_assign['user_id']."' >".$user_assign['user_name']."</option>
											                       
											                       ";
											                    }
											                   
											                   ?>
                </select>
                
                
              </div>
              <!-- /.form-group -->
             
              <!-- /.form-group -->
              <a href="./kanban.php">Сбросить фильтр</a>
            </div>
            <!-- /.col -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Номер робота</label>
                <select class="form-control select3" style="width: 100%;" id="filter_robot">
                     <option value="0">Выберите робота</option>
                  <?
                   $arr_robots = $robots->get_robots();
                   
                    if (isset($arr_robots)) {
                foreach ($arr_robots as &$robot) {
                     if ($robot['progress']==100 ) {
                    
                    echo  ' <option value="'.$robot['id'].'">'.$robot['version'].'.'.$robot['number'].'('.$robot['name'].')</option>';
                    
                    
                     }
                }
                    }
                   
                  ?>
                </select>
              </div>
            </div>
            
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
        
      </div>
      
        
    <div class="row">
       
        
        <? 
         $arr = $tickets->get_status(0);
          if (isset($arr)) {
                foreach ($arr as &$status) {
                    $bg_color = $status['color'];
                    $font_color = $status['font'];
                    $title =  $status['title'];
                    $id = $status['id'];
                    $arhiv = $id==2 ? 'exp1' : 'exp2';
                   
                echo '
                    <div class="col-md-2">
                      <div class="box box-default box-solid" style="border-color: '.$bg_color.';" id="overlay'.$id.'">
                        <div class="box-header with-border" style="background-color: '.$bg_color.'; background: '.$bg_color.'; color: '.$font_color.';" >
                          <h3 class="box-title">'.$title.'</h3>
                          <span class="pull-right dropdown"><i class="fa fa-ellipsis-h" data-toggle="dropdown"></i>
                          <ul class="dropdown-menu dropdown-menu-right">
                         
                            <li class="dropdown-header">По дате создания карточки</li>
                            <li class="divider"></li>
                            <li><a href="#" class="sort" data-sortby="date_create" data-sortdir="ASC" data-status="'.$id.'">сначала старые</a></li>
                            <li><a href="#" class="sort" data-sortby="date_create" data-sortdir="DESC" data-status="'.$id.'">сначала новые</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header">По дате изменения карточки</li>
                            <li class="divider"></li>
                             <li><a href="#" class="sort" data-sortby="update_date" data-sortdir="ASC" data-status="'.$id.'">сначала старые</a></li>
                            <li><a href="#" class="sort" data-sortby="update_date" data-sortdir="DESC" data-status="'.$id.'">сначала новые</a></li>
                            <li class="dropdown-header"></li>
                            <li class="divider"></li>
                            <li><a data-status="'.$id.'" href="#" class="'.($id == 3 ? 'arhiv' : '').'">Архивировать все карточки списка</a></li>
                          </ul>
                          </span>
                          <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body connectedSortable sortable" id="'.$id.'">
                ';  
                
                 $arr_tickets = $tickets->get_tickets();
                
                if (isset($arr_tickets)) {
                foreach ($arr_tickets as &$ticket) {
                    if ($ticket['status']==$id) {
                        $user_info = $user->get_info_user($ticket['assign']);
                         
                        $username_assign = $user_info['user_name'];
                        $ticket_date = new DateTime($ticket['update_date']);
                        $status = $ticket['status'];
                        
                        $status_info = $tickets->get_info_status($status);
                        $status = $status_info['title'];
                        
                        $robot_info = $robots->get_info_robot($ticket['robot']);
                               $robot_number = $robot_info['number'];
                               $robot_version = $robot_info['version'];
                               
                                $ticket_category = $ticket['category'];
                                $category_info = $tickets->get_info_category($ticket_category);
                                $ticket_category = $category_info['title'];
                                
                                $ticket_subcategory = $ticket['subcategory'];
                                $subcategory_info = $tickets->get_info_subcategory($ticket_subcategory);
                                $ticket_subcategory = $subcategory_info['title'];
                                
                                $ticket_description = $ticket['description'];
                                $ticket_class = $ticket['class'];
                        
                                $arr_comments = $tickets->get_comments($ticket['id']); 
                                $arr_count_comments = count($arr_comments);
                        $lng = mb_strlen($ticket_description,'UTF-8');
                        if($lng>100) {$ticket_description = mb_substr($ticket_description, 0, 100)."...";} 
                        $str_date_finish = "";
                        if ($ticket['finish_date']!='0000-00-00') {
                            $date_finish = new DateTime($ticket['finish_date']);
                            $str_date_finish = 'Ремонт назначен на <b>'.$date_finish->format('d.m.Y').'</b><br><br>';}
                        echo '
                        <div class="box box-solid" style="background-color: #f9f9f9;" id="'.$ticket['id'].'">
                                    <div class="box-body">
                                      <b>'.$username_assign.'</b> <span class="pull-right text-muted">'.$robot_version.'.'.$robot_number.' </span></br>
                                      <b><a href="./ticket.php?id='.$ticket['id'].'"><span class="ticket_class">'.$ticket_class.'</span>-'.$ticket['id'].' '.$ticket_category.':<span class="subcategory"> '.$ticket_subcategory.'</span></a></b> 
                                      <p>'.$ticket_description.'</p>
                                      '.$str_date_finish.'
                                      <span class="pull-right text-muted"><i class="fa fa-paperclip"></i> 0 <i class="fa fa-comments"></i> '.$arr_count_comments.'</span>
                                      <span class="pull-left text-muted"><i class="fa fa-calendar-o"></i> '.$ticket_date->format('d.m.y H:i').'</span>
                                    </div>
                            </div>
                        
                        ';
                        
                    }
                    
                    
                }
                }            
                            
                           
                       echo  '  
                        </div>
                        <!-- /.box-body -->
                      </div>
                      <!-- /.box -->
                    </div>
                ';
                    
                }   
            
              
          }    
        ?>
            
            
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
<div class="modal fade" id="add_result" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                  <textarea class="form-control" rows="5" id="result_description"></textarea>
                </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary" id="btn_add_reuslt">Сохранить</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="add_date" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Укажите дату решения проблемы</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div class="form-group">
                <label>Дата:</label>

                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="datepicker">
                </div>
                <!-- /.input group -->
              </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary" id="btn_add_date">Сохранить</button>
      </div>
    </div>
  </div>
</div>

  
</div>
<!-- ./wrapper -->
<?php include "./template/scripts.html";?>
<script src="../../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
 <!-- Select2 -->
    <script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
<script>

 //Date picker
    $('#datepicker').datepicker({
      format: 'dd.mm.yyyy',
      autoclose: true
    })
    
    
var id_s = 0;
 $( "#btn_add_reuslt" ).click(function() {
     
     var id = id_s;
     var result = $('#result_description').val();
     
      $.post( "./api.php", { 
                    action: "ticket_add_result", 
                    id: id,
                    result: result
                } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.reload(true);
                
              }
          });
     
 });
 
 $( "#btn_add_date" ).click(function() {
     
     var id = id_s;
     var date = $('#datepicker').val();
     
      $.post( "./api.php", { 
                    action: "ticket_add_date", 
                    id: id,
                    date: date
                } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.reload(true);
                
              }
          });
     
 });
 
  $( ".sort" ).click(function() {
      
     var sortBy = $(this).data( "sortby" );
     var sortDir = $(this).data( "sortdir" );
     var statusId = $(this).data( "status" );
     $("#"+statusId).empty();
     $("#overlay"+statusId).append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
     
      $.post( "./api.php", { 
                    action: "ticket_get", 
                    robot: 0,
                    user:0,
                    status: statusId,
                    sortby: sortBy,
                    sortdir: sortDir
                        } )
                  .done(function( data ) {
                      var tickets = jQuery.parseJSON (data);
                      //window.location.reload(true);
                      $.each(tickets, function( index, value ) {
                          console.log(statusId);
                          $("#"+statusId).append(' <div class="box box-solid" style="background-color: #f9f9f9;" id="'+value['id']+'"> \
                                    <div class="box-body"> \
                                    <b>'+value['assign']+'</b> <span class="pull-right text-muted">'+value['robot']+'</span></br> \
                                      <b><a href="./ticket.php?id='+value['id']+'">'+value['class']+'-'+value['id']+' '+value['category']+': '+value['subcategory']+'</a></b> \
                                      <p>'+value['description']+'</p> \
                                      <span class="pull-right text-muted"><i class="fa fa-paperclip"></i> 0 <i class="fa fa-comments"></i> '+value['comments']+'</span> \
                                      <span class="pull-left text-muted"><i class="fa fa-calendar-o"></i> '+value['update_date']+'</span> \
                                    </div>\
                            </div>');
                            $("#overlay"+statusId).find(".overlay").remove();
                          console.log(value);
                        });
                      
                     
                  });
                  
     
     
     
 });
 
  $( ".arhiv" ).click(function() {
      
     var statusId = $(this).data( "status" );
     $("#"+statusId).empty();
     $("#overlay"+statusId).append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    
      $.post( "./api.php", { 
                    action: "ticket_arhiv", 
                    id: statusId
                        } )
                  .done(function( data ) {
                      
                            $("#overlay"+statusId).find(".overlay").remove();
                         
                     
                      
                     
                  });
                  
     
     
     
 });


 // $('.comment').validator();    
   $('.select2').select2(); 
   $( ".fa-align-justify" ).click(function() {
   var id = $(this).attr("id");
   
     window.location.href = "./ticket.php?id="+id;

    });
  
    
     $( ".fa-times" ).click(function() {
               
                id_log = $(this).attr("id");
                
               
                
                $.post( "./api.php", { 
                    action: "delete_log", 
                    id: id_log
                        } )
                  .done(function( data ) {
                      window.location.reload(true);
                     
                  });
               

    });
    
    
    $('#orders').DataTable({
       "iDisplayLength": 100,
        "order": [[ 0, "desc" ]]
    } );
    
    <?php 
    
    if ($userdata['user_id']==29) {
    echo "
    //setTimeout(function() {window.location.reload();}, 20000);
    
    ";
    }
    ?>
 
 $( document ).ready(function() {

  
  //$("#filter_user").val(<?php if (isset($_GET['user'])) echo $_GET['user']; ?>); 
   $("#filter_robot").val(<?php if (isset($_GET['robot'])) echo $_GET['robot']; ?>); 
  
   //$('#filter_user').val(<?php if (isset($_GET['user'])) echo $_GET['user']; ?>).trigger('change');  
      
});
    
  $( function() {
    
    $('[data-toggle="popover"]').popover(); 

    
    $( ".sortable" ).sortable({
        stop: function( event, ui ) {
            var id = ui['item'][0]['id'];
            var status = ui['item'][0]['parentElement']['id'];
            console.log(ui);
            console.log(id);
            console.log(status);
            var subcategory = $("#"+id).find(".subcategory").text();
            var ticket_class = $("#"+id).find(".ticket_class").text();
            console.log(ticket_class);
             if (subcategory==0 || subcategory=="" || subcategory==null) {
                if (ticket_class=="P"){
                //$("#ticket_status option[value='0']").attr("selected","selected");
                alert("Не заполнена подкатегория!");
                return false;
                }
            }
            
            
           if (status==3) {
                    $('#add_result').modal('show');
                    id_s = id;
                }
                
             if (status==4) {
                    $('#add_date').modal('show');
                    id_s = id;
                }     
                
                
            if (status==0 || status==1  ||status==2 || status==5 || status==6 || status==7) {
            
             $.post( "./api.php", { 
                    action: "ticket_change_status", 
                    id: id,
                    status: status
                } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                //window.location.href = "./robots.php";
                
              }
          });
                }
        
  },    
      connectWith: ".connectedSortable"
    }).disableSelection();
  } );
  
  
 $( "#filter_user" )
  .change(function () {
   
   var user = $( "#filter_user" ).val();
   var robot = $( "#filter_robot" ).val();
   
   if (robot==0) {
        window.location.href = "./kanban.php?user="+user;
   } else {
        window.location.href = "./kanban.php?user="+user+"&robot="+robot;
       
   }
 
  }); 
  
   $( "#filter_robot" )
  .change(function () {
   
   var user = $( "#filter_user" ).val();
   var robot = $( "#filter_robot" ).val();
   
   if (user==0) {
        window.location.href = "./kanban.php?robot="+robot;
   } else {
        window.location.href = "./kanban.php?user="+user+"&robot="+robot;
       
   }
 
  }); 
    
</script>
</body>
</html>
