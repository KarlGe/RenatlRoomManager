<?php
	Class DbHandler{
		static $conn;
		function GetDBConn($config = null){
			if(!isset(self::$conn)){
				if($config == null){
					$config = parse_ini_file(__DIR__.'/../config.ini'); 
				}
				$servername = $config["servername"];
				$user = $config["user"];
				$pass = $config["pass"];
				$dbName = $config["dbName"];
				self::$conn = new PDO('mysql:host='.$servername.';dbname='.$dbName.';charset=utf8', $user, $pass);
				self::$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			}
			if (!self::$conn) {
			    die('Could not connect: ' . mysql_error());
			}
			return self::$conn;
		}
	}
?>