<?php

// Build	: xdaradar 
// Date	: 2008:08:17
// Version	: 1.0
// Changelog: added celldb.org as source using xml library 

require_once("xml.php");


$celldb_org_userid = "your userid";
$celldb_org_hash = "your hashvalue";
// you can get them at http://www.celldb.org/apisignup.php


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

// try to find the cellid in www.celldb.org 



$my_url = "http://celldb.org/api/?method=celldb.getcell&username=".$celldb_org_userid."&hash=".$celldb_org_hash."&mcc=".$mcc."&mnc=".$mnc."&cellid=".$cid."&lac=".$lac."&format=xml" ;
echo $my_url;

$result = file_get_contents($my_url);

$my_data = XML_unserialize($result);

if ($my_data['result']['cell'][0]['cellid']==$cid)
{
	$lat = $my_data['result']['cell'][0]['latitude'];
	$lon = $my_data['result']['cell'][0]['longitude'];
	$result = "Result:0|$lat|$lon";
	echo $result ;
	die() ;
}
else 
{
	echo "Result:6";
}

?>
