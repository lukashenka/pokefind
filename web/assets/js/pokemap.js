var Pokemap = {

    defaultCoord: {lat: -34.397, lng: 150.644},
    defaultZoom: 15,
    map: null,
    curLocationMarker: null,

    getMap: function () {
        return this.map;
    },

    getCurMarker: function () {
        return this.curLocationMarker;
    },
    setCurMarket: function (marker) {
        this.curLocationMarker = marker;
    },

    initMap: function () {

        this.map = new google.maps.Map(document.getElementById('map'), {
            center: this.defaultCoord,
            zoom: 15
        });

        this.refreshLocation();

    },

    refreshLocation: function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                Pokemap.setCurLocationMarker(pos);
                Pokemap.getMap().setCenter(pos);
            }, function () {
                handleLocationError(true, infoWindow, map.getCenter());
            });
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
        }
    },

    'setCurLocationMarker': function (coords) {

        var icon = {
            url: 'assets/esh.png',
            //state your size parameters in terms of pixels
            size: new google.maps.Size(50, 50),
            scaledSize: new google.maps.Size(50, 50),
            origin: new google.maps.Point(0,0)
        }

        var marker = new google.maps.Marker({
            position: coords,
            map: Pokemap.getMap(),
            icon: icon,
            title: "Настоящий Мастер покемонов"
        });
        this.setCurMarket(marker);
    }

};