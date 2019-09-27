<?php 
include 'include/class.inc.php';

$robot_info = $robots->get_info_robot($_GET['id']);
$robot_number =  $num = str_pad( $robot_info['number'], 4, "0", STR_PAD_LEFT); 
$robot_name= $robot_info['name'];
$robot_version= $robot_info['version'];
$robot_id= $robot_info['id'];
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
          <?php echo "<a href=\"./kanban.php\"><i class=\"fa fa-long-arrow-left\"></i> KANBAN</a>";?>
       Promobot <?php echo $robot_version.".".$robot_number; ?>
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
        
        
        
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $robot_name; ?></h3>
            </div>
            <!-- /.box-header -->
            
            
            
            <div class="box-body table-responsive">
               
              
      
     
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Добавить событие</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" class="comment">
              <div class="box-body">
                    <div class="form-group">
                  <label>Класс обращения </label>
                  <select class="form-control" id="ticket_class" required="required">
                      <option value="">Веберите класс...</option>
                      <option value="I">Консультация</option>
                      <option value="P">Проблема</option>
                      <option value="FR">Пожелание</option>
                   
											                       
                  </select>
                </div>
                  
                <div class="form-group" id="form_category">
                  <label>Категория <small>(<a href="#" data-toggle="modal" data-target="#add_category">Добавить</a>)</small></label>
                  <select class="form-control" id="category" >
                      <option value="0">Веберите категорию...</option>
                     <?php 
											                   $arr = $tickets->get_category();
											                
											                    foreach ($arr as &$category) {
											                       echo "
											                       <option value='".$category['id']."'>".$category['title']."</option>
											                       
											                       ";
											                    }
											                   
											                   ?>
                  </select>
                </div>
                
                <div class="form-group" id="form_subcategory">
                  <label>Уточнение <small>(<a href="#" data-toggle="modal" data-target="#add_subcategory">Добавить</a>)</small></label>
                  <select class="form-control" id="subcategory" >
                  </select>
                </div>
                
                
                <div class="form-group">
                  <label>Описание</label>
                  <textarea class="form-control" rows="3" placeholder="Введите описание ..." name="comment" id="comment" required="required"></textarea>
                </div>
             
              
              
              <div class="form-group">
                  <label>Статус</label>
                  <select class="form-control" id="status" required="required">
                                             <?php 
											                   $arr = $tickets->get_status();
											                
											                    foreach ($arr as &$status) {
											                       echo "
											                       <option value='".$status['id']."'>".$status['title']."</option>
											                       
											                       ";
											                    }
											                   
											                   ?>
                  </select>
                </div>
               </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Добавить</button>
              </div>
            </form>
          </div>
                
                
                <div class="col-md-12">
          <!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="false">Тикеты</a></li>
              <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="true">История</a></li>
             
             
             
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                  
                  
                   <table id="orders" class="table table-bordered table-striped">
                <thead>
                <tr>
                  
                  <th>#</th>
                  <th>Категория</th>
                  <th>Подкатегория</th>
                  <th>Статус</th>
                  <th>Ответственный</th>
                  <th>Дата создания</th>
                  <th>Дата изменения</th>
                  <th ></th>
                 
                 
                </tr>
                </thead>
                <tbody>
                
                
                <? 
                $arr_tickets = $tickets->get_tickets($robot_id);
                
                
             if (isset($arr_tickets)) {
                foreach ($arr_tickets as &$ticket) {
                    $category_info = $tickets->get_info_category($ticket['category']);
                    $ticket_category = $category_info['title'];
                    
                    $subcategory_info = $tickets->get_info_subcategory($ticket['subcategory']);
                    $ticket_subcategory = $subcategory_info['title'];

                    $user_info = $user->get_info_user($ticket['assign']);
                    $ticket_assign = $user_info['user_name'];
                    
                    $status_info = $tickets->get_info_status($ticket['status']);
                    $ticket_status_str = $status_info['title'];
                    $color = $status_info['color'];
                    $font = $status_info['font'];
                    
                    $ticket_update =  $ticket['update_date'];
                    $ticket_update = new DateTime($ticket_update);
                    $ticket_create =  $ticket['date_create'];
                    $ticket_create = new DateTime($ticket_create);
                    $ticket_ico = "<i class='fa fa-2x fa-align-justify' style='cursor: pointer; ' id='".$ticket['id']."'></i>";
                    
                  echo "
                    <tr style='background: ".$color."; color: ".$font."'>
                     
                        
                        <td>".$ticket['class']."-".$ticket['id']."</td>
                        <td>".$ticket_category."</td>
                         <td>".$ticket_subcategory."</td>
                        <td>".$ticket_status_str."</td>
                        <td>".$ticket_assign."</td>
                        <td>".$ticket_create->format('d.m.Y H:i:s')." </td>
                        <td>".$ticket_update->format('d.m.Y H:i:s')."</td>
                         <td style='text-align: center;'>".$ticket_ico."</td>
                       
                    </tr>
                       
                       
                       ";  
                    
                }
}
               ?>
                    
                </tbody>
                </table>
               
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
               <table id="orders" class="table table-bordered table-striped">
                <thead>
                <tr>
                  
                  <th>ID</th>
                  <th>Источник</th>
                  <th>Уровень</th>
                  <th>Статус</th>
                  <th>Пользователь</th>
                  <th>Дата</th>
                   <th ></th>
                  <?php if ($userdata['group']==1) {echo '<th>Удалить</th>';} ?>
                 
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $robots->get_log($_GET['id']);
                
                if (isset($arr)) {
                foreach ($arr as &$log) {
                    $ticket = "";
                    $user_info = $user->get_info_user($log['update_user']);
                    $log_date = new DateTime($log['update_date']);
                   
                    
                    
                    
                    if ($log['source']=="TICKET") {
                        
                       
                        //$tickets_info = $tickets->info($log['ticket_id']);
                        $status_info = $tickets->get_info_status($log['level']);
                        $status = $status_info['title'];
                        
                        $ticket_info = $tickets->info($log['ticket_id']);
                        $ticket_id = $ticket_info['id'];
                        $ticket_author = $ticket_info['user_create'];
                        $ticket_robot = $ticket_info['robot'];
                        $ticket_category = $ticket_info['category'];
                        $category_info = $tickets->get_info_category($ticket_category);
                        $ticket_category = $category_info['title'];
                        $ticket_subcategory = $ticket_info['subcategory'];
                        $subcategory_info = $tickets->get_info_subcategory($ticket_subcategory);
                        $ticket_subcategory = $subcategory_info['title'];
                        $ticket_description =  $ticket_info['description'];
                        $ticket_update =  $ticket_info['update_date'];
                        
                        $ticket_class =  $ticket_info['class'];
                        
                        
                       // print_r($log['ticket_id']);
                        $ticket = "<i class='fa fa-2x fa-align-justify' style='cursor: pointer; ' id='".$log['ticket_id']."'></i>";
                        $level = $status;
                        $color = $status_info['color'];
                        $font = $status_info['font'];
                        
                        switch ($ticket_class) {
                            case "I":
                                $icon =  "<span style='font-size:16px'>❓</span>";
                                break;
                            case "P":
                                 $icon =  "<span style='font-size:16px'>🆘</span>";
                                break;
                            case "FR":
                                 $icon =  "<span style='font-size:16px'>🆕</span>";
                                break;
                            default:
                                 $icon =  "<span style='font-size:16px'>🆘</span>";
                                break;    
                        }
                        if ($ticket_class=='')$ticket_class = 'P';
                        $comment =  $icon. " ".$ticket_class."-".$log['ticket_id']." ".$ticket_category.": ". $ticket_subcategory;
                    } else {
                       
                    $level = $log['level'];
                    $comment = $log['comment'];
                    $font = "";
                    switch ($level) {
                        case "INFO":
                            $color = "#f1f7c1";
                            break;
                        case "GOOD":
                            $color = "#c1f7cc";
                            break;
                        case "WARNING":
                            $color = "#f7c1e4";
                            break;
                        case "MODERN":
                            $color = "#dce0ff";
                            break; 
                        case "TICKET":
                            $color = "#90bec5";
                            break;     
                    } 
                        
                    }
                    
                    
                    
                    
                    
                    $out_del = "";
                    if ($userdata['group']==1) {$out_del = " <td><center><i class='fa fa-2x fa-times' style='cursor: pointer;' id='".$log['id']."'></i></center></td>";}
                   
                       echo "
                    <tr style='background: ".$color."; color: ".$font."'>
                     
                        
                        <td>".$log['id']."</td>
                        <td>".$log['source']."</td>
                        <td>".$level."</td>
                        <td>".$comment."</td>
                        <td>".$user_info['user_name']." </td>
                        <td>".$log_date->format('d.m.Y H:i:s')."</td>
                        <td style='text-align: center;'>".$ticket."</td>
                       ".$out_del."
                       
                    </tr>
                       
                       
                       ";
                    }
                }
               
                ?>
              </table>
              </div>
              <!-- /.tab-pane -->
              
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
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
  
  	<div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_category" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавить категорию</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<form data-toggle="validator" id="add_provider_form" name="add_category_form" role="form">
						<!-- select -->
						<div class="form-group">
							<label>Название</label> <input class="form-control" id="category_title" name="category_title" required="required" type="text">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_add_category" type="button">Добавить</button>
				</div>
			</div>
		</div>
	</div>
	
	<div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_subcategory" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавить подкатегорию</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<form data-toggle="validator" id="add_provider_form" name="add_subcategory_form" role="form">
						<!-- select -->
					
						<div class="form-group">
							<label>Название</label> <input class="form-control" id="subcategory_title" name="subcategory_title" required="required" type="text">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_add_subcategory" type="button">Добавить</button>
				</div>
			</div>
		</div>
	</div>
	

  
