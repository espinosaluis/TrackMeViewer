<?php
    //////////////////////////////////////////////////////////////////////////////
    //
    // TrackMe Maps Display Configuration
    // Version: 1.30
    // Date:    04/14/2010
    // Time:    16:00:00
    //
    // TrackMe app built by LEM
    // For more information go to:
    // https://forum.xda-developers.com/showthread.php?t=1340211
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

    // Some paps/tiles provider require "api keys" or "acccess tokens". See tileprovider.php for details
    $googleapikey = "Replace with your Google API Key";
    $mapboxaccesstoken = "";
    $thunderforestapikey = "";
    $tomtomapikey = "";
    $hereapikey = "";

    // If you have Google Analytics Account. Obtain from https://www.google.com/analytics/home/?et=reset&hl=en-US
    // or just leave blank if you do not want to use it
    $googleanalyticsaccount = "";

    // Show bearing arrows on map ("yes" or "no")
    $showbearings = "yes";

    // Display crosshair at map center ("yes" or "no")
    $crosshair = "yes";

    // Click to center map ("yes" or "no")
    $clickcenter = "yes";

    // Units ("imperial" or "metric")
    $units = "metric";

    // Language ("english", "italian", "german", "french" or "spanish")
    $language = "english";

    // Tile Provider (see tileprovider.php for details - use one of the array keys)
    $tileprovider = "OpenStreetMap Mapnik";

    // Shall a Public Transport map be overlayed on the selected map ("yes" or "no")
    $tilePT = "no";

    // Map auto-refresh in seconds (0 for manual refresh)
    $refresh = "120";

    // Public Page Access ("yes" or "no")
    $publicpage = "no";

    // Date Display Format
    // Must be valid php date format (http://us.php.net/date)
    $dateformat = "d.m.Y H:i:s";

    // Allow customization of display ("yes" or "no")
    $allowcustom = "yes";
?>
