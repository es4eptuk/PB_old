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
       Финансы
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Подкатегории расходов</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="checks" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th></th>
                  <th>ID</th>
                  <th>Наименование</th>
                </tr>
                </thead>
                <tbody>
                    
                    
                <?php 
                
                $arr = $oneC->get_cost_subcategory($_GET['id']);
                
                if (isset($arr)) {
                foreach ($arr as &$subcategory) {
                        $id_subcategory= $subcategory['id'];
                       echo "
                    <tr id='".$id_subcategory."'>
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$id_subcategory."'></i></td>
                        <td>".$id_subcategory."</td>
                        <td>".$subcategory['subcategory_title']."</td>
                    </tr>
                       
                       
                       ";
                    }
                }
                
                ?>
              </table>
               
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#add_subcategory">Добавить позицию</button>
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

	<div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_subcategory" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавить подкатегорию</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					
						<!-- select -->
					
						<div class="form-group">
							<label>Название</label> <input class="form-control" id="subcategory_title" name="subcategory_title" required="required" type="text">
						</div>
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_add_subcategory" type="button">Добавить</button>
				</div>
			</div>
		</div>
	</div>
	
<?php include 'template/scripts.php';?>

<!-- page script -->
<script>

$("#btn_add_subcategory").click(function() {

    var category = <?php echo $_GET['id']?> ;
    
 	var title = $('#subcategory_title').val();
 	console.log(category);
 	
 	if (category==0) {return false;}
 
 	if (title != "") {
 		$.post("./api.php", {
 			action: "1c_add_cost_subcategory",
 			category: category,
 			title: title
 		}).done(function(data) {
 			
 			if (data == "false") {
 				alert("Data Loaded: " + data);
 				return false;
 			} else {
 				 window.location.reload(true);
 				//return false;
 			}
 		});
 	}
 });



</script>
</body>
</html>
