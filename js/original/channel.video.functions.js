var $ = jQuery;
$(document).ready(function() {
  var playerwrap = $('.ds-video-fluidMedia').first();
  var toggleFocusOn = false;
  var playerToggle = $('.ds-player-togglemode');
  var player = playerwrap.find('.player');
  if (player.length === 0) {
    return;
  }
  var minifyVid = player.attr('data-minifyvid') == '1' ? true : false;
  var autoRedir = player.attr('data-autoredir') == '1' ? true : false;
  var autoPlay = player.attr('data-autoplay') === '1' ? true : false;
  var enableRecPlaylist = player.attr('data-recplaylist') == '1' ? true : false;

  // adjustments to the theater mode playlist carousel and standard mode playlist

  $('.ds-owl-fa').remove(); // don't want the play buttons
  $('.owl-item div:nth-child(2)').remove(); // don't want the titles here

  populatePlaylist();

  if (getCurrentPlaylistMode() == 'std') {
    showPlaylistStandardMode();
  } else {
    showPlaylistTheaterMode();
  }

  if (!enableRecPlaylist) {
    $('.ds-playlist-theater-mode').html('');
    $('.ds-video').removeClass('ds-col-9').addClass('ds-col-12');
    $('.ds-playlist-standard-mode').remove();
    playerToggle.remove();
  } else {
    var currentPlaylistMode = getCurrentPlaylistMode();
    if(currentPlaylistMode == 'std') {
      setTimeout(function() {
        $('.ds-playlist-theater-mode').css({'display': 'none'});
      },1000);
    }
  }



  $('.owl-item').on('mousemove', function(e) {
    var tip = $('.carousel-tooltip');
    var posX = $(this).offset().left - $(this).width();
    if (tip.length != 0) {
      tip.css({
        left: posX
      })
    }
  });

  $('.rec-list-item').click(function(e) {
    e.preventDefault();

    var videoId = $(this).attr('href').replace('#', '');
    var strUrl = window.location.href;
    if (strUrl.indexOf('?') != -1) {
      strUrl = strUrl.split('?')[0];
    }
    window.location.assign(strUrl + '?video=' + videoId);

  });


  $('.owl-item').hover(
    function() {
      // setup
      if ($('.ds-playlist-theater-mode .carousel-tooltip').length == 0) {
        $('.ds-playlist-theater-mode').append('<div class="carousel-tooltip"></div>');
      }
      var tooltip = $('.carousel-tooltip');
      var parentContainer = $('.ds-playlist-theater-mode');
      var posX = $(this).offset().left - $(this).width();
      var posY = 20;
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
      });

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
      thumb.find('.owl-thumb').css('opacity', '0.7');
    }

  }


  function resizePlayer(scaleTransClass) {
    var container = $('.ds-video-headliner');
    var currentPlaylistMode = getCurrentPlaylistMode();
    var dsVideofluidMedia = $('.ds-video-fluidMedia').addClass(scaleTransClass);
    var player = dsVideofluidMedia.find('.player').addClass(scaleTransClass);
    var videoJS = player.find('.video-js').addClass(scaleTransClass);
    var dspVidJsPlayerHtml5Api = videoJS.find('#dsp-vid-js-player_html5_api').addClass(scaleTransClass);
    var wContainer = container.outerWidth();
    var playerWidth = wContainer;
    if(currentPlaylistMode == 'std') {
      playerWidth = playerWidth * 0.75;
    }
    var playerHeight = playerWidth * 0.5625;
    var playerSizeCSS = {'width': playerWidth, 'height':  playerHeight};

    dsVideofluidMedia.css(playerSizeCSS);
    player.css(playerSizeCSS);
    videoJS.css(playerSizeCSS);
    dspVidJsPlayerHtml5Api.css(playerSizeCSS);
    setTimeout(function() {
      dsVideofluidMedia.removeClass(scaleTransClass);
      player.removeClass(scaleTransClass);
      videoJS.removeClass(scaleTransClass);
      dspVidJsPlayerHtml5Api.removeClass(scaleTransClass);
    },1000);
  }


  $(window).resize(function() {
    var currentPlaylistMode = getCurrentPlaylistMode();
    if($(window).width() <= 800 && currentPlaylistMode == 'std') {
      showPlaylistTheaterMode();
      resizePlayer('scale-transition');
    } else {
      resizePlayer('no-scale-transition');
    }
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
    togglePlaylistMode();
  });

  function togglePlaylistMode() {
    var currentPlaylistMode = getCurrentPlaylistMode();
    if (currentPlaylistMode == 'std') {
      showPlaylistTheaterMode();
    } else {
      showPlaylistStandardMode();
    }
    resizePlayer('scale-transition');
  }



  if (minifyVid) {
    if (playerwrap.length === 0) {
      return;
    }



    var anibox = $('#anibox');
    var scroll_offset = 130;
    anibox.css(getTargetProps());


    $(window).scroll(function(e) {

      if ($(window).width() <= 800) {
        return;
      }

      var videoJS = player.find('.video-js');
      var vidObj = player.find('#dsp-vid-js-player_html5_api');
      var fullContainer = player.parent();
      var wFull = fullContainer.width();
      var wSmall = $(window).width() * 0.2;

      var vidFull = {
        width: wFull,
        height: wFull * .5625,
        boxShadow: 'none',
        outline: 0,
        position: 'inherit',
        right: 0,
        top: 0,
        borderTop: 'inherit'
      };

      var vidSmall = {
        zIndex: '10',
        width: wSmall,
        height: wSmall *.5625,
        boxShadow: '0 5px 2px rgba(0, 0, 0, 0.4)',
        outline: '3px solid #fff',
        position: 'fixed',
        top: 100,
        right: $(window).width() * 0.02
      };


      var scroll_top = $(this).scrollTop();
      if (scroll_top > (playerwrap.offset().top + scroll_offset) && !player.hasClass('onsidebar')) {
        player.hide();
        if (!$('.active-playlist').hasClass('ds-playlist-theater-mode')) {
          $('.active-playlist').hide();
        }
        anibox.show();
        var targetProps = getTargetProps();
        anibox.animate(targetProps, {
          duration: 500,
          complete: function() {
            videoJS.css({"width": vidSmall.width, "height": vidSmall.height});
            vidObj.css({"width": vidSmall.width, "height": vidSmall.height});
            player.show();
            anibox.hide();
          }
        });
        player.addClass('onsidebar').css(vidSmall);
      }

      if (scroll_top < (playerwrap.offset().top + scroll_offset) && player.hasClass('onsidebar')) {
        player.hide();
        anibox.show();

        var targetProps = getTargetProps();

        anibox.animate(targetProps, {
          duration: 500,
          complete: function() {
            videoJS.css({"width": vidFull.width, "height": vidFull.height});
            vidObj.css({"width": vidFull.width, "height": vidFull.height});
            player.show();
            anibox.hide();
            if (!$('.active-playlist').hasClass('ds-playlist-theater-mode')) {
              $('.active-playlist').show();
            }
          }
        });
        player.removeClass('onsidebar').css(vidFull);
      }

    });

    function getTargetProps() {
      var anibox = $('#anibox');
      var props = {};
      var vid = $('.player');
      var fullContainer = vid.parent();


      if (!anibox.hasClass('full-display')) {
        // expanding - return the full-size properties
        anibox.removeClass('mini-display').addClass('full-display');
        props.width = vid.width();
        props.height = vid.height();
        props.left = fullContainer.position().left;
        props.top = fullContainer.position().top;
        props['z-index'] = '1000';
      } else {

        // minifying - return the mini-size properties
        anibox.removeClass('full-display').addClass('mini-display');
        props.width = $(window).width() * 0.2;
        props.height = props.width * .5625;
        props.top = 100;
        props.left = $(window).width() - ($(window).width() * 0.2);
        props['z-index'] = '1000';
      }
      return props;
    }

    function showPlaylistStandardMode() {
      // show standard mode
      $('.ds-video').removeClass('ds-col-12').addClass('ds-col-9');
      $('.ds-playlist-standard-mode').addClass('active-playlist').addClass('scale-transition').addClass('opacity-transition-full').addClass('ds-col-3').removeClass('ds-col-0');
      $('.ds-playlist-theater-mode').removeClass('active-playlist').css({'display': 'none'});
      setCurrentPlaylistMode('std');
      setTimeout(function() {
        $('.ds-playlist-standard-mode').removeClass('scale-transition').removeClass('opacity-transition-full');
      },1000);
    }

    function showPlaylistTheaterMode() {
      // show theater mode
      $('.ds-video').removeClass('ds-col-9').addClass('ds-col-12');
      $('.ds-playlist-standard-mode').removeClass('active-playlist').addClass('scale-transition').addClass('opacity-transition-none').removeClass('ds-col-3').addClass('ds-col-0');
      $('.ds-playlist-theater-mode').addClass('active-playlist').css({'display': 'block'});
      setCurrentPlaylistMode('theater');
      $('.owl-carousel').trigger('refresh.owl.carousel');
      setTimeout(function() {
        $('.ds-playlist-standard-mode').removeClass('scale-transition').removeClass('opacity-transition-none');
      },1000);

    }

  }

  var i = 0;
  checkVidLoaded();



  function populatePlaylist() {

    // populate the right-hand side playlist with items from the Owl Carousel

    var carouselItems = $('.related-videos-carousel').find('a');

    if (carouselItems.length == 0) {
      enableRecPlaylist = false;
    } else {
      var strItemList = '';
      var strPlaylist = '<div class="ds-playlist-outer-container">'
      + '<div class="ds-playlist-inner-container">'
      + '<div><label>RELATED VIDEOS</label></div><ul>';
      $.each(carouselItems, function(key, val) {
        var itemName = $(this).attr('data-title');
        var itemDesc = $(this).attr('data-desc') != '' ? $(this).attr('data-desc') : 'No Description Currently Available';
        var videoId = $(this).attr('href');
        var imgSrc = $(this).find('img').attr('src');

        if (strItemList.indexOf(itemName) === -1) {
          // to make sure all list items are unique
          strItemList += itemName + ','

          strPlaylist += '<li>';
          strPlaylist += ' <a href="' + videoId + '" class="rec-list-item" data-title="' + itemName + '">' + '   <div class="playlist-item">';
          strPlaylist += '     <div class="playlist-img"><img src="' + imgSrc + '" /></div>';
          strPlaylist += '     <div class="playlist-info">';
          strPlaylist += '       <div class="playlist-title">' + itemName + '</div>';
          strPlaylist += '     </div>';
          strPlaylist += '   </div>';
          strPlaylist += ' </a>';
          strPlaylist += '</li>';
        }
      });

      strPlaylist += '</ul></div></div>';
      $('.ds-playlist-standard-mode').append(strPlaylist);
    }

  }

  function getStayInTheaterMode() {
       return $(window).width() <= 800;
  }


  function getCurrentPlaylistMode() {
    return sessionStorage.getItem('playlistMode') === 'std' ? 'std' : 'theater';
  }

  function setCurrentPlaylistMode(val) {
    sessionStorage.setItem('playlistMode', val);
  }


  function checkVidLoaded() {
    var max = 10;
    var vid = $('#dsp-vid-js-player_html5_api')[0];
    i++;

    if (vid == undefined && i < max) {
      setTimeout(function() {
        checkVidLoaded()
      }, 1000);
    } else {

      // resize the poster image
      var poster = $('.vjs-poster');
      var posterBkg = "";
      if($(poster).length > 0) {
        posterBkg = $(poster).css('background-image').replace('")', '/1000/562")');
        $(poster).css('background-image', posterBkg);
      }

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
          console.clear();
          console.log(aryVidList);
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


});
