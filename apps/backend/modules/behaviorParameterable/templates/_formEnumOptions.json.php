<?php echo $form->renderHiddenFields() ?>
<dl>
  <dt><?php echo $form[$culture]['title']->renderLabel() ?>:</dt>
  <dd>
    <?php echo $form[$culture]['title']->render() ?>
    <span class="help"><?php echo $form[$culture]['title']->renderHelp() ?></span>

    <?php if ($form[$culture]['title']->hasError()): ?>
    <span><?php echo $form[$culture]['title']->renderError() ?></span>
    <?php endif ?>
  </dd>
</dl>

<dl>
  <dt><?php echo $form[$culture]['hint']->renderLabel() ?>:</dt>
  <dd>
    <?php echo $form[$culture]['hint']->render() ?>
    <span class="help"><?php echo $form[$culture]['hint']->renderHelp() ?></span>
  </dd>
</dl>

<dl>
  <dt><?php echo $form['is_require']->renderLabel() ?>:</dt>
  <dd>
    <?php echo $form['is_require']->render() ?>
    <span class="help"><?php echo $form['is_require']->renderHelp() ?></span>

    <?php if ($form['is_require']->hasError()): ?>
    <span><?php echo $form['is_require']->renderError() ?></span>
    <?php endif ?>
  </dd>
</dl>

<dl>
  <dt><?php echo $form['options']['is_dynamic']->renderLabel() ?>:</dt>
  <dd>
    <?php echo $form['options']['is_dynamic']->render() ?>
    <span class="help"><?php echo $form['options']['is_dynamic']->renderHelp() ?></span>

    <?php if ($form['options']['is_dynamic']->hasError()): ?>
    <span><?php echo $form['options']['is_dynamic']->renderError() ?></span>
    <?php endif ?>
  </dd>
</dl>

<dl>
  <dt><?php echo $form['options']['items']->renderLabel() ?>:</dt>
  <dd>
    <?php echo $form['options']['items']->render() ?>
    <span class="help"><?php echo $form['options']['items']->renderHelp() ?></span>

    <?php if ($form['options']['items']->hasError()): ?>
    <span><?php echo $form['options']['items']->renderError() ?></span>
    <?php endif ?>
  </dd>
</dl> 