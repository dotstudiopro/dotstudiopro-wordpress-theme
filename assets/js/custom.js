(function ($) {

    /**
     * Tooltip options
     */
    $('.tooltippp').tooltipster({
        maxWidth: 300,
        contentCloning: true,
        contentAsHTML: true,
        interactive: true,
        animation: 'fade',
        delay: 200,
    });
    /**
     * init search
     */
    var searcheelements = document.querySelectorAll('[id=sb-search]');
    searcheelements.forEach(function (item) {
        new UISearch(item);
    });

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
                jQuery('body').addClass('search-suggestions-open');
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

        },
        renderItem: function (item, search) {

            if (jQuery(".suggesion-loading").length == 0) {
                html += '<div class="suggesion-loading"></div><div class="suggesion-overlay">';
            }

            if (item.flag == 'directors') {
                html += '<div class="directors_information information-top clearfix"><h5>Directors:</h5><ul>';
                $.each(item.data, function (key, value) {
                    html += '<li class="autocomplete-suggestion-title" data-val="' + value.name + '"><a href="#" class="suggesion_click" data-search="' + value.name + '">' + value.name + '</a></li>';
                })
                html += '</ul></div>';
            }
            if (item.flag == 'actors') {
                html += '<div class="actors_information information-top clearfix"><h5>Actors:</h5><ul>';
                $.each(item.data, function (key, value) {
                    html += '<li class="autocomplete-suggestion-title" data-val="' + value.name + '"><a href="#" class="suggesion_click" data-search="' + value.name + '">' + value.name + '</a></li>';
                })
                html += '</ul></div>';
            }
            if (item.flag == 'title') {
                html += '<div class="title_information information-top clearfix"><h5>Title:</h5><ul>';
                $.each(item.data, function (key, value) {
                    html += '<li class="autocomplete-suggestion-title" data-val="' + value.name + '"><a href="#" class="suggesion_click" data-search="' + value.name + '">' + value.name + '</a></li>';
                })
                html += '</ul></div>';
            }
            if (item.flag == 'tags') {
                html += '<div class="tags_information information-top clearfix"><h5>Tags:</h5><ul>';
                $.each(item.data, function (key, value) {
                    html += '<li class="autocomplete-suggestion-title" data-val="' + value.name + '"><a href="#" class="suggesion_click" data-search="' + value.name + '">' + value.name + '</a></li>';
                })
                html += '</ul></div>';
            }
            if (item.flag == 'channel') {
                html += '<div class="channl_information clearfix mt-4 row"><h3 class="ch_name mb-4 w-100">' + item.data[0].title + '</h3>';
                $.each(item.data, function (key, value) {
                    html += '<div class="autocomplete-suggestion-channel col-lg-2 col-md-3 col-6" data-val="' + value.name + '"><a href="' + value.url + '" title="' + value.name + '"><img src="' + value.image + '/265/149"><h5 class="pt-2 pb-1 text-center">' + value.name + '</h4></a></div>';
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
    autocomplete.on('keyup.autocomplete', function (e) {
        if (autocomplete.val().length < 2) {
            jQuery('body').removeClass('search-suggestions-open');
            $(".slider").slick('setPosition');
        }
    });

    $("body").click(function () {
        if (jQuery('div.autocomplete-suggestions').is(':hidden')) {
            if (jQuery("body").hasClass("search-suggestions-open")) {
                $('body').removeClass('search-suggestions-open');
                $(".slider").slick('setPosition');
            }
        }
    });

})(jQuery);



/**
 * Blur event to remove the class if exist
 * @param {type} param1
 * @param {type} param2
 */

jQuery(window).on("blur", function (event) {
    if (jQuery('div.autocomplete-suggestions').is(':hidden')) {
        if (jQuery("body").hasClass("search-suggestions-open")) {
            jQuery("body").removeClass("search-suggestions-open")
        } else {
            return true;
        }
    }
});


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
        html += '<h3 class="ch_name mb-4 w-100">' + response.data[0].title + '</h3>';
        var i;

        jQuery.each(response.data, function (key, value) {
            html += '<div class="autocomplete-suggestion-channel col-lg-2 col-md-3 col-6" data-val="' + value.name + '"><a href="' + value.url + '" title="' + value.name + '"><img src="' + value.image + '/265/149"><h5 class="pt-2 pb-1 text-center">' + value.name + '</h5></a></div>';
        });



        jQuery('.channl_information').append(html);
    });
    searchRequest.fail(function (response) {
        console.log(response);
    })
});

