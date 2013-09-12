<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
function pdf_create($html, $filename, $stream = TRUE, $invoice = FALSE)
{
	
  require_once("dompdf/dompdf_config.inc.php");
  
  $dompdf = new DOMPDF();
  $dompdf->load_html($html);
  $dompdf->render();
  
  //$log_path = BASEPATH . 'logs/pdf_create_log.txt';
  //$fp = fopen($log_path, 'a+');
  
  //fwrite($fp, "pdf call came in\n");
  
  if ($stream == TRUE)
  {
	
    $dompdf->stream($filename.".pdf");
    //fwrite($fp, "Stream request received - delivered\n");
  }
  else
  {
    
    //fwrite($fp, 'write request received');
    
    $file_path = dirname(FCPATH) . '/vps_data/';
    if ($invoice == TRUE) $file_path .= 'invoice_data/';
    
    $written = write_file($file_path . "{$filename}.pdf", $dompdf->output());
    
       
    
  }
  
  //fclose($fp);
  
}