jQuery(document).ready(function ($) {
  //Add data to div
  $("[data-link]").on("click", function (e) {
    e.preventDefault();
    var did = $(this).data("link");
    $("div" + did).toggleClass("is-active");
    $(this).toggleClass("is-active");
    $(this).attr('aria-expanded', function (i, attr) {
      return attr == 'true' ? 'false' : 'true'
    });
  });

  //Active class to header on scroll
  $(window).on("scroll", function () {
    if ($(this).scrollTop() > 1) {
      $(".header").addClass("active");
    } else {
      $(".header").removeClass("active");
    }
  });

  //fixed header
  if (
    $("#wrapper-header").hasClass("header__fixed") &&
    !$("#page").hasClass("vertical")
  ) {
    $("#wrapper-header").css("position", "fixed");
  }
  var $header_wrapper = $("#wrapper-header").outerHeight();
  if (
    $("#wrapper-header").hasClass("header__fixed") &&
    !$("#wrapper-header").hasClass("transparent") &&
    !$("#page").hasClass("vertical")
  ) {
    $("#page-content").css("margin-top", $header_wrapper);
  }

  // close mobile menu when menu item clicked
  $(".mobile-menu__link").click(function () {
    $("#mobilemenu").toggleClass("is-active");
    $(".header__mobile-button").toggleClass("is-active");
  });

  // mobile menu logo wrapper height
  $(".mobile-menu__logo-wrap").css("min-height", $header_wrapper);

  // mobile menu jump focus to the close button after the last item

  function manageMenuFocus() {
    const menuButton = document.querySelector('button[data-link="#mobilemenu"]');
    const mobileMenu = document.querySelector('ul.mobile-menu__menu');
    if (!menuButton || !mobileMenu) return;
    const menuItems = mobileMenu.querySelectorAll('a, button, input, [tabindex="0"]');

    menuItems.forEach((item, index) => {
      item.addEventListener('keydown', (event) => {
        if (event.key === 'Tab' && !event.shiftKey) { // Check for Tab key without Shift
          if (index === menuItems.length - 1) { // If it's the last item in the menu
            event.preventDefault(); // Prevent default Tab behavior
            menuButton.focus(); // Move focus back to the button
          }
        }
      });
    });
  }
  manageMenuFocus();

  // Keep footer always down the page
  var $footer_404 = $("#wrapper-footer").innerHeight();
  var $header_404 = $("#wrapper-header").innerHeight();
  var $height_screen_404 = window.innerHeight;
  var $new_height_404 = $height_screen_404 - $header_404 - $footer_404;

  $("#page-content").css("min-height", $new_height_404);

  //submenu
  $(".menu-main-container ul li").mouseover(function () {
    $(this).find(".dropdown-menu").addClass("dropdown-menu--active");
  });

  $(".menu-main-container ul li").mouseleave(function () {
    $(this).find(".dropdown-menu").removeClass("dropdown-menu--active");
  });

  // When the user clicks on the button, scroll to the top of the document
  $("button#to-top").click(function () {
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return false;
  });

  //Open mobile submenu on click
  $(".mobile-menu__menu .menu-item-has-children").click(function () {
    $(this).toggleClass("mobile-menu__item--active");
  });

  $(".mobile-menu__menu .menu-item-has-children .mobile-menu__link").on("focus", function () {
    $(this).parent().addClass("mobile-menu__item--active");
  });


  // fullwidth Gutenberg elements

  function handleFullWidthSizing() {
    const scrollbarWidth = window.innerWidth - document.body.clientWidth;
    const margin = document.body.clientWidth / 2;
    const alignfullElements = document.querySelectorAll(".entry-content > .alignfull");

    alignfullElements.forEach((element) => {
      element.style.width = `calc(100vw - ${scrollbarWidth}px)`;
      element.style.marginLeft = `calc(${-margin}px + 50%)`;
    });
  }
  if (!document.getElementById("left-sidebar") && !document.getElementById("right-sidebar")) {
    handleFullWidthSizing();
    $(window).on("resize", function () {
      handleFullWidthSizing();
    });
  }
});

/**
 * PhotoSwipe Custom Icon Library
 * Contains SVG paths for different icon styles
 */
