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
      <h1>Дополнительный функционал</h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <button type="button" class="btn btn-primary" id="change_reserv_to_null">Обнулить резервы</button>
              <button type="button" class="btn btn-primary" id="add_new_reserv">Создать новые резервы</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
<!--
              <table id="pos" class="table table-responsive">
                <thead>
                    <tr>
                      <th>POS_ID</th>
                      <th>Арткул</th>
                      <th>Наименование</th>
                      <th>НА СКЛАДЕ</th>
                      <th>У ПОСТАВЩИКА</th>
                      <th>В DB</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                /*
                    $arr = $int1C->c_get_leftover(null, true) ;
                    foreach ($arr as &$pos) {
                        $bg = (($pos['storage'] + $pos['transfer']) != $pos['total']) ? "class='bg-yellow'" : "";
                        echo "
                        <tr ".$bg.">
                            <td>".$pos['pos_id']."</td>
                            <td>".$pos['art']."</td>
                            <td>".$pos['title']."</td>
                            <td>".$pos['storage']."</td>
                            <td>".$pos['transfer']."</td>
                            <td>".$pos['total']."</td>
                        </tr>
                        ";
                    }
                */
                ?>
                </tbody>
              </table>
-->
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


    $("#change_reserv_to_null").click(function () {
        $(this).prop('disabled', true);
        $.post("./api.php", {action: "change_reserv_to_null"}).done(function (data) {
            let obj = jQuery.parseJSON(data);
            if (obj['result'] == false) {
                alert(obj['err']);
                return false;
            } else {
                alert(obj['err']);
                window.location.reload(true);
            }
        });
    });

    $("#add_new_reserv").click(function () {
        $(this).prop('disabled', true);
        $.post("./api.php", {action: "add_new_reserv"}).done(function (data) {
            let obj = jQuery.parseJSON(data);
            if (obj['result'] == false) {
                alert(obj['err']);
                return false;
            } else {
                alert(obj['err']);
                window.location.reload(true);
            }
        });
    });

    $("#btn_transfer").click(function () {
        $(this).prop('disabled', true);
        $.post("./api.php", {action: "c_download_from_1c_transfer"}).done(function (data) {
            let obj = jQuery.parseJSON(data);
            if (obj['result'] == false) {
                alert(obj['err']);
                return false;
            } else {
                alert(obj['err']);
                window.location.reload(true);
            }
        });
    });

    $("#btn_invent").click(function () {
        $(this).prop('disabled', true);
        $.post("./api.php", {action: "c_invent_from_1c_leftovers"}).done(function (data) {
            let obj = jQuery.parseJSON(data);
            if (obj['result'] == false) {
                alert(obj['err']);
                return false;
            } else {
                alert(obj['err']);
                window.location.reload(true);
            }
        });
    });

    $('#pos').DataTable({
        "iDisplayLength": 100,
        "order": [[0, "asc"]]
    });

</script>


</body>
</html>
