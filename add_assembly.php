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
				<h1>Сборки</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Добавить сборку</h3>
							</div><!-- /.box-header -->
							<div class="box-body">
								<form data-toggle="validator" id="add_pos" name="add_pos" role="form">
									
									<div class="form-group">
										<label>Название</label> 
										 <input type="text" class="form-control" name="title" required="required" id="title">
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
                                      <th>Удаление</th>
                                    </tr>
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

	<?php include 'template/scripts.php'; ?>
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

                        $('#listPos tr:eq(0)').after('<tr> \
                        <td>'+pos_info['id']+'</td> \
                        <td>'+pos_info['vendor_code']+'</td> \
                        <td>'+pos_info['title']+'</td> \
                        <td class="quant"><span>1</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="1"></td> \
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
   
 
 
 function save_close() {
    var title =  $("#title").val();
  
    var TableArray = [];
        TableArray.push([title,""]);
        
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
       
       if (title!="")  {
       	$.post("./api.php", {
 			action: "add_assembly",
 			json: JsonString
 		}).done(function(data) {
 			console.log(data);
 			window.location.href = "./assembly.php";
 		});
       }
       
       return false;
 }

 function save_new() {
      var category =  $("#category").val();
     var provider =  $("#provider").val();
     
     var TableArray = [];
        TableArray.push([$('#category').val(),$('#provider').val()]);
        
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
       
        if (category!=0 && provider!=0)  {
           	$.post("./api.php", {
     			action: "add_order",
     			json: JsonString
     		}).done(function(data) {
     			console.log(data);
     			var category =  $("#category").val();
     			
     		});
        
       
       window.location.href = "./add_order.php";
       return false;
        }
 }

  });

	</script>
</body>
</html>