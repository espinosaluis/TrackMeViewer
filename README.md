# TrackMeViewer

* Version: 3.5
* Date:    08/15/2020

Browser/MySQL/PHP based Application to display trips recorded by TrackMe App on Android

This is a simple description to help you get TrackMeViewer running on your own server. 
The TrackMeViewer currently only works together with the TrackMe App, an application built by Luis Espinosa (_LEM_) on XDA Developers.

Please feel free to modify the files to meet your needs.
Post comments and questions to the forum thread mentioned below.

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

## Translations
The translations into French, Italian, Dutch and Slovak were developed via Google Translator.

See also
--------
* http://luisespinosa.com
* http://forum.xda-developers.com/showthread.php?t=340667

Thanks again to Luis Espinosa for developing and maintaining the application and for taking comments and feedback to drive his development.
