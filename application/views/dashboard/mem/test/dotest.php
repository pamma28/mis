<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-pencil fa-lg"></i> Do<small>Test</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
		<div class="box-header">
			<?php if ($this->session->flashdata('v')!=null){ ?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<?=$this->session->flashdata('v');?>
			</div>
			<?php } else if ($this->session->flashdata('x')!=null){ ?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<?=$this->session->flashdata('x');?>
			</div>		
			<?php } ?>
			
		</div>
		<div class="box-body table-responsive">
		<?php if (($active=='')) {
			echo form_open(base_url('Member/Test/submittest'),array('name'=>'ftest','id'=>'populatetest', 'method'=>'POST'));?>
			
			<div class="panel panel-primary">
				<div class="panel-heading panel-heading-sm">
					<h5 class="panel-title text-center"><b><?=$t['tname'];?></b></h5>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-3 col-sm-3">
							<div class="panel panel-default">
								<div class="panel-body">
									<h4 class="text-center"><b>Question Code</b></h4>
									<hr class="divider"/>
									<h1 class="text-center"><b><?=$mytest[0]['code'];?></b></h1>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="panel panel-default">
								<div class="panel-body">
									<h4 class="text-center"><b>Detail Test Information</b></h4>
									<hr class="divider"/>
									<div class="row">
										<p class="col-md-6 col-sm-6"><i class="fa fa-calendar"></i> <b>Date:</b> <?=$t['jdate'];?></p>
										<p class="col-md-6 col-sm-6"><i class="fa fa-clock-o"></i> <b>Session:</b> <?=$t['jsesi'];?></p>
									</div>
									<div class="row">
										<p class="col-md-6 col-sm-6"><i class="fa fa-building"></i> <b>Room:</b> <?=$t['jroom'];?></p>
										<p class="col-md-6 col-sm-6"><i class="fa fa-wpforms"></i> <b>Test:</b> <?=$t['tname'];?></p>
									</div>
									<div class="row">
										<p class="col-md-6 col-sm-6"><i class="fa fa-user"></i> <b>Member:</b> <?=$me;?></p>
										<p class="col-md-6 col-sm-6"><i class="fa fa-unlock"></i> <b>Controlled by:</b> <?=$t['uname'];?></p>
									</div>
								</div>
							</div>
							</div>
						<div class="col-md-3 col-sm-3">
							<div class="panel panel-default">
								<div class="panel-body">
									<h4 class="text-center"><b>Timer</b></h4>
									<hr class="divider"/>
									<h2 class="text-center"><i class="fa fa-clock-o"></i> <span id="countdown">00:00:00</span></h2>
								</div>
							</div>
						</div>
					</div>

					<!-- Question Box -->
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<?php if ($runout==1) {?>
							<div id="allquestion">
							<?php print('<div class="hidden" id="allquestion">'.json_encode(array_column($mytest,'idq')).'</div>');?>
							<h4 class="text-info"><b>Instruction:</b> Please do the test carefully and precisely.</h4>
							 			
							 			<div id="loading-report" class="alert">
										    <button type="button" class="close" data-dismiss="alert">x</button>
										    <i><span id="loading-content"></span></i>
								        </div>

							<div id="question">									
								<div class="panel with-nav-tabs panel-primary">
								    <div class="panel-heading">
								        <ul class="nav nav-tabs" id="tabquest">
								           	<?php 
								           		$tot = count($mytest);
								           		$avgwidth =round(100/$tot);
								           		if($avgwidth<5) $avgwidth='5';
								           		$a=1;
								           		$arrq = array_column($mytest,'idq');
								               	foreach ($arrq as $k=>$v) {
								               	$labelclass = ($mytest[$k]['pickedanswer']!='') ? 'success' : 'danger';
								               	print('<li style="width:'.$avgwidth.'%;" data-id="'.$a.'"><a style="width:100%;text-align:center;font-weight:bold;" href="#tab'.($a).'primary" data-toggle="tab" class="navigate"><span class="label label-'.$labelclass.' label'.$a.'">'.($a).'</span></a></li>');
								               	$a++;
											} ?>
								               	
								        </ul>
								    </div>
								    <div class="panel-body">
								        <div id="loading-submit" class="float-right hidden">
								        		<div class="text-center">
								        			<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
								        			<span class="sr-only">Loading...</span>
								        		</div>
								        </div>
								     
								        <div class="tab-content" id="view-question">
								       		<?php 
								       			$a=1;
								       			foreach ($mytest as $v) {
								       			print('<div class="tab-pane fade" id="tab'.($a).'primary">'.
								       				'<h4><u><b>'.$v['subject'].'</b></u></h4>');
								       			if (array_key_exists('q_paragraph',$v['attach'])){
								       				print('<div class="well"><blockquote>');
								       				if (($v['attach']['q_file']!='') and ($v['attach']['q_filetype']=='img')){
								       					print('<div class="text-center">');
								       					print('<img src="'.base_url('upload/attach/'.$v['attach']['q_file']).'" class="img-responsive"/>');
								       					print('</div>');
								       				}
								       				else if (($v['attach']['q_file']!='') and ($v['attach']['q_filetype']=='mp3')){
								       					print('<div class="text-center">');
									       				print('<audio controls="controls" preload="none" class="attach'.$v['attach']['indexatt'].'">
															<source width="375" height="20" type="audio/mp3" src="'.base_url('upload/attach/'.$v['attach']['q_file']).'" />
														</audio>');
														print('</div>');
													}
													else if (($v['attach']['q_file']!='') and ($v['attach']['q_filetype']=='flash')){
								       					print('<div class="text-center">');
								       					print('<video width="480px" height="auto"  controls="controls" preload="none" class="attach'.$v['attach']['indexatt'].'">
															<source type="video/mp4" src="'.base_url('upload/attach/'.$v['attach']['q_file']).'"/>
															</video>');
								       					print('</div>');
								       				}

								       				print($v['attach']['q_paragraph']);
								       				print('</blockquote></div>');
								       			}

								       			print('<h3 class="">'.$a.'. '.$v['question'].'</h3>'.form_hidden('quest[]',$v['idq']));
												$totans = count($v['allanswer']);
												$leftcol = (($totans%2)==0) ? floor($totans/2) : floor($totans/2)+1;
												$char = 'A'; 
												print(form_hidden('qmanual[]',$v['qmanual']));
												if (!$v['qmanual']){	
														print('<div class="row"><div class="col-md-6 col-sm-6">');
														foreach ($v['allanswer'] as $kans => $vans) {
															$picked = ($vans['idans']==$v['pickedanswer']) ? 'checked' : '';
															$answer ='';
															($kans == ($leftcol)) ? print('</div><div class="col-md-6 col-sm-6">') : null;
															$answer = form_radio(array('name'=>'ans'.$a,'class'=>'checkbox icheck','value'=>$vans['idans'],'data-q'=>$v['idq'],'checked'=>$picked)).' '.$char.'. '.$vans['answer'].'<br/>';
															print($answer);
															$char ++;
														}
														print('</div></div>');
												} else {
													print(form_textarea(array('name'=>'ans'.$a,'class'=>'form-control','data-id'=>$a,'rows'=>4,'cols'=>40,'data-q'=>$v['idq'],'value'=>$v['pickedanswer'])));
												}
												
												print('</div>');	
												$a++;
								            	} 
								            ?>
								            <br/> 
											
								            <div class="btn-group pull-right">
												<a class="btn btn-primary navigate" id="nextquest"> Next <i class="fa fa-angle-right"></i></a>
												<a class="btn btn-primary navigate" id="lastquest"> Last <i class="fa fa-angle-double-right"></i></a>
											</div>
											<div class="btn-group pull-left">
												<a class="btn btn-primary navigate" id="firstquest"><i class="fa fa-angle-double-left"></i> First</a>
												<a class="btn btn-primary navigate" id="prevquest"><i class="fa fa-angle-left"></i> Previous</a>
											</div>
											<div class="text-center">
											<span class="label label-danger"> Not Answered</span>
											<span class="label label-success"> Answered</span>
											</div>
											
											<hr class="divider"/>
							          
											<?=$idresult;?><button type="submit" class="btn btn-info btn-block" id="submittest"><h4><i class="fa fa-send"></i> Submit Test </h4></button>
								        
											</div>
								        </div>
								    </div>
								</div>
									
							</div>
							
							<div id="runout" class="<?php ($runout<>1) ? null : print('hidden');?>">
								<div class="panel panel-default">
									<div class="panel-body">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<h3 class="text-center text-primary">Your Test Duration is Running out.</h3>
											<hr/>	
											<div class="panel panel-warning">
												<div class="panel-body">
													<h4 class="text-center"><span class="label label-warning"><i class="fa fa-exclamation-triangle"></i> Thank You for Your Test Submission.</span></h4>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							

						</div>
					</div>
				</div>
			</div>
			

			
							<?php } ?>

							<div id="runout" class="<?php ($runout<>1) ? null : print('hidden');?>">
								<div class="panel panel-default">
									<div class="panel-body">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<h3 class="text-center text-primary">Your Test Duration is Running out.</h3>
											<hr/>	
											<div class="panel panel-warning">
												<div class="panel-body">
													<h4 class="text-center"><span class="label label-warning"><i class="fa fa-exclamation-triangle"></i> Thank You for Your Test Submission.</span></h4>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
		<?php echo form_close();
		} else { ?>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h3 class="text-center text-primary">Test is not Activated yet.</h3>
						<hr/>	
						<div class="panel panel-warning">
							<div class="panel-body">
								<h4 class="text-center"><span class="label label-warning"><i class="fa fa-exclamation-triangle"></i> Test is not available now. Please wait until the test is activated.</span></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			
		<?php } ?>
		</div>
		
	</div>
	
	<div id="modalreport" class="modal fade bs-report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
			<div class="modal-header">
			   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			   <h4><i class="fa fa-info-circle text-info"></i> Notification</h4>
			</div>
			<div class="modal-body">
			   <p class="text-center" id="reporttext"></p>
			</div>
        </div>
    </div>

	<script type="text/javascript">
	
   	$('.navigate').on('click', function(event){
   		var dataid =$("#tabquest").find('.active').data('id');
   		updateanswer(dataid);
   		$('#question').focus();
	});
  	
  	$('input').on('ifChecked', function(event){
  		var dataid =$("#tabquest").find('.active').data('id');
  		updateanswer(dataid);
  		$('#question').focus();
	});

	$('#submittest').on('click', function(event){
		event.preventDefault();
   		var dataid =$("#tabquest").find('.active').data('id');
   		updateanswer(dataid);
   		$('#question').focus();
	});

   	function updateanswer(dataid){
   		$('#question').addClass('disabled');
		$('#view-question').addClass('hidden');
		$('#loading-submit').removeClass('hidden');
   		var allqans = $("#populatetest").serialize();
   		$.post('<?=base_url('Member/Test/savemyanswer');?>',{
				allqans : allqans}, 
				function (data) {
					if (data){   						
   						$('.label'+dataid).removeClass('label-danger');
   						$('.label'+dataid).addClass('label-success');
   						$("#loading-content").text("Update Answer Success.");
   						$("#loading-report").addClass("alert-success");
                		$("#loading-report").alert();
               			$("#loading-report").fadeTo(500, 300).slideUp(300, function(){
               			$("#loading-report").slideUp(300);
                		});   
            			

					} else {
						$("#loading-content").text("Update Answer Failed, Check Your Connection.");
						$("#loading-report").addClass("alert-danger");
                		$("#loading-report").alert();
               			$("#loading-report").fadeTo(500, 300).slideUp(300, function(){
               			$("#loading-report").slideUp(300);
                		});   
					}
   				$('#view-question').removeClass('hidden');
				$('#loading-submit').addClass('hidden');
				$('#question').removeClass('disabled');
				});
   		
   	}
	
   	$('#navigate').click(function(){
	    var multiple = $('input:checked').map(function(){
	        var $this = $(this);
	        return {name: $this.attr('name'), value: $this.val()};
	    }).get();
	    return false;
	});

	$(document).ready(function(){
		$('video,audio').on('ended',function(){
			var target = '.'+$(this).attr('class');
			$(target).addClass('hidden');
		});
		$('#tab1primary').addClass(' active in');
		$('a[href="#tab1primary"]').closest('li').addClass('active');
	
		 $("#loading-report").hide();
	});
	
	$('#nextquest').click(function(){
		$("#tabquest > .active").next('li').find('a').trigger('click');
	});
	
	$('#prevquest').click(function(){
		$("#tabquest > .active").prev('li').find('a').trigger('click');
	});

	$('#firstquest').click(function(){
		$("#tabquest").find('a:first').trigger('click');
	});

	$('#lastquest').click(function(){
		$("#tabquest").find('a:last').trigger('click');
	});

	$(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue'
        });
      });

	$("#countdown")
	  .countdown("<?=date('Y-m-d H:i:s',$remain);?>", function(event) {
	    $(this).text(
	      event.strftime('%H:%M:%S')
	    );
	  }).on('finish.countdown',function(){
	  	var dataid =$("#tabquest").find('.active').data('id');
  		updateanswer(dataid);
	  	$('#allquestion').empty();
	  	$('#runout').removeClass('hidden');
	  });

	$('#modalreport').on("hidden.bs.modal", function (e) {
	    $('#loading').addClass("hidden");
		});
	$('#modalreport').on('shown.bs.modal', function() {
	})
	
	
	</script>

</section>