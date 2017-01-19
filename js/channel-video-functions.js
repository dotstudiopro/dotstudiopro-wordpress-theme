var $ = jQuery;
$(document).ready(function() {
  var playerwrap = $('.ds-video-fluidMedia').first();
  var toggleFocusOn = false;
  var playerToggle = $('.ds-player-togglemode');


  var player = playerwrap.find('.player');
  if (player.length === 0) {
    return;
  }

  var minifyVid = player.attr('data-minifyVid') == '1' ? true : false;
  var autoRedir = player.attr('data-autoRedir') == '1' ? true : false;
  var autoPlay = player.attr('data-autoPlay') == '1' ? true : false;

  // adjustments to the theater mode playlist carousel and standard mode playlist

  $('.ds-owl-fa').remove(); // don't want the play buttons
  $('.owl-item div:nth-child(2)').remove(); // don't want the titles here
  $('.ds-playlist-standard-mode').css('height', $('.ds-playlist-standard-mode').parent().height());


  populatePlaylist();


  $('.owl-item').hover(
    function() {
      // setup
      if ($('.ds-playlist-theater-mode .carousel-tooltip').length == 0) {
        $('.ds-playlist-theater-mode').append('<div class="carousel-tooltip"></div>');
      }
      var tooltip = $('.carousel-tooltip');
      var parentContainer = $('.ds-playlist-theater-mode');
      var posX = $(this).offset().left - $(this).width();
      var posY = 0;
      var parentX = parentContainer.offset().left - $(this).width();
      var parentWidth = parentContainer.width();
      var maxRight = parentWidth - parentX;
      var tipWidth = $(this).width();
      var parentWidth = $('.ds-playlist-theater-mode').width();

      if (parentX >= posX) {
        posX = parentX + 20;
      } else if (posX > maxRight) {
        posX = maxRight - $(this).width() / 2;
      }

      var toolText = $(this).find('a').attr('data-title');
      tooltip.html(toolText);
      tooltip.css({
        left: posX,
        top: posY,
        'min-width': tipWidth
      })
      tooltip.fadeIn(800);

      // make the image grow 
      animateThumb($(this), 1);

    },


    function() {
      $('.carousel-tooltip').remove();
      animateThumb($(this), -1);
    }

  );

  function animateThumb(thumb, aniFactor) {

    var oThumbWidth = $('.owl-item').first().width() > $('.owl-item').last().width() ? $('.owl-item').first().width() : $('.owl-item').last().width();
    var oThumbHeight = $('.owl-item').first().height() > $('.owl-item').last().height() ? $('.owl-item').first().height() : $('.owl-item').last().height();

    var props = {};

    if (aniFactor == 1) {
      //props.width = oThumbWidth * 1.1;
      //props.height = oThumbHeight * 1.1;
      thumb.find('.owl-thumb').css('opacity', '1.0');
    } else {
      props.width = oThumbWidth;
      //props.height = oThumbHeight;
      thumb.find('.owl-thumb').css('opacity', '0.5');
    }

    /*
    thumb.animate(props, {
      duration: 200,
    });
    */
  }



  $(window).resize(function() {
    $('.ds-playlist-standard-mode').css('height', $('.video-js').height());
  });

  player.mouseenter(function() {
    // display the theater or standard mode button
    playerToggle.slideDown();
  });

  playerToggle.mouseenter(function() {
    // display the theater or standard mode button
    toggleFocusOn = true;
  });

  playerToggle.mouseleave(function() {
    // display the theater or standard mode button
    toggleFocusOn = false;
  });

  player.mouseleave(function() {
    // display the theater or standard mode button
    setTimeout(function() {
      if (!toggleFocusOn) {
        playerToggle.slideUp();
      }
    }, 100);
  });

  playerToggle.click(function() {
    if ($('.ds-playlist-standard-mode').hasClass('active-playlist')) {
      // show theater mode
      $('.ds-video').removeClass('col-md-8').addClass('col-md-12');
      $('.ds-playlist-standard-mode').removeClass('col-md-4').removeClass('active-playlist');
      $('.ds-playlist-theater-mode').addClass('active-playlist');
      $('.owl-carousel').trigger('refresh.owl.carousel');

    } else {
      // show standard mode
      $('.ds-video').removeClass('col-md-12').addClass('col-md-8');
      $('.ds-playlist-standard-mode').addClass('col-md-4').addClass('active-playlist');
      $('.ds-playlist-theater-mode').removeClass('active-playlist');
    }
    $(window).trigger('resize');
    $('.ds-playlist-standard-mode').css('height', $('.video-js').height());
  });



  if (minifyVid) {
    playerwrap.append('<div id="anibox">&nbsp;</div>');

    var anibox = $('#anibox');
    anibox.hide();
    var lt = playerwrap.offset().left;
    var aniboxFull = {
      'background-color': '#000',
      'width': player.parent().width(),
      'left': lt,
      'top': player.parent().position().top,
      'position': 'fixed',
      'z-index': '1000'
    };
    anibox.css(aniboxFull);


    if (playerwrap.length === 0) {
      return;
    }



    $(window).scroll(function(e) {
      //if ( ! sidebar.is(':visible') )
      //  return true;
      var vidWidth = $(window).width() * 0.2;
      var vidFull = {
        width: '100%',
        height: '100%',
        boxShadow: 'none',
        outline: 0,
        position: 'inherit',
        right: 0,
        top: 0,
        borderTop: 'inherit'
      };

      var vidSmall = {
        zIndex: '10',
        width: vidWidth,
        height: 'auto',
        boxShadow: '0 5px 2px rgba(0, 0, 0, 0.4)',
        outline: '3px solid #fff',
        position: 'fixed',
        top: 100,
        right: vidWidth * 0.1
      };


      var smLt = player.offset().left;
      var aniboxSmall = {
        'width': vidWidth,
        'height': vidWidth * 9 / 16,
        'top': 100,
        'outline': '3px solid #fff',
        'position': 'fixed',
        right: vidWidth * 0.1
      };

      aniboxFull.height = aniboxFull.width * .5625;



      var scroll_top = $(this).scrollTop();

      if (scroll_top > (playerwrap.offset().top + 300) && !player.hasClass('onsidebar')) {
        player.hide();
        anibox.show();
        if (!$('.active-playlist').hasClass('ds-playlist-theater-mode')) {
          $('.active-playlist').hide();
        }
        aniboxSmall.left = $(window).width() - 100;
        anibox.animate(aniboxSmall, {
          duration: 500,
          complete: function() {
            player.show();
            anibox.hide();
          }
        });

        player.addClass('onsidebar').css(vidSmall);
      }

      if (scroll_top < (playerwrap.offset().top + 300) && player.hasClass('onsidebar')) {
        player.hide();
        anibox.show()
        anibox.animate(aniboxFull, {
          duration: 500,
          complete: function() {
            player.show();
            anibox.hide();
            if (!$('.active-playlist').hasClass('ds-playlist-theater-mode')) {
              $('.active-playlist').show();
            }
          }
        });
        player.removeClass('onsidebar').css(vidFull);
      }

      $(window).trigger('resize');

    });

  }

  var i = 0;
  checkVidLoaded();

  function checkVidLoaded() {
    var max = 10;
    var vid = $('#dsp-vid-js-player_html5_api')[0];
    i++;

    if (vid == undefined && i < max) {
      setTimeout(function() {
        checkVidLoaded()
      }, 1000);
    } else {

      if (autoPlay) {
        try {
          vid.play();
        } catch (e) {
          console.log(e);
        }

      }

      // auto redirect functionality
      if (autoRedir) {
        vid.onended = function(e) {
          var aryVidList = $('ul.ds-video-thumbnails li');
          var aryURLs = [];

          $.each(aryVidList, function(key, val) {
            if ($(this).hasClass('selected')) {
              strToPush = 'selected'
            } else {
              strToPush = $(this).find('a').attr('href');
            }
            aryURLs.push(strToPush);
          });

          var vidIdx = aryURLs.indexOf('selected') + 1;
          var strURL = aryURLs[vidIdx];
          if (strURL == undefined) {
            strURL = aryURLs[0];
          }
          window.location.assign(strURL);
        }

      }

    }

  }


  function populatePlaylist() {

    // populate the right-hand side playlist with items from the Owl Carousel

    var carouselItems = $('.related-videos-carousel').find('a');
    var strItemList = '';
    var strPlaylist = '<div><label>RELATED VIDEOS</label></div><ul>'

    $.each(carouselItems, function(key, val) {
      var itemName = $(this).attr('data-title');
      var itemUrl = $(this).attr('href');
      var itemDesc = $(this).attr('data-desc');
      var imgSrc = $(this).find('img').attr('src');

      if (strItemList.indexOf(itemName) === -1) {
        // to make sure all list items are unique
        strItemList += itemName + ','

        strPlaylist += '<li>';
        strPlaylist += ' <a href="' + itemUrl + '">' + '   <div class="playlist-item">';
        strPlaylist += '     <div class="playlist-img"><img src="' + imgSrc + '" /></div>';
        strPlaylist += '     <div class="playlist-info">';
        strPlaylist += '       <div class="playlist-title">' + itemName + '</div>';
        strPlaylist += '       <div class="playlist-desc">' + itemDesc + '</div>';
        strPlaylist += '     </div>';
        strPlaylist += '   </div>';
        strPlaylist += ' </a>';
        strPlaylist += '</li>';
      }
    });

    strPlaylist += '</ul>';
    $('.ds-playlist-standard-mode').append(strPlaylist);

  }


});