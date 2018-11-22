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
     * 
     * @type jqXHRSerch result autocomplete code
     */

    var searchRequest;
    $('.search-autocomplete').autoComplete({
        minChars: 2,
        source: function (term, suggest) {
            try {
                searchRequest.abort();
            } catch (e) {
            }
            var searchRequest = $.post(
                    jsVariable.ajaxUrl,
                    {
                        'action': 'autocomplete',
                        'search': term,
                    }
            );
            searchRequest.done(function (response) {
                console.log(response);
            });
            searchRequest.fail(function (response) {
                console.log(response);
            })

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