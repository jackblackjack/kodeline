<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Редактирование "<?php echo $object['title'] ?>"</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><label for="<?php echo $form['is_active']->renderId() ?>"><?php echo $form['is_active']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['is_active']->render() ?>

          <?php if ($form['is_active']->hasError()): ?>
          <span><?php echo $form['is_active']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['title']->renderId() ?>"><?php echo $form['title']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['title']->render() ?>

          <?php if ($form['title']->hasError()): ?>
          <span><?php echo $form['title']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['annotation']->renderId() ?>"><?php echo $form['annotation']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['annotation']->render() ?>

          <?php if ($form['annotation']->hasError()): ?>
          <span><?php echo $form['annotation']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['detail']->renderId() ?>"><?php echo $form['detail']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['detail']->render() ?>

          <?php if ($form['detail']->hasError()): ?>
          <span><?php echo $form['detail']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      Открыть <a href="">расширенные параметры</a>

      <dl class="submit">
        <input type="submit" value="Изменить" />
      </dl>                    
    </fieldset>
  </form>
</div>