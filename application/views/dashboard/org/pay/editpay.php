<?php echo form_open(base_url('Organizer/Payment/updatepay'),array('name'=>'uplogin', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-file-text"></i> Edit Registration Data</h4>
</div>
<div class="modal-body">
	
	<?=$rdata;?>
	
</div>
<div class="modal-footer">
	<?=$inid.$instat.$invto;?>
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inbtn;?>
</div>
<?php echo form_close();?>
<script>
  $('.selectpicker').selectpicker({
		  size: 6
		});
	
	var b = moment($('#issued').text());
	var a = moment($("input[name='fvtoo']").val());
	$('#validto').val(a.diff(b, 'months'));
	$('#nomi,#paid,#change,#validto').on('change',function(e) {
				$('#change').val($('#nomi').val()-$('#paid').val());
				$('input[name="fvtoo"]').val(moment().add('M',$('#validto').val()).format('YYYY-MM-DD HH:mm:ss'));
			});
	$("#nomi,#paid,#change,#validto").numeric();
	
	
</script>