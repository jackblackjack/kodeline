<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<h2>Редактирование "<?php echo $object['title'] ?>"</h2>

<div class="form">
  <?php echo $form->renderFormTag(null, array('method' => 'POST')) ?>         
    <?php echo $form->renderHiddenFields() ?>
    <fieldset>
      <dl>
        <dt><?php echo $form['is_active']->renderLabel() ?>:</dt>
        <dd><?php echo $form['is_active']->render() ?></dd>
      </dl>

      <dl>
        <dt><?php echo $form['title']->renderLabel() ?>:</dt>
        <dd><?php echo $form['title']->render() ?></dd>
      </dl>

      <dl>
        <dt><?php echo $form['annotation']->renderLabel() ?>:</dt>
        <dd><?php echo $form['annotation']->render() ?></dd>
      </dl>

      <dl>
        <dt><?php echo $form['detail']->renderLabel() ?>:</dt>
        <dd><?php echo $form['detail']->render() ?></dd>
      </dl>
    </fieldset>

    <h2>Параметры элемента</h2>
    <?php if ($paramsForm = $form->getEmbeddedForm('parameters')): ?>
      <?php echo $form['parameters']->renderHiddenFields() ?>
      <?php $arParamForms = $paramsForm->getEmbeddedForms(); ?>
      <?php foreach ($arParamForms as $pname => $pform): ?>
      <?php echo $form['parameters'][$pname]->renderHiddenFields() ?>
      <dl>
        <dt><?php echo $pname ?></dt>
        <dd><?php echo $form['parameters'][$pname]['value']->render() ?></dd>
      </dl>
      <?php endforeach ?>
    <?php endif ?>

    <dl class="submit"><input type="submit" value="Сохранить изменения" /></dl>
  </form>
</div>