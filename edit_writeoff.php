<?php 
include 'include/class.inc.php';

$writeoff1 = $writeoff->get_info_writeoff($_GET['id']);
$writeoff_id = $writeoff1['id'];
$writeoff_date = $writeoff1['update_date'];
$writeoff_category = $writeoff1['category'];
$writeoff_description = $writeoff1['description'];
$writeoff_price = $writeoff1['total_price'];
$writeoff_user_id = $writeoff1['update_user'];
?>
<?php include 'template/head.html' ?>

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
				<h1>Списания</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Списание № <span id="writeoff_id"><?php echo $writeoff_id; ?></span></h3>
							</div><!-- /.box-header -->
							<div class="box-body">
                                <b>Общая сумма списания:</b> <?php echo number_format($writeoff_price, 2, ',', ' ') ; ?> <br><br>
							     <div class="form-group">
										<label>Категория</label> <select class="form-control" id="category" name="category" required="required" >
											<?php $arr = ["Модернизация","Брак","Сервис","Производство","Разработка","Давальческие материалы","Возврат поставщику","Покраска/Покрытие","Не прокатило"];
											foreach ($arr as &$value) {
                                               if ($writeoff_category == $value) {
                                                   echo '<option value="'.$value.'" selected>'.$value.'</option>';
                                               } else {
                                                  echo '<option value="'.$value.'">'.$value.'</option>';
                                               }
                                            }
											
											?>
										</select>
									</div>
							   
							   	    <div class="form-group">
										<label>Описание</label> 
										 <input type="text" class="form-control" name="description" required="required" id="description" value="<?php echo $writeoff_description; ?> ">
									</div>
									
									<div class="print"><b>Категория: </b><?php echo $writeoff_category;?></div>
									<div class="print"><b>Описание: </b><?php echo $writeoff_description;?></div>
							   
									<table class="table table-hover" id="listPos">
                                    <tbody><tr>
                                      <th>ID</th>
                                      <th>posID</th>
                                      <th>Артикул</th>
                                      <th>Наименование</th>
                                      <th>Количество</th>
                                      <th>Удаление</th>
                                    </tr>
                                   
                                    <?php 
                                    $arr_pos = $writeoff->get_pos_in_writeoff($writeoff_id);
                                    
                                foreach ($arr_pos as &$value) { 
                                    
                                $title = $value['pos_title'];
                                  $vendor_code = $value['vendor_code']; 
                                  
                                echo '   
                                    <tr> 
                                    <td>'.$value['id'].'</td>
                                    <td>'.$value['pos_id'].'</td>
                                    <td>'.$vendor_code.'</td> 
                                    <td>'.$title.'</td> 
                                    <td class="quant"><span style="position: absolute;">'.$value['pos_count'].'</span><input type="text" class="form-control quant_inp"  style="position: relative; height: 20px; width: 55px; text-align: center;" placeholder="'.$value['pos_count'].'" value="'.$value['pos_count'].'"></td> 
                                    <td><i class="fa fa-2x fa-remove" style="cursor: pointer;" id="'.$value['pos_id'].'" data-target="'.$value['pos_count'].'"></i></td> 
                                    </tr>
                        ';
                                }
                                    
                                    ?>
                                    
                                    </tbody>
                                    </table>
									
							
							<?php 
							if ($userdata['user_id'] == 35 || $userdata['user_id'] == 14) {
							    echo '
							    
							    	<div class="box-footer">
										<button class="btn btn-primary" id="save_close" type="submit">Сохранить</button> 
										<button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>
							</div>
							    ';
							}
							
							?>
								
						
									
									
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
     var count = $(this).data("target"); 
     var id = $(this).attr('id');
      $.post("./api.php", {
 			action: "del_pos_writeoff",
 			id: <?php echo $writeoff_id; ?>,
 			pos_id: id,
 			count: count,
 		}).done(function(data) {
            window.location.reload(true);
 		});
 		
 		

   
   });   
 
 
  function save_close() {
    $(this).prop('disabled', true);
    var id =  $("#writeoff_id").text();
    var title =  "<?php echo  $writeoff_category; ?>";
    var description =  $("#description").val();
   
    var TableArray = [];
        TableArray.push([title,description]);
        
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
       
       
       	$.post("./api.php", {
 			action: "edit_writeoff",
 			id: id,
 			json: JsonString
 		}).done(function(data) {
 			console.log(data);
 			window.location.href = "./writeoff.php";
 			return false;
 		});
       
       
       return false;
 }

 function save_new() {
     
     return false;
 }
 
  $( "#delete" ).click(function() { 
    $(this).last().addClass( "disabled" );
  delete_writeoff();
  return false;
  });
  
   function delete_writeoff() {
     var id =  $("#writeoff_id").text();
     $.post( "./api.php", { 
        action: "del_writeoff", 
        id: id
 
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                 // console.log(data);
                window.location.href = "./writeoff.php";  
              }
          });
    
   }

  });

	</script>
</body>
</html>