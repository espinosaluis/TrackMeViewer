<?php

    //////////////////////////////////////////////////////////////////////////////
    //
    // TrackMe Google Maps Display Installer
    // Version: 1.20
    // Date:    12/22/2007
    // Time:    16:00:00
    //
    // TrackMe built by Staryon
    // For more information go to:
    // http://forum.xda-developers.com/showthread.php?t=340667
    //
    // Please feel free to modify the files to meet your needs.
    // Post comments and questions to the forum thread above.
    //
    //////////////////////////////////////////////////////////////////////////////

    if (file_exists('config.php'))
    {
        require_once('config.php');
    }

    $colorarray = array(
        '#fff' => 'White',
        '#ccc' => 'Gray',
        '#000' => 'Black',
        '#f00' => 'Red',
        '#f90' => 'Orange',
        '#ff0' => 'Yellow',
        '#090' => 'Green',
        '#00f' => 'Blue',
        '#90c' => 'Purple',
        '#f0c' => 'Pink',
        '#963' => 'Brown');

    $html  = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
    $html .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
    $html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
    $html .= "  <head>\n";
    $html .= "    <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
    $html .= "    <title>TrackMe Server Side Installation</title>\n";
    $html .= "  </head>\n";
    $html .= "  <body>\n";
    $html .= "    <div align=center>\n";
    $html .= "      <h1>TrackMe Server Side Installation</h1>\n";

    if (isset($_POST["action"]) && $_POST["action"] == "install")
    {
		$installtype 			= $_POST["installtype"];
        $dbname      			= $_POST["dbname"];
        $dbuser      			= $_POST["dbuser"];
        $dbpassword  			= $_POST["dbpassword"];
        $dbserver    			= $_POST["dbserver"];
        $show_bearings    		= $_POST["show_bearings"];
        $folder      			= $_POST["folder"];
        $apikey      			= $_POST["googleapikey"];
		$googleanalyticsaccount = $_POST["googleanalyticsaccount"];
        $googleview  			= $_POST["googleview"];
		$showmap  	 			= $_POST["showmap"];
        $username    			= $_POST["username"];
        $bgcolor     			= $_POST["bgcolor"];
        $mapwidth    			= trim($_POST["mapwidth"]," px") . "px";
        $mapheight   			= trim($_POST["mapheight"]," px") . "px";
        $mapborder   			= trim($_POST["mapborder"]," px") . "px";
        $bordercolor 			= $_POST["bordercolor"];
        $mapalign    			= $_POST["mapalign"];
        $date_format 			= $_POST["date_format"];


        $html .= "    </div>\n";
        if(!@mysql_connect("$dbserver","$dbuser","$dbpassword"))
        {
            $html .= "ERROR: Failed to connect to database.";
            print $html;
            die();
        }

        mysql_select_db("$dbname");
		if ($installtype == "newinstall") {
          $str = file_get_contents("database.sql");
          $sql = explode(';', $str);
          foreach ($sql as $query) {
             if (!empty($query)) {
                $r = mysql_query($query);
             }
          }
 //       $html .= "Install Type: $installtype<br>\n";
        $html .= "Database tables created successfully<br>\n";
		} else {
		//will sql updates here in needed.
//        $html .= "Install Type: $installtype<br>\n";
        $html .= "Database updated successfully<br>\n";
		}

        //Create config.inc file
        if ($configfile = fopen ("config.php", "w+"))
        {
            $fp = fwrite($configfile,"<?php\r\n");
            $fp = fwrite($configfile,"    //////////////////////////////////////////////////////////////////////////////\r\n");
            $fp = fwrite($configfile,"    //\r\n");
            $fp = fwrite($configfile,"    // TrackMe Google Maps Display Configuration\r\n");
            $fp = fwrite($configfile,"    // Version: 1.20\r\n");
            $fp = fwrite($configfile,"    // Date:    12/22/2007\r\n");
            $fp = fwrite($configfile,"    // Time:    16:00:00\r\n");
            $fp = fwrite($configfile,"    //\r\n");
            $fp = fwrite($configfile,"    // TrackMe built by Staryon\r\n");
            $fp = fwrite($configfile,"    // For more information go to:\r\n");
            $fp = fwrite($configfile,"    // http://forum.xda-developers.com/showthread.php?t=340667\r\n");
            $fp = fwrite($configfile,"    //\r\n");
            $fp = fwrite($configfile,"    // Please feel free to modify the index.php file to meet your needs.\r\n");
            $fp = fwrite($configfile,"    // Post comments and questions to the forum thread above.\r\n");
            $fp = fwrite($configfile,"    //\r\n");
            $fp = fwrite($configfile,"    //////////////////////////////////////////////////////////////////////////////\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Database Information\r\n");
            $fp = fwrite($configfile,"    \$DBIP         = \"$dbserver\";\r\n");
            $fp = fwrite($configfile,"    \$DBUSER       = \"$dbuser\";\r\n");
            $fp = fwrite($configfile,"    \$DBPASS       = \"$dbpassword\";\r\n");
            $fp = fwrite($configfile,"    \$DBNAME       = \"$dbname\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Google API Key. Obtain from http://www.google.com/apis/maps/signup.html\r\n");
            $fp = fwrite($configfile,"    \$googleapikey = \"$apikey\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // if you have Google Analytics Account. Obtain from https://www.google.com/analytics/home/?et=reset&hl=en-US\r\n");
            $fp = fwrite($configfile,"    // or just leave blank if you do not want to use\r\n");
            $fp = fwrite($configfile,"    \$googleanalyticsaccount   = \"$googleanalyticsaccount\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Google Maps default view (G_NORMAL_MAP,G_SATELLITE_MAP, G_HYBRID_MAP or G_PHYSICAL_MAP)\r\n");
            $fp = fwrite($configfile,"    \$googleview   = \"$googleview\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    //to show all map points when you arrive at the page and when you change trips\r\n");
            $fp = fwrite($configfile,"    //0=dont show,  1=show\r\n");
			$fp = fwrite($configfile,"    \$showmap=\"$showmap\";\r\n");
            $fp = fwrite($configfile,"    // Map style\r\n");
            $fp = fwrite($configfile,"    \$mapwidth     = \"$mapwidth\";\r\n");
            $fp = fwrite($configfile,"    \$mapheight    = \"$mapheight\";\r\n");
            $fp = fwrite($configfile,"    \$mapborder    = \"$mapborder\";\r\n");
            $fp = fwrite($configfile,"    \$bordercolor  = \"$bordercolor\";\r\n");
            $fp = fwrite($configfile,"    \$mapalign     = \"$mapalign\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Background color for map display\r\n");
            $fp = fwrite($configfile,"    \$bgcolor      = \"$bgcolor\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Show bearing arrows on map (yes or no)\r\n");
            $fp = fwrite($configfile,"    \$show_bearings    = \"$show_bearings\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Display crosshair at map center (yes or no)\r\n");
            $fp = fwrite($configfile,"    \$crosshair    = \"$crosshair\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Click to center map (yes or no)\r\n");
            $fp = fwrite($configfile,"    \$clickcenter  = \"$clickcenter\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Display map overview (yes or no) (yes or no)\r\n");
            $fp = fwrite($configfile,"    \$overview     = \"$overview\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Map auto-refresh in seconds (0 for manual refresh)\r\n");
            $fp = fwrite($configfile,"    \$refresh      = \"$refresh\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Public Page Access (yes or no)\r\n");
            $fp = fwrite($configfile,"    \$public_page  = \"$public_page\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Date Display Format\r\n");
            $fp = fwrite($configfile,"    // Must be valid php date format (http://us.php.net/date)\r\n");
            $fp = fwrite($configfile,"    \$date_format  = \"$date_format\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Units (imperial or metric)\r\n");
            $fp = fwrite($configfile,"    \$units        = \"$units\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Language (English, Italian, German, French or Spanish)\r\n");
            $fp = fwrite($configfile,"    \$language     = \"$language\";\r\n");
            $fp = fwrite($configfile,"    \r\n");
            $fp = fwrite($configfile,"    // Allow customization of display (yes or no)\r\n");
            $fp = fwrite($configfile,"    \$allow_custom = \"yes\";\r\n");
            $fp = fwrite($configfile,"?>\r\n");
        }
        else
        {
          $html .= "ERROR: config.php can not be opened. You need to edit it manually.<br>";
        }
        fclose($configfile);

        $html .= "<br>\n";
        $html .= "<br>\n";
        $html .= "Remember to create two folders (\"routes\" and \"pics\") in your TrackMe folder.<br>\n";
        $html .= "<br>\n";
        $html .= "If there are no errors listed above then the installation of server side files and tables is complete. You MUST delete the install.php and database.sql files before the map page will display. Once you delete install.php you can <a href=\"index.php\">click here</a> to view your custom map.<br>\n";
        $html .= "<br>\n";
        $html .= "If you have not installed the application on your Windows Mobile Professional device you can download the latest version <a href=\"http://luisespinosa.com/bin/trackme/TrackMe.CAB\">here</a><br>\n";
        $html .= "<br>\n";
        $html .= "<br>\n";
    }
    else
    {
        $html .= "      (*) Must be created manually before submitting this form!";
        $html .= "      <form name=\"install\" action=\"install.php\" method=\"post\">";
        $html .= "          <table border=\"0\">\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Installation Type:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
		$html .= "                      <select name=\"installtype\">\n";
        $html .= "                          <option value=\"newinstall\" SELECTED>New Install</option>\n";
        $html .= "                          <option value=\"upgradeinstall\">Upgrade</option>\n";
        $html .= "                      </select>\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Database Name (*):\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"dbname\" value=\"$DBNAME\" size=\"40\">\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Database User (*):\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"dbuser\" value=\"$DBUSER\" size=\"40\">\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Database Password (*):\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"dbpassword\" value=\"$DBPASS\" size=\"40\">\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Database Server Address:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"dbserver\" value=\"$DBIP\" size=\"40\" value=\"localhost\">\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";

        $html .= "              <tr>\n";
        $html .= "                  <td colspan=\"2\">\n";
        $html .= "                      &nbsp;\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Google Maps API Key (obtain <a href=\"http://www.google.com/apis/maps/signup.html\" target=\"_blank\">here</a>):\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"googleapikey\" value=\"$googleapikey\" size=\"50\">\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Google Analytics Account (obtain <a href=\"https://www.google.com/analytics/home/?et=reset&hl=en-US\" target=\"_blank\">here</a>):\n";
        $html .= "                      Leave Blank if you do not want to use.\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"googleanalyticsaccount\" value=\"$googleanalyticsaccount\" size=\"50\">\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
		$html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Map default view:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"googleview\">\n";
        $html .= "                          <option value=\"G_NORMAL_MAP\" SELECTED>Normal</option>\n";
        $html .= "                          <option value=\"G_SATELLITE_MAP\">Satellite</option>\n";
        $html .= "                          <option value=\"G_HYBRID_MAP\">Hybrid</option>\n";
        $html .= "                          <option value=\"G_PHYSICAL_MAP\">Physical</option>\n";
        $html .= "                      </select>\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";

        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Show Map Data on Load:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"showmap\">";
        if ($showmap)
        {
            $html .= "                      <option value=\"$showmap\" SELECTED>$showmap</option>";
        }
        $html .= "                          <option value=\"yes\">Yes</option>";
        $html .= "                          <option value=\"no\">No</option>";
        $html .= "                      </select>";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";

        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Show Bearing Arrows:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"show_bearings\">";
        if ($show_bearings)
        {
            $html .= "                      <option value=\"$show_bearings\" SELECTED>$show_bearings</option>";
        }
        $html .= "                          <option value=\"yes\">Yes</option>";
        $html .= "                          <option value=\"no\">No</option>";
        $html .= "                      </select>";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";

        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Map width:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"mapwidth\" value=\"" . trim($mapwidth," px") . "\" value=\"600\" size=5>\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Map height:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"mapheight\" value=\"" . trim($mapheight," px") . "\" value=\"400\" size=5>\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Map border:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"mapborder\" value=\"" . trim($mapborder," px") . "\" value=\"2\" size=5>\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Map border color:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"bordercolor\">";
        if ($bordercolor)
        {
            $currentcolor = array_key_exists($bordercolor, $colorarray) ? $colorarray[$bordercolor] : "Custom $bordercolor";
            $html .= "                      <option value=\"$bordercolor\">$currentcolor</option>";
        }
        foreach($colorarray as $hexval => $clrname)
        {
            $html .= "                      <option value=\"$hexval\">$clrname</option>";
        }
        $html .= "                      </select>";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Display crosshair at map center:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"crosshair\">";
        if ($crosshair)
        {
            $html .= "                      <option value=\"$crosshair\" SELECTED>$crosshair</option>";
        }
        $html .= "                          <option value=\"yes\">Yes</option>";
        $html .= "                          <option value=\"no\">No</option>";
        $html .= "                      </select>";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Click to center map:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"clickcenter\">";
        if ($clickcenter)
        {
            $html .= "                      <option value=\"$clickcenter\" SELECTED>$clickcenter</option>";
        }
        $html .= "                          <option value=\"yes\">Yes</option>";
        $html .= "                          <option value=\"no\">No</option>";
        $html .= "                      </select>";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Display map overview:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"overview\">";
        if ($overview)
        {
            $html .= "                      <option value=\"$overview\" SELECTED>$overview</option>";
        }
        $html .= "                          <option value=\"yes\">Yes</option>";
        $html .= "                          <option value=\"no\">No</option>";
        $html .= "                      </select>";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td colspan=\"2\">\n";
        $html .= "                      &nbsp;\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Map align:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"mapalign\">";
        if ($mapalign)
        {
            $html .= "                      <option value=\"$mapalign\" SELECTED>" . ucfirst($mapalign) . "</option>";
        }
        $html .= "                          <option value=\"center\">Center</option>";
        $html .= "                          <option value=\"left\">Left</option>";
        $html .= "                          <option value=\"right\">Right</option>";
        $html .= "                      </select>";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td colspan=\"2\">\n";
        $html .= "                      &nbsp;\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Display background color:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"bgcolor\">";
        if ($bgcolor)
        {
            $currentcolor = array_key_exists($bgcolor, $colorarray) ? $colorarray[$bgcolor] : "Custom $bgcolor";
            $html .= "                      <option value=\"$bgcolor\">$currentcolor</option>";
        }
        foreach($colorarray as $hexval => $clrname)
        {
            $html .= "                      <option value=\"$hexval\">$clrname</option>";
        }
        $html .= "                      </select>";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td colspan=\"2\">\n";
        $html .= "                      &nbsp;\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Date format (<a href=\"http://us.php.net/date\" target=\"_blank\">http://us.php.net/date</a>):\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                  <select name=\"date_format\">";
        if ($date_format)
        {
            $html .= "                  <option value=\"$date_format\">" . date("$date_format") . "</option>";
        }
        $html .= "                      <option value=\"Y-m-d H:i:s\">" . date("Y-m-d H:i:s") . "</option>";
        $html .= "                      <option value=\"F j, Y, g:i a\">" . date("F j, Y, g:i a") . "</option>";
        $html .= "                      <option value=\"F j, Y, H:i:s\">" . date("F j, Y, H:i:s") . "</option>";
        $html .= "                      <option value=\"D, M j, Y, g:i a\">" . date("D, M j, Y, g:i a") . "</option>";
        $html .= "                      <option value=\"D, M j, Y, H:i:s\">" . date("D, M j, Y, H:i:s") . "</option>";
        $html .= "                      <option value=\"m/d/Y g:i a\">" . date("m/d/Y g:i a") . "</option>";
        $html .= "                      <option value=\"m/d/Y H:i:s\">" . date("m/d/Y H:i:s") . "</option>";
        $html .= "                      <option value=\"m/d/y g:i a\">" . date("m/d/y g:i a") . "</option>";
        $html .= "                      <option value=\"m/d/y H:i:s\">" . date("m/d/y H:i:s") . "</option>";
        $html .= "                      <option value=\"m-d-Y g:i a\">" . date("m-d-Y g:i a") . "</option>";
        $html .= "                      <option value=\"m-d-Y H:i:s\">" . date("m-d-Y H:i:s") . "</option>";
        $html .= "                      <option value=\"M d Y g:i a\">" . date("M d Y g:i a") . "</option>";
        $html .= "                      <option value=\"M d Y H:i:s\">" . date("M d Y H:i:s") . "</option>";
        $html .= "                      <option value=\"d.m.Y g:i a\">" . date("d.m.Y g:i a") . "</option>";
        $html .= "                      <option value=\"d.m.Y H:i:s\">" . date("d.m.Y H:i:s") . "</option>";
        $html .= "                      <option value=\"d/m/Y g:i a\">" . date("d/m/Y g:i a") . "</option>";
        $html .= "                      <option value=\"d/m/Y H:i:s\">" . date("d/m/Y H:i:s") . "</option>";
        $html .= "                      <option value=\"d M Y g:i a\">" . date("d M Y g:i a") . "</option>";
        $html .= "                      <option value=\"d M Y H:i:s\">" . date("d M Y H:i:s") . "</option>";
        $html .= "                      <option value=\"d-M-Y g:i a\">" . date("d-M-Y g:i a") . "</option>";
        $html .= "                      <option value=\"d-M-Y H:i:s\">" . date("d-M-Y H:i:s") . "</option>";
        $html .= "                      <option value=\"r\">" . date("r") . "</option>";
        $html .= "                  </select>";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td colspan=\"2\">\n";
        $html .= "                      &nbsp;\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Map Auto-refresh in seconds (0 for manual refresh):\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <input type=\"text\" name=\"refresh\" value=\"$refresh\" size=\"5\" value=\"99999\">\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Public page access:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"public_page\">\n";
        if ($public_page)
        {
            $html .= "                      <option SELECTED>$public_page</option>\n";
        }
        $html .= "                          <option>yes</option>\n";
        $html .= "                          <option>no</option>\n";
        $html .= "                      </select>\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td colspan=\"2\">\n";
        $html .= "                      &nbsp;\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Language:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"language\">\n";
        if ($language)
        {
            $html .= "                      <option SELECTED>$language</option>\n";
        }
        $html .= "                          <option>english</option>\n";
        $html .= "                          <option>italian</option>\n";
        $html .= "                          <option>german</option>\n";
        $html .= "                          <option>french</option>\n";
        $html .= "                          <option>spanish</option>\n";
        $html .= "                      </select>\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Units:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\"units\">\n";
        if ($units)
        {
            $html .= "                      <option SELECTED>$units</option>\n";
        }
        $html .= "                          <option>imperial</option>\n";
        $html .= "                          <option>metric</option>\n";
        $html .= "                      </select>\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td colspan=\"2\">\n";
        $html .= "                      &nbsp;\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td align=\"right\">\n";
        $html .= "                      Allow Viewer Customization:\n";
        $html .= "                  </td>\n";
        $html .= "                  <td align=\"left\">\n";
        $html .= "                      <select name=\$allow_custom\">\n";
        if ($language)
        {
            $html .= "                      <option SELECTED>$allow_custom</option>\n";
        }
        $html .= "                          <option>yes</option>\n";
        $html .= "                          <option>no</option>\n";
        $html .= "                      </select>\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "              <tr>\n";
        $html .= "                  <td colspan=\"2\" align=\"center\">\n";
        $html .= "                      <input type=\"hidden\" name=\"action\" value=\"install\">\n";
        $html .= "                      <input type=\"submit\" value=\"Complete Installation\"\n";
        $html .= "                  </td>\n";
        $html .= "              </tr>\n";
        $html .= "          </table>\n";
        $html .= "      </form>\n";
        $html .= "    </div>\n";
    }

    $html .= "        <div align=center>\n";
    $html .= "          <br>\n";
    $html .= "          Tracking information provided by <a href=\"http://forum.xda-developers.com/showthread.php?t=340667\" target=\"_blank\">TrackMe</a>\n";
    $html .= "        </div>\n";
    $html .= "      </body>\n";
    $html .= "    </html>\n";

    print $html;

?>
