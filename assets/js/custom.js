(function ($) {

    /**
     * Tooltip options
     */
    $('.tooltippp').tooltipster({
        maxWidth: 200,
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

    $('.search-autocomplete').autoComplete({
        minChars: 2,
        delay: 500,
        source: function (term, suggest) {
            var nonce = $('.search-autocomplete').data('nonce');
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
                suggest(response.data);
            });
            searchRequest.fail(function (response) {
                console.log(response);
            })

        },
        renderItem: function (item, search) {
            return '<div class="autocomplete-suggestion" data-val="' + item['title'] + '"><img src="' + item['image'] + '/10/10"><div class="title">' + item['title'] + '</div></div>';
        },
        onSelect: function (e, term, item) {
            jQuery('.sb-search-input').val(term);
            jQuery('.search-form').submit();
        }
    });


})(jQuery);

/**
 * Back to top button
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
 * Add functionality to Login button when clicked to trigger auth0 login
 */
jQuery('.dsp-auth0-login-button').click(function() {
    // Make sure we have the auth0 login button before we try triggering events
    if (jQuery("#a0LoginButton").length > 0) {
        jQuery("#a0LoginButton").trigger('click');
    } else {
        // If we don't have the button, redirect the user to the login
        window.location.href = jQuery(this).data('login_url');
    }
});
