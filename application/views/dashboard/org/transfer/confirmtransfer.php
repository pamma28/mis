<?php echo form_open(base_url('Organizer/Transfer/updatetransfer'),array('name'=>'uplogin', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-credit-card"></i> Confirm/Reject Transfer Payment</h4>
</div>
<div class="modal-body">
	
	<?=$rdata;?>
	
</div>
<div class="modal-footer">
	<?=$inid;?>
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Cancel</a>
	<?=$inbtn;?>
</div>
<?php echo form_close();?>
<script>
   $(function() {
    $('#idconfirm').bootstrapToggle({
		size: "large",
		onstyle: "success",
		offstyle: "danger",
		width: 300,
		height: 50
	});
  })
	
</script>