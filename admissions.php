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
       Поступления
        
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
              <table id="orders" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Номер</th>
                  <th>Дата поступления</th>
                  <th>Группа</th>
                  <th>Контрагент</th>
                  <th>Описание</th>
                  <th>Ответственный</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $admission->get_admission($_GET['id']);
                
                if (isset($arr)) {
                foreach ($arr as &$pos) {
                        $user_info = $user->get_info_user($pos['responsible']);
                        $admis_date = new DateTime($pos['date']);
                        $provider = $position->get_info_pos_provider($pos['provider']);
                        
                       echo "
                    <tr id='".$pos['id']."'>
                        <td>".$pos['id']."</td>
                        <td>".$admis_date->format('d.m.Y')."</td>
                        <td>".$position->getCategoryes[$pos['category']]['title']."</td>
                        <td>".$provider['title'].", ".$provider['type']."</td>
                        <td>".$pos['description']."</td>
                        <td>".$user_info['user_name']."</td>
                    </tr>
                       
                       
                       ";
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
<!-- Modal
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
                  <select class="form-control select2" name="provider" id="provider" required="required">
                   <option>Выберите поставщика...</option>
                   <?php 
                   $arr = $position->get_pos_provider();
                    foreach ($arr as &$provider) {
                       echo "<option value='".$provider['id']."'>".$provider['type']." ".$provider['title']."</option>";
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
-->
<?php include 'template/scripts.php'; ?>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<!-- Select2 -->
<script src="./bower_components/select2/dist/js/select2.full.min.js"></script>

<script>
    //$('.select2').select2();

    /*
    var id_order=0;


    $( "#orders tr" ).click(function() {
            id_order = $(this).attr("id");
            //window.location.href = "./edit_admission.php?id=" + id_order;    
         
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
   */

   /*
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

    */
 /*
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
     
 }

  */
 $('#orders').DataTable({
       "iDisplayLength": 100,
        "order": [[ 0, "desc" ]]
    } );
</script>
</body>
</html>
