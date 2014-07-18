<div class="header">
  <div class="right_header">
    Пользователь: <?php echo $sf_user->getWelcomeName() ?>
    | <a href="<?php echo url_for('@user_logout') ?>" class="logout">Выход</a></div>
  <div class="jclock"></div>
</div>