<?php use_helper('I18N') ?>

<h1><?php echo __('Добро пожаловать!', null, 'kl_cms_users_plugin') ?></h1>
<?php echo get_partial('loginForm', array('form' => $form)) ?>