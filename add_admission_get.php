<?php 
include 'include/class.inc.php';
?>
<?php include 'template/head.php' ?>

<?php 
	$info_order = $orders->get_info_order($_GET['id']);
	//print_r($info_order);
	$category_id = $info_order['order_category'];
	$category_name = $position->get_name_pos_category($category_id);
	$provider_id = $info_order['order_provider'];
	$provider_name = $position->get_info_pos_provider($provider_id);

		$provider_name = 	$provider_name['title'];

?>

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
										<label>Категория:</label> 
										
										<span id="catregory">
										    
										   <?php echo $category_name;?>
										    
										</span>
									</div>
									<div class="form-group">
										<label>Поставщик: </label> 
										
										<span id="provider">
										    
										     
										   <?php echo $provider_name;?>
										    
										</span>
									</div>
								    <div class="form-group">
										<label>Заказ № </label> 
										
										<span id="order"><?php echo $_GET['id']; ?></span>
									</div>
									
								   
									
									<table class="table table-hover" id="listPos">
                                    <tbody><tr>
                                      <th>ID</th>
                                      <th>Артикул</th>
                                      <th>Наименование</th>
                                      <th>Заказанное количество</th>
                                      <th>Отгружено</th>
                                      <th>Поступление</th>
                                        <th>  </th>
                                        <?php if ($provider_id==49 || $provider_id==1) {

                                            echo " <th>Покраска</th>";
                                            echo " <th>Сварка/Зенковка</th>";
                                        } ?>

                                    </tr>
                                   
                                   <?php 
                                    $arr_pos = $orders->get_pos_in_order($_GET['id']);
                                    
                                foreach ($arr_pos as &$value) { 
                                    $date = new DateTime($value['pos_date']);
                                    $pos_date = $date->format('d.m.Y');
                                    echo '   
                                        <tr class="table_tr"> 
                            <td>'.$value['id'].'</td> 
                            <td>'.$value['vendor_code'].'</td> 
                            <td>'.$value['title'].'</td> 
                            <td class="quant tot">'.$value['pos_count'].'</td> 
                            <td class="finish">'.$value['pos_count_finish'].'</td> 
                            <td class="quant inp_tot"><span>0</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="0"></td> 
                            <td></td>
                             ';
                                    if ($provider_id==49 || $provider_id==1) {
                                        echo '   <td class="quant"><span>0</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="0" id="quant_'.$value['id'].'"></td> ';
                                        echo '   <td class="quant"><span>0</span><input type="text" class="form-control quant_inp"  style="position: relative; top: -20px; width: 55px; text-align: center;" placeholder="0" id="quant_'.$value['id'].'"></td> ';
                                    }

                       echo '</tr>' ;
                                }
                                    ?>
                                    
                                    </tbody>
                                    </table>
									
									<div class="box-footer">
                                        <button class="btn btn-primary" id="auto" >Автозаполнение</button>
                                        <button class="btn btn-primary" id="save_close" >Сохранить и закрыть</button>
										<button class="btn btn-primary" id="save_new" >Сохранить и создать новое поступление</button>
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

 $("#save_close").click(function() {
 	save_close();
 	return false;
 });
 
 $("#save_new").click(function() {
 	save_new();
 	return false;
 });


    $("#auto").click(function() {

        $( ".table_tr" ).each(function( index ) {
           var val = $( this ).find(".tot").text();
           $( this ).find(".inp_tot span").text(val);
            $( this ).find(".inp_tot .quant_inp").val(val);


           console.log(val);
        });

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
     var id =  <?php echo $_GET['id'] ?> ;
     var category =  <?php echo $category_id ?> ;
     var provider =   <?php echo $provider_id ?> ;
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
      var id =  <?php echo $_GET['id'] ?> ;
     var category =  <?php echo $category_id ?> ;
     var provider =   <?php echo $provider_id ?> ;
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

	</script>
</body>
</html>