Room = function(arrayID, dbID, roomNum, poster, roomDepth, roomWidth, roomHeight, greenLock, notes, moveOutDate, offlineRoom, lockType){
	this.arrayID = arrayID;
	this.dbID = dbID;
	this.roomNum = roomNum;
	this.poster = poster;
	this.roomDepth = roomDepth;
	this.roomWidth = roomWidth;
	this.roomHeight = roomHeight;
	this.greenLock = greenLock;
	this.notes = notes;
	this.moveOutDate = moveOutDate;
	this.offlineRoom = offlineRoom;
	this.lockType = lockType;
}
var messageTypes = {"Normal":1, "Error":2};
var Rooms = new Array(0);
var preloadImages = new Array(0);
var lockImgArray;
var deletedRooms = new Array(0);
var offlineRooms = false;


var online = "true";

var roomArrayID;

function AddRoom(room){
	CheckOnline(function(onlineresult){
		if(online && onlineresult){
			$.ajax({
		        type: "POST",
		        url: "../php/addRoom.php",
		        data: {'room': room},
		        success: function(data){
		        	room.dbID = data;
		        	PushRoom(room);
					ShowMessage("Added room "+room.roomNum+" to online storage",messageTypes.Normal);
		    	}
		    });	
		}
		else{
			room.dbID = -1;
			room.offlineRoom = true;
			offlineRooms = true;
			PushRoom(room);
			localStorage.setItem("rooms",JSON.stringify(Rooms));
			ShowMessage("Added room "+room.roomNum+" to local storage",messageTypes.Normal);
		}
	});
}
function PushRoom(room){
	Rooms.unshift(room);
	SetArrayIDs();
}
function DeleteRoom(arrayID){
	//index = findWithAttr(Rooms, "roomID", roomID);
	dbID = Rooms[arrayID].dbID;
	offlineRoom = Rooms[arrayID].offlineRoom;
	console.log(dbID);
	if(arrayID > -1){
		Rooms.splice(arrayID, 1);
		SetArrayIDs();
	}
	if(!offlineRoom){
		CheckOnline(function(onlineresult){
			if(online && onlineresult){
				$.ajax({
			        type: "POST",
			        url: "../php/deleteRoom.php",
			        data: {'roomID': dbID},
			        success: function(data){
			    	}
				});	
			}
			else{
				deletedRooms.push(dbID);
				offlineRooms = true;
				localStorage.setItem("deletedRooms",JSON.stringify(deletedRooms));
			}
		});
	}
	
}
function GetRooms(){
	Rooms = new Array(0);
	$.get('../php/getRooms.php', function(data) {
		var objects = JSON.parse(data);
		for(roomArrayID = 0; roomArrayID < objects.length; roomArrayID++){
			room = new Room(
				roomArrayID, objects[roomArrayID]["id"],objects[roomArrayID]["roomNum"],objects[roomArrayID]["poster"],
				objects[roomArrayID]["depth"],objects[roomArrayID]["width"],objects[roomArrayID]["height"],
				objects[roomArrayID]["greenLock"],objects[roomArrayID]["notes"],objects[roomArrayID]["moveOutDate"],false, objects[roomArrayID]["lockType"]);
			Rooms.push(room);
		}
	});
}
function SyncRooms(){
	localRooms = JSON.parse( localStorage.getItem( 'rooms' ));
	localDeletedRooms = JSON.parse( localStorage.getItem( 'deletedRooms' ));
	$.ajax({
        type: "POST",
        url: "../php/syncRooms.php",
        data: {'rooms': localRooms, 'deletedRooms' : localDeletedRooms},
        success: function(data){
        	GetRooms();
        	console.log(data);
        	offlineRooms = false;
        	localStorage.clear();
            window.location = "?message=synced";
        	//room.dbID = data;
    	}
    });	
	//
}
function SetArrayIDs(){
	for(roomArrayID = 0; roomArrayID < Rooms.length; roomArrayID++){
		Rooms[roomArrayID].arrayID = roomArrayID;
	}
}
function BindEditable(element){
	$(element).editable(function(value, settings) { 
     /* Do your stuff here... */
     	return editableFunction($(this), value);
  	}, 
  	{ 
  		data    : function(string) {return $.trim(string)},
		tooltip    : "Click to edit..."
 	});
}
function BindEditableArea(element){
	$(element).editable(function(value, settings) { 
     /* Do your stuff here... */
     	return editableFunction($(this), value);
  	}, 
  	{ 
  		type 	: 'textarea',
  		submit    : 'OK',
  		data    : function(string) {return $.trim(string)},
		tooltip    : "Click to edit..."
 	});
}
function editableFunction(element, value){
	id = element.attr("id");
	valueType = element.attr("valueType");
	room = GetLocalRoom(id);
	room[valueType] = value;
	console.log(room[valueType]);
	CheckOnline(function(onlineresult){
		if(online && onlineresult){
			$.ajax({
		        type: "POST",
		        url: "../php/editRoom.php",
		        data: {'valueType': valueType, 'id': room.dbID, 'value' : value},
		        success: function(data){
					
		    	}
		    });
		}
		else{
			room.offlineRoom = true;
			offlineRooms = true;
			localStorage.setItem("rooms",JSON.stringify(Rooms));
		}
	});
 	return value;
}
function ToggleValue(value, element){
	if(value == 1){
		value = 0;
	}
	else if(value == 0){
		value = 1;
	}
	var id = $(element).attr("id");
	var room = GetLocalRoom(id);
	var valueType = $(element).attr("valueType");
	if(valueType == 'lockType'){
		value=ToggleLock(value);
		room.lockType = value;
		console.log(value);
	}
	else if(valueType == 'poster'){
		room.poster = value;
	}
	CheckOnline(function(onlineresult){
		if(online && onlineresult){
			EditRoom(valueType, value, room.dbID);
		}
		else{
			room.offlineRoom = true;
			offlineRooms = true;
			localStorage.setItem("rooms",JSON.stringify(Rooms));
		}
	});
}
function EditRoom(valueType, value, dbId){
	$.ajax({
        type: "POST",
        url: "../php/editRoom.php",
        data: {'valueType': valueType, 'id': dbId, 'value' : value},
        success: function(data){
			
    	}
    });	
}
function ToggleLock(value){
	switch (value) {
	    case "normalLocked":
	        return "normalUnlocked"
	        break;
	    case "normalUnlocked":
	        return "normalLocked"
	        break;
        case "overlockLocked":
	        return "overlockUnlocked"
	        break;
        case "overlockUnlocked":
	        return "overlockLocked"
	        break;
        case "unavailableLocked":
	        return "unavailableUnlocked"
	        break;
        case "unavailableUnlocked":
	        return "unavailableLocked"
	        break;
	} 
}
function GetLocalRoom(arrayID){
	return Rooms[arrayID];
}
function findWithAttr(array, attr, value) {
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] === value) {
            return i;
        }
    }
    return null;
}
function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}
function CheckExistingRoom(roomNum, callback){
	CheckOnline(function(onlineresult){
		if(online && onlineresult){
			$.ajax({
		        type: "POST",
		        url: "../php/getRoom.php",
		        data: {'roomNum': roomNum},
		        success: function(data){
		        	if(data == "null"){
		        		callback(null);
		        		return;
		        	}
		        	else{
			        	data = JSON.parse(data);
						newRoom = new Room(
						roomArrayID, data["id"],data["roomNum"],data["poster"],
						data["depth"],data["width"],data["height"],
						data["greenLock"],data["notes"],data["moveOutDate"],false, data["lockType"]);
			    		callback(newRoom);
			    		return;
		        	}
		    	}
		    });	
		}
		else{
			callback(null);
		}
	});
}
function CheckOnline(callback){
	url = "//" + window.location.hostname + "/?rand=" + Math.floor((1 + Math.random()) * 0x10000);
	$.ajax({
        type: "HEAD",
	    timeout: 500, // sets timeout to .5 seconds
	    url: url,
	    error: function(x, t, m){
	    	if(t === "timeout"){
		    	SetOffline();
		    	callback(false);
	    	}
	    },
	    success: function(){
	    	callback(true);
	    },
	    cache: false
	});
}
function SetOffline(){
	online = false;
	$("#syncBox").show();
}
function ShowMessage(message, type){
	if(type == messageTypes.Normal){
		box = $("#messageBox");
	}
	else if(type == messageTypes.Error){
		box = $("#errorBox");
	}
	box.text(message);
	box.effect("slide", { direction: 'down', mode: 'show' }, 500).delay(1500).effect("slide", { direction: 'down', mode: 'hide' }, 500);
}
function SetOnline(callback){
	CheckOnline(function(onlineresult){
		if(onlineresult){
			online = true;
			$("#syncBox").hide();
		}
		else{

			ShowMessage("You are not connected to wifi, please check your wifi and try again",messageTypes.Error);
		}
		callback(onlineresult);
	});
}
function PreloadImages() {
	for (var type in lockImgArray) {
        var img = new Image();
        img.onload = function() {
            
        }
        img.src = lockImgArray[type];
        preloadImages.push({type:type,image:img});
    }
}
function GetUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
$(document).ready(function() {
	if(GetUrlParameter("message") == "synced"){
		ShowMessage("Rooms synchronized successfully", messageTypes.Normal);
	}
	PreloadImages();
	GetRooms();
	rivets.bind($('#roomList'), {rooms: Rooms})
	rivets.formatters.eq = function(value, checkAgainst)
	{
	  return (value == checkAgainst);
	}
	rivets.binders.lockimage = function(el, value){
			$(el).attr("src", lockImgArray[value]);
	}
	rivets.binders.editable = function(el, value) {
  		BindEditable(el);
	}
	rivets.binders.editablearea = function(el, value) {
  		BindEditableArea(el);
	}

	$(document.body).on( "click", ".deleteBtn", function() {
		DeleteRoom($(this).attr("id"));
	});
	$(document.body).on( "click", ".optionsBottomRow img", function() {
		element = $(this).parent().parent();
		if (!element.attr('data-toggled') || element.attr('data-toggled') == 'false'){
			element.animate({
				marginTop: '-0.2em'
			}, 500, function() {
			    $(this).attr('data-toggled','true');
			});			
		}
		else if (element.attr('data-toggled') == 'true'){
			element.animate({
				marginTop: '-2.5em'
			}, 500, function() {
			    $(this).attr('data-toggled','false');
			});			
		}
	});

	$("#syncBox").click(function(){
		SetOnline(function(onlineResult){
			if(onlineResult){
				SyncRooms();
			}	
		});
		
	})
	$('#AddRoomBtn').click(function(){
		roomNum = $('#rooomNumInput').val();
		if(findWithAttr(Rooms, "roomNum", roomNum) == null){
			CheckExistingRoom(roomNum, function(results){
				newRoom = results;
				if(newRoom == null){
					newRoom = new Room(roomArrayID,-1, roomNum, 0, 0, 0, 0, 0, "Click to edit", "0000-00-00", false,"normalUnlocked");
				}
				roomArrayID++;
		    	AddRoom(newRoom);
			});	
		}
	});
	$(document.body).on( "click", ".addOverlock", function() {
		id = $(this).attr("id");
		Rooms[id].lockType = "overlockUnlocked";
		EditRoom("lockType", Rooms[id].lockType, Rooms[id].dbID);
	});
	$(document.body).on( "click", ".removeOverlock", function() {
		id = $(this).attr("id");
		Rooms[id].lockType = "normalUnlocked";
		EditRoom("lockType", Rooms[id].lockType, Rooms[id].dbID);
	});
	$(document.body).on( "click", ".lockImg, .roomPoster", function() {
		ToggleValue($(this).attr("fieldValue"), $(this));
	});
 });

