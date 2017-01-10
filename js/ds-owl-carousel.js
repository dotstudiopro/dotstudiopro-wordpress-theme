		jQuery(function($){
			$('body').css('overflow-x', 'hidden');
			var owl = $('.owl-carousel');
			
			resizeCarousel();

			$(window).resize(function(){
				resizeCarousel();
			});

			
			owl.owlCarousel({
			    items: 4,
   				// nav:true,
			    loop:true,
			    margin:10,
			    center:true,
			    autoplay:true,
			    autoplayTimeout:3000,
			    // autoplayHoverPause:true
			});

			owl.mouseleave(function(){
				$(this).trigger('play.owl.autoplay',[3000]);
			});

			owl.mouseenter(function(){
			    $(this).trigger('stop.owl.autoplay');
			});

			function resizeCarousel() {
				var w = $(window).width();
				var ocw = $('#owl-carosel-width').width();
				var ocwWidth = w >=ocw ? ocw : w;
				owl.width(ocwWidth);
			}

		});