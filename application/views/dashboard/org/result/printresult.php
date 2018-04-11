<div class="hidden clearfix" id="printme">
    <div class="visible">
<link href="<?php echo base_url('assets'); ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets'); ?>/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <!-- Site wrapper -->
		<div class="panel">
			
			<div class="panel-body">
				<?=$printlistlogin;?>
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