<?php 
include 'include/class.inc.php';

if (isset($_POST['check_show_all'])) {
    $check_show_all = 1;
} else {
    $check_show_all = 0;
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
      <h1>Произвольные списания</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="">
                    <form action="./writeoff_b.php" method="post">
                        <div class="form-group">
                            <?php
                            if (isset($_POST['check_show_all'])) {
                                $checked = 'checked';
                            } else {
                                $checked = '';
                            }
                            echo '
                            <div class="checkbox">
                                <label><input type="checkbox" id="check_show_all" name="check_show_all" '.$checked.'>Отображать проведеные списания</label>
                            </div>
                            ';
                            ?>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="add_filtr" name="">Применить</button>
                            <button type="reset" class="btn btn-default" id="del_filtr" name="" onclick="javascript:document.location = './writeoff_b.php'">Сбросить</button>
                        </div>
                    </form>
                </div>
                <br>
                <table id="writeoffs" class="table table-responsive">
                <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Списан</th>
                        <th>Дата составления</th>
                        <th>Категория</th>
                        <th>Описание</th>
                        <th>Сумма</th>
                        <th>Пользователь</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $show_all = ($check_show_all == 1) ? 0 : 1;
                $arr = $writeoff->get_writeoff(0, $show_all);
                if (isset($arr)) {
                    foreach ($arr as &$pos) {
                        $user_info = $user->get_info_user($pos['update_user']);
                        $create_date = new DateTime($pos['update_date']);
                        $written = $pos['written'] ? '<i class="fa fa-check"></i>' : '';
                        echo "
                            <tr>
                                <td>".$pos['id']."</td>
                                <td>".$written."</td>
                                <td>".$create_date->format('d.m.Y')."</td>
                                <td>".$pos['category']."</td>
                                <td>".$pos['description']."</td>
                                <td>".$pos['total_price']."</td>
                                <td>".$user_info['user_name']."</td>
                                <td><i class='fa fa-2x fa-eye' style='cursor: pointer;' data-id='".$pos['id']."'></i></td>
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

        $("#writeoffs").on('click', '.fa-eye', function () {
            id_writeoff = $(this).data("id");
            window.location.href = "./edit_writeoff_b.php?id=" + id_writeoff;
        });

    });
  
</script>
</body>
</html>
