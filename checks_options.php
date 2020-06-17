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
       Чек-лист
        
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
               
               <div class="margin"> 
                    <div class="btn-group">
                      <button type="button" class="btn btn-default">Опция</button>
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                          <?php
                              echo '<li><a href="checks_options.php?id='.$_GET['id'].'">Убрать фильтр</a></li>';
                              $options = $robots->getOptions;
                              foreach ($options as $option) {
                                  echo '<li><a href="checks_options.php?id='.$_GET['id'].'&option='.$option['id_option'].'">'.$option['title'].'</a></li>';
                              }
                          ?>
                      </ul>
                    </div>
             </div>
                
              <table id="checks" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Номер</th>
                  <th>Наименование</th>
                  <th>Комплект деталей</th>
                  <th>Опция</th>
                  <th></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                    $option = (isset($_GET['option'])) ? $_GET['option'] : 0;
                    $arr = $checks->get_checks_on_option_in_cat($option, $_GET['id']);
                    if (isset($arr)) {
                        foreach ($arr as &$check) {
                            $id_check = $check['check_id'];
                            $title = $check['check_title'];
                            $cat = $check['check_category'];
                            $kit_out = "";
                            if ($check['id_kit']!=0) {
                                $kit_out = "<a href='edit_kit.php?id=".$check['id_kit']."'><i class='fa fa-2x fa-cubes'></i></a>";
                            }
                            $option_out = "";
                            if ($check['id_option']!=0) {
                                $option_out = "<a href='edit_option.php?id=".$check['id_option']."'><i class='fa fa-2x fa-plus-square'></i></a>";
                            }
                            echo "
                                <tr id='row_".$id_check."'>
                                    <td>".$id_check."</td>                                    
                                    <td>".$title."</td>
                                    <td align='center'>".$kit_out."</td>
                                    <td align='center'>".$option_out."</td>                                    
                                    <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' data-id='".$id_check."' data-title='".$title."' data-kit='".$check['id_kit']."' data-category='".$cat."'></i></td>
                                    <td><i class='fa fa-2x fa-remove' style='cursor: pointer;' data-id='".$id_check."'></i></td>
                                </tr>
                            ";
                        }
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
                    <label>Категория</label>
                    <select class="form-control" name="category" placeholder="Выберите категорию" id="category_edit" required="required">
                        <option value="0">Выберите категорию...</option>
                        <?php
                        $arr = $position->getCategoryes;

                        foreach ($arr as &$category) {
                            echo "
                       <option value='".$category['id']."'>".$category['title']."</option>
                       
                       ";
                        }

                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Наименование</label>
                    <input type="text" class="form-control" name="title_edit" required="required" id="title_edit">
                </div>

                <div class="form-group">
                    <label>Комплект деталей</label>

                    <select class="form-control" name="kit"  id="kit_edit" required="required">
                        <option value="0"></option>
                        <?php
                        $arr = $position->get_kit();

                        foreach ($arr as &$kit) {
                            echo "<option value='".$kit['id_kit']."'>".$kit['kit_title']." (" .$kit['title'].")</option>";
                        }

                        ?>
                    </select>
                </div>


                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="btn_edit" name="">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include 'template/scripts.php';?>

<!-- page script -->
<script>
    var id_check = 0;
    var id_option = <?php echo $_GET['id']; ?> ;
    $( ".fa-pencil" ).click(function() {

        id_check = $(this).data("id");
        var title = $(this).data( "title" );
        var category = $(this).data( "category" );
        var kit = $(this).data( "kit" );
        console.log(title);

        $('#check_edit').modal('show');
        $('#category_edit').val(category);
        $('#title_edit').val(title);
        $('#kit_edit').val(kit);

    });
    $( ".fa-remove" ).click(function() {
        var id = $(this).data("id");
        $.post( "./api.php", {
            action: "del_check_in_option",
            id: id,
        } )
            .done(function( data ) {
                window.location.href = "./checks_options.php?id=<?php echo $_GET['id'];?><?php echo (isset($_GET['option'])) ? "&option=".$_GET['option'] : "";?>";
                return false;
            });

    });

    $( "#btn_edit" ).click(function() {

        var id =  id_check;
        var title =  $('#title_edit').val();
        var category =  $('#category_edit').val();
        var kit =  $('#kit_edit').val();

        $.post( "./api.php", {
            action: "edit_check_on_option",
            id: id,
            title: title,
            category: category,
            kit: kit

        } )
            .done(function( data ) {
                if (data=="false") {alert( "Data Loaded: " + data ); }
                else {
                    window.location.href = "./checks_options.php?id=<?php echo $_GET['id'];?><?php echo (isset($_GET['option'])) ? "&option=".$_GET['option'] : "";?>";
                }
            });

    });
</script>
</body>
</html>
