(function($) {
    const $q = function(q, res) {
            if (document.querySelectorAll) {
                res = document.querySelectorAll(q);
            } else {
                var d = document,
                    a = d.styleSheets[0] || d.createStyleSheet();
                a.addRule(q, 'f:b');
                for (let l = d.all, b = 0, c = [], f = l.length; b < f; b++)
                    l[b].currentStyle.f && c.push(l[b]);

                a.removeRule(0);
                res = c;
            }
            return res;
        };

    function loadImage(el, fn) {
        const img = new Image(),
            src = el.getAttribute('data-src');
        img.onload = function() {
            if (!!el.parent)
                el.parent.replaceChild(img, el)
            else
                el.src = src;

            fn ? fn() : null;
        }
        img.src = src;
    }

    const images = document.querySelectorAll('img.lazy');

    const onIntersection = (entries, observer) => {
      // Loop through the entries
      entries.forEach(entry => {
        // Are we in viewport?
        if (entry.intersectionRatio > 0) {

          // Stop watching and load the image
          observer.unobserve(entry.target);
          loadImage(entry.target);
        }
      });
    }

    const config = {
      // If the image gets within 100px in the Y axis, start the download.
      rootMargin: '100px 0px',
      threshold: 0.01
    };

    // The observer for the images on the page
    let observer = new IntersectionObserver(onIntersection, config);
    images.forEach(image => {
        observer.observe(image);
    });

})(jQuery);