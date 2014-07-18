<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="<?php echo $sf_user->getCulture() ?>"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="<?php echo $sf_user->getCulture() ?>"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="<?php echo $sf_user->getCulture() ?>"> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9" lang="<?php echo $sf_user->getCulture() ?>"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="<?php echo $sf_user->getCulture() ?>"> <!--<![endif]-->
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link href="/favicon.ico" type="image/png" rel="icon">
    <?php include_component('default', 'javascriptCheck'); ?>
  </head>

  <?php $module = sfContext::getInstance()->getModuleName() ?>
  <?php $action = sfContext::getInstance()->getActionName() ?>
  <?php if ('default' == $module && 'index' == $action): ?>
  <body class="home page page-template page-template-page-home-php">
  <?php else: ?>
  <body class="page page-template page-template-page-fullwidth-php">
  <?php endif ?>
    <div id="motopress-main" class="main-holder">
      <?php include_partial('global/layout/default/header') ?>

      <div class="motopress-wrapper content-holder clearfix">
        <div class="container">
          <?php echo $sf_content ?>
        </div>
      </div>

      <?php include_partial('global/layout/default/footer') ?>
    </div>
    <div id="back-top-wrapper" class="visible-desktop">
      <p id="back-top"><a href="#top" onClick="_gaq.push(['_trackEvent', 'Click', 'Utility', 'To top page']);"><span></span></a></p>
    </div>

    <?php $hlpBroker->js->beginInlineJavascript(); ?>
      /* Init hyphenation */
      function initHyph() { Hyphenator.config({ displaytogglebox : true, minwordlength : 4 }); Hyphenator.run(); };

      /* Init navigation menu */
      function initPage()
      {
        jQuery('.sf-menu').mobileMenu({defaultText: "Выберите раздел..."});

        (function() {
          var myCamera = jQuery('#camera522c37bc81cb0');
          if (!myCamera.hasClass('motopress-camera')) {
            myCamera.addClass('motopress-camera');
            myCamera.camera({
              alignment           : 'topCenter',    /* topLeft, topCenter, topRight, centerLeft, center, centerRight, bottomLeft, bottomCenter, bottomRight */
              autoAdvance         : true,           /* true, false */
              mobileAutoAdvance   : true,           /* true, false. Auto-advancing for mobile devices */
              barDirection        : 'leftToRight',  /* 'leftToRight', 'rightToLeft', 'topToBottom', 'bottomToTop' */
              barPosition         : 'top',          /* 'bottom', 'left', 'top', 'right' */
              cols                : 12,
              easing              : 'easeOutQuad',  /* for the complete list http://jqueryui.com/demos/effect/easing.html */
              mobileEasing        : '',             /* leave empty if you want to display the same easing on mobile devices and on desktop etc. */
              fx                  : 'simpleFade',   /* 'random','simpleFade', 'curtainTopLeft', 'curtainTopRight', 'curtainBottomLeft', 'curtainBottomRight', 'curtainSliceLeft', 'curtainSliceRight', 'blindCurtainTopLeft', 'blindCurtainTopRight', 'blindCurtainBottomLeft', 'blindCurtainBottomRight', 'blindCurtainSliceBottom', 'blindCurtainSliceTop', 'stampede', 'mosaic', 'mosaicReverse', 'mosaicRandom', 'mosaicSpiral', 'mosaicSpiralReverse', 'topLeftBottomRight', 'bottomRightTopLeft', 'bottomLeftTopRight', 'bottomLeftTopRight'
                                                      you can also use more than one effect, just separate them with commas: 'simpleFade, scrollRight, scrollBottom' */
              mobileFx            : '',         /* leave empty if you want to display the same effect on mobile devices and on desktop etc. */
              gridDifference      : 250,        /* to make the grid blocks slower than the slices, this value must be smaller than transPeriod */
              height              : '100%',     /* here you can type pixels (for instance '300px'), a percentage (relative to the width of the slideshow, for instance '50%') or 'auto' */
              imagePath           : '/images/', /* he path to the image folder (it serves for the blank.gif, when you want to display videos) */
              loader              : 'no',       /* pie, bar, none (even if you choose "pie", old browsers like IE8- can't display it... they will display always a loading bar) */
              loaderColor         : '#ffffff',
              loaderBgColor       : '#eb8a7c',
              loaderOpacity       : 1,          /* 0, .1, .2, .3, .4, .5, .6, .7, .8, .9, 1 */
              loaderPadding       : 0,          /* how many empty pixels you want to display between the loader and its background */
              loaderStroke        : 3,          /* the thickness both of the pie loader and of the bar loader. Remember: for the pie, the loader thickness must be less than a half of the pie diameter */
              minHeight           : '147px',    /* you can also leave it blank */
              navigation          : true,       /* true or false, to display or not the navigation buttons */
              navigationHover     : false,      /* if true the navigation button (prev, next and play/stop buttons) will be visible on hover state only, if false they will be visible always */
              pagination          : false,
              playPause           : false,      /* true or false, to display or not the play/pause buttons */
              pieDiameter         : 33,
              piePosition         : 'rightTop', /* 'rightTop', 'leftTop', 'leftBottom', 'rightBottom' */
              portrait            : true,       /* true, false. Select true if you don't want that your images are cropped */
              rows                : 8,
              slicedCols          : 12,
              slicedRows          : 8,
              thumbnails          : false,
              time                : 7000,   /* milliseconds between the end of the sliding effect and the start of the next one */
              transPeriod         : 1500,   /* lenght of the sliding effect in milliseconds */

              /* callbacks */
              onEndTransition     : function() {  },  /* this callback is invoked when the transition effect ends */
              onLoaded            : function() {  },  /* this callback is invoked when the image on a slide has completely loaded */
              onStartLoading      : function() {  },  /* this callback is invoked when the image on a slide start loading */
              onStartTransition   : function() {  }   /* this callback is invoked when the transition effect starts */
            });
          }
        })();


        // main navigation init
        jQuery('ul.sf-menu').superfish({
          delay:       1000,    // the delay in milliseconds that the mouse can remain outside a sub-menu without it closing
          animation:   {opacity:'show',height:'show'}, // used to animate the sub-menu open
          speed:       'normal',  // animation speed 
          autoArrows:  false,   // generation of arrow mark-up (for submenu)
          disableHI: true // to disable hoverIntent detection
        });

        /* Zoom fix */
        //IPad/IPhone
        var viewportmeta = document.querySelector && document.querySelector('meta[name="viewport"]'), ua = navigator.userAgent,
            gestureStart = function () { viewportmeta.content = "width=device-width, minimum-scale=0.25, maximum-scale=1.6"; },
            scaleFix = function () {
              if (viewportmeta && /iPhone|iPad/.test(ua) && !/Opera Mini/.test(ua)) {
                viewportmeta.content = "width=device-width, minimum-scale=1.0, maximum-scale=1.0";
                document.addEventListener("gesturestart", gestureStart, false);
              }
            };

        scaleFix();
      };
    <?php $hlpBroker->js->endInlineJavascript(); ?>
    <?php //include_partial('global/block/counters'); ?>
  </body>
</html>