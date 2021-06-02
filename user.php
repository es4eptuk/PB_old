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
       Пользователь
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
                  <th>Уровень</th>
                  <th>Статус</th>
                  
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
                           
                            
                    }
                    
                     $out_del = "";
                    if ($userdata['group']==1) {$out_del = " <td><center><i class='fa fa-2x fa-times' style='cursor: pointer;' id='".$log['id']."'></i></center></td>";}
                    
                   $robot_info = $robots->get_info_robot($log['robot_id']);
                   $robot_number = $robot_info['number'];
                       echo "
                    <tr style='background: ".$color."'>
                     
                        
                        <td>".$log['id']."</td>
                        <td><a href='./robot.php?id=".$log['robot_id']."'>".$robot_number."</a></td>
                        <td>".$log['level']."</td>
                        <td>".$log['comment']."</td>
                       
                        <td>".$user_info['user_name']." </td>
                        <td>".$log_date->format('d.m.Y H:m:s')."</td>
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
<?php include 'template/scripts.php'; ?>

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
    
</script>
</body>
</html>
