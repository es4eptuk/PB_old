<?php 
include 'include/class.inc.php';
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
       Лог системы
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">История операций</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="pos" class="table table-responsive">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Операция</th>
                  <th>Метод</th>
                  <th>Пользователь</th>
                  <th>Дата</th>
                  
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $log->get_all();
               
                if (isset($arr)) {
                foreach ($arr as &$pos) {
                    $user_info = $user->get_info_user($pos['update_user']);
                       echo "
                       <tr>
                          <td>".$pos['id']."</td>
                          <td>".$pos['log']."</td>
                          <td>".$pos['method']."</td>
                          <td>".$user_info['user_name']."</td>
                          <td>".$pos['update_date']."</td>
                          
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
<!-- Modal -->
<div class="modal fade" id="pos_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Редактирование позиции</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
          <form role="form" data-toggle="validator" id="add_pos">
                <!-- text input -->
                <div class="form-group">
                  <label>Наименование</label>
                  <input type="text" class="form-control" name="title" required="required" id="title">
                </div>
                <div class="form-group">
                  <label>Описание</label>
                  <input type="text" class="form-control" name="longtitle" id="longtitle">
                </div>

                

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
                  <label>Подкатегория</label>
                  <select class="form-control" name="subcategory" id="subcategory" required="required">
                    
                  </select>
                </div>
                
              
                <div class="form-group">
                  <label>Артикул</label>
                  <input type="text" class="form-control" name="vendorcode" required="required" id="vendorcode">
                </div>
                
                 <div class="form-group">
                  <label>Поставщик <small>(<a href="#" data-toggle="modal" data-target="#add_provider">Добавить</a>)</small></label>
                  <select class="form-control" name="provider" placeholder="Веберите категорию" id="provider" required="required">
                   <option>Веберите поставщика...</option>
                   <?php 
                   $arr = $position->get_pos_provider();
                
                    foreach ($arr as &$provider) {
                       echo "
                       <option value='".$provider['id']."'>".$provider['title'].", ".$provider['type']."</option>
                       
                       ";
                    }
                   
                   ?>
                  </select>
                  
                  
                </div>
                
                <div class="form-group">
                  <label>Стоимость</label>
                  <input type="text" class="form-control" name="price" placeholder="0.00" id="price">
                </div>
                
                <div class="form-group">
                  <label>Количество на робота</label>
                  <input type="text" class="form-control" name="quant_robot" placeholder="0" required="required" id="quant_robot">
                </div>
                
                 <div class="form-group">
                  <label>Количество на складе</label>
                  <input type="text" class="form-control" name="quant_total" placeholder="0" id="quant_total">
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

<!-- Modal -->
	<div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_provider" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавить поставщика</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<form data-toggle="validator" id="add_provider_form" name="add_provider_form" role="form">
						<!-- select -->
						<div class="form-group">
							<label>Форма собственности</label> <select class="form-control" id="provider_type" name="provider_type" required="required">
								<option value="ИП">
									ИП
								</option>
								<option value="ООО">
									ООО
								</option>
								<option value="ОАО">
									ОАО
								</option>
								<option value="ЗАО">
									ЗАО
								</option>
								<option value="ЗАО">
									Ltd.
								</option>
							</select>
						</div>
						<div class="form-group">
							<label>Наименование</label> <input class="form-control" id="provider_title" name="provider_title" required="required" type="text">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_add_provider" type="button">Добавить</button>
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
var id_pos=0;


$( "#category" )
  .change(function () {
    var id = "";
    
    $( "#category option:selected" ).each(function() {
      id = $( this ).val();
    });
 
    $.post( "./api.php", { action: "get_pos_sub_category", subcategory: id } )
    .done(function( data ) {
        $('option', $("#subcategory")).remove();
        var obj = jQuery.parseJSON(data);
        //console.log(obj);
        $.each( obj, function( key, value ) {
          $('#subcategory')
         .append($("<option></option>")
                    .attr("value",value['id'])
                    .text(value['title'])); 
                    
        });
    });
  });


    $( "#pos .fa-pencil" ).click(function() {
               
                id_pos = $(this).attr("id");
                
                $('#pos_edit').modal('show');
                
                $.post( "./api.php", { 
                    action: "get_info_pos", 
                    id: id_pos
                        } )
                  .done(function( data ) {
                     var obj = jQuery.parseJSON (data);
                    
                     
                      $.post( "./api.php", { 
                            action: "get_info_user", 
                            id: obj['update_user']
                        } )
                              .done(function( data ) {
                                  if (data=="false") {alert( "Data Loaded: " + data ); }
                                  else {
                                    var obj2 = jQuery.parseJSON (data);
                                    //console.log(obj['user_name']);  
                                     $('#update').text("Изменено: " + obj['update_date'] + "  (" +obj2['user_name'] +")");
                                    
                                  }
                              });
                     
                     
                   
                    // console.log (get_info_user(obj['update_user']));
                     $('#title').val(obj['title']);
                     $('#longtitle').val(obj['longtitle']);
                     $('#category').val(obj['category']);
                    
                     
                     $.post( "./api.php", { action: "get_pos_sub_category", subcategory: obj['category'] } )
                        .done(function( data ) {
                            $('option', $("#subcategory")).remove();
                            var obj2 = jQuery.parseJSON(data);
                            //console.log(obj);
                            $.each( obj2, function( key, value ) {
                              $('#subcategory')
                             .append($("<option></option>")
                                        .attr("value",value['id'])
                                        .text(value['title'])); 
                                        
                            });
                            $('#subcategory').val(obj['subcategory']);
                        });
                     
                     
                     $('#vendorcode').val(obj['vendor_code']);
                     $('#provider').val(obj['provider']);
                     $('#price').val(obj['price']);
                     $('#quant_robot').val(obj['quant_robot']);
                     $('#quant_total').val(obj['total']);
                  });
               

    });



  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
  
  
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
               // window.location.href = "./pos.php?id="+category;  
              }
          });
    
 }
 
 function get_info_user(id) {
     
     
     
 }
  $("#btn_add_provider").click(function() {
 	var type = $('#provider_type').val();
 	var title = $('#provider_title').val();
 	//alert("123");
 	if (title != "") {
 		$.post("./api.php", {
 			action: "add_pos_provider",
 			type: type,
 			title: title
 		}).done(function(data) {
 			console.log(data);
 			if (data == "false") {
 				alert("Data Loaded: " + data);
 				return false;
 			} else {
 				$('#provider').append("<option value='" + data + "' selected>" + type + " " + title + "<\/option>");
 				$('#add_provider').modal('hide');
 				//return false;
 			}
 		});
 	}
 });
 
  $('#pos').DataTable({
       "iDisplayLength": 100,
        "order": [[ 7, "asc" ]]
    } );
</script>
</body>
</html>
