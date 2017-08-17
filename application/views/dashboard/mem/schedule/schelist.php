<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-check-square-o fa-lg"></i> Choose<small>Schedule</small></h1>
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
			<?php if ($date) { ?>
			<div class="panel panel-primary">
				<div class="panel-heading panel-heading-sm">
					<h5 class="panel-title text-center"><b>Schedule List</b></h5>
				</div>
				<div class="panel-body">
				<?=$listdata;?>
				</div>
			</div>
			<hr class="divider"/>
			<div class="row">
				<div id="loading" class="hidden col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4">
					<div class="circle"></div>
					<div class="circle1"></div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading panel-heading-sm">
					<h5 class="panel-title text-center"><b>My Schedule</b></h5>
				</div>
				<div class="panel-body">
				<?=$mysche;?>
				</div>
			</div>
			<?php } else { ?>
			<div class="col-md-12 cols-sm-12">
			<h3 class="text-center text-primary">Out of Choose Schedule Phase</h3> <hr class="divider"/>
			<div class="panel panel-default">
				<div class="panel-body">
					<h3 class="text-center"><span class="label label-warning"><i class="fa fa-exclamation-triangle"></i> Please choose schedule on <b><?=$startdate;?></b> until <b><?=$enddate;?></b>.</span></h3>
				</div>
			</div>
			</div>
			<?php } ?>
		</div>
		
	</div>
	
	<div id="modalreport" class="modal fade bs-report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
			<div class="modal-header">
			   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			   <h4><i class="fa fa-info-circle text-info"></i> Notification</h4>
			</div>
			<div class="modal-body">
			   <p class="text-center" id="reporttext"></p>
			</div>
        </div>
    </div>

	<script type="text/javascript">
	$('#modalreport').on("hidden.bs.modal", function (e) {
	    $('#loading').addClass("hidden");
		});
	$('#modalreport').on('shown.bs.modal', function() {
	})
	//select schedule	
		$(document.body).on('click', '.btn-choose' ,function(e){
			e.preventDefault();
			$('#loading').removeClass('hidden');
			// ajax progress
			var stat = [],
			idjdwl = $(this).data('id'),
			btn = $(this).closest('td'),
			quo = $(this).closest('tr').find(".jquo");
			$.post($(this).attr('data-href'),{
				id : idjdwl }, 
				function (data) {
					stat = $.parseJSON(data);
				if (stat[0]){
					btn.html('<span class="label label-default disabled" data-id="'+idjdwl+'"><i class="fa fa-minus"></i> Choosen </span>');
					quo.text(parseInt(quo.text())-1);
					$('#reporttext').html(stat[1]);
					$('#modalreport').modal('show');
					$('#mysche').append(stat[2]);
				} else {
					if (stat[1].indexOf('Quota') >= 0){
						quo.text(0);
						btn.html('<span class="label label-default disabled" data-id="'+idjdwl+'"><i class="fa fa-minus"></i> No Quota </span>');
					}
					$('#loading').addClass("hidden");
					$('#reporttext').html(stat[1]);
					$('#modalreport').modal('show');
				}
	        });
		});
	//delete schedule	
		$(document.body).on('click', '.btn-remove' ,function(e){
			e.preventDefault();
			$('#loading').removeClass('hidden');
			// ajax progress
			var stat = [],
			idjdwl = $(this).data('id');
			$content = $(this).closest('tr');
			$pil = $("#mylist").find('span[data-id="'+idjdwl+'"]').closest('td');
			quo = $("#mylist").find('span[data-id="'+idjdwl+'"]').closest('tr').find('.jquo');
			$.post($(this).attr('data-href'),{
				id : idjdwl }, 
				function (data) {
					stat = $.parseJSON(data);
				if (stat[0]){
					quo.text(parseInt(quo.text())+1);
					$('#reporttext').html(stat[1]);
					$content.remove();
					$pil.empty();
					if (quo.text()!=0){
					$pil.html('<a href="#mysche" data-href="<?=base_url('Member/Scheduletest/choosesche');?>"  role="button" alt="Choose" class="btn btn-default btn-xs btn-choose" title="Choose" data-id="'+idjdwl+'"><i class="fa fa-check"></i> Choose</a>');
					} else {
						$pil.html('<span class="label label-default disabled" data-id="'+idjdwl+'"><i class="fa fa-minus"></i> No Quota </span>');
					}
					$('#modalreport').modal('show');
				} else {
					
					$('#loading').addClass("hidden");
					$('#reporttext').html(stat[1]);
					$('#modalreport').modal('show');					
				}
	        });
		});
	
	</script>

</section>