const photoswipeIconStyles = {
  // Default icons from PhotoSwipe library
  default: {
    arrow: '<path d="M29 43l-3 3-16-16 16-16 3 3-13 13 13 13z" id="pswp__icn-arrow"/>',
    close: '<path d="M24 10l-2-2-6 6-6-6-2 2 6 6-6 6 2 2 6-6 6 6 2-2-6-6z" id="pswp__icn-close"/>'
  },

  // Style 1 - Chevron arrow and X close
  style_1: {
    arrow: '<svg class="custom-icon" viewBox="0 0 240.823 240.823"><path d="M57.633,129.007L165.93,237.268c4.752,4.74,12.451,4.74,17.215,0c4.752-4.74,4.752-12.439,0-17.179l-99.707-99.671l99.695-99.671c4.752-4.74,4.752-12.439,0-17.191c-4.752-4.74-12.463-4.74-17.215,0L57.621,111.816C52.942,116.507,52.942,124.327,57.633,129.007z" id="pswp__icn-arrow"/></svg>',
    close: '<svg class="custom-icon" viewBox="0 0 32 32"><g stroke-width="2.5"><path d="m6.101 6.1 19.798 19.798"/><path d="m25.899 6.1-19.798 19.798"/></g></svg>'
  },

  // Style 2 - Circle with arrow and circle with X
  style_2: {
    arrow: '<svg class="custom-icon" viewBox="0 0 512 512"><path d="M256,0C114.837,0,0,114.837,0,256s114.837,256,256,256s256-114.837,256-256S397.163,0,256,0z M313.749,347.584c8.341,8.341,8.341,21.824,0,30.165c-4.16,4.16-9.621,6.251-15.083,6.251c-5.461,0-10.923-2.091-15.083-6.251L176.917,271.083c-8.341-8.341-8.341-21.824,0-30.165l106.667-106.667c8.341-8.341,21.824-8.341,30.165,0s8.341,21.824,0,30.165L222.165,256L313.749,347.584z" id="pswp__icn-arrow"/></svg>',
    close: '<svg class="custom-icon" viewBox="0 0 24 24"><path d="m12 1a11 11 0 1 0 11 11 11.013 11.013 0 0 0 -11-11zm4.242 13.829a1 1 0 1 1 -1.414 1.414l-2.828-2.829-2.828 2.829a1 1 0 0 1 -1.414-1.414l2.828-2.829-2.828-2.829a1 1 0 1 1 1.414-1.414l2.828 2.829 2.828-2.829a1 1 0 1 1 1.414 1.414l-2.828 2.829z" id="pswp__icn-close"/></svg>'
  }
};

