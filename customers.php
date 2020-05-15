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
      <h1>Покупатели</h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
                <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#add_customer">Добавить покупателя</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="pos" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Наименование организации</th>
                    <th>ИНН</th>
                    <th>КЛ:ФИО</th>
                    <th>КЛ:Телефон</th>
                    <th>КЛ:Email</th>
                    <th>Адрес</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                    $arr = $robots->get_customers();
                    foreach ($arr as &$pos) {
                       echo "
                           <tr >
                              <td>".$pos['name']."</td>
                              <td>".$pos['inn']."</td>
                              <td>".$pos['fio']."</td>                                                          
                              <td>".$pos['phone']."</td>
                              <td>".$pos['email']."</td>
                              <td>".$pos['address']."</td>
                              <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' data-id='".$pos['id']."'></i></td>
                              <td><i class='fa fa-2x fa-remove' style='cursor: pointer;' data-id='".$pos['id']."'></i></td>
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
<!-- ./wrapper -->
<!-- Modal -->
<div class="modal fade" id="pos_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Редактирование покупателя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" data-toggle="validator" id="add_pos">
                    <!-- text input -->
                    <div class="form-group">
                        <label>Наименование организации</label>
                        <input type="text" class="form-control" name="name" required="required" id="edit_name">
                    </div>
                    <div class="form-group">
                        <label>ИНН</label>
                        <input type="text" class="form-control" name="inn" id="edit_inn">
                    </div>
                    <div class="form-group">
                        <label>Контактное лицо ФИО</label>
                        <textarea class="form-control" rows="1" placeholder="Enter ..." id="edit_fio"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <textarea class="form-control" rows="1" placeholder="+7 ..." id="edit_phone"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <textarea class="form-control" rows="1" placeholder="@ ..." id="edit_email"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Адрес</label>
                        <textarea class="form-control" rows="3" placeholder="Россия ..." id="edit_address"></textarea>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_customer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить покупателя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" data-toggle="validator" id="add_customer_form">
                    <div class="form-group">
                        <label>Наименование организации</label>
                        <input type="text" class="form-control" name="name" id="name" required="required">
                    </div>
                    <div class="form-group">
                        <label>ИНН</label>
                        <input type="text" class="form-control" name="inn" id="inn">
                    </div>
                    <div class="form-group">
                        <label>Контактное лицо ФИО</label>
                        <textarea class="form-control" rows="1" placeholder="Enter ..." id="fio"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <textarea class="form-control" rows="1" placeholder="+7 ..." id="phone"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <textarea class="form-control" rows="1" placeholder="@ ..." id="email"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Адрес</label>
                        <textarea class="form-control" rows="3" placeholder="Россия ..." id="address"></textarea>
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary" id="btn_add_customer">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

    var id_pos = 0;

    $("#pos").on('click', '.fa-pencil', function () {
        id_pos = $(this).data("id");
        $('#pos_edit').modal('show');
        $.post("./api.php", {
            action: "get_info_customer",
            id: id_pos
        }).done(function (data) {
            var obj = jQuery.parseJSON(data);
            $('#edit_name').val(obj['name']);
            $('#edit_inn').val(obj['inn']);
            $('#edit_fio').val(obj['fio']);
            $('#edit_email').val(obj['email']);
            $('#edit_phone').val(obj['phone']);
            $('#edit_address').val(obj['address']);
        });
    });

    $("#pos").on('click', '.fa-remove', function () {
        if (confirm("Ты хорошо подумал?")) {
            var id = $(this).data("id");
            $.post("./api.php", {
                action: "del_customer",
                id: id
            }).done(function (data) {
                window.location.reload(true);
                return false;
            });
        } else {
            alert("Это правильное решение!");
        }
    });

    $(function () {
        $('#example1').DataTable()
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': true,
            'autoWidth': false
        })
    })

    $("#save_close").click(function () {
        save_close();
        return false;
    });

    function save_close() {
        var name = $('#edit_name').val();
        var inn = $('#edit_inn').val();
        var fio = $('#edit_fio').val();
        var email = $('#edit_email').val();
        var phone = $('#edit_phone').val();
        var address = $('#edit_address').val();
        if (name != "") {
            $.post("./api.php", {
                action: "edit_customer",
                id: id_pos,
                name: name,
                fio: fio,
                phone: phone,
                email: email,
                address: address,
                inn: inn
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    window.location.href = "./customers.php";
                }
            });
        } else {
            return false;
        }
    }

    $("#btn_add_customer").click(function () {
        var name = $('#name').val();
        var inn = $('#inn').val();
        var fio = $('#fio').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var address = $('#address').val();
        if (name != "") {
            $.post("./api.php", {
                action: "add_full_customer",
                name: name,
                fio: fio,
                phone: phone,
                email: email,
                address: address,
                inn: inn
            }).done(function (data) {
                //console.log(data);
                if (data == "false") {
                    alert("Data Loaded: " + data);
                    return false;
                } else {
                    window.location.reload(true);
                }
            });
        } else {
            return false;
        }
    });

    $('#pos').DataTable({
        "iDisplayLength": 100,
        "order": [[0, "asc"]]
    });

</script>
</body>
</html>
