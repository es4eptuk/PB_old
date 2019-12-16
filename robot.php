<?php 
include 'include/class.inc.php';

$robot_info = $robots->get_info_robot($_GET['id']);
$robot_number = $robot_info['number'];
$robot_name= $robot_info['name'];
$robot_version= $robot_info['version'];
$robot_id= $robot_info['id'];
$robot_progress= $robot_info['progress'];
?>

<?php include 'template/head.html' ?>

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
       Promobot <?php echo $robot_version.".".$robot_number; ?>
      </h1>
      
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
        <a href="./check.php?category=1&robot=<?php echo $robot_id; ?>">           
        <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-gear"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Механика</span>
              <span class="info-box-number"><?php
                  $mh = $checks->get_progress($robot_id, 1);

                  if (isset($mh)) {

                      echo $mh;
                  }


                  ?>%</span>

              <div class="progress">
                <div class="progress-bar" style="width: <?php echo $checks->get_progress($robot_id, 1); ?>%"></div>
              </div>
                  
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        <!-- /.col -->
        
        
         <a href="./check.php?category=2&robot=<?php echo $robot_id; ?>"> 
        <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-laptop"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Аппаратка</span>
              <span class="info-box-number"><?php

                  $hp = $checks->get_progress($robot_id, 2);

                  if (isset($hp)) {

                      echo $hp;
                  }



                ?>%</span>

              <div class="progress">
                <div class="progress-bar" style="width: <?php echo $checks->get_progress($robot_id, 2); ?>%"></div>
              </div>
                 
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        <!-- /.col -->
        
        
         <a href="./check.php?category=5&robot=<?php echo $robot_id; ?>"> 
         <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-purple-active">
            <span class="info-box-icon"><i class="fa fa-sliders"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Настройка</span>
             <span class="info-box-number"><?php


                 $hs = $checks->get_progress($robot_id, 5);

                 if (isset($hs)) {

                     echo $hs;
                 }





                 ?>%</span>

              <div class="progress">
                <div class="progress-bar" style="width: <?php echo $checks->get_progress($robot_id, 5); ?>%"></div>
              </div>
                  
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        
         <a href="./check.php?category=3&robot=<?php echo $robot_id; ?>"> 
        <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-random"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Корпус</span>
              <span class="info-box-number"><?php

                  $bd = $checks->get_progress($robot_id, 3);

                  if (isset($bd)) {

                      echo $bd;
                  }






                  ?>%</span>

              <div class="progress">
                <div class="progress-bar" style="width: <?php echo $checks->get_progress($robot_id, 3); ?>%"></div>
              </div>
                  
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        
         <a href="./check.php?category=4&robot=<?php echo $robot_id; ?>"> 
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12" >
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="glyphicon glyphicon-briefcase"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Упаковка</span>
             <span class="info-box-number"><?php

                 $up = $checks->get_progress($robot_id, 4);

                 if (isset($up)) {

                     echo $up;
                 }




                 ?>%</span>

              <div class="progress">
                <div class="progress-bar" style="width: <?php echo $checks->get_progress($robot_id, 4); ?>%"></div>
              </div>
                  
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
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
                  <div class="form-group">
                  <a class="btn btn-app" onclick="onRemont();">
                   <span class="badge bg-yellow"><? echo $robots->countRemont($robot_id);?></span>
                   <i class="fa fa-wrench"></i> Ремонт
                  </a>
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
<!-- jQuery 3 -->
<script src="../../bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- page script -->
<script>
 // $('.comment').validator();   
 var robot =  <?php echo $robot_id; ?>;  
 
 $('.comment').submit(function(){
    
  var level= $(this).find('#level').val();
  var comment = $(this).find('#comment').val();
  
  
  
  
  
   $.post( "./api.php", { 
        action: "add_log", 
        robot: robot,
        level: level,
        comment: comment,
        number: <? echo (int)$robot_number; ?>
        
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./robot.php?id="+robot;
                
              }
          });
  
  
  return false;
});
    
    
     $( ".fa-times" ).click(function() {
               
                id_log = $(this).attr("id");
                
               
                
                $.post( "./api.php", { 
                    action: "delete_log", 
                    id: id_log
                        } )
                  .done(function( data ) {
                      window.location.reload(true);
                     
                  });
               

    });
    
    function onRemont() {
        var isRemont = confirm("Вы действительно хотите перевести робота в ремонт? Все чек-листы будут сброшены.");
        var progress = <? echo $robot_progress;?>;

        if (isRemont && progress===100) {
            
           $.post( "./api.php", { 
                    action: "robot_remont", 
                    robot: robot
                        } )
                  .done(function( data ) {
                      window.location.reload(true);
                  });
            
        }
    }
    
</script>
</body>
</html>
