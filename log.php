<?php 
include 'include/class.inc.php';

//$robot_info = $robots->get_info_robot($_GET['id']);
//$robot_number = $robot_info['number'];
//$robot_name= $robot_info['name'];
//$robot_id= $robot_info['id'];
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
       История операций
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
        
        
        
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            
            <!-- /.box-header -->
            
            
            
            <div class="box-body table-responsive">
               
             
      
     
          
         
                
              <table id="orders" class="table table-bordered table-striped">
                <thead>
                <tr>
                  
                  <th>ID</th>
                  <th>Робот</th>
                  <th>Источник</th>
                  <th>Уровень</th>
                  <th>Статус</th>
                  <th>Пользователь</th>
                  <th>Дата</th>
                  <?php if ($userdata['group']==1) {echo '<th>Удалить</th>';} ?>
                 
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $robots->get_log(0);
                
                if (isset($arr)) {
                foreach ($arr as &$log) {
                    
                    $user_info = $user->get_info_user($log['update_user']);
                    $log_date = new DateTime($log['update_date']);
                    $level = $log['level'];
                    
                    switch ($level) {
                        case "INFO":
                            $color = "#f1f7c1";
                            break;
                        case "GOOD":
                            $color = "#c1f7cc";
                            break;
                        case "WARNING":
                            $color = "#f7c1e4";
                            break;
                        case "MODERN":
                            $color = "#dce0ff";
                            break;   
                        case "TICKET":
                            $color = "#90bec5";
                            break;       
                           
                            
                    }
                    
                     $out_del = "";
                    if ($userdata['group']==1) {$out_del = " <td><center><i class='fa fa-2x fa-times' style='cursor: pointer;' id='".$log['id']."'></i></center></td>";}
                    
                   $robot_info = $robots->get_info_robot($log['robot_id']);
                   $robot_number = $robot_info['number'];
                   $robot_version = $robot_info['version'];
                       echo "
                    <tr style='background: ".$color."'>
                     
                        
                        <td>".$log['id']."</td>
                        <td><a href='./robot.php?id=".$log['robot_id']."'>".$robot_version.".".$robot_number."</a></td>
                        <td>".$log['source']."</td>
                        <td>".$log['level']."</td>
                        <td>".$log['comment']."</td>
                       
                        <td>".$user_info['user_name']." </td>
                        <td>".$log_date->format('d.m.Y H:i:s')."</td>
                         ".$out_del."
                      
                       
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
 // $('.comment').validator();    
 
    
    
     $( ".fa-times" ).click(function() {
               
                id_log = $(this).attr("id");
                
               
                
                $.post( "./api.php", { 
                    action: "delete_log", 
                    id: id_log
                        } )
                  .done(function( data ) {
                      window.location.reload(true);
                     
                  });
               

    });
    
    
    $('#orders').DataTable({
       "iDisplayLength": 100,
        "order": [[ 0, "desc" ]]
    } );
    
    <?php 
    
    if ($userdata['user_id']==29) {
    echo "
    setTimeout(function() {window.location.reload();}, 20000);
    
    ";
    }
    ?>
</script>
</body>
</html>