// PhotoSwipe gallery
(function ($) {
  var gallery;
  // Init empty gallery array
  var container = {
    dataSource: [],
    showHideAnimationType: "none",
    bgOpacity: 1,
    showHideOpacity: true,
    closeOnScroll: false,
    index: 0,
  };

  const showCaptions = typeof minimalioLightboxSettings !== 'undefined' && minimalioLightboxSettings.showCaptions === 'yes';

  // Add padding function to container
  container.paddingFn = (viewportSize, itemData, index) => {
    const captionsEnabled = showCaptions && itemData.caption;
    return {
      top: viewportSize.x < 768 ? 50 : 20,
      bottom: captionsEnabled ? (viewportSize.x < 768 ? 120 : 80) : (viewportSize.x < 768 ? 60 : 20),
      left: viewportSize.x < 768 ? 0 : 70,
      right: viewportSize.x < 768 ? 0 : 70,
    };
  };

  // Define click event on gallery item
  $(".photoswipe-image").click(function (event) {
    // Prevent location change
    event.preventDefault();
    // Set container's index to the clicked item
    container.index = $(this)
      .parent("figure")
      .parent("div")
      .parent("div")
      .index();
    // Initialize PhotoSwipe
    initPhotoSwipeGallery();
  });
  $(document).ajaxStop(function () {
    // Define click event on gallery item after ajax calls (for load more)
    $(".photoswipe-image").click(function (event) {
      // Prevent location change
      event.preventDefault();
      // Set container's index to the clicked item
      container.index = $(this)
        .parent("figure")
        .parent("div")
        .parent("div")
        .index();
      // Initialize PhotoSwipe
      initPhotoSwipeGallery();
    });
  });
  function initPhotoSwipeGallery() {
    // Empty the dataSource array
    container.dataSource = [];
    // Loop over gallery items and push them to the dataSource array
    $(".pswp__wrap")
      .find("figure.post-card")
      .each(function () {
        var item;
        var isHtml = $(this)[0].classList.contains("photoswipe-html"); // check if we need to construct an HTML lightbox.
        var isVideo = $(this)[0].classList.contains("photoswipe-vimeo"); // check if we need to construct a video lightbox.
        var contentContainer = $("#content").attr("class");
        var $link = $(this).find("a");
        if (isHtml === true) {
          var card_content = $(this).find(".hidden_content");
          var content =
            card_content.length > 0 ? card_content[0].innerHTML : "";
          item = {
            html:
              '<div class="photoswipe-html ' +
              contentContainer +
              '" style="height:100vh; height:100svh; overflow:hidden;display: flex; align-items:center;">' +
              '<div class="photoswipe-html-inner" style="width:100%;">' +
              '<div class = "photoswipe-html-content"><p> ' +
              content +
              " </div></p>" +
              " </div>" +
              "</div>",
          };
          container.dataSource.push(item);
        } else if (isVideo === true) {
          var vimeo = $(this).find(".hidden_vimeo");
          var videoId = vimeo.length > 0 ? vimeo[0].innerText.trim() : "";
          if (videoId !== "") {
            item = {
              html:
                '<div class="photoswipe-vimeo ' +
                contentContainer +
                '" style="display: flex; justify-content: center; align-items: center; width: 1500px; max-width: 100%; height: 100vh; margin: 0 auto;">' +
                '<div style="padding:56.25% 0 0 0;position:relative;width: 100%;"><iframe src="https://player.vimeo.com/video/' +
                videoId +
                '?badge=0&amp;autopause=0&amp;quality_selector=1&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;" title=""></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>' +
                "</div>",
            };
          } else {
            item = {
              html:
                '<div class="photoswipe-vimeo" style="display: flex; justify-content: center; align-items: center;">' +
                '<p style="position: absolute; top: 0; top: 50%; margin-top: -50px;">Video is coming soon.</p>' +
                "</div>",
            };
          }
          container.dataSource.push(item);
        }
        else {
          // Get caption from figcaption element if it exists
          var captionText = $(this).find("figcaption").text().trim();
          var $width = $link.data("width");
          var $height = $link.data("height");
          var $src = $link.attr("href");

          // Check for lazy-loaded images - look for the actual image src in data attributes
          var $img = $(this).find("img");
          if ($img.length > 0) {
            // Try common lazy loading data attributes (covers most plugins)
            var lazyLoadSrc = $img.attr("data-src") ||
                             $img.attr("data-lazy-src") ||
                             $img.attr("data-original") ||
                             $img.attr("data-srcset") ||
                             $img.attr("data-lazy");

            // If we find a lazy-load src and the link href is a placeholder, use the lazy-load src
            if (lazyLoadSrc && ($src.indexOf("placeholder") > -1 || $src.indexOf("data:image") > -1 || $src === lazyLoadSrc)) {
              $src = lazyLoadSrc;
            }

            // Also check if dimensions are stored in data attributes
            if (!$width || !$height) {
              $width = $width || $img.attr("data-width") || $img.data("width");
              $height = $height || $img.attr("data-height") || $img.data("height");
            }
          }

          // If dimensions exist, use them immediately (fast path)
          if ($width && $height) {
            item = {
              src: $src,
              w: $width,
              h: $height,
              caption: captionText || "",
            };
            container.dataSource.push(item);
          }
          else {
            // Dimensions missing - load image to get natural dimensions (fallback)
            var img = new Image();
            img.onload = function () {
              item = {
                src: $src,
                w: this.naturalWidth,
                h: this.naturalHeight,
                caption: captionText || "",
              };
              container.dataSource.push(item);
            };
            img.onerror = function () {
              // Fallback to default dimensions if image fails to load
              item = {
                src: $src,
                w: 1200,
                h: 800,
                caption: captionText || "",
              };
              container.dataSource.push(item);
            };
            img.src = $src;
          }
        }
      });
    if (typeof lbwpsInit !== "undefined") {
      lbwpsInit();
    }

    // Create PhotoSwipe instance
    gallery = new PhotoSwipe(container);

    // Register caption UI element before initializing
    if (showCaptions) {
      gallery.on('uiRegister', function () {
        gallery.ui.registerElement({
          name: 'custom-caption',
          order: 9,
          isButton: false,
          appendTo: 'root',
          onInit: (el, pswp) => {
            pswp.on('change', () => {
              if (pswp.currSlide && pswp.currSlide.data) {
                var currentItem = pswp.currSlide.data;
                el.innerHTML = currentItem.caption || '';
              }
            });
          }
        });
      });
    }

    // Get icon style from localized PHP variable
    const iconStyle = (typeof minimalioLightboxSettings !== 'undefined' && minimalioLightboxSettings.iconStyle)
      ? minimalioLightboxSettings.iconStyle
      : 'default';

    // Replace icons after PhotoSwipe opens (only if not using default)
    if (iconStyle !== 'default') {
      const customIcons = photoswipeIconStyles[iconStyle];
      gallery.on('openingAnimationEnd', function () {
        // Replace arrow prev icon
        const arrowPrev = document.querySelector('.pswp__button--arrow--prev .pswp__icn');
        if (arrowPrev) arrowPrev.outerHTML = customIcons.arrow;

        // Replace arrow next icon
        const arrowNext = document.querySelector('.pswp__button--arrow--next .pswp__icn');
        if (arrowNext) {
          arrowNext.outerHTML = customIcons.arrow;
          const svg = document.querySelector('.pswp__button--arrow--next svg');
          if (svg) svg.style.transform = 'scaleX(-1)';
        }

        // Replace close icon
        const closeIcon = document.querySelector('.pswp__button--close .pswp__icn');
        if (closeIcon) closeIcon.outerHTML = customIcons.close;
      });
    }

    gallery.init();

    // gallery.listen("destroy", function () {
    //   setTimeout(function () {
    //     $(".pswp").removeClass().addClass("pswp");
    //   }, 100);
    // });
  }
})(jQuery);

