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

var lang = {
	get: function(key) {
		var trans = this._get(key);
		var args = Array.prototype.slice.call(arguments, 1);
		if (args.length > 0) {
			trans = trans.replace(/({+)(\d+)(}+)/g, function(match, before, number, after) {
				if (before.length == after.length && typeof args[number] != 'undefined') {
					if (before.length % 2 == 0)
						var repl = number;
					else
						var repl = args[number];
					var count = Math.floor(before.length / 2);
					before = new Array(count).join('{');
					after = new Array(count).join('}');
					return before + repl + after;
				} else {
					return match;
				}
			});
		}
		return trans;
	},

	_get: function(key) {
		// Extra check in case the mapping is still null
		if (this._trans && key in this._trans)
			return this._trans[key];
		else if (this._fallback && key in this._fallback)
			return this._fallback[key];
		else {
			console.log("Key '" + key + "' not loaded (yet), " +
				"loaded translation: " + (this._trans !== null) + "; " +
				"loaded fallback: " + (this._fallback !== null));
			return key;
		}
	},

	setCode: function(code, fallback) {
		if (fallback === undefined)
			fallback = false;
		if (code === this._fallbackCode && this._fallback)
			this._trans = this._fallback;
		else {
			execrequest('i18n/' + code + '.json', function(contentAsJSON) {
				content = JSON.parse(contentAsJSON);
				if (fallback) {
					lang._fallbackCode = code;
					lang._fallback = content;
				} else {
					lang._trans = content;
				}
				lang._code = code;
			});
		}
	},

	_code: "",
	_queried: false,
	_trans: null,
	_fallback: null,
	_fallbackCode: false,

};

lang.setCode("en", true); // Load fallback by default
