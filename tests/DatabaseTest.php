<?php
    require_once("tests/util.php");

    class DatabaseTest extends DatabaseTestCase
    {

        public function testConnectSave()
        {
            $conn = self::$connection;
            $this->assertInstanceOf('TrackMePDO', connect_save($conn));
            $conn["user"] = "invaliduser";
            $this->assertNull(connect_save($conn));
        }

        public function testConnect()
        {
            $conn = self::$connection;
            $this->assertInstanceOf('TrackMePDO', connect($conn));
            $this->expectException('PDOException');
            $conn["user"] = "invaliduser";
            connect($conn);
        }

    }
?>
