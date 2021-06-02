<?php 
include 'include/class.inc.php';

$allowed = $position->getAllowedAssembly($userdata["user_id"]);
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
       Сборки
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">


          <div class="box">
            <?php if ($allowed) { ?>
            <div class="box-header">
                <a href="./add_assembly.php" class="btn btn-primary" >Добавить сборку</a>
            </div>
            <?php } ?>

            <!-- /.box-header -->
            <div class="box-body">
             
                
              <table id="items" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Артикул</th>
                    <th>Название</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $position->get_assembly(false, true);

                if (isset($arr)) {
                    foreach ($arr as &$pos) {
                        $knopka = ($allowed) ? "<i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$pos['id_assembly']."'></i>" : "";
                        echo "
                            <tr>
                                <td>".$pos['id_assembly']."</td>
                                <td>".$pos['vendor_code']."</td>                        
                                <td>".$pos['title']."</td>
                                <td>".$knopka."</td>
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
<?php if ($allowed) { ?>
<script>

    $( "#items .fa-pencil" ).click(function() {
            id_element = $(this).attr("id");
            window.location.href = "./edit_assembly.php?id=" + id_element;    
    });

    $('#items').DataTable({
        "iDisplayLength": 50
    });
    

</script>
<?php } ?>
</body>
</html>
