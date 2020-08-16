<?php
	//////////////////////////////////////////////////////////////////////////////
	//
	// TrackMeViewer - Browser/MySQL/PHP based Application to display trips recorded by TrackMe App on Android
	// Version: 3.5
	// Date:    08/15/2020
	//
	// For more information go to:
	// http://forum.xda-developers.com/showthread.php?t=340667
	//
	// Please feel free to modify the files to meet your needs.
	// Post comments and questions to the forum thread above.
	//
	//////////////////////////////////////////////////////////////////////////////

// List of tile/map providers can be exteded here
// Examples can be found at https://leaflet-extras.github.io/leaflet-providers/preview/
// "url", "maxZoom" and "attribution" are the minimum requried info to be provided - all other extra will be ignored
// Add a tile provider definition into the array - double quotes in "url" or "attribution" needs to be escaped with '\'
// Optionally some tile provider (with *) require an "apikey" or an "access_token" specified. They must be set in the config.php file

$tileproviders = array(
"OpenStreetMap Mapnik"       => array(	"url" => "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
					"maxZoom" => 19,
					"attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"OpenStreetMap DE"           => array(	"url" => "https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png",
					"maxZoom" => 18,
					"attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"CyclOSM"                    => array(	"url" => "https://dev.{s}.tile.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png",
					"maxZoom" => 20,
					"attribution" => "<a href=\"https://github.com/cyclosm/cyclosm-cartocss-style/releases\" title=\"CyclOSM - Open Bicycle render\">CyclOSM</a> | Map data: &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"CartoDB"                    => array(	"url" => "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
					"maxZoom" => 19,
					"attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors &copy; <a href=\"https://carto.com/attributions\">CARTO</a>"),
"CartoDB Voyager"            => array(	"url" => "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
					"maxZoom" => 19,
					"attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors &copy; <a href=\"https://carto.com/attributions\">CARTO</a>"),
"*HERE (aka Nokia)"          => array(	"url" => "https://1.base.maps.ls.hereapi.com/maptile/2.1/maptile/newest/normal.day/{z}/{x}/{y}/256/png8?&apikey=" . $hereapikey . "&lg=eng",
					"maxZoom" => 20,
					"attribution" => "Map &copy; 1987-" . date('Y') . " <a href=\"http://developer.here.com\">HERE</a>"),
"*HERE (aka Nokia) Satellite"=> array(	"url" => "https://1.aerial.maps.ls.hereapi.com/maptile/2.1/maptile/newest/satellite.day/{z}/{x}/{y}/256/png?apikey=" . $hereapikey . "&lg=eng",
					"maxZoom" => 20,
					"attribution" => "Map &copy; 1987-" . date('Y') . " <a href=\"http://developer.here.com\">HERE</a>"),
"*HERE (aka Nokia) Hybrid"   => array(	"url" => "https://1.aerial.maps.ls.hereapi.com/maptile/2.1/maptile/newest/hybrid.day/{z}/{x}/{y}/256/png?apikey=" . $hereapikey . "&lg=eng",
					"maxZoom" => 20,
					"attribution" => "Map &copy; 1987-" . date('Y') . " <a href=\"http://developer.here.com\">HERE</a>"),
"*HERE (aka Nokia) Terrain"  => array(	"url" => "https://1.aerial.maps.ls.hereapi.com/maptile/2.1/maptile/newest/terrain.day.mobile/{z}/{x}/{y}/256/png?apikey=" . $hereapikey . "&lg=eng",
					"maxZoom" => 20,
					"attribution" => "Map &copy; 1987-" . date('Y') . " <a href=\"http://developer.here.com\">HERE</a>"),
"OpenTopoMap"                => array(	"url" => "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
					"maxZoom" => 17,
					"attribution" => "Map data: &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors, <a href=\"http://viewfinderpanoramas.org\">SRTM</a> | Map style: &copy; <a href=\"https://opentopomap.org\">OpenTopoMap</a> (<a href=\"https://creativecommons.org/licenses/by-sa/3.0/\">CC-BY-SA</a>)"),
"*MapBox Street v11"         => array(	"url" => "https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=" . $mapboxaccesstoken,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.mapbox.com/about/maps/\">Mapbox</a> &copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> <strong><a href=\"https://www.mapbox.com/map-feedback/\" target=\"_blank\">Improve this map</a></strong>"),
"*MapBox Outdoors v11"       => array(	"url" => "https://api.mapbox.com/styles/v1/mapbox/outdoors-v11/tiles/{z}/{x}/{y}?access_token=" . $mapboxaccesstoken,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.mapbox.com/about/maps/\">Mapbox</a> &copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> <strong><a href=\"https://www.mapbox.com/map-feedback/\" target=\"_blank\">Improve this map</a></strong>"),
"*MapBox Light v10"          => array(	"url" => "https://api.mapbox.com/styles/v1/mapbox/light-v10/tiles/{z}/{x}/{y}?access_token=" . $mapboxaccesstoken,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.mapbox.com/about/maps/\">Mapbox</a> &copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> <strong><a href=\"https://www.mapbox.com/map-feedback/\" target=\"_blank\">Improve this map</a></strong>"),
"*MapBox Dark v10"           => array(	"url" => "https://api.mapbox.com/styles/v1/mapbox/dark-v10/tiles/{z}/{x}/{y}?access_token=" . $mapboxaccesstoken,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.mapbox.com/about/maps/\">Mapbox</a> &copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> <strong><a href=\"https://www.mapbox.com/map-feedback/\" target=\"_blank\">Improve this map</a></strong>"),
"*MapBox Satellite v9"       => array(	"url" => "https://api.mapbox.com/styles/v1/mapbox/satellite-v9/tiles/{z}/{x}/{y}?access_token=" . $mapboxaccesstoken,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.mapbox.com/about/maps/\">Mapbox</a> &copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> <strong><a href=\"https://www.mapbox.com/map-feedback/\" target=\"_blank\">Improve this map</a></strong>"),
"*MapBox Satellite-Street v11"=> array(	"url" => "https://api.mapbox.com/styles/v1/mapbox/satellite-streets-v11/tiles/{z}/{x}/{y}?access_token=" . $mapboxaccesstoken,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.mapbox.com/about/maps/\">Mapbox</a> &copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> <strong><a href=\"https://www.mapbox.com/map-feedback/\" target=\"_blank\">Improve this map</a></strong>"),
"MtbMap"                     => array(	"url" => "http://tile.mtbmap.cz/mtbmap_tiles/{z}/{x}/{y}.png",
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors &amp; USGS"),
"*TomTom"                    => array(	"url" => "https://c.api.tomtom.com/map/1/tile/basic/main/{z}/{x}/{y}.png?key=" . $tomtomapikey,
					"maxZoom" => 22,
					"attribution" => "<a href=\"https://tomtom.com\" target=\"_blank\">&copy;  1992 - " . date('Y') . " TomTom.</a>"),
"*TomTom Hybrid"             => array(	"url" => "https://c.api.tomtom.com/map/1/tile/hybrid/main/{z}/{x}/{y}.png?key=" . $tomtomapikey,
					"maxZoom" => 22,
					"attribution" => "<a href=\"https://tomtom.com\" target=\"_blank\">&copy;  1992 - " . date('Y') . " TomTom.</a>"),
"*TomTom Labels"             => array(	"url" => "https://c.api.tomtom.com/map/1/tile/labels/main/{z}/{x}/{y}.png?key=" . $tomtomapikey,
					"maxZoom" => 22,
					"attribution" => "<a href=\"https://tomtom.com\" target=\"_blank\">&copy;  1992 - " . date('Y') . " TomTom.</a>"),
"HikeBike HikeBike"          => array(	"url" => "https://tiles.wmflabs.org/hikebike/{z}/{x}/{y}.png",
					"maxZoom" => 19,
					"attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"*Google Maps"               => array(	"url" => "https://mt1.google.com/maps/vt/lyrs=r&x={x}&y={y}&z={z}?apikey=" . $googleapikey,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.google.com/maps\"><font color=\"#4285F4\">G</font><font color=\"#DB4437\">o</font><font color=\"#F4B400\">o</font><font color=\"#4285F4\">g</font><font color=\"#0F9D58\">l</font><font color=\"#DB4437\">e</font></a>"),
"*Google Satellite"          => array(	"url" => "http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}?apikey=" . $googleapikey,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.google.com/maps\"><font color=\"#4285F4\">G</font><font color=\"#DB4437\">o</font><font color=\"#F4B400\">o</font><font color=\"#4285F4\">g</font><font color=\"#0F9D58\">l</font><font color=\"#DB4437\">e</font></a>"),
"*Google Satellite Hybrid"   => array(	"url" => "https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}?apikey=" . $googleapikey,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.google.com/maps\"><font color=\"#4285F4\">G</font><font color=\"#DB4437\">o</font><font color=\"#F4B400\">o</font><font color=\"#4285F4\">g</font><font color=\"#0F9D58\">l</font><font color=\"#DB4437\">e</font></a>"),
"*Google Terrain"            => array(	"url" => "https://mt1.google.com/vt/lyrs=p&x={x}&y={y}&z={z}?apikey=" . $googleapikey,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.google.com/maps\"><font color=\"#4285F4\">G</font><font color=\"#DB4437\">o</font><font color=\"#F4B400\">o</font><font color=\"#4285F4\">g</font><font color=\"#0F9D58\">l</font><font color=\"#DB4437\">e</font></a>"),
"*Google Roads"              => array(	"url" => "https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}?apikey=" . $googleapikey,
					"maxZoom" => 20,
					"attribution" => "&copy; <a href=\"https://www.google.com/maps\"><font color=\"#4285F4\">G</font><font color=\"#DB4437\">o</font><font color=\"#F4B400\">o</font><font color=\"#4285F4\">g</font><font color=\"#0F9D58\">l</font><font color=\"#DB4437\">e</font></a>"),
"Esri WorldImagery"          => array(	"url" => "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
					"maxZoom" => 20,
					"attribution" => "Tiles &copy; <a href=\"http://www.esri.com/\">Esri</a> &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community"),
"Esri WorldTopoMap"          => array(	"url" => "https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}",
					"maxZoom" => 20,
					"attribution" => "Tiles &copy; <a href=\"http://www.esri.com/\">Esri</a> &mdash; Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community"),
"*Thunderforest OpenCycleMap" => array(	"url" => "https://{s}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png?apikey=" . $thunderforestapikey,
					"maxZoom" => 22,
					"attribution" => "&copy; <a href=\"http://www.thunderforest.com/\">Thunderforest</a>, &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"*Thunderforest Transport"    => array(	"url" => "https://{s}.tile.thunderforest.com/transport/{z}/{x}/{y}.png?apikey=" . $thunderforestapikey,
					"maxZoom" => 22,
					"attribution" => "&copy; <a href=\"http://www.thunderforest.com/\">Thunderforest</a>, &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"*Thunderforest Landscape"    => array(	"url" => "https://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png?apikey=" . $thunderforestapikey,
					"maxZoom" => 22,
					"attribution" => "&copy; <a href=\"http://www.thunderforest.com/\">Thunderforest</a>, &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"*Thunderforest Outdoors"     => array(	"url" => "https://{s}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png?apikey=" . $thunderforestapikey,
					"maxZoom" => 22,
					"attribution" => "&copy; <a href=\"http://www.thunderforest.com/\">Thunderforest</a>, &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors")
);

?>
