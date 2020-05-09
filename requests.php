<?php
	if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
		include_once("fix_mysql.inc.php");
	}

	define("R_OK", 0);

	require_once("database.php");


	function run($connection) {
		$requireddb = urldecode($_GET["db"]);
		if ($requireddb == "" || $requireddb < 8) {
			return "Result:5";
		}

		$db = connect_save($connection);
		if (is_null($db)) {
			return "Result:4";
		}

		// Check username and password
		$username = $_GET["u"];
		$password = $_GET["p"];

		// User not specified
		if ($username == "" || $password == "") {
			return "Result:3";
		}

		$userid = $db->valid_login($username, $password);
		switch ($userid) {
			case NO_USER:
				$userid = $db->create_login($username, $password);
				if ($userid < 0)
					return result(2);
				break;
			case INVALID_CREDENTIALS:
				return result(1); // User exists, password incorrect
			case LOCKED_USER:
				return "User disabled. Please contact system administrator";
		}

		$tripname = urldecode($_GET["tn"]);
		$action = $_GET["a"];

		if ($action == "noop") {
			return "Result:0";
		}

		if ($action == "sendemail" ) {
			$to = $_GET["to"];
			$body = $_GET["body"];
			$subject = $_GET["subject"];

			if ($subject == "")
				$subject = "Notification alert";

			mail($to,$subject, $body, "From: TrackMe Alert System\nX-Mailer: PHP/");

			echo "Result:0";
			die();
		}

		if ($action == "geticonlist") {
			$result = $db->exec_sql("SELECT Name FROM icons ORDER BY Name")->fetchAll(PDO::FETCH_COLUMN, 0);
			return success($result);
		}

		// TODO: As long as this is both using PDO and mysql, start connection here in parallel
		//       mysql is not used before this line so start as late as possible
		if (!@mysql_connect("$connection[host]","$connection[user]","$connection[pass]")) {
			return "Result:4";
		}

		mysql_select_db("$connection[name]");

		if ($action == "upload") {
			$tripid = 'null';
			$locked = 0;

			if ($tripname != "") {
				$result=mysql_query("Select ID, Locked FROM trips WHERE FK_Users_ID='$userid' AND Name='$tripname'");
				if ($row=mysql_fetch_array($result)) {
					$tripid=$row['ID'];
					$locked=$row['Locked'];
				} else { // Trip doesn't exist. Let's create it.
					mysql_query("INSERT INTO trips (FK_Users_ID, Name) VALUES ('$userid','$tripname')");

					$result = mysql_query("SELECT ID, Locked FROM trips WHERE FK_Users_ID='$userid' AND Name='$tripname'");
					if ($row=mysql_fetch_array($result)) {
						$tripid=$row['ID'];
						$locked=$row['Locked'];
					}

					if ($tripid == 'null') {
						echo "Result:6"; // Unable to create trip.
						die();
					}
				}
			}

			if ($locked == 1) {
				echo "Result:8"; // Trip is locked
				die();
			}

			(isset($_GET["lat"]))      ? $lat               = $_GET["lat"]                 : $lat               = null;
			(isset($_GET["long"]))     ? $long              = $_GET["long"]                : $long              = null;
			(isset($_GET["do"]))       ? $dateoccurred      = urldecode($_GET["do"])       : $dateoccurred      = null;
			(isset($_GET["alt"]))      ? $altitude          = urldecode($_GET["alt"])      : $altitude          = "";
			(isset($_GET["ang"]))      ? $angle             = urldecode($_GET["ang"])      : $angle             = "";
			(isset($_GET["sp"]))       ? $speed             = urldecode($_GET["sp"])       : $speed             = "";
			(isset($_GET["iconname"])) ? $iconname          = urldecode($_GET["iconname"]) : $iconname          = "";
			(isset($_GET["comments"])) ? $comments          = urldecode($_GET["comments"]) : $comments          = "";
			(isset($_GET["imageurl"])) ? $imageurl          = urldecode($_GET["imageurl"]) : $imageurl          = "";
			(isset($_GET["cid"]))      ? $cellid            = urldecode($_GET["cid"])      : $cellid            = "";
			(isset($_GET["ss"]))       ? $signalstrength    = urldecode($_GET["ss"])       : $signalstrength    = "";
			(isset($_GET["ssmax"]))    ? $signalstrengthmax = urldecode($_GET["ssmax"])    : $signalstrengthmax = "";
			(isset($_GET["ssmin"]))    ? $signalstrengthmin = urldecode($_GET["ssmin"])    : $signalstrengthmin = "";
			(isset($_GET["bs"]))       ? $batterystatus     = urldecode($_GET["bs"])       : $batterystatus     = "";
			(isset($_GET["upss"]))     ? $uploadss          = urldecode($_GET["upss"])     : $uploadss          = "";
			(isset($_GET["upcellext"]))? $upcellext         = urldecode($_GET["upcellext"]): $upcellext         = "";

			$iconid = "null";
			if ($iconname != "" ) {
				$result=mysql_query("SELECT ID FROM icons WHERE Name='$iconname'");
				if ($row=mysql_fetch_array($result))
					$iconid = $row['ID'];
			}

			$sql = "INSERT INTO positions (FK_Users_ID, FK_Trips_ID, Latitude, Longitude, DateOccurred, FK_Icons_ID, Speed, Altitude, Comments, ImageURL, Angle, Signalstrength, Signalstrengthmax, Signalstrengthmin, Batterystatus) VALUES ('$userid', $tripid, '$lat', '$long', '$dateoccurred', $iconid,";

			if ($speed == "")
				$sql .= "null, ";
			else
				$sql .= "'".$speed."', ";

			if ($altitude == "")
				$sql .= "null, ";
			else
				$sql .= "'".$altitude."', ";

			if ($comments == "")
				$sql .= "null, ";
			else
				$sql .= "'".$comments."', ";

			if ($imageurl == "")
				$sql .= "null, ";
			else
				$sql .= "'".$imageurl."', ";

			if ($angle == "")
				$sql .= "null, ";
			else
				$sql .= "'".$angle."', ";

			if ($uploadss == 1) {
				if ($signalstrength == "")
					$sql .= "null, ";
				else
					$sql .= $signalstrength.", ";

				if ($signalstrengthmax == "")
					$sql .= "null, ";
				else
					$sql .= $signalstrengthmax.", ";

				if ($signalstrengthmin == "")
					$sql .= "null, ";
				else
					$sql .= $signalstrengthmin.", ";
			} else {
				$sql .= "null, null, null, ";
			}

			if ($batterystatus == "")
				$sql .= "null";
			else
				$sql .= $batterystatus;

			$sql .= ")";

			$result = mysql_query($sql);
			if (!$result) {
				echo "Result:7|".mysql_error();
				die();
			}

			if ($upcellext == 1 && $cellid != "") {
				$sql = "INSERT INTO cellids (CellID, Latitude, Longitude, Signalstrength, Signalstrengthmax, Signalstrengthmin) VALUES ('$cellid', '$lat', '$long', ";

				if ($signalstrength == "")
					$sqlc.= "null, ";
				else
					$sql .= $signalstrength.", ";

				if ($signalstrengthmax == "")
					$sql .= "null, ";
				else
					$sql .= $signalstrengthmax.", ";

				if ($signalstrengthmin == "")
					$sql .= "null";
				else
					$sql .= $signalstrengthmin;

				$sql.=")";

				mysql_query($sql);
			}

			echo "Result:0";
			die();
		}

		if ($action == "updatepositiondata" || $action == "updateimageurl") {
			$id = urldecode($_GET["id"]);
			$ignorelocking = urldecode($_GET["ignorelocking"]);

			if ($id == "") {
				echo "Result:6"; // ID not specified
				die();
			}

			if ($ignorelocking == "")
				$ignorelocking = 0;

			$locked = 0;
			$result = mysql_query("SELECT locked FROM trips A1 INNER JOIN positions A2 ON A2.FK_Trips_ID=A1.ID WHERE A2.FK_Users_ID='$userid' AND A2.ID='$id'");
			if ($row = mysql_fetch_array($result)) {
				$locked = $row['Locked'];
				if ($locked == 1 && $ignorelocking == 0) {
					echo "Result:8";
					die();
				}
			} else {
				echo "Result:7"; // Trip not found
				die();
			}

			$sql = "UPDATE positions SET ";

			if (isset($_GET["imageurl"])) {
				$imageurl = urldecode($_GET["imageurl"]);

				if ($imageurl != "" ) {
					$iconid='null';
					$result = mysql_query("SELECT ID FROM icons WHERE Name='Camera'");
					if ($row = mysql_fetch_array($result))
						$iconid=$row['ID'];

					$sql .= " FK_Icons_ID=$iconid, ImageURL='$imageurl', ";
				} else {
					$sql .= " ImageURL=null, ";
				}
			}

			if (isset($_GET["comments"])) {
				$comments = urldecode($_GET["comments"]);

				if ($comments == "")
					$sql .= " Comments=null, ";
				else
					$sql .= " Comments='$comments', ";
			}

			$sql .= "ID=ID WHERE id=$id AND FK_Users_ID='$userid'";

			mysql_query($sql);
			echo "Result:0";
			die();
		}

		if ($action == "delete") {
			$locked = 0;
			$tripid = "";
			$result = mysql_query("Select ID, Locked FROM trips WHERE FK_Users_ID='$userid' and Name='$tripname'");
			if ($row = mysql_fetch_array($result)) {
				$tripid=$row['ID'];
			$locked = $row['Locked'];

				if ($locked == 1)
				{
					echo "Result:8";
					die();
				}
			} else {
				echo "Result:7"; // trip not found
				die();
			}

			if ($tripname == "<None>")
				$sql = "DELETE FROM positions WHERE FK_Trips_ID IS null ";
			elseif ($tripname != "")
				$sql = "DELETE FROM positions WHERE FK_Trips_ID='$tripid' ";
			else
				$sql = "DELETE FROM positions WHERE 1=1 ";

			$sql .= " AND FK_Users_ID = '$userid' ";

			$datefrom = urldecode($_GET["df"]);
			$dateto = urldecode($_GET["dt"]);

			if ($datefrom != "")
				$sql.=" and DateOccurred>='$datefrom' ";
			if ($dateto != "")
				$sql.=" and DateOccurred<='$dateto' ";

			mysql_query($sql);
			echo "Result:0";
			die();
		}

		if ($action == "deletepositionbyid") {
			$positionid = urldecode($_GET["positionid"]);
			if ($positionid == "") {
				echo "Result:6";
				die();
			}

			$locked = 0;
			$result = mysql_query("SELECT locked FROM trips A1 INNER JOIN positions A2 ON A2.FK_Trips_ID=A1.ID WHERE A2.FK_Users_ID='$userid' AND A2.ID='$positionid'");
			if ($row = mysql_fetch_array($result)) {
				$locked = $row['Locked'];
				if ($locked == 1)
				{
					echo "Result:8";
					die();
				}
			} else {
				echo "Result:7"; // trip not found
				die();
			}

			$sql = "DELETE FROM positions WHERE ID='$positionid' AND FK_Users_ID='$userid'";

			mysql_query($sql);
			echo "Result:0";
			die();
		}

		if ($action == "findclosestpositionbytime") {
			$date = urldecode($_GET["date"]);

			if ($date == "") {
				echo "Result:6"; // date not specified
				die();
			}

			$sql  = "SELECT ID, DateOccurred FROM positions ";
			$sql .= "WHERE DateOccurred=(SELECT MIN(DateOccurred) ";
			$sql .= "FROM positions WHERE ABS(TIMESTAMPDIFF(SECOND, '$date', DateOccurred))= ";
			$sql .= "(SELECT MIN(ABS(TIMESTAMPDIFF(SECOND, '$date', DateOccurred))) ";
			$sql .= "FROM positions WHERE FK_Users_ID='$userid') AND FK_USERS_ID='$userid') ";
			$sql .= "AND FK_Users_ID='$userid'";

			$result = mysql_query($sql);

			if ($row=mysql_fetch_array($result))
			{
				echo "Result:0|".$row['ID']."|".$row['DateOccurred'];
			}
			else
				echo "Result:7"; // No positions from user found

			die();
		}

		if ($action == "findclosestpositionbyposition") {
			$lat = $_GET["lat"];
			$long = $_GET["long"];

			if ($lat == "" || $long== "") {
				echo "Result:6"; // position not specified
				die();
			}

			$sql  = "SELECT(DEGREES(ACOS(SIN(RADIANS(Latitude)) * SIN(RADIANS(".$lat.")) +";
			$sql .= "COS(RADIANS(Latitude)) * COS(RADIANS(".$lat.")) * COS(RADIANS(Longitude - ".$long.")) ) * 60 * 1.1515 ";
			$sql .= ")) AS Distance, ID, DateOccurred FROM positions WHERE FK_Users_ID='$userid' ORDER BY Distance ASC LIMIT 0,1";

			$result = mysql_query($sql);

			if ($row = mysql_fetch_array($result)) {
				echo "Result:0|".$row['ID']."|".$row['DateOccurred']."|".$row['Distance'];
			} else
				echo "Result:7"; // No positions from user found

			die();
		} 

		if ($action == "findnearbypushpins") {
			$lat = $_GET["lat"];
			$long = $_GET["long"];
			$radius = $_GET["radius"];

			if ($lat == "" || $long == "") {
				echo "Result:6"; // position not specified
				die();
			}

			if ($radius == "")
				$radius = 50.0;

			$sql  = "SELECT Latitude, Longitude, Distance, Positioncomments, Positionimageurl, Tripname FROM (SELECT z.Latitude, z.Longitude, p.Radius, p.Distance_unit ";
			$sql .= "* DEGREES(ACOS(COS(RADIANS(p.Latpoint)) * COS(RADIANS(z.Latitude)) * COS(RADIANS(p.Longpoint - z.Longitude)) + SIN(RADIANS(p.Latpoint)) ";
			$sql .= "* SIN(RADIANS(z.Latitude)))) AS Distance, z.Comments AS Positioncomments, z.ImageURL AS Positionimageurl, TT.Name AS Tripname FROM positions AS z LEFT JOIN trips TT ON TT.ID = z.FK_Trips_ID JOIN (/* these are the query parameters */ ";
			$sql .= "SELECT ".$lat." AS Latpoint, ".$long." AS Longpoint, ".$radius." AS Radius, 111.045 AS Distance_unit) AS p ON 1=1 WHERE ";
			$sql .= "z.FK_Users_ID='$userid' AND (z.Comments<>'' OR z.Imageurl<>'') ";

			if ($tripname != "") {
				$tripid = "";

				$result = mysql_query("SELECT ID FROM trips WHERE FK_Users_ID='$userid' AND Name='$tripname'");

				if ($row = mysql_fetch_array($result))
					$tripid=$row['ID'];

				if ($tripid <> "")
					$sql .= "AND (z.FK_Trips_ID<>".$tripid." or z.FK_Trips_ID IS null) ";
			}

			$sql .= "AND z.Latitude BETWEEN p.Latpoint - (p.Radius / p.Distance_unit) AND p.Latpoint + (p.Radius / p.Distance_unit) ";
			$sql .= "AND z.Longitude BETWEEN p.Longpoint - (p.Radius / (p.Distance_unit * COS(RADIANS(p.Latpoint)))) AND p.Longpoint + (p.Radius / (p.Distance_unit * COS(RADIANS(p.Latpoint)))) ";
			$sql .= ") AS d WHERE Distance<=Radius ORDER BY Distance LIMIT 15";

			$result = mysql_query($sql);

			$output = "";
			while ($row=mysql_fetch_array($result)) {
				$output .= $row['Latitude']."|".$row['Longitude']."|".$row['Distance']."|".$row['Positioncomments']."|".$row['Positionimageurl']."|".$row['Tripname']."\n";
			}

			echo "Result:0|$output";
			die();
		}

		if ($action == "findclosestbuddy") {
			$result = mysql_query("SELECT Latitude, Longitude FROM positions WHERE FK_Users_ID='$userid' ORDER BY DateOccurred DESC LIMIT 0,1");

			if ($row=mysql_fetch_array($result)) {
				/*
				$sql  = "SELECT(DEGREES(ACOS(SIN(RADIANS(Latitude)) * SIN(RADIANS(".$row['Latitude'].")) +";
				$sql .= "COS(RADIANS(Latitude)) * COS(RADIANS(".$row['Latitude'].")) * COS(RADIANS(Longitude - ".$row['Longitude']."))) * 60 * 1.1515 ";
				$sql .= ")) AS Distance, DateOccurred, FK_Users_ID FROM positions WHERE FK_Users_ID<>'$userid' ORDER BY Distance ASC LIMIT 0,1";

				$result=mysql_query($sql);

				if ($row = mysql_fetch_array($result)) {
					echo "Result:0|".$row['Distance']."|".$row['DateOccurred']."|".$row['FK_Users_ID'];
				} else
					echo "Result:7"; // No positions from other users found
				*/

				echo "Result:7";
			} else
				echo "Result:6"; // No positions for selected user

			die();
		} 

		// Trips
		if ($action == "gettripinfo") {
			if ($tripname == "") {
				echo "Result:6"; // trip not specified
				die();
			}

			$result = mysql_query("SELECT ID, Locked, Comments FROM trips WHERE FK_Users_ID='$userid' AND Name='$tripname'");
			if ($row = mysql_fetch_array($result)) {
				$output = $row['ID']."|".$row['Locked']."|".$row['Comments']."\n";
			} else {
				echo "Result:7"; // trip not found
				die();
			}

			echo "Result:0|$output";
			die();
		}

		if ($action == "gettripfull" || $action == "gettriphighlights") {
			if ($tripname == "") {
				echo "Result:6"; // trip not specified
				die();
			}

			$tripid = "";
			$result = mysql_query("SELECT ID FROM trips WHERE FK_Users_ID='$userid' AND Name='$tripname'");
			if ($row=mysql_fetch_array($result)) {
				$tripid=$row['ID'];
			} else {
				echo "Result:7"; // trip not found
				die();
			}

			$output = "";
			$result = mysql_query("SELECT Latitude, Longitude, ImageURL, Comments, A2.URL IconURL, DateOccurred, A1.ID, A1.Altitude, A1.Speed, A1.Angle FROM positions A1 LEFT JOIN icons A2 ON A1.FK_Icons_ID=A2.ID WHERE FK_Trips_ID='$tripid' ORDER BY DateOccurred");
			while ($row=mysql_fetch_array($result)) {
				$output .= $row['Latitude']."|".$row['Longitude']."|".$row['ImageURL']."|".$row['Comments']."|".$row['IconURL']."|".$row['DateOccurred']."|".$row['ID']."|".$row['Altitude']."|".$row['Speed']."|".$row['Angle']."\n";
			}

			echo "Result:0|$output";
			die();
		}

		if ($action == "gettriplist") {
			$order = $_GET["order"];

			$triplist = "";
			$sql = "SELECT A1.Locked, A1.Comments, A1.Name, 
				(SELECT MAX(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Startdate, 
				(SELECT MAX(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Enddate, 
				(SELECT TIMEDIFF(MAX(A2.DateOccurred), MIN(A2.DateOccurred)) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Totaltime,
				(SELECT COUNT(*) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID AND A2.Comments IS NOT null) AS Totalcomments,
				(SELECT COUNT(*) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID AND A2.ImageURL IS NOT null) AS Totalimages,
				(SELECT IFNULL(MAX(Speed), 0) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Maxspeed,
				(SELECT IFNULL(MIN(Altitude), 0) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Minaltitude,
				(SELECT IFNULL(MAX(Altitude), 0) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Maxaltitude
				FROM trips A1 WHERE A1.FK_Users_ID='$userid' ";

			$datefrom = urldecode($_GET["df"]);
			$dateto = urldecode($_GET["dt"]);

			if ($datefrom != "")
				$sql .= " AND (SELECT MIN(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID)>='$datefrom' ";
			if ($dateto != "")
				$sql .= " AND (SELECT MIN(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID)<='$dateto' ";

			if ($order == "" || $order == "0")
				$sql.= " ORDER BY Name";
			else
				$sql.= " ORDER BY Startdate DESC";

			$result = mysql_query($sql);

			while( $row=mysql_fetch_array($result) ) {
				$triplist.=$row['Name']."|"
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
			echo "Result:0|$triplist";
			die();
		}

		if ($action == "updatetripdata") {
			if ($tripname == "") {
				echo "Result:6"; // trip not specified
				die();
			}

			$tripid = "";
			$locked = 0;
			$result = mysql_query("SELECT ID, Locked FROM trips WHERE FK_Users_ID='$userid' AND Name='$tripname'");
			if ($row = mysql_fetch_array($result)) {
				$tripid=$row['ID'];
				$locked = $row['Locked'];

				if ($locked == 1) {
					echo "Result:8";
					die();
				}
			} else {
				echo "Result:7"; // trip not found
				die();
			}

			$sql = "UPDATE trips SET ";

			if (isset($_GET["comments"])) {
				$comments = urldecode($_GET["comments"]);

				if ($comments != "")
					$sql .= " Comments='$comments', ";
				else
					$sql .= " Comments=null, ";
			}

			$sql .= "ID=id WHERE ID='$tripid' AND FK_Users_ID='$userid'";

			mysql_query($sql);
			echo "Result:0";
			die();
		}

		if ($action == "updatelocking") {
			if ($tripname == "") {
				echo "Result:6"; // trip not specified
				die();
			}

			$tripid = "";
			$result = mysql_query("SELECT ID FROM trips WHERE FK_Users_ID='$userid' AND Name='$tripname'");
			if ($row = mysql_fetch_array($result))
			{
				$tripid=$row['ID'];
			} else {
				echo "Result:7"; // trip not found
				die();
			}

			$locked = urldecode($_GET["locked"]);

			$sql = "UPDATE trips SET Locked='$locked' where ID='$tripid' AND FK_Users_ID='$userid'";

			mysql_query($sql);
			echo "Result:0";
			die();
		}

		if ($action == "deletetrip") {
			if ($tripname == "") {
				echo "Result:6"; // trip not specified
				die();
			}

			$tripid = "";
			$locked = 0;
			$result = mysql_query("SELECT ID, Locked FROM trips WHERE FK_Users_ID='$userid' AND Name='$tripname'");
			if ($row = mysql_fetch_array($result)) {
				$tripid=$row['ID'];
				$locked = $row['Locked'];

				if ($locked == 1) {
					echo "Result:8";
					die();
				}

				mysql_query("DELETE FROM positions WHERE FK_Trips_ID='$tripid' AND FK_Users_ID='$userid'");
				mysql_query("DELETE FROM trips WHERE ID='$tripid' AND FK_Users_ID='$userid'");

				echo "Result:0";
				die();
			} else {
				echo "Result:7"; // trip not found
				die();
			}
		}

		if ($action == "addtrip") {
			if ($tripname == "") {
				echo "Result:6"; // trip not specified
				die();
			}

			mysql_query("INSERT INTO trips (Name, FK_Users_ID) VALUES ('$tripname','$userid')");
			echo "Result:0";
			die();
		}

		if ($action == "renametrip") {
			if ($tripname == "")
			{
				echo "Result:6"; // trip not specified
				die();
			}

			$newname = $_GET["newname"];
			if ($newname == "")
			{
				echo "Result:9"; // new name not specified
				die();
			}

			$locked = 0;
			$result = mysql_query("SELECT Locked FROM trips WHERE FK_Users_ID='$userid' AND Name='$tripname'");
			if ($row = mysql_fetch_array($result)) {
				$locked = $row['Locked'];

				if ($locked == 1) {
					echo "Result:8";
					die();
				}
			} else {
				echo "Result:7"; // trip not found
				die();
			}

			$result = mysql_query("SELECT ID FROM trips WHERE FK_Users_ID='$userid' AND Name='$newname'");
			if ($row = mysql_fetch_array($result)) {
				echo "Result:10"; // new name already exists
				die();
			}

			mysql_query("UPDATE trips SET Name='$newname' WHERE Name='$tripname' AND FK_Users_ID='$userid'");
			echo "Result:0";
			die();
		}
	}

	// Run by default when included/required, unless __norun is set to true
	if (!isset($__norun) || !$__norun) {
		echo run(toConnectionArray($DBIP, $DBNAME, $DBUSER, $DBPASS));
	}

	function success($message="") {
		return result(R_OK, $message);
	}

	function result($id=R_OK, $message="") {
		if (is_array($message))
			$message = implode("|", $message);
		if ($message)
			$message = "|$message";
		return "Result:$id$message";
	}

?>

