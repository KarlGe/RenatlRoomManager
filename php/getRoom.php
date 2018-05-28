<?php
		include("../snippets/phpInit.php");
		$roomNum = $_POST["roomNum"];
		$room = $roomCheckDB->GetRoomByRoomNum($roomNum);
		echo(json_encode($room));
		//error_log($_POST["id"]." Value: ".$_POST["value"], 0);
?>