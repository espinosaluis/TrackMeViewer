<?php

if ($_REQUEST["myl"] != "") {
  $temp = split(":", $_REQUEST["myl"]);
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

    if (($cid > 0xffff) && ($mcc != 0) && ($mnc != 0)) {
        $mode = 0x00000005;
    } else {
        $mode = 0x00000003;
        $cid &= 0xFFFF;
    }

    $data = pack("nNNnnnCNNNnNNNNNN",
                 0x000e, 0x00000000, 0x00000000, 0x0000, 0x0000, 0x0000, 0x1b,
                 $mnc, $mcc, $mode, 0x0000, $cid, $lac, $mnc, $mcc, 0xffffffff, 0x00000000);

$context = array (
        'http' => array (
            'method' => 'POST',
            'header'=> "Content-type: application/binary\r\n"
                . "Content-Length: " . strlen($data) . "\r\n",
            'content' => $data
            )
        );

$xcontext = stream_context_create($context);

$str=file_get_contents("http://www.google.com/glm/mmap",FALSE,$xcontext);

if (strlen($str) > 10) 
{
        $str = substr($str, 7, 8);
        // The returned string contains both values as a two complements big
        // endian numbers, but PHP's pack does only support machine dependent
        // endianness. So if the machine is little endian, we need to "reverse"
        // each part of the result.
        if (pack('L', 1) !== pack('N', 1))
        {
            $lat = substr($str, 0, 4);
            $lon = substr($str, 4, 4);
            $str = strrev($lat) . strrev($lon);
        }
        $lat_lon = unpack("l2", $str);
        $lat = $lat_lon[1]/1000000;
        $lon = $lat_lon[2]/1000000;
  echo "Result:0|$lat|$lon";
} 
else 
{
  echo "Result:6";
}

?>
