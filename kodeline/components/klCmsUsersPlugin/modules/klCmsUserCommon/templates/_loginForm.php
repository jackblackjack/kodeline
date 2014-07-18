<?php use_helper('I18N') ?>

<form action="<?php echo url_for('@user_login') ?>" method="post">
  <table>
    <tbody>
      <?php echo $form ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="<?php echo __('Login', null, 'kl_cms_users_plugin') ?>" />
          
          <?php $routes = $sf_context->getRouting()->getRoutes() ?>
          <?php if (isset($routes['user_forgot_password'])): ?>
            <a href="<?php echo url_for('@user_forgot_password') ?>"><?php echo __('Forgot your password?', null, 'kl_cms_users_plugin') ?></a>
          <?php endif; ?>

          <?php if (isset($routes['user_signup'])): ?>
            &nbsp; <a href="<?php echo url_for('@user_signup') ?>"><?php echo __('Want to register?', null, 'kl_cms_users_plugin') ?></a>
          <?php endif; ?>
        </td>
      </tr>
    </tfoot>
  </table>
</form>