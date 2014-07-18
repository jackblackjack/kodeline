<?php use_helper('I18N') ?>

<div class="login_form">
  <h3>Панель администратора</h3>

  <?php $routes = $sf_context->getRouting()->getRoutes() ?>
  <?php if (isset($routes['sf_guard_forgot_password'])): ?>
    <a class="forgot_pass" href="<?php echo url_for('@sf_guard_forgot_password') ?>"><?php echo __('Forgot your password?', null, 'sf_guard') ?></a>
  <?php endif; ?>

  <?php echo $form->renderFormTag(url_for('@sf_guard_signin'), array('method' => 'POST', 'class' => 'niceform')) ?>
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><label for="<?php echo $form['username']->renderId() ?>">Логин или e-mail:</label></dt>
        <dd><input type="text" name="<?php echo $form['username']->renderName() ?>" id="<?php echo $form['username']->renderId() ?>" size="54" /></dd>
      </dl>
      <dl>
        <dt><label for="<?php echo $form['password']->renderId() ?>">Пароль:</label></dt>
        <dd><input type="password" name="<?php echo $form['password']->renderName() ?>" id="<?php echo $form['password']->renderId() ?>" size="54" /></dd>
      </dl>
      <dl>
        <dt><label></label></dt>
        <dd>
          <input type="checkbox" name="<?php echo $form['remember']->renderName() ?>" id="<?php echo $form['remember']->renderId() ?>" />
          <label class="<?php echo $form['remember']->renderId() ?>">Запомнить меня</label>
        </dd>
      </dl>

      <dl class="submit">
        <input type="submit" value="<?php echo __('Войти', null, 'sf_guard') ?>" />
      </dl>
    </fieldset>
  </form>
</div>

<?php //echo $form ?>