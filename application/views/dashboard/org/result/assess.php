<!-- Content Header (Page header) -->
<section class="content-header">
  
  <h1><i class="fa fa-legal fa-lg"></i> Assess<small>Test Result</small></h1>
    <ol class="breadcrumb">
            <?=set_breadcrumb();?>
    </ol>
</section>

<?php echo form_open(base_url('Organizer/Result/updateassesment'),array('name'=>'upasses', 'method'=>'POST','class'=>'form-horizontal'));?>
<!-- Main content -->
<section class="content">
  <div class="box">
  <div class="box-body">
      <div class="panel panel-default">
    <div class="panel-body text-center">
      <h3 class="text-center"><b>Detail Test Information</b></h3>
      <hr class="divider"/>
      <div class="row">
        <h3 class="col-md-6 col-sm-6"><b>Test Code:</b> <br/><?=$t['q_randcode'];?></h3>
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
      <?php 
        $a = 1;
        $tmpsubject='';
        foreach ($generatedq as $k => $v) {
          if ($tmpsubject<>$v['subject']){
            echo '<h3><b>'.$v['subject'].'</b></h3>';
            $tmpsubject = $v['subject'];
          }
          echo '<div class="row"><div class="col-md-10 col-sm-10"><h4>'.$a.'. '.$v['question'].'</h4><div class="row">';
            
          if (!$v['qmanual']){
            $t = 'A';
            foreach ($v['allanswer'] as $key => $val) {
              $totans = count($v['allanswer']);
              $colans = floor(12/$totans);
              if (($v['pickedanswer']==$val['idans']) and ($val['keyanswer'])){
                echo '<div class="col-md-'.$colans.'"><span class="bg-green">'.$t.'. '.$val['answer'].'</span></div>';  
              } else if ($v['pickedanswer']==$val['idans']){
                echo '<div class="col-md-'.$colans.'"><span class="bg-danger">'.$t.'. '.$val['answer'].'</span></div>'; 
              } else if ($val['keyanswer']){
                echo '<div class="col-md-'.$colans.'"><span class="bg-blue">'.$t.'. '.$val['answer'].'</span></div>'; 
              } else{
                echo '<div class="col-md-'.$colans.'">'.$t.'. '.$val['answer'].'</div>';  
              }

              $t++;
            }
            ($v['answermark']=='1') ? $mark ='<p class="text-center"><b><i><span class="fa fa-check fa-success"></span> Correct</i></b></p>': $mark= '<p class="text-center"><b><i><span class="fa fa-ban fa-danger"></span> Incorrect</i></b></p>';
          } else {
            print('<div class="col-md-12 col-sm-12"><pre><p>'.$v['pickedanswer'].'</p></pre></div>'.form_hidden('arrq[]',$v['idq']));
            $mark = '<div class="text-center" width="20%"><input class="markorg" name="mark[]" id="mark'.$a.'" data-slider-id="markSlider" type="text" data-slider-min="0" data-slider-max="10" data-slider-step="0.25" data-slider-value="'.($v['answermark']*10).'" /></div>' ;
          }

          echo '</div></div><div class="col-md-2 col-sm-2"><div class="box"><h4 class="text-center"><b>Mark</b></h4><div class="box-body">'.$mark.'</div></div></div></div>';
        $a++;
        }
      ?>
    
  <h4><b>Details: </b></h4>
  <span class="bg-green">Correct</span>
  <span class="bg-danger">Picked</span>
  <span class="bg-primary">Key Answer</span>
    
      
    	
    	
  </div>
    <div class="box-footer text-center">
    <p><b><i>*Please make sure the assesment is done before you click submit.</i></b></p>
      <?=$inid;?>
      <?=$inbtn;?>
    </div>
    
  </div>
</section>
<?php echo form_close();?>

<script>  
$(document).ready(function(){
     $(".markorg").slider({
        tooltip: 'always',
        precision: 2
     }).on("slide", function(slideEvt){
          var v = slideEvt.value;
          //alert(v);
        $(this).closest('.slider-track').find('.slider-selection').css("background","yellow");
      });
});
</script>