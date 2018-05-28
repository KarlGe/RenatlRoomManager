<?php
		include("../snippets/phpInit.php");
		$roomArray = $roomCheckDB->GetRooms(100,0);
		echo(json_encode($roomArray));
		//error_log($_POST["id"]." Value: ".$_POST["value"], 0);
?>