<?php 
include 'include/class.inc.php';

if (isset($_GET['month'])) $id_month = $_GET['month'];
if (isset($_GET['status'])) $id_status = $_GET['status'];


?>

<?php include 'template/head.html' ?>
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

 <?php include 'template/header.html' ?>
  <!-- Left side column. contains the logo and sidebar -->
  <?php include 'template/sidebar.html';?>

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
                        <li><a href="plan.php?id=<?php echo $_GET['id'];?>&version=2">В2</a></li>
                        <li><a href="plan.php?id=<?php echo $_GET['id'];?>&version=4">В4</a></li>
                       
                      </ul>
                    </div>
               
                
               <br><br>
              <dl>
                  <? 
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
                    <?   echo $out; ?>
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
                  
                   $arr_pos = $position->get_pos_in_kit_cat($_GET['id'],$get_version);
                   //print_r($arr_pos);
                   
                   
                   foreach ($arr_pos as &$pos) {
                       if($pos['assembly']!= 0) {
                        $idd = $pos['id'];
                        $arr_pos_assembly = $position->get_pos_in_assembly($pos['assembly']);
                          foreach ($arr_pos_assembly as &$pos_ass) {
                              $iddd = $pos_ass['id_pos'];
                              $arr_pos[$iddd]['id'] = $iddd;
                              $arr_pos[$iddd]['subcategory'] = $pos_ass['subcategory'];
                              $arr_pos[$iddd]['category'] = 0;
                              $arr_pos[$iddd]['vendor_code'] = $pos_ass['vendor_code'];
                              $arr_pos[$iddd]['title'] = $pos_ass['title'];
                              $arr_pos[$iddd]['total'] = $pos_ass['total'];
                              $arr_pos[$iddd]['provider'] = $pos_ass['provider'];
                              $arr_pos[$iddd]['summary'] = $pos_ass['summary'];
                              $arr_pos[$iddd]['price'] = $pos_ass['price'];
                              $arr_pos[$iddd]['assembly'] = $pos_ass['assembly'];
                              $arr_pos[$iddd]['version'] = $pos_ass['version'];
                              $arr_pos[$iddd]['min_balance'] = $pos_ass['min_balance'];
                              $arr_pos[$iddd]['inassembly'] = 1;
                              $arr_pos[$iddd]['onrobot'] = 0;
                              //print_r($pos_ass);
                              
                          }
                        
                       }
                   }
                  // print_r($assambly_pos);
                   
                   //print_r($arr_pos);
                 // print_r( $plan->get_operation(75));
                    foreach ($arr_pos as &$pos) {
                        //echo $pos['title']."<br>";
                        $total = $pos['total'];
                        //echo $pos['inassembly'];
                        
                        unset($need_pos_a);
                        unset($need_pos_o);
                        unset($need_pos);
                        $need_pos_a=$plan->get_operation_assembly($pos['id']);
                        $need_pos_o = $plan->get_operation($pos['id']);
                        //echo $pos['id']." get_operation_assembly - ";
                        //print_r($need_pos_a);
                        //echo "<br>";
                       //echo $pos['id']." get_operation - ";
                       // print_r($need_pos_o);
                       // echo "<br>";
                        
                       
                        if (isset($need_pos_o)) {
                            //echo "--1--";
                         foreach ($need_pos_o as $arr_m => $mon) {
                             if (isset($need_pos_a[$arr_m]['count'])) {
                                 
                            $need_pos_a[$arr_m]['count'] +=  $mon['count'];
                             }else {
                                 //echo "!!!!".[$arr_m]['count'];
                                 $need_pos_a[$arr_m]['count'] =  $mon['count']; 
                             }
                             foreach ($mon['robots'] as $robot) {
                                 $need_pos_a[$arr_m]['robots'][] = $robot;
                            }
                          } 
                         
                          //unset($need_pos_a);
                          //unset($need_pos_o);
                          $need_pos = $need_pos_a;
                           
                        } else {
                            // echo "--2--";
                            if(isset($need_pos_a)) {
                           //echo "--2--";
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
                          //unset($need_pos);
                           $need_pos = $need_pos_o; 
                           
                            }
                        } 
                        
                         
                         //print_r($need_pos);
                       // echo "<br><br>";
                     //echo "<br>---------------<br><br>";
                       
                       
                      /* if (isset($pos['inassembly'])) {
                            //$need_pos_a = $plan->get_operation_assembly($pos['id']);
                            //$need_pos_o = $plan->get_operation($pos['id']);
                           // $need_pos = array_merge($need_pos_a, $need_pos_o);
                            $need_pos=$plan->get_operation_assembly($pos['id']);
                            print_r($need_pos);
                        }
                        else {
                            $need_pos = $plan->get_operation($pos['id']);
                            echo "<br>";
                            echo "<br>";
                            print_r($need_pos);
                        }
                        
                        */
                        
                        
                        
                        //print_r($need_pos);
                        $total_order = $plan->get_ordered_items($pos['id']);
                        $ordered_info = $plan->get_ordered_items_info($pos['id']);
                       // print_r($ordered_info);
                        $arr_pos_edit[$pos['id']]['subcategory'] = $position->get_name_subcategory($pos['subcategory']);
                        $arr_pos_edit[$pos['id']]['category'] = $pos['category'];
                        $arr_pos_edit[$pos['id']]['vendor_code'] = $pos['vendor_code'];
                        $arr_pos_edit[$pos['id']]['title'] = $pos['title'];
                        $arr_pos_edit[$pos['id']]['total'] = $pos['total'];
                        $arr_pos_edit[$pos['id']]['provider'] = $pos['provider'];
                        $arr_pos_edit[$pos['id']]['summary'] = $pos['summary'];
                        $arr_pos_edit[$pos['id']]['delta'] = $pos['total'] - $pos['min_balance'] ;
                        $arr_pos_edit[$pos['id']]['onrobot'] = 0 ;
                        $prev = 0;
                        
                        $arr_pos_edit[$pos['id']]['count'][$pos['version']] = 0;
                        
                        //echo  $arr_pos_edit[$pos['pos_id']]['count'][$pos['equipment_id']];
                        foreach ($arr_robot as $key_r => $value_r) {
                             //echo "$key_r<br>";
                            $ordered_out ="";
                             if (isset($ordered_info)) {
                               foreach ($ordered_info as &$info) {
                                   $ordered_out.= "<a href='./edit_order.php?id=".$info['order_id']."'>". date("d.m.Y", strtotime($info['pos_date'])). " - ".$info['pos_count']." шт.</a><br>";
                               }
                               }

                            //print_r($value_r);
                            
                            if(!isset($arr_pos_edit[$pos['id']]['month'][$key_r]['need'])) $arr_pos_edit[$pos['id']]['month'][$key_r]['need'] = 0;
                            $need = 0;
                            foreach ($value_r as $key_m => $value_m) {
                                if (isset($arr_pos_edit[$pos['id']]['count'][$key_m])) {
                                    
                                    //echo "Месяц - ".$key_r." | ";
                                    //echo "Версия - ".$key_m." | ";
                                   // echo "Количесвто - ".$value_m." | ";
                                    if (!isset($need_pos[$key_r]['count']) || $need_pos[$key_r]['count'] < 0) $need_pos[$key_r]['count'] = 0;
                                    $need += $need_pos[$key_r]['count'];  
                                    
                                    
                                   
                                    $arr_pos_edit[$pos['id']]['month'][$key_r]['need'] = $need_pos[$key_r]['count'];
                                    if (!isset($need_pos[$key_r]['robots'])) $need_pos[$key_r]['robots'] = "";
                                    $arr_pos_edit[$pos['id']]['month'][$key_r]['robots'] = $need_pos[$key_r]['robots'];
                                    //echo $arr_pos_edit[$pos['pos_id']]['month'][$key_r]['need'];
                                    //echo "Итог - ".$need." <br> ";
                                    //echo $arr_pos_edit[$pos['pos_id']]['count'][$key_m];
                                }
                            }
                            
                            $count_month = $need;
                            $total = $total - $count_month;
                            
                            if ($total < 0 ) {
                              // $total = abs($total);
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
                                //$order_count = 
                                
                               $non = $total_order - $need;
                                if (($need-$count_month) <= $total_order )
                                {
                               
                                    $status = '<span class="ordered" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$ordered_out.'">В заказе</span>' ;
                                    $status_n = 2;
                                
                                } else {
                                    $nn = $need-$count_month-$total_order;
                                    //$status = "<span class='not_ordered'>Не заказано $nn </span>";
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
                                    
                                    //echo $arr_pos_edit[$pos['pos_id']]['vendor_code']." - ".$arr_order[$key_r][$pos['pos_id']]['count']."<br>";
                                    
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

                                //$arr_pos_edit[$pos['pos_id']]['count'][$pos['equipment_id']];
                                //$arr_pos_edit[$pos['pos_id']]['month'][$key_r]['nado'] = 0;
                                
                                //count($value_r);
                                
                        }
                        
                       

                    }
                    
                    //print_r($arr_order);
                    
                  
                    
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



	

<?php include "./template/scripts.html";?>
<script>



$( document ).ready(function() {
   $("#month").val(<?php if (isset($_GET['month'])) echo $_GET['month']; ?>);
   $("#status").val(<?php if (isset($_GET['status'])) echo $_GET['status']; ?>);
   $("#subcategory").val(<?php if (isset($_GET['subcategory'])) echo $_GET['subcategory']; ?>);
   $("#version").val(<?php if (isset($_GET['version'])) echo $_GET['version']; ?>);
   
   
   $(".add_order").click(function() {
     var date = $(this).attr("id");  
     var arr_order = ""; 
     var dataString = <? echo json_encode($arr_order); ?>;
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
