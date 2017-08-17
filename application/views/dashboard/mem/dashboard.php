<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Dashboard<small>Member</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box box-solid box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
				<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title text-center "><b class="text-primary">Membership Progress</b></h4>
				</div>
				<div class="panel-body">
					<div style="display:inline-block;width:100%;overflow-y:auto;">
					<ul class="timeline timeline-horizontal">
						<?=$arrprogress;?>
					</ul>
				</div>
				</div>
				</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
				<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title text-center"><?=$tmptitle;?></h4>
				</div>
				<div class="panel-body">
					<?=$tmpcontent;?>
				</div>
				</div>
				</div>
			</div>

			<hr class="divider"/>
			<div class="row">
            	<div class="col-md-6" >
            	<div class="panel panel-default">
            		<div class="panel-body">             
					<h4 class="text-center text-info"><b>Recent Articles</b></h4>
					<hr/>
						<ul class="products-list product-list-in-box">
							<?=$dtatcl;?><!-- /.item -->
						</ul>
					</div>
				</div>
           		 </div><!-- /.col -->

				<div class="col-md-6">
					<div class="panel panel-default">
					<div class="panel-body">
					<h4 class="text-center text-info"><b>Agendas</b></h4>
					<hr/>
						<?=$dtagn;?>
             		</div>
             		</div>
					
				</div>
          	</div>
		
		</div>
		</div>
	</div>
		
</section>		