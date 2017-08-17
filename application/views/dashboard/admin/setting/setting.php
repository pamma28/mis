<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Setting <small>Parameters</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<section class="content">
<div class="row">
<div class="col-md-6">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title" align="center">Regular Class Parameter</h3>
		</div>
		<div class="panel-body">
		<?php
		echo form_open('setting/parameter',array('name'=>'setting', 'method'=>'POST','class'=>'form-horizontal'));
		?>
			 <div class="form-group">
				<label for="inputperiod" class="col-sm-2 control-label">Recent Period</label>
				<div class="col-sm-10">
				  <?=$inper;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputprice" class="col-sm-2 control-label">Price</label>
				<div class="col-sm-10">
				  <?=$inpr;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputquota" class="col-sm-2 control-label">Qouta</label>
				<div class="col-sm-10">
				  <?=$inq;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputbegin" class="col-sm-2 control-label">Begin Registration</label>
				<div class="col-sm-10">
				  <?=$inbgn;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputend" class="col-sm-2 control-label">End Registration</label>
				<div class="col-sm-10">
				  <?=$inov;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputbank" class="col-sm-2 control-label">Bank Name</label>
				<div class="col-sm-10">
				  <?=$injatm;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputbankan" class="col-sm-2 control-label">Bank Belonging (a.n)</label>
				<div class="col-sm-10">
				  <?=$inan;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputbanknum" class="col-sm-2 control-label">Bank Account Number</label>
				<div class="col-sm-10">
				  <?=$inatm;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputcp" class="col-sm-2 control-label">CP Number</label>
				<div class="col-sm-10">
				  <?=$inhp;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputbbm" class="col-sm-2 control-label">CP BBM</label>
				<div class="col-sm-10">
				  <?=$inbbm;?>
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputmail" class="col-sm-2 control-label">CP Email</label>
				<div class="col-sm-10">
				  <?=$inemail;?>
				</div>
			  </div>
			  <hr class="divider"/>
			  <div class="form-group">
				<div class="col-sm-4 pull-right">
				  <?=$insend;?>
				</div>
			  </div>
        

		<?php form_close(); ?>


		</div>
	</div>

</div>
<div class="col-md-6">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title" align="center">Announcement</h3>
		</div>
		<div class="panel-body">
		
		</div>
	</div>

</div>


	<script>
	$(document).ready(function(){
    $("#begindate").inputmask('date');
    $("#overdate").inputmask('date');}
	);
	</script>					
</section>