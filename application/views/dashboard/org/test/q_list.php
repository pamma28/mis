<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Setting <small>Parameters</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<section class="content">

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">Placement Test</h3>
	</div>
	<div class="panel-body">
		<table class="table table-hover table-strip">
		<thead>
		<tr>
		<th>Subject</th><th>Question</th><th>Answer</th><th>Key</th><th>Menu</th></tr>
		</thead>
		<tbody>
		<tr>
		<td>Reading</td>
		<td>what mean?</td>
		<td>
		<div class="row">
			<div class="col-md-6">
			A. 
			<br/>
			B. 
			<br/>
			C. 
			</div>
			<div class="col-md-6">
			D. 
			<br/>
			E. 
			</div>
		</div>
		</td>
		<td>
			C.
		</td>
		<td><a href="http://localhost/mis/Admin/fullpds?id=35" alt="Full Data"><span class="fa fa-list-alt"></span></a> 
		<a href="http://localhost/mis/Admin/editpds?id=35" alt="Edit Data"><span class="fa fa-edit"></span></a> 
		<a href="http://localhost/mis/Admin/deletepds?id=35" alt="Delete Data" onclick="return confirm('Delete?')"><span class="fa fa-trash-o"></span></a>
		</td></tr>
		</tbody>
		</table>
	
	</div>
</div>				
</section>
<script type="text/javascript">
var t= new Date();
t.setMinutes(t.getMinutes() + 30);
$('#time').countdown(t)
	.on('update.countdown', function(event) {
	var format = '%H:%M:%S';
	$(this).html(event.strftime(format));
	})
	.on('finish.countdown', function(event) {
	$(this).html('This time has expired!')
	.parent().addClass('disabled');
	});
</script>