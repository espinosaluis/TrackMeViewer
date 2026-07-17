<?php
	require_once("config.php");
	if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
		include_once("fix_mysql.inc.php");
	}

	$requireddb = urldecode($_GET["db"]);
	if ($requireddb == "" || $requireddb < 8) {
		echo "Result:5";
		die;
	}

	if (!@mysql_connect("$DBIP","$DBUSER","$DBPASS")) {
		echo "Result:4";
		die();
	}

	mysql_select_db("$DBNAME");

	$id = mysql_real_escape_string($_GET["id"]);

	// User not specified
	if ($id == "") {
		echo "Result:3";
		die();
	}

	$action = $_GET["a"];
	$trailmaxaccuracy = 30;
	if (isset($_GET["ta"])) {
		$trailmaxaccuracy = intval($_GET["ta"]);
		if ($trailmaxaccuracy <= 0) {
			$trailmaxaccuracy = 30;
		}
	}
//	sleep(10);

	if ($action=="noop") {
		echo "Result:0";
		die();
	}

	if ($action == "update") {
		$lat = $_GET["lat"];
		$long = $_GET["long"];
		$acc = $_GET["acc"];
		$altitude = isset($_GET["al"]) ? mysql_real_escape_string(urldecode($_GET["al"])) : "";
		$speed = isset($_GET["sp"]) ? mysql_real_escape_string(urldecode($_GET["sp"])) : "";
		$angle = isset($_GET["an"]) ? mysql_real_escape_string(urldecode($_GET["an"])) : "";
		$battery = isset($_GET["ba"]) ? intval($_GET["ba"]) : "";
		$dateoccurred = urldecode($_GET["do"]);

		$sql = "INSERT INTO cloud (DeviceID, Latitude, Longitude, Altitude, Speed, Angle, DateOccurred, Accuracy, BatteryStatus) VALUES ('$id', '$lat', '$long', ";

		if ($altitude <> "")
			$sql .= "'$altitude', ";
		else
			$sql .= "null, ";

		if ($speed <> "")
			$sql .= "'$speed', ";
		else
			$sql .= "null, ";

		if ($angle <> "")
			$sql .= "'$angle'";
		else
			$sql .= "null";

		$sql .= ", '$dateoccurred', ";

		if ($acc <> "")
			$sql .= "'$acc', ";
		else
			$sql .= "null, ";

		if ($battery !== "")
			$sql .= "'$battery')";
		else
			$sql .= "null)";

		if (!mysql_query($sql)) {
			echo "Result:6|Insert failed|" . mysql_error();
			die();
		}

		$insertid = mysql_insert_id();
		if (!mysql_query("DELETE FROM cloud WHERE DateOccurred < DATE_SUB(NOW(), INTERVAL 1 DAY)")) {
			echo "Result:7|Cleanup failed|" . mysql_error();
			die();
		}

		$stored = 0;
		if ($insertid > 0) {
			$check = mysql_query("SELECT COUNT(*) AS Count FROM cloud WHERE ID=" . intval($insertid));
			if ($check) {
				$row = mysql_fetch_array($check);
				if ($row && intval($row['Count']) > 0)
					$stored = 1;
			}
		}

		if ($stored != 1) {
			echo "Result:8|Row not stored|ID:" . $insertid . "|DateOccurred:" . $dateoccurred;
			die();
		}

		echo "Result:0|Stored:1|ID:" . $insertid . "|DateOccurred:" . $dateoccurred;
		die();
	}

	if ($action == "show") {
		$lat = $_GET["lat"];
		$long = $_GET["long"];
		$datefrom = $_GET["df"];

		$output = "";

		$sql  = "SELECT 'C' AS EntryType, (DEGREES(ACOS(SIN(RADIANS(A1.Latitude)) * SIN(RADIANS(".$lat.")) +";
		$sql .= "COS(RADIANS(A1.Latitude)) * COS(RADIANS(".$lat.")) * COS(RADIANS(A1.Longitude - ".$long."))) * 60 * 1.1515 * 1.609344";
		$sql .= ")) AS Distance, A1.DeviceID, A1.Latitude, A1.Longitude, A1.Accuracy, A1.DateOccurred, A1.Speed, A1.Altitude, A1.BatteryStatus";
		$sql .= " FROM cloud A1";
		$sql .= " INNER JOIN (SELECT DeviceID, MAX(ID) AS MaxID FROM cloud";
		if ($datefrom != "")
			$sql .= " WHERE DateOccurred>='$datefrom'";
		$sql .= " GROUP BY DeviceID) A2 ON A1.DeviceID=A2.DeviceID AND A1.ID=A2.MaxID";
		$sql .= " WHERE A1.DeviceID<>'".$id."'";
		$sql .= " ORDER BY Distance ASC LIMIT 0,100";

		$result = mysql_query($sql);
		while ($row=mysql_fetch_array($result)) {
			$output.=$row['EntryType']."|".$row['DeviceID']."|".$row['Latitude']."|".$row['Longitude']."|".$row['DateOccurred']."|".$row['Accuracy']."|".$row['Distance']."|".$row['Speed']."|".$row['Altitude']."|".$row['BatteryStatus'];
			$output.="\n";
		}

		$sql  = "SELECT 'T' AS EntryType, (DEGREES(ACOS(SIN(RADIANS(Latitude)) * SIN(RADIANS(".$lat.")) +";
		$sql .= "COS(RADIANS(Latitude)) * COS(RADIANS(".$lat.")) * COS(RADIANS(Longitude - ".$long."))) * 60 * 1.1515 * 1.609344";
		$sql .= ")) AS Distance, DeviceID, Latitude, Longitude, Accuracy, DateOccurred, Speed, Altitude, BatteryStatus";
		$sql .= " FROM cloud WHERE DeviceID<>'".$id."'";

		if ($datefrom != "")
			$sql .= " AND DateOccurred>='$datefrom' ";

		$sql .= " AND Accuracy IS NOT NULL AND Accuracy > 0 AND Accuracy<=".$trailmaxaccuracy." ";

		$sql .= "ORDER BY DeviceID ASC, DateOccurred ASC LIMIT 0,5000";

		$result = mysql_query($sql);
		while ($row=mysql_fetch_array($result)) {
			$output.=$row['EntryType']."|".$row['DeviceID']."|".$row['Latitude']."|".$row['Longitude']."|".$row['DateOccurred']."|".$row['Accuracy']."|".$row['Distance']."|".$row['Speed']."|".$row['Altitude']."|".$row['BatteryStatus'];
			$output.="\n";
		}

		echo "Result:0|$output";
		die();
	}

	if ($action == "requestshare") {
		$target = isset($_GET["target"]) ? mysql_real_escape_string($_GET["target"]) : "";
		$command = isset($_GET["cmd"]) ? strtolower(mysql_real_escape_string($_GET["cmd"])) : "";
		if ($target == "" || ($command != "start" && $command != "stop")) {
			echo "Result:9|Invalid request";
			die();
		}

		if (!mysql_query("INSERT INTO cloud_requests (TargetDeviceID, RequesterDeviceID, Command) VALUES ('".$target."', '".$id."', '".$command."')")) {
			echo "Result:10|Request insert failed|" . mysql_error();
			die();
		}

		mysql_query("DELETE FROM cloud_requests WHERE DateRequested < DATE_SUB(NOW(), INTERVAL 7 DAY)");
		echo "Result:0|Queued:1";
		die();
	}

	if ($action == "pullrequest") {
		$result = mysql_query("SELECT ID, RequesterDeviceID, Command, DateRequested FROM cloud_requests WHERE TargetDeviceID='".$id."' ORDER BY ID ASC LIMIT 0,1");
		if (!$result) {
			echo "Result:11|Request fetch failed|" . mysql_error();
			die();
		}

		$row = mysql_fetch_array($result);
		if (!$row) {
			echo "Result:0|Pending:0";
			die();
		}

		echo "Result:0|Pending:1|RequestID:".$row["ID"]."|RequesterID:".$row["RequesterDeviceID"]."|Command:".$row["Command"]."|DateRequested:".$row["DateRequested"];
		die();
	}

	if ($action == "ackrequest") {
		$requestid = isset($_GET["rid"]) ? intval($_GET["rid"]) : 0;
		if ($requestid <= 0) {
			echo "Result:12|Invalid request id";
			die();
		}

		if (!mysql_query("DELETE FROM cloud_requests WHERE ID=".$requestid." AND TargetDeviceID='".$id."'")) {
			echo "Result:13|Request delete failed|" . mysql_error();
			die();
		}

		echo "Result:0|Acknowledged:1";
		die();
	}
?>
