<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');		

class ImgGenerator {

	public function imgcerti($id=null,$quality=null)
	{	
		$qual = ($quality==null) ? 75 : $quality;
		$CI =& get_instance();
		$CI->load->model('Msetting');
		$CI->load->model(array('Msetting','Mcerti'));
		ob_clean();
		// Set the content type header - in this case image/jpeg
		header('Content-Type: image/jpeg');

		$fontname = FCPATH.'assets/fonts/'.$CI->Msetting->getset('fontcerti');
		$filename = base_url('upload/design/'.$CI->Mcerti->fileDefault());
		$arrsize = explode(',', $CI->Msetting->getset('sizecerti'));
		$arrmargin = explode(',', $CI->Msetting->getset('margincerti'));
		$arrcolor = explode(',', $CI->Msetting->getset('colorcerti'));
		$arrcolumn = explode(',', $CI->Msetting->getset('columncerti'));
		$arrcenter = explode(',', $CI->Msetting->getset('centercerti'));
		$pretext = $CI->Msetting->getset('pretextcerti');
		$lvltext = $CI->Msetting->getset('leveltextcerti');
		$title = $CI->Msetting->getset('titletextcerti');
		$namesign = $CI->Msetting->getset('namesigntextcerti');
		$nosign = $CI->Msetting->getset('nosigntextcerti');
		
		
		list($image_width, $height) = getimagesize($filename);
		

		 if (isset($id))
		 	{
		 		$CI->load->model('Mcerti'); 
		 		$arrcerti = $CI->Mcerti->detailcerti(array('nocerti','uname','unim','lvlname','cread','clisten','cwrite','cgrammar','cspeak'),$id);
				
		 		$texts = array($arrcerti[0]['uname'],$arrcerti[0]['nocerti']);
		 		array_push($texts, $pretext);
		 		array_push($texts,'LISTENING COMPREHENTION--'.$arrcerti[0]['clisten'].'//GRAMMAR AND STRUCTURE--'.$arrcerti[0]['cgrammar'].'//READING COMPREHENTION--'.$arrcerti[0]['cread'].'//WRITING EXPRESSION--'.$arrcerti[0]['cwrite'].'//SPEAKING--'.$arrcerti[0]['cspeak']);
		 		array_push($texts, str_replace('{LEVEL}', strtoupper($arrcerti[0]['lvlname']), $lvltext));
		 		array_push($texts, $title,$namesign,$nosign);

		 	} else {


		 		$texts = array('FULL NAME','Certificate Number');
		 		array_push($texts, $pretext);
		 		array_push($texts,
		 				'LISTENING COMPREHENTION--A//GRAMMAR AND STRUCTURE--A//READING COMPREHENTION--A//WRITING EXPRESSION--A//SPEAKING--A');
		 		array_push($texts, str_replace('{LEVEL}','ELEMENTARY', $lvltext));
		 		array_push($texts, $title,$namesign,$nosign);
			}
			

		 		// define the base image that we lay our text on
				$im = imagecreatefromjpeg($filename);

				// setup the text colours
				$color['black'] = imagecolorallocate($im, 0, 0, 0);
				$color['green'] = imagecolorallocate($im, 55, 189, 102);
				$color['blue'] = imagecolorallocate($im, 0, 30, 255);
				$color['red'] = imagecolorallocate($im, 255, 30, 0);

				// this defines the starting height for the text block
				$y = imagesy($im) - $height;
					 
				// loop through the array and write the text
				$u=1; $i=0;	$prevsize=0;end($texts); $last = prev($texts);
				foreach ($texts as $k=>$value){
					$i += $arrmargin[$k];
				
						//count how many line	
						$arrline = explode('//', $value);
					if ($arrcolumn[$k]=='1')	
					{ 
						if(count($arrline)>1){
							$ay = $y;
							foreach ($arrline as $a => $line) {
								// center the text in our image - returns the x value
								$dimensions = imagettfbbox($arrsize[$k], 0, $fontname, $line);
								$x = ceil(($image_width - $dimensions[4]) / 2);
								imagettftext($im, $arrsize[$k], 0, $x, $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $line);
								$ay+=$arrsize[$k]+ceil($arrsize[$k]/2);
								$y = $ay;
							}
						} else {
							// center the text in our image - returns the x value
							$dimensions = imagettfbbox($arrsize[$k], 0, $fontname, $value);
							$x = ceil(($image_width - $dimensions[4]) / 2);
							imagettftext($im, $arrsize[$k], 0, $x, $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $value);
							($u==1) ?  imageline( $im, $x , $y+$i+$prevsize+3 , $x+$dimensions[4]+10 , $y+$i+$prevsize+3 ,  $color[$arrcolor[$k]] ): null;
						}
					} else {
						//if column were 2 and >1 line
						if(count($arrline)>1){
							$ay = $y;
							foreach ($arrline as $a => $line) {
								$arrtext = explode('--',$line);
								$firsttext = imagettfbbox($arrsize[$k], 0, $fontname, $arrtext[0]);
								$column = ceil($image_width/$arrcolumn[$k]);
								$secondtext = imagettfbbox($arrsize[$k], 0, $fontname, $arrtext[1]);
								
								if($arrcenter[$k]){	
								imagettftext($im, $arrsize[$k], 0, ceil(($column - $firsttext[4])/2), $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[0]);
								imagettftext($im, $arrsize[$k], 0, ceil(((3*$column)-$secondtext[4])/2), $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[1]);
								} else {
									imagettftext($im, $arrsize[$k], 0, $column-ceil($column/2), $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[0]);
									imagettftext($im, $arrsize[$k], 0, $column+ceil($column/2), $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[1]);

								}
								$ay += $arrsize[$k] + ceil($arrsize[$k]/2);
								$y = $ay;
							}
						} else {
							$arrtext = explode('--',$value);
							$firsttext = imagettfbbox($arrsize[$k], 0, $fontname, $arrtext[0]);
							$column = ceil($image_width/$arrcolumn[$k]);
							$secondtext = imagettfbbox($arrsize[$k], 0, $fontname, $arrtext[1]);
							if($arrcenter[$k]){	
								imagettftext($im, $arrsize[$k], 0, ceil(($column - $firsttext[4])/2), $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[0]);
								imagettftext($im, $arrsize[$k], 0, ceil(((3*$column)-$secondtext[4])/2), $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[1]);
							} else {
								imagettftext($im, $arrsize[$k], 0, $column-ceil($column/2), $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[0]);
								imagettftext($im, $arrsize[$k], 0, $column+ceil($column/2), $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[1]);

							}
						}
						
						//create underline
						if ($last==$value){
							imageline( $im, ceil(($column - $firsttext[4])/2) , $y+$i+$prevsize+3 , ceil(($column + $firsttext[4])/2) , $y+$i+$prevsize+3 ,  $color[$arrcolor[$k]]);
							imageline( $im, ceil(((3*$column)-$secondtext[4])/2), $y+$i+$prevsize+3 , ceil(((3*$column)+$secondtext[4])/2) , $y+$i+$prevsize+3 ,  $color[$arrcolor[$k]]);
							
						}
					}

					// add 32px to the line height for the next text block
					$u++; $prevsize=$arrsize[$k];
					
				}

		 	
		// Skip the filename parameter using NULL, then set the quality to 75%
		imagejpeg($im, NULL, $qual);

		// Free up memory
		imagedestroy($im);
		
	}

}

