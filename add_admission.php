<?php 
include 'include/class.inc.php';
?>
<?php include 'template/head.php' ?>

<!DOCTYPE html>
<html>
<head>
	<title>Добавить поступление</title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<?php include 'template/header.php' ?>
		<!-- Left side column. contains the logo and sidebar -->
		<?php include 'template/sidebar.php';?>
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>Поступления</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Добавить поступление</h3>
							</div><!-- /.box-header -->
							<div class="box-body">
								<form data-toggle="validator" id="add_pos" name="add_pos" role="form">
									
									<div class="form-group">
										<label>Категория</label> <select class="form-control" id="category" name="category" required="required" >
											<option value="0">
												Веберите категорию...
											</option><?php 
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
										<label>Заказ № </label> <select class="form-control" id="order" name="order" required="required">
											<option value="0">
												Веберите заказ...
											</option>
										</select>
									</div>
                                    <p class="p-label">Добавить позицию</p>
								    <div class="form-group input-group" id="pos">
                                      
                                      <input type="text" class="form-control" name="pos" id="search_pos" placeholder="Введите название позиции...">
                                      <span class="input-group-btn">
                                          <button type="button" class="btn btn-info btn-flat" id="add">+</button>
                                      </span>
                                    </div>
                                    
									
									<table class="table table-hover" id="listPos">
                                    <thead>
                                    <tr>
                                      <th>ID</th>
                                      <th>Артикул</th>
                                      <th>Наименование</th>
                                      <th>Заказанное количество</th>
                                      <th>Отгружено</th>
                                      <th>Поступление</th>
                                      <th>Возврат</th>
                                    </tr>
                                   </thead>
                                    <tbody>
                                    
                                    </tbody>
                                    </table>
									
									<div class="box-footer">
										<button class="btn btn-primary" id="save_close" type="submit">Сохранить и закрыть</button> 
										<button class="btn btn-primary" id="save_new" type="submit">Сохранить и создать новое поступление</button>
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
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

	<script>
	
$(document).ready(function() { 	
var arr_str = [];
var arr_ids = [];
var arr_pos = [];
var pos_info = [];
var category_data = [];
var category = "0";
var provider = "0";
$( "#category" )
  .change(function () {
    var id = "";
    
    $( "#category option:selected" ).each(function() {
      id = $( this ).val();
    });
 
    $.post( "./api.php", { action: "get_orders", id: id, status: 0 } )
    .done(function( data ) {
       
        $('option', $("#order")).remove();
        
        $('#order').append($("<option selected></option>")
                    .attr("value",0)
                    .attr("data-target","1")
                    .text("Без заказа"));   
        
        var obj = jQuery.parseJSON(data);
        
        $.each( obj, function( key, value ) {
          $('#order').append($("<option></option>")
                    .attr("value",value['order_id'])
                    .attr("data-target",value['order_provider'])
                    .text("Заказ №"+value['order_id']+" ("+value['type']+" " + value['title']+") на " + value['order_delivery']));   
        });
    });
 
 
  });

$( "#order" )
  .change(function () {
      var id = $(this).val();
      provider = $("#order option:selected").data("target");
      console.log(provider);
  
       
        $("#listPos > tbody").html("");
    
    $.post( "./api.php", { action: "get_pos_in_order", id: id } )
            .done(function( data ) {  
                 console.log(data);
                 var obj = jQuery.parseJSON(data);
                 $.each( obj, function( key, value ) {
                       $('#listPos tbody').append('<tr> \
                        <td>'+value['id']+'</td> \
                        <td>'+value['vendor_code']+'</td> \
                        <td>'+value['title']+'</td> \
                        <td class="quant_order">'+value['pos_count']+'</td> \
                        <td class="quant_finish">'+value['pos_count_finish']+'</td> \
                        <td class="quant"><span>0</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="0"></td> \
                        <td class="quant"><span>0</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="0"></td> \
                        </tr>');
                    });
            });
 });
 
 $("#save_close").click(function() {
    $(this).last().addClass( "disabled" );
 	save_close();
 	return false;
 });
 
 $("#save_new").click(function() {
    $(this).last().addClass( "disabled" ); 
 	save_new();
 	return false;
 });
 

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

   
 
 
 function save_close() {
     var id =  $("#order").val();
     provider = $("#order option:selected").data("target");
     category =  $("#category").val();
     console.log(category);
     console.log(provider);
     var TableArray = [];
        
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
       
       if (order!=0)  {
       	$.post("./api.php", {
 			action: "add_admission",
 			order_id: id,
 			json: JsonString,
 			category : category,
 			provider : provider
 		}).done(function(data) {
 			console.log(data);
 			
 			window.location.href = "./admissions.php?id="+ category;
 		});
       }
       
       return false;
 }

 function save_new() {
      var id =  $("#order").val();
     category =  $("#category").val();
     console.log(category);
     console.log(provider);
     var TableArray = [];
        
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
       
       if (order!=0)  {
       	$.post("./api.php", {
 			action: "add_admission",
 			order_id: id,
 			json: JsonString,
 			category : category,
 			provider : provider
 		}).done(function(data) {
 			console.log(data);
 			window.location.href = "./add_admission.php";
 		});
       }
       
       return false;
 }

  });
  
  
    $("#search_pos").autocomplete({
          source: "./tt.php", // url-адрес
          minLength: 2 // минимальное количество для совершения запроса
    });
    
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
                     console.log(pos_info);

                        $('#listPos tbody').append('<tr> \
                        <td>'+pos_info['id']+'</td> \
                        <td>'+pos_info['vendor_code']+'</td> \
                        <td>'+pos_info['title']+'</td> \
                        <td class="quant_order">0</td> \
                        <td class="quant_finish">0</td> \
                        <td class="quant"><span>0</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="0"></td> \
                        <td class="quant"><span>0</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="0"></td> \
                        </tr>');
                        
                        $('#search_pos').val(""); 
       
                  });
        return false;
  });    
	</script>
</body>
</html>