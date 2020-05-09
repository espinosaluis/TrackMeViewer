<?php
	require_once("base.php");

	class KMLExporter extends Exporter {

		private function create_icon($id, $href, $scaled) {
			$code  = "  <Style id=\"" . $id . "\">\n";
			$code .= "   <IconStyle>\n";
			if ($scaled)
				$code .= "    <scale>0.5</scale>\n";
			$code .= "    <Icon>\n";
			$code .= "     <href>" . $href . "</href>\n";
			if ($scaled) {
				$code .= "     <x>0</x>\n";
				$code .= "     <y>0</y>\n";
				$code .= "     <w>32</w>\n";
				$code .= "     <h>32</h>\n";
			}
			$code .= "    </Icon>\n";
			$code .= "   </IconStyle>\n";
			$code .= "  </Style>\n";
		return $code;
		}

		public function export($showbearings) {
			$currentpath = "http://".$_SERVER['HTTP_HOST'];

			$customicons = "";
			foreach (array("yellow", "green", "red") as $color) {
				$id = "Icon" . ucfirst($color);
				$href = $currentpath . "/mm_20_$color.png";
				$customicons .= $this->create_icon($id, $href, $color === "yellow");
			}

			for ($angle = 0; $angle < 360; $angle += 45) {
				$id = "IconArrow$angle";
				$href = $currentpath . "/arrow$angle.png";
				$customicons .= $this->create_icon($id, $href, true);
			}
			$result = $this->exec_sql(true);
			$result = $result->fetchAll();

			$header  = "<?xml version='1.0' encoding='utf-8'?>\n";
			$header .= "<kml xmlns='http://earth.google.com/kml/2.0'>\n";
			$header .= " <Document>\n";

			$output  = "  <Name>TrackMe Trip with bearings=" . $showbearings . "</Name>\n";
			$output .= "  <NetworkLinkControl>\n";
			$output .= "   <minRefreshPeriod>12</minRefreshPeriod>\n";
			$output .= "  </NetworkLinkControl>\n";

			$group = "";
			$iconIds = array();

			for ($count = 0; $count < count($result); $count++) {
				$row = $result[$count];
				$this->simulate_old($row);
				if ($row['FK_Icons_ID'])
					$iconIds[] = $row['FK_Icons_ID'];
				$speedMPH = number_format($row['Speed']*2.2369362920544,2);
				$speedKPH = number_format($row['Speed']*3.6,2);
				if ($row['Altitude'] > 0) {
					$altitudeFeet = number_format($row['Altitude']*3.2808399,2);
					$altitudeM = number_format($row['Altitude'],2);
				} else {
					$altitudeFeet = number_format(0,2);
					$altitudeM = number_format(0,2);
				}
				$angle = number_format($row['Angle'],2);
				$is_last = ($count == count($result) - 1); // Last pushpin
				$row['UnixDateOccured'] = strtotime($row['DateOccurred']);

				if ($is_last) {
					$output .= "  <LookAt>\n";
					$output .= "   <longitude>".$row['Longitude']."</longitude>\n";
					$output .= "   <latitude>".$row['Latitude']."</latitude>\n";
					$output .= "   <range>1000</range>\n";
					$output .= "   <tilt>60</tilt>\n";
					$output .= "   <heading>0</heading>\n";
					$output .= "  </LookAt>\n";
					$output .= "  <visibility>1</visibility>\n";
					$output .= "  <open>0</open>\n";
				}

				$output .= "  <Placemark>\n";

				$output .= "   <TimeStamp><when>" . strftime("%Y-%m-%dT%TZ", $row['UnixDateOccured']) . "</when></TimeStamp>\n";

				if ($is_last) {
					$name = $row['DateOccurred'];
					if ($row['Tripname'] != "")
						$name = "Trip: $row[Tripname] $name";
				} else {
					$name = $row['Comments'];
				}

				if ($name) {
					$output .= "   <name>$name</name>\n";
				} else
					$output .= "   <name>" . $count . "/" . count($result) . "</name>\n";

				$output .= "   <description>\n";
				$output .= "    <![CDATA[\n";
				$output .= "     User: <b>" . $this->username . "</b><hr>\n";
				$output .= "     <table>\n";
				$output .= "      <tr>\n";
				$output .= "       <td>Time:</td>\n";
				$output .= "       <td>" . $row['DateOccurred'] . "</td>\n";
				$output .= "      </tr>\n";
				$output .= "      <tr>\n";
				$output .= "       <td>Trip:</td>\n";
				$output .= "       <td>" . $row['Tripname'] . "</td>\n";
				$output .= "      </tr>\n";
				$output .= "      <tr>\n";
				$output .= "       <td>Speed [mph - km/h]:</td>\n";
				$output .= "       <td>" . $speedMPH . " - " . $speedKPH . "</td>\n";
				$output .= "      </tr>\n";

				if ($row['Altitude'] < 0) {
					$output .= "      <tr>\n";
					$output .= "       <td>Altitude</td>\n";
					$output .= "       <td>Unknown</td>\n";
					$output .= "      </tr>\n";
				} else {
					$output .= "      <tr>\n";
					$output .= "       <td>Altitude [ft - m]:</td>\n";
					$output .= "       <td>" . $altitudeFeet . " - " . $altitudeM . "</td>\n";
					$output .= "      </tr>\n";
				}
				if ($row['Comments'] != "")
					$output .= "      <tr>\n";
					$output .= "       <td>Comments:</td>\n";
					$output .= "       <td>" . $row['Comments'] . "</td>\n";
					$output .= "      </tr>\n";

				if ($row['SignalStrength'] != "") {
					$output .= "      <tr>\n";
					$output .= "       <td>Signal Strength [dBm]:</td>\n";
					$output .= "       <td>" . $row['SignalStrength'];
					if ($row['SignalStrengthMax'] != "" && $row['SignalStrengthMin'] != "") {
						$range = ($row['SignalStrengthMax'] - $row['SignalStrengthMin'])/5;

						if ($row['SignalStrength'] >= $row['SignalStrengthMax'] - $range)
							$output .= "(Excellent)</td>\n";
						elseif ($row['SignalStrength'] >= $row['SignalStrengthMax'] - $range*2)
							$output .= "(Very Good)</td>\n";
						elseif ($row['SignalStrength'] >= $row['SignalStrengthMax'] - $range*3)
							$output .= "(Good)</td>\n";
						elseif ($row['SignalStrength'] >= $row['SignalStrengthMax'] - $range*4)
							$output .= "(Poor)</td>\n";
						else 
							$output .= "(Very Poor)</td>\n";
					}
					$output .= "      </tr>\n";
				}

				if ($row['BatteryStatus'] != "" ) {
					$output .= "      <tr>\n";
					$output .= "       <td>Battery Status [%]:</td>\n";
					$output .= "       <td>" . $row['BatteryStatus'] . "</td>\n";
					$output .= "      </tr>\n";
				}

				if ($row['ImageURL'] != "" ) {
					$output .= "      <tr>\n";
					$output .= "       <td colspan='2'>\n";
					$output .= "        <a href='" . $row['ImageURL'] . "'><img src='" . $row['ImageURL'] . "' height='200' width='240'></a>\n";
					$output .= "       </td>\n";
					$output .= "      </tr>\n";
				}

				$output .= "     </table>\n";
				$output .= "     <hr><b>TrackMe. Created by Luis Espinosa</b><br>http://www.luisespinosa.com\n";
				$output .= "    ]]>\n";
				$output .= "   </description>\n";

				$icon = "CustomIcon$row[FK_Icons_ID]";
				if ($is_last) {
					if ($row['FK_Icons_ID'] == "")
						$icon = "IconRed";
				} elseif ($count == 0) {
					$icon = "IconGreen";
				} elseif ($row['FK_Icons_ID'] == "") {
					if ($row['Angle'] != "" && $showbearings == "yes") {
						$direction = (int) (($row['Angle'] - 22.5) / 45) % 8;
						$direction *= 45;
						$icon = "IconArrow" . $direction;
					} else {
						$icon = "IconYellow";
					}
				}
				$output .= "   <styleUrl>#" . $icon . "</styleUrl>\n";

				$output .= "   <Point>\n";
				$output .= "    <altitudeMode>clampedToGround</altitudeMode>\n";
				// Add the Altitude to the point - by definition it's metric
				$output .= "    <coordinates>" . $row['Longitude'] . ", " . $row['Latitude'] . ", " . $altitudeM . "</coordinates>\n";
				$output .= "   </Point>\n";

				$output .= "  </Placemark>\n";

				// Since we locked the altitude to the ground only send Lon and Lat to the path
				$group .= $row['Longitude'] . "," . $row['Latitude'] . " ";
			}

			// Draw line
			$output .= "  <Placemark>\n";
			$output .= "   <Style>\n";
			$output .= "    <LineStyle>\n";
			$output .= "     <color>ff0000ff</color>\n";
			$output .= "     <width>5</width>\n";
			$output .= "    </LineStyle>\n";
			$output .= "   </Style>\n";

			$output .= "   <LineString>\n";
			$output .= "    <extrude>1</extrude>\n";
			$output .= "    <tessellate>1</tessellate>\n";
			$output .= "    <altitudeMode>clampedToGround</altitudeMode>\n";
			$output .= "    <coordinates>" . $group . "</coordinates>\n";
			$output .= "   </LineString>\n";
			$output .= "  </Placemark>\n";

			$footer  = " </Document>\n";
			$footer .= "</kml>\n";

			// Generate code for custom icons
			$iconIds = array_unique($iconIds, SORT_NUMERIC);
			if (count($iconIds) > 0) {
				// Wrap icon ids in an array as it should add the array itself as a parameter, not each value
				$params = implode(',', array_fill(0, count($iconIds), '?'));
				$result = $this->db->exec_sql("SELECT ID, URL FROM icons WHERE ID IN ($params)", $iconIds);
				while( $row = $result->fetch() ) {
					$customicons .= $this->create_icon("CustomIcon" . $row['ID'], $row['URL'], false);
				}
			}

			return $header.$customicons.$output.$footer;
		}
	}

?>
