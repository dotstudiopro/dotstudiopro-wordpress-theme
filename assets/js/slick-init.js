(function ($) {
    var selector = slick_carousel.selector;
    console.log(slick_carousel.infinite);
    var responsive_array = [
        {
            breakpoint: 768,
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

    $(".slider").slick({
        autoplay: true,
        autoplaySpeed: 2000,
        infinite: true,
    });

})(jQuery);
