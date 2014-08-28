/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {

        $('.datetimepicker').datetimepicker({
        lang: 'fr',
        i18n:{
            fr:{
                months:["Janvier", "Février", "Mars", "Avril",
                    "Mai", "Juin", "Juillet", "Août", "Septembre",
                    "Octobre", "Novembre", "Décembre"],
                dayOfWeek:[
                    "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam",
                ]
            }
        },
        timepicker:false,
        format:'d M Y'
    });   
    

});


