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
       Комплектации
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
           
            <!-- /.box-header -->
            <div class="box-body">
             
                
              <table id="orders" class="table table-bordered table-striped">
                <thead>
                <tr>
                 
                  <th>ID</th>
                 
                  <th>Название</th>
                 
                   <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $position->get_equipment();
                
                if (isset($arr)) {
                foreach ($arr as &$pos) {

                       echo "
                    <tr>
                        <td>".$pos['id']."</td>
                        <td>".$pos['title']."</td>
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$pos['id']."'></i></td>
                    </tr>
                       
                       
                       ";
                    
                }
                } 
                ?>
              </table>
              
            </div>
            
              <div class="box-footer">
                    <a href="./add_equipment.php" class="btn btn-primary" >Добавить комплектацию</a>
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
                  <select class="form-control" name="category" placeholder="Веберите категорию" id="category" required="required">
                   <option>Веберите категорию...</option>
                   <?php 
                   $arr = $position->get_pos_category();
                
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
                  <select class="form-control" name="provider" placeholder="Веберите категорию" id="provider" required="required">
                   <option>Веберите поставщика...</option>
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
                  <select class="form-control" name="status" placeholder="Веберите статус" id="status" required="required">
                   
                    <option value="0">Новый</option>
                    <option value="1">Отгружен</option>
                    <option value="2">Отменен</option>
                  </select>
                  
                  
                </div>
                
                <div class="form-group">
                  <label>Ответственный </label>
                  <select class="form-control" name="responsible" placeholder="Веберите пользователя" id="responsible" required="required">
                   <option>Веберите пользователя...</option>
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
            id_equipment = $(this).attr("id");
            window.location.href = "./edit_equipment.php?id=" + id_equipment;    
         
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
  
  function save_close() {
    var title =  $('#title').val();
    var longtitle =  $('#longtitle').val();
    var category =  $('#category').val();
    var subcategory =  $('#subcategory').val();
    var vendorcode =  $('#vendorcode').val();
    var provider =  $('#provider').val();
    var price =  $('#price').val();
    var quant_robot =  $('#quant_robot').val(); 
    var quant_total =  $('#quant_total').val();
    
    
      $.post( "./api.php", { 
        action: "edit_pos", 
        id: id_pos,
        title: title,
        longtitle: longtitle ,
        category: category ,
        subcategory: subcategory ,
        vendorcode: vendorcode ,
        provider: provider ,
        price: price ,
        quant_robot: quant_robot ,
        quant_total: quant_total 
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
