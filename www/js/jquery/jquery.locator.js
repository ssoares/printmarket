/*
 * ©2014 Lucas Drapeau
 * 
 */


(function($) {
//plugin name
    var pluginName = "locator";
    //default plugins settings here
    var defaultSettings = {
        mapId: 'custom',
        storeList: {},
        circleRadius: 550,
        responsive: true,
        defaultLatitude: 45.3947687,
        defaultLongitude: -71.95111279999998,
        defaultDistance: 50,
        centerOffset: [0, 0],
        ajaxRequests: false,
        mapOptions: {
            mapTypeControl: false,
            panControlOptions: {position: google.maps.ControlPosition.RIGHT_CENTER},
            streetViewControl: false,
            zoomControlOptions: {position: google.maps.ControlPosition.RIGHT_CENTER},
            scrollwheel: false,
        },
        bubbleStyle: {
            shadowStyle: 1,
            padding: 24,
            backgroundColor: 'rgb(255,255,255)',
            borderRadius: 8,
            arrowSize: 10,
            borderWidth: 1,
            borderColor: '#535353',
            disableAutoPan: true,
            hideCloseButton: false,
            arrowPosition: -30,
            backgroundClassName: 'bubble',
            arrowStyle: 3,
            minHeight: 160,
            minWidth: 250,
            maxWidth: 10000,
            maxHeight: 10000,
            closeImg: 'close.gif'
        },
        circleStyle: {
            fillColor: '#265383',
            fillOpacity: 0.4,
            strokeColor: '#010202',
            strokeOpacity: 0,
            strokeWeight: 0
        },
        resultTagsToCheckFromGeoLoc: [
            {'geoloc': 'street_number', 'us': 'street-number'},
            {'geoloc': 'route', 'us': 'route'},
            {'geoloc': 'sublocality_level_1', 'us': 'subcity'},
            {'geoloc': 'locality', 'us': 'city'},
            {'geoloc': 'postal_code', 'us': 'zip-code'},
            {'geoloc': 'administrative_area_level_2', 'us': 'region'},
            {'geoloc': 'administrative_area_level_1', 'us': 'state'},
            {'geoloc': 'country', 'us': 'country'}
        ],
        mandatoryTags: ['state', 'country'],
        colorList: {
            plain: [{stylers: []}],
            papier: [{featureType: "all", stylers: [{saturation: -72}, {hue: "#007fff"}, {weight: 0.5}, {lightness: 27}, {gamma: 0.52}]}],
            plancher: [{featureType: "all", stylers: [{saturation: 60}, {hue: "#009cda"}, {lightness: -30}, {gamma: 3}]}]
        },
        theme: 'plain'
    };
    //static constants
    //pi to radian constant
    var PI_TO_RADIAN = Math.PI / 180;
    //static functions 
    //get the closest point in an array of LatLng to an initial LatLng
    var getClosestPoint = function(resultList, aini, index) {
        if (resultList == null)
            throw new Error('No result to be parsed');
        var closestIt = 0;
        for (var i in resultList) {
            var result = resultList[i];
            if (i > 0)
            {
                if (calculateDistance(result[index].lat(), result[index].lng(), aini.lat(), aini.lng()) <
                        calculateDistance(resultList[closestIt][index].lat(), resultList[closestIt][index].lng(), aini.lat(), aini.lng())) {
                    closestIt = i;
                }
            }
        }
        return resultList[closestIt][index];
    };
    var getBestTags = function(resultList) {
        var tagsReturn = {};
        var tagsCatalog = {};
        for (var i in resultList) {
            var result = resultList[i];
            for (var j in result) {
                var tag = j;
                var value = result[j];
                if (!tagsCatalog[tag])
                    tagsCatalog[tag] = {};
                if (!tagsCatalog[tag][value]) {
                    tagsCatalog[tag][value] = 0;
                }
                tagsCatalog[tag][value]++;
            }
        }
        for (var i in tagsCatalog) {
            var tagValueList = tagsCatalog[i];
            var valueToReturn = '';
            var valueCount = 0;
            for (var j in tagValueList) {
                if (tagValueList[j] > valueCount) {
                    valueToReturn = j;
                    valueCount = tagValueList[j];
                }
            }
            tagsReturn[i] = valueToReturn;
        }
        return tagsReturn;
    };
    var getCorrectZoom = function(adistance, apixelr) {
        var z = Math.floor(Math.log(64 * apixelr / adistance) / Math.log(2));
        return(z);
    };
    //get distance between two coordinates
    var calculateDistance = function(alat1, alng1, alat2, alng2) {
        var R = 6371; // km
        var dLat = (alat2 - alat1) * PI_TO_RADIAN;
        var dLon = (alng2 - alng1) * PI_TO_RADIAN;
        var lat1 = alat1 * PI_TO_RADIAN;
        var lat2 = alat2 * PI_TO_RADIAN;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c;
        return Math.abs(d);
    };
//private properties and methods    
    var members = {
        _$this: null,
        _gmap: null,
        _geocode: null,
        _gmapOptions: null,
        _storeList: [],
        _circle: null,
        _mapStyle: null,
        _mapOptions: null,
        _hasGeoloc: false,
        _querySent: false,
        _displayRadius: 0,
        _centerOffset: [0, 0],
        _circleRadius: 0,
        _currentPointers: [],
        _currentPopup: null,
        _currentDistance: 0,
        _currentTags: [],
        _currentCenter: null,
        _bubbleXPos: 0,
        _bubbleYPos: 0,
        _geolocatorId: null,
        _dispatchEvent: function(event, data) {
            this._$this.trigger(pluginName + '.' + event, data);
        },
        _call: function(name, args) {
            this._$this[pluginName](name, args);
        },
        //show the closest stores on the map
        _showClosestStores: function(ini, distance, tags) {
            var that = this;
            //destroy the current stores
            for (var i in this._currentPointers) {
                this._currentPointers[i].setMap(null);
            }
            var closeStoreList = [];
            //get the stores within the distance
            closeStoreList = this._filterStoresList(distance, ini, tags);
            //if there's no store, call the callback
            if (closeStoreList.length == 0) {
                this._dispatchEvent('no-store');
            }
            else {
//show the new stores
                for (var i in closeStoreList) {
                    var markIcon = {url: closeStoreList[i].icon};
                    var markOptions = {
                        map: this._gmap,
                        position: new google.maps.LatLng(closeStoreList[i].lat, closeStoreList[i].lng),
                        icon: markIcon
                    };
                    var mark = new google.maps.Marker(markOptions);
                    mark.customPopup = closeStoreList[i].popup;
                    mark.addListener('click', function(e) {
                        e.stop();
                        if (that._currentPopup)
                            that._currentPopup.close();
                        that._currentPopup = new InfoBubble($.extend({}, {
                            content: this.customPopup,
                            position: that._call('getPosition')
                        }, that._settings.bubbleStyle));
                        that._currentPopup.open(that._gmap, this);
                        if (that._settings.responsive)
                            that._onResizeAuto();
                    });
                    this._currentPointers.push(mark);
                    this._dispatchEvent('parseStore', closeStoreList[i]);
                }
                this._dispatchEvent('success');
            }
        },
        _confirmGeoLoc: function(callback) {
            var that = this;
            if (navigator.geolocation) {
                this._querySent = false;
                var success = function(pos) {
                    navigator.geolocation.clearWatch(this._geolocatorId);
                    if (pos.coords.longitude) {
                        if (!that._querySent) {
                            that._mapOptions.center = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
                            that._call('recenterMap');
                            callback({center: that._mapOptions.center});
                            that._querySent = true;
                        }
                    }
                };
                var error = function(error) {
                    navigator.geolocation.clearWatch(this._geolocatorId);
                    switch (error.code)
                    {
                        case(1) :
                            that._dispatchEvent('desactivated-geoloc');
                            break;
                        case(2) :
                            that._dispatchEvent('error-geoloc');
                            break;
                        case(3) :
                            if (that._mapOptions.center) //the user probably did not move, so we must send back the previous position
                                callback({center: that._mapOptions.center});
                            else
                                that._dispatchEvent('error-geoloc');
                            break;
                        default:
                            break;
                    }
                };
                this._geolocatorId = navigator.geolocation.getCurrentPosition(success, error, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
                );
            }
            else {
                that._dispatchEvent('no-geoloc');
            }
        },
        _moveCoordByPixels: function(acoord, apixel) {
            var zoom = this._gmap.getZoom();
            var newLat = (apixel[1] * 180) / (256 * Math.pow(2, zoom));
            var newLng = (apixel[0] * 360) / (256 * Math.pow(2, zoom));
            return new google.maps.LatLng(acoord.lat() - newLat, acoord.lng() - newLng);
        },
        //get the stores list depending on distance
        _filterStoresList: function(distance, ini, tags) {
            var newStoreList = [];
            for (var i in this._storeList.stores) {
                if (calculateDistance(this._storeList.stores[i].lat, this._storeList.stores[i].lng, ini.lat(), ini.lng()) < distance) { //distance
                    var storeIt = true;
                    var storeTags = this._storeList.stores[i].tags;
                    if (storeTags) {
                        for (var j in this._settings.mandatoryTags) {
                            var tagToVerify = this._settings.mandatoryTags[j];
                            if (tags[tagToVerify] === '')//verifies empty fields
                                break;
                            if (typeof (storeTags[tagToVerify]) === 'undefined') {
                                storeIt = false;
                                break;
                            }
                            //object detection not tested
                            else if (typeof (storeTags[tagToVerify]) === 'object') {
                                var hasFound = false;
                                for (var k in storeTags[tagToVerify]) {
                                    if (tags[tagToVerify] == storeTags[tagToVerify][k]) {
                                        hasFound = true;
                                        break;
                                    }
                                }
                                if (!hasFound) {
                                    storeIt = false;
                                    break;
                                }
                            }
                            else {
                                if (tags[tagToVerify] != storeTags[tagToVerify]) {
                                    storeIt = false;
                                    break;
                                }
                            }
                        }
                    }
                    if (storeIt) {
                        newStoreList.push(this._storeList.stores[i]);
                    }

                }
            }
            return newStoreList;
        },
        _showCircleOverlay: function(adistance, apos) {
            if (this._circle)
                this._circle.setMap(null);
            var circleOptions = $.extend({}, {
                center: apos,
                map: this._gmap,
                radius: adistance * 1000
            }, this._settings.circleStyle);
            this._circle = new google.maps.Circle(circleOptions)
        },
        _getResultsFromAddress: function(address, callback) {
            var that = this;
            this._geocoder.geocode({address: address, location: this._settings.center}, function(result, status) {
                that._filterResultsFromGeoLoc(result, status, callback, address)
            });
        },
        _getResultsFromLatLng: function(latlng, callback) {
            var that = this;
            this._geocoder.geocode({latLng: latlng}, function(result, status) {
                that._filterResultsFromGeoLoc(result, status, callback, latlng)
            });
        },
        _filterResultsFromGeoLoc: function(resultList, status, callback, original) {
            switch (status) {
                case(google.maps.GeocoderStatus.OK):
                    var dataToSend = [];
                    for (var i in resultList) {
                        var result = resultList[i];
                        var resultToSend = {};
                        resultToSend.coords = result.geometry.location;
                        //check for tags
                        for (var j in result.address_components) {
                            var comp = result.address_components[j];
                            for (var k in this._settings.resultTagsToCheckFromGeoLoc) {
                                if (comp.types[0] == this._settings.resultTagsToCheckFromGeoLoc[k].geoloc) {
                                    resultToSend[this._settings.resultTagsToCheckFromGeoLoc[k].us] = comp.short_name;
                                    break;
                                }
                            }
                        }
                        dataToSend.push(resultToSend);
                    }
                    //verify if there's any result
                    if (dataToSend.length == 0) {
                        var event = {address: original};
                        this._dispatchEvent('search-error', event);
                    }
                    else {
                        callback(dataToSend);
                    }
                    break;
                default:
                    break;
            }
        },
        _applyResults: function(resultList, tags) {
            this._currentCenter = getClosestPoint(resultList, this._mapOptions.center, 'coords');
            //compress tags data into a convenient array
            var absoluteTags = getBestTags(resultList);
            delete absoluteTags['coords'];
            this._currentTags = $.extend({}, absoluteTags, tags);
            this._call('recenterMap');
            this._showCircleOverlay(this._currentDistance, this._currentCenter);
            if (!this._settings.ajaxRequests)
                this._showClosestStores(this._currentCenter, this._currentDistance, this._currentTags);
            var event = {
                currentCenter: this._currentCenter,
                currentDistance: this._currentDistance,
                currentTags: this._currentTags
            }
            this._dispatchEvent('load-stores', event);
        },
        _initMap: function() {
            var that = this;

            this._geocoder = new google.maps.Geocoder();
            this._currentCenter = new google.maps.LatLng(this._settings.defaultLatitude, this._settings.defaultLongitude);
            this._mapOptions = {
                center: this._currentCenter,
                zoom: 10,
                mapTypeId: this._settings.mapId
            };
            this._gmap = new google.maps.Map(this._$this[0], $.extend({}, this._mapOptions, this._settings.mapOptions));
            this._call('recenterMap');
            this._mapStyle = new google.maps.StyledMapType(this._settings.colorList[this._settings.theme], {name: this._settings.mapId});
            this._gmap.mapTypes.set(this._settings.mapId, this._mapStyle);
            //centers the map depending if the user provides geolocation infos
            //confirmGeoLoc(false);
            google.maps.event.addDomListener(this._gmap, 'idle', function() {
                that._dispatchEvent('map-init');
                google.maps.event.clearListeners(that._gmap, 'idle');

                //events 
                if (that._settings.responsive) {
                    $(window).on('resize', function() {
                        that._onResizeAuto();
                    }).trigger('resize');
                }
            });
        },
        _onResizeAuto: function() {
            var w = this._$this.width() / 1.3;
            if (w < this._circleRadius) {
                this._displayRadius = w;
                if (this._currentPopup) {
                    this._currentPopup.setArrowPosition(50);
                }
            }
            if (this._currentPopup) {
                this._gmap.setCenter(this._currentPopup.getPosition());
            }
            else {
                this._call('recenterMap');
            }
        },
        //constructor, sort of
        init: function(obj) {
            this._$this = $(obj);
            this._storeList = this._settings.storeList;

            //init some variables
            this._centerOffset = this._settings.centerOffset;
            this._circleRadius = this._settings.circleRadius;
            this._currentDistance = this._settings.defaultDistance;
            this._displayRadius = this._circleRadius;
            //starts the map
            if (this._$this.length > 0) {
                this._initMap();
            }
            else
                throw new Error('HTML element must exist');


        }
    }
//public methods here
    members.methods = {
        //find the closest stores and show them on the map
        findStores: function(obj, args) { //address, distance, tags
            obj._currentDistance = args.distance;
            if (args.address == '') {
                obj._dispatchEvent('no-address');
            }
            else {
                obj._getResultsFromAddress(args.address, function(resultList) {
                    obj._applyResults(resultList, args.tags);
                });
            }
        },
        //find stores by geo localisation
        findStoresByGeoLoc: function(obj, args) {//distance, tags            
            obj._currentDistance = args.distance;
            obj._confirmGeoLoc(function(data) {
                var pos = data.center;
                obj._getResultsFromLatLng(pos, function(resultList) {
                    obj._applyResults(resultList, args.tags);
                });

            });
        },
        //sets the offset of the center in pixels
        //the parameter needs an array of 2 number : x and y
        setCenterOffset: function(obj, args) {
            obj._centerOffset = args.offset;
        },
        setCircleRadius: function(obj, args) {
            obj._circleRadius = Math.abs(args.pixelr);
        },
        recenterMap: function(obj, args) {
            obj._gmap.setCenter(obj._moveCoordByPixels(obj._currentCenter, obj._centerOffset));
            obj._gmap.setZoom(getCorrectZoom(obj._currentDistance, obj._displayRadius));
        },
        refreshMap: function(obj, args) {
            obj._showClosestStores(obj._currentCenter, obj._currentDistance, obj._currentTags);
        },
        getCurrentDistance: function(obj, args) {
            return obj._currentDistance;
        },
        getTag: function(obj, args) {//name
            return obj._currentTags[args.name];
        },
        setStoreList: function(obj, args) {
            obj._storeList = args.storeList;
        }

    };
    //everything below should be left as it is
    $.fn[pluginName] = function(options, args) {
        if (typeof options === "string") {
            var valToRet = null;
            var ret = this.each(function() {
                var data = $(this).data('data' + pluginName);
                if (typeof data === 'object') {
                    if (data.methods[options]) {
                        valToRet = data.methods[options](data, args);
                    }
                }
            });
            return valToRet || ret;
        }
        else {
            return this.each(function() {
                var settings = $.extend({}, defaultSettings, options);
                if (typeof $(this).data('data' + pluginName) === 'undefined') {
                    var dataToSave = Object.create(members);
                    dataToSave._settings = Object.create(settings);
                    $(this).data('data' + pluginName, dataToSave);
                    dataToSave.init(this);
                }
            });
        }
    };
}(jQuery));
if (!Object.create) {
    Object.create = (function() {
        function F() {
        }

        return function(o) {
            if (arguments.length != 1) {
                throw new Error('Object.create implementation only accepts one parameter.');
            }
            F.prototype = o;
            return new F()
        }
    })()
}