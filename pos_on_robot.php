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
               Складские остатки
            </h1>

        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">


                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><?php echo $position->getCategoryes[$_GET['id']]['title'] ?></h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="pos" class="table table-responsive">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Артикул</th>
                                    <th>Наименование</th>
                                    <th>Количество на складе</th>
                                    <th>На робота</th>
                                    <th>Роботов</th>


                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $arr = $position->get_pos_in_kit_cat($_GET['id'],4,1);


                                foreach ($arr as &$pos) {
if ($pos['SUM(pos_kit_items.count)'] < 0 ) $pos['SUM(pos_kit_items.count)'] = 0;
                                    if ($pos['SUM(pos_kit_items.count)'] !=0) {$pos['posOnRobot'] = floor($pos['total'] / $pos['SUM(pos_kit_items.count)']); } else {$pos['posOnRobot'] = 0;}

                                }

                                foreach ($arr as &$pos) {
                                    echo "
                       <tr>
                          <td>" . $pos['id'] . "</td>
                          <td>" . $pos['vendor_code'] . "</td>
                          <td>" . $pos['title'] . "</td>         
                          <td>" . $pos['total'] . "</td>
                          <td>" . $pos['SUM(pos_kit_items.count)'] . "</td>
                          <td><b>" . $pos['posOnRobot'] . "</b></td>
                         
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
    var id_pos=0;


    $( "#category" )
        .change(function () {
            var id = "";

            $( "#category option:selected" ).each(function() {
                id = $( this ).val();
            });

            $.post( "./api.php", { action: "get_pos_sub_category", subcategory: id } )
                .done(function( data ) {
                    $('option', $("#subcategory")).remove();
                    var obj = jQuery.parseJSON(data);
                    //console.log(obj);
                    $.each( obj, function( key, value ) {
                        $('#subcategory')
                            .append($("<option></option>")
                                .attr("value",value['id'])
                                .text(value['title']));

                    });
                });
        });


    $( "#pos .fa-pencil" ).click(function() {

        id_pos = $(this).attr("id");

        $('#pos_edit').modal('show');

        $.post( "./api.php", {
            action: "get_info_pos",
            id: id_pos
        } )
            .done(function( data ) {
                var obj = jQuery.parseJSON (data);


                $.post( "./api.php", {
                    action: "get_info_user",
                    id: obj['update_user']
                } )
                    .done(function( data ) {
                        if (data=="false") {alert( "Data Loaded: " + data ); }
                        else {
                            var obj2 = jQuery.parseJSON (data);
                            //console.log(obj['user_name']);
                            $('#update').text("Изменено: " + obj['update_date'] + "  (" +obj2['user_name'] +")");

                        }
                    });



                // console.log (get_info_user(obj['update_user']));
                $('#title').val(obj['title']);
                $('#longtitle').val(obj['longtitle']);
                $('#category').val(obj['category']);


                $.post( "./api.php", { action: "get_pos_sub_category", subcategory: obj['category'] } )
                    .done(function( data ) {
                        $('option', $("#subcategory")).remove();
                        var obj2 = jQuery.parseJSON(data);
                        //console.log(obj);
                        $.each( obj2, function( key, value ) {
                            $('#subcategory')
                                .append($("<option></option>")
                                    .attr("value",value['id'])
                                    .text(value['title']));

                        });
                        $('#subcategory').val(obj['subcategory']);
                    });


                $('#vendorcode').val(obj['vendor_code']);
                $('#provider').val(obj['provider']);
                $('#price').val(obj['price']);
                $('#assembly').val(obj['assembly']);
                $('#quant_robot').val(obj['quant_robot']);
                $('#quant_total').val(obj['total']);
                $('#min_balance').val(obj['min_balance']);
                console.log(obj['summary']);
                if(obj['summary']=="1") {$('#summary').prop('checked', true);} else {$('#summary').prop('checked', false);}


            });


    });



    $(function () {
        $('#example1').DataTable()
        $('#example2').DataTable({
            'paging'      : true,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false
        })
    })


    $( "#save_close" ).click(function() {
        $( this ).hide();
        $('.overlay').show();
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

    function save_close() {

        var title =  $('#title').val();
        var longtitle =  $('#longtitle').val();
        var category =  $('#category').val();
        var subcategory =  $('#subcategory').val();

        var vendorcode =  $('#vendorcode').val();
        var provider =  $('#provider').val();
        var price =  $('#price').val();
        var quant_robot =  0;
        var quant_total =  $('#quant_total').val();
        var min_balance =  $('#min_balance').val();
        var assembly =  $('#assembly').val();
        var file =  $('#file').val();
        var summary =  0;
        if($("#summary").prop("checked")) {
            summary =  1;
        }

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
            quant_total: quant_total ,
            min_balance: min_balance ,
            assembly : assembly,
            summary : summary,
            file : file
        } )
            .done(function( data ) {
                if (data=="false") {alert( "Data Loaded: " + data ); }
                else {

                    var file_data = $('#file').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file', file_data);
                    form_data.append('category', category);
                    form_data.append('vendor', vendorcode);
                    //alert(form_data);
                    $.ajax({
                        url: 'upload.php',
                        dataType: 'text',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function(php_script_response){
                            //alert(php_script_response);
                            window.location.href = "./pos.php?id="+category;
                        }
                    });


                    //
                }
            });

    }

    function get_info_user(id) {



    }
    $("#btn_add_provider").click(function() {
        var type = $('#provider_type').val();
        var title = $('#provider_title').val();
        //alert("123");
        if (title != "") {
            $.post("./api.php", {
                action: "add_pos_provider",
                type: type,
                title: title
            }).done(function(data) {
                console.log(data);
                if (data == "false") {
                    alert("Data Loaded: " + data);
                    return false;
                } else {
                    $('#provider').append("<option value='" + data + "' selected>" + type + " " + title + "<\/option>");
                    $('#add_provider').modal('hide');
                    //return false;
                }
            });
        }
    });

    $('#pos').DataTable({
        "iDisplayLength": 500,
        "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "All"]],
        "order": [[ 5, "DESC" ]]
    } );
</script>


</body>
</html>
