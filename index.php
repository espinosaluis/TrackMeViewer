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

	$versiontext = "3.5";

	require_once("database.php");
	require_once("tileprovider.php");

	session_start();
	$debug0 = false; $debug1 = false; $debug2 = false; $debug3 = false; $debug4 = false;

	if (!ini_get('date.timezone')) {
		date_default_timezone_set('GMT');
	}
	$_REQUEST = array_merge($_GET, $_POST);

	if (dirname($_SERVER['PHP_SELF']) == "/") {
		$siteroot ="http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php";
	} else {
		$siteroot ="http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php";
	}

	if ($debug0) debug2console("\$_REQUEST[".count($_REQUEST, COUNT_RECURSIVE)."]=", $_REQUEST);
	if ($debug0) debug2console("\$_GET[".count($_GET, COUNT_RECURSIVE)."]=", $_GET);
	if ($debug0) debug2console("\$_POST[".count($_POST, COUNT_RECURSIVE)."]=", $_POST);
	if ($debug4) debug2console("\$_SERVER[".count($_SERVER, COUNT_RECURSIVE)."]=", $_SERVER);
	if ($debug0) debug2console("\$_COOKIE[".count($_COOKIE, COUNT_RECURSIVE)."]=", $_COOKIE);
	if ($debug0) debug2console("\$_ENV[".count($_ENV, COUNT_RECURSIVE)."]=", $_ENV);

	// Attributes use: config.php values or Default values ---overwrittenby---> Cookies values ---overwrittenby---> Request values
	// config.php attributes Default values
	require("config.php");
	// Non-config.php attributes Default values
	$tripID           = null;
	$tripgroup        = "";
	$filterwith       = "None";
	$filterstart      = null;
	$filterend        = null;
	$livetracking     = 0;
	$chartdisplay     = 0;
	$attributedisplay = 0;
	$zoomlevel        = 11;
	$navigationwidth  = "210px";
	$ID               = null;
	$username         = null;
	$password         = null;
	$action           = null;

	// Cookies values overwrite initial values
	if (isset($_COOKIE['tripID']))           $tripID           = $_COOKIE['tripID'];
	if (isset($_COOKIE['tripgroup']))        $tripgroup        = $_COOKIE['tripgroup'];
	if (isset($_COOKIE['filterwith']))       $filterwith       = $_COOKIE['filterwith'];
	if (isset($_COOKIE['filterstart']))      $filterstart      = $_COOKIE['filterstart'];
	if (isset($_COOKIE['filterend']))        $filterend        = $_COOKIE['filterend'];
	if (isset($_COOKIE['livetracking']))     $livetracking     = $_COOKIE['livetracking'];
	if (isset($_COOKIE['chartdisplay']))     $chartdisplay     = $_COOKIE['chartdisplay'];
	if (isset($_COOKIE['attributedisplay'])) $attributedisplay = $_COOKIE['attributedisplay'];
	if (isset($_COOKIE['interval']))         $interval         = $_COOKIE['interval'];
	if (isset($_COOKIE['zoomlevel']))        $zoomlevel        = $_COOKIE['zoomlevel'];
	if (isset($_COOKIE['navigationwidth']))  $navigationwidth  = $_COOKIE['navigationwidth'];
	if (isset($_COOKIE['linecolor']))        $linecolor        = $_COOKIE['linecolor'];
	if (isset($_COOKIE['showbearings']))     $showbearings     = $_COOKIE['showbearings'];
	if (isset($_COOKIE['markertype']))       $markertype       = $_COOKIE['markertype'];
	if (isset($_COOKIE['crosshair']))        $crosshair        = $_COOKIE['crosshair'];
	if (isset($_COOKIE['clickcenter']))      $clickcenter      = $_COOKIE['clickcenter'];
	if (isset($_COOKIE['language']))         $language         = $_COOKIE['language'];
	if (isset($_COOKIE['units']))            $units            = $_COOKIE['units'];
	if (isset($_COOKIE['tileprovider']))     $tileprovider     = $_COOKIE['tileprovider'];
	if (isset($_COOKIE['tilePT']))           $tilePT           = $_COOKIE['tilePT'];

	// Request values overwrite initial and cookies values
	if (isset($_REQUEST['ID']))                  $ID               = $_REQUEST['ID'];
	if (isset($_REQUEST['username']))            $username         = $_REQUEST['username'];
	if (isset($_REQUEST['password']))            $password         = $_REQUEST['password'];
	if (isset($_REQUEST['action']))              $action           = $_REQUEST['action'];
	if (isset($_REQUEST['settripID']))           $tripID           = $_REQUEST['settripID'];
	if (isset($_REQUEST['settripgroup']))        $tripgroup        = $_REQUEST['settripgroup'];
	if (isset($_REQUEST['setfilterwith']))       $filterwith       = $_REQUEST['setfilterwith'];
	if (isset($_REQUEST['setfilterstart']))      $filterstart      = $_REQUEST['setfilterstart'];
	if (isset($_REQUEST['setfilterend']))        $filterend        = $_REQUEST['setfilterend'];
	if (isset($_REQUEST['setlivetracking']))     $livetracking     = $_REQUEST['setlivetracking'];
	if (isset($_REQUEST['setchartdisplay']))     $chartdisplay     = $_REQUEST['setchartdisplay'];
	if (isset($_REQUEST['setattributedisplay'])) $attributedisplay = $_REQUEST['setattributedisplay'];
	if (isset($_REQUEST['interval']))            $interval         = $_REQUEST['interval'];
	if (isset($_REQUEST['zoomlevel']))           $zoomlevel        = $_REQUEST['zoomlevel'];
	if (isset($_REQUEST['navigationwidth']))     $navigationwidth  = $_REQUEST['navigationwidth'];
	if (isset($_REQUEST['setlinecolor']))        $linecolor        = $_REQUEST['setlinecolor'];
	if (isset($_REQUEST['setshowbearings']))     $showbearings     = $_REQUEST['setshowbearings'];
	if (isset($_REQUEST['setmarkertype']))       $markertype       = $_REQUEST['setmarkertype'];
	if (isset($_REQUEST['setcrosshair']))        $crosshair        = $_REQUEST['setcrosshair'];
	if (isset($_REQUEST['setclickcenter']))      $clickcenter      = $_REQUEST['setclickcenter'];
	if (isset($_REQUEST['setlanguage']))         $language         = $_REQUEST['setlanguage'];
	if (isset($_REQUEST['setunits']))            $units            = $_REQUEST['setunits'];
	if (isset($_REQUEST['settileprovider']))     $tileprovider     = $_REQUEST['settileprovider'];
	if (isset($_REQUEST['settilePT']))           $tilePT           = $_REQUEST['settilePT'];

	if ($debug0) debug2console("Attribs:", "action=$action ID=$ID username=$username password=$password interval=$interval zoomlevel=$zoomlevel");
	if ($debug0) debug2console("Attribs:", "livetracking=$livetracking chartdisplay=$chartdisplay attributedisplay=$attributedisplay linecolor=$linecolor markertype=$markertype showbearings=$showbearings crosshair=$crosshair clickcenter=$clickcenter language=$language units=$units tileprovider=$tileprovider tilePT=$tilePT");
	if ($debug0) debug2console("Attribs:", "tripID=$tripID tripgroup=$tripgroup filterwith=$filterwith filterstart=$filterstart filterend=$filterend");

	require_once("language.php");

	try {
		$db = connect();
	} catch (PDOException $e) {
		$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
		$html .= " <head>\n";
		$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
		$html .= "  <link rel=\"shortcut icon\" href=\"favicon.ico\">\n";
		$html .= "  <title>$windowtitle (v$versiontext)</title>\n";
		$html .= " </head>\n";
		$html .= " <body>\n";
		$html .= "  <div align=\"center\">\n";
		$html .= "   $database_fail_text<br>\n";
		$html .= "   <br>\n";
		$html .= "   " . $e->getMessage() . "<br>" . $e . "\n";
		$html .= "  </div>\n";
		$html .= " </body>\n";
		$html .= "</html>\n";
		print $html;
		exit;
	}

	$num_users     = $db->get_count("users");
	$num_trips     = $db->get_count("trips");
	$num_positions = $db->get_count("positions");
	$num_icons     = $db->get_count("icons");

	if ($filterstart != null) {
		$filterstartNLS = preg_replace("/[^0-9 \.:\-]/", "", $filterstart);
	} else
		$filterstartNLS = null;
	if ($filterend != null) {
		$filterendNLS   = preg_replace("/[^0-9 \.:\-]/", "", $filterend);
	} else
		$filterendNLS   = null;

	if (is_numeric($tripID) && !isset($ID)) {
		$ID = $db->exec_sql("SELECT FK_Users_ID FROM trips WHERE ID=?", $tripID)->fetchColumn();
		if ($ID === false)
			unset($ID);
	}

	if ($num_users < 1 || $num_trips < 1 || $num_positions < 1 || $num_icons < 1) {
		$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
		$html .= " <head>\n";
		$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
		$html .= "  <link rel=\"shortcut icon\" href=\"favicon.ico\">\n";
		$html .= "  <title>$windowtitle (v$versiontext)</title>\n";
		$html .= " </head>\n";
		$html .= " <body>\n";
		$html .= "  <div align=\"center\">\n";
		$html .= "   $no_data_text<br>\n";
		$html .= "  </div>\n";
		$html .= " </body>\n";
		$html .= "</html>\n";
		print $html;
		exit;
	}

	if (file_exists("install.php") || file_exists("database.sql")) {
		$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
		$html .= " <head>\n";
		$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
		$html .= "  <link rel=\"shortcut icon\" href=\"favicon.ico\">\n";
		$html .= "  <title>$windowtitle (v$versiontext)</title>\n";
		$html .= " </head>\n";
		$html .= " <body>\n";
		$html .= "  <div align=\"center\">\n";
		$html .= "   $incomplete_install_text<br>\n";
		$html .= "  </div>\n";
		$html .= " </body>\n";
		$html .= "</html>\n";
		print $html;
		exit;
	}

	if ($publicpage != "yes") {
		if ($action == "logout") {
			unset($_SESSION['ID']);
		}
		if (isset($username) && isset($password)) {
			if (preg_match("/^([a-zA-Z0-9._])+$/", "$_REQUEST[username]")) {
				$login_id = $db->valid_login($username, $password);
				if ($login_id >= 0) {
					$_SESSION['ID'] = $login_id;
				}
			}
		}
		(isset($_SESSION['ID'])) ? $ID = $_SESSION['ID'] : $ID = null;

		if (!isset($_SESSION['ID'])) {
			$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
			$html .= " <head>\n";
			$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
			$html .= "  <link rel=\"stylesheet\" href=\"layout.css\" type=\"text/css\">\n";
			$html .= "  <title>$windowtitle (v$versiontext)</title>\n";
			$html .= " </head>\n";
			$html .= " <body onload=\"placeFocus()\">\n";
			$html .= "  <script>\n";
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
			$html .= "  <center>\n";
			$html .= "   <br><br>\n";
			$html .= "   <div id=\"loginsection\" align=\"center\">\n";
			$html .= "    <h1>$windowtitle (v$versiontext)</h1>\n";
			$html .= "    $page_private_text<br>\n";
			$html .= "    <br><br>\n";
			$html .= "    <form id=\"form_login\" name=\"form_login\" method=\"post\"><br>\n";
			$html .= "     <table>\n";
			$html .= "      <tr style=\"border: 10px\">\n";
			$html .= "       <td style=\"text-align: right;\">&nbsp;&nbsp;&nbsp;$login_username_text:&nbsp;&nbsp;</td>\n";
			$html .= "       <td align=\"center\"><input type=\"text\" class=\"textinputfield\" name=\"username\" size=\"50\" /></td>\n";
			$html .= "      </tr>\n";
			$html .= "      <tr>\n";
			$html .= "       <td style=\"text-align: right;\">&nbsp;&nbsp;&nbsp;$login_password_text:&nbsp;&nbsp;</td>\n";
			$html .= "       <td align=\"center\"><input type=\"password\" class=\"textinputfield\" name=\"password\" size=\"50\" /></td>\n";
			$html .= "      </tr>\n";
			$html .= "      <tr>\n";
			$html .= "       <td align=\"center\" colspan=\"2\"><input type=\"submit\" class=\"buttonshort\" value=\"$login_button_text\" /></td>\n";
			$html .= "      </tr>\n";
			$html .= "     </table>\n";
			$html .= "    </form>\n"; // form_login
			$html .= "    <br>\n";
			$html .= "   </div>\n"; // loginsection
			$html .= "  </center>\n";
			$html .= " </body>\n";
			$html .= "</html>\n";
			print $html;
			exit;
		}
	}

	// Normal page starts here
	$html = "";
	$html .= "<!DOCTYPE html>\n";
	$html .= "<html lang=\"" . $lang->code . "\">\n";
	$html .= " <head>\n";
	$html .= "  <meta charset=\"utf-8\" />\n";
	$html .= "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n";
	$html .= "  <link rel=\"shortcut icon\" href=\"favicon.ico\" />\n";
	$html .= "  <link rel=\"stylesheet\" type=\"text/css\" href=\"https://unpkg.com/leaflet@1.6.0/dist/leaflet.css\" integrity=\"sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==\" crossorigin=\"\" />\n";
	$html .= "  <link rel=\"stylesheet\" type=\"text/css\" href=\"flatpickr.min.css\" />\n";
	$html .= "  <link rel=\"stylesheet\" type=\"text/css\" href=\"cookieconsent.min.css\" />\n";
	$html .= "  <link rel=\"stylesheet\" type=\"text/css\" href=\"layout.css\" />\n";
	$html .= "  <script src=\"https://unpkg.com/leaflet@1.6.0/dist/leaflet.js\" crossorigin=\"\"></script>\n";
	$html .= "  <script src=\"iro.js\"></script>\n";
	$html .= "  <script src=\"flatpickr.min.js\"></script>\n";
	$html .= "  <script src=\"flatpickrNLS.js\"></script>\n";
	$html .= "  <script src=\"cookieconsent.min.js\" data-cfasync=\"false\"></script>\n";
	$html .= "  <script src=\"jquery-3.5.1.min.js\"></script>\n";
	$html .= "  <script src=\"jquery.sparkline.min.js\"></script>\n";
	$html .= "  <script src=\"sweetalert2.all.min.js\"></script>\n";
	$html .= "  <script src=\"colResizable-1.6.min.js\"></script>\n";
	$html .= "  <script src=\"main.js\"></script>\n";
	$html .= "  <script src=\"lang.js\"></script>\n";
	$html .= "  <title>$windowtitle (v$versiontext)</title>\n";
	$html .= " </head>\n";

	if ($livetracking) {
		$html .= " <body onload=\"initInterval();\">\n";
	} else {
		$html .= " <body>\n";
	}

	$html .= "  <script>\n";
	$html .= "   var debug0 = " . ($debug0 ? "true" : "false") . ";\n";
	$html .= "   var debug1 = " . ($debug1 ? "true" : "false") . ";\n";
	$html .= "   var debug2 = " . ($debug2 ? "true" : "false") . ";\n";
	$html .= "   var debug3 = " . ($debug3 ? "true" : "false") . ";\n";
	$html .= "   var debug4 = " . ($debug4 ? "true" : "false") . ";\n";
	$html .= "  </script>\n";
	$html .= "  <table id=\"trackmeviewer\">\n";
	$html .= "   <tr>\n";
	$html .= "    <td>\n";

	$html .= "     <div id=\"mapsection\"></div>\n"; // mapsection

	$html .= "     <div id=\"creditsection\">Powered by <a href=\"http://www.luisespinosa.com/trackme_eng.html\">TrackMeViewer (v$versiontext)</a> by <a href=\"http://www.luisespinosa.com/central_eng.php\">Luis Espinosa & friends</a></div>\n"; // creditsection

	$html .= "    </td>\n";
	$html .= "    <td id=\"navigationsectioncell\" style=\"width: 210px; font-size: 90%;\">\n";

	$html .= "     <div id=\"navigationsection\">\n";

	if ($publicpage == "yes") {
		$html .= "      <form id=\"form_user\" name=\"form_user\" method=\"post\">\n";
		$html .= "       <select id=\"ID\" name=\"ID\" class=\"pulldown\">\n";

		$findusers = $db->exec_sql("SELECT * FROM users ORDER BY username");
		while ($founduser = $findusers->fetch()) {
			if (!isset($ID)) {
				$ID     = $founduser['ID'];
				$tripID = "";
			}
			if ($founduser['ID'] == $ID) {
				$html .= "        <option value=\"$founduser[ID]\" selected>$founduser[username]</option>\n";
				$username = $founduser['username'];
			} else {
				$html .= "        <option value=\"$founduser[ID]\">$founduser[username]</option>\n";
			}
		}
		$html .= "       </select>\n";
		$html .= "       <input type=\"submit\" class=\"button\" id=\"userbutton\" value=\"$select_user_text\" />\n";
		$html .= "      </form>\n"; // form_user
	} else {
		$founduser = $db->exec_sql("SELECT * FROM users WHERE ID=? LIMIT 1", $ID)->fetch();
		$username = $founduser['username'];
		$html .= "      <form id=\"form_logout\" name=\"form_logout\" method=\"post\" style=\"display: inline;\">\n";
		$html .= "       <br>\n";
		$html .= "       <b><u>$trip_data_text:</u></b>\n";
		$html .= "       " . $founduser['username'] . "<br>\n";
		$html .= "       <input type=\"hidden\" name=\"ID\" value=\"$ID\" />\n";
		$html .= "       <input type=\"hidden\" name=\"action\" value=\"logout\" />\n";
		$html .= "       <input type=\"submit\" class=\"button\" id=\"logoutbutton\" value=\"$logout_button_text\" />\n";
		$html .= "      </form>\n"; // form_logout
	}
	$html .= "      <br><br>\n";

	$html .= "      <form id=\"form_attributes\" name=\"form_attributes\" method=\"post\" accept-charset=\"utf-8\" style=\"display: inline;\">\n";

	if ($livetracking) { $chk = " checked"; } else { $chk = ""; }
	$html .= "       <input type=\"hidden\" name=\"setlivetracking\" value=\"0\" />\n";
	$html .= "       <input type=\"checkbox\" name=\"setlivetracking\" id=\"setlivetracking\" value=\"1\"$chk onClick=\"document.form_attributes.submit();\" />\n";
	$html .= "       <label id=\"label_setlivetracking\" for=\"setlivetracking\" style=\"color: lightblue;\">$livetracking_text<br></label>\n";

	if (!$livetracking) {
		if ($chartdisplay) { $chk = " checked"; } else { $chk = ""; }
		$html .= "       <input type=\"hidden\" name=\"setchartdisplay\" value=\"0\" />\n";
		$html .= "       <input type=\"checkbox\" name=\"setchartdisplay\" id=\"setchartdisplay\" value=\"1\"$chk onClick=\"document.form_attributes.submit();\" />\n";
		$html .= "       <label id=\"label_setchartdisplay\" for=\"setchartdisplay\" style=\"color: lightblue;\">$chartdisplay_text<br></label>\n";
	}

	if ($allowcustom == "yes") {
		if ($attributedisplay) { $chk = " checked"; } else { $chk = ""; }
		$html .= "       <input type=\"hidden\" name=\"setattributedisplay\" value=\"0\" />\n";
		$html .= "       <input type=\"checkbox\" name=\"setattributedisplay\" id=\"setattributedisplay\" value=\"1\"$chk onClick=\"toggleDisplayOptions();\" />\n";
		$html .= "       <label id=\"label_setattributedisplay\" for=\"setattributedisplay\" style=\"color: lightblue;\">$attributedisplay_text<br></label>\n";

		$html .= "       <div id=\"attribute_section\" style=\"display: " . ($attributedisplay ? "inline" : "none") . ";\">\n";
		$html .= "        <b><u>$options_title:</u></b><br>\n";
		if ($livetracking) {
		} else {
			$html .= "        <div id=\"colorLine\" class=\"wheel\" style=\"display: inline;\">\n";
			$html .= "         <input type=\"text\" name=\"setlinecolor\" id=\"setlinecolor\" style=\"visibility: hidden; position: absolute;\" value=\"$linecolor\" />\n";
			$html .= "        </div>\n";
			$html .= "        <label id=\"label_setlinecolor\" for=\"setlinecolor\">$options_linecolor_text<br></label>\n";

			$chk = ($markertype ? " checked" : "");
			$html .= "        <input type=\"hidden\" name=\"setmarkertype\" value=\"0\" />\n";
			$html .= "        <input type=\"checkbox\" name=\"setmarkertype\" id=\"setmarkertype\" value=\"1\"$chk onClick=\"document.form_attributes.submit();\" />\n";
			$html .= "        <label id=\"label_setmarkertype\" for=\"setmarkertype\">$options_markertype_text<br></label>\n";

			$chk = ($showbearings ? " checked" : "");
			if ($markertype) {
				$color    = "color: #ffffff;";
				$value    = " value=\"1\"";
				$disabled = "";
			} else {
				$color    = "color: #aaaaaa;";
				$value    = " value=\"0\"";
				$disabled = " disabled";
			}
			$html .= "        <input type=\"hidden\" name=\"setshowbearings\" value=\"0\" />\n";
			$html .= "        <input type=\"checkbox\" name=\"setshowbearings\" id=\"setshowbearings\" value=\"1\"$chk$disabled onClick=\"document.form_attributes.submit();\" />\n";
			$html .= "        <label id=\"label_setshowbearings\" for=\"setshowbearings\" style=\"$color\">$options_showbearing_text<br></label>\n";
		}

		$chk = ($crosshair ? " checked" : "");
		$html .= "        <input type=\"hidden\" name=\"setcrosshair\" value=\"0\" />\n";
		$html .= "        <input type=\"checkbox\" name=\"setcrosshair\" id=\"setcrosshair\" value=\"1\"$chk onClick=\"document.form_attributes.submit();\" />\n";
		$html .= "        <label id=\"label_setcrosshair\" for=\"setcrosshair\">$options_crosshair_text<br></label>\n";

		$chk = ($clickcenter ? " checked" : "");
		$html .= "        <input type=\"hidden\" name=\"setclickcenter\" value=\"0\" />\n";
		$html .= "        <input type=\"checkbox\" name=\"setclickcenter\" id=\"setclickcenter\" value=\"1\"$chk onClick=\"document.form_attributes.submit();\" />\n";
		$html .= "        <label id=\"label_setclickcenter\" for=\"setclickcenter\">$options_clickcenter_text<br></label>\n";

		$html .= "        <label id=\"label_setlanguage\" for=\"setlanguage\">$options_language_text<br></label>\n";
		$html .= "        <select id=\"setlanguage\" name=\"setlanguage\" class=\"pulldown\" onchange=\"document.form_attributes.submit();\">\n";
		foreach (array_values($languages) as $lang_entry) {
			$lang_name = ($lang_entry->en ? strtolower($lang_entry->en) : "english");
			$html .= "         <option value=\"$lang_name\"" . ($language === $lang_name ? " selected" : "") . ">$lang_entry->full_name</option>\n";
		}
		$html .= "        </select><br>\n";

		$html .= "        <label id=\"label_setunits\" for=\"setunits\">$options_units_text<br></label>\n";
		$html .= "        <select id=\"setunits\" name=\"setunits\" class=\"pulldown\" onchange=\"document.form_attributes.submit();\">\n";
		$html .= "         <option value=\"imperial\"" . ($units == "imperial" ? " selected" : "") . ">Imperial</option>\n";
		$html .= "         <option value=\"metric\""   . ($units == "metric"   ? " selected" : "") . ">Metric</option>\n";
		$html .= "        </select><br>\n";

		$html .= "        <label id=\"label_settileprovider\" for=\"settileprovider\">$options_tileprovider_text<br></label>\n";
		$html .= "        <select id=\"settileprovider\" name=\"settileprovider\" class=\"pulldown\" onchange=\"document.form_attributes.submit();\">\n";
		foreach ($tileproviders as $tileprovidername => $tileproviderspecs) {
			$html .= "         <option value=\"" . $tileprovidername . "\"" . ($tileprovidername == $tileprovider ? " selected" : "") . ">" . $tileprovidername . "</option>\n";
		}
		$html .= "        </select><br>\n";

		$chk = ($tilePT ? " checked" : "");
		$html .= "        <input type=\"hidden\" name=\"settilePT\" value=\"0\" />\n";
		$html .= "        <input type=\"checkbox\" name=\"settilePT\" id=\"settilePT\" value=\"1\"$chk onClick=\"document.form_attributes.submit();\" />\n";
		$html .= "        <label id=\"label_settilePT\" for=\"settilePT\">$options_tilePT_text<br></label>\n";

		$html .= "       </div>\n"; // attribute_section

		$html .= "       <br>\n";
	}

	if ($livetracking) {
		$tripname = "";
	} else {
		$stmt = "SELECT A1.*, (SELECT MIN(A2.DateOccurred) FROM positions A2 WHERE A2.FK_Trips_ID=A1.ID) AS Startdate FROM trips A1 WHERE A1.FK_Users_ID=? ORDER BY Startdate DESC";
		$foundtrips = $db->exec_sql($stmt, $ID)->fetchAll();
		if (count($foundtrips) == 0) {
			$tripID   = $trip_any_text;
			$tripname = "";
			$comments = "";
		} else {
			$selectedtrip = "";
			$groupedtrips = false;
			foreach ($foundtrips as $foundtrip) {
				list($trippartgroup, $trippartname) = gettripnameparts($foundtrip['Name'], $no_tripgroup_text);
				if ($trippartgroup != $no_tripgroup_text) $groupedtrips = true;
				$tripsnameindexed[$trippartgroup][$trippartname] = $foundtrip;
				$tripsintindexed[$trippartgroup][] = $foundtrip;
				if ($foundtrip['ID'] == $tripID) $selectedtrip = $foundtrip;
			}

			if (!isset($tripsintindexed[$tripgroup][0])) {
				$tripgroup = array_keys($tripsnameindexed)[0];
			}
			$anytrip = array("ID" => $trip_any_text, "Name" => $trip_any_text, "FK_Users_ID" => null, "Comments" => null);
			if ($selectedtrip == "") {
				if ($tripID == $trip_any_text) {
					$selectedtrip = $anytrip;
				} else {
					$selectedtrip = $tripsintindexed[$tripgroup][0];
				}
			}

			$deleteButton = ($selectedtrip['FK_Users_ID'] === $_SESSION['ID']);
			$tripID   = $selectedtrip['ID'];
			$tripname = $selectedtrip['Name'];
			$comments = $selectedtrip['Comments'];
			if ($tripID != $trip_any_text) list($tripgroup, $dummy) = gettripnameparts($tripname, $no_tripgroup_text);
			if ($debug1) debug2console("Selected tripID:", $tripID);
			if ($debug1) debug2console("Selected tripname:", $tripname);
			if ($debug1) debug2console("Selected tripcomment:", $comments);
			if ($debug1) debug2console("Selected tripgroup:", $tripgroup);

			$html .= "       <b><u>$trip_title:</u></b><br>\n";

			if ($groupedtrips) {
				$html .= "       <table>\n";
				$html .= "        <tr>\n";
				$html .= "         <td style=\"text-align: left;\">\n";
				$html .= "          $trip_group:\n";
				$html .= "         </td>\n";
				$html .= "         <td style=\"text-align: right;\">\n";
				$html .= "          <select id=\"settripgroup\" name=\"settripgroup\" class=\"pulldownshorter\" onchange=\"submitTripGroup();\" >\n";
				foreach ($tripsnameindexed as $tripsgroupname => $tripsarray) {
					$html .= "           <option value=\"" . $tripsgroupname . "\"" . ($tripsgroupname == $tripgroup ? " selected" : "") . ">" . $tripsgroupname . "</option>\n";
				}
				$html .= "          </select>\n";
				$html .= "         </td>\n";
				$html .= "        </tr>\n";
				$html .= "        <tr>\n";
				$html .= "         <td style=\"text-align: left;\">\n";
				$html .= "          $trip_name:\n";
				$html .= "         </td>\n";
				$html .= "         <td style=\"text-align: right;\">\n";
				$html .= "          <select id=\"settripID\" name=\"settripID\" class=\"pulldownshorter\" onchange=\"submitTrip();\" >\n";
			} else {
				$html .= "       <select id=\"settripID\" name=\"settripID\" class=\"pulldown\" onchange=\"submitTrip();\" >\n";
			}

			$tripsnameindexed[$tripgroup] = array_merge(array($trip_any_text => $anytrip), $tripsnameindexed[$tripgroup]);
			foreach ($tripsnameindexed[$tripgroup] as $tripsname => $trip) {
				list($trippartgroup, $trippartname) = gettripnameparts($tripsname, $no_tripgroup_text);
				$html .= "           <option value=\"" . $trip['ID'] . "\"" . ($trip['ID'] == $tripID ? " selected" : "") . ">" . $trippartname . "</option>\n";
			}
			if ($groupedtrips) {
				$html .= "          </select>\n";
				$html .= "         </td>\n";
				$html .= "        </tr>\n";
				$html .= "       </table>\n";
			} else {
				$html .= "       </select>\n";
			}

			if ($deleteButton) {
				$html .= "       <input type=\"button\" class=\"buttonshort\" id=\"deletetripbutton\" value=\"$delete_trip_button_text\" onclick=\"deleteTrip();\" />\n";
				if ($allowDBchange == "yes") {
					$html .= "       <input type=\"button\" class=\"buttonshort\" id=\"renametripbutton\" value=\"$rename_trip_button_text\" onclick=\"renameTrip();\" />\n";
					$html .= "       <br>\n";
					if ($comments == "") { $dis = " disabled"; $cla = "buttonshortreverse"; } else { $dis = ""; $cla = "buttonshort"; }
					$html .= "       <input type=\"button\" class=\"" . $cla . "\" id=\"deletecommentsbutton\" value=\"$delete_trip_comments_button_text\"$dis onclick=\"deleteTripComments();\" />\n";
					$html .= "       <input type=\"button\" class=\"buttonshort\" id=\"changecommentsbutton\" value=\"$change_trip_comments_button_text\" onclick=\"changeTripComments();\" />\n";
				}
				$html .= "       <br><br>\n";
			}
		}
	}

	$params = array($ID);
	if ($livetracking) {
		$where = "";
		$limit = "1";
	} else {
		$where = "";
		$limit = "0";
		if ($tripname !== $trip_any_text) {
			$where .= " AND FK_Trips_ID=?";
			$params[] = $tripID;
		}
		if ($tripgroup !== $no_tripgroup_text) {
			$where .= " AND FK_Trips_ID IN (SELECT ID FROM trips WHERE Name LIKE ?)";
			$params[] = $tripgroup . ":%";
		}

		if ($filterstartNLS != null && trim($filterstartNLS) != "") {
			$filterstartDB = date_format(date_create_from_format($dateformat . " " . $timeformat, $filterstartNLS), "Y-m-d H:i:s");
			$filterendDB   = date_format(date_create_from_format($dateformat . " " . $timeformat, $filterendNLS  ), "Y-m-d H:i:s");
			$where .= " AND DateOccurred BETWEEN ? AND ?";
			$params[] = $filterstartDB;
			$params[] = $filterendDB;
		}
	}
	if ($limit > 0) {
		$limit = "DESC LIMIT " . $limit;
	} else {
		$limit = "";
	}

	$stmt = "SELECT * FROM positions WHERE FK_Users_ID=?$where ORDER BY DateOccurred $limit";
	$result = $db->exec_sql($stmt, $params);

	$rounds           = 1;
	$total_time       = 0;
	$pcount           = 0;
	$ccount           = 0;
	$total_miles      = 0;		$total_kilometers = 0;
	$speed_mph_max    = 0;		$speed_kph_max    = 0;
	$speed_mph_min    = 999999;	$speed_kph_min    = 999999;
	$alt_ft_start     = 0;		$alt_m_start      = 0;
	$alt_ft_end       = 0;		$alt_m_end        = 0;
	$alt_ft_max       = 0;		$alt_m_max        = 0;
	$alt_ft_min       = 999999;	$alt_m_min        = 999999;
	$alt_ft_tot_desc  = 0;		$alt_m_tot_desc   = 0;
	$alt_ft_tot_asc   = 0;		$alt_m_tot_asc    = 0;
	$alt_max_asc      = 0;
	$alt_max_desc     = 0;
	$subtract_time    = 0;

	while ($row = $result->fetch()) {
		$lat    = $row['Latitude'];
		$lon    = $row['Longitude'];
		$mph    = $row['Speed'] * 2.2369362920544;
		$kph    = $row['Speed'] * 3.6;
		$altft   = $row['Altitude'] * 3.2808399;
		$altm = $row['Altitude'];
		if ($row['ImageURL'] != '')
			$pcount++;
		if ($row['Comments'] != '')
			$ccount++;
		$currdayDB = $row['DateOccurred'];

		if ($rounds == 1) {
			$filterstartNLS     = date_format(date_create_from_format("Y-m-d H:i:s", $currdayDB), $dateformat . " " . $timeformat);
			$firstdayDB         = $currdayDB;
			$alt_ft_start       = $altft;
			$alt_m_start        = $altm;
			$alt_ft_end         = $altft;
			$alt_m_end          = $altm;
		} else {
			$leg_miles          = calcDistance($lat, $lon, $holdlat, $holdlon, "m");
			$leg_feet           = $leg_miles * 5280;
			$total_miles        += $leg_miles;
			$total_kilometers   = $total_miles * 1.609344;
			$alt_ft_end         = $altft;
			$alt_m_end          = $altm;
			if ($leg_feet <= 3) $subtract_time += getElapsedTime($holddayDB, $currdayDB);
			if ($altft - $holdaltft > 0) $alt_ft_tot_asc  += ($altft - $holdaltft);
			if ($altm  - $holdaltm  > 0) $alt_m_tot_asc   += ($altm  - $holdaltm);
			if ($altft - $holdaltft < 0) $alt_ft_tot_desc += ($altft - $holdaltft);
			if ($altm  - $holdaltm  < 0) $alt_m_tot_desc  += ($altm  - $holdaltm);
			if ($leg_feet > 0) if (($altft - $holdaltft)/$leg_feet > $alt_max_asc)  $alt_max_asc  = ($altft - $holdaltft)/$leg_feet*100;
			if ($leg_feet > 0) if (($altft - $holdaltft)/$leg_feet < $alt_max_desc) $alt_max_desc = ($altft - $holdaltft)/$leg_feet*100;
		}

		$holdlat   = $lat;
		$holdlon   = $lon;
		$holdaltft = $altft;
		$holdaltm  = $altm;
		$holddayDB = $currdayDB;
		if ($mph > $speed_mph_max) $speed_mph_max = $mph;
		if ($mph < $speed_mph_min) $speed_mph_min = $mph;
		if ($kph > $speed_kph_max) $speed_kph_max = $kph;
		if ($kph < $speed_kph_min) $speed_kph_min = $kph;
		if ($altft > $alt_ft_max) $alt_ft_max = $altft;
		if ($altft < $alt_ft_min) $alt_ft_min = $altft;
		if ($altm > $alt_m_max) $alt_m_max = $altm;
		if ($altm < $alt_m_min) $alt_m_min = $altm;
		$rounds++;
	}

	$rounds--;
	if ($rounds == 0) {
		$filterstartNLS = $filterstart;
		$filterendNLS   = $filterend;
		$total_time = 0;
	} else {
		$filterendNLS = date_format(date_create_from_format("Y-m-d H:i:s", $currdayDB), $dateformat . " " . $timeformat);
		$total_time = getElapsedTime($firstdayDB, $currdayDB);
	}

	if ($total_time == 0) $total_time = 0.001;
	$display_total_time = gmdate("H:i:s", $total_time);
	$move_time = $total_time - $subtract_time;
	$display_move_time = gmdate("H:i:s", $move_time);
	$startday_date = substr($filterstartNLS, 0, 10);
	$startday_time = substr($filterstartNLS, -8, 8);
	$endday_date   = substr($filterendNLS, 0, 10);
	$endday_time   = substr($filterendNLS, -8, 8);
	$speed_mph_avg = $total_miles      * 3600 / $total_time;
	$speed_kph_avg = $total_kilometers * 3600 / $total_time;
	if ($speed_mph_max > 0) $pace_pmi_max = 60 / $speed_mph_max;
	else                    $pace_pmi_max = 0;
	if ($speed_mph_min > 0) $pace_pmi_min = 60 / $speed_mph_min;
	else                    $pace_pmi_min = 0;
	if ($speed_mph_avg > 0) $pace_pmi_avg = 60 / $speed_mph_avg;
	else                    $pace_pmi_avg = 0;
	if ($speed_kph_max > 0) $pace_pkm_max = 60 / $speed_kph_max;
	else                    $pace_pkm_max = 0;
	if ($speed_kph_min > 0) $pace_pkm_min = 60 / $speed_kph_min;
	else                    $pace_pkm_min = 0;
	if ($speed_kph_avg > 0) $pace_pkm_avg = 60 / $speed_kph_avg;
	else                    $pace_pkm_avg = 0;
	$alt_ft_tot_diff = $alt_ft_end - $alt_ft_start;
	$alt_m_tot_diff  = $alt_m_end  - $alt_m_start;

	if (!$livetracking) {
		$html .= "       <b><u>$filter_title:</u></b>\n";
		$html .= "       <select id=\"setfilterwith\" name=\"setfilterwith\" class=\"pulldown\" onchange=\"document.form_attributes.submit();\" form=\"form_attributes\">\n";
		$html .= "        <option value=\"None\""         . ($filterwith == "None"         ? " selected" : "") . ">$filter_none_text</option>\n";
		$html .= "        <option value=\"Photo\""        . ($filterwith == "Photo"        ? " selected" : "") . ">$filter_photo_text</option>\n";
		$html .= "        <option value=\"Comment\""      . ($filterwith == "Comment"      ? " selected" : "") . ">$filter_comment_text</option>\n";
		$html .= "        <option value=\"PhotoComment\"" . ($filterwith == "PhotoComment" ? " selected" : "") . ">$filter_photo_comment_text</option>\n";
		$html .= "        <option value=\"Last20\""       . ($filterwith == "Last20"       ? " selected" : "") . ">$filter_last20_text</option>\n";
		$html .= "       </select>\n";

		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "         <td style=\"text-align: left;\">\n";
		$html .= "          $filter_startdate_text:\n";
		$html .= "         </td>\n";
		$html .= "         <td style=\"text-align: right;\">\n";
		$html .= "          <input type=\"text\" class=\"textinputfield\" id=\"setfilterstart\" name=\"setfilterstart\" value=\"$filterstartNLS\">\n";
		$html .= "         </td>\n";
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		$html .= "         <td style=\"text-align: left;\">\n";
		$html .= "          $filter_enddate_text:\n";
		$html .= "         </td>\n";
		$html .= "         <td style=\"text-align: right;\">\n";
		$html .= "          <input type=\"text\" class=\"textinputfield\" id=\"setfilterend\" name=\"setfilterend\" value=\"$filterendNLS\">\n";
		$html .= "         </td>\n";
		$html .= "        </tr>\n";
		$html .= "       </table>\n";
		$html .= "       <br><br>\n";
	}

	if ($livetracking) {
		$html .= "       <b><u>$reloadoptions_title:</u></b>\n";
		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "         <td style=\"text-align: right;\">\n";
		$html .= "          $reloadoptions_interval_text:\n";
		$html .= "         </td>\n";
		$html .= "         <td align=\"center\">\n";
		$html .= "          <input type=\"text\" class=\"intervalinputfield\" name=\"interval\" value=\"-\" size=\"1\" style=\"display: inline-block;\" />\n";
		$html .= "         </td>\n";
		$html .= "         <td style=\"text-align: left;\">\n";
		$html .= "          $reloadoptions_sec_text\n";
		$html .= "         </td>\n";
		$html .= "         <td width=\"70%\">\n";
		$html .= "          <input type=\"button\" class=\"button\" name=\"start\" value=\"$start_timer_text\" style=\"display: inline-block;\" onClick=\"clearTimeout(k); initInterval();\" /><br>\n";
		$html .= "         </td>\n";
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		$html .= "         <td style=\"text-align: right;\">\n";
		$html .= "          $reloadoptions_reloadin_text:\n";
		$html .= "         </td>\n";
		$html .= "         <td align=\"center\">\n";
		$html .= "          <input type=\"text\" class=\"intervalinputfield\" name=\"seconds\" value=\"-\" size=\"1\" style=\"display: inline-block;\" readonly/>\n";
		$html .= "         </td>\n";
		$html .= "         <td style=\"text-align: left;\">\n";
		$html .= "          $reloadoptions_sec_text\n";
		$html .= "         </td>\n";
		$html .= "         <td width=\"70%\">\n";
		$html .= "          <input type=\"button\" class=\"button\" name=\"stop\" value=\"$stop_timer_text\" style=\"display: inline-block;\" onClick=\"clearTimeout(k); document.form_attributes.seconds.value = document.form_attributes.interval.value;\" />\n";
		$html .= "         </td>\n";
		$html .= "        </tr>\n";
		$html .= "       </table>\n";

		$html .= "       <br>\n";

		$html .= "       <b><u>$zoomlevel_title:</u></b>\n";
		$html .= "       <select id=\"zoomlevel\" name=\"zoomlevel\" class=\"pulldown\">\n";
		$html .= "        <option value=3"  . ($zoomlevel ==  3 ? " selected" : "") . ">Level 3 ($zoomlevel_world_text)\n";
		$html .= "        <option value=4"  . ($zoomlevel ==  4 ? " selected" : "") . ">Level 4\n";
		$html .= "        <option value=5"  . ($zoomlevel ==  5 ? " selected" : "") . ">Level 5 ($zoomlevel_continent_text)\n";
		$html .= "        <option value=6"  . ($zoomlevel ==  6 ? " selected" : "") . ">Level 6\n";
		$html .= "        <option value=7"  . ($zoomlevel ==  7 ? " selected" : "") . ">Level 7 ($zoomlevel_country_text)\n";
		$html .= "        <option value=8"  . ($zoomlevel ==  8 ? " selected" : "") . ">Level 8\n";
		$html .= "        <option value=9"  . ($zoomlevel ==  9 ? " selected" : "") . ">Level 9 ($zoomlevel_area_text)\n";
		$html .= "        <option value=10" . ($zoomlevel == 10 ? " selected" : "") . ">Level 10\n";
		$html .= "        <option value=11" . ($zoomlevel == 11 ? " selected" : "") . ">Level 11 ($zoomlevel_city_text)\n";
		$html .= "        <option value=12" . ($zoomlevel == 12 ? " selected" : "") . ">Level 12\n";
		$html .= "        <option value=13" . ($zoomlevel == 13 ? " selected" : "") . ">Level 13 ($zoomlevel_village_text)\n";
		$html .= "        <option value=14" . ($zoomlevel == 14 ? " selected" : "") . ">Level 14\n";
		$html .= "        <option value=15" . ($zoomlevel == 15 ? " selected" : "") . ">Level 15 ($zoomlevel_road_text)\n";
		$html .= "        <option value=16" . ($zoomlevel == 16 ? " selected" : "") . ">Level 16\n";
		$html .= "        <option value=17" . ($zoomlevel == 17 ? " selected" : "") . ">Level 17 ($zoomlevel_block_text)\n";
		$html .= "        <option value=18" . ($zoomlevel == 18 ? " selected" : "") . ">Level 18\n";
		$html .= "        <option value=19" . ($zoomlevel == 19 ? " selected" : "") . ">Level 19 ($zoomlevel_house_text)\n";
		$html .= "       </select>\n";

		$html .= "   <br><br>\n";
		if ($units == "metric") {
			$html .= "       <b>$balloon_speed_text: </b>"          . number_format($kph, 2)              . " $balloon_unit_speed_metric_text<br>\n";
			$html .= "       <b>$balloon_altitude_text: </b>"       . number_format($altm, 2)             . " $balloon_unit_altitude_metric_text<br>\n";
			$html .= "       <b>$balloon_total_distance_text: </b>" . number_format($total_kilometers, 2) . " $balloon_unit_distance_metric_text\n";
		} else {
			$html .= "       <b>$balloon_speed_text: </b>"          . number_format($mph, 2)              . " $balloon_unit_speed_imperial_text<br>\n";
			$html .= "       <b>$balloon_altitude_text: </b>"       . number_format($altft, 2)            . " $balloon_unit_altitude_imperial_text<br>\n";
			$html .= "       <b>$balloon_total_distance_text: </b>" . number_format($total_miles, 2)      . " $balloon_unit_distance_imperial_text\n";
		}
	} else {
		$html .= "       <b><u>$summary_trip_info_title:</u></b>\n";
		$html .= "       <br>\n";
		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "          <td><b>$summary_trip_comments_text:</b></td>\n";
		$html .= "          <td>$comments</td>\n";
		$html .= "        </tr>\n";
		$html .= "       </table>\n";
		$html .= "       <br>\n";
		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$balloon_total_distance_text:</b></td>\n";
		$html .= "         <td>" . (($units == "metric") ? number_format($total_kilometers,2) . " $balloon_unit_distance_metric_text" : number_format($total_miles,2) . " $balloon_unit_distance_imperial_text") . "</td>\n";
		$html .= "        </tr>\n";
		$html .= "       </table>\n";
		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_total_time_text:</b>\n";
		$html .= "         <td>$display_total_time</td>\n";
		$html .= "         <td><b>$summary_move_time_text:</b></td>\n";
		$html .= "         <td>$display_move_time</td>\n";
		$html .= "        </tr>\n";
		$html .= "       </table>\n";
		$html .= "       <br>\n";
		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_start_date_time_text:</b></td>\n";
		$html .= "         <td>$startday_date</td>\n";
		$html .= "         <td>$startday_time</td>\n";
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_end_date_time_text:</b></td>\n";
		$html .= "         <td>$endday_date</td>\n";
		$html .= "         <td>$endday_time</td>\n";
		$html .= "        </tr>\n";
		$html .= "       </table>\n";
		$html .= "       <br>\n";
		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>&nbsp;</b></td>\n";
		$html .= "         <td><b>$summary_max_text</b></td>\n";
		$html .= "         <td><b>$summary_min_text</b></td>\n";
		$html .= "         <td><b>$summary_avg_text</b></td>\n";
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_speed_text:</b></td>\n";
		if ($units == "metric") {
			$html .= "         <td>" . number_format($speed_kph_max, 2) . " $balloon_unit_speed_metric_text</td>\n";
			$html .= "         <td>" . number_format($speed_kph_min, 2) . " $balloon_unit_speed_metric_text</td>\n";
			$html .= "         <td>" . number_format($speed_kph_avg, 2) . " $balloon_unit_speed_metric_text</td>\n";
		} else {
			$html .= "         <td>" . number_format($speed_mph_max, 2) . " $balloon_unit_speed_imperial_text</td>\n";
			$html .= "         <td>" . number_format($speed_mph_min, 2) . " $balloon_unit_speed_imperial_text</td>\n";
			$html .= "         <td>" . number_format($speed_mph_avg, 2) . " $balloon_unit_speed_imperial_text</td>\n";
		}
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_pace_text:</b></td>\n";
		if ($units == "metric") {
			$html .= "         <td>" . number_format($pace_pkm_max, 2) . " $balloon_unit_pace_metric_text</td>\n";
			$html .= "         <td>" . number_format($pace_pkm_min, 2) . " $balloon_unit_pace_metric_text</td>\n";
			$html .= "         <td>" . number_format($pace_pkm_avg, 2) . " $balloon_unit_pace_metric_text</td>\n";
		} else {
			$html .= "         <td>" . number_format($pace_pmi_max, 2) . " $balloon_unit_pace_imperial_text</td>\n";
			$html .= "         <td>" . number_format($pace_pmi_min, 2) . " $balloon_unit_pace_imperial_text</td>\n";
			$html .= "         <td>" . number_format($pace_pmi_avg, 2) . " $balloon_unit_pace_imperial_text</td>\n";
		}
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_alt_text:</b></td>\n";
		if ($units == "metric") {
			$html .= "         <td>" . number_format($alt_m_max, 0) . " $balloon_unit_altitude_metric_text</td>\n";
			$html .= "         <td>" . number_format($alt_m_min, 0) . " $balloon_unit_altitude_metric_text</td>\n";
		} else {
			$html .= "         <td>" . number_format($alt_ft_max, 0) . " $balloon_unit_altitude_imperial_text</td>\n";
			$html .= "         <td>" . number_format($alt_ft_min, 0) . " $balloon_unit_altitude_imperial_text</td>\n";
		}
		$html .= "         <td><b>&nbsp;</b></td>\n";
		$html .= "        </tr>\n";
		$html .= "       </table>\n";
		$html .= "       <br>\n";
		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>&nbsp;</b></td>\n";
		$html .= "         <td><b>$summary_start_text</b></td>\n";
		$html .= "         <td><b>$summary_end_text</b></td>\n";
		$html .= "         <td><b>$summary_diff_text</b></td>\n";
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_alt_text:</b></td>\n";
		if ($units == "metric") {
			$html .= "         <td>" . number_format($alt_m_start, 0)    . " $balloon_unit_altitude_metric_text</td>\n";
			$html .= "         <td>" . number_format($alt_m_end, 0)      . " $balloon_unit_altitude_metric_text</td>\n";
			$html .= "         <td>" . number_format($alt_m_tot_diff, 0) . " $balloon_unit_altitude_metric_text</td>\n";
		} else {
			$html .= "         <td>" . number_format($alt_ft_start, 0)    . " $balloon_unit_altitude_imperial_text</td>\n";
			$html .= "         <td>" . number_format($alt_ft_end, 0)      . " $balloon_unit_altitude_imperial_text</td>\n";
			$html .= "         <td>" . number_format($alt_ft_tot_diff, 0) . " $balloon_unit_altitude_imperial_text</td>\n";
		}
		$html .= "        </tr>\n";
		$html .= "       </table>\n";
		$html .= "       <br>\n";
		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>&nbsp;</b></td>\n";
		$html .= "         <td><b>$summary_total_text</b></td>\n";
		$html .= "         <td><b>$summary_max_text</b></td>\n";
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		if ($units == "metric") {
			$html .= "         <td><b>$summary_asc_text:</b></td>\n";
			$html .= "         <td>" . number_format($alt_m_tot_asc, 0) . " $balloon_unit_altitude_metric_text</td>\n";
			$html .= "         <td>" . number_format($alt_max_asc, 2)   . " %</td>\n";
			$html .= "        </tr>\n";
			$html .= "        <tr>\n";
			$html .= "         <td><b>$summary_desc_text:</b></td>\n";
			$html .= "         <td>" . number_format($alt_m_tot_desc, 0) . " $balloon_unit_altitude_metric_text<br>\n";
			$html .= "         <td>" . number_format($alt_max_desc, 2)   . " %<br>\n";
		} else {
			$html .= "         <td><b>$summary_asc_text:</b></td>\n";
			$html .= "         <td>" . number_format($alt_ft_tot_asc, 0). " $balloon_unit_altitude_imperial_text</d>\n";
			$html .= "         <td>" . number_format($alt_max_asc, 2)   . " %</td>\n";
			$html .= "        </tr>\n";
			$html .= "        <tr>\n";
			$html .= "         <td><b>$summary_desc_text:</b></td>\n";
			$html .= "         <td>" . number_format($alt_ft_tot_desc, 0) . " $balloon_unit_altitude_imperial_text</td>\n";
			$html .= "         <td>" . number_format($alt_max_desc, 2)    . " %</td>\n";
		}
		$html .= "        </tr>\n";
		$html .= "       </table>\n";
		$html .= "       <br>\n";
		$html .= "       <table>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_points_text:</b></td>\n";
		$html .= "         <td>$rounds</td>\n";
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_waypoint_comments_text:</b></td>\n";
		$html .= "         <td>$ccount</td>\n";
		$html .= "        </tr>\n";
		$html .= "        <tr>\n";
		$html .= "         <td><b>$summary_photos_text:</b></td>\n";
		$html .= "         <td>$pcount</td>\n";
		$html .= "        </tr>\n";
		$html .= "       </table>\n";
		$html .= "       <br><br>\n";
	}

	$html .= "       <input type=\"hidden\" name=\"ID\" value=\"$ID\" />\n";
	$html .= "       <input type=\"hidden\" name=\"username\" value=\"$username\" />\n";
	$html .= "       <input type=\"hidden\" name=\"password\" value=\"$password\" />\n";
	$html .= "      </form>\n"; // form_attributes

	if (!$livetracking) {
		// 2009-05-07 DMR Add Link to download the currently displayed data. -->
		$html .= "      <b><u>$downloadtrip_title:</u></b><br>\n";
		if ($filterstartNLS != null && trim($filterstartNLS) != "") {
			$filterstartDB = date_format(date_create_from_format($dateformat . " " . $timeformat, $filterstartNLS), "Y-m-d H:i:s");
			$filterendDB   = date_format(date_create_from_format($dateformat . " " . $timeformat, $filterendNLS  ), "Y-m-d H:i:s");
		} else {
			$filterstartDB = "";
			$filterendDB   = "";
		}
		$exportOptions  = "&db=8";
		$exportOptions .= "&df=" . urlencode($filterstartDB);
		$exportOptions .= "&dt=" . urlencode($filterendDB);
		$exportOptions .= "&tn=" . urlencode($tripname);
		$exportOptions .= "&sb=" . ($showbearings ? "yes" : "no");
		$html .= "      <form id=\"form_downloadkml\" name=\"form_downloadkml\" method=\"post\" action=\"download.php?a=kml" . $exportOptions . "\" style=\"display: inline;\">\n";
		$html .= "       <input type=\"hidden\" name=\"ID\" value=\"$ID\" />\n";
		$html .= "       <input type=\"submit\" class=\"buttonshort\" id=\"downloadkml\" value=\"KML Format\" />\n";
		$html .= "      </form>\n"; // form_downloadkml
//		$html .= "      &nbsp;\n";
		$html .= "      <form id=\"form_downloadgpx\" name=\"form_downloadgpx\" method=\"post\" action=\"download.php?a=gpx" . $exportOptions . "\" style=\"display: inline;\">\n";
		$html .= "       <input type=\"hidden\" name=\"ID\" value=\"$ID\" />\n";
		$html .= "       <input type=\"submit\" class=\"buttonshort\" id=\"downloadgpx\" value=\"GPX Format\" />\n";
		$html .= "      </form>\n"; // form_downloadgpx
		// 2009-05-07 DMR Add Link to download the currently displayed data. <--
	}
	$html .= "      <br><br><br>\n";
	$html .= "     </div>\n"; // navigationsection

	$html .= "     <script>\n";
	$html .= "      lang.setCode(\"$lang->code\");\n";
	if ($livetracking) {
	} else {
		$options = "dateFormat: \"$dateformat $timeformat\", locale: \"$lang->code\", enableTime: true, enableSeconds: true, hourIncrement: 1, minuteIncrement: 1, time_24hr: true, shorthandCurrentMonth: true, onClose: function() { document.form_attributes.submit(); }";
		$html .= "      document.addEventListener(\"DOMContentLoaded\", function() { flatpickr(\"#setfilterstart\", { $options }); })\n";
		$options = "dateFormat: \"$dateformat $timeformat\", locale: \"$lang->code\", enableTime: true, enableSeconds: true, hourIncrement: 1, minuteIncrement: 1, time_24hr: true, shorthandCurrentMonth: true, onClose: function() { document.form_attributes.submit();   }";
		$html .= "      document.addEventListener(\"DOMContentLoaded\", function() { flatpickr(\"#setfilterend\",   { $options }); })\n";
	}
	$html .= "      cookieAgreement(\n";
	$html .= "        \"linecolor=$linecolor\", \n";
	$html .= "        \"showbearings=$showbearings\", \n";
	$html .= "        \"markertype=$markertype\", \n";
	$html .= "        \"crosshair=$crosshair\", \n";
	$html .= "        \"clickcenter=$clickcenter\", \n";
	$html .= "        \"language=$language\", \n";
	$html .= "        \"units=$units\", \n";
	$html .= "        \"tileprovider=$tileprovider\", \n";
	$html .= "        \"tilePT=$tilePT\", \n";
	$html .= "        \"tripID=$tripID\", \n";
	$html .= "        \"tripgroup=$tripgroup\", \n";
	$html .= "        \"filterwith=$filterwith\", \n";
	$html .= "        \"filterstart=$filterstart\", \n";
	$html .= "        \"filterend=$filterend\", \n";
	$html .= "        \"livetracking=$livetracking\", \n";
	$html .= "        \"chartdisplay=$chartdisplay\", \n";
	$html .= "        \"attributedisplay=$attributedisplay\", \n";
	$html .= "        \"interval=$interval\", \n";
	$html .= "        \"zoomlevel=$zoomlevel\"\n";
	$html .= "      );\n";

	$html .= "      var charttype = \"elevation\";\n";
	$html .= "      var username = \"$username\";\n";
	$html .= "      var password = \"$password\";\n";
	$html .= "      var interval = " . ($interval == "-" ? "\"$interval\"" : $interval) . ";\n";
	if (!$livetracking) {
		$html .= "      var tripname = \"$tripname\";\n";

		$html .= "      var colorWheel = new iro.ColorPicker(\"#colorLine\", { width: 200, color: \"$linecolor\", handleRadius: 3, display: \"inline-block\", layout: [{component: iro.ui.Slider, options: { sliderType: \"hue\" } }] });\n";
		$html .= "      colorWheel.on(\"color:change\", function(color, changes){ document.getElementById(\"setlinecolor\").value = color.hexString; document.form_attributes.submit(); });\n";
	}

	$html .= "      // Leaflet Map API initialisation\n";
	$html .= "      var map = L.map(\"mapsection\", {center: [0, 0], zoom: 0});\n";
	$html .= "      L.tileLayer(\"".$tileproviders[$tileprovider]['url'] . "\", {\n";
	if (isset($tileproviders[$tileprovider]['maxZoom'])) {
		$html .= "        maxZoom: " . $tileproviders[$tileprovider]['maxZoom'] . ",\n";
	}
	$html .= "        attribution: '" . escapeJSString($tileproviders[$tileprovider]['attribution']) . "'\n";
	$html .= "       }).addTo(map);\n";
	if ($tilePT) {
		$html .= "      L.tileLayer('http://openptmap.org/tiles/{z}/{x}/{y}.png', {\n";
		$html .= "        maxZoom: 17,\n";
		$html .= "       attribution: 'Map data: &copy; <a href=\"http://www.openptmap.org\">OpenPtMap</a> contributors'\n";
		$html .= "       }).addTo(map);\n";
	}
	$html .= "      var bounds = null;\n";
	$html .= "      var markers = [];\n";
	$html .= "      var markersLatLng = [];\n";

	if ($crosshair) {
		$html .= "      var centerCrosshair = L.icon({iconUrl: 'crosshair.gif', iconSize: [17, 17], iconAnchor: [8, 8],});\n";
		$html .= "      centerCross = L.marker(map.getCenter(), {icon: centerCrosshair, interactive: false}).addTo(map);\n";
		$html .= "      function setCenterCross() {\n";
		$html .= "       centerCross.setLatLng(map.getCenter());\n";
		$html .= "      };\n";
		$html .= "      map.on(\"zoom move load viewreset resize zoomlevelchange\", setCenterCross);\n";
	}
	if ($clickcenter) {
		$html .= "      map.on(\"click\", function(e) { map.panTo(e.latlng); });\n";
	} else {
		$html .= "      map.off(\"click\");\n";
	}

	$html .= "      var showBearings  = " . ($showbearings ? "true" : "false") . ";\n";
	$html .= "      var useMetric     = " . ($units == "metric" ? "true" : "false") . ";\n";
	$html .= "      var allowDBchange = " . ($allowDBchange == "yes" ? "true" : "false") . ";\n";

	$params = array($ID);
	$where  = " FK_Users_ID=?";
	if ($livetracking) {
		$where .= "";
		$limit = 1;
	} else {
		if ($filterwith == "Photo") {
			$where .= " AND ImageURL!=''";
			$limit = 0;
		} elseif ($filterwith == "Comment") {
			$where .= " AND Comments!=''";
			$limit = 0;
		} elseif ($filterwith == "PhotoComment") {
			$where .= " AND (Comments!='' OR ImageURL!='')";
			$limit = 0;
		} elseif ($filterwith == "Last20") {
			$where .= "";
			$limit = 20;
		} else {
			$where .= "";
			$limit = 0;
		}

		if ($tripname !== $trip_any_text) {
			$where .= " AND FK_Trips_ID=?";
			$params[] = $tripID;
		} else
		if ($tripgroup !== $no_tripgroup_text) {
			$where .= " AND FK_Trips_ID IN (SELECT ID from trips WHERE Name LIKE ?)";
			$params[] = $tripgroup . ":%";
		} else {
			$where .= " AND FK_Trips_ID NOT IN (SELECT ID from trips WHERE Name LIKE ?)";
			$params[] = "%:%";
		}

		if ($filterstartNLS != null && trim($filterstartNLS) != "") {
			$filterstartDB = date_format(date_create_from_format($dateformat . " " . $timeformat, $filterstartNLS), "Y-m-d H:i:s");
			$filterendDB   = date_format(date_create_from_format($dateformat . " " . $timeformat, $filterendNLS  ), "Y-m-d H:i:s");
			$where .= " AND DateOccurred BETWEEN ? AND ?";
			$params[] = $filterstartDB;
			$params[] = $filterendDB;
		}
	}
	if ($limit > 0) {
		$limit = " DESC LIMIT " . $limit;
	} else {
		$limit = "";
	}

	$stmt = "SELECT positions.*, icons.URL FROM positions LEFT JOIN icons ON positions.FK_Icons_ID=icons.ID WHERE$where ORDER BY DateOccurred$limit";
	if ($debug1) debug2console("SQL for the Trips:" , $stmt);
	if ($debug1) debug2console("SQL for the Trips:" , $params);
	$positions = $db->exec_sql($stmt, $params);
	$result = $positions->fetchAll();
	$count = count($result);

	$altsfeet     = "";		$altsmeters   = "";
	$altfeetmax   = 0;		$altmetersmax = 0;
	$altfeetmin   = 999999;		$altmetersmin = 999999;
	$speedsmph    = "";		$speedskph    = "";
	$speedmphmax  = 0;		$speedkphmax  = 0;
	$speedmphmin  = 999999;		$speedkphmin  = 999999;
	$pitchesm     = "";		$pitchesk     = "";
	$pitchmmax    = 0;		$pitchkmax    = 0;
	$pitchmmin    = 999999;		$pitchkmin    = 999999;

	$any = ($tripname == $trip_any_text ? "true" : "false");
	$lasttripID   = "";
	$lasttripname = "";
	if ($debug1) debug2console("We have rounds:", $count);
	for ($rounds = 1; $rounds <= $count; $rounds++) {
		$row = $result[$rounds - 1];
		if ($debug1) debug2console("In round:", $rounds);
		if ($debug1) debug2console("with trip:", $row);
		$row['ImageURL'] = escapeJSString($row['ImageURL']);
		$row['Comments'] = escapeJSString($row['Comments']);

		$tripname = $db->exec_sql("SELECT Name FROM trips WHERE ID=?", $row['FK_Trips_ID'])->fetchColumn();
		$tripname = escapeJSString($tripname);
		if ($lasttripID != $row['FK_Trips_ID']) {
			if ($rounds != 1) {
				$html .= "   trip.end(\"$lasttripname\", \"$linecolor\", $any);\n";
			}
			$html .= "      var trip = new Trip(\"" . $tripname . "\", \"" . escapeJSString($username) . "\");\n";
		}

		if (!is_null($row['URL'])) {
			$parameter = "'" . $row['URL'] . "'";
		} elseif ($rounds == 1) {
			$parameter = "iconGreen";
		} elseif ($rounds < $count) {
			if ($markertype) {
				$parameter = "true";
			} else{
				$parameter = "false";
			}
		} else {
			$parameter = "iconRed";
		}

		$dataParameter = "";
		if (!is_null($row['Angle']))
			$dataParameter = ", bearing: " . $row['Angle'];

		$formattedTS  = escapeJSString(date($dateformat . " " . $timeformat, strtotime($row['DateOccurred'])));

		$html .= "      trip.appendMarker({latitude: " . $row['Latitude'] . ", longitude: " . $row['Longitude'] . ", tripname: '" . $tripname . "', timestamp: '" . $row['DateOccurred']. "', speed: " . $row['Speed'] . ", altitude: " . $row['Altitude'] . ", comment: '" . $row['Comments'] . "', photo: '" . $row['ImageURL'] . "'" . $dataParameter . ", formattedTS: '" . $formattedTS . "'}, " . $parameter . ", " . $row['ID']. ");\n";

		$lat   = $row['Latitude'];
		$lon   = $row['Longitude'];
		$altft = $row['Altitude'] * 3.2808399;
		$altm  = $row['Altitude'];
		$mph   = $row['Speed'] * 2.2369362920544;
		$kph   = $row['Speed'] * 3.6;
		if ($rounds == 1) {
			$total_miles = 0;
			$pitch       = 0;
		} else {
			$leg_miles   = calcDistance($lat, $lon, $holdlat, $holdlon, "m");
			$leg_feet    = $leg_miles * 5280;
			$total_miles += $leg_miles;
			$pitch       = ($leg_feet > 0) ? ($altft - $holdaltft)/$leg_feet*100 : 0;
		}
		$total_kilometers   = $total_miles * 1.609344;

		$altsfeet   .= strval($total_miles)      . ":" . strval($altft) . ",";
		$altsmeters .= strval($total_kilometers) . ":" . strval($altm) . ",";
		$speedsmph  .= strval($total_miles)      . ":" . strval($mph) . ",";
		$speedskph  .= strval($total_kilometers) . ":" . strval($kph) . ",";
		$pitchesm   .= strval($total_miles)      . ":" . strval($pitch) . ",";
		$pitchesk   .= strval($total_kilometers) . ":" . strval($pitch) . ",";

		$holdlat   = $lat;
		$holdlon   = $lon;
		$holdaltft = $altft;

		if ($altft   > $altfeetmax)   $altfeetmax   = $altft;
		if ($altft   < $altfeetmin)   $altfeetmin   = $altft;
		if ($altm > $altmetersmax) $altmetersmax = $altm;
		if ($altm < $altmetersmin) $altmetersmin = $altm;
		if ($mph    > $speedmphmax)  $speedmphmax  = $mph;
		if ($mph    < $speedmphmin)  $speedmphmin  = $mph;
		if ($kph    > $speedkphmax)  $speedkphmax  = $kph;
		if ($kph    < $speedkphmin)  $speedkphmin  = $kph;
		if ($pitch  > $pitchmmax)    $pitchmmax    = $pitch;
		if ($pitch  < $pitchmmin)    $pitchmmin    = $pitch;
		if ($pitch  > $pitchkmax)    $pitchkmax    = $pitch;
		if ($pitch  < $pitchkmin)    $pitchkmin    = $pitch;

		$lasttripID   = $row['FK_Trips_ID'];
		$lasttripname = $tripname;
	}
	if ($count != 0) $html .= "      trip.end(\"$lasttripname\", \"$linecolor\", $any);\n";

	if ($units == "metric") {
		$alts      = $altsmeters;
		$speeds    = $speedskph;
		$pitches   = $pitchesk;
		$altunit   = $balloon_unit_altitude_metric_text;
		$speedunit = $balloon_unit_speed_metric_text;
		$altmax    = number_format($altmetersmax, 0);
		$altmin    = number_format($altmetersmin, 0);
		$speedmax  = number_format($speedkphmax, 1);
		$speedmin  = number_format($speedkphmin, 1);
		$pitchmax  = number_format($pitchkmax, 0);
		$pitchmin  = number_format($pitchkmin, 0);
	} else {
		$alts      = $altsfeet;
		$speeds    = $speedsmph;
		$pitches   = $pitchesm;
		$altunit   = $balloon_unit_altitude_imperial_text;
		$speedunit = $balloon_unit_speed_imperial_text;
		$altmax    = number_format($altfeetmax, 0);
		$altmin    = number_format($altfeetmin, 0);
		$speedmax  = number_format($speedmphmax, 1);
		$speedmin  = number_format($speedmphmin, 1);
		$pitchmax  = number_format($pitchmmax, 0);
		$pitchmin  = number_format($pitchmmin, 0);
	}
	$alts     = substr($alts,    0, -1);
	$speeds   = substr($speeds,  0, -1);
	$pitches  = substr($pitches, 0, -1);

	$html .= "      if (bounds == null) {\n";
	$html .= "       bounds = L.latLngBounds(L.latLng(0, 0).toBounds(12000000));\n";
	$html .= "      }\n";
	$html .= "      map.fitBounds(bounds);\n";

	if ($livetracking) {
		$html .= "      map.setZoom($zoomlevel);\n";
	}
	$html .= "     </script>\n";

	$html .= "     <div id=\"chartsection\" style=\"display: none;\">\n";
	$html .= "      <div style=\"width: 100%; text-align: right;\">\n";  //ROB ???
	$html .= "       <span id=\"chartelevation\" class=\"charttypeselected\" onclick=\"elevationclick();\">$balloon_altitude_text [$altunit]</span>&nbsp;&nbsp;&nbsp;&nbsp;\n";
	$html .= "       <span id=\"chartspeed\"     class=\"charttype\"         onclick=\"speedclick();    \">$balloon_speed_text [$speedunit]</span>&nbsp;&nbsp;&nbsp;&nbsp;\n";
	$html .= "       <span id=\"chartpitch\"     class=\"charttype\"         onclick=\"pitchclick();    \">$balloon_pitch_text [%]</span>\n";
	$html .= "       &nbsp;&nbsp;&nbsp;\n";
	$html .= "       <span class=\"chartsparkline\" elevationvalues=\"$alts\" speedvalues=\"$speeds\" pitchvalues=\"$pitches\" sparktype=\"line\" sparklineWidth=\"2\" sparkwidth=\"100%\" sparkheight=\"18.5vh\" sparkspotRadius=\"3\">...</span>\n";
	$html .= "       <span class=\"chartmax\" id=\"chartaltmax\"   style=\"display: none;\">$altmax</span>\n";
	$html .= "       <span class=\"chartmax\" id=\"chartspeedmax\" style=\"display: none;\">$speedmax</span>\n";
	$html .= "       <span class=\"chartmax\" id=\"chartpitchmax\" style=\"display: none;\">$pitchmax</span>\n";
	$html .= "       <span class=\"chartmin\" id=\"chartaltmin\"   style=\"display: none;\">$altmin</span>\n";
	$html .= "       <span class=\"chartmin\" id=\"chartspeedmin\" style=\"display: none;\">$speedmin</span>\n";
	$html .= "       <span class=\"chartmin\" id=\"chartpitchmin\" style=\"display: none;\">$pitchmin</span>\n";
	$html .= "      </div>\n";
	$html .= "     </div>\n"; // chartsection

	$html .= "    </td>\n";
	$html .= "   </tr>\n";
	$html .= "  </table>\n";

	$html .= "  <script>\n";
	$html .= "   document.getElementById(\"navigationsectioncell\").style.width = \"$navigationwidth\";\n";
