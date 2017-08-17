<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-edit fa-check"></i> Activate<small>Schedule</small></h1>
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
		<div class="row">
			<div class="col-md-6">
			</div>
			<div class="col-md-6 text-right">
					<?php 
						echo form_open(current_full_url(),array('name'=>'fq', 'method'=>'GET','class'=>'form-inline'));
					?>		
					<div class="input-group">
					<span class="input-group-addon">Filter</span>
					<?php
						echo $inc.'</div><div class="input-group">'.$inq.
						'<span class="input-group-btn">';
						echo $bq.$inv.'</span></div>';
						echo form_close();
					?>
					<div class="text-right"><a href="<?=current_url();?>" class="label label-danger">Clear Search</a> <a class="label label-info" role="button" data-toggle="collapse" href="#collapseAdvanced" aria-expanded="<?php echo(empty($d)? 'false': 'true');?>" aria-controls="collapseExample">Advanced search</a></div>
			</div>
		</div>
		<div class="row">
			<div class="<?php echo(empty($d)? 'collapse': 'collapse in');?> col-md-12" id="collapseAdvanced" aria-expanded="<?php echo(empty($d)? 'false': 'true');?>">
			  <div class="well">
				<?php 
						echo form_open(current_full_url(),array('name'=>'fadvq', 'method'=>'GET','class'=>'form-inline'));
				?>	
					
						<?=$advance;?>
					<div class="text-right">
					<?=$bq;?>
					</div>
				<?php 
				echo form_close();
				?>
			  </div>
			</div>
		</div>		
	</div>
	<div class="box-body table-responsive">
	<?=$listlogin;?>
		
	</div>
		
		<div class="box-footer clearfix">
			<!-- Show perpage -->
			<div class="pull-left">
				<div class="btn-group">
							  <button class="btn btn-default" type="button">Per Page</button>
							  <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							  </button>
							  <ul role="menu" class="dropdown-menu">
								<?php
								foreach($perpage as $p){
								echo '<li><a href="'.$urlperpage.$p.'">'.$p.'</a></li>';
								}?>
							  </ul>
				</div>
			</div>
			<div class="pull-right">
			<!-- Show pagination links -->
			<ul class="pagination pagination-sm no-margin pull-right">
			<?php foreach ($links as $link) {
			echo "<li>". $link."</li>";
			} ?>
			</ul>
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