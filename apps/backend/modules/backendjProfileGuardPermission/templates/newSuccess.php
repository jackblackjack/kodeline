<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php include_partial('global/block/metas', array('metas' => array('title' => "Новая роль"))); ?>

<h2>Новая роль</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><?php echo $form['name']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['name']->render() ?>
          <span class="hint"><?php echo $form['name']->renderHelp() ?></span>

          <?php if ($form['name']->hasError()): ?>
          <span><?php echo $form['name']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['description']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['description']->render() ?>
          <span class="hint"><?php echo $form['description']->renderHelp() ?></span>

          <?php if ($form['description']->hasError()): ?>
          <span><?php echo $form['description']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['users_list']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['users_list']->render() ?>
          <span class="hint"><?php echo $form['users_list']->renderHelp() ?></span>

          <?php if ($form['users_list']->hasError()): ?>
          <span><?php echo $form['users_list']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl class="submit">
        <input type="submit" value="Добавить роль" />
      </dl>                    
    </fieldset>
  </form>
</div>
