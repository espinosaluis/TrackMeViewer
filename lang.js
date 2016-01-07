var lang = {

    get: function(key) {
        // Extra check in case the mapping is still null
        if (this._trans && key in this._trans)
            return this._trans[key];
        else if (this._fallback && key in this._fallback)
            return this._fallback[key];
        else {
            console.log("Key '" + key + "' not loaded (yet), " +
                        "loaded translation: " + (this._trans !== null) +
                        "; loaded fallback " + (this._fallback !== null));
            return key;
        }
    },

    setCode: function(code, fallback) {
        if (fallback === undefined)
            fallback = false;
        if (code === this._fallbackCode && this._fallback)
            this._trans = this._fallback;
        else {
            query('i18n/' + code + '.json', function(content) {
                content = JSON.parse(content);
                if (fallback) {
                    lang._fallbackCode = code;
                    lang._fallback = content;
                }
                else
                    lang._trans = content;
            });
        }
    },

    _queried: false,
    _trans: null,
    _fallback: null,
    _fallbackCode: false,

};

// Load fallback by default
lang.setCode("en", true);
