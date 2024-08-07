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
       Роботы
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">в производстве</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                
                
              <table id="robots" class="table  table-hover">
                <thead>
                <tr>
                  
                  <th>Номер</th>
                  <th style="width: 15%;">Кодовое имя</th>
                  <th>Материалы</th>
                   <th>%</th>
                  
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $robots->get_robots();
                $output = "";
                $count_writeoff['4'] = 0;
                $count_writeoff['2'] = 0;
                $count_nowriteoff['4'] = 0;
                $count_nowriteoff['2'] = 0;
                $count['4'] = 0;
                $count['2'] = 0;
                
                if (isset($arr)) {
                foreach ($arr as &$robot) {
                     $num = str_pad($robot['number'], 4, "0", STR_PAD_LEFT);  
                     if ($robot['progress']!=100) {
                     $version = $robot['version'];
                     $count[$version]++;
                    if($robot['writeoff']==0) {$robot['writeoff'] = "Не списаны";  $count_nowriteoff[$version]++;} else  {$robot['writeoff'] = "Списаны"; $count_writeoff[$version]++;}
                       
                        
                    $output.= "
                    <tr>
                        <td>".$robot['version'].".".$num."</td>
                        <td>".$robot['name']."</td>
                        <td>".$robot['writeoff']."</td>
                        <td>".$robot['progress']."</td>
                    </tr>
                       ";
                    }
                    }
                }
               echo "<p>Всего в работе В2: ".$count['2']." из них списано <b>".$count_writeoff['2']." </b></p>" ;
               echo "<p>Всего в работе В4: ".$count['4']." из них списано <b>".$count_writeoff['4']." </b></p>" ;
               echo $output;
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
<!-- ./wrapper -->

<!-- Modal -->
<div class="modal fade" id="add_robot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Добавление робота<span id="operation_id"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          
          <form role="form" data-toggle="validator" id="add_pos">
                <!-- text input -->
                <!-- select -->
                

                 <div class="form-group">
                  <label>Версия</label>
                  <select class="form-control" name="version" id="version">
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                  </select>
                </div>
                
                 <div class="form-group">
                  <label>Номер робота</label></label>
                  <input type="text" class="form-control" name="number" required="required" id="number">
                </div>
                <div class="form-group">
                  <label>Кодовое имя</label>
                  <input type="text" class="form-control" name="name" id="name">
                </div>
                <div class="form-group">
                  <label>Заказчик <small>(<a href="#" data-toggle="modal" data-target="#add_customer">Добавить</a>)</small></label>
                  <select class="form-control" name="customer" id="customer">
                      <option value="0"></option>
                    <?php 
                   $arr = $robots->get_customers();
                
                    foreach ($arr as &$customer) {
                       echo "
                       <option value='".$customer['id']."'>".$customer['name']."</option>
                       
                       ";
                    }
                   
                   ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Язык на роботе</label>
                  <select class="form-control" name="language_robot" id="language_robot">
                    <option value="russian">Русский</option>
                    <option value="english">Английский</option>
                    <option value="spanish">Испаниский</option>
                    <option value="turkish">Турецкий</option>
                    <option value="arab">Арабский</option>
                    <option value="portuguese">Португальский</option>
                    <option value="german">Немецкий</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Язык инструкции</label>
                  <select class="form-control" name="language_doc" id="language_doc">
                    <option value="russian">Русский</option>
                    <option value="english">Английский</option>
                  </select>
                </div>
                
                 <div class="form-group">
                  <label>Напряжение зарядной станции</label>
                  <select class="form-control" name="charger" id="charger">
                    <option value="220">220</option>
                    <option value="110">110</option>
                  </select>
                </div>
                
                
                
                <div class="form-group">
                    <label for="exampleInputFile">Комплектация</label>
                    
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="photo">
                      Фотопринтер
                    </label>
                  </div>

                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="termo">
                      Чековый принтер
                    </label>
                  </div>

                  <div class="checkbox">
                    <label>
                      <input type="checkbox"  id="dispenser">
                      Диспенсер
                    </label>
                  </div>
                  
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="terminal">
                      Подготовка под терминал
                    </label>
                  </div>
                  
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="kaznachey">
                      Казначей
                    </label>
                  </div>
                  
                 
                  
                  
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="lidar">
                      LiDAR + IMU
                    </label>
                  </div>
                  
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="other">
                      Другое
                    </label>
                  </div>
                  
                </div>
                
                <div class="form-group">
                  <label>Цвет</label>
                  <input type="text" class="form-control" name="color" id="color">
                </div>
                
                <div class="form-group">
                  <label>Брендирование </label>
                  <input type="text" class="form-control" name="brand" id="brand">
                </div>
                
                <div class="form-group">
                  <label>ИКП</label>
                  <input type="text" class="form-control" name="ikp" id="ikp">
                </div>
                
                 <div class="form-group">
                  <label>Наличие АКБ</label>
                  <select class="form-control" name="battery" id="battery">
                    <option value="1">Да</option>
                    <option value="0">Нет</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Дополнительная информация</label>
                  <input type="text" class="form-control" name="dop" id="dop">
                </div>
                
                <div class="checkbox">
                    <label>
                      <input type="checkbox" id="send">
                      Отправленный
                    </label>
                  </div>
                  
                    <div class="form-group">
                <label>Начало производства:</label>

                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" class="datepicker" id="datepicker" value="<?php echo $robot_date->format('d.m.Y') ?>">
                </div>
                <!-- /.input group -->
              </div> 
              
              <div class="form-group">
                <label>Первый тест:</label>

                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" class="datepicker" id="datepicker2" value="<?php echo $robot_date_test->format('d.m.Y') ?>">
                </div>
                <!-- /.input group -->
              </div> 
                
                <div id="update"></div>
                
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="save_close" name="">Сохранить</button>
                    <button type="button" class="btn btn-primary btn-danger pull-right" id="Close" name="">Закрыть</button>
                </div>
              </form>
         
      </div>
      
    </div>
  </div>
