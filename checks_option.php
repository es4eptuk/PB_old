<?php 
include 'include/class.inc.php';
$option_info = $robots->get_info_option($_GET['id']);
$title_option = $option_info['title'];



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
       Чек - лист для опции
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
           
            <!-- /.box-header -->
            
             <div class="box-header">
              <h3 class="box-title"><?php echo $title_option;?></h3>
            </div>
            
            <div class="box-body">
             
                
              <table id="items" class="table table-bordered table-striped">
                <thead>
                <tr>
                 
                  <th>ID</th>
                  <th>Категория</th>
                  <th>Название</th>
                  <th>Комплект деталей</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $checks->get_checks_on_option($_GET['id']);
                
                if (isset($arr)) {
                foreach ($arr as &$pos) {
                    
                     $kit_out = "";
                     if ($pos['id_kit']!=0) {
                         $kit_out = "<a href='edit_kit.php?id=".$pos['id_kit']."'><i class='fa fa-2x fa-cubes'></i></a>";
                     }
                    
                       echo "
                    <tr>
                        <td>".$pos['check_id']."</td>
                        <td>".$pos['title']."</td>
                        <td>".$pos['check_title']."</td>
                        
                        <td align='center'>".$kit_out."</td>
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$pos['check_id']."' data-title='".$pos['check_title']."' data-category='".$pos['check_category']."' data-kit='".$pos['id_kit']."'></i></td>
                    </tr>
                       
                       
                       ";
                    
                }
                } 
                ?>
              </table>
              
            </div>
            
              <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="btn_add_operation">Добавить операцию</button>
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
                  <label>Категория</label>
                  <select class="form-control" name="category" placeholder="Веберите категорию" id="category" required="required">
                   <option value="0">Веберите категорию...</option>
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
                  <label>Наименование</label>
                  <input type="text" class="form-control" name="title" required="required" id="title">
                </div>
                
                <div class="form-group">
                  <label>Комплект деталей</label>

                  <select class="form-control" name="kit"  id="kit" required="required">
                   <option value="0"></option>
                   <?php 
                   $arr = $position->get_kit();
                
                    foreach ($arr as &$kit) {
									echo "<option value='".$kit['id_kit']."'>".$kit['kit_title']." (" .$kit['title'].")</option>"; 
                        }
                   
                   ?>
                  </select>
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

<div class="modal fade" id="check_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Редактирование операции<span id="operation_id"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
          
              <div class="form-group">
                  <label>Категория</label>
                  <select class="form-control" name="category" placeholder="Веберите категорию" id="category_edit" required="required">
                   <option value="0">Веберите категорию...</option>
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
                  <label>Наименование</label>
                  <input type="text" class="form-control" name="title_edit" required="required" id="title_edit">
                </div>
                
                                <div class="form-group">
                  <label>Комплект деталей</label>

                  <select class="form-control" name="kit"  id="kit_edit" required="required">
                   <option value="0"></option>
                   <?php 
                   $arr = $position->get_kit();
                
                    foreach ($arr as &$kit) {
									echo "<option value='".$kit['id_kit']."'>".$kit['kit_title']." (" .$kit['title'].")</option>"; 
                        }
                   
                   ?>
                  </select>
                </div>


                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="btn_edit" name="">Сохранить</button>
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
var id_check = 0;
var id_option = <?php echo $_GET['id']; ?> ;
$( ".fa-pencil" ).click(function() {
               
                    id_check = $(this).attr("id");
                    var title = $(this).data( "title" );
                    var category = $(this).data( "category" );
                    var kit = $(this).data( "kit" );
                    console.log(title);
                     
                    $('#check_edit').modal('show');
                    $('#category_edit').val(category);
                    $('#title_edit').val(title);
                    $('#kit_edit').val(kit);

                  });
                  
 $( "#btn_edit" ).click(function() { 
    
    var id =  id_check;
    var title =  $('#title_edit').val();
    var category =  $('#category_edit').val();
    var kit =  $('#kit_edit').val();
   
      $.post( "./api.php", { 
        action: "edit_check_on_option", 
        id: id,
        title: title,
        category: category,
        kit: kit
       
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./checks_option.php?id="+id_option;  
              }
          });
  
  });  
  
     $( "#btn_add_operation" ).click(function() {
   
    $('#add_operation').modal('show');
     $.post( "./api.php", { 
                            action: "get_checks_group", 
                            category: <?php echo $_GET['id']; ?>
                        } )
                              .done(function( data ) {
                                  if (data=="false") {alert( "Data Loaded: " + data ); }
                                  else {
                                    var obj = jQuery.parseJSON (data);
                                   $.each( obj, function( key, value ) {
                                      $('#group')
                                     .append($("<option></option>")
                                                .attr("value",value['id'])
                                                .text(value['title'])); 
                                                
                                    }); 
                                   
                                    
                                  }
                              });            
                
               

    });
    
$( "#save_close" ).click(function() { 
   save_close();  
  return false;
  }); 
  
function save_close() {
    var category = $('#category').val();
    var title =  $('#title').val();
    var kit =  $('#kit').val();
    var version =  4;
    
    if (category != 0 ) {
      $.post( "./api.php", { 
        action: "add_check_on_option", 
        id_option: id_option,
        title: title ,
        category: category,
        version: version,
        kit: kit
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./checks_option.php?id="+id_option;  
              }
          });
          
    }
    
 }
</script>
</body>
</html>
