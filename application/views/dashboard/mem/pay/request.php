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
		<?php if ($ustatus) {
			if ($registperiod) { ?>
		<div class="row">
			<?php if ($lunas) { ?>
				<div class="col-md-12 col-sm-12 col-xs-12">
				<h3 class="text-center text-primary">Your Payment Has Been Completed</h3> <hr class="divider"/>
				<div class="panel panel-success">
					<div class="panel-body">
						<h3 class="text-center"><span class="label label-success"><i class="fa fa-check"></i> Please check on "My Payment" menu.</span></h3>
					</div>
				</div>
				</div>
			<?php } else { ?>	
				<?php if ($totreq>0) { ?>
				<div class="col-md-12 col-sm-12 col-xs-12">
				<h3 class="text-center text-primary">Request Is Unavailable</h3> <hr class="divider"/>
				<div class="panel panel-warning">
					<div class="panel-body">
						<h3 class="text-center"><span class="label label-warning"><i class="fa fa-exclamation-triangle"></i> Please wait until previous request is processed.</span></h3>
					</div>
				</div>
				</div>
			<?php } else { ?>
			<div class="col-md-8 col-sm-12 col-xs-12">
				<h3 class="text-center text-primary">Transfer Validation Form</h3> <hr class="divider"/>
				<?php echo form_open(base_url('Member/Confirmpay/sendrequest'),array('name'=>'uptransfer', 'method'=>'POST','class'=>'form-horizontal','id'=>"formrequest"));?>
				<?=$rdata;?>
				  	<div class="bg-red required hidden text-left">*Please make sure your input is not empty</div>
				  	<div class="panel-footer text-right">
			        <?=$inbtn;?>
			    	</div>
				<?php echo form_close(); ?>
		    </div>
		    <div class="col-md-4 col-sm-12 col-xs-12">
		    	<div class="panel panel-info">
		    		<div class="panel-body">
		    			<div class="table-responsive bg-info">
		    				<small>
								<table class="table table-condesed">
									<tbody><tr>
						                <th style="width:50%">Details</th>
						                <th>Amount</th>
						              </tr><tr>
						                <td>Price:</td>
						                <td><?=$price;?></td>
						              </tr>
						              <tr>
						                <td>Unique Code</td>
						                <td><?=$code;?></td>
						              </tr>
						              <tr>
						                <th><h4><b>Total:</b></h4></th>
						                <th><h4><b><?=$totpay;?></b></h4></th>
						              </tr>
						            </tbody>
								</table>
							</small>
						</div>
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

		<!-- Modal Confirm Request-->
		<div class="modal fade" id="confirm-send" tabindex="-1" role="dialog" aria-labelledby="myConfirmLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4><i class="fa fa-trash"></i> Are you sure want to request validation?</h4>
	            </div>
				
	            <div class="modal-footer">
	            	<div class="text-left"><small><i>PS: Make sure all data is correct.</i></small></div>
	                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
	                <a class="btn btn-primary btn-ok btn-sm" id="confirmsubmit">Send Request</a>
	            </div>
	        </div>
	    </div>
		</div>
			  
		<?php } }
		} else { ?>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<h3 class="text-center text-primary">Out of Payment Phase</h3> <hr class="divider"/>
				<div class="panel panel-warning">
					<div class="panel-body">
						<h3 class="text-center"><span class="label label-warning"><i class="fa fa-calendar"></i> Today is not payment phase. Payment phase is between <b><?=$startpay;?></b> until <b><?=$endpay;?></b></span></h3>
					</div>
				</div>
			</div>
		<?php } } else { ?>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<h3 class="text-center text-danger">Cannot Request Payment Validation</h3> <hr class="divider"/>
				<div class="panel panel-warning">
					<div class="panel-body">
						<h3 class="text-center"><span class="label label-info"><i class="fa fa-file-text"></i> You need to complete your registration data in "Registration" Menu</span></h3>
					</div>
				</div>
			</div>
		<?php } ?>
	    </div>
		</div>

    </div>
	</div>

	<script type="text/javascript">
	$(document).ready(function(){
				$("#tdate").inputmask('date');
				$("#accno").inputmask('[999999999999999999999999999999]');
				$("#tnomi").inputmask({mask:'[999999]','placeholder':'0'});
	
	$(document).on('click','#submitrequest',function(){
			var curInputs = $(this).closest('#formrequest').find("input[type='text'],select"),isValid=true;
			$(".col-sm-9").removeClass("has-error");
			for(var i=0; i<curInputs.length; i++)	
			{
				 if (!curInputs[i].validity.valid){
	                isValid = false;
	                $(curInputs[i]).closest(".col-sm-9").addClass("has-error");
	                $('.required').removeClass('hidden');
	                
	            }
			}
			if(isValid){
			$('.required').addClass('hidden');
			$("#confirm-send").modal("show");	
			}
		});
	$(document).on('click','#confirmsubmit',function(){
		$("#formrequest").submit();
		});
	});
	</script>

</section>