// Init PhotoSwipe lightBox on Gutenberg image gallery.
(function ($) {
  const showCaptions = typeof minimalioLightboxSettings !== 'undefined' && minimalioLightboxSettings.showCaptions === 'yes';

  $(".wp-block-gallery").each(function () {
    var $pic = $(this),
      getItems = function () {
        var items = {
          dataSource: [],
          showHideAnimationType: "none",
          bgOpacity: 1,
          showHideOpacity: true,
          closeOnScroll: false,
          initialZoomLevel: "fit",
          secondaryZoomLevel: "fit",
          maxZoomLevel: "fit",
          zoom: false,
          paddingFn: (viewportSize, itemData, index) => {
            const captionsEnabled = showCaptions && itemData.caption;
            return {
              // check based on slide index
              top: viewportSize.x < 768 ? 50 : 20,
              // check based on viewport size - add more bottom padding if captions are enabled
              bottom: captionsEnabled ? (viewportSize.x < 768 ? 120 : 80) : (viewportSize.x < 768 ? 60 : 20),
              // check based on image size
              left: viewportSize.x < 768 ? 0 : 70,
              right: viewportSize.x < 768 ? 0 : 70,
            };
          },
          index: 0,
        };
        $pic.find(".wp-block-image").each(function () {
          var $img = $(this).find("img");
          var $src = $img.attr("src"),
            $width = $img.attr("width"),
            $height = $img.attr("height");

          // Check for lazy-loaded images - look for the actual image src in data attributes
          var lazyLoadSrc = $img.attr("data-src") ||
                           $img.attr("data-lazy-src") ||
                           $img.attr("data-original") ||
                           $img.attr("data-srcset") ||
                           $img.attr("data-lazy");

          // If we find a lazy-load src and the current src is a placeholder, use the lazy-load src
          if (lazyLoadSrc && ($src.indexOf("placeholder") > -1 || $src.indexOf("data:image") > -1)) {
            $src = lazyLoadSrc;
          }

          // Also check if dimensions are stored in data attributes
          if (!$width || !$height) {
            $width = $width || $img.attr("data-width") || $img.data("width");
            $height = $height || $img.attr("data-height") || $img.data("height");
          }

          // Get caption from figcaption element if it exists
          var captionText = $(this).find("figcaption").text().trim();

          // If dimensions exist, use them immediately (fast path)
          if ($width && $height) {
            var item = {
              src: $src,
              w: $width,
              h: $height,
              caption: captionText || "",
            };
            items.dataSource.push(item);
          } else {
            // Dimensions missing - load image to get natural dimensions (fallback)
            var img = new Image();
            img.onload = function () {
              var item = {
                src: $src,
                w: this.naturalWidth,
                h: this.naturalHeight,
                caption: captionText || "",
              };
              items.dataSource.push(item);
            };
            img.onerror = function () {
              // Fallback to default dimensions if image fails to load
              var item = {
                src: $src,
                w: 1200,
                h: 800,
                caption: captionText || "",
              };
              items.dataSource.push(item);
            };
            img.src = $src;
          }
        });
        return items;
      };
    var items = getItems();

    $pic.on("click", "figure", function (event) {
      event.preventDefault();
      items.index = $(this).index();

      // Initialize PhotoSwipe
      var lightBox = new PhotoSwipe(items);

      // Register caption UI element before initializing
      if (showCaptions) {
        lightBox.on('uiRegister', function () {
          lightBox.ui.registerElement({
            name: 'custom-caption',
            order: 9,
            isButton: false,
            appendTo: 'root',
            onInit: (el, pswp) => {
              pswp.on('change', () => {
                if (pswp.currSlide && pswp.currSlide.data) {
                  var currentItem = pswp.currSlide.data;
                  el.innerHTML = currentItem.caption || '';
                }
              });
            }
          });
        });
      }

      // Get icon style from localized PHP variable
      const iconStyle = (typeof minimalioLightboxSettings !== 'undefined' && minimalioLightboxSettings.iconStyle)
        ? minimalioLightboxSettings.iconStyle
        : 'default';

      // Replace icons after PhotoSwipe opens (only if not using default)
      if (iconStyle !== 'default') {
        const customIcons = photoswipeIconStyles[iconStyle];
        lightBox.on('openingAnimationEnd', function () {
          // Replace arrow prev icon
          const arrowPrev = document.querySelector('.pswp__button--arrow--prev .pswp__icn');
          if (arrowPrev) arrowPrev.outerHTML = customIcons.arrow;

          // Replace arrow next icon
          const arrowNext = document.querySelector('.pswp__button--arrow--next .pswp__icn');
          if (arrowNext) {
            arrowNext.outerHTML = customIcons.arrow;
            const svg = document.querySelector('.pswp__button--arrow--next svg');
            if (svg) svg.style.transform = 'scaleX(-1)';
          }

          // Replace close icon
          const closeIcon = document.querySelector('.pswp__button--close .pswp__icn');
          if (closeIcon) closeIcon.outerHTML = customIcons.close;
        });
      }

      lightBox.init();
    });
  });
})(jQuery);