</div>

	<div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="add_customer" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавить клиента</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<form data-toggle="validator" id="add_provider_form" name="add_provider_form" role="form">
						<!-- select -->
					
						<div class="form-group">
							<label>Наименование</label> <input class="form-control" id="name_cust" name="name_cust" required="required" type="text">
						</div>
						
						<div class="form-group">
							<label>ФИО</label> <input class="form-control" id="fio" name="fio" required="required" type="text">
						</div>
						
						<div class="form-group">
							<label>Email</label> <input class="form-control" id="email" name="email" required="required" type="text">
						</div>
						
						<div class="form-group">
							<label>Телефон</label> <input class="form-control" id="phone" name="phone" required="required" type="text">
						</div>
						
						<div class="form-group">
							<label>Адрес</label> <input class="form-control" id="address" name="address" required="required" type="text">
						</div>
						
					</form>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> 
					<button class="btn btn-primary" id="btn_add_customer" type="button">Добавить</button>
				</div>
			</div>
		</div>
	</div>
	


<?php include 'template/scripts.php';?>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="../../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script>

 //Date picker
    $('.datepicker').datepicker({
      format: 'dd.mm.yyyy',
      startDate: '-3d',
      autoclose: true
    })
    
 $("#btn_add_customer").click(function() {
 	var name_cust = $('#name_cust').val();
 	var fio = $('#fio').val();
 	var email = $('#email').val();
 	var phone = $('#phone').val();
 	var address = $('#address').val();
 	//alert("123");
 	
 	if (name_cust != "") {
 		$.post("./api.php", {
 			action: "add_customer",
 			name: name_cust,
 			fio: fio,
 			email: email,
 			phone: phone,
 			address: address
 		}).done(function(data) {
 			console.log(data);
 			if (data == "false") {
 				alert("Data Loaded: " + data);
 				return false;
 			} else {
 				$('#customer').append("<option value='" + data + "' selected>" + name_cust + "<\/option>");
 				$('#add_customer').modal('hide');
 				//return false;
 			}
 		});
 	}
 });

 $( ".fa-align-justify" ).click(function() {
   var id = $(this).attr("id");
   
     window.location.href = "./robot.php?id="+id;

    });
    
    
 $( "#btn_add_robot" ).click(function() {
   
    $('#add_robot').modal('show');
     

    });    
    
 $( "#save_close" ).click(function() { 
   save_close();  
  return false;
  }); 
  
  
  function save_close() {
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
    var send =  $('#send').is(':checked') ? 1 : 0;
    var date = $('#datepicker').val();
    var date_test = $('#datepicker2').val();
    
      $.post( "./api.php", { 
        action: "add_robot", 
        number: number,
        version: version,
        name: name,
        photo: photo,
        termo: termo,
        dispenser: dispenser,
        terminal: terminal,
        kaznachey: kaznachey,
        lidar: lidar,
        other: other,
        customer: customer,
        language_robot: language_robot,
        language_doc: language_doc,
        charger: charger,
        color: color,
        brand: brand,
        ikp: ikp,
        battery: battery,
        dop: dop,
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
    
 }
 
 $('#check_show_all').change(function() {
        if($(this).is(":checked")) {
            //var returnVal = confirm("Are you sure?");
            $(this).attr("checked", true);
        }
       // alert($(this).is(':checked')); 
        $("#show_all").submit();
        
    });
    
 $( "#robots1 tbody" ).sortable({
  stop: function( event, ui ) {
       var arr_robot = [];
       var id_ = 'robots tbody';
       var cols_ = document.querySelectorAll('#' + id_ + ' tr');
       
        $.each( cols_, function( key, value ) {
        var idd = $(value).attr('id');
        arr_robot.push(idd);
       
        });
        
    JSON.stringify(arr_robot);  
    
     $.post( "./api.php", { 
        action: "sortable", 
        json: arr_robot
        
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                //window.location.href = "./robots.php";
                
              }
          });
    
    
    console.log(arr_robot);
       
       

      
  },
  connectWith: ".connectedSortable"
}).disableSelection();  

 $('#robots').DataTable({
       "iDisplayLength": 100,
        "order": [[ 0, "desc" ]]
    } );
    

</script>
</body>
</html>
