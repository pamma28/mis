<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-list fa-lg"></i> Subject<small>Data</small></h1>
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
				<a href="<?=base_url('Organizer/Subject/addsubject?url=').base_url('Organizer/Subject/allsubject');?>" data-target="#DetailModal" data-toggle="modal" role="button" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Subject</a> 
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
			</div>
		</div>	
	</div>
	<div class="box-body table-responsive">
		<div class="btn-toolbar">
			<div class="btn-group">
					<button class="btn btn-default btn-sm" type="button">Action:</button>
					<button data-toggle="dropdown" class="btn btn-default dropdown-toggle btn-sm" type="button">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
					</button>
					<ul role="menu" class="dropdown-menu">
					<a href="#" class="btn btn-sm text-danger" data-finput="0" data-btn="btn btn-sm btn-danger" data-icon="fa fa-ban" data-title="Delete All" data-toggle="modal" data-target=".bs-selecteddata"><i class="fa fa-trash-o"></i> Delete All</a>
					</ul>
			</div>
		</div>
		<div class="btn-group pull-right">
		
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
			
		</div>
	</div>
	
	<!-- Modal Details Data-->
	<div class="modal fade" id="DetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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
		tables.push('- '+$(this).parent().parent().find('.idname').text()+'<br/>');
		values.push($(this).val());
		$('#selecteduser').html(tables);
		$('#selectedid').val(values);
		});
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