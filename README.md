# TrackMeViewer
Web viewer for TrackMe app compatible with Android, Windows Phone and Windows Mobile clients

//////////////////////////////////////////////////////////////////////////////
//
// TrackMe Google Maps Display Readme
//
// TrackMe built by Luis Espinosa (_LEM_)
// For more information go to:
// http://luisespinosa.com
// http://forum.xda-developers.com/showthread.php?t=1340211
//
// Please feel free to modify the files to meet your needs.
// Post comments and questions to the forum thread above.
//
//////////////////////////////////////////////////////////////////////////////


This is a simple script to help you get TrackMe running on your own server. I take no credit for any of the work done on TrackMe, an application built by Luis Espinosa (_LEM_) on XDA Developers.

You can download the most recent version of the TrackMe client here: 


1. Android
https://play.google.com/store/apps/details?id=LEM.TrackMe 

2. Windows Mobile 
http://forum.xda-developers.com/showthread.php?t=340667

3. Windows Phone 7
http://www.windowsphone.com/en-us/store/app/trackme/8ebd20c9-1d80-e011-986b-78e7d1fa76f8   


Requirements:
1) A Web server with MySQL and PHP
2) Android phone / Windows Phone 7 / Windows Mobile Professional (Pocket PC)


Installation Steps:
1) FTP all of the files from the Zip to a folder on your web server. In my examples I will use the folder "trackme". Please note that folder names are CaSE sENsiTiVE!!! This folder must be writeable or the config file will not be built correctly. If you choose to not make the folder writeable that is fine, you just need to manually configure the config.php file before uploading. The fields in the file are clearly marked.
2) In the same folder as the php files create a new folder called "routes".
3) In the same folder as the php files create a new folder called "pics" and CHMOD it to 777.
4) Using your MySQL access create a database which will house the tables created by the install script.
5) Make sure the user account you want to use for the installer has access to create tables in that database.
6) Get a Google Maps API key from here: http://www.google.com/apis/maps/signup.html
7) Open your browser. I only tested this installer from Internet Explorer 7.0.
8) Browse to your web server and folder, specifying the install.php file. Example: http://www.yourdomain.com/trackme/install.php
9) Fill in the blanks with the database information used above, name of the folder where you uploaded all of the php files, Google Maps API key, and all of the other configuration settings.
10) Click the Complete Installation button
11) If you got errors, go back and try again, making note of the errors and fix them.
12) If you didn't get errors, delete the install.php and database.sql files
13) Now you need to grab the CAB file and install TrackMe on your Windows Mobile Professional device. You can get the latest CAB file here: http://luisespinosa.com/bin/trackme/TrackMe.CAB
14) Once you install it, start it up and click MENU -> CONFIG
15) Make sure the GPS tab is configured correctly for your GPS device
16) On the REMOTE tab put in the account name and password you want to use. It doesn't matter what you use, the account will be created for you when you start the GPS tracking.
17) The server section of the REMOTE tab should be your server that you just installed the php files on. Make sure you also specify the subfolder name with a leading /. This folder name should be the same folder where you uploaded the php files in step 1.
18) On the REALTIME TRACKING tab select the REMOTE option
19) Click OK
20) Click START. If the application is configured correctly it should try to get a fix from the GPS and start uploading position information.
21) Open your browser and point it to your server and folder like this: http://www.yourdomain.com/trackme

With any luck you should get a map with your points plotted. If not, post in the XDA Developers forum listed above and we will try to help you out.

Thanks again to Luis Espinosa for developing and maintaining the application and for taking comments and feedback to drive his development.
