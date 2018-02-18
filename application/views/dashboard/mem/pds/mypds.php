<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-edit fa-lg"></i> Registration<small>Data</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box box-primary">
	<div class="box-body">
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
	
	<div class="row">
		<div class="col-md-8 col-sm-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="panel-title text-center">
					<b class="text-primary">Personal Data Sheet</b>
					<div class="pull-left">
						<a href="<?=base_url('Member/Managepds/editpds');?>" class="btn btn-primary"><i class="fa fa-edit fa-lg"></i> Edit Data</a>
					</div>
					<div class="pull-right">
						<div class="btn-group">
							
							<a href="#" data-href="<?=base_url('Member/Registration/printpds');?>" class="btn btn-info" data-toggle="modal" data-target="#confirm-print" title="Print Data"><i class="fa fa-print fa-lg"></i> Print Data</a>
						</div>
					</div>
					<h5></h5>
					</div>
				</div>
				<div class="panel-body">
				<?=$rdata;?>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body">
				<h4 class="text-center text-success"><b>Personal Data Sheet Progress</b></h4>
				<hr class="divider"/>
				<div class="progress">
				  <div class="progress-bar progress-bar-striped progress-bar-success active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?=100-ceil(($totmiss/$totinp)*100);?>%">
				    <span class="sr-only"><?=100-ceil(($totmiss/$totinp)*100);?>% Complete</span>
				    <?=100-ceil(($totmiss/$totinp)*100);?>% Complete
				  </div>
				</div>
				</div>
			</div>
			<div class="panel panel-warning">
				<div class="panel-body">
              		<div class="info-box bg-yellow">
		                <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
		                <div class="info-box-content">
		                  <span class="info-box-text"><b>Total Missing Data</b></span>
		                  <span class="info-box-number"><?=$totmiss;?></span>
		                  <div class="progress">
		                    <div style="width: <?=100-ceil(($totmiss/$totinp)*100);?>%;" class="progress-bar"></div>
		                  </div>
		                  <span class="progress-description">
		                    <?=$rmissing;?>
		                  </span>
		                </div><!-- /.info-box-content -->
		              </div>
				</div>
			</div>
			
		</div>
	</div>
	</div>
	</div>

	<!-- Modal Delete Data-->
	<div class="modal fade" id="confirm-print" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa fa-print"></i> Are you sure want to print?</h4>
            </div>
			
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary btn-ok btn-sm">Print</a>
            </div>
        </div>
    </div>
	</div>

	<script type="text/javascript">
	 $('#confirm-print').on('show.bs.modal', function(e) {
    $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });	
	</script>

</section>