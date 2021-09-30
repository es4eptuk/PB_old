<?php include 'include/class.inc.php';

?>
<?php

    if (isset($_POST['script']) && isset($_POST['bd'])) {
        $action = (!empty($_POST['script'])) ? $_POST['script'] : null;
        $bd_name = (!empty($_POST['bd'])) ? $_POST['bd'] : null;
    }

    if (!empty($action) && !empty($bd_name)) {

        ini_set('max_execution_time', '300000');
        set_time_limit(0);
        ini_set('memory_limit', '4096M');
        ignore_user_abort(true);

        //создаем подключение к БД
        global $database_server, $database_user, $database_password;
        $dsn = "mysql:host=$database_server;dbname=$bd_name;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        //result = 'Все гуд!';
        if ($action == 'edit_pos') {
            $result = edit_pos($dsn, $database_user, $database_password, $opt);
        }
        if ($action == 'add_pos') {
            $result = add_pos($dsn, $database_user, $database_password, $opt);
        }
    }
    //
    function edit_pos($dsn, $database_user, $database_password, $opt) {
        $pdo = new PDO($dsn, $database_user, $database_password, $opt);
        //
        $query = "SELECT * FROM `pos_items`";
        $result = $pdo->query($query);
        $old_items = [];
        while ($line = $result->fetch()) {
            $old_items[$line["id"]] = $line;
        }
        $query = "SELECT * FROM `1c_pos_items_prepare`";
        $result = $pdo->query($query);
        $edit_items = [];
        while ($line = $result->fetch()) {
            $edit_items[$line["id"]] = $line;
        }
        $errors = [];
        foreach ($edit_items as $id => $item) {
            if (array_key_exists($id, $old_items)) {
                if ($old_items[$id]['assembly'] != 0) {
                    $errors[] = "Это сборка: $id :: {$old_items[$id]['vendor_code']} :: {$old_items[$id]['title']}";
                }
            }
        }
        if ($errors != []) {
            $er = print_r($errors, true);
            return $er;
        }
        //
        $date = date("Y-m-d H:i:s");
        $i = 0;
        foreach ($edit_items as $id => $item) {
            if (array_key_exists($id, $old_items)) {
                //создать сборку
                $query = "INSERT INTO `pos_assembly` (`id_assembly`, `title`, `update_user`, `update_date`) VALUES (NULL, '{$item['new_code']}', 75, '$date')";
                $result = $pdo->query($query);
                $idd_as = $pdo->lastInsertId();
                //изменить позицию
                $query = "UPDATE `pos_items` SET `vendor_code` = '{$item['new_code']}', `title` = '{$item['new_title']}', `assembly` = '{$idd_as}' WHERE `id` = {$id}";
                $result = $pdo->query($query);
                //добавить новую позицию
                $query = "INSERT INTO `pos_items` (`id`, `category`, `unit`, `subcategory`, `title`, `vendor_code`, `provider`, `price`, `longtitle`, `quant_robot`, `total`, `update_date`, `update_user`, `development`, `p_vendor`, `p_vendor_code`) VALUES (NULL, '{$old_items[$id]['category']}', '{$old_items[$id]['unit']}', '{$old_items[$id]['subcategory']}', '{$old_items[$id]['title']}', '{$old_items[$id]['vendor_code']}', '{$old_items[$id]['provider']}', '{$old_items[$id]['price']}', '{$old_items[$id]['longtitle']}', '{$old_items[$id]['quant_robot']}', '{$old_items[$id]['total']}', '{$old_items[$id]['update_date']}', '{$old_items[$id]['update_user']}', '{$old_items[$id]['development']}', '{$old_items[$id]['p_vendor']}', '{$old_items[$id]['p_vendor_code']}')";
                $result = $pdo->query($query);
                $idd_pos = $pdo->lastInsertId();
                //скопировать файл
                $file = PATCH_DIR . "/img/catalog/1/" . $item['vendor_code'] . ".jpg";
                if (file_exists($file)) {
                    $new_file = PATCH_DIR . "/img/catalog/1/" . $item['new_code'] . ".jpg";
                    copy($file, $new_file);
                    $file = PATCH_DIR . "/img/catalog/1/thumb/" . $item['vendor_code'] . ".jpg";
                    $new_file = PATCH_DIR . "/img/catalog/1/thumb/" . $item['new_code'] . ".jpg";
                    copy($file, $new_file);
                }
                //вставить в сборку новую позицию
                $query = "INSERT INTO `pos_assembly_items` (`id_assembly`, `id_pos`, `count`) VALUES ( $idd_as, $idd_pos, 1);";
                $result = $pdo->query($query);
                $i++;
            }
        }
        return "OK! - ".$i;
    }

    //
    function add_pos($dsn, $database_user, $database_password, $opt) {
        $pdo = new PDO($dsn, $database_user, $database_password, $opt);
        //
        $query = "SELECT * FROM `pos_items`";
        $result = $pdo->query($query);
        $old_items = [];
        while ($line = $result->fetch()) {
            $old_items[$line["id"]] = $line;
        }
        $query = "SELECT * FROM `1c_pos_items_prepare_as`";
        $result = $pdo->query($query);
        $edit_items = [];
        while ($line = $result->fetch()) {
            $edit_items[$line["id"]] = $line;
        }
        $errors = [];
        foreach ($edit_items as $id => $item) {
            if (array_key_exists($id, $old_items)) {
                if ($old_items[$id]['assembly'] == 0) {
                    $errors[] = "ЭТО НЕ СБОРКА: $id :: {$old_items[$id]['vendor_code']} :: {$old_items[$id]['title']}";
                }
            }
        }
        if ($errors != []) {
            $er = print_r($errors, true);
            return $er;
        }
        //
        $date = date("Y-m-d H:i:s");
        $i = 0;
        foreach ($edit_items as $id => $item) {
            if (array_key_exists($id, $old_items)) {
                //создать сборку
                $query = "INSERT INTO `pos_assembly` (`id_assembly`, `title`, `update_user`, `update_date`) VALUES (NULL, '{$item['new_code']}', 75, '$date')";
                $result = $pdo->query($query);
                $idd_as = $pdo->lastInsertId();
                //добавить новую позицию
                $query   = "INSERT INTO `pos_items` (`id`, `category`, `unit`, `subcategory`, `title`, `vendor_code`, `provider`, `price`, `longtitle`, `quant_robot`, `total`, `update_date`, `update_user`, `development`, `p_vendor`, `p_vendor_code`, `assembly`) VALUES (NULL, '{$old_items[$id]['category']}', '{$old_items[$id]['unit']}', '{$old_items[$id]['subcategory']}', '{$item['new_title']}', '{$item['new_code']}', '{$old_items[$id]['provider']}', '{$old_items[$id]['price']}', '{$old_items[$id]['longtitle']}', '{$old_items[$id]['quant_robot']}', '{$old_items[$id]['total']}', '{$old_items[$id]['update_date']}', '{$old_items[$id]['update_user']}', '{$old_items[$id]['development']}', '{$old_items[$id]['p_vendor']}', '{$old_items[$id]['p_vendor_code']}', $idd_as)";
                $result = $pdo->query($query);
                $idd_pos = $pdo->lastInsertId();
                //скопировать файл
                $file = PATCH_DIR . "/img/catalog/1/".$item['vendor_code'].".jpg";
                if (file_exists($file)) {
                    $new_file = PATCH_DIR . "/img/catalog/1/".$item['new_code'].".jpg";
                    copy($file, $new_file);
                    $file = PATCH_DIR . "/img/catalog/1/thumb/".$item['vendor_code'].".jpg";
                    $new_file = PATCH_DIR . "/img/catalog/1/thumb/".$item['new_code'].".jpg";
                    copy($file, $new_file);
                }
                //вставить в сборку новую позицию
                $query  = "INSERT INTO `pos_assembly_items` (`id_assembly`, `id_pos`, `count`) VALUES ( $idd_as, $id, 1);";
                $result = $pdo->query($query);
                //заменить в комплетках
                $query = "UPDATE `pos_kit_items` SET `id_pos` = $idd_pos WHERE `id_pos` = $id";
                $result = $pdo->query($query);
                $i++;
            }
        }


        return "OK! - ".$i;
    }

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
            <h1>Списания</h1>
        </section><!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Запустить скрипт</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <?php if(isset($result)) {
                                print_r('<p class="p-label">Ответ:</p>');
                                print_r('<pre>');
                                print_r($result);
                                print_r('</pre><hr>');
                            }?>
                            <form action="" method="post">
                                <div class="form-group">
                                    <label>Скрипт:</label> <select class="form-control" id="script" name="script" required="required" >
                                        <option value="0">Выберите скрипт...</option>
                                        <option value="edit_pos">Запустить скрипт подмены (не сборки)</option>
                                        <option value="add_pos">Запустить скрипт подмены (сборки)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>База:</label> <select class="form-control" id="bd" name="bd" required="required" >
                                        <option value="0">Выберите базу...</option>
                                        <option value="promobot_test">Тестовая база</option>
                                        <option value="promobot_db2">Рабочая база</option>
                                    </select>
                                </div>
                                <div class="box-footer">
                                    <button class="btn btn-primary" id="save_close" type="submit">Запустить</button>
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

<?php include 'template/scripts.php'; ?>

<script>
</script>

</body>
</html>
