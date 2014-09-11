/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {

    $('.datetimepicker').datetimepicker({
        lang: 'fr',
        i18n: {
            fr: {
                months: ["Janvier", "Février", "Mars", "Avril",
                    "Mai", "Juin", "Juillet", "Août", "Septembre",
                    "Octobre", "Novembre", "Décembre"],
                dayOfWeek: [
                    "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam",
                ]
            }
        },
        timepicker: true,
        format: 'd M Y H:i'
    });

    $('.summernote').summernote({
        lang: 'fr-FR',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
        ]
    });

    var geocoder;
    var map;
    
    var initMap = function() {
        geocoder = new google.maps.Geocoder();
        var mapX = 48.856614;
        var mapY = 2.3522219000000177;
        var adressExist = $('#google_map_x').length && $('#google_map_y').length;
        if (adressExist) {
            mapX = $('#google_map_x').val();
            mapY = $('#google_map_y').val();
        }
        var latlng = new google.maps.LatLng(mapX,mapY);
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
    }

    var findAdress = function() {
        
        var adresse = $('.google_map_adress').val();
        
        geocoder.geocode({'address': adresse}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                
                
                $('.mapX_setting').val(results[0].geometry.location.k);
                $('.mapY_setting').val(results[0].geometry.location.B);
                
                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            } else {
                alert('Adresse introuvable: ' + status);
            }
        });
    }


// Lancement de la construction de la carte google map
    google.maps.event.addDomListener(window, 'load', initMap);

    $('.google_map_adress').blur(findAdress);

    $(".select2").select2();

});



