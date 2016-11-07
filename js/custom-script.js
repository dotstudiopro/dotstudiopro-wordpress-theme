//Tabs
jQuery(function ($) {

  $('ul.ds-tabs li').click(function () {
    var tab_id = $(this).attr('data-tab');

    $('ul.ds-tabs li').removeClass('current animated fadeIn');
    $('.ds-tab-content').removeClass('current animated fadeIn');

    $(this).addClass('current animated fadeIn');
    $("#" + tab_id).addClass('current animated fadeIn');
  })

})
//Scroll to anchors
jQuery(function ($) {
  $('a[href*="#"]:not([href="#"])').click(function () {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
});
//Gridder

jQuery(function ($) {
	
	if(!$('.gridder').length){
		
		return;
		
	}

  // Call Gridder
  $('.gridder').gridderExpander({
    scroll: true,
    scrollOffset: 30,
    scrollTo: "panel", // panel or listitem
    animationSpeed: 400,
    animationEasing: "easeInOutExpo",
    showNav: true, // Show Navigation
    nextText: "Next", // Next button text
    prevText: "Previous", // Previous button text
    closeText: "Close", // Close button text
    onStart: function () {
      //Gridder Inititialized
    },
    onContent: function () {
      //Gridder Content Loaded
    },
    onClosed: function () {
      //Gridder Closed
    }
  });

});

// Toggle and Reveal
jQuery(function ($) {

  if (typeof $('.ds-video-headliner-description')[0] == "undefined" || $('.ds-video-headliner-description').length < 1) {

    var h = 0;

  } else {

    var h = $('.ds-video-headliner-description')[0].scrollHeight;

  }

  $('.ds-more').click(function (e) {
    e.stopPropagation();
    $('.ds-video-headliner-description').animate({
      'height': h
    })
  });

  $(document).click(function () {
    $('.ds-video-headliner-description').animate({
      'height': '100px'
    })
  });

});

//Limit Characters

jQuery(function ($) {
  $(".character-limit-90").each(function (i) {
    len = $(this).text().length;
    if (len > 90) {
      $(this).text($(this).text().substr(0, 90) + '...');
    }
  });
});

// Sharing POP UP

(function ($, window) {
  function windowPopup(url, width, height) {
    // Calculate the position of the popup so
    // it’s centered on the screen.
    var left = (screen.width / 2) - (width / 2),
      top = (screen.height / 2) - (height / 2);
    window.open(
      url,
      "_new",
      "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=" + width + ",height=" + height + ",top=" + top + ",left=" + left
    );
  }
  $(function () {
    $(".js-social-share").on("click", function (e) {
      console.log("FIRING");
      e.preventDefault();

      windowPopup($(this).attr("href"), 500, 300);
    });
  });
})(jQuery, window);


// Super quick and dirty lazy loader script
  function elementInViewport(el) {
    var top = el.offsetTop;
    var left = el.offsetLeft;
    var width = el.offsetWidth;
    var height = el.offsetHeight;

    while(el.offsetParent) {
      el = el.offsetParent;
      top += el.offsetTop;
      left += el.offsetLeft;
    }

    return (
      top < (window.pageYOffset + window.innerHeight) &&
      left < (window.pageXOffset + window.innerWidth) &&
      (top + height) > window.pageYOffset &&
      (left + width) > window.pageXOffset
    );
  }

  jQuery(window).scroll(function(){
    jQuery("img.lazy:not([src])").each(function(){
      console.log(jQuery(jQuery(this).parent().find('.ds-overlay')));
      jQuery(jQuery(this).parent().find('.ds-overlay')).hide();
      // If the element is in the viewport, set the image source
      if(elementInViewport(this)){
        jQuery(this).hide().attr('src', jQuery(this).attr('data-original')).fadeIn('slow');
        jQuery(this).parent().find('.ds-overlay').delay( 800 ).fadeIn('slow');
      }
    });
  });