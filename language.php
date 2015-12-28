<?php

    //////////////////////////////////////////////////////////////////////////////
    //
    // TrackMe Google Maps Display Language File
    // Version: 1.21
    // Date:    02/09/2009
    // 
    //
    // TrackMe built by Staryon
    // For more information go to:
    // http://forum.xda-developers.com/showthread.php?t=340667
    //
    // Post comments and questions to the forum thread above.
    //
    //////////////////////////////////////////////////////////////////////////////

    $version_text                            = "2.0";


    // Only for the deprecated support of not using $lang
    $mapping = array("title_text" => "title",
                     "footer_text" => "footer",
                     "incomplete_install_text" => "incomplete-install",
                     "no_data_text" => "no-data",
                     "database_fail_text" => "database-fail",
                     "trip_title" => "trip-title",
                     "trip_none_text" => "trip-none",
                     "trip_any_text" => "trip-any",
                     "trip_button_text" => "trip-select",
                     "location_button_text" => "live-tracking",
                     "location_button_text_off" => "live-tracking-off",
                     "filter_title" => "filter-title",
                     "filter_none_text" => "filter-none",
                     "filter_photo_comment_text" => "filter-photo-comment",
                     "filter_photo_text" => "filter-photo",
                     "filter_comment_text" => "filter-comment",
                     "filter_last_20" => "filter-last20",
                     "filter_daterange" => "filter-daterange",
                     "startdate_text" => "filter-startdate",
                     "enddate_text" => "filter-enddate",
                     "filter_button_text" => "filter-select",
                     "display_options_title_text" => "display-options-title",
                     "display_header_text" => "display-header",
                     "display_showbearing_text" => "display-showbearing",
                     "display_crosshair_text" => "display-crosshair",
                     "display_clickcenter_text" => "display-clickcenter",
                     "display_overview_text" => "display-overview",
                     "display_language_text" => "display-language",
                     "display_units_text" => "display-units",
                     "display_button_text" => "display-button",
                     "tripsummary_title" => "summary-title",
                     "summary_time" => "summary-time",
                     "summary_photos" => "summary-photos",
                     "summary_comments" => "summary-comments",
                     "date_title" => "date-title",
                     "tripstatus_title" => "tripstatus-title",
                     "user_button_text" => "select-user",
                     "showconfig_button_text" => "config-show",
                     "showconfig_button_text_off" => "config-hide",
                     "page_private" => "page-private",
                     "trip_data" => "trip-data",
                     "login_text" => "login-username",
                     "password_text" => "login-password",
                     "login_button_text" => "login-button",
                     "user_balloon_text" => "balloon-user",
                     "trip_balloon_text" => "balloon-trip",
                     "time_balloon_text" => "balloon-time",
                     "speed_balloon_text" => "balloon-speed",
                     "altitude_balloon_text" => "balloon-altitude",
                     "total_time_balloon_text" => "balloon-total-time",
                     "avg_speed_balloon_text" => "balloon-avg-speed",
                     "total_distance_balloon_text" => "balloon-total-distance",
                     "point_balloon_text" => "balloon-point",
                     "comment_balloon_text" => "balloon-comment",
                     "speed_imperial_unit_balloon_text" => "unit-speed-imperial",
                     "distance_imperial_unit_balloon_text" => "unit-distance-imperial",
                     "height_imperial_unit_balloon_text" => "unit-height-imperial",
                     "speed_metric_unit_balloon_text" => "unit-speed-metric",
                     "distance_metric_unit_balloon_text" => "unit-distance-metric",
                     "height_metric_unit_balloon_text" => "unit-height-metric");

    class Language implements ArrayAccess {

        private $lang = array();

        public function __construct($code) {
            $this->code = $code;
            $contents = file_get_contents("i18n/$code.json");
            $this->lang = json_decode($contents, true);
            $this->en = $this->lang["@metadata"]["en-name"];
            $this->name = $this->lang["@metadata"]["name"];
            if ($this->en && $this->name)
                $this->full_name = "$this->name ($this->en)";
            else if ($this->en)
                $this->full_name = $this->en;
            else
                $this->full_name = $this->name;
        }

        public function offsetSet($offset, $value) {
            throw new LogicException("Attempting to change the language definition.");
        }

        public function offsetUnset($offset) {
            throw new LogicException("Attempting to change the language definition.");
        }

        public function offsetExists($offset) {
            return array_key_exists($offset, $this->lang);
        }

        public function offsetGet($offset) {
            if (array_key_exists($offset, $this->lang))
                return $this->lang[$offset];
            else
            {
                global $languages;
                $en = $languages["en"];
                if ($en !== $this)
                    return $en[$offset];
                else
                    throw new LogicException("Unknown language key '$offset'");
            }
        }
    }

    if (!isset($language))
        $language = "english";

    $selected_code = "en";
    $languages = array();
    foreach(scandir("i18n") as $fn)
    {
        if (substr($fn, -5) === ".json")
        {
            $lang_code = substr($fn, 0, -5);
            $lang = new Language($lang_code);
            $languages[$lang_code] = $lang;
            if (strtolower($lang->en) === $language || $lang_code === $language)
                $selected_code = $lang_code;
        }
    }

    $lang = $languages[$selected_code];

    // Only to support using the variables directly without using $lang
    foreach($mapping as $old => $new)
    {
        $$old = $lang[$new];
    }

?>
