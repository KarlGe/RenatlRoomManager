
<link rel="stylesheet" href="css/testStyles.css">
<?php
    set_include_path(implode(PATH_SEPARATOR, array(get_include_path(), '../classes')));
    spl_autoload_register();
    include("TestFramework.php");

class DBTest extends Testable{

    public static $roomCheckDB;
    
    public function __construct()
    {
        $config = parse_ini_file('config.ini');
        self::$roomCheckDB = new RoomCheckDB($config);
    }

    public function TestDBGetRoom(){
        $room = new Room;
        $roomNote = "Dette er et testrom";
        $room->notes = $roomNote;
        $roomId = self::$roomCheckDB->AddRoom($room);
        $room = self::$roomCheckDB->GetRoomById($roomId);
        Assert::AreEqual($room->notes, $roomNote);
    }
    public function TestDBAddRoom(){
        $room = new Room();
        $roomId = self::$roomCheckDB->AddRoom($room);
        Assert::IsANumber($roomId);
    }
    public function TestDBDeleteRoom(){
        $room = new Room();
        $room->roomNum = rand(1000, 9999);
        $roomId = self::$roomCheckDB->AddRoom($room);
        Assert::IsANumber(self::$roomCheckDB->DeleteRoom($roomId));
    }
}


$dbTests = new DBTest();
$dbTests->RunTests();

/**
 * a simple Test suite with two tests
 **/
class MyTest extends Testable
{
    /**
     * This test is designed to fail
     **/
    public function TestOne()
    {
        Assert::AreEqual( 1, 2 );
    }

    /**
     * This test is designed to succeed
     **/
    public function TestTwo()
    {
        Assert::AreEqual( 1, 1 );
    }

    public function TestRoom(){
        $room = new Room();
        Assert::AreEqual($room->roomNum, null);
    }
}



// this is how to use it.
//$test = new MyTest();
//$test->RunTests();

?>