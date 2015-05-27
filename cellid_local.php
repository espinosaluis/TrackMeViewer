<?php

require_once("config.php");

if(!@mysql_connect("$DBIP","$DBUSER","$DBPASS"))
{
	echo "Result:4";
	die();
}

mysql_select_db("$DBNAME");
	

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


$result=mysql_query("Select latitude,longitude FROM cellids WHERE cellid='$mcc-$mnc-$lac-$cid' order by dateadded desc limit 0,1");
	
if ( $row=mysql_fetch_array($result) )
	echo "Result:0|".$row['latitude']."|".$row['longitude'];	
else
	echo "Result:6"; // No lat/long for specified CellID

die();		


?>
