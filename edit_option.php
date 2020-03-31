<?php 
include 'include/class.inc.php';

$option = $robots->get_info_option($_GET['id']);
$option_id = $option['id_option'];
$option_title = $option['title'];
$option_kit = $option['id_kit'];

?>
<?php include 'template/head.php' ?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<?php include 'template/header.php' ?>
		<!-- Left side column. contains the logo and sidebar -->
		<?php include 'template/sidebar.php';?>
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>Опции робота</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Опция # <span id="option_id"><?php echo $option_id; ?></span> - <?php echo $option_title; ?></h3>
							</div><!-- /.box-header -->
							<div class="box-body">
								<form data-toggle="validator" id="add_pos" name="add_pos" role="form">
									
									<div class="form-group">
										<label>Название</label> 
										 <input type="text" class="form-control" name="title" required="required" id="title" value="<?php echo $option_title; ?> ">
									</div>
									
									
								
									
									<div class="box-footer">
										<button class="btn btn-primary" id="save_close" type="submit">Сохранить</button> 
										<button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>
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

                        $('#listPos tr:first').after('<tr> \
                        <td></td> \
                        <td>'+pos_info['id']+'</td> \
                        <td>'+pos_info['vendor_code']+'</td> \
                        <td>'+pos_info['title']+'</td> \
                        <td class="quant"><span style="display: none;">'+pos_info['quant_robot']+'</span><input type="text" class="form-control quant_inp"  style="position: relative; height: 20px; width: 55px; text-align: center;" placeholder="'+pos_info['quant_robot']+'"></td> \
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
      
     var id = $(this).attr('id');
      $.post("./api.php", {
 			action: "del_pos_option",
 			id: <?php echo $option_id; ?>,
 			id_row: id
 		}).done(function(data) {
 			
            window.location.reload(true);
           
 		
 		});
 		
 		

   
   });  
 
 
 function save_close() {
    $(this).prop('disabled', true);
    var id =  $("#option_id").text();
    var title =  $("#title").val();
    var kit =  $("#kit").val();
   
       
       	$.post("./api.php", {
 			action: "edit_option",
 			id: id,
 			title: title
 		}).done(function(data) {
 			console.log(data);
 			window.location.href = "./options.php";
 			return false;
 		});
       
       
       return false;
 }

 function save_new() {
     
     return false;
 }
 
  

  });



 

	</script>
</body>
</html>