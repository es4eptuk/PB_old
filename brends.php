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
      <h1>
       Бренды
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
                <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#add_brend">Добавить бренд</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="pos" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Название</th>
                  <th>Статус</th>
                  <th></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                $arr = $position->get_list_brend();
                foreach ($arr as &$pos) {
                       echo "
                       <tr >
                          <td>".$pos['id']."</td>
                          <td>".$pos['name']."</td>                          
                          <td>".$position->getStatus[$pos['status']]."</td>
                          <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$pos['id']."'></i></td>
                          <td><i class='fa fa-2x fa-remove' style='cursor: pointer;' id='".$pos['id']."'></i></td>
                        </tr>                     
                       ";
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
<div class="modal fade" id="pos_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Редактирование бренда</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form role="form" data-toggle="validator" id="edit_pos">
            <div class="form-group">
              <label>Название</label>
              <input type="text" class="form-control" id="edit_name" name="name" required="required">
            </div>
            <div class="form-group">
              <label>Статус</label>
              <select class="form-control" id="edit_status" name="status" required="required">
                  <option>Выбирите статус</option>
                  <?php
                  $arr = $position->getStatus;
                  foreach ($arr as $id => $status) {
                      echo "<option value='".$id."' >".$status."</option>";
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

<div class="modal fade" id="add_brend" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Добавить бренд</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
           <form role="form" data-toggle="validator" id="add_brend_form">
                <div class="form-group">
                  <label>Название</label>
                  <input type="text" class="form-control" id="name" name="name" required="required">
                </div>
               <!-- select -->
               <div class="form-group">
                   <label>Статус</label>
                   <select class="form-control" id="status" name="status" required="required">
                       <option>Выбирите статус</option>
                       <?php
                       $arr = $position->getStatus;
                       foreach ($arr as $id => $status) {
                           echo "<option value='".$id."' >".$status."</option>";
                       }
                       ?>
                   </select>
               </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary" id="btn_add_brend">Добавить</button>
      </div>
    </div>
  </div>
</div>


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

    var id_pos = 0;

    $("#pos").on("click", ".fa-pencil", function () {
        id_pos = $(this).attr("id");
        $('#pos_edit').modal('show');
        $.post("./api.php", {
            action: "get_info_brend",
            id: id_pos
        }).done(function (data) {
            var obj = jQuery.parseJSON(data);
            $('#edit_name').val(obj['name']);
            $('#edit_status').val(obj['status']);
            console.log(data);
        });
    });

    $("#pos").on("click", ".fa-remove", function () {
        if (confirm("Ты хорошо подумал?")) {
            var id = $(this).attr("id");
            $.post("./api.php", {
                action: "del_brend",
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
        var name = $('#edit_name').val();
        var status = $('#edit_status').val();
        $.post("./api.php", {
            action: "edit_brend",
            id: id_pos,
            name: name,
            status: status
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.href = "./brends.php";
            }
        });
    }

    $("#btn_add_brend").click(function () {
        var name = $('#name').val();
        var status = $('#status').val();
        if (name != "") {
            $.post("./api.php", {
                action: "add_brend",
                name: name,
                status: status
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                    return false;
                } else {
                    window.location.reload(true);
                }
            });
        }
    });

    $('#pos').DataTable({
        "iDisplayLength": 100,
        "order": [[0, "asc"]]
    });

</script>
</body>
</html>
