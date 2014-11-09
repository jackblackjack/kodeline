<?php decorate_with('simple') ?>
<?php use_helper('I18N') ?>
<?php use_stylesheet('/backend/css/login.min.css') ?>
<?php use_javascript('/backend/js/core/login.js') ?>

<div id="mws-login-form">
  <form class="mws-form" action="<?php echo url_for('@user_login') ?>" method="post">
    <?php echo $form->renderHiddenFields() ?>

    <div class="mws-form-row">
      <div class="mws-form-item">
        <?php echo $form['username']->render(array('class' => 'mws-login-username required', 'placeholder' => 'Введите логин или email')) ?>

        <?php if ($form['username']->hasError()): ?>
        <div class="mws-error"><?php echo $form['username']->renderError() ?></div>
        <?php endif ?>
      </div>
    </div>
    <div class="mws-form-row">
      <div class="mws-form-item">
        <?php echo $form['password']->render(array('class' => 'mws-login-password required', 'placeholder' => 'Введите пароль')) ?>
      </div>
    </div>

    <div id="mws-login-remember" class="mws-form-row mws-inset">
      <ul class="mws-form-list inline">
        <li>
          <?php echo $form['remember']->render() ?>
          <?php echo $form['remember']->renderLabel() ?>
        </li>
      </ul>
    </div>

    <div class="mws-form-row">
      <input type="submit" value="Войти" class="btn btn-success mws-login-button">
    </div>
  </form>
  <?php $routes = $sf_context->getRouting()->getRoutes() ?>
  <?php if (isset($routes['user_forgot_password'])): ?>
    <a href="<?php echo url_for('@user_forgot_password') ?>"><?php echo __('Забыли Ваш пароль?', null, 'kl_cms_users_plugin') ?></a>
  <?php endif; ?>

  <?php if (isset($routes['user_signup'])): ?>
    &nbsp; <a href="<?php echo url_for('@user_signup') ?>"><?php echo __('Зарегистрироваться', null, 'kl_cms_users_plugin') ?></a>
  <?php endif; ?>
</div>