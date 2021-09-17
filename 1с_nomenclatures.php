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
      <h1>Номенклатура из 1С</h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <button type="button" class="btn btn-primary" id="btn_update">Обновить данные</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

              <table id="pos" class="table table-responsive">
                <thead>
                    <tr>
                      <th>DB_POS_ID</th>
                      <th>UUID</th>
                      <th>Код-1С</th>
                      <th>Артикул</th>
                      <th>Наименование</th>
                      <th>Папки</th>
                      <th>Удален</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $arr = $int1C->c_get_nomenclatures() ;
                    foreach ($arr as &$pos) {
                        $folder = $pos["second_parent"]." / ".$pos["first_parent"];
                        $deletion = ($pos["deletion"] == 1) ? "<i class='fa fa-check'></i>" : "";
                        echo "
                        <tr>
                            <td>".$pos['pos_id']."</td>
                            <td>".$pos['uuid']."</td>
                            <td>".$pos['code']."</td>
                            <td>".$pos['art']."</td>
                            <td>".$pos['title']."</td>
                            <td>".$folder."</td>
                            <td>".$deletion."</td>
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
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<?php include 'template/scripts.php'; ?>

<script>

    $("#btn_update").click(function () {
        $(this).prop('disabled', true);
        $.post("./api.php", {action: "c_download_from_1c_nomenclatures"}).done(function (data) {
            let obj = jQuery.parseJSON(data);
            if (obj['result'] == false) {
                console.log(obj['err']);
                alert(obj['err']);
                return false;
            } else {
                window.location.reload(true);
            }
        });
    });

    $('#pos').DataTable({
        "iDisplayLength": 100,
        "order": [[2, "asc"]]
    });

</script>


</body>
</html>
