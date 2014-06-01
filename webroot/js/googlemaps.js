(function() {

    // =========================================================================
    // Auxiliary functions

    //--------------------------------------------------------------------------
    // check if Google Maps API is loaded
    var __isLoaded = false;
    var isApiLoaded = function() {
        return (__isLoaded || (__isLoaded = (typeof window.google !== 'undefined'
                && typeof window.google.maps !== 'undefined')));
    };

    // =========================================================================
    // Public API

    window.CakePHPGoogleMaps = {};

    /**
     * Create a new map using specified options and div ID.
     *
     * @param {string} id DOM ID of an existing <div> node.
     * @param {object} options Options passed to constructor.
     * @returns {google.maps.Map}
     */
    CakePHPGoogleMaps.create = function(id, options) {
        if (!isApiLoaded()) {
            throw new Error('Google Maps was not loaded!');
        }

        if (typeof options === 'string') {
            options = JSON.parse(options);
        }

        return new google.maps.Map(document.getElementById(id), options);
    };

})();
