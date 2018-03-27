<?php echo form_open(base_url('Organizer/Memberaccount/updateaccount'),array('name'=>'uplogin', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-edit"></i> Edit Member Account</h4>
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
	$(document).ready(function(){
		$("#return").inputmask('08[99999999999]');
	});
  // toogle chechkbox allow data
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
  
  //check email availabelity
  $('#Email').bind('keyup change', function() {
	var email = $('#Email').val();
    $.post('<?php echo base_url('Organizer/Memberaccount/checkemail'); ?>', {email: email}, function(d) {
                        if (d == 1)
                        {
                            $('#valsuccess').css('display', 'none');
                            $('#valfailed').css('display', 'block');
                            $('#btnedit').attr('disabled', 'disabled');
							
                        } 
						else if(d == 0)
                        {
                            $('#valfailed').css('display', 'none');
                            $('#valsuccess').css('display', 'block');
							$('#btnedit').removeAttr('disabled');
                        }
                    });
	});

  $('#return').bind('keyup change', function() {
    var hp = $(this).val().replace("_","");
    var eid = $(this).attr('id');
    $.post('<?php echo base_url('Register/checkphone'); ?>', {nohp: hp}, function(d) {
        if (d == 1)
            {
                $('#'+eid).parent().find('.text-danger').removeClass('hidden');
                $('#btnedit').attr('disabled', 'disabled');
            }
            else
            {
                $('#'+eid).parent().find('.text-danger').addClass('hidden');
                $('#btnedit').removeAttr('disabled');
            }
        });
    });
	
</script>