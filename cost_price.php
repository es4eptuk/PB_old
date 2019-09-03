<?php 
include 'include/class.inc.php';
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
       Финансы
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Себестоимость</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
               
               <?php 
               $query = "";
               
               
               
               $mh = $position->get_kit(1,4,0);
               $hp = $position->get_kit(2,4,0);
               $bd = $position->get_kit(3,4,0);
               $up = $position->get_kit(4,4,0);
               
               //print_r($mh);
               $sum_mh = 0;
               $sum_hp = 0;
               $sum_bd = 0;
               $sum_up = 0;
               $sum_all = 0;
               
               $out_mh = 0;
               $out_hp = 0;
               $out_bd = 0;
               $out_up = 0;
               $out_all = 0;
               
               foreach ($mh as &$value) {
                    $arr_pos = $position->get_pos_in_kit($value['id_kit']);
                        foreach ($arr_pos as &$value_pos) {
                        $sum_mh = $sum_mh + ($value_pos['price']*$value_pos['count']);
                        $out_mh .= "<tr><td>".$value_pos['title']."</td><td>".$value_pos['price']."</td><td>".$value_pos['count']."</td><td>".$value_pos['price']*$value_pos['count']."</td></tr>";
                         }
                }
                
                foreach ($hp as &$value) {
                    $arr_pos = $position->get_pos_in_kit($value['id_kit']);
                        foreach ($arr_pos as &$value_pos) {
                        $sum_hp = $sum_hp + ($value_pos['price']*$value_pos['count']);
                        $out_hp .= "<tr><td>".$value_pos['title']."</td><td>".$value_pos['price']."</td><td>".$value_pos['count']."</td><td>".$value_pos['price']*$value_pos['count']."</td></tr>";

                         }
                }
                
                
                if (isset($bd)) {
                 foreach ($bd as &$value) {
                    $arr_pos = $position->get_pos_in_kit($value['id_kit']);
                        foreach ($arr_pos as &$value_pos) {
                        $sum_bd = $sum_bd + ($value_pos['price']*$value_pos['count']);
                        $out_bd .= "<tr><td>".$value_pos['title']."</td><td>".$value_pos['price']."</td><td>".$value_pos['count']."</td><td>".$value_pos['price']*$value_pos['count']."</td></tr>";

                         }
                }
                }
                
                foreach ($up as &$value) {
                    $arr_pos = $position->get_pos_in_kit($value['id_kit']);
                        foreach ($arr_pos as &$value_pos) {
                        $sum_up = $sum_up + ($value_pos['price']*$value_pos['count']);
                        $out_up .= "<tr><td>".$value_pos['title']."</td><td>".$value_pos['price']."</td><td>".$value_pos['count']."</td><td>".$value_pos['price']*$value_pos['count']."</td></tr>";

                         }
                }
                
               $sum_bd = 80000;
               setlocale(LC_MONETARY, 'ru_RU');
               $sum_all = $sum_mh + $sum_hp + $sum_bd + $sum_up;
               echo "<h4><b>Общая: </b> ".money_format('%.2n', $sum_all)." руб.</h4>";
               echo "<p><b>Механика: </b>".$sum_mh." руб.</p>";
               echo "<p><b>Аппаратка: </b>".$sum_hp." руб.</p>";
               echo "<p><b>Корпус: </b>".$sum_bd." руб.</p>";
               echo "<p><b>Упаковка: </b>".$sum_up." руб.</p>"; 
               
               //echo "<table>";
               //echo $out_up;
               //echo "</table>";
               ?> 
               
               
               
             <div id="donut-chart" style="height: 300px;"></div>
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
<div class="modal fade" id="order_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Заказ № <span id="order_id"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  </div>
</div>
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

<!-- FLOT CHARTS -->
<script src="../../bower_components/Flot/jquery.flot.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="../../bower_components/Flot/jquery.flot.resize.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="../../bower_components/Flot/jquery.flot.pie.js"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src="../../bower_components/Flot/jquery.flot.categories.js"></script>

<script>
 $(function () {
     
    /*
     * DONUT CHART
     * -----------
     */

    var donutData = [
      { label: 'Механика', data: <?php echo $sum_mh;?>, color: '#3c8dbc' },
      { label: 'Аппаратка', data: <?php echo $sum_hp;?>, color: '#0073b7' },
      { label: 'Корпус', data: <?php echo $sum_bd;?>, color: '#00c0ef' },
      { label: 'упаковка', data: <?php echo $sum_up;?>, color: '#00a7d0' }
    ]
    $.plot('#donut-chart', donutData, {
      series: {
        pie: {
          show       : true,
          radius     : 1,
          innerRadius: 0.5,
          label      : {
            show     : true,
            radius   : 2 / 3,
            formatter: labelFormatter,
            threshold: 0.1
          }

        }
      },
      legend: {
        show: false
      }
    })
    /*
     * END DONUT CHART
     */ 
     
 })
  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:11px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
      + label
      + '<br>'
      + Math.round(series.percent) + '%</div>'
  }
</script>
</body>
</html>
