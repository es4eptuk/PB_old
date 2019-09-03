<?php 
include 'include/class.inc.php';

//print_r($_GET['month']);
$month_arr = $_GET['month'];


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
       Прибыль
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Фильтр</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
         <form>
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
               
               <div class="form-group">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" value="2018-08-01" name="month[]">
                      Август 2018
                    </label>
                  </div>

                  <div class="checkbox">
                    <label>
                      <input type="checkbox" value="2018-09-01" name="month[]">
                      Сентябрь 2018
                    </label>
                  </div>

                 <div class="checkbox">
                    <label>
                      <input type="checkbox" value="2018-10-01" name="month[]">
                      Октябрь 2018
                    </label>
                  </div>
                  
                   <div class="checkbox">
                    <label>
                      <input type="checkbox" value="2018-11-01" name="month[]">
                      Ноябрь 2018
                    </label>
                  </div>
                  
                   <div class="checkbox">
                    <label>
                      <input type="checkbox" value="2018-12-01" name="month[]">
                      Декабрь 2018
                    </label>
                  </div>
                  
                   <div class="checkbox">
                    <label>
                      <input type="checkbox" value="2019-01-01" name="month[]">
                      Январь 2019
                    </label>
                  </div>
                 
                </div>
               
            
            <!-- /.col -->
            
            
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer"> <!-- /.form-group -->
             
                <button type="submit" class="btn btn-primary" >Поиск</button>
              </div>
              
              </form>
      </div>
        <div class="box">
            <div class="box-body">
              <table class="table table-bordered table-striped" id="PP">
									<thead>    
                                    <tr>
                                      <th></th>
                                      <? 
                                      foreach ($month_arr as $key=>$value) {
                                        echo '<th>'.$value.'</th>' ;
                                      }
                                      ?>
                                      <th>Сумма за выбранный период</th>
                                    </tr>
                                     </thead>
                                   <tbody>
                                       
                                       <?php 
                
                $arr = $oneC->get_cat_income();
                $output_total_PP = "";
                if (isset($arr)) {
                foreach ($arr as &$category) {

                        $id_category = $category['id'];
                        $output_incom = "";
                        $output_incom_total = "";
                        $output_V2 = "";
                        $total_V2 = 0;
                        $output_V4 = "";
                        $total_V4 = 0;
                        $total_month = 0;
                       
                        foreach ($month_arr as $key => $value) {
                            if (isset( $total_month[$value]))  $total_month[$value] = 0;
                            if (isset( $incom_month[$value]))  $incom_month[$value] = 0;
                            $summ = 0;
                            $PP_month[$value] = 0;
                            $date_arr = date_parse($value);
                            //echo "<b>".$value."</b><br>";
                            $param['startDate'] = $value;
                            $time = strtotime($value);
                            $param['endDate'] =  date("Y-m-d", strtotime("+1 month", $time));
                            
                            $param['category'] = $id_category;
                            $income = $oneC->get_income($param);
                            if (isset($income)) {$summ = array_sum(array_column($income, 'summ'));} 
                            echo $total_month[$value]."  ";
                            //$total_month[$value] += $summ;
                            
                            //$output_incom_total.= "<td>".number_format($total_month[$value], 2, ',', ' ')."</td>";
                            $output_incom.="<td>".number_format($summ, 2, ',', ' ')."</td>";
                            
                            //$incom_month[$value]+= $summ;
                            
                            $sendV2 = $results->sendRobot($param['startDate'],$param['endDate'],2);
                            $output_V2.="<td>".count($sendV2)."</td>";
                            $total_V2+= count($sendV2);
                            
                            $sendV4 = $results->sendRobot($param['startDate'],$param['endDate'],4);
                            $output_V4.="<td>".count($sendV4)."</td>";
                            $total_V4+= count($sendV4);
                            
                            
                            
                        }
                        $output_incom.="<td>".number_format($total_month, 2, ',', ' ')."</td>";
                        $output_V2.="<td>".$total_V2."</td>";
                        $output_V4.="<td>".$total_V4."</td>";
                        
                       echo "
                                        <tr>
                                            <td>".$category['category_title']."</td>
                                            ".$output_incom."
                                        </tr>
                       ";
                    }
                }
                
                ?>
                                       
                                        <tr >
                                            <td><b>ИТОГО ПОСТУПЛЕНИЯ (C пересчетом ЦБ-5%)</b></td>
                                            <? echo $output_incom_total ?>
                                        </tr> 
                                        <tr >
                                           
                                        </tr> 
                                        
                                        <tr>
                                            <td>Кол-во отгруженных роботов V2</td>
                                            <? echo $output_V2; ?>
                                            
                                        </tr>
                                        
                                         <tr>
                                            <td>Кол-во отгруженных роботов V4</td>
                                            <? echo $output_V4; ?>
                                        </tr>
                                        
                                         <tr>
                                            <td>Задолженность покупателей RUR (к выплате)</td>
                                            <?  ?>
                                        </tr>
                                        
                                         <tr>
                                            <td>Задолженность покупателей USD (к выплате)</td>
                                            <?  ?>
                                        </tr>
                                        
                                         <tr>
                                            <td>Задолженность покупателей EUR (к выплате)</td>
                                            <?  ?>
                                        </tr>
                                        
                                         <tr>
                                            <td><b>ИТОГО ДЕБЕТОРКА  (C пересчетом ЦБ-5%)</b></td>
                                            <?  ?>
                                        </tr>
                                        
                                        
                                        
                                        <?  
                                            
                                             $arr_cat = $oneC->get_cost_category();
                                             foreach ($arr_cat as $key => $value) {
                                                 
                                                 echo " <tr>
                                                            <td class='text-red'>
                                                            ".$value['category_title']."
                                                            </td>
                                                       </tr>";
                                                 
                                                $arr_subcat = $oneC->get_cost_subcategory($value['id']); 
                                                 foreach ($arr_subcat as $key => $value) {
                                                     $output_PP = "";
                                                      $PP_cat_total = 0;
                                                      
                                                      foreach ($month_arr as $key_month => $month) {
                                                          
                                                        $param_PP['startDate'] = $month;
                                                        $time = strtotime($month);
                                                        $param_PP['endDate'] =  date("Y-m-d", strtotime("+1 month", $time));
                                                        $param_PP['purpose'] = $value['subcategory_title'];
                                                        $PP_month_cat = $oneC->get_stat_PP($param_PP);
                                                        $output_PP .= "<td>".number_format($PP_month_cat, 2, ',', ' ')."</td>";
                                                        
                                                        $PP_month[$month]+= $PP_month_cat;
                                                        
                                                        $PP_cat_total +=$PP_month_cat;
                                                      }
                                                      
                                                      $output_PP.="<td>".number_format($PP_cat_total, 2, ',', ' ')."</td>";
                                                      echo "<tr>
                                                            <td><span style='padding-left: 20px;'>".$value['subcategory_title']."</span></td>
                                                            ".$output_PP."
                                                        </tr>";
                                                      }
                                             }
                                             
                                             
                                              foreach ($month_arr as $key_month => $month) {
                                                
                                                $output_total_PP.= "<td>".number_format($PP_month[$month], 2, ',', ' ')."</td>";  
                                                  
                                              }
                                             
                                             
                                              echo "<tr><td><b>ИТОГО РАСХОДОВ</b></td>".$output_total_PP."</tr>";          
                                                        
                                             ?>
                                             
                                             
                                        
                                        
                                       
        
        
         </tbody>
                                    </table>
             
               </div>
            <!-- /.box-body -->
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
<script src="../../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
 <!-- Select2 -->
    <script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
