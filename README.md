# TrackMeViewer

TrackMeViewer is the PHP/MySQL web viewer for TrackMe. It lets you browse recorded trips, inspect live position updates, review photos and comments, and export route data from any modern browser.

## Current status

- Viewer version: `4.0`
- Android app version in this repository: `7.0.0`
- Primary client platform: Android

## What it does

- Displays recorded and live tracking data stored by TrackMe
- Shows trip summaries, markers, photos, comments, and bearings
- Supports GPX and KML exports
- Works with multiple map and tile providers
- Can be configured for private or public access

## Requirements

- A web server capable of running PHP
- A MySQL-compatible database
- Write access to the TrackMeViewer directory during installation, or a manually prepared `config.php`

## Installation

1. Upload the contents of `TrackMeViewer` to your web server. In the examples below, the target folder is named `trackme`.
2. Create `routes` and `pics` inside that folder.
3. Ensure the web server can write to the installation directory during setup, or prepare `config.php` manually before upload.
4. Create an empty database for TrackMeViewer.
5. Confirm the database user can create tables in that database.
6. Optionally prepare API keys or access tokens for the map providers you plan to use.
7. Open `install.php` in a browser. Example: `https://yourdomain.example/trackme/install.php`
8. Enter the database settings, folder name, map provider credentials, and viewer options.
9. Complete the installation.
10. Delete `install.php` and `database.sql` after setup finishes successfully.

## Using the viewer

1. Install and configure the TrackMe Android app.
2. Point the app at your TrackMeViewer server.
3. Start recording or syncing positions from the Android app.
4. Open your TrackMeViewer URL in a browser to inspect live or recorded trips.

## Android client

- Google Play: <https://play.google.com/store/apps/details?id=LEM.TrackMe>

## Notes

- `routes` is used for exported route files.
- `pics` stores uploaded images associated with trips.
- Some tile providers require their own API key or access token.
- Folder and file names may be case-sensitive depending on your hosting environment.

## Credits

TrackMe was created by Luis Espinosa (`LEM`). TrackMeViewer in this repository is the web companion used to browse data collected by the Android app.

Useful project links:

- <http://www.luisespinosa.com>
- <http://forum.xda-developers.com/showthread.php?t=340667>
