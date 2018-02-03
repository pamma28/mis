<?php echo form_open(base_url('Organizer/Template/savetmp'),array('name'=>'addtmp', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-pencil-square"></i> Add Template</h4>
</div>
<div class="modal-body">
	<div class="well">
								<h4 class="text-center"><strong>Special Code</strong></h4>
									<div class="col-md-6">
									<div class="bg-info">
										<dl class="dl-horizontal">
											<dt><code>{honor}</code></dt> <dd>: Mr./Ms.</dd>
											<dt><code>{name}</code></dt> <dd>: Full Name</dd>
											<dt><code>{NIM}</code></dt> <dd>: NIM</dd>
											<dt><code>{faculty}</code></dt> <dd>: Faculty</dd>
											<dt><code>{period}</code></dt> <dd>: Period</dd>
										</dl>
									</div>
									</div>
									<div class="col-md-6">
									<div class="bg-info">
										<dl class="dl-horizontal">
											<dt><code>{email}</code></dt> <dd>: Email</dd>
											<dt><code>{phone}</code></dt> <dd>: Phone Number</dd>
											<dt><code>{level}</code></dt> <dd>: Level (if any)</dd>
											<dt><code>{payment}</code></dt> <dd>: Payment Status</dd>
											<dt><code>{birthdate}</code></dt> <dd>: Birthdate</dd>
										</dl>
									</div>
									</div>
									<p class="text-info">Note: Put the code in Template Content and it will change into each spesific detail of Registrant/Mail Recipient. <b class="label label-info">e.g: {name} will send as "Bill Gates"</b></p>
							</div> 
	<?=$rdata;?>
</div>
<div class="modal-footer">
	<?=$inid;?>
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inbtn;?>
</div>
<?php echo form_close();?>
<script>
$(document).ready(function() {
$("#fcont").summernote({
			 minHeight: 300
		});	
});
</script>
