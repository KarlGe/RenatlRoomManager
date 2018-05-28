<?php
		include("../snippets/phpInit.php");
		$roomArray = $_POST["room"];
		$room = new Room(null,$roomArray["roomNum"], $roomArray["poster"], $roomArray["roomDepth"], $roomArray["roomWidth"], $roomArray["roomHeight"], $roomArray["lockType"], $roomArray["notes"], $roomArray["moveOutDate"]);
		echo ($roomCheckDB->AddRoom($room));
		//error_log($_POST["id"]." Value: ".$_POST["value"], 0);
?>