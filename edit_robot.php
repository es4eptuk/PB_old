<?php 
include 'include/class.inc.php';

$id = $_GET['id'];
$robot_info = $robots->get_info_robot($id);
$robot_version = $robot_info['version'];
$robot_number = $robot_info['number'];
$robot_name = $robot_info['name'];
$robot_customer = $robot_info['customer'];
$robot_language_robot = $robot_info['language_robot'];
$robot_language_doc = $robot_info['language_doc'];
$robot_charger = $robot_info['charger'];
$robot_color = $robot_info['color'];
$robot_brand = $robot_info['brand'];
$robot_ikp = $robot_info['ikp'];
$robot_battery = $robot_info['battery'];
$robot_dop = $robot_info['dop'];
$robot_dop_manufactur = $robot_info['dop_manufactur'];
$robot_photo = $robot_info['photo'];
$robot_termo = $robot_info['termo'];
$robot_dispenser = $robot_info['dispenser'];
$robot_terminal = $robot_info['terminal'];
$robot_kaznachey = $robot_info['kaznachey'];
$robot_lidar = $robot_info['lidar'];
$robot_other = $robot_info['other'];
$robot_progress = $robot_info['progress'];
$robot_date = $robot_info['date'];
$robot_date = new DateTime($robot_date);
$robot_date = $robot_date->format('d.m.Y');
$robot_test = $robot_info['date_test'];
$robot_test = new DateTime($robot_test);
$robot_test = $robot_test->format('d.m.Y');
?>
<?php include 'template/head.html' ?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<? include 'template/header.html' ?>
		<!-- Left side column. contains the logo and sidebar -->
		<?php include 'template/sidebar.html';?>
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>Роботы</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Редактирование робота <? echo $robot_version.".".$robot_number; ?></h3>
							</div><!-- /.box-header -->
							<div class="box-body">
						 
                <!-- text input -->
                <!-- select -->
                

                 <div class="form-group">
                  <label>Версия</label>
                  <select class="form-control" name="version" id="version">
                      
                      <? 
                        $array_version = [
                                                                        4 => 4,
                                                                        3 => 3,
                                                                        2 => 2
                                                                        
                                                                    ];
                                                                    
                         foreach ($array_version as $key => $value) { 
								if ( $key == $robot_version ) {  
                                    echo "<option value='".$key."' selected>".$value."</option>";
									} else {
									echo "<option value='".$key."'>".$value."</option>"; 
											}
									}                                            
                      
                      
                      ?>
                    
                  </select>
                </div>
                
                 <div class="form-group">
                  <label>Номер робота</label></label>
                  <input type="text" class="form-control" name="number" required="required" id="number" value="<? echo $robot_number ?>">
                </div>
                <div class="form-group">
                  <label>Кодовое имя</label>
                  <input type="text" class="form-control" name="name" id="name" value="<? echo $robot_name ?>">
                </div>
                <div class="form-group">
                  <label>Заказчик <small>(<a href="#" data-toggle="modal" data-target="#add_customer">Добавить</a>)</small></label>
                  <select class="form-control" name="customer" id="customer">
                      <option value="0"></option>
                    <?php 
                   $arr = $robots->get_customers();
                
                    foreach ($arr as &$customer) {
                        
                        if ($customer['id']==$robot_customer) {
                       echo "
                       <option value='".$customer['id']."' selected>".$customer['name']."</option>
                       ";
                        } else {
                             echo "
                       <option value='".$customer['id']."'>".$customer['name']."</option>
                       ";
                            
                        }
                    }
                   
                   ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Язык на роботе</label>
                  <select class="form-control" name="language_robot" id="language_robot">
                      
                       <? 
                        $language_robot = [
                                            "russian" => "Русский",
                                            "english" => "Английский",                          
                                             "spanish" => "Испаниский",
                                              "turkish" => "Турецкий",
                                               "arab" => "Арабский",
                                                "portuguese" => "Португальский",
                                                 "german" => "Немецкий"
                                                                    ];
                                                                    
                         foreach ($language_robot as $key => $value) { 
								if ( $key == $robot_language_robot ) {  
                                    echo "<option value='".$key."' selected>".$value."</option>";
									} else {
									echo "<option value='".$key."'>".$value."</option>"; 
											}
									}                                            

                      ?>
                      
                      
                   
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Язык инструкции</label>
                  <select class="form-control" name="language_doc" id="language_doc">
                     <? 
                        $language_doc = [
                                            "russian" => "Русский",
                                            "english" => "Английский"                          
                                             
                                                                    ];
                                                                    
                         foreach ($language_doc as $key => $value) { 
								if ( $key == $robot_language_doc ) {  
                                    echo "<option value='".$key."' selected>".$value."</option>";
									} else {
									echo "<option value='".$key."'>".$value."</option>"; 
											}
									}                                            

                      ?>
                    
                    
                  </select>
                </div>
                
                 <div class="form-group">
                  <label>Напряжение зарядной станции</label>
                  <select class="form-control" name="charger" id="charger">
                   
                   <? 
                        $charger= [
                                            "220" => "220",
                                            "110" => "110"                          
                                             
                                                                    ];
                                                                    
                         foreach ($charger as $key => $value) { 
								if ( $key == $robot_charger ) {  
                                    echo "<option value='".$key."' selected>".$value."</option>";
									} else {
									echo "<option value='".$key."'>".$value."</option>"; 
											}
									}                                            

                      ?>
                   
                   
                    
                  </select>
                </div>
                
                
                
                <div class="form-group">
                    <label for="exampleInputFile">Комплектация</label>
                    
                    <?
                    $options = $robots->get_robot_options($id);
                    
                    foreach ($options as &$value) {
                        $check = ($value['check'] ==1) ? "checked" : "";
                            echo '<div class="checkbox">
                    <label>
                      <input type="checkbox" class="check" id="'.$value['id'].'"  '.$check.' name="options" value=  '.$value['id'].'>
                      '.$value['title'].'
                    </label>
                  </div>';
                        }
                    
                    ?>
                    
                  

                  
                  
                </div>
                
                <div class="form-group">
                  <label>Цвет</label>
                  <input type="text" class="form-control" name="color" id="color" value="<? echo $robot_color ?>">
                </div>
                
                <div class="form-group">
                  <label>Брендирование </label>
                  <input type="text" class="form-control" name="brand" id="brand" value="<? echo $robot_brand ?>">
                </div>
                
                <div class="form-group">
                  <label>ИКП</label>
                  <input type="text" class="form-control" name="ikp" id="ikp" value="<? echo $robot_ikp ?>">
                </div>
                
                 <div class="form-group">
                  <label>Наличие АКБ</label>
                  <select class="form-control" name="battery" id="battery">
                      
                       <? 
                        $battery= [
                                            1 => "Да",
                                            0 => "Нет"                          
                                             
                                                                    ];
                                                                    
                         foreach ($battery as $key => $value) { 
								if ( $key == $robot_battery ) {  
                                    echo "<option  value='".$key."' selected>".$value."</option>";
									} else {
									echo "<option  value='".$key."'>".$value."</option>"; 
											}
									}                                            

                      ?>
                      
                      
                   
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Дополнительная информация</label>
                  <input type="text" class="form-control" name="dop" id="dop" value="<? echo $robot_dop ?>">
                </div>


                                <div class="form-group">
                                    <label>Информация от производства</label>
                                    <textarea rows="5" cols="45" class="form-control" name="dop_manufactur" id="dop_manufactur"><? echo $robot_dop_manufactur ?></textarea>
                                </div>
                
                <div class="checkbox">
                    <label>
                      <input type="checkbox" id="send" <? echo ($robot_progress ==100) ? "checked" : ""; ?>>
                      Отправленный
                    </label>
                  </div>
                  
                  <div class="form-group">
                <label>Начало производства:</label>

                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="datepicker" class="datepicker" value="<? echo $robot_date ?>">
                </div>
                <!-- /.input group -->
              </div> 
              
               <div class="form-group">
                <label>Первый тест:</label>

                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="datepicker2" class="datepicker" value="<? echo $robot_test ?>">
                </div>
                <!-- /.input group -->
              </div> 
                
                <div id="update"></div>
                
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="save_close" name="" >Сохранить</button>
                    <button type="button" class="btn btn-primary btn-danger pull-right" id="delete" name="">Удалить</button>
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
									ЗАО
								</option>
								<option value="Ltd.">
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
    <script src="../../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

	<script>
	 //Date picker
    $('#datepicker').datepicker({
      format: 'dd.mm.yyyy',
     
      autoclose: true
    })
    
    $('#datepicker2').datepicker({
      format: 'dd.mm.yyyy',
       
      autoclose: true
    })
    
