(function($) {

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
    searcheelements.forEach(function(item) {
        new UISearch(item);
    });



})(jQuery);

function storeVideoPoint(vjs) {
    window.addEventListener('beforeunload', function(e) {
        //e.preventDefault();
        var play_time = vjs.currentTime();
        var video_id = jQuery('.player').data('video_id');
        var nonce = jQuery('.player').data('nonce');
        if (video_id && play_time && nonce) {
            jQuery.post(
                jsVariable.ajaxUrl, {
                    'action': 'save_point_data',
                    'play_time': play_time,
                    'video_id': video_id,
                    'nonce': nonce
                }
            );
        }
        return null;
    });
}

/**
 * Remove the class on keyup jquery
 * @param {type} param
 */
jQuery(document).keyup(function(e) {
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

jQuery(window).on("blur", function(event) {
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
jQuery(window).scroll(function() {
    if (jQuery(this).scrollTop() >= 50) {
        jQuery('#return-to-top').fadeIn(200);
        jQuery('header').addClass('small-header');
    } else {
        jQuery('#return-to-top').fadeOut(200);
        jQuery('header').removeClass('small-header');
    }
});

jQuery('#return-to-top').click(function() {
    jQuery('body,html').animate({
        scrollTop: 0
    }, 500);
});

jQuery('.dspdl-customer-form-button > button').click(function(){
    // Make sure we have cleared the error message before we submit again
    jQuery('.dspdl-customer-form-message').html("").fadeOut('slow');
    // Get button saved in a variable for access later
    var btn = this;
    var input = jQuery('.dspdl-customer-form-code > input');
    var container = jQuery(input).parent().parent();
    var code = jQuery(input).val();
    var nonce = jQuery('.dspdl-customer-form-code > input.customer_code').val();
    console.log(nonce);
    if (!code) {
        jQuery('.dspdl-customer-form-message').html("Please enter a code to attach your device.").fadeIn('slow');
        return;
    }
    jQuery(btn).prop('disabled', true).parent().addClass('disabled');
    jQuery(input).prop('disabled', true).parent().addClass('disabled');

    // In order to make sure we can put the loader in and take the text out without screwing up the height of the button
    jQuery(".dspdl-customer-form-button > button").height(jQuery(".dspdl-customer-form-button > button").height());
    // Remove the button text so we don't have any overlayed text on our background loader
    jQuery(".dspdl-customer-form-button > button").html("").addClass('activateloader');

    var customer_code = jQuery.post(
        jsVariable.ajaxUrl, {
            code: code,
            nonce: nonce,
            action: "dspdl_ajax_customer_code_theme"
        }
    );

    customer_code.done(function(response) {
        jQuery(container).fadeOut('slow', function() {
            jQuery(container).html("<h3 class='dspdl-connection-success'>Your device has been successfully connected!</h3>").fadeIn('slow');
        });
    });

    customer_code.fail(function(response) {
        jQuery(btn).prop('disabled', false).parent().removeClass('disabled');
        jQuery(input).prop('disabled', false).parent().removeClass('disabled');
        jQuery(".dspdl-customer-form-button > button").html("Submit Code").removeClass('activateloader');
        jQuery('.dspdl-customer-form-message').html(response.responseJSON.data.message).fadeIn('slow');
        return;
    })
});

/**
 * function to put cursor at the end for search field
 * @returns {jQuery.fn@call;each}
 */

jQuery.fn.putCursorAtEnd = function() {

    return this.each(function() {
        var $el = jQuery(this),
            el = this;
        if (!$el.is(":focus")) {
            $el.focus();
        }
        if (el.setSelectionRange) {
            var len = $el.val().length * 2;
            setTimeout(function() {
                el.setSelectionRange(len, len);
            }, 1);
        } else {
            $el.val($el.val());
        }
        this.scrollTop = 999999;
    });

};

(function() {
    var searchInput = jQuery(".search-autocomplete");
    searchInput
        .putCursorAtEnd() // should be chainable
        .on("focus", function() { // could be on any event
            searchInput.putCursorAtEnd()
        });
})();

/**
 * Apply custom scrollbar
 * @param {type} $
 * @returns {undefined}
 */
(function($) {
    $(window).on("load resize", function() {
        if ($(window).width() < 992) {
            $("#content-1").mCustomScrollbar({
                theme: "minimal"
            });
        } else {
            $("#content-1").mCustomScrollbar('destroy');
        }
    });

    jQuery(document).ajaxStart(function(event) {
        $("div.autocomplete-suggestions").mCustomScrollbar('destroy');
    });

    jQuery(document).ajaxComplete(function(event) {
        $("div.autocomplete-suggestions").mCustomScrollbar({
            theme: "minimal",
            scrollEasing: "easeOut"
        });
    });
})(jQuery);

/**
 * Add functionality to Login button when clicked to trigger auth0 login
 */
(function($) {
    $('.dsp-auth0-login-button').click(function() {
        // Make sure we have the auth0 login button before we try triggering events
        if (jQuery("#a0LoginButton").length > 0) {
            jQuery("#a0LoginButton").trigger('click');
        } else {
            // If we don't have the button, redirect the user to the login
            window.location.href = jQuery(this).data('login_url');
        }
    });

    /**
     * Add funcationality to add to my watch list
     */

    $(document).on('click', '.manage_my_list', function(e) {
        e.preventDefault();
        $(this).prop('disabled', true);

        var action = $(this).data('action');
        var nonce = $(this).data('nonce');
        var channel_id = $(this).data('channel_id');
        var parent_channel_id = $(this).data('parent_channel_id');
        var remove_list_nonce = null;
        var manage_my_list = $.post(
            jsVariable.ajaxUrl, {
                'action': action,
                'nonce': nonce,
                'channel_id': channel_id,
                'parent_channel_id': parent_channel_id
            }
        );
        manage_my_list.done(function(response) {
            $(this).prop('disabled', false);
            if (action == 'addToMyList') {
                remove_list_nonce = $('.manage_my_list').next().data('nonce');
                $('.my_list_button').html('<button class="btn btn-danger manage_my_list" data-channel_id="' + channel_id + '" data-parent_channel_id="' + parent_channel_id + '" data-action="removeFromMyList" data-nonce="' + remove_list_nonce + '"><i class="fa fa-minus-circle"></i>Remove from My List</button>');
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
    window.addEventListener('beforeunload', function(e) {
        if (typeof DotPlayer.on !== "undefined") {
            //e.preventDefault();
            var play_time = DotPlayer.currentTime();
            var video_id = jQuery('.player').data('video_id');
            var nonce = jQuery('.player').data('nonce');
            if (video_id && play_time && nonce) {
                $.post(
                    jsVariable.ajaxUrl, {
                        'action': 'save_point_data',
                        'play_time': play_time,
                        'video_id': video_id,
                        'nonce': nonce
                    }
                );
            }
            return null;
        }
    });
})(jQuery);
/**
 * Toogle the class on menu icon click
 */
jQuery(document).on('click', '.navbar-toggler', function() {
    jQuery('body').toggleClass('fixed-body');
});

/**
 * Youbora analytics integration
 */
jQuery(document).ready(() => {
    const dspYouboraLoad = setInterval(() => {
        if (typeof youbora !== 'undefined' && typeof youbora.Plugin !== 'undefined') {
            clearInterval(dspYouboraLoad);
            window.plugin = new youbora.Plugin({
                'accountCode': 'dotstudioz',
                'debug': 1,
                'parse.hls': true,
                'parse.cdnNode': true,
                'extraparam.1': jsVariable.company_id,
                'extraparam.2': jsVariable.subdomain
            });
            window.plugin.infinity.begin();
        }
    }, 500);
})

/**
* Session Expired then show dialog
*/
jQuery(document).ready(() => {
    if(getCookie('dsp_session_expired')) {
        $("#login_again_dialog").modal("show");
        $("header").css("z-index", "-1");
    }
});

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}