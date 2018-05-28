<?php
	function dbConnect(){
		static $conn;
		if(!isset($conn)){
			$config = parse_ini_file(__DIR__.'/../config.ini'); 
			$servername = $config["servername"];
			$user = $config["user"];
			$pass = $config["pass"];
			$dbName = $config["dbName"];
			$conn = new PDO('mysql:host='.$servername.';dbname='.$dbName.';charset=utf8', $user, $pass);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		}
		if (!$conn) {
		    die('Could not connect: ' . mysql_error());
		}
		return $conn;
	}
?>
