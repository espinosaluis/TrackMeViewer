<?php
	//////////////////////////////////////////////////////////////////////////////
	//
	// TrackMe Maps Display Installer
	// Version: 1.30
	// Date:    04/14/2020
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

	$no_config = !file_exists('config.php');
	require_once('database.php');
	require_once('tileprovider.php');

	$html  = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
	$html .= " \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	$html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
	$html .= " <head>\n";
	$html .= "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
	$html .= "  <title>TrackMe Server Side Installation</title>\n";
	$html .= " </head>\n";
	$html .= " <body>\n";
	$html .= "  <div align=center>\n";
	$html .= "   <h1>TrackMe Server Side Installation</h1>\n";

	if (isset($_POST["action"]) && $_POST["action"] == "install") {
		$DBIP                   = $_POST["DBIP"];
		$DBUSER                 = $_POST["DBUSER"];
		$DBPASS                 = $_POST["DBPASS"];
		$DBNAME                 = $_POST["DBNAME"];
		$googleapikey           = $_POST["googleapikey"];
		$mapboxaccesstoken      = $_POST["mapboxaccesstoken"];
		$thunderforestapikey    = $_POST["thunderforestapikey"];
		$tomtomapikey           = $_POST["tomtomapikey"];
		$hereapikey             = $_POST["hereapikey"];
		$googleanalyticsaccount = $_POST["googleanalyticsaccount"];
		$showbearings           = $_POST["showbearings"];
		$crosshair              = $_POST["crosshair"];
		$clickcenter            = $_POST["clickcenter"];
		$units                  = $_POST["units"];
		$language               = $_POST["language"];
		$tileprovider           = $_POST["tileprovider"];
		$tilePT                 = $_POST["tilePT"];
		$refresh                = $_POST["refresh"];
		$publicpage             = $_POST["publicpage"];
		$dateformat             = $_POST["dateformat"];
		$allowcustom            = $_POST["allowcustom"];
		
		$folder                 = "";
		$username               = "";
		$mapwidth               = "";
		$mapheight              = "";
		$mapborder              = "";
		$bordercolor            = "";
		$mapalign               = "";

		$html .= "  </div>\n";
		$connection = array('host' => $DBIP, 'name' => $DBNAME, 'user' => $DBUSER, 'pass' => $DBPASS);
		$db = connect_save($connection);
		if (is_null($db)) {
			$html .= "  ERROR: Failed to connect to database.";
			print $html;
			die();
		}

		$sql = file_get_contents("database.sql");
		$db->exec($sql);
		$html .= "  Database tables created successfully<br>\n";

		//Create config.php file
		if ($configfile = fopen ("config.php", "w+")) {
			$fp = fwrite($configfile,"<?php\n");
			$fp = fwrite($configfile,"    //////////////////////////////////////////////////////////////////////////////\n");
			$fp = fwrite($configfile,"    //\n");
			$fp = fwrite($configfile,"    // TrackMe Maps Display Configuration\n");
			$fp = fwrite($configfile,"    // Version: 1.30\n");
			$fp = fwrite($configfile,"    // Date:    04/14/2010\n");
			$fp = fwrite($configfile,"    // Time:    16:00:00\n");
			$fp = fwrite($configfile,"    //\n");
			$fp = fwrite($configfile,"    // TrackMe built by Staryon\n");
			$fp = fwrite($configfile,"    // For more information go to:\n");
			$fp = fwrite($configfile,"    // http://forum.xda-developers.com/showthread.php?t=340667\n");
			$fp = fwrite($configfile,"    //\n");
			$fp = fwrite($configfile,"    // Please feel free to modify the index.php file to meet your needs.\n");
			$fp = fwrite($configfile,"    // Post comments and questions to the forum thread above.\n");
			$fp = fwrite($configfile,"    //\n");
			$fp = fwrite($configfile,"    //////////////////////////////////////////////////////////////////////////////\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Database Information\n");
			$fp = fwrite($configfile,"    \$DBIP   = \"$DBIP\";\n");
			$fp = fwrite($configfile,"    \$DBUSER = \"$DBUSER\";\n");
			$fp = fwrite($configfile,"    \$DBPASS = \"$DBPASS\";\n");
			$fp = fwrite($configfile,"    \$DBNAME = \"$DBNAME\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Some paps/tiles provider require \"api keys\" or \"acccess tokens\". See tileprovider.php for details\n");
			$fp = fwrite($configfile,"    \$googleapikey = \"$googleapikey\";\n");
			$fp = fwrite($configfile,"    \$mapboxaccesstoken = \"$mapboxaccesstoken\";\n");
			$fp = fwrite($configfile,"    \$thunderforestapikey = \"$thunderforestapikey\";\n");
			$fp = fwrite($configfile,"    \$tomtomapikey = \"$tomtomapikey\";\n");
			$fp = fwrite($configfile,"    \$hereapikey = \"$hereapikey\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // If you have Google Analytics Account. Obtain from https://www.google.com/analytics/home/?et=reset&hl=en-US\n");
			$fp = fwrite($configfile,"    // or just leave blank if you do not want to use it\n");
			$fp = fwrite($configfile,"    \$googleanalyticsaccount = \"$googleanalyticsaccount\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Show bearing arrows on map (\"yes\" or \"no\")\n");
			$fp = fwrite($configfile,"    \$showbearings = \"$showbearings\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Display crosshair at map center (\"yes\" or \"no\")\n");
			$fp = fwrite($configfile,"    \$crosshair = \"$crosshair\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Click to center map (\"yes\" or \"no\")\n");
			$fp = fwrite($configfile,"    \$clickcenter = \"$clickcenter\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Units (\"imperial\" or \"metric\")\n");
			$fp = fwrite($configfile,"    \$units = \"$units\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Language (\"english\", \"italian\", \"german\", \"french\", \"spanish\", \"dutch\" or \"slovak\")\n");
			$fp = fwrite($configfile,"    \$language = \"$language\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Tile Provider (see tileprovider.php for details - use one of the array keys)\n");
			$fp = fwrite($configfile,"    \$tileprovider = \"$tileprovider\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Shall a Public Transport map be overlayed on the selected map (\"yes\" or \"no\")\n");
			$fp = fwrite($configfile,"    \$tilePT = \"$tilePT\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Map auto-refresh in seconds (0 for manual refresh)\n");
			$fp = fwrite($configfile,"    \$refresh = \"$refresh\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Public Page Access (\"yes\" or \"no\")\n");
			$fp = fwrite($configfile,"    \$publicpage = \"$publicpage\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Date Display Format\n");
			$fp = fwrite($configfile,"    // Must be valid php date format (http://us.php.net/date)\n");
			$fp = fwrite($configfile,"    \$dateformat = \"$dateformat\";\n");
			$fp = fwrite($configfile,"    \n");
			$fp = fwrite($configfile,"    // Allow customization of display (\"yes\" or \"no\")\n");
			$fp = fwrite($configfile,"    \$allowcustom = \"$allowcustom\";\n");
			$fp = fwrite($configfile,"?>\n");
		} else {
			$html .= "  ERROR: config.php can not be opened. You need to edit it manually.<br>";
		}
		fclose($configfile);

		$html .= "  config.php file created successfully<br>\n";
		$html .= "  <br>\n";
		$html .= "  <br>\n";
		$html .= "  Remember to create two folders (\"routes\" and \"pics\") in your TrackMe folder.<br>\n";
		$html .= "  <br>\n";
		$html .= "  If there are no errors listed above then the installation of server side files and tables is complete.\n";
		$html .= "  You MUST delete the install.php and database.sql files before the map page will display.\n";
		$html .= "  Once you delete install.php you can <a href=\"index.php\">click here</a> to view your custom map.<br>\n";
		$html .= "  <br>\n";
		$html .= "  If you have not installed the application on your Windows Mobile Professional device you can download the latest version <a href=\"http://luisespinosa.com/bin/trackme/TrackMe.CAB\">here</a><br>\n";
		$html .= "  <br>\n";
		$html .= "  <br>\n";
	} else {
		$html .= "   (*) Must be created manually before submitting this form!\n";
		$html .= "   <form name=\"install\" method=\"post\">\n";
		$html .= "    <table border=\"0\">\n";
		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Database Server Address:\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"DBIP\" value=\"$DBIP\" size=\"40\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";
		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Database User (*):\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"DBUSER\" value=\"$DBUSER\" size=\"40\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";
		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Database Password (*):\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"DBPASS\" value=\"$DBPASS\" size=\"40\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";
		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Database Name (*):\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"DBNAME\" value=\"$DBNAME\" size=\"40\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\">\n";
		$html .= "       &nbsp;\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Google Maps API Key (obtain <a href=\"http://www.google.com/apis/maps/signup.html\" target=\"_blank\">here</a>):\n";
		$html .= "       Leave blank, if you do not want to use Google Maps.\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"googleapikey\" value=\"$googleapikey\" size=\"50\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";
		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Mapbox Access Token (obtain <a href=\"https://www.mapbox.com/maps/\" target=\"_blank\">here</a>):\n";
		$html .= "       Leave blank, if you do not want to use Mapbox Maps.\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"mapboxaccesstoken\" value=\"$mapboxaccesstoken\" size=\"115\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";
		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       ThunderForest Mas API Key (obtain <a href=\"https://www.thunderforest.com/\" target=\"_blank\">here</a>):\n";
		$html .= "       Leave blank, if you do not want to use ThunderForest Maps.\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"thunderforestapikey\" value=\"$thunderforestapikey\" size=\"40\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";
		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       TomTom Maps API Key (obtain <a href=\"https://developer.tomtom.com/\" target=\"_blank\">here</a>):\n";
		$html .= "       Leave blank, if you do not want to use TomTom Maps.\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"tomtomapikey\" value=\"$tomtomapikey\" size=\"40\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";
		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       HERE Maps API Key (obtain <a href=\"https://developer.here.com/\" target=\"_blank\">here</a>):\n";
		$html .= "       Leave blank, if you do not want to use HERE Maps.\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"hereapikey\" value=\"$hereapikey\" size=\"55\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\">\n";
		$html .= "       &nbsp;\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Google Analytics Account (obtain <a href=\"https://www.google.com/analytics/home/?et=reset&hl=en-US\" target=\"_blank\">here</a>):\n";
		$html .= "       Leave blank, if you do not want to use.\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"googleanalyticsaccount\" value=\"$googleanalyticsaccount\" size=\"50\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\">\n";
		$html .= "       &nbsp;\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Show Bearing Arrows:\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"showbearings\">\n";
		$html .= "        <option value=\"$showbearings\" selected>$showbearings</option>\n";
		$html .= "        <option value=\"yes\">yes</option>\n";
		$html .= "        <option value=\"no\">no</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Display a Crosshair Sign at the center of the map:\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"crosshair\">\n";
		$html .= "        <option value=\"$crosshair\" selected>$crosshair</option>\n";
		$html .= "        <option value=\"yes\">yes</option>\n";
		$html .= "        <option value=\"no\">no</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Center the map on mouse click position:\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"clickcenter\">\n";
		$html .= "        <option value=\"$clickcenter\" selected>$clickcenter</option>\n";
		$html .= "        <option value=\"yes\">yes</option>\n";
		$html .= "        <option value=\"no\">no</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\">\n";
		$html .= "       &nbsp;\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Units:\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"units\">\n";
		$html .= "        <option value=\"$units\" selected>$units</option>\n";
		$html .= "        <option value=\"imperial\">imperial</option>\n";
		$html .= "        <option value=\"metric\">metric</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";
		$html .= "     <tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Language:\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"language\">\n";
		$html .= "        <option value=\"$language\" selected>$language</option>\n";
		$html .= "        <option value=\"english\">english</option>\n";
		$html .= "        <option value=\"italian\">italian</option>\n";
		$html .= "        <option value=\"german\">german</option>\n";
		$html .= "        <option value=\"french\">french</option>\n";
		$html .= "        <option value=\"spanish\">spanish</option>\n";
		$html .= "        <option value=\"dutch\">dutch</option>\n";
		$html .= "        <option value=\"slovak\">slovak</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\">\n";
		$html .= "       &nbsp;\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Tile or Map Provider (* needs an apikey or access token):\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"tileprovider\">\n";
		$html .= "        <option value=\"$tileprovider\" selected>$tileprovider</option>\n";
		foreach ($tileproviders as $tileprovidername => $tileproviderspecs) {
			$html .= "        <option value=\"$tileprovidername\">$tileprovidername</option>\n";
		}
		$html .= "        <option value=\"yes\">yes</option>\n";
		$html .= "        <option value=\"no\">no</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Shall a Publich Transport map be overlayed:\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"tilePT\">\n";
		$html .= "        <option value=\"$tilePT\" selected>$tilePT</option>\n";
		$html .= "        <option value=\"yes\">yes</option>\n";
		$html .= "        <option value=\"no\">no</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\">\n";
		$html .= "       &nbsp;\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Map Auto-refresh in seconds (0 for manual refresh):\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <input type=\"text\" name=\"refresh\" value=\"$refresh\" size=\"5\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Public page access:\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"publicpage\">\n";
		$html .= "        <option  value=\"$publicpage\" selected>$publicpage</option>\n";
		$html .= "        <option value=\"yes\">Yes</option>\n";
		$html .= "        <option value=\"no\">No</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\">\n";
		$html .= "       &nbsp;\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Date format (<a href=\"http://us.php.net/date\" target=\"_blank\">http://us.php.net/date</a>):\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"dateformat\">\n";
		$html .= "        <option value=\"$dateformat\">" . date("$dateformat") . "</option>\n";
		$html .= "        <option value=\"Y-m-d H:i:s\">" . date("Y-m-d H:i:s") . "</option>\n";
		$html .= "        <option value=\"F j, Y, g:i a\">" . date("F j, Y, g:i a") . "</option>\n";
		$html .= "        <option value=\"F j, Y, H:i:s\">" . date("F j, Y, H:i:s") . "</option>\n";
		$html .= "        <option value=\"D, M j, Y, g:i a\">" . date("D, M j, Y, g:i a") . "</option>\n";
		$html .= "        <option value=\"D, M j, Y, H:i:s\">" . date("D, M j, Y, H:i:s") . "</option>\n";
		$html .= "        <option value=\"m/d/Y g:i a\">" . date("m/d/Y g:i a") . "</option>\n";
		$html .= "        <option value=\"m/d/Y H:i:s\">" . date("m/d/Y H:i:s") . "</option>\n";
		$html .= "        <option value=\"m/d/y g:i a\">" . date("m/d/y g:i a") . "</option>\n";
		$html .= "        <option value=\"m/d/y H:i:s\">" . date("m/d/y H:i:s") . "</option>\n";
		$html .= "        <option value=\"m-d-Y g:i a\">" . date("m-d-Y g:i a") . "</option>\n";
		$html .= "        <option value=\"m-d-Y H:i:s\">" . date("m-d-Y H:i:s") . "</option>\n";
		$html .= "        <option value=\"M d Y g:i a\">" . date("M d Y g:i a") . "</option>\n";
		$html .= "        <option value=\"M d Y H:i:s\">" . date("M d Y H:i:s") . "</option>\n";
		$html .= "        <option value=\"d.m.Y g:i a\">" . date("d.m.Y g:i a") . "</option>\n";
		$html .= "        <option value=\"d.m.Y H:i:s\">" . date("d.m.Y H:i:s") . "</option>\n";
		$html .= "        <option value=\"d/m/Y g:i a\">" . date("d/m/Y g:i a") . "</option>\n";
		$html .= "        <option value=\"d/m/Y H:i:s\">" . date("d/m/Y H:i:s") . "</option>\n";
		$html .= "        <option value=\"d M Y g:i a\">" . date("d M Y g:i a") . "</option>\n";
		$html .= "        <option value=\"d M Y H:i:s\">" . date("d M Y H:i:s") . "</option>\n";
		$html .= "        <option value=\"d-M-Y g:i a\">" . date("d-M-Y g:i a") . "</option>\n";
		$html .= "        <option value=\"d-M-Y H:i:s\">" . date("d-M-Y H:i:s") . "</option>\n";
		$html .= "        <option value=\"r\">" . date("r") . "</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\">\n";
		$html .= "       &nbsp;\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td align=\"right\">\n";
		$html .= "       Allow Viewer Customization:\n";
		$html .= "      </td>\n";
		$html .= "      <td align=\"left\">\n";
		$html .= "       <select name=\"allowcustom\">\n";
		$html .= "         <option  value=\"$allowcustom\" selected>$allowcustom</option>\n";
		$html .= "        <option value=\"yes\">yes</option>\n";
		$html .= "        <option value=\"no\">no</option>\n";
		$html .= "       </select>\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\">\n";
		$html .= "       &nbsp;\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";

		$html .= "     <tr>\n";
		$html .= "      <td colspan=\"2\" align=\"center\">\n";
		$html .= "       <input type=\"hidden\" name=\"action\" value=\"install\">\n";
		$html .= "       <input type=\"submit\" value=\"Complete Installation\">\n";
		$html .= "      </td>\n";
		$html .= "     </tr>\n";
		$html .= "    </table>\n";
		$html .= "   </form>\n";
		$html .= "  </div>\n";
	}

	$html .= "  <div align=center>\n";
	$html .= "   <br>\n";
	$html .= "   Tracking information provided by <a href=\"http://forum.xda-developers.com/showthread.php?t=340667\" target=\"_blank\">TrackMe</a>\n";
	$html .= "  </div>\n";
	$html .= " </body>\n";
	$html .= "</html>\n";

	print $html;

?>
