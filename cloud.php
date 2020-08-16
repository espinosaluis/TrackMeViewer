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

		$db = connect_save($connection);
		if (is_null($db)) {
			return "Result:4";
		}

		$id = $_GET["id"];
		if ($id == "") {
			return "Result:3";
		}

		$action = $_GET["a"];

		if ($action == "noop") {
			return "Result:0";
		}

		if ($action == "update") {
			$lat          = $_GET["lat"];
			$long         = $_GET["long"];
			$acc          = $_GET["acc"];
			$pub          = $_GET["pub"];
			$name         = $_GET["dn"];
			$dateoccurred = $_GET["do"];

			if ($pub == "") $pub="1";

			$result  = $db->exec_sql("SELECT ID FROM cloud WHERE ID=?", $id);
			$nume = $result->rowCount();
			if ($nume > 0) {
				$params = array();
				$sql = "UPDATE cloud SET Public=?, Latitude=?, Longitude=?, DateOccurred=?, Accuracy=?, DisplayName=? WHERE ID=?";
				$params[] = $pub;
				$params[] = $lat;
				$params[] = $long;
				$params[] = $dateoccurred;
				if ($acc == "")
					$params[] = null;
				else
					$params[] = $acc;

				if ($name == "")
					$params[] = null;
				else
					$params[] = $name;
				$params[] = $id;

				$db->exec_sql($sql, $params);
			} else {
				$params = array();
				$sql = "INSERT INTO cloud (ID,  Public, Latitude, Longitude, DateOccurred, Accuracy, DisplayName) VALUES (?, ?, ?, ?, ?, ?, ?)";
				$params[] = $id;
				$params[] = $pub;
				$params[] = $lat;
				$params[] = $long;
				$params[] = $dateoccurred;
				if ($acc == "")
					$params[] = null;
				else
					$params[] = $acc;

				if ($name == "")
					$params[] = null;
				else
					$params[] = $name;

				$db->exec_sql($sql, $params);
			}

			return "Result:0";
		}

		if ($action == "show") {
			$lat      = $_GET["lat"];
			$long     = $_GET["long"];
			$datefrom = $_GET["df"];

			$params = array();
			$sql  = "SELECT(DEGREES(ACOS(SIN(RADIANS(Latitude)) * SIN(RADIANS(?)) +";
			$sql .= "COS(RADIANS(Latitude)) * COS(RADIANS(?)) * COS(RADIANS(Longitude - ?))) * 60 * 1.1515 * 1.609344"; // multiplied by 1.609344 for km
			$sql .= ")) AS Distance, ID, Latitude, Longitude, Accuracy, DateOccurred, DisplayName, Public FROM cloud WHERE ID<>?";
			$params[] = $lat;
			$params[] = $lat;
			$params[] = $long;
			$params[] = $id;
			if ($datefrom != "") {
				$sql .= " AND DateOccurred>=? ";
				$params[] = $datefrom;
			}
			$sql .= "ORDER BY Distance ASC LIMIT 0,100";

			$result = $db->exec_sql($sql, $params);

			$output = "";
			while ($result->fetch()) {
				$output .= $row['ID']."|".$row['Latitude']."|".$row['Longitude']."|".$row['DateOccurred']."|".$row['Accuracy']."|".$row['Distance']."|".$row['DisplayName']."|".$row['Public']."\n";
			}

			return "Result:0|$output";
		}
	}

	// Run by default when included/required
	echo run(toConnectionArray($DBIP, $DBNAME, $DBUSER, $DBPASS));

?>
