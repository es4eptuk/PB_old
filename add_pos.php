<?php 
include 'include/class.inc.php';
?>

<?php include 'template/head.html' ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'template/header.html' ?>

  <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/sidebar.html';?>

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
          
        <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Добавить позицию</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" data-toggle="validator" id="add_pos">
                <!-- text input -->
                <div class="form-group">
                  <label>Наименование</label>
                  <input type="text" class="form-control" name="title" required="required" id="title" value="<? if(isset($_GET['title']))  echo $_GET['title']; ?>">
                </div>
                <div class="form-group">
                  <label>Описание</label>
                  <input type="text" class="form-control" name="longtitle" id="longtitle" value="<? if(isset($_GET['longtitle']))  echo $_GET['longtitle']; ?>">
                </div>

                

                <!-- select -->
                <div class="form-group">
                  <label>Категория</label>
                  <select class="form-control" name="category" placeholder="Веберите категорию" id="category" required="required">
                   <option value="0">Веберите категорию...</option>
                   <?php 
                   $arr = $position->get_pos_category();
                
                if (isset($_GET['category'])) {
                    foreach ($arr as &$category) {
                        
                        if ($_GET['category'] == $category['id']) {
                       echo "
                       <option value='".$category['id']."' selected>".$category['title']."</option>
                       ";
                        } else {
                            
                             echo "
                       <option value='".$category['id']."' >".$category['title']."</option>
                       ";
                        }
                    }
                }
                
                else {
                    foreach ($arr as &$category) {
                       echo "
                       <option value='".$category['id']."'>".$category['title']."</option>
                       ";
                    }
                    
                }
                   
                   ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Подкатегория</label>
                  <select class="form-control" name="subcategory" id="subcategory" >
                    
                    
                    
               
                    
                    
                  </select>
                </div>
                
              
                
                <div class="form-group">
                  <label>Артикул</label>
                  <?
                  $gen_code = $position->generate_art();
                  $gen_code = $gen_code['max(id)'];
                  $cat = "";
                  if (isset($_GET['category'])) {
                  switch ($_GET['category']) {
                    case 1:
                        $cat = "MH";
                        break;
                    case 2:
                        $cat = "HP";
                        break;
                    case 3:
                        $cat = "BD";
                        break;
                    case 4:
                        $cat = "PK";
                        break; 
                    case 5:
                        $cat = "HS";
                        break;    
                }
                  }
                  
                  
                  //print_r($gen_code);
                  ?>
                  <input type="text" class="form-control" name="vendorcode"  id="vendorcode" value="<? echo $cat."-".$gen_code; ?>">
                </div>
                
                 <div class="form-group">
                  <label>Поставщик <small>(<a href="#" data-toggle="modal" data-target="#add_provider">Добавить</a>)</small></label>
                  <select class="form-control" name="provider" placeholder="Веберите категорию" id="provider" required="required">
                   <option>Веберите поставщика...</option>
                   <?php 
                   $arr = $position->get_pos_provider();
                
                 if (isset($_GET['provider'])) {
                
                    foreach ($arr as &$provider) {
                        
                        if ($_GET['provider']==$provider['id']) {
                       echo "
                       <option value='".$provider['id']."' selected>".$provider['type']." ".$provider['title']."</option>
                       ";
                        } else {
                           echo "
                       <option value='".$provider['id']."'>".$provider['type']." ".$provider['title']."</option>
                       ";  
                            
                        }
                    }
                    
                 } else {
                     
                      foreach ($arr as &$provider) {
                       echo "
                       <option value='".$provider['id']."'>".$provider['type']." ".$provider['title']."</option>
                       
                       ";
                    }
                     
                 }
                   
                   ?>
                  </select>
                  
                  
                </div>
                
                <div class="form-group">
                  <label>Стоимость</label>
                  <input type="text" class="form-control" name="price" placeholder="0.00" id="price" value="<? if(isset($_GET['price']))  echo $_GET['price']; ?>">
                </div>
                
               
                
                 <div class="form-group">
                  <label>Количество на складе</label>
                  <input type="text" class="form-control" name="quant_total" placeholder="0" id="quant_total">
                </div>
                
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="save_close">Сохранить и закрыть</button>
                    <button type="submit" class="btn btn-primary" id="save_new">Сохранить и создать новую позицию</button>
                </div>
              </form>
 
            </div>
            <!-- /.box-body -->
          </div>
         
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
 

 
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<!-- Modal -->
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
                  <select class="form-control" name="provider_type" placeholder="Веберите форму собственности" id="provider_type" required="required">
                  <option value="ИП">ИП</option>
                  <option value="ООО">ООО</option>
                  <option value="ОАО">ОАО</option>
                  <option value="ЗАО">ЗАО</option>
                  <option value="ЗАО">Ltd.</option>
                  </select>
                </div> 
                
                <div class="form-group">
                  <label>Наименование</label>
                  <input type="text" class="form-control" name="provider_title" id="provider_title" required="required">
                </div>
                
                
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary" id="btn_add_provider">Добавить</button>
      </div>
    </div>
  </div>
