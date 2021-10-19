<?php

ini_set('max_execution_time', '100000');
set_time_limit(0);
ini_set('memory_limit', '4096M');
ignore_user_abort(true);

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
$categoryes = $position->getCategoryes;
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
              <h3 class="box-title"><?= $categoryes[$_GET['id']]['title'] ?></h3>
            </div>
            <!-- /.box-header -->
            
           
            
            <div class="box-body table-responsive no-padding">
                <div class="margin">

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
                    $arr_robot = $plan->get_robot_inprocess_new();
                    $arr_robot_num = $plan->get_robot_inprocess_num_new();

                    //$arr_robot = (isset($arr_robot)) ? $arr_robot : [];
                    //ksort($arr_robot);
                    echo "<dt>Потребность</dt><br>";
                    foreach ($arr_robot as $v => $c) {
                        if (in_array($v, $v_filtr) || $v_filtr == []) {
                            $name = trim($version[$v]['title']);
                            $robotss = implode(', ', $arr_robot_num[$v]);
                            echo "<dd><span style='display:block;float:left;width:110px;padding-left:10px'>$name</span><span style='display:inline-block;width:80%;'> - $c ($robotss)</span></dd>";
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
                    $out .= '<th colspan="3"><b>Потребность</b> <button type="button" class="btn btn-block btn-primary btn-xs add_order" data-date="1">Заказ</button></th>';
                    $out2 .= '<th><b>надо</b></th><th><b>есть</b></th><th><b>статус</b></th>';
                ?>
                    
              <table class="table table-bordered">
                  <thead>
                  <tr>
                    <th colspan="3"><b>Номенклатура</b></th>
                    <th colspan="3">
                        <b>Склад</b><br>
                        <button type="button" class="btn btn-block btn-danger btn-xs add_order" data-date="0">Заказ</button>
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
                  foreach ($arr_robot as $v => $c) {
                      if (isset($v)) {
                          if (!in_array($v, $v_filtr) && $v_filtr != []) {
                              continue;
                          }
                          foreach ($plan->get_check_in_process_by_version_new($v) as $chesk) {
                              foreach ($arr_kit_items[$chesk['id_kit']] as $id_pos => $count) {
                                  $arr_need[$v][] = [
                                      'id_pos' => $id_pos,
                                      'count' => $count,
                                      'operation' => $chesk['operation'].' ('.$count.')',
                                  ];
                              }
                          }
                      }
                  }
                  unset($arr_kit_items);

                  $arr_inneed = [];
                  foreach ($arr_need as $version => $positions) {
                      foreach ($positions as $pos) {
                          $count = $pos['count'];
                          $operation = $pos['operation'];
                          if (isset($arr_inneed[$version][$pos['id_pos']])) {
                              $arr_inneed[$version][$pos['id_pos']]['count'] = $arr_inneed[$version][$pos['id_pos']]['count'] + $count;
                              $arr_inneed[$version][$pos['id_pos']]['operation'][] = $operation;
                          } else {
                              $arr_inneed[$version][$pos['id_pos']] = [
                                  'count' => $count,
                                  'operation' => [$operation],
                              ];
                          }
                      }
                  }
                  unset($arr_need);

                  //собираем все позиции в заказе, пока без категории $_GET['id']
                  $orderss = $orders->get_orders_items_inprocess();
                  //создаем массив заказов [id_pos => [id_order => in_order]]
                  foreach ($orderss as $v) {
                      $in_order = $v['pos_count'] - $v['pos_count_finish'];
                      $pos_date = date('d.m.Y', strtotime($v['pos_date']));
                      if ($in_order > 0) {
                          $arr_orders[$v['pos_id']][$v['order_id']] = [
                              'count' => $in_order,
                              'date' => $pos_date,
                              'category' => $categoryes[$v['order_category']]['title'],
                          ];
                      }
                  }
                  unset($orderss);

                  //создаем массив позиций по категории (без архивных и сборных позиций)
                  $arr_pos = $position->get_pos_in_category();
                  $arr_pos = (isset($arr_pos)) ? $arr_pos : [];
                  //подготовка массива
                  foreach ($arr_pos as $k => $v) {
                      //удаляем лишние поля
                      unset($arr_pos[$k]['longtitle']);
                      unset($arr_pos[$k]['version']);
                      unset($arr_pos[$k]['quant_robot']);
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
                      $arr_pos[$k]['orders'] = (isset($arr_orders[$k])) ? $arr_orders[$k] : [];
                      $arr_pos[$k]['stock'] = ($v['total'] > 0) ? $v['total'] : 0;
                      $arr_pos[$k]['order'] = array_sum (array_column($arr_pos[$k]['orders'], 'count'));
                      $arr_pos[$k]['inorder'] = ($arr_pos[$k]['need']<0 && (abs($arr_pos[$k]['need'])-$arr_pos[$k]['order'])>0)
                          ? abs($arr_pos[$k]['need']) - $arr_pos[$k]['order']
                          : 0;
                      //
                      $arr_pos[$k]['deleting_post'] = 0;
                      $incount = 0;
                      $operation = [];
                      foreach ($arr_robot as $version => $count) {
                          if (isset($arr_inneed[$version][$k])) {
                              $incount = $incount + $arr_inneed[$version][$k]['count'];
                              $operation = array_merge ($operation, $arr_inneed[$version][$k]['operation']);
                          }
                      }
                      //считаем занчения
                      $instock = ($arr_pos[$k]['stock'] - $incount >= 0) ? $incount : $arr_pos[$k]['stock'];
                      $inorder = ($incount - $instock - $arr_pos[$k]['order'] <= 0) ? 0 : $incount - $instock - $arr_pos[$k]['order'];
                      //присваеваем значения
                      $arr_pos[$k]['in']['inneed'] = $incount;
                      $arr_pos[$k]['in']['instock'] = $instock;
                      $arr_pos[$k]['in']['inorder'] = $inorder;
                      $arr_pos[$k]['in']['operation'] = $operation;
                  }


                  //обработка вхождений
                  $arr_pos = $plan->prepare_array_items($arr_pos);

                  //удаляем лишнее
                  foreach ($arr_pos as $k => $v) {
                      if ($v['category'] == $_GET['id']) {
                          $arr_pos[$k]['in']['operation'] = implode("<br>", $arr_pos[$k]['in']['operation']);
                          //определяем статус
                          $arr_pos[$k]['in']['status'] = 0;
                          if ($arr_pos[$k]['in']['inneed'] == $arr_pos[$k]['in']['instock'] && $arr_pos[$k]['in']['inneed'] != 0) {
                              $arr_pos[$k]['in']['status'] = 3;
                          }
                          if ($arr_pos[$k]['in']['inneed'] != $arr_pos[$k]['in']['instock'] && $arr_pos[$k]['in']['inorder'] == 0) {
                              $arr_pos[$k]['in']['status'] = 2;
                          }
                          if ($arr_pos[$k]['in']['inneed'] != $arr_pos[$k]['in']['instock'] && $arr_pos[$k]['in']['inorder'] != 0) {
                              $arr_pos[$k]['in']['status'] = 1;
                          }
                          $arr_pos[$k]['deleting_post'] = $arr_pos[$k]['deleting_post'] + $arr_pos[$k]['in']['inneed'];
                          if ((isset($_GET['version']) && $arr_pos[$k]['deleting_post']==0) || ($v_filtr != [] && $arr_pos[$k]['deleting_post']==0)) {
                              unset($arr_pos[$k]);
                              continue;
                          }
                      } else {
                          unset($arr_pos[$k]);
                      }
                  }
                  ksort($arr_pos);
                  unset($arr_inneed);
                  unset($arr_orders);

                  //вывод массива
                  $arr_pos = (isset($arr_pos)) ? $arr_pos : [];
                  foreach ($arr_pos as $k => $v) {
                      $error = ($v['inorder'] > 0) ? "background-color: #f5c5dd;" : "";
                      $error = ($v['total'] < 0) ? "background-color: #c5d6f5;" : $error;
                      echo '<tr style="'.$error.'">';
                      //создаем ссылки на заказы
                      $orders = "";
                      foreach ($v['orders'] as $id => $info) {
                          $orders .= "<a href='./edit_order.php?id=".$id."' target='_blank'>".$info['date']." (".$info['category'].") - ".$info['count']." шт.</a><br>";
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

                      //вывод Потребности
                      $color = $color_statuses[$v['in']['status']];
                      $checks = str_replace("\"", "'", $v['in']['operation']);
                      $toorder = ($v['in']['inorder'] != 0) ? "<br>(".$v['in']['inorder'].")" : "";
                      echo '
                        <td>
                            <span style="color:'.$color.'" data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$checks.'">
                                '.$v['in']['inneed'].'                         
                            </span>                            
                        </td>
                        <td>
                            <span style="color:'.$color.'">
                                '.$v['in']['instock'].'                         
                            </span>                              
                        </td>
                        <td>
                            <span style="color:'.$color.'" data-template=\'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>\' data-toggle="tooltip" data-html="true" data-delay=\'{"show":"100", "hide":"3000"}\' data-placement="bottom" title="'.$orders.'">
                                '.$statuses[$v['in']['status']].$toorder.'                            
                            </span>
                        </td>                            
                      ';
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
            var yes = confirm("Вы действительно готовы сформировать заказы поставщику?");
            if (yes == true) {
                $.post("./api.php", {
                    action: "add_order_plan_new_new",
                    category: category,
                    version: version,
                    month: date,
                    filter: filter
                }).done(function (data) {
                    //console.log(data);
                    if (data == '') {
                        alert('Заказы не сформировались, т.к. заказывать нечего!');
                    } else {
                        alert('Заказы успешно сформированны: ' + data + '.');
                    }
                    window.location.reload(true);
                });
            } else {
                return false;
            }
        });
    });
</script>

</body>
</html>
