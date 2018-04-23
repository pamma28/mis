<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-info-circle"></i> Your Transfer Details</h4>
</div>
<div class="modal-body">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title text-center"><b>Validation Transfer Details </b></h3>
		</div>
		<div class="panel-body">
		<?=$rdata;?>
			<div class="box box-primary">
				<div class="box-body text-center">
					<h4 class="text-primary"> <b>Validation Result </b> <?=$status;?></h4>
					<?php if (null!=$dateproc) {?>
					<sub>Your request has been processed at <b> <?=$dateproc;?></b></sub>
					<?php } else {?>
					<sub>Your request is being proccessed</sub>
					<?php } ?>
				</div>
			</div>
		<small>
			<ul>
			<li><i>Total amount paid includes <b> Your Payment Code</b> <?=$code;?></i></li>
			<li><i>if your payment is confirmed/approved, please check your payment proof in your email</i></li>
			<li><i>If your payment is rejected/declined, please contact us directly by bringing payment proof/receipt you have to Secretariat Builidng House of SEF</i></li>
			</ul>
		</small>
		</div>
	</div>
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok" data-dismiss="modal">Close</a>
</div>
<script type="text/javascript">
	$("#openprev").click(function(a){
		a.preventDefault();
		$('#DetailModal').removeData("bs.modal").find(".modal-content").empty();
		$('#DetailModal').find(".modal-content").load($(this).attr("href"));
	});
</script>
