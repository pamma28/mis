<?php echo form_open_multipart(base_url('Organizer/Design/savedesign'),array('name'=>'addlogin', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-plus"></i> Add Certificate Design</h4>
</div>
<div class="modal-body">
	
	<?=$rdata;?>
	
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inbtn;?>
</div>
<script type="text/javascript">
	$(function() {
    $('#iddefault').bootstrapToggle({
		size: "small",
		onstyle: "primary",
		offstyle: "default",
		width: 100,
		height: 35,
		default:false
	});
  })
</script>
<?php echo form_close();?>