<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="/css/yui-combo-min.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
    <link href="/css/main.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
    <!--[if IE 5]><style type="text/css">#wrapper { width:828px!important; } #content .sidebar  { overflow:hidden; }</style><![endif]-->
    <!--[if lte IE 6]>
        <link rel="stylesheet" type="text/css" href="/css/ie6.css" media="screen,projection,print" />
        <script type="text/javascript" src="/js/fixpng.js"></script>
    <![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" href="/css/ie7.css" media="screen,projection,print" /><![endif]-->
  </head>
  <body class="yui-skin-sam">
    <div id="wrapper">
      <div id="header">
        <h1 class="logo"><a href="/"><span></span></a></h1>
      </div>

      <div id="content">
        <div class="content">
          <div class="profile">
            <div class="errorPage">
              <p class="error">500: Internal Server Error.</p>
              <p class="message">The requested page cannot be displayed.<br/>Please try again later.</p>

              <div class="nextStep nextStepPadLeft">
                <a href="javascript:void(0)" class="prevStepBt inputEx-Button-Link" onClick="history.back(); return false;">
                  <span>Back to previous page</span>
                </a>
                <a href="/" class="nextStepBt inputEx-Button-Link">
                  <span>Go to homepage</span>
                </a>
                <div style="clear: both;"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <div id="footer">
        <p class="copyright">&copy; <?php echo date('Y') ?> <?php echo $_SERVER['HOST_NAME'] ?></p>
      </div>
    </div>
  </body>
</html>