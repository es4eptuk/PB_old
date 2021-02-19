<?php 
include 'include/class.inc.php';

function file_force_download($file) {
    if (file_exists($file)) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        unlink($file);
        exit;
    }
}

if (isset($_POST['print'])) {
    $file = $orders->createFileOrder($_POST['order_id']);
    file_force_download($file);
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
      <h1>
       Заказы
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $position->getCategoryes[$_GET['id']]['title'] ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form id="show_all">
                    <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
                    <div class="checkbox">
                    <label>
                      <?php   if ( isset($_GET['show_all']) == 'on') {
                        
                      echo '<input type="checkbox" id="check_show_all" name="show_all" checked>';
                      
                      } else {
                          
                      echo '<input type="checkbox" id="check_show_all" name="show_all"  >';
                          
                      }
                      
                      ?>
                      Отображать завершенные заказы
                    </label>
                  </div>
                    
                </form>
                
              <table id="orders" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th></th>
                  <th>Номер</th>
                  <th>Дата составления</th>
                  
                  <th>Версия робота</th>
                  <th>Контрагент</th>
                  <th>Срок поставки</th>
                  <th>% выполнения</th>
                  <th>Стоимость, руб</th>
                  <th>Статус</th>
                  <th>Просрок, дней</th>
                  <th>Оплата</th>
                  <th>Ответственный</th>
                  <th></th>
                  <th></th>
                  <th></th>
                 
                </tr>
                </thead>
                <tbody>
                <?php 
                
                $arr = $orders->get_orders($_GET['id']);
                //print_r($arr);
                if (isset($arr)) {
                foreach ($arr as &$pos) {
                    
                    if ($pos['order_status']!=2 || isset($_GET['show_all'])=='on') {
                    
                    
                        $user_info = $user->get_info_user($pos['order_responsible']);
                        $order_date = new DateTime($pos['order_date']);
                        $order_delivery = new DateTime($pos['order_delivery']);
                        
                        switch ($pos['order_status']) {
                        case 0:
                            $pos['order_status'] = "Новый";
                            break;
                        case 1:
                            $pos['order_status'] = "Принят";
                            break;
                        case 2:
                            $pos['order_status'] = "Отгружен";
                            break;
                        case 3:
                            $pos['order_status'] = "Отменен";
                            break;
                        default:
                            $pos['order_status'] = "Неизвестен";
                    }
                        $provider = $position->get_info_pos_provider($pos['order_provider']);
                        $auto="";
                        if ($pos['auto']) {$auto="<small class='label pull-right bg-blue'>Auto</small>";}
                        $checkPayment = ($pos['order_payment']==true) ? 'checked' : '';
                       echo "
                    <tr >
                    
                        <td>".$auto."</td>
                        <td>".$pos['order_id']."</td>
                        <td>".$order_date->format('d.m.Y')."</td>
                        
                        <td>".$pos['version']."</td>
                        <td>".$provider['title'].", ".$provider['type']."</td>
                        <td>".$order_delivery->format('d.m.Y')."</td>
                        <td>".$pos['order_completion']."</td>
                        <td>".number_format($pos['order_price'], 2, ',', ' ')."</td>
                        <td>".$pos['order_status']."</td>
                        <td>".$pos['order_prosecution']. "</td>
                       
                        <td><div style='text-align: center;'><input type='checkbox' data-id='" .$pos['order_id']."' class='payment' ".$checkPayment." ></div></td>
                        <td>".$user_info['user_name']."</td>
                         
                        <td><i class='fa fa-2x fa-copy' style='cursor: pointer;' data-id='".$pos['order_id']."'></i></td>
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' data-id='".$pos['order_id']."'></i></td>
                        <td><i class='fa fa-2x fa-print' style='cursor: pointer;' data-id='".$pos['order_id']."'></i></td>
                       
                       
                    </tr>
                       
                       
                       ";
                    }
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
    <div style="display: none;">
        <form action="" method="post" name="order_print" id="order_print">
            <input type="hidden" id="print" name="print" value="">
            <input type="hidden" id="order_id" name="order_id" value="">
        </form>
    </div>
</div>
<!-- ./wrapper -->
<!-- Modal -->
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
    var id_order = 0;
    paramStorage = localStorage;

    $("#orders").on('click', '.fa-pencil', function () {
        id_order = $(this).data("id");
        window.location.href = "./edit_order.php?id=" + id_order;
        paramStorage.searchStr = $('#orders_filter input').val();
    });

    $("#orders").on('click', '.fa-copy', function () {
        id_order = $(this).data("id");
        window.location.href = "./copy_order.php?id=" + id_order;
    });

    $("#orders").on('click', '.fa-print', function () {
        id_order = $(this).data("id");
        $('input#order_id').val(id_order);
        document.getElementById('order_print').submit()
        /*$.post("./api.php", {
            action: "print_order",
            id: id_order,
        }).done(function (data) {
            if (data == '') {
                alert('Заказы не сформировались, т.к. заказывать нечего!');
            } else {
                alert('Заказы успешно сформированны: ' + data + '.');
            }
            window.location.href = data;
        });*/
        return false;
    });

    $('#check_show_all').change(function () {
        if ($(this).is(":checked")) {
            //var returnVal = confirm("Are you sure?");
            $(this).attr("checked", true);
        }
        // alert($(this).is(':checked'));
        $("#show_all").submit();
    });

    $('.payment').change(function () {
        var id = $(this).data("id");
        //var returnVal = confirm("Вы уверены что хотите изменить статус оплаты?");
        var returnVal = true;
        var val;
        if (this.checked) {
            val = 1;
        } else {
            val = 0;
        }
        if (returnVal) {
            $.post("./api.php", {
                action: "setPaymentStatus",
                id: id,
                value: val
            }).done(function (data) {
                if (data == "false") {
                    alert("Data Loaded: " + data);
                } else {
                    window.location.reload(true);
                }
            });
        } else {
            return false;
        }
        //console.log(returnVal);
        //console.log(id);
    });

    $('#orders').DataTable({
        "iDisplayLength": 100,
        "order": [[1, "desc"]]
    });

    $('#orders_filter input').val(paramStorage.getItem('searchStr'));
    $('#orders_filter input').keyup();
  
</script>
</body>
</html>
