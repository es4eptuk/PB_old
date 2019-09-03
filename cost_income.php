<?php 
include 'include/class.inc.php';

//$robot_info = $robots->get_info_robot($_GET['id']);
//$robot_number = $robot_info['number'];
//$robot_name= $robot_info['name'];
//$robot_id= $robot_info['id'];
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
       Доходы
        
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
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
             <div class="form-group">
                  <label>Дата</label>
                  <input type="text" class="form-control" id="startDate" placeholder="Начало ...">
                </div>
                
               
               <div class="form-group">
                <label>Категория</label>
                <select class="form-control select3" style="width: 100%;" id="category">
                     <option value="0">Выберите категорию</option>
                  <?
                   $arr_cat = $oneC->get_cat_income();
                   
                          foreach ($arr_cat as &$value) {
                                echo '<option value="'.$value['id'].'">'.$value['title'].'</option>';
                            }
                   
                   print_r($arr_cat);
                  ?>
                </select>
              </div>
             
            </div>
            <!-- /.col -->
            <div class="col-md-6">
                
                 <div class="form-group">
                  <label>Дата</label>
                  <input type="text" class="form-control" id="endDate"placeholder="Конец ...">
                </div> 

            
            
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer"> <!-- /.form-group -->
              <a href="#">Сбросить фильтр</a>
                <button type="submit" class="btn btn-primary pull-right" id="btn_search">Поиск</button>
              </div>
      </div>
        <div class="box">
            <div class="box-body">
              <table class="table table-bordered table-striped" id="PP">
									<thead>    
                                    <tr>
                                      <th>Номер</th>
                                      <th>Дата</th>
                                      <th>Контрагент</th>
                                      <th>Назначение</th>
                                      <th>Категория</th>
                                      <th>Сумма</th>
                                       <th>Валюта</th>
                                    </tr>
                                     </thead>
                                   <tbody>
        
        
       
        
        
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

<div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="change_cat" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Изменить категорию</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
						<!-- select -->
						<div class="form-group">
                  <label>Категория</label>
                  <select class="form-control" id="income_cat">
                   
                  </select>
                </div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-dismiss="modal" type="button">Закрыть</button> <button class="btn btn-primary" id="btn_change_cat" type="button">Сохранить</button>
				</div>
			</div>
		</div>
	</div>
	
<!-- ./wrapper -->
<?php include "./template/scripts.html";?>
<script src="../../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
 <!-- Select2 -->
    <script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
<script>
   var id_edit = 0; 
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
     var category = $("#category").val();
     
     if(startDate!="") { param['startDate'] = startDate;}
     if(endDate!="") { param['endDate'] = endDate;}
     if(category!="") { param['category'] = category;}
      $("#PP tbody").empty();
     
     var json = JSON.stringify(param)
     
     //console.log(json);
     
      $.post( "./api.php", { 
                    action: "income_search", 
                    param: param
                } )
          .done(function( data ) {
              if (data=="false") {alert( "Data Loaded: " + data ); }
              else {
                 //console.log(data); 
                
                 var PP = jQuery.parseJSON (data);
                 var sum = 0;
                   $.each(PP, function( index, value ) {
                       sum = sum + parseFloat(value['summ']);
                       $("#PP tbody").append("<tr> \
                       <td>"+value['number']+"</td> \
                       <td>"+value['date']+"</td> \
                       <td>"+value['contragent']+"</td> \
                       <td>"+value['description']+"</td> \
                       <td><a href='#' onclick='change_cat("+value['id']+","+value['category_id']+"); return false;' id='income_"+value['id']+"'>"+value['category']+"</a></td> \
                       <td>"+number_format(value['summ'], 2, ',', ' ')+"</td> \
                       <td>"+value['currency']+"</td> \
                       <tr> \
                       ");
                       
                   });
                 
                 
                  $("#PP tbody").append("<tr> \
                       <td></td> \
                       <td></td> \
                       <td></td> \
                       <td></td> \
                       <td><b class='pull-right'>Итого:</b></td> \
                       <td>"+number_format(sum, 2, ',', ' ')+"</td> \
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

function change_cat(id,value) {
     $('#change_cat').modal('show');
       id_edit = id;
       $.post( "./api.php", { 
                            action: "1c_get_cat_income"
                        } )
                              .done(function( data ) {
                                  if (data=="false") {alert( "Data Loaded: " + data ); }
                                  else {
                                    var obj = jQuery.parseJSON (data);
                                     $('#income_cat').empty();
                                    $.each( obj, function( key, value ) {
                                        if (value['id'] == value) {
                                              $('#income_cat').append("<option value='"+value['id']+"' selected>"+value['category_title']+"</option>");
                                        } else {
                                             $('#income_cat').append("<option value='"+value['id']+"'>"+value['category_title']+"</option>");
                                        }
                                     
                                    });
                                    //console.log(obj);  
                                     //$('#update').text("Изменено: " + obj['update_date'] + "  (" +obj2['user_name'] +")");
                                     
                                     
                                    
                                  }
                              });
    
}

 $("#btn_change_cat").click(function() {
      var category = $('#income_cat').val();
     
      $.post( "./api.php", { 
                            action: "1c_change_income_cat",
                            id: id_edit,
                            value: category
                        } )
                              .done(function( data ) {
                                  if (data=="false") {alert( "Data Loaded: " + data ); }
                                  else {
                                    $('#change_cat').modal('hide');  
                                    console.log('#income_'+id_edit);
                                    console.log($('#income_'+id_edit).text());
                                    var text = $("#income_cat option:selected").text();
                                    $('#income_'+id_edit).text(text);
                                   // $('#PP').children('#income_'+id_edit).text("data");
                                  }
                              });
     

 });


  $(function () {
   
    $('#PP').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })


</script>
</body>
</html>
