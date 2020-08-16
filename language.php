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

	// Only for the deprecated support of not using $lang
	$mapping = array("footer_text" => "footer",
			"incomplete_install_text" => "incomplete-install",
			"no_data_text" => "no-data",
			"database_fail_text" => "database-fail",
			"any_trip_question_text" => "any-trip-question",
			"yes_for_all_checkmark_text" => "yes-for-all-checkmark",
			"mod_trip_warning_text" => "mod-trip-warning",
			"any_trip_warning_text" => "any-trip-warning",
			"select_trip_information_text" => "select-trip-information",
			"delete_trip_question_text" => "delete-trip-question",
			"delete_trip_button_text" => "delete-trip-button",
			"delete_trip_information_text" => "delete-trip-information",
			"rename_trip_question_text" => "rename-trip-question",
			"rename_trip_button_text" => "rename-trip-button",
			"rename_trip_information_text" => "rename-trip-information",
			"delete_trip_comments_question_text" => "delete-trip-comments-question",
			"delete_trip_comments_button_text" => "delete-trip-comments-button",
			"delete_trip_comments_information_text" => "delete-trip-comments-information",
			"change_trip_comments_question_text" => "change-trip-comments-question",
			"change_trip_comments_button_text" => "change-trip-comments-button",
			"change_trip_comments_information_text" => "change-trip-comments-information",
			"delete_waypoint_question_text" => "delete-waypoint-question",
			"delete_waypoint_button_text" => "delete-waypoint-button",
			"delete_waypoint_information_text" => "delete-waypoint-information",
			"delete_waypoint_comments_question_text" => "delete-waypoint-comments-question",
			"delete_waypoint_comments_button_text" => "delete-waypoint-comments-button",
			"delete_waypoint_comments_information_text" => "delete-waypoint-comments-information",
			"change_waypoint_comments_question_text" => "change-waypoint-comments-question",
			"change_waypoint_comments_button_text" => "change-waypoint-comments-button",
			"change_waypoint_comments_information_text" => "change-waypoint-comments-information",
			"delete_waypoint_photo_question_text" => "delete-waypoint-photo-question",
			"delete_waypoint_photo_button_text" => "delete-waypoint-photo-button",
			"delete_waypoint_photo_information_text" => "delete-waypoint-photo-information",
			"change_waypoint_photo_question_text" => "change-waypoint-photo-question",
			"change_waypoint_photo_button_text" => "change-waypoint-photo-button",
			"change_waypoint_photo_information_text" => "change-waypoint-photo-information",
			"trip_title" => "trip-title",
			"trip_group" => "trip-group",
			"trip_name" => "trip-name",
			"trip_any_text" => "trip-any",
			"no_tripgroup_text" => "no-tripgroup",
			"livetracking_text" => "livetracking",
			"chartdisplay_text" => "chartdisplay",
			"attributedisplay_text" => "attributedisplay",
			"filter_title" => "filter-title",
			"filter_none_text" => "filter-none",
			"filter_photo_text" => "filter-photo",
			"filter_comment_text" => "filter-comment",
			"filter_photo_comment_text" => "filter-photo-comment",
			"filter_last20_text" => "filter-last20",
			"filter_startdate_text" => "filter-startdate",
			"filter_enddate_text" => "filter-enddate",
			"options_title" => "options-title",
			"options_linecolor_text" => "options-linecolor",
			"options_showbearing_text" => "options-showbearing",
			"options_markertype_text" => "options-markertype",
			"options_crosshair_text" => "options-crosshair",
			"options_clickcenter_text" => "options-clickcenter",
			"options_language_text" => "options-language",
			"options_units_text" => "options-units",
			"options_tileprovider_text" => "options-tileprovider",
			"options_tilePT_text" => "options-tilePT",
			"summary_trip_info_title" => "summary-trip-info",
			"summary_trip_comments_text" => "summary-trip-comments",
			"summary_start_date_time_text" => "summary-start-date-time",
			"summary_end_date_time_text" => "summary-end-date-time",
			"summary_total_time_text" => "summary-total-time",
			"summary_move_time_text" => "summary-move-time",
			"summary_max_text" => "summary-max",
			"summary_min_text" => "summary-min",
			"summary_avg_text" => "summary-avg",
			"summary_start_text" => "summary-start",
			"summary_end_text" => "summary-end",
			"summary_diff_text" => "summary-diff",
			"summary_total_text" => "summary-total",
			"summary_speed_text" => "summary-speed",
			"summary_pace_text" => "summary-pace",
			"summary_alt_text" => "summary-alt",
			"summary_asc_text" => "summary-asc",
			"summary_desc_text" => "summary-desc",
			"summary_photos_text" => "summary-photos",
			"summary_waypoint_comments_text" => "summary-waypoint-comments",
			"summary_points_text" => "summary-points",
			"reloadoptions_title" => "reloadoptions-title",
			"reloadoptions_interval_text" => "reloadoptions-interval",
			"reloadoptions_reloadin_text" => "reloadoptions-reloadin",
			"reloadoptions_sec_text" => "reloadoptions-sec",
			"select_user_text" => "select-user",
			"start_timer_text" => "start-timer",
			"stop_timer_text" => "stop-timer",
			"zoomlevel_title" => "zoomlevel-title",
			"zoomlevel_select_text" => "zoomlevel-select",
			"zoomlevel_world_text" => "zoomlevel-world",
			"zoomlevel_continent_text" => "zoomlevel-continent",
			"zoomlevel_country_text" => "zoomlevel-country",
			"zoomlevel_area_text" => "zoomlevel-area",
			"zoomlevel_city_text" => "zoomlevel-city",
			"zoomlevel_village_text" => "zoomlevel-village",
			"zoomlevel_road_text" => "zoomlevel-road",
			"zoomlevel_block_text" => "zoomlevel-block",
			"zoomlevel_house_text" => "zoomlevel-house",
			"downloadtrip_title" => "downloadtrip-title",
			"page_private_text" => "page-private",
			"trip_data_text" => "trip-data",
			"login_username_text" => "login-username",
			"login_password_text" => "login-password",
			"login_button_text" => "login-button",
			"logout_button_text" => "logout-button",
			"balloon_user_text" => "balloon-user",
			"balloon_trip_text" => "balloon-trip",
			"balloon_time_text" => "balloon-time",
			"balloon_speed_text" => "balloon-speed",
			"balloon_altitude_text" => "balloon-altitude",
			"balloon_pitch_text" => "balloon-pitch",
			"balloon_distance_text" => "balloon-distance",
			"balloon_latitude_text" => "balloon-latitude",
			"balloon_longitude_text" => "balloon-longitude",
			"balloon_total_time_text" => "balloon-total-time",
			"balloon_avg_speed_text" => "balloon-avg-speed",
			"balloon_total_distance_text" => "balloon-total-distance",
			"balloon_point_text" => "balloon-point",
			"balloon_comment_text" => "balloon-comment",
			"balloon_unit_speed_imperial_text" => "balloon-unit-speed-imperial",
			"balloon_unit_pace_imperial_text" => "balloon-unit-pace-imperial",
			"balloon_unit_distance_imperial_text" => "balloon-unit-distance-imperial",
			"balloon_unit_altitude_imperial_text" => "balloon-unit-altitude-imperial",
			"balloon_unit_speed_metric_text" => "balloon-unit-speed-metric",
			"balloon_unit_pace_metric_text" => "balloon-unit-pace-metric",
			"balloon_unit_distance_metric_text" => "balloon-unit-distance-metric",
			"balloon_unit_altitude_metric_text" => "balloon-unit-altitude-metric");

	class Language implements ArrayAccess {

		private $lang = array();

		public function __construct($code) {
			$this->code = $code;
			$contents = file_get_contents("i18n/$code.json");
			$this->lang = json_decode($contents, true);
			$this->en = $this->lang["@metadata"]["en-name"];
			$this->name = $this->lang["@metadata"]["name"];
			if ($this->en && $this->name) {
				$this->full_name = "$this->name ($this->en)";
			} elseif ($this->en) {
				$this->full_name = $this->en;
			} else {
				$this->full_name = $this->name;
			}
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
			if (array_key_exists($offset, $this->lang)) {
				return $this->lang[$offset];
			} else {
				global $languages;
				$en = $languages["en"];
				if ($en !== $this) {
					return $en[$offset];
				} else {
					throw new LogicException("Unknown language key '$offset'");
				}
			}
		}
	}

	if (!isset($language))
		$language = "english";

	$selected_code = "en";
	$languages = array();
	foreach (scandir("i18n") as $fn) {
		if (substr($fn, -5) === ".json") {
			$lang_code = substr($fn, 0, -5);
			$lang = new Language($lang_code);
			$languages[$lang_code] = $lang;
			if (strtolower($lang->en) === $language || $lang_code === $language) {
				$selected_code = $lang_code;
			}
		}
	}

	$lang = $languages[$selected_code];

	// Only to support using the variables directly without using $lang
	foreach ($mapping as $old => $new) {
		$$old = $lang[$new];
	}
?>
