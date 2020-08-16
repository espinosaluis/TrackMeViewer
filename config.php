<?php
    //////////////////////////////////////////////////////////////////////////////
    //
    // TrackMeViewer - Browser/MySQL/PHP based Application to display trips recorded by TrackMe App on Android
    // Version: 3.5
    // Date:    08/15/2020
    //
    // Previously built by Staryon
    // For more information go to:
    // http://forum.xda-developers.com/showthread.php?t=340667
    //
    // Please feel free to modify the index.php file to meet your needs.
    // Post comments and questions to the forum thread above.
    //
    //////////////////////////////////////////////////////////////////////////////
    
    // Database Information
    $DBIP   = "";
    $DBUSER = "";
    $DBPASS = "";
    $DBNAME = "";
    
    // Some maps/tiles provider require "api keys" or "acccess tokens". See tileprovider.php for details
    $googleapikey = "";
    $mapboxaccesstoken = "";
    $thunderforestapikey = "";
    $tomtomapikey = "";
    $hereapikey = "";
    
    // If you have Google Analytics Account. Obtain from https://www.google.com/analytics/home/?et=reset&hl=en-US
    // or just leave blank if you do not want to use it
    // $googleanalyticsaccount = "";
    
    // Show title in Browser Window
    $windowtitle = "My TrackMeViewer";
    
    // Color for the trip line ("#rrggbb")
    $linecolor = "#0000ff";
    
    // Display markers at map (1 or 0)
    $markertype = 1;
    
    // Show bearing arrows on map (1 or 0)
    $showbearings = 1;
    
    // Display crosshair at map center (1 or 0)
    $crosshair = 0;
    
    // Click to center map (1 or 0)
    $clickcenter = 0;
    
    // Units ("imperial" or "metric")
    $units = "metric";
    
    // Language ("english", "italian", "german", "french", "spanish", "dutch" or "slovak")
    $language = "english";
    
    // Tile Provider (see tileprovider.php for details - use one of the array keys)
    $tileprovider = "OpenStreetMap Mapnik";
    
    // Shall a Public Transport map be overlayed on the selected map (1 or 0)
    $tilePT = 0;
    
    // Map auto-refresh interval in seconds (0 for manual refresh)
    $interval = "120";
    
    // Public Page Access ("yes" or "no")
    $publicpage = "no";
    
    // Date Display Format
    // Must be valid php date() format (https://www.php.net/manual/en/function.date.php)
    $dateformat = "d.m.Y";
    
    // Time Display Format
    // Must be valid php date() format (https://www.php.net/manual/en/function.date.php)
    $timeformat = "H:i:s";
    
    // Allow customization of display ("yes" or "no")
    $allowcustom = "yes";
    
    // Allow changes to database for trips, markers, comments and photos ("yes" or "no")
    $allowDBchange = "yes";
?>
