var Pokemap = {

    defaultCoord: {lat: 53, lng: 27},
    defaultZoom: 15,
    map: null,
    curLocationMarker: null,
    pokeMarkers: [],
    pokeWindowMarkers: [],
    refreshLocationTimer: null,
    refreshLocationTimerInterval: 5000,
    updateDisable: false,


    getUID: function () {
        if (!$.cookie("uid")) {
            var uid = "pokeweb" + (new Date()).getTime();
            $.cookie("uid", uid);
        }
        return $.cookie("uid");
    },

    getMap: function () {
        return this.map;
    },

    getCurMarker: function () {
        return this.curLocationMarker;
    },
    setCurMarker: function (marker) {
        this.curLocationMarker = marker;
    },

    initMap: function () {

        this.map = new google.maps.Map(document.getElementById('map'), {
            center: this.defaultCoord,
            zoom: this.defaultZoom,
            disableDefaultUI: true,
        });

        this.map.controls[google.maps.ControlPosition.TOP_CENTER].push(this.getCenterControl());
        this.map.controls[google.maps.ControlPosition.TOP_CENTER].push(this.getPokemonsControl());
        this.map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(this.getInfoText());

        Pokemap.getCurLocation(function (pos) {
            Pokemap.setCurLocationMarker(pos);
            Pokemap.getMap().setCenter(pos);
            Pokemap.getMap().setZoom(15);
            Pokemap.getPokemons(pos);
        });

        this.refreshLocationTimer = setInterval(function () {
            Pokemap.refreshLocation();
        }, this.refreshLocationTimerInterval)
    },

    getCurLocation: function (callback) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                console.log('Found position:' + pos.lat + "/" + pos.lng);
                callback(pos);
            }, function () {
                alert("Ошибка определения геолокации")
            });
        } else {
            // Browser doesn't support Geolocation
            alert("Ошибка определения геолокации. Браузер не поддерживает геолокацию")
        }
    },

    refreshLocation: function () {
        this.getCurLocation(function (pos) {
            Pokemap.setCurLocationMarker(pos);
            console.log("Location refreshed");
        });
    },

    'setCurLocationMarker': function (coords) {

        this.animate(jQuery("#eshImage"));

        if (this.getCurMarker()) {
            this.getCurMarker().setMap(null);
        }
        console.log("Cur location marker: " + coords);
        var icon = {
            url: 'assets/nav.png',
            //state your size parameters in terms of pixels
            size: new google.maps.Size(25, 25),
            scaledSize: new google.maps.Size(25, 25),
            origin: new google.maps.Point(0, 0)
        };

        var marker = new google.maps.Marker({
            position: coords,
            map: Pokemap.getMap(),
            title: "Настоящий Мастер покемонов"
        });
        this.setCurMarker(marker);
    },

    getCenterControl: function () {
        // Create a div to hold the control.
        var controlDiv = document.createElement('div');

        var controlUI = document.createElement('div');
        controlUI.style.cursor = 'pointer';
        controlUI.style.textAlign = 'center';
        controlUI.style.marginBottom = '30px';
        controlUI.title = 'Click to recenter the map';
        controlDiv.appendChild(controlUI);

        var controlText = document.createElement('div');
        controlText.style.color = 'rgb(25,25,25)';
        controlText.style.paddingLeft = '5px';
        controlText.innerHTML = '<div class="btn btn-default btn-sm">  <img id="eshImage" src="assets/esh.png" style="width: 50px; height: 50px"/> Центрировать</div>';
        controlUI.appendChild(controlText);


        controlUI.addEventListener('click', function () {
            Pokemap.getCurLocation(function (pos) {
                Pokemap.setCurLocationMarker(pos);
                Pokemap.getMap().setCenter(pos);
                Pokemap.getMap().setZoom(18);
            });

        });
        return controlDiv;
    },

    getInfoText: function () {
        // Create a div to hold the control.
        var controlDiv = document.createElement('div');

        var controlUI = document.createElement('div');
        controlUI.style.cursor = 'pointer';
        controlUI.style.textAlign = 'center';
        controlUI.title = 'Click to refresh the map';
        controlDiv.appendChild(controlUI);

        var controlText = document.createElement('div');
        controlText.style.color = 'rgb(25,25,25)';
        controlText.innerHTML = '<div class="alert alert-warning" ">После обновления если покемоны не нашлись, попробуйте переобновить через 20 сек пока бот обработает область.</div>';
        controlUI.appendChild(controlText);


        controlUI.addEventListener('click', function () {
            Pokemap.getCurLocation(function (pos) {
                Pokemap.setCurLocationMarker(pos);
                Pokemap.getMap().setCenter(pos);
                Pokemap.getMap().setZoom(18);
            });

        });
        return controlDiv;
    },


    getPokemonsControl: function () {
        // Create a div to hold the control.
        var controlDiv = document.createElement('div');

        var controlUI = document.createElement('div');
        controlUI.style.cursor = 'pointer';
        controlUI.style.textAlign = 'center';
        controlUI.style.marginBottom = '30px';
        controlUI.title = 'Click to recenter the map';
        controlUI.id = "getPokemonsControl";
        controlDiv.appendChild(controlUI);

        var controlText = document.createElement('div');
        controlText.style.color = 'rgb(25,25,25)';
        controlText.style.paddingLeft = '5px';
        controlText.style.paddingRight = '5px';
        controlText.innerHTML = '<div class="btn btn-default btn-sm"><img id="pokeballImage" src="assets/pokeball.png" style="width: 50px; height: 50px"/>Обновить</div >';
        controlUI.appendChild(controlText);


        controlUI.addEventListener('click', function () {
            Pokemap.getCurLocation(function (pos) {
                if(!Pokemap.updateDisable) {
                    Pokemap.getPokemons(pos)
                }
            });

        });
        return controlDiv;
    },

    clearPokeMarkers: function () {
        $.each(Pokemap.pokeMarkers, function (key, pokeMarker) {
                pokeMarker.setMap(null);
            }
        );
    },
    closePokeWindows: function () {
        $.each(Pokemap.pokeWindowMarkers, function (key, window) {
                window.close();
            }
        );
    },
    getPokemons: function (pos) {

        this.clearPokeMarkers();
        this.animate(jQuery("#pokeballImage"));
        Pokemap.updateDisable = true;

        $.ajax({
            url: "../rest/pokemon/list/" + pos.lat + "/" + pos.lng,
            type: 'GET',
            dataType: 'json',
            headers: {'Userguid': Pokemap.getUID()},
            success: function (json) {
                $.each(json, function (key, data) {

                    var date = new Date(data.expired);
                    var seconds = parseInt((date.getTime() - (new Date()).getTime()) / 1000);
                    var dateSeconds = (date.getSeconds()) > 10 ? date.getSeconds() : '0' + date.getSeconds();
                    var infowindow = new google.maps.InfoWindow({
                        content: '' +
                        'Имя: ' + data.name + '<br>' +
                        'Истекает:  ' + date.getHours() + ':' + date.getMinutes() + '<br>' +
                        'Осталось:  ' + seconds + ' Cекунд <br>'

                    });
                    var latLng = new google.maps.LatLng(data.lat, data.lng);
                    // Creating a marker and putting it on the map
                    var image = "assets/new-icons/" + data.pokeuid + ".png";
                    var marker = new google.maps.Marker({
                        position: latLng,
                        map: Pokemap.getMap(),
                        icon: image,
                        title: data.name

                    });
                    marker.addListener('click', function () {
                        Pokemap.closePokeWindows();
                        infowindow.open(Pokemap.getMap(), marker);
                    });

                    Pokemap.pokeMarkers.push(marker);
                    Pokemap.pokeWindowMarkers.push(infowindow);
                });

                Pokemap.updateDisable = false;
            }
        });
    },

    animate: function (el) {
        // caching the object for performance reasons

        // we use a pseudo object for the animation
        // (starts from `0` to `angle`), you can name it as you want
        $({deg: 0}).animate({deg: 360}, {
            duration: 1000,
            step: function (now) {
                // in the step-callback (that is fired each step of the animation),
                // you can use the `now` paramter which contains the current
                // animation-position (`0` up to `angle`)
                el.css({
                    transform: 'rotate(' + now + 'deg)'
                });
            }
        });
    }

};