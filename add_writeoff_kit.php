<?php
include 'include/class.inc.php';
?>
<?php include 'template/head.html' ?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <? include 'template/header.html' ?>
    <!-- Left side column. contains the logo and sidebar -->
    <?php include 'template/sidebar.html';?>
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
                            <h3 class="box-title">Добавить списание</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <form data-toggle="validator" id="add_pos" name="add_pos" role="form">

                                <div class="form-group">
                                    <label>Категория</label> <select class="form-control" id="category" name="category" required="required" >
                                        <option value="0">
                                            Веберите категорию...
                                        </option>
                                        <option value="Модернизация">Модернизация</option>
                                        <option value="Брак">Брак</option>
                                        <option value="Сервис">Сервис</option>
                                        <option value="Разработка">Разработка</option>
                                        <option value="Продажа">Продажа</option>
                                        <option value="Маркетинг">Маркетинг</option>
                                        <option value="Содержание офиса">Содержание офиса</option>
                                        <option value="Давальческие материалы">Давальческие материалы</option>
                                        <option value="Возврат поставщику">Возврат поставщику</option>
                                        <option value="Покраска/Покрытие">Покраска/Покрытие</option>
                                        <option value="Сварка/Зенковка">Сварка/Зенковка</option>
                                        <option value="Не прокатило">Не прокатило</option>
                                    </select>
                                </div>

                                <div class="form-group" style="display: none;" id="prvd">
                                    <label>Контрагент <small>(<a data-target="#add_provider" data-toggle="modal" href="#">Добавить</a>)</small></label>
                                    <select class="form-control" id="provider" name="provider" required="required">
                                        <option value="0">
                                            Веберите контррагента...
                                        </option><?php
                                        $arr = $position->get_pos_provider();

                                        foreach ($arr as &$provider) {
                                            echo "
											                       <option value='".$provider['id']."'>".$provider['title'].", ".$provider['type']."</option>
											                       ";
                                        }

                                        ?>
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>Описание</label>
                                    <textarea class="form-control" rows="3" placeholder="Укажите уточнение к списанию ..." id="description"></textarea>
                                </div>

                                <div class="form-group input-group" id="pos">

                                    <input type="text" class="form-control" name="pos" id="search_pos" placeholder="Введите название позиции...">
                                    <span class="input-group-btn">
                                          <button type="button" class="btn btn-info btn-flat" id="add">+</button>
                                      </span>
                                </div>

                                <table class="table table-hover" id="listPos">
                                    <tbody><tr>
                                        <th>ID</th>
                                        <th>Артикул</th>
                                        <th>Наименование</th>
                                        <th>Количество</th>
                                        <th>Сумма</th>
                                        <th>Удаление</th>
                                    </tr>

                                    <?
                                    if (isset($_GET['copy'])) {
                                        $id = $_GET['copy'];

                                        $arr_pos = $writeoff->get_pos_in_writeoff($id);

                                        foreach ($arr_pos as &$value) {

                                            $title = $value['pos_title'];
                                            $vendor_code = $value['vendor_code'];

                                            echo '   
                                    <tr> 
                                    <td>'.$value['id'].'</td>
                                    <td>'.$value['pos_id'].'</td>
                                    <td>'.$vendor_code.'</td> 
                                    <td>'.$title.'</td> 
                                    <td class="quant"><span style="display: none;">'.$value['pos_count'].'</span><input type="text" class="form-control quant_inp"  style="position: relative; height: 20px; width: 55px; text-align: center;" placeholder="'.$value['pos_count'].'" value="'.$value['pos_count'].'"></td> 
                                    <td><i class="fa fa-2x fa-remove" style="cursor: pointer;" id="'.$value['pos_id'].'" data-target="'.$value['pos_count'].'"></i></td> 
                                    </tr>
                        ';
                                        }

                                    }
                                    ?>


                                    </tbody>
                                </table>

                                <div class="box-footer">
                                    <button class="btn btn-primary" id="save_close" type="submit">Сохранить и закрыть</button>

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

