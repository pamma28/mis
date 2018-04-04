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