</div>

<?php include './template/scripts.html'; ?>
<script>
  
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
  
  
    $( "#version" )
  .change(function () {
    var cat = $( "#category" ).val(); 
    var version = $( this ).val();
    var max_id = <? echo $gen_code; ?>+1;
    var cat_str=""
    
    switch (cat) {
          case "1":
            cat_str = "MH";
            console.log(cat);
            break;
          case "2":
            cat_str = "HP";
            break;
          case "3":
            cat_str = "BD";
            break;
           case "4":
            cat_str = "PK";
            break;
            case "5":
            cat_str = "HS";
            break;
        
        }
    
    var code = cat_str+"-"+version+"-"+max_id;
    $( "#vendorcode" ).val(code); 
    //alert(code);

 
 
  });
  
  $( "#save_close" ).click(function() {
      $(this).last().addClass( "disabled" );
     save_close();
     return false;
    });
    
  $( "#save_new" ).click(function() {
      $(this).last().addClass( "disabled" );
      save_new();
      return false;
    });
    
    
   $( "#btn_add_provider" ).click(function() {
     var type =  $('#provider_type').val();
     var title = $('#provider_title').val();
    //alert("123");
    if (title!="") {
     
      $.post( "./api.php", { 
        action: "add_pos_provider", 
        type: type,
        title: title 
    } )
          .done(function( data ) {
              console.log(data);
              if (data=="false") {alert( "Data Loaded: " + data );  return false;}
              else {
                  $('#provider').append("<option value='"+ data +"' selected>"+ title +"</option>");
                  $('#add_provider').modal('hide');
                  //return false;
              }
          });
     
     
    }
    
    });  
  
 $('#add_pos').validator();
 $('#add_provider_form').validator();
  
  
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
    
    if (title!="" && category!="0" && subcategory!="" ) {
    
      $.post( "./api.php", { 
        action: "add_pos", 
        title: title,
        longtitle: longtitle ,
        category: category ,
        subcategory: subcategory ,
       
        vendorcode: vendorcode ,
        provider: provider ,
        price: price ,
        quant_robot: 0 ,
        quant_total: quant_total 
    } )
          .done(function( data ) {
              data.replace(new RegExp("\\r?\\n", "g"), "");
                console.log(data);
              if (data=="false") {alert( "Невозможно добавить позицию"); return false; }
              else {
                 
                  window.location.href = "./pos.php?id="+category;
              }
          });
          
    }
    
 }
 
 function save_new() {
    var title =  $('#title').val();
    var longtitle =  $('#longtitle').val();
    var category =  $('#category').val();
    var subcategory =  $('#subcategory').val();
    var vendorcode =  $('#vendorcode').val();
    var provider =  $('#provider').val();
    var price =  $('#price').val();
    var quant_robot =  0; 
    var quant_total =  $('#quant_total').val(); 
    
     if (title!="" && category!="0" && subcategory!="" ) {
    
      $.post( "./api.php", { 
        action: "add_pos", 
        title: title,
        longtitle: longtitle ,
        category: category ,
        subcategory: subcategory ,
        vendorcode: vendorcode ,
        provider: provider ,
        price: price ,
        quant_robot: 0 ,
        quant_total: quant_total 
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                  window.location.href = "./add_pos.php";
              }
          });
     }
 }
  
</script>



</body>
</html>
