<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Редактирование "<?php echo $object['title'] ?>"</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><?php echo $form['is_active']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['is_active']->render() ?>
          <span class="hint"><?php echo $form['is_active']->renderHelp() ?></span>

          <?php if ($form['is_active']->hasError()): ?>
          <span><?php echo $form['is_active']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['title']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['title']->render() ?>
          <span class="hint"><?php echo $form['title']->renderHelp() ?></span>

          <?php if ($form['title']->hasError()): ?>
          <span><?php echo $form['title']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['annotation']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['annotation']->render() ?>
          <span class="hint"><?php echo $form['annotation']->renderHelp() ?></span>

          <?php if ($form['annotation']->hasError()): ?>
          <span><?php echo $form['annotation']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['detail']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['detail']->render() ?>
          <span class="hint"><?php echo $form['detail']->renderHelp() ?></span>

          <?php if ($form['detail']->hasError()): ?>
          <span><?php echo $form['detail']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['slug']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['slug']->render() ?>
          <span class="hint"><?php echo $form['slug']->renderHelp() ?></span>

          <?php if ($form['slug']->hasError()): ?>
          <span><?php echo $form['slug']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl class="submit">
        <input type="submit" value="Сохранить изменения" />
      </dl>                    
    </fieldset>
  </form>
</div>