/**
 * @class CakePHPGoogleMaps
 * @constructor
 * @property {google.maps.Map} map Reference to map object created
 * @property {object} items Set of itens that exists on the map.
 */
window.CakePHPGoogleMaps = function(id, options) {
    this.map = new google.maps.Map(document.getElementById(id), options);
    this.items = {};
};

/**
 * Add a item on the map.
 *
 * This method can create many type of objects that exists in Google Maps API. Anything in `google.maps` that is a
 * constructor and accepts one object of options as parameter can be used. This includes Marker, InfoWindow, Polygon,
 * Circle, ...
 *
 * @param {string} type Type of item to be created and added to the map ({@see CakePHPGoogleMaps.itemTypes}).
 * @param {string} id Identifier of the created item. Will be saved and can be used to operate over it later.
 * @param {object} options Object of options to be passed to
 * @returns {boolean} If item was created and added to the map or not.
 */
CakePHPGoogleMaps.prototype.add = function(type, id, options) {
    if (typeof google.maps[type] !== 'function') {
        return false;
    }

    var self = this,
        items = self.items;

    options.map = this.map;
    items[type] = items[type] || {};

    try {
        items[type][id] = new google.maps[type](options);
    }
    catch (e) {
        return false;
    }

    return true;
};

/**
 * Removes a item previously created with method `add`.
 *
 * @param {string} type Item type.
 * @param {string} id Item identifier.
 * @returns {boolean} If it was removed or not.
 */
CakePHPGoogleMaps.prototype.remove = function(type, id) {
    var items = this.items;

    if (typeof items[type] === 'object') {
        if (typeof items[type][id].setVisible === 'function') {
            items[type][id].setVisible(false);
        }
        if (typeof items[type][id].setMap === 'function') {
            items[type][id].setMap(null);
        }
        delete items[type][id];
        return true;
    }

    return false;
};