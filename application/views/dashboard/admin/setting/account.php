<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-wrench fa-lg"></i> Account<small>Setting</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-md-6"> 
				<?php echo form_open(base_url('Member/Setting'),array('name'=>'fdetailacc','class'=>'form-horizontal','method'=>'POST'));?>
					<div class="panel panel-primary">
						<div class="panel-heading panel-heading-sm">
							<h3 class="panel-title text-center"><span class="fa fa-cogs"></span> <b><?=$acc['title'];?></b></h3>
						</div>
					<div class="panel-body">
						<?php if ($this->session->flashdata('vpho')!=null){ ?>
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<?=$this->session->flashdata('vpho');?>
						</div>
						<?php } else if ($this->session->flashdata('xpho')!=null){ ?>
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<?=$this->session->flashdata('xpho');?>
						</div>		
						<?php } ?>
						<?php if ($this->session->flashdata('vacc')!=null){ ?>
							<div class="alert alert-success alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<?=$this->session->flashdata('vacc');?>
							</div>
							<?php } else if ($this->session->flashdata('xacc')!=null){ ?>
							<div class="alert alert-danger alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<?=$this->session->flashdata('xacc');?>
							</div>		
							<?php } ?>
								<?=$acc['table'];?>
					</div>
					<div class="panel-footer text-right">
						<?=$acc['finputs'];?>
						<?=$acc['fbtn'];?>
						</div>
					</div>
					<?php  echo form_close(); ?> 
			</div>	
		
		
		<div class="col-md-6"> 
			<?php echo form_open(base_url('Member/Setting'),array('name'=>'fpass','class'=>'form-horizontal','method'=>'POST'));?>
				<div class="panel panel-primary">
					<div class="panel-heading panel-heading-sm">
						<h3 class="panel-title text-center"><span class="fa fa-key"></span> <b><?=$pass['title'];?></b></h3>
					</div>
				<div class="panel-body">
					<?php if ($this->session->flashdata('vpass')!=null){ ?>
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<?=$this->session->flashdata('vpass');?>
						</div>
						<?php } else if ($this->session->flashdata('xpass')!=null){ ?>
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<?=$this->session->flashdata('xpass');?>
						</div>		
						<?php } ?>
							<?=$pass['table'];?>
				</div>
				<div class="panel-footer text-right">
					<?=$pass['finputs'];?>
					<?=$pass['fbtn'];?>
					</div>
				</div>
				<?php  echo form_close(); ?> 
		</div>
	</div>
	</div>	
</div>
		
		
	
	
	<!-- Modal Foto-->
	<div class="modal fade" id="fotoModal" tabindex="-1" role="dialog" aria-labelledby="fotoLabel" aria-hidden="true">
    <div class="modal-dialog">
       <div class="modal-content">
        	<div class="modal-header">
			   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			   <h4><i class="fa fa-image"></i> <span id="titlepreview"> <b><?=$pho['title'];?></b></span></h4>
			</div>
			<div class="modal-body">
				<?php echo form_open_multipart(base_url('Member/Setting'),array('name'=>'fphoacc','class'=>'form-horizontal','method'=>'POST'));?>
				
					
							<?=$pho['table'];?>
				
			</div>
			<div class="modal-footer text-right">
				<?=$pho['finputs'];?>
				<?=$pho['fbtn'];?>
			</div>
			<?php  echo form_close(); ?> 
			</div>	
        </div>
    </div>
	</div>
	
	
	<script type="text/javascript">
	$(document).ready(function(){
		$('#fuemail').bind('keyup change', function(e) {
			var email = $(this).val(), emailnow = $("#emailnow").text();
			var eid = $(this).attr('id');
			$.post('<?php echo base_url('Register/checkemail'); ?>', {email: email}, function(d) {
								if ((d == 1) && (email!=emailnow))
								{
									$('#'+eid).parent().find('.text-danger').removeClass('hidden');
									$('#btnupdateacc').attr('disabled', 'disabled');
								}
								else
								{
									$('#'+eid).parent().find('.text-danger').addClass('hidden');
									$('#btnupdateacc').removeAttr('disabled');
								}
							});
			});

		$('#fupassnew').on('change', function(e) {
			var newpass = $(this).val(), oldpass = $("#fupassold").val();
			var eid = $(this).attr('id');
			if (newpass==oldpass){
				alert("Your new password is the same as old password");
				$(this).val('');
				}
			});
		$('#fupassnew2').on('change', function(e) {
			var newpass2 = $(this).val(), newpass = $("#fupassnew").val();
			var eid = $(this).attr('id');
			if (newpass2!=newpass){
				alert("Your confirmation of new password is not match");
				$(this).val('');
				}
			});
		
		$.uploadPreview({
		    input_field: "#fufoto",   // Default: .image-upload
		    preview_box: "#prevmyphoto",  // Default: .image-preview
		    no_label: true                 // Default: false
		  });
	});
	</script>
</section>