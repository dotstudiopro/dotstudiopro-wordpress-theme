(function ($) {
    /**
     * function to display autocomplete result for search page
     * @type jqXHRSerch result autocomplete code
     */

    var html = '';
    var autocomplete = $('.search-autocomplete').autoComplete({
        minChars: 2,
        delay: 500,
        cache: false,
        source: function (term, suggest) {
            var nonce = $('.search-autocomplete').data('nonce');
            jQuery('.autocomplete-suggestions').show();
            if (jQuery(".suggesion-loading").length == 0) {
                jQuery(".autocomplete-suggestions").append("<div class='suggesion-loading'></div>");
            }
            jQuery('.suggesion-loading').show();
            jQuery('.suggesion-overlay').addClass('add-opacity');
            try {
                searchRequest.abort();
            } catch (e) {
            }
            var searchRequest = $.post(
                    jsVariable.ajaxUrl,
                    {
                        'action': 'autocomplete',
                        'search': term,
                        'nonce': nonce,
                    }
            );
            searchRequest.done(function (response) {
                jQuery('.suggesion-loading').hide();
                jQuery('.suggesion-overlay').removeClass('add-opacity');
                html = '';
                jQuery(".autocomplete-suggestions").html('');
                if (jQuery('div.autocomplete-suggestions').is(':visible')) {
                    jQuery('body').addClass('search-suggestions-open');
                }
                if (response.data.length == 0) {
                    suggest([{flag: 'empty', data: ''}]);
                } else {
                    $.each(response.data, function (key, value) {
                        suggest([{flag: key, data: response.data[key]}]);
                    })
                }
            });
            searchRequest.fail(function (response) {
                jQuery('.suggesion-loading').hide();
                jQuery('.suggesion-overlay').removeClass('add-opacity');
                console.log(response);
            })
            autocomplete.on('keyup.autocomplete', function (e) {
                if (autocomplete.val().length < 2) {
                    searchRequest.abort();
                    jQuery('body').removeClass('search-suggestions-open');
                }
            });
        },
        renderItem: function (item, search) {

            if (jQuery(".suggesion-loading").length == 0) {
                html += '<div class="suggesion-loading"></div><div class="suggesion-overlay">';
            }

            if (item.flag == 'directors') {
                html += '<div class="directors_information information-top clearfix"><h5>Directors:</h5><ul>';
                $.each(item.data, function (key, value) {
                    html += '<li class="autocomplete-suggestion-title" data-val="' + value.name + '"><a href="javascript:" class="suggesion_click" data-search="' + value.name + '">' + value.name + '</a></li>';
                })
                html += '</ul></div>';
            }
            if (item.flag == 'actors') {
                html += '<div class="actors_information information-top clearfix"><h5>Actors:</h5><ul>';
                $.each(item.data, function (key, value) {
                    html += '<li class="autocomplete-suggestion-title" data-val="' + value.name + '"><a href="javascript:" class="suggesion_click" data-search="' + value.name + '">' + value.name + '</a></li>';
                })
                html += '</ul></div>';
            }
            if (item.flag == 'title') {
                html += '<div class="title_information information-top clearfix"><h5>Title:</h5><ul>';
                $.each(item.data, function (key, value) {
                    html += '<li class="autocomplete-suggestion-title" data-val="' + value.name + '"><a href="javascript:" class="suggesion_click" data-search="' + value.name + '">' + value.name + '</a></li>';
                })
                html += '</ul></div>';
            }
            if (item.flag == 'tags') {
                html += '<div class="tags_information information-top clearfix"><h5>Tags:</h5><ul>';
                $.each(item.data, function (key, value) {
                    html += '<li class="autocomplete-suggestion-title" data-val="' + value.name + '"><a href="javascript:" class="suggesion_click" data-search="' + value.name + '">' + value.name + '</a></li>';
                })
                html += '</ul></div>';
            }
            if (item.flag == 'channel') {
                var title = '';
                if (typeof item.data[0].title != "undefined") {
                    title = item.data[0].title;
                }
                html += '<div class="channl_information clearfix mt-4 row"><h3 class="ch_name mb-4 w-100">' + title + '</h3>';
                $.each(item.data, function (key, value) {
                    var image_attributes = '';
                    if(!$.isEmptyObject(value.image_attributes)) {
                        image_attributes = 'srcset = "' + value.image_attributes.srcset + '" sizes = "' + value.image_attributes.sizes + '"';
                    }
                    html += '<div class="autocomplete-suggestion-channel col-lg-2 col-md-3 col-6" data-val="' + value.name + '"><a href="' + value.url + '" title="' + value.name + '"><img src="' + value.image + '" ' + image_attributes + '><h5 class="pt-2 pb-1 text-center">' + value.name + '</h4></a></div>';
                })
                html += '</div>';
                html += '</div>';
            }
            if (item.flag == 'empty') {
                html += '<div class=" empty-data information-top clearfix">';
                html += '<h4>It seems we can’t find what you’re looking for. Perhaps searching can help.</h4>';
                html += '</div>';
            }

            return html;
        },
    });

    /**
     * remove the class if class exis on body even suggesion is close
     */
    $("body").click(function () {
        if (jQuery('div.autocomplete-suggestions').is(':hidden')) {
            if (jQuery("body").hasClass("search-suggestions-open")) {
                $('body').removeClass('search-suggestions-open');
            }
        }
    });
})(jQuery);

/**
 * Ajax call to change the result based on search suggesion click
 */

jQuery(document).on('click', '.suggesion_click', function () {
    jQuery('.suggesion-loading').show();
    jQuery('.suggesion-overlay').addClass('add-opacity');
    var html = '';
    var searchRequest = $.post(
            jsVariable.ajaxUrl,
            {
                'action': 'search_suggesion',
                'search': $(this).data('search'),
            }
    );
    searchRequest.done(function (response) {
        jQuery('.channl_information').html('');
        jQuery('.suggesion-loading').hide();
        jQuery('.suggesion-overlay').removeClass('add-opacity');
        var title = '';
        if (typeof response.data[0].title != "undefined") {
            title = response.data[0].title;
        }
        html += '<h3 class="ch_name mb-4 w-100">' + title + '</h3>';
        var i;
        if (response.data.length == 0) {
            html += '<h4>It seems we can’t find what you’re looking for. Perhaps searching can help.</h4>';
        } else {
            jQuery.each(response.data, function (key, value) {
                var image_attributes = '';
                if(!$.isEmptyObject(value.image_attributes)) {
                    image_attributes = 'srcset = "' + value.image_attributes.srcset + '" sizes = "' + value.image_attributes.sizes + '"';
                }
                html += '<div class="autocomplete-suggestion-channel col-lg-2 col-md-3 col-6" data-val="' + value.name + '"><a href="' + value.url + '" title="' + value.name + '"><img src="' + value.image + '" ' + image_attributes + '><h5 class="pt-2 pb-1 text-center">' + value.name + '</h5></a></div>';
            });
        }


        jQuery('.channl_information').append(html);
    });
    searchRequest.fail(function (response) {
        console.log(response);
    })

    jQuery('.search-autocomplete').on('keyup.autocomplete', function (e) {
        if (jQuery('.search-autocomplete').val().length < 2) {
            searchRequest.abort();
        }
    });
});
