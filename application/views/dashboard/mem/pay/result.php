<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-edit fa-lg"></i> Validation<small>Result</small></h1>
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
	<?=$listdata;?>
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
	
	<!-- Modal Import Data-->
	<div class="modal fade bs-import-data" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open_multipart($factimp,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-cloud-upload"></i> Import Data</h4>
			</div>
			<div class="modal-body">
					<div class="form-group">
					<label>Download Pre-defined .xls file </label>
					</br><a href="<?=base_url('Organizer/PDS/predefinedimport');?>" type="button" class="btn btn-warning btn-sm">Download sample format import .xls</a>
					<p class="help-block">Please only use this format to import data.</p>
					</div>
					<div class="form-group">
					<label>Choose File </label>
					<?=$finfile;?>
					<p class="help-block">Please choose Microsoft Excel 2003 version (.xls)</p>
					<span class="label label-info"><i class="fa fa-exclamation-triangle"></i> Import will automatically create new user (NIM) and its password based on birthdate(ddmmyyyy).</span>
					</div>				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<?php echo $fbtnimport;?>
			</div>
				<?=form_close();?>
		</div>
	  </div>
	</div>
	
	<!-- Modal Export Data-->
	<div class="modal fade bs-export-data" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open($factexp,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-cloud-download"></i> Export Data</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Choose Column(s)</label>
					<div class="input-group">
                    <?=$fcol;?>
					</div>
					<br/>
					<div class="input-group">
						<?=$fcheckcol;?>
						<?=$funcheckcol;?>
					</div>
					<p class="help-block">*Hold ctrl to choose multiple columns.</p>
							
				</div>
				<hr class="divider"/>
				<div class="form-group">
					<label>Determine Interval Time</label>
					<div class="input-group">
						<span class="input-group-addon"><?=$fusedate;?></span>
						<?=$fdtrange;?>
						<span class="input-group-addon"><i class="fa fa-calendar"></i> Use Date</span>
					</div>
					<p class="help-block">*Leave uncheck to export all data.</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<?=$fbtnexport;?>
			</div>
				<?=form_close();?>
		</div>
	  </div>
	</div>
	
	
	<!-- Modal Details Data-->
	<div class="modal fade" id="DetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			
        </div>
    </div>
	</div>
		
	<script type="text/javascript">
	//details data	
		$('#DetailModal').on("hidden.bs.modal", function (e) {
		$(e.target).removeData("bs.modal").find(".modal-body").empty();
		});
	</script>

</section>