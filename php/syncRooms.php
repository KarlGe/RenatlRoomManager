<?php
		include("../snippets/phpInit.php");
		$dataArray = null;
		$deletedRooms = null;
		if(isset($_POST["rooms"])){
			$dataArray = $_POST["rooms"];
		}
		if(isset($_POST["deletedRooms"])){
			$deletedRooms = $_POST["deletedRooms"];
		}
		$roomArray = array();
		foreach ($dataArray as $room) {
			$newRoom = new Room(
				$room["dbID"], $room["roomNum"], $room["poster"], $room["roomDepth"], 
				$room["roomWidth"], $room["roomHeight"], $room["lockType"], $room["notes"], 
				$room["moveOutDate"]);
			if($room["notes"]){
				$newRoom->offlineRoom = true;
			}
			array_push($roomArray, $newRoom);
		}
		//$roomCheckDB->SyncRooms($roomArray);
		echo ($roomCheckDB->SyncRooms($roomArray, $deletedRooms));
		//error_log($_POST["id"]." Value: ".$_POST["value"], 0);
?>