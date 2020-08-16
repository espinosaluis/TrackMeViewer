<?php
	//////////////////////////////////////////////////////////////////////////////
	//
	// TrackMeViewer - Browser/MySQL/PHP based Application to display trips recorded by TrackMe App on Android
	// Version: 3.5
	// Date:    08/15/2020
	//
	// For more information go to:
	// http://forum.xda-developers.com/showthread.php?t=340667
	//
	// Please feel free to modify the files to meet your needs.
	// Post comments and questions to the forum thread above.
	//
	//////////////////////////////////////////////////////////////////////////////

	require_once("config.php");

	define('NO_USER', -1);
	define('LOCKED_USER', -2);
	define('INVALID_CREDENTIALS', -3);

	function toConnectionArray($DBIP, $DBNAME, $DBUSER, $DBPASS) {
		return array('host' => $DBIP, 'name' => $DBNAME, 'user' => $DBUSER, 'pass' => $DBPASS);
	}

	function connect($connection = false) {
		if ($connection === false) {
			global $DBIP, $DBNAME, $DBUSER, $DBPASS;
			$connection = toConnectionArray($DBIP, $DBNAME, $DBUSER, $DBPASS);
		}
		return new TrackMePDO("mysql:host=" . $connection['host'] . ";dbname=" . $connection['name'], $connection['user'], $connection['pass']);
	}

	function connect_save($connection=false) {
		try {
			return connect($connection);
		} catch (PDOException $e) {
			return null;
		}
	}

	class TrackMePDO extends PDO {
		function get_count($tablename) {
			$result = $this->exec_sql("SELECT COUNT(*) FROM " . $tablename);
			if ($result === false) {
				return 0;
			} else {
				$row = $result->fetchAll();
				return $row[0][0];
			}
		}

		function exec_sql() {
			$args = func_get_args();
			$statement = $args[0];
			if (count($args) == 1)
				$args = array();
			elseif (is_array($args[1]))
				$args = $args[1];
			else
				$args = array_slice($args, 1);
			$stmt = $this->prepare($statement);
			for ($i = 0; $i < count($args); $i++)
				$stmt->bindParam($i + 1, $args[$i]);
			if ($stmt->execute())
				return $stmt;
			else
				return false;
		}

		private function static_hash($password) {
			$salt = "trackmeuser";
			return MD5($salt.$password);
		}

		private function verify_password($password, $hash) {
			return $hash === $this->static_hash($password);
		}

		function create_login($username, $password) {
			$hash = $this->static_hash($password);
			$this->exec_sql("INSERT INTO users (username, password) VALUES (?, ?)", $username, $hash);
			return $this->valid_login($username, $password);
		}

		function valid_login($username, $password, $allow_disabled=false) {
			$user = $this->exec_sql("SELECT ID, password, Enabled FROM users WHERE username=?", $username)->fetch();
			if ($user === false) {
				return NO_USER;
			} elseif ($this->verify_password($password, $user['password'])) {
				if (!$allow_disabled && $user['Enabled'] == 0) {
					return LOCKED_USER;
				} else {
					return $user['ID'];
				}
			} else {
				return INVALID_CREDENTIALS;
			}
		}
	}

?>