<?php include './template/scripts.html'; ?>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<!-- Select2 -->
<script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
<script>

    $(document).ready(function() {

        $('.select2').select2();

        var arr_str = [];
        var arr_ids = [];
        var arr_pos = [];
        var pos_info = [];
        var category_data = [];
        var category1 = "---";
        $("#save_close").click(function() {
            $(this).last().addClass( "disabled" );
            save_close();
            return false;
        });

        $("#save_new").click(function() {
            save_new();
            return false;
        });

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
                        $('#provider').append("<option value='" + data + "' selected>" + title + "<\/option>");
                        $('#add_provider').modal('hide');
                        //return false;
                    }
                });
            }
        });

        $("#search_pos").autocomplete({
            source: "./tt.php", // url-адрес
            minLength: 2 // минимальное количество для совершения запроса
        });

        function set_category(data) {
            category1 =  data;

            console.log(category1);
        }

        function set_subcategory(data) {
            subcategory1 =  data;
        }

        $("#add").click(function() {
            var str = $('#search_pos').val();
            arr_str = str.split('::');
            var id = arr_str[0];
            var vendor_code = arr_str[1];
            var title = arr_str[2];
            var subcategory="";
            $.post( "./api.php", {
                action: "get_info_pos",
                id: id
            } )
                .done(function( data1) {
                    pos_info = jQuery.parseJSON (data1);

                    $('#listPos tr:last').after('<tr> \
                        <td>'+pos_info['id']+'</td> \
                        <td>'+pos_info['vendor_code']+'</td> \
                        <td>'+pos_info['title']+'</td> \
                        <td class="quant"><span>1</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="1"></td> \
                        <td>'+pos_info['price']+'</td> \
                        <td><i class="fa fa-2x fa-remove" style="cursor: pointer;"></i></td> \
                        </tr>');
                    $('#search_pos').val("");

                });


            //arr_ids.push([arr_str[0], arr_str[1]]);

            return false;
        });

        $('#add_pos').validator();
        $('#add_provider_form').validator();



        $("#listPos").on("keyup", ".quant_inp, .date_inp", function() {
            var val = $( this ).val();
            $( this ).parent().find( "span" ).text(val);
        });

        $("#listPos").on("keyup", ".quant_inp", function() {
            var price = $( this ).parent().parent().find( ".price" ).text();
            var quant = $( this ).val();
            var sum = price * quant;

            $( this ).parent().parent().find( ".sum" ).text(sum);
        });


        $("#listPos").on("click", ".fa-remove", function() {

            $(this).parent().parent().fadeOut("normal", function() {
                $(this).remove();
            });
        });

        $( "#category" )
            .change(function () {
                var title = $(this).val();
                if (title == "Возврат поставщику" || title == "Покраска/Покрытие" ) {
                    $('#prvd').show();
                } else {
                    $('#prvd').hide();
                }

                console.log(title);
            });



        function save_close() {
            $(this).prop('disabled', true);
            var category =  $("#category").val();
            var provider =  $("#provider").val();
            var description =  $("#description").val();
            var TableArray = [];
            TableArray.push([$('#category').val(),$("#description").val(),0,0,$("#provider").val()]);

            $("#listPos tr").each(function() {
                var arrayOfThisRow = [];
                var tableData = $(this).find('td');
                if (tableData.length > 0) {
                    tableData.each(function() { arrayOfThisRow.push($(this).text()); });
                    TableArray.push(arrayOfThisRow);
                }
            });


            var JsonString = JSON.stringify(TableArray);
            console.log(JsonString);

            if (category!=0)  {
                $.post("./api.php", {
                    action: "add_writeoff",
                    json: JsonString
                }).done(function(data) {
                    console.log(data);
                    window.location.href = "./writeoff.php?id="+ category;
                });
            }

            return false;
        }



    });

</script>
</body>
</html>