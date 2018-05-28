<?php 
	include("../snippets/Header.php");
?>
<div id="addRoomBox">
	Room number
	<input type="number" id="rooomNumInput" name="rooomNum">
	<button id="AddRoomBtn">Add</button>
</div>
<div id="roomList">
    <div class="room" rv-each-room="rooms">
    	<div class="roomHeader">
    		<div valueType="roomNum" rv-id="room.arrayID" rv-editable class="roomNum">
    			{room.roomNum}
    		</div>

    		<div class="roomDimensions">
    			<p>D: <span valueType="roomDepth" rv-id="room.arrayID" rv-editable>{room.roomDepth}</span>m</p>
				<p>W: <span valueType="roomWidth" rv-id="room.arrayID" rv-editable>{room.roomWidth}</span>m</p>
				<p>H: <span valueType="roomHeight" rv-id="room.arrayID" rv-editable>{room.roomHeight}</span>m</p>
			</div>
			<div class="deleteBtnWrapper"><img rv-id="room.arrayID" class="deleteBtn" src="../img/cross.png"></div>
		</div>
		<div  class="roomBody">
			<div id="roomOptions">
				<div class="optionsTopRow">
					<button class="addOverlock" rv-id="room.arrayID">Add overlock</button>
					<button class="removeOverlock" rv-id="room.arrayID">Remove overlock</button>
				</div>
				<div class="optionsBottomRow">
					<img src="../img/gearIcon.png">
				</div>
			</div>
			<div class="roomMainbody">
				<div class="roomLock">
					<!--<img rv-show="room.greenLock | eq 0" class="lockImg" valueType="greenlock" fieldValue="0" rv-id="room.arrayID" src="../img/locks/lockUnlocked.png">
					<img rv-show="room.greenLock | eq 1" class="lockImg" valueType="greenlock" fieldValue="1" rv-id="room.arrayID" src="../img/locks/lockLocked.png">-->
					<img rv-lockimage="room.lockType" class="lockImg" valueType="lockType" rv-fieldValue="room.lockType" rv-id="room.arrayID" src="">
				</div>				
				<div valueType="notes" rv-id="room.arrayID" class="roomNotes" rv-editablearea>
					{room.notes}
				</div>
			</div>
		</div>
		<div class="roomFooter">
			<div rv-id="room.arrayID" class="roomMoveout">
				Moveout: <span rv-id="room.arrayID" valueType="moveOutDate" rv-editable>{room.moveOutDate}</span>
			</div>
			<div rv-show="room.poster | eq 1" rv-id="room.arrayID" class="roomPoster" valueType="poster" fieldValue="1" class="roomPoster">
				Poster: Yes
			</div>
			<div rv-show="room.poster | eq 0" rv-id="room.arrayID" class="roomPoster" valueType="poster" fieldValue="0" class="roomPoster">
				Poster: No
			</div>
		</div>
    </div>
</div>

<?php
	include("../snippets/Footer.php");
?>

<!--
echo('<div class="roomBody">');
echo('</div>');
-->
