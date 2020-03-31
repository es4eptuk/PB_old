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
       Ticket - система
        
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
                  <th>Статус</th>
                  <th>Проблема</th>
                  <th>Пользователь</th>
                  <th>Дата</th>
                  <th></th>
                  <?php if ($userdata['group']==1) {echo '<th>Удалить</th>';} ?>
                 
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $tickets->get_tickets();
                
                if (isset($arr)) {
                foreach ($arr as &$ticket) {
                    
                    $user_info = $user->get_info_user($ticket['update_user']);
                    $ticket_date = new DateTime($ticket['update_date']);
                    $status = $ticket['status'];
                    
                    
                    switch ($status) {
                        case 1:
                            $color = "#f1f7c1";
                            break;
                        case 2:
                            $color = "#c1f7cc";
                            break;
                        case 3:
                            $color = "#f7c1e4";
                            break;
                        case 4:
                            $color = "#dce0ff";
                            break;   
                        case 5:
                            $color = "#90bec5";
                            break;       
                           
                            
                    }
                    
                    $status_info = $tickets->get_info_status($status);
                    $status = $status_info['title'];
                    
                     $out_del = "";
                    if ($userdata['group']==1) {$out_del = " <td><center><i class='fa fa-2x fa-times' style='cursor: pointer;' id='".$ticket['id']."'></i></center></td>";}
                    
                   $robot_info = $robots->get_info_robot($ticket['robot']);
                   $robot_number = $robot_info['number'];
                   $robot_version = $robot_info['version'];
                   
                    $ticket_category = $ticket['category'];
                    $category_info = $tickets->get_info_category($ticket_category);
                    $ticket_category = $category_info['title'];
                    
                    $ticket_subcategory = $ticket['subcategory'];
                    $subcategory_info = $tickets->get_info_subcategory($ticket_subcategory);
                    $ticket_subcategory = $subcategory_info['title'];
                    $ticket_edit = "<i class='fa fa-2x fa-align-justify' style='cursor: pointer; ' id='".$ticket['id']."'></i>";
                    
                    
                    
                       echo "
                    <tr style='background: ".$color."'>
                     
                        
                        <td>".$ticket['id']."</td>
                        <td><a href='./robot.php?id=".$ticket['robot']."'>".$robot_version.".".$robot_number."</a></td>
                        <td>".$status."</td>
                        <td>".$ticket_category.": ".$ticket_subcategory."</td>
                       
                        <td>".$user_info['user_name']." </td>
                        <td>".$ticket_date->format('d.m.Y H:i:s')."</td>
                        
                        <td style='text-align: center;'>".$ticket_edit."</td>
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
 
   $( ".fa-align-justify" ).click(function() {
   var id = $(this).attr("id");
   
     window.location.href = "./ticket.php?id="+id;

    });
  
    
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
