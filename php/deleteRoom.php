<?php
		include("../snippets/phpInit.php");
		$roomId = $_POST["roomID"];
		$roomCheckDB->DeleteRoom($roomId);
?>