<?php 
include 'include/class.inc.php';

$robot_info = $robots->get_info_robot($_GET['id']);
$robot_number = $robot_info['number'];
$robot_name= $robot_info['name'];
$robot_version= $robot_info['version'];
$robot_id= $robot_info['id'];
$robot_progress= $robot_info['progress'];

//время сборки текущее
$robot_statistics = $statistics->get_robot_production_statistics($robot_id);
if ($robot_statistics != null) {
    $statistics_status = 'start';
    $end_time = time();
    $time_p = ($robot_statistics['time_pause'] != null) ? $robot_statistics['time_pause'] : 0;
    if ($robot_statistics['start_pause'] != null) {
        $statistics_status = 'pause';
        $end_time = $robot_statistics['start_pause'];
        $time_pause = $statistics->get_time_spent($robot_statistics['start_pause'], time());
        $hh_pause = intval($time_pause/3600);
        $mm_pause = intval(($time_pause - $hh_pause * 3600)/60);
        $statistics_time_pause = $hh_pause.':'.$mm_pause;
    }
    if ($robot_statistics['date_end'] != null) {
        $statistics_status = 'stop';
        $end_time = $robot_statistics['date_end'];
    }
    $time = $statistics->get_time_spent($robot_statistics['date_start'], $end_time) - $time_p;
    $hh = intval($time/3600);
    $mm = intval(($time - $hh * 3600)/60);
    $statistics_time = $hh.':'.$mm;
} else {
    $statistics_status = 'no';
}

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
      <h1>Promobot <?php echo $robot_version.".".$robot_number; ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $robot_name; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
               <div class="row">

        <a <?= ($statistics_status == 'pause') ? 'onclick="onPause();"' : 'href="./check.php?category=1&robot='.$robot_id.'"'?>>
        <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-gear"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Механика</span>
              <span class="info-box-number"><?php
                  $mh = $checks->get_progress($robot_id, 1);
                  if ($mh !== false) {
                      echo $mh.'%';
                  } else {
                      echo 'Нет';
                  }
                  ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo ($mh !== false) ? $mh : '100'; ?>%"></div>
              </div>
            </div>
          </div>
        </div>
        </a>

        <a <?= ($statistics_status == 'pause') ? 'onclick="onPause();"' : 'href="./check.php?category=2&robot='.$robot_id.'"'?>>
        <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-laptop"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Аппаратка</span>
              <span class="info-box-number"><?php
                  $hp = $checks->get_progress($robot_id, 2);
                  if ($hp !== false) {
                      echo $hp.'%';
                  } else {
                      echo 'Нет';
                  }
                  ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo ($hp !== false) ? $hp : '100'; ?>%"></div>
              </div>
            </div>
          </div>
        </div>
        </a>

         <a <?= ($statistics_status == 'pause') ? 'onclick="onPause();"' : 'href="./check.php?category=5&robot='.$robot_id.'"'?>>
         <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-purple-active">
            <span class="info-box-icon"><i class="fa fa-sliders"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Настройка</span>
             <span class="info-box-number"><?php
                 $hs = $checks->get_progress($robot_id, 5);
                 if ($hs !== false) {
                     echo $hs.'%';
                 } else {
                     echo 'Нет';
                 }
                 ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo ($hs !== false) ? $hs : '100'; ?>%"></div>
              </div>
            </div>
          </div>
        </div>
        </a>

        <a <?= ($statistics_status == 'pause') ? 'onclick="onPause();"' : 'href="./check.php?category=3&robot='.$robot_id.'"'?>>
        <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-random"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Корпус</span>
              <span class="info-box-number"><?php
                  $bd = $checks->get_progress($robot_id, 3);
                  if ($bd !== false) {
                      echo $bd.'%';
                  } else {
                      echo 'Нет';
                  }
                  ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo ($bd !== false) ? $bd : '100'; ?>%"></div>
              </div>
            </div>
          </div>
        </div>
        </a>

        <a <?= ($statistics_status == 'pause') ? 'onclick="onPause();"' : 'href="./check.php?category=4&robot='.$robot_id.'"'?>>
        <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="glyphicon glyphicon-briefcase"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Упаковка</span>
             <span class="info-box-number"><?php
                 $up = $checks->get_progress($robot_id, 4);
                 if ($up !== false) {
                     echo $up.'%';
                 } else {
                     echo 'Нет';
                 }
                 ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo ($up !== false) ? $up : '100'; ?>%"></div>
              </div>
            </div>
          </div>
        </div>
        </a>
        
        
        <!-- /.col -->
      </div> 
      
     
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Добавить событие</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            
            
            <form role="form" class="comment">
              <div class="box-body">
                  <div class="row">
                      <?php
                      if ($userdata['user_id'] == 75 || $userdata['user_id'] == 14 || $userdata['user_id'] == 17) {
                          //предварительные расчеты

                          //расчеты для вывода
                          if ($statistics_status != 'no') {
                              $pause = '';
                              if ($statistics_status == 'start') {
                                  $icon = 'fa fa-pause';
                              }
                              if ($statistics_status == 'stop') {
                                  $icon = 'fa fa-stop';
                              }
                              if ($statistics_status == 'pause') {
                                  $icon = 'fa fa-play';
                                  $pause = '<span class="badge bg-yellow">' . $statistics_time_pause . '</span>';
                              }
                              echo '
                              <div class="form-group" style="float:left;margin-right:10px">
                                  <a class="btn btn-app" id="robot_production_statistics">
                                      ' . $pause . '
                                      <i class="' . $icon . '"></i>' . $statistics_time . '
                                  </a>
                              </div>
                              ';
                          }
                      }
                      ?>
                      <div class="form-group">
                          <a class="btn btn-app" onclick="onRemont();">
                              <span class="badge bg-yellow"><?php echo $robots->countRemont($robot_id); ?></span>
                              <i class="fa fa-wrench"></i> Ремонт
                          </a>
                      </div>
                  </div>
                  <div class="form-group">
                      <label>Тип</label>
                      <select class="form-control" id="level" required="required">
                          <option value="WARNING">Проблема</option>
                          <option value="MODERN">Доработка</option>
                          <option value="INFO">Комментарий</option>


                      </select>
                  </div>
                <div class="form-group">
                  <label>Описание</label>
                  <textarea class="form-control" rows="3" placeholder="Введите описание ..." name="comment" id="comment" required="required"></textarea>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Добавить</button>
              </div>
            </form>
          </div>
                
              <table id="orders" class="table table-bordered table-striped">
                <thead>
                <tr>
                  
                  <th>ID</th>
                  <th>Уровень</th>
                  <th>Статус</th>
                  <th>Пользователь</th>
                  <th>Дата</th>
                  <?php if ($userdata['group']==1) {echo '<th>Удалить</th>';} ?>
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $robots->get_log($_GET['id']);
                
                if (isset($arr)) {
                foreach ($arr as &$log) {
                    
                    $user_info = $user->get_info_user($log['update_user']);
                    $log_date = new DateTime($log['update_date']);
                    $level = $log['level'];
                    
                    switch ($level) {
                        case "INFO":
                            $color = "#f1f7c1";
                            break;
                        case "GOOD":
                            $color = "#c1f7cc";
                            break;
                        case "WARNING":
                            $color = "#f7c1e4";
                            break;
                        case "MODERN":
                            $color = "#dce0ff";
                            break; 
                        case "TICKET":
                            $color = "#90bec5";
                            break;
                        default:
                            $color = "#fff";
                            
                    }
                    $out_del = "";
                    if ($userdata['group']==1) {$out_del = " <td><center><i class='fa fa-2x fa-times' style='cursor: pointer;' id='".$log['id']."'></i></center></td>";}
                    
                       echo "
                    <tr style='background: ".$color."'>
                     
                        
                        <td>".$log['id']."</td>
                        <td>".$log['level']."</td>
                        <td>".$log['comment']."</td>
                        <td>".$user_info['user_name']." </td>
                        <td>".$log_date->format('d.m.Y H:i:s')."</td>
                        
                       ".$out_del."
                       
                    </tr>
                       
                       
                       ";
                    }
                }
               
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
<?php include 'template/scripts.php'; ?>
<script>
    // $('.comment').validator();
    var robot =  <?php echo $robot_id; ?>;

    $('.comment').submit(function () {
        var level = $(this).find('#level').val();
        var comment = $(this).find('#comment').val();
        $.post("./api.php", {
            action: "add_log",
            robot: robot,
            level: level,
            comment: comment,
            number: <?php echo (int)$robot_number; ?>
        }).done(function (data) {
            if (data == "false") {
                alert("Data Loaded: " + data);
            } else {
                window.location.href = "./robot.php?id=" + robot;
            }
        });
        return false;
    });

    $("body").on('click', '.fa-times', function () {
        id_log = $(this).attr("id");
        $.post("./api.php", {
            action: "delete_log",
            id: id_log
        }).done(function (data) {
            window.location.reload(true);
        });
    });

    $("body").on('click', '#robot_production_statistics', function () {
        $.post("./api.php", {
            action: "change_status_robot_production_statistics",
            id: robot
        }).done(function (data) {
            console.log(data);
            if (data == 'true') {
                window.location.reload(true);
            } else {
                return false;
            }
        });
    });

    function onRemont() {
        var isRemont = confirm("Вы действительно хотите перевести робота в ремонт? Все чек-листы будут сброшены.");
        var progress = <?php echo $robot_progress;?>;
        if (isRemont && progress === 100) {
            $.post("./api.php", {
                action: "robot_remont",
                robot: robot
            }).done(function (data) {
                window.location.reload(true);
            });
        }
    }

    function onPause() {
        alert("Сборка стоит на паузе, для активации обратитесь к начальнику производства!");
    }
    
</script>
</body>
</html>
