<?php 
include 'include/class.inc.php';
$statuses = [
    0 => '---',
    1 => 'Не заказано',
    2 => 'В заказе',
    3 => 'Принято',
];
$color_statuses = [
    0 => '#008000',
    1 => '#de4e4e',
    2 => '#d0d058',
    3 => '#008000',
];

$arr_eq = $robots->getEquipment;

$v_filtr = [];
foreach ($arr_eq as $eq) {
    if (isset($_POST[$eq['id']])) {
        array_push($v_filtr, $eq['id']);
    }
}

    //print_r($v_filtr);

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
      <h1>План</h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $position->getCategoryes[$_GET['id']]['title'] ;
             
             // echo $plan->get_ordered_items(65);
              ?></h3>
            </div>
            <!-- /.box-header -->
            
           
            
            <div class="box-body table-responsive no-padding">
                <div class="margin">
                    <!--
                    <div class="btn-group">
                      <button type="button" class="btn btn-default">Версия робота</button>
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                          <?php
                              echo '<li><a href="plan.php?id='.$_GET['id'].'">Убрать фильтр</a></li>';
                              foreach ($arr_eq as $eq) {
                                  echo '<li><a href="plan.php?id='.$_GET['id'].'&version='.$eq['id'].'">'.$eq['title'].'</a></li>';
                              }
                          ?>
                      </ul>
                    </div>
                    -->
                    <div class="">
                    <form action="plan.php?id=<?= $_GET['id']?>" method="post">
                        <div class="form-group">
                            <?php
                            foreach ($arr_eq as $eq) {
                                if (isset($_POST[$eq['id']])) {
                                    $checked = 'checked';
                                } else {
                                    $checked = '';
                                }
                                echo '<div class="checkbox">';
                                echo '<label><input type="checkbox" id="'.$eq['id'].'" name="'.$eq['id'].'" '.$checked.'> '.$eq['title'].'</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="add_filtr" name="">Применить</button>
                            <button type="reset" class="btn btn-default" id="del_filtr" name="" onclick="javascript:document.location = './plan.php?id=' + <?= $_GET['id']?>;">Сбросить</button>
                        </div>
                    </form>
                    </div>
                
               <br><br>
              <dl>
                <?php
                    //подготовка плана производства роботов по месяцам
                    $version = $robots->getEquipment;
                    $arr_robot = $plan->get_robot_inprocess();
                    $arr_robot_num = $plan->get_robot_inprocess_num();

                    $arr_robot = (isset($arr_robot)) ? $arr_robot : [];
                    ksort($arr_robot);
                    foreach ($arr_robot as $k => $v) {
                        echo "<dt>$k</dt>";
                        foreach ($v as $kv => $vv) {
                            if (in_array($kv, $v_filtr) || $v_filtr == []) {
                                $name = trim($version[$kv]['title']);
                                $robots = implode(', ', $arr_robot_num[$k][$kv]);
                                echo "<dd><span style='display:block;float:left;width:110px;padding-left:10px'>$name</span><span style='display:inline-block;width:80%;'> - $vv ($robots)</span></dd>";
                            }
                        }
                    }
                ?>
                  
                  
               
              </dl>
             </div> 
                <?php
                    //подготовка шапки по месяцам
                    $out ="";
                    $out2="";
                    //на основе дат собираем шапку таблицы
                    foreach ($arr_robot as $k => $v) {
                        $out .= '<th colspan="3"><b>'.$k.'</b> <button type="button" class="btn btn-block btn-primary btn-xs add_order" data-date="'.$k.'">Заказ</button></th>';
                        //$out2 .= '<th style="width: 47px"><b>надо</b></th><th style="width: 47px"><b>есть</b></th><th style="width: 86px"><b>статус</b></th>';
                        $out2 .= '<th><b>надо</b></th><th><b>есть</b></th><th><b>статус</b></th>';
                    }
                ?>
                    
              <table class="table table-bordered">
                  <thead>
                  <tr>
                    <th colspan="3"><b>Номенклатура</b></th>
                    <th colspan="3">
                        <b>Склад</b><br>
                        <button type="button" class="btn btn-block btn-primary btn-xs add_order" data-date="0" <?php echo (isset($_GET['version']) || $v_filtr != []) ? "disabled" : "";?>>Заказ</button>
                        <!--<button type="button" class="btn btn-primary btn-xs add_order" data-date="" style="width:48%;" <?php echo (!isset($_GET['version'])) ? "disabled" : "";?>>На робота</button>-->
                    </th>
                    <?php echo $out; ?>
                  </tr>
                  <tr>
                    <th><b>Подгруппа</b></th>
                    <th><b>Артикул</b></th>
                    <th><b>Наименование</b></th>
                    <th><b>Заказать</b></th>
                    <th><b>R остаток</b></th>
                    <th><b>На складе</b></th>
                    <?php echo $out2; ?>
                  </tr>
                  </thead>
                  <tbody>
                  <?php

                  //подготовка потребностей
                  $arr_kit_items = $plan->get_kits();
                  $arr_need = [];
                  foreach ($arr_robot as $k => $v) {
                      if (isset($v)) {
                          foreach ($v as $kv => $vv) {
                              if (!in_array($kv, $v_filtr) && $v_filtr != []) {
                                  continue;
                              }
                              foreach ($plan->get_check_in_process_by_version($k,$kv) as $chesk) {
                                  //$arr_need[$k][$kv][$chesk['operation']] = $arr_kit_items[$chesk['id_kit']];
                                  foreach ($arr_kit_items[$chesk['id_kit']] as $id_pos => $count) {
                                      $arr_need[$k][$kv][] = [
                                          'id_pos' => $id_pos,
                                          'count' => $count,
                                          'operation' => $chesk['operation'].' ('.$count.')',
                                      ];
                                  }
                              }
                          }
                      }
                  }
                  $arr_inneed = [];
                  foreach ($arr_need as $month => $versions) {
                      foreach ($versions as $version => $positions) {
                          foreach ($positions as $pos) {
                              //$arr_inneed[] = $pos['count'];
                              $count = $pos['count'];
                              $operation = $pos['operation'];
                              if (isset($arr_inneed[$month][$version][$pos['id_pos']])) {

                                  $arr_inneed[$month][$version][$pos['id_pos']]['count'] = $arr_inneed[$month][$version][$pos['id_pos']]['count'] + $count;
                                  $arr_inneed[$month][$version][$pos['id_pos']]['operation'][] = $operation;
                              } else {
                                  //$arr_inneed[$month][$version][$pos['id_pos']] = [];
                                  $arr_inneed[$month][$version][$pos['id_pos']] = [
                                      'count' => $count,
                                      'operation' => [$operation],
                                  ];
                              }
                          }
                      }
                  }
                  unset($arr_need);
                  unset($arr_kit_items);


                  /*print_r('<pre>');
                  print_r($arr_inneed);
                  print_r('</pre>');
                  die;*/

                  //собираем все позиции в заказе, пока без категории $_GET['id']
                  $orders = $orders->get_orders_items_inprocess();
                  //создаем массив заказов [id_pos => [id_order => in_order]]
                  foreach ($orders as $v) {
                      $in_order = $v['pos_count'] - $v['pos_count_finish'];
                      $pos_date = date('d.m.Y', strtotime($v['pos_date']));
                      if ($in_order > 0) {
                          $arr_orders[$v['pos_id']][$v['order_id']] = [
                              'count' => $in_order,
                              'date' => $pos_date,
                          ];
                      }
                  }
                  unset($orders);
                  /*print_r('<pre>');
                  print_r($arr_orders);
                  print_r('</pre>');
                  die;*/

                  //создаем массив позиций по категории (без архивных и сборных позиций)
                  $arr_pos = $position->get_pos_in_category($_GET['id']);
                  $arr_pos = (isset($arr_pos)) ? $arr_pos : [];
                  //подготовка массива
                  foreach ($arr_pos as $k => $v) {
                      //удаляем сборки
                      /*if ($v['assembly'] != 0) {
                          unset($arr_pos[$k]);
                          continue;
                      }*/
                      //удаляем лишние поля
                      unset($arr_pos[$k]['longtitle']);
                      unset($arr_pos[$k]['version']);
                      unset($arr_pos[$k]['quant_robot']);
                      unset($arr_pos[$k]['assembly']);
                      unset($arr_pos[$k]['summary']);
                      unset($arr_pos[$k]['apply']);
                      unset($arr_pos[$k]['ow']);
                      unset($arr_pos[$k]['img']);
                      unset($arr_pos[$k]['archive']);
                      unset($arr_pos[$k]['update_date']);
                      unset($arr_pos[$k]['update_user']);
                      //редактируем поля
                      $arr_pos[$k]['subcategory'] = $position->getSubcategoryes[$v['subcategory']]['title'];
                      //добавляем поля
                      $arr_pos[$k]['need'] = $v['total'] - $v['reserv'] - $v['min_balance']; //общая потребность
                      $arr_pos[$k]['onrobot'] = ""; //на робота если будет проставлен фильтр по роботу
                      $arr_pos[$k]['orders'] = (isset($arr_orders[$k])) ? $arr_orders[$k] : [];
                      $stock = ($v['total'] > 0) ? $v['total'] : 0;
                      $order = array_sum (array_column($arr_pos[$k]['orders'], 'count'));
                      $arr_pos[$k]['inorder'] = ($arr_pos[$k]['need']<0 && (abs($arr_pos[$k]['need'])-$order)>0) ? abs($arr_pos[$k]['need']) - $order : 0;
                      $deleting = 0;
                      $sum_inorder = 0;
                      foreach ($arr_robot as $month => $versions) {
                          $incount = 0;
                          $inorder = 0;
                          $operation = [];
                          //если есть фильтр по версиям
                          if (isset($_GET['version'])) {
                              if (isset($arr_inneed[$month][$_GET['version']][$k])) {
                                  $incount = $arr_inneed[$month][$_GET['version']][$k]['count'];
                                  $operation = $arr_inneed[$month][$_GET['version']][$k]['operation'];
                              }
                          //если нет фильтра по версиям
                          } else {
                              foreach ($versions as $version => $count) {
                                  if (isset($arr_inneed[$month][$version][$k])) {
                                      $incount = $incount + $arr_inneed[$month][$version][$k]['count'];
                                      $operation = array_merge ($operation, $arr_inneed[$month][$version][$k]['operation']);
                                  }

                              }
                          }
                          //считаем занчения
                          $instock = ($stock - $incount >= 0) ? $incount : $stock;
                          $stock = ($stock - $instock > 0) ? $stock - $instock : 0;
                          $inorder = ($incount - $instock - $order <= 0) ? 0 : $incount - $instock - $order;
                          $order = (($order - ($incount - $instock)) <= 0) ? 0 : ($order - ($incount - $instock));
                          $sum_inorder = $sum_inorder + $inorder;
                          //присваеваем значения
                          $arr_pos[$k]['month'][$month]['inneed'] = $incount;
                          $arr_pos[$k]['month'][$month]['instock'] = $instock;
                          $arr_pos[$k]['month'][$month]['inorder'] = ($inorder == 0) ? 0 : $sum_inorder;
                          $arr_pos[$k]['month'][$month]['operation'] = implode("<br>", $operation);
                          //определяем статус
                          $arr_pos[$k]['month'][$month]['status'] = 0;
                          if ($incount == $instock && $incount != 0) {
                              $arr_pos[$k]['month'][$month]['status'] = 3;
                          }
                          if ($incount != $instock && $inorder == 0) {
                              $arr_pos[$k]['month'][$month]['status'] = 2;
                          }
                          if ($incount != $instock && $inorder != 0) {
                              $arr_pos[$k]['month'][$month]['status'] = 1;
                          }
                          $deleting = $deleting + $incount;
                      }
                      if ((isset($_GET['version']) && $deleting==0) || ($v_filtr != [] && $deleting==0)) {
                          unset($arr_pos[$k]);
                          continue;
                      }
                  }
                  ksort($arr_pos);


                  unset($arr_inneed);
                  unset($arr_orders);
                  /*print_r('<pre>');
                  print_r($arr_pos);
                  print_r('</pre>');
                  die;*/

                  //вывод массива
                  $arr_pos = (isset($arr_pos)) ? $arr_pos : [];
                  foreach ($arr_pos as $k => $v) {
                      $error = ($v['inorder'] > 0) ? "background-color: #f5c5dd;" : "";
                      $error = ($v['total'] < 0) ? "background-color: #c5d6f5;" : $error;
                      echo '<tr style="'.$error.'">';
                      //создаем ссылки на заказы
                      $orders = "";
                      foreach ($v['orders'] as $id => $info) {
                          $orders .= "<a href='./edit_order.php?id=".$id."'>".$info['date']." - ".$info['count']." шт.</a><br>";
                      }
                      //создаем шапку слева
                      echo '
                        <td>'.$v['subcategory'].'</td>
                        <td>'.$v['vendor_code'].'</td>
                        <td>'.$v['title'].'</td>
                        <td>
                            <span style="font-weight:800;" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$orders.'">
                                '.$v['inorder'].'
                            </span>
                        </td>                        
                        <td><b>'.$v['need'].'</b></td>
                        <td><b>'.$v['total'].'</b></td>
                      ';
                      //вывод по месяцам
                      foreach ($v['month'] as $km => $vm) {
                          $color = $color_statuses[$vm['status']];
                          $checks = $vm['operation'];
                          $toorder = ($vm['inorder'] != 0) ? "<br>(".$vm['inorder'].")" : "";
                          echo '
                            <td>
                                <span style="color:'.$color.'" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$checks.'">
                                    '.$vm['inneed'].'                         
                                </span>                            
                            </td>
                            <td>
                                <span style="color:'.$color.'">
                                    '.$vm['instock'].'                         
                                </span>                              
                            </td>
                            <td>
                                <span style="color:'.$color.'" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$orders.'">
                                    '.$statuses[$vm['status']].$toorder.'                            
                                </span>
                            </td>                            
                          ';
                      }
                      echo '</tr>';
                  }

                  ?>
                  </tbody>
                </table>
               
                
               <?php 
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
    $(document).ready(function () {
        $('body').on('click', '.add_order', function () {
            var category = <?php echo (isset($_GET['id'])) ? $_GET['id'] : 0; ?>;
            var version = <?php echo (isset($_GET['version'])) ? $_GET['version'] : 0; ?>;
            var date = $(this).data('date');
            var filter = JSON.stringify([<?php echo implode(',', $v_filtr);?>]);
            //console.log(filter);
            //return false;
            $.post("./api.php", {
                action: "add_order_plan_new",
                category: category,
                version: version,
                month: date,
                filter: filter
            }).done(function (data) {
                console.log(data);
                window.location.href = data;
                setTimeout(function() {
                    //document.location = './plan.php?id=' + <?= $_GET['id']?>;
                    window.location.reload(true);
                    //window.location.href = "./plan.php?id=<?= $_GET['id']?>";
                }, 1000);
                return false;
            });
        });
    });
</script>

</body>
</html>