//Get the url parameters
function getURLParameter(name) {
  return (
    decodeURIComponent(
      (new RegExp("[?|&]" + name + "=" + "([^&;]+?)(&|#|;|$)").exec(
        location.search
      ) || [null, ""])[1].replace(/\+/g, "%20")
    ) || null
  );
}

function hoverVideoInit() {
  let postcards = document.querySelectorAll(".post-card");
  let postCardsArray = [...postcards];

  postCardsArray.forEach((postcard) => {
    let video = postcard.getElementsByClassName("portfolio-hover-video");

    if (video[0] != undefined) {
      postcard.addEventListener("mouseenter", () => {
        video[0].play();
      });

      postcard.addEventListener("mouseleave", () => {
        video[0].pause();
        video[0].currentTime = 0;
      });
    }
  });
}

//Change the URL to our anchor on portfolio category click
window.onload = function () {
  var hash = document.location.hash.substring(1);
  if (hash.length == 0) {
    var anc = getURLParameter("category");
    if (anc) {
      var anc_clean = anc.replace(/\\|\//g, "");

      jQuery(".posts-ajax__categories-wrapper")
        .find('[data-label="' + anc_clean + '"]')
        .trigger("click");
    }
  }
};

window.onload = function () {
  hoverVideoInit();
}

document.querySelectorAll('[role="button"]')?.forEach(function (button) {
  button.addEventListener('keydown', function (e) {
    const keyD = e.key !== undefined ? e.key : e.keyCode;
    if ((keyD === 'Enter' || keyD === 13) || (['Spacebar', ' '].indexOf(keyD) >= 0 || keyD === 32)) {

      e.preventDefault();
      this.click();
    }
  });
});

document.querySelectorAll('[role="button"]')?.forEach(function (button) {
  button.addEventListener('keydown', function (e) {
    const keyD = e.key !== undefined ? e.key : e.keyCode;
    if ((keyD === 'Enter' || keyD === 13) || (['Spacebar', ' '].indexOf(keyD) >= 0 || keyD === 32)) {

      e.preventDefault();
      this.click();
    }
  });
});
//Load JS before Ajax
export function masoneryEffect() {

  /**
   * Set appropriate spanning to any masonry item
   *
   * @param item Object A brick/tile/cell inside the masonry
   */
  function resizeMasonryItem(item) {
    /* Get the grid object, its row-gap, and the size of its implicit rows */
    var grid = document.getElementsByClassName('grid')[0],

      rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-row-gap')),
      rowHeight = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-auto-rows'));

    /*
    * Spanning for any brick = S
    * Grid's row-gap = G
    * Size of grid's implicitly create row-track = R
    * Height of item content = H
    * Net height of the item = H1 = H + G
    * Net height of the implicit row-track = T = G + R
    * S = H1 / T
    */
    var rowSpan = Math.ceil((item.querySelector('.post-card ').getBoundingClientRect().height + rowGap) / (rowHeight + rowGap));

    /* Set the spanning as calculated above (S) */
    item.style.gridRowEnd = 'span ' + rowSpan;
  }

  /**
   * Apply spanning to all the masonry items
   *
   * Loop through all the items and apply the spanning to them using
   * `resizeMasonryItem()` function.
   *
   * @uses resizeMasonryItem
   */
  function resizeAllMasonryItems() {
    // Get all item class objects in one list
    var allItems = document.getElementsByClassName('grid-item');

    /*
    * Loop through the above list and execute the spanning function to
    * each list-item (i.e. each masonry item)
    */
    for (var i = 0; i < allItems.length; i++) {
      resizeMasonryItem(allItems[i]);
    }
  }

  /**
   * Resize the items when all the images inside the masonry grid
   * finish loading. This will ensure that all the content inside our
   * masonry items is visible.
   *
   * @uses ImagesLoaded
   * @uses resizeMasonryItem
   */
  function waitForImages() {
    var allItems = document.getElementsByClassName('grid-item');
    for (var i = 0; i < allItems.length; i++) {
      imagesLoaded(allItems[i], function (instance) {
        var item = instance.elements[0];
        resizeMasonryItem(item);
      });
    }
  }

  /* Resize all the grid items on the load and resize events */
  var masonryEvents = ['load', 'resize'];
  masonryEvents.forEach(function (event) {
    window.addEventListener(event, resizeAllMasonryItems);
  });

  /* Do a resize once more when all the images finish loading */
  waitForImages();

}

/** Initiate the function */
if (document.getElementsByClassName('grid').length > 0) {
  masoneryEffect();
}
//Load JS before Ajax
export function masoneryGalleryEffect(
  gridItem = 'wp-block-image',
  gridElement = 'wp-block-minimalio-blocks-minimalio-gallery masonry',
  columnGapElement = 'column-gap'
) {
  /**
  * Set appropriate spanning to any masonry item
  *
  * @param item Object A brick/tile/cell inside the masonry
  */
  function resizeMasonryItem(item, grid) {
    var rowHeight = 1;   // grid-auto-rows: 1px (CSS should have row-gap: 0)

    // Get column-gap value from CSS to use as row gap
    var rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue(columnGapElement));

    /*
    * With row-gap: 0 in CSS, we add the gap manually
    * contentHeight + desired gap, then divide by rowHeight
    */
    var contentHeight = item.getBoundingClientRect().height;

    var rowSpan = Math.ceil(contentHeight / rowHeight) + rowGap;

    /* Set the spanning as calculated above (S) */
    item.style.gridRowEnd = 'span ' + rowSpan;
  }

  /**
   * Apply spanning to all the masonry items
   *
   * Loop through all the items and apply the spanning to them using
   * `resizeMasonryItem()` function.
   *
   * @uses resizeMasonryItem
   */
  function resizeAllMasonryItems() {
    // Get all masonry galleries
    var masonryGalleries = document.querySelectorAll('.' + gridElement.split(' ').join('.'));

    // Loop through each masonry gallery
    for (var g = 0; g < masonryGalleries.length; g++) {
      var gallery = masonryGalleries[g];
      // Get items only within this specific masonry gallery
      var allItems = gallery.getElementsByClassName(gridItem);

      /*
      * Loop through the above list and execute the spanning function to
      * each list-item (i.e. each masonry item)
      */
      for (var i = 0; i < allItems.length; i++) {
        resizeMasonryItem(allItems[i], gallery);
      }
    }
  }

  /**
   * Resize the items when all the images inside the masonry grid
   * finish loading. This will ensure that all the content inside our
   * masonry items is visible.
   *
   * @uses ImagesLoaded
   * @uses resizeMasonryItem
   */
  function waitForImages() {
    // Get all masonry galleries
    var masonryGalleries = document.querySelectorAll('.' + gridElement.split(' ').join('.'));

    // Loop through each masonry gallery
    for (var g = 0; g < masonryGalleries.length; g++) {
      var gallery = masonryGalleries[g];
      // Get items only within this specific masonry gallery
      var allItems = gallery.getElementsByClassName(gridItem);

      for (var i = 0; i < allItems.length; i++) {
        (function(currentGallery) {
          imagesLoaded(allItems[i], function (instance) {
            var item = instance.elements[0];
            resizeMasonryItem(item, currentGallery);
          });
        })(gallery);
      }
    }
  }

  /* Resize all the grid items on the load and resize events */
  var masonryEvents = ['load', 'resize'];
  masonryEvents.forEach(function (event) {
    window.addEventListener(event, resizeAllMasonryItems);
  });

  /* Do a resize once more when all the images finish loading */
  waitForImages();

}

