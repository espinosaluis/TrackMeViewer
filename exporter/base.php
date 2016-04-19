<?php

    class Exporter
    {

        public function __construct($db, $userid, $tripid, $datefrom, $dateto)
        {
            $this->db = $db;
            $this->datefrom = $datefrom;
            $this->dateto = $dateto;
            if (is_null($tripid)) {
                if (is_null($userid)) {
                    throw new InvalidArgumentException("userid cannot be null when tripid is null");
                }
                $this->tripname = "None";
            } else if ($tripid === true) {
                if (is_null($userid)) {
                    throw new InvalidArgumentException("userid cannot be null when tripid is null");
                }
                $this->tripname = "All";
            } else {
                $row = $this->db->exec_sql("SELECT `FK_Users_ID`, `Name` FROM `trips` WHERE `ID` = ?",
                                           $tripid)->fetch();
                if (is_null($userid)) {
                    $userid = $row["FK_Users_ID"];
                } else if ($userid != $row["FK_Users_ID"]) {
                    throw new InvalidArgumentException("Given user does not own given trip");
                }
                $this->tripname = $row["Name"];
            }
            $this->tripid = $tripid;
            $this->userid = $userid;
            $this->username = $this->db->exec_sql("SELECT `username` FROM `users` WHERE `ID` = ?",
                                                  $this->userid)->fetchColumn();
        }

        // Find better home for this function
        private static function get_default($needle, $array, $default = null)
        {
            return array_key_exists($needle, $array) ? $array[$needle] : $default;
        }

        // TODO: Handle negative tripids, and invalid tripnames.
        // Return "null" for positions without any trip and "true" for all trips
        // otherwise the trip id.
        public static function normalize($db, $userid, $parameters)
        {
            $tripid = Exporter::get_default("t", $parameters);
            $tripname = Exporter::get_default("tn", $parameters);
            if (!is_null($tripname)) {
                if (!is_null($tripid)) {
                    throw new InvalidArgumentException("Either define trip id or name but not both.");
                } else {
                    if ($tripname === "<None>") {
                        $tripid = null;
                    } else if ($tripname === "") {
                        $tripid = true;
                    } else {
                        $tripid = $db->exec_sql("SELECT `ID` FROM `trips` WHERE `Name` = ? AND `FK_Users_ID` = ?",
                                                $tripname, $userid)->fetchColumn();
                    }
                }
            } else if (!is_null($tripid)) {
                if ($tripid === "n") {
                    $tripid = null;
                } else if ($tripid === "a") {
                    $tripid = true;
                } else if (ctype_digit($parameters["t"])) {
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
