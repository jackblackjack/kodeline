<?php include_partial('global/block/metas', array('metas' => array('title' => "Новый элемент словаря"))); ?>
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Новый элемент словаря</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><?php echo $form['is_active']->renderLabel() ?>:</dt>
        <dd><?php echo $form['is_active']->render() ?></dd>
      </dl>

      <!--dl>
        <dt><?php echo $form['parent_id']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['parent_id']->render() ?>

          <?php if ($form['parent_id']->hasError()): ?>
          <span><?php echo $form['parent_id']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl-->

      <dl>
        <dt><?php echo $form['title']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['title']->render() ?>

          <?php if ($form['title']->hasError()): ?>
          <span><?php echo $form['title']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['annotation']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['annotation']->render() ?>

          <?php if ($form['annotation']->hasError()): ?>
          <span><?php echo $form['annotation']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl>
        <dt><?php echo $form['detail']->renderLabel() ?>:</dt>
        <dd>
          <?php echo $form['detail']->render() ?>

          <?php if ($form['detail']->hasError()): ?>
          <span><?php echo $form['detail']->renderError() ?></span>
          <?php endif ?>
        </dd>
      </dl>

      <dl class="submit">
        <input type="submit" value="Добавить товар" />
      </dl>                    
    </fieldset>
  </form>
</div>