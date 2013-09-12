<?
error_reporting(0);
require('html2fpdf.php');
$pdf=new HTML2FPDF();
$pdf->AddPage();
$fp = fopen("sample.html","r");
$strContent = fread($fp, filesize("sample.html"));
fclose($fp);
$pdf->WriteHTML($strContent);
$pdf->Output("sample.pdf");
//fwrite("sample.pdf",'w');
echo "PDF file is generated successfully!";
echo '<br><a href ="sample.pdf" title="sample PDF">Click Here</a>';
?>