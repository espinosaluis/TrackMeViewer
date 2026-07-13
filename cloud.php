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

	$displayedname = mysql_real_escape_string($_GET["u"]);
	$id = mysql_real_escape_string($_GET["id"]);

	// User not specified
	if ($id == "") {
		echo "Result:3";
		die();
	}

	$action = $_GET["a"];
//	sleep(10);

	if ($action=="noop") {
		echo "Result:0";
		die();
	}

	function cloud_has_icon_column() {
		static $hasColumn = null;
		if ($hasColumn !== null) {
			return $hasColumn;
		}

		$result = mysql_query("SHOW COLUMNS FROM cloud LIKE 'IconName'");
		$hasColumn = $result && mysql_num_rows($result) > 0;
		return $hasColumn;
	}

	if ($action == "update") {
		$lat = $_GET["lat"];
		$long = $_GET["long"];
		$acc = $_GET["acc"];
		$pub = $_GET["pub"];
		$name = $_GET["dn"];
		$iconname = isset($_GET["iconname"]) ? mysql_real_escape_string(urldecode($_GET["iconname"])) : "";
		$dateoccurred = urldecode($_GET["do"]);

		if ($pub == "") $pub="1";

		$sql = "SELECT ID FROM cloud WHERE ID='$id'";
		$result = mysql_query($sql);
		$nume = mysql_num_rows($result);
		if ($nume > 0) {
			$sql = "UPDATE cloud SET Public='$pub', Latitude='$lat', Longitude='$long', DateOccurred='$dateoccurred', ";

			if ($acc <> "") 
				$sql .= "Accuracy = '$acc',";
			else
				$sql .= "Accuracy = null,";

			if ($name <> "") 
				$sql .= "DisplayName = '$name' ";
			else
				$sql .= "DisplayName = null ";

			if (cloud_has_icon_column()) {
				if ($iconname <> "")
					$sql .= ", IconName = '$iconname' ";
				else
					$sql .= ", IconName = null ";
			}

			$sql .= "WHERE ID='$id'";

			mysql_query($sql);
		} else {
			if (cloud_has_icon_column())
				$sql = "INSERT INTO cloud (ID, Latitude, Longitude, DateOccurred, Accuracy, DisplayName, Public, IconName) VALUES ('$id', '$lat', '$long', '$dateoccurred', ";
			else
				$sql = "INSERT INTO cloud (ID, Latitude, Longitude, DateOccurred, Accuracy, DisplayName, Public) VALUES ('$id', '$lat', '$long', '$dateoccurred', ";

			if ($acc <> "") 
				$sql .= "'$acc', ";
			else
				$sql .= "null, ";

			if ($name <> "") 
				$sql .= "'$name', ";
			else
				$sql .= "null, ";

			$sql .= $pub;

			if (cloud_has_icon_column()) {
				if ($iconname <> "")
					$sql .= ", '$iconname'";
				else
					$sql .= ", null";
			}

			$sql .= ")";

			mysql_query($sql);
		}

		echo "Result:0";
		die();
	}

	if ($action == "show") {
		$lat = $_GET["lat"];
		$long = $_GET["long"];
		$datefrom = $_GET["df"];

		$output = "";

		$sql  = "SELECT(DEGREES(ACOS(SIN(RADIANS(Latitude)) * SIN(RADIANS(".$lat.")) +";
		$sql .= "COS(RADIANS(Latitude)) * COS(RADIANS(".$lat.")) * COS(RADIANS(Longitude - ".$long."))) * 60 * 1.1515 * 1.609344"; // multiplied by 1.609344 for km
		$sql .= ")) AS Distance, ID, Latitude, Longitude, Accuracy, DateOccurred, DisplayName, Public";
		if (cloud_has_icon_column())
			$sql .= ", IconName";
		$sql .= " FROM cloud WHERE ID<>'".$id."'";

		if ($datefrom != "")
			$sql .= " AND DateOccurred>='$datefrom' ";

		$sql .= "ORDER BY Distance ASC LIMIT 0,100";

		$result = mysql_query($sql);
		while ($row=mysql_fetch_array($result)) {
			$output.=$row['ID']."|".$row['Latitude']."|".$row['Longitude']."|".$row['DateOccurred']."|".$row['Accuracy']."|".$row['Distance']."|".$row['DisplayName']."|".$row['Public'];
			if (cloud_has_icon_column())
				$output.="|".$row['IconName'];
			$output.="\n";
		}

		echo "Result:0|$output";
		die();
	}
?>
