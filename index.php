<?php

    session_start();

    require_once("database.php");


  if (dirname($_SERVER['PHP_SELF'])=="/") {
    $siteroot ="http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
  }
  else {
    $siteroot ="http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/";
  }

    $action            = $_REQUEST["action"];
    $storeshowbearings = $_REQUEST["storeshowbearings"];
    $storecrosshair    = $_REQUEST["storecrosshair"];
    $storeclickcenter  = $_REQUEST["storeclickcenter"];
    $storelanguage     = $_REQUEST["storelanguage"];
    $storeunits        = $_REQUEST["storeunits"];
    $storestartdate    = $_REQUEST["storestartdate"];
    $storeenddate      = $_REQUEST["storeenddate"];
    $custom_view       = $_REQUEST["custom_view"];

    if ($action == "form_display" || $custom_view == "yes")
    {
        if($_REQUEST["setcrosshair"])
        {
            $crosshair = "yes";
        }
        elseif($action == "form_display")
        {
            $crosshair = "no";
        }
        else
        {
            $crosshair = $storecrosshair;
        }

        if($_REQUEST["setclickcenter"])
        {
            $clickcenter = "yes";
        }
        elseif($action == "form_display")
        {
            $clickcenter = "no";
        }
        else
        {
            $clickcenter = $storeclickcenter;
        }

        if($_REQUEST["setshowbearings"])
        {
            $show_bearings    = "yes";
        }
        elseif($action == "form_display")
        {
            $show_bearings    = "no";
        }
        else
        {
            $show_bearings    = $storeshowbearings;
        }

        $units    = $storeunits;
        if($_REQUEST["units"])
        {
            $units       = $_REQUEST["units"];
        }
        $language    = $storelanguage;
        if($_REQUEST["language"])
        {
            $language       = $_REQUEST["language"];
        }

        $storecrosshair   = $crosshair;
        $storeclickcenter = $clickcenter;
        $storeunits       = $units;
        $storelanguage    = $language;
        $storeshowbearings = $show_bearings;
        $custom_view      = "yes";
    }

    require_once('language.php');

    try {
        $db = connect();
    } catch (PDOException $e) {
        $db = null;
        $html  = " <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
        $html .= "        <head>\n";
        $html .= "            <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
	$html .= "            <link rel=\"shortcut icon\" href=\"favicon.ico\">\n";
	$html .= "            <title>$title_text (v" . $version_text . ")</title>\n";
        $html .= "        </head>\n";
        $html .= "        <body bgcolor=\"$bgcolor\">\n";
        $html .= "            <div align=center>\n";
	$html .= "                $database_fail_text<br>\n";
        $html .= "                <br>\n";
        $html .= "                " . $e->getMessage() . "<br>\n";
        $html .= "                <br>\n";
        $html .= "                <br>\n";
        $html .= "                <br>\n";
        $html .= "                <br>\n";
    }

    // Delete trip
    if (isset($_GET['deleteTrip']) && is_numeric($_GET['deleteTrip'])) {
        if (isset($_SESSION["ID"]))
        {
            $tripId = (int)$_GET['deleteTrip'];
            $trip = $db->exec_sql("SELECT `FK_Users_ID` FROM `trips` WHERE `ID` = ?", $tripId)->fetch();
            if ($trip === false)
                $err = $lang["delete-trip-wrong-id"];
            else if ($trip["FK_Users_ID"] !== $_SESSION["ID"])
                $err = $lang["delete-trip-not-owner"];
            else
                $err = false;
        }
        else
        {
            $err = $lang["delete-trip-no-login"];
        }

        if ($err === false)
        {
            try {
                $db->beginTransaction();
                $db->exec_sql("DELETE FROM `trips` WHERE `ID` = ?", $tripId);
                $db->exec_sql("DELETE FROM `positions` WHERE `FK_Trips_ID` = ?", $tripId);
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                $err = $e->getMessage();
            }
        }
        if ($err === false)
        {
            header('Location: '.$siteroot);
        }
        else
        {
            $html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
            $html .= "    <head>\n";
            $html .= "        <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
            $html .= "        <link rel=\"shortcut icon\" href=\"favicon.ico\">\n";
            $html .= "        <title>$title_text (v" . $version_text . ")</title>\n";
            $html .= "    </head>\n";
            $html .= "    <body bgcolor=\"$bgcolor\">\n";
            $html .= "        <div align=center>\n";
            $html .= "            " . $lang["delete-trip-title"] . "<br>\n";
            $html .= "            <br>\n";
            $html .= "            $err<br>\n";
            $html .= "            <br>\n";
            $html .= "            <br>\n";
            $html .= "            <br>\n";
            $html .= "            <br>\n";
        }
        $db = null;
    }

    if (!is_null($db))
    {
        $num_users = $db->get_count("users");
        $num_trips = $db->get_count("trips");
        $num_positions = $db->get_count("positions");
        $num_icons = $db->get_count("icons");


        $filter            = $_REQUEST["filter"];
        $trip              = preg_replace("/[^a-zA-Z0-9]/", "", $_REQUEST["trip"]);
        $ID                = $_REQUEST["ID"];
        $username          = $_REQUEST["username"];
        $password          = $_REQUEST["password"];
        $startday          = preg_replace("/[^0-9 :\-]/", "", $_REQUEST["startday"]);
        $endday            = preg_replace("/[^0-9 :\-]/", "", $_REQUEST["endday"]);

        if (is_numeric($trip) && !isset($ID))
        {
            $ID = $db->exec_sql("SELECT FK_Users_ID FROM trips WHERE ID = ?",
                                $trip)->fetchColumn();
            if ($ID === false)
                unset($ID);
        }

        if ($num_users < 1 || $num_trips < 1 || $num_positions < 1 || $num_icons < 1)
        {
            $html  = "    <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
            $html .= "        <head>\n";
            $html .= "            <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
			$html .= "            <link rel=\"shortcut icon\" href=\"favicon.ico\">\n";
            $html .= "            <title>$title_text (v" . $version_text . ")</title>\n";
            $html .= "            <script src=\"https://maps.googleapis.com/maps/api/js?key=$googleapikey\" type=\"text/javascript\"></script>\n";
			$html .= "        </head>\n";
            $html .= "        <body bgcolor=\"$bgcolor\">\n";
            $html .= "            <div align=center>\n";
            $html .= "                $no_data_text<br>\n";
            $html .= "                <br>\n";
        }
        elseif (file_exists("install.php") || file_exists("database.sql"))
        {
            $html  = "    <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
            $html .= "        <head>\n";
            $html .= "            <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
			$html .= "            <link rel=\"shortcut icon\" href=\"favicon.ico\">\n";
            $html .= "            <title>$title_text (v" . $version_text . ")</title>\n";
            $html .= "            <script src=\"https://maps.googleapis.com/maps/api/js?key=$googleapikey\" type=\"text/javascript\"></script>\n";
			$html .= "        </head>\n";
            $html .= "        <body bgcolor=\"$bgcolor\">\n";
            $html .= "            <div align=center>\n";
            $html .= "                $incomplete_install_text<br>\n";
            $html .= "                <br>\n";
        }
        else
        {
            if($public_page != "yes")
            {
                if ($action == "logout")
                {
                    unset($_SESSION['ID']);
                }

                if(isset($username) && isset($password))
                {
                    if (preg_match("/^([a-zA-Z0-9._])+$/", "$_REQUEST[username]"))
                    {
                        $login_id = $db->valid_login($username, $password);
                        if ($login_id >= 0)
                        {
                            $_SESSION['ID'] = $login_id;
                        }
                    }
                }
                $ID = $_SESSION['ID'];
            }

            if(isset($_SESSION['ID']) || $public_page == "yes")
            {
                $html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
                $html .= "    <head>\n";
                $html .= "        <meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">\n";
		$html .= "        <link rel=\"shortcut icon\" href=\"favicon.ico\">\n";
                $html .= "        <title>$title_text (v" . $version_text . ")</title>\n";
                $html .= "        <link rel=\"stylesheet\" href=\"layout.css\" type=\"text/css\">\n";
                $html .= "        <link rel=\"stylesheet\" href=\"calendar-win2k-cold-1.css\" type=\"text/css\">\n";
                $html .= "        <script type=\"text/javascript\" src=\"calendar.js\"></script>\n";
                $html .= "        <script type=\"text/javascript\" src=\"lang/calendar-en.js\"></script>\n";
                $html .= "        <script type=\"text/javascript\" src=\"calendar-setup.js\"></script>\n";
		$html .= "        <script src=\"https://maps.googleapis.com/maps/api/js?key=$googleapikey\" type=\"text/javascript\"></script>\n";
                $html .= "        <script type=\"text/javascript\" src=\"main.js\"></script>\n";
                $html .= "        <script type=\"text/javascript\" src=\"lang.js\"></script>\n";
		$html .= "    </head>\n";

if(isset($_REQUEST[last_location])){
$html .= "<BODY  onload=\"init_interval()\">\n";
				   }
				 else
				  {
$html .= "<BODY>\n";
				  }
$html .= "            <script type=\"text/javascript\">\n";
$html .= "        function getValue(varname)      \n";
$html .= "        {      \n";
$html .= "          var url = window.location.href;      \n";
$html .= "          var qparts = url.split(\"?\");      \n";
$html .= "          if (qparts.length == 0)      \n";
$html .= "          {      \n";
$html .= "            return \"\";      \n";
$html .= "          }      \n";
$html .= "          var query = qparts[1];      \n";
$html .= "          var vars = query.split(\"&\");      \n";
$html .= "          var value = \"\";      \n";
$html .= "          for (i=0;i<vars.length;i++)      \n";
$html .= "          {      \n";
$html .= "            var parts = vars[i].split(\"=\");      \n";
$html .= "            if (parts[0] == varname)      \n";
$html .= "            {      \n";
$html .= "              value = parts[1];      \n";
$html .= "              break;      \n";
$html .= "            }      \n";
$html .= "          }      \n";
$html .= "          value = unescape(value);      \n";
$html .= "          value.replace(/\+/g,\" \");      \n";
$html .= "          return value;      \n";
$html .= "        }      \n";

$html .= "            		function showInfo()\n";
$html .= "            		{\n";
$html .= "            		var elem = document.getElementById('configsection');\n";
$html .= "            			if(elem.style.display == \"none\")\n";
$html .= "            			{\n";
$html .= "            			elem.style.display=\"inline\";\n";
$html .= "            		  	document.getElementById(\"showcfgbutton\").value = \"$showconfig_button_text_off\";\n";
$html .= "            			}\n";
$html .= "            			else\n";
$html .= "            			{\n";
$html .= "            			elem.style.display=\"none\";\n";
$html .= "            		  	document.getElementById(\"showcfgbutton\").value = \"$showconfig_button_text\";\n";
$html .= "            			}\n";
$html .= "            		}\n";
$html .= "            		function livetrack()\n";
$html .= "            		{\n";
$html .= "            			if(document.getElementById(\"last_location\").value == \"$location_button_text\")\n";
$html .= "            			{\n";
$html .= "            		  		location=\"index.php?last_location=yes&interval=60&zoomlevel=2\";\n";
$html .= "            			}\n";
$html .= "            			else\n";
$html .= "            			{\n";
$html .= "            		  		location=\"index.php\";\n";
$html .= "            			}\n";
$html .= "            		}\n";
$html .= "            		function submittrip()\n";
$html .= "            		{\n";
$html .= "            		  document.form_trip.submit();\n";
$html .= "            		}

        function deleteTrip() {
          var selTrip = document.getElementById('selTrip').value;

          if (selTrip != 'None' && selTrip !='Any') {
            if (confirm('Are you sure you want to delete this trip?')) {
              var url = document.location.protocol +'//'+ document.location.hostname + document.location.pathname + '?deleteTrip='+selTrip;
              window.location.href = url;
            }
          }
          else {
            alert('Please select a trip!');
          }
        }
\n";
$html .= "            </script>\n";

$html .= "<div class=\"nav\" id=\"nav\">\n  <!-- astrovue -->";
$html .= "<div class=\"scroll\">\n";


if($public_page == "yes")
   {
   $html .= "      <form name=\"form_user\" action=\"index.php\" method=\"post\">\n";
   $html .= "      <select name=\"ID\" class=\"pulldownlayout\">\n";
                $findusers = $db->exec_sql("Select * FROM users ORDER BY username");
                while($founduser = $findusers->fetch())
        {
          if(!isset($ID))
              {
              $ID = $founduser["ID"];
              $trip = "";
              }
          if($founduser[ID] == $ID)
              {
              $html .= "       <option value=\"$founduser[ID]\"  SELECTED>$founduser[username]</option>\n";
              $username = $founduser[username];
              } else {
                     $html .= "       <option value=\"$founduser[ID]\">$founduser[username]</option>\n";
                     }
         }
     $html .= "</select><br>\n";
     $html .= "<input type=\"hidden\" name=\"trip\" value=\"\">\n";
     $html .= "<input type=\"hidden\" name=\"storeshowbearings\" value=\"$storeshowbearings\">\n";
     $html .= "<input type=\"hidden\" name=\"storecrosshair\" value=\"$storecrosshair\">\n";
     $html .= "<input type=\"hidden\" name=\"storeclickcenter\" value=\"$storeclickcenter\">\n";
     $html .= "<input type=\"hidden\" name=\"storeunits\" value=\"$storeunits\">\n";
     $html .= "<input type=\"hidden\" name=\"storelanguage\" value=\"$storelanguage\">\n";
     $html .= "<input type=\"hidden\" name=\"custom_view\" value=\"$custom_view\">\n";
     $html .= "<input type=\"hidden\" name=\"storestartdate\" value=\"$storestartdate\">\n";
     $html .= "<input type=\"hidden\" name=\"storeenddate\" value=\"$storeenddate\">\n";
     $html .= "<input type=\"submit\" class=\"buttonlayout\" name=\"user\" value=\"$user_button_text\">\n";
     //show or hide config button
     if($allow_custom == "yes")
           {
           $html .= "<br><br><br><input type=\"button\" class=\"buttonlayout\" id=\"showcfgbutton\" value=\"$showconfig_button_text\" onClick=\"showInfo()\" >\n";
           }
     if(isset($_REQUEST[last_location])) {
           $html .= "<br><input type=\"button\" class=\"buttonlayout\" id=\"last_location\" value=\"$location_button_text_off\" onClick=\"livetrack()\"><br><br>\n";
           } else {
                  $html .= " <br><input type=\"button\" class=\"buttonlayout\" id=\"last_location\" value=\"$location_button_text\" onClick=\"livetrack()\"><br><br><br>\n";
                  }
     $html .= "</form>\n";
} else {
                $finduser = $db->exec_sql("Select * FROM users WHERE ID = ? LIMIT 1", $ID);
                $founduser = $finduser->fetch();
       $username = $founduser['username'];
       $html .= "                 $trip_data<br>\n";
       $html .= "                    " . $founduser["username"] . " (<a href=\"index.php?action=logout\">log out</a>)\n";
       if ($public_page == "no")
                 {
                 if($allow_custom == "yes")
                       {
                       $html .= "<br><br><input type=\"button\" class=\"buttonlayout\" id=\"showcfgbutton\" value=\"$showconfig_button_text\" onClick=\"showInfo()\" >\n";
                       }
                  if(isset($_REQUEST[last_location]))
                       {
                       $html .= "  <br><input type=\"button\" class=\"buttonlayout\" id=\"last_location\" value=\"$location_button_text_off\" onClick=\"livetrack()\"><br><br>\n";
                       } else {
                              $html .= " <br><input type=\"button\" class=\"buttonlayout\" id=\"last_location\" value=\"$location_button_text\" onClick=\"livetrack()\"><br><br>\n";
                              }
                 }
       }

if(isset($_REQUEST[last_location]))   //if we are in live tracking then display this in center
      {
      }else {
            $html .= "<form name=\"form_trip\" action=\"index.php\" method=\"post\">\n";










		$html .= "                 $trip_title<br>\n";

                $findtrips = $db->exec_sql("Select A1.*, (select min( A2.dateoccurred ) from positions A2 where A2.FK_TRIPS_ID=A1.ID) AS startdate  FROM trips A1 WHERE A1.FK_Users_ID = ? order by startdate desc ", $ID);
                $foundTrips = array(array("ID" => "None", "Name" => $lang["trip-none"], "FK_Users_ID" => null),
                                    array("ID" => "Any", "Name" => $lang["trip-any"], "FK_Users_ID" => null));
                $foundTrips = array_merge($foundTrips, $findtrips->fetchAll());
                // In case the selected trip is invalid, default to the first non-generic trip
                // or None if there are only generic trips
                $selectedTrip = $foundTrips[count($foundTrips) > 2 ? 2 : 0];
                foreach ($foundTrips as $foundtrip)
                {
                    if ($foundtrip["ID"] == $trip)
                        $selectedTrip = $foundtrip;
                }

                $deleteButton = ($selectedTrip["FK_Users_ID"] === $_SESSION["ID"]);
                $trip = $selectedTrip["ID"];
                $tripname = $selectedTrip["Name"];

                $tripWidth = $deleteButton ? "style=\"width:134px\"" : "";
                $html .= "                        <select id=\"selTrip\" $tripWidth name=\"trip\" class=\"pulldownlayout trip\" onchange=\"javascript:submittrip();\" >\n";

                foreach ($foundTrips as $foundtrip)
                {
                    if($foundtrip[ID] == $trip)
                    {
                        $html .= "                        <option value=\"$foundtrip[ID]\" SELECTED>$foundtrip[Name]</option>\n";
                    }
                    else
                    {
                        $html .= "                        <option value=\"$foundtrip[ID]\">$foundtrip[Name]</option>\n";
                    }
                }
                $html .= "                            </select>";
                if ($deleteButton)
                    $html .= "<input type=\"button\" onclick=\"deleteTrip();\" class=\"pulldownlayout\" style=\"width:12px; text-align:center\" value=\"X\" id=\"delete-trip\">\n";


                $html .= "                            <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
		$html .= "                            <input type=\"hidden\" name=\"storeshowbearings\" value=\"$storeshowbearings\">\n";
                $html .= "                            <input type=\"hidden\" name=\"storecrosshair\" value=\"$storecrosshair\">\n";
                $html .= "                            <input type=\"hidden\" name=\"storeclickcenter\" value=\"$storeclickcenter\">\n";
                $html .= "                            <input type=\"hidden\" name=\"storeunits\" value=\"$storeunits\">\n";
                $html .= "                            <input type=\"hidden\" name=\"storelanguage\" value=\"$storelanguage\">\n";
                $html .= "                            <input type=\"hidden\" name=\"custom_view\" value=\"$custom_view\">\n";
                $html .= "                            <input type=\"hidden\" name=\"storestartdate\" value=\"$storestartdate\">\n";
                $html .= "                            <input type=\"hidden\" name=\"storeenddate\" value=\"$storeenddate\">\n";
		$html .= "                            <input type=\"hidden\" name=\"database_data\" value=\"$trip_button_text\">\n";
                $html .= "                            </form>\n";
				}
				if(isset($_REQUEST[last_location])) //show last location is on
				{

$html .= "        <SCRIPT type=\"text/javascript\">      \n";
$html .= "        function init_interval()      \n";
$html .= "        {     \n";
$html .= "        if (document.intervalclock.interval.value == \"-\"){  \n";
$html .= "              document.intervalclock.interval.value = getValue(\"interval\");   \n";
$html .= "                                                       }      \n";
$html .= "        if (document.intervalclock.interval.value < 10){      \n";
$html .= "                  alert(\"Minimum interval is 10 seconds\");  \n";
$html .= "                t = 60;       \n";
$html .= "                document.intervalclock.interval.value = 60;   \n";
$html .= "              }else { \n";
$html .= "                t = document.intervalclock.interval.value;    \n";
$html .= "                    } \n";
$html .= "        k = setTimeout('showclock()',1000);   \n";
$html .= "        }     \n";
$html .= "        function showclock()  \n";
$html .= "        {     \n";
$html .= "        t = t - 1;    \n";
$html .= "        if (t == 0){if (document.intervalclock.interval.value < 10){  \n";
$html .= "                  alert(\"Minimum interval is 10 seconds\");  \n";
$html .= "                t = 60;       \n";
$html .= "                document.intervalclock.interval.value = 60 ;  \n";
$html .= "              }else { \n";
$html .= "                if (document.zoomform.zoom.value == \"-\" )document.zoomform.zoom.value = 1 \n";
$html .= "                window.location.href = (\"index.php?last_location=yes&interval=\" + document.intervalclock.interval.value + \"&zoomlevel=\" + document.zoomform.zoom.value); \n";
$html .= "                    } \n";
$html .= "                 }    \n";
$html .= "        document.intervalclock.seconds.value = t;     \n";
$html .= "        k = setTimeout('showclock()',1000);   \n";
$html .= "        }     \n";
$html .= "        </SCRIPT>     \n";

$html .= "        <br><br><b><u>$tripstatus_title</u></b><br>\n";
$html .= "        <table><tr><td align=right> \n";
$html .= "        <FORM NAME=\"intervalclock\" action=\"post\">	\n";
$html .= "        Interval:     \n";
$html .= "        <INPUT TYPE=\"text\" CLASS=\"intervalinputfield\" NAME=\"interval\" VALUE=\"-\" size=\"1\">     \n";
$html .= "        sec.     \n";
$html .= "        <INPUT TYPE=\"button\" CLASS=\"intervalbutton\" NAME=\"start\" VALUE=\"Start\" onClick=\"clearTimeout(k);init_interval();\"><br>     \n";
$html .= "        Reload:     \n";
$html .= "        <INPUT TYPE=\"text\" CLASS=\"intervalinputfield\" NAME=\"seconds\" VALUE=\"-\" size=\"1\">     \n";
$html .= "        sec.     \n";
$html .= "        <INPUT TYPE=\"button\" CLASS=\"intervalbutton\" NAME=\"stop\" VALUE=\"Stop\" onClick=\"clearTimeout(k);document.intervalclock.seconds.value=document.intervalclock.interval.value;\">  \n";
$html .= "        </FORM>     \n";
$html .= "        </td></tr></table> \n";

$html .= "        	  <FORM NAME=\"zoomform\" action=\"post\">      \n";
$html .= "                <br><b><u>Zoom level Google Maps</u></b><br>\n";
$html .= "                <SELECT class=\"pulldownlayout\" name=\"zoom\">      \n";
$html .= "                <OPTION SELECTED value=0>Choose Zoomlevel     \n";
$html .= "                <OPTION value=1>Zoomlevel 0     \n";
$html .= "                <OPTION value=1>Zoomlevel 1     \n";
$html .= "                <OPTION value=2>Zoomlevel 2     \n";
$html .= "                <OPTION value=3>Zoomlevel 3     \n";
$html .= "                <OPTION value=4>Zoomlevel 4     \n";
$html .= "                <OPTION value=5>Zoomlevel 5     \n";
$html .= "                <OPTION value=6>Zoomlevel 6     \n";
$html .= "                <OPTION value=7>Zoomlevel 7     \n";
$html .= "                <OPTION value=8>Zoomlevel 8     \n";
$html .= "                <OPTION value=9>Zoomlevel 9     \n";
$html .= "                <OPTION value=10>Zoomlevel 10     \n";
$html .= "                <OPTION value=11>Zoomlevel 11     \n";
$html .= "                <OPTION value=12>Zoomlevel 12     \n";
$html .= "                <OPTION value=13>Zoomlevel 13     \n";
$html .= "                <OPTION value=14>Zoomlevel 14     \n";
$html .= "                <OPTION value=15>Zoomlevel 15     \n";
$html .= "                <OPTION value=16>Zoomlevel 16     \n";
$html .= "                <OPTION value=17>Zoomlevel 17     \n";
$html .= "        	  </SELECT>      \n";
$html .= "                </FORM> <br>    \n";

			}
				if(isset($_REQUEST[last_location]))   //if we are in live tracking then display this in center
				{
				} else {
                $html .= "                        <form name=\"form_filter\" action=\"index.php?showmapdata=1\" method=\"post\">\n";
		$html .= "                        $filter_title\n<br>";
                $html .= "                            <select name=\"filter\" class=\"pulldownlayout\">\n";
                if($filter == "Photo")
                {
                    $html .= "                            <option value=\"None\">$filter_none_text</option>\n";
                    $html .= "                            <option value=\"Photo\" SELECTED>$filter_photo_text</option>\n";
                    $html .= "                            <option value=\"Comment\">$filter_comment_text</option>\n";
                    $html .= "                            <option value=\"PhotoComment\">$filter_photo_comment_text</option>\n";
                    $html .= "                            <option value=\"Last20\">$filter_last_20</option>\n";
                }
                elseif($filter == "Comment")
                {
                    $html .= "                            <option value=\"None\">$filter_none_text</option>\n";
                    $html .= "                            <option value=\"Photo\">$filter_photo_text</option>\n";
                    $html .= "                            <option value=\"Comment\" SELECTED>$filter_comment_text</option>\n";
                    $html .= "                            <option value=\"PhotoComment\">$filter_photo_comment_text</option>\n";
                    $html .= "                            <option value=\"Last20\">$filter_last_20</option>\n";
                }
                elseif($filter == "PhotoComment")
                {
                    $html .= "                            <option value=\"None\">$filter_none_text</option>\n";
                    $html .= "                            <option value=\"Photo\">$filter_photo_text</option>\n";
                    $html .= "                            <option value=\"Comment\">$filter_comment_text</option>\n";
                    $html .= "                            <option value=\"PhotoComment\" SELECTED>$filter_photo_comment_text</option>\n";
                    $html .= "                            <option value=\"Last20\">$filter_last_20</option>\n";
                }
                elseif($filter == "Last20")
                {
                    $html .= "                            <option value=\"None\">$filter_none_text</option>\n";
                    $html .= "                            <option value=\"Photo\">$filter_photo_text</option>\n";
                    $html .= "                            <option value=\"Comment\">$filter_comment_text</option>\n";
                    $html .= "                            <option value=\"PhotoComment\">$filter_photo_comment_text</option>\n";
                    $html .= "                            <option value=\"Last20\" SELECTED>$filter_last_20</option>\n";
                }
                else
                {
                    $html .= "                            <option value=\"None\" SELECTED>$filter_none_text</option>\n";
                    $html .= "                            <option value=\"Photo\">$filter_photo_text</option>\n";
                    $html .= "                            <option value=\"Comment\">$filter_comment_text</option>\n";
                    $html .= "                            <option value=\"PhotoComment\">$filter_photo_comment_text</option>\n";
                    $html .= "                            <option value=\"Last20\">$filter_last_20</option>\n";
				}
                $html .= "                            </select>\n";
                $html .= "                            <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
                $html .= "                            <input type=\"hidden\" name=\"trip\" value=\"$trip\">\n";
		$html .= "                            <input type=\"hidden\" name=\"storeshowbearings\" value=\"$storeshowbearings\">\n";
		$html .= "                            <input type=\"hidden\" name=\"storecrosshair\" value=\"$storecrosshair\">\n";
                $html .= "                            <input type=\"hidden\" name=\"storeclickcenter\" value=\"$storeclickcenter\">\n";
                $html .= "                            <input type=\"hidden\" name=\"storeunits\" value=\"$storeunits\">\n";
                $html .= "                            <input type=\"hidden\" name=\"storelanguage\" value=\"$storelanguage\">\n";
                $html .= "                            <input type=\"hidden\" name=\"custom_view\" value=\"$custom_view\">\n";
                $html .= "                        <input type=\"hidden\" name=\"storestartdate\" value=\"$storestartdate\">\n";
                $html .= "                        <input type=\"hidden\" name=\"storeenddate\" value=\"$storeenddate\">\n";
			}

                $params = array($ID);
                if(isset($_REQUEST[last_location]))
                {
                    $limit = "DESC LIMIT 1";
                    $where = "";
                }
                else
                {
                    $limit = "";
                    if ($tripname == "None")
                        $where = " AND FK_Trips_ID is NULL";
                    elseif ($tripname != "Any")
                    {
                        $where = " AND FK_Trips_ID = ?";
                        $params[] = $trip;
                    }
                    else
                        $where = "";

                    // if startday is not blank then don't lookup the start and end of entire trip
                    if (isset($startday) && trim($startday) != "")
                    {
                        $where .= " AND DateOccurred BETWEEN ? AND ?";
                        $params[] = $startday;
                        $params[] = $endday;
                    }
                }

                $result = $db->exec_sql("SELECT * FROM positions " .
                                        "WHERE FK_Users_ID=? $where " .
                                        "ORDER BY DateOccurred $limit", $params);

$rounds      = 1;
$total_miles = 0;
$leg_time    = 0;
                $pcount=0;
                $ccount=0;
                while($row = $result->fetch())
	{
		$mph     = $row['Speed'] * 2.2369362920544;
		$kph     = $row['Speed'] * 3.6;
		$ft      = $row['Altitude'] * 3.2808399;
		$meters  = $row['Altitude'];
                    if ($row['ImageURL'] != '')
                        $pcount++;
                    if ($row['Comments'] != '')
                        $ccount++;

                    $endday = $row['DateOccurred'];
			if($rounds == 1)
			{
				$total_time = 0;
				$display_total_time = gmdate("H:i:s", $total_time);
                        $startday = $endday;
			}
			else
			{
				$leg_miles        = distance($row['Latitude'], $row['Longitude'], $holdlat, $holdlong, "m");
				$total_miles      = $total_miles + $leg_miles;
				$total_kilometers = $total_miles * 1.609344;
				$leg_time         = $row['DateOccurred'];
				$total_time       = get_elapsed_time($startday, $leg_time);
				$total_time       = gmdate("H:i:s", $total_time);
			}
		$rounds++;
		$holdlat  = $row['Latitude'];
		$holdlong = $row['Longitude'];
	}
if(isset($_REQUEST[last_location]))
	{
	$pcount=0;
	$ccount=0;
	}

				if(isset($_REQUEST[last_location]))   //if we are in live tracking then display this in center
				{
				} else {

                $html .= "                         <input type=\"submit\" class=\"buttonlayout\"  name=\"filter_data\" value=\"$filter_button_text\"><br><br><br>\n";
                $html .= "                         <div> $startdate_text </div> <input type=\"text\" class=\"textinputfield\" id=\"startday\" name=\"startday\" value=\"$startday\"><br>\n";
                $html .= "                         <div> $enddate_text </div>  <input type=\"text\" class=\"textinputfield\" id=\"endday\" name=\"endday\" value=\"$endday\">\n";
				$html .= "					<script type=\"text/javascript\">\n";
				$html .= "					Calendar.setup({\n";
				$html .= "					inputField 	   : \"startday\",\n";
				$html .= "					ifFormat 	   : \"%Y-%m-%d %H:%M:%S\", \n";
				$html .= "					showsTime      :    true, \n";
				$html .= "					timeFormat     :    \"24\" \n";
				$html .= "					}); \n";
				$html .= "					Calendar.setup({ \n";
				$html .= "					inputField 	   : \"endday\",\n";
				$html .= "					ifFormat 	   : \"%Y-%m-%d %H:%M:%S\", \n";
				$html .= "					showsTime      :    true, \n";
				$html .= "					timeFormat     :    \"24\" \n";
				$html .= "					}); \n";
				$html .= "					</script> \n";
               $html .= "                        </form>\n";
			   }
               $html .= "                       <br><b><u>$tripsummary_title</u></b><br>\n";


                                if(isset($_REQUEST[last_location])) //show last location is on
                                {
                        if($units == "metric")
                        {
                            $html .= "<b>$speed_balloon_text: </b>" . number_format($kph,2) . " " . $speed_metric_unit_balloon_text . "<br><b>$altitude_balloon_text: </b>" . number_format($meters,2) . " " . $height_metric_unit_balloon_text . "<br><b>$total_distance_balloon_text: </b>" . number_format($total_kilometers,2) . " " . $distance_metric_unit_balloon_text . "";
                        }
                        else
                        {
                            $html .= "<b>$speed_balloon_text: </b>" . number_format($mph,2) . " " . $speed_imperial_unit_balloon_text . "<br><b>$altitude_balloon_text: </b>" . number_format($ft,2) . " " . $height_imperial_unit_balloon_text . "<br><b>$total_distance_balloon_text: </b>" . number_format($total_miles,2) . " " . $distance_imperial_unit_balloon_text . "";
                        }                }
                                else
                                {
                                        if($units == "metric")
                                        {
                                                $html .= "$total_distance_balloon_text: " . number_format($total_kilometers,2) . " " . $distance_metric_unit_balloon_text . "<br>";
                                        }
                                        else
                                        {
                                                $html .= "$total_distance_balloon_text: " . number_format($total_miles,2) . " " . $distance_imperial_unit_balloon_text . "<br>";
                                        }
                                        $html .= "                                                      $summary_time $total_time<br>\n";
                                        $html .= "                                                      $summary_photos $pcount<br>\n";
                                        $html .= "                                                      $summary_comments $ccount\n";

// 2009-05-07 DMR Add Link to download the currently displayed data. -->
                                        $html .= "                                                    <br><br><b><u>Download Data</u></b><br>\n";
                                        // Required Params
// Removing from Export code                                $ExportOptions = "&db=1234567";
// Use the Cookie soe we don't display it in the URL        $ExportOptions .= "&u=" . $username;
// Use the Cookie soe we don't display it in the URL        $ExportOptions .= "&p=" . $password;
                                        //
                                        $ExportOptions .= "&df=" . $startday;
                                        $ExportOptions .= "&dt=" . $endday;
                                        $ExportOptions .= "&tn=" . $tripname;
                                        $ExportOptions .= "&sb=" . $storeshowbearings; //0=no 1=Yes
                                        $html .= "                                                    <a href=\"download.php?a=kml" . $ExportOptions . "\">KML Format</a><br>\n";
                                        $html .= "                                                    <a href=\"download.php?a=gpx" . $ExportOptions . "\">GPX Format</a><br>\n";

// 2009-05-07 DMR Add Link to download the currently displayed data. <--
                                }


                    $html .= "</div></div>\n";
                    $html .= "                <div id=\"configsection\" style=\"display:none;\">\n";
                    $html .= "                    $display_options_title_text:\n";

		    if(isset($_REQUEST[last_location])) {

		    $html .= "                    <form name=\"form_display\" action=\"\" method=\"post\">\n";
						        }
						      else
							{
                    $html .= "                    <form name=\"form_display\" action=\"index.php\" method=\"post\">\n";
							}
                    if($show_bearings == "yes")
                    {
                        $html .= "                    <input type=\"checkbox\" name=\"setshowbearings\" CHECKED> $display_showbearing_text<br>\n";
                    }
                    else
                    {
                        $html .= "                    <input type=\"checkbox\" name=\"setshowbearings\"> $display_showbearing_text<br>\n";
                    }

					if($crosshair == "yes")
                    {
                        $html .= "                    <input type=\"checkbox\" name=\"setcrosshair\" CHECKED> $display_crosshair_text<br>\n";
                    }
                    else
                    {
                        $html .= "                    <input type=\"checkbox\" name=\"setcrosshair\"> $display_crosshair_text<br>\n";
                    }
                    if($clickcenter == "yes")
                    {
                        $html .= "                    <input type=\"checkbox\" name=\"setclickcenter\" CHECKED> $display_clickcenter_text<br>\n";
                    }
                    else
                    {
                        $html .= "                    <input type=\"checkbox\" name=\"setclickcenter\"> $display_clickcenter_text<br>\n";
                    }
                    $html .= "                        <select name=\"language\" class=\"pulldownlayout\">\n";
                    foreach (array_values($languages) as $lang_entry)
                    {
                        if ($lang_entry->en)
                            $lang_name = strtolower($lang_entry->en);
                        else
                            $lang_name = "english";
                        $html .= "                            <option value=\"$lang_name\"";
                        if($language === $lang_name)
                            $html .= " SELECTED";
                        $html .= ">$lang_entry->full_name</option>\n";
                    }
                    $html .= "                        </select>$display_language_text<br>\n";
                    $html .= "                        <select name=\"units\" class=\"pulldownlayout\">\n";
                    $html .= "                            <option value=\"imperial\""; if($units == "imperial") { $html .= " SELECTED"; } $html .= ">Imperial</option>\n";
                    $html .= "                            <option value=\"metric\""; if($units == "metric") { $html .= " SELECTED"; } $html .= ">Metric</option>\n";
                    $html .= "                        </select> $display_units_text<br>\n";
                    $html .= "                        <br>\n";
                    if(isset($_REQUEST[last_location]))
                    {
                        $html .= "                    <input type=\"hidden\" name=\"last_location\" value=\"$location_button_text\">\n";
                    }
                    if(isset($filter))
                    {
                        $html .= "                    <input type=\"hidden\" name=\"filter\" value=\"$filter\">\n";
                    }
                    $html .= "                        <input type=\"hidden\" name=\"trip\" value=\"$trip\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"ID\" value=\"$ID\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"action\" value=\"form_display\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"storeshowbearings\" value=\"$storeshowbearings\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"storecrosshair\" value=\"$storecrosshair\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"storeclickcenter\" value=\"$storeclickcenter\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"storeunits\" value=\"$storeunits\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"storelanguage\" value=\"$storelanguage\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"custom_view\" value=\"$custom_view\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"storestartdate\" value=\"$storestartdate\">\n";
                    $html .= "                        <input type=\"hidden\" name=\"storeenddate\" value=\"$storeenddate\">\n";
                    $html .= "                        <input type=\"submit\" class=\"buttonlayout\" value=\"$display_button_text\">\n";
                    $html .= "                    </form>\n";
                    $html .= "                </div>\n";


		    $html .= " <div id=\"content\">\n";
                    $html .= " <div id=\"map\"></div>\n <!-- astrovue -->";
                    $html .= " <div class=\"credit\">Powered by <a href=\"http://www.luisespinosa.com/trackme_eng.html\">TrackMe (v" . $version_text . ")</a> by <a href=\"http://www.luisespino
sa.com/central_eng.php\">Luis Espinosa</a></div>\n";
                    $html .= " </div>\n";

                if ($googleview === "G_NORMAL_MAP")
                    $googleview = "ROADMAP";
                elseif ($googleview === "G_SATELLITE_MAP")
                    $googleview = "SATELLITE";
                elseif ($googleview === "G_HYBRID_MAP")
                    $googleview = "HYBRID";
                elseif ($googleview === "G_PHYSICAL_MAP")
                    $googleview = "TERRAIN";

                if ($googleview !== "ROADMAP" && $googleview !== "SATELLITE" &&
                        $googleview !== "HYBRID" && $googleview !== "TERRAIN")
                    // Invalid option
                    $googleview = "ROADMAP";

                $html .= "            <script type=\"text/javascript\">\n";
                $html .= "            //<![CDATA[\n";
                $html .= "                lang.setCode('$lang->code');\n";
                $html .= "                var geocoder = null;\n";
                $html .= "                var online = true;\n";
                $html .= "                var bounds = new google.maps.LatLngBounds();\n";
                $html .= "                var map = new google.maps.Map(document.getElementById(\"map\"),\n";
                $html .= "                                              {center: new google.maps.LatLng(0, 0),\n";
                $html .= "                                               mapTypeId: google.maps.MapTypeId.$googleview,\n";
                $html .= "                                               mapTypeControl: true,\n";
                $html .= "                                               scrollwheel: true,\n";
                $html .= "                                               scaleControl: true});\n";

                if($crosshair == "yes")
                {
                    $html .= "            var centerCrosshair = {\n";
                    $html .= "                url: 'crosshair.gif',\n";
                    $html .= "                size: new google.maps.Size(17, 17),\n";
                    $html .= "                anchor: new google.maps.Point(8, 8)\n";
                    $html .= "            };\n";
                    $html .= "            centerCross = new google.maps.Marker({position: map.getCenter(),\n";
                    $html .= "                                                  icon: centerCrosshair,\n";
                    $html .= "                                                  clickable: false });\n";
                    $html .= "            centerCross.setMap(map);\n";
                    $html .= "            function setCenterCross() {\n";
                    $html .= "                centerCross.setPosition(map.getCenter());\n";
                    $html .= "            };\n";
                    $html .= "            map.addListener(\"drag\", setCenterCross);\n";
                    $html .= "            map.addListener(\"resize\", setCenterCross);\n";
                    $html .= "            map.addListener(\"zoom_changed\", setCenterCross);\n";
                    $html .= "            map.addListener(\"center_changed\", setCenterCross);\n";
                }
                if($clickcenter == "yes")
                {
                    $html .= "            map.addListener(\"click\", function(e) { map.panTo(e.latLng); } );\n";
                }

                // Write configuration to JS
                if ($show_bearings == "yes") {
                    $html .= "                var showBearings = true;\n";
                } else {
                    $html .= "                var showBearings = false;\n";
                }
                $html .= "                var useMetric = " . ($units == "metric" ? "true" : "false") . ";\n\n";

                $params = array();
                if (isset($_REQUEST[last_location]))  //show last location is on
                {
                    $where = "";
                    $limit = 1;
                    $showmapdata = 1;
                }
                else
                {
                    if($filter == "Photo")
                    {
                        $where = "ImageURL != ''";
                        $limit = 0;
                    }
                    elseif($filter == "Comment")
                    {
                        $where = "Comments != ''";
                        $limit = 0;
                    }
                    elseif($filter == "PhotoComment")
                    {
                        $where = "(Comments != '' OR ImageURL != '')";
                        $limit = 0;
                    }
                    elseif($filter == "Last20")
                    {
                        $where = "";
                        $limit = 20;
                    }
                    else
                    {
                        $where = "";
                        $limit = 0;
                    }
                    if ($where != "")
                        $where .= " AND";

                    if ($tripname != "Any")
                    {
                        if ($tripname == "None")
                        {
                            $count = 0;
                        }
                        else
                        {
                            // TODO: use parameters
                            $count = $db->get_count("positions " .
                                                    "WHERE FK_Users_ID='$ID' AND " .
                                                    "FK_Trips_ID='$trip' AND " .
                                                    "DateOccurred BETWEEN '$startday' AND '$endday'");
                        }
                        if ($count == 0)
                            $where .= " FK_Trips_ID is NULL AND";
                        else
                        {
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

                if ($showmap != "yes" && $showmapdata != 1)
                    $params[] = 'ZZ';
                else
                    $params[] = $ID;

                $query = $db->exec_sql("SELECT positions.*, icons.URL FROM positions " .
                                       "LEFT JOIN icons ON positions.FK_Icons_ID=icons.ID " .
                                       "WHERE $where FK_Users_ID=? " .
                                       "ORDER BY DateOccurred $limit",
                                       $params);

                $result = $query->fetchAll();
                $count = count($result);
                $avg_speed = 0;
                foreach ($result as $row)
                {
                    $avg_speed += $row['Speed'];
                }
                $avg_speed /= $count;

                if ($tripname == "Any")
                {
                $tripnameText = $trip_any_text;
               	}
               	elseif ($tripname == "None")
               	{
                $tripnameText = $trip_none_text;
               	}
              	else
             	{
                $tripnameText = $tripname;
              	}
                $html .= "            var trip = new Trip('" . escape_js_str($tripnameText) . "', '" . escape_js_str($username) . "');\n";
                for ($rounds = 1; $rounds <= $count; $rounds++)
                {
                    $row = $result[$rounds - 1];
                    // escape the strings for JS
                    $row['ImageURL'] = escape_js_str($row['ImageURL']);
                    $row['Comments'] = escape_js_str($row['Comments']);

                    if (!is_null($row['URL']))
                    {
                        $parameter = "'$row[URL]'";
                    }
                    elseif($rounds < $count)
                    {
                        $parameter = "true";
                    }
                    else
                    {
                        $parameter = "iconRed";
                    }

                    $dataParameter = "";
                    if (!is_null($row['Angle']))
                        $dataParameter .= ", bearing: $row[Angle]";

                    $formattedTS = escape_js_str(date($date_format, strtotime($row['DateOccurred'])));

                    $html .= "        trip.appendMarker({latitude: $row[Latitude], longitude: $row[Longitude], timestamp: '$row[DateOccurred]', speed: $row[Speed], avgSpeed: $avg_speed, altitude: $row[Altitude], comment: '$row[Comments]', photo: '$row[ImageURL]' $dataParameter, formattedTS: '$formattedTS'}, $parameter);\n";
                }

		if(isset($_REQUEST[last_location])) //show last location is on
                                {
                $html .= " 	map.setZoom(16 - document.zoomform.zoom.value); \n";
			        }
                else
                {
                    $html .= "        map.fitBounds(bounds); \n";
                }
                $html .= "                map.setCenter(bounds.getCenter());\n";
                $html .= "            //]]>\n";
                $html .= "            </script>\n";
			}
            else
            {
                unset($_SESSION['ID']);
		$html  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
                $html .= "    <head>\n";
                $html .= "        <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
	        $html .= "        <link rel=\"stylesheet\" href=\"layout.css\" type=\"text/css\">\n";
                $html .= "        <title>$title_text (v" . $version_text . ")</title>\n";
                $html .= "    </head>\n";
                $html .= "    <body bgcolor=\"$bgcolor\" OnLoad=\"placeFocus()\">\n";
                $html .= "        <SCRIPT type=\"text/javascript\">\n";
                $html .= "            function placeFocus() {\n";
                $html .= "                if (document.forms.length > 0) {\n";
                $html .= "                    var field = document.forms[0];\n";
                $html .= "                    for (i = 0; i < field.length; i++) {\n";
                $html .= "                        if ((field.elements[i].type == \"text\") || (field.elements[i].type == \"textarea\") || (field.elements[i].type.toString().charAt(0) == \"s\")) {\n";
                $html .= "                            document.forms[0].elements[i].focus();\n";
                $html .= "                            break;\n";
                $html .= "                        }\n";
                $html .= "                    }\n";
                $html .= "                }\n";
                $html .= "            }\n";
                $html .= "        </script><center><br><br>\n";
                $html .= "        <div class=\"loginwindow\" align=center>\n";
                $html .= "            <h2>$title_text (v" . $version_text . ")</h2>\n";
                $html .= "            $page_private<br>\n"; 				//trackmeIT
                $html .= "            <br>\n";
                $html .= "            <br>\n";
                $html .= "            <form action=\"index.php\" method=\"post\"><br>\n";
                $html .= "                <table border=\"0\">";
                $html .= "                    <tr>\n";
                $html .= "                        <td align=\"right\">\n";
                $html .= "                            $login_text: \n";
                $html .= "                        </td>\n";
                $html .= "                        <td>\n";
                $html .= "                            <input class=\"textinputfield\" type=\"text\" name=\"username\" size=\"10\">\n";
                $html .= "                        </td>\n";
                $html .= "                    </tr>\n";
                $html .= "                    <tr>\n";
                $html .= "                        <td align=\"right\">\n";
                $html .= "                            $password_text: \n";
                $html .= "                        </td>\n";
                $html .= "                        <td>\n";
                $html .= "                            <input class=\"textinputfield\" type=\"password\" name=\"password\" size=\"10\">\n";
                $html .= "                        </td>\n";
                $html .= "                    </tr>\n";
                $html .= "                    <tr>\n";
                $html .= "                        <td align=\"right\" colspan=\"2\">\n";
                $html .= "                            <input class=\"buttonlayout\" type=\"submit\" value=\"$login_button_text\">\n";
                $html .= "                        </td>\n";
                $html .= "                    </tr>\n";
                $html .= "                </table>\n";
                $html .= "            </form>\n";
                $html .= "            <br>\n";
                $html .= "            <br>\n";
                $html .= "            <br>\n";
                $html .= "            <br>\n";
                $html .= "            <br></div></center>\n";
            }
        }

    }
	$html .= "         <!--       <div id=\"footertext\">\n";
    $html .= "                    $footer_text <a href=\"http://forum.xda-developers.com/showthread.php?t=340667\" target=\"_blank\">TrackMe</a>\n";
    $html .= "                </div>\n -->  ";
	//google analytics
	if(isset($googleanalyticsaccount)) {
	$html .= "<script type=\"text/javascript\">\n";
	$html .= "   var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\n";
	$html .= "   document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\n";
	$html .= "</script>\n";
	$html .= "<script type=\"text/javascript\">\n";
	$html .= "   var pageTracker = _gat._getTracker(\"$googleanalyticsaccount\");\n";
	$html .= "   pageTracker._initData();\n";
	$html .= "   pageTracker._trackPageview();\n";
	$html .= "</script>\n";
	}
    $html .= "            </body>\n";
    $html .= "        </html>\n";

    $db = null;  // Close database
    print $html;

    // Function to calculate distance between points
    function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
    	if ($lat1 == $lat2 && $lon1 == $lon2) { return 0; }
      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

      if ($unit == "K")
      {
        return ($miles * 1.609344);
      }
      else if ($unit == "N")
      {
        return ($miles * 0.8684);
      }
      else
      {
        return $miles;
      }
    }

    // Function to convert MySQL dates
    function get_mysql_to_epoch($date)
    {
        list( $year, $month, $day, $hour, $minute, $second )
            = preg_split( '([^0-9])', $date );
        return date( 'U', mktime( $hour, $minute, $second, $month, $day,
            $year ) );
    }

    // Function to calculate time between points
    function get_elapsed_time($time_start, $time_end, $units = 'seconds', $decimals = 0)
    {
        $divider['years']   = ( 60 * 60 * 24 * 365 );
        $divider['months']  = ( 60 * 60 * 24 * 365 / 12 );
        $divider['weeks']   = ( 60 * 60 * 24 * 7 );
        $divider['days']    = ( 60 * 60 * 24 );
        $divider['hours']   = ( 60 * 60 );
        $divider['minutes'] = ( 60 );
        $divider['seconds'] = 1;

        $elapsed_time = ( ( get_mysql_to_epoch( $time_end )
                        - get_mysql_to_epoch( $time_start ) )
                        / $divider[$units] );
        $elapsed_time = sprintf( "%0.{$decimals}f", $elapsed_time );

        return $elapsed_time;
    }

    function escape_js_str($str)
    {
        return str_replace("'", "\\'", str_replace("\\", "\\\\", $str));
    }
?>
