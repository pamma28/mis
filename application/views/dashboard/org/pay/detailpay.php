<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-money"></i> Payment Details</h4>
</div>
<div class="modal-body">
	<div class="pull-right">
	
	</div>
	<?=$rdata;?>
</div>
<div class="modal-footer">
	<a href="<?=base_url('Organizer/Payment/printinvoice?id=').$id;?>" class="btn btn-primary "><i class="fa fa-print fa-2x"></i> Print</a>
	<a class="btn btn-default btn-ok" data-dismiss="modal"><i class="fa fa-close fa-2x"></i> Close</a>
</div>
