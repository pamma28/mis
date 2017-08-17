<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-image fa-lg"></i> Certificate<small>Design</small></h1>
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
				<a href="<?=base_url('Organizer/Design/adddesign');?>" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#DetailModal"><i class="fa fa-plus"></i> Add </a>
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
		
		<div class="form-inline">
		<div class="input-group">
			<span class="input-group-addon"><?=$checkall;?></span>
			<span class="input-group-addon">Check All</span>
		</div>
			<div class="input-group">
					
					<button data-toggle="dropdown" class="btn btn-default dropdown-toggle btn-sm" type="button">
					Actions: 
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
					</button>
					<ul role="menu" class="dropdown-menu">
					<a href="#" class="btn btn-sm text-success" data-finput="1" data-btn="btn btn-sm btn-success" data-icon="fa fa-check" data-title="Allow All" data-toggle="modal" data-target=".bs-selecteddata"><i class="fa fa-check"></i> Allow All</a>
					<a href="#" class="btn btn-sm text-danger" data-finput="0" data-btn="btn btn-sm btn-danger" data-icon="fa fa-ban" data-title="Deny All" data-toggle="modal" data-target=".bs-selecteddata"><i class="fa fa-ban"></i> Deny All</a>
					</ul>
			</div>
		</div>
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
			<br/>
			<br/>
			
			<fieldset class="scheduler-border">
			<legend class="scheduler-border"><a role="button" data-toggle="collapse" href="#collapseSetting" aria-expanded="false" aria-controls="collapseSetting">Setting Registration Phase</a></legend>
			<div id="collapseSetting" class="collapse">
			<form name="fsetperiod" class="form-inline" action="<?=$fsendper;?>" method="POST" >
			<div class="form-group">
				<label class="input-label" for="startTime">Registration Phase (Range Date) :</label>
				<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span><?=$fregist;?>
				</div>
			</div>
			<?=$fbtnperiod;?>
			</form>
			</div>
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
					<span class="label label-danger"><i class="fa fa-exclamation-triangle"></i> Do not choose all columns, it will not fit into 1 page (too wide).</span>
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
	
	<!-- Modal Details Data-->
	<div class="modal fade" id="DetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
			
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
	
	<!-- Modal Selected Data-->
	<div class="modal fade bs-selecteddata" id="SelectedModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<?php echo form_open($factselected,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" ><i id="selectedicon"></i> <span id="selectedtitle"></span></h4>
			</div>
			<div class="modal-body">
					Are you sure want to <span id="selectedcontent"></span> selected data?
					<div class="bg-info" id="selecteduser" style="max-height:100px;overflow-y: auto;width:250px;"></div>
			</div>
			<div class="modal-footer">
				<?=$idac.$idtype;?>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input type="submit" id="selectedbutton" value="Confirm">
			</div>
				<?=form_close();?>
        </div>
    </div>
	</div>
	
	
	<script type="text/javascript">
	//delete modal confirmation
	$('#confirm-delete').on('show.bs.modal', function(e) {
		$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
		});
	
	// populate checked data and show in modal
	$('#SelectedModal').on('show.bs.modal', function(e) {
		$(this).find('#selectedicon').attr('class',$(e.relatedTarget).data('icon'));
		$(this).find('#selectedtitle').text($(e.relatedTarget).data('title'));
		$(this).find('#selectedcontent').text($(e.relatedTarget).data('title'));
		$(this).find('#selectedbutton').attr('class',$(e.relatedTarget).data('btn'));
		$(this).find('#selectedtype').val($(e.relatedTarget).data('finput'));
		var tables = new Array();
		var values = new Array();
		$.each($("input[name='check[]']:checked"), function() {
		tables.push('- '+$(this).parent().find('.idname').text()+'<br/>');
		values.push($(this).val());
		$('#selecteduser').html(tables);
		$('#selectedid').val(values);
		});
		if($(e.relatedTarget).data('finput')=='2'){$('#selectedid').val($(e.relatedTarget).data('fconfirm'));}
		});
	
	//details data	
		$('#DetailModal').on("hidden.bs.modal", function (e) {
		$(e.target).removeData("bs.modal").find(".modal-body").empty();
		});
			
		
	$('.selectall').click(function() {
        $('.selectcol option').prop('selected', true);
    });
	$('.unselectall').click(function() {
        $('.selectcol option').prop('selected', false);
    });
	$('.close').click(function() {
        $('.selectcol option').prop('selected', false);
    });
	
	// function check/uncheck all at once
	$(function () {
    $("#c_all").click(function () {
        if ($("#c_all").is(':checked')) {
            $("input[name='check[]']").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("input[name='check[]']").each(function () {
                $(this).prop("checked", false);
            });
        }
    });
	});
	
	
	
	</script>
</section>