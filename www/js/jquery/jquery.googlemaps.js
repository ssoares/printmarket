/*
 * Lucas Drapeau
 */


(function($) {
    //plugin name
    var pluginName = "cibleGoogleMaps";
    //default plugins settings here
    var defaultSettings = {
        latitude: 45.394555,
        longitude: -71.951963,
        zoom: 16,
        name: "carte",
        color: 'plain',
        icon: 'image/map-icon.png',
        scrollwheel: false,
        disableDefaultUI: false,
        colorList: {
            plain: [{stylers: []}],
            blue: [{stylers: [{hue: "#009cda"}, {saturation: 60}, {lightness: -30}, {gamma: 3}]}]
        }
    };
    var PI_TO_RADIAN = Math.PI / 180;
    var getAverage = function(array) {
        var total = 0;
        for (var i in array) {
            total += array[i];
        }
        return total / array.length;
    };
    var parseIntArray = function(array) {
        for (var i in array) {
            array[i] = parseFloat(array[i]);
        }
        return array;
    };
    var getCorrectZoom = function(adistance, apixelr) {
        return Math.floor(Math.log(64 * apixelr / adistance) / Math.log(2));
    };
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
        settings: '',
        $this: null,
        $map: null,
        gmap: null,
        center: null,
        mapOptions: null,
        arrayLatitude: [],
        arrayLongitude: [],
        averageLatitude: 0,
        averageLongitude: 0,
        minLatitude: 0,
        minLongitude: 0,
        maxLatitude: 0,
        maxLongitude: 0,
        maxDistance: 0,
        googleMapInit: function() {
            //set the events here
            var that = this;
            $(window).on('resize', function() {
                that.$this[pluginName]('onResize');
            });
            $(window).trigger('resize');
        },
        checkMaxDistance: function() {
            this.minLatitude = this.maxLatitude = this.averageLatitude;
            this.minLongitude = this.maxLongitude = this.averageLongitude;
            for (var i in this.arrayLatitude) {
                var lat = this.arrayLatitude[i];
                var lng = this.arrayLongitude[i];

                if (this.minLatitude > lat)
                    this.minLatitude = lat;
                if (this.maxLatitude < lat)
                    this.maxLatitude = lat;
                if (this.minLongitude > lng)
                    this.minLongitude = lng;
                if (this.maxLongitude < lng)
                    this.maxLongitude = lng;
            }
            this.maxDistance = calculateDistance(this.minLatitude, this.minLongitude, this.maxLatitude, this.maxLongitude);

        },
        //constructor, sort of
        init: function(obj) {
            this.$this = $(obj);
            var that = this;
            if (typeof google === 'undefined') {
                throw new Error('Google Maps Api not found');
                return;
            }
            if (this.$this.attr('data-latitude')) {
                this.arrayLatitude = parseIntArray(this.$this.attr('data-latitude').split(","));
                this.averageLatitude = getAverage(this.arrayLatitude);
            }
            else
                this.arrayLatitude[0] = this.averageLatitude = this.settings.latitude;

            if (this.$this.attr('data-longitude')) {
                this.arrayLongitude = parseIntArray(this.$this.attr('data-longitude').split(","));
                this.averageLongitude = getAverage(this.arrayLongitude);
            }
            else
                this.arrayLongitude[0] = this.averageLongitude = this.settings.longitude;

            if (this.$this.attr('data-zoom'))
                this.settings.zoom = parseFloat(this.$this.attr('data-zoom'));
            else {
                //this.calculateZoom();               
            }
            if (this.$this.attr('data-color') !== "")
                this.settings.color = this.$this.attr('data-color');

            // Create a new StyledMapType object, passing it the array of styles,
            // as well as the name to be displayed on the map type control.
            var styledMap = new google.maps.StyledMapType(
                    this.settings.colorList[this.settings.color],
                    {name: this.settings.name}
            );

            this.center = new google.maps.LatLng(this.averageLatitude, this.averageLongitude);

            //calculate distance between extremities
            this.checkMaxDistance();

            // Create a map object, and include the MapTypeId to add
            // to the map type control.
            this.mapOptions = {
                zoom: this.settings.zoom,
                center: this.center,
                scrollwheel: this.settings.scrollwheel,
                disableDefaultUI: this.settings.disableDefaultUI
            };
            this.gmap = new google.maps.Map(this.$this[0],
                    this.mapOptions);
            //Associate the styled map with the MapTypeId and set it to display.
            var coordsLength = (this.arrayLatitude.length < this.arrayLongitude.length) ? this.arrayLatitude.length : this.arrayLongitude.length;
            for (var i = 0; i < coordsLength; i++) {
                var coordCenter = new google.maps.LatLng(this.arrayLatitude[i], this.arrayLongitude[i]);
                var marker = new google.maps.Marker({
                    position: coordCenter,
                    map: this.gmap,
                    icon: this.settings.icon
                });
            }

            this.gmap.mapTypes.set('map_style', styledMap);
            this.gmap.setMapTypeId('map_style');

            google.maps.event.addListenerOnce(this.gmap, 'idle', function() {
                that.googleMapInit();
            });
        }
    }
    //public methods here
    members.methods = {
        onResize: function(obj) {
            google.maps.event.trigger(obj.gmap, 'resize');
            obj.gmap.setCenter(obj.mapOptions.center);
            var radius = obj.$this.width();
            //TODO : rezoom
            var newZoom = this.settings.zoom;
            if (obj.maxDistance)
                var newZoom = getCorrectZoom(obj.maxDistance, radius);
            obj.gmap.setZoom(newZoom);
        },
    };
    //everything below should be left as it is
    $.fn[pluginName] = function(options, args) {
        if (typeof options === "string") {
            return this.each(function() {
                var data = $(this).data('data' + pluginName);
                if (typeof data === 'object') {
                    if (data.methods[options]) {
                        data.methods[options](data, args);
                    }
                }
            });
        }
        else {
            return this.each(function() {
                var settings = $.extend({}, defaultSettings, options);
                if (typeof $(this).data('data' + pluginName) === 'undefined') {
                    var dataToSave = Object.create(members);
                    dataToSave.settings = Object.create(settings);
                    dataToSave.init(this);
                    $(this).data('data' + pluginName, dataToSave);
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