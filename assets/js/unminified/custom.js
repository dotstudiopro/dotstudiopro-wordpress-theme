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



})(jQuery);

/**
 * Remove the class on keyup jquery
 * @param {type} param
 */
jQuery(document).keyup(function (e) {
    if (e.key === "Escape") {
        if (jQuery("body").hasClass("search-suggestions-open")) {
            jQuery("body").removeClass("search-suggestions-open");
        }
    }
});


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

    jQuery(document).ajaxStart(function (event) {
        $("div.autocomplete-suggestions").mCustomScrollbar('destroy');
    });

    jQuery(document).ajaxComplete(function (event) {
        $("div.autocomplete-suggestions").mCustomScrollbar({
            theme: "minimal",
            scrollEasing: "easeOut"
        });
    });
})(jQuery);

/**
 * Add functionality to Login button when clicked to trigger auth0 login
 */
(function ($) {
    $('.dsp-auth0-login-button').click(function () {
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

    $('.manage_my_list').click(function (e) {
        e.preventDefault();
        $(this).prop('disabled', true);

        var action = $(this).data('action');
        var nonce = $(this).data('nonce');
        var channel_id = $(this).data('channel_id');
        var parent_channel_id = $(this).data('parent_channel_id');
        var manage_my_list = $.post(
                jsVariable.ajaxUrl,
                {
                    'action': action,
                    'nonce': nonce,
                    'channel_id': channel_id,
                    'parent_channel_id': parent_channel_id
                }
        );
        manage_my_list.done(function (response) {
            $(this).prop('disabled', false);
            if (action == 'addToMyList') {
                $('.my_list_button').html('<a href="/my-list" class="btn btn-danger"><i class="fa fa-minus-circle"></i>Remove from My List</a>');
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
        if (typeof (dotstudiozPlayer) != "undefined" && dotstudiozPlayer !== null) {
            e.preventDefault();
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
})(jQuery);
/**
 * Toogle the class on menu icon click
 */
jQuery(document).on('click', '.navbar-toggler', function () {
    jQuery('body').toggleClass('fixed-body');
});