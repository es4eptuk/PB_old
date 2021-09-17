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
      <h1>Склады из 1С</h1>
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
                      <th>ID</th>
                      <th>UUID</th>
                      <th>Наименование</th>
                      <th>Использовать</th>
                      <th>Удален</th>
                      <th></th>
                      <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $arr = $int1C->c_get_warehouses() ;
                    foreach ($arr as &$pos) {
                        $on = ($pos["on"] == 1) ? "<i class='fa fa-check'></i>" : "";
                        $deletion = ($pos["deletion"] == 1) ? "<i class='fa fa-check'></i>" : "";
                        echo "
                        <tr>
                            <td>".$pos['id']."</td>
                            <td>".$pos['uuid']."</td>
                            <td>".$pos['title']."</td>
                            <td>".$on."</td>
                            <td>".$deletion."</td>
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
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- Modal -->
<div class="modal fade" id="pos_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Редактирование склада</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" data-toggle="validator" id="">
                    <!-- text input -->
                    <div class="form-group">
                        <label>UUID</label>
                        <input type="text" class="form-control" name="uuid" id="edit_uuid" disabled="disabled">
                    </div>
                    <div class="form-group">
                        <label>Название</label>
                        <input type="text" class="form-control" name="title" id="edit_title">
                    </div>
                    <div class="form-group">
                        <label>Использовать</label>
                        <select class="form-control" name="on" id="edit_on">
                            <?php
                                foreach ($int1C->getList as $id => $name) {
                                    echo "<option value='".$id."'>".$name."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Удалено</label>
                        <select class="form-control" name="deletion" id="edit_deletion" disabled="disabled">
                            <?php
                            foreach ($int1C->getList as $id => $name) {
                                echo "<option value='".$id."'>".$name."</option>";
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

<?php include 'template/scripts.php'; ?>

<script>

    var id_pos = 0;

    $("#pos").on('click', '.fa-pencil', function () {
        id_pos = $(this).data("id");
        $('#pos_edit').modal('show');
        $.post("./api.php", {
            action: "c_get_warehouses",
            id: id_pos
        }).done(function (data) {
            let obj = jQuery.parseJSON(data);
            $('#edit_uuid').val(obj[id_pos]['uuid']);
            $('#edit_title').val(obj[id_pos]['title']);
            $('#edit_on').val(obj[id_pos]['on']);
            $('#edit_deletion').val(obj[id_pos]['deletion']);
        });
    });

    $("#pos").on('click', '.fa-remove', function () {
        if (confirm("Ты хорошо подумал?")) {
            var id = $(this).data("id");
            $.post("./api.php", {
                action: "c_delete_warehouse",
                id: id
            }).done(function (data) {
                window.location.reload(true);
                return false;
            });
        } else {
            alert("Это правильное решение!");
        }
    });

    $("#save_close").click(function () {
        save_close();
        return false;
    });

    function save_close() {
        var uuid = $('#edit_uuid').val();
        var title = $('#edit_title').val();
        var on = $('#edit_on').val();
        var deletion = $('#edit_deletion').val();
        $.post("./api.php", {
            action: "c_update_warehouse",
            id: id_pos,
            uuid: uuid,
            title: title,
            on: on,
            deletion: deletion
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
    }

    $("#btn_update").click(function () {
        $(this).prop('disabled', true);
        $.post("./api.php", {action: "c_download_from_1c_warehouses"}).done(function (data) {
            let obj = jQuery.parseJSON(data);
            if (obj['result'] == false) {
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