/** Initiate the function */
if (document.querySelector('.grid[data-grid="masonry"]')) {
  masoneryGalleryEffect();
}
import { masoneryEffect } from './masonery.js';
jQuery(document).ready(function($){

    //Base variables
    var card = $('.posts-ajax__posts').data('card');
    var cardAuto =  $('.posts__posts-infinite').data('ajax');
    var grid = $('.posts-ajax__posts').data('grid');
    var author = $('.posts-ajax__posts').data('author');
    var nr_columns = $('.posts-ajax__posts').data('columns');
    var nr_posts = $('.posts-ajax__posts').data('posts_number');


    function hoverVideoInit() {
      let postcards = document.querySelectorAll(".post-card");
      let postCardsArray = [...postcards];
    
      postCardsArray.forEach((postcard) => {
        let video = postcard.getElementsByClassName("portfolio-hover-video");
    
        if (video[0] != undefined) {
          postcard.addEventListener("mouseenter", () => {
            video[0].play();
          });
    
          postcard.addEventListener("mouseleave", () => {
            video[0].pause();
            video[0].currentTime = 0;
          });
        }
      });
    }
  

    /** Load more button */
    function loadMorePosts() {
        $('.my-posts').on('click', '#load-more-ajax', function (e) {
            e.preventDefault();
            var parent = $(this).parent();
            var parent_wrap = parent.parent();
            var current_cards = parent_wrap.find('.post-card');
            var post_type = parent_wrap.data('type');
            var current_ids = '';
            $(current_cards).each(function(i) {
                if(i < current_cards.length - 1) {
                    var separator = ', ';
                } else {
                    var separator = '';
                }
                current_ids += $(this).data('card-id')+separator;

            });

            //Get just the active category
            var target = $('.posts-ajax__tab.checked').data('label');
            //Get the total amount of posts for current category
            var countPosts = $('.posts-ajax__tab.checked').data('number');

            if(typeof target == 'undefined'){
              target = 'all';
              countPosts = $('.posts-ajax__posts').data('number');
            }

            var data = {
              'action': 'load',
              'card': card,
              'grid': grid,
              'author': author,
              'nr_columns': nr_columns,
              'nr_posts': nr_posts,
              'category': target,
              'exclude': current_ids,
              'post_type': post_type,
              'nonce': wpAjaxLoad.nonce
            };

            $.ajax({
              url : wpAjaxLoad.ajax_loadUrl,
              data : data,
              type : 'POST',
              success : function( result_more) {
                  $('.posts__row').append( result_more ).hide().fadeIn();

                  if(current_cards.length + nr_posts >= countPosts) {
                    $('#load-more-ajax').remove();
                  }

                  /** Initiate maspnery the function */
                  masoneryEffect();

                  hoverVideoInit();
              }
            });

            e.stopPropagation();
        });
    }

    //Initiat load more
    loadMorePosts();

    /** Load more on scroll */
    var winCached = $(window), docCached = $(document);
    var loadingSpinner = $('.lds-dual-ring');
    var lastScroll = $('.posts-ajax__posts').scrollTop();

    /** This works with current layout, when we know exactly what elements come after
    the posts/portfolio section (.footer__minimalio, .footer__minimalio).
    var footerMinimalio = $('.footer__minimalio').length > 0 ? $('.footer__minimalio').height() : 0;
    var footerWidgets = $('.footer__widgets').length > 0 ? $('.footer__widgets').height() : 0;
    var footerTotal = footerMinimalio + footerWidgets;
    **/

    /** We need to get all elements that come after the .posts-ajax:
    1. siblings of .posts-ajax if any
    2. siblings of the main.site-content (footer element will be here)
    **/

    var siblings = $(".posts-ajax").nextAll();
    var parentSiblings = $(".site-content").nextAll();
    var siblingsHeight = siblings.length > 0 ? siblings.height() : 0;
    var parentSiblingsHeight = parentSiblings.length > 0 ? parentSiblings.height() : 0;

    var footerTotal = siblingsHeight + parentSiblingsHeight;

    function loadMoreOnScroll() {
      dettachScrollEvent();
      if (cardAuto) {
        $(document)
        .ajaxStart(function () {
          loadingSpinner.show();
        })
        .ajaxStop(function () {
          loadingSpinner.hide();
        });
      setTimeout(function() { //this timeout simulates the delay from the ajax post
        // ajax call get data from server and append to the div
   if (($(this).scrollTop() > lastScroll)){
        var parent = $('.posts__row').parent(); //Get just the active category
        var target = $('.posts-ajax__tab.checked').data('label');
        var countPosts = $('.posts-ajax__tab.checked').data('number');
        if(typeof target == 'undefined'){
          target = 'all';
          countPosts = $('.posts-ajax__posts').data('number');
        }
        /** Execute just f there is the right block */
        if (parent) {
          var current_cards = parent.find('.post-card');
          var post_type = parent.data('type');
          var current_ids = '';
          $(current_cards).each(function (i) {
            if (i < current_cards.length - 1) {
              var separator = ', ';
            } else {
              var separator = '';
            }
            current_ids += $(this).data('card-id') + separator;
          }); //console.log(current_ids);
          if (current_cards.length + 1 <= countPosts) {
          var data = {
            'action': 'load',
            'card': card,
            'grid': grid,
            'author': author,
            'nr_columns': nr_columns,
            'nr_posts': nr_posts,
            'category': target,
            'exclude': current_ids,
            'post_type': post_type,
            'nonce': wpAjaxLoad.nonce
          };

          $.ajax({
            url: wpAjaxLoad.ajax_loadUrl,
            data: data,
            type: 'POST',
            success: function success(result_more) {
              // console.log(data);
              $('.posts__row').append(result_more);
              if(current_cards.length + nr_posts >= countPosts) {
                $('#load-more-ajax').remove();
              }
              /** Initiate maspnery the function */
              masoneryEffect();
              hoverVideoInit();
            },
          });
          }
          }
        }
        attachScrollEvent();
      }, 500);
    }
    }

    function infiNLoader() {
    if ((winCached.scrollTop() + winCached.height() > docCached.height() - footerTotal) - 100)  {
      loadMoreOnScroll();
      lastScroll = $(this).scrollTop();
      //alert("near bottom! Adding more dummy content for infinite scrolling");
    }
  }

  function attachScrollEvent() {
    winCached.scroll(infiNLoader);
  }

  function dettachScrollEvent() {
    winCached.unbind('scroll', infiNLoader);
  }

  loadMoreOnScroll();

  /** End Load more on scroll */


    $( document ).ajaxStop(function() {
        //Initiat load more after filter
        //loadMorePosts();
        //Initiate load more on scroll afte filter
        //loadMoreOnScroll();


      // Init empty gallery array
      var container = [];

      // Loop over gallery items and push it to the array
      $('.posts__row').find('figure').each(function() {
        var $link = $(this).find('a'),
          item = {
            src: $link.attr('href'),
            w: $link.data('width'),
            h: $link.data('height'),
            title: $link.data('caption')
          };
        container.push(item);
      });

      // Define click event on gallery item
      $('.photoswipe-image').click(function(event) {

        // Prevent location change
        event.preventDefault();

        // Define object and gallery options
        var $pswp = $('.pswp')[0],
          options = {
            index: $(this).parent('figure').parent('div').index(),
            bgOpacity: 0.85,
            showHideOpacity: true
          };

        // Initialize PhotoSwipe
        var gallery = new PhotoSwipe($pswp, PhotoSwipeUI_Default, container, options);
        gallery.init();
      });


    });

});

