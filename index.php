<?php
	session_start();

	require_once("config.php");
	require_once("database.php");
	require_once("tileprovider.php");

	if (dirname($_SERVER['PHP_SELF'])=="/") {
		$siteroot ="http://" . $_SERVER['HTTP_HOST'] . ":" .  $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
		$siteroot ="http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	} else {
		$siteroot ="http://" . $_SERVER['HTTP_HOST'] . ":" .  $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
		$siteroot ="http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
	}

	$html = "";
	(isset($_REQUEST["action"])) ? $action = $_REQUEST["action"] : $action = null;
	(isset($_REQUEST["livetracking"])) ? $livetracking = $_REQUEST["livetracking"] === "yes" : $livetracking = null;

	if (isset($_COOKIE["showbearings"])) $showbearings = $_COOKIE["showbearings"];
	if (isset($_COOKIE["crosshair"]))    $crosshair    = $_COOKIE["crosshair"];
	if (isset($_COOKIE["clickcenter"]))  $clickcenter  = $_COOKIE["clickcenter"];
	if (isset($_COOKIE["language"]))     $language     = $_COOKIE["language"];
	if (isset($_COOKIE["units"]))        $units        = $_COOKIE["units"];
	if (isset($_COOKIE["tileprovider"])) $tileprovider = $_COOKIE["tileprovider"];
	if (isset($_COOKIE["tilePT"]))       $tilePT       = $_COOKIE["tilePT"];

	if ($action == "form_options") {
		(isset($_REQUEST["setshowbearings"])) ? (($_REQUEST["setshowbearings"] == "on") ? $showbearings = "yes" : $showbearings = "no") : $showbearings = "no";
		(isset($_REQUEST["setcrosshair"]))    ? (($_REQUEST["setcrosshair"] == "on")    ? $crosshair    = "yes" : $crosshair    = "no") : $crosshair    = "no";
		(isset($_REQUEST["setclickcenter"]))  ? (($_REQUEST["setclickcenter"] == "on")  ? $clickcenter  = "yes" : $clickcenter  = "no") : $clickcenter  = "no";
		                                                                                  $language     = $_REQUEST["setlanguage"];
		                                                                                  $units        = $_REQUEST["setunits"];
		                                                                                  $tileprovider = $_REQUEST["settileprovider"];
		(isset($_REQUEST["settilePT"]))       ? (($_REQUEST["settilePT"] == "on")       ? $tilePT       = "yes" : $tilePT       = "no") : $tilePT       = "no";
	}
	setcookie("showbearings", $showbearings, 2147483647, "/");
	setcookie("crosshair",    $crosshair,    2147483647, "/");
	setcookie("clickcenter",  $clickcenter,  2147483647, "/");
	setcookie("language",     $language,     2147483647, "/");
	setcookie("units",        $units,        2147483647, "/");
	setcookie("tileprovider", $tileprovider, 2147483647, "/");
	setcookie("tilePT",       $tilePT,       2147483647, "/");

	require_once("language.php");

	if (!isset($tileproviders[$tileprovider])) {
		$tileprovider = "OSM (OpenStreetMap)";
	}

	try {
		$db = connect();
	} catch (PDOException $e) {
		$db = null;
		$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
		$html .= " <head>\n";
		$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
		$html .= "  <link rel=\"shortcut icon\" href=\"images/favicon.ico\">\n";
		$html .= "  <title>$title_text (v" . $version_text . ")</title>\n";
		$html .= " </head>\n";
		$html .= " <body>\n";
		$html .= "  <div align=center>\n";
		$html .= "   $database_fail_text<br>\n";
		$html .= "   <br>\n";
		$html .= "   " . $e->getMessage() . "<br>\n";
		$html .= "   <br>\n";
		$html .= "   <br>\n";
		$html .= "   <br>\n";
		$html .= "   <br>\n";
	}

