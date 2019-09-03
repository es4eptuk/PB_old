<?php 
include 'include/class.inc.php';
?>
<?php include 'template/head.html' ?>

<!DOCTYPE html>
<html>
<head>
	<title>Добавить событие</title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<? include 'template/header.html' ?>
		<!-- Left side column. contains the logo and sidebar -->
		<?php include 'template/sidebar.html';?>
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>Планировщик</h1>
			</section><!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">Добавить событие</h3>
							</div><!-- /.box-header -->
							<div class="box-body">
							    <div class="col-xs-1" style="padding-top: 27px">
							       <button class="btn btn-block btn-success btn-xs" id="btnAdd">Добавить</button>   
							    </div>
						      	<div class="col-xs-6">
						      	<div id="items">
						      	    <div class="row" >
                                    <div class="col-xs-3" style="padding-bottom: 10px;">
                                          <label>Дата  </label>
                                      <input type="text" class="form-control" name="date[]" class="date" >
                                    </div>
                                    <div class="col-xs-5">
                                    
                                          <label>Задача </label>
                                          <select class="form-control">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                          </select>
                                       
                                    </div>
                                    <div class="col-xs-4">
                                          <label>Затраченное время, % </label>
                                      <input type="text" class="form-control" name="time[]" placeholder="100">
                                    </div>
                                  </div>
						      	</div>    
						        
                                </div>  

							</div><!-- /.box-body -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</section><!-- /.content -->
		</div><!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
		<div class="control-sidebar-bg"></div>
	</div><!-- ./wrapper -->

	<?php include './template/scripts.html'; ?>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- bootstrap datepicker -->
<script src="../../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function(){


var d = new Date();
var curr_date = d.getDate();
var curr_month = d.getMonth() + 1;
var curr_year = d.getFullYear();
var formated_date = curr_date+"."+curr_month+"."+curr_year;

//$('.date').val(formated_date);

//Date picker
    $('.date').datepicker({
      autoclose: true
    })



	
	$('#btnAdd').click(function() {
		$('    <div class="row" > \
                    <div class="col-xs-3" style="padding-bottom: 10px;"> \
                            <label>Дата  </label>\
                            <input type="text" class="form-control" name="date[]" >\
                    </div>\
                    <div class="col-xs-5"> \
                            <label>Задача </label> \
                            <select class="form-control"> \
                                   <option>option 1</option>\
                                    <option>option 2</option>\
                                    <option>option 3</option>\
                                    <option>option 4</option>\
                                    <option>option 5</option>\
                                          </select>\
                    </div> \
                    <div class="col-xs-4"> \
                            <label>Затраченное время, % </label> \
                            <input type="text" class="form-control" name="time[]" placeholder="100"> \
                                    </div> \
                                  </div>').fadeIn('slow').appendTo('#items');
	});
	
// here's our click function for when the forms submitted
	
	$('.submit').click(function(){
								
	
	var answers = [];
    $.each($('.field'), function() {
        answers.push($(this).val()); 
    });
	
    if(answers.length == 0) { 
        answers = "none"; 
    }   

	alert(answers);
	
	return false;
								
	});
	
	

});

</script>
</body>
</html>