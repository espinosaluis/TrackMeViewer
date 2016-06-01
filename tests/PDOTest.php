<?php
    require_once("tests/util.php");

    class PDOTest extends DatabasePDOTestCase
    {

        public function testLogin()
        {
            $username = "track";
            $password = "password";
            $this->assertGreaterThanOrEqual(0, $this->db->valid_login($username, $password));
            $this->assertEquals(NO_USER, $this->db->valid_login("invalid$username", $password));
            $this->assertEquals(INVALID_CREDENTIALS, $this->db->valid_login($username, "invalid$password"));
        }

        public function testCount()
        {
            $this->assertGreaterThanOrEqual(0, $this->db->get_count("trips"));
        }

        public function testAccountCreation()
        {
            $username = "track2";
            $password = "password";
            $this->db->exec_sql("DELETE FROM users WHERE username=?", $username);
            $this->assertEquals(NO_USER, $this->db->valid_login($username, $password));
            $uid = $this->db->create_login($username, $password);
            $this->assertGreaterThanOrEqual(0, $uid);
            $this->assertEquals($uid, $this->db->valid_login($username, $password));
            $this->db->exec_sql("DELETE FROM users WHERE username=?", $username);
        }

    }
?>
