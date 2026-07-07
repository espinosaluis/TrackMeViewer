<?php

// Build	: xdaradar 
// Date	: 2008:08:16
// Version	: 0.3
// Changelog: opencellid.org as source 

// first pickup the cellid info

$myl = $_REQUEST["myl"];

if ($_REQUEST["myl"] != "") {
  $temp = split(":", $myl);
  $mcc = $temp[0];
  $mnc = $temp[1];
  $lac = $temp[2];
  $cid = $temp[3];
} else {
  $mcc = $_REQUEST["mcc"];
  $mnc = $_REQUEST["mnc"];
  $lac = $_REQUEST["lac"];
  $cid = $_REQUEST["cid"];
}

// try to find the cellid in www.opencellid.org 

$my_url = "http://www.opencellid.org/cell/get?mcc=".$mcc."&mnc=".$mnc."&cellid=".$cid."&lac=".$lac;

$result = file_get_contents($my_url);

$p = xml_parser_create();
xml_parse_into_struct($p,$result,$my_data) ;
xml_parser_free($p);

if ($my_data[0]['attributes']['STAT']=="ok")
{
	$lat = $my_data[1]['attributes']['LAT'];
	$lon = $my_data[1]['attributes']['LON'];
	if (($lat<>"0.0") and ($lon<>"0.0"))
	{
		$result = "Result:0|$lat|$lon";
		echo $result ;
		die() ;
	}
	else
		echo "Result:6";
}
else 
{
	echo "Result:6";
}

?>
