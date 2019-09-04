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
       Заказы
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $position->get_name_category($_GET['id']) ?></h3>
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
                       
                        <td><div style=\"text-align: center;\"><input type='checkbox' id='" .$pos['order_id']."' class='payment' ".$checkPayment." ></div></td>
                         <td>".$user_info['user_name']."</td>
                         
                        <td><i class='fa fa-2x fa-copy' style='cursor: pointer;' id='".$pos['order_id']."'></i></td>
                        <td><i class='fa fa-2x fa-pencil' style='cursor: pointer;' id='".$pos['order_id']."'></i></td>
                       
                       
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
var id_order=0;
paramStorage = localStorage;


    $( "#orders .fa-pencil" ).click(function() {
            id_order = $(this).attr("id");
            window.location.href = "./edit_order.php?id=" + id_order;   
            paramStorage.searchStr = $('#orders_filter input').val();
            
         
    });
    
     $( "#orders .fa-copy" ).click(function() {
            id_order = $(this).attr("id");
            window.location.href = "./copy_order.php?id=" + id_order;    
         
    });



 
  
  
  $( "#save_close" ).click(function() { 
  save_close();
  return false;
  });
  
    $( "#delete" ).click(function() { 
  delete_pos();
  return false;
  });
  
   function delete_pos() {
     var category =  $('#category').val();
     $.post( "./api.php", { 
        action: "delete_pos", 
        id: id_pos
        
        
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./pos.php?id="+category;  
              }
          });
    
   }
  
  function save_close() {
    var title =  $('#title').val();
    var longtitle =  $('#longtitle').val();
    var category =  $('#category').val();
    var subcategory =  $('#subcategory').val();
    var vendorcode =  $('#vendorcode').val();
    var provider =  $('#provider').val();
    var price =  $('#price').val();
    var quant_robot =  $('#quant_robot').val(); 
    var quant_total =  $('#quant_total').val();
    
    
      $.post( "./api.php", { 
        action: "edit_pos", 
        id: id_pos,
        title: title,
        longtitle: longtitle ,
        category: category ,
        subcategory: subcategory ,
        vendorcode: vendorcode ,
        provider: provider ,
        price: price ,
        quant_robot: quant_robot ,
        quant_total: quant_total 
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                window.location.href = "./pos.php?id="+category;  
              }
          });
    
 }
 
 function get_info_user(id) {
     
      $.post( "./api.php", { 
        action: "get_info_user", 
        id: id
    } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                var obj = jQuery.parseJSON (data);
                //console.log(obj['user_name']);  
                return obj['user_name'] ; 
              }
          });
          
    $("#orders").on("click", "td .fa-copy", function() {
        alert('ХУЙ');
        return false;
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
 
  $('#orders').DataTable({
       "iDisplayLength": 100,
        "order": [[ 1, "desc" ]]
    } );
  
  $('#orders_filter input').val(paramStorage.getItem('searchStr'));
  $('#orders_filter input').keyup(); 
  
</script>
</body>
</html>
