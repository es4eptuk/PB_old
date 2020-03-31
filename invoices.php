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
                Счета

            </h1>

        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">


                    <div class="box">

                        <!-- /.box-header -->

                        <div class="box-header">
                            <a href="./add_writeoff.php" class="btn btn-primary" >Добавить счет</a>
                        </div>
                        <div class="box-body">


                            <table id="orders" class="table table-bordered table-striped">
                                <thead>
                                <tr>

                                    <th>Номер</th>
                                    <th>Описание</th>
                                    <th>Комментарий</th>
                                    <th>Желаемая дата оплаты</th>
                                    <th>Фактическая оплата</th>
                                    <th>НИОКР</th>
                                    <th>Категория</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (isset($_GET['robot'])) {$robot= $_GET['robot'];} else {$robot = 0;}
                                $arr = $invoice->get_invoices();

                                if (isset($arr)) {
                                    foreach ($arr as &$pos) {




                                        //$user_info = $user->get_info_user($pos['update_user']);
                                        $invoiceDesired = new DateTime($pos['invoiceDesired']);
                                        $invoicePaymentDate = new DateTime($pos['invoicePaymentDate']);

                                        echo "
                    <tr>
                        <td>".$pos['invoiceId']."</td>
                        <td>".$pos['invoiceTitle']."</td>
                        <td>".$pos['invoiceComment']."</td>
                        <td>".$invoiceDesired->format('d.m.Y')."</td>
                        <td>".$invoicePaymentDate->format('d.m.Y')."</td>
                        <td>".$pos['invoiceCategoryTemp']."</td>
                         <td>".$pos['invoiceCategory']."</td>
                       
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$pos['invoiceId']."'></i></td>
                    </tr>
                       
                       
                       ";

                                    }
                                }
                                ?>
                            </table>

                        </div>

                        <div class="box-footer">
                            <a href="./add_writeoff.php" class="btn btn-primary" >Добавить списание</a>
                            <a href="./add_writeoff_kit.php" class="btn btn-primary" >Списать комплект</a>
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
<div class="modal fade" id="order_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Заказ № <span id="order_id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form role="form" data-toggle="validator" id="add_pos">
                    <!-- text input -->
                    <!-- select -->
                    <div class="form-group">
                        <label>Категория</label>
                        <select class="form-control" name="category" placeholder="Веберите категорию" id="category" required="required">
                            <option>Веберите категорию...</option>
                            <?php
                            $arr = $position->get_pos_category();

                            foreach ($arr as &$category) {
                                echo "
                       <option value='".$category['id']."'>".$category['title']."</option>
                       
                       ";
                            }

                            ?>
                        </select>
                    </div>


                    <div class="form-group">
                        <label>Поставщик <small>(<a href="#" data-toggle="modal" data-target="#add_provider">Добавить</a>)</small></label>
                        <select class="form-control" name="provider" placeholder="Веберите категорию" id="provider" required="required">
                            <option>Веберите поставщика...</option>
                            <?php
                            $arr = $position->get_pos_provider();

                            foreach ($arr as &$provider) {
                                echo "
                       <option value='".$provider['id']."'>".$provider['type']." ".$provider['title']."</option>
                       
                       ";
                            }

                            ?>
                        </select>


                    </div>

                    <div class="form-group">
                        <label>Статус </label>
                        <select class="form-control" name="status" placeholder="Веберите статус" id="status" required="required">

                            <option value="0">Новый</option>
                            <option value="1">Отгружен</option>
                            <option value="2">Отменен</option>
                        </select>


                    </div>

                    <div class="form-group">
                        <label>Ответственный </label>
                        <select class="form-control" name="responsible" placeholder="Веберите пользователя" id="responsible" required="required">
                            <option>Веберите пользователя...</option>
                            <?php
                            $arr = $user->get_users($group = 0);

                            foreach ($arr as &$user) {
                                echo "
                       <option value='".$user['user_id']."'>".$user['user_name']."</option>
                       
                       ";
                            }

                            ?>
                        </select>


                    </div>



                    <div id="update"></div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                        <button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>
                    </div>
                </form>

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
    var id_order=0;


    $( "#orders .fa-pencil" ).click(function() {
        id_order = $(this).attr("id");
        window.location.href = "./edit_writeoff.php?id=" + id_order;

    });



    $( "#save_close" ).click(function() {
        save_close();
        return false;
    });

    $( "#delete" ).click(function() {
        delete_pos();
        return false;
    });

    function delete_pos() {
        var category =  $('#category').val();
        $.post( "./api.php", {
            action: "delete_pos",
            id: id_pos


        } )
            .done(function( data ) {
                if (data=="false") {alert( "Data Loaded: " + data ); }
                else {
                    window.location.href = "./pos.php?id="+category;
                }
            });

    }

    $( "#orders .fa-copy" ).click(function() {
        id_writeoff = $(this).attr("id");
        window.location.href = "./add_writeoff.php?copy=" + id_writeoff;

    });

    function save_close() {
        var title =  $('#title').val();
        var longtitle =  $('#longtitle').val();
        var category =  $('#category').val();
        var subcategory =  $('#subcategory').val();
        var vendorcode =  $('#vendorcode').val();
        var provider =  $('#provider').val();
        var price =  $('#price').val();
        var quant_robot =  $('#quant_robot').val();
        var quant_total =  $('#quant_total').val();


        $.post( "./api.php", {
            action: "edit_pos",
            id: id_pos,
            title: title,
            longtitle: longtitle ,
            category: category ,
            subcategory: subcategory ,
            vendorcode: vendorcode ,
            provider: provider ,
            price: price ,
            quant_robot: quant_robot ,
            quant_total: quant_total
        } )
            .done(function( data ) {
                if (data=="false") {alert( "Data Loaded: " + data ); }
                else {
                    window.location.href = "./pos.php?id="+category;
                }
            });

    }

    function get_info_user(id) {

        $.post( "./api.php", {
            action: "get_info_user",
            id: id
        } )
            .done(function( data ) {
                if (data=="false") {alert( "Data Loaded: " + data ); }
                else {
                    var obj = jQuery.parseJSON (data);
                    //console.log(obj['user_name']);
                    return obj['user_name'] ;
                }
            });

        $("#orders").on("click", "td .fa-copy", function() {

            return false;
        });



    }

    $('#check_show_all').change(function() {
        if($(this).is(":checked")) {
            //var returnVal = confirm("Are you sure?");
            $(this).attr("checked", true);
        }
        // alert($(this).is(':checked'));
        $("#show_all").submit();

    });

    $('#orders').DataTable({
        "iDisplayLength": 100,
        "order": [[ 0, "desc" ]]
    } );


</script>
</body>
</html>
