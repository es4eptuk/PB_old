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
            <h1>Комплектации</h1>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <table id="versions" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $arr = $robots->getEquipment;
                                if (isset($arr)) {
                                    foreach ($arr as &$pos) {
                                        echo "
                                        <tr>
                                            <td>" . $pos['id'] . "</td>
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
                            <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#add_version">Добавить версию</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="control-sidebar-bg"></div>
</div>
<!-- Modal -->
<div class="modal fade" id="edit_version" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
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
<div class="modal fade" id="add_version" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
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
                        <label>Название</label>
                        <input type="text" class="form-control" name="title" id="title" required="required">
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-primary" id="btn_add_version">Добавить</button>
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

    $("#versions").on('click', '.fa-pencil', function () {
        id_pos = $(this).data("id");
        $('#edit_version').modal('show');
        $.post("./api.php", {
            action: "get_info_version",
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
            action: "edit_version",
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

    $("#btn_add_version").click(function () {
        var title = $('#title').val();
        if (title != "") {
            $.post("./api.php", {
                action: "add_version",
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

    $('#versions').DataTable({
        "iDisplayLength": 100,
        "order": [[1, "ASC"]]
    });

</script>
</body>
</html>
