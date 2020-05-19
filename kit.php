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
       Комплекты
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
           
            <!-- /.box-header -->
            <div class="box-body">
              <div class="margin"> 
                    <div class="btn-group">
                      <button type="button" class="btn btn-default">Категория</button>
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                         <li><a href="?category=0">Все</a></li>
                        <li><a href="?category=2">Аппаратка</a></li>
                        <li><a href="?category=1">Механика</a></li>
                        <li><a href="?category=3">Корпус</a></li>
                        <li><a href="?category=4">Отладка/Упаковка</a></li>
                        <li><a href="?category=5">Настройка</a></li>
                      </ul>
                    </div>
             </div> 
                
              <table id="items" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Категория</th>
                  <th>Версия</th>
                  <th>Название</th>
                  <th>Привязка</th>
                  <th></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                
                if (!isset($_GET['category'])) {$category = 0;} else {$category = $_GET['category'];}
                
                $arr = $position->get_kit($category);
                
                if (isset($arr)) {
                    foreach ($arr as &$pos) {
                        if ($pos['delete'] == 0) {
                            $binding = "";
                            if ($pos['count'] != 0) {
                                $binding = "Есть";
                            } else {
                                $binding = "Нет";
                            }
                            echo "
                        <tr>
                            <td>".$pos['id_kit']."</td>
                            <td>".$pos['title']."</td>
                            <td>".$pos['version']."</td>
                            <td>".$pos['kit_title']."</td>
                            <td>".$binding."</td>
                            <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' data-id='".$pos['id_kit']."'></i></td>
                            <td><i class='fa fa-2x fa-copy' style='cursor: pointer;' data-id='".$pos['id_kit']."'></i></td>                        
                        </tr>
                        ";
                        }
                    }
                }
                ?>
              </table>
              
            </div>
            
              <div class="box-footer">
                    <a href="./add_kit.php" class="btn btn-primary" >Добавить комплект</a>
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
    $(document).ready(function () {

        $("#items").on('click', '.fa-pencil', function () {
                id_element = $(this).data("id");
                window.location.href = "./edit_kit.php?id=" + id_element;
        });
        $("#items").on('click', '.fa-copy', function () {
        id_element = $(this).data("id");
        window.location.href = "./split_kit.php?id=" + id_element;
        });

    });
</script>
</body>
</html>
