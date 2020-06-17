<?php 
include 'include/class.inc.php';

$listSubversion = $robots->getSubVersion;
$listVersion = $robots->getEquipment;
?>

<?php include 'template/head.php' ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
 <?php include 'template/header.php' ?>
  <?php include 'template/sidebar.php';?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Чек-лист</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $position->getCategoryes[$_GET['id']]['title'] ?></h3>
            </div>
            <div class="box-body">
               <div class="margin">
                    <div class="btn-group">
                      <button type="button" class="btn btn-default">Версия робота</button>
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                          <?php
                          $subversions = $robots->getSubVersion;
                          foreach ($subversions as &$version) {
                              echo '<li><a href="checks.php?id='.$_GET['id'].'&subversion='.$version["id"].'">'.$version["title"].'</a></li>';
                          }
                          ?>
                      </ul>
                    </div>
                    <div style="margin-left:20px;display:inline-block;font-weight:bold;"><?= (isset($_GET['subversion'])) ? $listSubversion[$_GET['subversion']]['title'] : "" ?></div>
             </div>
              <table id="checks" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Версия</th>
                  <th>Подверсия</th>
                  <th>Номер</th>
                  <th>Группа</th>
                  <th>Наименование</th>
                  <th>Комплект деталей</th>
                  <th></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $subversion = (isset($_GET['subversion'])) ? $_GET['subversion'] : 0;
                $arr = $checks->get_checks_in_cat($_GET['id'], $subversion);
                if (isset($arr)) {
                    foreach ($arr as &$check) {
                         $id_check = $check['id'];
                         $kit_out = "";
                         if ($check['kit']!=0) {
                             $kit_out = "<a href='edit_kit.php?id=".$check['kit']."'><i class='fa fa-2x fa-cubes'></i></a>";
                         }
                         echo "
                            <tr id='".$id_check."'>
                                <td>".$listVersion[$check['version']]['title']."</td>
                                <td>".$listSubversion[$check['subversion']]['title']."</td>
                                <td><span>".$check['sort']."</span></td>
                                <td>".$check['group']."</td>
                                <td>".$check['title']."</td>
                                <td align='center'>".$kit_out."</td>
                                <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$check['id']."' data-title='".$check['title']."' data-kit='".$check['kit']."'></i></td>
                                <td><i class='fa fa-2x fa-remove' style='cursor: pointer;'  data-id='".$check['id']."' data-version='".$check['version']."'></i></td>
                            </tr>
                         ";
                    }
                }
                ?>
              </table>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="btn_add_operation">Добавить операцию</button>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <div class="control-sidebar-bg"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="add_operation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавление операции<span id="operation_id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" data-toggle="validator" id="add_pos">
                    <div class="form-group">
                        <label>Подверсия робота</label>
                        <select class="form-control" name="group" placeholder="Выберите версию" id="version" required="required">
                            <option></option>
                            <?php
                            foreach ($subversions as &$version) {
                                $selected = "";
                                if (isset($_GET['subversion'])) {
                                    $selected = ($_GET['subversion'] == $version['id']) ? "selected" : "";
                                }
                                echo "<option value='" . $version['id'] . "' " . $selected . ">" . $version['title'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Наименование</label>
                        <input type="text" class="form-control" name="title" required="required" id="title">
                    </div>
                    <div class="form-group">
                        <label>Порядковый номер</label>
                        <input type="text" class="form-control" name="sort" id="sort">
                    </div>
                    <div class="form-group">
                        <label>Комплект деталей</label>
                        <select class="form-control" name="kit" id="kit" required="required">
                            <option value="0"></option>
                            <?php
                            $arr = $position->get_kit($_GET['id'], -1, -1, 1);
                            foreach ($arr as &$kit) {
                                echo "<option value='" . $kit['id_kit'] . "'>" . $kit['kit_title'] . " (" . $kit['title'] . ")</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div id="update"></div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                        <button type="button" class="btn btn-primary btn-danger pull-right" data-dismiss="modal">Закрыть</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="check_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Редактирование операции<span id="operation_id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Подверсия робота</label>
                    <select class="form-control" name="version" placeholder="Выберите версию" id="version_edit" required="required">
                        <?php
                        foreach ($subversions as &$version) {
                            echo "<option value='" . $version['id'] . "'>" . $version['title'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Наименование</label>
                    <input type="text" class="form-control" name="title" required="required" id="check_title">
                </div>
                <div class="form-group">
                    <label>Комплект деталей</label>
                    <select class="form-control" name="kit" id="kit_edit" required="required">
                        <option value="0"></option>
                        <?php
                        $arr = $position->get_kit($_GET['id'], -1, -1, 1);
                        foreach ($arr as &$kit) {
                            echo "<option value='" . $kit['id_kit'] . "'>" . $kit['kit_title'] . " (" . $kit['title'] . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">⚠️ Имеются связанные комплекты:</p>
                <div id="update"></div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="btn_edit" name="">Сохранить</button>
                    <button type="button" class="btn btn-primary btn-danger pull-right" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'template/scripts.php';?>

<!-- page script -->
<script>

    $('#checks').DataTable({
        "iDisplayLength": 100,
        "order": [[1, "desc"], [2, "asc"]]
    });

    var id_check = 0;
    var id_cat = <?= $_GET['id'] ?>;

    $(".fa-pencil").click(function () {
        id_check = $(this).attr("id");
        $('#check_edit').modal('show');
        $.post("./api.php", {
            action: "get_info_check",
            id: id_check
        }).done(function (data) {
            var obj = jQuery.parseJSON(data);
            console.log(obj);
            $('#check_title').val(obj['title']);
            $('#kit').val(obj['kit']);
            $('#version_edit').val(obj['subversion']);
            $('#kit_edit').val(obj['kit']);
            //console.log(obj['version']);
        });
    });

    $(".fa-remove").click(function () {
        var id = $(this).data("id");
        var version = $(this).data("version");
        $.post("./api.php", {
            action: "del_check",
            id: id,
            version: version
        }).done(function (data) {
            location.reload();
            //window.location.href = "./checks.php?id=" + id_cat + "&subversion=" + version;
            return false;
        });
    });

    $("#btn_add_operation").click(function () {
        $('#add_operation').modal('show');
        $.post("./api.php", {
            action: "get_checks_group",
            category: <?= $_GET['id'] ?>
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                var obj = jQuery.parseJSON(data);
                $.each(obj, function (key, value) {
                    $('#group')
                        .append($("<option></option>")
                            .attr("value", value['id'])
                            .text(value['title']));
                });
            }
        });
    });

    $("#save_close").click(function () {
        save_close();
        return false;
    });

    $("#btn_edit").click(function () {
        var id = id_check;
        var title = $('#check_title').val();
        var kit = $('#kit_edit').val();
        var version = $('#version_edit').val();
        $.post("./api.php", {
            action: "edit_check",
            id: id,
            title: title,
            kit: kit,
            version: version
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                location.reload();
                //window.location.href = "./checks.php?id=" + id_cat;
            }
        });
    });

    function save_close() {
        var category =  <?php echo $_GET['id']; ?> ;
        var group = $('#group').val();
        var title = $('#title').val();
        var sort = $('#sort').val();
        var version = $('#version').val();
        var kit = $('#kit').val();
        $.post("./api.php", {
            action: "add_check",
            category: category,
            group: group,
            title: title,
            sort: sort,
            version: version,
            kit: kit
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                location.reload();
                //window.location.href = "./checks.php?id=" + category + "&subversion=" + version;
            }
        });
    }

    $("#checks tbody").sortable({
        stop: function (event, ui) {
            var arr_robot = [];
            var id_ = 'checks tbody';
            var cols_ = document.querySelectorAll('#' + id_ + ' tr');
            $.each(cols_, function (key, value) {
                var idd = $(value).attr('id');
                arr_robot.push(idd);
            });
            JSON.stringify(arr_robot);
            $.post("./api.php", {
                action: "sortable_check",
                json: arr_robot
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    location.reload();
                    // window.location.href = "./robots.php";
                }
            });
            //console.log(arr_robot);
        }
    });

</script>
</body>
</html>
