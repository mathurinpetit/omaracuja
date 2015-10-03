
var geocoder;
var map;

var initMap = function() {
    $('.event_map_choose').each(function() {
        geocoder = new google.maps.Geocoder();
        var mapX = 48.856614;
        var mapY = 2.3522219000000177;
        var adressExist = $('#google_map_x').length && $('#google_map_y').length;
        if (adressExist) {
            mapX = $('#google_map_x').val();
            mapY = $('#google_map_y').val();
        }
        var latlng = new google.maps.LatLng(mapX, mapY);
        var mapOptions = {
            zoom: 14,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

        if (adressExist) {
            var marker = new google.maps.Marker({
                map: map,
                position: latlng
            });
        }
    });
}

var findAdress = function() {

    var adresse = $('.google_map_adress').val();
    
    geocoder.geocode({'address': adresse}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            $('.mapX_setting').val(results[0].geometry.location.lat());
            $('.mapY_setting').val(results[0].geometry.location.lng());

            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
        } else {
            alert('Adresse introuvable: ' + status);
        }
    });
}



$(document).ready(function() {

    initMap();

    $('.google_map_adress').blur(findAdress);

});

