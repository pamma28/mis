<div id="containeradd">
<?php echo form_open(base_url('Organizer/Article/savearticle'),array('name'=>'addtmp', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-newspaper-o"></i> Add Article</h4>
</div>
<div class="modal-body">
	<?=$rdata;?>
</div>
<div class="modal-footer">
	<?=$inid;?>
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inbtn;?>
</div>
<?php echo form_close();?>
</div>
<script>
$(document).ready(function() {
$("#fatclcont").summernote({
			 minHeight: 275
		});	
	$('#btncat').on('click', function(){
			$(this).parent().find("i.fa").toggleClass('fa-plus fa-minus');;
		});
	
	$('#faddcat').click(function(){
		if ($('#reportadd').html()!=''){
			$('#faddcat').val('');
			$('#reportadd').html('');
		}
	});
	
	$("#fbtnaddcat").click(function(){
		if ($('#faddcat').val()!='')
		{
		$.post('<?=base_url('Organizer/Article/addcategory')?>',{
					category: $('#faddcat').val()
				}, function (data) {
                if (data == true){
					$('#containeradd').html('');
					$('#containeradd').load("<?=base_url('Organizer/Article/addarticle');?>",function(e){
					$('#reportadd').attr('class','text-success');
					$('#reportadd').html('<b>Success add Category.</b>');
					
						
					});
					
				} else {
					$('#reportadd').attr('class','text-danger');
					$('#reportadd').html('<b>Error: failed to add Category.</b>');
					$('#faddcat').focus();
				}
            });
		} else {
			alert("Error: Category field is blank.");
			$('#faddcat').focus();
		}
	});
});

</script>