</div>
<!-- ./wrapper -->
<!-- jQuery 3 -->
<script src="../../bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- page script -->
<script>
$('#form_category').hide();
$('#form_subcategory').hide();

$("#btn_add_category").click(function() {
 
 	var title = $('#category_title').val();
    var ticket_class =  $('#ticket_class').val();
 	if (title != "") {
 		$.post("./api.php", {
 			action: "ticket_add_category",
 			title: title,
 			cat_class: ticket_class
 		}).done(function(data) {
 			
 			if (data == "false") {
 				alert("Data Loaded: " + data);
 				return false;
 			} else {
 			   
 				$('#category').append("<option value='" + data + "' selected>" + title + "<\/option>");
 				
 			      $('option', $("#subcategory")).remove();
 				
 				$('#add_category').modal('hide');
 				//return false;
 			}
 		});
 	}
 });

$("#btn_add_subcategory").click(function() {
    
    var category = $('#category').val();
 	var title = $('#subcategory_title').val();
 	console.log(category);
 	
 	if (category==0) {return false;}
 
 	if (title != "") {
 		$.post("./api.php", {
 			action: "ticket_add_subcategory",
 			category: category,
 			title: title
 		}).done(function(data) {
 			
 			if (data == "false") {
 				alert("Data Loaded: " + data);
 				return false;
 			} else {
 			   
 				$('#subcategory').append("<option value='" + data + "' selected>" + title + "<\/option>");
 				$('#add_subcategory').modal('hide');
 				//return false;
 			}
 		});
 	}
 });

 $( ".fa-align-justify" ).click(function() {
   var id = $(this).attr("id");
     window.location.href = "./ticket.php?id="+id;
    });

