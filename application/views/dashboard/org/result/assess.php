<!-- Content Header (Page header) -->
<section class="content-header">
  
  <h1><i class="fa fa-legal fa-lg"></i> Assess<small>Test Result</small></h1>
    <ol class="breadcrumb">
            <?=set_breadcrumb();?>
    </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="box">
  <div class="box-body">
      <div class="panel panel-default">
    <div class="panel-body text-center">
      <h3 class="text-center"><b>Detail Test Information</b></h3>
      <hr class="divider"/>
      <div class="row">
        <h3 class="col-md-6 col-sm-6"><b>Code:</b> <br/><?=$t['q_randcode'];?></h3>
        <h3 class="col-md-6 col-sm-6"><b>Temporary Score:</b> <br/><?=$t['q_tmpscore'];?></h3>
      </div>
      <div class="row">
        <p class="col-md-6 col-sm-6"><i class="fa fa-calendar"></i> <b>Date:</b> <?=$t['jdate'];?></p>
        <p class="col-md-6 col-sm-6"><i class="fa fa-clock-o"></i> <b>Session:</b> <?=$t['jsesi'];?></p>
      </div>
      <div class="row">
        <p class="col-md-6 col-sm-6"><i class="fa fa-building"></i> <b>Room:</b> <?=$t['jroom'];?></p>
        <p class="col-md-6 col-sm-6"><i class="fa fa-wpforms"></i> <b>Test:</b> <?=$t['tname'];?></p>
      </div>
      <div class="row">
        <p class="col-md-6 col-sm-6"><i class="fa fa-user"></i> <b>Member:</b> <?=$t['mem'];?></p>
        <p class="col-md-6 col-sm-6"><i class="fa fa-unlock"></i> <b>Corrector:</b> <?=$t['org'];?></p>
      </div>
    </div>
  </div>


  <div class="panel panel-info">
    <div class="panel-body">
    <h3 class="text-center"><b>Question List</b></h3>
    <div class="row">
    <div class="col-md-10 col-sm-10">
      <?php 
        
        $a = 1;
        $tmpsubject='';
        foreach ($generatedq as $k => $v) {
          if ($tmpsubject<>$v['subject']){
            echo '<h4><b>'.$v['subject'].'</b></h4>';
            $tmpsubject = $v['subject'];
          }
          echo '<p>'.$a.'. '.$v['question'].'</p>';
          if (in_array($v['idq'], $manual)){
            $form = array(
                'name'=>'answer'.$v['idq'],
                'rows'=>'2',
                'cols'=>'150',
                'disabled'=>'disabled',
                'class'=>'form-control');
            $mark = array(
                'name'=>'mark'.$v['idq'],
                'required'=>'required',
                'class'=>'form-control');
            echo '<div class="row"><div class="col-md-10 col-sm-10">'.form_textarea($form,$v['pickedanswer']).'</div><div class="col-md-2 col-sm-2"><h4 class="text-center"><b>Mark</b></h4>'.form_input($mark).'</div></div>';

          } else {
            echo '<div class="row"><div class="col-md-1"></div>';
            $t = 'A';
            foreach ($v['allanswer'] as $key => $val) {
                $totans = count($v['allanswer']);
                $colans = floor(10/$totans);
                if (($v['pickedanswer']==$val['idans']) and ($val['keyanswer'])){
                  echo '<div class="col-md-'.$colans.'"><span class="label label-success">'.$t.'. '.$val['answer'].'</span></div>'; 
                } else if ($v['pickedanswer']==$val['idans']){
                  echo '<div class="col-md-'.$colans.'"><span class="label label-danger">'.$t.'. '.$val['answer'].'</span></div>';  
                } else if ($val['keyanswer']){
                  echo '<div class="col-md-'.$colans.'"><span class="label label-primary">'.$t.'. '.$val['answer'].'</span></div>'; 
                } else{
                  echo '<div class="col-md-'.$colans.'">'.$t.'. '.$val['answer'].'</div>';  
                }
                $t++;
            }
          echo '</div>';
          }

        $a++;
        }
      ?>
      </div>
      </div>
    </div>
  </div>
  <h4><b>Details: </b></h4>
  <span class="label label-success">Correct</span>
  <span class="label label-danger">Picked</span>
  <span class="label label-primary">Key Answer</span>
    
      <?php echo form_open(base_url('Organizer/PDS/updatepds'),array('name'=>'uplogin', 'method'=>'POST','class'=>'form-horizontal'));?>
    	
    	
    	
  </div>
    <div class="box-footer">
      <?=$inid.$inst;?>
      <a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
      <?=$inbtn;?>
    </div>
    
  </div>
</section>
<script>
  $('.selectpicker').selectpicker({
		  size: 6
		});
$(document).ready(function(){
			$("#bdate").inputmask('date');}
			);
  //check email availabelity
  $('#Email').bind('keyup change', function() {
	var email = $('#Email').val();
    $.post('<?php echo base_url('Organizer/Memberaccount/checkemail'); ?>', {email: email}, function(d) {
                        if (d == 1)
                        {
                            $('#valsuccess').css('display', 'none');
                            $('#valfailed').css('display', 'block');
                            $('#submit').attr('disabled', 'disabled');
							
                        } 
						else if(d == 0)
                        {
                            $('#valfailed').css('display', 'none');
                            $('#valsuccess').css('display', 'block');
							$('#submit').removeAttr('disabled');
                        }
                    });
	});
	
</script>