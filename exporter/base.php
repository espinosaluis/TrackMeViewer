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

	class Exporter {
		public function __construct($db, $userid, $tripid, $datefrom, $dateto) {
			$this->db = $db;
			$this->datefrom = $datefrom;
			$this->dateto = $dateto;
			if (is_null($tripid)) {
				if (is_null($userid)) {
					throw new InvalidArgumentException("userid cannot be null when tripid is null");
				}
				$this->tripname = "None";
			} elseif ($tripid === true) {
				if (is_null($userid)) {
					throw new InvalidArgumentException("userid cannot be null when tripid is null");
				}
				$this->tripname = "All";
			} else {
				$row = $this->db->exec_sql("SELECT FK_Users_ID, Name FROM trips WHERE ID=?", $tripid)->fetch();
				if (is_null($userid)) {
					$userid = $row['FK_Users_ID'];
				} elseif ($userid != $row['FK_Users_ID']) {
					throw new InvalidArgumentException("Given user does not own given trip");
				}
				$this->tripname = $row['Name'];
			}
			$this->tripid = $tripid;
			$this->userid = $userid;
			$this->username = $this->db->exec_sql("SELECT username FROM users WHERE ID=?", $this->userid)->fetchColumn();
		}

		protected function exec_sql($ascending) {
			$params = array();
			$cond = " WHERE positions.FK_Users_ID=?";
			if (is_null($this->tripid)) {
				$cond .= " AND positions.FK_Trips_ID is null";
			} elseif ($this->tripid !== true) {
				$cond = " INNER JOIN trips ON positions.FK_Trips_ID = trips.ID AND trips.ID=? $cond";
				$params[] = $this->tripid;
			} else {
				$cond = " LEFT JOIN trips ON positions.FK_Trips_ID = trips.ID $cond";
			}

			$params[] = $this->userid;
			if ($this->datefrom != "") {
				$cond .= " AND DateOccurred>=?";
				$params[] = $this->datefrom;
			}
			if ($this->dateto != "") {
				$cond .= " AND DateOccurred<=?";
				$params[] = $this->dateto;
			}
			$cond .= " ORDER BY DateOccurred ";
			if ($ascending)
				$cond .= "ASC";
			else
				$cond .= "DESC";
			$sql = "SELECT DateOccurred, Latitude, Longitude, Speed, Altitude, FK_Icons_ID, trips.Name, positions.Comments, positions.ImageURL, positions.Angle, positions.SignalStrength, positions.SignalStrengthMax, positions.SignalStrengthMin, positions.BatteryStatus FROM positions";
			return $this->db->exec_sql($sql . $cond, $params);
		}

		protected function simulate_old(&$row) {
			// Old queries didn't use proper capitalisation
			// This is adding the old query's attributes to the new query
			// It can be removed as soon as there is nothing left using the old
			// attributes
			foreach (array("Latitude", "Longitude", "Speed", "Altitude", "Comments", "ImageURL", "Angle", "SignalStrength",
					"SignalStrengthMax", "SignalStrengthMin", "BatteryStatus") as $name)
				$row[strtolower($name)] = $row[$name];
			$row['Tripname'] = $row['Name'];
			$row['Customicon'] = $row['FK_Icons_ID'];
		}

		// Find better home for this function
		private static function get_default($needle, $array, $default = null) {
			return array_key_exists($needle, $array) ? $array[$needle] : $default;
		}

		// TODO: Handle negative tripids, and invalid tripnames.
		// Return "null" for positions without any trip and "true" for all trips
		// otherwise the trip id.
		public static function normalize($db, $userid, $parameters) {
			$tripid = Exporter::get_default("t", $parameters);
			$tripname = Exporter::get_default("tn", $parameters);
			if (!is_null($tripname)) {
				if (!is_null($tripid)) {
					throw new InvalidArgumentException("Either define trip id or name but not both.");
				} else {
					if ($tripname === "<None>") {
						$tripid = null;
					} elseif ($tripname === "") {
						$tripid = true;
					} else {
						$tripid = $db->exec_sql("SELECT ID FROM trips WHERE Name=? AND FK_Users_ID=?", $tripname, $userid)->fetchColumn();
					}
				}
			} elseif (!is_null($tripid)) {
				if ($tripid === "n") {
					$tripid = null;
				} elseif ($tripid === "a") {
					$tripid = true;
				} elseif (ctype_digit($parameters["t"])) {
					$tripid = intval($tripid);
				} else {
					throw new InvalidArgumentException("Trip id must be number.");
				}
			} else {
				throw new InvalidArgumentException("Neither tripname or trip id are defined.");
			}

			return $tripid;
		}

	}
?>
