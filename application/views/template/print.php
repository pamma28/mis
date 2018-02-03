
<div class="hidden" id="printme">
    <!-- Site wrapper -->
    <div class="visible">
		<div class="panel">
			<div class="panel-heading text-center">
				<h4><strong><?=$title;?></strong></h4>
			</div>
			<div class="panel-body">
				Detail Registration Data:
				
			</div>
        <div class="pull-right hidden-xs">
		<small><i>Generated on <strong><?=Date("d-m-Y H:i:s");?></strong>.</i></small>
        </div>
          <small><i>Print generated in <strong>{elapsed_time}</strong> seconds.</i></small>
		</div>
    </div><!-- ./wrapper -->
	
	<script>
		$('#startprint').click(function(){
			$('#printme').show().printThis({
			debug: false,
			importCSS: false,
			printContainer: true,
			loadCSS: "",
			pageTitle: "",
			removeInline: false
			});
		});
	</script>
</div>