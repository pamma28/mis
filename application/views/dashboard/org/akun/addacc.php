<?php echo form_open(base_url('Organizer/Memberaccount/saveaccount'),array('name'=>'addlogin', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-plus"></i> Add Member Account</h4>
</div>
<div class="modal-body">
	
	<?=$rdata;?>
	
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inbtn;?>
</div>
<?php echo form_close();?>
<script>
    $(document).ready(function(){
        
        $("#nohp").inputmask('08[99999999999]');
    });

    $('#togglePassword').on('click', function(){
            $(this).find("i.fa").toggleClass('fa-eye fa-eye-slash');
            var type    = ($(this).find("i.fa").hasClass('fa-eye-slash') ? 'text' : 'password');
            var input   = $('#Password');
            var replace = input.clone().attr('type', type);
            input.replaceWith(replace);
        });

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
  });
  
  $('#Email').bind('keyup change', function() {
	var email = $('#Email').val();
    $.post('<?php echo base_url('Organizer/Memberaccount/checkemail'); ?>', {email: email}, function(d) {
                        if (d == 1)
                        {
                            $('#valsuccess').css('display', 'none');
                            $('#valfailed').css('display', 'block');
                        }
                        else
                        {
                            $('#valfailed').css('display', 'none');
                            $('#valsuccess').css('display', 'block');
                        }
                    });
	});
	
	$('#Username').bind('keyup change', function() {
	var user = $('#Username').val();
    $.post('<?php echo base_url('Organizer/Memberaccount/checkuser'); ?>', {user: user}, function(d) {
                        if (d == 1)
                        {
                            $('#usuccess').css('display', 'none');
                            $('#ufailed').css('display', 'block');
							$('#submit').attr('disabled', 'disabled');
                        }
                        else
                        {
                            $('#ufailed').css('display', 'none');
                            $('#usuccess').css('display', 'block');
							$('#submit').removeAttr('disabled');
                        }
                    });
	});
</script>