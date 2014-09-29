/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {

    $.datepicker.setDefaults($.datepicker.regional[ "fr" ]);

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

    $('.summernote_event').summernote({
        lang: 'fr-FR',
        height: 480,
        minHeight: 480,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
        ]
    });
    $(".select2").select2();
    
    
    $("[rel='tooltip']").tooltip();

    $('.thumbnail').hover(
            function() {
                $(this).find('.caption').slideDown(250); //.fadeIn(250)
            },
            function() {
                $(this).find('.caption').slideUp(250); //.fadeOut(205)
            }
    );
    $('.carousel').carousel('pause');
    
    $('.btn-zoom').click(function (){
        console.log('click');
        $('#picture-view-modal').modal("show");
    });
});



