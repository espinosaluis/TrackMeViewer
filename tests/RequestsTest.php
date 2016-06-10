<?php
    require_once("tests/util.php");

    class RequestsTest extends DatabaseTestCase
    {

        public static function setUpBeforeClass()
        {
            self::defineGET();
            parent::setUpBeforeClass();
            $__norun = true;
            require_once("requests.php");
            unset($__norun);
        }

        private static function defineGET()
        {
            $_GET["db"] = 42;
            $_GET["u"] = "track";
            $_GET["p"] = "password";
            $_GET["tn"] = "Hi's Bars";
            $_GET["a"] = "noop";
        }

        private static function runRequests() {
            return run(self::$connection);
        }

        private function assertResult($result, $code=0, $values=0) {
            $parts = explode("|", $result);
            $this->assertEquals("Result:$code", $parts[0]);
            if ($values === true) {
                $this->assertGreaterThan(1, count($parts));
            } else {
                $this->assertEquals($values + 1, count($parts));
            }
        }

        public function setUp()
        {
            self::defineGET();
        }

        public function testWrongDB()
        {
            $_GET["db"] = 0;
            $this->assertEquals("Result:5", self::runRequests());
        }

        public function testNoUser()
        {
            $_GET["u"] = "";
            $this->assertEquals("Result:3", self::runRequests());
        }

        public function testNoPassword()
        {
            $_GET["p"] = "";
            $this->assertEquals("Result:3", self::runRequests());
        }

        public function testInvalid()
        {
            $_GET["p"] = "invalid$_GET[p]";
            $this->assertEquals("Result:1", self::runRequests());
        }

        public function testNew()
        {
            $_GET["u"] = "track2";
            $db = connect(self::$connection);
            $db->exec_sql("DELETE FROM users WHERE username='track2'");
            $before = $db->exec_sql("SELECT username FROM users")->fetchAll(PDO::FETCH_COLUMN, 0);
            $this->assertEquals("Result:0", self::runRequests());
            $after = $db->exec_sql("SELECT username FROM users")->fetchAll(PDO::FETCH_COLUMN, 0);
            $before[] = "track2";
            $this->assertEquals(sort($after), sort($before));
            $db = null;
        }

        public function testDisabled() {
            $_GET["u"] = "Disabled";
            $this->assertEquals("User disabled. Please contact system administrator", self::runRequests());
        }

        public function testNoop()
        {
            $this->assertEquals("Result:0", self::runRequests());
        }

        public function testGetIconList()
        {
            $_GET["a"] = "geticonlist";
            $this->assertResult(self::runRequests(), 0, true);
        }

    }
?>
