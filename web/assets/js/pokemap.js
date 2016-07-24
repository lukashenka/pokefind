var Pokemap = {

    defaultCoord: {lat: -34.397, lng: 150.644},
    defaultZoom: 15,
    map: null,
    curLocationMarker: null,
    refreshLocationTimer: null,
    refreshLocationTimerInterval: 10000,


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

        this.refreshLocationTimer = setInterval(function () {
            this.refreshLocation();
        }, this.refreshLocationTimerInterval)
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
                alert("Ошибка определения геолокации")
            });
        } else {
            // Browser doesn't support Geolocation
            alert("Ошибка определения геолокации. Браузер не поддерживает геолокацию")
        }
    },

    'setCurLocationMarker': function (coords) {

        var icon = {
            url: 'assets/esh.png',
            //state your size parameters in terms of pixels
            size: new google.maps.Size(50, 50),
            scaledSize: new google.maps.Size(50, 50),
            origin: new google.maps.Point(0, 0)
        };

        var marker = new google.maps.Marker({
            position: coords,
            map: Pokemap.getMap(),
            icon: icon,
            title: "Настоящий Мастер покемонов"
        });
        this.setCurMarket(marker);
    },

    'getPokemons': function (lat, lng) {
        $.ajax({
            url: "../rest/pokemon/list/" + lat + "/" + lng,
            type: 'GET',
            dataType: 'json',
            beforeSend: setHeader,
            success: function (json) {
                $.each(json, function (key, data) {

                    var infowindow = new google.maps.InfoWindow({
                        content: '' +
                        'Имя: ' + data.name + '<br>' +
                        'Истекает:  ' + data.expired + '<br>'

                    });


                    var latLng = new google.maps.LatLng(data.lat, data.lng);
                    // Creating a marker and putting it on the map
                    var image = "assets/larger-icons/" + data.pokeuid + ".png";
                    var marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        icon: image,
                        title: data.name

                    });
                    marker.setMap(map);
                    marker.addListener('click', function () {
                        infowindow.open(map, marker);
                    });
                });
            }
        });
    }

};