<?php


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

$my_url = "http://luisespinosa.com/trackme/cellid_local.php?mcc=".$mcc."&mnc=".$mnc."&lac=".$lac."&cid=".$cid;
$result = file_get_contents($my_url);
echo $result;

?>
