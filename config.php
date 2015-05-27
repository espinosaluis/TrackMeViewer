<?php
    //////////////////////////////////////////////////////////////////////////////
    //
    // TrackMe Google Maps Display Configuration
    // Version: 1.20
    // Date:    12/22/2007
    // Time:    16:00:00
    //
    // TrackMe built by Staryon
    // For more information go to:
    // http://forum.xda-developers.com/showthread.php?t=340667
    //
    // Please feel free to modify the index.php file to meet your needs.
    // Post comments and questions to the forum thread above.
    //
    //////////////////////////////////////////////////////////////////////////////

    // Database Information
    $DBIP         = "localhost";
    $DBUSER       = "Replace with MySQL User Name";
    $DBPASS       = "Replace with MySQL Password";
    $DBNAME       = "Replace with MySQL Database Name";



    // Google API Key. Obtain from http://www.google.com/apis/maps/signup.html
    $googleapikey = "Replace with your Google API Key";
	// if you have Google Analytics Account, put account number here, otherwise leave blank
	$googleanalyticsaccount="";
	
    // Google Maps default view (G_NORMAL_MAP,G_SATELLITE_MAP, G_HYBRID_MAP or G_PHYSICAL_MAP)
    $googleview   = "G_NORMAL_MAP";
	//to show all map points when you arrive at the page and when you change trips (yes or no)
	$showmap="yes";

    // Map style
    $mapwidth     = "700px";
    $mapheight    = "500px";
    $mapborder    = "1px";
    $bordercolor  = "#000";
    $mapalign     = "center";

    // Background color for map display
    $bgcolor      = "#cccccc";

   // Display Header Title (yes or no)
    $show_header = "no";

    //Show bearing arrows
	$show_bearings	  ="yes";

	// Display crosshair at map center (yes or no)
    $crosshair    = "yes";

    // Click to center map (yes or no)
    $clickcenter  = "yes";

    // Display map overview (yes or no)
    $overview     = "yes";

    // Map auto-refresh in seconds (0 for manual refresh)
    $refresh      = "120";

    // Public Page Access (yes or no)
    $public_page  = "yes";

    // Date Display Format
    // Must be valid php date format (http://us.php.net/date)
    $date_format  = "d/m/Y H:i:s";

    // Units (imperial or metric)
    $units       = "metric";

    // Language (English, Italian, German, French or Spanish) //trackmeIT
    $language     = "english";

    // Allow customization of display (yes or no)
    $allow_custom = "yes";
?>
