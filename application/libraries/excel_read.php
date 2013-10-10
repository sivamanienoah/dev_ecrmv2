<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 *  ======================================= 
 *  Author     : Muhammad Surya Ikhsanudin 
 *  License    : Protected 
 *  Email      : mutofiyah@gmail.com 
 *   
 *  Dilarang merubah, mengganti dan mendistribusikan 
 *  ulang tanpa sepengetahuan Author 
 *  ======================================= 
 */  
require_once "PHPExcel/IOFactory.php"; 
require_once "PHPExcel.php"; 

class Excel_read extends PHPExcel { 
    public function __construct() { 
        parent::__construct(); 
    } 
	public function parseSpreadsheet($file) {
		// Open the Excel document and grab information about it.
		$excelReader = PHPExcel_IOFactory::createReaderForFile($file);
		$spreadsheetInfo = $excelReader->listWorksheetInfo($file);
		
		// Initial setup of read filter.
		$readFilter = new MyReadFilter();
		$excelReader->setReadFilter($readFilter);
		$chunkSize = 500000;

		for ($startRow = 0; $startRow <= $spreadsheetInfo[0]['totalRows']; $startRow += $chunkSize){
			$readFilter->setRows($startRow, $chunkSize);
			// Read the next batch of rows
			$excelDataObj = $excelReader->load($file);
			$excelDataObj->setActiveSheetIndex(0);
			// Turn the rows read into an array
			$excelData = $excelDataObj->getActiveSheet()->toArray(null, true, true, true);
		}
		return $excelData;
	}
}

class MyReadFilter implements PHPExcel_Reader_IReadFilter {
	private $_startRow = 0;
	private $_endRow = 0;

	public function setRows($startRow, $chunkSize) {
		$this->_startRow = $startRow;
		$this->_endRow = $startRow + $chunkSize;
	}

	public function readCell($column, $row, $worksheetName = '') {
		if ($row >= $this->_startRow && $row <= $this->_endRow) return true;
		return false;
	}
}
				
?>