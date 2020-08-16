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
	require_once("database.php");

	function run($connection) {
		$requireddb = $_GET["db"];
		if ($requireddb == "" || $requireddb < 8) {
			return "Result:5";
		}

		$db = connect_save();
		if (is_null($db)) {
			return "Result:4";
		}

		$username = $_GET["u"];
		$password = $_GET["p"];

		$userid = $db->valid_login($username, $password);
		if ($userid < 0) {
			return "Result:1";
		}

		$action = $_GET["a"];

		if ($action == "kml") {
			if (!file_exists("routes"))
				mkdir("routes");

			$myfile = "routes/".$username.".kml";

			if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], "./$myfile")) {
				return "Result:0";
			} else {
				return "Result:6";
			}
		}

		if ($action == "pic") {
			if (!file_exists("pics"))
				mkdir("pics");

			$newname = $_GET["newname"];

			$ext = strtolower(substr(strrchr($newname, '.'), 1));
			if ($ext != "jpg" && $ext != "bmp" && $ext != "gif" && $ext != "png") {
				return "Result:7";
			}

			$myfile = "pics/".$newname;

			if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], "./$myfile")) {
				return "Result:0";
			} else {
				return "Result:6|" . var_dump($_FILES);
			}
		}
	}

	// Run by default when included/required
	echo run(toConnectionArray($DBIP, $DBNAME, $DBUSER, $DBPASS));

?>
