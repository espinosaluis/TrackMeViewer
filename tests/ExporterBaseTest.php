<?php
    require_once("tests/util.php");

    class ExporterBaseTest extends DatabasePDOTestCase
    {

        const USERNAME = "track";
        const UID = 3;
        const TRIPNAME = "Hi's Bars";
        const TID = 15;

        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            require_once("exporter/base.php");
        }

        public function assertExporter($expectedTID, $expectedName, $exporter)
        {
            $this->assertEquals(self::UID, $exporter->userid);
            $this->assertEquals(self::USERNAME, $exporter->username);
            $this->assertEquals($expectedTID, $exporter->tripid);
            $this->assertEquals($expectedName, $exporter->tripname);
        }

        public function testNormalizeNoArgs()
        {
            $this->expectException('InvalidArgumentException');
            Exporter::normalize($this->db, self::UID, array());
        }

        public function testNormalizeWrongType()
        {
            $this->expectException('InvalidArgumentException');
            Exporter::normalize($this->db, self::UID, array("t" => "text"));
        }

        public function testNormalizeBoth()
        {
            $this->expectException('InvalidArgumentException');
            Exporter::normalize($this->db, self::UID, array("t" => "15", "tn" => self::TRIPNAME));
        }

        public function testNormalizeValid()
        {
            $this->assertEquals(15, Exporter::normalize($this->db, self::UID, array("t" => "15")));
            $this->assertEquals(15, Exporter::normalize($this->db, self::UID, array("tn" => self::TRIPNAME)));
            $this->assertTrue(Exporter::normalize($this->db, self::UID, array("t" => "a")));
            $this->assertTrue(Exporter::normalize($this->db, self::UID, array("tn" => "")));
            $this->assertNull(Exporter::normalize($this->db, self::UID, array("t" => "n")));
            $this->assertNull(Exporter::normalize($this->db, self::UID, array("tn" => "<None>")));
        }

        public function testConstructorValid()
        {
            $this->assertExporter(self::TID, self::TRIPNAME, new Exporter($this->db, self::UID, self::TID, "", ""));
            $this->assertExporter(true, "All", new Exporter($this->db, self::UID, true, "", ""));
            $this->assertExporter(null, "None", new Exporter($this->db, self::UID, null, "", ""));
        }

    }
?>
