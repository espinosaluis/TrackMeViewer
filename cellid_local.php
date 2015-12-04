<?php

require_once("database.php");

$db = connect_save();

if ($db === null)
{
	echo "Result:4";
	die();
}

if ($_REQUEST["myl"] != "") 
{
  $temp = split(":", $_REQUEST["myl"]);
  $mcc = $temp[0];
  $mnc = $temp[1];
  $lac = $temp[2];
  $cid = $temp[3];
} else 
{
  $mcc = $_REQUEST["mcc"];
  $mnc = $_REQUEST["mnc"];
  $lac = $_REQUEST["lac"];
  $cid = $_REQUEST["cid"];
}

if ( $mcc == "" || $mnc == "" || $lac == "" || $cid == "" )	
{
		echo "Result:7"; // CellID not specified
	 	die();		
}


$result = $db->exec_sql("SELECT Latitude, Longitude FROM cellids WHERE CellID=? ORDER BY DateAdded DESC LIMIT 0,1", "$mcc-$mnc-$lac-$cid");
	
if ( $row=$result->fetch() )
        echo "Result:0|$row[Latitude]|$row[Longitude]";
else
	echo "Result:6"; // No lat/long for specified CellID

die();		


?>
