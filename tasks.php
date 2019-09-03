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
      Планировщик
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Задачи</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
               
               
                
              <table id="checks" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Номер</th>
                  <th>Наименование</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                    
                    
                <?php 
                
                $arr = $task->get_tasks();
                
                if (isset($arr)) {
                foreach ($arr as &$tasks) {
                    
                    
                        //$user_info = $user->get_info_user($pos['order_responsible']);
                        //$order_date = new DateTime($pos['order_date']);
                        //$order_delivery = new DateTime($pos['order_delivery']);
                       
                        $id_task = $tasks['id'];
                       echo "
                    <tr id='".$id_task."'>
                        <td>".$tasks['id']."</td>
                        <td>".$tasks['task_title']."</td>
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$tasks['id']."'></i></td>
                    </tr>
                       
                       
                       ";
                    }
                }
                
                ?>
              </table>
               
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#add_task">Добавить задачу</button>
                    <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#add_event">Добавить событие</button>
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

	<div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_task" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавить задачу</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
				
						<!-- select -->
						<div class="form-group">
							<label>Название</label> <input class="form-control" id="task_title" name="task_title" required="required" type="text">
						</div>
						
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_add_task" type="button">Добавить</button>
				</div>
			</div>
		</div>
	</div>
	


<?php include "./template/scripts.html";?>

<!-- page script -->
<script>




$("#btn_add_task").click(function() {
    
    var param = {};
 	var title = $('#task_title').val();
     
     if(title!="") { param['title'] = title;}

    
 	if (title != "") {
 		$.post("./api.php", {
 			action: "add_task",
 			param: param
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
