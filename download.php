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

	session_start();

	if (!ini_get('date.timezone'))
	{
		date_default_timezone_set('GMT');
	}
	require_once("database.php");
	require_once("exporter/base.php");

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

	if (!isset($_SESSION["ID"])) {
		echo "<Result>Not Logged in or this is not a private system</Result>";
		die();
	}

	$action       = $_GET["a"];
	$datefrom     = $_GET["df"];
	$dateto       = $_GET["dt"];
	$showbearings = $_GET["sb"];

	$userid = $_SESSION["ID"];
	$tripid = Exporter::normalize($db, $userid, $_GET);

	if ($action == "kml") {
		require_once("exporter/kml.php");
		$exporter = new KMLExporter($db, $userid, $tripid, $datefrom, $dateto);
	} elseif ($action == "gpx") {
		require_once("exporter/gpx.php");
		$exporter = new GPXExporter($db, $userid, $tripid, $datefrom, $dateto);
	} else {
		echo "<Result>Invalid action selected</Result>";
		die();
	}

	$output = $exporter->export($showbearings);

	// Create file
	$FileName = str_replace(" ", "_", $exporter->username . "_" . $exporter->tripname . "_" . $datefrom . "_" . $dateto . ".$action");

	// Set the name of the downloaded file
	header("Content-type: text/$action");
	header("Content-Disposition:attachment;filename=" . $FileName);
	echo "$output";

	$db = null;
?>
