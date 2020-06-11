<?php
include 'include/class.inc.php';
?>

<?php include 'template/head.php' ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'template/header.php' ?>
    <?php include 'template/sidebar.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Список подверсий</h1>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <table id="subversions" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Версия</th>
                                    <th>Название</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $vesr = $robots->getEquipment;
                                $arr = $robots->get_subversion();
                                if (isset($arr)) {
                                    foreach ($arr as &$pos) {
                                        echo "
                                        <tr>
                                            <td>" . $pos['id'] . "</td>
                                            <td>" . $vesr[$pos['id_version']]['title'] . "</td>
                                            <td>" . $pos['title'] . "</td>
                                            <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' data-id='" . $pos['id'] . "'></i></td>
                                        </tr>
                                        ";
                                    }
                                }
                                ?>
                            </table>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#add_subversion">Добавить подверсию</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="control-sidebar-bg"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit_subversion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Редактирование покупателя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" data-toggle="validator" id="edit">
                    <div class="form-group">
                        <label>Название</label>
                        <input type="text" class="form-control" name="edit_title" id="edit_title" required="required">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_subversion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить покупателя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" data-toggle="validator" id="add">
                    <div class="form-group">
                        <label>Версия</label>
                        <select class="form-control" name="version" id="version" required="required">
                            <option value="0">Выбирите версию</option>
                            <?php
                            foreach ($robots->getEquipment as $eq) {
                                echo "<option value='" . $eq['id'] . "'>" . $eq['title'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Название</label>
                        <input type="text" class="form-control" name="title" id="title" required="required">
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-primary" id="btn_add_subversion">Добавить</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'template/scripts.php'; ?>
<!-- page script -->
<script>

    var id_pos = 0;

    $("#subversions").on('click', '.fa-pencil', function () {
        id_pos = $(this).data("id");
        $('#edit_subversion').modal('show');
        $.post("./api.php", {
            action: "get_info_subversion",
            id: id_pos
        }).done(function (data) {
            var obj = jQuery.parseJSON(data);
            $('#edit_title').val(obj['title']);
        });
    });

    $("#save_close").click(function () {
        save_close();
        return false;
    });

    function save_close() {
        var title = $('#edit_title').val();
        $.post("./api.php", {
            action: "edit_subversion",
            id: id_pos,
            title: title
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.reload(true);
            }
        });
    }

    $("#btn_add_subversion").click(function () {
        var version = $('#version').val();
        var title = $('#title').val();
        if (version != "0" && title != "") {
            $.post("./api.php", {
                action: "add_subversion",
                version: version,
                title: title
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    window.location.reload(true);
                }
            });
        } else {
            return false;
        }
    });

    $('#subversions').DataTable({
        "iDisplayLength": 100,
        "order": [[1, "desc"], [2, "asc"]]
    });

</script>
</body>
</html>
