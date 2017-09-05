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
						echo '<h3><b>'.$v['subject'].'</b></h3>';
						$tmpsubject = $v['subject'];
					}
					echo '<div class="row"><div class="col-md-10 col-sm-10"><h4>'.$a.'. '.$v['question'].'</h4><div class="row">';
						
					if (!$v['qmanual']){
						$t = 'A';
						foreach ($v['allanswer'] as $key => $val) {
							$totans = count($v['allanswer']);
							$colans = floor(12/$totans);
							if (($v['pickedanswer']==$val['idans']) and ($val['keyanswer'])){
								echo '<div class="col-md-'.$colans.'"><span class="bg-green">'.$t.'. '.$val['answer'].'</span></div>';	
							} else if ($v['pickedanswer']==$val['idans']){
								echo '<div class="col-md-'.$colans.'"><span class="bg-danger">'.$t.'. '.$val['answer'].'</span></div>';	
							} else if ($val['keyanswer']){
								echo '<div class="col-md-'.$colans.'"><span class="bg-blue">'.$t.'. '.$val['answer'].'</span></div>';	
							} else{
								echo '<div class="col-md-'.$colans.'">'.$t.'. '.$val['answer'].'</div>';	
							}

							$t++;
						}
						($v['answermark']=='1') ? $mark ='<p class="text-center"><b><i><span class="fa fa-check fa-success"></span> Correct</i></b></p>': $mark= '<p class="text-center"><b><i><span class="fa fa-ban fa-danger"></span> Incorrect</i></b></p>';
					} else {
						print('<div class="col-md-12 col-sm-12"><pre><p>'.$v['pickedanswer'].'</p></pre></div>');
						$mark = '<p class="text-center"><i>Not Yet</i></p>' ;
					}

					echo '</div></div><div class="col-md-2 col-sm-2"><div class="box"><h4 class="text-center"><b>Mark</b></h4><div class="box-body">'.$mark.'</div></div></div></div>';
				$a++;
				}
			?>
		</div>
	</div>
	<h4><b>Details: </b></h4>
	<span class="bg-green">Correct</span>
	<span class="bg-danger">Picked</span>
	<span class="bg-blue">Key Answer</span>
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn-sm" data-dismiss="modal">Close</a>
</div>
