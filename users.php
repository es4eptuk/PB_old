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
      <h1>Пользователи</h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
                <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#add_user">Добавить пользователя</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="pos" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>ФИО</th>
                    <th>Email</th>
                    <th>Группа</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                    $arr = $user->get_users();
                    foreach ($arr as &$pos) {
                       echo "
                           <tr >
                              <td>".$pos['user_id']."</td>                           
                              <td>".$pos['user_name']."</td>
                              <td>".$pos['user_email']."</td>
                              <td>".$user->getGroups[$pos['group']]['title']."</td>                                                          
                              <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' data-id='".$pos['user_id']."'></i></td>
                              <td><i class='fa fa-2x fa-remove' style='cursor: pointer;' data-id='".$pos['user_id']."'></i></td>
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
                        <label>ФИО</label>
                        <input type="text" class="form-control" name="name" required="required" id="edit_name">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" id="edit_email">
                    </div>
                    <div class="form-group">
                        <label>Группа</label>
                        <select class="form-control" name="group" id="edit_group">
                            <?php
                            foreach ($user->getGroups as $group) {
                                echo "<option value='" . $group['id'] . "'>" . $group['title'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить покупателя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" data-toggle="validator" id="add_user_form">
                    <div class="form-group">
                        <label>Логин</label>
                        <input type="text" class="form-control" name="login" id="login">
                    </div>
                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="text" class="form-control" name="password" id="password">
                    </div>
                    <div class="form-group">
                        <label>ФИО</label>
                        <input type="text" class="form-control" name="name" required="required" id="name">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" id="email">
                    </div>
                    <div class="form-group">
                        <label>Группа</label>
                        <select class="form-control" name="group" id="group">
                            <?php
                            foreach ($user->getGroups as $group) {
                                echo "<option value='" . $group['id'] . "'>" . $group['title'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary" id="btn_add_user">Добавить</button>
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
            action: "get_info_user",
            id: id_pos
        }).done(function (data) {
            var obj = jQuery.parseJSON(data);
            $('#edit_name').val(obj['user_name']);
            $('#edit_email').val(obj['user_email']);
            $('#edit_group').val(obj['group']);
        });
    });

    $("#pos").on('click', '.fa-remove', function () {
        if (confirm("Ты хорошо подумал?")) {
            var id = $(this).data("id");
            $.post("./api.php", {
                action: "del_user",
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
        var email = $('#edit_email').val();
        var group = $('#edit_group').val();
        $.post("./api.php", {
            action: "edit_user",
            id: id_pos,
            name: name,
            email: email,
            group: group,
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
    }

    $("#btn_add_user").click(function () {
        var login = $('#login').val();
        var password = $('#password').val();
        var name = $('#name').val();
        var email = $('#email').val();
        var group = $('#group').val();
        if (login != "" && password != "") {
            $.post("./api.php", {
                action: "add_user",
                login: login,
                password: password,
                name: name,
                email: email,
                group: group
            }).done(function (data) {
                var obj = jQuery.parseJSON(data);
                if (obj['result'] == false) {
                    console.log(obj['err']);
                    alert(obj['err']);
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
