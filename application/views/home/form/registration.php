<section class="content-header">
          <h1>
            Registration <small>New Member</small>
            
          </h1>
</section>

<section class="content">
	<div class="box box-primary">
		<div class="box-body">
		<h3 class="text-center">Registration Form <?=$period;?></h3>
				<section>
				<div class="wizard">
					<div class="wizard-inner">
						<div class="connecting-line"></div>
						<ul class="nav nav-tabs" role="tablist">

							<li role="presentation" class="active">
								<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1">
									<span class="round-tab">
										<i class="fa fa-user"></i>
									</span>
								</a>
							</li>

	
							<li role="presentation" class="disabled">
								<a href="#step2" data-toggle="tab" aria-controls="step3" role="tab" title="Step 2">
									<span class="round-tab">
										<i class="fa fa-edit"></i>
									</span>
								</a>
							</li>

							<li role="presentation" class="disabled">
								<a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
									<span class="round-tab">
										<i class="fa fa-check"></i>
									</span>
								</a>
							</li>
						</ul>
					</div>

		
					<form role="form" action="<?=base_url('Register/save');?>" method="POST" name="PDS">
						<div class="tab-content">
							<div class="tab-pane active" role="tabpanel" id="step1">
								<div class="panel panel-primary">
									<div class="panel-heading">
									<h4 class="panel-title text-center"><i class="fa fa-user fa-lg"></i> Basic Information</h4>
									</div>
									<div class="panel-body">

												<?php
														if ((validation_errors()) or ($this->session->flashdata("failedregist")!=null)){?>
											      <div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											      <?php echo '<h5 class="text-center"><b>Registration Failed</b></h5><p class="text-center"><i>'.validation_errors().$this->session->flashdata('failedregist').'</i></p></div>';} ?>


												<div class="form-group">
													<h5 class="text-center"><b class="label label-primary "><i>Your NIM will be your Username</i></b></h5>
													<?=$inuser;?>
													<p class="text-danger hidden"><b><i>NIM has been registered</i></b></p>
												</div>
												<div class="form-group">
															<?=$innama;?>
												</div>
												<div class="row">
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$ingen;?>
														</div>
													</div>
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$infac;?>
														</div>
													</div>
												</div>
												

												<div class="row">
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$inhp;?>
														</div>
													</div>
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$inemail;?>
															<p class="text-danger hidden"><b><i>Email has been registered</i></b></p>
														</div>
													</div>
												</div>											
											
										<p class="text-danger required hidden">* Please complete the required input(s)</p>
										</div>
									<div class="panel-footer text-right">
										<button type="button" class="btn btn-primary next-step" id="btncontinue">Continue</button>
									
									</div>
								</div>
							</div>
							<div class="tab-pane" role="tabpanel" id="step2">
								<div class="panel panel-primary">
									<div class="panel-heading">
									<h4 class="panel-title text-center"><i class="fa fa-edit fa-lg"></i> Detail Information</h4>
									</div>
									<div class="panel-body">
										<h5 class="text-center"><b class="label label-info"><i>You can skip this form if necessarry.</i></b></h5>
										<div class="row">
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$inbplc;?>
														</div>
													</div>
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$inbdt;?>
														</div>
													</div>
										</div>

												<div class="form-group">
													<?=$inaddn;?>
												</div>
												<div class="form-group">
													<?=$inaddh;?>
												</div>

												<div class="form-group">
													<?=$insoc;?>
												</div>
									
									</div>
								<div class="panel-footer text-right">

									<button type="button" class="btn btn-default prev-step">Previous</button>
									<button type="button" class="btn btn-info next-step">Skip</button>
									<button type="button" class="btn btn-primary next-step" >Continue</button>
								</div>
							</div>	
							</div>
							
							<div class="tab-pane" role="tabpanel" id="complete">
								<div class="panel panel-primary">
									<div class="panel-heading">
									<h4 class="panel-title text-center"><i class="fa fa-check fa-lg"></i> Term and Condition</h4>
									</div>
									<div class="panel-body">
										<div class="form-group text-center">
											<?=$inagree;?> <b> I Agree on Term & Condition</b>
										</div>
										<div class="form-group">
											<?=$interm;?>
										</div>
											<div class="row">
												<div class="col-md-2 col-md-offset-5 col-xs-4 col-xs-offset-4">
												<div class="form-group">
													<h5 class="text-center"><b>Insert captcha code:</b></h5>
													<h5 class="text-center"><span id="imgcaptcha"><img src="<?=$this->session->userdata('imgcaptcha');?>" class="img img-thumbnail" alt="Captcha Code"></span><button class="btn btn-default" alt="refresh captcha" id="refreshcaptcha" type="button"><span class="fa fa-refresh"></span></button></h5>
													<?=$incaptcha;?>
												</div>
												</div>
											</div>
									</div>
								<div class="panel-footer text-right">
									<?=$inkode;?>
									<button type="button" class="btn btn-default prev-step">Previous</button>
									<?=$insend;?>
								</div>
							</div>	
							
							</div>
							<div class="clearfix"></div>
						</div>
					</form>
				</div>
			</section>
		  
		</div>
	</div>


	<script>
	$(document).ready(function () {

        $('#agree').iCheck({
          checkboxClass: 'icheckbox_flat-blue',
          radioClass: 'iradio_flat-blue',
          increaseArea: '0%' // optional
        });


	    //Initialize tooltips
	    $('.nav-tabs > li a[title], input, textarea, select').tooltip({
	    	position: {
	        my: "left+15 center",
	        at: "right center",
	        collision: "flipfit"
	    },
		    content: function () {
		        return $(this).prop('title');
		    }
		}).off('focusin');
		    
	    //Wizard
	    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

	        var $target = $(e.target);
	    
	        if ($target.parent().hasClass('disabled')) {
	            return false;
	        }
	    });

	    $(".next-step").click(function (e) {
			var curInputs = $(this).closest(".tab-pane").find("input[type='text'],input[type='email'],select"),
				isValid = true;
			$(".form-group").removeClass("has-error");
	        for(var i=0; i<curInputs.length; i++){
	            if (!curInputs[i].validity.valid){
	                isValid = false;
	                $(curInputs[i]).closest(".form-group").addClass("has-error");
					$('.required').removeClass('hidden');
	            }
			}
			
	        if (isValid){
			var $active = $('.wizard .nav-tabs li.active');
	        $active.next().removeClass('disabled');
	        $('.required').addClass('hidden');
	        nextTab($active);
			}

	    });
   
   	 	$(".prev-step").click(function (e) {
        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);
    	});
		
		$('#refreshcaptcha').click(function(e){
			e.preventDefault();
			$.post('<?=base_url('Register/recaptcha');?>', {user: null}, function(d) {
				$('#imgcaptcha').empty();
				$('#imgcaptcha').html('<img src="'+d+'" class="img-thumbnail"/>');
			});
			});

	});

	function nextTab(elem) {
	    $(elem).next().find('a[data-toggle="tab"]').click();
		
	}
	function prevTab(elem) {
	    $(elem).prev().find('a[data-toggle="tab"]').click();
	}

	$("#birthdate").inputmask();
	$("#nohp").inputmask('08[99999999999]');
	 $(function () {
        $('#agree').iCheck({
          checkboxClass: 'icheckbox_flat-blue',
          radioClass: 'iradio_flat-blue',
          increaseArea: '0%' // optional
        });
      });


	  $('#email').bind('keyup change', function(e) {
			var email = $(this).val();
			var eid = $(this).attr('id');
			$.post('<?php echo base_url('Register/checkemail'); ?>', {email: email}, function(d) {
								if (d == 1)
								{
									$('#'+eid).parent().find('.text-danger').removeClass('hidden');
									$('#btncontinue').attr('disabled', 'disabled').button('refresh');
								}
								else
								{
									$('#'+eid).parent().find('.text-danger').addClass('hidden');
									$('#btncontinue').removeAttr('disabled').button('refresh');
								}
							});
			});
			
			$('#user').bind('keyup change', function(e) {
			var user = $(this).val();
			var eid = $(this).attr('id');
			$.post('<?php echo base_url('Register/checkuser'); ?>', {user: user}, function(d) {
								if (d == 1)
								{
									$('#'+eid).parent().find('.text-danger').removeClass('hidden');
									$('#btncontinue').attr('disabled', 'disabled');
								}
								else
								{
									$('#'+eid).parent().find('.text-danger').addClass('hidden');
									$('#btncontinue').removeAttr('disabled');
								}
							});
			});
			

			
		
	</script>					
</section>