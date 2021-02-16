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
      <h1>Формы обрабочика</h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
                <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#pos_add">Добавить форму</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="pos" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>id</th>
                    <th>key</th>
                    <th>url</th>
                    <th>name</th>
                    <th>handler</th>
                    <th>script</th>
                    <th>directionBy</th>
                    <th>countryBy</th>
                    <th>direction</th>
                    <th>country</th>
                    <th>status</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                    $arr = $bitrixForm->get_list_forms();
                    foreach ($arr as &$pos) {
                       echo "
                           <tr >
                              <td>".$pos['id']."</td>                           
                              <td>".$pos['key']."</td>
                              <td>".$pos['url']."</td>
                              <td>".$pos['name']."</td>
                              <td>".$bitrixForm->getListHandlers[$pos['handler']]."</td>                          
                              <td>".$bitrixForm->getListScripts[$pos['script']]."</td>
                              <td>".$bitrixForm->getListDirectionsBy[$pos['directionBy']]."</td>
                              <td>".$bitrixForm->getListCountryBy[$pos['countryBy']]."</td>
                              <td>".$bitrixForm->getListDirections[$pos['direction']]."</td>
                              <td>".$bitrixForm->getListCountry[$pos['country']]['name_ru']."</td>                                                              
                              <td>".$bitrixForm->getListStatuses[$pos['status']]."</td>                                                          
                              <td>
                                <i class='fa fa-2x fa-pencil' style='cursor: pointer;' data-id='".$pos['id']."'></i>
                              </td>
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
                <form role="form" data-toggle="validator" id="pos_edit">
                    <!-- text input -->
                    <div class="form-group">
                        <label>key</label>
                        <input type="text" class="form-control" name="key" required="required" id="edit_key">
                    </div>
                    <div class="form-group">
                        <label>url</label>
                        <input type="text" class="form-control" name="url" id="edit_url">
                    </div>
                    <div class="form-group">
                        <label>name</label>
                        <input type="text" class="form-control" name="name" id="edit_name">
                    </div>
                    <div class="form-group">
                        <label>handler</label>
                        <select class="form-control" name="handler" id="edit_handler">
                            <?php
                            foreach ($bitrixForm->getListHandlers as $id => $handler) {
                                echo "<option value='" . $id . "'>" . $handler . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>script</label>
                        <select class="form-control" name="script" id="edit_script">
                            <?php
                            foreach ($bitrixForm->getListScripts as $id => $script) {
                                echo "<option value='" . $id . "'>" . $script . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>directionBy</label>
                        <select class="form-control" name="directionBy" id="edit_directionBy">
                            <?php
                            foreach ($bitrixForm->getListDirectionsBy as $id => $directionBy) {
                                echo "<option value='" . $id . "'>" . $directionBy . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>countryBy</label>
                        <select class="form-control" name="countryBy" id="edit_countryBy">
                            <?php
                            foreach ($bitrixForm->getListCountryBy as $id => $countryBy) {
                                echo "<option value='" . $id . "'>" . $countryBy . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>direction</label>
                        <select class="form-control" name="direction" id="edit_direction">
                            <?php
                            foreach ($bitrixForm->getListDirections as $id => $direction) {
                                echo "<option value='" . $id . "'>" . $direction . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>country</label>
                        <select class="form-control" name="country" id="edit_country">
                            <?php
                            foreach ($bitrixForm->getListCountry as $id => $country) {
                                echo "<option value='" . $id . "'>" . $country['name_ru'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>status</label>
                        <select class="form-control" name="status" id="edit_status">
                            <?php
                            foreach ($bitrixForm->getListStatuses as $id => $status) {
                                echo "<option value='" . $id . "'>" . $status . "</option>";
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

<div class="modal fade" id="pos_add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить покупателя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" data-toggle="validator" id="pos_add">
                    <div class="form-group">
                        <label>key</label>
                        <input type="text" class="form-control" name="key" required="required" id="add_key">
                    </div>
                    <div class="form-group">
                        <label>url</label>
                        <input type="text" class="form-control" name="url" id="add_url">
                    </div>
                    <div class="form-group">
                        <label>name</label>
                        <input type="text" class="form-control" name="name" id="add_name">
                    </div>
                    <div class="form-group">
                        <label>handler</label>
                        <select class="form-control" name="handler" id="add_handler">
                            <?php
                            foreach ($bitrixForm->getListHandlers as $id => $handler) {
                                echo "<option value='" . $id . "'>" . $handler . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>script</label>
                        <select class="form-control" name="script" id="add_script">
                            <?php
                            foreach ($bitrixForm->getListScripts as $id => $script) {
                                echo "<option value='" . $id . "'>" . $script . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>directionBy</label>
                        <select class="form-control" name="directionBy" id="add_directionBy">
                            <?php
                            foreach ($bitrixForm->getListDirectionsBy as $id => $directionBy) {
                                echo "<option value='" . $id . "'>" . $directionBy . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>countryBy</label>
                        <select class="form-control" name="countryBy" id="add_countryBy">
                            <?php
                            foreach ($bitrixForm->getListCountryBy as $id => $countryBy) {
                                echo "<option value='" . $id . "'>" . $countryBy . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>direction</label>
                        <select class="form-control" name="direction" id="add_direction">
                            <?php
                            foreach ($bitrixForm->getListDirections as $id => $direction) {
                                echo "<option value='" . $id . "'>" . $direction . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>country</label>
                        <select class="form-control" name="country" id="add_country">
                            <?php
                            foreach ($bitrixForm->getListCountry as $id => $country) {
                                echo "<option value='" . $id . "'>" . $country['name_ru'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>status</label>
                        <select class="form-control" name="status" id="add_status">
                            <?php
                            foreach ($bitrixForm->getListStatuses as $id => $status) {
                                echo "<option value='" . $id . "'>" . $status . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary" id="btn_add_form">Добавить</button>
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
            action: "get_info_bitrix_form",
            id: id_pos
        }).done(function (data) {
            var obj = jQuery.parseJSON(data);
            $('#edit_key').val(obj['key']);
            $('#edit_url').val(obj['url']);
            $('#edit_name').val(obj['name']);
            $('#edit_handler').val(obj['handler']);
            $('#edit_script').val(obj['script']);
            $('#edit_status').val(obj['status']);
            $('#edit_directionBy').val(obj['directionBy']);
            $('#edit_countryBy').val(obj['countryBy']);
            $('#edit_direction').val(obj['direction']);
            $('#edit_country').val(obj['country']);
        });
    });

    $("#pos").on('click', '.fa-remove', function () {
        if (confirm("Ты хорошо подумал?")) {
            var id = $(this).data("id");
            $.post("./api.php", {
                action: "delete_bitrix_form",
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
        var key = $('#edit_key').val();
        var url = $('#edit_url').val();
        var name = $('#edit_name').val();
        var handler = $('#edit_handler').val();
        var script = $('#edit_script').val();
        var status = $('#edit_status').val();
        var directionBy = $('#edit_directionBy').val();
        var countryBy = $('#edit_countryBy').val();
        var direction = $('#edit_direction').val();
        var country = $('#edit_country').val();
        $.post("./api.php", {
            action: "update_bitrix_form",
            id: id_pos,
            key: key,
            url: url,
            name: name,
            handler: handler,
            script: script,
            status: status,
            directionBy: directionBy,
            countryBy: countryBy,
            direction: direction,
            country: country
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
    }

    $("#btn_add_form").click(function () {
        var key = $('#add_key').val();
        var url = $('#add_url').val();
        var name = $('#add_name').val();
        var handler = $('#add_handler').val();
        var script = $('#add_script').val();
        var status = $('#add_status').val();
        var directionBy = $('#add_directionBy').val();
        var countryBy = $('#add_countryBy').val();
        var direction = $('#add_direction').val();
        var country = $('#add_country').val();
        if (key != "" && url != "" && name != "") {
            $.post("./api.php", {
                action: "create_bitrix_form",
                key: key,
                url: url,
                name: name,
                handler: handler,
                script: script,
                status: status,
                directionBy: directionBy,
                countryBy: countryBy,
                direction: direction,
                country: country
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
        "order": [[2, "asc"]]
    });

</script>
</body>
</html>
