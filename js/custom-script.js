//Tabs
$(document).ready(function () {

  $('ul.ds-tabs li').click(function () {
    var tab_id = $(this).attr('data-tab');

    $('ul.ds-tabs li').removeClass('current animated fadeIn');
    $('.ds-tab-content').removeClass('current animated fadeIn');

    $(this).addClass('current animated fadeIn');
    $("#" + tab_id).addClass('current animated fadeIn');
  })

})
//Scroll to anchors
$(function () {
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

$(function () {
	
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
$(function () {

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

$(function () {
  $(".character-limit-90").each(function (i) {
    len = $(this).text().length;
    if (len > 90) {
      $(this).text($(this).text().substr(0, 90) + '...');
    }
  });
});


//Lazy Load Thumbnail List

$(document).ready(function () {

  $(".ds-lazyload li").slice(21).hide();

  var mincount = 21;
  var maxcount = 40;


  $(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() >= $(document).height() - 10) {
      $(".ds-lazyload li").slice(mincount, maxcount).fadeIn(1200);

      $("#loading").fadeIn(100).delay(1000).fadeOut(100);

      mincount = mincount + 21;
      maxcount = maxcount + 21

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

