<?php echo form_open(base_url('Organizer/Question/updateqtype'),array('name'=>'editqtype', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-pencil"></i> Edit Question Type</h4>
</div>
<div class="modal-body">
	
	<?=$rdata;?>
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inid.$inbtn;?>
</div>
<?php echo form_close();?>