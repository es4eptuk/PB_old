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
       Promobot DB
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

        
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

</body>
</html>
