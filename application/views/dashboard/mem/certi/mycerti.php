<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-certificate fa-lg"></i> My<small>Certificate</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
		<?php if ($date) { ?> 
		<div class="box-body table-responsive">
				
					<div class="panel panel-primary">
						<div class="panel-heading panel-heading-sm">
							<h5 class="panel-title text-center"><b>My Certificate</b></h5>
						</div>
						<div class="panel-body">
							<?php if ($available) {?>	
								<h4><b>Certificate No: <span class="text-primary"><?=$nocerti?></span></b></h4>
								<h4><b>Issued Date: <span class="text-green"><?=$certidate?></span></b></h4>
								<h4><b>Level: <span class="text-blue"><?=$lvlname;?></span></b></h4>
								<h4><b>Status: <span class="text-yellow"><?=$cstatus;?></b></span></h4>
							<hr class="clearfix">
									<?=$mysche;?>
							<?php } else {?>
								<h4 class="text-info text-center"><b><i>Your certificate is not ready.</i></b></h4>
							<?php } ?>
						</div>
								
					</div>
				
			</div>
		<?php } else { ?>

			<div class="col-md-12 cols-sm-12">
				<h3 class="text-center text-danger">Cannot Access Certificate</h3> <hr class="divider"/>
				<div class="panel panel-default">
					<div class="panel-body">
						<h3 class="text-center"><span class="label label-info"><i class="fa fa-info-circle"></i> Today is not certificate phase.</b></span></h3>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
	
	<!-- Modal Delete Data-->
	<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa fa-trash"></i> Are you sure want to delete selected data?</h4>
            </div>
			
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok btn-sm">Delete</a>
            </div>
        </div>
    </div>
	</div>

	
	
	<script type="text/javascript">
		//delete modal confirmation
	$('#confirm-delete').on('show.bs.modal', function(e) {
		$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
		});
	</script>

</section>