$(document).ready(function() { 	
var arr_str = [];
var arr_ids = [];
var arr_pos = [];
var pos_info = [];
var category_data = [];
var category1 = "---";

  $(".check").change(function() {
    var id = $(this).attr("id"); 
    var robot = <? echo $id; ?>
      
    if(this.checked) {
        val = 1;
    } else {
        val = 0;
    }
    
     $.post( "./api.php", { 
        action: "add_option_check",
        robot: robot,
        id: id,
        value: val
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                //window.location.href = "./robots.php";
              }
          });
    
    console.log(id);
      
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
 
  
 $("#delete").click(function() {
     $(this).last().addClass( "disabled" );
 $.post( "./api.php", { 
        action: "delete_robot", 
        id: <? echo $id ?>
        
        
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./robots.php";  
              }
          });
 });
 
 
  $("#save_close").click(function() {
      var options = [];
      $(this).last().addClass( "disabled" );
  var number =  $('#number').val();
    var name =  $('#name').val();
    var version =  $('#version').val();
    var photo =  $('#photo').is(':checked') ? 1 : 0;
    var termo =  $('#termo').is(':checked') ? 1 : 0;
    var dispenser =  $('#dispenser').is(':checked') ? 1 : 0;
    var terminal =  $('#terminal').is(':checked') ? 1 : 0;
    var kaznachey =  $('#kaznachey').is(':checked') ? 1 : 0;
    var lidar =  $('#lidar').is(':checked') ? 1 : 0;
    var other =  $('#other').is(':checked') ? 1 : 0;
    var customer =  $('#customer').val();
    var language_robot =  $('#language_robot').val();
    var language_doc =  $('#language_doc').val();
    var charger =  $('#charger').val();
    var color =  $('#color').val();
    var brand =  $('#brand').val();
    var ikp =  $('#ikp').val();
    var battery =  $('#battery').val();
      var dop =  $('#dop').val();
      var dop_manufactur =  $('#dop_manufactur').val();

    var date = $('#datepicker').val();
    var date_test = $('#datepicker2').val();
    var send =  $('#send').is(':checked') ? 1 : 0;
    
   $('input[name=options]').each(function () {
           if (this.checked) {
                options.push($(this).val());
           }
    });
    
   console.log(options);
    
      $.post( "./api.php", { 
        action: "edit_robot", 
        id: <? echo  $id ?>,
        number: number,
        version: version,
        name: name,
        options: options,
        customer: customer,
        language_robot: language_robot,
        language_doc: language_doc,
        charger: charger,
        color: color,
        brand: brand,
        ikp: ikp,
        battery: battery,
        dop: dop,
          dop_manufactur: dop_manufactur,
        date: date,
        date_test: date_test,
        send: send
        
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./robots.php";
              }
          });
 });
 
 


 function save_new() {
     
     return false;
 }
 

  


  });

	</script>
</body>
</html>