<?php echo form_open(base_url('Admin/Managelogin/updatelogin'),array('name'=>'uplogin', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-edit"></i> Edit Account</h4>
</div>
<div class="modal-body">
	
	<?=$rdata;?>
	
</div>
<div class="modal-footer">
	<?=$inid;?>
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inbtn;?>
</div>
<?php echo form_close();?>
<script>
  $(function() {
    $('#idallow').bootstrapToggle({
		on: "Allow",
		off: "Deny",
		size: "large",
		onstyle: "primary",
		offstyle: "danger",
		width: 80,
		height: 35
	});
  })
  
  $('#Email').bind('keyup change', function() {
	var email = $('#Email').val();
    $.post('<?php echo base_url('Admin/Managelogin/checkemail'); ?>', {email: email}, function(d) {
                        if (d == 1)
                        {
                            $('#valsuccess').css('display', 'none');
                            $('#valfailed').css('display', 'block');
							$('#submit').attr('disabled', 'disabled');
                        }
                        else
                        {
                            $('#valfailed').css('display', 'none');
                            $('#valsuccess').css('display', 'block');
							$('#submit').removeAttr('disabled');
                        }
                    });
	});
	
</script>