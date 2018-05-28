<?php

	function addRoom($conn,$roomNum, $poster, $depth, $width, $height, $greenLock, $notes, $moveOutDate){
		$moveOutDate = date('Y-m-d', strtotime($moveOutDate));
		$sql = "INSERT INTO romsjekk (roomNum,poster,depth,width,height,greenLock,notes,moveOutDate) 
				VALUES (:roomNum,:poster,:debth,:width,:height,:greenLock,:notes,:moveOutDate)";
		$q = $conn->prepare($sql);

		try {
			$q->execute(array(
				':roomNum'=>$roomNum,
				':poster'=>$poster,
				':debth'=>$depth,
				':width'=>$width,
				':height'=>$height,
				':greenLock'=>$greenLock,
				':notes'=>$notes,
	          	':moveOutDate'=>$moveOutDate));
		} catch(PDOException $ex) {
		    echo "An Error occured!"; //user friendly message
		    print_r($q->errorInfo());
		}
	}
	function editRoom($conn,$roomNum, $poster, $depth, $width, $height, $greenLock, $notes, $moveOutDate){
		// query
		$sql = "UPDATE romsjekk 
		        SET roomNum=?, poster=?, depth=?, width=?, height=?, greenLock=?, notes=?, moveOutDate=?
		        WHERE roomNum=?";
		$q = $conn->prepare($sql);
		try {
			$q->execute(array($roomNum,$poster,$depth,$width,$height,$greenLock,$notes,$moveOutDate,$roomNum));
		} catch(PDOException $ex) {
		    echo "An Error occured!"; //user friendly message
		    print_r($q->errorInfo());
		}
	}
?>