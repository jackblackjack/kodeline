<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Новый параметр</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><label for="<?php echo $form['is_public']->renderId() ?>"><?php echo $form['is_public']->renderLabel() ?>:</label></dt>
        <dd><?php echo $form['is_public']->render() ?></dd>
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
        <dt><label for="<?php echo $form['name']->renderId() ?>"><?php echo $form['name']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['name']->render() ?>

          <?php if ($form['name']->hasError()): ?>
          <span><?php echo $form['name']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['type']->renderId() ?>"><?php echo $form['type']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['type']->render() ?>

          <?php if ($form['type']->hasError()): ?>
          <span><?php echo $form['type']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['default_value']->renderId() ?>"><?php echo $form['default_value']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['default_value']->render() ?>

          <?php if ($form['default_value']->hasError()): ?>
          <span><?php echo $form['default_value']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><label for="<?php echo $form['length']->renderId() ?>"><?php echo $form['length']->renderLabel() ?>:</label></dt>
        <dd>
          <?php echo $form['length']->render() ?>

          <?php if ($form['length']->hasError()): ?>
          <span><?php echo $form['length']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl class="submit">
        <input type="submit" value="Добавить параметр" />
      </dl>                    
    </fieldset>
  </form>
</div>