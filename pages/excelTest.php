<?php
/** Include PHPExcel */
set_include_path(implode(PATH_SEPARATOR, array(get_include_path(), '../classes')));
spl_autoload_register();
include('/../Classes/PHPExcel.php');
$roomCheckDB = new RoomCheckDB();

$fileName = "../excel/rom8100.xlsx";
$sheetName = "Sheet1";

$excelReader = new PHPExcel();

$excelReader = PHPExcel_IOFactory::createReaderForFile($fileName);

$excelReader->setReadDataOnly();

$loadSheets = array($sheetName);
$excelReader->setLoadSheetsOnly($loadSheets);

$excelObj = $excelReader->load($fileName);

$excelObj->getActiveSheet()->toArray(null, true,true,true);

//get all sheet names from the file
$worksheetNames = $excelObj->getSheetNames($fileName);
$return = array();
foreach($worksheetNames as $key => $sheetName){
	//set the current active worksheet by name
	$excelObj->setActiveSheetIndexByName($sheetName);
	//create an assoc array with the sheet name as key and the sheet contents array as value
	$return[$sheetName] = $excelObj->getActiveSheet()->toArray(null, true,true,true);
}


foreach ($return[$sheetName] as $key => $excelRoom) {
	$roomNum = $excelRoom["A"];
	if(is_numeric($roomNum) && is_numeric($excelRoom["C"])){
		$room = new Room();
		$room->roomNum = $roomNum;
		$room->lockType = 1;
		$room->depth = substr($excelRoom["C"], 0, 4);
		$room->width = substr($excelRoom["D"], 0, 4);
		$room->height = substr($excelRoom["E"], 0, 4);
		$roomCheckDB->AddRoom($room);
	}
}
//show the final array
//var_dump($return);

?>