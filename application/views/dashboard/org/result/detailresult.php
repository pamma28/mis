<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-file-text"></i> Result Details</h4>
</div>
<div class="modal-body">
	<div class="panel panel-default">
		<div class="panel-body text-center">
			<h3 class="text-center"><b>Detail Test Information</b></h3>
			<hr class="divider"/>
			<div class="row">
				<h3 class="col-md-6 col-sm-6"><b>Code:</b> <br/><?=$t['q_randcode'];?></h3>
				<h3 class="col-md-6 col-sm-6"><b>Temporary Score:</b> <br/><?=$t['q_tmpscore'];?></h3>
			</div>
			<div class="row">
				<p class="col-md-6 col-sm-6"><i class="fa fa-calendar"></i> <b>Date:</b> <?=$t['jdate'];?></p>
				<p class="col-md-6 col-sm-6"><i class="fa fa-clock-o"></i> <b>Session:</b> <?=$t['jsesi'];?></p>
			</div>
			<div class="row">
				<p class="col-md-6 col-sm-6"><i class="fa fa-building"></i> <b>Room:</b> <?=$t['jroom'];?></p>
				<p class="col-md-6 col-sm-6"><i class="fa fa-wpforms"></i> <b>Test:</b> <?=$t['tname'];?></p>
			</div>
			<div class="row">
				<p class="col-md-6 col-sm-6"><i class="fa fa-user"></i> <b>Member:</b> <?=$t['mem'];?></p>
				<p class="col-md-6 col-sm-6"><i class="fa fa-unlock"></i> <b>Corrector:</b> <?=$t['org'];?></p>
			</div>
		</div>
	</div>

	<div class="panel panel-info">
		<div class="panel-body">
		<h3 class="text-center"><b>Question List</b></h3>
			<?php 
				$a = 1;
				$tmpsubject='';
				foreach ($generatedq as $k => $v) {
					if ($tmpsubject<>$v['subject']){
						echo '<h4><b>'.$v['subject'].'</b></h4>';
						$tmpsubject = $v['subject'];
					}
					echo '<p>'.$a.'. '.$v['question'].'</p><div class="row"><div class="col-md-1"></div>';
						
						$t = 'A';
						foreach ($v['allanswer'] as $key => $val) {
							$totans = count($v['allanswer']);
							$colans = floor(10/$totans);
							if (($v['pickedanswer']==$val['idans']) and ($val['keyanswer'])){
								echo '<div class="col-md-'.$colans.'"><span class="label label-success">'.$t.'. '.$val['answer'].'</span></div>';	
							} else if ($v['pickedanswer']==$val['idans']){
								echo '<div class="col-md-'.$colans.'"><span class="label label-danger">'.$t.'. '.$val['answer'].'</span></div>';	
							} else if ($val['keyanswer']){
								echo '<div class="col-md-'.$colans.'"><span class="label label-primary">'.$t.'. '.$val['answer'].'</span></div>';	
							} else{
								echo '<div class="col-md-'.$colans.'">'.$t.'. '.$val['answer'].'</div>';	
							}

							$t++;
						}

					echo '</div>';
				$a++;
				}
			?>
		</div>
	</div>
	<h4><b>Details: </b></h4>
	<span class="label label-success">Correct</span>
	<span class="label label-danger">Picked</span>
	<span class="label label-primary">Key Answer</span>
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn-sm" data-dismiss="modal">Close</a>
</div>
