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
    new UISearch(document.getElementById('sb-search'));
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