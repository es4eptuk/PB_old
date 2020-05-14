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
       Контрагенты
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
                                 <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#add_provider">Добавить контрагента</button>

            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="pos" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Юридическое название</th>
                  <th>Фирменное название</th>
                  <th>Телефон</th>
                  <th>Email</th>
                  <th>Адрес</th>
                  <th>Контакт</th>
                  <th></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
  
                
                $arr = $position->get_pos_provider();
                
                foreach ($arr as &$pos) {
                       echo "
                       <tr >
                          <td>".$pos['title'].", ".$pos['type']."</td>
                          <td>".$pos['name']."</td>                          
                          <td>".$pos['phone']."</td>
                          <td>".$pos['email']."</td>
                          <td>".$pos['address']."</td>
                          
                          <td>".$pos['contact']."</td>
                          
                          <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$pos['id']."'></i></td>
                          <td><i class='fa fa-2x fa-remove' style='cursor: pointer;' id='".$pos['id']."'></i></td>

                        </tr>
                       
                       
                       ";
                    }
                
                ?>
              </table>
            </div>
            
            <div class="box-footer">
                    <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#add_provider">Добавить контрагента</button>
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
        <h5 class="modal-title" id="exampleModalLabel">Редактирование контрагента</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
          <form role="form" data-toggle="validator" id="add_pos">
                <!-- text input -->
                <div class="form-group">
                  <label>Форма собственности</label>
                  <select class="form-control" id="edit_type" name="type" required="required">
                      <option value="ИП">ИП</option>
                      <option value="ООО">ООО</option>
                      <option value="ОАО">ОАО</option>
                      <option value="ЗАО">ЗАО</option>
                      <option value="Ltd.">Ltd.</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Юридическое название</label>
                  <input type="text" class="form-control" name="title" required="required" id="edit_title">
                </div>
                <div class="form-group">
                  <label>Фирменное название</label>
                  <input type="text" class="form-control" name="name" id="edit_name">
                </div>
                <div class="form-group">
                  <label>Телефон</label>
                  <textarea class="form-control" rows="1" placeholder="+7 ..." id="edit_phone"></textarea>
                </div>
                
                <div class="form-group">
                  <label>Email</label>
                  <textarea class="form-control" rows="1" placeholder="@ ..." id="edit_email"></textarea>
                </div>
                
                <div class="form-group">
                  <label>Адрес</label>
                  <textarea class="form-control" rows="3" placeholder="Россия ..." id="edit_address"></textarea>
                </div>
                
                <div class="form-group">
                  <label>Контактное лицо</label>
                  <textarea class="form-control" rows="2" placeholder="Enter ..." id="edit_contact"></textarea>
                </div>
                
                <div id="update"></div>
                
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                   <!-- <button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button> -->
                </div>
              </form>
         
      </div>
      
    </div>
  </div>
</div>

<div class="modal fade" id="add_provider" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Добавить поставщика</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
           <form role="form" data-toggle="validator" id="add_provider_form">

                <!-- select -->
                <div class="form-group">
                  <label>Форма собственности</label>
                  <select class="form-control" name="provider_type" placeholder="Веберите форму собственности" id="type" required="required">
                      <option value="ИП">ИП</option>
                      <option value="ООО">ООО</option>
                      <option value="ОАО">ОАО</option>
                      <option value="ЗАО">ЗАО</option>
                      <option value="Ltd.">Ltd.</option>
                  </select>
                </div> 
                
                <div class="form-group">
                  <label>Юридическое название</label>
                  <input type="text" class="form-control" name="title" id="title" required="required">
                </div>
                <div class="form-group">
                   <label>Фирменное название</label>
                   <input type="text" class="form-control" name="name" id="name">
                </div>
                <div class="form-group">
                  <label>Телефон</label>
                  <textarea class="form-control" rows="1" placeholder="+7 ..." id="phone"></textarea>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <textarea class="form-control" rows="1" placeholder="@ ..." id="email"></textarea>
                </div>
                <div class="form-group">
                  <label>Адрес</label>
                  <textarea class="form-control" rows="3" placeholder="Россия ..." id="address"></textarea>
                </div>
                
                <div class="form-group">
                  <label>Контактное лицо</label>
                  <textarea class="form-control" rows="2" placeholder="Enter ..." id="contact"></textarea>
                </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary" id="btn_add_provider">Добавить</button>
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

    var id_pos = 0;

    /*
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
    */

    $("#pos .fa-pencil").click(function () {
        id_pos = $(this).attr("id");
        $('#pos_edit').modal('show');
        $.post("./api.php", {
            action: "get_info_pos_provider",
            provider: id_pos
        }).done(function (data) {
            var obj = jQuery.parseJSON(data);
            //console.log(obj);
            // console.log (get_info_user(obj['update_user']));
            $('#edit_title').val(obj['title']);
            $('#edit_name').val(obj['name']);
            $('#edit_type').val(obj['type']);
            $('#edit_phone').val(obj['phone']);
            $('#edit_email').val(obj['email']);
            $('#edit_address').val(obj['address']);
            $('#edit_contact').val(obj['contact']);
        });
    });

    $(".fa-remove").click(function () {
        if (confirm("Ты хорошо подумал?")) {
            var id = $(this).attr("id");
            $.post("./api.php", {
                action: "del_contragent",
                id: id
            }).done(function (data) {
                window.location.reload(true);
                return false;
            });
        } else {
            alert("Это правильное решение!");
        }
    });

    $(function () {
        $('#example1').DataTable()
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': true,
            'autoWidth': false
        })
    })

    $("#save_close").click(function () {
        save_close();
        return false;
    });

    /*
    $("#delete").click(function () {
        delete_pos();
        return false;
    });
    function delete_pos() {
        var category = $('#category').val();
        $.post("./api.php", {
            action: "delete_pos",
            id: id_pos
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.href = "./pos.php?id=" + category;
            }
        });
    }
    */

    function save_close() {
        var title = $('#edit_title').val();
        var name = $('#edit_name').val();
        var type = $('#edit_type').val();
        var phone = $('#edit_phone').val();
        var email = $('#edit_email').val();
        var address = $('#edit_address').val();
        var contact = $('#edit_contact').val();
        $.post("./api.php", {
            action: "edit_provider",
            id: id_pos,
            title: title,
            name: name,
            type: type,
            phone: phone,
            email: email,
            address: address,
            contact: contact

        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.href = "./contragents.php";
            }
        });
    }

    function get_info_user(id) {
    }

    $("#btn_add_provider").click(function () {
        var title = $('#title').val();
        var name = $('#name').val();
        var type = $('#type').val();
        var phone = $('#phone').val();
        var email = $('#email').val();
        var address = $('#address').val();
        var contact = $('#contact').val();
        //alert("123");
        if (title != "") {
            $.post("./api.php", {
                action: "add_full_provider",
                type: type,
                title: title,
                name: name,
                phone: phone,
                email: email,
                address: address,
                contact: contact
            }).done(function (data) {
                console.log(data);
                if (data == "false") {
                    alert("Data Loaded: " + data);
                    return false;
                } else {
                    window.location.reload(true);
                    //return false;
                }
            });
        }
    });

    $('#pos').DataTable({
        "iDisplayLength": 100,
        "order": [[0, "asc"]]
    });

</script>
</body>
</html>
