<!DOCTYPE html>
<!--[if lt IE 7]><html class="lt-ie9 lt-ie8 lt-ie7" lang="<?php echo $sf_user->getCulture() ?>"><![endif]-->
<!--[if IE 7]><html class="lt-ie9 lt-ie8" lang="<?php echo $sf_user->getCulture() ?>"><![endif]-->
<!--[if IE 8]><html class="lt-ie9" lang="<?php echo $sf_user->getCulture() ?>"><![endif]-->
<!--[if gt IE 8]><!--><html lang="<?php echo $sf_user->getCulture() ?>"><!--<![endif]-->
<head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
</head>

<body>
  <div id="mws-login-wrapper">
    <div id="mws-login">
      <div class="mws-login-lock"><i class="icon-lock"></i></div>
      <?php echo $sf_content ?>
    </div>
  </div>
</body>
</html>
