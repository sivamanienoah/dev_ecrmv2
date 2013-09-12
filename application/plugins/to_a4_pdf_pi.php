<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
function pdf_create2($html, $filename, $stream = TRUE)
{

  require_once("dompdf/dompdf_config.inc.php");
  require_once("file_pi.php");

  $dompdf = new DOMPDF();
  $dompdf->load_html($html);
  $dompdf->set_paper('a4', 'portrait');
  $dompdf->render();
  
  if ($stream == TRUE)
  {
	$dompdf->stream($filename.".pdf");
  }
  else{
	//$file_path = dirname(FCPATH) . "/reading/wpdata/images/pdf/";

	if (!write_file($file_path . $filename.".pdf", $dompdf->output()))
	{
		 echo $file_path;
		 echo 'Unable to write the file';
	}
  }
}

function pdf_create($html, $filename, $stream = TRUE)
{
	 echo 'here' ;
  require_once("dompdf/dompdf_config.inc.php");
  require_once("file_pi.php");
  
  $dompdf = new DOMPDF();
  
			
	//$dompdf->image("c://xmapp/htdocs/vcslocaldev/assets/img/qlogo.jpg", "JPG", 20, 10, 137, 100);
	
  $dompdf->load_html($html);
  $dompdf->set_paper('a4', 'portrait');
  $dompdf->render();
   
  if ($stream == TRUE)
  {
    $dompdf->stream($filename.".pdf");
  }
  else
  { 
    $file_path = dirname(FCPATH) . "/reading/css/pdf/";
    //echo $file_path;
    write_file($file_path . $filename.".pdf", $dompdf->output());  
  }  
}


