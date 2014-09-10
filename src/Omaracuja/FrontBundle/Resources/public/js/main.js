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
// initialisation de la carte Google Map de départ
    var initMap = function() {
        geocoder = new google.maps.Geocoder();

        var latlng = new google.maps.LatLng(48.856614, 2.3522219000000177);
        var mapOptions = {
            zoom: 14,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        // map-canvas est le conteneur HTML de la carte Google Map
        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    }

    var findAdress = function() {
        // Récupération de l'adresse tapée dans le formulaire
        var adresse = $('.google_map_adress').val();
        console.log(adresse);
        geocoder.geocode({'address': adresse}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                // Récupération des coordonnées GPS du lieu tapé dans le formulaire
                var strposition = results[0].geometry.location + "";
                strposition = strposition.replace('(', '');
                strposition = strposition.replace(')', '');
                // Création du marqueur du lieu (épingle)
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

    $('#google_map_refresh_map').click(findAdress);
});