<script>
    
    $.datepicker.regional['ru'] = {
        closeText: 'Закрыть',
        prevText: '&#x3c;Пред',
        nextText: 'След&#x3e;',
        currentText: 'Сегодня',
        monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
            'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
        monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
            'Июл','Авг','Сен','Окт','Ноя','Дек'],
        dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
        dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false
    };
    $.datepicker.setDefaults($.datepicker.regional['ru']);
    
    $('#startDate, #endDate').datepicker({
      format: 'dd.mm.yyyy',
      autoclose: true 
    })
    
    
     $( "#btn_search" ).click(function() {
     
     var param = {};
     var startDate = $("#startDate").val();
     var endDate = $("#endDate").val();
     var contragent = $("#contragent").val();
     var purpose = $("#purpose").val();
     
     if(startDate!="") { param['startDate'] = startDate;}
     if(endDate!="") { param['endDate'] = endDate;}
     if(contragent!="") { param['contragent'] = contragent;}
     if(purpose!="") { param['purpose'] = purpose;}
      $("#PP tbody").empty();
     
     var json = JSON.stringify(param)
     
     console.log(json);
     
      $.post( "./api.php", { 
                    action: "PP_search", 
                    param: param
                } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                 console.log(data); 
                
                 var PP = jQuery.parseJSON (data);
                 var sum = 0;
                   $.each(PP, function( index, value ) {
                       sum = sum + parseFloat(value['summ']);
                       $("#PP tbody").append("<tr> \
                       <td>"+value['number']+"</td> \
                       <td>"+value['date']+"</td> \
                       <td>"+value['contragent']+"</td> \
                       <td>"+number_format(value['summ'], 2, ',', ' ')+"</td> \
                       <td>"+value['description']+"</td> \
                       <tr> \
                       ");
                       
                   });
                 
                 
                  $("#PP tbody").append("<tr> \
                       <td></td> \
                       <td></td> \
                       <td><b class='pull-right'>Итого:</b></td> \
                       <td>"+number_format(sum, 2, ',', ' ')+"</td> \
                       <td></td> \
                       <tr> \
                       ");
                
               // window.location.reload(true);
                
              }
          });
     
     return false;
     
    
     
 });
 
 
 function number_format( number, decimals, dec_point, thousands_sep ) {	// Format a number with grouped thousands
	// 
	// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +	 bugfix by: Michael White (http://crestidg.com)

	var i, j, kw, kd, km;

	// input sanitation & defaults
	if( isNaN(decimals = Math.abs(decimals)) ){
		decimals = 2;
	}
	if( dec_point == undefined ){
		dec_point = ",";
	}
	if( thousands_sep == undefined ){
		thousands_sep = ".";
	}

	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

	if( (j = i.length) > 3 ){
		j = j % 3;
	} else{
		j = 0;
	}

	km = (j ? i.substr(0, j) + thousands_sep : "");
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	//kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
	kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


	return km + kw + kd;
}





</script>
</body>
</html>
