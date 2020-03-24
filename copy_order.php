<?php 
include 'include/class.inc.php';

$current_month = date('m');
    $current_year = date('y');
    $tmp_date = "25.".$current_month.".".$current_year;

    $order_date =  date('d.m.Y',strtotime("$tmp_date +1 month"));

    $order_date = new DateTime($order_date);
    $order_date = $order_date->format('d.m.Y');

?>
<?php include 'template/head.html' ;

$id = $_GET['id'];
$order = $orders->get_info_order($id);
$order_category = $order['order_category'];
$order_provider = $order['order_provider'];
$order_status = $order['order_status'];
$order_responsible = $order['order_responsible'];


?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<?php include 'template/header.html' ?>
		<!-- Left side column. contains the logo and sidebar -->
		<?php include 'template/sidebar.html';?>
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>Заказы</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Добавить заказ</h3>
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
											                       if ( $category['id'] == $order_category ) {
											                          echo "
											                       <option value='".$category['id']."' selected>".$category['title']."</option>
											                       
											                       ";  
											                           
											                       } else {
											                       echo "
											                       <option value='".$category['id']."'>".$category['title']."</option>
											                       
											                       ";
											                    }
											                    }
											                   
											                   
											                   ?>
										</select>
									</div>
								    <div class="form-group">
										<label>Контрагент <small>(<a data-target="#add_provider" data-toggle="modal" href="#">Добавить</a>)</small></label> <select class="form-control" id="provider" name="provider" required="required">
											<option value="0">
												Веберите контррагента...
											</option><?php 
											                  $arr = $position->get_pos_provider();
											                
											                    foreach ($arr as &$provider) {
											                      if ( $provider['id'] == $order_provider ) {  
											                        
											                       echo "
											                      <option value='".$provider['id']."' selected>".$provider['title'].", ".$provider['type']."</option>
											                       
											                       ";
											                      } else {
											                          
											                          echo "
											                       <option value='".$provider['id']."'>".$provider['type']." ".$provider['title']."</option>
											                       
											                       "; 
											                          
											                      }
											                    }
											                   
											                   ?>
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
                                    <tbody><tr>
                                      <th>ID</th>
                                      <th>Артикул</th>
                                      <th>Наименование</th>
                                      <th>Количество</th>
                                      <th>Цена</th>
                                      <th>Сумма</th>
                                      <th>Срок поставки</th>
                                      <th>Удаление</th>
                                    </tr>
                                   
                                   <?php 
                                    $arr_pos = $orders->get_pos_in_order($id);
                                   //print_r (  $arr_pos);
                                foreach ($arr_pos as &$value) { 
                                    $date = new DateTime($value['pos_date']);
                                    $pos_date = $date->format('d.m.Y');
                                echo '   
                                    <tr> 
                        <td>'.$value['id'].'</td> 
                        <td>'.$value['vendor_code'].'</td> 
                        <td>'.$value['title'].'</td> 
                        <td class="quant"><span>0</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="0" value="0"></td>  
                        
                        <td class="price">'.$value['price'] / $value['pos_count'].'</td> 
                        <td class="sum">'.$value['price'].'</td> 
                        <td>
                            <div class="input-group date" style="width: 135px;"> 
                                <div class="input-group-addon"> 
                                    <i class="fa fa-calendar"></i> 
                                </div> 
                                <span style="position: absolute;">'.$order_date.'</span>
                                <input type="text" class="form-control pull-right date_inp" style="position: relative; text-align: center;" placeholder="'.$order_date.'" value="'.$order_date.'"> 
                            </div>
                        </td> 
                        <td><i class="fa fa-2x fa-remove" style="cursor: pointer;"></i></td> 
                        </tr>
                        ';
                                }
                                    
                                    ?>
                                    
                                    </tbody>
                                    </table>
									
									<div class="box-footer">
										<button class="btn btn-primary" id="save_close" type="submit">Сохранить и закрыть</button> 
										<button class="btn btn-primary" id="save_new" type="submit">Сохранить и создать новый заказ</button>
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
	<?php include './template/scripts.html'; ?>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

	<script>
	
$(document).ready(function() { 	
var arr_str = [];
var arr_ids = [];
var arr_pos = [];
var pos_info = [];
var category_data = [];
var category1 = "---";
 $("#save_close").click(function() {
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
          source: "./tt.php?action=pos_search", // url-адрес
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
                        <td class="price">'+pos_info['price']+'</td> \
                        <td class="sum">'+pos_info['price']+'</td> \
                        <td><div class="input-group date" style="width: 135px;"> \
                          <div class="input-group-addon"> \
                            <i class="fa fa-calendar"></i> \
                          </div> \
                          <span style="position: absolute;"><?php echo $order_date; ?></span><input type="text" class="form-control pull-right date_inp" style="position: relative; text-align: center;" placeholder="<?php echo $order_date; ?>" value="<?php echo $order_date; ?>"> \
                        </div></td> \
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
 			
 			window.location.href = "./orders.php?id="+ category;
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