//	$html .= "   document.getElementById(\"trackmeviewer\").style.display='none';\n";
//	$html .= "   document.getElementById(\"trackmeviewer\").offsetHeight;\n";
//	$html .= "   document.getElementById(\"trackmeviewer\").style.display='block';\n";
	$html .= "   $(document.getElementById(\"trackmeviewer\")).colResizable({liveDrag:true, gripInnerHtml:\"<div class='grip'></div>\", draggingClass:\"dragging\", onResize:navigationwidthchange});\n";
	$html .= "  </script>\n";

	if ($chartdisplay) {
		$html .= "  <script>\n";
		$html .= "   showChart();\n";
		$html .= "  </script>\n";
	}

	$html .= "  <!-- <div id=\"footertext\">$footer_text <a href=\"http://forum.xda-developers.com/showthread.php?t=340667\" target=\"_blank\">TrackMe</a></div> -->\n";

//	if (isset($googleanalyticsaccount)) {
//		$html .= "   if (getCookieValue(\"cookieconsent_status\") == \"allow\") {\n";
//		$html .= "    var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\n";
//		$html .= "    document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\n";
//		$html .= "   }";
//		$html .= "  </script>\n";
//		$html .= "  <script>\n";
//		$html .= "   if (getCookieValue(\"cookieconsent_status\") == \"allow\") {\n";
//		$html .= "    var pageTracker = _gat._getTracker(\"$googleanalyticsaccount\");\n";
//		$html .= "    pageTracker._initData();\n";
//		$html .= "    pageTracker._trackPageview();\n";
//		$html .= "   }";
//		$html .= "  </script>\n";
//	}

	$html .= " </body>\n";
	$html .= "</html>\n";

	$db = null;
	print $html;
	exit;

	// Function to calculate distance between points
	function calcDistance($lat1, $lon1, $lat2, $lon2, $unit) {
		if ($lat1 == $lat2 && $lon1 == $lon2) { return 0; }
		$theta = $lon1 - $lon2;
		if (abs(deg2rad($lat1 - $lat2)) < 0.0000001 && abs(deg2rad($theta)) < 0.0000001) return 0;
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

	function debug2console($prompt, $data) {
		$output = $data;
		if (is_array($output)) $output = mapped_implode(",", $output, "=");
		echo "<script>console.log(\"Debug Objects: $prompt >>>>> $output\");</script>";
		return;
	}

	function mapped_implode($glue, $array, $symbol = '=') {
		return implode($glue, array_map(
			function($k, $v) use($symbol) {
				return $k . $symbol . $v;
			},
			array_keys($array),
			array_values($array)
			)
		);
	}

	function gettripnameparts($fulltripname, $no_tripgroup) {
		$tripnameparts = explode(":", $fulltripname, 2);
		if (isset($tripnameparts[1])) {
			$trippartgroup = $tripnameparts[0];
			$trippartname  = $tripnameparts[1];
		} else {
			$trippartgroup = $no_tripgroup;
			$trippartname  = $tripnameparts[0];
		}
		return array($trippartgroup, $trippartname);
	}
?>
