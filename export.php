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

	require_once("database.php");
	require_once("exporter/base.php");

	header("Content-type: text/xml");

	$requireddb = $_GET["db"];
	if ($requireddb == "" || $requireddb < 8) {
		echo "<Result>5</Result>";
		die();
	}

	$db = connect_save();
	if (is_null($db)) {
		echo "<Result>4</Result>";
		die();
	}

	$action       = $_GET["a"];
	$username     = $_GET["u"];
	$password     = $_GET["p"];
	$datefrom     = $_GET["df"];
	$dateto       = $_GET["dt"];
	$showbearings = $_GET["sb"];


	$userid = $db->valid_login($username, $password);
	if ($userid < 0) {
		echo "<Result>1</Result>";
		die();
	}

	$tripid = Exporter::normalize($db, $userid, $_GET);

	if ($action == "kml") {
		require_once("exporter/kml.php");
		$exporter = new KMLExporter($db, $userid, $tripid, $datefrom, $dateto);
	} elseif ($action == "gpx") {
		require_once("exporter/gpx.php");
		$exporter = new GPXExporter($db, $userid, $tripid, $datefrom, $dateto);
	} else {
		echo "<Result>6</Result>";
		die();
	}

	$output = $exporter->export($showbearings);

	// Create file
	if (!file_exists("routes"))
		mkdir("routes");

	$file = "routes/$username.$action";
	$file_handle = fopen($file,"w");
	fwrite($file_handle, $output);
	fclose($file_handle);

	//echo "<Result>$output</Result>";
	echo "<Result>0</Result>";

?>

