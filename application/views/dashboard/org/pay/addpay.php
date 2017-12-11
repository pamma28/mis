<!-- Content Header (Page header) -->
<section class="content-header">
	<h1><i class="fa fa-plus"></i> Add<small>Payment Data</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
	<h2 align="center">Payment Cashier</h2>
		
	<div class="row">
		<div class="col-md-8">
				<?php if ($this->session->flashdata('v')!=null){ ?>
				<div style="padding:0 20px;">
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<?=$this->session->flashdata('v');?>
				</div>
				</div>
				<?php } else if ($this->session->flashdata('x')!=null){ ?>
				<div style="padding:0 20px;">
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<?=$this->session->flashdata('x');?>
				</div>		
				</div>		
				<?php } ?>
		<?php echo form_open(base_url('Organizer/Payment/savepay'),array('name'=>'addpay', 'method'=>'POST'));?>
			<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="text-center"><b>Invoice Detail</b></div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6 col-sm-6" align="center">
						<div class="info-box well">
						<span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
						<div class="info-box-content">
						  <span class="info-box-text">Total Paid</span>
						  <h3>Rp. <span id="tpaid">0</span> ,-</h3>
						</div>
						<!-- /.info-box-content -->
					  </div>
					</div>
					<div class="col-md-6 col-sm-6" align="center">
						<div class="info-box well">
						<span class="info-box-icon bg-primary"><i class="fa fa-money"></i></span>

						<div class="info-box-content">
						  <span class="info-box-text">Total Change</span>
						  <h3>Rp. <span id="tchange">0</span> ,-</h3>
						</div>
						<!-- /.info-box-content -->
					  </div>
					</div>
				</div>
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=$col[0];?></label>
						<div class="col-sm-9"><?=$notrans;?></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=$col[2];?></label>
						<div class="col-sm-9"><?=$piluser;?></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=$col[1];?></label>
						<div class="col-sm-9"><?=$piljtrans;?></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=$col[4];?></label>
						<div class="col-sm-9"><?=$paid;?>
						<small><i>*Please add (-) minus into nominal paid, if transaction is withdrawal.</i></small></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=$col[3];?></label>
						<div class="col-sm-9"><?=$nomi;?></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=$col[5];?></label>
						<div class="col-sm-9"><?=$ret;?></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=$col[6];?></label>
						<div class="col-sm-9"><?=$vto;?></div>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<div class="btn-group btn-group-justified" role="group" aria-label="myButton">
					<div class="btn-group" role="group">
						<button type="reset" class="btn btn-danger btn-lg">Reset</button>
					</div>
					<div class="btn-group" role="group">
					  <?=$vtoo.$notran.$met.$inred;?>
					</div>
					<div class="btn-group" role="group">
					  <?=$inbtn;?>
					</div>
				</div>
			</div>
			</div>
			<?=form_close();?>
		</div>
		<div class="col-md-4">
			<div class="row">
				<div align="center" class="col-md-12 col-sm-12"> 
				<div class="box box-primary box-solid">
					<div class="box-header with-border">Info Selected Member</div>
					<div class="bg-aqua"><h3 id="ulunas">---------</h3></div>
					<div class="row">
							<div class="col-md-6 col-sm-6" align="center">
								<div class=" bg-info"><i class="fa fa-calculator fa-2x"></i>
								<div class="panel-body" align="center">
								  <span class="info-box-text">Total Transaction</span>
								  <h4 id="tottrans">0</h4>
								</div>
								<!-- /.info-box-content -->
								</div>
							</div>
							<div class="col-md-6 col-sm-6" align="center">
								<div class="bg-success"><i class="fa fa-credit-card-alt fa-2x"></i>
								<div class="panel-body" align="center">
								  <span class="info-box-text">Total Paid</span>
								  <h4>Rp. <span id="totpaid">0</span>,-</h4>
								</div>
								<!-- /.info-box-content -->
								</div>
							</div>
					</div>
					<small><i>
					*Find out details transaction history (if any) in the bottom.
					</i></small>
				</div>
				</div>
			</div>
			<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="text-center"><b>Invoice Preview</b></div>
			</div>
			<div class="panel-body">
					<div align="center">
						<img class="img-thumbnail" style="height:100px;" src="<?=base_url('upload/qr/sample.png');?>"/>
						<p class="text-info">Scan to find out Your Invoice Number.</p>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label>Invoice Issued</label></div>
						<div class="col-sm-6 col-md-7">: <span id="vdate"></span> </div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label><?=$col[6];?></label></div>
						<div class="col-sm-6 col-md-7">: <span  id="vvto"></span> </div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label><?=$col[0];?></label></div>
						<div class="col-sm-6 col-md-7">: <span id="vnoinv"></span> </div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label><?=$col[2];?></label></div>
						<div class="col-sm-6 col-md-7">: <span id="vname"></span> </div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label>NIM</label></div>
						<div class="col-sm-6 col-md-7">: <span id="vnim"></span> </div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label>Email</label></div>
						<div class="col-sm-6 col-md-7">: <span id="vmail"></span> </div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label><?=$col[1];?></label></div>
						<div class="col-sm-6 col-md-7">: <span id="vtype"></span> </div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label><?=$col[4];?></label></div>
						<div class="col-sm-6 col-md-7">: Rp. <span id="vpaid">0</span> ,-</div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label><?=$col[3];?></label></div>
						<div class="col-sm-6 col-md-7">: Rp. <span id="vnomi">0</span> ,-</div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-5"><label><?=$col[5];?></label></div>
						<div class="col-sm-6 col-md-7">: Rp. <span id="vchange">0</span> ,-</div>
					</div>
			</div>
			<div class="panel-footer">
				<div class="text-right">
					
						<small><i>Issued by: <?=$this->session->userdata('name');?> on <?=date('d-M-Y H:i:s');?></i></small>
				</div>
			</div>
			</div>
		</div>
	</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="box box-info box-solid">
				<div class="box-header with-border" align="center">
				<b>Transaction History </b>
					<div class="box-tools pull-right">
                    <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-caret-up fa-lg"></i></button>
					</div>
				</div>
			<div class="box-body">
			
			<div class=" table-responsive">
			<table class="table table-hover table-bordered" id="recordedtrans">
				<tr>
					<th>Invoice Date</th>
					<th><?=$col[6];?></th>
					<th><?=$col[0];?></th>
					<th><?=$col[1];?></th>
					<th><?=$col[4];?></th>
					<th><?=$col[3];?></th>
					<th><?=$col[5];?></th>
					<th>PIC</th>
				</tr>
			</table>
			</div>
			</div>
			</div>
		</div>
	</div>
	
		<script>
		 $('.selectpicker').selectpicker({
		  size: 6
		});
		  
			$('#User').on('change',function(e) {
			var selected = $(this).find('option:selected').val();
			$.post('<?php echo base_url('Organizer/Payment/getdetailpay'); ?>',{user: selected}, function(d) {
				d = $.parseJSON(d);
				$(function() {
						$("#recordedtrans").find("tr:gt(0)").remove();
						$('#totpaid,#tottrans').html('0');
						$('#ulunas').html('---------');
						$.each(d, function(i, item) {
							var $tr = $('<tr>').append(
								$('<td>').text(item.tdate),
								$('<td>').text(item.valid_to),
								$('<td>').text(item.tnotrans),
								$('<td>').text(item.transname),
								$('<td>').text(item.tpaid),
								$('<td>').text(item.tnomi),
								$('<td>').text(item.tchange),
								$('<td>').text(item.rname)
							).appendTo('#recordedtrans');
						$('#totpaid').html(item.totpaid);
						$('#tottrans').html(item.tottrans);
						$('#vnoinv').html(item.notrans);
						$('#vname').html(item.mname);
						$('#vnim').html(item.unim);
						$('#vmail').html(item.uemail);
						$('#NoTrans').val(item.notrans);
						$("input[name='fnotrans']").val(item.notrans);
							if (item.ulunas=='1'){
							$('#ulunas').html('Fully Paid');
							}else if (item.ulunas=='0'){
							$('#ulunas').html('Not Yet');
							} else{
							$('#ulunas').html('---------');}
						});
					});
				
				});
			});  

			$('#User,#TransactionType,#nomi,#paid,#change,#validto').on('change',function(e) {
				$('#vdate').html(moment().format('DD-MMM-YYYY HH:mm:ss'));
				$('#vtype').html($('#TransactionType option:selected').text());
				$('#vnim').html($('#User option:selected').val());
				$('#vnoinv').html($('#NoTrans').val());
				$('#vnomi').html($('#nomi').val());
				$('#vpaid,#tpaid').html($('#paid').val());
				$('#change').val($('#nomi').val()-$('#paid').val());
				$('#vchange,#tchange').html($('#nomi').val()-$('#paid').val());
				$('#vvto').html(moment().add('M',$('#validto').val()).format('DD-MMM-YYYY HH:mm:ss'));
				$("input[name='fvtoo']").val(moment().add('M',$('#validto').val()).format('YYYY-MM-DD HH:mm:ss'));
			});
			$("#nomi,#paid,#change,#validto").numeric();
			
			$('#redi').on('click',function(e) {
				$('input[name="fredi"]').val('1');
			});
		</script>
</section>