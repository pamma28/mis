<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-file-text"></i>Test Result Details</h4>
</div>
<div class="modal-body">
		
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
										<p class="col-md-6 col-sm-6"><i class="fa fa-user"></i> <b>Member:</b> <?=$t['uname'];?></p>
										<p class="col-md-6 col-sm-6"><i class="fa fa-unlock"></i> <b>Controlled by:</b> <?=$t['uname'];?></p>
									</div>
								</div>
							</div>
							</div>
						<div class="col-md-3 col-sm-3">
							<div class="panel panel-default">
								<div class="panel-body">
									<h4 class="text-center"><b> Final Result</b></h4>
									<hr class="divider"/>
									<h2 class="text-center"><b> <span id="finalresult"><?=$finalscore;?></span></b></h2>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 col-sm-12 text-center">
							<div class="panel panel-info">
								<div class="panel-body">
								<p class="lead">Congratulations, We welcome you in</p>
								<h3 class="text-primary"><b>'<?=$t['lvlname'];?>'</b></h3>
								<p class="lead">Level</p>
								</div>
							</div>
						</div>
					</div>

					<!-- Question Box -->
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="text-center"><a class="btn btn-primary btn-sm" role="button" data-toggle="collapse" href="#allquestion" aria-expanded="false" aria-controls="allquestion">Details of Your Mark</a></div> <hr/>

							<div id="allquestion" class="collapse">
							
							<div id="question">
								<div class="panel panel-info">									
									<div class="panel-body">
								       
								       		<?php 
								       			$a=1;
								       			foreach ($mytest as $v) {
								       			print('<h4><u><b>'.$v['subject'].'</b></u></h4>');
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

								       			print('<h5 class="">'.$a.'. '.$v['question'].'</h5>'.form_hidden('quest[]',$v['idq']));
												print('<div class="row"><div class="col-md-10">'); 
												$totans = count($v['allanswer']);
												($totans>0) ? $leftcol = floor(12/$totans) : $leftcol='12';
												$char = 'A';
												if (!$v['qmanual']){	
														print('<div class="row">');
														foreach ($v['allanswer'] as $kans => $vans) {
															$picked = ($vans['idans']==$v['pickedanswer']) ? 'checked' : '';
															$answer ='';
															
															$answer = form_radio(array('name'=>'ans'.$a,'class'=>'checkbox icheck','value'=>$vans['idans'],'data-q'=>$v['idq'],'checked'=>$picked,'readonly'=>'readonly','disabled'=>'disabled')).' '.$char.'. '.$vans['answer'].'<br/>';
															print('<div class="col-md-'.$leftcol.' col-sm-'.$leftcol.'">'.$answer.'</div>');
															$char ++;
														}
														print('</div>');
												} else {
													print(form_textarea(array('name'=>'ans'.$a,'class'=>'form-control','data-id'=>$a,'rows'=>4,'cols'=>40,'data-q'=>$v['idq'],'value'=>$v['pickedanswer'],'readonly'=>'readonly','disabled'=>'disabled')));
												}
												
												print('</div><div class="col-md-2 text-center"><div class="box box-info><div class="box-body"><b class="text-info">Your Mark</b><p>'.$v['mark'].'</p></div></div></div>');	
												$a++;
								            	} 
								            ?>
								            <br/> 
											
								           
								    </div>
								</div>  
							</div>
							

							

						</div>
					</div>
				</div>
			</div>

</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn-sm" data-dismiss="modal">Close</a>
</div>	

	<script type="text/javascript">
		

	</script>

