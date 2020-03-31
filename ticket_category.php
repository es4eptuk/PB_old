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
       Ticket - система
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Категории обращений</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
               
               
                
              <table id="checks" class="table table-bordered table-striped">
                <thead>
                <tr>
                 
                  <th></th>
                  <th>Номер</th>
                   <th>Тип</th>
                  <th>Наименование</th>
                 
                  
                </tr>
                </thead>
                <tbody>
                    
                    
                <?php 
                
                $arr = $tickets->get_category();
                
                if (isset($arr)) {
                foreach ($arr as &$ticket) {
                    
                    
                        //$user_info = $user->get_info_user($pos['order_responsible']);
                        //$order_date = new DateTime($pos['order_date']);
                        //$order_delivery = new DateTime($pos['order_delivery']);
                       
                        $id_ticket = $ticket['id'];
                       echo "
                    <tr id='".$id_ticket."'>
                    
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$ticket['id']."'></i></td>
                       
                        <td>".$ticket['id']."</td>
                         <td>".$ticket['class']."</td>
                        <td><a href='./ticket_subcategory.php?id=".$ticket['id']."'>".$ticket['title']."</a></td>

                    </tr>
                       
                       
                       ";
                    }
                }
                
                ?>
              </table>
               
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#add_category">Добавить категорию</button>
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
</div>
<!-- ./wrapper -->

	<div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_category" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавить категорию</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
				
						<!-- select -->
						<div class="form-group">
							<label>Название</label> <input class="form-control" id="category_title" name="category_title" required="required" type="text">
						</div>
						
						<select class="form-control" id="category_class" required="required">
                                  <option value="">Веберите класс...</option>
                                  <option value="I">Консультация</option>
                                  <option value="P">Проблема</option>
                                  <option value="FR">Пожелание</option>
            			</select>
				
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_add_category" type="button">Добавить</button>
				</div>
			</div>
		</div>
	</div>
	


<?php include 'template/scripts.php';?>

<!-- page script -->
<script>




$("#btn_add_category").click(function() {
 
 	var title = $('#category_title').val();
    var cat_class = $('#category_class').val();
 	if (title != "") {
 		$.post("./api.php", {
 			action: "ticket_add_category",
 			title: title,
 			cat_class: cat_class
 		}).done(function(data) {
 			
 			if (data == "false") {
 				alert("Data Loaded: " + data);
 				return false;
 			} else {
 			
 			 window.location.reload(true);
 			}
 		});
 	}
 });
    
 
  
  
 
  
</script>
</body>
</html>
