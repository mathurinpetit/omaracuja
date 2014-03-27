/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var cache = {};


var initCommentToggleEdit = function() {
    $('button.comment-toggle_edit').each(function() {
        $(this).click(function() {
            var id = $(this).attr('id').replace(/comment\-toggle\-/, '');
            if ($('#comment-show-' + id).hasClass('hidden')) {
                $('#comment-edit-' + id).addClass('hidden');
                $('#comment-show-' + id).removeClass('hidden');
            } else {
                $('#comment-show-' + id).addClass('hidden');
                $('#comment-edit-' + id).removeClass('hidden');
                $('#comment-edit-' + id + ' > div > textarea').focus().select();
            }
        });

    });
}

var initSelect2Ajax = function(nameField) {
    var select2Field = $("input[data-id=" + nameField + "]");
    var url_ajax = $(select2Field).data('url');
    select2Field.attr('type', 'hidden');
    select2Field.select2({
                tags: [],
                language: 'fr',
                minimumInputLength: 2,
                tokenSeparators: [','],
                width: '100%',
                cache: false,
                containerCssClass: 'form-control',
                ajax: { 
                    url: url_ajax,
                    dataType: 'jsonp',
                    data: function (term, page) {
                        return {
                            term: term,
                            page_limit: 10
                        };
                    },
                    results: function (data, page) { 
                        var results = [];
                        $.each(data.results, function (index, item) {
                            results.push({
                                id: item.id,
                                text: item.text
                            });
                        });
                        return {
                            results: results
                        };
                    }
                },
                initSelection: function (element, callback) {
                    var data = [];
                    $(element.val().split(',')).each(function () {
                        
                       var tabInit = this.split('#');                       
                        data.push({id: tabInit[0], text: tabInit[1]});
                    });
                    callback(data);
                },
                createSearchChoice: function (term) {
                    return {id: term, text: term};
                },
                formatNoMatches: function (term) {
                    return '';
                },
                formatResultCssClass: function (object) {
                    return '';
                }
            });
            select2Field.on('select2-removed', function(e){
                var id = e.choice.id;
                var text = e.choice.text;
                var hidden_component = $(this).parent().children('input[type="hidden"]');
                if(id > 0){
                   var value = $(hidden_component).val();
                   var reg = new RegExp('('+id+'#'+text+'[,]?)','gi');
                   $(hidden_component).val(value.replace(reg, ''));
                }
            });
};

var initAutoCompleteAjax = function(nameField, nameRealIdField) {
    $("input[data-id=" + nameField + "]").autocomplete({
        autoFocus: false,
        source: function(request, response)
        {
            if (request.term in cache)
            {
                response($.map(cache[request.term], function(item)
                {
                    return {
                        label: item.name,
                        value: function()
                        {
                            $('input[data-id=' + nameField + ']').val(item.id);
                            $('input[data-id=' + nameRealIdField + ']').val(item.id);
                            return item.name;
                        }
                    };
                }));
            }
            else
            {
                var url = $(this.element).attr('data-url');
                var objData = {};
                objData = {term: request.term};

                $.ajax({
                    url: url,
                    dataType: "json",
                    data: objData,
                    type: 'POST',
                    success: function(data)
                    {
                        cache[request.term] = data;
                        response($.map(data, function(item)
                        {
                            return {
                                label: (item.id === "-1") ? item.name + ' (ajouter)' : item.name,
                                value: function()
                                {
                                        $('input[data-id=' + nameField + ']').val(item.id);
                                        $('input[data-id=' + nameRealIdField + ']').val(item.id);
                                        return item.name;
                                }
                            };
                        }));
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        console.log(textStatus, errorThrown);
                    }
                });
            }
        },
        minLength: 3,
        delay: 300
    });
}

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

    initAutoCompleteAjax('provenance', 'provenanceId');
    initSelect2Ajax('tags');
    
    initCommentToggleEdit();
});


