<?php
	$lat = urldecode($_GET["lat"]);
	$long = urldecode($_GET["long"]);
	$centerlong = urldecode($_GET["centerlong"]);
	$centerlat = urldecode($_GET["centerlat"]);
	$w = urldecode($_GET["w"]);
	$h = urldecode($_GET["h"]);
	$z = urldecode($_GET["z"]);

	if ($centerlong == "") $centerlong=$long;
	if ($centerlat == "") $centerlat=$lat;
	if ($w == "") $w=320;
	if ($h == "") $h=240;
	if ($z == "") $z=2;

	if ($z < 0) $z=0;

	sscanf($lat, "%[^.].%s", $ipart, $decpart);
	$decpart .="000000" ;
	$decpart = substr ( $decpart,0,6);
	if ($lat > 0 && $ipart == '0') $ipart = '';
	if ($lat < 0 && $ipart == '-0') $ipart = '-';
	$lataux = $ipart.$decpart;

	sscanf($long, "%[^.].%s", $ipart, $decpart);
	$decpart .="000000" ;
	$decpart = substr ( $decpart,0,6);
	if ($long > 0 && $ipart == '0') $ipart = '';
	if ($long < 0 && $ipart == '-0') $ipart = '-';
	$longaux = $ipart.$decpart;

	sscanf($centerlat, "%[^.].%s", $ipart, $decpart);
	$decpart .="000000" ;
	$decpart = substr ( $decpart,0,6);
	if ($centerlat > 0 && $ipart == '0') $ipart = '';
	if ($centerlat < 0 && $ipart == '-0') $ipart = '-';
	$centerlataux = $ipart.$decpart;

	sscanf($centerlong, "%[^.].%s", $ipart, $decpart);
	$decpart .="000000" ;
	$decpart = substr ( $decpart,0,6);
	if ($centerlong > 0 && $ipart == '0') $ipart = '';
	if ($centerlong < 0 && $ipart == '-0') $ipart = '-';
	$centerlongaux = $ipart.$decpart;

	$url = "http://maps.google.com/mapdata?latitude_e6=".$centerlataux."&longitude_e6=".$centerlongaux."&Point=b&Point.latitude_e6=".$lataux."&Point.longitude_e6=".$longaux."&Point.iconid=15&Point=e&zm=4800&w=".$w."&h=".$h."&cc=&min_priority=5&zl=".$z;

	$html  = "";
	$html .= "<table border='0'>";
	$html .= " <tr>";

	$html .= "  <td>";
	$html .= "   <form name='form_user' action='gm.php' method='get'>";
	$html .= "    <input name='z' value=".($z-1)." type='hidden' >";
	$html .= "    <input name='lat' value=$lat type='hidden' >";
	$html .= "    <input name='long' value=$long type='hidden' >";
	$html .= "    <input name='centerlong' value=$centerlong type='hidden' >";
	$html .= "    <input name='centerlat' value=$centerlat type='hidden' >";
	$html .= "    <input type='submit' value='Zoom+'>";
	$html .= "   </form>";
	$html .= "  </td>";

	$html .= "  <td>";
	$html .= "   <form name='form_user' action='gm.php' method='get'>";
	$html .= "    <input name='z' value=".($z+1)." type='hidden' >";
	$html .= "    <input name='lat' value=$lat type='hidden' >";
	$html .= "    <input name='long' value=$long type='hidden' >";
	$html .= "    <input name='centerlong' value=$centerlong type='hidden' >";
	$html .= "    <input name='centerlat' value=$centerlat type='hidden' >";
	$html .= "    <input type='submit' value='Zoom-'>";
	$html .= "   </form>";
	$html .= "  </td>";

	$html .= "  <td>";
	$html .= "   <form name='form_user' action='gm.php' method='get'>";
	$html .= "    <input name='z' value=$z type='hidden' >";
	$html .= "    <input name='lat' value=$lat type='hidden' >";
	$html .= "    <input name='long' value=$long type='hidden' >";
	$html .= "    <input name='centerlat' value=$centerlat type='hidden' >";
	$html .= "    <input name='centerlong' value=".($centerlong-0.005)." type='hidden' >";
	$html .= "    <input type='submit' value='<<'>";
	$html .= "    </form>";
	$html .= "   </td>";

	$html .= "  <td>";
	$html .= "   <form name='form_user' action='gm.php' method='get'>";
	$html .= "    <input name='z' value=$z type='hidden' >";
	$html .= "    <input name='lat' value=$lat type='hidden' >";
	$html .= "    <input name='long' value=$long type='hidden' >";
	$html .= "    <input name='centerlat' value=$centerlat type='hidden' >";
	$html .= "    <input name='centerlong' value=".($centerlong+0.005)." type='hidden' >";
	$html .= "    <input type='submit' value='>>'>";
	$html .= "   </form>";
	$html .= "  </td>";

	$html .= "  <td>";
	$html .= "   <form name='form_user' action='gm.php' method='get'>";
	$html .= "    <input name='z' value=$z type='hidden' >";
	$html .= "    <input name='lat' value=$lat type='hidden' >";
	$html .= "    <input name='long' value=$long type='hidden' >";
	$html .= "    <input name='centerlong' value=$centerlong type='hidden' >";
	$html .= "    <input name='centerlat' value=".($centerlat+0.005)." type='hidden' >";
	$html .= "    <input type='submit' value='Up'>";
	$html .= "   </form>";
	$html .= "  </td>";

	$html .= "  <td>";
	$html .= "   <form name='form_user' action='gm.php' method='get'>";
	$html .= "    <input name='z' value=$z type='hidden' >";
	$html .= "    <input name='lat' value=$lat type='hidden' >";
	$html .= "    <input name='long' value=$long type='hidden' >";
	$html .= "    <input name='centerlong' value=$centerlong type='hidden' >";
	$html .= "    <input name='centerlat' value=".($centerlat-0.005)." type='hidden' >";
	$html .= "    <input type='submit' value='Down'>";
	$html .= "   </form>";
	$html .= "  </td>";

	$html .= " </tr>";
	$html .= "</table>";

	echo $html;

	echo "<img src=\"$url\">";

	echo $html;

?>

