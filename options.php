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
       Опции робота
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
           
            <!-- /.box-header -->
            <div class="box-body">
             
                
              <table id="items" class="table table-bordered table-striped">
                <thead>
                <tr>
                 
                  <th>ID</th>
                 
                  <th>Название</th>
                  
                  <th>Чек - лист</th>
                 
                   <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $robots->get_options();
                
                if (isset($arr)) {
                foreach ($arr as &$pos) {
                    
                    
                     
                    
                    
                    $check_out = "<a href='checks_option.php?id=".$pos['id_option']."'><i class='fa fa-2x fa-check-square-o'></i></a>";
                     

                       echo "
                    <tr>
                        <td>".$pos['id_option']."</td>
                        <td>".$pos['title']."</td>
                        
                        
                        <td align='center'>".$check_out."</td>
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$pos['id_option']."'></i></td>
                    </tr>
                       
                       
                       ";
                    
                }
                } 
                ?>
              </table>
              
            </div>
            
              <div class="box-footer">
                    <a href="./add_kit.php" class="btn btn-primary" >Добавить опцию</a>
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
<!-- jQuery 3 -->
<script src="./bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="./bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="./bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="./bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="./bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="./bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="./dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="./dist/js/demo.js"></script>
<!-- page script -->
<script>

    $( "#items .fa-pencil" ).click(function() {
            id_element = $(this).attr("id");
            window.location.href = "./edit_option.php?id=" + id_element;    
    });
    

</script>
</body>
</html>
