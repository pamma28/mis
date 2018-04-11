<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-certificate fa-file-image-o"></i> Preview<small>Certificate</small></h1>
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
			
			<div class="col-md-6 col-md-offset-6 text-right">
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
		
			<div class="col-md-4 col-sm-4" style="max-height: 650px;">
				
				<small>
				<?=$listlogin;?>
				</small>
				<div class="pull-left">	
						<a role="button" class="btn btn-lg btn-info" data-toggle="collapse" href="#collapseSetting" aria-expanded="false" aria-controls="collapseSetting" id="focussetting"><span class="fa fa-gear"> Setting Certificate Texts</span></a>
				</div>
			</div>
			<div class="col-md-8 col-sm-8 text-center">
					
					<div id="loading" class="hidden">
						<div class="circle"></div>
						<div class="circle1"></div>
					</div>
					<div id="divpreview">
						<img src="<?=base_url('Organizer/Certificate/previewcerti');?>" class="img-thumbnail" id="imgcerti" />
					</div>
				
			</div>				
		
		
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
			<br/>
			<br/>
			
			<fieldset class="scheduler-border">
			<legend class="scheduler-border"><a role="button" data-toggle="collapse" href="#collapseSetting" aria-expanded="false" aria-controls="collapseSetting">Setting Certificate Texts</a></legend>
			<div id="settingbox">
			<div id="collapseSetting" class="collapse">
			<table class="table table-striped">
			<form name="fsetsetting" class="form-inline" action="<?=$fsendper;?>" method="POST" enctype="multipart/form-data" accept-charset="utf-8">
			<tr>
				<td colspan="2">
				<div class="well">
				<h5 class="text-info"><span class="fa fa-wrench"></span> Setting Font</h5>
				<dl class="dl-horizontal">
					<dt>Current font</dt> <dd> <?=$font;?></dd>
					<dt>Upload new font</dt> <dd><?=$ffont;?></dd>
				</dl>
				<p class="label label-danger"><i><span class="fa fa-info-circle"></span> Be careful! Once new font uploaded, it will delete old font</i></p>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="well">
					<h5 class="text-info"><span class="fa fa-wrench"></span> Setting Size and Margin</h5>
					<?php $header = array('Full Name','Certificate Number','Preface','Assessment Score','Level','Title','Signature Name','Signature No');?>
					<table class="table table-condensed">
						<thead>
							<td><b>Text</b></td>
							<td><b>Size</b></td>
							<td><b>Margin</b></td>
							<td><b>Color</b></td>
							<td><b>Column</b></td>
							<td><b>Justify</b></td>
						</thead>
						<?php
							foreach ($fsize as $k => $v) {
								echo '<tr><td>'.$header[$k].'</td>';
								echo '<td>'.$v.'</td><td>'.$fmargin[$k].'</td><td>'.$fcolor[$k].'</td><td>'.$fcolumn[$k].'</td><td>'.$fcenter[$k].'</td></tr>';
							}
						?>
						
					</table>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<div class="well">
					<h5 class="text-info"><span class="fa fa-font"></span> Setting Predefined Texts</h5>
					<table class="table table-condensed">
						<tr>
							<td><b>Preface Text</b><?=$fpretext;?></td>
							<td><b>Level</b> <?=$fleveltext;?>
							<p class="label label-info"><i><span class="fa fa-info-circle"></span> <var>{LEVEL}</var> will be replaced by actual level of its data</i></p></td>
						</tr>
						<tr>
							<td><b>Name Title (Left)</b><?=$ftitletext[0];?></td>
							<td><b>Name Title (Right)</b><?=$ftitletext[1];?></td>
						</tr>
						<tr>
							<td><b>Full Name of Signature (Left)</b><?=$fsignnametext[0];?></td>
							<td><b>Full Name of Signature (Right)</b><?=$fsignnametext[1];?></td>
						</tr>
						<tr>
							<td><b>ID Number of Signature (Left)</b><?=$fsignnotext[0];?></td>
							<td><b>ID Number of Signature (Right)</b><?=$fsignnotext[1];?></td>
						</tr>
					</table>
				</div>
				</td>
			</tr>
			
			<tr>
			<td colspan="2"><div class="text-right"><?=$fbtnperiod;?></div></td>
			</tr>
			</form>
			</table>
			</div></div>
			</fieldset>
			
		</div>
	</div>
	
	
	<!-- Modal Print Data-->
	<div class="modal fade bs-print-data" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open($factprint,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-print"></i> Print Data</h4>
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
					<p class="help-block">*Leave uncheck to print all data.</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<?=$fbtnprint;?>
			</div>
				<?=form_close();?>
		</div>
	  </div>
	</div>
	
	
	
	
	<script type="text/javascript">
		//select schedule	
		$(document.body).on('click', '.prevcerti' ,function(e){
			e.preventDefault();
			var href = $(this).data('href');
			$('#loading').removeClass('hidden');
			$('#divpreview').load(href,function(){
				$('#divpreview').empty();
				$('#divpreview').html('<img src="'+href+'" class="img-thumbnail" id="imgcerti"/>');
				$('#loading').addClass('hidden');
			});
		});

		$('#focussetting').click(function(){
			 $('html, body').animate({
        	scrollTop: $("#settingbox").offset().top
    		}, 500);
		});
	
	</script>
</section>