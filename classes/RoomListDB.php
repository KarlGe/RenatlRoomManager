<?php
	require_once '../snippets/phpInit.php';
	Class RoomListDB{
		public static $dbConn;
		public function __construct() {
			if(!isset(self::$dbconn)){
				$dbHandler = new DbHandler();
				self::$dbConn = $dbHandler->GetDBConn();
			}
		}
		function AddRoom(Room $room){
			$moveOutDate = date('Y-m-d', strtotime($room->moveOutDate));
			$sql = "INSERT INTO rooms (roomNum,poster,depth,width,height,measuredDate,sizeCode,price,available,roomClass,moveOutDate) 
					VALUES (:roomNum,:poster,:depth,:width,:height,:measuredDate,:sizeCode,:price,:available,:roomClass,:moveOutDate)";
			$q = self::$dbConn->prepare($sql);

			try {
				$q->execute(array(
					':roomNum'=>$room->roomNum,
					':poster'=>$room->poster,
					':depth'=>$room->depth,
					':width'=>$room->width,
					':height'=>$room->height,
					':roomClass'=>$room->roomClass,
					':measuredDate'=>$room->measuredDate,
					':sizeCode'=>$room->sizeCode,
					':price'=>$room->price,
					':available'=>$room->available,
		          	':moveOutDate'=>$moveOutDate));
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
			return self::$dbConn->lastInsertId();
		}
		function NewRoom(Room $room){

		}
		function DeleteRoom($roomId){
			// query
			$sql = "DELETE FROM rooms 
			        WHERE ID = :roomId";
			$q = self::$dbConn->prepare($sql);
			try {
				$q->execute(array(':roomId'=>$roomId));
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
		}
		function EditRoom(Room $room){
			// query
			$sql = "UPDATE rooms
					SET roomNum = :roomNum, poster = :poster, depth = :depth, 
					width = :width, height = :height, measuredDate = :measuredDate,
					moveOutDate = :moveOutDate, sizeCode = :sizeCode, 
					price = :price, available = :available, roomClass = :roomClass
					WHERE ID = :roomId";
			$q = self::$dbConn->prepare($sql);
			try{
				$q->execute(array(
					':roomNum'=>$room->roomNum,':poster'=>$room->poster,':depth'=>$room->depth,
					':width'=>$room->width,':height'=>$room->height,':measuredDate'=>$room->measuredDate,
					':moveOutDate'=>$room->moveOutDate,':roomId'=>$room->id,':sizeCode'=>$room->sizeCode,
					':price'=>$room->price,':available'=>$room->available,':roomClass'=>$room->roomClass));
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
		}
		function SetValue($roomId, $valueType, $value){
			$roomNum = $this->GetRoomNum($roomId);
			if($roomNum != null){
				$column = "";
				switch($valueType)
				{
				    case "roomNum":
				        $column = 'roomNum';
				        break;
			        case "roomDepth":
				        $column = 'depth';
				        break;
					case "roomWidth":
				        $column = 'width';
				        break;
				    case "roomHeight":
				        $column = 'height';
				        break;
			        case "moveOutDate":
				        $column = 'moveOutDate';
				        break;
					case "poster":
				        $column = 'poster';
				        break;				        
				}
				if($column != ""){
					$sql = "UPDATE rooms
							SET {$column} = :value
							WHERE roomNum = :roomNum";
					$q = self::$dbConn->prepare($sql);
					try{
						$q->execute(array(':value'=>$value,':roomNum'=>$roomNum));
					} catch(PDOException $ex) {
					    echo "An Error occured!"; //user friendly message
					    print_r($q->errorInfo());
					}	
				}
			}	
		}
		function GetRoomNum($roomID){
			$sth = self::$dbConn->prepare('SELECT roomNum FROM romsjekk WHERE ID = :roomID LIMIT 1');
			try{
				$sth->execute(array(':roomID'=>$roomID));
				if ($sth->rowCount() > 0) {
					$row = $sth->fetch();
					return $row["roomNum"];
				}
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
			return null;
		}
		function GetRooms($numRows, $offset){
			$roomArray = array();
			$sth = self::$dbConn->prepare('SELECT * FROM rooms LIMIT :numRows OFFSET :offset');
			$sth->bindParam(":numRows", $numRows, PDO::PARAM_INT);
			$sth->bindParam(":offset", $offset, PDO::PARAM_INT);
			try {
				$sth->execute(array(':roomNum'=>$dbRoom->roomNum));
				if ($sth->rowCount() > 0) {
					$rows = $sth->fetchAll();  
					foreach ( $rows as $row )  
					{
						$room = new Room(
							$row["id"], $row["roomNum"], $row["poster"], 
							$row["depth"], $row["width"], $row["height"], 
							null, null, $row["moveOutDate"],
							$row["sizeCode"],$row["price"],$row["available"],$row["roomClass"],$row["measuredDate"]);
						array_push($roomArray, $room);
					}
				}
			} catch(PDOException $ex) {
			    echo "An Error occured!"; //user friendly message
			    print_r($q->errorInfo());
			}
			return $roomArray;
		}
		function GetRoom($roomNum){
			$sth = self::$dbConn->prepare('SELECT * FROM rooms WHERE roomNum = :roomNum LIMIT 1');
			try{
				$sth->execute(array(':roomNum'=>$roomNum));
				if ($sth->rowCount() > 0) {
					$row = $sth->fetch();
					$room = new Room(
							$row["id"], $row["roomNum"], $row["poster"], 
							$row["depth"], $row["width"], $row["height"], 
							null, null, $row["moveOutDate"],
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
			$dbRoom = $this->GetRoom($room->roomNum);
			if($dbRoom == null)
			{
				$this->AddRoom($room);
				return $room;
			}
			else{
				if($this->SetDBDimensions($room, $dbRoom)){
					$dbRoom->depth = $room->depth;
					$dbRoom->width = $room->width;
					$dbRoom->height = $room->height;
					$dbRoom->mesauredDate = date('Y-m-d');
				} 
				if($room->poster != null && $dbRoom->poster != $room->poster){
					$dbRoom->poster = $room->poster;
				}
				if($room->sizeCode != null && $dbRoom->sizeCode != $room->sizeCode){
					$dbRoom->sizeCode = $room->sizeCode;
				}
				if($room->price != null && $dbRoom->price != $room->price){
					$dbRoom->price = $room->price;
				}
				if($room->available != null && $dbRoom->available != $room->available){
					$dbRoom->available = $room->available;
				}
				if($room->roomClass != null && $dbRoom->roomClass != $room->roomClass){
					$dbRoom->roomClass = $room->roomClass;
				}
				if($room->moveOutDate != null && $dbRoom->moveOutDate != $room->moveOutDate){
					$dbRoom->moveOutDate = $room->moveOutDate;
				}
				$this->EditRoom($dbRoom);
			}
			return $dbRoom;
		}
		function SyncRooms($roomArray){
			foreach ($roomArray as $room) {
				$room = $this->SyncRoom($room);
			}
			return $roomArray;
		}	
		function SetDBDimensions($room, $dbRoom){
			if($room->depth == $dbRoom->depth && $room->width == $dbRoom->width && $room->height == $dbRoom->height)
				return false;
			else if(
				($room->depth != 0 && $room->depth != null) ||
				($room->width != 0 && $room->width != null) ||
				($room->height != 0 && $room->height != null)){
				return true;
			}
			return false;
		}	
	}
	
?>