<?php
    set_include_path(implode(PATH_SEPARATOR, array(get_include_path(), '../classes')));
    spl_autoload_register();
	Class RoomCheckDB{
		public static $dbConn;
		public function __construct($config = null) {
			if(!isset(self::$dbconn)){
				$this->SetDBConn($config);
			}
		}
		function SetDBConn($config){
				$dbHandler = new DbHandler();
				self::$dbConn = $dbHandler->GetDBConn($config);
		}
		function AddRoom(Room $room){
			$checkRoom = $this->GetRoomByRoomNum($room->roomNum);
			if($checkRoom != null){
				$room->id = $checkRoom->id;
				$this->SetValue($checkRoom->id, 'roomCheckActive', 1);
				return $checkRoom->id;
			}
			else{
				$moveOutDate = date('Y-m-d', strtotime($room->moveOutDate));
				//$measuredDate = dateNow
				$sql = "INSERT INTO romsjekk (roomNum,poster,depth,width,height,lockType,notes,moveOutDate,roomCheckActive) 
						VALUES (:roomNum,:poster,:debth,:width,:height,:lockType,:notes,:moveOutDate,1)";
				
				$q = self::$dbConn->prepare($sql);
				try {
					$q->execute(array(
						':roomNum'=>$room->roomNum,
						':poster'=>$room->poster,
						':debth'=>$room->depth,
						':width'=>$room->width,
						':height'=>$room->height,
						':lockType'=>$this->GetLockID($room->lockType),
						':notes'=>$room->notes,
			          	':moveOutDate'=>$moveOutDate));
				} catch(PDOException $ex) {
				    echo "An Error occured!"; //user friendly message
				    print_r($q->errorInfo());
				}
				return self::$dbConn->lastInsertId();	
			}
		}
		function DeleteRoom($roomId){
			if(self::GetRoomById($roomId)){
				// query
				$sql = "UPDATE romsjekk 
				        SET roomCheckActive=0, locktype=2
				        WHERE ID=:roomId";
				$q = self::$dbConn->prepare($sql);
				try {
					$q->execute(array(':roomId'=>$roomId));
				} catch(PDOException $ex) {
				    return "Error when trying to remove room #".$roomId; //user friendly message
				    print_r($q->errorInfo());
				}
				return $roomId;	
			}
			return "Room #".$roomId." does not exist.";
		}
		function EditRoom(Room $room){
			// query
			$sql = "UPDATE romsjekk 
			        SET roomNum=?, poster=?, depth=?, width=?, height=?, greenLock=?, notes=?, moveOutDate=?
			        WHERE roomNum=?";
			$q = self::$dbConn->prepare($sql);
			try {
				$q->execute(array($room->roomNum,$room->poster,$room->depth,$room->width,$room->height,$room->greenLock,$room->notes,$room->moveOutDate,$room->roomNum));
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
		}
		function SetGreenLock($roomNum, $greenLock){
			$sql = "UPDATE romsjekk
					SET greenLock = :greenLock
					WHERE roomNum = :roomNum";
			$q = self::$dbConn->prepare($sql);
			try{
				$q->execute(array(':greenLock'=>$greenLock,':roomNum'=>$roomNum));
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
		}
		function SetValue($roomId, $valueType, $value){
			switch($valueType)
			{
			    case "roomNum":
			        $column = 'roomNum';
			        break;
		        case "roomDepth":
			        $column = 'depth';
			        $value = str_replace(",",".",$value);
			        break;
				case "roomWidth":
			        $column = 'width';
			        $value = str_replace(",",".",$value);
			        break;
			    case "roomHeight":
			        $column = 'height';
			        $value = str_replace(",",".",$value);
			        break;	
			    case "notes":
			        $column = 'notes';
			        break;
		        case "moveOutDate":
			        $column = 'moveOutDate';
			        break;	
	        	case "lockType":
			        $column = 'lockType';
			        $value = $this->GetLockID($value);
			        echo($value." ".$roomId);
			        break;	
				case "poster":
			        $column = 'poster';
			        break;		
		        case "roomCheckActive":
		        	$column = 'roomCheckActive';
		        	break;      
			}

			$sql = "UPDATE romsjekk SET {$column} = :value";
			if($column == 'height' || $column == 'width' || $column == 'depth'){
				$sql += "measuredDate = ".date("Y-m-d");
			}
			$sql += "WHERE ID = :roomId";
			//Sjekk om dette funker.
			$q = self::$dbConn->prepare($sql);
			try{
				$q->execute(array(':value'=>$value,':roomId'=>$roomId));
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
		}
		function GetRooms($numRows, $offset){
    		$roomListDB = new RoomListDB();
			$sth = self::$dbConn->prepare('SELECT romsjekk.id, roomNum, poster, depth, width, height, notes, moveOutDate, romsjekk.lockType, locks.lockType as lockType
									FROM romsjekk
									LEFT JOIN locks ON romsjekk.lockType = locks.ID
									WHERE roomCheckActive = 1
									ORDER BY roomNum ASC 
									LIMIT :numRows OFFSET :offset');
			$sth->bindParam(":numRows", $numRows, PDO::PARAM_INT);
			$sth->bindParam(":offset", $offset, PDO::PARAM_INT);
			try {
				$sth->execute();
				$rows = $sth->fetchAll();  
				$roomArray = array();
				foreach ( $rows as $row )  
				{
					$room = new Room(
					$row["id"], $row["roomNum"], $row["poster"], 
					$row["depth"], $row["width"], $row["height"], 
					$row["lockType"], $row["notes"], $row["moveOutDate"]);
					array_push($roomArray, $room);	
				}
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
			return $roomArray;
		}
		function GetRoomByRoomNum($roomNum){
			$sth = self::$dbConn->prepare('SELECT * FROM romsjekk WHERE roomNum = :roomNum LIMIT 1');
			try{
				$sth->execute(array(':roomNum'=>$roomNum));
				if ($sth->rowCount() > 0) {
					$row = $sth->fetch();
					$room = new Room(
							$row["ID"], $row["roomNum"], $row["poster"], 
							$row["depth"], $row["width"], $row["height"], 
							$this->GetLockType($row["lockType"]), $row["notes"], $row["moveOutDate"],
							$row["sizeCode"],$row["price"],$row["available"],$row["roomClass"],$row["measuredDate"]);
					return $room;
				}
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
			return null;
		}
		function GetRoomById($id){
			$sth = self::$dbConn->prepare('SELECT * FROM romsjekk WHERE ID = :id LIMIT 1');
			try{
				$sth->execute(array(':id'=>$id));
				if ($sth->rowCount() > 0) {
					$row = $sth->fetch();
					$room = new Room(
							$row["ID"], $row["roomNum"], $row["poster"], 
							$row["depth"], $row["width"], $row["height"], 
							$this->GetLockType($row["lockType"]), $row["notes"], $row["moveOutDate"],
							$row["sizeCode"],$row["price"],$row["available"],$row["roomClass"],$row["measuredDate"]);
					return $room;
				}
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
			return null;
		}
		function SyncRoom($room)
		{
			$checkRoom = $this->GetRoomByRoomNum($room->roomNum);
			if($checkRoom != null){
				$room->lockType = $this->GetLockID($room->lockType);
				if($checkRoom->width != 0){
					$room->width = $checkRoom->width;
					$room->height = $checkRoom->height;
					$room->depth = $checkRoom->depth;
					$room->measuredDate = date("Y-m-d");
				}
				if($room->notes != $checkRoom->notes && $room->notes != "Click to edit"){
					$room->notes = $room->notes . " " . $checkRoom->notes;
				}
				if($room->poster == 0){
					$room->poster = $checkRoom->poster;
				}
				print($room->poster);
				$room->id = $checkRoom->id;
				$sql = "UPDATE romsjekk
						SET roomNum = :roomNum, poster = :poster, depth = :depth, 
						width = :width, height = :height, measuredDate = :measuredDate lockType = :lockType,
						notes = :notes, moveOutDate = :moveOutDate, roomCheckActive = :roomCheckActive
						WHERE ID = :roomId";
				$q = self::$dbConn->prepare($sql);
				try{
					$q->execute(array(
						':roomNum'=>$room->roomNum,':poster'=>$room->poster,':depth'=>$room->depth,
						':width'=>$room->width,':height'=>$room->height, ':measuredDate'=>$room->measuredDate, ':lockType'=>$room->lockType,
						':notes'=>$room->notes,':moveOutDate'=>$room->moveOutDate,':roomCheckActive'=> 1,':roomId'=>$room->id));
				} catch(PDOException $ex) {
				    echo "An Error occured!"; //user friendly message
				    print_r($q->errorInfo());
				}
			}
			else{
				$this->AddRoom($room);
			}
		}
		function SyncRooms($roomArray, $deletedRooms){
			if($deletedRooms != null){
				foreach ($deletedRooms as $deletedRoom) {
					$this->DeleteRoom($deletedRoom);
				}	
			}
			if($roomArray != null){
				foreach ($roomArray as $room) {
					$this->SyncRoom($room);
				}
			}
		}
		function GetLockID($lockType){
			$sth = self::$dbConn->prepare('SELECT ID FROM locks WHERE lockType = :lockType LIMIT 1');
			try{
				$sth->execute(array(':lockType'=>$lockType));
				if ($sth->rowCount() > 0) {
					$row = $sth->fetch();
					return $row["ID"];
				}
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
			return 1;
		}
		function GetLockType($lockID){
			$sth = self::$dbConn->prepare('SELECT lockType FROM locks WHERE ID = :lockID LIMIT 1');
			try{
				$sth->execute(array(':lockID'=>$lockID));
				if ($sth->rowCount() > 0) {
					$row = $sth->fetch();
					return $row["lockType"];
				}
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
			return 1;
		}
		function GetLockImgArray(){
			$path = "../";
			$sth = self::$dbConn->prepare('SELECT * FROM locks');
			try {
				$sth->execute();
				$rows = $sth->fetchAll();  
				$lockArray = array();
				$obj = new stdClass;

				foreach ( $rows as $row )  
				{
					print_r($row["lockType"]);
					echo $path.$row["lockSrc"];
					$obj->$row["lockType"] = $path.$row["lockSrc"];
				}
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
			return $obj;
		}
	}
	
?>