/**
 * Back to top button js
 * @param {type} param
 */
jQuery(window).scroll(function () {
    if (jQuery(this).scrollTop() >= 50) {
        jQuery('#return-to-top').fadeIn(200);
        jQuery('header').addClass('small-header');
    } else {
        jQuery('#return-to-top').fadeOut(200);
        jQuery('header').removeClass('small-header');
    }
});

jQuery('#return-to-top').click(function () {
    jQuery('body,html').animate({
        scrollTop: 0
    }, 500);
});

/**
 * function to put cursor at the end for search field
 * @returns {jQuery.fn@call;each}
 */

jQuery.fn.putCursorAtEnd = function () {

    return this.each(function () {
        var $el = jQuery(this),
                el = this;
        if (!$el.is(":focus")) {
            $el.focus();
        }
        if (el.setSelectionRange) {
            var len = $el.val().length * 2;
            setTimeout(function () {
                el.setSelectionRange(len, len);
            }, 1);
        } else {
            $el.val($el.val());
        }
        this.scrollTop = 999999;
    });

};

(function () {
    var searchInput = jQuery(".search-autocomplete");
    searchInput
            .putCursorAtEnd() // should be chainable
            .on("focus", function () { // could be on any event
                searchInput.putCursorAtEnd()
            });
})();

/**
 * Apply custom scrollbar
 * @param {type} $
 * @returns {undefined}
 */
(function ($) {
    $(window).on("load resize", function () {
        if ($(window).width() < 992) {
            $("#content-1").mCustomScrollbar({
                theme: "minimal"
            });
        } else {
            $("#content-1").mCustomScrollbar('destroy');
        }
    });
    jQuery(document).ajaxComplete(function (event) {
        $("div.autocomplete-suggestions").mCustomScrollbar({
            theme: "minimal"
        });
    });
})(jQuery);

/**
 * Add functionality to Login button when clicked to trigger auth0 login
 */
jQuery('.dsp-auth0-login-button').click(function () {
    // Make sure we have the auth0 login button before we try triggering events
    if (jQuery("#a0LoginButton").length > 0) {
        jQuery("#a0LoginButton").trigger('click');
    } else {
        // If we don't have the button, redirect the user to the login
        window.location.href = jQuery(this).data('login_url');
    }
});

/**
 * Action to load login pop-up if user is not logged-in
 */
jQuery('.login-link').on('click', function (e) {
    e.preventDefault();
    $('#a0LoginButton').click();
});

/**
 * Add funcationality to add to my watch list
 */

jQuery('.manage_my_list').click(function (e) {
    e.preventDefault();
    $(this).prop('disabled', true);

    var action = $(this).data('action');
    var nonce = $(this).data('nonce');
    var channel_id = $(this).data('channel_id');
    var manage_my_list = $.post(
            jsVariable.ajaxUrl,
            {
                'action': action,
                'nonce': nonce,
                'channel_id': channel_id
            }
    );
    manage_my_list.done(function (response) {
        $(this).prop('disabled', false);
        if (action == 'addToMyList') {
            $('.my_list_button').html('<a href="/my-list" class="btn btn-danger text-uppercase"><i class="fa fa-minus-circle"></i>Remove from My List</a>');
        } else {
            window.location.reload();
        }
    });
});

/**
 * Event to store the point data when the video page is refreshed
 * @param {type} param1
 * @param {type} param2
 */

window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
    if (typeof (dotstudiozPlayer) != "undefined" && dotstudiozPlayer !== null) {
        var play_time = dotstudiozPlayer.player.currentTime();
        var video_id = jQuery('.player').data('video_id');
        var nonce = jQuery('.player').data('nonce');
        if (video_id && play_time && nonce) {
            $.post(
                    jsVariable.ajaxUrl,
                    {
                        'action': 'save_point_data',
                        'play_time': play_time,
                        'video_id': video_id,
                        'nonce': nonce
                    }
            );
        }
        return true;
    }
});

