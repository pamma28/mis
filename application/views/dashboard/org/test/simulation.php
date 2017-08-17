<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Placement <small>Test</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<section class="content">

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">Placement Test</h3>
	</div>
	<div class="panel-body">
	<div class="row">
		<div class="col-md-3">
		<div class="panel panel-info disabled">
			<div class="panel-heading">
			<h3 class="panel-title" align="center">Identity</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-5">
					Name <br/>
					NIM <br/>
					Date <br/>
					Time 
					</div>
					<div class="col-md-7">
					: Budi <br/>
					: B1G7121 <br/>
					: 2015/06/06 <br/>
					: 17:44:02 
					</div>
				</div>
			</div>
			<div class="panel-footer">
			<small>Supervised by </small> </div>
		</div>
		</div><!-- end biodata-->
		
		<div class="col-md-3 col-md-offset-6">
		<div class="panel panel-danger disabled">
			<div class="panel-body">
			<h3 class="panel-title" align="center">Time <span id="time"></span></h3>
			</div>
		</div>
		</div><!-- end time-->
		
	</div>
	<hr class="clearboth"/>
	
		<h4>Section Reading</h4>
		<strong>instruction</strong>
	<div class="box">
		<div class="box-body">
		1. Soal disini
		</div>
		<div class="box-footer">
			<div class="row">
				<div class="col-md-6">
				A. asdasdas
				<br/>
				B. askjasbfa
				<br/>
				C. asdasdas
				</div>
				<div class="col-md-6">
				D. owieutowieuy
				<br/>
				E. woieioewhty
				</div>
			</div>
		</div>
	</div>
	
</div>
</div>				
</section>
<script type="text/javascript">
var t= new Date();
t.setMinutes(t.getMinutes() + 30);
$('#time').countdown(t)
	.on('update.countdown', function(event) {
	var format = '%H:%M:%S';
	$(this).html(event.strftime(format));
	})
	.on('finish.countdown', function(event) {
	$(this).html('This time has expired!')
	.parent().addClass('disabled');
	});
</script>