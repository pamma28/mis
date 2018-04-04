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
				
					<div class="panel panel-primary">
						<div class="panel-heading panel-heading-sm">
							<h5 class="panel-title text-center"><b>My Certificate</b></h5>
						</div>
						<div class="panel-body">
								
								<h4><b>Certificate No: <span class="text-primary"><?=$nocerti?></span></b></h4>
								<h4><b>Issued Date: <span class="text-green"><?=$certidate?></span></b></h4>
								<h4><b>Level: <span class="text-blue"><?=$lvlname;?></span></b></h4>
								<h4><b>Status: <span class="text-yellow"><?=$cstatus;?></b></span></h4>
							<hr class="clearfix">
									<?=$mysche;?>
						</div>
								
					</div>
				
			</div>
		
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