$( "#ticket_class" )
  .change(function () {
   var ticket_class =  $('#ticket_class').val();
    
    switch(ticket_class) {
              case 'I':  
                //alert ('I');
                $('#form_category').show();
                $('#form_subcategory').hide();
                break;
            
              case 'P':  
                $('#form_category').show();
                $('#form_subcategory').show();
                //alert ('P');
                break;
            
              case 'FR': 
                $('#form_category').show();
                $('#form_subcategory').hide();
                //alert ('FR');
                break;
            }

    });
 
 
 

 // $('.comment').validator();    
 $('.comment').submit(function(){
  var robot =  <?php echo $robot_id; ?>;    
  
  var ticket_class =  $('#ticket_class').val();
  
              switch(ticket_class) {
              case 'I':  
               // alert ('I');
               var category =  $('#category').val();
                var subcategory =  "";
                break;
            
              case 'P':  
               
                
                var category =  $('#category').val();
                var subcategory =  $('#subcategory').val();
                var category_str =  $('#category option:selected').text();
                var subcategory_str =  $('#subcategory option:selected').text();
                //alert ('P');
                break;
            
              case 'FR': 
                var category =  $('#category').val();
                var subcategory =  "";
                //alert ('FR');
                break;
            }
  
  
  
 
  var comment = $(this).find('#comment').val();
  var status = $('#status').val();
  
  //comment = category_str + ': ' + subcategory_str;
  
  
  
  
   $.post( "./api.php", { 
        action: "ticket_add", 
        robot: robot,
        category: category ,
        subcategory: subcategory ,
        ticket_class: ticket_class ,
        status: status, 
        comment: comment
        
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./robot_card.php?id="+robot;
                
              }
          });
  
  
  return false;
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
    
$( "#category" )
  .change(function () {
    var id = "";
    var ticket_class =  $('#ticket_class').val();
    $( "#category option:selected" ).each(function() {
      id = $( this ).val();
    });
 
    $.post( "./api.php", { action: "ticket_get_subcategory", category: id, cat: ticket_class } )
    .done(function( data ) {
        $('option', $("#subcategory")).remove();
         $('#subcategory').append("<option value='0' selected>Неизвестно<\/option>");
        var obj = jQuery.parseJSON(data);
        console.log(data);
        $.each( obj, function( key, value ) {
         
          $('#subcategory')
         .append($("<option></option>")
                    .attr("value",value['id'])
                    .text(value['title'])); 
                    
                    
        });

    });
 
 
  });    
    
$( "#ticket_class" )
  .change(function () {
    var type = "";
   
    $( "#ticket_class option:selected" ).each(function() {
      type = $( this ).val();
    });
 
    $.post( "./api.php", { action: "ticket_get_category", type: type} )
    .done(function( data ) {
        $('option', $("#category")).remove();
         $('#category').append("<option value='0' selected>Неизвестно<\/option>");
        var obj = jQuery.parseJSON(data);
        console.log(data);
        $.each( obj, function( key, value ) {
         
          $('#category')
         .append($("<option></option>")
                    .attr("value",value['id'])
                    .text(value['title'])); 
                    
                    
        });

    });
 
 
  });
</script>
</body>
</html>
