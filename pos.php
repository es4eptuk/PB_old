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
       Справочник номенклатуры
        
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
              <form id="show_all">
                <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
                <div class="checkbox">
                    <label>
                        <?php   if ( isset($_GET['show_all']) == 'on') {
                            echo '<input type="checkbox" id="check_show_all" name="show_all" checked>';
                        } else {
                            echo '<input type="checkbox" id="check_show_all" name="show_all"  >';
                        }
                        ?>
                        Отображать архивные позиции
                    </label>
                </div>

              </form>
              <table id="pos" class="table table-responsive">
                <thead>
                <tr>
                  <th>Подгруппа</th>
                  <th>Артикул</th>
                  <th>Наименование</th>
                  <th>Количество на складе</th>
                  <th>Неснижаемый остаток</th>
                  <th>Поставщик</th>
                  <th>Стоимость</th>
                  <th>Сборка</th>
                  <th>Изображение</th>
                  <th>Ред.</th>
                  <th>Коп.</th>
                  <th>Лог</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($_GET['show_all'])) {
                    $archive = 1;
                } else {
                    $archive = 0;
                }
                $arr = $position->get_pos_in_category($_GET['id'], 0, 0, $archive);
               
                
                foreach ($arr as &$pos) {
                     $provider = $position->get_info_pos_provider($pos['provider']);
                     //$real = $pos['total']-$pos['reserv'];

                    //$order_date = $orders->orderDate($pos['id']);
                     $assembly_out = "";
                     if ($pos['assembly']!=0) {
                         $assembly_out = "<a href='edit_assembly.php?id=".$pos['assembly']."'><i class='fa fa-2x fa-codepen'></i></a>";
                     }
                     
                     $filename = 'img/catalog/'.$_GET['id'].'/'.$pos['vendor_code'].".jpg";
                     $filename_thumb = 'img/catalog/'.$_GET['id'].'/thumb/'.$pos['vendor_code'].".jpg";
//echo $filename_thumb;
                        if (file_exists($filename_thumb)) {
                            $img =  '<a class="fancybox" href="'.$filename.'" target="_blank"><img alt="'.$pos['vendor_code'].'" src="'.$filename_thumb.'" /></a>';
                        } else {
                            $img = "<img src='/img/no-image.png' width='100'></img>";
                        }
                     
                       echo "
                       <tr>
                          <td>".$position->getSubcategoryes[$pos['subcategory']]['title']."</td>
                          <td>".$pos['vendor_code']."</td>
                          <td>".$pos['title']."</td>
                          <td>".$pos['total']."</td>
                          <td>".$pos['min_balance']."</td>
                          <td>".$provider['title'].", ".$provider['type']."</td>
                          <td>".$pos['price']."</td>
                          <td>".$assembly_out."</td>
                          <td>".$img."</td>
                          <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' data-id='".$pos['id']."'></i></td>
                          <td><a href='add_pos.php?title=".$pos['title']."&longtitle=".$pos['longtitle']."&category=".$_GET['id']."&subcategory=".$pos['subcategory']."&provider=".$pos['provider']."&price=".$pos['price']."'><i class='fa fa-2x fa-copy' style='cursor: pointer;' id='".$pos['id']."'></i></a></td>
                          <td><a href='pos_log.php?id=".$pos['id']."'><i class='fa fa-2x fa-list-alt'></i></a></td>
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
        <h5 class="modal-title" id="exampleModalLabel">Редактирование позиции</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
          <form role="form" data-toggle="validator" id="add_pos">
                <!-- text input -->
                <div class="form-group">
                  <label>Наименование</label>
                  <input type="text" class="form-control" name="title" required="required" id="title">
                </div>
                <div class="form-group">
                  <label>Описание</label>
                  <input type="text" class="form-control" name="longtitle" id="longtitle">
                </div>

                <!-- select -->
                <div class="form-group">
                  <label>Единицы измерения</label>
                  <select class="form-control" name="unit" placeholder="" id="unit" required="required">
                      <?php
                      $arr = $position->getUnits;
                      foreach ($arr as &$unit) {
                          echo "<option value='".$unit['id']."' >".$unit['title']."</option>";
                      }
                      ?>
                  </select>
                </div>

                <!-- select -->
                <div class="form-group">
                  <label>Категория</label>
                  <select class="form-control" name="category" placeholder="Выберите категорию" id="category" required="required">
                   <option>Выберите категорию...</option>
                   <?php 
                   $arr = $position->getCategoryes;
                   foreach ($arr as &$category) {
                       echo "<option value='".$category['id']."'>".$category['title']."</option>";
                   }
                   ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Подкатегория</label>
                  <select class="form-control" name="subcategory" id="subcategory" required="required">
                    
                  </select>
                </div>
                
              
                <div class="form-group">
                  <label>Артикул</label>
                  <input type="text" class="form-control" name="vendorcode" required="required" id="vendorcode">
                </div>
                
                 <div class="form-group">
                  <label>Поставщик <small>(<a href="#" data-toggle="modal" data-target="#add_provider">Добавить</a>)</small></label>
                  <select class="form-control" name="provider" placeholder="Выберите категорию" id="provider" required="required">
                   <option>Выберите поставщика...</option>
                   <?php 
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
                  <label>Стоимость</label>
                  <input type="text" class="form-control" name="price" placeholder="0.00" id="price">
                </div>
                
                 <div class="form-group">
                  <label>Сборка</label>

                  <select class="form-control" name="assembly"  id="assembly" required="required">
                   <option value="0"></option>
                   <?php 
                   $arr = $position->get_assembly(false, true);
                
                    foreach ($arr as &$provider) {
                        
                        
                       echo "
                       <option value='".$provider['id_assembly']."'>".$provider['title']."</option>
                       ";
                    }
                   
                   ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Неснижаемый остаток</label>
                  <input type="text" class="form-control" name="min_balance" placeholder="0" id="min_balance">
                </div>
                
                <div class="form-group">
                  <label>Количество на складе</label>
                  <input type="text" class="form-control" name="quant_total" placeholder="0" id="quant_total">
                </div>
                
                <div class="form-group">
                  <div class="checkbox">
                    <label><input type="checkbox" id="summary" >Суммировать при заказе</label>
                  </div>
                </div>

                <div class="form-group">
                  <div class="checkbox">
                      <label><input type="checkbox" id="archive" >Архивная позиция </label>
                  </div>
                </div>
                <div class="form-group">
                  <label for="file">Изображение</label>
                  <input type="file" id="file">
                  <p class="help-block"></p>
                </div>

                <div class="form-group">
                  <div class="">
                      <p class="p-label" id="in-kit">Состоит в комплектах: </p>

                  </div>
                </div>

                <div class="form-group">
                  <div class="">
                      <p class="p-label" id="in-assembly">Состоит в сборках: </p>
                  </div>
                </div>
                
                <div id="update"></div>
                
                <div class="box-footer" >
                    <div class="overlay" style="display:none"><img src="img/25.gif" width="30"></div>
                    <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                    <button type="button" class="btn btn-primary btn-warning" id="warehouse" name="">Переместить</button>
                    <button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>
                </div>
              </form>
         
      </div>
      
    </div>
  </div>
</div>

<!-- Modal -->
	<div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_provider" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавить поставщика</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<form data-toggle="validator" id="add_provider_form" name="add_provider_form" role="form">
						<!-- select -->
						<div class="form-group">
							<label>Форма собственности</label> <select class="form-control" id="provider_type" name="provider_type" required="required">
								<option value="ИП">
									ИП
								</option>
								<option value="ООО">
									ООО
								</option>
								<option value="ОАО">
									ОАО
								</option>
								<option value="ЗАО">
									ЗАО
								</option>
								<option value="ЗАО">
									Ltd.
								</option>
							</select>
						</div>
						<div class="form-group">
							<label>Наименование</label> <input class="form-control" id="provider_title" name="provider_title" required="required" type="text">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_add_provider" type="button">Добавить</button>
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

    $(document).ready(function () {
        /* Для таблицы */

        // Setup - add a text input to each footer cell
        /*
        $('#example tfoot th').each(function () {
            var title = $('#example thead th').eq($(this).index()).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });
        */
        // DataTable
        /*var table = $('#example').DataTable({
            stateSave: true
        });*/
        //инициализация таблиц
        var table = $('#pos').DataTable({
            "iDisplayLength": 100,
            "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "All"]],
            "order": [[2, 'asc']],
            stateSave: true
        });
        /*
        // Restore state
        var state = table.state.loaded();
        if (state) {
            table.columns().eq(0).each(function (colIdx) {
                var colSearch = state.columns[colIdx].search;

                if (colSearch.search) {
                    $('input', table.column(colIdx).footer()).val(colSearch.search);
                }
            });

            table.draw();
        }
        // Apply the search
        table.columns().eq(0).each(function (colIdx) {
            $('input', table.column(colIdx).footer()).on('keyup change', function () {
                table
                    .column(colIdx)
                    .search(this.value)
                    .draw();
            });
        });
        */

        var id_pos = 0;
        var pos_in_assembly = null;
        var pos_in_kits = null;
        var pos_is_assembly = null;
        //при смене категории загружаем подкатегории
        $("#category").change(function () {
            var id = "";
            $("#category option:selected").each(function () {
                id = $(this).val();
            });
            $.post("./api.php", {action: "get_pos_sub_category", subcategory: id})
                .done(function (data) {
                    $('option', $("#subcategory")).remove();
                    var obj = jQuery.parseJSON(data);
                    //console.log(obj);
                    $.each(obj, function (key, value) {
                        $('#subcategory')
                            .append($("<option></option>")
                                .attr("value", value['id'])
                                .text(value['title']));
                    });
                });
        });
        //при нажатии на кнопку редактирования позиции
        $("#pos").on('click', '.fa-pencil', function () {
            id_pos = $(this).data("id");
            console.log (id_pos);
            $('#pos_edit').modal('show');
            //собираем форму для редактирования позиции
            $.post("./api.php", {action: "get_info_pos", id: id_pos})
                .done(function (data) {
                    var obj = jQuery.parseJSON(data);
                    //информация о пользователе
                    $.post("./api.php", {action: "get_info_user", id: obj['update_user']})
                        .done(function (data) {
                            if (data == "false") {
                                alert("Data Loaded: " + data);
                            } else {
                                var obj2 = jQuery.parseJSON(data);
                                //console.log(obj['user_name']);
                                $('#update').text("Изменено: " + obj['update_date'] + "  (" + obj2['user_name'] + ")");
                            }
                        });
                    // console.log (get_info_user(obj['update_user']));
                    $('#title').val(obj['title']);
                    $('#longtitle').val(obj['longtitle']);
                    $('#unit').val(obj['unit']);
                    $('#category').val(obj['category']);
                    //загружаем подкатегории по выбранной категории
                    $.post("./api.php", {action: "get_pos_sub_category", subcategory: obj['category']})
                        .done(function (data) {
                            $('option', $("#subcategory")).remove();
                            var obj2 = jQuery.parseJSON(data);
                            //console.log(obj);
                            $.each(obj2, function (key, value) {
                                $('#subcategory')
                                    .append($("<option></option>")
                                        .attr("value", value['id'])
                                        .text(value['title']));

                            });
                            $('#subcategory').val(obj['subcategory']);
                        });
                    //находим все комплекты где состоит деталь и вставляем в поле
                    $.post("./api.php", {action: "get_kit_by_pos", id: id_pos})
                        .done(function (data) {
                            $('a', $("#in-kit")).remove();
                            var objKits = jQuery.parseJSON(data);
                            pos_in_kits = objKits;
                            if (objKits != null) {
                                $.each(objKits, function (key, value) {
                                    $('#in-kit').append($("<a></a>").attr("href", './edit_kit.php?id=' + value).text(' ' + value));
                                });
                            } else {
                                $('#in-kit').append($("<a></a>").attr("style", "font-weight: 700;").text('нет'));
                            }
                        });
                    //находим все сборки где состоит деталь и вставляем в поле
                    $.post("./api.php", {action: "get_assembly_by_pos", id: id_pos})
                        .done(function (data) {
                            $('a', $("#in-assembly")).remove();
                            var objAssembly = jQuery.parseJSON(data);
                            pos_in_assembly = objAssembly;
                            if (objAssembly != null) {
                                $.each(objAssembly, function (key, value) {
                                    $('#in-assembly').append($("<a></a>").attr("href", './edit_assembly.php?id=' + value).text(' ' + value));
                                });
                            } else {
                                $('#in-assembly').append($("<a></a>").attr("style", "font-weight: 700;").text('нет'));
                            }
                        });
                    //присваеваем глобальную переменную если позиция является сборкой
                    if (obj['assembly'] != 0) {
                        pos_is_assembly = obj['assembly']
                    }
                    //заменяем поля в форме редактирования позиции
                    $('#vendorcode').val(obj['vendor_code']);
                    $('#provider').val(obj['provider']);
                    $('#price').val(obj['price']);
                    $('#assembly').val(obj['assembly']);
                    $('#quant_robot').val(obj['quant_robot']);
                    $('#quant_total').val(obj['total']);
                    $('#min_balance').val(obj['min_balance']);
                    //console.log(obj['summary']);
                    if (obj['summary'] == "1") {
                        $('#summary').prop('checked', true);
                    } else {
                        $('#summary').prop('checked', false);
                    }
                    if (obj['archive'] == "1") {
                        $('#archive').prop('checked', true);
                    } else {
                        $('#archive').prop('checked', false);
                    }
                });
        });
        //нет такого???
        /*$(function () {
            $('#example1').DataTable()
            $('#example2').DataTable({
                'paging': true,
                'lengthChange': false,
                'searching': false,
                'ordering': true,
                'info': true,
                'autoWidth': false
            })
        })*/
        //при нажатии на кнопку сохранить
        $("#save_close").click(function () {
            $(this).hide();
            $('.overlay').show();
            save_close();
            return false;
        });
        //при нажатии на кнопку "удалить"
        $("#delete").click(function () {
            delete_pos();
            return false;
        });
        //функция удаления позиции
        function delete_pos() {
            var category = $('#category').val();
            $.post("./api.php", {
                action: "delete_pos",
                id: id_pos
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    //window.location.href = "./pos.php?id=" + category;
                    location.reload();
                }
            });
        }
        //при нажатии на кнопку "переместить"
        $("#warehouse").click(function () {
            if (pos_in_assembly == null && pos_in_kits == null && pos_is_assembly == null) {
                to_warehouse();
            } else {
                alert('Позиция, которая состоит в комплектах и сборках, а так же является сборкой переместить нельзя!');
            }
            return false;
        });
        //функция перемещения позиции
        function to_warehouse() {
            var category = $('#category').val();
            $.post("./api.php", {
                action: "to_warehouse",
                id: id_pos
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    //window.location.href = "./pos.php?id=" + category;
                    location.reload();
                }
            });
        }
        //функция записи изменений позиции
        function save_close() {
            var title = $('#title').val();
            var longtitle = $('#longtitle').val();
            var category = $('#category').val();
            var unit = $('#unit').val();
            var subcategory = $('#subcategory').val();
            var vendorcode = $('#vendorcode').val();
            var provider = $('#provider').val();
            var price = $('#price').val();
            var quant_robot = 0;
            var quant_total = $('#quant_total').val();
            var min_balance = $('#min_balance').val();
            var assembly = $('#assembly').val();
            var file = $('#file').val();
            var summary = 0;
            if ($("#summary").prop("checked")) {
                summary = 1;
            }
            var archive = 0;
            if ($("#archive").prop("checked")) {
                archive = 1;
            }
            $.post("./api.php", {
                action: "edit_pos",
                id: id_pos,
                title: title,
                longtitle: longtitle,
                category: category,
                unit: unit,
                subcategory: subcategory,
                vendorcode: vendorcode,
                provider: provider,
                price: price,
                quant_robot: quant_robot,
                quant_total: quant_total,
                min_balance: min_balance,
                assembly: assembly,
                summary: summary,
                archive: archive,
                file: file
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
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
                        success: function (php_script_response) {
                            //alert(php_script_response);
                            //window.location.href = "./pos.php?id=" + category;
                            location.reload();
                        }
                    });
                }
            });
        }
        //??
        /*function get_info_user(id) {
        }*/
        //функция добавления поставщика
        $("#btn_add_provider").click(function () {
            var type = $('#provider_type').val();
            var title = $('#provider_title').val();
            //alert("123");
            if (title != "") {
                $.post("./api.php", {
                    action: "add_pos_provider",
                    type: type,
                    title: title
                }).done(function (data) {
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
        //отображать архив
        $('#check_show_all').change(function () {
            if ($(this).is(":checked")) {
                $(this).attr("checked", true);
            }
            $("#show_all").submit();
        });
    });

</script>
</body>
</html>
