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

	// This script is invoked for a couple of requests coming from the TrackeMe App
	// Major parameters are: "a" (action), "u" (username), "p" (password) and "db" (databaseversion) - others vary according to action

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

		$username = $_GET["u"];
		$password = $_GET["p"];

		if ($username == "" || $password == "") {
			return "Result:3";
		}

		$userid = $db->valid_login($username, $password);
		switch ($userid) {
			case NO_USER:
				$userid = $db->create_login($username, $password);
				if ($userid < 0)
					return "Result:2";
				break;
			case INVALID_CREDENTIALS:
				return "Result:1"; // User exists, password incorrect
			case LOCKED_USER:
				return "User disabled. Please contact system administrator";
		}

		$action   = $_GET["a"];

		if ($action == "noop") {
			return "Result:0";
		}

		if ($action == "sendemail" ) {
			$to      = $_GET["to"];
			$body    = $_GET["body"];
			$subject = $_GET["subject"];

			if ($subject == "")
				$subject = "Notification alert";

			mail($to,$subject, $body, "From: TrackMe Alert System\nX-Mailer: PHP/");

			return "Result:0";
		}

		if ($action == "geticonlist") {
			$result = $db->exec_sql("SELECT Name FROM icons ORDER BY Name")->fetchAll(PDO::FETCH_COLUMN, 0);
			return "Result:0|" . implode("|", $result);
		}

		if ($action == "upload") {
			$tripname = $_GET["tn"];
			$tripid = "null";
			$locked = 0;

			if ($tripname != "") {
				$row = $db->exec_sql("Select ID, Locked FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $tripname)->fetch();
				if ($row === false) {  // Trip doesn't exist. Let's create it.
					$db->exec_sql("INSERT INTO trips (FK_Users_ID, Name) VALUES (?, ?)", $userid, $tripname);
					$row = $db->exec_sql("SELECT ID, Locked FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $tripname)->fetch();
					if ($row === false) {
						return "Result:6"; // Unable to create trip.
					}
				}
				$tripid = $row['ID'];
				$locked = $row['Locked'];
			}

			if ($locked == 1) {
				return "Result:8"; // Trip is locked
			}

			(isset($_GET["lat"]))      ? $lat               = $_GET["lat"]      : $lat               = null;
			(isset($_GET["long"]))     ? $long              = $_GET["long"]     : $long              = null;
			(isset($_GET["do"]))       ? $dateoccurred      = $_GET["do"]       : $dateoccurred      = null;
			(isset($_GET["alt"]))      ? $altitude          = $_GET["alt"]      : $altitude          = "";
			(isset($_GET["ang"]))      ? $angle             = $_GET["ang"]      : $angle             = "";
			(isset($_GET["sp"]))       ? $speed             = $_GET["sp"]       : $speed             = "";
			(isset($_GET["iconname"])) ? $iconname          = $_GET["iconname"] : $iconname          = "";
			(isset($_GET["comments"])) ? $comments          = $_GET["comments"] : $comments          = "";
			(isset($_GET["imageurl"])) ? $imageurl          = $_GET["imageurl"] : $imageurl          = "";
			(isset($_GET["cid"]))      ? $cellid            = $_GET["cid"]      : $cellid            = "";
			(isset($_GET["ss"]))       ? $signalstrength    = $_GET["ss"]       : $signalstrength    = "";
			(isset($_GET["ssmax"]))    ? $signalstrengthmax = $_GET["ssmax"]    : $signalstrengthmax = "";
			(isset($_GET["ssmin"]))    ? $signalstrengthmin = $_GET["ssmin"]    : $signalstrengthmin = "";
			(isset($_GET["bs"]))       ? $batterystatus     = $_GET["bs"]       : $batterystatus     = "";
			(isset($_GET["upss"]))     ? $uploadss          = $_GET["upss"]     : $uploadss          = "";
			(isset($_GET["upcellext"]))? $upcellext         = $_GET["upcellext"]: $upcellext         = "";

			$iconid = null;
			if ($iconname != "" ) {
				$row = $db->exec_sql("SELECT ID FROM icons WHERE Name=?", $iconname)->fetch();
				if (!($row === false))
					$iconid = $row['ID'];
			}

			$params = array();
			$sql = "INSERT INTO positions (FK_Users_ID, FK_Trips_ID, Latitude, Longitude, DateOccurred, FK_Icons_ID, Speed, Altitude, Comments, ImageURL, Angle, Signalstrength, Signalstrengthmax, Signalstrengthmin, Batterystatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$params[] = $userid;
			$params[] = $tripid;
			$params[] = $lat;
			$params[] = $long;
			$params[] = $dateoccurred;
			$params[] = $iconid;
			if ($speed == "")
				$params[] = null;
			else
				$params[] = $speed;

			if ($altitude == "")
				$params[] = null;
			else
				$params[] = $altitude;

			if ($comments == "")
				$params[] = null;
			else
				$params[] = $comments;

			if ($imageurl == "")
				$params[] = null;
			else
				$params[] = $imageurl;

			if ($angle == "")
				$params[] = null;
			else
				$params[] = $angle;

			if ($uploadss == 1) {
				if ($signalstrength == "")
					$params[] = null;
				else
					$params[] = $signalstrength;

				if ($signalstrengthmax == "")
					$params[] = null;
				else
					$params[] = $signalstrengthmax;

				if ($signalstrengthmin == "")
					$params[] = null;
				else
					$params[] = $signalstrengthmin;
			} else {
				$params[] = null;
				$params[] = null;
				$params[] = null;
			}

			if ($batterystatus == "")
				$params[] = null;
			else
				$params[] = batterystatus;

			$result = $db->exec_sql($sql, $params);
			if (!$result) {
				return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
			}

			if ($upcellext == 1 && $cellid != "") {
				$params = array();
				$sql = "INSERT INTO cellids (CellID, Latitude, Longitude, Signalstrength, Signalstrengthmax, Signalstrengthmin) VALUES (?, ?, ?, ?, ?, ?)";
				$params[] = $cellid;
				$params[] = $lat;
				$params[] = $long;
				if ($signalstrength == "")
					$params[] = null;
				else
					$params[] = $signalstrength;

				if ($signalstrengthmax == "")
					$params[] = null;
				else
					$params[] = $signalstrengthmax;

				if ($signalstrengthmin == "")
					$params[] = null;
				else
					$params[] = $signalstrengthmin;

				$result = $db->exec_sql($sql, $params);
				if (!$result) {
					return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
				}
			}

			return "Result:0";
		}

		if ($action == "updatepositiondata" || $action == "updateimageurl") {
			$id            = $_GET["id"];
			$ignorelocking = $_GET["ignorelocking"];

			if ($id == "") {
				return "Result:6"; // ID not specified
			}

			if ($ignorelocking == "")
				$ignorelocking = 0;

			$locked = 0;
			$sql = "SELECT Locked FROM trips A1 INNER JOIN positions A2 ON A2.FK_Trips_ID=A1.ID WHERE A2.FK_Users_ID=? AND A2.ID=?";
			$params = array();
			$params[] = $userid;
			$params[] = $id;
			$row = $db->exec_sql($sql, $params)->fetch();
			if ($row === false) {
				return "Result:7"; // Trip not found
			} else {
				$locked = $row['Locked'];
				if ($locked == 1 && $ignorelocking == 0) {
					return "Result:8";
				}
			}

			$params = array();
			$sql = "UPDATE positions SET ";

			if (isset($_GET["imageurl"])) {
				$imageurl = $_GET["imageurl"];

				if ($imageurl == "" ) {
					$sql .= " FK_Icons_ID=null, ImageURL=null, ";
				} else {
					$iconid = "null";
					$row = $db->exec_sql("SELECT ID FROM icons WHERE Name='Camera'")->fetch();
					if (!($row === false))
						$iconid = $row['ID'];

					$sql .= " FK_Icons_ID=?, ImageURL=?, ";
					$params[] = $iconid;
					$params[] = $imageurl;
				}
			}

			if (isset($_GET["comments"])) {
				$comments = $_GET["comments"];

				if ($comments == "")
					$sql .= " Comments=null, ";
				else {
					$sql .= " Comments=?, ";
					$params[] = $comments;
				}
			}

			$sql .= "ID=ID WHERE ID=? AND FK_Users_ID=?";
			$params[] = $id;
			$params[] = $userid;

			$result = $db->exec_sql($sql, $params);
			if (!$result) {
				return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
			}
			return "Result:0";
		}

		if ($action == "delete") {
			$tripname = $_GET["tn"];
			$datefrom = $_GET["df"];
			$dateto   = $_GET["dt"];

			$locked = 0;
			$tripid = "null";
			$row = $db->exec_sql("Select ID, Locked FROM trips WHERE FK_Users_ID=? and Name=?", $userid, $tripname)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				$tripid = $row['ID'];
				$locked = $row['Locked'];
				if ($locked == 1) {
					return "Result:8";
				}
			}

			$params = array();
			if ($tripname != "") {
				$sql = "DELETE FROM positions WHERE FK_Trips_ID=? ";
				$params[] = $tripid;
			} else {
				$sql = "DELETE FROM positions WHERE 1=1 ";
			}

			$sql .= " AND FK_Users_ID=? ";
			$params[] = $userid;


			if ($datefrom != "") {
				$sql.=" and DateOccurred>=? ";
				$params[] = $datefrom;
			}
			if ($dateto != "") {
				$sql.=" and DateOccurred<=? ";
				$params[] = $dateto;
			}

			$result = $db->exec_sql($sql, $params);
			if (!$result) {
				return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
			}
			return "Result:0";
		}

		if ($action == "deletepositionbyid") {
			$positionid = $_GET["positionid"];
			if ($positionid == "") {
				return "Result:6"; // ID not specified
			}

			$locked = 0;
			$row = $db->exec_sql("SELECT Locked FROM trips A1 INNER JOIN positions A2 ON A2.FK_Trips_ID=A1.ID WHERE A2.FK_Users_ID=? AND A2.ID=?", $userid, $positionid)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				$locked = $row['Locked'];
				if ($locked == 1) {
					return "Result:8";
				}
			}

			$result = $db->exec_sql("DELETE FROM positions WHERE ID=? AND FK_Users_ID=?", $positionid, $userid);
			if (!$result) {
				return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
			}
			return "Result:0";
		}

		if ($action == "findclosestpositionbytime") {
			$date = $_GET["date"];

			if ($date == "") {
				return "Result:6"; // date not specified
			}

			$row = $db->exec_sql("SELECT ID, DateOccurred FROM positions WHERE DateOccurred=(SELECT MIN(DateOccurred) FROM positions WHERE ABS(TIMESTAMPDIFF(SECOND, ?, DateOccurred))=(SELECT MIN(ABS(TIMESTAMPDIFF(SECOND, ?, DateOccurred))) FROM positions WHERE FK_Users_ID=?) AND FK_USERS_ID=?) AND FK_Users_ID=?", $date, $date, $userid, $userid, $userid)->fetch();
			if ($row === false) {
				return "Result:7"; // No positions from user found
			} else {
				return "Result:0|" . $row['ID'] . "|" . $row['DateOccurred'];
			}
		}

		if ($action == "findclosestpositionbyposition") {
			$lat  = $_GET["lat"];
			$long = $_GET["long"];

			if ($lat == "" || $long == "") {
				return "Result:6"; // position not specified
			}

			$row = $db->exec_sql("SELECT(DEGREES(ACOS(SIN(RADIANS(Latitude))*SIN(RADIANS(?)) + COS(RADIANS(Latitude))*COS(RADIANS(?))*COS(RADIANS(Longitude - ?)) )*60*1.1515)) AS Distance, ID, DateOccurred FROM positions WHERE FK_Users_ID=? ORDER BY Distance ASC LIMIT 0,1", $lat, $lat, $long, $userid)->fetch();
			if ($row === false) {
				return "Result:7"; // No positions from user found
			} else {
				return "Result:0|".$row['ID']."|".$row['DateOccurred']."|".$row['Distance'];
			}
		}

		if ($action == "findnearbypushpins") {
			$tripname = $_GET["tn"];
			$lat      = $_GET["lat"];
			$long     = $_GET["long"];
			$radius   = $_GET["radius"];

			if ($lat == "" || $long == "") {
				return "Result:6"; // position not specified
			}

			if ($radius == "")
				$radius = 50.0;

			$params = array();
			$sql  = "SELECT Latitude, Longitude, Distance, Positioncomments, Positionimageurl, Tripname FROM (SELECT z.Latitude, z.Longitude, p.Radius, p.Distance_unit ";
			$sql .= "* DEGREES(ACOS(COS(RADIANS(p.Latpoint)) * COS(RADIANS(z.Latitude)) * COS(RADIANS(p.Longpoint - z.Longitude)) + SIN(RADIANS(p.Latpoint)) ";
			$sql .= "* SIN(RADIANS(z.Latitude)))) AS Distance, z.Comments AS Positioncomments, z.ImageURL AS Positionimageurl, TT.Name AS Tripname FROM positions AS z LEFT JOIN trips TT ON TT.ID = z.FK_Trips_ID JOIN (/* these are the query parameters */ ";
			$sql .= "SELECT ? AS Latpoint, ? AS Longpoint, ? AS Radius, 111.045 AS Distance_unit) AS p ON 1=1 WHERE ";
			$sql .= "z.FK_Users_ID=? AND (z.Comments<>'' OR z.Imageurl<>'') ";
			$params[] = $lat;
			$params[] = $long;
			$params[] = $radius;
			$params[] = $userid;

			if ($tripname != "") {
				$tripid = "";
				$row = $db->exec_sql("SELECT ID FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $tripname)->fetch();
				if (!($row === false))
					$tripid = $row['ID'];

				if ($tripid <> "") {
					$sql .= "AND (z.FK_Trips_ID<>? OR z.FK_Trips_ID IS null) ";
					$params[] = $tripid;
				}
			}

			$sql .= "AND z.Latitude BETWEEN p.Latpoint - (p.Radius / p.Distance_unit) AND p.Latpoint + (p.Radius / p.Distance_unit) ";
			$sql .= "AND z.Longitude BETWEEN p.Longpoint - (p.Radius / (p.Distance_unit * COS(RADIANS(p.Latpoint)))) AND p.Longpoint + (p.Radius / (p.Distance_unit * COS(RADIANS(p.Latpoint)))) ";
			$sql .= ") AS d WHERE Distance<=Radius ORDER BY Distance LIMIT 15";

			$result = $db->exec-sql($sql, $params);

			$output = "";
			while ($row = $result->fetch()) {
				$output .= $row['Latitude']."|".$row['Longitude']."|".$row['Distance']."|".$row['Positioncomments']."|".$row['Positionimageurl']."|".$row['Tripname']."\n";
			}
			return "Result:0|$output";
		}

		if ($action == "findclosestbuddy") {
			$row = $db->exec_sql("SELECT Latitude, Longitude FROM positions WHERE FK_Users_ID=? ORDER BY DateOccurred DESC LIMIT 0,1", $userid)->fetch();
			if ($row === false) {
				return "Result:6"; // No positions for selected user
			} else {
				/*
				$sql  = "SELECT(DEGREES(ACOS(SIN(RADIANS(Latitude)) * SIN(RADIANS(".$row['Latitude'].")) +";
				$sql .= "COS(RADIANS(Latitude)) * COS(RADIANS(".$row['Latitude'].")) * COS(RADIANS(Longitude - ".$row['Longitude']."))) * 60 * 1.1515 ";
				$sql .= ")) AS Distance, DateOccurred, FK_Users_ID FROM positions WHERE FK_Users_ID<>'$userid' ORDER BY Distance ASC LIMIT 0,1";

				$row = $db->exec_sql($sql);
				if ($row === false) {
					return "Result:7"; // No positions from other users found
				} else
					return "Result:0|" . $row['Distance'] . "|" . $row['DateOccurred'] . "|" . $row['FK_Users_ID'];
				*/
				return "Result:7"; // currently not supported
			}
		}

		if ($action == "gettripinfo") {
			$tripname = $_GET["tn"];
			if ($tripname == "") {
				return "Result:6"; // trip not specified
			}

			$row = $db->exec_sql("SELECT ID, Locked, Comments FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $tripname)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				return "Result:0|" . $row['ID'] . "|" . $row['Locked'] . "|" . $row['Comments'] . "\n";
			}
		}

		if ($action == "gettripfull" || $action == "gettriphighlights") {
			$tripname = $_GET["tn"];
			if ($tripname == "") {
				return "Result:6"; // trip not specified
			}

			$tripid = "";
			$row = $db->exec_sql("SELECT ID FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $tripname)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				$tripid = $row['ID'];
			}

			$result = $db->exec_sql("SELECT Latitude, Longitude, ImageURL, Comments, A2.URL IconURL, DateOccurred, A1.ID, A1.Altitude, A1.Speed, A1.Angle FROM positions A1 LEFT JOIN icons A2 ON A1.FK_Icons_ID=A2.ID WHERE FK_Trips_ID=? ORDER BY DateOccurred", $tripid);

			$output = "";
			while ($row = $result->fetch()) {
				$output .= $row['Latitude']."|".$row['Longitude']."|".$row['ImageURL']."|".$row['Comments']."|".$row['IconURL']."|".$row['DateOccurred']."|".$row['ID']."|".$row['Altitude']."|".$row['Speed']."|".$row['Angle']."\n";
			}

			return "Result:0|$output";
		}

		if ($action == "gettriplist") {
			$datefrom = $_GET["df"];
			$dateto   = $_GET["dt"];
			$order    = $_GET["order"];

			$params = array();
			$sql = "SELECT A1.Locked, A1.Comments, A1.Name,
				(SELECT MAX(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Startdate,
				(SELECT MAX(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Enddate,
				(SELECT TIMEDIFF(MAX(A2.DateOccurred), MIN(A2.DateOccurred)) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Totaltime,
				(SELECT COUNT(*) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID AND A2.Comments IS NOT null) AS Totalcomments,
				(SELECT COUNT(*) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID AND A2.ImageURL IS NOT null) AS Totalimages,
				(SELECT IFNULL(MAX(Speed), 0) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Maxspeed,
				(SELECT IFNULL(MIN(Altitude), 0) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Minaltitude,
				(SELECT IFNULL(MAX(Altitude), 0) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Maxaltitude
				FROM trips A1 WHERE A1.FK_Users_ID=? ";
			$params[] = $userid;

			if ($datefrom != "") {
				$sql .= " AND (SELECT MIN(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID)>=? ";
				$params[] = $datefrom;
			}
			if ($dateto != "") {
				$sql .= " AND (SELECT MIN(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID)<=? ";
				$params[] = $dateto;
			}

			if ($order == "" || $order == "0")
				$sql.= " ORDER BY Name";
			else
				$sql.= " ORDER BY Startdate DESC";

			$result = $db->exec_sql($sql, $params);

			$triplist = "";
			while ($row = $result->fetch()) {
				$triplist .= $row['Name']."|"
						.$row['Startdate']."|"
						.$row['Enddate']."|"
						.$row['Comments']."|"
						.$row['Locked']."|"
						.$row['Totaltime']."|"
						.$row['Totalcomments']."|"
						.$row['Totalimages']."|"
						.$row['Maxspeed']."|"
						.$row['Minaltitude']."|"
						.$row['Maxaltitude']
						."\n";
			}

			$triplist = substr($triplist, 0, -1);
			return "Result:0|$triplist";
		}

		if ($action == "updatetripdata") {
			$tripname = $_GET["tn"];
			if ($tripname == "") {
				return "Result:6"; // trip not specified
			}

			$tripid = "";
			$locked = 0;
			$row = $db->exec_sql("SELECT ID, Locked FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $tripname)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				$tripid = $row['ID'];
				$locked = $row['Locked'];
				if ($locked == 1) {
					return "Result:8";
				}
			}

			$params = array();
			$sql = "UPDATE trips SET ";

			if (isset($_GET["comments"])) {
				$comments = $_GET["comments"];

				if ($comments != "") {
					$sql .= " Comments=?, ";
					$params[] = $comments;
				} else {
					$sql .= " Comments=null, ";
				}
			}

			$sql .= "ID=id WHERE ID=? AND FK_Users_ID=?";
			$params[] = $tripid;
			$params[] = $userid;

			$result = $db->exec_sql($sql, $params);
			if (!$result) {
				return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
			}
			return "Result:0";
		}

		if ($action == "updatelocking") {
			$tripname = $_GET["tn"];
			$locked   = $_GET["locked"];
			if ($tripname == "") {
				return "Result:6"; // trip not specified
			}

			$tripid = "";
			$row = $db->exec_sql("SELECT ID FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $tripname)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				$tripid=$row['ID'];
			}

			$result = $db->exec_sql("UPDATE trips SET Locked=? where ID=? AND FK_Users_ID=?", $locked, $tripid, $userid);
			if (!$result) {
				return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
			}
			return "Result:0";
		}

		if ($action == "deletetrip") {
			$tripname = $_GET["tn"];
			if ($tripname == "") {
				echo "Result:6"; // trip not specified
				die();
			}

			$locked = 0;
			$row = $db->exec_sql("SELECT ID, Locked FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $tripname)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				$tripid = $row['ID'];
				$locked = $row['Locked'];
				if ($locked == 1) {
					return "Result:8";
				}

				try {
					$db->beginTransaction();
					$db->exec_sql("DELETE FROM positions WHERE FK_Trips_ID=? AND FK_Users_ID=?", $tripid, $userid);
					$db->exec_sql("DELETE FROM trips WHERE ID=? AND FK_Users_ID=?", $tripid, $userid);
					$db->commit();
				} catch (Exception $e) {
					$db->rollback();
					return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];;
				}

				return "Result:0";
			}
		}

		if ($action == "deletetripbyid") {
			$tripid = $_GET["tripid"];
			if ($tripid == "") {
				return "Result:6"; // trip not specified
			}

			$locked = 0;
			$row = $db->exec_sql("SELECT Locked FROM trips WHERE FK_Users_ID=? AND ID=?", $userid, $tripid)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				$locked = $row['Locked'];
				if ($locked == 1) {
					return "Result:8";
				}

				try {
					$db->beginTransaction();
					$db->exec_sql("DELETE FROM positions WHERE FK_Trips_ID=? AND FK_Users_ID=?", $tripid, $userid);
					$db->exec_sql("DELETE FROM trips WHERE ID=? AND FK_Users_ID=?", $tripid, $userid);
					$db->commit();
				} catch (Exception $e) {
					$db->rollback();
					return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];;
				}

				return "Result:0";
			}
		}

		if ($action == "addtrip") {
			$tripname = $_GET["tn"];
			if ($tripname == "") {
				return "Result:6"; // trip not specified
			}

			$result = $db->exec_sql("INSERT INTO trips (Name, FK_Users_ID) VALUES (?, ?)", $tripname, $userid);
			if (!$result) {
				return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
			}
			return "Result:0";
		}

		if ($action == "renametrip") {
			$tripname = $_GET["tn"];
			if ($tripname == "") {
				return "Result:6"; // trip not specified
			}

			$newname = $_GET["newname"];
			if ($newname == "") {
				return "Result:9"; // new name not specified
			}

			$locked = 0;
			$row = $db->exec_sql("SELECT Locked FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $tripname)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				$locked = $row['Locked'];
				if ($locked == 1) {
					return "Result:8";
				}
			}

			$row = $db->exec_sql("SELECT ID FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $newname)->fetch();
			if (!($row === false)) {
				return "Result:10"; // new name already exists
			}

			$result = $db->exec_sql("UPDATE trips SET Name=? WHERE Name=? AND FK_Users_ID=?", $newname, $tripname, $userid);
			if (!$result) {
				return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
			}
			return "Result:0";
		}

		if ($action == "renametripbyid") {
			$tripid = $_GET["tripid"];
			if ($tripid == "") {
				return "Result:6"; // trip not specified
			}

			$newname = $_GET["newname"];
			if ($newname == "") {
				return "Result:9"; // new name not specified
			}

			$locked = 0;
			$row = $db->exec_sql("SELECT Locked FROM trips WHERE FK_Users_ID=? AND ID=?", $userid, $tripid)->fetch();
			if ($row === false) {
				return "Result:7"; // trip not found
			} else {
				$locked = $row['Locked'];
				if ($locked == 1) {
					return "Result:8";
				}
			}

			$row = $db->exec_sql("SELECT Name FROM trips WHERE FK_Users_ID=? AND Name=?", $userid, $newname)->fetch();
			if (!($row === false)) {
				return "Result:10"; // new name already exists
			}

			$sql = "UPDATE trips SET Name=? WHERE ID=? AND FK_Users_ID=?" . $newname . $tripid . $userid;
			$result = $db->exec_sql("UPDATE trips SET Name=? WHERE ID=? AND FK_Users_ID=?", $newname, $tripid, $userid);
			if (!$result) {
				return "Result:7|" . $db->errorCode() . "|" . $db->errorInfo()[2];
			}
			return "Result:0";
		}
	}

	// Run by default when included/required
	echo run(toConnectionArray($DBIP, $DBNAME, $DBUSER, $DBPASS));

?>
