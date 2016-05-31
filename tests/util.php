<?php
    require_once("config.php");
    require_once("database.php");

    abstract class TestCase extends PHPUnit_Framework_TestCase
    {

        public function expectException($exception)
        {
            if (method_exists('PHPUnit_Framework_TestCase', "expectException"))
                return parent::expectException($exception);
            else
                $this->markTestIncomplete("expectException is not available");
        }

    }

    abstract class DatabaseTestCase extends TestCase
    {

        protected static $connection;

        public static function setUpBeforeClass()
        {
            global $DBIP, $DBNAME, $DBUSER, $DBPASS;
            self::$connection = toConnectionArray($DBIP, $DBNAME, $DBUSER, $DBPASS);
        }

    }

    abstract class DatabasePDOTestCase extends DatabaseTestCase
    {

        public function setUp()
        {
            $this->db = connect(self::$connection);
        }

    }

?>
