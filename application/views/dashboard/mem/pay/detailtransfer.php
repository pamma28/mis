<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-bank"></i> Details Transfer</h4>
</div>
<div class="modal-body">
	<?=$rdata;?>
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok" data-dismiss="modal">Close</a>
</div>
<script type="text/javascript">
	$("#openprev").click(function(a){
		a.preventDefault();
		$('#DetailModal').removeData("bs.modal").find(".modal-content").empty();
		$('#DetailModal').find(".modal-content").load($(this).attr("href"));
	});
</script>
