(function ($) {

    /**
     * Global slider options
     */
    if (typeof (slick_carousel) != 'undefined') {
        var selector = slick_carousel.selector;
        var responsive_array = [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: parseInt(slick_carousel.tablet_slidetoshow),
                    slidesToScroll: parseInt(slick_carousel.tablet_slidetoshow),
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: parseInt(slick_carousel.mobile_slidetoshow),
                    slidesToScroll: parseInt(slick_carousel.mobile_slidetoshow),
                }
            }
        ];
        /*
         * Global configuration for slick slider.
         */
        $.each(selector, function (index, value) {
            $("." + value).slick({
                slidesToShow: parseInt(slick_carousel.slidetoshow),
                slidesToScroll: parseInt(slick_carousel.slidetoscroll),
                infinite: (slick_carousel.infinite == 1) ? true : false,
                speed: parseInt(slick_carousel.slidespeed),
                dots: (slick_carousel.pagination == 1) ? true : false,
                autoplay: (slick_carousel.autoplay == 1) ? true : false,
                autoplaySpeed: parseInt(slick_carousel.autoplayspeed),
                adaptiveHeight: true,
                responsive: (slick_carousel.responsive == 1) ? responsive_array : [],
            });
        });

        if (slick_carousel.navigation == 0) {
            $('.slick-prev').hide();
            $('.slick-next').hide();
        }
    }

    /**
     * Related content slider option
     */
    if (typeof (slick_related_carousel) != 'undefined') {
        var related_selector = slick_related_carousel.selector;
        var responsive_array = [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: parseInt(slick_related_carousel.tablet_slidetoshow),
                    slidesToScroll: parseInt(slick_related_carousel.tablet_slidetoshow),
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: parseInt(slick_related_carousel.mobile_slidetoshow),
                    slidesToScroll: parseInt(slick_related_carousel.mobile_slidetoshow),
                }
            }
        ];
        /*
         * Global configuration for slick slider.
         */

        $("." + related_selector).slick({
            slidesToShow: parseInt(slick_related_carousel.slidetoshow),
            slidesToScroll: parseInt(slick_related_carousel.slidetoscroll),
            infinite: (slick_related_carousel.infinite == 1) ? true : false,
            speed: parseInt(slick_related_carousel.slidespeed),
            dots: (slick_related_carousel.pagination == 1) ? true : false,
            autoplay: (slick_related_carousel.autoplay == 1) ? true : false,
            autoplaySpeed: parseInt(slick_related_carousel.autoplayspeed),
            adaptiveHeight: true,
            responsive: (slick_related_carousel.responsive == 1) ? responsive_array : [],
        });


        if (slick_related_carousel.navigation == 0) {
            $('.slick-prev').hide();
            $('.slick-next').hide();
        }
    }

    /**
     * Home-page main courosol slick slider option
     */

    $(".slider").slick({
        lazyLoad: 'ondemand',
        autoplay: true,
        autoplaySpeed: 5000,
        infinite: true,
    });
    
    /**
     * Carousel settings for continue watch rails
     */
    if (typeof (slick_continue_watch) != 'undefined') {
        
        var selector = slick_continue_watch.selector;
        var responsive_array = [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: parseInt(slick_continue_watch.tablet_slidetoshow),
                    slidesToScroll: parseInt(slick_continue_watch.tablet_slidetoshow),
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: parseInt(slick_continue_watch.mobile_slidetoshow),
                    slidesToScroll: parseInt(slick_continue_watch.mobile_slidetoshow),
                }
            }
        ];
        /*
         * Global configuration for slick slider.
         */
        console.log('selector = ' + selector);
        $.each(selector, function (index, value) {
        console.log('value = ' + value);
        console.log('index = ' + index);
            $("." + value).slick({
                slidesToShow: parseInt(slick_continue_watch.slidetoshow),
                slidesToScroll: parseInt(slick_continue_watch.slidetoscroll),
                infinite: (slick_continue_watch.infinite == 1) ? true : false,
                speed: parseInt(slick_continue_watch.slidespeed),
                dots: (slick_continue_watch.pagination == 1) ? true : false,
                autoplay: (slick_continue_watch.autoplay == 1) ? true : false,
                autoplaySpeed: parseInt(slick_continue_watch.autoplayspeed),
                adaptiveHeight: true,
                responsive: (slick_continue_watch.responsive == 1) ? responsive_array : [],
            });
        });
    }


})(jQuery);
