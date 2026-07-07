<?php
	require_once("base.php");

	class GPXExporter extends Exporter {

		public function export($showbearings) {
			$result = $this->exec_sql(false);

			$n=0;
			$bounds_lat_min = 0;
			$bounds_lat_max = 0;
			$bounds_lon_min = 0;
			$bounds_lon_max = 0;
			$wptdata = "";

			$trkptdata  = "<trk>\n";
			$trkptdata .= " <trkseg>\n";
			while ($row=$result->fetch()) {
				$this->simulate_old($row);
				if (($row['Latitude']<$bounds_lat_min && $bounds_lat_min!=0) || $bounds_lat_min==0) { $bounds_lat_min = $row['Latitude']; }
				if (($row['Latitude']>$bounds_lat_max && $bounds_lat_max!=0) || $bounds_lat_max==0) { $bounds_lat_max = $row['Latitude']; }
				if (($row['Longitude']<$bounds_lon_min && $bounds_lon_min!=0) || $bounds_lon_min==0) { $bounds_lon_min = $row['Longitude']; }
				if (($row['Longitude']>$bounds_lon_max && $bounds_lon_max!=0) || $bounds_lon_max==0) { $bounds_lon_max = $row['Longitude']; }
				$speedMPH = number_format($row['Speed']*2.2369362920544,2);
				$speedKPH = number_format($row['Speed']*3.6,2);
				$altitudeFeet = number_format($row['Altitude']*3.2808399,2);
				$altitudeM = number_format($row['Altitude'],2);
				$angle = number_format($row['Angle'],2);
				/*
				$wptdata.="<wpt lat=\"" . $row['Latitude'] . "\" lon=\"" . $row['Longitude'] . "\">\n";
				$wptdata.=" <ele>" . $row['Altitude'] . "</ele>\n";
				$wptdata.=" <time>" . date('Y-m-d', $row['DateOccured']) . "T" . date('H:i:s',$row['DateOccured']) . "Z</time>\n";
				$wptdata.=" <name><![CDATA[" . date('Y-m-d', $row['DateOccured']) . "-" . str_pad($n, 3, "0", STR_PAD_LEFT)."]]></name>\n";
				//$wptdata.=" <cmt><![CDATA[" . $row['Comment'] . "]]></cmt>\n";
				//$wptdata.=" <desc><![CDATA[Speed: " . $speedMPH . " MPH (" . $speedKPH . " km/h)]]></desc>\n";
				//$wptdata.=" <sym>Dot</sym>\n";
				//$wptdata.=" <type><![CDATA[Dot]]></type>\n";
				$wptdata.="</wpt>\n";
				*/
				$row['DateOccured'] = strtotime($row['DateOccurred']);
				$trkptdata .= "   <trkpt lat=\"" . $row['Latitude'] . "\" lon=\"" . $row['Longitude'] . "\">\n";
				$trkptdata .= "    <ele>" . $altitudeM . "</ele>\n";
				$trkptdata .= "    <time>" . date('Y-m-d', $row['DateOccured']) . "T" . date('H:i:s', $row['DateOccured']) . "Z</time>\n";
				$trkptdata .= "    <desc><![CDATA[Lat.=$row[Latitude], Long.=$row[Longitude], Alt.=$altitudeM, Speed=$speedKPH" . "km/h, Course=$angle" . "deg.]]></desc>\n";
				$trkptdata .= "   </trkpt>\n";
				$n++;
			}
			$trkptdata .= " </trkseg>\n";
			$trkptdata .= "</trk>\n";

			$header  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
			$header .= "<gpx version=\"1.1\" creator=\"GPX-Exporter by Ulrich Wolf - http://wolf-u.li\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns=\"http://www.topografix.com/GPX/1/1\" xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd\">\n";
			$header .= " <metadata>\n";
			$header .= "  <name>".$this->tripname."</name>\n";
			$header .= "  <desc>GPX-Track of TrackMe</desc>\n";
			$header .= "  <author>\n";
			$header .= "   <name>Ulrich Wolf</name>\n";
			$header .= "   <link href=\"http://wolf-u.li\">\n";
			$header .= "    <text>wolf-u.li</text>\n";
			$header .= "   </link>\n";
			$header .= "  </author>\n";
			$header .= "  <time>".date('Y-m-d')."T".date('H:i:s')."Z</time>\n";
			$header .= "  <keywords><![CDATA[Geocaching,Geotagging,GPS]]></keywords>\n";
			$header .= "  <bounds minlat=\"" . $bounds_lat_min . "\" minlon=\"" . $bounds_lon_min . "\" maxlat=\"" . $bounds_lat_max . "\" maxlon=\"" . $bounds_lon_max . "\"/>\n";
			$header .= " </metadata>\n";

			$footer  = "</gpx>\n";

			return $header.$wptdata.$trkptdata.$footer;
		}
	}

?>
