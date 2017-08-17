<div class=" hidden clearfix" id="printme">
    <div class="visible">
<link href="<?php echo base_url('assets'); ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets'); ?>/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <!-- Print wrapper -->
	<div class="row">
		<div class="col-md-6 col-offset-md-3">
		<div class="panel ">
			<div class="panel-heading">
				<div class="text-center"><h4><b>PAYMENT INVOICE NO:<?=$notrans;?></b></h4></div>
			</div>
			<div class="panel-body">
					<?=$rdata;?>
			</div>
			<div class="panel-footer">
				<div class="well">
				<br/>
				<br/>
				<br/>
				<br/>
				<b>Signed and Stamped by: </b>
				</div>
				<div class="text-right">
						<small><i>Issued by: <?=$pic.' on '.date("d-m-Y H:i:s");?></i></small>
				</div>
			</div>
			</div>
		</div>
	</div>
    </div><!-- ./wrapper -->
<script>
window.addEventListener('load', function () {
			$('.visible').show().printThis({
			debug: false,
			importCSS: false,
			printContainer: true,
			loadCSS: "",
			pageTitle: "",
			removeInline: false
			});
		}, false);
		
</script>
</div>