//	Delete trip
	if (isset($_GET["deleteTrip"]) && is_numeric($_GET["deleteTrip"])) {
		if (isset($_SESSION["ID"])) {
			$tripId = (int)$_GET["deleteTrip"];
			$trip = $db->exec_sql("SELECT FK_Users_ID FROM trips WHERE ID=?", $tripId)->fetch();
			if ($trip === false)
				$err = $lang["delete-trip-wrong-id"];
			elseif ($trip["FK_Users_ID"] !== $_SESSION["ID"])
				$err = $lang["delete-trip-not-owner"];
			else
				$err = false;
		} else {
			$err = $lang["delete-trip-no-login"];
		}

		if ($err === false) {
			try {
				$db->beginTransaction();
				$db->exec_sql("DELETE FROM trips WHERE ID=?", $tripId);
				$db->exec_sql("DELETE FROM positions WHERE FK_Trips_ID=?", $tripId);
				$db->commit();
			} catch (Exception $e) {
				$db->rollback();
				$err = $e->getMessage();
			}
		}
		if ($err === false) {
			header('Location: '.$siteroot);
			$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
			$html .= " <head>\n";
			$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
			$html .= "  <link rel=\"shortcut icon\" href=\"images/favicon.ico\">\n";
			$html .= "  <title>" . $title_text . "(v" . $version_text . ")</title>\n";
			$html .= " </head>\n";
			$html .= " <body>\n";
		} else {
			$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
			$html .= " <head>\n";
			$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
			$html .= "  <link rel=\"shortcut icon\" href=\"images/favicon.ico\">\n";
			$html .= "  <title>" . $title_text . "(v" . $version_text . ")</title>\n";
			$html .= " </head>\n";
			$html .= " <body>\n";
			$html .= "  <div align=center>\n";
			$html .= "   " . $lang["delete-trip-title"] . "<br>\n";
			$html .= "   <br>\n";
			$html .= "   $err<br>\n";
			$html .= "   <br>\n";
			$html .= "   <br>\n";
			$html .= "   <br>\n";
			$html .= "   <br>\n";
		}
		$db = null;
	}

	if (!is_null($db)) {
		$num_users = $db->get_count("users");
		$num_trips = $db->get_count("trips");
		$num_positions = $db->get_count("positions");
		$num_icons = $db->get_count("icons");

		(isset($_REQUEST["setfilter"])) ? $filter   = $_REQUEST["setfilter"] : $filter   = null;
		(isset($_REQUEST["trip"]))      ? $trip     = $_REQUEST["trip"]      : $trip     = null;
		(isset($_REQUEST["ID"]))        ? $ID       = $_REQUEST["ID"]        : $ID       = null;
		(isset($_REQUEST["username"]))  ? $username = $_REQUEST["username"]  : $username = null;
		(isset($_REQUEST["password"]))  ? $password = $_REQUEST["password"]  : $password = null;
		(isset($_REQUEST["startday"]))  ? $startday = $_REQUEST["startday"]  : $startday = null;
		(isset($_REQUEST["endday"]))    ? $endday   = $_REQUEST["endday"]    : $endday   = null;

		$trip     = preg_replace("/[^a-zA-Z0-9]/", "", $trip);
		$startday = preg_replace("/[^0-9 :\-]/", "", $startday);
		$endday   = preg_replace("/[^0-9 :\-]/", "", $endday);

		if (is_numeric($trip) && !isset($ID)) {
			$ID = $db->exec_sql("SELECT FK_Users_ID FROM trips WHERE ID=?", $trip)->fetchColumn();
			if ($ID === false)
				unset($ID);
		}

		if ($num_users < 1 || $num_trips < 1 || $num_positions < 1 || $num_icons < 1) {
			$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
			$html .= " <head>\n";
			$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
			$html .= "  <link rel=\"shortcut icon\" href=\"images/favicon.ico\">\n";
			$html .= "  <title>$title_text (v" . $version_text . ")</title>\n";
			$html .= " </head>\n";
			$html .= " <body>\n";
			$html .= "  <div align=center>\n";
			$html .= "   $no_data_text<br>\n";
			$html .= "   <br>\n";
		} elseif (file_exists("install.php") || file_exists("database.sql")) {
			$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
			$html .= " <head>\n";
			$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
			$html .= "  <link rel=\"shortcut icon\" href=\"images/favicon.ico\">\n";
			$html .= "  <title>$title_text (v" . $version_text . ")</title>\n";
			$html .= " </head>\n";
			$html .= " <body>\n";
			$html .= "  <div align=center>\n";
			$html .= "   $incomplete_install_text<br>\n";
			$html .= "   <br>\n";
		} else {
			if ($publicpage != "yes") {
				if ($action == "logout") {
					unset($_SESSION["ID"]);
				}
				if (isset($username) && isset($password)) {
					if (preg_match("/^([a-zA-Z0-9._])+$/", "$_REQUEST[username]")) {
						$login_id = $db->valid_login($username, $password);
						if ($login_id >= 0) {
							$_SESSION["ID"] = $login_id;
						}
					}
				}
				$ID = $_SESSION["ID"];
			}

			if (isset($_SESSION["ID"]) || $publicpage == "yes") {
				$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
				$html .= " <head>\n";
				$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">\n";
				$html .= "  <link rel=\"shortcut icon\" href=\"images/favicon.ico\">\n";
				$html .= "  <title>$title_text (v" . $version_text . ")</title>\n";
				$html .= "  <link rel=\"stylesheet\" href=\"layout.css?v=" . @filemtime("layout.css") . "\" type=\"text/css\">\n";
				$html .= "  <link rel=\"stylesheet\" href=\"https://unpkg.com/leaflet@1.6.0/dist/leaflet.css\" integrity=\"sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==\" crossorigin=\"\"/>\n";
				$html .= "  <!-- Make sure you put this AFTER Leaflet's CSS -->\n";
//	for debugging us this	$html .= "  <script type=\"text/javascript\" src=\"https://unpkg.com/leaflet@1.6.0/dist/leaflet-src.js\" integrity=\"sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==\" crossorigin=\"\"></script>\n";
				$html .= "  <script type=\"text/javascript\" src=\"https://unpkg.com/leaflet@1.6.0/dist/leaflet.js\" crossorigin=\"\"></script>\n";
				$html .= "  <script type=\"text/javascript\" src=\"main.js?v=" . @filemtime("main.js") . "\"></script>\n";
				$html .= "  <script type=\"text/javascript\" src=\"lang.js?v=" . @filemtime("lang.js") . "\"></script>\n";
				$html .= " </head>\n";

				if ($livetracking) {
					$html .= " <body onload=\"initInterval();\">\n";
				} else {
					$html .= " <body>\n";
				}

				$html .= "  <script type=\"text/javascript\">\n";
				$deleteTripConfirm = escapeJSString($lang["delete-trip-confirm"]);
				$deleteTripSelect = escapeJSString($lang["delete-trip-select"]);
				$liveMinInterval = escapeJSString($lang["live-min-interval"]);
				$html .= "   function getValue(varname) {\n";
				$html .= "    var url = window.location.href;\n";
				$html .= "    var qparts = url.split(\"?\");\n";
				$html .= "    if (qparts.length == 0) {\n";
				$html .= "     return \"\";\n";
				$html .= "    }\n";
				$html .= "    var queryparts = qparts[1];\n";
				$html .= "    var vars = queryparts.split(\"&\");\n";
				$html .= "    var value = \"\";\n";
				$html .= "    for (i=0;i<vars.length;i++) {\n";
				$html .= "     var parts = vars[i].split(\"=\");\n";
				$html .= "     if (parts[0] == varname) {\n";
				$html .= "      value = parts[1];\n";
				$html .= "      break;\n";
				$html .= "     }\n";
				$html .= "    }\n";
				$html .= "    value = unescape(value);\n";
				$html .= "    value.replace(/\+/g,\" \");\n";
				$html .= "    return value;\n";
				$html .= "   }\n";

				$html .= "   function showInfo() {\n";
				$html .= "    var elem = document.getElementById('configsection');\n";
				$html .= "    var backdrop = document.getElementById('configbackdrop');\n";
				$html .= "    if (elem.style.display == \"none\") {\n";
				$html .= "     elem.style.display=\"block\";\n";
				$html .= "     if (backdrop) backdrop.style.display=\"block\";\n";
				$html .= "     document.getElementById(\"showcfgbutton\").value = \"$showconfig_button_text_off\";\n";
				$html .= "    } else {\n";
				$html .= "     elem.style.display=\"none\";\n";
				$html .= "     if (backdrop) backdrop.style.display=\"none\";\n";
				$html .= "     document.getElementById(\"showcfgbutton\").value = \"$showconfig_button_text\";\n";
				$html .= "    }\n";
				$html .= "   }\n";

				$html .= "   function liveTrack() {\n";
				$html .= "    if (document.getElementById(\"livebutton\").value == \"$location_button_text\") {\n";
				$html .= "     location=\"index.php?livetracking=yes&interval=60&zoomlevel=8\";\n";
				$html .= "    } else {\n";
				$html .= "     location=\"index.php\";\n";
				$html .= "    }\n";
				$html .= "   }\n";

				$html .= "   function submitTrip() {\n";
				$html .= "    document.form_trip.submit();\n";
				$html .= "   }\n";

				$html .= "   function deleteTrip() {\n";
				$html .= "    var selTrip = document.getElementById('selTrip').value;\n";
				$html .= "    if (selTrip != \"$trip_none_text\" && selTrip != \"$trip_any_text\") {\n";
				$html .= "     if (confirm('" . $deleteTripConfirm . "')) {\n";
				$html .= "      var url = document.location.protocol +'//'+ document.location.hostname + ':' + document.location.port + document.location.pathname + '?deleteTrip='+selTrip;\n";
				$html .= "      window.location.href = url;\n";
				$html .= "     }\n";
				$html .= "    } else {\n";
				$html .= "     alert('" . $deleteTripSelect . "');\n";
				$html .= "    }\n";
				$html .= "   }\n";

				$html .= "   function dateTimeLocalToServer(value) {\n";
				$html .= "    if (!value) {\n";
				$html .= "     return \"\";\n";
				$html .= "    }\n";
				$html .= "    if (value.length === 16) {\n";
				$html .= "     value += \":00\";\n";
				$html .= "    }\n";
				$html .= "    return value.replace(\"T\", \" \");\n";
				$html .= "   }\n";

				$html .= "   function serverToDateTimeLocal(value) {\n";
				$html .= "    if (!value) {\n";
				$html .= "     return \"\";\n";
				$html .= "    }\n";
				$html .= "    return value.replace(\" \", \"T\").slice(0, 16);\n";
				$html .= "   }\n";

				$html .= "   function syncFilterDateInputs() {\n";
				$html .= "    var startHidden = document.getElementById(\"startday\");\n";
				$html .= "    var endHidden = document.getElementById(\"endday\");\n";
				$html .= "    var startDisplay = document.getElementById(\"startday_display\");\n";
				$html .= "    var endDisplay = document.getElementById(\"endday_display\");\n";
				$html .= "    if (!startHidden || !endHidden || !startDisplay || !endDisplay) {\n";
				$html .= "     return;\n";
				$html .= "    }\n";
				$html .= "    startDisplay.value = serverToDateTimeLocal(startHidden.value);\n";
				$html .= "    endDisplay.value = serverToDateTimeLocal(endHidden.value);\n";
				$html .= "    var form = document.forms['form_filter'];\n";
				$html .= "    if (form) {\n";
				$html .= "     form.addEventListener('submit', function() {\n";
				$html .= "      startHidden.value = dateTimeLocalToServer(startDisplay.value);\n";
				$html .= "      endHidden.value = dateTimeLocalToServer(endDisplay.value);\n";
				$html .= "     });\n";
				$html .= "    }\n";
				$html .= "   }\n";

				$html .= "  </script>\n";

				$html .= "  <div id=\"navigationsection\">\n";
				$html .= "   <div class=\"navpanel\">\n";

				if ($publicpage == "yes") {
					$html .= "   <div class=\"panelcard panelcard-user\">\n";
					$html .= "   <form name=\"form_user\" method=\"post\">\n";
					$html .= "    =\"ID\" class=\"pulldown\">\n";
					$findusers = $db->exec_sql("SELECT * FROM users ORDER BY Username");
					while ($founduser = $findusers->fetch()) {
						if (!isset($ID)) {
							$ID = $founduser["ID"];
							$trip = "";
						}
						if ($founduser["ID"] == $ID) {
							$html .= "     <option value=\"$founduser[ID]\" selected>$founduser[username]</option>\n";
							$username = $founduser["username"];
						} else {
							$html .= "     <option value=\"$founduser[ID]\">$founduser[username]</option>\n";
						}
					}
					$html .= "    </select>\n";
					$html .= "    <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
					$html .= "    <input type=\"hidden\" name=\"trip\" value=\"\">\n";
					$html .= "    <input type=\"submit\" class=\"button\" id=\"userbutton\" value=\"$user_button_text\">\n";
					$html .= "   </form>\n";
					$html .= "   </div>\n";
				} else {
					$finduser = $db->exec_sql("SELECT * FROM users WHERE ID=? LIMIT 1", $ID);
					$founduser = $finduser->fetch();
					$username = $founduser['username'];
					$html .= "   <div class=\"panelcard panelcard-user\">\n";
					$html .= "    <div class=\"paneltitle\">$trip_data</div>\n";
					$html .= "   <form name=\"form_logout\" method=\"post\">\n";
					$html .= "    <div class=\"userrow\">\n";
					$html .= "     <span class=\"usernamechip\">" . $founduser["username"] . "</span>\n";
					$html .= "    <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
					$html .= "    <input type=\"hidden\" name=\"action\" value=\"logout\">\n";
					$html .= "    <input type=\"submit\" class=\"buttonshort\" id=\"logoutbutton\" value=\"$logout_button_text\">\n";
					$html .= "    </div>\n";
					$html .= "   </form>\n";
					$html .= "   </div>\n";
				}

				$html .= "   <div class=\"panelcard panelcard-actions\">\n";
				if ($allowcustom == "yes") {
					$html .= "   <input type=\"button\" class=\"button\" id=\"showcfgbutton\" value=\"$showconfig_button_text\" onClick=\"showInfo();\" >\n";
				}

				if ($livetracking) {
					$html .= "   <input type=\"button\" class=\"button\" id=\"livebutton\" value=\"$location_button_text_off\" onClick=\"liveTrack();\">\n";
				} else {
					$html .= "   <input type=\"button\" class=\"button\" id=\"livebutton\" value=\"$location_button_text\" onClick=\"liveTrack();\">\n";
				}
				$html .= "   </div>\n";

				$tripname = $trip_none_text;
				if ($livetracking) {
				} else {
					$findtrips = $db->exec_sql("SELECT A1.*, (SELECT MIN(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Startdate FROM trips A1 WHERE A1.FK_Users_ID=? ORDER BY Startdate DESC ", $ID);
					$foundTrips = array(array("ID" => "None", "Name" => $trip_none_text, "FK_Users_ID" => null),
						array("ID" => "Any", "Name" => $trip_any_text, "FK_Users_ID" => null));
					$foundTrips = array_merge($foundTrips, $findtrips->fetchAll());
					// In case the selected trip is invalid, default to the first non-generic trip
					// or None if there are only generic trips
					$selectedTrip = $foundTrips[count($foundTrips) > 2 ? 2 : 0];
					foreach ($foundTrips as $foundtrip) {
					if ($foundtrip["ID"] == $trip)
						$selectedTrip = $foundtrip;
					}

					$deleteButton = ($selectedTrip["FK_Users_ID"] === $_SESSION["ID"]);
					$trip = $selectedTrip["ID"];
					$tripname = $selectedTrip["Name"];
				}
				if ($livetracking) {
					$html .= "   <script type=\"text/javascript\">\n";
					$html .= "    function initInterval() {\n";
					$html .= "     if (document.form_intervalclock.interval.value == \"-\") {\n";
					$html .= "      document.form_intervalclock.interval.value = getValue(\"interval\");\n";
					$html .= "     }\n";
					$html .= "     if (document.form_intervalclock.interval.value < 10) {\n";
					$html .= "      alert(\"" . $liveMinInterval . "\");\n";
					$html .= "      t = 60;\n";
					$html .= "      document.form_intervalclock.interval.value = 60;\n";
					$html .= "     } else {\n";
					$html .= "      t = document.form_intervalclock.interval.value;\n";
					$html .= "     }\n";
					$html .= "     k = setTimeout('showClock()', 1000);\n";
					$html .= "    }\n";

					$html .= "    function showClock() {\n";
					$html .= "     t = t - 1;\n";
					$html .= "     if (t == 0) {\n";
					$html .= "      if (document.form_intervalclock.interval.value < 10) {\n";
					$html .= "       alert(\"" . $liveMinInterval . "\");\n";
					$html .= "       t = 60;\n";
					$html .= "       document.form_intervalclock.interval.value = 60;\n";
					$html .= "      } else {\n";
					$html .= "       if (document.form_zoom.zoom.value != \"-\") {\n";
					$html .= "        if (document.form_zoom.zoom.value != zoomlevel) {\n";
					$html .= "         zoomlevel = document.form_zoom.zoom.value;\n";
					$html .= "        }\n";
					$html .= "       }\n";
					$html .= "       window.location.href = (\"index.php?livetracking=yes&interval=\" + document.form_intervalclock.interval.value + \"&zoomlevel=\" + zoomlevel); \n";
					$html .= "      }\n";
					$html .= "     }\n";
					$html .= "     document.form_intervalclock.seconds.value = t;\n";
					$html .= "     k = setTimeout('showClock()', 1000);\n";
					$html .= "    }\n";
					$html .= "   </script>\n";

					$html .= "   <div class=\"panelcard panelcard-live\">\n";
					$html .= "   <div class=\"paneltitle\">$reloadoptions_title</div>\n";
					$html .= "   <table>\n";
					$html .= "    <tr>\n";
					$html .= "     <td align=right>\n";
					$html .= "      <form name=\"form_intervalclock\">\n";
					$html .= "       " . $lang["live-interval"] . "\n";
					$html .= "       <input type=\"text\" class=\"intervalinputfield\" name=\"interval\" value=\"-\" size=\"1\">\n";
					$html .= "       " . $lang["live-seconds"] . "\n";
					$html .= "       <input type=\"button\" class=\"intervalbutton\" name=\"start\" value=\"" . $lang["live-start"] . "\" onClick=\"clearTimeout(k); initInterval();\"><br>\n";
					$html .= "       " . $lang["live-reload"] . "\n";
					$html .= "       <input type=\"text\" class=\"intervalinputfield\" name=\"seconds\" value=\"-\" size=\"1\">\n";
					$html .= "       " . $lang["live-seconds"] . "\n";
					$html .= "       <input type=\"button\" class=\"intervalbutton\" name=\"stop\" value=\"" . $lang["live-stop"] . "\" onClick=\"clearTimeout(k); document.form_intervalclock.seconds.value = document.form_intervalclock.interval.value;\">\n";
					$html .= "       <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
					$html .= "      </form>\n";
					$html .= "     </td>\n";
					$html .= "    </tr>\n";
					$html .= "   </table>\n";
					$html .= "   </div>\n";

					$html .= "   <form name=\"form_zoom\">\n";
					$html .= "   <div class=\"panelcard panelcard-live\">\n";
					$html .= "    <div class=\"paneltitle\">$zoomlevel_title</div>\n";
					$html .= "    <select class=\"pulldown\" name=\"zoom\">\n";
					$html .= "     <option value=\"-\" selected>" . $lang["zoom-choose"] . "\n";
					$html .= "     <option value=3>3\n";
					$html .= "     <option value=4>4\n";
					$html .= "     <option value=5>5\n";
					$html .= "     <option value=6>6\n";
					$html .= "     <option value=7>7\n";
					$html .= "     <option value=8>8\n";
					$html .= "     <option value=9>9\n";
					$html .= "     <option value=10>10\n";
					$html .= "     <option value=11>11\n";
					$html .= "     <option value=12>12\n";
					$html .= "     <option value=13>13\n";
					$html .= "     <option value=14>14\n";
					$html .= "     <option value=15>15\n";
					$html .= "     <option value=16>16\n";
					$html .= "     <option value=17>17\n";
					$html .= "     <option value=18>18\n";
					$html .= "    </select>\n";
					$html .= "   </div>\n";
					$html .= "   </form>\n";
				}

				if ($livetracking) {
				} else {
					$html .= "   <div class=\"panelcard panelcard-filter\">\n";
					$html .= "   <form name=\"form_filter\" method=\"post\">\n";
					$html .= "    <div class=\"paneltitle\">$filter_title</div>\n";
					$html .= "    <select name=\"setfilter\" class=\"pulldown\">\n";
					if ($filter == "Photo") {
						$html .= "     <option value=\"None\">$filter_none_text</option>\n";
						$html .= "     <option value=\"Photo\" selected>$filter_photo_text</option>\n";
						$html .= "     <option value=\"Comment\">$filter_comment_text</option>\n";
						$html .= "     <option value=\"PhotoComment\">$filter_photo_comment_text</option>\n";
						$html .= "     <option value=\"Last20\">$filter_last_20</option>\n";
					} elseif ($filter == "Comment") {
						$html .= "     <option value=\"None\">$filter_none_text</option>\n";
						$html .= "     <option value=\"Photo\">$filter_photo_text</option>\n";
						$html .= "     <option value=\"Comment\" selected>$filter_comment_text</option>\n";
						$html .= "     <option value=\"PhotoComment\">$filter_photo_comment_text</option>\n";
						$html .= "     <option value=\"Last20\">$filter_last_20</option>\n";
					} elseif ($filter == "PhotoComment") {
						$html .= "     <option value=\"None\">$filter_none_text</option>\n";
						$html .= "     <option value=\"Photo\">$filter_photo_text</option>\n";
						$html .= "     <option value=\"Comment\">$filter_comment_text</option>\n";
						$html .= "     <option value=\"PhotoComment\" selected>$filter_photo_comment_text</option>\n";
						$html .= "     <option value=\"Last20\">$filter_last_20</option>\n";
					} elseif ($filter == "Last20") {
						$html .= "     <option value=\"None\">$filter_none_text</option>\n";
						$html .= "     <option value=\"Photo\">$filter_photo_text</option>\n";
						$html .= "     <option value=\"Comment\">$filter_comment_text</option>\n";
						$html .= "     <option value=\"PhotoComment\">$filter_photo_comment_text</option>\n";
						$html .= "     <option value=\"Last20\" selected>$filter_last_20</option>\n";
					} else {
						$html .= "     <option value=\"None\" selected>$filter_none_text</option>\n";
						$html .= "     <option value=\"Photo\">$filter_photo_text</option>\n";
						$html .= "     <option value=\"Comment\">$filter_comment_text</option>\n";
						$html .= "     <option value=\"PhotoComment\">$filter_photo_comment_text</option>\n";
						$html .= "     <option value=\"Last20\">$filter_last_20</option>\n";
					}
					$html .= "    </select>\n";
					$html .= "    <div class=\"filterdategrid\">\n";
					$html .= "     <div class=\"filterdatefield\">\n";
					$html .= "      <div class=\"paneltitle paneltitle-sub\">$startdate_text</div>\n";
					$html .= "      <input type=\"datetime-local\" class=\"textinputfield datetimeinputfield\" id=\"startday_display\" value=\"\">\n";
					$html .= "      <input type=\"hidden\" id=\"startday\" name=\"startday\" value=\"$startday\">\n";
					$html .= "     </div>\n";
					$html .= "     <div class=\"filterdatefield\">\n";
					$html .= "      <div class=\"paneltitle paneltitle-sub\">$enddate_text</div>\n";
					$html .= "      <input type=\"datetime-local\" class=\"textinputfield datetimeinputfield\" id=\"endday_display\" value=\"\">\n";
					$html .= "      <input type=\"hidden\" id=\"endday\" name=\"endday\" value=\"$endday\">\n";
					$html .= "     </div>\n";
					$html .= "    </div>\n";
				}

				$params = array($ID);
				if ($livetracking) {
					$limit = "DESC LIMIT 1";
					$where = "";
				} else {
					$limit = "";
					if ($tripname == $trip_none_text)
						$where = " AND FK_Trips_ID is NULL";
					elseif ($tripname == $trip_any_text)
					{
						$where = "";
					} else {
						$where = " AND FK_Trips_ID=?";
						$params[] = $trip;
					}

					// if startday is not null or not blank, then use start and end day to limit search
					if ($startday != null && trim($startday) != "") {
						$where .= " AND DateOccurred BETWEEN ? AND ?";
						$params[] = $startday;
						$params[] = $endday;
					}
				}

				$result = $db->exec_sql("SELECT * FROM positions " .
					"WHERE FK_Users_ID=? $where " .
					"ORDER BY DateOccurred $limit", $params);

				$rounds   = 1;
				$leg_time = 0;
				$pcount   = 0;
				$ccount   = 0;

				while ($row = $result->fetch()) {
					$mph    = $row['Speed'] * 2.2369362920544;
					$kph    = $row['Speed'] * 3.6;
					$ft     = $row['Altitude'] * 3.2808399;
					$meters = $row['Altitude'];
					if ($row['ImageURL'] != '')
						$pcount++;
					if ($row['Comments'] != '')
						$ccount++;

					$endday = $row['DateOccurred'];
					if ($rounds == 1) {
						$total_time         = 0;
						$display_total_time = gmdate("H:i:s", $total_time);
						$total_miles        = 0;
						$total_kilometers   = 0;
						$startday           = $endday;
					} else {
						$leg_miles          = calcDistance($row['Latitude'], $row['Longitude'], $holdlat, $holdlong, "m");
						$total_miles        = $total_miles + $leg_miles;
						$total_kilometers   = $total_miles * 1.609344;
						$leg_time           = $row['DateOccurred'];
						$total_time         = getElapsedTime($startday, $leg_time);
						$display_total_time = gmdate("H:i:s", $total_time);
					}
					$rounds++;
					$holdlat  = $row['Latitude'];
					$holdlong = $row['Longitude'];
				}

				if ($livetracking) {
				} else {
					$html .= "    <input type=\"submit\" class=\"button\" name=\"filter_data\" value=\"$filter_button_text\">\n";
					$html .= "    <script type=\"text/javascript\">\n";
					$html .= "     syncFilterDateInputs();\n";
					$html .= "    </script>\n";
					$html .= "    <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
					$html .= "    <input type=\"hidden\" name=\"trip\" value=\"$trip\">\n";
					$html .= "   </form>\n";
					$html .= "   </div>\n";
				}

				if ($livetracking) {
					if ($units == "metric") {
						$html .= "   <b>$speed_balloon_text: </b>" . number_format($kph,2) . " $speed_metric_unit_balloon_text<br>\n";
						$html .= "   <b>$altitude_balloon_text: </b>" . number_format($meters,2) . " $height_metric_unit_balloon_text</br>\n";
						$html .= "   <b>$total_distance_balloon_text: </b>" . number_format($total_kilometers,2) . " $distance_metric_unit_balloon_text\n";
					} else {
						$html .= "   <b>$speed_balloon_text: </b>" . number_format($mph,2) . " $speed_imperial_unit_balloon_text<br>\n";
						$html .= "   <b>$altitude_balloon_text: </b>" . number_format($ft,2) . " $height_imperial_unit_balloon_text<br>\n";
						$html .= "   <b>$total_distance_balloon_text: </b>" . number_format($total_miles,2) . " $distance_imperial_unit_balloon_text\n";
					}
				} else {
					$routeContextName = ($tripname == $trip_none_text || $tripname == $trip_any_text) ? $lang["route-all"] : $tripname;
					$html .= "   <div class=\"panelcard panelcard-summary\">\n";
					$html .= "    <div class=\"paneltitle\">$summary_title</div>\n";
					$html .= "   <form name=\"form_trip\" method=\"post\">\n";
					$html .= "    <select id=\"selTrip\" name=\"trip\" class=\"pulldown\" onchange=\"submitTrip();\" >\n";
					foreach ($foundTrips as $foundtrip) {
						if ($foundtrip["ID"] == $trip) {
							$html .= "     <option value=\"$foundtrip[ID]\" selected>$foundtrip[Name]</option>\n";
						} else {
							$html .= "     <option value=\"$foundtrip[ID]\">$foundtrip[Name]</option>\n";
						}
					}
					$html .= "    </select>\n";
					if ($deleteButton)
						$html .= "    <input type=\"button\" class=\"deletebutton\" value=\"" . $lang["delete-button"] . "\" onclick=\"deleteTrip();\">\n";
					$html .= "    <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
					$html .= "   </form>\n";
					$html .= "    <div class=\"routehero routehero-inline\">\n";
					$html .= "     <div class=\"routehero-name\">" . $routeContextName . "</div>\n";
					$html .= "     <div class=\"routehero-range\">$startday " . $lang["date-range-separator"] . " $endday</div>\n";
					$html .= "    </div>\n";
					if ($units == "metric") {
						$html .= "    <div class=\"summaryhero\"><div class=\"summaryhero-label\">$total_distance_balloon_text</div><div class=\"summaryhero-value\">" . number_format($total_kilometers,2) . " <span class=\"summaryhero-unit\">$distance_metric_unit_balloon_text</span></div></div>\n";
					} else {
						$html .= "    <div class=\"summaryhero\"><div class=\"summaryhero-label\">$total_distance_balloon_text</div><div class=\"summaryhero-value\">" . number_format($total_miles,2) . " <span class=\"summaryhero-unit\">$distance_imperial_unit_balloon_text</span></div></div>\n";
					}
					$html .= "    <div class=\"statsgrid statsgrid-compact\">\n";
					$html .= "    <div class=\"statline\"><span class=\"statlabel\">$summary_time</span><span class=\"statvalue\">$display_total_time</span></div>\n";
					$html .= "    <div class=\"statline\"><span class=\"statlabel\">$summary_photos</span><span class=\"statvalue\">$pcount</span></div>\n";
					$html .= "    <div class=\"statline\"><span class=\"statlabel\">$summary_comments</span><span class=\"statvalue\">$ccount</span></div>\n";
					$html .= "    </div>\n";
					$html .= "   </div>\n";

					// 2009-05-07 DMR Add Link to download the currently displayed data. -->
					$html .= "   <div class=\"panelcard panelcard-download\">\n";
					$html .= "    <div class=\"paneltitle\">$downloadtrip_title</div>\n";

					// Required Params
					// Removing from Export code                           $ExportOptions = "&db=1234567";
					// Use the Cookie so we don't display it in the URL    $ExportOptions .= "&u=" . $username;
					// Use the Cookie so we don't display it in the URL    $ExportOptions .= "&p=" . $password;
					$ExportOptions  = "&df=" . $startday;
					$ExportOptions .= "&dt=" . $endday;
					$ExportOptions .= "&tn=" . $tripname;
					$ExportOptions .= "&sb=" . $showbearings; //0=no 1=Yes
					$html .= "   <form name=\"form_download\" method=\"post\" action=\"download.php?a=kml" . $ExportOptions . "\">\n";
					$html .= "    <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
					$html .= "    <input type=\"submit\" class=\"button\" id=\"downloadkml\" value=\"" . $lang["download-kml"] . "\">\n";
					$html .= "   </form>\n";
					$html .= "   <form name=\"form_download\" method=\"post\" action=\"download.php?a=gpx" . $ExportOptions . "\">\n";
					$html .= "    <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
					$html .= "    <input type=\"submit\" class=\"button\" id=\"downloadgpx\" value=\"" . $lang["download-gpx"] . "\">\n";
					$html .= "   </form>\n";
					$html .= "   </div>\n";
					// 2009-05-07 DMR Add Link to download the currently displayed data. <--

				}

				$html .= "   </div>\n";
				$html .= "  </div>\n";

				$html .= "  <div id=\"configbackdrop\" style=\"display:none;\" onclick=\"showInfo();\"></div>\n";
				$html .= "  <div id=\"configsection\" style=\"display:none;\">\n";
				$html .= "   <div class=\"configtitle\">$display_options_title_text</div>\n";
				$html .= "   <div class=\"configintro\">" . $lang["config-intro"] . "</div>\n";
				$html .= "   <form name=\"form_options\" method=\"post\" class=\"configform\">\n";
				$html .= "    <input type=\"hidden\" name=\"action\" value=\"form_options\">\n";
				if ($showbearings == "yes") {
					$html .= "    <label class=\"configtoggle\"><span class=\"configtogglebox\"><input type=\"checkbox\" name=\"setshowbearings\" checked></span><span class=\"configtogglelabel\">$display_showbearing_text</span></label>\n";
				} else {
					$html .= "    <label class=\"configtoggle\"><span class=\"configtogglebox\"><input type=\"checkbox\" name=\"setshowbearings\"></span><span class=\"configtogglelabel\">$display_showbearing_text</span></label>\n";
				}

				if ($crosshair == "yes") {
					$html .= "    <label class=\"configtoggle\"><span class=\"configtogglebox\"><input type=\"checkbox\" name=\"setcrosshair\" checked></span><span class=\"configtogglelabel\">$display_crosshair_text</span></label>\n";
				} else {
					$html .= "    <label class=\"configtoggle\"><span class=\"configtogglebox\"><input type=\"checkbox\" name=\"setcrosshair\"></span><span class=\"configtogglelabel\">$display_crosshair_text</span></label>\n";
				}

				if ($clickcenter == "yes") {
					$html .= "    <label class=\"configtoggle\"><span class=\"configtogglebox\"><input type=\"checkbox\" name=\"setclickcenter\" checked></span><span class=\"configtogglelabel\">$display_clickcenter_text</span></label>\n";
				} else {
					$html .= "    <label class=\"configtoggle\"><span class=\"configtogglebox\"><input type=\"checkbox\" name=\"setclickcenter\"></span><span class=\"configtogglelabel\">$display_clickcenter_text</span></label>\n";
				}

				$html .= "    <div class=\"configfield\">\n";
				$html .= "     <label class=\"configlabel\">$display_language_text</label>\n";
				$html .= "    <select name=\"setlanguage\" class=\"pulldown\">\n";
				foreach (array_values($languages) as $lang_entry) {
					if ($lang_entry->en)
						$lang_name = strtolower($lang_entry->en);
					else
						$lang_name = "english";
					if ($language === $lang_name) {
						$html .= "     <option value=\"$lang_name\" selected>$lang_entry->full_name</option>\n";
					} else {
						$html .= "     <option value=\"$lang_name\">$lang_entry->full_name</option>\n";
					}
				}
				$html .= "    </select>\n";
				$html .= "    </div>\n";

				$html .= "    <div class=\"configfield\">\n";
				$html .= "     <label class=\"configlabel\">$display_units_text</label>\n";
				$html .= "    <select name=\"setunits\" class=\"pulldown\">\n";
				$html .= "     <option value=\"imperial\""; if ($units == "imperial") { $html .= " selected"; } $html .= ">Imperial</option>\n";
				$html .= "     <option value=\"metric\""; if ($units == "metric") { $html .= " selected"; } $html .= ">Metric</option>\n";
				$html .= "    </select>\n";
				$html .= "    </div>\n";

				$html .= "    <div class=\"configfield\">\n";
				$html .= "     <label class=\"configlabel\">$display_tileprovider_text</label>\n";
				$html .= "    <select name=\"settileprovider\" class=\"pulldown\">\n";
				foreach ($tileproviders as $tileprovidername => $tileproviderspecs) {
					$html .= "     <option value=\"" . $tileprovidername . "\""; if ($tileprovidername == $tileprovider) { $html .= " selected"; }; $html .= ">" . $tileprovidername . "</option>\n";
				}
				$html .= "    </select>\n";
				$html .= "    </div>\n";

				if ($tilePT == "yes") {
					$html .= "    <label class=\"configtoggle\"><span class=\"configtogglebox\"><input type=\"checkbox\" name=\"settilePT\" checked></span><span class=\"configtogglelabel\">$display_tilePT_text</span></label>\n";
				} else {
					$html .= "    <label class=\"configtoggle\"><span class=\"configtogglebox\"><input type=\"checkbox\" name=\"settilePT\"></span><span class=\"configtogglelabel\">$display_tilePT_text</span></label>\n";
				}

				if ($livetracking) {
					$html .= "    <input type=\"hidden\" name=\"livetracking\" value=\"$location_button_text\">\n";
				}
				if (isset($filter)) {
					$html .= "    <input type=\"hidden\" name=\"filter\" value=\"$filter\">\n";
				}
				$html .= "    <input type=\"hidden\" name=\"trip\" value=\"$trip\">\n";
				$html .= "    <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
				$html .= "    <div class=\"configactions\"><input type=\"submit\" class=\"button\" value=\"$display_button_text\"></div>\n";
				$html .= "   </form>\n";
				$html .= "  </div>\n";

				$html .= "  <div id=\"mapsection\"></div>\n";

				$html .= "  <div id=\"creditsection\">Powered by <a href=\"http://www.luisespinosa.com\">TrackMe (v" . $version_text . ")</a> by <a href=\"https://github.com/espinosaluis/TrackMeViewer\">Luis Espinosa & friends</a></div>\n";

				$html .= "  <script type=\"text/javascript\">\n";
				$html .= "   // Leaflet Map API initialisation\n";
				$html .= "   var map = L.map(\"mapsection\", {center: [0, 0], zoom: 0});\n";
				$html .= "   L.tileLayer('".$tileproviders[$tileprovider]["url"] . "', {\n";
				if (isset($tileproviders[$tileprovider]["maxZoom"]))
					$html .= "     maxZoom: " . $tileproviders[$tileprovider]["maxZoom"] . ",\n";
				$html .= "     attribution: '" . $tileproviders[$tileprovider]["attribution"] . "'\n";
				$html .= "    }).addTo(map);\n";
				if ($tilePT == "yes") {
					$html .= "   L.tileLayer('http://openptmap.org/tiles/{z}/{x}/{y}.png', {\n";
					$html .= "     maxZoom: 17,\n";
					$html .= "    attribution: 'Map data: &copy; <a href=\"http://www.openptmap.org\">OpenPtMap</a> contributors'\n";
					$html .= "    }).addTo(map);\n";
				}
				$html .= "  </script>\n";

				$html .= "  <script type=\"text/javascript\">\n";
				$html .= "   //<![CDATA[\n";
				$html .= "   lang.setCode('$lang->code');\n";
				$html .= "   var geocoder = null;\n";
				$html .= "   var online = true;\n";
				$html .= "   var bounds = null;\n";
				$html .= "   var markersLatLng = [];\n";
				if ($livetracking) {
					$html .= "   var zoomlevel = getValue(\"zoomlevel\");\n";
				} else {
					$html .= "   var zoomlevel = 0;\n";
				}

				if ($crosshair == "yes") {
					$html .= "   var centerCrosshair = L.icon({iconUrl: 'images/crosshair.gif', iconSize: [17, 17], iconAnchor: [8, 8],});\n";
					$html .= "   centerCross = L.marker(map.getCenter(), {icon: centerCrosshair, interactive: false}).addTo(map);\n";
					$html .= "   function setCenterCross() {\n";
					$html .= "    centerCross.setLatLng(map.getCenter());\n";
					$html .= "   };\n";
					$html .= "   map.on(\"zoom move load viewreset resize zoomlevelchange\", setCenterCross);\n";
				}
				if ($clickcenter == "yes") {
					$html .= "   map.on(\"click\", function(e) { map.panTo(e.latlng); });\n";
				} else {
					$html .= "   map.off(\"click\");\n";
				}

				// Write configuration to JS
				if ($showbearings == "yes") {
					$html .= "   var showBearings = true;\n";
				} else {
					$html .= "   var showBearings = false;\n";
				}
				$html .= "   var useMetric = " . ($units == "metric" ? "true" : "false") . ";\n\n";

				$params = array();
				if ($livetracking) {
					$where = "";
					$limit = 1;
				} else {
					if ($filter == "Photo") {
						$where = "ImageURL != ''";
						$limit = 0;
					} elseif ($filter == "Comment") {
						$where = "Comments != ''";
						$limit = 0;
					} elseif ($filter == "PhotoComment") {
						$where = "(Comments!='' OR ImageURL!='')";
						$limit = 0;
					} elseif ($filter == "Last20") {
						$where = "";
						$limit = 20;
					} else {
						$where = "";
						$limit = 0;
					}
					if ($where != "")
						$where .= " AND";

					if ($tripname != $trip_any_text) {
						if ($tripname == $trip_none_text) {
							$count = 0;
						} else {
							// TODO: use parameters
							$count = $db->get_count("positions " .
								"WHERE FK_Users_ID='$ID' AND " .
								"FK_Trips_ID='$trip' AND " .
								"DateOccurred BETWEEN '$startday' AND '$endday'");
						}
						if ($count == 0) {
							$where .= " FK_Trips_ID is NULL AND";
						} else {
							$where .= " FK_Trips_ID=? AND";
							$params[] = $trip;
						}
					}
					$where .= " DateOccurred BETWEEN ? AND ? AND";
					$params[] = $startday;
					$params[] = $endday;
				}

				if ($limit > 0)
					$limit = " DESC LIMIT $limit";
				else
					$limit = "";

				$params[] = $ID;
				$positions = $db->exec_sql("SELECT positions.*, icons.URL FROM positions " .
					"LEFT JOIN icons ON positions.FK_Icons_ID=icons.ID " .
					"WHERE $where FK_Users_ID=? " .
					"ORDER BY DateOccurred $limit",
					$params);

				$result = $positions->fetchAll();
				$count = count($result);

				if ($tripname == $trip_any_text) {
					$tripnameText = $trip_any_text;
				} elseif ($tripname == $trip_none_text) {
					$tripnameText = $trip_none_text;
				} else {
					$tripnameText = $tripname;
				}
				$html .= "   var trip = new Trip('" . escapeJSString($tripnameText) . "', '" . escapeJSString($username) . "');\n";
				for ($rounds = 1; $rounds <= $count; $rounds++) {
					$row = $result[$rounds - 1];
					// escape the strings for JS
					$row['ImageURL'] = escapeJSString($row['ImageURL']);
					$row['Comments'] = escapeJSString($row['Comments']);

					if (!is_null($row['URL'])) {
						$parameter = "'" . $row['URL'] . "'";
					} elseif ($rounds < $count) {
						$parameter = "true";
					} else {
						$parameter = "iconRed";
					}

					$dataParameter = "";
					if (!is_null($row['Angle']))
						$dataParameter = ", bearing: " . $row['Angle'];

					$formattedTS = escapeJSString(date($dateformat, strtotime($row['DateOccurred'])));

					$html .= "   trip.appendMarker({latitude: " . $row['Latitude'] . ", longitude: " . $row['Longitude'] . ", timestamp: '" . $row['DateOccurred']. "', speed: " . $row['Speed'] . ", altitude: " . $row['Altitude'] . ", comment: '" . $row['Comments'] . "', photo: '" . $row['ImageURL'] . "'" . $dataParameter . ", formattedTS: '" . $formattedTS . "'}, " . $parameter . ");\n";
				}

				$html .= "   var polyline = L.polyline(markersLatLng, {color: \"#000000\", weight: 3, opacity: 1}).addTo(map)\n";

				$html .= "   if (bounds == null) {\n";
				$html .= "    bounds = L.latLngBounds(L.latLng(0, 0).toBounds(12000000));\n";
				$html .= "   }\n";
				$html .= "   map.fitBounds(bounds);\n";

				if ($livetracking) {
					$html .= "   map.setZoom(zoomlevel);\n";
				}
				$html .= "   //]]>\n";
				$html .= "  </script>\n";
			} else {
				unset($_SESSION["ID"]);
				$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
				$html .= " <head>\n";
				$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
				$html .= "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
				$html .= "  <link rel=\"stylesheet\" href=\"layout.css?v=" . @filemtime("layout.css") . "\" type=\"text/css\">\n";
				$html .= "  <style type=\"text/css\">\n";
				$html .= "   html, body { margin:0; padding:0; min-height:100%; }\n";
				$html .= "   body.loginpage { background:#0f1724; background-image:linear-gradient(135deg, #0f1724 0%, #12263b 55%, #0a1018 100%); color:#eaf2ff; font-family:Segoe UI, Tahoma, Arial, sans-serif; }\n";
				$html .= "   #loginpagewrap { min-height:100vh; min-height:100svh; min-height:100dvh; display:flex; align-items:center; justify-content:center; padding:32px 20px; box-sizing:border-box; }\n";
				$html .= "   #loginsection { width:360px; max-width:100%; padding:28px; box-sizing:border-box; background:#111a27; border:1px solid #30445d; border-radius:20px; box-shadow:0 24px 60px rgba(0,0,0,0.45); }\n";
				$html .= "   #loginsection .loginbrand { display:flex; justify-content:center; margin:0 0 8px 0; }\n";
				$html .= "   #loginsection .loginbrand img { display:block; width:216px; max-width:84%; height:auto; filter:drop-shadow(0 14px 28px rgba(0,0,0,0.28)); }\n";
				$html .= "   #loginsection .loginbadge { display:block; width:fit-content; margin:0 auto 10px auto; padding:6px 10px; border:1px solid #35567c; border-radius:999px; color:#b7d8ff; background:#16273a; font-size:10px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; }\n";
				$html .= "   #loginsection .loginversion { margin:0 0 12px 0; color:#7fb4ff; font-size:12px; font-weight:700; text-align:center; }\n";
				$html .= "   #loginsection .loginintro { margin:0 0 18px 0; color:#b9c8da; font-size:13px; line-height:1.5; }\n";
				$html .= "   #loginsection .loginfield { margin-bottom:14px; }\n";
				$html .= "   #loginsection .loginlabel { display:block; margin-bottom:6px; color:#89a0bd; font-size:10px; font-weight:700; letter-spacing:1.1px; text-transform:uppercase; }\n";
				$html .= "   #loginsection .textinputfield { width:100%; padding:12px 14px; box-sizing:border-box; background:#0d1520; color:#f4f8ff; border:1px solid #58708c; border-radius:12px; font-size:14px; font-weight:600; }\n";
				$html .= "   #loginsection .button { width:100%; padding:12px 14px; box-sizing:border-box; border:1px solid #89c5ff; border-radius:12px; background:#3378e2; color:#ffffff; font-size:13px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; cursor:pointer; }\n";
				$html .= "   #loginsection .loginfootnote { margin-top:18px; padding-top:16px; border-top:1px solid #26384e; color:#7890ac; font-size:11px; line-height:1.5; }\n";
				$html .= "   @media screen and (max-width: 520px) { #loginpagewrap { align-items:flex-start; padding:16px 10px; } #loginsection { padding:18px 16px 16px 16px; border-radius:18px; } #loginsection .loginbrand { margin-bottom:6px; } #loginsection .loginbrand img { width:172px; max-width:72%; } #loginsection .loginbadge { margin-bottom:8px; padding:5px 9px; } #loginsection .loginversion { margin-bottom:8px; } #loginsection .loginintro { margin-bottom:14px; font-size:12px; line-height:1.4; } #loginsection .loginfield { margin-bottom:10px; } #loginsection .loginlabel { margin-bottom:5px; } #loginsection .textinputfield, #loginsection .button { padding:11px 12px; } #loginsection .loginfootnote { margin-top:14px; padding-top:12px; font-size:10px; line-height:1.4; } }\n";
				$html .= "  </style>\n";
				$html .= "  <title>$title_text (v" . $version_text . ")</title>\n";
				$html .= " </head>\n";
				$html .= " <body class=\"loginpage\" onload=\"placeFocus()\">\n";
				$html .= "  <script type=\"text/javascript\">\n";
				$html .= "   function placeFocus() {\n";
				$html .= "    if (document.forms.length > 0) {\n";
				$html .= "     var field = document.forms[0];\n";
				$html .= "     for (i = 0; i < field.length; i++) {\n";
				$html .= "      if ((field.elements[i].type == \"text\") || (field.elements[i].type == \"textarea\") || (field.elements[i].type.toString().charAt(0) == \"s\")) {\n";
				$html .= "       document.forms[0].elements[i].focus();\n";
				$html .= "       break;\n";
				$html .= "      }\n";
				$html .= "     }\n";
				$html .= "    }\n";
				$html .= "   }\n";
				$html .= "  </script>\n";
				$html .= "  <div id=\"loginpagewrap\">\n";
				$html .= "   <div id=\"loginsection\">\n";
				$html .= "    <div class=\"loginbrand\"><img src=\"images/trackme-logo.png\" alt=\"TrackMe\"></div>\n";
				$html .= "    <div class=\"loginbadge\">" . $lang["login-badge"] . "</div>\n";
				$html .= "    <div class=\"loginversion\">v" . $version_text . "</div>\n";
				$html .= "    <p class=\"loginintro\">$page_private</p>\n"; //trackmeIT
				$html .= "    <form name=\"form_login\" method=\"post\" class=\"loginform\">\n";
				$html .= "     <div class=\"loginfield\">\n";
				$html .= "      <label class=\"loginlabel\" for=\"login-username\">$login_text</label>\n";
				$html .= "      <input type=\"text\" class=\"textinputfield\" id=\"login-username\" name=\"username\" size=\"10\" autocomplete=\"username\">\n";
				$html .= "     </div>\n";
				$html .= "     <div class=\"loginfield\">\n";
				$html .= "      <label class=\"loginlabel\" for=\"login-password\">$password_text</label>\n";
				$html .= "      <input type=\"password\" class=\"textinputfield\" id=\"login-password\" name=\"password\" size=\"10\" autocomplete=\"current-password\">\n";
				$html .= "     </div>\n";
				$html .= "     <div class=\"loginaction\"><input type=\"submit\" class=\"button\" value=\"$login_button_text\"></div>\n";
				$html .= "    </form>\n";
				$html .= "    <div class=\"loginfootnote\">" . $lang["login-footnote"] . "</div>\n";
				$html .= "   </div>\n";
				$html .= "  </div>\n";
			}
		}
	}

	$html .= "  <!-- <div id=\"footertext\">$footer_text <a href=\"http://forum.xda-developers.com/showthread.php?t=340667\" target=\"_blank\">TrackMe</a></div> -->\n";
	//google analytics
	if (isset($googleanalyticsaccount)) {
		$html .= "  <script type=\"text/javascript\">\n";
		$html .= "   var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\n";
		$html .= "   document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\n";
		$html .= "  </script>\n";
		$html .= "  <script type=\"text/javascript\">\n";
		$html .= "   var pageTracker = _gat._getTracker(\"$googleanalyticsaccount\");\n";
		$html .= "   pageTracker._initData();\n";
		$html .= "   pageTracker._trackPageview();\n";
		$html .= "  </script>\n";
	}
	$html .= " </body>\n";
	$html .= "</html>\n";

	$db = null;  // Close database
	print $html;

	// Function to calculate distance between points
	function calcDistance($lat1, $lon1, $lat2, $lon2, $unit) {
		if ($lat1 == $lat2 && $lon1 == $lon2) { return 0; }
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344);
		} elseif ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}

	// Function to convert MySQL dates into UNIX seconds sind 1.1.1970
	function convDateToSeconds($date) {
		list($year, $month, $day, $hour, $minute, $second ) = preg_split('([^0-9])', $date);
		return date('U', mktime($hour, $minute, $second, $month, $day, $year));
	}

	// Function to calculate time between points
	function getElapsedTime($time_start, $time_end, $units = 'seconds', $decimals = 0) {
		$divider['years']   = ( 60 * 60 * 24 * 365 );
		$divider['months']  = ( 60 * 60 * 24 * 365 / 12 );
		$divider['weeks']   = ( 60 * 60 * 24 * 7 );
		$divider['days']    = ( 60 * 60 * 24 );
		$divider['hours']   = ( 60 * 60 );
		$divider['minutes'] = ( 60 );
		$divider['seconds'] = 1;

		$elapsed_time = ((convDateToSeconds($time_end) - convDateToSeconds($time_start)) / $divider[$units]);
		$elapsed_time = sprintf("%0.{$decimals}f", $elapsed_time);
		return $elapsed_time;
	}

	function escapeJSString($str) {
		return str_replace("'", "\\'", str_replace("\\", "\\\\", $str));
	}
?>
