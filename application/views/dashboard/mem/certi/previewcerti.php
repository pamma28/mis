<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-image fa-lg"></i> Preview<small>Certificate</small></h1>
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
				<div class="col-md-6">
					<div class="box box-info">
						<div class="box-body text-center">
							
						<img id="certiimg" src="<?=base_url('Member/Certificate/previewmycertismall');?>" class="img-thumbnail" width=600 data-zoom-image="<?=base_url('Member/Certificate/previewmycertireal');?>">
								
						</div>
					</div>
				</div>
				<div class="col-md-6 text-center text-disabled">
					<div class="bg-gray" id="zoomedimg"  style="height: 350px;width: 350px;margin:40px auto;padding: 20px;vertical-align: baseline;">
						<h4><i>Zoom Image</i></h4>
						<i class="fa fa-image fa-4x" style=""></i>
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
	$(document).ready(function(){

		
		$("#certiimg").elevateZoom({tint:true, tintColour:'#161616', tintOpacity:0.5, zoomWindowPosition: "zoomedimg", zoomWindowHeight: 350, zoomWindowWidth:350, borderSize: 0, easing:true,zoomWindowFadeIn: 300,
			zoomWindowFadeOut: 300});
	});
	</script>

</section>