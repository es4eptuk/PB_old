<?php 
include 'include/class.inc.php';

if (isset($_GET['month'])) $id_month = $_GET['month'];
if (isset($_GET['status'])) $id_status = $_GET['status'];


?>

<?php include 'template/head.php' ?>
<style>
    .not_ordered  {
        color: #de4e4e;
    }
    
    .ordered  {
        color: #d0d058;
    }
    
    .adopted  {
        color: #008000;
    }
    
    
    
</style>

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
       План
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $position->get_name_category($_GET['id']) ;
             
             // echo $plan->get_ordered_items(65);
              ?></h3>
            </div>
            <!-- /.box-header -->
            
           
            
            <div class="box-body table-responsive">
                <div class="margin"> 
                    <div class="btn-group">
                      <button type="button" class="btn btn-default">Версия робота</button>
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu">

                          <?php $arr_eq = $robots->getEquipment;
                          foreach ($arr_eq as $eq) {
                              echo '<li><a href="plan.php?id='.$_GET['id'].'&version='.$eq['id'].'">'.$eq['title'].'</a></li>';
                          }


                          ?>


                       
                      </ul>
                    </div>
               
                
               <br><br>
              <dl>
                  <?php 
                   $arr_robot = $plan->get_robot_inprocess();
                    ksort($arr_robot);
                    //print_r($arr_robot);
                     foreach ($arr_robot as $key => $value) {
                      echo "<dt>$key</dt>";
                         foreach ($value as $key_v => $value_v) {
                             echo "<dd>$key_v - $value_v</dd>";
                         }
                     }
                    
                  ?>
                  
                  
               
              </dl>
             </div> 
                
            
                
                <?php 
                    $out ="";
                    $out2="";
                    
                    $arr_robot = $plan->get_robot_inprocess();
                    ksort($arr_robot);
                    //print_r($arr_robot);
                    
                    foreach ($arr_robot as $key => $value) {
                         $out .= '<th colspan="3"><b>'.$key.'</b> <button type="button" class="btn btn-block btn-primary btn-xs add_order" id="'.$key.'">Заказ</button></th>';
                         $out2 .= '
                                <th><b>надо</b></th>
                                <th><b>есть</b></th>
                                <th><b>статус</b></th>';
                    }
                    
                   
                    ?>
                    
              <table class="table table-bordered table-striped ">
                  <thead>
                  <tr>
                    <th rowspan="2"><b>Подгруппа</b></th>
                    <th colspan="4"><b>Номенклатура</b></th>
                    <?php   echo $out; ?>
                  </tr>
                  <tr>
                    <th><b>Артикул</b></th>
                    <th><b>Наименование</b></th>
                    <th><b>На складе</b></th>
                    <th><b>На робота</b></th>
                    <?php 
                    echo $out2;
                    ?>
                  </tr>
                  </thead>
                  <tbody>
                   <?php 
                   
                   $get_sybcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : 0;
                   $get_version = isset($_GET['version']) ? $_GET['version'] : 0;

                   //собираем список деталей из всех активных комплектов (ид_категория, ид_версия)
                   $arr_pos = $position->get_pos_in_kit_cat($_GET['id'],$get_version);
                   //print_r($arr_pos);

                   //делаем проверку на сборку, т.е. проверяем является ли позиция сборкой
                   foreach ($arr_pos as &$pos) {
                       if($pos['assembly']!= 0) {
                        $idd = $pos['id'];
                        //получаем список деталей из сбороки (ид_сборка)
                        $arr_pos_assembly = $position->get_pos_in_assembly($pos['assembly']);
                           //делаем подмену в позициях + добавляем новые в массив
                          foreach ($arr_pos_assembly as &$pos_ass) {
                              $iddd = $pos_ass['id_pos'];
                              $arr_pos[$iddd]['id'] = $iddd;
                              $arr_pos[$iddd]['subcategory'] = $pos_ass['subcategory'];
                              $arr_pos[$iddd]['category'] = 0; //у аномалии произошло обнуление категории
                              $arr_pos[$iddd]['vendor_code'] = $pos_ass['vendor_code'];
                              $arr_pos[$iddd]['title'] = $pos_ass['title'];
                              $arr_pos[$iddd]['total'] = $pos_ass['total'];
                              $arr_pos[$iddd]['provider'] = $pos_ass['provider'];
                              $arr_pos[$iddd]['summary'] = $pos_ass['summary'];
                              $arr_pos[$iddd]['price'] = $pos_ass['price'];
                              $arr_pos[$iddd]['assembly'] = $pos_ass['assembly'];
                              $arr_pos[$iddd]['version'] = $pos['version']; //$pos_ass['version']; //у аномалии произошло обнуление версии
                              $arr_pos[$iddd]['min_balance'] = $pos_ass['min_balance'];
                              $arr_pos[$iddd]['inassembly'] = 1; //метка деталь состоит в сборке
                              $arr_pos[$iddd]['onrobot'] = 0; //???
                          }
                        
                       }
                   }
                  // print_r($assambly_pos);

                   /*print_r('<pre>');
                   print_r($arr_pos);
                   print_r('</pre>');*/
                   //print_r( $plan->get_operation(75));

                   //
                   foreach ($arr_pos as &$pos) {
                       $total = $pos['total'];
                       unset($need_pos_a);
                       unset($need_pos_o);
                       unset($need_pos);
                       //определяем общую потребность запчастей если они состоят в комплектах через незавершенные операции, если запчасть не состоит в комплектах или состоит но операций нет, то null
                       $need_pos_a = $plan->get_operation_assembly($pos['id']);
                       //определяем общую потребность запчастей через незавершенные операции
                       $need_pos_o = $plan->get_operation($pos['id']);
                       //объеденяем потребности (самой позиции напрямую и через комплекты)
                       if (isset($need_pos_o)) {
                           foreach ($need_pos_o as $arr_m => $mon) {
                               if (isset($need_pos_a[$arr_m]['count'])) {
                                   $need_pos_a[$arr_m]['count'] +=  $mon['count'];
                               } else {
                                   $need_pos_a[$arr_m]['count'] =  $mon['count'];
                               }
                               foreach ($mon['robots'] as $robot) {
                                   $need_pos_a[$arr_m]['robots'][] = $robot;
                               }
                           }
                           $need_pos = $need_pos_a;
                       } else {
                           if(isset($need_pos_a)) {
                               foreach ($need_pos_a as $arr_m => $mon) {
                                   if (isset($need_pos_o[$arr_m]['count'])) {
                                       $need_pos_o[$arr_m]['count'] +=  $mon['count'];
                                   } else {
                                       $need_pos_o[$arr_m]['count'] =  $mon['count'];
                                   }
                                   foreach ($mon['robots'] as $robot) {
                                       $need_pos_o[$arr_m]['robots'][] = $robot;
                                   }
                               }
                               $need_pos = $need_pos_o;
                           }
                       }
                       /*if ($pos['id'] == 1505 || $pos['id'] == 1504) {
                           print_r('<pre>');
                           print_r($pos['id']);
                           print_r('<hr>');
                           //var_dump($need_pos_a);
                           print_r($need_pos);
                           print_r('</pre>');
                       }*/
                       //continue;

                       //определяем количество текущей запчасти в заказах (не отгруженных)
                       $total_order = $plan->get_ordered_items($pos['id']);
                       //массив заказов с текущей запчастью (где есть хотя бы 1 неотгруженная запчасть)
                       $ordered_info = $plan->get_ordered_items_info($pos['id']);
                       //создаем массив для дальнейшей обработки
                       $arr_pos_edit[$pos['id']]['subcategory'] = $position->get_name_subcategory($pos['subcategory']); //не имеет смысла использовать название !!!
                       $arr_pos_edit[$pos['id']]['category'] = $pos['category'];
                       $arr_pos_edit[$pos['id']]['vendor_code'] = $pos['vendor_code'];
                       $arr_pos_edit[$pos['id']]['title'] = $pos['title'];
                       $arr_pos_edit[$pos['id']]['total'] = $pos['total'];
                       $arr_pos_edit[$pos['id']]['provider'] = $pos['provider'];
                       $arr_pos_edit[$pos['id']]['summary'] = $pos['summary'];
                       $arr_pos_edit[$pos['id']]['delta'] = $pos['total'] - $pos['min_balance'];
                       $arr_pos_edit[$pos['id']]['onrobot'] = 0;
                       $arr_pos_edit[$pos['id']]['count'][$pos['version']] = 0; //у аномалии произошло обнуление версии
                       //
                       $prev = 0;

                       /*if ($pos['id'] == 1505 || $pos['id'] == 1504) {
                           print_r('<pre>');
                           print_r($arr_pos_edit[$pos['id']]);
                           //print_r('<hr>');
                           //print_r($ordered_info);
                           print_r('</pre>');
                       }*/


                       //цикл формируещий массив данных для вывода
                       foreach ($arr_robot as $key_r => $value_r) {
                           $ordered_out ="";
                           //проверяем есть ли заказы и создаем ссылки на заказы где есть хотя бы 1 неотгруженная запчасть
                           if (isset($ordered_info)) {
                               foreach ($ordered_info as &$info) {
                                   $ordered_out.= "<a href='./edit_order.php?id=".$info['order_id']."'>". date("d.m.Y", strtotime($info['pos_date'])). " - ".$info['pos_count']." шт.</a><br>";
                               }
                           }
                           //
                           if (!isset($arr_pos_edit[$pos['id']]['month'][$key_r]['need'])) {$arr_pos_edit[$pos['id']]['month'][$key_r]['need'] = 0;}

                           //
                           $need = 0;
                           foreach ($value_r as $key_m => $value_m) {
                               if (isset($arr_pos_edit[$pos['id']]['count'][$key_m])) {
                                   if (!isset($need_pos[$key_r]['count']) || $need_pos[$key_r]['count'] < 0) {$need_pos[$key_r]['count'] = 0;}
                                   $need += $need_pos[$key_r]['count'];
                                   $arr_pos_edit[$pos['id']]['month'][$key_r]['need'] = $need_pos[$key_r]['count'];
                                   if (!isset($need_pos[$key_r]['robots'])) $need_pos[$key_r]['robots'] = "";
                                   $arr_pos_edit[$pos['id']]['month'][$key_r]['robots'] = $need_pos[$key_r]['robots'];
                               }
                           }
                           /*if ($pos['id'] == 1505 || $pos['id'] == 1504) {
                                print_r('<pre>');
                                print_r($arr_pos_edit[$pos['id']]['month'][$key_r]['need']);
                                print_r('<hr>');
                                //print_r($ordered_info);
                                print_r('</pre>');
                            }
                           continue;*/
                           $count_month = $need;
                           $total = $total - $count_month;
                           if ($total < 0 ) {
                               $count_month = $count_month - abs($total);
                           }
                           if ($count_month <0 ) {$count_month = 0;}
                           if (($need-$count_month)==0) {
                               if ($need==0 && $count_month==0) {
                                   $status = "<span class='adopted'>---</span>";
                                   $status_n = 1;
                               }else{
                                   $status = "<span class='adopted'>Принят</span>";
                                   $status_n = 1;
                               }
                           } else {
                               $non = $total_order - $need;
                               if (($need-$count_month) <= $total_order )
                               {
                                   $status = '<span class="ordered" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$ordered_out.'">В заказе</span>' ;
                                   $status_n = 2;
                               } else {
                                   $nn = $need-$count_month-$total_order;
                                   $status = '<span class="not_ordered" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$ordered_out.'">Не заказано '.$nn.'</span>' ;
                                   $status_n = 3;
                                   $arr_order['category'] =  $pos['category'];
                                   $arr_order[$key_r][$pos['id']]['id'] =  $pos['id'];
                                   if (!isset($arr_order[$key_r][$pos['id']]['count'])) {$arr_order[$key_r][$pos['id']]['count'] = 0;}
                                   if ($arr_pos_edit[$pos['id']]['summary']==1) {
                                       $arr_order[$key_r][$pos['id']]['count'] =  $nn;
                                       $prev = $need;
                                   } else {
                                       $arr_order[$key_r][$pos['id']]['count'] =  $need-$count_month;
                                   }
                                   if ($pos['provider']==0)$pos['provider']=1;
                                   $arr_order[$key_r][$pos['id']]['provider'] =  $pos['provider'];
                                   $arr_order[$key_r][$pos['id']]['vendor_code'] =  $pos['vendor_code'];
                                   $arr_order[$key_r][$pos['id']]['title'] =  $pos['title'];
                                   $arr_order[$key_r][$pos['id']]['price'] =  $pos['price'];
                               }
                               $total_order = $total_order - ($need-$count_month);
                           }
                           $arr_pos_edit[$pos['id']]['month'][$key_r]['total'] = $count_month;
                           $arr_pos_edit[$pos['id']]['month'][$key_r]['status'] = $status;
                           $arr_pos_edit[$pos['id']]['month'][$key_r]['status_id'] = $status_n;
                       }
                   }
                   //die;
                    
                    //print_r($arr_order);
                    
                  
                   //
                  foreach ($arr_pos_edit as $key => $value) {
                      $month = $value['month'];
                      $error = "";
                      if ($value['delta'] < 0 ) $error = "background-color: #f5c5dd;";

                      echo "<tr style='$error'>
                         
                          <td>".$value['subcategory']."</td>
                          <td>".$value['vendor_code']."</td>
                          <td>".$value['title']."</td>
                          <td><b>".$value['total']."</b></td>
                          <td><b>".$value['onrobot']."</b></td>
                         
                      ";

                      //
                     foreach ($value['month'] as $key_m => $value_m) {
                        $out_info = "";
                        if(isset($value_m['robots']) && $value_m['robots']!="") {
                            //print_r($value_m['robots']);
                            // echo "<br><br>";
                            foreach ($value_m['robots'] as &$val_operation) {
                             $out_info .=  $val_operation.'<br>';
                            }
                        }
                        $temp = '<td><span class="adopted" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"1000"}\' data-placement="bottom" title="'.$out_info.'">'.$value_m['need'].'</span></td>';
                        echo $temp;
                        //$out_info = "";
                        echo "
                        <td>".$value_m['total']."</td>
                        <td>".$value_m['status']."</td>
                        ";
                     }
                     echo" </tr>";
                  }
                    
                       ?>
                  </tbody>
                </table>
               
                
               <?php 
              // print_r($arr_order);
               ?>
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



	

<?php include 'template/scripts.php';?>
<script>



$( document ).ready(function() {
   $("#month").val(<?php if (isset($_GET['month'])) echo $_GET['month']; ?>);
   $("#status").val(<?php if (isset($_GET['status'])) echo $_GET['status']; ?>);
   $("#subcategory").val(<?php if (isset($_GET['subcategory'])) echo $_GET['subcategory']; ?>);
   $("#version").val(<?php if (isset($_GET['version'])) echo $_GET['version']; ?>);
   
   
   $(".add_order").click(function() {
     var date = $(this).attr("id");  
     var arr_order = ""; 
     var dataString = <?php echo json_encode($arr_order); ?>;
     var jsonString = JSON.stringify(dataString);
     console.log(jsonString);
     
        $.post("./api.php", {
 			action: "add_order_plan",
 			arr_order: jsonString,
 			month: date
 		}).done(function(data) {
 			console.log(data);
 			window.location.href = data;
 		//	window.location.reload(true);
 			return false;
 		});
       
   });
   
  
   
});


                                                             
</script>
</body>
</html>
