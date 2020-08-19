# TrackMeViewer

* Version: 3.5
* Date:    08/15/2020

Browser/MySQL/PHP based Application to display trips recorded by TrackMe App on Android

This is a simple description to help you get TrackMeViewer running on your own server. 
The TrackMeViewer currently only works together with the TrackMe App, an application built by Luis Espinosa (_LEM_) on XDA Developers.

Please feel free to modify the files to meet your needs.
Post comments and questions to the forum thread mentioned below.

## Features and Functions added in v3.5

### For users: 
1. Let the user allow to select the colour for the trip line
2. Use specified Date and Time formats from config.php in all displays
3. Switched to a less complex date and time picker for start and end date/time way point filtering
4. Let the user allow to suppress the position markers, if the trip recording provides to many or too dense positions
5. Remember last trip seen by the user
6. Let the user allow to group trips by choosing proper naming (:) and display groups of trips together ("[Any]")
7. Let the user allow to delete one single Marker Point (position) of a trip from the database
8. Show detailed trip data to the side of the map (resizeable). Things such as: max speed, max altitude, min altitude, descent ascent percentage, moving time, full trip time, etc
9. Let the user allow to scroll through each point of a trip (backward/forward) from the details pop-up
10. Show the latitude and longitude of the Marker Points in the details pop-up in different notations (e.g. 48.86648N, 9°01'30.4"E, 9°5.01304'W)
11. Store user options settings in cookies and ask for allow or deny
12. Issue a warning once a day, when a user modifies a trip, that it will be overwritten with a resynch from the TrackMe App
13. Add a warning when user selects "[Any]" trips
14. Changed "Display Options Show/Hide" check box to toggle option settings and make them to work immediately
### For Web App owner:
1. Let the Web App owner to allow to run Apache2 Web Server on any port (not only 80) or path.
2. Let the Web App owner to specify the Web Page title in the config.php
### For maintainer:
1. Removed no longer necessary "None" trip selection
2. Got rid of extra " ... = urldecode($_GET ...". Superglobal $_GET is already decoded
3. Consequently and everywhere use PDO for all database access with bound parameters to prohibit data injection
4. Got rid of "UNDEFINED INDEX " notices issued by php and saved in Apache log file
5. Restructured and reformatted code for better readability and maintenance

## Requirements
It requires a web server with MySQL and PHP.

## Installation
1. FTP all of the files from the Zip to a folder on your web server. The examples here will use the folder "trackme".
   Please note that folder names are CaSE sENsiTiVE!!! This folder must be writeable or the config file will not be built correctly.
   If you choose to not make the folder writeable, that is fine. You just need to manually configure the config.php file before using.
   The fields in the file are clearly marked.
2. In the same folder as the php files, create a new folder called "routes".
3. In the same folder as the php files, create a new folder called "pics" and CHMOD it to 777.
4. Using your MySQL access (e.g. phpMyAdmin), create a database with any name, which will house the tables created by the install script.
5. Make sure the user account you want to use for the installer has access to create tables in that database.
6. Optionally get API Keys and/or Access Tokens from various map tile providers. See their correspnding develpers pages for links and procedures.
   Check the config.php file for a list of such provider requireing keys and tokens.
7. Open your browser for the Installer to generate or update the config.php.
8. Browse to your web server and folder, specifying the install.php file, e.g.: http://www.yourdomain.com/trackme/install.php
9. Fill in the blanks with the database access information used above.
10. Click the "Complete Installation" button.
11. If you got errors, go back and try again, making note of the errors and fix them.
12. If you didn't get errors, delete or rename both the install.php and database.sql files.


## Client
You can download the most recent version of the TrackMe client App for Android from the Google App Store.

See also
--------
* http://luisespinosa.com
* http://forum.xda-developers.com/showthread.php?t=340667

Thanks again to Luis Espinosa for developing and maintaining the application and for taking comments and feedback to drive his development.
