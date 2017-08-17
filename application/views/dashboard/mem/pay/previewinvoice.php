<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-file-text"></i> Invoice Preview</h4>
</div>
<div class="modal-body">
	<a href="<?=$printlink;?>" class="btn btn-primary btn-block"><i class="fa fa-print"></i> Print Now</a>
	<?=$rdata;?>
</div>
<div class="modal-footer">
	<a href="<?=$printlink;?>" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
	<a class="btn btn-default btn-ok" data-dismiss="modal">Close</a>
</div>