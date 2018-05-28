<?php
class Room
{
	public $id;
	public $roomNum; 
	public $poster;
	public $depth;
	public $width;
	public $height;
	public $measuredDate;
	public $lockType;
	public $notes;
	public $moveOutDate;
	public $offlineRoom;
	public $sizeCode;
	public $price;
	public $available;
	public $roomClass;

	public function __construct(
		$id = null, $roomNum = 0000,$poster = 0,$depth = null,
		$width = null,$height = null,$lockType = null,$notes = "",
		$moveOutDate = null,$sizeCode = null,$price = null,$available = null,
		$roomClass = null, $measuredDate =null){
		$this->id = $id;
		$this->roomNum = $roomNum; 
		$this->poster = $poster;
		$this->depth = $depth;
		$this->width = $width;
		$this->height = $height;
		$this->lockType = $lockType;
		$this->notes = $notes;
		$this->moveOutDate = $moveOutDate;
		$this->sizeCode = $sizeCode;
		$this->price = $price;
		$this->available = $available;
		$this->roomClass = $roomClass;
		$this->measuredDate = $measuredDate;
		$offlineRoom = false;
	}
}
?>
