<?php
include 'include/class.inc.php';
?>
<?php include 'template/head.php' ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'template/header.php' ?>
    <!-- Left side column. contains the logo and sidebar -->
    <?php include 'template/sidebar.php';?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Опции робота</h1>
        </section><!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Добавить опцию</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <form data-toggle="validator" id="add" name="add" role="form" method="post">
                                <div class="form-group">
                                    <label>Версия робота</label>
                                    <select class="form-control" name="version" placeholder="Выберите категорию" id="version" required="required">
                                        <option value="0">Для любой версии</option>
                                        <?php
                                            $arr = $robots->getEquipment;
                                            foreach ($arr as $version) {
                                                echo "<option value='".$version['id']."'>".$version['title']."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Название</label>
                                    <input type="text" class="form-control" name="title" required="required" id="title" value="">
                                </div>
                                <div class="box-footer">
                                    <button class="btn btn-primary" id="save_close" type="submit">Сохранить</button>
                                </div>
                            </form>
                        </div><!-- /.box-body -->
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </section><!-- /.content -->
    </div><!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div><!-- ./wrapper -->
<!-- Modal -->
<?php include 'template/scripts.php'; ?>

<script>
    $(document).ready(function () {
        //активация функции записи при нажатии кнопки
        $("#save_close").click(function () {
            $(this).last().addClass("disabled");
            save_close();
            return false;
        });
        //функция отправки данных на запись
        function save_close() {
            $(this).prop('disabled', true);
            var version = $("#version").val();
            var title = $("#title").val();
            //console.log(title.length);
            //return false;
            if (title.length <= 2) {
                return false;
            }
            $.post("./api.php", {
                action: "add_option",
                version: version,
                title: title
            }).done(function (data) {
                console.log(data);
                window.location.href = "./options.php";
                return false;
            });
            return false;
        }
    });
</script>
</body>
</html>