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
    
    .robot {
        background-color: beige;
        height: 96px;
        float: left;
        width: 144px;
        margin: 10px;
        text-align: center;
        
    }
    
    .robot span {
        display: none;
        
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
       План производства
        
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Месяц
              </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive" >
                
             
           <div class="box-group" id="accordion">
               
                
                
            <?php
             $arr = $robots->get_robots();
               
                if (isset($arr)) {
                foreach ($arr as &$robot) {
                   
                    
                  echo '
                  <div class="panel box box-primary" id="'.$robot['id'].'">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$robot['id'].'" aria-expanded="false" class="collapsed">
                        '.$robot['name'].'
                      </a>
                    </h4>
                  </div>
                  <div id="collapse'.$robot['id'].'" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                    <div class="box-body">
                       <h3>4.'.$robot['number'].'</h3>
                    </div>
                  </div>
                </div>
                    ';  
                }
                
                }
            ?>
           
               
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



	

<?php include 'template/scripts.php';?>
<script>
$( function() {
    
    $( "#accordion" ).sortable({
  stop: function( event, ui ) {
       var arr_robot = [];
       var id_ = 'accordion';
       var cols_ = document.querySelectorAll('#' + id_ + ' .panel');
       
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
}).disableSelection();;
    
  } );
  
  
  
</script>
</body>
</html>
