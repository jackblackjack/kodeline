<?php decorate_with(dirname(__FILE__) . '/defaultLayout.php') ?>

<style>
html, body {
  width: 100%;
  height: 100%;
  background: #F7F7F7;
  padding: 0px;
  margin: 0px;
}
#bad_browser {
  left: 50%;
  top: 50%;
  text-align: center;
  width: 530px;
  margin: -200px 0px 0px -250px;
  background: #FFF;
  line-height: 180%;
  border-bottom: 1px solid #E4E4E4;
  -webkit-box-shadow: 0 0 3px rgba(0, 0, 0, 0.15);
  -moz-box-shadow: 0 0 3px rgba(0, 0, 0, 0.15);
  box-shadow: 0 0 3px rgba(0, 0, 0, 0.15);
}
#content {
  padding: 20px;
  font-size: 1.19em;
}
#head {
  behavior: url(/js/iepngfix.htc?1);
  height: 59px;
  background: #587EA3 url(/images/flat_logo.png) no-repeat 18px 50%;
}
#content div {
  margin: 10px 0 15px 0;
}
#content #browsers {
  width: 480px;
  height: 136px;
  margin: 15px auto 0px;
}
#browsers a {
  behavior: url(/js/iepngfix.htc?1);
  float: left;
  width: 120px;
  height: 20px;
  padding: 106px 0px 13px 0;
  -webkit-border-radius: 4px;
  -khtml-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
}
#browsers a:hover {
  text-decoration: none;
  background-color: #edf1f5!important;
}
.is_2x #head {
  background-image: url(/images/flat_logo_2x.png);
  background-size: 132px 26px;
}
.is_2x #browsers a  {
  background-size: 80px 80px!important;
}
</style>
<!--[if lte IE 8]>
<style>
#bad_browser {
  border: none;
}
#wrap {
  border: solid #C3C3C3;
  border-width: 0px 1px 1px;
}
#content {
  border: solid #D9E0E7;
  border-width: 0px 1px 1px;
}
</style>
<![endif]-->

<div id="bad_browser">
  <div id="head"></div>
  <div id="wrap"><div id="content">
    JavaScript and Cookies need to be supported in order to use the site.
    <div>
      To be able to use all of the site&#39;s functions, download and install one of the following browsers:
      <div id="browsers" style="width: 360px;"><a href="http://www.mozilla-europe.org/" target="_blank" style="background: url(/images/firefox.png) no-repeat 50% 17px;">Firefox</a><a href="http://www.google.com/chrome/" target="_blank" style="background: url(/images/chrome.png) no-repeat 50% 17px;">Chrome</a><a href="http://www.opera.com/" target="_blank" style="background: url(/images/opera.png) no-repeat 50% 15px;">Opera</a></div>
    </div>
    Alternatively, you can use <a href="http://m.vk.com/">the mobile version of the site</a>.
  </div></div>
</div>