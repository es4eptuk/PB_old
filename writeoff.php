<?php 
include 'include/class.inc.php';

function file_force_download($file) {
    if (file_exists($file)) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        unlink($file);
        exit;
    }
}

if (isset($_POST['print'])) {
    $file = $writeoff->createFileDeliveryNote($_POST['writeoff_id']);
    file_force_download($file);
}


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
      <h1>Списания</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
                <a href="./add_writeoff.php" class="btn btn-primary" >Добавить списание</a>
                <!--<a href="./add_writeoff_kit.php" class="btn btn-primary" >Списать комплект</a>-->
            </div>
            <div class="box-body">
                <table id="writeoffs" class="table table-responsive">
                <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Дата составления</th>
                        <th>Категория</th>
                        <th>Описание</th>
                        <th>Сумма</th>
                        <th>Пользователь</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (isset($_GET['robot'])) {$robot= $_GET['robot'];} else {$robot = 0;}
                $arr = $writeoff->get_writeoff($robot);
                if (isset($arr)) {
                    foreach ($arr as &$pos) {
                        $user_info = $user->get_info_user($pos['update_user']);
                        $create_date = new DateTime($pos['update_date']);
                        $tn = '';
                        if ($pos['category'] == "Возврат поставщику" || $pos['category'] == "Покраска/Покрытие" || $pos['category'] == "Давальческие материалы") {
                            $tn = "<i class='fa fa-2x fa-print' style='cursor: pointer;' data-id='".$pos['id']."'></i>";
                        }
                        echo "
                            <tr>
                                <td>".$pos['id']."</td>
                                <td>".$create_date->format('d.m.Y')."</td>
                                <td>".$pos['category']."</td>
                                <td>".$pos['description']."</td>
                                <td>".$pos['total_price']."</td>
                                <td>".$user_info['user_name']."</td>
                                <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' data-id='".$pos['id']."'></i></td>
                                <td><i class='fa fa-2x fa-copy' style='cursor: pointer;' data-id='".$pos['id']."'></i></td>
                                <td>".$tn."</td>
                            </tr>
                        ";
                    }
                }
                ?>
              </table>
            </div>
            <div class="box-footer">
                <a href="./add_writeoff.php" class="btn btn-primary" >Добавить списание</a>
                <!--<a href="./add_writeoff_kit.php" class="btn btn-primary" >Списать комплект</a>-->
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

  <div style="display: none;">
    <form action="" method="post" name="writeoff_print" id="writeoff_print">
        <input type="hidden" id="print" name="print" value="">
        <input type="hidden" id="writeoff_id" name="writeoff_id" value="">
    </form>
  </div>

</div>
<!-- ./wrapper -->
<!-- Modal -->
<div class="modal fade" id="" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Заказ № <span id="order_id"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          

      </div>
    </div>
  </div>
</div>
<?php include 'template/scripts.php'; ?>

<!-- page script -->
<script>

    $(document).ready(function () {

        var table = $('#writeoffs').DataTable({
            "iDisplayLength": 100,
            "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "All"]],
            "order": [[0, 'desc']],
            stateSave: true
        });

        $("#writeoffs").on('click', '.fa-copy', function () {
            id_writeoff = $(this).data("id");
            window.location.href = "./add_writeoff.php?copy=" + id_writeoff;
        });

        $("#writeoffs").on('click', '.fa-pencil', function () {
            id_writeoff = $(this).data("id");
            window.location.href = "./edit_writeoff.php?id=" + id_writeoff;
        });

        $("#writeoffs").on('click', '.fa-print', function () {
            var id_writeoff = $(this).data("id");
            $('input#writeoff_id').val(id_writeoff);
            document.getElementById('writeoff_print').submit()
            /*$.post("./api.php", {
                action: "print_order",
                id: id_order,
            }).done(function (data) {
                if (data == '') {
                    alert('Заказы не сформировались, т.к. заказывать нечего!');
                } else {
                    alert('Заказы успешно сформированны: ' + data + '.');
                }
                window.location.href = data;
            });*/
            return false;
        });

    });
  
</script>
</body>
</html>
