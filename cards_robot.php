<?php 
include 'include/class.inc.php';

 $arr = $robots->get_robots();
 //$paramRobot = (isset($_GET['robot']) ? $_GET['robot'] : 0);

                $arr_tickets = $tickets->get_tickets();
                $finish = array();
                $inprocess = array();
                $remont = array();
                $process = array();
                $wait = array();
                $robot_problem = 0;
                $open_tickets = 0;
                $remont_tickets = 0;
                $process_tickets = 0;
                
                foreach ($arr_tickets as &$ticket) {
                    $ticket_status = $ticket['status'];
                    $ticket_robot = $ticket['robot'];
                    
                    if ($ticket_status==3 || $ticket_status==6) {
                       // if(isset($finish[$ticket_robot])) {$finish[$ticket_robot]}
                        $finish[$ticket_robot] = isset($finish[$ticket_robot] ) + 1;
                    } 
                    
                    if ($ticket_status==1 || $ticket_status==2 || $ticket_status==4 || $ticket_status==5) {
                       $inprocess[$ticket_robot] = isset($inprocess[$ticket_robot] ) + 1;
                       $open_tickets++;
                    }
                    
                    if ($ticket_status==4) {
                       $remont[$ticket_robot]['count'] = isset($remont[$ticket_robot] ) + 1;
                        $date_finish = new DateTime($ticket['finish_date']);
                       $remont[$ticket_robot]['date'] = $date_finish->format('d.m.Y');
                       $remont_tickets++;
                       
                    }
                    
                    if ($ticket_status==2) {
                       $process[$ticket_robot] = isset($process[$ticket_robot] ) + 1;
                       $process_tickets++;
                       
                    }
                    
                    
                     if ($ticket_status==7 ) {
                       $wait[$ticket_robot] = isset($wait[$ticket_robot] ) + 1;
                    }
                }
                //print_r($finish);
                //print_r($inprocess);

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
        
       <div class="box box-info">
            <div class="box-header">
              <h3 class="box-title">Статистика</h3>
            </div>
            <div class="box-body">
             
             <div class="col-xs-6">
          <p class="lead">На сегодня <?php echo date("d.m.Y"); ?></p>

          <div class="table-responsive">
            <table class="table">
              <tbody><tr>
                <th style="width:50%">Открытых тикетов:</th>
                <td><?php echo array_sum($inprocess); ?>
                </td>
              </tr>
              <tr>
                <th>Проблемных роботов:</th>
                <td class="dop"><?php echo count($inprocess);?> <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                <div class="robots" style="display: none">
                    <ul>
                    
                    <?php 
                    $count = 0;
                     foreach ($inprocess as $key => $value) {
                         $robot_info = $robots->get_info_robot($key);
                         $robot_info['number'] = str_pad($robot_info['number'], 4, "0", STR_PAD_LEFT); 
                         $number = $robot_info['version'].".".$robot_info['number'];
                         $inprocess_sort[$count]['number'] = $robot_info['version'].".".$robot_info['number'];
                         $inprocess_sort[$count]['id'] = $key;
                         $count++;
                     }
                     
                     function cmp($a, $b)
                        {
                            return strcmp($a["number"], $b["number"]);
                        }
                        
                      usort($inprocess_sort, "cmp");
                       foreach ($inprocess_sort as $key => $value) {
                         echo "<li><a href='./robot_card.php?id=".$value['id']."'  >".$value['number']."</a></li>";
                     }
                    ?>
                  </ul>   
                  </div>
                </td>
              </tr>
              <tr>
                <th>В процессе решения:</th>
                <td class="dop"><?php echo $process_tickets;?> <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                 <div class="robots" style="display: none">
                    <ul>
                    <?php 
                     foreach ($process as $key => $value) {
                         $robot_info = $robots->get_info_robot($key);
                         $robot_info['number'] = str_pad($robot_info['number'], 4, "0", STR_PAD_LEFT); 
                         $number = $robot_info['version'].".".$robot_info['number'];
                         echo "<li><a href='./robot_card.php?id=".$key."'>".$number." (".$value.")</a></li>";
                     }
                    ?>
                  </ul>  
                   </div> 
                </td>
              </tr>
              <tr>
                <th>Ожидают ремонта:</th>
                <td class="dop"> <?php echo count($remont);?> <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                <div class="robots" style="display: none">
                    <ul>
                    <?php 
                    //print_r($remont);
                     foreach ($remont as $key => $value) {
                         $robot_info = $robots->get_info_robot($key);
                         $robot_info['number'] = str_pad($robot_info['number'], 4, "0", STR_PAD_LEFT); 
                         $number = $robot_info['version'].".".$robot_info['number'];
                         $date_color = "";
                         if ($value['date']==date("d.m.Y")) {
                             $date_color = "text-yellow";
                         }
                         echo "<li><a href='./robot_card.php?id=".$key."'>".$number." (".$value['count'].")</a> - <span class='".$date_color."'>".$value['date']."</span></li>";
                     }
                    ?>
                  </ul>  
                   </div>
                </td>
              </tr>
            </tbody></table>
          </div>
        </div>
        <div class="col-xs-6">
         <p class="lead">За вчера</p>
