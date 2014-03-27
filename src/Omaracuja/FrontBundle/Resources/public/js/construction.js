/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {

    var theWindow = $(window),
            $bg = $("#bg"),
            aspectRatio = $bg.width() / $bg.height();

    function resizeBg() {

        if ((theWindow.width() / theWindow.height()) < aspectRatio) {
            $bg
                    .removeClass()
                    .addClass('bgheight');
        } else {
            $bg
                    .removeClass()
                    .addClass('bgwidth');
        }

    }

    theWindow.resize(function() {
        resizeBg();
    }).trigger("resize");

});