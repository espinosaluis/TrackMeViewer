<?php

// List of tile/map providers can be exteded here
// Examples can be found at https://leaflet-extras.github.io/leaflet-providers/preview/
// "url", "maxZoom" and "attribution" are the minimum requried info to be provided - all other extra will be ignored
// Add a tile provider definition into the array - double quotes in "url" or "attributuion" needs to be escaped with '\'
// Optionally some tile provider require an "apikey" or an "access_token" specified. They must be set in the config.php file

$tileproviders = array(
"Google Maps Standard"       => array(    "url" => "https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}",
                                          "maxZoom" => 21,
                                          "attribution" => "&copy; <a href=\"https://www.google.com/maps\">Google Maps</a>"),
"Google Maps Hybrid"         => array(    "url" => "https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}",
                                          "maxZoom" => 21,
                                          "attribution" => "&copy; <a href=\"https://www.google.com/maps\">Google Maps</a>"),
"Google Maps Satellite"      => array(    "url" => "https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}",
                                          "maxZoom" => 21,
                                          "attribution" => "&copy; <a href=\"https://www.google.com/maps\">Google Maps</a>"),
"Google Maps Terrain"        => array(    "url" => "https://mt1.google.com/vt/lyrs=p&x={x}&y={y}&z={z}",
                                          "maxZoom" => 21,
                                          "attribution" => "&copy; <a href=\"https://www.google.com/maps\">Google Maps</a>"),
"Wikimedia Maps"             => array(    "url" => "https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png",
                                          "maxZoom" => 18,
                                          "attribution" => "&copy; <a href=\"https://wikimediafoundation.org/wiki/Maps_Terms_of_Use\">Wikimedia</a> &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"OSM (OpenStreetMap)"        => array(    "url" => "https://a.tile.openstreetmap.de/{z}/{x}/{y}.png",
                                          "maxZoom" => 19,
                                          "attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"OpenTopoMap"                => array(    "url" => "https://a.tile.opentopomap.org/{z}/{x}/{y}.png",
                                          "maxZoom" => 17,
                                          "attribution" => "Map data: &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors | Map style: &copy; <a href=\"https://opentopomap.org\">OpenTopoMap</a>"),
"ESRI WorldTopoMap"          => array(    "url" => "https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}",
                                          "maxZoom" => 19,
                                          "attribution" => "Tiles &copy; <a href=\"https://www.esri.com/\">Esri</a>"),
"ESRI WorldImagery"          => array(    "url" => "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
                                          "maxZoom" => 19,
                                          "attribution" => "Tiles &copy; <a href=\"https://www.esri.com/\">Esri</a>"),
"MtbMap (Mountain Bike Maps)"=> array(    "url" => "http://tile.mtbmap.cz/mtbmap_tiles/{z}/{x}/{y}.png",
                                          "maxZoom" => 19,
                                          "attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"wmflabs Hike & Bike"        => array(    "url" => "https://tiles.wmflabs.org/hikebike/{z}/{x}/{y}.png",
                                          "maxZoom" => 20,
                                          "attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"CartoDB"                    => array(    "url" => "https://1.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png",
                                          "maxZoom" => 21,
                                          "attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors &copy; <a href=\"https://carto.com/attributions\">CARTO</a>"),
"CartoDB Voyager"            => array(    "url" => "https://1.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png",
                                          "maxZoom" => 21,
                                          "attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors &copy; <a href=\"https://carto.com/attributions\">CARTO</a>"),
"OPNVKarte (Transport Map)"  => array(    "url" => "http://tile.memomaps.de/tilegen/{z}/{x}/{y}.png",
                                          "maxZoom" => 18,
                                          "attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"waymarkedtrails (Hiking)"   => array(    "url" => "https://tile.waymarkedtrails.org/hiking/{z}/{x}/{y}.png",
                                          "maxZoom" => 18,
                                          "attribution" => "&copy; <a href=\"https://waymarkedtrails.org/\">waymarkedtrails.org</a> &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"waymarkedtrails (Cycling)"  => array(    "url" => "https://tile.waymarkedtrails.org/cycling/{z}/{x}/{y}.png",
                                          "maxZoom" => 18,
                                          "attribution" => "&copy; <a href=\"https://waymarkedtrails.org/\">waymarkedtrails.org</a> &copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"),
"OpenPtMap"                  => array(    "url" => "http://www.openptmap.org/tiles/{z}/{x}/{y}.png",
                                          "maxZoom" => 17,
                                          "attribution" => "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors")
);

?>
