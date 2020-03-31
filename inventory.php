<?php 
include 'include/class.inc.php';

$current_month = date('m');
        $current_year = date('y');
        $tmp_date = "25.".$current_month.".".$current_year;
        
        $order_date =  date('d.m.Y',strtotime("$tmp_date +1 month"));
        
        $order_date = new DateTime($order_date);
		$order_date = $order_date->format('d.m.Y');




?>
<?php include 'template/head.php' ?>

<style>
    .assembly {
        font-size: 10px;
        margin-top: 10px;
    }
    
</style>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<?php include 'template/header.php' ?>
		<!-- Left side column. contains the logo and sidebar -->
		<?php include 'template/sidebar.php';?>
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>Инвентаризация</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
						
							<div class="box-body">
							
								    
								     
									
								
									
								    <div class="form-group input-group" id="pos">
                                      
                                      <input type="text" class="form-control" name="pos" id="search_pos" placeholder="Введите название позиции...">
                                      <span class="input-group-btn">
                                          <button type="button" class="btn btn-info btn-flat" id="add">+</button>
                                      </span>
                                    </div>
									
								
								    <h2 id="title"></h2>
								    <h4>Артикул: <span id="vendor_code"></span></h4>
							     	<h4>На складе: <b id="total"></b></h4>
							     	
							     	<div class="form-group">
                                      <label>Новое количество</label>
                                      <input type="text" class="form-control" name="quant_total" placeholder="0" id="quant_total">
                                    </div>
                                  
                                      <input type="hidden" class="form-control" name="id_pos" placeholder="0" id="id_pos">
                                   
								    <h5>Результат: <b id="result" class="text-red"></b></h5>
									
									<div class="box-footer">
										<button class="btn btn-primary" id="save_close" type="submit">Обновить данные</button> 
									</div>
							
							</div><!-- /.box-body -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</section><!-- /.content -->
		</div><!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
		<div class="control-sidebar-bg"></div>
	</div><!-- ./wrapper -->
	<!-- Modal -->


	
	<?php include 'template/scripts.php'; ?>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- Select2 -->
    <script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
	<script>
var arr_assembly = [];	
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
     $(this).last().addClass( "disabled" );
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

   
    var subcategory = "";
    var assembly = 0;
    var out_assembly = "";
    $.post("./api.php", {
            action: "get_info_pos",
            id: id
        })
        .done(function(data1) {
            pos_info = jQuery.parseJSON(data1);
            title = pos_info['title'];
            
            $('#title').text(title);
            $('#id_pos').val(pos_info['id']);
            $('#vendor_code').text(pos_info['vendor_code']);
            $('#total').text(pos_info['total']);
             $('#result').text("");
            
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
   
   
 $("#listPos").on("click", ".btn_get_assembly", function() {
     var str = $(this).attr("id");
     arr_str = str.split('_');
     var id = arr_str[1];
     $("#table_assembly > tbody").html("");
     console.log(arr_assembly[id]['new']);
     $.each(arr_assembly[id]['new'], function(index, element) {
                var def = 0;
                def =  element['real'] - element['count'];
                if (def > 0) { def = 0;}
                console.log(element['real']);
                var ordered = "";
                var idd = element['pos_id'];
                
                 $.post("./api.php", {
                    action: "orderDate",
                    id: idd
                }).done(function(data) {
                console.log(data);
                
                  $('#table_assembly tbody').append('<tr> \
                        <td>' + element['pos_id'] + '</td> \
                        <td>' + element['vendor_code'] + '</td> \
                        <td>' + element['title'] + '</td> \
                        <td>' + element['count'] + '</td> \
                        <td>' + element['real'] + '</td> \
                        <td><b>' + Math.abs(def)  + '</b></td> \
                        <td>' + data + '</td> \
                        </tr>');  
                
                
                }); 
              
              }); 
     
   
   });  
   
   
   
   
   
   
$("#listPos").on("keyup", ".quant_inp", function() {
     var price = $( this ).parent().parent().find( ".price" ).text();
     var quant = $( this ).val();
     var id = $( this ).attr("id");
     if (quant=="") quant = 1;
     var sum = price * quant;
     sum = sum.toFixed(1)
     
     if (arr_assembly[id] !== undefined) {
          $("#icon_"+id).html("<i class='fa fa-check-circle text-green fa-2x' data-toggle='modal' data-target='#get_assembly'></i>");
              $.each(arr_assembly[id]['source'], function(index, element) {
                    arr_assembly[id]['new'][index]['count'] = element['count'] * quant;
                    
                    if (arr_assembly[id]['new'][index]['real'] < arr_assembly[id]['new'][index]['count']) {
                            $("#icon_"+pos_info['id']).html("<i class='fa fa-exclamation-circle text-red fa-2x' data-toggle='modal' data-target='#get_assembly'></i>");
                        } 
                    
              });
        }

     console.log(arr_assembly);
     $( this ).parent().parent().find( ".sum" ).text(sum);
   });   


$("#listPos").on("click", ".fa-remove", function() {

    $(this).parent().parent().fadeOut("normal", function() {
        $(this).remove();
    });
   }); 
   
 
 
 function save_close() {
      var new_total = $("#quant_total").val();
      var id = $("#id_pos").val();
     
      console.log(new_total);
       
       
       if (id!=0)  {
       	$.post("./api.php", {
 			action: "invent",
 			id: id,
 		
 			new_total : new_total
 		}).done(function(data) {
 		    $('#result').text(data);
 		    
 		    
 		
 	     	$('#quant_total').val('0');
 	     	$('#search_pos').val("");
 	     	$('#id_pos').val('');
 	     	$('#title').text('');
            $('#vendor_code').text('');
            $('#total').text('');
 		 
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