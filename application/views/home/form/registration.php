<section class="content-header">
          <h1>
            Registration <small>New Member</small>
            
          </h1>
</section>

<section class="content">
	<div class="box box-primary">
		<div class="box-body">
		<h2 class="text-center">Regular Class <?=$period;?></h2>
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

					<form role="form">
						<div class="tab-content">
							<div class="tab-pane active" role="tabpanel" id="step1">
								<div class="panel panel-primary">
									<div class="panel-heading">
									<h4 class="panel-title text-center"><i class="fa fa-user fa-lg"></i> Basic Information</h4>
									</div>
									<div class="panel-body">
											
												<div class="row">
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$innama;?>
														</div>
													</div>
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$innim;?>
														</div>
													</div>
												</div>

												<div class="form-group">
													<?=$inbdt;?>
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
														</div>
													</div>
												</div>											
											
										<p class="text-danger hidden">* Please complete the required input(s)</p>
										</div>
									<div class="panel-footer text-right">
										<button type="button" class="btn btn-primary next-step">Continue</button>
									
									</div>
								</div>
							</div>
							<div class="tab-pane" role="tabpanel" id="step2">
								<div class="panel panel-primary">
									<div class="panel-heading">
									<h4 class="panel-title text-center"><i class="fa fa-edit fa-lg"></i> Detail Information (May Skipped)</h4>
									</div>
									<div class="panel-body">
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

												<div class="form-group">
													<?=$inaddn;?>
												</div>
												<div class="form-group">
													<?=$inaddh;?>
												</div>

										<div class="row">
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$inbplc;?>
														</div>
													</div>
													<div class="col-xs-6 col-sm-6 col-md-6">
														<div class="form-group">
															<?=$inbbm;?>
														</div>
													</div>
										</div>
									
									</div>
								<div class="panel-footer text-right">
									<button type="button" class="btn btn-default prev-step">Previous</button>
									<button type="button" class="btn btn-default next-step">Skip</button>
									<button type="button" class="btn btn-primary next-step">Continue</button>
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
													<?=$inaggree;?> <b> I Agree on Term & Condition</b>
												</div>
												<div class="form-group">
													<?=$interm;?>
												</div>
									</div>
								<div class="panel-footer text-right">
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
    //Initialize tooltips
    $('.nav-tabs > li a[title], input, textarea, select').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);
    
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {
		var curInputs = $(this).closest(".tab-pane").find("input[type='text'],input[type='email']"),
			isValid = true;
		$(".form-group").removeClass("has-error");
        for(var i=0; i<curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
				$('.text-danger').removeClass('hidden');
            }
		}
		
        if (isValid){
		var $active = $('.wizard .nav-tabs li.active');
        $active.next().removeClass('disabled');
        $('.text-danger').addClass('hidden');
        nextTab($active);
		}

    });
    $(".prev-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);

    });
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
	
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}
	</script>					
</section>