import { masoneryEffect } from './masonery.js';
jQuery(document).ready(function ($) {

  //Base variables
  var card = $('.posts-ajax__posts').data('card');
  var cardAuto = $('.posts__posts-infinite').data('ajax');
  var grid = $('.posts-ajax__posts').data('grid');
  var author = $('.posts-ajax__posts').data('author');
  var nr_columns = $('.posts-ajax__posts').data('columns');
  var nr_posts = $('.posts-ajax__posts').data('posts_number');
  var target = 'all';
  var post_type = $('.my-posts').data('type');

  function hoverVideoInit() {
    let postcards = document.querySelectorAll(".post-card");
    let postCardsArray = [...postcards];

    postCardsArray.forEach((postcard) => {
      let video = postcard.getElementsByClassName("portfolio-hover-video");

      if (video[0] != undefined) {
        postcard.addEventListener("mouseenter", () => {
          video[0].play();
        });

        postcard.addEventListener("mouseleave", () => {
          video[0].pause();
          video[0].currentTime = 0;
        });
      }
    });
  }

  $('input[type=radio][name=category]').change(function () {

    target = $(this).val();
    var countPosts = $(this).parent().data('number');


    //alert(target);
    $('[data-filter]').find($('[data-label]')).not('[data-label="' + target + '"]').removeClass('checked');

    $(this).toggleClass('checked');
    $('[data-filter]').parent().find($('[data-label="' + target + '"]')).addClass('checked');


    var data = {
      'action': 'filter',
      'card': card,
      'grid': grid,
      'author': author,
      'nr_columns': nr_columns,
      'nr_posts': nr_posts,
      'category': target,
      'load': 0,
      'post_type': post_type,
      'nonce': wpAjaxLoad.nonce
    };


    $.ajax({
      url: wpAjaxLoad.ajax_loadUrl,
      data: data,
      type: 'POST',
      success: function (result) {
        $('.my-posts').html(result).hide().fadeIn();
        // console.log(nr_posts, '-->', countPosts)
        setTimeout(function () {
          if (nr_posts >= countPosts || nr_posts === -1) {
            $('#load-more-ajax').remove();
          }

          /** Initiate maspnery the function */
          masoneryEffect();

        }, 200);

        hoverVideoInit();
      }
    });

  });

});
