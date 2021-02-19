<?php 
include 'include/class.inc.php';
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
       Заказы
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $position->getCategoryes[$_GET['id']]['title'] ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form id="show_all">
                    <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
                    <div class="checkbox">
                    <label>
                      <?php   if ( isset($_GET['show_all']) == 'on') {
                        
                      echo '<input type="checkbox" id="check_show_all" name="show_all" checked>';
                      
                      } else {
                          
                      echo '<input type="checkbox" id="check_show_all" name="show_all"  >';
                          
                      }
                      
                      ?>
                      Отображать завершенные заказы
                    </label>
                  </div>
                    
                </form>
                
              <table id="orders" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th></th>
                  <th></th>
                  <th>Номер</th>
                  <th>Дата составления</th>
                  <th>Группа</th>
                  <th>Контрагент</th>
                  <th>Срок поставки</th>
                  <th>% выполнения</th>
                  <th>Стоимость, руб</th>
                  <th>Статус</th>
                  <th>Просрок, дней</th>
                  <th>Ответственный</th>
                  
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $orders->get_orders_pos($_GET['id']);
                
                if (isset($arr)) {
                foreach ($arr as &$pos) {
                    
                    if ($pos['order_status']!=2 || isset($_GET['show_all'])=='on') {
                    
                    
                        $user_info = $user->get_info_user($pos['order_responsible']);
                        $order_date = new DateTime($pos['order_date']);
                        $order_delivery = new DateTime($pos['order_delivery']);
                        
                        switch ($pos['order_status']) {
                        case 0:
                            $pos['order_status'] = "Новый";
                            break;
                        case 1:
                            $pos['order_status'] = "Принят";
                            break;
                        case 2:
                            $pos['order_status'] = "Отгружен";
                            break;
                        case 3:
                            $pos['order_status'] = "Отменен";
                            break;
                        default:
                            $pos['order_status'] = "Неизвестен";
                    }
                        
                       echo "
                    <tr >
                     <td><i class='fa fa-2x fa-copy' style='cursor: pointer;' id='".$pos['id']."'></i></td>
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$pos['id']."'></i></td>
                        <td>".$pos['id']."</td>
                        <td>".$order_date->format('d.m.Y')."</td>
                        <td>".$position->getCategoryes[$pos['order_category']]['title']."</td>
                        <td>".$position->get_info_pos_provider($pos['order_provider'])."</td>
                        <td>".$order_delivery->format('d.m.Y')."</td>
                        <td>".$pos['order_completion']."</td>
                        <td>".$pos['order_price']."</td>
                        <td>".$pos['order_status']."</td>
                        <td>".$pos['order_prosecution']."</td>
                        <td>".$user_info['user_name']."</td>
                       
                    </tr>
                       
                       
                       ";
                    }
                }
                } 
                ?>
              </table>
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
<div class="modal fade" id="order_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Заказ № <span id="order_id"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
          <form role="form" data-toggle="validator" id="add_pos">
                <!-- text input -->
                <!-- select -->
                <div class="form-group">
                  <label>Категория</label>
                  <select class="form-control" name="category" placeholder="Выберите категорию" id="category" required="required">
                   <option>Выберите категорию...</option>
                   <?php 
                   $arr = $position->getCategoryes;
                
                    foreach ($arr as &$category) {
                       echo "
                       <option value='".$category['id']."'>".$category['title']."</option>
                       
                       ";
                    }
                   
                   ?>
                  </select>
                </div>

                
                 <div class="form-group">
                  <label>Поставщик <small>(<a href="#" data-toggle="modal" data-target="#add_provider">Добавить</a>)</small></label>
                  <select class="form-control" name="provider" placeholder="Выберите категорию" id="provider" required="required">
                   <option>Выберите поставщика...</option>
                   <?php 
                   $arr = $position->get_pos_provider();
                
                    foreach ($arr as &$provider) {
                       echo "
                       <option value='".$provider['id']."'>".$provider['type']." ".$provider['title']."</option>
                       
                       ";
                    }
                   
                   ?>
                  </select>
                  
                  
                </div>
                
                <div class="form-group">
                  <label>Статус </label>
                  <select class="form-control" name="status" placeholder="Выберите статус" id="status" required="required">
                   
                    <option value="0">Новый</option>
                    <option value="1">Отгружен</option>
                    <option value="2">Отменен</option>
                  </select>
                  
                  
                </div>
                
                <div class="form-group">
                  <label>Ответственный </label>
                  <select class="form-control" name="responsible" placeholder="Выберите пользователя" id="responsible" required="required">
                   <option>Выберите пользователя...</option>
                   <?php 
                   $arr = $user->get_users();
                
                    foreach ($arr as &$user) {
                       echo "
                       <option value='".$user['user_id']."'>".$user['user_name']."</option>
                       
                       ";
                    }
                   
                   ?>
                  </select>
                  
                  
                </div>
                
                
                
                <div id="update"></div>
                
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                    <button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>
                </div>
              </form>
         
      </div>
      
    </div>
  </div>
</div>
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
var id_order=0;


    $( "#orders .fa-pencil" ).click(function() {
            id_order = $(this).attr("id");
            window.location.href = "./edit_order.php?id=" + id_order;    
         
    });
    
     $( "#orders .fa-copy" ).click(function() {
            id_order = $(this).attr("id");
            window.location.href = "./copy_order.php?id=" + id_order;    
         
    });



 
  
  
  $( "#save_close" ).click(function() { 
  save_close();
  return false;
  });
  
    $( "#delete" ).click(function() { 
  delete_pos();
  return false;
  });
  
   function delete_pos() {
     var category =  $('#category').val();
     $.post( "./api.php", { 
        action: "delete_pos", 
        id: id_pos
        
        
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./pos.php?id="+category;  
              }
          });
    
   }
  

 function get_info_user(id) {
     
      $.post( "./api.php", { 
        action: "get_info_user", 
        id: id
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                var obj = jQuery.parseJSON (data);
                //console.log(obj['user_name']);  
                return obj['user_name'] ; 
              }
          });
          
    $("#orders").on("click", "td .fa-copy", function() {
        alert('ХУЙ');
        return false;
   });  
   
  
     
 }
 
 $('#check_show_all').change(function() {
        if($(this).is(":checked")) {
            //var returnVal = confirm("Are you sure?");
            $(this).attr("checked", true);
        }
       // alert($(this).is(':checked')); 
        $("#show_all").submit();
        
    });
 
  $('#orders').DataTable({
       "iDisplayLength": 100,
        "order": [[ 0, "desc" ]]
    } );
  
  
</script>
</body>
</html>
