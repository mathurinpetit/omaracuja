
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

            console.log(results[0].geometry.location);
            $('.mapX_setting').val(results[0].geometry.location.k);
            $('.mapY_setting').val(results[0].geometry.location.D);

            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
        } else {
            alert('Adresse introuvable: ' + status);
        }
    });
}


//var geocoders = new Object();
//var maps = new Object();
//var latLng = new Object();
//
//var initMaps = function() {
//
//    $('.event_map_show_multiple').each(function() {
//        var id_map = $(this).attr("id").replace('map-canvas-', '');
//        geocoders['map_' + id_map] = new google.maps.Geocoder();
//        var mapX = 48.856614;
//        var mapY = 2.3522219000000177;
//        var adressExist = $('#google_map_x_' + id_map).length && $('#google_map_y_' + id_map).length;
//        if (adressExist) {
//            mapX = $('#google_map_x_' + id_map).val();
//            mapY = $('#google_map_y_' + id_map).val();
//        }
//        latLng['map_' + id_map] = new google.maps.LatLng(mapX, mapY);
//
//        var mapOptions = {
//            zoom: 14,
//            center: latLng['map_' + id_map],
//            mapTypeId: google.maps.MapTypeId.ROADMAP
//        }
//
//        maps['map_' + id_map] = new google.maps.Map(document.getElementById('map-canvas-' + id_map), mapOptions);
//
//        if (adressExist) {
//            var marker = new google.maps.Marker({
//                map: maps['map_' + id_map],
//                position: latLng['map_' + id_map]
//            });
//        }
//    });
//}
//



$(document).ready(function() {
    //initMaps();
    initMap();

    $('.google_map_adress').blur(findAdress);

//    $('#accordion div.panel').click(function() {
//        $('#map_canvas').each(function() {
//            $(this).resize();
//            google.maps.event.trigger(map, "resize");
//
//        })
//        $('.event_map_show_multiple').each(function() {
//            $(this).resize();
//            var id_map = $(this).attr("id").replace('map-canvas-', '');
//            google.maps.event.trigger(maps['map_' + id_map], "resize");
//            var mapOptions = {
//                zoom: 14,
//                center: latLng['map_' + id_map]
//            }
//            delete(maps['map_' + id_map]);
//            maps['map_' + id_map] = new google.maps.Map(document.getElementById('map-canvas-' + id_map), mapOptions);
//            var marker = new google.maps.Marker({
//                map: maps['map_' + id_map],
//                position: latLng['map_' + id_map]
//            });
//        });
//    });

//    $('.panel-collapse').each(function() {
//        $(this).on('shown.bs.collapse', function() {
//            $('.panel-heading').each(function() {
//                $(this).trigger('click');
//            })
//        });
//    });

});

//google.maps.event.addDomListener(window, 'resize', function() {
//    map.setCenter(center);
//});