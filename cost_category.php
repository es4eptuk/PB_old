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
       Финансы
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Категории расходов</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
               
               
                
              <table id="checks" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th></th>
                  <th>Id</th>
                  <th>Наименование</th>
                </tr>
                </thead>
                <tbody>
                    
                    
                <?php 
                
                $arr = $oneC->get_cost_category();
                
                if (isset($arr)) {
                foreach ($arr as &$category) {

                        $id_category = $category['id'];
                       echo "
                            <tr id='".$id_category."'>
                                <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$id_category."'></i></td>
                                <td>".$id_category."</td>
                                <td><a href='./cost_subcategory.php?id=".$id_category."'>".$category['category_title']."</a></td>
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
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_add_category" type="button">Добавить</button>
				</div>
			</div>
		</div>
	</div>
<?php include "./template/scripts.html";?>
<!-- page script -->
<script>
$("#btn_add_category").click(function() {
 	var title = $('#category_title').val();
 	if (title != "") {
 		$.post("./api.php", {
 			action: "1c_add_cost_category",
 			title: title
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
