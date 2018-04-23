<div class="hidden clearfix" id="printme">
    <div class="visible">
<link href="<?php echo base_url('assets'); ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets'); ?>/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <!-- Site wrapper -->
		<div class="panel">
			<div class="panel-heading text-center">
				<h4><strong><?=$title;?></strong></h4>
			</div>
			<div class="panel-body">
				<?=$printmypds;?>
			</div>
        <div class="pull-right hidden-xs">
		<small><i>Generated on <strong><?=Date("d-m-Y H:i:s");?></strong>.</i></small>
        </div>
          <small><i>Print generated in <strong>{elapsed_time}</strong> seconds.</i></small>
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