<?php 
 $date_min = date("Y-m-d", strtotime("yesterday"));
 $date_max = date("Y-m-d");
 
 $date_minPr = date("Y-m-d", time() - 86400*1);
 $date_maxPr = date("Y-m-d", time() - 86400*1);
 
 $countNew24Pr = 0;
 $countNew24 = 0;
 $countResh24 = 0;
 $arr_new24 = $tickets->get_tickets(0,0,0,"update_date","DESC","date_create",$date_min,$date_max,"P");
 $arr_new24_sort = [];
 $arrTicketRobotNoProblem = [];
 //print_r($arr_new24);
   if (isset($arr_new24)){

  foreach ($arr_new24 as &$value) {
        $arr_new24_sort[] = $value['robot'];
        //echo $value['robot']." ";
    }
   $arr_new24_sort =  array_unique($arr_new24_sort);
  
   $countNew24 = count($arr_new24_sort);
  }
 
 
 $arr_new24Pr = $tickets->get_tickets(0,0,0,"update_date","DESC","date_create",$date_minPr,$date_maxPr,"P");
 
  if (isset($arr_new24Pr)){
  
  foreach ($arr_new24Pr as &$value) {
        $arr_new24Pr_sort[] = $value['robot'];
        //echo $value['robot']." ";
    }
   $arr_new24Pr_sort =  array_unique($arr_new24Pr_sort);
   $countNew24Pr = count($arr_new24Pr_sort);
  }
  


 $rNew = $countNew24-$countNew24Pr;



 $arr_Resh24 = $tickets->get_tickets(0,0,0,"update_date","DESC","inwork",$date_minPr." 00:00:00",$date_maxPr." 23:59:59","P");
 //print_r($arr_Resh24);
 if (isset($arr_Resh24)) {
  foreach ($arr_Resh24 as &$value) {
       
       
            $robot_info = $robots->get_info_robot($value['robot']);
            $robot_info['number'] = str_pad($robot_info['number'], 4, "0", STR_PAD_LEFT); 
            $number = $robot_info['version'].".".$robot_info['number'];
            $arrTicketRobotNoProblem[$value['robot']] = $number;
        
    }
    $countResh24 = count($arr_Resh24);
   // print_r($arr_Resh24);
 }   

 /*$arr_new24 = $tickets->get_tickets(0,0,0,"update_date","DESC","date_create",$date_min,$date_max);
 $arr_new24Pr = $tickets->get_tickets(0,0,0,"update_date","DESC","date_create",$date_minPr,$date_maxPr);
 $countNew24 = count($arr_new24);
 $countNew24Pr = count($arr_new24Pr);
 
 $procNew = $countNew24Pr*100/$countNew24;
 
 $arr_finish24 = $tickets->get_tickets(0,0,3,"update_date","DESC","update_date",$date_min,$date_max);
 $arr_finish24Pr = $tickets->get_tickets(0,0,3,"update_date","DESC","update_date",$date_minPr,$date_maxPr);
 $countfinish24 = count($arr_finish24);
 $countfinish24Pr = count($arr_finish24Pr);
 
 $procFinish = $countfinish24Pr*100/$countfinish24;

*/
// $arr_finish24 = $tickets->get_tickets(0,0,3,"update_date","DESC",$date_min,$date_max);
// $arr_process24 = $tickets->get_tickets(0,0,3,"update_date","DESC",$date_min,$date_max);
?>
          <div class="table-responsive">
            <table class="table">
              <tbody><tr>
                <th style="width:50%">Проблемных роботов:</th>
                <td class="dop"><?php echo $countNew24." (".($rNew < 0 ? '' : '+').$rNew.")";
                ?>
                <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                <div class="robots" style="display: none">
                    <ul>
                    
                    <?php 
                    $count=0;
                    //print_r($remont);
                     foreach ($arr_new24_sort as $value) {
                        $robot_info = $robots->get_info_robot($value);
                        $robot_info['number'] = str_pad($robot_info['number'], 4, "0", STR_PAD_LEFT); 
                        $number = $robot_info['version'].".".$robot_info['number'];
                        echo "<li><a href='./robot_card.php?id=".$value."'>".$number."</a></li>";
                     }
                   
                    ?>
                  </ul>  
                   </div>
                
                </td>
              </tr>
              
              <tr>
                <th style="width:50%">Исправленных роботов:</th>
                <td class="dop"><?php echo $countResh24 ;
                ?> <i class="fa fa-fw fa-plus-circle pull-right text-green" style="cursor: pointer;"></i>
                 <div class="robots" style="display: none">
                    <ul>
                    <?php 
                    //print_r($remont);
                     foreach ($arrTicketRobotNoProblem as $key => $value) {
                         echo "<li><a href='./robot_card.php?id=".$key."'>".$value."</a></li>";
                     }
                    ?>
                  </ul>  
                   </div>
                
                
                </td>
              </tr>
              
             
             
            </tbody></table>
          </div>
        </div>
              
            </div>
            <!-- /.box-body -->
          </div>
        
        
        
        
       
        
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">

            <!-- /.box-header -->
            <div class="box-body table-responsive">
              
              
                
              <table id="robots" class="table  table-hover">
                <thead>
                <tr>
                  
                  <th>Номер</th>
                  <th style="width: 15%;">Кодовое имя</th>
                  
                  <th>Тикеты</th>
                  <th>Изменен</th>
                  <th>Кем</th>
                  
                </tr>
                </thead>
                <tbody>
                <?php 

                if (isset($arr)) {
                foreach ($arr as &$robot) {
                    
                    if ($robot['progress']==100 ) {
                    $robot_id = $robot['id'];
                    $color = "#fff";
                    $user_info = $user->get_info_user($robot['update_user']);
                    $robot_date = new DateTime($robot['update_date']);
                    $finish_ticket = "";
                    $inprocess_ticket = "";
                    $wait_ticket = "";
                    
                    if (isset($finish[$robot_id])) {$finish_ticket = '<small class="label bg-green">'.$finish[$robot_id].'</small>';   }
                    if (isset($inprocess[$robot_id])) {$inprocess_ticket = '<small class="label bg-red">'.$inprocess[$robot_id].'</small>';   }
                    if (isset($wait[$robot_id])) {$wait_ticket = '<small class="label bg-yellow">'.$wait[$robot_id].'</small>';   }
                    //$inprocess_ticket = 
                    $robot['number'] = str_pad($robot['number'], 4, "0", STR_PAD_LEFT); 
                    echo "
                    <tr class='edit' id='".$robot['id']."' style='cursor: pointer; background: ".$color.";'>
                     
                        
                        <td>".$robot['version'].".".$robot['number']."</td>
                        <td>".$robot['name']."</td>
                        <td>$finish_ticket $inprocess_ticket $wait_ticket</td>
                        <td>".$robot_date->format('d.m.Y H:i:s')."</td>
                        <td>".$user_info['user_name']." </td>
                       

                    </tr>
                       
                       
                       ";
                    }
                }
                }
               
                ?>
              </table>
              
              <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="btn_add_robot">Добавить робота</button>
                </div>
                
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
                <div class="form-group ">
                  <label>Заказчик <small>(<a href="#" data-toggle="modal" data-target="#add_customer">Добавить</a>)</small></label>
                  <select class="form-control select2" name="customer" id="customer" style="width: 100%;">
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
                
                
                
                <div class="form-group" style="display: none">
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
<script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
<script>


 $('.select2').select2();
 
 $( ".edit" ).click(function() {
   var id = $(this).attr("id");
   
     window.location.href = "./robot_card.php?id="+id;

    });
    
    $( ".dop" ).click(function() {
   $(this).find(".robots").toggle( "slow" );
    });
    
    
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
        send: send
        
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./cards_robot.php";
                
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
    
 $( "#robots tbody" ).sortable({
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
