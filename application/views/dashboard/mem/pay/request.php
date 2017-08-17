<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-edit fa-lg"></i> Request<small>Validation</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	
	<div class="box box-primary">
	<div class="box-body">
		<div class="panel panel-default">
		<div class="panel-body">
		<?php if ($registperiod) { ?>
		<div class="row">
			<?php if ($lunas) { ?>
				<div class="col-md-12 col-sm-12 col-xs-12">
				<h3 class="text-center text-primary">Your Payment is Completed</h3> <hr class="divider"/>
				<div class="panel panel-success">
					<div class="panel-body">
						<h3 class="text-center"><span class="label label-success"><i class="fa fa-check"></i> Please check on "My Payment" menu.</span></h3>
					</div>
				</div>
				</div>
			<?php } else { ?>	
				<?php if ($totreq>0) { ?>
				<div class="col-md-12 col-sm-12 col-xs-12">
				<h3 class="text-center text-primary">Request Unavailable</h3> <hr class="divider"/>
				<div class="panel panel-warning">
					<div class="panel-body">
						<h3 class="text-center"><span class="label label-warning"><i class="fa fa-exclamation-triangle"></i> Please wait until previous request is being processed.</span></h3>
					</div>
				</div>
				</div>
			<?php } else { ?>
			<div class="col-md-8 col-sm-12 col-xs-12">
				<h3 class="text-center text-primary">Transfer Validation Form</h3> <hr class="divider"/>
				<?php echo form_open(base_url('Member/Confirmpay/sendrequest'),array('name'=>'uptransfer', 'method'=>'POST','class'=>'form-horizontal'));?>
				<?=$rdata;?>
		    </div>
		    <div class="col-md-4 col-sm-12 col-xs-12">
		    	<div class="panel panel-info">
		    		<div class="panel-body">
		    			<p class="text-info">Current Price <b>Rp. <?=$price;?></b></p>
		    			<p class="text-info">Your Unique Code <b><i><?=$code;?></i></b></p>
		    			<h4 class="text-success bg-green">Total Price Rp. <?=($price+$code);?></h4>
		    		</div>
		    	</div>
		    	<div class="panel panel-primary">
		    		<div class="panel-body">
		    			<p class="text-center text-primary"><b>Previous Request</b></p>
		    			<div class="table-responsive">
		    			<?=$prevreq;?>
		    			</div>
		    		</div>
		    	</div>
		    </div>
		</div>
		    <div class="panel-footer text-right">
		        <?=$inbtn;?>
		    </div>
		<?php echo form_close(); } }
		} else { ?>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<h3 class="text-center text-primary">Out of Payment Phase</h3> <hr class="divider"/>
				<div class="panel panel-warning">
					<div class="panel-body">
						<h4 class="text-center"><span class="label label-warning"><i class="fa fa-calendar"></i> Today is not payment phase. Payment phase is between <b><?=$startpay;?></b> until <b><?=$endpay;?></b></span></h4>
					</div>
				</div>
				</div>
		<?php }	?>
	    </div>
		</div>

    </div>
	</div>

	<script type="text/javascript">
	$(document).ready(function(){
				$("#tdate").inputmask('date');
		    });
	</script>

</section>