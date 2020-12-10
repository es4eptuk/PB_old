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
      <h1>Лог обрабочика</h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
              <table id="pos" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>date</th>
                    <th>key</th>
                    <th>url</th>
                    <th>name</th>
                    <th>handler</th>
                    <th>script</th>
                    <th>params</th>
                    <th>result</th>

                </tr>
                </thead>
                <tbody>
                <?php 
                    $arr = $bitrixForm->get_list_log_forms();
                    foreach ($arr as &$pos) {
                       $params = json_decode($pos['params'], true);
                       $params = print_r($params, true);
                       $result = json_decode($pos['result'], true);
                       $result = print_r($result, true);
                       echo "
                           <tr>
                              <td>".$pos['date']."</td>
                              <td>".$pos['key']."</td>
                              <td>".$pos['url']."</td>
                              <td>".$pos['name']."</td>
                              <td>".$bitrixForm->getListHandlers[$pos['handler']]."</td>
                              <td>".$bitrixForm->getListScripts[$pos['script']]."</td>
                              <td>
                                <div style='max-width:400px'><pre>".$params."</pre></div>
                              </td>
                              <td>
                                <div style='max-width:200px'><pre>".$result."</pre></div>
                              </td>                                                        
                           </tr>
                       ";
                    }
                ?>
                </tbody>
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

    $('#pos').DataTable({
        "iDisplayLength": 100,
        "order": [[0, "desc"]]
    });

</script>
</body>
</html>
