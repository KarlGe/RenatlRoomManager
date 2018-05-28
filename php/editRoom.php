<?php
		include("../snippets/phpInit.php");
		$valueType = $_POST["valueType"];
		$roomId = $_POST["id"];
		$value = $_POST["value"];
		$roomCheckDB->SetValue($roomId, $valueType, $value);
		echo($value);
		//error_log($_POST["id"]." Value: ".$_POST["value"], 0);
?>